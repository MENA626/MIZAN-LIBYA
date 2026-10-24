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
        Schema::create('document_summaries', function (Blueprint $table) {
            $table->id();
            // ربط الملخص بالمستند الأصلي
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            // نص التلخيص المولد بواسطة الذكاء الاصطناعي
            $table->text('summary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_summaries');
    }
};