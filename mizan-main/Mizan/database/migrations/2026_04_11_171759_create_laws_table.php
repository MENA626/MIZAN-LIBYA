<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laws', function (Blueprint $table) {
            $table->id();
            // الربط مع جدول التصنيفات
            $table->foreignId('category_id')->constrained('law_categories')->onDelete('cascade');
            $table->string('article_number'); // رقم المادة
            $table->string('article_title')->nullable(); // عنوان المادة
            $table->text('article_text'); // النص الأصلي للمادة
            $table->text('simplified_explanation')->nullable(); // التفسير المبسط للذكاء الاصطناعي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laws');
    }
};