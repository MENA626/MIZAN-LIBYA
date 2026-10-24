<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationArchive extends Model
{
    /**
     * تحديد اسم الجدول المرتبط بالموديل
     */
    protected $table = 'consultation_archives';

    /**
     * الحقول المسموح بتعبئتها جماعياً (Mass Assignment)
     */
    protected $fillable = [
        'consultation_id', 
        'lawyer_id', 
        'user_id', 
        'chat_history', 
        'final_status'
    ];

    /**
     * تحويل حقل الـ chat_history من نص JSON في القاعدة إلى مصفوفة PHP تلقائياً
     */
    protected $casts = [
        'chat_history' => 'array',
    ];

    /**
     * تفعيل الـ timestamps (created_at, updated_at) 
     * ليتولى لارافيل إدارتها تلقائياً
     */
    public $timestamps = true;
}