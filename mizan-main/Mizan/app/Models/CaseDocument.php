<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseDocument extends Model
{
    use HasFactory;

    protected $table = 'case_documents';

    protected $fillable = [
        'case_id',
        'sender_id',
        'receiver_id',
        'file_path',
        'document_name',
        'document_type',
        'description',
    ];

    /**
     * المستند تابع لقضية
     */
    public function legalCase()
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    /**
     * المرسل (محامي أو موكل)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * المستلم
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
