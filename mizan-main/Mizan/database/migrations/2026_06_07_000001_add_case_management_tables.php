<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * توسيع جدول القضايا الحالي وإنشاء جداول إدارة القضايا المتقدمة
     */
    public function up(): void
    {
        // 1. توسيع جدول القضايا الحالي بحقول جديدة
        Schema::table('cases', function (Blueprint $table) {
            // جعل user_id اختيارياً (القضية قد لا تكون مرتبطة بمستخدم مسجل بعد)
            $table->string('case_category')->nullable()->after('case_type');
            $table->string('court_name')->nullable()->after('case_category');
            $table->string('opponent_name')->nullable()->after('court_name');
            $table->decimal('claim_amount', 12, 2)->nullable()->after('opponent_name');
            $table->decimal('total_fees', 12, 2)->nullable()->after('claim_amount');
            $table->decimal('paid_fees', 12, 2)->nullable()->after('total_fees');
        });

        // 2. جدول جلسات القضية
        Schema::create('case_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->dateTime('session_date');
            $table->string('session_type')->default('مرافعة');
            $table->string('location')->nullable();
            $table->text('summary')->nullable();
            $table->boolean('our_attendance')->default(true);
            $table->boolean('opponent_attendance')->default(false);
            $table->boolean('expert_attendance')->nullable();
            $table->text('opponent_claims')->nullable();
            $table->text('our_response')->nullable();
            $table->text('postponement_reason')->nullable();
            $table->dateTime('next_session_date')->nullable();
            $table->text('next_session_goal')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, postponed
            $table->timestamps();
        });

        // 3. جدول تحديثات المحامي على القضية
        Schema::create('case_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('update_type')->default('info'); // info, warning, urgent, court_update
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // 4. جدول المهام المطلوبة من الموكل
        Schema::create('case_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default('normal'); // normal, urgent
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // 5. توسيع جدول مستندات القضية الموجود
        if (Schema::hasTable('case_documents')) {
            Schema::table('case_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('case_documents', 'document_name')) {
                    $table->string('document_name')->nullable()->after('file_path');
                }
                if (!Schema::hasColumn('case_documents', 'document_type')) {
                    $table->string('document_type')->nullable()->after('document_name');
                }
                if (!Schema::hasColumn('case_documents', 'description')) {
                    $table->text('description')->nullable()->after('document_type');
                }
            });
        }
    }

    /**
     * التراجع عن التغييرات
     */
    public function down(): void
    {
        Schema::dropIfExists('case_tasks');
        Schema::dropIfExists('case_updates');
        Schema::dropIfExists('case_sessions');

        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn([
                'case_category', 'court_name', 'opponent_name',
                'claim_amount', 'total_fees', 'paid_fees'
            ]);
        });

        if (Schema::hasTable('case_documents')) {
            Schema::table('case_documents', function (Blueprint $table) {
                if (Schema::hasColumn('case_documents', 'document_name')) $table->dropColumn('document_name');
                if (Schema::hasColumn('case_documents', 'document_type')) $table->dropColumn('document_type');
                if (Schema::hasColumn('case_documents', 'description')) $table->dropColumn('description');
            });
        }
    }
};
