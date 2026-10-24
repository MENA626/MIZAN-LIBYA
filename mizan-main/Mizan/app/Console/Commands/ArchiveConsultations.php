<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaidConsultation;
use App\Models\ChatSession;
use App\Models\ConsultationArchive;

class ArchiveConsultations extends Command
{
    // الاسم الذي ستستخدمينه لتشغيل الأمر يدوياً
    protected $signature = 'consultations:archive';

    // وصف الأمر
    protected $description = 'أرشفة الاستشارات التي مر عليها 20 يوماً وحذف المحادثات';

    public function handle()
    {
        // تحديد التاريخ (منذ 20 يوماً)
        $limitDate = now()->subDays(20);

        // جلب الاستشارات المكتملة أو المغلقة التي مر عليها 20 يوم
        $consultations = PaidConsultation::whereIn('status', ['completed', 'closed'])
            ->where('closed_at', '<', $limitDate)
            ->get();

        $this->info('جاري أرشفة ' . $consultations->count() . ' استشارة...');

        foreach ($consultations as $consultation) {
            $session = ChatSession::where('chatable_id', $consultation->id)
                                  ->where('chatable_type', PaidConsultation::class)
                                  ->first();
            
            if ($session) {
                // 1. نقل البيانات لجدول الأرشيف
                ConsultationArchive::create([
                    'consultation_id' => $consultation->id,
                    'lawyer_id'       => $consultation->lawyer_id,
                    'user_id'         => $consultation->user_id,
                    'chat_history'    => $session->messages, // بفضل cast array سيفهمها كـ JSON
                    'final_status'    => $consultation->status,
                ]);

                // 2. التنظيف النهائي (حذف الرسائل والجلسة)
                // تأكدي أن علاقة messages معرفة في موديل ChatSession
                $session->messages()->delete();
                $session->delete();

                $this->info("تم أرشفة الاستشارة رقم: {$consultation->id}");
            }
        }

        $this->info('تمت عملية الأرشفة بنجاح.');
    }
}