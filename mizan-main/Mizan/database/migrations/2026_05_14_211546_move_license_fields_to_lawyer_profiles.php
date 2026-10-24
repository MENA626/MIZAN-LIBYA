<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. إضافة الحقول لجدول lawyer_profiles إذا لم تكن موجودة
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('lawyer_profiles', 'license_number')) {
                $table->string('license_number')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('lawyer_profiles', 'syndicate_card_path')) {
                $table->string('syndicate_card_path')->nullable();
            }
        });

        // 2. حذف الحقول من جدول users إذا كانت موجودة
        Schema::table('users', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('users', 'license_number')) {
                $colsToDrop[] = 'license_number';
            }
            if (Schema::hasColumn('users', 'syndicate_card_path')) {
                $colsToDrop[] = 'syndicate_card_path';
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'license_number')) {
                $table->string('license_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'syndicate_card_path')) {
                $table->string('syndicate_card_path')->nullable();
            }
        });

        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('lawyer_profiles', 'license_number')) {
                $colsToDrop[] = 'license_number';
            }
            if (Schema::hasColumn('lawyer_profiles', 'syndicate_card_path')) {
                $colsToDrop[] = 'syndicate_card_path';
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};