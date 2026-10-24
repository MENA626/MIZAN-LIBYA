{{-- resources/views/layouts/lawyer.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ميزان - المحامي | @yield('title', 'لوحة المحامي')</title>
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta property="og:title" content="ميزان - لوحة المحامي">
    <meta property="og:description" content="منصة ميزان القانونية، تواصل مع عملائك وقم بإدارة استشاراتك بسهولة.">
    
    {{-- إضافة سكريبت Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <style>
    body { font-family: 'Cairo', sans-serif; }
    .sidebar-link {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.6rem 1rem; border-radius: 0.5rem;
        font-size: 0.85rem; font-weight: 600;
        color: rgba(255, 255, 255, 0.6);
        transition: all 0.2s ease;
    }
    .sidebar-link i { width: 1.25rem; text-align: center; font-size: 0.9rem; opacity: 0.8; }
    .sidebar-link:hover { color: #fff; background: rgba(255, 255, 255, 0.05); }
    .nav-active { color: #fff !important; background: rgba(59, 130, 246, 0.1) !important; }
    .nav-active i { color: #3b82f6 !important; opacity: 1; }

    .group:hover .fa-times { color: #dc2626 !important; }
    .group .flex span { margin-left: 25px !important; min-width: 50px; }
    .group form { margin-left: 10px !important; }
    [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-white text-slate-800">

    <div x-data="{ sidebarOpen: window.innerWidth > 768 }" 
         x-init="window.addEventListener('resize', () => { sidebarOpen = window.innerWidth > 768; })" 
         class="min-h-screen relative">
        
        {{-- Overlay للموبايل --}}
        <div 
            x-show="sidebarOpen && window.innerWidth <= 768"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-20 md:hidden"
            style="display: none;">
        </div>

        {{-- القائمة الجانبية --}}
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
            class="w-[260px] bg-[#0f172a] flex flex-col justify-between fixed right-0 top-0 h-full z-30 border-l border-white/5 transition-transform duration-300 ease-in-out">
            
            <div class="pt-6 pb-5 px-5 border-b border-white/5">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                        <i class="fas fa-balance-scale text-blue-400 text-sm"></i>
                    </div>
                    <div class="leading-none text-right">
                        <h1 class="text-lg font-bold text-white tracking-wide">ميزان</h1>
                    </div>
                </div>

                {{-- بيانات المحامي --}}
                <div class="flex items-center gap-3 bg-white/5 p-2.5 rounded-xl border border-white/5">
                    <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 border border-slate-700">
                        @if(Auth::check() && Auth::user()->avatar)
                            <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user-tie text-slate-400 text-lg"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-white text-sm font-semibold truncate">{{ Auth::user()->name ?? 'المحامي' }}</h2>
                        
                        <div class="flex items-center gap-1.5 text-blue-400 text-[10px] font-semibold tracking-wider mt-0.5">
                            <i class="fas fa-gavel"></i>
                            <span class="truncate">
                                {{ Auth::check() && Auth::user()->lawyerProfile && Auth::user()->lawyerProfile->specialty ? Auth::user()->lawyerProfile->specialty->name : 'تخصص غير محدد' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 px-3 mt-4 space-y-1 overflow-y-auto">
                <a href="{{ route('lawyer.dashboard') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.dashboard') ? 'nav-active' : '' }}">
                    <i class="fas fa-home"></i> لوحة التحكم
                </a>
                <a href="{{ route('lawyer.filemanager') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.filemanager') ? 'nav-active' : '' }}">
                    <i class="fas fa-microchip"></i> الخزنة الرقمية الذكية
                </a>
                <a href="{{ route('lawyer.ai_consultant') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.ai_consultant') ? 'nav-active' : '' }}">
                    <i class="fas fa-robot"></i> مساعد ميزان الذكي
                </a>
                <a href="{{ route('lawyer.appointments') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.appointments') ? 'nav-active' : '' }}">
                    <i class="fas fa-clock"></i> إدارة المواعيد
                </a>
                <a href="{{ route('lawyer.my-cases') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.my-cases', 'lawyer.cases.*') ? 'nav-active' : '' }}">
                    <i class="fas fa-folder-open"></i> إدارة القضايا
                </a>
                <a href="{{ route('lawyer.consultations') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.consultations') ? 'nav-active' : '' }}">
                    <i class="fas fa-file-alt"></i> إدارة الإستشارات
                </a>
                <a href="{{ route('lawyer.chat') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.chat') ? 'nav-active' : '' }}">
                    <i class="fas fa-comments"></i> المحادثات
                </a>
                <a href="{{ route('lawyer.wallet') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.wallet') ? 'nav-active' : '' }}">
                    <i class="fas fa-wallet"></i> المحفظة المالية
                </a>
                <a href="{{ route('lawyer.settings') }}" @click="if(window.innerWidth <= 768) sidebarOpen = false" class="sidebar-link {{ request()->routeIs('lawyer.settings', 'profile.update') ? 'nav-active' : '' }}">
                    <i class="fas fa-cog"></i> الإعدادات
                </a>
            </nav>
            
            <div class="px-3 pb-5 mt-2 border-t border-white/5 pt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-red-400/70 hover:text-red-400 hover:bg-red-500/10 border-0 cursor-pointer text-right">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- حاوية المحتوى --}}
        <div class="transition-all duration-300 ease-in-out min-h-screen flex flex-col"
             :style="sidebarOpen && window.innerWidth > 768 ? 'padding-right: 260px' : 'padding-right: 0'">
            
            <main class="flex-1 p-3 sm:p-5 md:p-7 w-full">
                
                {{-- الشريط العلوي --}}
                <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 shrink-0 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:text-slate-700 hover:border-slate-300 transition-colors">
                            <i class="fas fa-bars text-sm"></i>
                        </button>

                        <div>
                            <h1 class="text-slate-800 text-lg sm:text-xl md:text-2xl font-bold">@yield('page_title')</h1>
                            <p class="text-slate-400 text-[11px] sm:text-xs mt-0.5 hidden sm:block">@yield('page_subtitle')</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- التاريخ والساعة بتوقيت ليبيا --}}
                        <div class="hidden sm:flex items-center gap-3 text-blue-600 text-[13px] font-medium bg-blue-50/50 px-4 py-2 rounded-full border border-blue-100/50">
                            {{-- التاريخ --}}
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-calendar-alt text-blue-500"></i>
                                <span id="live-date">{{ now('Africa/Tripoli')->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</span>
                            </div>
                            
                            {{-- فاصل عمودي --}}
                            <div class="w-px h-3.5 bg-blue-200"></div>
                            
                            {{-- الساعة --}}
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock text-blue-500"></i>
                                <span id="live-clock" class="inline-flex items-center gap-1">
                                   {{ now('Africa/Tripoli')->locale('ar')->translatedFormat('h:i A') }}
                                </span>
                            </div>
                        </div>

                        {{-- الإشعارات --}}
                        <div class="flex items-center justify-center">
                            @if(View::exists('components.notification-bell'))
                                <x-notification-bell btnClass="w-10 h-10 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all border border-blue-100/50" />    
                            @else
                                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all border border-blue-100/50">
                                    <i class="fa-regular fa-bell text-lg"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="page-content">
                    @yield('content')
                </div>

            </main>
        </div>

    </div>

@stack('scripts')

{{-- كود إخفاء الرسائل وتحديث التاريخ والساعة تلقائياً --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. كود إخفاء الرسائل تلقائياً ---
        const alerts = document.querySelectorAll('.alert-message');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });

        // --- 2. كود تحديث التاريخ والساعة تلقائياً ---
        function updateDateTime() {
            const dateEl = document.getElementById('live-date');
            const clockEl = document.getElementById('live-clock');
            const now = new Date();
            
            // تحديث التاريخ
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('ar-LY', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
            
            // تحديث الساعة
            if (clockEl) {
                clockEl.textContent = now.toLocaleTimeString('ar-LY', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    hour12: true 
                });
            }
        }

        // تشغيل الدالة فوراً ثم التحديث كل ثانية
        updateDateTime();
        setInterval(updateDateTime, 1000);
    });
</script>
</body>
</html>