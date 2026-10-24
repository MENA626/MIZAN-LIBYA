<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistantAnalytic;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * عرض لوحة إحصائيات المساعد الذكي
     */
    public function index()
    {
        // 1. جلب الأسئلة الحرفية الأكثر تكراراً باستخدام دالة سوبابيز
        // الدالة ترجع: question, total_repetitions, category
        $repeatedQuestions = DB::select('SELECT * FROM get_most_repeated_questions()');

        // 2. جلب توزيع أنواع الأسئلة للرسم البياني الدائري أو الكروت
        // الدالة ترجع: category, total_queries
        $categoriesData = DB::select('SELECT * FROM get_categories_distribution()');

        // 3. جلب إجمالي عدد الاستشارات التي تم تقديمها
        $totalQueries = AssistantAnalytic::count();

        // إرسال البيانات إلى صفحة العرض (Blade View)
        return view('admin.analytics.index', compact(
            'repeatedQuestions', 
            'categoriesData', 
            'totalQueries'
        ));
    }
}