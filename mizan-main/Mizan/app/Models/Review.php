<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PaidConsultation;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'lawyer_id',
        'consultation_id',
        'rating',
        'comment'
    ];

    /*
    |---------------------------
    | العميل الذي كتب التقييم
    |---------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |---------------------------
    | المحامي المقيم
    |---------------------------
    */
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    /*
    |---------------------------
    | الاستشارة المدفوعة المرتبطة بالتقييم
    |---------------------------
    */
    public function consultation()
    {
        return $this->belongsTo(PaidConsultation::class, 'consultation_id');
    }
}