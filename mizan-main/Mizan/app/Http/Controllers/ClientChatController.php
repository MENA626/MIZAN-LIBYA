<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Message;
use App\Models\User;
use App\Models\PaidConsultation;
use App\Models\Transaction;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ClientChatController extends Controller
{
    /**
     * عرض واجهة الشات الرئيسية للعميل
     */
    public function index()
    {
        $consultations = PaidConsultation::where('user_id', Auth::id())
            ->whereIn('status', ['paid', 'active', 'accepted', 'pending', 'rejected', 'completed', 'closed'])
            ->with('lawyer')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('client.consultations', compact('consultations'));
    }

    /**
     * 🔍 جلب المحادثات مع دعم البحث
     */
    public function getConversations(Request $request)
    {
        $search = $request->query('search');

        $query = ChatSession::where('user_id', Auth::id())
            ->where('chatable_type', PaidConsultation::class)
            ->with(['lawyer', 'chatable', 'lastMessage']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHasMorph('chatable', [PaidConsultation::class], function ($subQ) use ($search) {
                    $subQ->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('lawyer', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $sessions = $query->orderBy('updated_at', 'desc')->get();
        $result = [];

        foreach ($sessions as $session) {
            $lawyerName = $session->lawyer->name ?? 'محامي';
            $title = optional($session->chatable)->title ?? 'استشارة مدفوعة';

            $result[] = [
                'id' => $session->id,
                'lawyer' => [
                    'id' => $session->lawyer_id,
                    'name' => $lawyerName,
                    'initial' => mb_substr($lawyerName, 0, 1, 'utf-8')
                ],
                'consultation_title' => $title,
                'last_message' => $session->lastMessage->message_text ?? null,
                'last_message_time' => $session->updated_at ? $session->updated_at->format('H:i') : '',
                'unread_count' => 0
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * بدء أو فتح غرفة المحادثة
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:paid_consultations,id'
        ]);

        $consultation = PaidConsultation::where('id', $request->consultation_id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['paid', 'active', 'completed', 'closed'])
            ->first();

        if (!$consultation) {
            return response()->json([
                'success' => false,
                'message' => 'الاستشارة غير متاحة للمحادثة حالياً.'
            ]);
        }

        $session = ChatSession::where('chatable_type', PaidConsultation::class)
            ->where('chatable_id', $consultation->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            if ($consultation->status === 'completed' || $consultation->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إنشاء غرفة محادثة جديدة لاستشارة منتهية أو مغلقة.'
                ]);
            }

            $session = ChatSession::create([
                'lawyer_id' => $consultation->lawyer_id,
                'user_id' => Auth::id(),
                'chatable_type' => PaidConsultation::class,
                'chatable_id' => $consultation->id,
                'status' => 'active'
            ]);
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $session->id,
            'session' => [
                'status' => $session->status
            ]
        ]);
    }

    /**
     * جلب رسائل الغرفة الحالية مع دعم المرفقات
     */
    public function getMessages($sessionId)
    {
        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $messages = Message::where('chat_session_id', $sessionId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? 'غير معروف',
                'message' => $msg->message_text,
                'attachment_path' => $msg->attachment_path,
                'attachment_name' => $msg->attachment_name,
                'attachment_type' => $msg->attachment_type,
                'attachment_size' => $msg->formatted_attachment_size,
                'file_url' => $msg->attachment_path ? route('chat.preview', ['filePath' => urlencode($msg->attachment_path)]) : null,
                'download_url' => $msg->attachment_path ? route('chat.download', ['filePath' => urlencode($msg->attachment_path)]) : null,
                'time' => $msg->created_at->format('H:i'),
                'is_user' => $msg->sender_id === Auth::id(),
            ];
        });

        $title = optional($session->chatable)->title ?? 'استشارة مدفوعة';

        return response()->json([
            'success' => true,
            'data' => $formattedMessages,
            'session' => [
                'id' => $session->id,
                'lawyer_name' => $session->lawyer->name ?? 'محامي',
                'lawyer_initial' => $session->lawyer ? mb_substr($session->lawyer->name, 0, 1, 'utf-8') : 'م',
                'consultation_title' => $title
            ]
        ]);
    }

    /**
     * 📎 إرسال رسالة مع معالجة المرفقات
     */
    public function sendMessage(Request $request)
    {
        Log::info('sendMessage called', [
            'has_file' => $request->hasFile('file'),
            'session_id' => $request->session_id,
            'message' => $request->message
        ]);

        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'message' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,doc,txt|max:10240'
        ]);

        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($session->status === 'completed' || $session->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'هذه المحادثة مغلقة حالياً.'], 403);
        }

        if (!$request->hasFile('file') && empty(trim($request->message))) {
            return response()->json(['success' => false, 'message' => 'لا يمكن إرسال رسالة فارغة.'], 422);
        }

        $messageData = DB::transaction(function () use ($session, $request) {
            $attachmentPath = null;
            $attachmentName = null;
            $attachmentType = null;
            $attachmentSize = null;
            $messageText = $request->message;

            // معالجة الملف المرفق
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                
                // إنشاء اسم فريد للملف
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $attachmentPath = $file->storeAs('chat_attachments/' . $session->id, $fileName, 'public');
                $attachmentName = $originalName;
                $attachmentType = $extension;
                $attachmentSize = $file->getSize();

                Log::info('File uploaded', [
                    'path' => $attachmentPath,
                    'name' => $attachmentName,
                    'type' => $attachmentType,
                    'size' => $attachmentSize
                ]);

                if (empty(trim($messageText))) {
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $messageText = "📷 [صورة مرفقة: {$originalName}]";
                    } elseif ($extension === 'pdf') {
                        $messageText = "📄 [ملف PDF مرفق: {$originalName}]";
                    } elseif (in_array($extension, ['doc', 'docx'])) {
                        $messageText = "📝 [مستند Word مرفق: {$originalName}]";
                    } else {
                        $messageText = "📎 [ملف مرفق: {$originalName}]";
                    }
                }
            }

            // إنشاء الرسالة
            $msg = Message::create([
                'chat_session_id' => $session->id,
                'sender_id' => Auth::id(),
                'message_text' => $messageText,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
                'attachment_size' => $attachmentSize,
                'is_read' => false
            ]);

            Log::info('Message saved', ['message_id' => $msg->id, 'attachment_path' => $msg->attachment_path]);

            $session->update(['updated_at' => now()]);

            // إشعار للمحامي
            DB::table('notifications')->insert([
                'user_id'    => $session->lawyer_id, 
                'title'      => 'رسالة جديدة من العميل',
                'body'       => 'لديك رسالة جديدة من العميل ' . Auth::user()->name,
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $msg;
        });

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $messageData->id,
                'message' => $messageData->message_text,
                'attachment_path' => $messageData->attachment_path,
                'attachment_name' => $messageData->attachment_name,
                'attachment_type' => $messageData->attachment_type,
                'attachment_size' => $messageData->formatted_attachment_size,
                'file_url' => $messageData->attachment_path ? route('chat.preview', ['filePath' => urlencode($messageData->attachment_path)]) : null,
                'download_url' => $messageData->attachment_path ? route('chat.download', ['filePath' => urlencode($messageData->attachment_path)]) : null,
                'time' => date('H:i', strtotime($messageData->created_at)),
                'is_user' => true,
                'sender_name' => Auth::user()->name
            ]
        ]);
    }

    /**
     * الفحص الأمني للجلسة
     */
    public function checkStatus($sessionId)
    {
        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'الجلسة غير موجودة.'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $session->status
        ]);
    }

    /**
     * إغلاق الاستشارة بدون تقييم
     */
    public function closeWithoutFeedback(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id'
        ]);

        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        DB::transaction(function () use ($session) {
            $session->update([
                'is_satisfied' => true,
                'rating' => null,
                'status' => 'completed'
            ]);

            $consultation = $session->chatable;
            if ($consultation instanceof PaidConsultation) {
                $consultation->update(['status' => 'completed']);
                
                Transaction::where('reference_type', PaidConsultation::class)
                    ->where('reference_id', $consultation->id)
                    ->where('status', 'escrow')
                    ->update(['status' => 'available']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'تم إكمال الاستشارة بنجاح'
        ]);
    }

 /**
 * تقديم بلاغ عن مشكلة
 */
public function reportDispute(Request $request)
{
    $request->validate([
        'session_id' => 'required|exists:chat_sessions,id',
        'reason' => 'required|string|min:10'
    ]);

    $session = ChatSession::where('id', $request->session_id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // التحقق من حالة الجلسة لمنع تقديم أكثر من بلاغ أو البلاغ بعد الإغلاق
    if ($session->status === 'completed' || $session->status === 'disputed') {
        return response()->json([
            'success' => false, 
            'message' => 'لا يمكن تقديم بلاغ، الاستشارة مغلقة أو تم تقديم بلاغ مسبقاً.'
        ], 403);
    }

    $consultation = $session->chatable;

    DB::transaction(function () use ($session, $consultation, $request) {
        
        // تحديث حالة الاستشارة والمعاملة المالية
        if ($consultation instanceof PaidConsultation) {
            $consultation->update(['status' => 'disputed']);
            
            Transaction::where('reference_type', PaidConsultation::class)
                ->where('reference_id', $consultation->id)
                ->update(['status' => 'frozen']);
        }

        // تحديث حالة الجلسة
        $session->update([
            'is_satisfied' => false,
            'status' => 'disputed'
        ]);

        // تسجيل البلاغ في قاعدة البيانات
        Dispute::create([
            'user_id' => Auth::id(),
            'lawyer_id' => $session->lawyer_id,
            'disputable_type' => PaidConsultation::class,
            'disputable_id' => $consultation->id,
            'reason' => $request->reason,
            'status' => 'open'
        ]);

        // إرسال إشعارات لجميع المديرين بكفاءة عالية
        $adminIds = User::where('role', 'admin')->pluck('id');
        $notifications = [];

        foreach ($adminIds as $adminId) {
            $notifications[] = [
                'user_id' => $adminId,
                'title' => 'بلاغ جديد من عميل',
                'body' => 'قام العميل ' . Auth::user()->name . ' بتقديم بلاغ على الاستشارة رقم #' . $consultation->id,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }
    });

    return response()->json([
        'success' => true,
        'message' => 'تم إرسال البلاغ بنجاح'
    ]);
}

    /**
     * حفظ تقييم المحامي
     */
    public function rateLawyer(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $consultation = $session->chatable;

        DB::transaction(function () use ($session, $consultation, $request) {
            
            $session->update([
                'is_satisfied' => true,
                'rating' => $request->rating,
                'rating_comment' => $request->comment,
                'status' => 'completed'
            ]);

            if ($consultation instanceof PaidConsultation) {
                $consultation->update(['status' => 'completed']);
                
                Transaction::where('reference_type', PaidConsultation::class)
                    ->where('reference_id', $consultation->id)
                    ->where('status', 'escrow')
                    ->update(['status' => 'available']);
            }

            $lawyer = User::find($session->lawyer_id);
            if ($lawyer) {
                $totalRatings = ChatSession::where('lawyer_id', $session->lawyer_id)
                    ->whereNotNull('rating')
                    ->count();
                
                $currentTotal = ChatSession::where('lawyer_id', $session->lawyer_id)
                    ->whereNotNull('rating')
                    ->sum('rating');
                
                $newRating = ($currentTotal + $request->rating) / ($totalRatings + 1);
                
                $lawyer->update([
                    'rating' => round($newRating, 2)
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ تقييمك بنجاح'
        ]);
    }

    /**
     * نافذة الرضا والتقييم النهائي
     */
    public function submitSatisfaction(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'satisfied' => 'required|boolean',
            'rating' => 'nullable|integer|min:0|max:5',
            'dispute_reason' => 'nullable|string|min:8'
        ]);

        $session = ChatSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $consultation = $session->chatable;

        DB::transaction(function () use ($session, $consultation, $request) {
            
            if ($request->satisfied) {
                
                if ($request->rating && $request->rating > 0) {
                    $lawyer = User::find($session->lawyer_id);
                    if ($lawyer) {
                        $currentRating = $lawyer->rating ?? 0;
                        $totalRatingsCount = DB::table('chat_sessions')
                            ->where('lawyer_id', $session->lawyer_id)
                            ->whereNotNull('rating')
                            ->count();

                        $newRating = (($currentRating * $totalRatingsCount) + $request->rating) / ($totalRatingsCount + 1);
                        
                        $lawyer->update([
                            'rating' => round($newRating, 2)
                        ]);
                    }
                }

                $session->update([
                    'is_satisfied' => true,
                    'rating' => $request->rating > 0 ? $request->rating : null,
                    'status' => 'completed'
                ]);

                if ($consultation instanceof PaidConsultation) {
                    $consultation->update(['status' => 'completed']);
                    
                    Transaction::where('reference_type', PaidConsultation::class)
                        ->where('reference_id', $consultation->id)
                        ->where('status', 'escrow')
                        ->update(['status' => 'available']);
                }

            } else {
                
                if ($consultation instanceof PaidConsultation) {
                    $consultation->update(['status' => 'closed']);
                    
                    Transaction::where('reference_type', PaidConsultation::class)
                        ->where('reference_id', $consultation->id)
                        ->update(['status' => 'frozen']);
                }

                $session->update([
                    'is_satisfied' => false,
                    'status' => 'closed'
                ]);

                Dispute::create([
                    'user_id' => Auth::id(),
                    'lawyer_id' => $session->lawyer_id,
                    'disputable_type' => PaidConsultation::class,
                    'disputable_id' => $consultation->id,
                    'reason' => $request->dispute_reason,
                    'status' => 'open'
                ]);

                $admins = User::where('role', 'admin')->get();
                $notifications = [];
                foreach ($admins as $admin) {
                    $notifications[] = [
                        'user_id' => $admin->id,
                        'title' => 'بلاغ نزاع جديد',
                        'body' => 'قام العميل ' . Auth::user()->name . ' بتقديم بلاغ شكوى رسمي ضد المحامي ' . $consultation->lawyer->name . ' بخصوص الاستشارة رقم #' . $consultation->id,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                if (!empty($notifications)) {
                    DB::table('notifications')->insert($notifications);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل ردك بنجاح'
        ]);
    }

    /**
     * جلب قائمة المحامين النشطين
     */
    public function getAvailableLawyers()
    {
        $lawyers = User::where('role', 'lawyer')
            ->where('is_active', true)
            ->get()
            ->map(function ($lawyer) {
                return [
                    'id' => $lawyer->id,
                    'name' => $lawyer->name,
                    'initial' => mb_substr($lawyer->name, 0, 1, 'utf-8'),
                    'email' => $lawyer->email,
                    'rating' => $lawyer->rating ?? '0.0'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $lawyers
        ]);
    }

    /**
     * 📎 تنزيل ملف مرفق
     */
    public function downloadAttachment($filePath)
    {
        $decodedPath = urldecode($filePath);
        
        if (!Storage::disk('public')->exists($decodedPath)) {
            abort(404, 'الملف غير موجود');
        }
        
        $pathParts = explode('/', $decodedPath);
        if (count($pathParts) >= 2 && $pathParts[0] === 'chat_attachments') {
            $sessionId = $pathParts[1];
            
            $session = ChatSession::where('id', $sessionId)
                ->where(function($query) {
                    $query->where('user_id', Auth::id())
                          ->orWhere('lawyer_id', Auth::id());
                })
                ->first();
                
            if (!$session) {
                abort(403, 'غير مصرح لك بتحميل هذا الملف');
            }
        }
        
        $originalName = basename($decodedPath);
        $message = Message::where('attachment_path', $decodedPath)->first();
        if ($message && $message->attachment_name) {
            $originalName = $message->attachment_name;
        }
        
   // استخدمي response()->download مع مسار الملف الحقيقي على القرص
return response()->download(storage_path('app/public/' . $decodedPath), $originalName);
    }
    
    /**
     * 👁️ معاينة الملف المرفق
     */
    public function previewAttachment($filePath)
    {
        $decodedPath = urldecode($filePath);
        
        if (!Storage::disk('public')->exists($decodedPath)) {
            abort(404, 'الملف غير موجود');
        }
        
        $pathParts = explode('/', $decodedPath);
        if (count($pathParts) >= 2 && $pathParts[0] === 'chat_attachments') {
            $sessionId = $pathParts[1];
            
            $session = ChatSession::where('id', $sessionId)
                ->where(function($query) {
                    $query->where('user_id', Auth::id())
                          ->orWhere('lawyer_id', Auth::id());
                })
                ->first();
                
            if (!$session) {
                abort(403, 'غير مصرح لك بمشاهدة هذا الملف');
            }
        }
        
        $extension = strtolower(pathinfo($decodedPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf'
        ];
        
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        $fileContent = Storage::disk('public')->get($decodedPath);
        
        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($decodedPath) . '"');
    }
}