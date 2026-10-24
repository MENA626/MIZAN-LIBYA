<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiBriefMessage extends Model
{
    use HasUuids;

    // إجبار الموديل على اسم جدول Supabase المطابق تماماً
    protected $table = 'ai_brief_messages'; 

    // إعلام لارافيل أن المعرف الرئيسي ليس رقماً تسلسلياً تلقائياً بل هو UUID (نصي)
    public $incrementing = false;
    protected $keyType = 'string';

    // فك حظر الحفظ بالكامل لضمان تدفق البيانات بسلاسة من الـ AJAX والـ API
    protected $guarded = [];

    /**
     * علاقة عكسية: الرسالة تنتمي إلى مذكرة استشارية رئيسية واحدة
     */
    public function brief(): BelongsTo
    {
        return $this->belongsTo(AiLegalBrief::class, 'brief_id');
    }
}