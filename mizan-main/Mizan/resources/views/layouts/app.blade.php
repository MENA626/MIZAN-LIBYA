<!DOCTYPE html>
<html lang="ar" dir="rtl">
     <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ميزان - @yield('title', 'العميل')</title>
    
    <!-- أيقونة النظام (ميزان) -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    
    <!-- دعم مشاركة الروابط واختصارات الهواتف -->
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta property="og:title" content="ميزان - لوحة تحكم العميل">
    <meta property="og:description" content="تصفح الخزنة الرقمية والخدمات القانونية الخاصة بك على منصة ميزان.">
    <meta property="og:image" content="{{ asset('apple-touch-icon.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f172a',
                        // تم تحديث اللون المميز هنا
                        accent: '#0f6afd', 
                        soft: '#f8fafc'
                    },
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0f172a;
            /* تم تحديث متغير CSS هنا أيضاً لضمان التناسق */
            --accent: #0f6afd; 
            --soft-bg: #f8fafc;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--soft-bg);
            color: var(--primary);
            overflow-x: hidden;
            min-height: 100vh;
        }

        .nav-link { position: relative; transition: all 0.3s ease; }
        .nav-link::after {
            content: ''; position: absolute; bottom: 0; left: 50%;
            width: 0; height: 2px; background: var(--accent);
            transition: all 0.3s ease; transform: translateX(-50%);
        }
        .nav-link:hover::after, .nav-active-pill::after { width: 60%; }
        /* تم تحديث الشفافية هنا لتناسب اللون الجديد */
        .nav-active-pill { color: white !important; background: rgba(15, 106, 253, 0.15); } 

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--soft-bg); }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .main-content { animation: fadeIn 0.6s ease-out; width: 100%; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="antialiased" x-data="{ mobileMenuOpen: false }" :class="{ 'overflow-hidden': mobileMenuOpen }">

<div class="hidden xl:block sticky top-0 w-full z-[100] bg-[#f8fafc] px-2 sm:px-4 md:px-8 pt-2 sm:pt-4 pb-2">
    <header class="bg-primary border-b border-black border-t border-white/5 rounded-2xl sm:rounded-[2rem] h-16 sm:h-20 flex items-center transition-all duration-500 shadow-2xl shadow-accent/20">
        <div class="w-full mx-auto px-3 sm:px-6 md:px-10 flex justify-between items-center">

            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl bg-accent flex items-center justify-center shadow-lg shadow-accent/30 group-hover:rotate-12 transition-transform">
                    <i class="fas fa-balance-scale text-white text-xl"></i>
                </div>
                <div class="leading-none">
                    <h1 class="text-xl font-black text-white tracking-tight">ميزان</h1>
                    <p class="text-[10px] text-white font-bold tracking-widest uppercase mt-1"> التقنـية في خدمة العدالة </p>
                </div>
            </a>

            <nav class="hidden xl:flex items-center gap-1">
                @php
                    $navItems = [
                        ['name' => 'الرئيسية', 'url' => '/client/dashboard', 'icon' => 'fa-home'],
                        ['name' => 'الخزنة الرقمية الذكية', 'url' => '/client/smart_library', 'icon' => 'fa-microchip'],
                        ['name' => 'مساعد ميزان الذكي', 'url' => '/client/assistant', 'icon' => 'fa-robot'],
                        ['name' => 'المحامين', 'url' => '/client/lawyers', 'icon' => 'fa-users'],
                        ['name' => 'استشاراتي', 'url' => '/client/consultations', 'icon' => 'fa-comment-dots'],
                        ['name' => 'قضيتي', 'url' => '/client/my-case', 'icon' => 'fa-folder-open'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ url($item['url']) }}"
                       class="nav-link px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2.5 transition-all
                       {{ Request::is(trim($item['url'], '/')) ? 'nav-active-pill text-white shadow-lg' : 'text-gray-300 hover:text-white' }}">
                        <i class="fas {{ $item['icon'] }} text-[12px] {{ Request::is(trim($item['url'], '/')) ? 'text-accent' : 'text-gray-500' }}"></i>
                        {{ $item['name'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">

                {{-- زر قائمة الموبايل --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="xl:hidden w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-all">
                    <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                </button>

            {{-- تم استبدال زر الجرس القديم بالمكون الجديد هنا --}}
              <x-notification-bell />

                <div class="relative group">
                    <button class="flex items-center gap-3 bg-white/10 p-1.5 pl-4 rounded-full border border-white/10 hover:border-accent/50 transition-all">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-accent to-primary flex items-center justify-center text-white text-xs font-bold overflow-hidden shrink-0">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr(Auth::user()->name ?? 'م', 0, 1, 'UTF-8') }}
                            @endif
                        </div>
                        <span class="text-xs font-bold text-white hidden lg:block">{{ Auth::user()->name ?? 'المستخدم' }}</span>
                    </button>
                    
                    <div class="absolute left-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all p-2 transform origin-top-left scale-95 group-hover:scale-100">
                        <a href="{{ url('/client/settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-soft transition text-primary font-bold text-sm">
                            <i class="fas fa-cog text-accent"></i> الإعدادات
                        </a>
                        <hr class="my-1 border-gray-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-50 transition text-red-500 font-bold text-sm text-right">
                                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </header>
</div>

{{-- الشريط العلوي للموبايل (يظهر فقط على الشاشات الصغيرة) --}}
<div class="xl:hidden fixed top-0 w-full z-[90] bg-slate-50 border-b border-slate-200 px-4 py-3 flex justify-between items-center shadow-sm">
    <div class="flex items-center gap-3">
        <button @click="mobileMenuOpen = true" class="w-9 h-9 shrink-0 rounded-lg bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:text-slate-700 hover:border-slate-300 transition-colors">
            <i class="fas fa-bars text-sm"></i>
        </button>
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                <i class="fas fa-balance-scale text-white text-xs"></i>
            </div>
            <h1 class="text-slate-800 text-lg font-black tracking-tight">ميزان</h1>
        </a>
    </div>
    <div class="flex items-center gap-2">
        <x-notification-bell btnClass="bg-white border-slate-200 text-slate-500 hover:bg-slate-50 shadow-sm" />
    </div>
</div>

{{-- قائمة الموبايل (Mobile Drawer) بتنسيق المحامي --}}
<div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/40 z-[99] xl:hidden" style="display: none;"></div>

<aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-250" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 h-full w-[260px] bg-[#0f172a] z-[101] xl:hidden flex flex-col justify-between border-l border-white/5" style="display: none;">
    
    {{-- رأس القائمة الجانبية --}}
    <div class="pt-6 pb-5 px-5 border-b border-white/5">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-600/15 flex items-center justify-center">
                    <i class="fas fa-balance-scale text-blue-400 text-sm"></i>
                </div>
                <div class="leading-none text-right">
                    <h1 class="text-lg font-bold text-white">ميزان</h1>
                </div>
            </div>
            <button @click="mobileMenuOpen = false" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        
        {{-- بيانات الزبون --}}
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full border border-white/10 flex items-center justify-center bg-slate-800 overflow-hidden shrink-0">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('uploads/avatars/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-slate-300 text-base font-semibold">
                        {{ mb_substr(Auth::user()->name ?? 'م', 0, 1, 'UTF-8') }}
                    </span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ Auth::user()->name ?? 'المستخدم' }}</p>
                <p class="text-slate-400 text-xs font-medium">العميل</p>
            </div>
        </div>
    </div>
    
    {{-- روابط التنقل --}}
    <nav class="flex-1 px-3 mt-4 space-y-1 overflow-y-auto">
        @php
            $mobileNavItems = [
                ['name' => 'الرئيسية', 'url' => '/client/dashboard', 'icon' => 'fa-home'],
                ['name' => 'الخزنة الرقمية', 'url' => '/client/smart_library', 'icon' => 'fa-folder-open'],
                ['name' => 'مساعد ميزان', 'url' => '/client/assistant', 'icon' => 'fa-robot'],
                ['name' => 'المحامين', 'url' => '/client/lawyers', 'icon' => 'fa-user-tie'],
                ['name' => 'استشاراتي', 'url' => '/client/consultations', 'icon' => 'fa-comment-dots'],
                ['name' => 'قضيتي', 'url' => '/client/my-case', 'icon' => 'fa-gavel'],
            ];
        @endphp
        
        @foreach($mobileNavItems as $item)
            <a href="{{ url($item['url']) }}" @click="mobileMenuOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ Request::is(trim($item['url'], '/')) ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas {{ $item['icon'] }} w-5 text-center"></i>
                {{ $item['name'] }}
            </a>
        @endforeach
        
        <a href="{{ url('/client/settings') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-slate-400 hover:bg-white/5 hover:text-white">
            <i class="fas fa-cog w-5 text-center"></i>
            الإعدادات
        </a>
    </nav>
    
    {{-- أزرار سفلية --}}
    <div class="px-3 pb-5 mt-2 border-t border-white/5 pt-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium w-full text-red-400/70 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>
</aside>

<main class="main-content pt-6 xl:pt-8 min-h-screen">
    @yield('content')
</main>
{{-- مكتبات التحديث اللحظي للوقت --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/ar.min.js"></script>
<script>moment.locale('ar');</script>

@stack('scripts')
</body>
</html>