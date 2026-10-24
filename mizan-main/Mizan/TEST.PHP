@extends('layouts.app')

@section('title', 'المكتبة القانونية الذكية | ميزان')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Cairo', sans-serif; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .bg-grid-pattern {
        background-image: linear-gradient(to right, #f8fafc 2px, transparent 2px),
                          linear-gradient(to bottom, #f8fafc 2px, transparent 2px);
        background-size: 50px 50px;
        background-position: center center;
    }

    .bg-stripes {
    background-color: #f8fafc; /* اختر اللون الذي تريده هنا */
    background-image: none;    /* إلغاء الخطوط */
}

    .dashed-line {
        position: absolute;
        top: 40%;
        left: 0;
        right: 0;
        height: 2px;
        background-image: linear-gradient(to right, #cbd5e1 50%, transparent 50%);
        background-size: 15px 2px;
        z-index: 0;
        opacity: 0.5;
    }

    /* حركات التموجات والخلفية الزرقاء */
    @keyframes gradient-move {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .animate-gradient-bg {
        background-size: 200% 200%;
        animation: gradient-move 10s ease infinite;
    }

    /* تأثير الموج المتدفق من الداخل */
    @keyframes wave-inside {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .wave-fluid {
        position: absolute;
        top: -60%;
        right: -20%;
        width: 150%;
        height: 200%;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 40%;
        animation: wave-inside 15s infinite linear;
        pointer-events: none;
    }
    .wave-fluid:nth-child(2) {
        top: -80%;
        right: -10%;
        width: 160%;
        background: rgba(255, 255, 255, 0.06);
        animation: wave-inside 20s infinite linear reverse;
        border-radius: 45%;
    }
    .wave-fluid:nth-child(3) {
        top: -40%;
        right: -30%;
        width: 140%;
        background: linear-gradient(to left, rgba(255,255,255,0.02), rgba(255,255,255,0.08));
        animation: wave-inside 25s infinite linear;
        border-radius: 42%;
    }

    /* حركة خفيفة للمستندات المتناثرة */
    @keyframes float-docs {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-8px) rotate(1deg); }
    }
    .animate-float-docs {
        animation: float-docs 6s ease-in-out infinite;
    }
</style>

<div class="h-screen bg-slate-50 text-slate-800 p-4 overflow-hidden flex gap-6" dir="rtl">

    <aside class="w-20 bg-white border border-gray-200 rounded-3xl flex flex-col items-center py-8 shadow-sm flex-shrink-0 z-50">
        <button class="mb-12 text-[#0061ff] hover:text-blue-800 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </button>
        
        <nav class="flex flex-col gap-8 w-full items-center relative z-50">
            <a href="#" onclick="switchTab('library'); return false;" id="nav-library" class="nav-item p-3 bg-blue-50 text-blue-600 rounded-xl shadow-sm relative group transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span class="absolute right-14 top-1/2 -translate-y-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">المكتبة الذكية</span>
            </a>

            <a href="#" onclick="switchTab('notifications'); return false;" id="nav-notifications" class="nav-item p-3 text-slate-400 hover:text-[#0061ff] hover:bg-slate-50 rounded-xl transition-all relative group">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-2.5 right-3 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full animate-pulse"></span>
                <span class="absolute right-14 top-1/2 -translate-y-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">تنبيهات الصلاحية</span>
            </a>

            <a href="#" onclick="switchTab('trash'); return false;" id="nav-trash" class="nav-item p-3 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all relative group">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span class="absolute right-14 top-1/2 -translate-y-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">المحذوفات مؤخراً</span>
            </a>
        </nav>
    </aside>

    <!-- ========================================== -->
    <!-- الواجهة الأولى: المكتبة الأساسية -->
    <!-- ========================================== -->
    <div id="view-library" class="flex-1 flex gap-6 overflow-hidden transition-opacity duration-300">
        <main class="flex-1 flex flex-col overflow-hidden">
            
            <!-- البنر الأزرق مع أمواج من الداخل والملفات المتناثرة البيضاء -->
            <div class="bg-gradient-to-l from-[#0061ff] to-[#0047cc] animate-gradient-bg rounded-[2rem] px-10 py-10 mt-2 mb-6 relative overflow-hidden text-white shadow-lg flex-shrink-0 flex items-center justify-between group">
                
                <!-- حاوية الأمواج (تأثير الموج الداخلي) -->
                <div class="absolute inset-0 z-0 pointer-events-none">
                    <div class="wave-fluid"></div>
                    <div class="wave-fluid"></div>
                    <div class="wave-fluid"></div>
                </div>
                
                <!-- المحتوى النصي (يمين) + زر الإضافة -->
                <div class="relative z-10 w-2/3 pl-8">
                    <span class="text-xs font-bold tracking-widest uppercase opacity-80 mb-2 block text-blue-200">Smart Legal Vault</span>
                    <h1 class="text-3xl md:text-3xl font-bold mb-3 leading-snug text-white drop-shadow-md">
                        الخزنة الرقمية الذكية <br>
                        <span class="text-blue-100 font-medium text-lg mt-1 block leading-[1.8]">حوّل طريقة إدارة مستنداتك إلى تجربة أكثر ذكاءً وكفاءة. احتفظ بجميع ملفاتك المهمة في مكتبة رقمية متطورة تضمن سهولة الحفظ والتنظيم والوصول السريع، واستخلص أبرز المعلومات والنقاط الأساسية من الوثائق الطويلة في لحظات، لتوفير الوقت وتعزيز الإنتاجية.</span>
                    </h1>

                    <!-- زر الإضافة أسفل النص -->
                    <button class="mt-6 bg-white text-[#0061ff] hover:bg-slate-50 hover:-translate-y-1 px-6 py-3 rounded-2xl font-bold flex items-center gap-3 transition-all shadow-lg text-sm border border-blue-50/50 w-max">
                        <span>إنشاء مجلد جديد</span>
                        <span class="bg-blue-50 text-[#0061ff] rounded-xl p-1.5 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        </span>
                    </button>
                </div>

                <!-- رسمة المستندات البيضاء الشفافة المتناثرة كجزء من الخلفية -->
                <div class="absolute left-10 top-1/2 transform -translate-y-1/2 z-0 w-1/3 flex justify-end pointer-events-none opacity-25 group-hover:opacity-40 transition-opacity duration-700">
                    <svg class="w-64 h-64 text-white animate-float-docs" viewBox="0 0 120 120" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                        
                        <!-- ورقة متطايرة صغيرة علوية يمين -->
                        <g transform="rotate(25 90 20)" stroke-width="1.5" opacity="0.6">
                            <path d="M85 10 h12 l6 6 v14 a2 2 0 0 1 -2 2 h-16 a2 2 0 0 1 -2 -2 v-18 a2 2 0 0 1 2 -2 z" fill="currentColor" fill-opacity="0.1"/>
                            <path d="M97 10 v6 h6"/>
                            <path d="M89 20 h10 M89 25 h7"/>
                        </g>

                        <!-- مستند رئيسي مايل لليسار في الخلف -->
                        <g transform="rotate(-15 35 60)" stroke-width="1.5" opacity="0.7">
                            <path d="M20 30 h25 l12 12 v40 a4 4 0 0 1 -4 4 h-33 a4 4 0 0 1 -4 -4 v-48 a4 4 0 0 1 4 -4 z" fill="currentColor" fill-opacity="0.15"/>
                            <path d="M45 30 v12 h12" stroke-linejoin="round"/>
                            <path d="M28 50 h20 M28 60 h25 M28 70 h15" stroke-linecap="round"/>
                        </g>

                        <!-- مستند رئيسي كبير في المنتصف مستقيم -->
                        <g stroke-width="2" opacity="1">
                            <path d="M45 25 h30 l15 15 v50 a4 4 0 0 1 -4 4 h-41 a4 4 0 0 1 -4 -4 v-61 a4 4 0 0 1 4 -4 z" fill="currentColor" fill-opacity="0.25"/>
                            <path d="M75 25 v15 h15" stroke-linejoin="round"/>
                            <path d="M55 55 h25 M55 68 h30 M55 81 h20" stroke-linecap="round"/>
                        </g>

                        <!-- ورقة متطايرة صغيرة أسفل يسار -->
                        <g transform="rotate(-20 20 90)" stroke-width="1" opacity="0.5">
                            <path d="M10 80 h10 l4 4 v10 a1.5 1.5 0 0 1 -1.5 1.5 h-12.5 a1.5 1.5 0 0 1 -1.5 -1.5 v-12.5 a1.5 1.5 0 0 1 1.5 -1.5 z" fill="currentColor" fill-opacity="0.1"/>
                            <path d="M20 80 v4 h4"/>
                        </g>
                        
                        <!-- نقاط / دوائر زينة متناثرة تندمج مع الخلفية -->
                        <circle cx="20" cy="30" r="1.5" fill="currentColor" opacity="0.4"/>
                        <circle cx="95" cy="70" r="2" fill="currentColor" opacity="0.5"/>
                        <circle cx="105" cy="45" r="1" fill="currentColor" opacity="0.3"/>
                        <circle cx="35" cy="100" r="2" fill="currentColor" opacity="0.6"/>
                    </svg>
                </div>
            </div>

            <div class="flex-1 bg-white border border-gray-200 rounded-[2rem] p-8 overflow-y-auto shadow-sm custom-scrollbar flex flex-col">
                
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                    <h2 class="text-xl font-bold text-slate-800">المجلدات الخاصة بك</h2>
                    <div class="relative w-72">
                        <input type="text" placeholder="ابحث عن مجلد أو مستند..." 
                               class="w-full bg-slate-50 border border-gray-200 rounded-full py-2.5 px-5 text-sm focus:outline-none focus:border-[#0061ff] focus:ring-1 focus:ring-[#0061ff] transition-shadow shadow-sm">
                        <svg class="w-4 h-4 text-gray-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- حجم المجلدات ثابت مع اللون الأزرق الداكن الأساسي -->
                <div class="flex flex-wrap gap-6 pt-2">
                    
                    <div class="relative group w-[260px] h-56 cursor-pointer flex-shrink-0">
                        <div class="absolute inset-0 bg-blue-800 rounded-[2rem] shadow-sm transition-transform duration-300 group-hover:scale-[1.02]"></div>
                        <div class="absolute bottom-16 right-6 w-3/4 h-28 bg-white/70 border border-gray-100 rounded-xl transform -rotate-6 transition-all duration-500 ease-out group-hover:-translate-y-12 group-hover:-rotate-12 group-hover:bg-white/90 shadow-sm"></div>
                        <div class="absolute bottom-16 right-8 w-3/4 h-28 bg-white/80 border border-gray-100 rounded-xl transform rotate-3 transition-all duration-500 ease-out group-hover:-translate-y-16 group-hover:rotate-6 group-hover:bg-white shadow-sm"></div>
                        <div class="absolute bottom-16 right-10 w-3/4 h-28 bg-white border border-gray-100 rounded-xl shadow-md transition-all duration-500 ease-out group-hover:-translate-y-8 flex flex-col p-4 gap-3 z-0">
                            <div class="w-1/2 h-2 bg-gray-200 rounded-full"></div>
                            <div class="w-3/4 h-2 bg-gray-200 rounded-full"></div>
                            <div class="w-1/3 h-2 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="absolute bottom-0 w-full h-[65%] bg-gradient-to-t from-blue-600 to-blue-500 backdrop-blur-md border-t border-white/20 rounded-[1.8rem] shadow-[0_-8px_20px_rgba(0,0,0,0.1)] flex flex-col justify-end p-5 z-10 transition-transform duration-300 group-hover:scale-[1.02]">
                            <div class="flex justify-between items-end">
                                <div>
                                    <h3 class="text-white font-bold text-lg mb-1 drop-shadow-sm">قضايا النزاع العقاري</h3>
                                    <p class="text-blue-100 text-xs font-medium opacity-90">12 ملف • تم التحديث أمس</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative group w-[260px] h-56 cursor-pointer flex-shrink-0">
                        <div class="absolute inset-0 bg-blue-800 rounded-[2rem] shadow-sm transition-transform duration-300 group-hover:scale-[1.02]"></div>
                        <div class="absolute bottom-16 right-6 w-3/4 h-28 bg-white/70 border border-gray-100 rounded-xl transform -rotate-6 transition-all duration-500 ease-out group-hover:-translate-y-12 group-hover:-rotate-12 group-hover:bg-white/90 shadow-sm"></div>
                        <div class="absolute bottom-16 right-8 w-3/4 h-28 bg-white/80 border border-gray-100 rounded-xl transform rotate-3 transition-all duration-500 ease-out group-hover:-translate-y-16 group-hover:rotate-6 group-hover:bg-white shadow-sm"></div>
                        <div class="absolute bottom-16 right-10 w-3/4 h-28 bg-white border border-gray-100 rounded-xl shadow-md transition-all duration-500 ease-out group-hover:-translate-y-8 flex flex-col p-4 gap-3 z-0">
                            <div class="w-1/2 h-2 bg-gray-200 rounded-full"></div>
                            <div class="w-3/4 h-2 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="absolute bottom-0 w-full h-[65%] bg-gradient-to-t from-blue-600 to-blue-500 backdrop-blur-md border-t border-white/20 rounded-[1.8rem] shadow-[0_-8px_20px_rgba(0,0,0,0.1)] flex flex-col justify-end p-5 z-10 transition-transform duration-300 group-hover:scale-[1.02]">
                            <div class="flex justify-between items-end">
                                <div>
                                    <h3 class="text-white font-bold text-lg mb-1 drop-shadow-sm">مستندات الموكل</h3>
                                    <p class="text-blue-100 text-xs font-medium opacity-90">4 ملفات • تم التحديث اليوم</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative group w-[260px] h-56 cursor-pointer flex-shrink-0">
                        <div class="absolute inset-0 bg-blue-800 rounded-[2rem] shadow-sm transition-transform duration-300 group-hover:scale-[1.02]"></div>
                        <div class="absolute bottom-16 right-6 w-3/4 h-28 bg-white/70 border border-gray-100 rounded-xl transform -rotate-6 transition-all duration-500 ease-out group-hover:-translate-y-12 group-hover:-rotate-12 group-hover:bg-white/90 shadow-sm"></div>
                        <div class="absolute bottom-16 right-8 w-3/4 h-28 bg-white/80 border border-gray-100 rounded-xl transform rotate-3 transition-all duration-500 ease-out group-hover:-translate-y-16 group-hover:rotate-6 group-hover:bg-white shadow-sm"></div>
                        <div class="absolute bottom-16 right-10 w-3/4 h-28 bg-white border border-gray-100 rounded-xl shadow-md transition-all duration-500 ease-out group-hover:-translate-y-8 flex flex-col p-4 gap-3 z-0">
                            <div class="w-1/2 h-2 bg-gray-200 rounded-full"></div>
                            <div class="w-3/4 h-2 bg-gray-200 rounded-full"></div>
                            <div class="w-1/3 h-2 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="absolute bottom-0 w-full h-[65%] bg-gradient-to-t from-blue-600 to-blue-500 backdrop-blur-md border-t border-white/20 rounded-[1.8rem] shadow-[0_-8px_20px_rgba(0,0,0,0.1)] flex flex-col justify-end p-5 z-10 transition-transform duration-300 group-hover:scale-[1.02]">
                            <div class="flex justify-between items-end">
                                <div>
                                    <h3 class="text-white font-bold text-lg mb-1 drop-shadow-sm">عقود تجارية</h3>
                                    <p class="text-blue-100 text-xs font-medium opacity-90">8 ملفات • الأسبوع الماضي</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- صندوق التلخيص تم توسيعه إلى w-[34rem] -->
        <aside class="w-[34rem] bg-white border border-gray-200 rounded-[2rem] p-6 flex flex-col shadow-sm flex-shrink-0">
            
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0061ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                أدوات المكتبة الذكية
            </h2>

            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-[#0061ff] hover:bg-blue-50/50 transition-all mb-4 bg-slate-50 relative group cursor-pointer">
                <div class="w-12 h-12 bg-white rounded-full shadow-sm mx-auto flex items-center justify-center text-gray-400 group-hover:text-[#0061ff] transition-colors mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <p class="text-sm font-bold text-slate-700">اسحب وأفلت المستند هنا</p>
                <p class="text-xs text-slate-400 mt-1">للحفظ أو الأرشفة المباشرة</p>
            </div>

            <button class="w-full bg-[#0f172a] hover:bg-black text-white py-3.5 rounded-xl text-sm font-bold shadow-md transition-all mb-6 flex justify-center items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd"></path></svg>
                تلخيص ذكي للمستند
            </button>

            <!-- نتيجة التلخيص -->
            <div class="flex-1 bg-slate-50 border border-gray-100 rounded-2xl p-6 flex flex-col overflow-hidden relative">
                <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-gray-200 pb-3">نتيجة التلخيص: <span class="text-sm font-semibold text-slate-500 font-mono">عقد_إيجار.pdf</span></h3>
                
                <div class="overflow-y-auto custom-scrollbar pr-2 flex-1 space-y-5">
                    <p class="text-sm text-slate-600 leading-relaxed font-medium">تم تحليل المستند بنجاح. إليك أبرز النقاط المستخرجة من العقد المرفق:</p>
                    
                    <ul class="text-sm text-slate-700 space-y-4 font-medium leading-relaxed">
                        <li class="flex items-start gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <span class="text-[#0061ff] mt-0.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            <span>يقر الطرفان بصحة التوقيعات على العقد المبرم وتطابقها التام مع السجلات الرسمية والبيانات الموثقة.</span>
                        </li>
                        <li class="flex items-start gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <span class="text-[#0061ff] mt-0.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                            <span>مدة العقد المتفق عليها هي <strong class="text-slate-900">سنتان كاملتان</strong>، وهي قابلة للتجديد بموافقة الطرفين قبل انتهاء المدة.</span>
                        </li>
                    </ul>
                </div>

                <!-- زر النسخ في الأسفل -->
                <div class="mt-5 pt-4 border-t border-gray-200 flex gap-3">
                    <button class="flex-1 bg-white hover:bg-gray-50 border border-gray-200 text-slate-700 py-3 rounded-xl text-sm font-bold transition-colors flex justify-center items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        نسخ الملخص
                    </button>
                    <button class="flex-1 bg-white hover:bg-gray-50 border border-gray-200 text-slate-700 py-3 rounded-xl text-sm font-bold transition-colors flex justify-center items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        حفظ كمسودة
                    </button>
                </div>
            </div>
        </aside>
    </div>

<!-- الواجهة الثانية: التنبيهات (مع أيقونات إضافية لكل حالة) -->
<main id="view-notifications" class="flex-1 hidden flex-col overflow-hidden bg-white border border-gray-200 rounded-[2rem] shadow-sm relative transition-opacity duration-300">
    <header class="flex justify-between items-center p-8 pb-4 z-10 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-wide flex items-center gap-2">
                <span class="text-[#0061ff] font-serif italic text-4xl">Smart</span>
                تنبيهات المستندات
            </h1>
            <p class="text-sm text-slate-500 mt-1 font-semibold">تتبع تواريخ الانتهاء والصلاحية للملفات القانونية</p>
        </div>
    </header>

    <div class="flex-1 p-8 overflow-y-auto custom-scrollbar bg-grid-pattern relative">
        <div class="dashed-line hidden lg:block"></div>
        <div class="flex flex-wrap gap-x-12 gap-y-16 mt-6 pb-12 relative z-10">
            
            <!-- 1. أحمر (حرج - ساعة رملية) -->
            <div class="relative pt-8 transition-transform hover:-translate-y-2 duration-300 w-[300px]">
                <div class="absolute top-3 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full shadow-[0_5px_15px_rgba(225,29,72,0.6)] bg-gradient-to-br from-rose-400 to-rose-700 border border-rose-300 relative">
                        <div class="absolute top-1.5 left-1.5 w-2.5 h-2.5 bg-white/60 rounded-full"></div>
                    </div>
                </div>
                <!-- أيقونة الحالة -->
                <div class="absolute -top-6 -left-3 w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg text-rose-500 z-30 border-4 border-rose-50">
                    <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="bg-rose-50 p-2.5 rounded-[2.2rem] shadow-sm h-full border border-rose-100">
                    <div class="bg-white rounded-[1.8rem] p-6 pt-10 h-full flex flex-col shadow-sm">
                        <h2 class="text-4xl font-black text-rose-600 mb-1">01</h2>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">جواز سفر الموكل</h3>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed mb-6 flex-1">
                            متبقي 5 أيام فقط على انتهاء الصلاحية. يجب اتخاذ إجراء فوري.
                        </p>
                        <div class="mt-auto border-t border-slate-100 pt-4 flex justify-between items-center">
                            <span class="bg-rose-100 text-rose-700 font-bold px-4 py-2 rounded-xl text-sm">حرج - عاجل</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. أصفر (تنبيه - تقويم) -->
            <div class="relative pt-8 transition-transform hover:-translate-y-2 duration-300 w-[300px]">
                <div class="absolute top-3 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full shadow-[0_5px_15px_rgba(245,158,11,0.6)] bg-gradient-to-br from-amber-400 to-amber-700 border border-amber-300 relative">
                        <div class="absolute top-1.5 left-1.5 w-2.5 h-2.5 bg-white/60 rounded-full"></div>
                    </div>
                </div>
                <!-- أيقونة الحالة -->
                <div class="absolute -top-6 -left-3 w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg text-amber-500 z-30 border-4 border-amber-50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="bg-amber-50 p-2.5 rounded-[2.2rem] shadow-sm h-full border border-amber-100">
                    <div class="bg-white rounded-[1.8rem] p-6 pt-10 h-full flex flex-col shadow-sm">
                        <h2 class="text-4xl font-black text-amber-500 mb-1">02</h2>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">عقد الإيجار</h3>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed mb-6 flex-1">
                            متبقي 28 يوم على الانتهاء. نوصي بالتواصل للتجديد.
                        </p>
                        <div class="mt-auto border-t border-slate-100 pt-4 flex justify-between items-center">
                            <span class="bg-amber-100 text-amber-700 font-bold px-4 py-2 rounded-xl text-sm">تنبيه - قريب</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. أخضر (ساري - درع صح) -->
            <div class="relative pt-8 transition-transform hover:-translate-y-2 duration-300 w-[300px]">
                <div class="absolute top-3 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full shadow-[0_5px_15px_rgba(16,185,129,0.6)] bg-gradient-to-br from-emerald-400 to-emerald-700 border border-emerald-300 relative">
                        <div class="absolute top-1.5 left-1.5 w-2.5 h-2.5 bg-white/60 rounded-full"></div>
                    </div>
                </div>
                <!-- أيقونة الحالة -->
                <div class="absolute -top-6 -left-3 w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg text-emerald-500 z-30 border-4 border-emerald-50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div class="bg-emerald-50 p-2.5 rounded-[2.2rem] shadow-sm h-full border border-emerald-100">
                    <div class="bg-white rounded-[1.8rem] p-6 pt-10 h-full flex flex-col shadow-sm">
                        <h2 class="text-4xl font-black text-emerald-500 mb-1">03</h2>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">التوكيل الرسمي</h3>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed mb-6 flex-1">
                            ساري المفعول. الوقت المتبقي آمن ولا يتطلب إجراء حالياً.
                        </p>
                        <div class="mt-auto border-t border-slate-100 pt-4 flex justify-between items-center">
                            <span class="bg-emerald-100 text-emerald-700 font-bold px-4 py-2 rounded-xl text-sm">ساري - آمن</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

    <!-- ========================================== -->
    <!-- الواجهة الثالثة: المحذوفات مؤخراً -->
    <!-- ========================================== -->
    <main id="view-trash" class="flex-1 hidden flex-col overflow-hidden bg-white border border-gray-200 rounded-[2rem] shadow-sm relative transition-opacity duration-300">
        <header class="flex justify-between items-center p-8 pb-6 z-10 bg-white border-b border-gray-100 shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-wide flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 text-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </div>
                    سلة المحذوفات
                </h1>
                <p class="text-sm text-slate-500 mt-2 font-medium">العناصر الموجودة هنا سيتم التخلص منها نهائياً بعد مرور 30 يوماً</p>
            </div>
            
            <button class="bg-white hover:bg-red-50 text-red-600 px-6 py-2.5 rounded-full text-sm font-bold transition-all flex items-center gap-2 border-2 border-red-100 hover:border-red-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                إفراغ السلة
            </button>
        </header>

        <div class="flex-1 p-8 overflow-y-auto custom-scrollbar bg-stripes">
            <div class="flex flex-wrap gap-6">
                
                <div class="w-[280px] bg-white border-2 border-dashed border-gray-300 hover:border-red-300 rounded-[1.5rem] p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden flex flex-col flex-shrink-0">
                    <div class="absolute -top-6 -right-6 w-16 h-16 bg-red-100 rounded-full blur-xl group-hover:bg-red-200 transition-colors"></div>
                    <div class="flex justify-between items-start mb-4 z-10">
                        <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0">
                            <button class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-500 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                            <button class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="z-10 flex-1">
                        <h3 class="text-base font-bold text-slate-700 line-through decoration-red-400 decoration-2 mb-1">plutu_test.pdf</h3>
                        <p class="text-xs text-slate-400 font-medium mb-4">حُذفت قبل 15 يوم</p>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mb-2 overflow-hidden z-10">
                        <div class="bg-red-400 h-1.5 rounded-full" style="width: 85%"></div>
                    </div>
                    <div class="flex justify-between items-center z-10">
                        <span class="text-[10px] text-red-500 font-bold">باقي 3 أيام</span>
                    </div>
                </div>

            </div>
        </div>
    </main>

</div>

<script>
    function switchTab(tab) {
        const views = ['library', 'notifications', 'trash'];
        
        views.forEach(v => {
            document.getElementById('view-' + v).classList.add('hidden');
            document.getElementById('view-' + v).classList.remove('flex');
            
            let nav = document.getElementById('nav-' + v);
            nav.classList.remove('bg-blue-50', 'text-[#0061ff]', 'shadow-sm', 'bg-red-50', 'text-red-500', 'text-red-600');
            nav.classList.add('text-slate-400');
        });

        document.getElementById('view-' + tab).classList.remove('hidden');
        document.getElementById('view-' + tab).classList.add('flex');
        
        let activeNav = document.getElementById('nav-' + tab);
        activeNav.classList.remove('text-slate-400');
        
        if(tab === 'trash') {
             activeNav.classList.add('bg-red-50', 'text-red-600', 'shadow-sm');
        } else {
             activeNav.classList.add('bg-blue-50', 'text-[#0061ff]', 'shadow-sm');
        }
    }
</script>
@endsection














@extends('layouts.lawyer')

@section('title', 'الإعدادات المهنية')
@section('page_title', 'الإعدادات المهنية')
@section('page_subtitle', 'تعديل ملفك المهني وإعدادات حسابك')

@section('lawyer_settings_active', 'nav-active')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-[1400px] mx-auto">
<style>
    /* 1. إخفاء الـ Checkbox الفعلي */
    input[type="checkbox"].sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    /* 2. تنسيق الـ Toggle عند التفعيل (تغيير اللون) */
    input[type="checkbox"]:checked + div {
        background-color: #4D96C1 !important;
    }

    /* 3. تحريك الدائرة عند التفعيل */
    input[type="checkbox"]:checked + div::after {
        transform: translateX(100%) !important;
    }
</style>
    
    {{-- ====================== Sidebar الإعدادات ====================== --}}
    <div class="lg:col-span-1">
        <div class="card lg:sticky lg:top-6 overflow-hidden">
            
            {{-- صورة الملف الشخصي والبيانات الأساسية --}}
            <div class="p-6 text-center border-b border-slate-100 bg-slate-50/50">
                <div class="relative inline-block group">
                    {{-- عرض الصورة المحدثة من مجلد public/uploads/avatars مباشرة --}}
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-800 flex items-center justify-center text-white text-3xl font-bold mx-auto border-4 border-white shadow-sm transition-transform duration-300 group-hover:scale-105">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('uploads/avatars/' . auth()->user()->avatar) }}" 
                                 alt="Profile Picture" 
                                 class="w-full h-full object-cover">
                        @else
                            {{ mb_substr(auth()->user()->name, 0, 1, 'UTF-8') }}
                        @endif
                    </div>

                    {{-- زر الكاميرا المتفاعل --}}
                    <label for="avatar_upload" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center hover:bg-blue-600 transition-all duration-300 shadow-md cursor-pointer hover:scale-110">
                        <i class="fas fa-camera text-xs"></i>
                        <input type="file" id="avatar_upload" name="avatar" class="hidden" accept="image/*" form="profile-form" onchange="this.form.submit()">
                    </label>
                </div>
                
                <h3 class="font-bold text-slate-800 mt-3">{{ auth()->user()->name }}</h3>

                <div class="flex justify-center gap-1 mt-2">
                    @php 
                        $current_rating = auth()->user()->lawyerProfile->rating ?? 0; 
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-xs {{ $i <= round($current_rating) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                    @endfor
                    <span class="text-[10px] text-slate-400 mr-1">({{ number_format($current_rating, 1) }})</span>
                </div>
            
                {{-- عرض الدرجة القضائية ديناميكياً --}}
                <p class="text-xs text-blue-600 font-semibold mt-1.5">
                    @php
                        $profileSpec = auth()->user()->lawyerProfile?->specialization ?? '3';
                    @endphp

                    @if($profileSpec == '1')
                        محامٍ ممارس
                    @elseif($profileSpec == '2')
                        محامٍ معتمد لدى المحكمة العليا
                    @elseif($profileSpec == '3')
                        مستشار قانوني
                    @elseif($profileSpec == '4')
                        محكم دولي معتمد
                    @else
                        مستشار قانوني
                    @endif
                </p>
            </div>
    
            {{-- قائمة التبويبات --}}
            <div class="p-4 flex overflow-x-auto lg:block lg:space-y-1 snap-x scrollbar-hide border-b lg:border-none border-slate-100" id="settings-tabs-container">
                <button class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 font-semibold" data-tab="profile">
                    <i class="fas fa-user-tie w-5 text-center text-slate-400"></i>
                    <span>الملف المهني</span>
                </button>
                <button class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 font-semibold" data-tab="professional">
                    <i class="fas fa-briefcase w-5 text-center text-slate-400"></i>
                    <span>المعلومات المهنية</span>
                </button>
                <button class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 font-semibold" data-tab="financial">
                    <i class="fas fa-wallet w-5 text-center text-slate-400"></i>
                    <span>الإعدادات المالية</span>
                </button>
                <button data-tab="working-hours" class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 font-semibold">
                    <i class="fas fa-clock w-5 text-center text-slate-400"></i>
                    <span>أوقات العمل</span>
                </button>
                <button class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 font-semibold" data-tab="notifications">
                    <i class="fas fa-bell w-5 text-center text-slate-400"></i>
                    <span>الإشعارات</span>
                </button>
            </div>
            
            {{-- زر حذف الحساب --}}
<div class="p-4 border-t border-slate-100 flex lg:block overflow-x-auto lg:space-y-1 snap-x scrollbar-hide">
    <button type="button" 
            onclick="document.getElementById('deleteAccountModal').classList.remove('hidden'); document.getElementById('deleteAccountModal').classList.add('flex');" 
            class="flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 font-semibold text-sm">
        <i class="fas fa-trash-alt w-5 text-center text-red-400"></i>
        <span>حذف الحساب</span>
    </button>
</div>
</div>
</div>



    {{-- ====================== محتوى الإعدادات ====================== --}}

    <div class="lg:col-span-2">
    {{-- رسائل النجاح والخطأ --}}
    @if(session('success'))
        <div class="alert-message mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-700 text-sm font-semibold animate-pulse">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle text-emerald-500"></i>
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-message mb-5 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-700 text-sm font-semibold">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            {{ session('error') }}
        </div>
    @endif


        
       {{-- ====================== تبويب الملف المهني ====================== --}}
        <div id="tab-profile" class="settings-content hidden">
            <div class="bg-gradient-to-br from-[#F5F9FC] via-[#FAFCFD] to-white rounded-2xl shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden border border-[#4D96C1]/20">
                
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#EBF3F8] to-white/70">
                    <h3 class="text-[#0E2333] text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-user-tie text-[#4D96C1] text-xl"></i>
                        الملف المهني
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">تحديث معلومات حسابك الشخصية</p>
                </div>
                
                <form id="profile-form" action="{{ route('lawyer.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-[#334155]">الاسم الكامل</label>
                            <div class="relative rounded-xl">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-[#334155]">اليد الإلكتروني</label>
                            <div class="relative rounded-xl">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <i class="fas fa-envelope text-sm"></i>
                                </div>
                                <input type="email" value="{{ auth()->user()->email }}" class="w-full pr-10 pl-4 py-2.5 border border-gray-100 bg-gray-50 text-gray-400 rounded-xl text-sm outline-none cursor-not-allowed shadow-inner" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-[#334155]">رقم الهاتف</label>
                            <div class="relative rounded-xl">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                                    <i class="fas fa-phone text-sm"></i>
                                </div>
                                <input type="tel" name="phone" value="{{ auth()->user()->phone }}" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-[#334155]">رقم رخصة المحاماة</label>
                            <div class="relative rounded-xl">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                                    <i class="fas fa-id-card text-sm"></i>
                                </div>
                                <input type="text" name="license_number" value="{{ auth()->user()->lawyerProfile->license_number ?? '' }}" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-[#334155]">عنوان المكتب</label>
                        <div class="relative rounded-xl">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1] mb-6">
                                <i class="fas fa-map-marker-alt text-sm"></i>
                            </div>
                            <textarea name="address" rows="2" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] resize-none shadow-sm">{{ auth()->user()->lawyerProfile->address ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-50 pt-4">
                        <button type="submit" class="px-8 py-3 bg-[#1A3B53] text-white rounded-xl font-semibold shadow-md flex items-center gap-2 transition-all duration-300 hover:bg-[#2D4A6E] hover:-translate-y-0.5 active:translate-y-0 shadow-slate-200">
                            <i class="fas fa-save text-sm"></i> حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

       {{-- ====================== تبويب المعلومات المهنية ====================== --}}
<div id="tab-professional" class="settings-content hidden">
    <div class="bg-gradient-to-br from-[#F5F9FC] via-[#FAFCFD] to-white rounded-2xl shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden border border-[#4D96C1]/20">
        
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#EBF3F8] to-white/70">
            <h3 class="text-[#0E2333] text-lg font-bold flex items-center gap-2">
                <i class="fas fa-briefcase text-[#4D96C1] text-xl"></i>
                المعلومات المهنية
            </h3>
            <p class="text-xs text-gray-400 mt-1">التخصصات والخبرات المهنية</p>
        </div>
        
        <form action="{{ route('lawyer.profile.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
            <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">
            <input type="hidden" name="license_number" value="{{ auth()->user()->lawyerProfile->license_number ?? '12345' }}">
            <input type="hidden" name="address" value="{{ auth()->user()->lawyerProfile->address ?? 'العنوان الافتراضي' }}">
            
            <div class="block w-full">
                <label class="block text-sm font-semibold mb-2 text-[#334155]">التخصص الرئيسي</label>
                <div class="relative rounded-xl shadow-sm max-w-md">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                        <i class="fas fa-scale-balanced text-sm"></i>
                    </div>
                    <select name="specialty_id" class="w-full pr-10 pl-10 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] bg-white appearance-none cursor-pointer">
                        <option value="" disabled {{ is_null(auth()->user()->lawyerProfile->specialty_id ?? null) ? 'selected' : '' }}>اختر تخصصك الرئيسي من القائمة...</option>
                        @foreach($allSpecialties as $specialty)
                            <option value="{{ $specialty->id }}" {{ (auth()->user()->lawyerProfile->specialty_id ?? null) == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
                <p class="text-[11px] text-gray-400 mt-1.5 mr-1"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-[#334155]">سنوات الخبرة</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                            <i class="fas fa-award text-sm"></i>
                        </div>
                        <input type="number" name="years_of_experience" value="{{ auth()->user()->lawyerProfile->years_of_experience ?? '0' }}" min="0" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-[#334155]">الدرجة القضائية / العلمية</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                            <i class="fas fa-gavel text-sm"></i>
                        </div>
                        @php $currentSpecialization = old('specialization', auth()->user()->lawyerProfile->specialization ?? '3'); @endphp
                        <select name="specialization" class="w-full pr-10 pl-10 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] bg-white appearance-none cursor-pointer">
                            <option value="1" {{ $currentSpecialization == '1' ? 'selected' : '' }}>محامٍ ممارس</option>
                            <option value="2" {{ $currentSpecialization == '2' ? 'selected' : '' }}>محامٍ معتمد لدى المحكمة العليا</option>
                            <option value="3" {{ $currentSpecialization == '3' ? 'selected' : '' }}>مستشار قانوني</option>
                            <option value="4" {{ $currentSpecialization == '4' ? 'selected' : '' }}>محكم دولي معتمد</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- حقل السيرة الذاتية --}}
<div class="block w-full">
    <label class="block text-sm font-semibold mb-2 text-[#334155]">السيرة الذاتية والنبذة التعريفية</label>
    <div class="relative rounded-xl shadow-sm">
        {{-- تم تعديل الـ class هنا --}}
        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-[#4D96C1]">
            <i class="fas fa-align-right text-sm"></i>
        </div>
        <textarea name="bio" rows="4" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] resize-none shadow-inner">{{ auth()->user()->lawyerProfile->bio ?? '' }}</textarea>
    </div>
    <p class="text-[11px] text-gray-400 mt-1.5 mr-1">هذه النبذة ستظهر للعملاء في صفحة البحث والمقارنة العامة.</p>
</div>

{{-- حقل المؤهلات الأكاديمية --}}
<div class="block w-full">
    <label class="block text-sm font-semibold mb-2 text-[#334155]">المؤهلات الأكاديمية العلمية</label>
    <div class="relative rounded-xl shadow-sm">
        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-[#4D96C1]">
            <i class="fas fa-graduation-cap text-sm"></i>
        </div>
        <textarea name="qualifications" rows="4" placeholder="اكتب كل مؤهل في سطر جديد.." class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] resize-none shadow-inner">{{ auth()->user()->lawyerProfile->qualifications ?? '' }}</textarea>
    </div>
    <p class="text-[11px] text-gray-400 mt-1.5 mr-1"></p>
</div>
<div class="block w-full">
    <label class="block text-sm font-semibold mb-2 text-[#334155]">الخبرات المهنية والميدانية</label>
    <div class="relative rounded-xl shadow-sm">
        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-[#4D96C1]">
            <i class="fas fa-briefcase text-sm"></i>
        </div>
        <textarea name="experience" rows="4" placeholder="اكتب كل خبرة في سطر جديد.." class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333] resize-none shadow-inner">{{ auth()->user()->lawyerProfile->experience ?? '' }}</textarea>
    </div>
    <p class="text-[11px] text-gray-400 mt-1.5 mr-1"></p>
</div>
            <div class="flex justify-end border-t border-gray-50 pt-4 w-full">
                <button type="submit" class="px-8 py-3 bg-[#1A3B53] hover:bg-[#2D4A6E] text-white rounded-xl font-semibold shadow-md flex items-center gap-2 transition-all duration-300 hover:-translate-y-0.5 shadow-slate-200/50">
                    <i class="fas fa-save text-sm"></i> 
                    <span>حفظ التغييرات</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ====================== تبويب الإعدادات المالية ====================== --}}
<div id="tab-financial" class="settings-content hidden">
    <div class="bg-gradient-to-br from-[#F5F9FC] via-[#FAFCFD] to-white rounded-2xl shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden border border-[#4D96C1]/20">
        
        {{-- الرأس --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#EBF3F8] to-white/70">
            <h3 class="text-[#0E2333] text-lg font-bold flex items-center gap-2">
                <i class="fas fa-wallet text-[#4D96C1] text-xl"></i>
                الإعدادات المالية
            </h3>
            <p class="text-xs text-gray-400 mt-1">تعديل وتحديث البيانات البنكية الخاصة بك</p>
        </div>
        
        <form action="{{ route('lawyer.settings.updateFinancial') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                
                {{-- اسم صاحب الحساب البنكي --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-[#334155]">اسم صاحب الحساب البنكي</label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                            <i class="fas fa-user-circle text-sm"></i>
                        </div>
                        <input type="text" name="account_name" value="{{ auth()->user()->lawyerProfile->account_name ?? '' }}" placeholder="الاسم الكامل" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333]">
                    </div>
                </div>

                {{-- رقم الحساب البنكي --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-[#334155]">رقم الحساب البنكي</label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                            <i class="fas fa-hashtag text-sm"></i>
                        </div>
                        <input type="text" name="account_number" value="{{ auth()->user()->lawyerProfile->account_number ?? '' }}" placeholder="مثال: 123456789" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333]" required>
                    </div>
                </div>

                {{-- اسم البنك --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-[#334155]">اسم البنك</label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                            <i class="fas fa-university text-sm"></i>
                        </div>
                        <input type="text" name="bank_name" value="{{ auth()->user()->lawyerProfile->bank_name ?? '' }}" placeholder="مثال: مصرف الجمهورية" class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333]" required>
                    </div>
                </div>

                {{-- رقم الحساب الدولي (IBAN) --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-[#334155]">رقم الحساب الدولي (IBAN)</label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#4D96C1]">
                            <i class="fas fa-globe text-sm"></i>
                        </div>
                        <input type="text" name="iban" value="{{ auth()->user()->lawyerProfile->iban ?? '' }}" placeholder="مثال: LY00000000..." class="w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl focus:border-[#4D96C1] focus:ring-4 focus:ring-[#4D96C1]/10 transition-all outline-none text-sm text-[#0E2333]">
                    </div>
                </div>
            </div>

            {{-- زر الحفظ --}}
            <div class="flex justify-end border-t border-gray-50 pt-4">
                <button type="submit" class="px-8 py-3 bg-[#1A3B53] text-white rounded-xl font-semibold shadow-md flex items-center gap-2 transition-all hover:bg-[#2D4A6E]">
                    <i class="fas fa-save text-sm"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

{{-- تبويب أوقات العمل بتصميم متطابق ومحدث --}}
<div id="tab-working-hours" class="settings-content hidden">
    <div class="bg-gradient-to-br from-[#F5F9FC] via-[#FAFCFD] to-white rounded-2xl shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden border border-[#4D96C1]/20">
        
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#EBF3F8] to-white/70">
            <h3 class="text-[#0E2333] text-lg font-bold flex items-center gap-2">
                <i class="fas fa-clock text-[#4D96C1] text-xl"></i>
                أوقات العمل الرسمية
            </h3>
            <p class="text-xs text-gray-400 mt-1">حدد أيام وساعات العمل المتاحة، يوم الجمعة عطلة أسبوعية</p>
        </div>

        <form action="{{ route('lawyer.settings.updateWorkingHours') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php 
                    // إعادة ترتيب الأيام لتبدأ من السبت
                    $daysOrder = [6 => 'السبت', 0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة'];
                    $workingHours = auth()->user()->lawyerWorkingHours ?? collect();
                @endphp
                
                @foreach($daysOrder as $index => $day)
                    @php
                        $dayData = $workingHours->firstWhere('day_of_week', $index);
                        $startTime = ($dayData && $dayData->start_time) ? \Carbon\Carbon::parse($dayData->start_time)->format('H:i') : '';
                        $endTime = ($dayData && $dayData->end_time) ? \Carbon\Carbon::parse($dayData->end_time)->format('H:i') : '';
                    @endphp

                    @if($index == 5)
    {{-- تنسيق الجمعة (عطلة رسمية بهوية رمادية واضحة) --}}
    <div class="md:col-span-2 p-4 border border-gray-100 rounded-xl bg-gray-50 shadow-sm flex items-center justify-between transition-all">
        <div class="flex items-center gap-3">
            {{-- أيقونة الهلال --}}
            <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-150 text-slate-400">
                <i class="fas fa-moon"></i>
            </div>
            <div>
                <div class="font-bold text-sm text-slate-600">{{ $day }}</div>
                <div class="text-[10px] text-gray-400">  </div>
            </div>
        </div>
        {{-- شارة العطلة باللون الرمادي الواضح --}}
        <span class="text-xs font-bold text-slate-600 bg-slate-200/50 px-4 py-2 rounded-full border border-slate-300/50">
            عطلة رسمية
        </span>
    </div>
@else
                        {{-- تنسيق الأيام العادية --}}
                        <div class="p-4 border border-gray-100 rounded-xl bg-white shadow-sm hover:border-[#4D96C1]/30 transition-all">
                            <div class="font-bold text-sm text-[#334155] mb-3">{{ $day }}</div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <label class="flex items-center gap-2 text-xs cursor-pointer ml-2">
                                    <input type="checkbox" name="days[{{ $index }}][active]" value="1" {{ ($dayData && $dayData->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-[#4D96C1] focus:ring-[#4D96C1]">
                                    <span class="text-gray-600">متاح</span>
                                </label>
                                
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-gray-400 font-bold">من</span>
                                    <input type="time" name="days[{{ $index }}][start]" value="{{ $startTime }}" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 outline-none focus:border-[#4D96C1] w-[90px]">
                                    
                                    <span class="text-[10px] text-gray-400 font-bold">إلى</span>
                                    <input type="time" name="days[{{ $index }}][end]" value="{{ $endTime }}" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 outline-none focus:border-[#4D96C1] w-[90px]">
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
                <button type="submit" class="px-8 py-3 bg-[#1A3B53] text-white rounded-xl font-semibold shadow-md flex items-center gap-2 transition-all duration-300 hover:bg-[#2D4A6E] hover:-translate-y-0.5 active:translate-y-0 shadow-slate-200">
                    <i class="fas fa-save text-sm"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

{{-- تبويب الإشعارات --}}
<div id="tab-notifications" class="settings-content hidden">
    <div class="bg-gradient-to-br from-[#F5F9FC] via-[#FAFCFD] to-white rounded-2xl shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden border border-[#4D96C1]/20">
        
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#EBF3F8] to-white/70">
            <h3 class="text-[#0E2333] text-lg font-bold flex items-center gap-2">
                <i class="fas fa-bell text-[#4D96C1] text-xl"></i>
                 الإشعارات
            </h3>
            <p class="text-xs text-gray-400 mt-1">تحكم في التنبيهات التي ترغب باستلامها</p>
        </div>

        <form action="{{ route('lawyer.settings.updateNotifications') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @php
                    $notifications = [
                        ['name' => 'app_notifications', 'label' => 'إشعارات التطبيق', 'desc' => 'التحكم في فتح او إغلاق  كل الإشعارات '],
                        ['name' => 'new_consultation', 'label' => 'طلبات الاستشارات ', 'desc' => ' إشعار عند حجز عميل لإستشارات جديدة'],
                        ['name' => 'new_bookings', 'label' => 'طلبات الحجوزات ', 'desc' => 'إشعار عند طلب حجز عميل موعد جديد'],
                        ['name' => 'new_messages', 'label' => 'رسائل المحادثات', 'desc' => 'إشعار عند استلام رسالة نصية جديدة من عميل']
                    ];
                @endphp

                @foreach($notifications as $notif)
                    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl bg-white shadow-sm hover:border-[#4D96C1]/20 transition-all">
                        <div>
                            <div class="font-bold text-sm text-[#334155]">{{ $notif['label'] }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $notif['desc'] }}</div>
                        </div>
                        
                        {{-- تم إضافة الـ for والـ id للربط الصحيح بين الـ label والـ checkbox --}}
                        <label for="{{ $notif['name'] }}" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   id="{{ $notif['name'] }}" 
                                   name="{{ $notif['name'] }}" 
                                   value="1" 
                                   class="sr-only peer" 
                                   {{ (auth()->user()->notification_settings[$notif['name']] ?? true) ? 'checked' : '' }}>
                            
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:outline-none 
                                        peer-checked:after:translate-x-full peer-checked:bg-[#4D96C1] 
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                        after:bg-white after:border-gray-300 after:border after:rounded-full 
                                        after:h-5 after:w-5 after:transition-all">
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end border-t border-gray-50 pt-4">
                <button type="submit" class="px-8 py-3 bg-[#1A3B53] text-white rounded-xl font-semibold shadow-md flex items-center gap-2 transition-all duration-300 hover:bg-[#2D4A6E] hover:-translate-y-0.5 active:translate-y-0 shadow-slate-200">
                    <i class="fas fa-save text-sm"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

{{-- نافذة تأكيد حذف الحساب --}}
<div id="deleteAccountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] {{ $errors->has('delete_password') ? 'flex' : 'hidden' }} items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-0 mx-auto shadow-2xl overflow-hidden transform transition-all">
        
        {{-- رأس النافذة الجديد --}}
        <div class="p-6 pb-4 bg-red-50 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">حذف الحساب نهائياً</h3>
                    <p class="text-xs text-red-600/70 mt-0.5">سيتم حذف بياناتك كمحامي. لا يمكن التراجع عن هذا الإجراء.</p>
                </div>
            </div>
        </div>
        
        <form action="{{ route('lawyer.settings.deleteAccount') }}" method="POST" class="p-6">
            @csrf
            @method('DELETE')
            
            <div class="mb-5">
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 mb-5">
                    <i class="fas fa-info-circle text-amber-500 mt-0.5 text-sm"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        سيتم حذف حسابك وجميع بياناتك بشكل دائم، بما في ذلك الاستشارات والمحادثات والمستندات. لا يمكن استرجاع هذه البيانات بعد الحذف.
                    </p>
                </div>
            
                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">أدخل كلمة المرور لتأكيد الحذف</label>
                    <input type="password" name="delete_password" 
                           class="w-full p-3 border {{ $errors->has('delete_password') ? 'border-red-500 ring-1 ring-red-500' : 'border-slate-200' }} rounded-xl outline-none focus:ring-2 focus:ring-red-500/20" 
                           placeholder="كلمة المرور الحالية" required>
                    
                    {{-- رسالة الخطأ بنفس تصميم الكلاينت --}}
                    @if ($errors->has('delete_password'))
                        <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('delete_password') }}
                        </p>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="document.getElementById('deleteAccountModal').classList.add('hidden'); document.getElementById('deleteAccountModal').classList.remove('flex');" 
                            class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-200 transition-all">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="flex-1 py-3 px-4 bg-red-500 text-white rounded-xl font-semibold text-sm hover:bg-red-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-500/20">
                        <i class="fas fa-trash-alt text-xs"></i> تأكيد الحذف
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".settings-tab");
    const contents = document.querySelectorAll(".settings-content");

    // --- 1. إدارة التبويبات ---
    function activateTab(tabName) {
        // إذا لم يتم تمرير اسم تبويب، نفترض 'profile'
        let targetTabName = tabName || 'profile';
        const targetContent = document.getElementById(`tab-${targetTabName}`);
        
        // حماية: إذا كان التبويب المطلوب غير موجود، نعود لـ 'profile'
        if (!targetContent) { targetTabName = 'profile'; }
        
        tabs.forEach(btn => {
            if(btn.getAttribute("data-tab") === targetTabName) {
                // تفعيل التبويب النشط
                btn.classList.add("text-blue-600", "bg-blue-50", "rounded-l-none", "border-r-4", "border-blue-500");
                btn.classList.remove("text-slate-600", "hover:bg-slate-50", "border-transparent");
                
                if(!btn.querySelector('.fa-chevron-left')) {
                    const chevron = document.createElement('i');
                    chevron.className = 'fas fa-chevron-left mr-auto text-[10px] opacity-60 text-blue-600';
                    btn.appendChild(chevron);
                }
            } else {
                // إلغاء تفعيل التبويبات الأخرى
                btn.classList.remove("text-blue-600", "bg-blue-50", "rounded-l-none", "border-r-4", "border-blue-500");
                btn.classList.add("text-slate-600", "hover:bg-slate-50", "border-transparent");
                const existingChevron = btn.querySelector('.fa-chevron-left');
                if (existingChevron) existingChevron.remove();
            }
        });

        contents.forEach(content => {
            content.classList.toggle("hidden", content.getAttribute("id") !== `tab-${targetTabName}`);
        });
    }

    // --- التعديل هنا لضمان الأولوية ---
    // نقوم بجلب التبويب المحفوظ، لكننا نعطي الأولوية لـ 'profile' إذا كان هذا أول دخول
    const savedTab = localStorage.getItem("activeLawyerTab");
    
    // إذا كنت تريد أن يُفتح دائماً على "الملف المهني" عند كل تحديث للصفحة، اجعلها هكذا:
    activateTab('profile'); 
    
    // إذا كنت تريد أن يتذكر المتصفح التبويب الذي تركه المستخدم، استخدم هذا السطر بدلاً من السطر السابق:
    // activateTab(savedTab || 'profile');

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            const tabName = this.getAttribute("data-tab");
            localStorage.setItem("activeLawyerTab", tabName);
            activateTab(tabName);
        });
    });

    // --- 2. إدارة مفاتيح التبديل (Toggles) ---
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log(this.name + ' state: ' + this.checked);
        });
    });
});

// سكربت إخفاء الرسائل
document.addEventListener("DOMContentLoaded", function() {
    const alerts = document.querySelectorAll('.alert-message');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.classList.remove('animate-pulse');
                alert.style.transition = "opacity 0.5s ease, margin-bottom 0.5s ease";
                alert.style.opacity = "0";
                alert.style.marginBottom = "0"; 
                alert.style.paddingTop = "0";
                alert.style.paddingBottom = "0";
                alert.style.border = "none";
                alert.style.height = "0";
                alert.style.overflow = "hidden";
                setTimeout(() => { alert.remove(); }, 500);
            });
        }, 5000); 
    }
});
</script>
@endsection