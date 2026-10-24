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
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('lawyer_profiles', 'account_name')) {
                $table->string('account_name')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('lawyer_profiles', 'account_number')) {
                $table->string('account_number')->nullable()->after('account_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_name', 'account_number', 'iban']);
        });
    }
};
