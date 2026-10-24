{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ميزان | @yield('title', 'لوحة الإدارة')</title>
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Cairo', 'sans-serif'] }
                }
            }
        };
    </script>
    
    <style>
        body { font-family: 'Cairo', sans-serif; }
        
        .sidebar-link {
            display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
            padding: 0.6rem 1rem; border-radius: 0.5rem;
            font-size: 0.85rem; font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s ease;
        }
        .sidebar-link i { width: 1.25rem; text-align: center; font-size: 0.9rem; opacity: 0.8; }
        .sidebar-link:hover { color: #fff; background: rgba(255, 255, 255, 0.05); }
        .nav-active { color: #fff !important; background: rgba(59, 130, 246, 0.1) !important; }
        .nav-active i { color: #3b82f6 !important; opacity: 1; }

        .layout-wrapper { padding-right: 0; }
        @media (min-width: 768px) {
            .layout-wrapper { padding-right: 260px; }
            .layout-wrapper.sidebar-closed { padding-right: 0; }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 overflow-x-hidden">

    <div x-data="{ sidebarOpen: window.innerWidth > 768 }" 
         @resize.window="sidebarOpen = window.innerWidth > 768"
         class="min-h-screen flex flex-col transition-all duration-300 layout-wrapper"
         :class="!sidebarOpen ? 'sidebar-closed' : ''">
        
        {{-- خلفية شفافة للموبايل --}}
        <div x-show="sidebarOpen && window.innerWidth <= 768"
             x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/40 z-20 md:hidden"
             style="display: none;">
        </div>

        {{-- القائمة الجانبية --}}
<aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
       class="w-[260px] bg-[#0f172a] flex flex-col justify-between fixed right-0 top-0 h-full z-30 border-l border-white/5 transition-transform duration-300 ease-in-out">
    
    <div class="pt-6 pb-5 px-5 border-b border-white/5">
        
        {{-- هوية النظام --}}
        <div class="flex items-center justify-start gap-3 mb-6">
            {{-- الشعار (الأيقونة) --}}
            <div class="w-9 h-9 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-balance-scale text-blue-400 text-sm"></i>
            </div>
            
            {{-- اسم المنصة --}}
            <div class="leading-none text-right">
                <h1 class="text-lg font-bold text-white tracking-wide">ميزان</h1>
            </div>
        </div>

                {{-- بيانات المدير --}}
                <div class="flex items-center justify-between bg-white/5 p-2.5 rounded-xl border border-white/5 gap-3">
                    <div class="min-w-0 text-right flex-1">
                        <h2 class="text-white text-sm font-semibold truncate">{{ Auth::check() ? Auth::user()->name : 'المدير العام' }}</h2>
                        <span class="text-blue-400 text-[11px] font-semibold tracking-wider">إدارة النظام</span>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 border border-slate-700">
                        @if(Auth::check() && Auth::user()->avatar)
                            <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user-tie text-slate-400 text-lg"></i>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- الروابط --}}
            <nav class="flex-1 px-3 mt-4 space-y-1 overflow-y-auto">
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="sidebar-link @yield('admin_dashboard_active')">
                    <span class="flex-1 text-right">لوحة التحكم</span>
                    <i class="fas fa-home"></i>
                </a>
                <a href="{{ Route::has('admin.users') ? route('admin.users') : '#' }}" class="sidebar-link @yield('admin_users_active')">
                    <span class="flex-1 text-right">إدارة المستخدمين</span>
                    <i class="fas fa-users"></i>
                </a>
                <a href="{{ Route::has('admin.verification') ? route('admin.verification') : '#' }}" class="sidebar-link @yield('admin_verification_active')">
                    <span class="flex-1 text-right">توثيق المحامين</span>
                    <i class="fas fa-id-card"></i>
                </a>
                <a href="{{ Route::has('admin.all-cases') ? route('admin.all-cases') : '#' }}" class="sidebar-link @yield('admin_cases_active')">
                    <span class="flex-1 text-right">قضايا النظام</span>
                    <i class="fas fa-folder-open"></i>
                </a>
                <a href="{{ Route::has('admin.finance') ? route('admin.finance') : '#' }}" class="sidebar-link @yield('admin_finance_active')">
                    <span class="flex-1 text-right">الإدارة المالية والبلاغات</span>
                    <i class="fas fa-coins"></i>
                </a>
                <a href="{{ Route::has('admin.settings') ? route('admin.settings') : '#' }}" class="sidebar-link @yield('admin_settings_active')">
                    <span class="flex-1 text-right">الإعدادات</span>
                    <i class="fas fa-cog"></i>
                </a>
            </nav>
            
            {{-- تسجيل الخروج --}}
            <div class="px-3 pb-5 mt-2 border-t border-white/5 pt-3">
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-red-400/70 hover:text-red-400 hover:bg-red-500/10">
                        <span class="flex-1 text-right">تسجيل الخروج</span>
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </aside>

        {{-- حاوية المحتوى الرئيسي --}}
<main class="flex-1 p-5 md:p-7 relative w-full max-w-[100vw]">

    {{-- الشريط العلوي --}}
    <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <div class="flex items-center gap-4">
            {{-- زر القائمة (الثلاث شرطات) --}}
            <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 shrink-0 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:text-slate-700 hover:border-slate-300 transition-colors">
                <i class="fas fa-bars text-sm"></i>
            </button>

            {{-- عنوان الصفحة --}}
            <div>
                <h1 class="text-slate-800 text-xl md:text-2xl font-bold text-right">@yield('page_title')</h1>
                <p class="text-slate-400 text-xs mt-0.5 text-right">@yield('page_subtitle')</p>
            </div>
        </div>

        <div class="flex items-center gap-3 relative z-[100]">
            
            {{-- المكون الخاص بالإشعارات --}}
            <x-notification-bell btnClass="bg-slate-700 border-white/10 text-white hover:bg-slate-600" />
        
            
            {{-- التاريخ --}}
            <div class="hidden sm:flex bg-white border border-slate-200 px-3 py-1.5 rounded-lg text-slate-500 text-xs font-medium items-center gap-2 shadow-sm">
                <span>{{ now()->locale('ar')->isoFormat('D MMMM YYYY') }}</span>
                <i class="far fa-calendar-alt text-slate-400"></i> 
            </div>
        </div>
    </div>

    {{-- المحتوى الديناميكي --}}
    <div>
        @yield('content')
    </div>
    
</main>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>