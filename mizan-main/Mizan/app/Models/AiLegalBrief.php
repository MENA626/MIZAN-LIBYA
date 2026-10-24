<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiLegalBrief extends Model
{
    use HasUuids;

    // إجبار الموديل على اسم جدول Supabase المطابق تماماً
    protected $table = 'ai_legal_briefs'; 

    // إعلام لارافيل أن المعرف الرئيسي ليس رقماً تسلسلياً تلقائياً بل هو UUID (نصي)
    public $incrementing = false;
    protected $keyType = 'string';

    // فك حظر الحفظ بالكامل لضمان تدفق البيانات بسلاسة وتجنب أي MassAssignmentException
    protected $guarded = [];

    /**
     * علاقة المذكرة الاستشارية بالرسائل التابعة لها (مرتبة تصاعدياً من الأقدم للأحدث)
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiBriefMessage::class, 'brief_id')->orderBy('created_at', 'asc');
    }
}