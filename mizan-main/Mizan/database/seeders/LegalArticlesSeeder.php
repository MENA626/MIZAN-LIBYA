<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LegalArticlesSeeder extends Seeder
{
    public function run()
    {
        // تحديد ID الفئة لكل قانون (تأكدي أن هذه الأرقام موجودة في جدول categories عندك)
        $lawConfigs = [
            'penal_code.csv' => ['id' => 1, 'name' => 'قانون العقوبات'],
            'criminal_procedures_law.csv' => ['id' => 2, 'name' => 'قانون الإجراءات الجنائية'],
            'anti_cybercrime_law.csv' => ['id' => 3, 'name' => 'قانون مكافحة الجرائم الإلكترونية'],
            'personal_status_law.csv' => ['id' => 4, 'name' => 'قانون الأحوال الشخصية'],
        ];

        foreach ($lawConfigs as $fileName => $config) {
            $filePath = database_path('data/' . $fileName); 

            if (!File::exists($filePath)) {
                $this->command->warn("الملف $fileName غير موجود في database/data/");
                continue;
            }

            $fileHandle = fopen($filePath, 'r');
            fgetcsv($fileHandle); // تخطي العناوين

            $data = [];
            while (($row = fgetcsv($fileHandle)) !== false) {
                if (empty(array_filter($row))) continue;

               $data[] = [
                    'category_id'            => $config['id'],
                    'article_number'         => $row[6] ?? '', 
                    'article_title'          => $row[7] ?? null, // استخدام ?? بدلاً من ?: لمنع الخطأ
                    'article_text'           => $row[8] ?? '',
                    'simplified_explanation' => $row[9] ?? null,
                    'pdf_page'               => (isset($row[10]) && is_numeric($row[10])) ? (int)$row[10] : null,
                    'name'                   => $config['name'],
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ];
                
            }
            fclose($fileHandle);

            foreach (array_chunk($data, 100) as $chunk) {
                DB::table('laws')->insert($chunk); // اسم جدولك الحالي
            }
            $this->command->info("تم بنجاح صب بيانات: " . $config['name']);
        }
    }
}