{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ميزان - @yield('title', 'تسجيل الدخول')</title>
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta property="og:title" content="ميزان - تسجيل الدخول">
    <meta property="og:description" content="قم بتسجيل الدخول إلى حسابك في منصة ميزان القانونية لمتابعة قضاياك واستشاراتك.">
    <meta property="og:image" content="{{ asset('apple-touch-icon.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --mizan-primary: #0f6afd;   
            --mizan-primary-light: #eef4ff;
            --mizan-dark: #0a49b8; 
            --text-main: #0f172a;
            --text-secondary: #334155;
            --text-muted: #94a3b8; /* رمادي باهت */
            --line-color: #e2e8f0;
            --glass-bg: rgba(255, 255, 255, 0.45); /* جعلت الخلفية شفافة أكثر هنا */
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 24px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            position: relative;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(circle at 10% -10%, rgba(15, 106, 253, 0.12) 0%, transparent 50vw),
                radial-gradient(circle at 90% 110%, rgba(15, 106, 253, 0.08) 0%, transparent 55vw),
                radial-gradient(circle at -10% 100%, rgba(15, 106, 253, 0.05) 0%, transparent 40vw);
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        /* تأثير الدخول للواجهة */
        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* إشعارات الترحيب والوداع الدائرية تماماً */
        .toast-notification {
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #ffffff;
            color: var(--text-main);
            padding: 16px 32px;
            border-radius: 50px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--line-color);
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 15px;
            font-weight: 600;
            z-index: 9999;
            opacity: 0;
            animation: slideDownFadeIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
            transition: all 0.5s ease;
            width: max-content;
            max-width: 90%;
            overflow: hidden;
        }

        .toast-notification i {
            color: #10b981; 
            font-size: 20px;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: var(--mizan-primary);
            width: 100%;
            animation: progressShrink 10s linear forwards;
        }

        @keyframes slideDownFadeIn {
            0% { top: -50px; opacity: 0; transform: translateX(-50%) scale(0.9); }
            100% { top: 30px; opacity: 1; transform: translateX(-50%) scale(1); }
        }

        @keyframes progressShrink {
            0% { width: 100%; }
            100% { width: 0%; }
        }

        /* الحاوية الكبرى (الكرت) */
        .auth-wrapper {
            width: 100%;
            max-width: 1200px; 
            min-height: 700px; 
            background: #ffffff; 
            border-radius: 32px; 
            box-shadow: 0 20px 50px rgba(15, 106, 253, 0.08), 0 4px 12px rgba(0,0,0,0.03); 
            border: 1px solid rgba(15, 106, 253, 0.05); 
            display: flex;
            flex-direction: row; 
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        /* الجزء الأيمن المرئي */
        .visual-side {
            width: 50%;
            position: relative;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center; 
            z-index: 1;
        }

        .visual-bg {
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('images/5834482866123903142.jpg') }}");
            background-size: cover;
            background-position: center;
            z-index: -2;
        }

        /* لوحة النصوص الزجاجية الأكثر شفافية */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); /* زيادة غبش الخلفية لتعطي جمالية أعلى مع الشفافية الزائدة */
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 50px 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.03);
            position: relative;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: var(--mizan-primary-light);
            border-radius: 50%; 
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--mizan-primary);
            box-shadow: 0 4px 12px rgba(15, 106, 253, 0.15);
        }

        .logo-text {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--text-main);
            text-shadow: none !important;
        }

        .visual-title {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.4;
            margin-bottom: 18px;
            color: #0f172a;
            text-shadow: none !important;
        }

        .visual-subtitle {
            font-size: 17px;
            font-weight: 500;
            line-height: 1.7;
            color: #0f172a;
            text-shadow: none !important;
        }

        /* الجزء الأيسر (النموذج) */
        .form-side {
            width: 50%;
            padding: 50px 80px 30px 80px; /* تعديل الحشو ليناسب وجود التذييل بالأسفل */
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* لتوزيع محتوى الفورم في المنتصف والتذييل في الأسفل تماماً */
        }

        .form-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
        }

        /* التذييل بالرمادي الباهت */
        .form-footer {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 1.5px;
            font-family: system-ui, -apple-system, sans-serif;
            margin-top: 30px;
            text-transform: lowercase;
        }

        /* حقول الإدخال الدائرية بالكامل */
        .input-premium {
            width: 100%;
            border: 2px solid var(--line-color);
            border-radius: 50px; 
            padding: 16px 24px; 
            font-size: 15px;
            color: var(--text-main);
            background: #f8fafc;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .input-premium.pr-10 {
            padding-right: 50px !important;
        }
        
        .input-premium::placeholder {
            color: #94a3b8;
            font-weight: 500; 
        }

        .input-premium:focus {
            outline: none;
            border-color: var(--mizan-primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(15, 106, 253, 0.12); 
        }

        /* الأزرار الدائرية بالكامل */
        .btn-submit {
            width: 100%;
            background-color: var(--mizan-primary);
            color: #ffffff;
            border: none;
            border-radius: 50px; 
            padding: 18px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 20px rgba(15, 106, 253, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background-color: var(--mizan-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(15, 106, 253, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* التجاوب مع الشاشات (Responsive) */
        @media (max-width: 1024px) {
            .form-side { padding: 40px 40px 24px 40px; }
            .visual-title { font-size: 32px; }
        }

        @media (max-width: 850px) {
            body { padding: 0; background: #ffffff; }
            .auth-wrapper { flex-direction: column; min-height: 100vh; max-width: 100%; border-radius: 0; box-shadow: none; border: none; }
            .visual-side { display: none !important; }
            .form-side { width: 100%; padding: 40px 24px 24px 24px; min-height: 100vh; }
        }

        @media (max-width: 600px) {
            .toast-notification {
                padding: 14px 24px;
                font-size: 14px;
                border-radius: 50px;
                top: 20px;
                width: 90%;
            }
            @keyframes slideDownFadeIn {
                0% { top: -50px; opacity: 0; transform: translateX(-50%) scale(0.9); }
                100% { top: 20px; opacity: 1; transform: translateX(-50%) scale(1); }
            }
        }
    </style>
</head>
<body>

    @if (session('success') || session('status') || session('message'))
        <div id="toast-message" class="toast-notification">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') ?? session('status') ?? session('message') }}</span>
            <div class="toast-progress"></div>
        </div>
    @endif

    <div class="auth-wrapper fade-in-up">
        
        <div class="visual-side">
            <div class="visual-bg"></div>
            
            <div class="glass-panel">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <span class="logo-text">مِـيـزان</span>
                </div>

                <div class="visual-text-content">
                    <h2 class="visual-title">لأن حقوقك تستحق من يرشدك إليها ...</h2>
                    <p class="visual-subtitle">
                        ميزان منصة تجمع الخبرات القانونية والخدمات الرقمية لتمنحك تجربة سلسة وموثوقة في كل خطوة.
                    </p>
                </div>
            </div>
        </div>

        <div class="form-side">
            <div class="form-content">
                <main class="w-full">
                    @yield('content')
                </main>
            </div>
            
            <div class="form-footer">
                mizan 2026
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('toast-message');
            if (toast) {
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(-50%) translateY(-20px)';
                    setTimeout(() => toast.remove(), 500);
                }, 10000); 
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>