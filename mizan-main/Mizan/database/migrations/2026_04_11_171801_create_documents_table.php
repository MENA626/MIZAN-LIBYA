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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // ربط المستند بصاحبه (المستخدم) [cite: 489, 516]
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            // عنوان المستند أو اسمه [cite: 488]
            $table->string('title');
            // المسار الذي سيتم تخزين الملف فيه (Laravel Storage) [cite: 82]
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};