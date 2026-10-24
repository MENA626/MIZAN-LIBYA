<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_session_id', 
        'sender_id', 
        'message_text', 
        'is_read',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size'
    ];
    
    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function hasAttachment()
    {
        return !is_null($this->attachment_path);
    }
    
    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment_path) {
            return asset('storage/' . $this->attachment_path);
        }
        return null;
    }
    
    public function getFormattedAttachmentSizeAttribute()
    {
        if (!$this->attachment_size) return null;
        
        $bytes = $this->attachment_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}