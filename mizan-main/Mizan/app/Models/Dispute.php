<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lawyer_id', 'disputable_type', 'disputable_id', 'reason', 'status', 'admin_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function disputable()
    {
        return $this->morphTo();
    }
}
