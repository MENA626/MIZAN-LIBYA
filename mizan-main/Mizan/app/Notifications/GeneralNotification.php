<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public $data;

    // استقبال البيانات (العنوان والنص)
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // تحديد قناة الإرسال لتكون قاعدة البيانات
    public function via($notifiable)
    {
        return ['database'];
    }

    // تحويل البيانات لـ Array لتخزينها في جدول الإشعارات
    public function toArray($notifiable)
{   
    return [
        'info' => $this->data['text'], // سنمرر النص هنا
    ];
}
}