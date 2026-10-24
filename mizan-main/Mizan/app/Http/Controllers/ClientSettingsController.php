<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ClientSettingsController extends Controller
{
    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required'  => 'الاسم الكامل مطلوب.',
            'avatar.max'     => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ]);

        $user = Auth::user();

        try {
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $destinationPath = public_path('uploads/avatars');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                if ($user->avatar && File::exists(public_path('uploads/avatars/' . $user->avatar))) {
                    File::delete(public_path('uploads/avatars/' . $user->avatar));
                }

                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);
                $user->avatar = $fileName;
            }

            $user->name  = $request->name;
            $user->phone = $request->phone;
            $user->save();

            return back()->with('success', 'تم تحديث الملف الشخصي بنجاح.')->with('tab', 'profile');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ غير متوقع أثناء تحديث البيانات.')->with('tab', 'profile');
        }
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'current_password' => ['required', 'current_password'], 
        'password'         => ['required', 'min:8', 'confirmed'],
    ], [
        'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
        'password.min'                      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        'password.confirmed'                => 'تأكيد كلمة المرور غير متطابق.',
    ]);

    if ($validator->fails()) {
        // نرجع للخلف مع الأخطاء ونحدد التبويب security
        return back()->withErrors($validator)->with('tab', 'security')->withInput();
    }

    $user = Auth::user();
    $user->update(['password' => Hash::make($request->password)]);

    // نرجع للخلف مع رسالة نجاح ونحدد التبويب security
    return back()->with('success', 'تم تحديث كلمة المرور بنجاح.')->with('tab', 'security');
}

    /**
     * تحديث إعدادات الإشعارات
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $notificationKeys = ['app_notifications', 'new_consultation', 'new_bookings', 'new_messages'];
        $newSettings = [];
        
        foreach ($notificationKeys as $key) {
            $newSettings[$key] = $request->has($key);
        }
        
        $user->update(['notification_settings' => $newSettings]);
        
        return back()->with('success', 'تم حفظ إعدادات الإشعارات بنجاح.')->with('tab', 'notifications');
    }

    /**
     * حذف حساب العميل نهائياً
     */
    public function deleteAccount(Request $request)
    {
        // التحقق من كلمة المرور الخاصة بالحذف
        $request->validate([
            'delete_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('كلمة المرور غير صحيحة.');
                }
            }],
        ]);

        $user = Auth::user();

        // تنفيذ عمليات الحذف
        Auth::logout();
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'تم حذف حسابك بنجاح.');
    }
}