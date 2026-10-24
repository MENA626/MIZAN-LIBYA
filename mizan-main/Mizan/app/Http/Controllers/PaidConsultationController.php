<?php

namespace App\Http\Controllers;

use App\Models\PaidConsultation; // استدعاء الموديل الجديد الفريد المربوط بجدول paid_consultations
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaidConsultationController extends Controller
{
    /**
     * للعميل: عرض طلباته المدفوعة
     */
    public function index()
    { 
        $consultations = PaidConsultation::where('user_id', Auth::id())
            ->with('lawyer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('client.consultations', compact('consultations'));
    }

    /**
     * للعميل: حفظ الطلب الجديد القادم من البروفايل
     */
    public function store(Request $request)
    {
        $request->validate([
            'lawyer_id'         => 'required', 
            'title'             => 'required|string|max:255',
            'legal_category'    => 'required|string',
            'client_role'       => 'required|string',
            'opponent_name'     => 'nullable|string|max:255',
            'urgency_level'     => 'required|string',
            'has_existing_case' => 'required|string',
            'court_or_deadline' => 'nullable|string|max:255',
            'description'       => 'required|string',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status']  = 'pending';

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('consultations/attachments', 'public');
        }

        // 1. احفظي الاستشارة في متغير $consultation
        $consultation = PaidConsultation::create($data);

       // 2. الآن يمكنك استخدام $consultation->lawyer_id و $consultation->id
       DB::table('notifications')->insert([
         'user_id'    => $consultation->lawyer_id,
         'title'      => 'طلب استشارة جديد',
          'body'       => 'لديك طلب استشارة جديد من العميل ' . Auth::user()->name . '، بانتظار المراجعة والقبول.',
          'is_read'    => false,
          'created_at' => now(),
          'updated_at' => now()
           ]);
        
        

        return response()->json([
            'success' => true, 
            'message' => 'تم إرسال وقائع استشارتك بنجاح وجاري نقلها لجدول المحامي للتقييم.'
        ], 200);
    }

    /**
     * 🌟 للمحامي: عرض لوحة التحكم الرئيسية (Dashboard) مع الاستشارات الواردة
     */
    public function dashboard()
    {
        // جلب طلبات الاستشارة الجديدة الموجهة للمحامي الحالي والتي لا تزال قيد الانتظار مع بيانات العميل
        $incomingConsultations = PaidConsultation::where('lawyer_id', Auth::id())
            ->with('client')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5) // عرض آخر 5 طلبات فقط لجمالية التصميم البصري في الـ Dashboard
            ->get();

        return view('lawyer.dashboard', compact('incomingConsultations'));
    }

    /**
     * للمحامي: عرض شاشة الطلبات الواردة (الصفحة المنفصلة للأرشيف)
     */
    public function lawyerIndex()
    {
        $incomingConsultations = PaidConsultation::where('lawyer_id', Auth::id())
            ->with('client')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $archiveConsultations = PaidConsultation::where('lawyer_id', Auth::id())
            ->with('client')
            ->where('status', '!=', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('lawyer.consultations', compact('incomingConsultations', 'archiveConsultations'));
    }

    public function lawyerReview(Request $request, $id)
{
    // 1. التحقق من البيانات
    $request->validate([
        'status' => 'required|in:accepted,rejected',
        'price'  => 'required_if:status,accepted|numeric|min:0|nullable',
    ]);

    // 2. جلب الاستشارة والتأكد من الصلاحية
    $consultation = PaidConsultation::findOrFail($id);

    if ($consultation->lawyer_id !== Auth::id()) {
        return redirect()->back()->with('error', 'غير مصرح لك بتعديل هذه الاستشارة.');
    }

    // 3. تحديد الحالة والبيانات
    $isAccepted = ($request->status === 'accepted');
    
    $consultation->update([
        'status' => $isAccepted ? 'accepted' : 'rejected',
        'price'  => $isAccepted ? $request->price : null
    ]);

    // 4. إرسال الإشعار (بنفس تفاصيلك الأصلية)
    \Illuminate\Support\Facades\DB::table('notifications')->insert([
        'user_id'    => $consultation->user_id,
        'title'      => $isAccepted ? 'قبول طلب الاستشارة' : 'رفض طلب الاستشارة',
        'body'       => $isAccepted 
                        ? 'تم قبول طلب استشارتك من قبل المحامي ' . Auth::user()->name . '، بانتظار إتمام الدفع.' 
                        : 'نعتذر منك، تم رفض طلب استشارتك من قبل المحامي ' . Auth::user()->name . '.',
        'is_read'    => false,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // 5. الرسالة المرجعية (تم الحفاظ عليها كما هي)
    $message = $isAccepted 
        ? 'تم قبول الاستشارة وتحديد السعر بنجاح.' 
        : 'تم رفض طلب الاستشارة بنجاح وتم إخطار الموكل.';

    return redirect()->back()->with('success', $message);
}
}