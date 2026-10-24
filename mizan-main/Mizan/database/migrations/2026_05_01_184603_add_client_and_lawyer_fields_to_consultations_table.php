<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'client_id')) {
                $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('consultations', 'lawyer_id')) {
                $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('consultations', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('consultations', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('consultations', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('consultations', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'lawyer_id', 'title', 'description', 'amount', 'payment_status']);
        });
    }
};

