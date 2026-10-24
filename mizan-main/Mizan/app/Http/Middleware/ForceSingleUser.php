<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceSingleUser
{
    /**
     * معالجة الطلب للتأكد من أن المستخدم يملك جلسة واحدة فقط.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. التحقق إذا كان المستخدم مسجلاً للدخول بالفعل
        if (Auth::check()) {
            
            $user = Auth::user();
            
            // 2. جلب البصمة الأمنية (Fingerprint) المخزنة في الجلسة الحالية
            $storedFingerprint = $request->session()->get('user_fingerprint');

            // 3. التحقق من حالة الجلسة:
            // إذا لم توجد بصمة (أول دخول أو جلسة منتهية)، نقوم بتخزين البصمة الحالية فوراً
            // لضمان عدم طرد المستخدم عند محاولته الأولى للدخول.
            if (!$storedFingerprint) {
                $request->session()->put('user_fingerprint', $user->id);
                return $next($request);
            }

            // 4. التحقق من مطابقة البصمة:
            // نقوم بتحويل القيم إلى نص (string) لضمان دقة المقارنة وتجنب أخطاء الأنواع.
            if ((string)$storedFingerprint !== (string)$user->id) {
                
                // الطرد الصارم في حال اكتشاف تداخل أو تغيير في هوية الجلسة
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'تم تسجيل دخولك من مكان آخر أو انتهت صلاحية الجلسة.');
            }
        }

        // في حال كل شيء سليم، نمرر الطلب للوجهة المطلوبة
        return $next($request);
    }
}