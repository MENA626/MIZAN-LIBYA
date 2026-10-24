@extends('layouts.auth')

@section('title', 'تسجيل الدخول')

@section('content')
    <div style="width: 100%; direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">

     @if (session('info'))
    <div id="flash-message-info" style="margin-bottom: 20px; padding: 16px; background-color: #eff6ff; border-right: 4px solid #3b82f6; border-radius: 12px; text-align: right; transition: opacity 0.5s ease;">
        <p style="font-weight: 700; color: #1e40af; font-size: 14px; margin: 0;">{{ session('info') }}</p>
    </div>
     @endif

    @if (session('error'))
    <div id="flash-message-error" style="margin-bottom: 20px; padding: 16px; background-color: #fef2f2; border-right: 4px solid #ef4444; border-radius: 12px; text-align: right; transition: opacity 0.5s ease;">
        <p style="font-weight: 700; color: #991b1b; font-size: 14px; margin: 0;">{{ session('error') }}</p>
    </div>
    @endif

        {{-- الترويسة --}}
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 85px; height: 85px; border-radius: 50%; background-color: rgba(15, 106, 253, 0.08); color: var(--mizan-primary); box-shadow: 0 0 15px rgba(15, 106, 253, 0.1); margin-bottom: 12px;">
                <i class="fas fa-user-lock" style="font-size: 38px;"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 800; margin: 0 0 4px 0; color: var(--text-main); font-family: 'Cairo', sans-serif;">تسجيل الدخول</h2>
            <p style="font-size: 14px; font-weight: 500; color: var(--text-muted); margin: 0; font-family: 'Cairo', sans-serif;">الدخول الآمن لمنظومة ميزان القانونية</p>
        </div>

        {{-- الفورم --}}
        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            
            {{-- حقل البريد الإلكتروني --}}
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-family: 'Cairo', sans-serif;">البريد الإلكتروني</label>
                
                <div style="position: relative; width: 100%;">
                    <i class="fas fa-envelope" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 10;"></i>
                    
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@mizan.com" required
                           class="input-premium" 
                           style="width: 100%; box-sizing: border-box; padding: 14px 45px 14px 16px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                </div>
                
                @error('email')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 6px; display: block; font-weight: 600;">{{ $message }}</span>
                @enderror
            </div>
            
            {{-- حقل كلمة المرور --}}
            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; margin: 0; color: var(--text-main); font-family: 'Cairo', sans-serif;">كلمة المرور</label>
                    <a href="{{ route('password.request') }}" style="font-size: 12px; font-weight: 700; text-decoration: none; color: var(--mizan-primary); font-family: 'Cairo', sans-serif;">
                        نسيت كلمة السر؟
                    </a>
                </div>
                
                <div style="position: relative; width: 100%;">
                    <i class="fas fa-lock" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 10;"></i>
                    
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                           class="input-premium" 
                           style="width: 100%; box-sizing: border-box; padding: 14px 45px 14px 45px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                    
                    <button type="button" onclick="togglePassword()" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 0; cursor: pointer; color: #94a3b8; z-index: 10; outline: none;">
                        <i id="toggleIcon" class="fas fa-eye" style="font-size: 14px;"></i>
                    </button>
                </div>

                @error('password')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 6px; display: block; font-weight: 600;">{{ $message }}</span>
                @enderror
            </div>
            
            {{-- زر الدخول --}}
            <div>
                <button type="submit" class="btn-submit" style="width: 100%; padding: 14px; font-family: 'Cairo', sans-serif;">
                    <span>تسجيل الدخول</span>
                    <i class="fas fa-chevron-left" style="font-size: 10px; margin-right: 8px; opacity: 0.7;"></i>
                </button>
            </div>
        </form>
        
        {{-- رابط التسجيل --}}
        <div style="margin-top: 24px; text-align: center;">
            <p style="font-size: 14px; font-weight: 600; color: var(--text-muted); margin: 0; font-family: 'Cairo', sans-serif;">
                ليس لديك حساب؟ 
                <a href="{{ route('register') }}" style="font-weight: 800; text-decoration: none; color: var(--mizan-primary); margin-right: 4px;">
                    إنشاء حساب جديد
                </a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // دالة إخفاء الرسائل
    function hideFlashMessages() {
        const messages = ['flash-message-info', 'flash-message-error'];
        messages.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.opacity = '0'; // لجعلها تتلاشى تدريجياً
                setTimeout(() => {
                    element.style.display = 'none';
                }, 500); // إخفاء العنصر بعد نصف ثانية من انتهاء التلاشي
            }
        });
    }

    // الانتظار لمدة 5 ثواني ثم استدعاء الدالة
    setTimeout(hideFlashMessages, 7000);

    // دالة إظهار/إخفاء كلمة المرور 
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
            icon.style.color = 'var(--mizan-primary)';
        } else {
            password.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
            icon.style.color = '#94a3b8';
        }
    }
</script>
@endpush