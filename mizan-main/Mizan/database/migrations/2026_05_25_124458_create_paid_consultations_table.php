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
        if (!Schema::hasTable('paid_consultations')) {
            Schema::create('paid_consultations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('lawyer_id')->constrained('users');
                $table->string('title');
                $table->string('legal_category');
                $table->string('client_role');
                $table->string('opponent_name')->nullable();
                $table->string('urgency_level');
                $table->boolean('has_existing_case')->default(false);
                $table->string('court_or_deadline')->nullable();
                $table->text('description');
                $table->string('attachment')->nullable();
                $table->string('status')->default('pending');
                $table->decimal('price', 10, 2);
                $table->text('summary')->nullable(); 
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_consultations');
    }
};