<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidConsultation extends Model
{
    use HasFactory;

    // 🌟 الخطوة السحرية الإجبارية: ربط الموديل بجدول السحابة الجديد المنفصل تماماً
    protected $table = 'paid_consultations';

    protected $fillable = [
        'user_id',
        'lawyer_id',
        'title',
        'legal_category',
        'client_role',
        'opponent_name',
        'urgency_level',
        'has_existing_case',
        'court_or_deadline',
        'description',
        'attachment',
        'status',
        'price'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
}