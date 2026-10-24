<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseSession extends Model
{
    use HasFactory;

    protected $table = 'case_sessions';

    protected $fillable = [
        'case_id',
        'session_date',
        'session_type',
        'location',
        'summary',
        'our_attendance',
        'opponent_attendance',
        'expert_attendance',
        'opponent_claims',
        'our_response',
        'postponement_reason',
        'next_session_date',
        'next_session_goal',
        'status',
    ];

    protected $casts = [
        'session_date'      => 'datetime',
        'next_session_date' => 'datetime',
        'our_attendance'      => 'boolean',
        'opponent_attendance' => 'boolean',
        'expert_attendance'   => 'boolean',
    ];

    /**
     * الجلسة تابعة لقضية
     */
    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }
}
