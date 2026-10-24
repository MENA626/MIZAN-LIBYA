<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LegalCase;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. حساب الإحصائيات للأرقام التي تظهر في الصناديق العلوية
        $totalUsers = User::count();
        $totalLawyers = User::where('role', 'lawyer')->count();
        $pendingLawyers = User::where('role', 'lawyer')->where('status', 'pending')->count();
        $totalCases = LegalCase::count();
        
        // حساب إجمالي أرباح العمولات للمنصة (الذي يظهر فيه 131 د.ل)
        $totalCommissions = Transaction::where('type', 'commission')->sum('amount');
        
        // ================= [حسابات الرسم البياني] =================
        // حساب أرباح الأسبوع الحالي (آخر 7 أيام)
        $week1 = Transaction::where('type', 'commission')->where('created_at', '>=', now()->subDays(7))->sum('amount');

        // أرباح الأسبوع الماضي (من اليوم 8 إلى اليوم 14)
        $week2 = Transaction::where('type', 'commission')->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->sum('amount');

        // أرباح الأسبوع الثالث (من اليوم 15 إلى اليوم 21)
        $week3 = Transaction::where('type', 'commission')->whereBetween('created_at', [now()->subDays(21), now()->subDays(14)])->sum('amount');

        // أرباح الأسبوع الرابع (من اليوم 22 إلى اليوم 28)
        $week4 = Transaction::where('type', 'commission')->whereBetween('created_at', [now()->subDays(28), now()->subDays(21)])->sum('amount');
        // ==========================================================

        // 2. جلب آخر 5 طلبات توثيق للمحامين المعلقين
        $pendingVerifications = User::where('role', 'lawyer')
            ->where('status', 'pending')
            ->with('lawyerProfile')
            ->latest()
            ->take(3)
            ->get();
            
        // 3. جلب آخر 5 قضايا تم تسجيلها في النظام
        $latestCases = LegalCase::with(['lawyer'])->latest()->take(3)->get();

        // 4. تمرير وإرسال كل البيانات (بما فيها متغيرات الأسابيع للرسم البياني) إلى الـ View
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalLawyers', 
            'pendingLawyers', 
            'totalCases', 
            'totalCommissions', 
            'pendingVerifications', 
            'latestCases',
            'week1', // أرسلناها للـ Blade
            'week2', 
            'week3', 
            'week4'
        ));
    }
}
