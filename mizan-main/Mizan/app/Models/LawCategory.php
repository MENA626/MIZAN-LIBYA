<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawCategory extends Model
{
    // هذا السطر يخبر لارافيل بالحقول التي يسمح لنا بتعبئتها
    protected $fillable = ['id', 'name'];
}