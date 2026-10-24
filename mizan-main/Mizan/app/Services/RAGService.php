<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use App\Models\Law; // افترضنا أن عندك موديل للقوانين

class RAGService
{
    public function getLegalAnswer($userQuestion)
    {
        // 1. مرحلة الاسترجاع (Retrieval)
        // هنا نبحث عن القوانين ذات الصلة بسؤال المستخدم
        $relevantLaws = Law::where('content', 'like', "%{$userQuestion}%")
                            ->limit(3)
                            ->get();

        $context = "";
        foreach ($relevantLaws as $law) {
            $context .= "المادة: " . $law->title . " تنص على: " . $law->content . "\n";
        }

        // 2. مرحلة التوليد (Generation) باستخدام مفتاح جيميناي
        $prompt = "
            أنت مساعد قانوني ذكي في نظام 'ميزان'. 
            استخدم المعلومات التالية فقط للإجابة على سؤال المستخدم. 
            إذا لم تجد الإجابة، قل أنك لا تملك المعلومة في قاعدة بياناتنا القانونية.
            
            المعلومات القانونية:
            $context
            
            سؤال المستخدم:
            $userQuestion
        ";

        $response = Gemini::geminiPro()->generateContent($prompt);

        return $response->text();
    }
}