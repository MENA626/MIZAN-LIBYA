<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    // تحديد اسم الجدول في سوبابيز
    protected $table = 'appointments';

    // الحقول المسموح بتعبئتها
    protected $fillable = [
    'user_id', 'lawyer_id', 'date_time', 'status', 'notes', // أضف notes هنا
];

    /**
     * تحويل الحقول إلى أنواع بيانات معينة
     * هذا يضمن أن date_time يتم تحويله تلقائياً إلى كائن Carbon
     */
    protected $casts = [
        'date_time' => 'datetime',
    ];

    /**
     * العلاقة مع المحامي
     */
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    /**
     * العلاقة مع العميل (المستخدم الذي حجز الموعد)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}