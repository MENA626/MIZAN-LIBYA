<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $fillable = ['document_id', 'title', 'reminder_date', 'is_notified'];

    protected $casts = [
        'reminder_date' => 'date',
        'is_notified' => 'boolean'
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}