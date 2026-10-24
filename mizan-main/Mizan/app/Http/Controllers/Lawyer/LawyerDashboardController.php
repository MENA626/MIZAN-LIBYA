<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LegalCase;
use App\Models\Appointment;
use App\Models\PaidConsultation;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LawyerDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم المحامي مع تصنيف الاستشارات والرسم البياني
     */
    public function index()
    {
        $lawyerId = Auth::id();
        $now = Carbon::now('Africa/Tripoli');

        // جلب بيانات المحامي للملف الشخصي
        $lawyer = User::with('lawyerProfile')->find($lawyerId);

        $currentBalance = 0;
        $thisMonthEarnings = 0;
        $weeklyEarnings = [0.0, 0.0, 0.0, 0.0]; // قيم افتراضية للأسابيع الأربعة

        // ===== الرصيد المتاح: نفس معادلة المحفظة (جدول transactions) =====
        try {
            $availableBalance = Transaction::where('user_id', $lawyerId)
                ->where('status', 'available')
                ->where('type', 'credit')
                ->sum('amount');

            $withdrawnBalance = Transaction::where('user_id', $lawyerId)
                ->whereIn('status', ['pending', 'completed'])
                ->where('type', 'debit')
                ->sum('amount');

            // مطابق تمامًا لـ $actualAvailable في LawyerWalletController
            $currentBalance = (float) ($availableBalance - $withdrawnBalance);
        } catch (\Exception $e) {
            Log::error("Mizan Wallet Balance Error: " . $e->getMessage());
        }

        // أرباح الشهر الحالي وحسابات الرسم البياني (تبقى من جدول payments)
        try {
            $lawyerConsultationIds = PaidConsultation::where('lawyer_id', $lawyerId)->pluck('id')->toArray();

            if (!empty($lawyerConsultationIds)) {
                // الكويري الأساسي للمدفوعات المكتملة المرتبطة باستشارات هذا المحامي
                $basePaymentsQuery = Payment::whereIn('consultation_id', $lawyerConsultationIds)
                    ->where('payment_status', 'completed');

                // أرباح الشهر الحالي
                $thisMonthEarnings = (float) $basePaymentsQuery->clone()
                    ->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->sum('amount');

                // ================= [حسابات الرسم البياني التراكمي بالأسبوع] =================
                // الأسبوع الرابع (منذ 28 يوماً إلى قبل 21 يوماً)
                $week4 = (float) $basePaymentsQuery->clone()
                    ->whereBetween('created_at', [$now->copy()->subDays(28), $now->copy()->subDays(21)])
                    ->sum('amount');

                // الأسبوع الثالث (منذ 21 يوماً إلى قبل 14 يوماً)
                $week3 = (float) $basePaymentsQuery->clone()
                    ->whereBetween('created_at', [$now->copy()->subDays(21), $now->copy()->subDays(14)])
                    ->sum('amount');

                // الأسبوع الثاني (منذ 14 يوماً إلى قبل 7 أيام)
                $week2 = (float) $basePaymentsQuery->clone()
                    ->whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(7)])
                    ->sum('amount');

                // الأسبوع الحالي (آخر 7 أيام)
                $week1 = (float) $basePaymentsQuery->clone()
                    ->where('created_at', '>=', $now->copy()->subDays(7))
                    ->sum('amount');

                // مصفوفة الأرباح متوافقة بالترتيب الزمني لملف الـ Blade الخاص بك
                $weeklyEarnings = [$week4, $week3, $week2, $week1];
                // ====================================================================
            }
        } catch (\Exception $e) {
            Log::error("Mizan Financials Error: " . $e->getMessage());
        }

        $activeCasesCount = LegalCase::where('lawyer_id', $lawyerId)->count();
        $upcomingSessionsCount = Appointment::where('lawyer_id', $lawyerId)->count();

        // جلب الاستشارات وتصنيفها
        try {
            $allConsultations = PaidConsultation::with('client')
                ->where('lawyer_id', $lawyerId)
                ->latest()
                ->get();

            // المتغيرات المطلوبة للتقسيم الثلاثي
            $pendingConsultations = $allConsultations->where('status', 'pending');
            $acceptedConsultations = $allConsultations->where('status', 'accepted');
            $rejectedConsultations = $allConsultations->where('status', 'rejected');

            // للحفاظ على عمل التصميم الحالي، نجعل incomingConsultations هي الطلبات المعلقة
            $incomingConsultations = $pendingConsultations;

        } catch (\Exception $e) {
            $incomingConsultations = $pendingConsultations = $acceptedConsultations = $rejectedConsultations = collect();
        }

        // جلب مواعيد اليوم
        try {
            $todayAppointments = Appointment::with('client')
                ->where('lawyer_id', $lawyerId)
                ->orderBy('date_time', 'asc')
                ->get();
        } catch (\Exception $e) {
            $todayAppointments = collect();
        }

        // جلب القضايا
        try {
            $latestCases = LegalCase::with('client')
                ->where('lawyer_id', $lawyerId)
                ->whereHas('client', function($q) {
                    $q->where('role', '!=', 'lawyer');
                })
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $latestCases = collect();
        }

        return view('lawyer.dashboard', compact(
            'lawyer',
            'currentBalance',
            'thisMonthEarnings',
            'activeCasesCount',
            'upcomingSessionsCount',
            'incomingConsultations',
            'pendingConsultations',
            'acceptedConsultations',
            'rejectedConsultations',
            'todayAppointments',
            'latestCases',
            'weeklyEarnings'
        ));
    }


    /**
     * دالة معالجة الاستشارات (قبول أو رفض)
     */
    public function reviewConsultation(Request $request, $id)
    {
        $consultation = PaidConsultation::where('id', $id)
                            ->where('lawyer_id', Auth::id())
                            ->firstOrFail();

        if ($request->status === 'accepted') {
            // قبول الطلب وتحديث السعر
            $request->validate(['price' => 'required|numeric|min:0']);
            $consultation->update(['status' => 'accepted', 'price' => $request->price]);
            return back()->with('success', 'تم اعتماد الاستشارة بنجاح.');
        }

        if ($request->status === 'rejected') {
            // رفض الطلب
            $consultation->update(['status' => 'rejected']);
            return back()->with('success', 'تم رفض الاستشارة.');
        }

        return back()->withErrors('حدث خطأ في المعالجة.');
    }
}