<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AdminSettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * تحديث الملف الشخصي للمدير (الاسم والصورة)
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'avatar.image'  => 'الملف المرفق يجب أن يكون صورة.',
            'avatar.mimes'  => 'صيغة الصورة غير مدعومة.',
            'avatar.max'    => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $destinationPath = public_path('uploads/avatars');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // حذف الصورة القديمة
            if ($user->avatar && File::exists(public_path('uploads/avatars/' . $user->avatar))) {
                File::delete(public_path('uploads/avatars/' . $user->avatar));
            }

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);
            $user->avatar = $fileName;
        }

        $user->name = $request->name;
        // البريد الإلكتروني للمدير لا يجب تغييره هنا للأمان، أو يمكن تغييره إذا تطلب الأمر. 
        // سنكتفي بتعديل الاسم فقط والصورة كما في بقية النظام

        $user->save();

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    /**
     * تحديث كلمة مرور المدير
     */
    // داخل updatePassword في AdminSettingsController
public function updatePassword(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'current_password' => 'required',
        'password'         => 'required|min:8|confirmed',
    ], [
        'current_password.required' => 'كلمة المرور الحالية مطلوبة.',
        'password.min'              => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
        'password.confirmed'        => 'تأكيد كلمة المرور غير متطابق.',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->with('tab', 'security');
    }

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.'])->with('tab', 'security');
    }

    Auth::user()->update(['password' => Hash::make($request->password)]);

    return back()->with('success', 'تم تغيير كلمة المرور بنجاح.')->with('tab', 'security');
}

    /**
     * تحديث إعدادات الإشعارات للمدير
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $notificationKeys = [
            'app_notifications', 
            'new_cases_alerts', 
            'lawyer_verifications_alerts', 
            'finance_alerts'
        ];

        $newSettings = [];
        foreach ($notificationKeys as $key) {
            $newSettings[$key] = $request->has($key);
        }

        $user->update(['notification_settings' => $newSettings]);

        return back()->with('success', 'تم حفظ إعدادات الإشعارات بنجاح.');
    }

    public function deleteAccount(Request $request)
{
    // 1. التحقق من كلمة المرور
    $request->validate([
        'delete_password' => 'required',
    ]);

    // التحقق من صحة كلمة المرور الحالية للمدير
    if (!\Hash::check($request->delete_password, auth()->user()->password)) {
        return back()->withErrors(['delete_password' => 'كلمة المرور غير صحيحة.'])->with('tab', 'security');
    }

    // 2. حذف الحساب
    $user = auth()->user();
    $user->delete();

    // 3. تسجيل الخروج وإعادة التوجيه
    auth()->logout();
    return redirect('/')->with('success', 'تم حذف حسابك بنجاح.');
}
}
