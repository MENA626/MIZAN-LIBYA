<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Models\CaseTask;
use App\Models\CaseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientCaseController extends Controller
{
    /**
     * عرض قائمة قضايا العميل المسجل
     */
    public function index()
    {
        $cases = LegalCase::where('user_id', auth()->id())
            ->where('user_id', '!=', \DB::raw('lawyer_id')) // استبعاد القضايا التي المحامي = الموكل (قضايا قديمة)
            ->with(['lawyer', 'nextScheduledSession'])
            ->withCount(['sessions', 'pendingTasks'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.my-case', compact('cases'));
    }

    /**
     * عرض تفاصيل قضية محددة للعميل
     */
    public function show($id)
    {
        $case = LegalCase::where('user_id', auth()->id())
            ->with([
                'lawyer',
                'lawyer.lawyerProfile',
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

        // آخر جلسة مكتملة للتقرير المفصل
        $latestSession = $case->sessions->where('status', 'completed')->first();
        
        // الجلسة القادمة
        $nextSession = $case->sessions
            ->where('status', 'scheduled')
            ->where('session_date', '>=', now())
            ->sortBy('session_date')
            ->first();

        // التحديثات العاجلة
        $urgentUpdates = $case->updates->whereIn('update_type', ['urgent', 'warning', 'court_update']);

        return view('client.case-details', compact('case', 'latestSession', 'nextSession', 'urgentUpdates'));
    }

    public function toggleTask(Request $request, $id)
{
    $task = CaseTask::findOrFail($id);
    
    // منع التعديل إذا كانت مكتملة مسبقاً (حماية إضافية)
    if ($task->is_completed) {
        return response()->json(['success' => false, 'message' => 'المهمة مكتملة بالفعل ولا يمكن تعديلها']);
    }

    $case = LegalCase::where('user_id', auth()->id())->findOrFail($task->case_id);
    
    // تحديث الحالة دائماً إلى مكتمل
    $task->is_completed = true;
    $task->save();

    // إرسال إشعار للمحامي (مكتمل فقط)
    \DB::table('notifications')->insert([
        'user_id'    => $case->lawyer_id,
        'title'      => 'مهمة مكتملة', 
        'body' => 'قام الموكل ' . auth()->user()->name . ' بإكمال المهمة: ' . $task->title . ' في القضية رقم ' . $case->case_number,
        'is_read'    => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'is_completed' => true,
        'message' => 'تم إكمال المهمة بنجاح'
    ]);
}
    /**
     * تحميل مستند من القضية
     */
    public function downloadDocument($id)
    {
        $document = CaseDocument::findOrFail($id);
        
        // التحقق من أن المستند تابع لقضية العميل
        LegalCase::where('user_id', auth()->id())->findOrFail($document->case_id);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'الملف غير موجود أو تم حذفه.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->document_name ?? basename($document->file_path)
        );
    }
}
