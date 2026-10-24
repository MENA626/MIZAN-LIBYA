<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LawyerProfile extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها جماعياً (Mass Assignment)
     */
    protected $fillable = [
        'user_id', 
        'license_number', 
        'syndicate_card_path', 
        'bio', 
        'years_of_experience', 
        'address', 
        'bank_name', 
        'iban', 
        'account_name',        
        'account_number',      
        'specialty_id',
        'specialization', 
        'experience',          
        'qualifications',      
        'rating',              
        'rejection_reason',    
    ];

    /**
     * الذكاء البرمجي للتعامل مع القيم الافتراضية (Accessors)
     * إذا كانت القيمة في قاعدة البيانات NULL، يتم إرجاع قيمة افتراضية آمنة منعاً للأخطاء
     */

    // دالة التخصص الافتراضي
    public function getSpecializationAttribute($value)
    {
        return $value ?? '2';
    }

    // دالة سنوات الخبرة: إذا كانت NULL ترجع 0
    public function getYearsOfExperienceAttribute($value)
    {
        return $value ?? 0;
    }

    // دالة التقييم العام: إذا كانت NULL ترجع 0
    public function getRatingAttribute($value)
    {
        return $value ?? 0;
    }

    // دالة سعر الاستشارة الافتراضي: إذا كانت NULL ترجع 0
    public function getConsultationPriceAttribute($value)
    {
        return $value ?? 0;
    }

    /**
     * 1️⃣ العلاقة العكسية المحددة مع المستخدم (User)
     * لجلب البيانات الأساسية للمحامي (الاسم، البريد الإلكتروني، الهاتف، الصورة الشخصية)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 2️⃣ علاقة التقييمات والتعليقات (Reviews)
     * هامة جداً لاستقبال وحفظ النجوم والملاحظات التي يرسلها العميل فور انتهاء الاستشارة
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'lawyer_id', 'user_id');
    }

    /**
     * 3️⃣ جلب بيانات التخصص القانوني الديناميكي (Specialty)
     * لربط المحامي بقسمه القانوني المحدد (جنائي، أحوال شخصية، تجاري... إلخ)
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    /**
     * 4️⃣ علاقة أوقات العمل والجدول الزمني (Schedules) - [تم دمجها بنجاح]
     * لتمكين العملاء من استعراض المواعيد المتاحة للمحامي وحجزها بدقة
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'lawyer_id');
    }
}