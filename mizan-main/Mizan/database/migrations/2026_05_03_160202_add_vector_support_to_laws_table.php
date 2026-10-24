<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laws', function (Blueprint $table) {
            // التحقق وإضافة عمود الاسم
            if (!Schema::hasColumn('laws', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            // التحقق وإضافة التفسير المبسط
            if (!Schema::hasColumn('laws', 'simplified_explanation')) {
                $table->text('simplified_explanation')->nullable();
            }
            // التحقق وإضافة رقم الصفحة
            if (!Schema::hasColumn('laws', 'pdf_page')) {
                $table->integer('pdf_page')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('laws', function (Blueprint $table) {
            $table->dropColumn(['name', 'simplified_explanation', 'pdf_page']);
        });
    }
};