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
        if (!Schema::hasTable('disputes')) {
            Schema::create('disputes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // العميل صاحب البلاغ
                $table->foreignId('lawyer_id')->constrained('users')->onDelete('cascade'); // المحامي
                $table->nullableMorphs('disputable'); // الاستشارة أو القضية المتعلق بها البلاغ
                $table->text('reason'); // سبب البلاغ
                $table->enum('status', ['open', 'resolved', 'closed'])->default('open');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};

