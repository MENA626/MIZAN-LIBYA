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
        Schema::create('case_documents', function (Blueprint $table) {
            $table->id();
            // ربط المستند بالقضية التابع لها
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            // تحديد من أرسل الملف (سواء المحامي أو الموكل)
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            // تحديد من سيستلم الملف
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            // مسار تخزين ملف القضية
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_documents');
    }
};