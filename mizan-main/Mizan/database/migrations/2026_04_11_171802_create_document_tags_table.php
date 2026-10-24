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
        Schema::create('document_tags', function (Blueprint $table) {
            // ربط المادة بالمستند
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            // ربط المادة بالوسم
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            
            // جعل الزوج (document_id و tag_id) مفتاحاً أساسياً لمنع تكرار نفس الوسم لنفس الملف
            $table->primary(['document_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_tags');
    }
};
