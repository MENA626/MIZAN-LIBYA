<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // اسم الجدول في Supabase
    protected $table = 'payments'; 

    // الحقول مطابقة تماماً للقطة الشاشة الحية الخاصة بكِ
    protected $fillable = [
        'user_id',
        'consultation_id',
        'amount',
        'payment_status',
        'payment_method'
    ];

    /**
     * علاقة الدفعة بالاستشارة التابعة لها
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    /**
     * علاقة الدفعة بالعميل (الموكل) الذي قام بالدفع
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}