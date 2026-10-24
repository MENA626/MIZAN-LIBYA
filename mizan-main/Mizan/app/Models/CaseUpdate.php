<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseUpdate extends Model
{
    use HasFactory;

    protected $table = 'case_updates';

    protected $fillable = [
        'case_id',
        'lawyer_id',
        'title',
        'content',
        'update_type',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    /**
     * التحديث تابع لقضية
     */
    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    /**
     * التحديث أضافه محامي
     */
    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
}
