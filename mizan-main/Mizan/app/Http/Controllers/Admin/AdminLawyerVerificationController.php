<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminLawyerVerificationController extends Controller
{
    /**
     * 1. عرض واجهة التوثيق والتعامل مع طلبات البحث المفصولة (AJAX)
     */
    public function index(Request $request)
    {
        // استقبال النص القادم من حقل البحث في الواجهة (اسم المحامي أو رقم القيد)
        $search = $request->get('search');

        // جلب المحامين المعلقين من قاعدة البيانات مباشرة بناءً على شروط الفلترة
        $pendingLawyers = User::with('lawyerProfile')
            ->where('role', 'lawyer')
            ->where('status', 'pending')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhereHas('lawyerProfile', function ($profile) use ($search) {
                            $profile->where('license_number', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // حساب الإحصائيات ديناميكياً لتحديث الصناديق الفوقية ببيانات حية
        $stats = [
            'pending_count'       => User::where('role', 'lawyer')->where('status', 'pending')->count(),
            'approved_this_month' => User::where('role', 'lawyer')->where('status', 'approved')->where('updated_at', '>=', now()->startOfMonth())->count(),
            'total_lawyers'       => User::where('role', 'lawyer')->count(),
            'active_lawyers'      => User::where('role', 'lawyer')->where('status', 'approved')->count(),
        ];

        // الاستجابة الذكية لطلبات الجافا سكريبت المفصولة (API-like Response)
        if ($request->ajax()) {
            return response()->json([
                'stats'   => $stats,
                'lawyers' => $pendingLawyers,
                'count'   => $pendingLawyers->count()
            ]);
        }

        // العرض التقليدي المعتاد عند تحميل الصفحة لأول مرة
        return view('admin.verification', compact('pendingLawyers', 'stats'));
    }

    /**
     * 2. دالة الاعتماد والتوثيق الفوري للمحامي
     */
    public function approve($id)
    {
        // التأكد من أن المستخدم محامي وموجود في قاعدة البيانات
        $lawyer = User::where('role', 'lawyer')->findOrFail($id);
        
        // 1. تحديث حالته إلى مقبول في جدول users
        $lawyer->update([
            'status' => 'approved',
        ]);

        // 2. تصفير سبب الرفض القديم داخل جدول البروفايل (lawyer_profiles) إن وُجد
        if ($lawyer->lawyerProfile) {
            $lawyer->lawyerProfile->update([
                'rejection_reason' => null
            ]);
        }

        return redirect()->back()->with('success', 'تم اعتماد وتوثيق حساب المحامي بنجاح، وأصبح حسابه نشطاً الآن على منصة ميزان.');
    }

    /**
     * 3. دالة الرفض وحفظ الملاحظة الإدارية في جدول البروفايل
     */
    public function reject(Request $request, $id)
    {
        // التحقق من أن سبب الرفض مكتوب
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $lawyer = User::where('role', 'lawyer')->findOrFail($id);

        // 1. تحديث الحالة في جدول users ليكون مرفوضاً
        $lawyer->update([
            'status' => 'rejected',
        ]);

        // 2. تحديث وحفظ سبب الرفض داخل جدول البروفايل الموحد (lawyer_profiles)
        if ($lawyer->lawyerProfile) {
            $lawyer->lawyerProfile->update([
                'rejection_reason' => $request->rejection_reason
            ]);
        }

        return redirect()->back()->with('success', 'تم رفض طلب التوثيق، وحُفظ السبب ليظهر للمحامي فور محاولته الدخول.');
    }
}