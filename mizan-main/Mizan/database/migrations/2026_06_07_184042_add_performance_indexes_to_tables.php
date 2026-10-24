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
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'idx_users_role_status');
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->index(['lawyer_id', 'status'], 'idx_cases_lawyer_status');
            $table->index(['user_id', 'status'], 'idx_cases_user_status');
        });

        Schema::table('paid_consultations', function (Blueprint $table) {
            $table->index(['lawyer_id', 'status'], 'idx_consult_lawyer_status');
            $table->index(['user_id', 'status'], 'idx_consult_user_status');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['lawyer_id', 'date_time'], 'idx_appoint_lawyer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_status');
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->dropIndex('idx_cases_lawyer_status');
            $table->dropIndex('idx_cases_user_status');
        });

        Schema::table('paid_consultations', function (Blueprint $table) {
            $table->dropIndex('idx_consult_lawyer_status');
            $table->dropIndex('idx_consult_user_status');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appoint_lawyer_date');
        });
    }
};
