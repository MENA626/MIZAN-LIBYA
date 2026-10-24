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
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Drop the old constrained foreign key if it existed as constrained
            if (Schema::hasColumn('chat_sessions', 'consultation_id')) {
                $table->unsignedBigInteger('consultation_id')->nullable()->change();
            }
            
            // Add polymorphic relationships
            if (!Schema::hasColumn('chat_sessions', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('chat_sessions', 'lawyer_id')) {
                $table->unsignedBigInteger('lawyer_id')->nullable()->after('client_id');
            }
            if (!Schema::hasColumn('chat_sessions', 'chatable_type')) {
                $table->nullableMorphs('chatable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('chat_sessions', 'client_id')) {
                $colsToDrop[] = 'client_id';
            }
            if (Schema::hasColumn('chat_sessions', 'lawyer_id')) {
                $colsToDrop[] = 'lawyer_id';
            }
            if (Schema::hasColumn('chat_sessions', 'chatable_type')) {
                $colsToDrop[] = 'chatable_type';
            }
            if (Schema::hasColumn('chat_sessions', 'chatable_id')) {
                $colsToDrop[] = 'chatable_id';
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};

