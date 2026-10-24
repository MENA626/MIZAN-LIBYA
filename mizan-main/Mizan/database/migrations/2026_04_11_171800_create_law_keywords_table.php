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
        Schema::create('law_keywords', function (Blueprint $table) {
            $table->id();
            // الربط مع جدول القوانين الأساسي
            $table->foreignId('law_id')->constrained('laws')->onDelete('cascade');
            $table->string('keyword'); // الكلمة المفتاحية (مثل: تعويض، طلاق، عقار)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_keywords');
    }
};