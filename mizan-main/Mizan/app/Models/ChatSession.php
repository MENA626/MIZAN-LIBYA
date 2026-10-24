<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PaidConsultation;
use App\Models\Message;

class ChatSession extends Model
{
    protected $table = 'chat_sessions';

    protected $fillable = [
        'user_id',
        'lawyer_id',
        'chatable_type',
        'chatable_id',
        'status'
    ];

    /*
    |---------------------------
    | العلاقات الأساسية
    |---------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    /*
    |---------------------------
    | ربط الاستشارة المدفوعة (Polymorphic)
    |---------------------------
    */

    public function chatable()
    {
        return $this->morphTo();
    }

    /*
    |---------------------------
    | الرسائل
    |---------------------------
    */

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_session_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'chat_session_id')->latest();
    }
}