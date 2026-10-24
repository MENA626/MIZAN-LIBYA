<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\CaseSession;
use App\Models\CaseUpdate;
use App\Models\CaseTask;
use App\Models\CaseDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LawyerCaseController extends Controller
{
    /**
     * عرض قائمة القضايا
     */
    public function index()
    {
        $cases = LegalCase::where('lawyer_id', auth()->id())
            ->with(['client', 'nextScheduledSession'])
            ->withCount(['sessions', 'pendingTasks', 'caseDocuments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // جلب أنواع القضايا من جدول case_types
        $categories = DB::table('case_types')->orderBy('name', 'asc')->get();

        return view('lawyer.my-cases', compact('cases', 'categories'));
    }

    /**
     * البحث عن موكل بالبريد الإلكتروني (AJAX)
     */
    public function searchClient(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $client = User::where('role', 'client')
            ->where('email', $request->email)
            ->select('id', 'name', 'phone', 'email')
            ->first();

        if ($client) {
            return response()->json(['success' => true, 'client' => $client]);
        }

        return response()->json(['success' => false, 'message' => 'لم يتم العثور على حساب بهذا البريد الإلكتروني.']);
    }

    /**
     * عرض تفاصيل قضية محددة
     */
    public function show($id)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())
            ->with([
                'client',
                'sessions' => function ($q) {
                    $q->orderBy('session_date', 'desc');
                },
                'updates' => function ($q) {
                    $q->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
                },
                'tasks' => function ($q) {
                    $q->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")
                      ->orderBy('is_completed', 'asc')
                      ->orderBy('due_date', 'asc');
                },
                'caseDocuments' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                },
            ])
            ->findOrFail($id);

        return view('lawyer.case-details', compact('case'));
    }

    /**
     * إضافة قضية جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name'   => 'required|string|max:255',
            'client_phone'  => 'required|string',
            'case_type'     => 'required|string|max:255',
            'status'        => 'required|string',
            'filing_date'   => 'required|date',
            'details'       => 'required|string',
            'client_user_id'  => 'nullable|exists:users,id',
            'case_category'   => 'nullable|string|max:255',
            'court_name'      => 'nullable|string|max:255',
            'opponent_name'   => 'nullable|string|max:255',
            'claim_amount'    => 'nullable|numeric|min:0',
            'total_fees'      => 'nullable|numeric|min:0',
            'paid_fees'       => 'nullable|numeric|min:0',
            'attachments'     => 'nullable|file|mimes:pdf,zip,jpeg,png,jpg|max:10240'
        ]);

        try {
            // توليد رقم القضية تلقائياً
            $nextId = DB::table('cases')->max('id') + 1;
            $caseNumber = 'C-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            $filePath = null;
            if ($request->hasFile('attachments')) {
                $filePath = $request->file('attachments')->store('cases_docs', 'public');
            }

            // تحديد user_id: إذا تم اختيار موكل مسجل يُستخدم معرفه، وإلا يُستخدم معرف المحامي
            $userId = $request->client_user_id ?: auth()->id();

            $legalCase = LegalCase::create([
                'case_number'    => $caseNumber,
                'user_id'        => $userId,
                'lawyer_id'      => auth()->id(),
                'title'          => $request->case_type . ' - ' . $request->client_name,
                'client_name'    => $request->client_name,
                'client_phone'   => $request->client_phone,
                'case_type'      => $request->case_type,
                'case_category'  => $request->case_category,
                'court_name'     => $request->court_name,
                'opponent_name'  => $request->opponent_name,
                'claim_amount'   => $request->claim_amount,
                'total_fees'     => $request->total_fees,
                'paid_fees'      => $request->paid_fees,
                'status'         => $request->status,
                'filing_date'    => $request->filing_date,
                'details'        => $request->details,
                'file_path'      => $filePath
            ]);
            

            // إشعار الموكل إذا كان مستخدماً مسجلاً
            if ($request->client_user_id) {
                DB::table('notifications')->insert([
                    'user_id'    => $request->client_user_id,
                    'title'      => 'قضية جديدة ',
                    'body'       => 'تم فتح قضية جديدة لك بواسطة المحامي ' . auth()->user()->name . ' برقم ' . $caseNumber,
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return redirect()->back()->with('success', ' تم إضافة وتقييد القضية الجديدة بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    /**
     * تعديل قضية موجودة
     */
    public function update(Request $request, $id)
    {
        $legalCase = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);

        $request->validate([
            'client_name'   => 'required|string|max:255',
            'client_phone'  => 'required|string',
            'case_type'     => 'required|string|max:255',
            'status'        => 'required|string',
            'filing_date'   => 'required|date',
            'details'       => 'required|string',
            'client_user_id'  => 'nullable|exists:users,id',
            'case_category'   => 'nullable|string|max:255',
            'court_name'      => 'nullable|string|max:255',
            'opponent_name'   => 'nullable|string|max:255',
            'claim_amount'    => 'nullable|numeric|min:0',
            'total_fees'      => 'nullable|numeric|min:0',
            'paid_fees'       => 'nullable|numeric|min:0',
            'attachments'     => 'nullable|file|mimes:pdf,zip,jpeg,png,jpg|max:10240'
        ]);

        try {
            $filePath = $legalCase->file_path;

            if ($request->hasFile('attachments')) {
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
                $filePath = $request->file('attachments')->store('cases_docs', 'public');
            }

            // تحديث user_id إذا تم ربط الموكل
            $userId = $request->client_user_id ?: $legalCase->user_id;

            $legalCase->update([
                'user_id'        => $userId,
                'client_name'    => $request->client_name,
                'client_phone'   => $request->client_phone,
                'case_type'      => $request->case_type,
                'case_category'  => $request->case_category,
                'court_name'     => $request->court_name,
                'opponent_name'  => $request->opponent_name,
                'claim_amount'   => $request->claim_amount,
                'total_fees'     => $request->total_fees,
                'paid_fees'      => $request->paid_fees,
                'status'         => $request->status,
                'filing_date'    => $request->filing_date,
                'details'        => $request->details,
                'file_path'      => $filePath
            ]);

            // إشعار الموكل
            if ($legalCase->user_id && $legalCase->user_id != auth()->id()) {
                DB::table('notifications')->insert([
                    'user_id'    => $legalCase->user_id,
                    'title'      => 'تحديث بيانات قضيتك',
                    'body'       => 'تم تحديث بيانات قضيتك رقم ' . $legalCase->case_number . ' بواسطة المحامي: ' . auth()->user()->name,
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return redirect()->back()->with('success', ' تم تعديل بيانات ملف القضية بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل: ' . $e->getMessage());
        }
    }

    /**
     * حذف قضية
     */
    public function destroy($id)
    {
        $legalCase = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);

        if ($legalCase->file_path) {
            Storage::disk('public')->delete($legalCase->file_path);
        }

        $legalCase->delete();
        return redirect()->back()->with('success', ' تم حذف القضية من السجلات بنجاح!');
    }

    // ========================================================
    //  إدارة جلسات القضية
    // ========================================================

    /**
     * إضافة جلسة جديدة
     */
    public function storeSession(Request $request, $id)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);

        $request->validate([
            'session_date'         => 'required|date',
            'session_type'         => 'required|string|max:255',
            'location'             => 'nullable|string|max:255',
            'summary'              => 'nullable|string',
            'our_attendance'       => 'nullable|boolean',
            'opponent_attendance'  => 'nullable|boolean',
            'expert_attendance'    => 'nullable|boolean',
            'opponent_claims'      => 'nullable|string',
            'our_response'         => 'nullable|string',
            'postponement_reason'  => 'nullable|string',
            'next_session_date'    => 'nullable|date',
            'next_session_goal'    => 'nullable|string',
            'status'               => 'required|string',
        ]);

        try {
            CaseSession::create([
                'case_id'              => $case->id,
                'session_date'         => $request->session_date,
                'session_type'         => $request->session_type,
                'location'             => $request->location,
                'summary'              => $request->summary,
                'our_attendance'       => $request->boolean('our_attendance', true),
                'opponent_attendance'  => $request->boolean('opponent_attendance', false),
                'expert_attendance'    => $request->has('expert_attendance') ? $request->boolean('expert_attendance') : null,
                'opponent_claims'      => $request->opponent_claims,
                'our_response'         => $request->our_response,
                'postponement_reason'  => $request->postponement_reason,
                'next_session_date'    => $request->next_session_date,
                'next_session_goal'    => $request->next_session_goal,
                'status'               => $request->status,
            ]);

            // إشعار الموكل بالجلسة الجديدة
            if ($case->user_id && $case->user_id != auth()->id()) {
                DB::table('notifications')->insert([
                    'user_id'    => $case->user_id,
                    'title'      => ' جلسة جديدة لقضيتك',
                    'body'       => 'قام المحامي ' . auth()->user()->name . ' بإضافة جلسة جديدة لقضيتك رقم #' . $case->case_number . '، يرجى المراجعة.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->back()->with('success', ' تم إضافة الجلسة بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تعديل جلسة
     */
    public function updateSession(Request $request, $id, $sessionId)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);
        $session = CaseSession::where('case_id', $case->id)->findOrFail($sessionId);

        $request->validate([
            'session_date'         => 'required|date',
            'session_type'         => 'required|string|max:255',
            'location'             => 'nullable|string|max:255',
            'summary'              => 'nullable|string',
            'our_attendance'       => 'nullable|boolean',
            'opponent_attendance'  => 'nullable|boolean',
            'expert_attendance'    => 'nullable|boolean',
            'opponent_claims'      => 'nullable|string',
            'our_response'         => 'nullable|string',
            'postponement_reason'  => 'nullable|string',
            'next_session_date'    => 'nullable|date',
            'next_session_goal'    => 'nullable|string',
            'status'               => 'required|string',
        ]);

        try {
            $session->update([
                'session_date'         => $request->session_date,
                'session_type'         => $request->session_type,
                'location'             => $request->location,
                'summary'              => $request->summary,
                'our_attendance'       => $request->boolean('our_attendance', true),
                'opponent_attendance'  => $request->boolean('opponent_attendance', false),
                'expert_attendance'    => $request->has('expert_attendance') ? $request->boolean('expert_attendance') : null,
                'opponent_claims'      => $request->opponent_claims,
                'our_response'         => $request->our_response,
                'postponement_reason'  => $request->postponement_reason,
                'next_session_date'    => $request->next_session_date,
                'next_session_goal'    => $request->next_session_goal,
                'status'               => $request->status,
            ]);

            return redirect()->back()->with('success', ' تم تعديل الجلسة بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // ========================================================
    //  تنبيهات القضية
    // ========================================================

    /**
     * إضافة تنبيه جديد على القضية
     */
    public function storeUpdate(Request $request, $id)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'update_type' => 'required|string|in:info,warning,urgent,court_update',
            'is_pinned'   => 'nullable|boolean',
        ]);

        try {
            CaseUpdate::create([
                'case_id'     => $case->id,
                'lawyer_id'   => auth()->id(),
                'title'       => $request->title,
                'content'     => $request->content,
                'update_type' => $request->update_type,
                'is_pinned'   => $request->boolean('is_pinned', false),
            ]);

            // إشعار الموكل
            if ($case->user_id && $case->user_id != auth()->id()) {
                
                // 1. تحديد الأيقونة فقط
                $icon = match($request->update_type) {
                    'urgent'       => '🔴',
                    'warning'      => '⚠️',
                    'court_update' => '⚖️',
                    default        => 'ℹ️',
                };

                // 2. الصيغة المطلوبة كما طلبتِ تماماً:
                // "قام المحامي (الاسم) باضافة تنبيه جديد (الأيقونة) لقضيتك (الرقم), يرجى المراجعة"
                $body = 'قام المحامي ' . auth()->user()->name . ' بإضافة تنبيه جديد ' . $icon . ' لقضيتك رقم #' . $case->case_number . '، يرجى المراجعة.';

                DB::table('notifications')->insert([
                    'user_id'    => $case->user_id,
                    'title'      => ' تنبيه جديد لقضيتك', // العنوان كما طلبتِ
                    'body'       => $body,
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->back()->with('success', ' تم إضافة التنبيه بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // ========================================================
    //  مهام الموكل
    // ========================================================

    /**
     * إضافة مهمة جديدة للموكل
     */
    public function storeTask(Request $request, $id)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date',
            'priority'    => 'required|string|in:normal,urgent',
        ]);

        try {
            CaseTask::create([
                'case_id'     => $case->id,
                'title'       => $request->title,
                'description' => $request->description,
                'due_date'    => $request->due_date,
                'priority'    => $request->priority,
            ]);

            // إشعار الموكل بمهمة جديدة
if ($case->user_id && $case->user_id != auth()->id()) {

    // 1. تحديد الأيقونة حسب الأولوية
    $icon = ($request->priority === 'urgent') ? '🔴' : '📋';

    // 2. الصيغة المطلوبة: المحامي أضاف مهمة جديدة (الأيقونة) لقضيتك (الرقم)
    $body = 'قام المحامي ' . auth()->user()->name . ' بإضافة مهمة جديدة ' . $icon . ' لقضيتك رقم #' . $case->case_number . '، يرجى المراجعة.';

    DB::table('notifications')->insert([
        'user_id'    => $case->user_id,
        'title'      => ' مهمة جديدة لقضيتك', // عنوان ثابت وموحد
        'body'       => $body,
        'is_read'    => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

            return redirect()->back()->with('success', ' تم إضافة المهمة بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // ========================================================
    //  مستندات القضية
    // ========================================================

    /**
     * رفع مستند جديد للقضية
     */
    public function storeDocument(Request $request, $id)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);

        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpeg,png,jpg,zip|max:10240',
            'document_name' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:255',
            'description'   => 'nullable|string',
        ]);

        try {
            $filePath = $request->file('document_file')->store('case_documents', 'public');
            $originalName = $request->document_name ?: $request->file('document_file')->getClientOriginalName();

            CaseDocument::create([
                'case_id'       => $case->id,
                'sender_id'     => auth()->id(),
                'receiver_id'   => $case->user_id,
                'file_path'     => $filePath,
                'document_name' => $originalName,
                'document_type' => $request->document_type,
                'description'   => $request->description,
            ]);

            // إشعار الموكل بمستند جديد
if ($case->user_id && $case->user_id != auth()->id()) {

    // الصيغة الموحدة (بدون تفاصيل اسم المستند في الإشعار)
    $body = 'قام المحامي ' . auth()->user()->name . ' بإضافة مستند جديد لقضيتك رقم #' . $case->case_number . '، يرجى المراجعة.';

    DB::table('notifications')->insert([
        'user_id'    => $case->user_id,
        'title'      => ' مستند جديد لقضيتك', // عنوان موحد
        'body'       => $body,
        'is_read'    => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

            return redirect()->back()->with('success', ' تم رفع المستند بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    /**
     * حذف جلسة
     */
    public function destroySession($id, $sessionId)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);
        $session = CaseSession::where('case_id', $case->id)->findOrFail($sessionId);
        $session->delete();

        return redirect()->back()->with('success', ' تم حذف الجلسة بنجاح!');
    }

    /**
     * حذف تحديث
     */
    public function destroyUpdate($id, $updateId)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);
        $update = CaseUpdate::where('case_id', $case->id)->findOrFail($updateId);
        $update->delete();

        return redirect()->back()->with('success', ' تم حذف التنبيه بنجاح!');
    }

    /**
     * حذف مهمة
     */
    public function destroyTask($id, $taskId)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);
        $task = CaseTask::where('case_id', $case->id)->findOrFail($taskId);
        $task->delete();

        return redirect()->back()->with('success', ' تم حذف المهمة بنجاح!');
    }

    /**
     * حذف مستند
     */
    public function destroyDocument($id, $docId)
    {
        $case = LegalCase::where('lawyer_id', auth()->id())->findOrFail($id);
        $doc = CaseDocument::where('case_id', $case->id)->findOrFail($docId);

        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $doc->delete();

        return redirect()->back()->with('success', ' تم حذف المستند بنجاح!');
    }
}