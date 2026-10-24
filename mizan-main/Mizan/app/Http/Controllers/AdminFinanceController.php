<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Dispute;
use App\Models\PaidConsultation;
use App\Models\ConsultationArchive;

class AdminFinanceController extends Controller
{
    public function index()
    {
        // عمولات المنصة
        $platformRevenue = Transaction::where('type', 'commission')->sum('amount');
        
        $commissions = Transaction::where('type', 'commission')->orderBy('created_at', 'desc')->paginate(10);
        
        // طلبات السحب
        $withdrawalRequests = Transaction::where('type', 'debit')
            ->where('status', 'pending')
            ->with('user.lawyerProfile')
            ->orderBy('created_at', 'asc')
            ->get();
            
        // البلاغات
        $disputes = Dispute::where('status', 'open')
            ->with(['user', 'lawyer', 'disputable'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('admin.finance', compact('platformRevenue', 'commissions', 'withdrawalRequests', 'disputes'));
    }
    
public function viewChat($id) 
    {
        // 1. جلب البلاغ للتأكد من وجود بلاغ مفتوح وبما يخص الاستشارة تحديداً
        $dispute = \App\Models\Dispute::where('disputable_id', $id)
                             ->where('disputable_type', \App\Models\PaidConsultation::class)
                             ->where('status', 'open')
                             ->first();

        if (!$dispute) {
            return back()->with('error', 'لا يمكنك الاطلاع على المحادثة إلا إذا كان هناك بلاغ مفتوح.');
        }

        // 2. البحث عن جلسة المحادثة بين العميل والمحامي الخاص بهذه الاستشارة
        // نستخدم نفس الـ id (الذي يمثل الاستشارة) مع تحديد الـ type للتأكد
        $chatSession = \App\Models\ChatSession::where('chatable_id', $id)
                             ->where('chatable_type', \App\Models\PaidConsultation::class)
                             ->where('user_id', $dispute->user_id)
                             ->where('lawyer_id', $dispute->lawyer_id)
                             ->first();

        if (!$chatSession) {
            return back()->with('error', 'لم يتم العثور على جلسة محادثة مرتبطة بهذا النزاع.');
        }

        // 3. جلب الرسائل التابعة لهذه الجلسة مع اسم المرسل
        $messages = \App\Models\Message::where('chat_session_id', $chatSession->id)
                            ->with('sender')
                            ->orderBy('created_at', 'asc')
                            ->get();

        // 4. إرجاع صفحة العرض مع البيانات
        return view('admin.dispute_chat', compact('chatSession', 'messages'));
    }

public function approveWithdrawal($id)
{
    $transaction = Transaction::with('user')->findOrFail($id);
    
    // التحقق من حالة الطلب للتأكد من أنه يمكن معالجته
    if ($transaction->type === 'debit' && $transaction->status === 'pending') {
        
        // استخدام Transaction لضمان تنفيذ كل العمليات (التحديث والإشعار) ككتلة واحدة
        \Illuminate\Support\Facades\DB::transaction(function () use ($transaction) {
            
            // 1. تحديث حالة المعاملة إلى مكتملة
            $transaction->update(['status' => 'completed']);

            // 2. تحضير نص الرسالة في متغير منفصل
            $message = 'تم قبول طلب سحبك بمبلغ ' . number_format($transaction->amount, 2) . ' د.ل وتم تحويل المبلغ لحسابك البنكي.';

            // 3. إرسال إشعار للمحامي (بنفس أسلوب دالة resolveDispute)
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                [
                    'user_id'    => $transaction->user_id,
                    'title'      => 'تم قبول طلب السحب',
                    'body'       => $message,
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        });

        // 4. استخدام redirect بدلاً من back لمنع تكرار العملية عند إعادة تحميل الصفحة (Refresh)
        return redirect()->route('admin.finance')->with('success', 'تم الموافقة على طلب السحب وإبلاغ المحامي.');
    }
    
    // في حال كانت العملية غير صالحة أو تم معالجتها مسبقاً
    return redirect()->route('admin.finance')->with('error', 'طلب غير صالح.');
}


    public function resolveDispute(Request $request, $id)
{
    $request->validate([
        'decision' => 'required|in:refund_client,release_to_lawyer',
        'admin_notes' => 'required|string'
    ]);

    $dispute = Dispute::with(['user', 'lawyer'])->findOrFail($id);
    
    if ($dispute->status !== 'open') {
        return back()->with('error', 'هذا البلاغ مفصول فيه مسبقاً.');
    }

    $transaction = Transaction::where('reference_type', PaidConsultation::class)
        ->where('reference_id', $dispute->disputable_id)
        ->where('status', 'frozen')
        ->first();

    $clientMessage = "";
    $lawyerMessage = "";

    if ($request->decision === 'refund_client') {
        if ($transaction) { $transaction->delete(); }
        
        $dispute->update([
            'status' => 'resolved',
            'admin_notes' => 'تم استرجاع المبلغ للعميل: ' . $request->admin_notes
        ]);

        $clientMessage = 'تم الفصل في النزاع الخاص باستشارتك #' . $dispute->disputable_id . ' بينك وبين المحامي ' . $dispute->lawyer->name . ' لصالحك، وتم استرجاع المبلغ لحسابك.';
        $lawyerMessage = 'تم الفصل في النزاع الخاص بالاستشارة #' . $dispute->disputable_id . ' بينك وبين العميل ' . $dispute->user->name . ' لصالح العميل، وتم استرجاع المبلغ له.';
    
    } else {
        if ($transaction) { $transaction->update(['status' => 'available']); }
        
        $dispute->update([
            'status' => 'resolved',
            'admin_notes' => 'تم الإفراج عن المبلغ للمحامي: ' . $request->admin_notes
        ]);

        $lawyerMessage = 'تم الفصل في النزاع الخاص بالاستشارة #' . $dispute->disputable_id . ' بينك وبين العميل ' . $dispute->user->name . ' لصالحك، وتم الإفراج عن المبلغ لحسابك.';
        $clientMessage = 'تم الفصل في النزاع الخاص بإستشارتك #' . $dispute->disputable_id . ' بينك وبين المحامي ' . $dispute->lawyer->name . ' لصالح المحامي، وتم الإفراج عن المبلغ له.';
    }

    // إرسال الإشعارات المخصصة
    \Illuminate\Support\Facades\DB::table('notifications')->insert([
        [
            'user_id' => $dispute->user_id,
            'title' => 'نتيجة النزاع',
            'body' => $clientMessage,
            'is_read' => false,
            'created_at' => now(), 'updated_at' => now()
        ],
        [
            'user_id' => $dispute->lawyer_id,
            'title' => 'نتيجة النزاع',
            'body' => $lawyerMessage,
            'is_read' => false,
            'created_at' => now(), 'updated_at' => now()
        ]
    ]);

    return back()->with('success', 'تم الفصل في البلاغ وإبلاغ الأطراف.');
}
}
