<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCase extends Model
{
    use HasFactory;

    // تحديد اسم الجدول الفعلي في Supabase
    protected $table = 'cases';

    // الحقول المسموح بحفظها وتعديلها بالجملة
    protected $fillable = [
        'case_number',
        'user_id',       
        'lawyer_id',     
        'title', 
        'client_name', 
        'client_phone', 
        'case_type', 
        'case_category',
        'court_name',
        'opponent_name',
        'claim_amount',
        'total_fees',
        'paid_fees',
        'status', 
        'filing_date', 
        'details',
        'file_path' 
    ];

    // تحويل الحقول تلقائياً
    protected $casts = [
        'filing_date'  => 'date',
        'claim_amount' => 'decimal:2',
        'total_fees'   => 'decimal:2',
        'paid_fees'    => 'decimal:2',
    ];

    /**
     * علاقة القضية بالموكل (العميل صاحب القضية)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة القضية بالمستخدم (نفس client لكن باسم user للتوافق)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة القضية بالمحامي المسؤول
     */
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    /**
     * جلسات القضية
     */
    public function sessions()
    {
        return $this->hasMany(CaseSession::class, 'case_id')->orderBy('session_date', 'desc');
    }

    /**
     * تحديثات المحامي على القضية
     */
    public function updates()
    {
        return $this->hasMany(CaseUpdate::class, 'case_id')->orderBy('created_at', 'desc');
    }

    /**
     * المهام المطلوبة من الموكل
     */
    public function tasks()
    {
        return $this->hasMany(CaseTask::class, 'case_id')->orderBy('created_at', 'desc');
    }

    /**
     * مستندات القضية
     */
    public function caseDocuments()
    {
        return $this->hasMany(CaseDocument::class, 'case_id')->orderBy('created_at', 'desc');
    }

    /**
     * الملف المهني للمحامي
     */
    public function lawyerProfile()
    {
        return $this->hasOneThrough(
            LawyerProfile::class,
            User::class,
            'id',          // users.id
            'user_id',     // lawyer_profiles.user_id
            'lawyer_id',   // cases.lawyer_id
            'id'           // users.id
        );
    }

    /**
     * آخر جلسة مكتملة
     */
    public function latestCompletedSession()
    {
        return $this->hasOne(CaseSession::class, 'case_id')
            ->where('status', 'completed')
            ->orderBy('session_date', 'desc');
    }

    /**
     * الجلسة القادمة المجدولة
     */
    public function nextScheduledSession()
    {
        return $this->hasOne(CaseSession::class, 'case_id')
            ->where('status', 'scheduled')
            ->where('session_date', '>=', now())
            ->orderBy('session_date', 'asc');
    }

    /**
     * التحديثات العاجلة غير المقروءة
     */
    public function urgentUpdates()
    {
        return $this->hasMany(CaseUpdate::class, 'case_id')
            ->whereIn('update_type', ['urgent', 'warning'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * المهام غير المكتملة
     */
    public function pendingTasks()
    {
        return $this->hasMany(CaseTask::class, 'case_id')
            ->where('is_completed', false)
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END")
            ->orderBy('due_date', 'asc');
    }

    /**
     * حساب المبلغ المستحق
     */
    public function getRemainingFeesAttribute()
    {
        return ($this->total_fees ?? 0) - ($this->paid_fees ?? 0);
    }
}
