<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين مع معالجة البحث المتقدم والفلترة والترقيم (يدعم JSON و Web)
     */
    public function index(Request $request)
    {
        // حماية هيلكية: استبعاد حسابات الأدمن تلقائياً من الإدارة العامة لتجنب الحظر الذاتي بالخطأ
        $query = User::where('role', '!=', 'admin');

        // 🔍 1. معالجة البحث التراكمي الفوري بالاسم أو البريد الإلكتروني
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        // 🎭 2. فلترة الأدوار (موكل / محامي)
        if ($request->filled('role') && $request->role !== 'جميع الأدوار') {
            $query->where('role', $request->role);
        }

        // 🚦 3. فلترة الحالات المعتمدة في النظام (approved, pending, rejected)
        if ($request->filled('status') && $request->status !== 'جميع الحالات') {
            $query->where('status', $request->status);
        }

        // جلب البيانات مقسمة لصفحات مع الترتيب التنازلي الأحدث أولاً
        $users = $query->latest()->paginate(10);

        // 📊 [تحسين أداء صاروخي]: حساب كل الإحصائيات باستعلام مجمع واحد بدلاً من 3 استعلامات منفصلة
        $stats = User::selectRaw("
            COUNT(CASE WHEN role != 'admin' THEN 1 END) as total_users,
            COUNT(CASE WHEN role = 'client' THEN 1 END) as total_clients,
            COUNT(CASE WHEN role = 'lawyer' THEN 1 END) as total_lawyers
        ")->first();

        $totalUsers   = $stats->total_users ?? 0;
        $totalClients = $stats->total_clients ?? 0;
        $totalLawyers = $stats->total_lawyers ?? 0;

        // ⚡ [هنا الفصل التام للباك إند]: إذا كان الطلب AJAX أرسل البيانات كـ JSON صافي للفرونت إند
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'users' => $users->items(),
                'pagination' => (string) $users->appends($request->all())->links(), // روابط التنقل متضمنة الفلاتر الحالية
                'stats' => [
                    'totalUsers' => $totalUsers,
                    'totalClients' => $totalClients,
                    'totalLawyers' => $totalLawyers,
                ]
            ]);
        }

        // التحميل الأول والتقليدي للموقع (في حال تم الدخول بدون جافا سكريبت)
        return view('admin.users', compact('users', 'totalUsers', 'totalClients', 'totalLawyers'));
    }

    /**
     * 📝 تحديث بيانات حساب المستخدم عبر النافذة المنبثقة (Modal)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // التحقق الآمن لضمان عدم تكرار البريد واستخدام الحالات المعتمدة في النظام
        $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'   => ['required', Rule::in(['client', 'lawyer'])],
            'status' => ['required', Rule::in(['approved', 'pending', 'rejected'])],
        ]);

        // تنفيذ التحديث الفعلي
        $user->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $request->role,
            'status' => $request->status,
        ]);

        $message = "تم تحديث بيانات المستخدم ({$user->name}) بنجاح.";

        // إذا كان التحديث مبعوثاً عبر الفرونت إند المفصل (AJAX)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user'    => $user
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * 🚫 حظر أو إلغاء حظر الحساب بشكل فوري (Toggle Block)
     */
    public function toggleBlock(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // التبديل بين حالتي الحظر الفعلي والاعتماد والمطابق تماماً للموديل وقاعدة البيانات
        if ($user->status === 'rejected') {
            $user->status = 'approved';
            $message = "تم إلغاء حظر حساب المستخدم ({$user->name}) بنجاح وإعادته لوضع النشاط.";
        } else {
            $user->status = 'rejected';
            $message = "تم حظر حساب المستخدم ({$user->name}) بنجاح وتقييد وصوله للمنصة.";
        }

        $user->save();

        // إذا كان طلب الحظر مبعوثاً عبر الفرونت إند المفصل (AJAX)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status'  => $user->status
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}