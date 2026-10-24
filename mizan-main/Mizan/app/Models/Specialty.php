<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    // تم التعديل هنا ليتطابق مع اسم الجدول الحقيقي في قاعدة البيانات
    protected $table = 'specialties'; 

    // الحقول المسموح بحفظها
    protected $fillable = ['name'];

    /**
     * علاقة التخصص مع بروفايلات المحاميين
     */
    public function lawyerProfiles(): HasMany
    {
        return $this->hasMany(LawyerProfile::class, 'specialty_id');
    }
}