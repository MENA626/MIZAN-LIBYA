@extends('layouts.app')

@section('title', 'أرشيف القضايا')

@section('content')

<!-- استدعاء خط كايرو بأوزان متعددة للنظافة البصرية -->
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* =========================================
       الخلفية العامة للصفحة
       ========================================= */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        background-color: #f8fafc; 
        overflow-x: hidden;
    }
    
    .font-cairo { 
        font-family: 'Cairo', sans-serif; 
        -webkit-font-smoothing: antialiased; 
    }

    /* الحاوية الرئيسية (عرض كامل مع هامش) */
    .desk-container {
        position: relative;
        width: 100%;
        max-width: 100%; 
        padding: 0 2vw; 
        margin: 30px auto 40px auto; 
        height: 520px; 
        display: flex;
        align-items: flex-end; 
    }

    /* =========================================
       تصميم المجلدات
       ========================================= */
    .folder-wrapper {
        position: absolute;
        bottom: 0; 
        left: 2vw; 
        width: 96vw; 
        border-radius: 35px 35px 0 0; 
        transition: height 0.6s cubic-bezier(0.25, 1, 0.5, 1), transform 0.4s ease;
        cursor: pointer;
        box-shadow: 0 -4px 25px rgba(0, 0, 0, 0.08), 0 -1px 5px rgba(0,0,0,0.03); 
    }

    .folder-wrapper:hover:not(.state-active) {
        transform: translateY(-8px); 
    }

    .folder-tab {
        position: absolute;
        top: -48px; 
        height: 48px;
        padding: 0 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600; 
        font-size: 1.05rem;
        border-radius: 20px 20px 0 0; 
        transition: all 0.3s;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.03);
    }

    .tab-right .folder-tab { right: 4%; } 
    .tab-center .folder-tab { left: 50%; transform: translateX(-50%); }
    .tab-left .folder-tab { left: 4%; }

    .folder-body {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 35px 35px 0 0;
        padding: 40px 5vw; 
        display: flex;
        flex-direction: column;
        justify-content: space-between; 
        overflow: hidden; 
    }

    /* =========================================
       الألوان + شبكة المربعات البيضاء النظيفة داخل المجلد
       ========================================= */
    .theme-dark .folder-body { 
        background: 
            linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px),
            linear-gradient(135deg, #0f172a 0%, #1e293b 100%); 
        background-size: 30px 30px, 30px 30px, 100% 100%;
        color: #ffffff; 
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .theme-dark .folder-tab { background: #0f172a; color: #ffffff; border-top: 1px solid rgba(255, 255, 255, 0.1); }

    .theme-primary .folder-body { 
        background: 
            linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
            linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
        background-size: 30px 30px, 30px 30px, 100% 100%;
        color: #ffffff; 
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }
    .theme-primary .folder-tab { background: #2563eb; color: #ffffff; border-top: 1px solid rgba(255, 255, 255, 0.15); }

    .theme-light .folder-body { 
        background: 
            linear-gradient(rgba(255, 255, 255, 0.5) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.5) 1px, transparent 1px),
            linear-gradient(135deg, #f0f6ff 0%, #e0eaff 100%); 
        background-size: 30px 30px, 30px 30px, 100% 100%;
        color: #0f172a; 
        border-top: 1px solid #ffffff;
    }
    .theme-light .folder-tab { background: #f0f6ff; color: #0f172a; border-top: 1px solid #ffffff; }

    /* =========================================
       ختم ميزان النظيف (Clean Watermark Stamp)
       ========================================= */
    .mizan-clean-stamp {
        position: absolute;
        top: 35px;
        left: 5vw; /* محاذاة مع الهامش الداخلي */
        width: 110px;
        height: 110px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transform: rotate(-12deg);
        pointer-events: none; /* حتى لا يعيق النقر على العناصر */
        z-index: 0;
    }
    .mizan-clean-stamp::after {
        content: '';
        position: absolute;
        width: 96px;
        height: 96px;
        border: 1px dashed rgba(255, 255, 255, 0.15);
        border-radius: 50%;
    }
    .stamp-title {
        font-family: 'Cairo', sans-serif;
        font-weight: 900;
        font-size: 1.6rem;
        opacity: 0.12;
        letter-spacing: 1px;
    }
    .stamp-subtitle {
        font-weight: 700;
        font-size: 0.5rem;
        opacity: 0.15;
        letter-spacing: 1.5px;
        margin-top: 2px;
    }
    /* تعديل الختم للملف الفاتح */
    .theme-light .mizan-clean-stamp { border-color: rgba(15, 23, 42, 0.05); }
    .theme-light .mizan-clean-stamp::after { border-color: rgba(15, 23, 42, 0.08); }
    .theme-light .stamp-title, .theme-light .stamp-subtitle { opacity: 0.08; color: #0f172a; }

    /* أزرار الإجراءات */
    .action-btn { transition: all 0.3s ease; border-radius: 12px; }
    .theme-dark .action-btn { background-color: #ffffff; color: #0f172a; }
    .theme-primary .action-btn { background-color: #ffffff; color: #2563eb; }
    .theme-light .action-btn { background-color: #2563eb; color: #ffffff; }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

    /* الأحجام */
    .state-active { z-index: 30; height: 380px; cursor: default; }
    .state-stack-1 { z-index: 20; height: 440px; }
    .state-stack-2 { z-index: 10; height: 500px; }
    .state-stack-hidden { z-index: 5; height: 500px; opacity: 0; pointer-events: none; }

    /* حركات الظهور */
    .folder-content { opacity: 0; transition: opacity 0.2s ease, transform 0.3s ease; transform: translateY(10px); pointer-events: none; z-index: 10; relative; }
    .state-active .folder-content { 
        opacity: 1; 
        transform: translateY(0);
        transition: opacity 0.5s 0.3s ease, transform 0.5s 0.3s ease;
        pointer-events: auto;
    }

    /* كروت معلومات نظيفة */
    .info-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        padding: 12px 20px; 
        backdrop-filter: blur(4px);
    }
    .theme-light .info-card {
        background: rgba(255, 255, 255, 0.6);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .status-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .theme-dark .status-tag, .theme-primary .status-tag { background: rgba(255,255,255,0.1); }
    .theme-light .status-tag { background: rgba(0,0,0,0.05); }

</style>

@php
    $displayFolders = [];
    foreach($cases as $case) {
        $displayFolders[] = [ 'is_empty' => false, 'data' => $case ];
    }
    while(count($displayFolders) < 3) {
        $displayFolders[] = [ 'is_empty' => true, 'data' => null ];
    }
@endphp

<div class="w-full font-cairo flex flex-col pt-8 pb-8" dir="rtl">
    
    <div class="text-center mb-6 px-4 z-10 relative">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-2 tracking-tight">أرشيف القضايا القانونية</h1>
        <p class="text-slate-500 font-medium text-base md:text-lg">استعرض تفاصيل ومستجدات ملفاتك بكل تنظيم</p>
    </div>

    <div class="desk-container">
        
        @foreach($displayFolders as $index => $folder)
            @php
                $tabPositions = ['tab-right', 'tab-center', 'tab-left'];
                $tabClass = $tabPositions[$index % 3];
                
                $colorClasses = ['theme-dark', 'theme-primary', 'theme-light'];
                $colorClass = $colorClasses[$index % 3];
                
                if($index == 0) $stateClass = 'state-active';
                elseif($index == 1) $stateClass = 'state-stack-1';
                elseif($index == 2) $stateClass = 'state-stack-2';
                else $stateClass = 'state-stack-hidden';
            @endphp

            <div class="folder-wrapper {{ $tabClass }} {{ $stateClass }} {{ $colorClass }}" data-index="{{ $index }}" onclick="bringToFront({{ $index }})">
                
                <div class="folder-tab">
                    @if(!$folder['is_empty'])
                        <i class="far fa-folder-open ml-2 opacity-80"></i> ملف #{{ $folder['data']->case_number ?? $folder['data']->id }}
                    @else
                        <span class="opacity-70 font-medium"><i class="far fa-folder ml-2"></i> ملف إضافي</span>
                    @endif
                </div>

                <div class="folder-body relative">
                    
                    <!-- إضافة الختم النظيف داخل كل ملف -->
                    <div class="mizan-clean-stamp">
                        <span class="stamp-title">ميزان</span>
                        <span class="stamp-subtitle">نظام معتمد</span>
                    </div>

                    <div class="folder-content h-full flex flex-col justify-between relative z-10">
                        
                        <div class="w-full">
                            @if(!$folder['is_empty'])
                                @php $case = $folder['data']; @endphp
                                
                                <div class="mb-4 flex items-center justify-between">
                                    <div class="status-tag">
                                        <span class="w-2.5 h-2.5 rounded-full @if($case->status == 'open') bg-emerald-400 @elseif($case->status == 'closed') bg-rose-400 @else bg-amber-400 @endif"></span>
                                        @if($case->status == 'open') نشطة ومستمرة @elseif($case->status == 'closed') مُغلقة (منتهية) @else قيد التحضير @endif
                                    </div>
                                    
                                    @if($case->filing_date)
                                        <span class="text-sm font-medium opacity-80">
                                            سُجلت في: <span class="font-bold">{{ $case->filing_date->translatedFormat('Y/m/d') }}</span>
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-2xl md:text-3xl font-bold mb-6 leading-tight max-w-3xl">
                                    @if($case->opponent_name)
                                        دعوى ضد: <span class="opacity-90">{{ $case->opponent_name }}</span>
                                    @else
                                        {{ $case->case_type ?? 'تفاصيل القضية' }}
                                    @endif
                                </h2>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                                    <div class="info-card">
                                        <span class="block opacity-70 mb-1 text-[12px] font-medium">الجهة المختصة</span>
                                        <p class="font-bold text-sm md:text-base">{{ $case->court_name ?? 'غير محدد' }}</p>
                                    </div>
                                    <div class="info-card">
                                        <span class="block opacity-70 mb-1 text-[12px] font-medium">التصنيف القانوني</span>
                                        <p class="font-bold text-sm md:text-base">{{ $case->case_category ?? 'غير محدد' }}</p>
                                    </div>
                                    <div class="info-card">
                                        <span class="block opacity-70 mb-1 text-[12px] font-medium">إجمالي الجلسات</span>
                                        <p class="font-bold text-sm md:text-base">{{ $case->sessions_count ?? 0 }} جلسات</p>
                                    </div>
                                    <div class="info-card">
                                        <span class="block opacity-70 mb-1 text-[12px] font-medium">تاريخ الجلسة القادمة</span>
                                        <p class="font-bold text-sm md:text-base">{{ $case->nextScheduledSession ? $case->nextScheduledSession->session_date->translatedFormat('d M Y') : 'لا يوجد موعد' }}</p>
                                    </div>
                                </div>

                            @else
                                <div class="mt-14 text-center opacity-50 flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 {{ $colorClass == 'theme-light' ? 'bg-black bg-opacity-5' : 'bg-white bg-opacity-10' }}">
                                        <i class="far fa-file-alt text-2xl"></i>
                                    </div>
                                    <h2 class="text-xl font-medium mb-1">مساحة مخصصة لحفظ قضاياك المستقبلية</h2>
                                    <p class="text-sm font-normal opacity-70">يتم ترتيب ملفاتك الجديدة هنا تلقائياً عند إضافتها</p>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 flex justify-between items-end border-t border-opacity-10 mt-4 {{ $colorClass == 'theme-light' ? 'border-slate-300' : 'border-white' }}">
                            @if(!$folder['is_empty'])
                                <div>
                                    @if($case->lawyer)
                                        <p class="text-[12px] opacity-70 font-medium mb-1">المحامي المكلف بالملف</p>
                                        <p class="font-bold text-lg">{{ $case->lawyer->name }}</p>
                                    @endif
                                </div>
                                
                                <a href="{{ url('/client/my-case/'.$case->id) }}" class="action-btn px-6 py-2.5 font-bold text-sm flex items-center gap-2">
                                    فتح الملف <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>

<script>
    const folders = document.querySelectorAll('.folder-wrapper');
    const totalFolders = folders.length;

    function bringToFront(clickedIndex) {
        const targetFolder = document.querySelector(`.folder-wrapper[data-index="${clickedIndex}"]`);
        if(targetFolder.classList.contains('state-active')) return; 

        folders.forEach((folder) => {
            let currentIndex = parseInt(folder.getAttribute('data-index'));
            let newPosition = currentIndex - clickedIndex;
            
            if (newPosition < 0) {
                newPosition = totalFolders + newPosition;
            }

            folder.classList.remove('state-active', 'state-stack-1', 'state-stack-2', 'state-stack-hidden');

            if (newPosition === 0) {
                folder.classList.add('state-active');
            } else if (newPosition === 1) {
                folder.classList.add('state-stack-1');
            } else if (newPosition === 2) {
                folder.classList.add('state-stack-2');
            } else {
                folder.classList.add('state-stack-hidden');
            }
        });
    }
</script>

@endsection