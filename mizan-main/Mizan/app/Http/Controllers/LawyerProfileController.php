<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use App\Models\LawyerWorkingHour;
use App\Models\Specialty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class LawyerProfileController extends Controller
{
    /**
     * عرض صفحة الإعدادات: هذه الدالة هي المدخل الوحيد للصفحة
     */
    public function editSettings()
    {
        return view('lawyer.settings', [
            'allSpecialties' => Specialty::all()
        ]);
    }

    /**
     * تحديث البيانات الأساسية ومعلومات المكتب
     */
    public function updateProfile(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $user = auth()->user();

        // 2. معالجة الصورة
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $fileName);
            $user->avatar = $fileName;
        }

        // 3. تحديث جدول المستخدمين (User)
        $user->update([
            'name' => $request->name, 
            'phone' => $request->phone
        ]);

        // 4. تحديث جدول ملف المحامي (LawyerProfile)
        // نستخدم updateOrCreate لضمان إنشاء السجل إذا لم يكن موجوداً
        $user->lawyerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'license_number' => $request->license_number,
                'address' => $request->address,
            ]
        );

        return back()->with('success', 'تم تحديث البيانات الأساسية بنجاح.')->with('tab', 'profile');
    }

    /**
     * تحديث المعلومات المهنية
     */
    public function updateProfessionalInfo(Request $request)
    {
        $request->validate([
            'license_number' => 'required|string|max:50',
            'address' => 'required|string',
            'specialty_id' => 'nullable|exists:specialties,id',
            'years_of_experience' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience' => 'nullable|string',
        ]);

        auth()->user()->lawyerProfile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only(['license_number', 'address', 'specialty_id', 'years_of_experience', 'bio', 'qualifications', 'experience'])
        );

        return back()->with('success', 'تم تحديث المعلومات المهنية.')->with('tab', 'professional');
    }

    /**
     * تحديث البيانات المالية
     */
    public function updateFinancial(Request $request)
    {
        $request->validate([
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'iban' => 'nullable|string',
        ]);

        LawyerProfile::updateOrCreate(['user_id' => auth()->id()], $request->all());
        return back()->with('success', 'تم تحديث البيانات المالية.')->with('tab', 'financial');
    }

    // 3. تحديث أوقات العمل

    public function updateWorkingHours(Request $request)

    {
        $user = auth()->user();
        $days = $request->input('days');
        if ($days) {
            foreach ($days as $dayIndex => $data) {

                if ($dayIndex >= 0 && $dayIndex <= 6) {
                    LawyerWorkingHour::updateOrCreate(
                        ['lawyer_id' => $user->id, 'day_of_week' => $dayIndex],

                        [
                            'start_time' => $data['start'] ?? null,
                            'end_time'   => $data['end'] ?? null,
                            'is_active'  => isset($data['active']) ? true : false,
                        ]
                    );
                }
            }
        }
        return back()->with('success', 'تم حفظ أوقات العمل بنجاح.')->with('tab', 'working-hours');
    }



    // 4. تحديث الإشعارات

    public function updateNotifications(Request $request)

    {
        $user = auth()->user();
        $notificationKeys = ['app_notifications', 'new_consultation', 'new_bookings', 'new_messages'];
        $newSettings = [];
        foreach ($notificationKeys as $key) {
            $newSettings[$key] = $request->has($key);

        }
        $user->update(['notification_settings' => $newSettings]);
        return back()->with('success', 'تم حفظ إعدادات الإشعارات بنجاح')->with('tab', 'notifications');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
{
    $user = auth()->user();

    // التحقق من صحة المدخلات
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed', // 'confirmed' يعني يجب أن يتطابق مع new_password_confirmation تلقائياً
    ], [
        'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
        'new_password.required' => 'يرجى إدخال كلمة مرور جديدة.',
        'new_password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
        'new_password.confirmed' => 'كلمة المرور غير متطابقة.',
    ]);

    // التحقق من صحة كلمة المرور الحالية
    if (!\Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.'])->with('tab', 'security')->withInput();
    }

    // تحديث كلمة المرور
    $user->update(['password' => \Hash::make($request->new_password)]);

    return back()->with('success', 'تم تحديث كلمة المرور بنجاح.')->with('tab', 'security');
}


/**
 * حذف حساب المحامي
 */
public function deleteAccount(Request $request)
{
    // 1. التحقق من كلمة المرور
    $request->validate([
        'delete_password' => 'required',
    ]);

    // التحقق من صحة كلمة المرور الحالية للمستخدم
    if (!\Hash::check($request->delete_password, auth()->user()->password)) {
        return back()->withErrors(['delete_password' => 'كلمة المرور غير صحيحة.'])->with('tab', 'profile');
    }

    // 2. حذف الحساب
    $user = auth()->user();
    $user->delete();

    // 3. تسجيل الخروج وإعادة التوجيه
    auth()->logout();
    return redirect('/')->with('success', 'تم حذف حسابك بنجاح.');
}
}