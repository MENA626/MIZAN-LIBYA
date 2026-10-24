{{-- resources/views/shared/smart-vault.blade.php --}}
  @php
    $layout = 'layouts.app'; 
    if (Request::is('lawyer*')) {
        $layout = 'layouts.lawyer'; 
    } elseif (Request::is('admin*')) {
        $layout = 'layouts.admin'; 
    }
@endphp

@extends($layout)

@section('title', 'الخزنة الرقمية الذكية | ميزان')

@if(Request::is('lawyer*') || Request::is('admin*'))
    @section('page_title', 'الخزنة الرقمية الذكية')
    @section('page_subtitle', 'إدارة وأرشفة المستندات القانونية بالذكاء الاصطناعي')
@endif

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .animate-fade-in { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .ai-gradient-text { background: linear-gradient(90deg, #0f6afd, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

    .toast-notification {
        position: fixed; 
        top: 30px; 
        left: 50%; 
        transform: translateX(-50%) translateY(-100px); 
        background: linear-gradient(135deg, #0f6afd, #0f172a); 
        color: white; 
        padding: 14px 28px;
        border-radius: 60px; 
        font-weight: bold; 
        font-size: 14px; 
        z-index: 9999; 
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        box-shadow: 0 10px 30px rgba(15, 106, 253, 0.3); 
        direction: rtl;
    }
    .toast-notification.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast-notification.error { background: linear-gradient(135deg, #dc2626, #991b1b); box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3); }
    .toast-notification.success { background: linear-gradient(135deg, #059669, #064e3b); box-shadow: 0 10px 30px rgba(5, 150, 105, 0.3); }

    /* تأثيرات المجلد الحقيقي */
    .folder-back { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: inset 0 2px 10px rgba(0,0,0,0.02); }
    .folder-paper { transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); border-top: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; }
    .folder-front { transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); transform-origin: bottom right; box-shadow: 0 -2px 10px rgba(0,0,0,0.03); background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; }
    .group:hover .folder-front { transform: rotate(-5deg) translateY(3px); box-shadow: 0 -5px 15px rgba(15, 106, 253, 0.08); border-color: #cbd5e1; }
    .group:hover .folder-paper-1 { transform: translateY(-14px) rotate(2deg); }
    .group:hover .folder-paper-2 { transform: translateY(-7px) rotate(-1.5deg); }
    
    /* خلفية المربعات الباهتة لصفحة الملخصات */
    .bg-grid-white {
        background-color: #ffffff;
        background-image: linear-gradient(#f1f5f9 1px, transparent 1px), linear-gradient(90deg, #f1f5f9 1px, transparent 1px);
        background-size: 32px 32px;
    }

    /* خلفية الدفتر المخطط لصفحة التنبيهات */
    .bg-lined-paper {
        background-color: #f8fafc;
        background-image: repeating-linear-gradient(transparent, transparent 39px, #e2e8f0 39px, #e2e8f0 40px);
    }
</style>

<div class="{{ (Request::is('lawyer*') || Request::is('admin*')) ? 'h-[calc(100vh-170px)] lg:h-[calc(100vh-140px)]' : 'h-[calc(100vh-60px)] lg:h-screen bg-slate-50' }} text-slate-800 pb-20 lg:pb-4 overflow-hidden flex flex-col lg:flex-row gap-3 lg:gap-6 relative" dir="rtl" x-data="{ activeTab: 'library', aiPanelOpen: true }">

    {{-- شريط التنقل الجانبي الصغير للتبديل الداخلي --}}
    <aside class="fixed lg:static bottom-0 left-0 right-0 lg:w-24 bg-white border-t lg:border border-gray-200 rounded-t-[2rem] lg:rounded-[2rem] flex flex-row lg:flex-col items-center justify-around lg:justify-start py-2 lg:py-8 shadow-[0_-10px_40px_rgba(0,0,0,0.06)] lg:shadow-sm flex-shrink-0 z-[40] transition-transform duration-300">
        <nav class="flex flex-row lg:flex-col gap-2 lg:gap-8 w-full justify-around items-center relative z-40 px-2 lg:px-0">
            <a href="#" onclick="switchTab('library'); return false;" id="nav-library" class="nav-item p-3 lg:p-4 bg-blue-50/80 text-[#0f6afd] rounded-2xl shadow-sm relative group transition-all flex flex-col items-center gap-1.5 flex-1 lg:flex-none">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span class="text-[10px] lg:text-xs font-bold">المكتبة</span>
            </a>
            <a href="#" onclick="switchTab('summaries'); return false;" id="nav-summaries" class="nav-item p-3 lg:p-4 text-slate-400 hover:text-[#0f6afd] hover:bg-blue-50/50 rounded-2xl transition-all relative group flex flex-col items-center gap-1.5 flex-1 lg:flex-none">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-[10px] lg:text-xs font-bold">الملخصات</span>
            </a>
            <a href="#" onclick="switchTab('notifications'); return false;" id="nav-notifications" class="nav-item p-3 lg:p-4 text-slate-400 hover:text-[#0f172a] hover:bg-slate-100 rounded-2xl transition-all relative group flex flex-col items-center gap-1.5 flex-1 lg:flex-none">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-2 right-[30%] lg:right-3 lg:top-3 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full animate-pulse"></span>
                <span class="text-[10px] lg:text-xs font-bold">المهام</span>
            </a>
        </nav>
    </aside>

    {{-- واجهة المكتبة --}}
    <div id="view-library" class="flex-1 flex flex-col lg:flex-row gap-4 sm:gap-6 overflow-hidden transition-opacity duration-300 w-full h-full pb-4 sm:pb-0">
        <main class="flex-1 flex flex-col overflow-hidden w-full relative">
            
            {{-- بانر الترحيب --}}
            <div id="main-hero-banner" class="bg-[#0f172a] rounded-[1.5rem] lg:rounded-[2rem] p-6 lg:p-10 mt-1 lg:mt-2 mb-3 lg:mb-6 relative overflow-hidden shadow-xl flex-shrink-0 flex items-center justify-between transition-all duration-300">
                <div class="absolute inset-0 z-0 opacity-10" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
                
                <div class="relative z-10 w-full lg:w-3/5 lg:pl-6">
                    <span class="text-[10px] lg:text-xs font-black tracking-widest uppercase mb-2 block text-[#0f6afd] bg-[#0f6afd]/10 w-max px-3 py-1 rounded-md">Smart Legal Vault</span>
                    <h1 class="text-2xl lg:text-4xl font-black mb-3 leading-snug text-white">الخزنة الرقمية الذكية</h1>
                    <p class="hidden lg:block text-slate-400 font-medium text-sm lg:text-base mt-2 leading-relaxed max-w-md">نظم مستنداتك، حلل العقود واللوائح بالذكاء الاصطناعي، وتابع المهام والمواعيد الهامة بكل سهولة وسرية.</p>
                    
                    <button onclick="toggleModal('addFolderModal')" class="mt-6 bg-white text-[#0f172a] hover:bg-[#0f6afd] hover:text-white hover:shadow-lg hover:shadow-[#0f6afd]/30 px-6 py-3 rounded-xl font-bold flex items-center justify-center lg:justify-start gap-3 transition-all duration-300 w-full lg:w-max group">
                        <span>إنشاء مجلد جديد</span>
                        <span class="bg-slate-100 group-hover:bg-white/20 text-[#0f172a] group-hover:text-white rounded-lg p-1.5 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg></span>
                    </button>
                </div>

                <div class="hidden lg:block relative z-10 w-2/5 h-full min-h-[180px]">
                    <div class="absolute top-2 left-6 w-48 h-56 bg-slate-50 rounded-2xl shadow-lg transform -rotate-6 opacity-80 border border-slate-200 p-5 transition-transform hover:-translate-y-2">
                        <div class="w-1/3 h-2 bg-slate-200 rounded-full mb-4"></div>
                        <div class="w-full h-1.5 bg-slate-200 rounded-full mb-2"></div>
                        <div class="w-5/6 h-1.5 bg-slate-200 rounded-full mb-2"></div>
                        <div class="w-4/5 h-1.5 bg-slate-200 rounded-full mb-4"></div>
                    </div>
                    <div class="absolute top-6 left-16 w-56 h-64 bg-white rounded-2xl shadow-2xl transform rotate-3 border border-slate-100 p-6 flex flex-col justify-between transition-transform hover:-translate-y-2">
                        <div>
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-8 h-8 bg-[#0f6afd] rounded-lg flex items-center justify-center text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="w-24 h-3 bg-slate-100 rounded-full"></div>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full mb-3"></div>
                            <div class="w-4/5 h-2 bg-slate-100 rounded-full mb-3"></div>
                            <div class="w-full h-2 bg-slate-100 rounded-full mb-3"></div>
                            <div class="w-2/3 h-2 bg-slate-100 rounded-full"></div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl mt-auto border border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="w-10 h-1.5 bg-slate-300 rounded-full mb-2"></div>
                                <div class="text-[10px] font-black text-slate-800">عقد قانوني</div>
                            </div>
                            <div class="w-6 h-6 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 bg-white border border-gray-200 rounded-[1.5rem] lg:rounded-[2rem] p-4 lg:p-8 overflow-y-auto shadow-sm custom-scrollbar flex flex-col h-full min-h-[300px]">
                
                {{-- شبكة المجلدات --}}
                <div id="folders-grid" class="transition-all duration-300 h-full flex flex-col">
                    <div class="flex justify-between items-center mb-6 lg:mb-8 border-b border-gray-100 pb-3 lg:pb-4">
                        <h2 class="text-lg lg:text-2xl font-black text-[#0f172a]">ملفات القضايا والمستندات</h2>
                        @if(Request::is('lawyer*') || Request::is('admin*'))
                        <button @click="aiPanelOpen = !aiPanelOpen" class="hidden lg:flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-xl border border-slate-200 hover:border-blue-500 hover:text-blue-600 transition-all bg-slate-50 cursor-pointer shadow-sm">
                            <i class="fas fa-robot"></i>
                            <span x-text="aiPanelOpen ? 'إخفاء المحلل الذكي' : 'إظهار المحلل الذكي'"></span>
                        </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 lg:gap-8 pt-2">
                        @forelse($folders as $folder)
                        <div class="relative group cursor-pointer w-full h-[150px] lg:h-[180px]" onclick="openFolderDetails({{ $folder->id }}, '{{ addslashes($folder->name) }}')">
                            <div class="absolute bottom-0 right-0 w-full h-[90%] bg-blue-100/90 rounded-[1.2rem] rounded-tr-none folder-back group-hover:bg-[#0f6afd]/20 border border-blue-200/50">
                                <div class="absolute -top-4 right-0 w-[45%] h-5 bg-blue-100/90 rounded-t-xl folder-back group-hover:bg-[#0f6afd]/20 border-t border-l border-r border-blue-200/50"></div>
                            </div>
                            <div class="absolute bottom-2 left-3 right-4 h-[85%] bg-white rounded-t-xl folder-paper folder-paper-1 shadow-[0_-2px_5px_rgba(0,0,0,0.02)]"></div>
                            <div class="absolute bottom-2 left-4 right-3 h-[85%] bg-slate-50 rounded-t-xl folder-paper folder-paper-2 shadow-[0_-2px_5px_rgba(0,0,0,0.02)]"></div>
                            
                            <div class="absolute bottom-0 left-0 w-full h-[85%] folder-front rounded-[1.2rem] p-3 lg:p-4 flex flex-col justify-between z-10 overflow-hidden">
                                <div class="absolute top-0 right-0 w-12 h-12 bg-gradient-to-bl from-blue-50 to-transparent opacity-60 rounded-bl-[2rem]"></div>
                                
                                <div class="flex justify-between items-start z-10 relative">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-[#0f6afd]/10 text-[#0f6afd] rounded-xl flex items-center justify-center transition-colors duration-300 border border-[#0f6afd]/20">
                                        <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                    </div>
                                    <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="event.stopPropagation(); editFolder({{ $folder->id }}, '{{ addslashes($folder->name) }}')" class="p-1.5 text-slate-400 hover:text-[#0f6afd] bg-white border border-slate-100 rounded-md shadow-sm transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        <button type="button" onclick="event.stopPropagation(); openDeleteConfirm('{{ route('folders.destroy', $folder->id) }}')" class="p-1.5 text-slate-400 hover:text-red-500 bg-white border border-slate-100 rounded-md shadow-sm transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </div>
                                </div>
                                <div class="z-10 relative mt-2">
                                    <h3 class="text-[#0f172a] font-bold text-sm lg:text-base mb-0.5 truncate">{{ $folder->name }}</h3>
                                    <p class="text-slate-500 text-[10px] lg:text-xs font-bold">{{ $folder->documents->count() }} ملف</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full w-full h-full flex flex-col items-center justify-center text-slate-400 font-bold text-lg py-16">
                            <svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg>
                            لا توجد مجلدات حالياً، ابدأ بإنشاء مجلد جديد!
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- عرض المجلد الواحد --}}
                <div id="single-folder-view" class="hidden flex-col h-full animate-fade-in">
                    <div class="flex justify-between items-center mb-4 lg:mb-6 border-b border-gray-100 pb-3 lg:pb-4 sticky top-0 bg-white z-20">
                        <div class="flex items-center gap-3 lg:gap-4">
                            <button onclick="backToFolders()" class="p-2 bg-slate-50 border border-gray-100 rounded-xl shadow-sm hover:bg-[#0f172a] hover:text-white text-slate-600 transition-colors"><svg class="w-5 h-5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                            <h2 id="current-folder-title" class="text-xl lg:text-2xl font-black text-[#0f172a]">اسم المجلد</h2>
                        </div>
                        @if(Request::is('lawyer*') || Request::is('admin*'))
                        <button @click="aiPanelOpen = !aiPanelOpen" class="hidden lg:flex items-center gap-2 text-xs font-bold px-4 py-2 rounded-xl border border-slate-200 hover:border-blue-500 hover:text-blue-600 transition-all bg-slate-50 cursor-pointer shadow-sm">
                            <i class="fas fa-robot"></i>
                            <span x-text="aiPanelOpen ? 'إخفاء المحلل الذكي' : 'إظهار المحلل الذكي'"></span>
                        </button>
                        @endif
                    </div>

                    {{-- فورم إضافة المستند --}}
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl mb-6 shadow-sm border border-slate-100 relative p-4 lg:p-6 flex-shrink-0">
                        @csrf
                        <input type="hidden" name="folder_id" id="active-folder-id">
                        
                        <div class="border-2 border-dashed border-slate-300 rounded-[1.5rem] p-6 lg:p-8 flex flex-col items-center justify-center text-center hover:border-[#0f6afd] hover:bg-blue-50/30 transition-all duration-300 cursor-pointer group relative overflow-hidden" onclick="document.getElementById('file-upload').click()">
                            <div class="relative w-14 h-14 lg:w-16 lg:h-16 mb-4 group-hover:scale-110 transition-transform duration-300">
                                <div class="absolute inset-0 bg-[#0f6afd]/20 rounded-2xl transform -rotate-6 shadow-sm"></div>
                                <div class="absolute inset-0 bg-[#0f172a]/10 rounded-2xl transform rotate-3 shadow-sm"></div>
                                <div class="absolute inset-0 bg-[#0f6afd] rounded-2xl shadow-md flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                            </div>
                            <h3 class="text-sm lg:text-base font-black text-[#0f172a] mb-1">اسحب وأفلت الملف هنا</h3>
                            <p class="text-xs text-[#0f6afd] font-bold underline mb-4">أو اختر ملفاً من جهازك</p>
                            
                            <div class="flex gap-3 justify-center mb-4 relative z-20" onclick="event.stopPropagation()">
                                <button type="button" onclick="document.getElementById('camera-scan').click()" class="bg-slate-100 hover:bg-slate-200 text-[#0f172a] px-4 py-2 rounded-lg text-xs font-bold transition-colors flex items-center gap-2 border border-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    مسح ضوئي (سكانر)
                                </button>
                            </div>

                            <div class="flex w-full justify-center gap-4 text-[10px] lg:text-xs text-slate-400 font-bold">
                                <span>صيغ: pdf, docx, jpg, png</span>
                                <span>حد أقصى: 100MB</span>
                            </div>
                        </div>
                        
                        <input type="file" id="file-upload" name="document" class="hidden" onchange="handleFileSelect(this)">
                        <input type="file" id="camera-scan" name="document" accept="image/*" capture="environment" class="hidden" onchange="handleFileSelect(this)">
                        
                        <div id="file-upload-preview" class="hidden mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 text-[#0f6afd] px-3 py-1.5 rounded-lg text-xs font-bold">مُرفق</div>
                                <div class="text-sm font-bold text-[#0f172a]" id="file-name-display">filename.pdf</div>
                            </div>
                            <button type="button" onclick="clearFileSelection(event)" class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>

                        <div class="mt-4 flex gap-3 hidden" id="form-actions">
                            <button type="submit" class="bg-[#0f6afd] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-md hover:bg-blue-700 transition-colors">حفظ المستند</button>
                            <button type="button" onclick="clearFileSelection(event)" class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-xl text-sm font-bold hover:bg-slate-50 transition-colors">إلغاء</button>
                        </div>
                    </form>

                    {{-- شبكة المستندات --}}
                    <div id="folder-documents-list" class="flex-1 overflow-y-auto custom-scrollbar"></div>
                </div>
            </div>
        </main>

        {{-- واجهة محلل الذكاء الاصطناعي --}}
        <aside x-show="aiPanelOpen"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform translate-x-8"
               x-transition:enter-end="opacity-100 transform translate-x-0"
               x-transition:leave="transition ease-in duration-250"
               x-transition:leave-start="opacity-100 transform translate-x-0"
               x-transition:leave-end="opacity-0 transform translate-x-8"
               class="w-full {{ (Request::is('lawyer*') || Request::is('admin*')) ? 'lg:w-[26rem] xl:w-[28rem] 2xl:w-[32rem]' : 'lg:w-[38rem] xl:w-[42rem]' }} bg-gradient-to-b from-white to-slate-50 border border-slate-200 rounded-[1.5rem] lg:rounded-[2rem] p-4 lg:p-6 flex flex-col shadow-lg flex-shrink-0 lg:max-h-full mt-4 lg:mt-0 relative overflow-hidden">
            <header class="flex items-center justify-between mb-4 lg:mb-6 relative z-10">
                <h2 class="text-lg lg:text-xl font-black text-[#0f172a] flex items-center gap-3">
                    <span class="p-2.5 bg-[#0f6afd]/10 text-[#0f6afd] rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg></span>
                    <span class="ai-gradient-text">المحلل الذكي Mizan AI</span>
                </h2>
            </header>

            <input type="file" id="aiDocumentInput" class="hidden" accept=".pdf,image/png,image/jpeg,image/jpg" onchange="processAiDocument(this.files[0])">
            <div onclick="document.getElementById('aiDocumentInput').click()" class="border-2 border-dashed border-slate-300 rounded-2xl p-6 lg:p-8 text-center hover:border-[#0f6afd] hover:bg-blue-50/50 transition-all duration-300 mb-5 cursor-pointer group z-10 bg-white">
                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full mx-auto flex items-center justify-center text-[#0f6afd] group-hover:scale-110 group-hover:bg-[#0f6afd] group-hover:text-white transition-all duration-300 mb-4 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <p class="text-base font-black text-[#0f172a]">اسحب المستند القانوني هنا للتحليل</p>
                <p class="text-xs text-slate-400 mt-2 font-bold">يدعم ملفات PDF والصور لاستخراج أهم النقاط</p>
            </div>

            <div class="flex-1 min-h-[250px] bg-white border border-slate-100 rounded-2xl p-5 flex flex-col overflow-hidden relative shadow-inner z-10">
                <h3 class="text-sm font-black text-[#0f172a] mb-4 border-b border-gray-100 pb-3 flex items-center gap-2" id="aiResultTitle">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></div> النتيجة التحليلية
                </h3>
                <div id="aiSummaryContent" class="overflow-y-auto custom-scrollbar pr-2 flex-1">
                    <div class="flex flex-col items-center justify-center h-full opacity-50">
                        <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm text-slate-500 font-bold text-center">ارفع المستند ليقوم الذكاء الاصطناعي باستخراج الزبدة وهيكلتها.</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    {{-- واجهة الملخصات المحفوظة --}}
    <main id="view-summaries" class="flex-1 hidden flex-col overflow-hidden border border-gray-200 rounded-[1.5rem] lg:rounded-[2rem] shadow-sm transition-opacity duration-300 w-full h-full relative">
        <div class="absolute inset-0 bg-grid-white z-0 pointer-events-none"></div>
        <header class="p-6 lg:p-8 pb-4 z-10 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm flex items-center justify-between relative">
            <h1 class="text-xl lg:text-3xl font-black text-[#0f172a] flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 text-[#0f6afd] rounded-xl flex items-center justify-center"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                سجل الملخصات الذكية
            </h1>
        </header>
        <div class="flex-1 p-6 lg:p-8 overflow-y-auto custom-scrollbar relative z-10">
            <div id="saved-summaries-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div onclick="switchTab('library')" class="bg-white/80 backdrop-blur-sm border-2 border-dashed border-slate-300 rounded-[1.5rem] p-6 min-h-[300px] flex flex-col items-center justify-center text-slate-400 hover:text-[#0f6afd] hover:border-[#0f6afd] transition-all cursor-pointer group relative overflow-hidden">
                    <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-[#0f6afd] opacity-[0.10] blur-3xl rounded-full pointer-events-none transition-transform group-hover:scale-125"></div>
                    <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-50 transition-colors z-10"><span class="text-3xl font-light">+</span></div>
                    <span class="font-bold text-sm z-10">تحليل مستند جديد</span>
                </div>
                
                @foreach($summaries ?? [] as $summary)
                    <div class="bg-white rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 hover:shadow-xl transition-all duration-300 flex flex-col h-[300px] group relative overflow-hidden">
                        <div class="absolute -bottom-10 -right-10 w-56 h-56 bg-[#0f6afd] opacity-[0.25] blur-[40px] rounded-full pointer-events-none transition-transform duration-500 group-hover:scale-110"></div>
                        
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div class="w-12 h-12 rounded-2xl border border-blue-100 flex items-center justify-center text-[#0f6afd] bg-blue-50 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            
                            <div class="flex gap-2">
                                <button onclick="shareItem('{{ $summary->document_name }}', 'إليك ملخص المستند القانوني', '{{ url('/mizan/summaries/'.$summary->id) }}')" class="p-2 text-slate-400 hover:text-emerald-500 transition-colors bg-slate-50 rounded-lg hover:bg-emerald-50 border border-slate-100" title="مشاركة">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                                </button>
                                <button onclick="event.stopPropagation(); openDeleteConfirm('{{ route('summaries.destroy', $summary->id) }}')" class="p-2 text-slate-400 hover:text-red-500 transition-colors bg-slate-50 rounded-lg hover:bg-red-50 border border-slate-100" title="حذف الملخص">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>

                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-2 relative z-10">ملخص ذكي</span>
                        <h3 class="font-black text-xl text-[#0f172a] mb-2 line-clamp-2 leading-snug relative z-10">{{ $summary->document_name }}</h3>
                        <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed flex-1 relative z-10 font-medium">{{ Str::limit(strip_tags($summary->content), 80) }}</p>
                        
                        <div class="mt-auto pt-4 relative z-10">
                            <button onclick="showFullSummary({{ $summary->id }})" class="w-full py-2.5 bg-white border border-slate-200 hover:border-[#0f6afd] hover:text-[#0f6afd] hover:bg-blue-50/50 text-[#0f172a] font-bold rounded-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm shadow-sm hover:shadow">
                                عرض الملخص
                                <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    {{-- واجهة التنبيهات --}}
    <main id="view-notifications" class="flex-1 hidden flex-col overflow-hidden border border-gray-200 rounded-[1.5rem] lg:rounded-[2rem] shadow-sm transition-opacity duration-300 w-full h-full relative bg-lined-paper">
        <header class="p-6 lg:p-8 pb-4 z-10 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm flex items-center justify-between relative">
            <h1 class="text-xl lg:text-3xl font-black text-[#0f172a] flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center border border-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                سجل الملاحظات والمهام
            </h1>
            <div class="hidden sm:flex gap-4 text-xs font-bold bg-white border border-slate-200 px-5 py-3 rounded-xl shadow-sm">
                <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-emerald-500"></div> آمن (أكثر من 7 أيام)</span>
                <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-orange-500"></div> اقترب الموعد</span>
                <span class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-rose-500"></div> منتهي / اليوم</span>
            </div>
        </header>
        <div class="flex-1 p-6 lg:p-12 overflow-y-auto custom-scrollbar relative z-10">
            <div id="dynamic-notifications-grid" class="flex flex-wrap justify-center gap-8 lg:gap-12 relative z-10 pt-4 pb-12"></div>
        </div>
    </main>
</div>

{{-- منبثقة الإشعارات --}}
<div id="toastNotification" class="toast-notification"><span id="toastMessage">تم</span></div>

{{-- مودال عرض الملخص كاملاً --}}
<div id="fullSummaryModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white rounded-[1.5rem] p-6 lg:p-10 w-full max-w-[800px] max-h-[90vh] shadow-2xl flex flex-col border border-slate-100 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#0f6afd] to-[#38bdf8]"></div>
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100 mt-2">
            <h3 class="text-2xl font-black text-[#0f172a] flex items-center gap-2"><div class="w-2 h-8 bg-[#0f6afd] rounded-full"></div> الملخص الشامل</h3>
            <button onclick="toggleModal('fullSummaryModal')" class="p-2 hover:bg-slate-100 rounded-full transition bg-slate-50 text-slate-500 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="fullSummaryContent" class="overflow-y-auto custom-scrollbar pr-4 text-slate-700"></div>
    </div>
</div>

{{-- نماذج المودال (حذف، مجلدات، مستندات) --}}
<div id="customDeleteModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[1.5rem] p-8 w-[400px] shadow-2xl text-center border border-slate-100">
        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
        <h3 class="text-2xl font-black text-[#0f172a] mb-2">تأكيد الحذف</h3>
        <p class="text-slate-500 text-sm font-bold">هل أنت متأكد من رغبتك في الحذف؟ هذا الإجراء لا يمكن التراجع عنه.</p>
        <div class="flex gap-3 mt-8">
            <button onclick="toggleModal('customDeleteModal')" class="flex-1 px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors">إلغاء</button>
            <button onclick="submitCustomDelete()" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-colors shadow-md">نعم، احذف</button>
        </div>
    </div>
</div>

<div id="addFolderModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[1.5rem] p-8 w-96 shadow-2xl border border-slate-100">
        <h3 class="text-xl font-black text-[#0f172a] mb-6">إنشاء مجلد جديد</h3>
        <form action="{{ route('folders.store') }}" method="POST">
            @csrf
            <input type="text" name="name" required placeholder="أدخل اسم المجلد..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none mb-6 transition-all">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleModal('addFolderModal')" class="px-5 py-2.5 text-slate-500 hover:bg-slate-100 rounded-xl font-bold transition-colors">إلغاء</button>
                <button type="submit" class="px-5 py-2.5 bg-[#0f172a] hover:bg-[#0f6afd] text-white rounded-xl font-bold transition-colors shadow-md">حفظ والتالي</button>
            </div>
        </form>
    </div>
</div>

<div id="editFolderModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[1.5rem] p-8 w-96 shadow-2xl border border-slate-100">
        <h3 class="text-xl font-black text-[#0f172a] mb-6">تعديل اسم المجلد</h3>
        <form id="editFolderForm" method="POST">
            @csrf @method('PUT')
            <input type="text" name="name" id="editFolderName" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none mb-6 transition-all">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleModal('editFolderModal')" class="px-5 py-2.5 text-slate-500 hover:bg-slate-100 rounded-xl font-bold transition-colors">إلغاء</button>
                <button type="submit" class="px-5 py-2.5 bg-[#0f172a] hover:bg-[#0f6afd] text-white rounded-xl font-bold transition-colors shadow-md">تحديث</button>
            </div>
        </form>
    </div>
</div>

<div id="editDocumentModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-[1.5rem] p-8 w-[400px] shadow-2xl border border-slate-100">
        <h3 class="text-xl font-black text-[#0f172a] mb-6">تعديل اسم المستند</h3>
        <form id="editDocumentForm" method="POST">
            @csrf @method('PUT')
            <input type="text" name="title" id="editDocumentName" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none mb-6 transition-all">
            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleModal('editDocumentModal')" class="px-5 py-2.5 text-slate-500 hover:bg-slate-100 rounded-xl font-bold transition-colors">إلغاء</button>
                <button type="submit" class="px-5 py-2.5 bg-[#0f172a] hover:bg-[#0f6afd] text-white rounded-xl font-bold transition-colors shadow-md">تحديث</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال الخصائص --}}
<div id="documentMetadataModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity p-4">
    <div class="bg-white rounded-[1.5rem] p-6 lg:p-8 w-full max-w-[650px] max-h-[90vh] overflow-y-auto shadow-2xl custom-scrollbar border border-slate-100">
        <h3 class="text-2xl font-black text-[#0f172a] mb-8 flex items-center gap-3 border-b border-slate-100 pb-4">
            <span class="p-2.5 bg-blue-50 text-[#0f6afd] rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg></span>
            بيانات وخصائص المستند
        </h3>
        <form id="documentMetadataForm" method="POST">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم القضية / الملف</label>
                    <input type="text" name="case_number" id="metaCaseNumber" placeholder="مثال: ق/2026/150" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الجهة المختصة / المحكمة</label>
                    <input type="text" name="related_party" id="metaRelatedParty" placeholder="مثال: محكمة الاستئناف" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">تصنيف المستند</label>
                    <input type="text" name="classification" id="metaClassification" list="classificationOptions" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none transition-all">
                    <datalist id="classificationOptions"><option value="عقد"><option value="هوية/جواز"><option value="توكيل"><option value="مستند محكمة"><option value="عام"></datalist>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الأهمية</label>
                    <select name="importance" id="metaImportance" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none transition-all">
                        <option value="عالي">عالي (عاجل)</option><option value="متوسط">متوسط</option><option value="منخفض">منخفض</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-2">تاريخ الجلسة / انتهاء الصلاحية</label>
                <input type="date" name="expiry_date" id="metaExpiryDate" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none text-slate-700 transition-all">
            </div>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات إضافية</label>
                <textarea name="notes" id="metaNotes" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 font-bold focus:border-[#0f6afd] focus:ring-2 focus:ring-[#0f6afd]/20 outline-none custom-scrollbar transition-all"></textarea>
            </div>
            
            <div class="mb-8 flex items-center gap-3 bg-[#0f6afd]/5 p-4 rounded-xl border border-[#0f6afd]/20">
                <input type="checkbox" name="is_task" id="metaIsTask" value="1" class="w-5 h-5 text-[#0f6afd] rounded cursor-pointer focus:ring-[#0f6afd]">
                <label class="text-sm font-black text-[#0f172a] cursor-pointer select-none" for="metaIsTask">تثبيت كـ "مهمة/تنبيه" في لوحة المهام الذكية</label>
            </div>
            
            <div class="flex justify-end gap-3 pt-5 border-t border-slate-100">
                <button type="button" onclick="toggleModal('documentMetadataModal')" class="px-6 py-3 text-slate-500 hover:bg-slate-100 rounded-xl font-bold transition-colors">إلغاء</button>
                <button type="submit" class="px-8 py-3 bg-[#0f172a] text-white rounded-xl font-bold shadow-md hover:bg-[#0f6afd] transition-colors">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>

<script>
    const foldersData = @json($folders ?? []);
    let currentAiFilename = '';
    let currentAiSummary = '';

    window.onload = function() {
        let activeTab = localStorage.getItem('activeVaultTab') || 'library';
        switchTab(activeTab, false);
        let activeFolderId = localStorage.getItem('activeFolderId');
        if (activeFolderId && activeTab === 'library') {
            const folder = foldersData.find(f => f.id == activeFolderId);
            if (folder) openFolderDetails(folder.id, folder.name);
        }
        renderDynamicNotifications();
    };

    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) { modal.classList.toggle('hidden'); modal.classList.toggle('flex'); }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        document.getElementById('toastMessage').innerText = message;
        toast.className = `toast-notification ${type} show`;
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    async function shareItem(title, text, url) {
        if (navigator.share) {
            try {
                await navigator.share({ title: title, text: text, url: url });
                showToast('تمت المشاركة بنجاح!', 'success');
            } catch (err) { console.log('User cancelled or error', err); }
        } else {
            navigator.clipboard.writeText(url);
            showToast('تم نسخ الرابط للحافظة، يمكنك مشاركته الآن!', 'success');
        }
    }

    let actionUrlToDelete = '';
    function openDeleteConfirm(url) { actionUrlToDelete = url; toggleModal('customDeleteModal'); }
    
    function submitCustomDelete() {
        let form = document.createElement('form'); form.method = 'POST'; form.action = actionUrlToDelete;
        let c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}';
        let m = document.createElement('input'); m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE';
        form.appendChild(c); form.appendChild(m); document.body.appendChild(form); form.submit();
    }

    function editFolder(id, name) {
        document.getElementById('editFolderName').value = name;
        document.getElementById('editFolderForm').action = '/mizan/folders/' + id;
        toggleModal('editFolderModal');
    }

    function editDocumentName(id, title) {
        let nameWithoutExt = title.substring(0, title.lastIndexOf('.')) || title;
        document.getElementById('editDocumentName').value = nameWithoutExt;
        document.getElementById('editDocumentForm').action = '/mizan/documents/' + id + '/name';
        toggleModal('editDocumentModal');
    }

    function openDocumentMetadata(id, expiryDate, isTask, notes, classification, importance, caseNumber, relatedParty) {
        document.getElementById('metaExpiryDate').value = expiryDate && expiryDate !== 'null' ? expiryDate : '';
        document.getElementById('metaIsTask').checked = isTask === true || isTask === 'true' || isTask == 1;
        document.getElementById('metaNotes').value = notes && notes !== 'null' ? notes : '';
        document.getElementById('metaClassification').value = classification && classification !== 'null' ? classification : 'عام';
        document.getElementById('metaImportance').value = importance && importance !== 'null' ? importance : 'متوسط';
        document.getElementById('metaCaseNumber').value = caseNumber && caseNumber !== 'null' && caseNumber !== 'undefined' ? caseNumber : '';
        document.getElementById('metaRelatedParty').value = relatedParty && relatedParty !== 'null' && relatedParty !== 'undefined' ? relatedParty : '';
        
        document.getElementById('documentMetadataForm').action = '/mizan/documents/' + id + '/metadata';
        toggleModal('documentMetadataModal');
    }

    function printDocument(fileUrl) { 
        printJS({ printable: fileUrl, type: 'pdf', showModal: true, modalMessage: 'جاري التجهيز للطباعة...' }); 
    }

    function printAsScanner(fileUrl) {
        printJS({ printable: 'جاري معالجة الصورة وتحويلها لسكانر...', type: 'raw-html' });
        const img = new Image(); img.crossOrigin = "Anonymous"; img.src = fileUrl;
        img.onload = function() {
            const canvas = document.createElement('canvas'); const ctx = canvas.getContext('2d');
            canvas.width = img.width; canvas.height = img.height; ctx.drawImage(img, 0, 0);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height); const data = imageData.data;
            for (let i = 0; i < data.length; i += 4) {
                const avg = (data[i] * 0.299 + data[i+1] * 0.587 + data[i+2] * 0.114);
                const color = avg > 140 ? 255 : 0;
                data[i] = color; data[i+1] = color; data[i+2] = color;
            }
            ctx.putImageData(imageData, 0, 0);
            printJS({ printable: canvas.toDataURL('image/png'), type: 'image', imageStyle: 'width:100%;' });
        };
        img.onerror = function() { alert('خطأ في قراءة الصورة.'); };
    }

    function openFolderDetails(folderId, folderName) {
        localStorage.setItem('activeFolderId', folderId);
        document.getElementById('folders-grid').classList.add('hidden');
        document.getElementById('single-folder-view').classList.remove('hidden');
        document.getElementById('single-folder-view').classList.add('flex');
        document.getElementById('current-folder-title').innerText = folderName;
        document.getElementById('active-folder-id').value = folderId;
        
        const heroBanner = document.getElementById('main-hero-banner');
        if(heroBanner) { heroBanner.classList.add('hidden'); }

        const folder = foldersData.find(f => f.id == folderId);
        const container = document.getElementById('folder-documents-list');
        
        if (!folder || folder.documents.length === 0) {
            container.className = 'flex-1 flex items-center justify-center';
            container.innerHTML = '<div class="text-center text-slate-400 font-bold"><p class="text-lg">المجلد فارغ</p><p class="text-xs mt-2">اسحب المستندات أو الصور بالأعلى لإضافتها هنا</p></div>';
        } else {
            container.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 pb-4 content-start';
            container.innerHTML = '';
            
            folder.documents.forEach(doc => {
                let safeNotes = doc.notes ? doc.notes.replace(/'/g, "\\'").replace(/\n/g, "\\n") : '';
                let caseNum = doc.case_number ? doc.case_number.replace(/'/g, "\\'") : '';
                let relatedParty = doc.related_party ? doc.related_party.replace(/'/g, "\\'") : '';
                let ext = doc.title.split('.').pop().toLowerCase();
                let isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
                
                let iconHtml = isImage 
                    ? `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`
                    : `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
                
                let bgColor = isImage ? 'bg-indigo-50 text-indigo-600' : 'bg-blue-50 text-[#0f6afd]';
                let docUrl = `${window.location.origin}/mizan/documents/${doc.id}/view`;

                container.innerHTML += `
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md hover:border-[#0f6afd]/40 transition-all group flex flex-col h-[200px] relative">
                        <button type="button" onclick="openDeleteConfirm('/mizan/documents/${doc.id}')" class="absolute top-2 left-2 p-1.5 bg-red-50 text-red-500 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500 hover:text-white z-10" title="حذف المستند">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        
                        <button type="button" onclick="shareItem('${escapeHtml(doc.title)}', 'مستند قانوني من نظام ميزان', '${docUrl}')" class="absolute top-2 right-2 p-1.5 bg-slate-50 text-slate-400 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-emerald-500 hover:text-white z-10" title="مشاركة المستند">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        </button>
                        
                        <div class="flex-1 p-4 flex flex-col items-center justify-center text-center cursor-pointer" onclick="window.open('${docUrl}', '_blank')" title="عرض المستند">
                            <div class="w-14 h-14 mb-3 rounded-full flex items-center justify-center ${bgColor} group-hover:scale-110 transition-transform">
                                ${iconHtml}
                            </div>
                            <h4 class="font-bold text-[#0f172a] text-sm line-clamp-2 w-full px-2 leading-tight group-hover:text-[#0f6afd] transition-colors">${escapeHtml(doc.title)}</h4>
                            <span class="text-[10px] text-slate-400 mt-1 uppercase font-black tracking-widest bg-slate-50 px-2 py-0.5 rounded-md">${ext}</span>
                        </div>
                        
                        <div class="border-t border-slate-100 p-2.5 flex justify-center items-center gap-1 bg-slate-50 rounded-b-2xl">
                            <button onclick="editDocumentName(${doc.id}, '${escapeHtml(doc.title)}')" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-100 rounded-lg transition-colors tooltip" title="تعديل الاسم"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                            <button onclick="openDocumentMetadata(${doc.id}, '${doc.expiry_date || ''}', ${doc.is_task ? 'true' : 'false'}, '${safeNotes}', '${doc.classification || ''}', '${doc.importance || ''}', '${caseNum}', '${relatedParty}')" class="p-2 text-slate-400 hover:text-emerald-500 hover:bg-emerald-100 rounded-lg transition-colors tooltip" title="الخصائص"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg></button>
                            <button onclick="printDocument('${docUrl}')" class="p-2 text-slate-400 hover:text-[#0f6afd] hover:bg-blue-100 rounded-lg transition-colors tooltip" title="طباعة"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
                            ${isImage ? `<button onclick="printAsScanner('${docUrl}')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors tooltip" title="تحويل سكانر وطباعة"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg></button>` : ''}
                        </div>
                    </div>`;
            });
        }
    }

    function backToFolders() {
        localStorage.removeItem('activeFolderId');
        document.getElementById('single-folder-view').classList.add('hidden');
        document.getElementById('single-folder-view').classList.remove('flex');
        document.getElementById('folders-grid').classList.remove('hidden');
        
        const heroBanner = document.getElementById('main-hero-banner');
        if(heroBanner) { heroBanner.classList.remove('hidden'); }
        
        clearFileSelection();
    }

    function handleFileSelect(input) {
        if(input.files && input.files[0]) {
            document.getElementById('file-name-display').textContent = input.files[0].name;
            document.getElementById('file-upload-preview').classList.remove('hidden');
            document.getElementById('form-actions').classList.remove('hidden');
            
            if(input.id === 'file-upload') {
                document.getElementById('camera-scan').disabled = true;
                document.getElementById('file-upload').disabled = false;
            } else {
                document.getElementById('file-upload').disabled = true;
                document.getElementById('camera-scan').disabled = false;
            }
        }
    }

    function clearFileSelection(e) {
        if(e) e.preventDefault();
        document.getElementById('file-upload').value = '';
        document.getElementById('camera-scan').value = '';
        document.getElementById('file-upload').disabled = false;
        document.getElementById('camera-scan').disabled = false;
        
        document.getElementById('file-upload-preview').classList.add('hidden');
        document.getElementById('form-actions').classList.add('hidden');
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/[&<>]/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;'}[m]));
    }

    function renderDynamicNotifications() {
        const container = document.getElementById('dynamic-notifications-grid');
        if (!container) return;
        container.innerHTML = ''; let count = 1;

        foldersData.forEach(folder => {
            if (folder.documents) {
                folder.documents.forEach(doc => {
                    let isTask = doc.is_task == 1 || doc.is_task === true || doc.is_task === '1' || doc.is_task === 'true';
                    
                    if(isTask) {
                        let style = { pin: 'bg-emerald-500', bgBox: 'bg-emerald-50/80', text: 'text-emerald-600' };
                        let dateText = 'بدون تاريخ محدد';
                        
                        if(doc.expiry_date) {
                            let today = new Date(); today.setHours(0,0,0,0);
                            let expiry = new Date(doc.expiry_date); expiry.setHours(0,0,0,0);
                            let diffDays = Math.ceil((expiry - today) / (1000 * 60 * 60 * 24));
                            
                            if (diffDays < 0) {
                                style = { pin: 'bg-rose-500', bgBox: 'bg-rose-50/80', text: 'text-rose-600' };
                                dateText = 'منتهي';
                            } else if (diffDays === 0) {
                                style = { pin: 'bg-rose-500', bgBox: 'bg-rose-50/80', text: 'text-rose-600' };
                                dateText = 'ينتهي اليوم!';
                            } else if (diffDays <= 7) {
                                style = { pin: 'bg-orange-500', bgBox: 'bg-orange-50/80', text: 'text-orange-600' };
                                dateText = 'متبقي ' + diffDays + ' يوم';
                            } else {
                                style = { pin: 'bg-emerald-500', bgBox: 'bg-emerald-50/80', text: 'text-emerald-600' };
                                dateText = 'متبقي ' + diffDays + ' يوم';
                            }
                        }

                        let rotation = (count % 2 === 0) ? 'rotate-2' : '-rotate-2';
                        
                        let caseNumberHtml = doc.case_number ? `<div class="flex justify-between items-center py-1.5 border-b border-white/50"><span class="font-bold text-slate-500">رقم القضية:</span> <span class="font-black text-slate-700">${escapeHtml(doc.case_number)}</span></div>` : '';
                        let relatedPartyHtml = doc.related_party ? `<div class="flex justify-between items-center py-1.5 border-b border-white/50"><span class="font-bold text-slate-500">المحكمة/الجهة:</span> <span class="font-black text-slate-700 truncate max-w-[120px]" title="${escapeHtml(doc.related_party)}">${escapeHtml(doc.related_party)}</span></div>` : '';
                        let importanceHtml = doc.importance ? `<div class="flex justify-between items-center py-1.5 border-b border-white/50"><span class="font-bold text-slate-500">الأهمية:</span> <span class="font-black ${doc.importance === 'عالي' ? 'text-rose-500' : (doc.importance === 'متوسط' ? 'text-orange-500' : 'text-emerald-500')}">${escapeHtml(doc.importance)}</span></div>` : '';

                        container.innerHTML += `
                            <div class="relative w-72 lg:w-80 bg-white rounded-[2rem] p-2.5 shadow-[0_10px_30px_rgba(0,0,0,0.06)] transform ${rotation} hover:rotate-0 hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 group cursor-pointer" onclick="window.open('/mizan/documents/${doc.id}/view', '_blank')">
                                <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full ${style.pin} shadow-[0_4px_6px_rgba(0,0,0,0.3)] border-[1.5px] border-white z-20 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 bg-white/50 rounded-full absolute top-0.5 left-0.5"></div>
                                </div>
                                
                                <div class="${style.bgBox} rounded-[1.5rem] p-5 h-full flex flex-col relative overflow-hidden border border-slate-50/50">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="px-2.5 py-1 bg-white/90 rounded-md text-[10px] font-black ${style.text} shadow-sm border border-white">${dateText}</span>
                                        <span class="${style.text} font-serif italic text-xl font-black opacity-80">#${count.toString().padStart(2, '0')}</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <span class="text-[10px] font-black uppercase text-slate-500 block mb-1">${doc.classification || 'مستند عام'}</span>
                                        <h3 class="text-[#0f172a] font-black text-base leading-snug line-clamp-2">${escapeHtml(doc.title)}</h3>
                                    </div>
                                    
                                    <div class="text-[11px] mb-3 flex-1 flex flex-col gap-0.5">
                                        ${caseNumberHtml}
                                        ${relatedPartyHtml}
                                        ${importanceHtml}
                                    </div>
                                    
                                    <div class="text-[11px] text-slate-600 font-medium leading-relaxed bg-white/70 p-3 rounded-xl border border-white shadow-sm mt-auto">
                                        <span class="font-black text-slate-400 block mb-1">ملاحظات:</span>
                                        <span class="line-clamp-3">${doc.notes || 'لا توجد ملاحظات إضافية مسجلة.'}</span>
                                    </div>
                                </div>
                            </div>`;
                        count++;
                    }
                });
            }
        });
        if(count === 1) container.innerHTML = `<div class="w-full col-span-full flex flex-col items-center justify-center text-slate-400 mt-20 font-bold text-lg"><svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>اللوحة نظيفة! لا توجد مستندات مثبتة في لوحة المهام.</div>`;
    }

    function formatAiSummaryHTML(summaryText) {
        if (!summaryText) return '';
        let htmlList = '<div class="text-sm text-slate-700 leading-loose space-y-3 pb-6">';
        let lines = summaryText.split('\n');

        lines.forEach(line => {
            let text = line.trim();
            if (!text) { htmlList += '<div class="h-2"></div>'; return; }
            let safeText = escapeHtml(text);
            safeText = safeText.replace(/\*\*(.*?)\*\*/g, '<strong class="font-black text-[#0f172a] bg-[#0f6afd]/10 px-1.5 py-0.5 rounded">$1</strong>');

            if (text.startsWith('# ')) {
                htmlList += `<h1 class="text-xl lg:text-2xl font-black text-[#0f6afd] mt-6 mb-3 border-b border-blue-100 pb-2 flex items-center gap-2"><div class="w-1.5 h-6 bg-[#0f6afd] rounded-full"></div>${safeText.substring(2)}</h1>`;
            } else if (text.startsWith('## ')) {
                htmlList += `<h2 class="text-lg font-bold text-[#0f172a] mt-5 mb-2">${safeText.substring(3)}</h2>`;
            } else if (text.startsWith('### ')) {
                htmlList += `<h3 class="text-base font-bold text-slate-700 mt-4">${safeText.substring(4)}</h3>`;
            } else if (text.startsWith('- ') || text.startsWith('* ')) {
                htmlList += `<div class="flex items-start gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 mb-2 hover:border-[#0f6afd]/30 transition"><span class="text-[#0f6afd] mt-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span><span class="flex-1">${safeText.substring(2)}</span></div>`;
            } else if (/^\d+\.\s/.test(text)) {
                let match = text.match(/^(\d+)\.\s(.*)/);
                let number = match[1];
                let content = safeText.replace(/^\d+\.\s/, '');
                htmlList += `<div class="flex items-start gap-3 bg-white p-3 rounded-xl border border-slate-100 shadow-sm mb-2"><span class="flex items-center justify-center min-w-[24px] h-6 rounded-md bg-[#0f172a] text-white text-xs font-bold mt-0.5">${number}</span><span class="flex-1">${content}</span></div>`;
            } else {
                htmlList += `<p class="text-slate-600 px-2">${safeText}</p>`;
            }
        });
        htmlList += '</div>';
        return htmlList;
    }

    async function processAiDocument(file) {
        if (!file) return;
        
        const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) { showToast('المعذرة، التحليل يدعم ملفات PDF والصور فقط.', 'error'); return; }

        const titleEl = document.getElementById('aiResultTitle');
        const contentEl = document.getElementById('aiSummaryContent');
        titleEl.innerHTML = `<div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div> جاري التحليل: <span class="text-sm text-slate-500 mr-2">${escapeHtml(file.name)}</span>`;
        contentEl.innerHTML = `<div class="flex flex-col items-center justify-center h-full mt-10"><svg class="w-12 h-12 text-[#0f6afd] animate-spin mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="text-sm font-bold text-slate-600">جاري القراءة واستخراج الزبدة...</p></div>`;

        const formData = new FormData(); formData.append('document', file); formData.append('_token', '{{ csrf_token() }}');
        try {
            const response = await fetch('{{ route("documents.summarize") }}', { method: 'POST', body: formData });
            const result = await response.json();
            
            if (result.success) {
                currentAiFilename = result.filename;
                currentAiSummary = result.summary; 
                titleEl.innerHTML = `<div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div> النتيجة التحليلية: <span class="text-sm text-slate-500 mr-2">${escapeHtml(result.filename)}</span>`;
                
                let htmlList = formatAiSummaryHTML(result.summary);
                htmlList += `
                <div class="mt-4 pt-4 border-t border-slate-100 flex gap-3 sticky bottom-0 bg-white pb-2">
                    <button onclick="copyAiSummary()" class="flex-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 py-3 rounded-xl text-sm font-bold transition-colors">نسخ النص</button>
                    <button onclick="saveSummaryToHistory()" class="flex-1 bg-[#0f172a] hover:bg-[#0f6afd] text-white py-3 rounded-xl text-sm font-bold shadow-md transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> حفظ في السجل
                    </button>
                </div>`;
                contentEl.innerHTML = htmlList;
            } else { throw new Error(result.message || 'فشل التحليل'); }
        } catch (error) {
            titleEl.innerHTML = `<span class="text-red-500 font-bold">حدث خطأ</span>`;
            contentEl.innerHTML = `<div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold border border-red-100">${error.message}</div>`;
            showToast('خطأ: ' + error.message, 'error');
        }
        document.getElementById('aiDocumentInput').value = '';
    }

    function copyAiSummary() {
        if(currentAiSummary) { navigator.clipboard.writeText(currentAiSummary); showToast('تم النسخ بنجاح!', 'success'); }
    }

    async function saveSummaryToHistory() {
        if (!currentAiFilename || !currentAiSummary) return;
        try {
            const response = await fetch("{{ route('summaries.store') }}", { 
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ document_name: currentAiFilename, content: currentAiSummary })
            });
            const res = await response.json();
            if (response.ok && res.success) {
                showToast('تم الحفظ بالكامل في السجل بنجاح!', 'success');
                setTimeout(() => window.location.reload(), 1500); 
            } else { showToast('فشل الحفظ تأكد من الاتصال', 'error'); }
        } catch (e) { showToast('خطأ في الاتصال بالخادم', 'error'); }
    }

    async function showFullSummary(id) {
        toggleModal('fullSummaryModal');
        const contentBox = document.getElementById('fullSummaryContent');
        
        contentBox.innerHTML = `
            <div class="flex flex-col items-center justify-center py-20 opacity-80">
                <svg class="w-12 h-12 text-[#0f6afd] animate-spin mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="text-sm font-bold text-slate-600 animate-pulse">جاري تحميل الملخص الذكي...</p>
            </div>
        `;

        try {
            const response = await fetch(`/mizan/summaries/${id}`);
            const summary = await response.json();
            
            setTimeout(() => {
                contentBox.innerHTML = formatAiSummaryHTML(summary.content);
            }, 600);
            
        } catch(e) { 
            contentBox.innerHTML = '<div class="text-red-500 font-bold text-center py-10">حدث خطأ في تحميل الملخص.</div>';
            showToast('خطأ في عرض الملخص', 'error'); 
        }
    }

    function switchTab(tab, saveState = true) {
        if(saveState) localStorage.setItem('activeVaultTab', tab);
        ['library', 'summaries', 'notifications'].forEach(v => {
            const view = document.getElementById('view-' + v);
            const nav = document.getElementById('nav-' + v);
            if(view) { view.classList.add('hidden'); view.classList.remove('flex'); }
            if(nav) { nav.classList.remove('bg-blue-50/80', 'text-[#0f6afd]', 'shadow-sm'); nav.classList.add('text-slate-400'); }
        });
        const activeView = document.getElementById('view-' + tab);
        const activeNav = document.getElementById('nav-' + tab);
        if(activeView) { activeView.classList.remove('hidden'); activeView.classList.add('flex'); }
        if(activeNav) { activeNav.classList.remove('text-slate-400'); activeNav.classList.add('bg-blue-50/80', 'text-[#0f6afd]', 'shadow-sm'); }
    }
</script>
@endsection