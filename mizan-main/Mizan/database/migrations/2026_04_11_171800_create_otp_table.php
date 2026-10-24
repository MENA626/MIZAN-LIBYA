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
        // تغيير اسم الجدول من otp إلى user_otps لتجنب أي تعارض
        Schema::create('user_otps', function (Blueprint $table) {
            $table->id();
            // تحديد طول الكود بـ 6 خانات مثلاً لضمان التوافق
            $table->string('code', 6); 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_otps');
    }
};