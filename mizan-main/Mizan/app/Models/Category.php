<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // إيقاف التحديث التلقائي لحقل updated_at لأن جدولك لا يحتويه
    const UPDATED_AT = null;

    protected $fillable = [
        'name', 
        'icon'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}