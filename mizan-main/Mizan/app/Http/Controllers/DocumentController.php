<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Document;
use App\Models\DocumentSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;
use Exception;

class DocumentController extends Controller
{
    // ==========================================
    // إدارة المجلدات (الفولدرات)
    // ==========================================

    public function index()
    {
        // هنا نجيبوا في الفولدرات متع اليوزر اللي مسجل دخول توا، ومعاهم ملفاتهم وقداش عندهم ملف، ونرتبوهم من الجديد للقديم
        $folders = Folder::where('user_id', Auth::id())
                         ->with('documents')
                         ->withCount('documents')
                         ->latest()
                         ->get();

        // ونجيبوا حتى الملخصات اللي دارهم اليوزر هدا ونرتبوهم بالوقت من الأحدث للأقدم
        $summaries = DocumentSummary::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        // في النهاية نفتحوا الصفحة (smart-vault) ونبعتولها الفولدرات والملخصات باش ينعرضوا
        return view('shared.smart-vault', compact('folders', 'summaries'));
    }

    public function storeFolder(Request $request)
    {
        // نتأكدوا قبل إن اسم الفولدر مبعوت ومش فاضي وما يفوتش 255 حرف
        $request->validate(['name' => 'required|string|max:255']);
        
        // نوقعوا الفولدر الجديد في قاعدة البيانات ونربطوه بـ id متع اليوزر
        $folder = Folder::create(['name' => $request->name, 'user_id' => Auth::id()]);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'folder' => $folder, 'documents_count' => 0]);
        }
        return back()->with('success', 'تم إنشاء المجلد بنجاح');
    }

    public function updateFolder(Request $request, $id)
    {
        // نتأكدوا من الاسم الجديد أول حاجة
        $request->validate(['name' => 'required|string|max:255']);
        
        // نمشوا ندوروا على الفولدر بـ id متعها، ولازم يكون متع نفس اليوزر مش متع حد تاني، ولو ملقيناشي نطلعوا خطأ 404
        $folder = Folder::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        // نحدثوا اسم الفولدر توا
        $folder->update(['name' => $request->name]);

        // لو يبي جيسون نردوا عليه بـ true
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        // وإلا نرجعوا للخلف ونقولوله تم التعديل
        return back()->with('success', 'تم تعديل اسم المجلد');
    }

    public function destroyFolder(Request $request, $id)
    {
        // ندوروا على الفولدر ونجيبوا معاه الملفات اللي وسطه باش مانخلوش وراه الحوسة
        $folder = Folder::where('id', $id)->where('user_id', Auth::id())->with('documents')->firstOrFail();
        
        // نلفّوا على كل ملف وسط الفولدر هدا، ونمسحوا الملف الحقيقي المخزن في السيرفر (Storage) باش مانعبوش الهارد ديسك
        foreach($folder->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // بعد ما نظفنا السيرفر من الملفات، نمسحوا الفولدر نفسه من قاعدة البيانات
        $folder->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم حذف المجلد بما فيه من مستندات');
    }

    // ==========================================
    // إدارة المستندات (الرفع، الحذف، الخصائص)
    // ==========================================

    public function storeDocument(Request $request)
    {
        // نتأكدوا إن الفولدر اللي بنحطوا فيه موجود فعلاً، وإن الملف المرفوع صيغته صح (pdf, doc, png...) وحجمه ما يفوتش 10 ميجا
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        // نتأكدوا إن الفولدر هدا تابع لليوزر هدا مش لواحد تاني
        $folder = Folder::where('id', $request->folder_id)->where('user_id', Auth::id())->firstOrFail();

        // لو فيه ملف مبعوت فعلاً في الفورم
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName(); // ناخدوا اسمه الأصلي
            $path = $file->store('smart_vault', 'public'); // نخزنوه في مجلد smart_vault على السيرفر
            
            // نسجلوا بيانات الملف في قاعدة البيانات
            $document = Document::create([
                'folder_id' => $folder->id,
                'user_id' => Auth::id(),
                'title' => $originalName,
                'file_path' => $path
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'document' => $document]);
            }
            return back()->with('success', 'تم رفع المستند بنجاح');
        }
        
        // لو صارت أي لقطة والملف ماركبش، نرجعوا بخطأ
        return back()->withErrors(['document' => 'فشل في رفع الملف.']);
    }

    public function destroyDocument(Request $request, $id)
    {
        // ندوروا على الملف، ونتأكدوا عن طريق الفولدر إنه ملك لليوزر الحالي
        $document = Document::whereHas('folder', function($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $id)->firstOrFail();

        // لو الملف قاعد موجود في السيرفر، نمسحوه من الهارد ديسك طول
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // ونمسحوا سجله من قاعدة البيانات
        $document->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم حذف المستند نهائياً');
    }

    public function viewDocument($id)
    {
        // نجيبوا الملف ونتأكدوا من هويته وصاحبه
        $document = Document::whereHas('folder', function($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $id)->firstOrFail();
        
        // نطلعوا المسار الكامل للملف في السيرفر
        $path = Storage::disk('public')->path($document->file_path);
        
        // لو مش موجود على السيرفر، نعطوه خطأ 404 ونقولوله الملف طار مش قاعد
        if (!file_exists($path)) {
            abort(404, 'الملف غير موجود على الخادم.');
        }

        // نعرضوا الملف في المتصفح طول (inline) باش يقدر يقراه من غير ما ينزل عنده إجباري
        return response()->file($path, ['Content-Disposition' => 'inline; filename="' . $document->title . '"']);
    }

    public function updateDocumentName(Request $request, $id)
    {
        // لقطة لتعديل اسم الملف
        $request->validate(['title' => 'required|string|max:255']);
        
        $document = Document::whereHas('folder', function($query) { 
            $query->where('user_id', Auth::id()); 
        })->where('id', $id)->firstOrFail();

        // ناخدوا امتداد الملف القديم (زي pdf مثلاً)
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $newTitle = $request->title;
        
        // لو اليوزر كتب الاسم الجديد ونسى ما حطش الامتداد في آخره، نحطوه أحني بالسيف باش ما يخربش الملف
        if (!str_ends_with($newTitle, ".$extension")) {
            $newTitle .= ".$extension";
        }

        // نحدثوا الاسم توا
        $document->update(['title' => $newTitle]);
        return back()->with('success', 'تم تعديل اسم المستند');
    }

    public function updateDocumentMetadata(Request $request, $id)
    {
        // هنا نحدثوا في تفاصيل المستند (البيانات الإضافية) زي تاريخ الانتهاء والملاحظات والأهمية
        $request->validate([
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'classification' => 'nullable|string|max:255',
            'importance' => 'nullable|in:عالي,متوسط,منخفض',
        ]);

        $document = Document::whereHas('folder', function($query) { 
            $query->where('user_id', Auth::id()); 
        })->where('id', $id)->firstOrFail();

        // نعدلوا البيانات، ولو ما اختارش تصنيف نحطوا "عام" تلقائي، ولو ما اختارش أهمية نحطوا "متوسط"
        $document->update([
            'expiry_date' => $request->expiry_date,
            'is_task' => $request->has('is_task'), // لو مأشر على خانة المهمة تطلع true وإلا false
            'notes' => $request->notes,
            'classification' => $request->classification ?? 'عام',
            'importance' => $request->importance ?? 'متوسط',
        ]);

        return back()->with('success', 'تم حفظ خصائص المستند بنجاح');
    }

    // ==========================================
    // إدارة الملخصات 
    // ==========================================

    public function storeSummary(Request $request)
    {
        // دالة تخزين الملخص اللي طلعه الذكاء الاصطناعي
        $request->validate([
            'document_name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            // نسجلوا الملخص في الداتابيز باسم اليوزر
            $summary = DocumentSummary::create([
                'user_id' => Auth::id(),
                'document_name' => $request->document_name,
                'content' => $request->content,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الملخص بنجاح',
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            // لو صار خطأ نسجلوه في ملف الـ Log باش نعرفوا العلة وين، ونردوا بجيسون فيه خطأ 500
            Log::error('خطأ في حفظ الملخص: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل حفظ الملخص: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSummaries()
    {
        // دالة سريعة تجيب كل الملخصات متع اليوزر الحالي على هيئة جيسون
        $summaries = DocumentSummary::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($summaries);
    }

    public function getSummary($id)
    {
        // تجيب ملخص واحد محدد بـ id متعها
        $summary = DocumentSummary::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($summary);
    }

    public function destroySummary($id)
    {
        // تمسح ملخص معين من الداتابيز
        $summary = DocumentSummary::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $summary->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الملخص بنجاح'
        ]);
    }

    // ==========================================
    // التحليل الذكي للمستندات والصور (OCR مدعوم)
    // ==========================================
    public function summarizeDocument(Request $request)
    {
        // دالة التلخيص الكبيرة بالذكاء الاصطناعي، تقبل pdf وصور وحجمها لعند 15 ميجا
        $request->validate(['document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:15360']);

        try {
            $file = $request->file('document');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = $file->getClientOriginalName();

            // البرومبت الاحترافي الخالي من الإيموجيات والصارم في نقل البيانات
            $systemPrompt = <<<PROMPT
أنت محلل وثائق احترافي وخبير في استخراج المعلومات وتحليل المستندات.

مهمتك هي قراءة المستند المرفق وتحويل محتواه إلى تحليل شامل ودقيق ومنظم مع الالتزام الكامل بما ورد في النص.

═══════════════════════════════
القواعد الأساسية
═══════════════════════════════

1. اعتمد فقط على المعلومات الموجودة داخل المستند.

2. ممنوع اختراع:
   - أرقام.
   - تواريخ.
   - أسماء.
   - شركات.
   - مراحل.
   - نسب.
   - مواد قانونية.
   - استنتاجات لا يدعمها النص.

3. إذا كانت معلومة غير واضحة أو ناقصة:
   - اذكر أنها غير واضحة.
   - لا تحاول تخمينها.

4. ميّز دائماً بين:
   - المعلومات المذكورة صراحة في المستند.
   - الاستنتاجات التحليلية المستندة إلى النص.

5. انقل الأرقام والتواريخ والنسب كما وردت حرفياً.

6. إذا كان المستند قانونياً:
   - اشرح البنود والمواد بلغة مبسطة.
   - وضح آثارها القانونية.
   - لا تقدم رأياً قانونياً خارج ما يسمح به النص.

7. إذا كان المستند تقنياً أو إدارياً أو تجارياً:
   - ركز على الهيكل والأهداف والمتطلبات والعلاقات بين العناصر.

8. استخدم لغة عربية فصحى احترافية.

9. يمنع استخدام الإيموجي أو الرموز غير المهنية.

═══════════════════════════════
منهجية التحليل
═══════════════════════════════

- حدد نوع المستند.
- استخرج الهدف الرئيسي.
- استخرج جميع الأقسام والمحاور الرئيسية.
- استخرج المعلومات الجوهرية.
- استخرج الأرقام والتواريخ والنسب المهمة.
- وضح العلاقات بين العناصر المذكورة في المستند.
- حدد النقاط غير الواضحة أو التي تحتاج استكمالاً.

═══════════════════════════════
تنسيق المخرجات
═══════════════════════════════

# التحليل الشامل للمستند

## تعريف المستند
وصف دقيق لنوع المستند والغرض منه.

## الملخص التنفيذي
عرض مختصر لأهم ما يتضمنه المستند.

## التفاصيل والمحتوى
تحليل تفصيلي لجميع الأقسام والبنود والمعلومات الواردة.

## البيانات والأرقام المهمة
عرض منظم لجميع الأرقام والتواريخ والنسب الواردة in المستند.

## العلاقات والترابط
شرح العلاقة بين العناصر والأقسام استناداً إلى ما ورد في النص.

## الملاحظات المهمة
النقاط الجوهرية أو القيود أو الملاحظات التي ذكرها المستند.

## المعلومات غير الواضحة أو الناقصة
أي عناصر لم تكن واضحة أو لم يذكر المستند تفاصيل كافية عنها.

## الخلاصة
ملخص مهني يوضح الصورة العامة للمستند دون إضافة معلومات من خارج النص.

═══════════════════════════════
قاعدة ذهبية

إذا لم تذكر المعلومة في المستند فلا تذكرها.
إذا لم تكن متأكداً فاذكر ذلك بوضوح.
الدقة أهم من الإكمال.
PROMPT;

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            // لو كان الملف المرفوع PDF
            if ($extension === 'pdf') {
                $parser = new PdfParser();
                $pdf = $parser->parseFile($file->path()); // نقروا ملف الـ PDF
                $text = $pdf->getText(); // نطلعوا النص اللي وسطه
                
            
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
                $cleanText = trim(preg_replace('/\s+/', ' ', $text));

                // لو النص طلع فاضي، معناه الـ PDF هدا عبارة عن صور ممسوحة سكنر ومافيهش نص حقيقي، فنقولوله ارفعه كصورة احسن
                if (empty($cleanText)) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'المستند بصيغة PDF يبدو أنه ممسوح ضوئياً (صورة) ولا يحتوي على نصوص قابلة للقراءة بشكل مباشر. يرجى تحويله أو رفعه كصورة (JPG/PNG) ليعمل مع نظام الـ OCR الخاص بنا.'
                    ], 400);
                }

                // نجهزوا الرسالة ونحطوا فيها النص النظيف المأخوذ من الـ PDF
                $messages[] = [
                    'role' => 'user', 
                    'content' => "قم بتحليل هذا النص بدقة واحترافية:\n" . $cleanText
                ];
            } 
            // ولو كان الملف صورة (يعني بنشغلوا الـ OCR لقراءة النص من الصورة)
            else {
                $fileContent = file_get_contents($file->getRealPath());
                $base64Image = base64_encode($fileContent); // نحولوا الصورة لكود base64 باش يفهمها الموديل
                $mimeType = $file->getMimeType();
                $imageUrl = "data:{$mimeType};base64,{$base64Image}";

                // نبعتوا الصورة للـ الـ API متع OpenAI ونعلو الدقة لـ high باش يقرا الكلمات الصغيرة بوضوح
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => "قم بقراءة هذا المستند المصور (OCR) وتحليله بدقة واحترافية:"
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl,
                                'detail' => 'high' // للحصول على دقة OCR أعلى
                            ]
                        ]
                    ]
                ];
            }

            // نبعتوا البيانات لـ OpenAI (بالموديل gpt-4o) وعطيناه وقت كافي (180 ثانية) و حطينا التمبورتشر 0 باش مايخرفش ويعطينا بيانات دقيقة
            $response = Http::withToken(env('OPENAI_API_KEY'))->timeout(180)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o', 
                    'messages' => $messages,
                    'temperature' => 0.0, 
                    'max_tokens' => 4000
                ]);

            // لو الرد جاء تمام وناجح، نرجعوا بالملخص اللي صاغه الذكاء الاصطناعي
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'filename' => $filename,
                    'summary' => $response->json('choices.0.message.content')
                ]);
            }

            // لو الـ API ردت بخطأ، نسجلوه في اللوق ونقولوا لليوزر فشل التحليل
            Log::error('OpenAI API Error: ' . $response->body());
            return response()->json(['success' => false, 'message' => 'فشل في تحليل المستند من قبل الذكاء الاصطناعي.'], 500);

        } catch (\Throwable $e) {
            // لو صارت أي كارثة أو كراش في الكود، نمسكوها هنايا ونسجلوها وما نخلوش السيستم يعلق
            Log::error('Document Parsing/OCR Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء معالجة الملف: ' . $e->getMessage()], 500);
        }
    }
}