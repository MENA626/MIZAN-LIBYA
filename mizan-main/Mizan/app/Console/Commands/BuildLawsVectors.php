<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Law;
use Illuminate\Support\Facades\File;

class BuildLawsVectors extends Command
{
    protected $signature = 'laws:build-vectors';
    protected $description = 'استيراد المتجهات من ملف JSON محلي لمشروع ميزان';

    public function handle()
    {
        $jsonPath = database_path('data/laws_vectors.json');

        if (!File::exists($jsonPath)) {
            $this->error("❌ خطأ: الملف غير موجود في المسار: " . $jsonPath);
            return;
        }

        $this->info("🚀 جاري قراءة المتجهات من ملفك الخاص...");

        $vectors = json_decode(File::get($jsonPath), true);
        
        if (!$vectors) {
            $this->error("❌ خطأ: تنسيق ملف JSON غير صحيح أو الملف فارغ.");
            return;
        }

        $laws = Law::orderBy('id')->get();
        $successCount = 0;

        foreach ($laws as $index => $law) {
            $item = $vectors[$law->id] ?? $vectors[$index] ?? null;

            if ($item) {
                // استخراج المتجه الرقمي فقط، سواء كان الكائن يحتوي على مفتاح "vector" أم أنه المتجه نفسه
                $embedding = isset($item['vector']) ? $item['vector'] : $item;
                
                // في حال كان المتجه لا يزال مصفوفة منسقة كـ Object (مثلاً {"0":0, "1":0})
                // نقوم بتحويل القيم إلى مصفوفة رقمية بسيطة
                if (is_array($embedding)) {
                     // هذه الخطوة تضمن أن القيم مرتبة كأرقام وليست كـ (Key-Value)
                    $embedding = array_values($embedding);
                }

                // ضبط الأبعاد لتصبح 1536
                $paddedEmbedding = array_pad((array)$embedding, 1536, 0.0);
                
                // تحويل المصفوفة إلى نص بتنسيق [0,0,...] لضمان توافقها مع نوع Vector في PostgreSQL
                // هذه الخطوة تمنع الخطأ "Vector contents must start with ["
                $formattedVector = '[' . implode(',', $paddedEmbedding) . ']';
                
                $law->update(['embedding' => $formattedVector]);
                $successCount++;
                $this->info("✅ تم ربط المتجه للقانون: " . ($law->article_title ?? "رقم " . $law->id));
            } else {
                $this->warn("⚠️ لم يتم العثور على متجه للقانون رقم: " . $law->id);
            }
        }

        $this->info("🎉 انتهينا  ! تم استيراد {$successCount} متجه بنجاح.");
    }
}