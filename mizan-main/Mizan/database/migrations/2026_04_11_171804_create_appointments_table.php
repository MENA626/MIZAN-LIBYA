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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            // ربط الموعد بالمستخدم (الموكل)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // ربط الموعد بالمحامي
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            // تاريخ ووقت الموعد
            $table->dateTime('date_time');
            // حالة الموعد (مجدول، ملغى، مكتمل)
            $table->enum('status', ['scheduled', 'canceled', 'completed'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};