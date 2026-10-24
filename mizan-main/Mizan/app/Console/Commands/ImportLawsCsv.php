<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLawsCsv extends Command
{
    protected $signature = 'mizan:upload-csv';
    protected $description = 'الرفع الشامل والمطابق لبيانات ميزان مع دعم حقل category_id الإجباري';

    public function handle()
    {
        // مصفوفة تحتوي على كل ملف مع تحديد الترتيب ورقم التصنيف المناسب لكل قانون
        $filesConfig = [
            'civil_law.csv' => [
                'category_id' => 1, // تصنيف القانون المدني
                'indices' => ['law_name' => 1, 'law_year' => 2, 'law_number' => 3, 'category' => 4, 'chapter' => 5, 'article_number' => 6, 'article_title' => 7, 'article_text' => 8, 'simplified_explanation' => 9, 'pdf_page' => 10, 'status' => 11, 'notes' => 12]
            ],
            'personal_status_law.csv' => [
                'category_id' => 2, // تصنيف الأحوال الشخصية
                'indices' => ['law_name' => 0, 'category' => 1, 'article_number' => 2, 'article_title' => 3, 'article_text' => 4, 'simplified_explanation' => 5, 'pdf_page' => 6]
            ],
            'penal_code.csv' => [
                'category_id' => 3, // تصنيف قانون العقوبات
                'indices' => ['law_name' => 1, 'law_year' => 2, 'law_number' => 3, 'category' => 4, 'chapter' => 5, 'article_number' => 6, 'article_title' => 7, 'article_text' => 8, 'simplified_explanation' => 9, 'pdf_page' => 10, 'status' => 11, 'notes' => 12]
            ],
            'criminal_procedures_law.csv' => [
                'category_id' => 4, // تصنيف الإجراءات الجنائية
                'indices' => ['law_name' => 1, 'law_year' => 2, 'law_number' => 3, 'category' => 4, 'chapter' => 5, 'article_number' => 6, 'article_title' => 7, 'article_text' => 8, 'simplified_explanation' => 9, 'pdf_page' => 10, 'status' => 11, 'notes' => 12]
            ],
            'anti_cybercrime_law.csv' => [
                'category_id' => 5, // تصنيف الجرائم الإلكترونية
                'indices' => ['law_name' => 1, 'law_year' => 2, 'law_number' => 3, 'category' => 4, 'chapter' => 5, 'article_number' => 6, 'article_title' => 7, 'article_text' => 8, 'simplified_explanation' => 9, 'pdf_page' => 10, 'status' => 11, 'notes' => 12]
            ]
        ];

        $totalCount = 0;
        $this->info("🛡️ جاري بدء الرفع الهيكلي الصارم ودعم حقل category_id الجديد...");

        foreach ($filesConfig as $fileName => $config) {
            $path = database_path("data/{$fileName}");

            if (!file_exists($path)) {
                $this->error("⚠️ الملف غير موجود: {$fileName} - سيتم تخطيه.");
                continue;
            }

            $file = fopen($path, 'r');
            fgetcsv($file, 0, ',', '"', '\\'); 

            $this->line("⏳ جاري معالجة نصوص ملف: {$fileName}...");
            $count = 0;

            while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
                
                $val = function($key) use ($row, $config) {
                    $idx = $config['indices'][$key] ?? -1;
                    if ($idx >= 0 && isset($row[$idx]) && trim($row[$idx]) !== '') {
                        $str = trim($row[$idx]);
                        $encoding = mb_detect_encoding($str, ['UTF-8', 'ASCII', 'ISO-8859-1'], true);
                        return ($encoding === 'UTF-8') ? $str : mb_convert_encoding($str, 'UTF-8', $encoding ?: 'auto');
                    }
                    return null;
                };

                $lawName    = $val('law_name');
                $articleNum = $val('article_number');
                $articleTxt = $val('article_text');

                if (empty($lawName) && empty($articleTxt) && empty($articleNum)) {
                    continue; 
                }

                $data = [
                    'law_name'               => $lawName,
                    'category_id'            => $config['category_id'], // 🌟 هنا يتم تغذية الحقل الإجباري تلقائياً لحل المشكلة
                    'article_number'         => $articleNum,
                    'article_title'          => $val('article_title'),
                    'article_text'           => $articleTxt, 
                    'simplified_explanation' => $val('simplified_explanation'),
                    'pdf_page'               => $val('pdf_page') ? intval($val('pdf_page')) : null,
                    'law_year'               => $val('law_year') ? intval($val('law_year')) : null,
                    'law_number'             => $val('law_number') ? intval($val('law_number')) : null,
                    'category'               => $val('category'),
                    'chapter'                => $val('chapter'),
                    'status'                 => $val('status') ?? 'سارية',
                    'notes'                  => $val('notes'),
                ];

                if (empty($data['article_number'])) {
                    $data['article_number'] = ($count + 1);
                }
                if (empty($data['article_text'])) {
                    continue; 
                }

                $shortText = mb_substr($data['article_text'], 0, 50, 'UTF-8');

                DB::table('laws')->updateOrInsert([
                    'law_name'       => $data['law_name'],
                    'article_number' => $data['article_number'],
                    'article_text'   => $shortText
                ], $data);

                $count++;
                $totalCount++;
            }
            fclose($file);
            $this->info("✅ تم إيداع {$count} مادة كاملة ومطابقة من {$fileName}");

            DB::statement("SELECT setval(pg_get_serial_sequence('laws', 'id'), COALESCE(MAX(id), 1)) FROM laws;");
        }

        $this->info("🎉 النتيجة النهائية: تم رفع إجمالي {$totalCount} مادة قانونية بربط التصنيفات السليم 100%.");
    }
}