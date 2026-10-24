<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * تحديث جميع الإشعارات الخاصة بالمستخدم الحالي لتصبح مقروءة
     */
    public function markAsRead(Request $request)
    {
        DB::table('notifications')
            ->where('user_id', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        
        return response()->json(['status' => 'success']);
    }

    /**
     * حذف إشعار معين بناءً على ID
     */
    public function destroy($id)
    {
        // التأكد من أن الإشعار يخص المستخدم الحالي قبل الحذف لأغراض أمنية
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'تم حذف التنبيه بنجاح');
    }
}