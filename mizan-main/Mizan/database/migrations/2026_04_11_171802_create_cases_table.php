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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            // ربط القضية بالمؤكل (المستخدم العادي)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // ربط القضية بالمحامي المسؤول
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            // عنوان القضية (مثلاً: قضية تعويض مالي)
            $table->string('title');
            // حالة القضية
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};