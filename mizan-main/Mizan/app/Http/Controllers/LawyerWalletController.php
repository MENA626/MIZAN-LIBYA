<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class LawyerWalletController extends Controller
{
    public function index()
    {
        $lawyer = Auth::user();
        $profile = $lawyer->lawyerProfile;

        // الرصيد المتاح (available) والإيداعات
        $availableBalance = Transaction::where('user_id', $lawyer->id)
            ->where('status', 'available')
            ->where('type', 'credit')
            ->sum('amount');

        // الرصيد المعلق (escrow أو frozen)
        $escrowBalance = Transaction::where('user_id', $lawyer->id)
            ->whereIn('status', ['escrow', 'frozen'])
            ->where('type', 'credit')
            ->sum('amount');

        // الرصيد المسحوب (completed) مسجل كـ debit
        $completedWithdrawals = Transaction::where('user_id', $lawyer->id)
            ->where('status', 'completed')
            ->where('type', 'debit')
            ->sum('amount');
            
        // المبالغ قيد السحب (pending) مسجلة كـ debit
        $pendingWithdrawals = Transaction::where('user_id', $lawyer->id)
            ->where('status', 'pending')
            ->where('type', 'debit')
            ->sum('amount');
            
        // إجمالي المسحوبات (لخصمها من الرصيد المتاح)
        $withdrawnBalance = $completedWithdrawals + $pendingWithdrawals;
            
        // الرصيد الفعلي المتاح للسحب
        // (إجمالي الإيداعات المتاحة) - (إجمالي السحوبات)
        $actualAvailable = $availableBalance - $withdrawnBalance;

        $transactions = Transaction::where('user_id', $lawyer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('lawyer.wallet', compact('profile', 'actualAvailable', 'escrowBalance', 'completedWithdrawals', 'pendingWithdrawals', 'transactions'));
    }

public function withdraw(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:1'
    ]);

    $lawyer = Auth::user();

    // 1. التحقق من وجود بيانات بنكية
    $profile = $lawyer->lawyerProfile;
    if (!$profile || !$profile->bank_name || !$profile->account_number) {
        return back()->with('error', 'الرجاء إدخال بياناتك البنكية أولاً في ملفك الشخصي قبل طلب السحب.');
    }

    // 2. التحقق من منع التكرار (Idempotency check)
    $recentPending = Transaction::where('user_id', $lawyer->id)
        ->where('status', 'pending')
        ->where('type', 'debit')
        ->where('amount', $request->amount)
        ->where('created_at', '>=', now()->subMinutes(1))
        ->exists();

    if ($recentPending) {
        return back()->with('error', 'لقد قمت بتقديم هذا الطلب بالفعل، يرجى الانتظار قليلاً.');
    }

    // 3. التحقق من الرصيد المتاح
    $availableBalance = Transaction::where('user_id', $lawyer->id)
        ->where('status', 'available')
        ->where('type', 'credit')
        ->sum('amount');

    $withdrawnBalance = Transaction::where('user_id', $lawyer->id)
        ->whereIn('status', ['pending', 'completed'])
        ->where('type', 'debit')
        ->sum('amount');

    $actualAvailable = $availableBalance - $withdrawnBalance;

    if ($request->amount > $actualAvailable) {
        return back()->with('error', 'المبلغ المطلوب أكبر من رصيدك المتاح للسحب.');
    }

    // 4. إنشاء طلب السحب مع استخدام Transaction لضمان عدم حدوث أخطاء جزئية
    \Illuminate\Support\Facades\DB::transaction(function () use ($lawyer, $request) {
        
        // تسجيل المعاملة
        Transaction::create([
            'user_id' => $lawyer->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => 'طلب سحب رصيد (تحويل بنكي)',
            'status' => 'pending'
        ]);

        // 5. إرسال إشعارات لجميع المديرين بكفاءة عالية
        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id');
        $notifications = [];

        foreach ($adminIds as $adminId) {
            $notifications[] = [
                'user_id'    => $adminId,
                'title'      => 'طلب سحب جديد',
                'body'       => 'قام المحامي ' . $lawyer->name . ' بتقديم طلب سحب بمبلغ ' . number_format($request->amount, 2) . ' د.ل، يرجى مراجعة الطلب.',
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (!empty($notifications)) {
            \Illuminate\Support\Facades\DB::table('notifications')->insert($notifications);
        }
    });

    // 6. الحل الجذري لمنع التكرار عند الريلود (Post-Redirect-Get Pattern)
    return redirect()->route('lawyer.wallet')->with('success', 'تم تقديم طلب السحب بنجاح وسيتم مراجعته.');
}
}
