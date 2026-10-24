<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Summary extends Model
{
    // ✅ تغيير اسم الجدول ليتناسب مع الـ migration
    protected $table = 'document_summaries';

    // ✅ السماح بالحقول التالية للتعامل مع البيانات
    protected $fillable = [
        'user_id',           // UUID الخاص بالمستخدم في Supabase
        'document_id',       // معرف المستند الأصلي (اختياري)
        'document_name',     // اسم المستند
        'content',           // محتوى الملخص
        'file_path'          // مسار الملف (اختياري)
    ];

    // ✅ إضافة الـ casts للتعامل مع أنواع البيانات بشكل صحيح
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'document_id' => 'integer'
    ];

    // ✅ العلاقة مع المستخدم (Supabase UUID)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ✅ العلاقة مع المستند الأصلي (اختياري)
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }
}