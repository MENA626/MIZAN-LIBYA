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
            // إضافة الأعمدة فقط إذا لم تكن موجودة مسبقاً لمنع أي أخطاء تعارض مع Supabase
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'license_number')) {
                $table->string('license_number')->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('users', 'syndicate_card_path')) {
                $table->string('syndicate_card_path')->nullable()->after('license_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'license_number', 'syndicate_card_path']);
        });
    }
};