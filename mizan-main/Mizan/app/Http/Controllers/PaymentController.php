<?php


namespace App\Http\Controllers;

// هادو الموديلات اللي بنستعملوهم باش نكلمو الداتا بيز (قاعدة البيانات)
use App\Models\PaidConsultation;
use App\Models\Payment; // 🌟 استدعينا مودل الدفع هني باش نقدرو نخزنو فيه البيانات
use Illuminate\Http\Request;
use PlutuLocalBankCards; // هدي المكتبة (الباكج) الخاصة ببوابة الدفع Plutu
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 

class PaymentController extends Controller
{
    /**
     * -------------------------------------------------------------------
     *  Plutu الدالة هذي تبدا عملية الدفع وترفع الزبون لصفحة البنك في منصة 
     * -------------------------------------------------------------------
     */
    public function checkout($id)
    {
        // 1.  (ID) نمشو للداتا بيز وندورو على الاستشارة برقمها 
        $consultation = PaidConsultation::findOrFail($id);

        // 2. هني نديرو حماية: نتأكدو إن اللي خاش توا هو نفس صاحب الاستشارة + حالتها "مقبولة" + وسعرها مش فاضي
        if ($consultation->user_id !== Auth::id() || $consultation->status !== 'accepted' || empty($consultation->price)) {
            // لو في أي حاجة غلط، نردوه لصفحة استشاراته ونقولوله راهو في مشكلة
            return redirect()->route('client.consultations')->with('error', 'هذه الاستشارة غير صالحة للدفع حالياً.');
        }

        // 3. نجهزو البيانات اللي بنبعتوها لبوابة الدفع
        $amount = (float) $consultation->price; // ناخدو السعر ونردوه رقم عشري باش مايصيرش لخبطة
        $invoiceNo = 'INV-' . $consultation->id . '-' . time(); // نصنعو رقم فاتورة مميز (مثلا: INV-5-16800000)
        $returnUrl = route('payment.callback', ['id' => $consultation->id]); // هدا الرابط اللي بيرجع ليه الزبون بعد ما يخلص في البنك
        $customerIp = request()->ip(); // ناخدو عنوان الإنترنت (IP) متع الزبون

        try {
            // 4. ونستنو الرد منهم Plutu نبعتو البيانات لمنصة 
            $apiResponse = PlutuLocalBankCards::confirm($amount, $invoiceNo, $returnUrl, $customerIp, 'ar');

            // 5. نشيكو على الرد: هل وافقو وفتحولنا بوابة الدفع؟
            if ($apiResponse->getOriginalResponse()->isSuccessful()) {
                // لو الأمور تمام، ناخدو الرابط متع الدفع ونرفعو الزبون ليه باش يحط رقم بطاقته
                $redirectUrl = $apiResponse->getRedirectUrl();
                return redirect()->away($redirectUrl);
            } else {
                // لو المنصة رفضت أو فيها مشكلة، ناخدو كود ورسالة الخطأ
                $errorCode = $apiResponse->getOriginalResponse()->getErrorCode();
                $errorMessage = $apiResponse->getOriginalResponse()->getErrorMessage();
                
                
                Log::error('Plutu Checkout Error: ' . $errorMessage, ['code' => $errorCode]);
                return redirect()->route('client.consultations')->with('error', 'حدث خطأ أثناء الاتصال ببوابة الدفع: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // هدا الـ Catch يشد أي مشكلة كبيرة تصير في الكود الفوقي (زي انقطاع النت عالسيرفر مثلاً)
            Log::error('Plutu Exception: ' . $e->getMessage());
            return redirect()->route('client.consultations')->with('error', 'تعذر معالجة طلب الدفع. الرجاء المحاولة لاحقاً.');
        }
    }

    /**
     * -------------------------------------------------------------------
     * هذي الدالة تستقبل الزبون لما يرجع من منصة Plutu (سواء دفع أو ألغى)
     * -------------------------------------------------------------------
     */
    public function callback(Request $request)
    {
        // 1. ناخدو رقم الاستشارة من الرابط اللي رجع بيه الزبون
        $id = $request->query('id');
        $consultation = PaidConsultation::findOrFail($id);

        try {
            // 2. نعطو البيانات اللي رجعت من Plutu للباكج باش تتأكد منها وتفصلها
            $callback = PlutuLocalBankCards::callbackHandler($request->all());

            // 3. نسألو: هل عملية الدفع تمت بنجاح؟
            if ($callback->isApprovedTransaction()) {
                
                // -- أ. نحدثو الاستشارة ونخلوها "مدفوعة" باش تبدا الخدمة --
                $consultation->update([
                    'status' => 'paid',
                ]);

                // -- ب. 🌟 هني نسجلو الفاتورة في جدول الـ payments في الداتا بيز باش تقعد مخزنة عندنا 🌟 --
                Payment::create([
                    'user_id'         => $consultation->user_id, // رقم الزبون
                    'consultation_id' => $consultation->id,      // رقم الاستشارة
                    'amount'          => $consultation->price,   // القيمة المدفوعة
                    'payment_status'  => 'completed',            // الحالة: مكتملة
                    'payment_method'  => 'Plutu'                 // طريقة الدفع
                ]);
                
                // -- ج. نبعتو إشعار للزبون باش نطمنوه إن فلوسه وصلو --
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'user_id'    => $consultation->user_id,
                    'title'      => 'تمت عملية الدفع بنجاح',
                    'body'       => 'تم دفع طلب استشارتك للمحامي ' . $consultation->lawyer->name . ' بنجاح، يمكنك التواصل معه الآن.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // -- د. نبعتو إشعار للمحامي باش ينتبه ويبدا يخدم على الاستشارة --
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'user_id'    => $consultation->lawyer_id,
                    'title'      => 'تم استلام الدفعة',
                    'body'       => 'قام العميل ' . $consultation->client->name . ' (رقم الطلب #' . $consultation->id . ') بدفع أتعاب الاستشارة، يمكنك الآن البدء بتقديم الخدمة له.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // -- هـ. توة وقت الحسابات وتقسيم الفلوس --
                $price = (float) $consultation->price; // السعر الكلي
                $commission = $price * 0.10; // نحسبو 10% عمولة للمنصة
                $lawyerAmount = $price - $commission; // الباقي حق المحامي صافي

                //"escrow" (معلقة/محجوزة) لين يكمل استشارته نسجلو فلوس المحامي في حسابه، بس نخلوها 
                \App\Models\Transaction::create([
                    'user_id' => $consultation->lawyer_id,
                    'type' => 'credit',
                    'amount' => $lawyerAmount,
                    'description' => 'أتعاب استشارة مدفوعة رقم #' . $consultation->id,
                    'status' => 'escrow', // معلقة
                    'reference_type' => \App\Models\PaidConsultation::class,
                    'reference_id' => $consultation->id,
                ]);

                // ونسجلو عمولة المنصة في حساب الإدارة وتكون "مكتملة" طول
                \App\Models\Transaction::create([
                    'user_id' => null, // null معناها فلوس المنصة (الإدارة)
                    'type' => 'commission',
                    'amount' => $commission,
                    'description' => 'عمولة منصة عن استشارة رقم #' . $consultation->id,
                    'status' => 'completed',
                    'reference_type' => \App\Models\PaidConsultation::class,
                    'reference_id' => $consultation->id,
                ]);
                
                // -- و. نفتحو شات (محادثة) بين الزبون والمحامي باش يبدو يتناقشو --
                // firstOrCreate معناها: لو المحادثة موجودة جيبها، ولو مش موجودة افتح وحدة جديدة
                \App\Models\ChatSession::firstOrCreate([
                    'chatable_type' => \App\Models\PaidConsultation::class,
                    'chatable_id'   => $consultation->id,
                ], [
                    'client_id' => $consultation->user_id,
                    'lawyer_id' => $consultation->lawyer_id,
                    'status' => 'active'
                ]);

                // نردو الزبون لصفحته ونقولوله مبروك الدفع تم
                return redirect()->route('client.consultations')->with('success', 'تم الدفع بنجاح! يمكن للمحامي الآن البدء في الاستشارة عبر المحادثة.');
            
            // 4. باهي لو الزبون فتح صفحة البنك وبعدين دار تراجع (إلغاء)؟
            } elseif ($callback->isCanceledTransaction()) {
                return redirect()->route('client.consultations')->with('error', 'تم إلغاء عملية الدفع من قبلك.');
            
            // 5. لو العملية اترفضت (رصيد مافيش، البطاقة غلط، الخ...)
            } else {
                return redirect()->route('client.consultations')->with('error', 'حدث خطأ في عملية الدفع أو تم رفضها.');
            }
        } catch (\Exception $e) {
            // لو صار كراش في الكود هني، نسجلوه ونردو الزبون بمسج خطأ
            Log::error('Plutu Callback Exception: ' . $e->getMessage());
            return redirect()->route('client.consultations')->with('error', 'حدث خطأ أثناء التحقق من الدفع.');
        }
    }

    /**
     * -------------------------------------------------------------------
     * هذي الدالة خاصة بدفع أتعاب "القضايا" (ممكن يدفع جزء أو المبلغ كله)
     * -------------------------------------------------------------------
     */
    public function checkoutCase(Request $request, $id)
    {
        // 1. نجيبو القضية من الداتا بيز
        $case = \App\Models\LegalCase::findOrFail($id);

        // 2. تأكيد أمان: لازم الزبون الحالي يكون هو نفسه صاحب القضية
        if ($case->user_id !== Auth::id()) {
            return redirect()->route('client.my-case.show', $id)->with('error', 'غير مصرح لك بدفع هذه القضية.');
        }

        // 3. نتأكدو إن الزبون دخل قيمة صحيحة في الفورم، ولازم تكون رقم وأكبر من صفر
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $amount = (float) $request->input('amount'); // القيمة اللي كتبها الزبون يبي يدفعها
        $remaining = $case->total_fees - $case->paid_fees; // نحسبو قداش مزال عليه (الإجمالي - المدفوع)

        // 4. لو هو دافع كل شي من قبل والمتبقي صفر أو أقل، نوقفوه
        if ($remaining <= 0) {
            return redirect()->route('client.my-case.show', $id)->with('error', 'تم سداد كامل أتعاب هذه القضية مسبقاً.');
        }

        // 5. لو هو كتب مبلغ أكبر من اللي مفروض يدفعه، نوقفوه ونقولوله قداش مزال عليه بالضبط
        if ($amount > $remaining) {
            return redirect()->route('client.my-case.show', $id)->with('error', 'المبلغ المدخل أكبر من الأتعاب المتبقية (' . $remaining . ' د.ل).');
        }

        // 6. نجهزو رقم الفاتورة
        $invoiceNo = 'INV-CASE-' . $case->id . '-' . time();

        // 7. هني لقطة مهمة: نسجلو "نية دفع" (Payment Intent) في الداتا بيز وحالتها pending (قيد الانتظار)
        // الفايدة منها: باش لما يرجع من البنك، نعرفو هو قداش كان ناوي يدفع بالضبط ومايصيرش تلاعب في الرابط
        \App\Models\Transaction::create([
            'user_id' => Auth::id(),
            'type' => 'payment_intent',
            'amount' => $amount,
            'description' => $invoiceNo, // نربطوها برقم الفاتورة باش نلقوها بعدين
            'status' => 'pending',
            'reference_type' => \App\Models\LegalCase::class,
            'reference_id' => $case->id,
        ]);

        // نجهزو رابط الرجوع وعنوان الآي بي
        $returnUrl = route('client.my-case.payment.callback', ['id' => $case->id, 'invoice' => $invoiceNo]);
        $customerIp = request()->ip();

        try {
            // نكلمو منصة الدفع
            $apiResponse = PlutuLocalBankCards::confirm($amount, $invoiceNo, $returnUrl, $customerIp, 'ar');

            // لو كل شي تمام، نرفعوه لصفحة البنك
            if ($apiResponse->getOriginalResponse()->isSuccessful()) {
                $redirectUrl = $apiResponse->getRedirectUrl();
                return redirect()->away($redirectUrl);
            } else {
                // لو في خطأ من البوابة، نسجلوه ونردو الزبون
                $errorCode = $apiResponse->getOriginalResponse()->getErrorCode();
                $errorMessage = $apiResponse->getOriginalResponse()->getErrorMessage();
                Log::error('Plutu Case Checkout Error: ' . $errorMessage, ['code' => $errorCode]);
                return redirect()->route('client.my-case.show', $id)->with('error', 'حدث خطأ أثناء الاتصال ببوابة الدفع: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Plutu Case Exception: ' . $e->getMessage());
            return redirect()->route('client.my-case.show', $id)->with('error', 'تعذر معالجة طلب الدفع. الرجاء المحاولة لاحقاً.');
        }
    }

    /**
     * -------------------------------------------------------------------
     * هذي الدالة تستقبل الزبون بعد ما يكمل دفع جزء أو كل حق "القضية"
     * -------------------------------------------------------------------
     */
    public function callbackCase(Request $request)
    {
        // 1. ناخدو رقم القضية ورقم الفاتورة من الرابط
        $id = $request->query('id');
        $invoiceNo = $request->query('invoice');
        $case = \App\Models\LegalCase::findOrFail($id);

        try {
            // 2. نعطو البيانات لمنصة Plutu باش تشيك عليها
            $callback = PlutuLocalBankCards::callbackHandler($request->all());

            // 3. لو الدفع تم بنجاح ومؤكد
            if ($callback->isApprovedTransaction()) {
                
                // -- أ. نمشو للداتا بيز وندورو على "نية الدفع" اللي سجلناها قبل ما يرفعناه للبنك --
                $intent = \App\Models\Transaction::where('reference_type', \App\Models\LegalCase::class)
                            ->where('reference_id', $case->id)
                            ->where('type', 'payment_intent')
                            ->where('description', $invoiceNo) // ندوروها برقم الفاتورة
                            ->where('status', 'pending')       // لازم تكون مازالت قيد الانتظار
                            ->first();

                // لو مالقيناش النية (ممكن حد يحاول يلعب بالرابط)، نوقفوه
                if (!$intent) {
                    return redirect()->route('client.my-case.show', $case->id)->with('error', 'تعذر العثور على تفاصيل عملية الدفع.');
                }

                // -- ب. ناخدو القيمة من نية الدفع، ونغيرو حالة النية إلى دفعة "مكتملة" --
                $amount = $intent->amount;
                $intent->update(['status' => 'completed', 'type' => 'payment']); 

                // -- ج. 🌟 نسجلو بيانات الدفع في جدول المدفوعات الخاص بالقضايا 🌟 --
                // تأكدي إن حقل case_id زدناه في المودل والمايجريشن متع الداتا بيز
                Payment::create([
                    'user_id'         => $case->user_id,
                    'consultation_id' => null, // خلينا هذي null لأننا توة نخلصو في قضية مش استشارة
                    'case_id'         => $case->id, // ⚠️ هدا الحقل اللي بيربط الدفعة بالقضية
                    'amount'          => $amount,
                    'payment_status'  => 'completed',
                    'payment_method'  => 'Plutu'
                ]);

                // -- د. نزيدو المبلغ اللي اندفع توة لخانة "المدفوع" في جدول القضايا --
                $case->paid_fees += $amount;
                $case->save();
                
                // -- هـ. نحسبو العمولة متعنا (10%)، بس ردي بالك: تنحسب من "المبلغ اللي دفعه توة بس" مش من إجمالي القضية --
                $commission = $amount * 0.10;
                $lawyerAmount = $amount - $commission; // هدا الصافي للمحامي من الدفعة هذي

                // نسجلو الفلوس للمحامي (وهني درناها available يعني متاحة طول لأنها دفعة من قضية مش استشارة)
                \App\Models\Transaction::create([
                    'user_id' => $case->lawyer_id,
                    'type' => 'credit',
                    'amount' => $lawyerAmount,
                    'description' => 'دفعة من أتعاب القضية رقم #' . $case->case_number,
                    'status' => 'available', 
                    'reference_type' => \App\Models\LegalCase::class,
                    'reference_id' => $case->id,
                ]);

                // ونسجلو عمولة المنصة
                \App\Models\Transaction::create([
                    'user_id' => null,
                    'type' => 'commission',
                    'amount' => $commission,
                    'description' => 'عمولة منصة (10%) عن دفعة في القضية رقم #' . $case->case_number,
                    'status' => 'completed',
                    'reference_type' => \App\Models\LegalCase::class,
                    'reference_id' => $case->id,
                ]);

                // -- و. إشعار للزبون --
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'user_id'    => $case->user_id,
                    'title'      => 'تمت عملية الدفع بنجاح',
                    'body'       => 'تم دفع مبلغ ' . $amount . ' د.ل كجزء من أتعاب القضية رقم ' . $case->case_number . ' بنجاح.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // -- ز. إشعار للمحامي --
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'user_id'    => $case->lawyer_id,
                    'title'      => 'دفعة أتعاب جديدة',
                    'body'       => 'قام العميل ' . $case->client_name . ' بدفع مبلغ ' . $amount . ' د.ل في القضية رقم ' . $case->case_number . '.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // نردو الزبون لصفحة تفاصيل قضيته ونقولوله يعطيك الصحة
                return redirect()->route('client.my-case.show', $case->id)->with('success', 'تم دفع مبلغ ' . $amount . ' د.ل بنجاح! شكراً لك.');
            
            // 4. لو الزبون ألغى العملية من صفحة البنك
            } elseif ($callback->isCanceledTransaction()) {
                return redirect()->route('client.my-case.show', $case->id)->with('error', 'تم إلغاء عملية الدفع من قبلك.');
            
            // 5. لو البنك رفض العملية (رصيد غير كافي، بطاقة منتهية الخ..)
            } else {
                return redirect()->route('client.my-case.show', $case->id)->with('error', 'حدث خطأ في عملية الدفع أو تم رفضها.');
            }
        } catch (\Exception $e) {
            // لو صار كراش في الكود نبلغوه بخطأ
            Log::error('Plutu Case Callback Exception: ' . $e->getMessage());
            return redirect()->route('client.my-case.show', $case->id)->with('error', 'حدث خطأ أثناء التحقق من الدفع.');
        }
    }
}