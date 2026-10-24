<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Message;
use App\Models\PaidConsultation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LawyerChatController extends Controller
{
    public function index()
    {
        return view('lawyer.chat');
    }

    /**
     * 🔍 جلب المحادثات (تم إصلاح مشكلة تكرار "عميل" هنا)
     */
    public function getConversations()
    {
        // 🚀 الحل الجذري: whereHas('user') تمنع أي محادثة يتيمة أو بدون عميل من الظهور
        $sessions = ChatSession::where('lawyer_id', Auth::id())
            ->whereHas('user') 
            ->with(['user', 'chatable', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions->map(function ($session) {
                $clientName = $session->user->name; // أصبحنا متأكدين من وجود المستخدم
                
                return [
                    'id'                 => $session->id,
                    'client'             => [
                        'id'      => $session->user_id,
                        'name'    => $clientName,
                        'initial' => mb_substr($clientName, 0, 1, 'UTF-8'),
                        'avatar'  => $this->resolveAvatarUrl($session->user)
                    ],
                    'consultation_title' => $session->chatable->title ?? 'استشارة عامة',
                    'last_message'       => $session->lastMessage?->message_text,
                    'last_message_time'  => $session->updated_at?->format('H:i') ?? '',
                    'unread_count'       => 0,
                    'status'             => $session->status
                ];
            })
        ]);
    }

    /**
     * 💬 جلب رسائل الغرفة الحالية
     */
    public function getMessages($sessionId)
    {
        $session = ChatSession::where('id', $sessionId)
            ->where('lawyer_id', Auth::id())
            ->whereHas('user')
            ->with(['user', 'chatable'])
            ->firstOrFail();

        $messages = Message::where('chat_session_id', $sessionId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $messages->map(fn($msg) => $this->formatMessageData($msg)),
            'session' => [
                'id'                 => $session->id,
                'client_name'        => $session->user->name,
                'client_initial'     => mb_substr($session->user->name, 0, 1, 'UTF-8'),
                'client_avatar'      => $this->resolveAvatarUrl($session->user),
                'consultation_title' => $session->chatable->title ?? 'استشارة عامة',
                'status'             => $session->status
            ]
        ]);
    }

    /**
     * 📎 إرسال رسالة مع معالجة المرفقات
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'message'    => 'nullable|string|max:2000',
            'file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,doc,txt|max:10240'
        ]);

        $session = ChatSession::where('id', $request->session_id)
            ->where('lawyer_id', Auth::id())
            ->firstOrFail();

        if (in_array($session->status, ['completed', 'closed'])) {
            return response()->json(['success' => false, 'message' => 'هذه المحادثة مغلقة حالياً.'], 403);
        }

        if (!$request->hasFile('file') && empty(trim($request->message))) {
            return response()->json(['success' => false, 'message' => 'لا يمكن إرسال رسالة فارغة.'], 422);
        }

        $messageData = DB::transaction(function () use ($session, $request) {
            $attachmentData = $this->handleFileUpload($request, $session->id);
            
            $messageText = $request->message;
            if (empty(trim($messageText)) && $attachmentData) {
                $messageText = $this->generateAttachmentCaption($attachmentData['type'], $attachmentData['name']);
            }

            // 1. إنشاء الرسالة
            $msg = Message::create([
                'chat_session_id' => $session->id,
                'sender_id'       => Auth::id(),
                'message_text'    => $messageText,
                'attachment_path' => $attachmentData['path'] ?? null,
                'attachment_name' => $attachmentData['name'] ?? null,
                'attachment_type' => $attachmentData['type'] ?? null,
                'attachment_size' => $attachmentData['size'] ?? null,
                'is_read'         => false
            ]);

            $session->touch();

            // 2. إشعار العميل
            DB::table('notifications')->insert([
                'user_id'    => $session->user_id,
                'title'      => 'رسالة جديدة من المحامي',
                'body'       => 'لديك رسالة جديدة من المحامي ' . Auth::user()->name,
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $msg;
        });

        return response()->json([
            'success' => true,
            'message' => $this->formatMessageData($messageData, true)
        ]);
    }

/**
     * 🏁 إنهاء الاستشارة
     */
    public function endConsultation(Request $request)
    {
        $request->validate(['session_id' => 'required|exists:chat_sessions,id']);

        $session = ChatSession::where('id', $request->session_id)
            ->where('lawyer_id', Auth::id())
            ->firstOrFail();

        $lawyerName = Auth::user()->name;

        DB::transaction(function () use ($session, $lawyerName) {
            // تحديث حالة الاستشارة وتسجيل وقت الإغلاق (Closed At) لضمان الأرشفة
            if ($session->chatable_type === PaidConsultation::class && $session->chatable) {
                $session->chatable->update([
                    'status' => 'completed',
                    'closed_at' => now() 
                ]);
                
                // تحويل المعاملة المالية من محجوزة إلى متاحة
                Transaction::where('reference_type', PaidConsultation::class)
                    ->where('reference_id', $session->chatable->id)
                    ->where('status', 'escrow')
                    ->update(['status' => 'available']);
            }

            // إغلاق جلسة المحادثة
            $session->update(['status' => 'closed']);

            // إرسال إشعار للعميل
            DB::table('notifications')->insert([
                'user_id'    => $session->user_id, 
                'title'      => 'إنهاء الاستشارة',
                'body'       => 'تم إنهاء الاستشارة القانونية الخاصة بك من قبل المحامي: ' . $lawyerName,
                'is_read'    => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        return response()->json(['success' => true]);
    }

    /**
     * 👁️ معاينة الملف المرفق
     */
    public function previewAttachment($filePath)
    {
        $decodedPath = urldecode($filePath);
        $this->authorizeFileAccess($decodedPath);
        
        $extension = strtolower(pathinfo($decodedPath, PATHINFO_EXTENSION));
        $fileContent = Storage::disk('public')->get($decodedPath);
        
        $mimeType = match($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            'bmp'         => 'image/bmp',
            'pdf'         => 'application/pdf',
            default       => abort(415, 'نوع الملف غير مدعوم للمعاينة')
        };
        
        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('Content-Disposition', 'inline; filename="' . basename($decodedPath) . '"');
    }

    /**
     * ⬇️ تحميل ملف مرفق
     */
    public function downloadAttachment($filePath)
    {
        $decodedPath = urldecode($filePath);
        $this->authorizeFileAccess($decodedPath);
        
        $message = Message::where('attachment_path', $decodedPath)->first();
        $originalName = $message?->attachment_name ?? basename($decodedPath);
        
      // نقوم بتحويل المسار النسبي إلى المسار الكامل على السيرفر
$fullPath = storage_path('app/public/' . $decodedPath);

// نستخدم رد التحميل الخاص بلارافيل
return response()->download($fullPath, $originalName);
    }

    /* =========================================================
       🛠️ دوال مساعدة (Helper Methods) لتنظيف الكود
       ========================================================= */

    private function resolveAvatarUrl(?User $user): ?string
    {
        if (!$user || !$user->avatar) return null;
        return filter_var($user->avatar, FILTER_VALIDATE_URL) 
            ? $user->avatar 
            : asset('uploads/avatars/' . basename($user->avatar));
    }

    private function formatMessageData($msg, $isCurrentAuth = false): array
    {
        $hasAttachment = !empty($msg->attachment_path);
        return [
            'id'              => $msg->id,
            'sender_id'       => $msg->sender_id,
            'sender_name'     => $isCurrentAuth ? Auth::user()->name : ($msg->sender->name ?? 'غير معروف'),
            'message'         => $msg->message_text,
            'attachment_path' => $msg->attachment_path,
            'attachment_name' => $msg->attachment_name,
            'attachment_type' => $msg->attachment_type,
            'attachment_size' => $msg->formatted_attachment_size ?? null,
            'file_url'        => $hasAttachment ? route('lawyer.chat.preview', ['filePath' => urlencode($msg->attachment_path)]) : null,
            'download_url'    => $hasAttachment ? route('lawyer.chat.download', ['filePath' => urlencode($msg->attachment_path)]) : null,
            'time'            => $msg->created_at->format('H:i'),
            'is_user'         => $msg->sender_id === Auth::id(),
        ];
    }

    private function handleFileUpload(Request $request, $sessionId): ?array
    {
        if (!$request->hasFile('file')) return null;

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        
        return [
            'path' => $file->storeAs("chat_attachments/{$sessionId}", $fileName, 'public'),
            'name' => $file->getClientOriginalName(),
            'type' => $extension,
            'size' => $file->getSize()
        ];
    }

    private function generateAttachmentCaption(string $extension, string $originalName): string
    {
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return "📷 [صورة مرفقة: {$originalName}]";
        } elseif ($extension === 'pdf') {
            return "📄 [ملف PDF مرفق: {$originalName}]";
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return "📝 [مستند Word مرفق: {$originalName}]";
        }
        return "📎 [ملف مرفق: {$originalName}]";
    }

    private function authorizeFileAccess(string $decodedPath): void
    {
        if (!Storage::disk('public')->exists($decodedPath)) {
            abort(404, 'الملف غير موجود');
        }

        $pathParts = explode('/', $decodedPath);
        if (count($pathParts) >= 2 && $pathParts[0] === 'chat_attachments') {
            $sessionId = $pathParts[1];
            $hasAccess = ChatSession::where('id', $sessionId)
                ->where(function($query) {
                    $query->where('lawyer_id', Auth::id())
                          ->orWhere('user_id', Auth::id());
                })->exists();

            if (!$hasAccess) abort(403, 'غير مصرح لك بالوصول لهذا الملف');
        }
    }
}