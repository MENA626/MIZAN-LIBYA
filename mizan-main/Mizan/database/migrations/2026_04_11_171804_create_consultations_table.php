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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            // ربط الاستشارة بالمستخدم (الموكل)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // ربط الاستشارة بالمحامي المطلوب
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            // حالة الاستشارة (قيد الانتظار، نشطة، مكتملة)
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};