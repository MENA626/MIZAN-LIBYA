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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // ربط الجدول بملف المحامي
            $table->foreignId('lawyer_id')->constrained('lawyer_profiles')->onDelete('cascade');
            
            // تخزين اليوم (مثلاً: Sunday, Monday...)
            $table->string('day_of_week'); 
            
            // وقت البداية والنهاية
            $table->time('start_time')->nullable(); 
            $table->time('end_time')->nullable();
            
            // هل اليوم عطلة؟ (عشان لو المحامي سكر يوم كامل)
            $table->boolean('is_off')->default(false); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};