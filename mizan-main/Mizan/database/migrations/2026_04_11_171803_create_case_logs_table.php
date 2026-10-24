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
        Schema::create('case_logs', function (Blueprint $table) {
            $table->id();
            // ربط السجل بالقضية المعنية
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            // نوع الإجراء (مثلاً: تحديث حالة، إضافة مستند، تغيير موعد)
            $table->string('action');
            // تفاصيل إضافية عن الإجراء
            $table->text('description')->nullable();
            // تاريخ ووقت وقوع الإجراء يتم تخزينه تلقائياً عبر timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_logs');
    }
};