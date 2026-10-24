<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\LawyerProfile;
use App\Models\LegalCase;
use App\Models\Review;
use App\Models\LawyerWorkingHour;
use App\Models\Folder;
use App\Models\Document;
use App\Models\DocumentSummary;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * الحقول القابلة للتعبئة جماعياً
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'status',
        'notification_settings',
    ];

    /**
     * الحقول المخفية
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع البيانات
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_settings' => 'array',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function lawyerProfile()
    {
        return $this->hasOne(LawyerProfile::class)->withDefault();
    }

    public function cases()
    {
        return $this->hasMany(LegalCase::class, 'lawyer_id');
    }

    public function reviews() 
    {
        return $this->hasMany(Review::class, 'lawyer_id');
    }

    public function lawyerWorkingHours()
    {
        return $this->hasMany(LawyerWorkingHour::class, 'lawyer_id', 'id');
    }

    /**
     * دالة إضافية لتسهيل الوصول لأوقات العمل
     */
    public function workingHours()
    {
        return $this->lawyerWorkingHours();
    }

    public function folders()
    {
        return $this->hasMany(Folder::class, 'user_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id', 'id');
    }

    public function summaries()
    {
        return $this->hasMany(DocumentSummary::class, 'user_id', 'id');
    }
}