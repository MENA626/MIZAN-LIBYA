<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id', 'folder_id', 'title', 'file_path',
        'classification', 'importance', 'expiry_date', 'notes', 'is_task'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_task' => 'boolean',
        'user_id' => 'string',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function summaries()
    {
        return $this->hasMany(DocumentSummary::class, 'document_id', 'id');
    }
}