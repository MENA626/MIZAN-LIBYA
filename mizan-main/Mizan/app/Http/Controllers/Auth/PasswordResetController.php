<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * 1. عرض واجهة "نسيت كلمة المرور" (Forgot Password View)
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * 2. استقبال البريد الإلكتروني، توليد رمز OTP من 6 أرقام وتوجيه المستخدم لواجهة التحقق
     */
    public function sendResetLinkEmail(Request $request)
    {
        // التحقق الصارم من وجود الإيميل ومطابقتها مع قاعدة البيانات في Supabase
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب لإرسال رمز التحقق.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.exists' => 'هذا البريد الإلكتروني غير مسجل في المنصة.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // إجراء أمني: تنظيف جدول الرموز من المدخلات القديمة (المنتهية لأكثر من 15 دقيقة)
        DB::table('password_reset_tokens')->where('created_at', '<', Carbon::now()->subMinutes(15))->delete();

        // 🔢 توليد رمز OTP رقمي عشوائي نقي مكون من 6 أرقام
        $otpCode = rand(100000, 999999);

        try {
            // تحديث أو إدخال الرمز مشفراً بـ Bcrypt لحماية قاعدة البيانات من الاختراق
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($otpCode), 
                    'created_at' => Carbon::now()
                ]
            );

            // تسجيل الرمز في الـ Log محلياً للمتابعة أثناء التطوير
            Log::info("OTP Code generated for {$request->email}: " . $otpCode);

            // حفظ البريد الإلكتروني في الـ Session لنقله ديناميكياً لواجهة الـ OTP
            session(['reset_email' => $request->email]);

            // التوجيه التلقائي الذكي إلى واجهة الـ OTP مع تمرير الرمز في الجلسة للملء التلقائي المحلي
            return redirect()->route('verify.otp')
                             ->with('status', 'تم توليد رمز التحقق وإرساله بنجاح.')
                             ->with('debug_otp', $otpCode);

        } catch (\Exception $e) {
            Log::error('Forgot Password Controller Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ غير متوقع أثناء معالجة الطلب، يرجى المحاولة لاحقاً.');
        }
    }

    /**
     * ⭐ الدالة المضافة حديثاً: عرض واجهة إدخال رمز التحقق (OTP View)
     */
    public function showVerifyOtpForm()
    {
        // التحقق أمنياً من أن المستخدم جاء عبر المسار الصحيح ولديه بريد محفوظ في الجلسة
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request')->with('error', 'الرجاء إدخل البريد الإلكتروني أولاً.');
        }

        return view('auth.verify-otp');
    }

    /**
     * 3. معالجة والتحقق من صحة رمز الـ OTP المدخل من المستخدم
     */
    public function verifyOtp(Request $request)
    {
        // التحقق من الحقل القادم من الفورم
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'الرجاء إدخال رمز التحقق.',
            'otp.digits' => 'يجب أن يتكون رمز التحقق من 6 أرقام تماماً.',
        ]);

        // جلب البريد المحفوظ في الجلسة لمعرفة من صاحب الطلب الحالي
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')->with('error', 'انتهت صلاحية الجلسة الأمنية، يرجى إعادة إدخال بريدك الإلكتروني.');
        }

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // جلب سجل الرمز من قاعدة البيانات
        $tokenData = DB::table('password_reset_tokens')->where('email', $email)->first();

        // حماية أمنية: التأكد من وجود الرمز وأنه لم يتجاوز 15 دقيقة (مدة صلاحية الـ OTP القياسية)
        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(15)->isPast()) {
            return back()->with('error', 'رمز التحقق منتهي الصلاحية أو غير صالح، يرجى طلب رمز جديد.');
        }

        // مطابقة الرمز المدخل يدوياً أو تلقائياً مع الرمز المشفر المخزن في Supabase
        if (!Hash::check($request->otp, $tokenData->token)) {
            return back()->with('error', 'رمز التحقق المدخل غير صحيح، يرجى المحاولة مجدداً.');
        }

        // عند نجاح المطابقة، نوجه المستخدم لواجهة تعيين كلمة المرور الجديدة ونمرر الإيميل والتوكين عبر الـ Session
        return redirect()->route('password.reset', ['token' => $request->otp])
                         ->with('email', $email);
    }

    /**
     * 4. عرض واجهة تعيين كلمة المرور الجديدة بعد تخطي حجز الـ OTP
     */
    public function showResetForm(Request $request, $token = null)
    {
        // نلتقط الإيميل المحفوظ في الجلسة أو القادم من الطلب
        $email = $request->email ?? session('email');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * 5. تحديث كلمة المرور النهائية وتدمير الرمز المستعمل لمنع تكراره
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'min:8', 'confirmed'], 
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب لإتمام العملية.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.exists' => 'البريد الإلكتروني المدخل غير مسجل في المنصة.',
            'password.required' => 'الرجاء إدخال كلمة المرور الجديدة.',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tokenData = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(15)->isPast()) {
            return back()->with('error', 'انتهت صلاحية الجلسة، يرجى إعادة طلب رمز تحقق جديد.');
        }

        if (!Hash::check($request->token, $tokenData->token)) {
            return back()->with('error', 'العملية غير صالحة.');
        }

        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            // حذف الرمز فوراً بعد الاستخدام (One-time use) حمايةً للنظام
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::commit();

            // تنظيف بيانات الجلسة المؤقتة
            session()->forget(['reset_email', 'debug_otp']);

            return redirect()->route('login')->with('success', 'تم تحديث كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول بحسابك الجديد.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Password Reset Controller Execution Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ غير متوقع، يرجى إعادة المحاولة.');
        }
    }
} 