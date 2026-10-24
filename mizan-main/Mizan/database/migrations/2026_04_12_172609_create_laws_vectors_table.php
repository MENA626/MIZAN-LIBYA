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
    Schema::create('laws_vectors', function (Blueprint $table) {
        $table->id();
        $table->string('article_number');
        $table->text('article_text');
        $table->json('vector_data'); // لتخزين مصفوفة الأرقام (Vector)
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laws_vectors');
    }
};
