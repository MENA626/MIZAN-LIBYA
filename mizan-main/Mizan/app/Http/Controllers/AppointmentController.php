<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentController extends Controller
{

// عند الحجز الجديد
    public function store(Request $request)
    {
        $request->validate([
            'lawyer_id'        => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'notes'            => 'nullable|string|max:500',
        ]);

        try {
            // 1. التحقق من أوقات العمل قبل أي شيء
            $lawyer = \App\Models\User::findOrFail($request->lawyer_id);
            $bookingDate = Carbon::parse($request->appointment_date);
            $dayOfWeek = $bookingDate->dayOfWeek; // 0=أحد، 1=اثنين... 6=سبت
            $bookingTime = Carbon::parse($request->appointment_time)->format('H:i');

            $workingDay = $lawyer->workingHours()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            // التحقق: هل المحامي يعمل في هذا اليوم؟
            if (!$workingDay) {
                return $this->errorResponse($request, ' عذراً، المحامي غير متاح في هذا اليوم.');
            }

            // التحقق: هل الوقت ضمن ساعات العمل؟
            if ($bookingTime < Carbon::parse($workingDay->start_time)->format('H:i') || 
                $bookingTime > Carbon::parse($workingDay->end_time)->format('H:i')) {
                return $this->errorResponse($request, 'عذراً، التوقيت المختار خارج ساعات العمل الرسمية للمحامي.');
            }

            // 2. دمج التاريخ والوقت
            $bookingDateTime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time, 'Africa/Tripoli');
            $now = Carbon::now('Africa/Tripoli');

            if ($bookingDateTime->lessThanOrEqualTo($now)) {
                return $this->errorResponse($request, 'عذراً، لا يمكنك حجز موعد في وقت مضى.');
            }

            // 3. التحقق: هل الموعد محجوز مسبقاً؟
            $isBooked = Appointment::where('lawyer_id', $request->lawyer_id)
                ->where('date_time', $bookingDateTime->toDateTimeString())
                ->where('status', 'scheduled') 
                ->exists();

            if ($isBooked) {
                return $this->errorResponse($request, 'عذراً، هذا الموعد محجوز مسبقاً.');
            }

            $appointment = Appointment::create([
                'user_id'   => Auth::id(),        
                'lawyer_id' => $request->lawyer_id,
                'date_time' => $bookingDateTime->toDateTimeString(),
                'status'    => 'scheduled',
                'notes'     => $request->notes,
            ]);

            // ✅ إضافة إشعار للمحامي
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'user_id'    => $request->lawyer_id,
                'title'      => 'طلب حجز موعد جديد',
                'body'       => 'قام العميل ' . Auth::user()->name . ' بإرسال طلب حجز موعد جديد، بانتظار مراجعته.',
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $this->successResponse($request, 'تم إرسال طلب الحجز بنجاح.');

        } catch (\Exception $e) {
            Log::error("Booking Error: " . $e->getMessage());
            return $this->errorResponse($request, '❌ حدث خطأ في النظام.', 500);
        }
    }
    // دالة القبول
    public function approve($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['status' => 'completed']);

            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'user_id'    => $appointment->user_id,
                'title'      => 'قبول طلب الحجز',
                'body'       => 'قام المحامي ' . Auth::user()->name . ' بقبول الموعد، يمكنك التواصل معه في الموعد المحدد.',
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return back()->with('success', 'تم قبول الموعد بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الموافقة.');
        }
    }

    // دالة الرفض
    public function reject($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['status' => 'cancelled']);

            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'user_id'    => $appointment->user_id,
                'title'      => 'رفض طلب الحجز',
                'body'       => 'عذراً، قام المحامي ' . Auth::user()->name . ' برفض طلب حجز الموعد الخاص بك.',
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return back()->with('success', 'تم رفض الموعد بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الرفض.');
        }
    }

    private function successResponse($request, $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return back()->with('success', $message);
    }

    private function errorResponse($request, $message, $code = 422)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], $code);
        }
        return back()->with('error', $message);
    }

    public function lawyerAppointments()
    {
        $appointments = \App\Models\Appointment::where('lawyer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('lawyer.appointments', compact('appointments'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'appointment_ids' => 'required|array',
            'appointment_ids.*' => 'exists:appointments,id',
        ]);

        try {
            // تنفيذ عملية الحذف
            Appointment::whereIn('id', $request->appointment_ids)->delete();
            
            // رسالة النجاح الجديدة المطلوبة
            return back()->with('success', 'تم حذف سجلات المواعيد القديمة بنجاح.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء عملية الحذف.');
        }
    }
}