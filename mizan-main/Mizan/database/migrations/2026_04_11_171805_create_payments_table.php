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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // ربط الدفعة بالمستخدم (الموكل) الذي قام بالدفع
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // ربط الدفعة بالاستشارة المعنية
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            // المبلغ المدفوع
            $table->decimal('amount', 10, 2); 
            // حالة الدفع (قيد الانتظار، مكتملة، فشلت)
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            // وسيلة الدفع (مثلاً: سداد، بطاقة مصرفية، كاش)
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};