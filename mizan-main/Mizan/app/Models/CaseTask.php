<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseTask extends Model
{
    use HasFactory;

    protected $table = 'case_tasks';

    protected $fillable = [
        'case_id',
        'title',
        'description',
        'due_date',
        'priority',
        'is_completed',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'is_completed' => 'boolean',
    ];

    /**
     * المهمة تابعة لقضية
     */
    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }
}
