<?php

namespace App\Http\Controllers;
// استيراد المكتبات والأدوات اللي يحتاجها الكود باش يخدم صح وبدون أخطاء
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AiLegalBrief;    // 📌 موديل الجلسات
use App\Models\AiBriefMessage;  // 📌 موديل الرسائل
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class AiController extends Controller
{
    /**
     * الدالة الرئيسية لنظام "ميزان" -      
     */

    public function testAi(Request $request)
    {
        $userQuestion = $request->input('question');
        $briefId = $request->input('brief_id'); //  استقبال معرف الجلسة من واجهة الشات المحدثة
        $history = []; // تجهيز مصفوفة فارغة باش نعبوا فيها المحادثة


        //  يتذكر شن صارA I لو فيه معرف جلسة قديم، نمشوا نجيبوا الرسايل القديمة باش الـ 
        if ($briefId) {
            $messagesFromDb = AiBriefMessage::where('brief_id', $briefId)->orderBy('created_at', 'asc')->get();
            foreach ($messagesFromDb as $msg) {
                // نعبوا المصفوفة بالترتيب: من بعث الرسالة وشن كان محتواها
                $history[] = ['role' => $msg->sender, 'content' => $msg->message];
            }
        }
        

        // لو المستخدم بعث سؤال فاضي، نردوا عليه طول من هنايا ونقولوله السؤال مطلوب
        if (!$userQuestion) {
            return response()->json(['success' => false, 'error' => 'السؤال مطلوب']);
        }

        // Supabase حفظ سؤال المستخدم في جدول تفاصيل الرسائل بـ  
        if ($briefId) {
            try {
                AiBriefMessage::create([
                    'brief_id' => $briefId,
                    'sender' => 'user',
                    'message' => $userQuestion
                ]);

                // تحديث عنوان الفقاعة الجانبية تلقائياً في السايد بار لو كانت أول رسالة بالحوار
                $brief = AiLegalBrief::find($briefId);
                if ($brief && $brief->messages()->count() <= 1) {
                    $brief->update(['title' => mb_substr($userQuestion, 0, 22) . '...']);
                }

                //  Log باش ما يعطلش الكود لو صار أي خطأ في حفظ رسالة المستخدم، نسجلوه في الـ 
            } catch (\Exception $e) {
                Log::error('Mizan Saving User Message Error: ' . $e->getMessage());
            }
        }

        //OpenAI سحب مفتاح الـ API الخاص بـ  .env من ملف الـ 
        try {
            $apiKey = env('OPENAI_API_KEY');
            if (!$apiKey) {
                throw new \Exception("مفتاح OPENAI_API_KEY مفقود من ملف .env");
            }


           //  RAG هذا البارت هو العقل الموجه للـ 
            $analysis = $this->analyzeUserIntent($userQuestion, $history, $apiKey);
            $isFollowUp = str_starts_with($analysis, 'FOLLOW');
            
            $context = '';
            $pdfLink = null;

          
            // لو السؤال جديد (مش متابعة)، نمشوا ندوروا في قاعدة البيانات على القوانين المناسبة
            if (!$isFollowUp) {

                // 1.  نظفنا النص   
                $searchQuery = trim(str_replace('NEW:', '', $analysis));

                // 2. هنا بعثنا النص النظيف لدالة البحث الدلالي (getSemanticContext)
                $semanticResult = $this->getSemanticContext($searchQuery, $apiKey);

                // 3. هنا فصلنا النتيجة: خدينا النص بروحها، والرابط بروحها
                $context = $semanticResult['text']; 
                $pdfLink = $semanticResult['pdf_link']; 
            }
            

            // 3. حقن الهوية البروتوكولية الصارمة لنظام ميزان في الـ System Instruction
            $systemInstruction = 'أنت مساعد قانوني ذكي متخصص حصريًا في القوانين الليبية والوثائق القانونية المعتمدة داخل قاعدة البيانات الخاصة بالنظام. تتمثل مهمتك في تقديم معلومات وتحليلات قانونية دقيقة، حيادية، ومسؤولة، بالاعتماد فقط على النصوص والوثائق القانونية المتوفرة داخل قاعدة المعرفة المعتمدة للنظام (المرفقة لك)، دون استخدام أي معرفة قانونية خارجية أو افتراضات غير موثقة. يجب أن تكون جميع إجاباتك مهنية، واضحة، مفهومة لعامة المستخدمين، ومصاغة بأسلوب خبير قانوني يشرح ويوجه بشكل مسؤول.

---
[الهوية القانونية ونطاق المعرفة]:
1. أنت لست محاميًا بشريًا، ولا تمثل جهة قضائية، ولا تصدر أحكامًا ملزمة. أنت نظام ذكاء اصطناعي يقدم تحليلات استنادًا فقط إلى الوثائق المتاحة.
2. يُمنع منعاً باتاً اختلاق مواد قانونية، أو أرقام مواد، أو نصوص، أو سوابق غير موثقة في السياق المرفق. إذا لم توجد معلومة كافية داخل قاعدة البيانات, صرح بذلك بوضوح وشفافية.

---
[أسلوب التواصل وقواعد السلوك]:
- استخدم اللغة العربية الفصحى الواضحة والمبسطة. تجنب التعقيد القانوني غير الضروري، واشرح المصطلحات غير الشائعة عند الحاجة فقط.
- التزم بالحياد الكامل، وافصل بين الحقائق القانونية والتحليل التفسيري، وتجنب الجزم بنتائج القضايا أو الأحكام القضائية، ووضح أن الفصل النهائي يعود للجهات القضائية المختصة.
- يُمنع منعاً باتاً تقديم طرق للتحايل على القانون، أو تعطيل العدالة، أو تشجيع إخفاء الأدلة.

---
[بروتوكولات التعامل مع السيناريوهات المختلفة - طبقها بحسم]:
1. عند توفر معلومات قانونية كافية: اشرح الوضع بلغة مبسطة، ووضح كيفية انطباق القانون على الوقائع، واذكر الخيارات القانونية المتاحة.
2. عند نقص المعلومات من المستخدم: لا تقدم استنتاجًا قانونيًا نهائيًا، بل اطلب المعلومات الجوهرية الناقصة بشكل واضح (مثل: تاريخ الواقعة، نوع العقد، المستندات، الشهود) ووضح أهميتها للتحليل.
3. عند غياب المعلومة من قاعدة البيانات: صرح بوضوح بأن البيانات المتاحة لا تتضمن إجابة مؤكدة، واقترح على المستخدم التواصل مع محامٍ مرخص عبر التطبيق.
4. عند وجود أكثر من تفسير أو تعارض: اعرض التفسيرات بحيادية ووضح العوامل المؤثرة دون ترجيح قطعي، وطبّق قواعد التدرج القانوني بحسب البيانات المتاحة.
5. في القضايا العاجلة أو الجنائية (توقيف، استدعاء، تحقيق، اتهام جنائي): أعطِ الأولوية القصوى فوراً للإجراءات الوقائية العاجلة (مثل: أهمية الاستعانة بمحام، الحفاظ على المستندات والأدلة، تجنب الإدلاء بأقوال غير مدروسة دون محام) قبل الشرح التفصيلي.

---
[شكل ومكونات الإجابة النهائية]:
يجب أن تتضمن الإجابة الهيكلية التالية:
- مقدمة مختصرة توضح فهم المشكلة وتحليل المعضلة.
- شرح قانوني مبسط وواضح وتطبيقه على الوقائع المقدمة (بناءً على النصوص المرفقة).
- توضيح الخيارات أو الإجراءات القانونية الممكنة والتنبيه لأي غموض.
- اقتراح التواصل مع محامٍ مرخص عبر التطبيق عند الحاجة.
- النص التالي حرفياً كإخلاء مسؤولية في السطر الأخير قبل المراجع:
"هذه المعلومات مقدمة لأغراض التوعية القانونية العامة استنادًا إلى البيانات القانونية المتوفرة داخل النظام، ولا تُعد استشارة قانونية مكتملة أو بديلًا عن محامٍ مرخص. في القضايا المهمة أو المعقدة يُنصح بالتواصل مع محامٍ عبر التطبيق للحصول على تقييم قانوني متخصص."';

            $messages = [];
            // وضع التعليمات الصارمة للنظام كأول عنصر في مصفوفة الرسائل باش خلااااص نضمن ان يلتزم بيها
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
            
            // حقن ذاكرة المحادثة المستمرة (History)
            if (is_array($history)) {
                foreach ($history as $msg) {
                    if (isset($msg['role']) && isset($msg['content'])) {
                        $role = ($msg['role'] === 'ai' || $msg['role'] === 'assistant') ? 'assistant' : 'user';
                        $messages[] = ['role' => $role, 'content' => $msg['content']];
                    }
                }
            }

            // تدمج السوال مع الجواب النهايي وتحطهم في قالب نهايي
            $fullPrompt = $this->buildFinalPrompt($userQuestion, $context, $isFollowUp);
            $messages[] = ['role' => 'user', 'content' => $fullPrompt];

         // (اللي فيه القواعد + الذاكرة + السؤال + مواد القانون المسترجعة)   هنا بعثنا الصندوق 
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->timeout(60)->post("https://api.openai.com/v1/chat/completions", [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.1, 
                'max_tokens' => 2500,
            ]);

            $responseData = $response->json();

            if (isset($responseData['error'])) {
                $openaiError = $responseData['error']['message'] ?? json_encode($responseData['error']);
                return response()->json(['success' => false, 'error' => 'خطأ API: ' . $openaiError]);
            }

            $aiAnswer = $responseData['choices'][0]['message']['content'] ?? 'حدث خطأ في توليد الإجابة.';

            // إخفاء رابط المرجع إذا كان السياق المسترجع فارغاً منعاً للروابط العشوائية
            if (empty($context)) {
                $pdfLink = null;
            }

            // ب. حفظ رد الذكاء الاصطناعي النهائي ورابط السند بجدول الرسائل في Supabase تلقائياً
            if ($briefId) {
                try {
                    AiBriefMessage::create([
                        'brief_id' => $briefId,
                        'sender' => 'ai',
                        'message' => $aiAnswer,
                        'source_link' => $pdfLink
                    ]);
                    
                    // تحديث وقت تعديل الجلسة في قاعدة البيانات
                    if (isset($brief)) {
                        $brief->update(['updated_at' => now()]);
                    }
                } catch (\Exception $e) {
                    Log::error('Mizan Saving AI Message Error: ' . $e->getMessage());
                }
            }
// إرجاع النتيجة النهائية في شكل جيسووون  
            return response()->json([
                'success' => true,
                'final_legal_answer' => $aiAnswer,
                'source_link' => $pdfLink 
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            Log::error('Mizan System Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'خطأ نظام: ' . $e->getMessage()]);
        }
    }











   // 1. دالة معالجة النية اللغوية وتفكيك العامية لـ فصحى
    private function analyzeUserIntent($question, $history, $apiKey)
    {
        $historyText = '';
        if (is_array($history) && count($history) > 0) {
            $recent = array_slice($history, -4); 
            foreach ($recent as $msg) {
                $role = ($msg['role'] === 'ai' || $msg['role'] === 'assistant') ? 'System' : 'User';
                $historyText .= "{$role}: {$msg['content']}\n";
            }
        }

        $prompt = "أنت خبير قانوني ومحلل لغوي بارع. مهمتك تفكيك لغة الشارع الليبية العامية واستخراج التكييف القانوني لها وتحويلها لمصطلحات فصحى دقيقة.
السياق السابق:
{$historyText}
السؤال الجديد:
\"{$question}\"

التعليمات:
1. إذا كان السؤال قضية جديدة، استخرج الجوهر القانوني وحوله إلى 6 مصطلحات قانونية فصحى بحتة تستخدم في صياغة التشريعات والمحاكم.
رد حصراً بهذه الصيغة: NEW: مصطلح1 مصطلح2 مصطلح3
2. إذا كان السؤال مجرد متابعة، رد بـ: FOLLOW";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json'
            ])->post("https://api.openai.com/v1/chat/completions", [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.0
            ]);

            $responseData = $response->json();
            $result = $responseData['choices'][0]['message']['content'] ?? 'NEW: ' . $question;
            return trim($result);
        } catch (\Exception $e) {
            return 'NEW: ' . $question; 
        }
    }





   // 2. Embeddings دالة توليد الـ
    //  Vector Similarity Search  وتنفيذ الـ 
    //عبر  pgvector بمسافة الكوساين <=>"
    private function getSemanticContext($query, $apiKey)
    {
        try {
            $embeddingResponse = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-3-small',
                'input' => $query, // 👈 هنا بعثنا السؤال النظيف
            ]);

            if (!$embeddingResponse->successful()) { // 👈 وهنا استلمنا المتجه (الأرقام) وجهزناه
                Log::error('OpenAI Embedding Error: ' . $embeddingResponse->body());
                return ['text' => '', 'pdf_link' => null];
            }

            $vector = $embeddingResponse->json()['data'][0]['embedding'];
            $vectorString = '[' . implode(',', $vector) . ']';

        
            $topLaws = DB::table('laws')
                ->select('law_name', 'article_number', 'article_text', 'simplified_explanation', 'pdf_page') 
                ->whereNotNull('embedding')
                ->orderByRaw("embedding <=> ?::vector", [$vectorString])
                ->limit(3)
                ->get();

            if ($topLaws->isEmpty()) {
                return ['text' => '', 'pdf_link' => null];
            }

            $topResult = $topLaws->first();
            $lawName = $topResult->law_name ?? ''; 
            $page = $topResult->pdf_page ?? 1;

            $pdfFile = $this->matchPdfFile($lawName);
            $fullPdfLink = asset("pdfs/{$pdfFile}") . "#page=" . $page;

            $contextText = $topLaws->map(function($law) {
            
                return "📌 [التشريع: {$law->law_name}] | مادة رقم: ({$law->article_number})\nنص المادة: {$law->article_text}\nالتفسير الميسر: {$law->simplified_explanation}";
            })->implode("\n\n---\n\n");

            return [
                'text' => $contextText,
                'pdf_link' => $fullPdfLink
            ];

        } catch (\Exception $e) {
            Log::error('Vector Search Error: ' . $e->getMessage());
            return ['text' => '', 'pdf_link' => null];
        }
    }






   // 3. داله ربط التشريع مع الملف متعها
    private function matchPdfFile($lawName)
    {
        if (str_contains($lawName, 'العقوبات')) return 'penal_code.pdf';
        if (str_contains($lawName, 'الأحوال') || str_contains($lawName, 'الاحوال')) return 'personal_status_law.pdf';
        if (str_contains($lawName, 'الإجراءات') || str_contains($lawName, 'الاجراءات')) return 'criminal_procedures_law.pdf';
        if (str_contains($lawName, 'الجرائم') || str_contains($lawName, 'الكتروني') || str_contains($lawName, 'مكافحة')) return 'anti_cybercrime_law.pdf';
        return 'libyan_civil_law.pdf'; 
    }

   





    // 4. دالة هندسة البرومبت وتجميع السياق وتفعيل بروتوكول غياب المعلومة لحظر الهلوسة
    private function buildFinalPrompt($question, $context, $isFollowUp)
    {
        if ($isFollowUp) {
            return "المستخدم يتابع المحادثة السابقة ويسأل: \"{$question}\". أجب وفق السيناريو المناسب والقواعد الصارمة والأسلوب المبسط الحذر دون الخروج عن البيانات المتوفرة.";
        }

        if (empty($context)) {
            return "سؤال المستخدم: \"{$question}\"\n\n[تنبيه للمساعد]: السياق المسترجع فارغ تماماً من قاعدة البيانات. يرجى تفعيل (بروتوكول غياب المعلومة) فوراً، والتصريح بذلك للمستخدم بوضوح والتحفظ في الرد والاعتماد فقط على القواعد العامة الموثقة دون اختراع أي مواد.";
        }

        return "المستندات القانونية المتاحة والمستخرجة من قاعدة البيانات كدليل حتمي لسؤال المستخدم:\n{$context}\n\nسؤال المستخدم الفعلي: \"{$question}\"\n\nقم بصياغة الرد الآن بتطبيق بروتوكولات السلوك والسيناريوهات المناسبة والالتزام بشكل الإجابة النهائي وإخلاء المسؤولية.";
    }
}