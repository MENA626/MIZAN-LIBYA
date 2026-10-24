<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerWorkingHour extends Model
{
    // هذا السطر يربط المودل بالجدول الذي أنشأته في قاعدة البيانات
    protected $table = 'lawyer_working_hours'; 

    // هذه الأعمدة هي التي سيسمح لارافيل بتعديلها أو إضافتها (Mass Assignment)
    protected $fillable = ['lawyer_id', 'day_of_week', 'start_time', 'end_time', 'is_active'];
}