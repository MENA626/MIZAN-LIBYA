<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * 1. عرض واجهة تسجيل الدخول
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * 2. معالجة طلب تسجيل الدخول (مُصحح ومحدث)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // التحقق من حالة حساب المحامي
            if ($user->role === 'lawyer') {
                if ($user->status === 'pending') {
                    Auth::logout();
                    return redirect()->route('login')->with('info', 'حسابك بانتظار التوثيق من قبل الإدارة.');
                }

                if ($user->status === 'rejected') {
                    // جلب سبب الرفض من جدول البروفايل
                    $reason = $user->lawyerProfile ? $user->lawyerProfile->rejection_reason : 'لا يوجد سبب محدد.';
                    Auth::logout(); // تسجيل خروجه فوراً
                    
                    return redirect()->route('login')->with('error', 'نعتذر منك، تم رفض حسابك من قبل الإدارة. السبب: ' . $reason . ' - يرجى محاولة التسجيل مرة أخرى بعد تعديل البيانات.');
                }
            }

            // --- البصمة الأمنية ---
            $request->session()->regenerate();
            $request->session()->put('user_fingerprint', $user->id); 

            // التوجيه الذكي
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'lawyer') {
                return redirect()->intended('/lawyer/dashboard');
            } else {
                return redirect()->intended('/client/dashboard');
            }
        }

        return back()->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'])->withInput();
    }

    /**
     * 3. عرض واجهة إنشاء حساب
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * 4. معالجة طلب إنشاء الحساب (مُعدل لدعم الملفات والتوثيق)
     */
    public function register(Request $request)
    {
        // 1. توحيد الحقل القادم من الفورم (user_type أو role)
        $inputRole = $request->input('user_type') ?? $request->input('role');
        $request->merge(['role' => $inputRole]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required', 'in:client,lawyer'],
            'license_number' => ['required_if:role,lawyer', 'nullable', 'string', 'unique:lawyer_profiles,license_number'],
            'syndicate_card' => ['required_if:role,lawyer', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // 2. معالجة رفع الصورة
            $cardPath = null;
            if ($inputRole === 'lawyer' && $request->hasFile('syndicate_card')) {
                $file = $request->file('syndicate_card');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/syndicate_cards'), $fileName);
                $cardPath = 'uploads/syndicate_cards/' . $fileName;
            }

            // 3. إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $inputRole,
                'status' => ($inputRole === 'lawyer') ? 'pending' : 'approved',
                'syndicate_card_path' => $cardPath,
            ]);

            // 4. إنشاء ملف المحامي وإرسال إشعار
            if ($inputRole === 'lawyer') {
                $user->lawyerProfile()->create([
                    'license_number' => $request->license_number,
                    'syndicate_card_path' => $cardPath,
                    'specialization' => 'قانون عام',
                ]);

                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    DB::table('notifications')->insert([
                        'user_id'    => $admin->id,
                        'title'      => 'طلب توثيق جديد',
                        'body'       => 'هناك محامي جديد بانتظار المراجعة: ' . $user->name,
                        'is_read'    => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            // منطق التوجيه بعد التسجيل:
            
            // إذا كان محامي: توجيه لصفحة تسجيل الدخول مع رسالة
            if ($inputRole === 'lawyer') {
                return redirect()->route('login')->with('info', 'تم تقديم طلبك بنجاح، بانتظار موافقة الإدارة.');
            }

            // إذا كان عميل: توجيه للواجهة الرئيسية (/) مع رسالة
            return redirect('/')->with('success', 'تم إنشاء حسابك بنجاح! يمكنك الآن تسجيل الدخول.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ تقني، حاول مجدداً.');
        }
    }

    /**
     * 5. تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}