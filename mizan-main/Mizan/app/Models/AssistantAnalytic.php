<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistantAnalytic extends Model
{
    use HasFactory;

    // تحديد اسم الجدول المطابق لما أنشأناه في Supabase
    protected $table = 'assistant_analytics';

    // إيقاف التحديث التلقائي للوقت لأننا نعتمد على created_at الخاص بـ Supabase فقط
    public $timestamps = false; 

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'question_text',
        'ai_response_text',
        'legal_category',
        'user_feedback',
        'is_resolved',
    ];

    // تحويل أنواع البيانات لسهولة التعامل معها برمجياً
    protected $casts = [
        'user_feedback' => 'boolean',
        'is_resolved' => 'boolean',
    ];
}