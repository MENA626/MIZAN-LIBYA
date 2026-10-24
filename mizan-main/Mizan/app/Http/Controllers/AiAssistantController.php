<?php

namespace App\Http\Controllers;

use App\Models\AiLegalBrief;
use App\Models\AiBriefMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    /**
     * 1. عرض صفحة المساعد المشتركة
     */
    public function index()
    {
        return view('shared.ai-assistant');
    }

    /**
     * 2. جلب سجل الاستشارات (العناوين فقط ليكون خفيفاً وسريعاً جداً)
     */
    public function getConversations(Request $request)
    {
        // التقاط رابط الصفحة الحالية للمستخدم لضمان تحديد نوع النظام بدقة (client, lawyer, admin)
        $referer = $request->header('referer');
        $systemType = 'client';
        
        if (str_contains($referer, '/lawyer')) {
            $systemType = 'lawyer';
        } elseif (str_contains($referer, '/admin')) {
            $systemType = 'admin';
        }
        
        // الحصول على معرف المستخدم الحالي، وإذا كان فارغاً نستخدم 1 كقيمة افتراضية تجريبية
        $userId = Auth::id() ?: 1;

        // جلب الحقول الأساسية فقط وإخفاء الاستشارات الفارغة التي لم يتم إرسال أي رسالة فيها
        $briefs = AiLegalBrief::where('user_id', $userId)
            ->where('system_type', $systemType)
            ->has('messages')
            ->select('id', 'title', 'created_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        $formattedBriefs = $briefs->map(function ($brief) {
            return [
                'id' => $brief->id,
                'title' => $brief->title,
                'created_at' => $brief->created_at->toISOString(),
                'messages' => [] // مصفوفة فارغة مؤقتاً لحين الضغط على الجلسة
            ];
        });

        return response()->json($formattedBriefs);
    }

    /**
     * 3. جلب رسائل محادثة معينة فقط عند الضغط عليها (Lazy Loading)
     */
    public function getMessages($id)
    {
        $brief = AiLegalBrief::findOrFail($id);
        
        $messages = $brief->messages()->orderBy('created_at', 'asc')->get()->map(function($msg) {
            // محاولة تحويل source_link من JSON إلى مصفوفة (للمراجع المتعددة الجديدة)
            $sourceLinks = [];
            $sourceLinkRaw = $msg->source_link;
            if ($sourceLinkRaw) {
                $decoded = json_decode($sourceLinkRaw, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $sourceLinks = $decoded;
                } else {
                    // توافق مع الروابط القديمة (رابط واحد كنص)
                    $sourceLinks = [['law_name' => 'المرجع القانوني', 'article_number' => '', 'url' => $sourceLinkRaw]];
                }
            }

            return [
                'sender' => $msg->sender,
                'message' => $msg->message,
                'source_link' => $sourceLinkRaw,
                'source_links' => $sourceLinks,
                'created_at' => $msg->created_at ? $msg->created_at->toISOString() : null
            ];
        });

        return response()->json($messages);
    }

    /**
     * 4. إنشاء جلسة استشارية (Brief) جديدة فارغة في قاعدة البيانات
     */
    public function storeConversation(Request $request)
    {
        $referer = $request->header('referer');
        $systemType = 'client';
        
        if (str_contains($referer, '/lawyer')) {
            $systemType = 'lawyer';
        } elseif (str_contains($referer, '/admin')) {
            $systemType = 'admin';
        }
        
        $userId = Auth::id() ?: 1;

        $brief = AiLegalBrief::create([
            'user_id' => $userId,
            'title' => $request->input('title', 'استشارة جديدة'),
            'system_type' => $systemType
        ]);

        return response()->json([
            'id' => $brief->id,
            'title' => $brief->title,
            'created_at' => $brief->created_at->toISOString(),
            'messages' => []
        ]);
    }

    /**
     * 5. حذف الجلسة الاستشارية نهائياً
     */
    public function deleteConversation($id)
    {
        $userId = Auth::id() ?: 1;
        
        $brief = AiLegalBrief::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $brief->delete();

        return response()->json(['success' => true]);
    }
}