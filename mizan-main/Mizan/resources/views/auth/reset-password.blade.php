@extends('layouts.auth')

@section('title', 'تعيين كلمة مرور جديدة')
@section('subtitle', 'يجب أن تكون كلمة المرور قوية وآمنة')

@section('content')
    <div style="width: 100%; direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
        
        {{-- الترويسة --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 85px; height: 85px; border-radius: 50%; background-color: rgba(15, 106, 253, 0.08); color: var(--mizan-primary); box-shadow: 0 0 15px rgba(15, 106, 253, 0.1); margin-bottom: 12px;">
                <i class="fas fa-lock" style="font-size: 38px;"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 800; margin: 0 0 4px 0; color: var(--text-main); font-family: 'Cairo', sans-serif;">تعيين كلمة مرور جديدة</h2>
            <p style="font-size: 14px; font-weight: 500; color: var(--text-muted); margin: 0; font-family: 'Cairo', sans-serif;">يجب أن تكون كلمة المرور قوية وآمنة</p>
        </div>

        {{-- رسائل الخطأ الأساسية --}}
        @if ($errors->any())
            <div style="margin-bottom: 24px; padding: 14px; background-color: #fef2f2; border-right: 4px solid #ef4444; border-radius: 12px;">
                <p style="font-weight: 700; color: #991b1b; font-size: 13px; margin: 0 0 8px 0;">لم يتم قبول البيانات:</p>
                <ul style="margin: 0; padding-right: 20px; color: #b91c1c; font-size: 12px; font-weight: 600;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom: 4px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- رسائل الخطأ من الـ Session --}}
        @if (session('error'))
            <div style="margin-bottom: 24px; padding: 14px; background-color: #fef2f2; border-right: 4px solid #ef4444; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 16px;"></i>
                <span style="font-weight: 600; color: #991b1b; font-size: 13px; font-family: 'Cairo', sans-serif;">{{ session('error') }}</span>
            </div>
        @endif

        {{-- الفورم --}}
        <form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? request()->route('token') ?? request()->token }}">
            <input type="hidden" name="email" value="{{ $email ?? request()->email ?? old('email') }}">

            {{-- كلمة المرور الجديدة --}}
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">كلمة المرور الجديدة</label>
                
                <div style="position: relative; width: 100%;">
                    <i class="fas fa-lock" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 10;"></i>
                    
                    <input type="password" name="password" id="newPassword" placeholder="أدخل كلمة المرور" required
                           class="input-premium" 
                           style="width: 100%; box-sizing: border-box; padding: 14px 45px 14px 45px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;" onkeyup="checkPasswordStrength()">
                           
                    <button type="button" onclick="togglePassword('newPassword', 'toggleIcon1')" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 0; cursor: pointer; color: #94a3b8; z-index: 10; outline: none;">
                        <i id="toggleIcon1" class="fas fa-eye" style="font-size: 14px;"></i>
                    </button>
                </div>
            </div>

            {{-- مؤشر القوة --}}
            <div style="margin-bottom: 24px;">
                <div style="display: flex; gap: 6px; height: 4px; margin-bottom: 6px; flex-direction: row-reverse;">
                    <div id="strength1" style="flex: 1; border-radius: 50px; background-color: #e2e8f0; transition: all 0.3s;"></div>
                    <div id="strength2" style="flex: 1; border-radius: 50px; background-color: #e2e8f0; transition: all 0.3s;"></div>
                    <div id="strength3" style="flex: 1; border-radius: 50px; background-color: #e2e8f0; transition: all 0.3s;"></div>
                </div>
                <p id="strengthText" style="font-size: 11px; font-weight: 600; color: #94a3b8; margin: 0; font-family: 'Cairo', sans-serif;">مقياس أمان كلمة المرور</p>
            </div>

            {{-- تأكيد كلمة المرور --}}
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">تأكيد كلمة المرور</label>
                
                <div style="position: relative; width: 100%;">
                    <i class="fas fa-check-circle" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 10;"></i>
                    
                    <input type="password" name="password_confirmation" id="confirmPassword" placeholder="أعد إدخال كلمة المرور" required
                           class="input-premium" 
                           style="width: 100%; box-sizing: border-box; padding: 14px 45px 14px 45px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;" onkeyup="checkPasswordMatch()">
                           
                    <button type="button" onclick="togglePassword('confirmPassword', 'toggleIcon2')" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 0; cursor: pointer; color: #94a3b8; z-index: 10; outline: none;">
                        <i id="toggleIcon2" class="fas fa-eye" style="font-size: 14px;"></i>
                    </button>
                </div>
                <p id="matchText" style="font-size: 11px; font-weight: 600; margin-top: 6px; height: 16px; font-family: 'Cairo', sans-serif;"></p>
            </div>

            {{-- زر الحفظ --}}
            <div style="padding-top: 4px;">
                <button type="submit" class="btn-submit" style="width: 100%; padding: 14px; font-family: 'Cairo', sans-serif; font-size: 15px;">
                    <i class="fas fa-save" style="font-size: 14px; margin-left: 8px; opacity: 0.8;"></i>
                    <span>تحديث كلمة المرور</span>
                </button>
            </div>
        </form>

        {{-- التذييل --}}
        <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center;">
            <p style="font-size: 12px; color: #94a3b8; font-weight: 600; font-family: 'Cairo', sans-serif; margin: 0;">
                <i class="fas fa-info-circle" style="margin-left: 4px;"></i> نصيحة: استخدم مزيجاً من الحروف، الأرقام، والرموز
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            icon.style.color = 'var(--mizan-primary)';
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            icon.style.color = '#94a3b8';
        }
    }

    function checkPasswordStrength() {
        const password = document.getElementById('newPassword').value;
        const s1 = document.getElementById('strength1');
        const s2 = document.getElementById('strength2');
        const s3 = document.getElementById('strength3');
        const text = document.getElementById('strengthText');
        
        [s1, s2, s3].forEach(s => s.style.backgroundColor = '#e2e8f0');

        if (password.length === 0) {
            text.textContent = 'مقياس أمان كلمة المرور';
            text.style.color = '#94a3b8';
            return;
        }

        let strength = 0;
        if (password.length >= 8) strength++; 
        if (password.match(/[a-z]/) && password.match(/[0-9]/)) strength++; 
        if (password.match(/[^a-zA-Z0-9]/)) strength++; 

        if (strength <= 1) {
            s1.style.backgroundColor = '#ef4444'; 
            text.textContent = 'حماية ضعيفة';
            text.style.color = '#ef4444';
        } else if (strength === 2) {
            s1.style.backgroundColor = '#f59e0b'; 
            s2.style.backgroundColor = '#f59e0b';
            text.textContent = 'حماية متوسطة';
            text.style.color = '#f59e0b';
        } else {
            s1.style.backgroundColor = '#10b981'; 
            s2.style.backgroundColor = '#10b981';
            s3.style.backgroundColor = '#10b981';
            text.textContent = 'حماية قوية';
            text.style.color = '#10b981';
        }
        
        checkPasswordMatch();
    }

    function checkPasswordMatch() {
        const password = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        const matchText = document.getElementById('matchText');
        
        if (confirm.length === 0) {
            matchText.textContent = '';
        } else if (password === confirm && password.length > 0) {
            matchText.textContent = '✓ الكلمتان متطابقتان';
            matchText.style.color = '#10b981';
        } else {
            matchText.textContent = '✗ الكلمتان غير متطابقتين';
            matchText.style.color = '#ef4444';
        }
    }

    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        const password = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        if (password !== confirm) {
            e.preventDefault();
            alert('الرجاء التأكد من تطابق كلمتي المرور.');
        }
    });
</script>
@endpush