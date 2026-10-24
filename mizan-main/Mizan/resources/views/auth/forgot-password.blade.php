@extends('layouts.auth')

@section('title', 'نسيت كلمة المرور؟')
@section('subtitle', 'أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين')

@section('content')
    <div style="width: 100%; direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
        
        {{-- الترويسة --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 85px; height: 85px; border-radius: 50%; background-color: rgba(15, 106, 253, 0.08); color: var(--mizan-primary); box-shadow: 0 0 15px rgba(15, 106, 253, 0.1); margin-bottom: 12px;">
                <i class="fas fa-key" style="font-size: 38px;"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 800; margin: 0 0 4px 0; color: var(--text-main); font-family: 'Cairo', sans-serif;">نسيت كلمة المرور؟</h2>
            <p style="font-size: 14px; font-weight: 500; color: var(--text-muted); margin: 0; font-family: 'Cairo', sans-serif;">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين</p>
        </div>

        {{-- رسالة النجاح --}}
        @if (session('status'))
            <div style="margin-bottom: 24px; padding: 14px; background-color: #ecfdf5; border-right: 4px solid #10b981; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 16px;"></i>
                <span style="font-weight: 600; color: #065f46; font-size: 13px; font-family: 'Cairo', sans-serif;">{{ session('status') }}</span>
            </div>
        @endif

        {{-- رسالة الخطأ --}}
        @if (session('error'))
            <div style="margin-bottom: 24px; padding: 14px; background-color: #fef2f2; border-right: 4px solid #ef4444; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 16px;"></i>
                <span style="font-weight: 600; color: #991b1b; font-size: 13px; font-family: 'Cairo', sans-serif;">{{ session('error') }}</span>
            </div>
        @endif

        {{-- رابط وضع التطوير (Debug Link) --}}
        @if (session('debug_link'))
            <div style="margin-bottom: 24px; padding: 16px; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px;">
                <p style="font-size: 13px; font-weight: 700; color: #1e40af; margin: 0 0 8px 0; font-family: 'Cairo', sans-serif;">
                    <i class="fas fa-tools" style="margin-left: 4px;"></i> وضع التطوير:
                </p>
                <p style="font-size: 12px; color: #64748b; margin: 0 0 12px 0; font-weight: 600; font-family: 'Cairo', sans-serif;">لم يتم ربط خادم الإيميل بعد — استخدم الرابط التالي:</p>
                <a href="{{ session('debug_link') }}" 
                   style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 10px; background-color: #2563eb; color: white; font-weight: 700; font-size: 12px; border-radius: 8px; text-decoration: none; gap: 8px; font-family: 'Cairo', sans-serif;">
                    فتح رابط إعادة التعيين
                    <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>
                </a>
            </div>
        @endif

        {{-- الفورم --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">البريد الإلكتروني المسجل</label>
                
                <div style="position: relative; width: 100%;">
                    <i class="fas fa-envelope" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 10;"></i>
                    
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@mizan.com" required autofocus
                           class="input-premium" 
                           style="width: 100%; box-sizing: border-box; padding: 14px 45px 14px 16px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                </div>
                
                @error('email')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 6px; display: block; font-weight: 600;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="padding-top: 4px;">
                <button type="submit" class="btn-submit" style="width: 100%; padding: 14px; font-family: 'Cairo', sans-serif; font-size: 15px;">
                    <span>إرسال رابط الاستعادة</span>
                    <i class="fas fa-paper-plane" style="font-size: 12px; margin-right: 8px; opacity: 0.7;"></i>
                </button>
            </div>
        </form>
        
        {{-- التذييل ورابط العودة --}}
        <div style="margin-top: 32px; text-align: center;">
            <p style="font-size: 12px; color: #94a3b8; margin: 0 0 16px 0; display: flex; align-items: center; justify-content: center; gap: 6px; font-weight: 600; font-family: 'Cairo', sans-serif;">
                <i class="fas fa-shield-alt" style="color: #cbd5e1;"></i>
                ستصلك رسالة تحتوي على رابط آمن لإعادة تعيين كلمة المرور
            </p>
            <a href="{{ route('login') }}" style="font-size: 14px; font-weight: 800; color: var(--mizan-primary); text-decoration: none; font-family: 'Cairo', sans-serif;">
                العودة لتسجيل الدخول
            </a>
        </div>
    </div>
@endsection