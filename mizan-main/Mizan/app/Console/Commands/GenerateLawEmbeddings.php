<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;

class GenerateLawEmbeddings extends Command
{
    // حافظنا على السجنتشر الخاص بكِ ليعمل كما هو
    protected $signature = 'laws:generate-embeddings';
    protected $description = 'معالجة مستمرة للمواد القانونية وتحويلها لمتجهات لمشروع ميزان عبر OpenAI';

    public function handle()
    {
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            $this->error("❌ مفتاح OPENAI_API_KEY مفقود من ملف .env");
            return;
        }

        $this->info("🚀 جاري بدء المعالجة الآلية لجميع المواد عبر العقل المدبر لـ OpenAI...");

        // حلقة تكرار مستمرة حتى معالجة الـ 1,305 مادة كاملة
        while (true) {
            // جلب 30 مادة في كل دفعة باستخدام DB مباشرة لضمان أعلى سرعة وتوافق
            $laws = DB::table('laws')->whereNull('embedding')->limit(30)->get();

            if ($laws->isEmpty()) {
                $this->info('✅ مبروك يا منوش! انتهت كل المواد وتم ملء قبو ميزان الذكي بالكامل.');
                break; 
            }

            foreach ($laws as $law) {
                try {
                    // دمج النص بناءً على أسماء الحقول الجديدة والمطابقة 100% للجداول المرفوعة
                    $contentToEmbed = "اسم القانون: {$law->law_name} | مادة: {$law->article_number} | عنوان: {$law->article_title} | نص: {$law->article_text} | التفسير: {$law->simplified_explanation}";

                    // الاتصال السريع بـ OpenAI عبر HTTP Client الخاص بلارافيل
                    $response = Http::withHeaders([
                        'Authorization' => "Bearer {$apiKey}",
                        'Content-Type' => 'application/json',
                    ])->timeout(30)->post('https://api.openai.com/v1/embeddings', [
                        'model' => 'text-embedding-3-small', 
                        'input' => $contentToEmbed,
                    ]);

                    if ($response->successful()) {
                        // استخراج المصفوفة وتحويلها لنص يفهمه Supabase
                        $vector = $response->json()['data'][0]['embedding'];
                        $vectorString = '[' . implode(',', $vector) . ']';

                        // التحديث المباشر الآمن متوافقاً مع نوع البيانات vector(1536)
                        DB::table('laws')->where('id', $law->id)->update([
                            'embedding' => DB::raw("'{$vectorString}'::vector")
                        ]);

                        $this->line("✅ مادة {$law->article_number} من {$law->law_name} - تمت الأرشفة بنجاح");
                        
                    } else {
                        // معالجة حالة الضغط وتخطيها بذكاء دون كسر الحلقة
                        if ($response->status() == 429) {
                            $this->warn("⚠️ ازدحام طلبات مؤقت.. تبريد لمدة 10 ثوانٍ...");
                            sleep(10);
                        } else {
                            throw new Exception("خطأ من الـ API: " . $response->body());
                        }
                    }

                } catch (Exception $e) {
                    $this->error("❌ خطأ في المادة {$law->article_number}: " . $e->getMessage());
                }
            }
            
            $this->info("✨ اكتملت دفعة.. جاري الانتقال للدفعة التالية تلقائياً...");
            
            // تبريد بسيط جداً لحماية الـ Rate Limit الخاص بحسابك
            usleep(200000); 
        }
    }
}