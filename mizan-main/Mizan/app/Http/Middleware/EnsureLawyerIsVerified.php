<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureLawyerIsVerified
{
    /**
     * التحقق من حالة حساب المحامي قبل السماح له بدخول لوحة التحكم
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. التأكد أولاً أنه مسجل دخول وأنه يحمل صلاحية محامي
        if (!$user || $user->role !== 'lawyer') {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
        }

        // 2. إذا كان الحساب بانتظار المراجعة (Pending)
        if ($user->status === 'pending') {
            Auth::logout();
            return redirect()->route('login')->with('warning', 'حسابك بانتظار مراجعة التوثيق من قبل إدارة المنصة ونقابة المحامين. سنقوم بإشعارك فور تفعيل الحساب.');
        }

        // 3. إذا كان الحساب مرفوضاً (Rejected)
        if ($user->status === 'rejected') {
            // نأخذ سبب الرفض المكتوب من الأدمن ونعرضه له عند باب اللوجن
            $reason = $user->rejection_reason ?? 'الرجاء التواصل مع الدعم الفني للمنصة.';
            Auth::logout();
            return redirect()->route('login')->with('error', 'تم رفض طلب توثيق حسابك للسبب التالي: ' . $reason);
        }

        // 4. إذا كان مقبولاً (approved) يمر بسلام إلى لوحة تحكمه
        return $next($request);
    }
}