<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSummary extends Model
{
    protected $table = 'document_summaries';

    protected $fillable = [
        'user_id',
        'document_id',
        'document_name',
        'content',
        'file_path'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }
}