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
            if (!Schema::hasColumn('lawyer_profiles', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('lawyer_profiles', 'iban')) {
                $table->string('iban')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lawyer_profiles', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('lawyer_profiles', 'bank_name')) {
                $colsToDrop[] = 'bank_name';
            }
            if (Schema::hasColumn('lawyer_profiles', 'iban')) {
                $colsToDrop[] = 'iban';
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};