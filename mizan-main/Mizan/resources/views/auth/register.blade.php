@extends('layouts.auth')

@section('title', 'إنشاء حساب جديد')
@section('subtitle', 'انضم إلى منصة ميزان القانونية')

@section('content')
    <div style="width: 100%; direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
        
        {{-- رأس الصفحة --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <h2 style="font-size: 24px; font-weight: 800; margin: 0 0 6px 0; color: var(--text-main); font-family: 'Cairo', sans-serif;">إنشاء حساب جديد</h2>
            <p style="font-size: 14px; font-weight: 500; color: var(--text-muted); margin: 0; font-family: 'Cairo', sans-serif;">انضم إلى منصة ميزان القانونية</p>
        </div>

        {{-- رسائل الخطأ --}}
        @if ($errors->any())
            <div style="margin-bottom: 24px; padding: 14px; background-color: #fef2f2; border-right: 4px solid #ef4444; border-radius: 12px;">
                <p style="font-weight: 700; color: #991b1b; font-size: 13px; margin: 0 0 8px 0;">يرجى تصحيح الأخطاء التالية:</p>
                <ul style="margin: 0; padding-right: 20px; color: #b91c1c; font-size: 12px; font-weight: 600;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom: 4px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- مفاتيح اختيار نوع الحساب --}}
        <div style="display: flex; gap: 8px; margin-bottom: 24px; padding: 6px; background-color: #f1f5f9; border-radius: 50px;">
            <button type="button" id="clientToggle" style="flex: 1; padding: 12px 0; border-radius: 50px; font-weight: 700; font-size: 13px; border: none; cursor: pointer; background-color: var(--mizan-primary); color: white; font-family: 'Cairo', sans-serif; transition: all 0.3s;">
                مواطن
            </button>
            <button type="button" id="lawyerToggle" style="flex: 1; padding: 12px 0; border-radius: 50px; font-weight: 700; font-size: 13px; border: none; cursor: pointer; background-color: transparent; color: #64748b; font-family: 'Cairo', sans-serif; transition: all 0.3s;">
                محامٍ / مستشار قانوني
            </button>
        </div>
        
        <form id="registerForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_type" id="userTypeInput" value="{{ old('user_type', 'client') }}">

            {{-- الاسم الكامل ورقم الهاتف --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="أدخل اسمك الكامل" required
                           class="input-premium" style="width: 100%; box-sizing: border-box; padding: 14px 20px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+218 91 234 5678" required
                           class="input-premium" style="width: 100%; box-sizing: border-box; padding: 14px 20px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                </div>
            </div>

            {{-- البريد الإلكتروني --}}
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="mail@example.com" required
                       class="input-premium" style="width: 100%; box-sizing: border-box; padding: 14px 20px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
            </div>

            {{-- حقول المحامي --}}
            <div id="lawyerFieldsContainer" style="display: none; margin-bottom: 16px;">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">رقم القيد النقابي</label>
                    <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" placeholder="أدخل رقم العضوية النقابية"
                           class="input-premium" style="width: 100%; box-sizing: border-box; padding: 14px 20px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">صورة الكارت النقابي (التوثيق)</label>
                    
                    <div id="file_text_fallback" 
                         class="input-premium" 
                         style="cursor: pointer; background-color: #f8fafc; display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; box-sizing: border-box; font-family: 'Cairo', sans-serif; font-size: 14px;" 
                         onclick="document.getElementById('syndicate_card').click();">
                        
                        <span id="file_label_text" style="color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600;">اضغط لرفع صورة الكارت النقابي</span>
                        <i id="uploadIcon" class="fas fa-cloud-upload-alt" style="color: #94a3b8; font-size: 16px;"></i>
                    </div>
                    
                    <input type="file" name="syndicate_card" id="syndicate_card" style="display: none;" accept="image/*,.pdf" onchange="updateFileName(this)" />
                </div>
            </div>
            
            {{-- كلمات المرور --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">كلمة المرور</label>
                    <div style="position: relative; width: 100%;">
                        <input type="password" name="password" id="password" placeholder="••••••••" required 
                               class="input-premium" style="width: 100%; box-sizing: border-box; padding: 14px 20px 14px 45px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8;" onclick="togglePassword('password', 'eyeIcon1')">
                            <i id="eyeIcon1" class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Cairo', sans-serif;">تأكيد كلمة المرور</label>
                    <div style="position: relative; width: 100%;">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required 
                               class="input-premium" style="width: 100%; box-sizing: border-box; padding: 14px 20px 14px 45px; text-align: right; direction: rtl; font-family: 'Cairo', sans-serif; font-size: 14px;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8;" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                            <i id="eyeIcon2" class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- زر الإرسال --}}
            <div>
                <button type="submit" class="btn-submit" style="width: 100%; padding: 14px; font-family: 'Cairo', sans-serif; font-size: 15px;">
                    <span>إنشاء الحساب</span>
                </button>
            </div>
        </form>
        
        {{-- الرابط السفلي --}}
        <div style="margin-top: 24px; text-align: center;">
            <p style="font-size: 14px; color: var(--text-muted); font-weight: 600; font-family: 'Cairo', sans-serif; margin: 0;">
                لديك حساب بالفعل؟ 
                <a href="{{ route('login') }}" style="color: var(--mizan-primary); font-weight: 800; text-decoration: none; margin-right: 4px;">تسجيل الدخول</a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const clientBtn = document.getElementById('clientToggle');
    const lawyerBtn = document.getElementById('lawyerToggle');
    const lawyerFieldsContainer = document.getElementById('lawyerFieldsContainer');
    const userTypeInput = document.getElementById('userTypeInput');
    const licenseInput = document.getElementById('license_number');
    const fileInput = document.getElementById('syndicate_card');
    
    function setType(type) {
        if (type === 'client') {
            clientBtn.style.backgroundColor = 'var(--mizan-primary)';
            clientBtn.style.color = 'white';
            lawyerBtn.style.backgroundColor = 'transparent';
            lawyerBtn.style.color = '#64748b';
            
            lawyerFieldsContainer.style.display = 'none';
            licenseInput.removeAttribute('required');
            fileInput.removeAttribute('required');
            userTypeInput.value = 'client';
        } else {
            lawyerBtn.style.backgroundColor = 'var(--mizan-primary)';
            lawyerBtn.style.color = 'white';
            clientBtn.style.backgroundColor = 'transparent';
            clientBtn.style.color = '#64748b';
            
            lawyerFieldsContainer.style.display = 'block';
            licenseInput.setAttribute('required', 'required');
            fileInput.setAttribute('required', 'required');
            userTypeInput.value = 'lawyer';
        }
    }
    
    function updateFileName(input) {
        const fileLabelText = document.getElementById('file_label_text');
        const uploadIcon = document.getElementById('uploadIcon');
        
        if (input.files && input.files.length > 0) {
            fileLabelText.innerText = "تم اختيار: " + input.files[0].name;
            fileLabelText.style.color = '#059669'; 
            uploadIcon.className = 'fas fa-check-circle';
            uploadIcon.style.color = '#10b981';
        } else {
            fileLabelText.innerText = "اضغط لرفع صورة الكارت النقابي";
            fileLabelText.style.color = '#94a3b8';
            uploadIcon.className = 'fas fa-cloud-upload-alt';
            uploadIcon.style.color = '#94a3b8';
        }
    }

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            icon.style.color = 'var(--mizan-primary)';
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            icon.style.color = '#94a3b8';
        }
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        setType(userTypeInput.value);
    });
    
    clientBtn.addEventListener('click', () => setType('client'));
    lawyerBtn.addEventListener('click', () => setType('lawyer'));
</script>
@endpush