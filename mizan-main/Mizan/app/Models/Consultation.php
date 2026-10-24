<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    protected $table = 'cases';
    
    protected $fillable = [
        'user_id', // ✨ تم التعديل هنا
        'lawyer_id',
        'title',
        'description',
        'status',
        'consultation_date',
        'meeting_link',
        'duration',
        'amount',
        'payment_status',
        'notes'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'duration' => 'integer',
        'amount' => 'decimal:2'
    ];

    /**
     * العلاقة مع المستخدم (العميل سابقاً)
     */
    public function user(): BelongsTo // ✨ تم تعديل اسم الدالة والعلاقة لترتبط بـ user_id مباشرة
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * العلاقة مع المحامي
     */
    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    /**
     * العلاقة مع جلسة الشات
     */
    public function chatSession(): HasOne
    {
        return $this->hasOne(ChatSession::class);
    }

    /**
     * العلاقة مع المدفوعات
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * العلاقة مع المراجعات
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * التحقق من حالة الاستشارة
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * الحصول على اسم الحالة بالعربية
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد المراجعة',
            'active' => 'نشطة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغية',
            default => 'غير معروف'
        };
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'active' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}