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
        Schema::create('lawyer_profiles', function (Blueprint $table) {
            $table->id();
            
            // ربط ملف المحامي بجدول المستخدمين
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // التعديل هنا: ربط الحقل بجدول الفئات الفعلي لديكِ law_categories
            $table->foreignId('specialty_id')->nullable()->constrained('law_categories')->onDelete('set null');

            // حقل الدرجة القضائية والقيمة الافتراضية '2' (محام معتمد لدى المحكمة العليا)
            $table->string('specialization')->default('2'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_profiles');
    }
};