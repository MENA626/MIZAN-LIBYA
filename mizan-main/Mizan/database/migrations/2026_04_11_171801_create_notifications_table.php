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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            // ربط الإشعار بالمستخدم المستهدف
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title'); // عنوان الإشعار
            $table->text('body');  // محتوى الإشعار بالتفصيل
            $table->boolean('is_read')->default(false); // حالة القراءة (غير مقروء افتراضياً)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};