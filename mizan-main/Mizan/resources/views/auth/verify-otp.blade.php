@extends('layouts.auth')

@section('title', 'التحقق من رمز الأمان')
@section('subtitle', 'أدخل الرمز المكون من 6 أرقام المرسل إليك')

@section('content')
    <div style="width: 100%; direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
        
        {{-- الترويسة --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 85px; height: 85px; border-radius: 50%; background-color: rgba(15, 106, 253, 0.08); color: var(--mizan-primary); box-shadow: 0 0 15px rgba(15, 106, 253, 0.1); margin-bottom: 12px;">
                <i class="fas fa-shield-alt" style="font-size: 38px;"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 800; margin: 0 0 4px 0; color: var(--text-main); font-family: 'Cairo', sans-serif;">التحقق من رمز الأمان</h2>
            <p style="font-size: 14px; font-weight: 500; color: var(--text-muted); margin: 0; font-family: 'Cairo', sans-serif;">أدخل الرمز المكون من 6 أرقام المرسل إليك</p>
        </div>

        {{-- وضع التطوير (Debug OTP) --}}
        @if (session('debug_otp') || request()->session()->get('debug_otp'))
            <div style="margin-bottom: 24px; padding: 14px; background-color: #ecfdf5; border-right: 4px solid #10b981; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-key" style="color: #10b981; font-size: 16px;"></i>
                <div>
                    <span style="font-weight: 600; color: #065f46; font-size: 13px; font-family: 'Cairo', sans-serif;">رمز التحقق: </span>
                    <span style="font-weight: 800; color: #064e3b; font-size: 16px; letter-spacing: 2px;">{{ session('debug_otp') ?? request()->session()->get('debug_otp') }}</span>
                </div>
            </div>
        @endif

        {{-- رسالة الخطأ --}}
        @if (session('error'))
            <div style="margin-bottom: 24px; padding: 14px; background-color: #fef2f2; border-right: 4px solid #ef4444; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 16px;"></i>
                <span style="font-weight: 600; color: #991b1b; font-size: 13px; font-family: 'Cairo', sans-serif;">{{ session('error') }}</span>
            </div>
        @endif

        {{-- البريد المستهدف --}}
        <div style="text-align: center; margin-bottom: 24px; padding: 12px; background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 8px;">
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #334155; font-family: 'Cairo', sans-serif; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-envelope" style="color: #94a3b8;"></i>
                <span dir="ltr">{{ session('reset_email', request()->session()->get('reset_email') ?? 'your-email@example.com') }}</span>
            </p>
        </div>
        
        <form id="otpForm" action="{{ route('verify.otp.post') }}" method="POST">
            @csrf
            <input type="hidden" name="otp" id="final_otp">

            {{-- حقول OTP (مربعات الأرقام) --}}
            <div style="display: flex; justify-content: center; gap: 12px; margin-bottom: 24px;" dir="ltr">
                @for ($i = 1; $i <= 6; $i++)
                    <input type="text" maxlength="1" id="otp{{ $i }}" 
                           style="width: 45px; height: 55px; text-align: center; font-size: 22px; font-weight: 800; color: #0f172a; background-color: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; outline: none; transition: all 0.3s; font-family: 'Cairo', sans-serif;"
                           onfocus="this.style.borderColor='var(--mizan-primary)'; this.style.backgroundColor='#ffffff';"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';"
                           onkeyup="moveToNext(this, {{ $i }})"
                           inputmode="numeric" pattern="[0-9]*">
                @endfor
            </div>
        </form>
        
        {{-- المؤقت وإعادة الإرسال --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div id="timerContainer" style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></div>
                <span id="timer" style="font-size: 14px; font-weight: 700; color: #334155; font-family: 'Cairo', sans-serif;">00:59</span>
            </div>
            <button id="resendBtn" style="background: transparent; border: none; font-size: 13px; font-weight: 700; color: var(--mizan-primary); cursor: not-allowed; opacity: 0.4; font-family: 'Cairo', sans-serif; display: flex; align-items: center; gap: 6px;" disabled>
                إعادة إرسال الرمز
                <i class="fas fa-redo-alt" style="font-size: 11px;"></i>
            </button>
        </div>
        
        {{-- زر التأكيد --}}
        <div>
            <button type="button" id="verifyBtn" class="btn-submit" style="width: 100%; padding: 14px; font-family: 'Cairo', sans-serif; font-size: 15px; display: flex; justify-content: center; align-items: center; gap: 8px;">
                <i class="fas fa-check-circle" style="font-size: 14px; opacity: 0.8;"></i>
                <span>تأكيد الرمز</span>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function moveToNext(current, index) {
        // السماح فقط بالأرقام
        current.value = current.value.replace(/[^0-9]/g, '');
        
        if (current.value.length === 1 && index < 6) {
            document.getElementById(`otp${index + 1}`).focus();
        }
    }

    let timeLeft = 59;
    const timerElement = document.getElementById('timer');
    const resendBtn = document.getElementById('resendBtn');
    const timerContainer = document.getElementById('timerContainer');

    const timerInterval = setInterval(() => {
        timeLeft--;
        timerElement.textContent = `00:${String(timeLeft).padStart(2, '0')}`;

        if (timeLeft <= 10) {
            timerElement.style.color = '#ef4444'; // يتحول للأحمر في آخر 10 ثواني
        }

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            // تعديل خصائص الزر بالـ JS المباشر بدلاً من Tailwind
            resendBtn.disabled = false;
            resendBtn.style.opacity = '1';
            resendBtn.style.cursor = 'pointer';
            timerContainer.style.display = 'none';
        }
    }, 1000);

    function submitOtpForm() {
        let code = "";
        for (let i = 1; i <= 6; i++) {
            code += document.getElementById(`otp${i}`).value;
        }
        
        if (code.length === 6) {
            document.getElementById('final_otp').value = code;
            document.getElementById('otpForm').submit();
        } else {
            alert('الرجاء إكمال إدخال الرمز المكون من 6 أرقام.');
        }
    }

    document.getElementById('verifyBtn').addEventListener('click', submitOtpForm);

    // تعبئة تلقائية في وضع التطوير
    window.addEventListener('DOMContentLoaded', () => {
        const debugOtp = "{{ session('debug_otp') ?? request()->session()->get('debug_otp') }}";
        
        if (debugOtp && debugOtp.length === 6) {
            clearInterval(timerInterval); 
            for (let i = 1; i <= 6; i++) {
                const input = document.getElementById(`otp${i}`);
                input.value = debugOtp[i - 1];
                input.style.borderColor = 'var(--mizan-primary)'; // تلوين الحقول عند التعبئة التلقائية
            }
            setTimeout(() => submitOtpForm(), 600);
        } else {
            document.getElementById('otp1').focus();
        }
    });
</script>
@endpush