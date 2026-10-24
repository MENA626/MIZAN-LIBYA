<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['user_id', 'name'];

    protected $casts = [
        'user_id' => 'string',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}