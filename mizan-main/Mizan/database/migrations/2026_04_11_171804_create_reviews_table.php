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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // ربط التقييم بالمحامي الذي تم تقييمه
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            // ربط التقييم بالاستشارة المعنية لضمان أن التقييم حقيقي
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            // درجة التقييم (مثلاً من 1 إلى 5)
            $table->integer('rating');
            // تعليق المستخدم النصي
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};