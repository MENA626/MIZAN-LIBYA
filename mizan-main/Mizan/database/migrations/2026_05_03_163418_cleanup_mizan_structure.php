<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. حذف جداول المتجهات المكررة (البيانات الآن مخزنة في عمود embedding داخل جدول laws)
        Schema::dropIfExists('embeddings');
        Schema::dropIfExists('laws_vectors');

        // 2. تطوير جدول المستندات (Documents)
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                // إضافة عمود الملخص الذكي للجدول الأصلي بدلاً من جدول منفصل
                if (!Schema::hasColumn('documents', 'ai_summary')) {
                    $table->text('ai_summary')->nullable();
                }
            });
        }

        // 3. حذف جدول الملخصات المنفصل لتبسيط الاستعلامات (Queries)
        Schema::dropIfExists('document_summaries');
        
        // 4. حذف جدول الكلمات المفتاحية إذا كنتِ ستعتمدين على الوسوم (Tags) العامة
        // Schema::dropIfExists('law_keywords'); 
    }

    public function down()
    {
        // لا نحتاج لعمل شيء هنا في الغالب
    }
};