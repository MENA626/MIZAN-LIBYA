<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LawyerCase extends Model
{
    use HasFactory;

    protected $table = 'cases'; // 

    protected $fillable = [
        'user_id',     // معرف العميل 
        'lawyer_id',   // معرف المحامي 
        'title',       // عنوان القضية 
        'description', // تفاصيل القضية 
        'case_type',   // نوع القضية 
        'status',      // 'pending', 'active', 'closed' 
    ];

    // القضية تنتمي لموكل (مستخدم) 
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // القضية تابعة لمحامي 
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
}