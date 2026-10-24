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
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            // الربط مع جدول القوانين
            $table->foreignId('law_id')->constrained('laws')->onDelete('cascade');
            // حقل من نوع JSON لتخزين مصفوفة الأرقام (Vectors) الخاصة بالذكاء الاصطناعي
            $table->json('vector'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};