<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    protected $table = 'laws';

    protected $fillable = [
        'category_id',
        'article_number',
        'article_title',
        'article_text',
        'simplified_explanation',
        'name',
        'pdf_page',
        'embedding' // هذا هو العمود السحري
    ];

    protected $casts = [
        'embedding' => 'array', // ضروري جداً ليتعامل Laravel مع المتجهات كمصفوفة أرقام
    ];
}