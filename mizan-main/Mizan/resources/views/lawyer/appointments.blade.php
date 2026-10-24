@extends('layouts.lawyer')

@section('title', 'إدارة المواعيد')

{{-- العنوان الآن داخل الصندوق نفسه (أسفل) — أُزيلت page_title/page_subtitle لتفادي تكرار العنوان من القالب layouts.lawyer --}}

@section('lawyer_appointments_active', 'nav-active')

@section('content')
{{-- خط Cairo --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    .appointments-page, .appointments-page * { font-family: 'Cairo', sans-serif !important; }
</style>

@php
    $totalCount     = $appointments->count();
    $newCount       = $appointments->where('status', 'scheduled')->count();
    $acceptedCount  = $appointments->where('status', 'completed')->count();
    $rejectedCount  = $appointments->where('status', 'cancelled')->count();

    $barColors = [
        'scheduled' => 'bg-[#0f6afd]',
        'completed' => 'bg-emerald-400',
        'cancelled' => 'bg-rose-400',
    ];
@endphp

<div class="appointments-page">
  {{-- الحاوية الكبرى --}}
  <div class="rounded-[2.25rem] bg-[#F4F5FA] border border-slate-200/70 shadow-[0_40px_90px_-40px_rgba(20,20,45,0.18)] p-5 md:p-7 space-y-5">

    {{-- الرأس --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3.5">
        <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0f6afd] text-white shadow-lg shadow-[#0f6afd]/30">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h5"/><path d="M17.5 17.5 16 16.3V14"/><circle cx="16" cy="16" r="6"/></svg>
        </span>
        <div>
          <h1 class="text-2xl font-extrabold text-[#0E2333] tracking-tight">إدارة المواعيد</h1>
          <p class="text-slate-500 text-sm font-medium mt-0.5">إدارة طلبات مواعيد حجز العملاء</p>
        </div>
      </div>
    </div>

    {{-- بطاقات الإحصائيات --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-[0_10px_30px_-18px_rgba(20,20,45,0.18)] flex items-center gap-3">
            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0E2333]/5 text-[#0E2333] shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/></svg>
            </span>
            <div>
                <p class="text-2xl font-black text-[#0E2333] leading-none">{{ $totalCount }}</p>
                <p class="text-slate-400 text-xs font-bold mt-1.5">إجمالي المواعيد</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-[0_10px_30px_-18px_rgba(20,20,45,0.18)] flex items-center gap-3">
            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0f6afd]/10 text-[#0f6afd] shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18M8 2v4M16 2v4M10 16h4M12 14v4"/></svg>
            </span>
            <div>
                <p class="text-2xl font-black text-[#0f6afd] leading-none">{{ $newCount }}</p>
                <p class="text-slate-400 text-xs font-bold mt-1.5">مواعيد جديدة</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-[0_10px_30px_-18px_rgba(20,20,45,0.18)] flex items-center gap-3">
            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-100/70 text-emerald-600 shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M21 14V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6M3 10h18m-2.5 7.5L17 19l-1.5-1.5"/><path d="m16 20 2 2 4-4"/></svg>
            </span>
            <div>
                <p class="text-2xl font-black text-emerald-600 leading-none">{{ $acceptedCount }}</p>
                <p class="text-slate-400 text-xs font-bold mt-1.5">مقبولة</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/70 shadow-[0_10px_30px_-18px_rgba(20,20,45,0.18)] flex items-center gap-3">
            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-rose-100/70 text-rose-500 shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M21 14V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7"/><path d="M3 10h18M15 18h6"/></svg>
            </span>
            <div>
                <p class="text-2xl font-black text-rose-500 leading-none">{{ $rejectedCount }}</p>
                <p class="text-slate-400 text-xs font-bold mt-1.5">مرفوضة</p>
            </div>
        </div>
    </div>

    {{-- رسائل النظام --}}
    @if(session('success'))
        <div class="alert-message flex items-center gap-3 px-4 py-3 text-sm text-emerald-800 rounded-2xl bg-emerald-50/80 border border-emerald-200/70 font-bold">
            <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shrink-0 shadow-sm shadow-emerald-500/30">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-message flex items-center gap-3 px-4 py-3 text-sm text-rose-800 rounded-2xl bg-rose-50/80 border border-rose-200/70 font-bold">
            <span class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-rose-400 to-rose-600 text-white shrink-0 shadow-sm shadow-rose-500/30">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </span>
            {{ session('error') }}
        </div>
    @endif

    {{-- القائمة + التقويم جنباً إلى جنب --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

    {{-- قائمة المواعيد (يسار) --}}
    <div class="xl:col-span-5 xl:order-2 bg-white rounded-3xl border border-slate-200/70 shadow-[0_20px_50px_-30px_rgba(20,20,45,0.18)] overflow-hidden">
        <div class="px-5 py-5 flex flex-wrap justify-between items-center gap-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-blue-50 text-[#0f6afd] shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                <div>
                    <h3 class="text-[#0E2333] text-base font-black leading-tight">جدول المواعيد الواردة</h3>
                    <p class="text-slate-400 text-xs font-medium mt-0.5">تابع طلبات الحجز وقم بإدارتها</p>
                </div>
            </div>

            <form id="bulk-delete-form" action="{{ route('lawyer.appointments.bulkDelete') }}" method="POST">
                @csrf
                <button type="submit" id="delete-btn" class="inline-flex items-center gap-2 bg-gradient-to-br from-rose-400 to-rose-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold hidden hover:from-rose-500 hover:to-rose-700 shadow-sm shadow-rose-500/30 transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                    <span id="delete-btn-text">حذف المحدد</span>
                </button>
            </form>
        </div>

        {{-- شريط التحديد --}}
        <div class="px-5 py-3 flex items-center gap-2.5 bg-slate-50/50">
            <input type="checkbox" id="select-all" class="w-4 h-4 rounded-md border-slate-300 text-[#0f6afd] focus:ring-[#0f6afd] cursor-pointer">
            <span class="text-xs font-semibold text-slate-500">تحديد المعالَجة</span>
            <span class="mr-auto text-[11px] font-bold text-slate-500 bg-white border border-slate-200 rounded-full px-2.5 py-1">{{ $totalCount }} مواعيد</span>
        </div>

        {{-- بطاقات المواعيد --}}
        @php
            $avatarColors = [
                'scheduled' => 'bg-gradient-to-br from-[#3b82f6] to-[#0f6afd] shadow-[#0f6afd]/30',
                'completed' => 'bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-500/30',
                'cancelled' => 'bg-gradient-to-br from-rose-400 to-rose-600 shadow-rose-500/30',
            ];
        @endphp
        <div class="p-4 space-y-3">
            @forelse($appointments as $app)
                <div class="relative rounded-2xl border border-slate-200/80 bg-white p-4 pr-5 hover:shadow-[0_16px_40px_-24px_rgba(20,20,45,0.4)] hover:border-slate-300 transition-all">
                    {{-- شريط الحالة --}}
                    <span class="absolute top-4 bottom-4 right-0 w-1 rounded-full {{ $barColors[$app->status] ?? 'bg-slate-300' }}"></span>

                    <div class="flex items-center gap-3">
                        {{-- خانة التحديد / قيد الانتظار --}}
                        <div class="shrink-0 w-5 flex justify-center">
                            @if($app->status == 'completed' || $app->status == 'cancelled')
                                <input type="checkbox" name="appointment_ids[]" value="{{ $app->id }}" form="bulk-delete-form" class="appointment-checkbox w-4 h-4 rounded-md border-slate-300 text-[#0f6afd] focus:ring-[#0f6afd] cursor-pointer">
                            @else
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-50 text-[#0f6afd]" title="قيد الانتظار">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                            @endif
                        </div>

                        {{-- الصورة الرمزية: أيقونة مستخدم يتغيّر لونها حسب الحالة --}}
                        <span class="flex items-center justify-center w-11 h-11 rounded-2xl text-white shrink-0 shadow-md {{ $avatarColors[$app->status] ?? 'bg-slate-400 shadow-slate-400/30' }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>

                        {{-- الاسم + الموعد --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-[#0E2333] truncate">{{ $app->client->name ?? 'عميل' }}</p>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 mt-2">
                                <span class="inline-flex items-center rounded-lg bg-blue-50 text-[#0f6afd] px-2 py-1 text-[11px] font-bold">
                                    {{ $app->date_time->translatedFormat('l') }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500">
                                    <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/></svg>
                                    {{ $app->date_time->format('Y-m-d') }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500">
                                    <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    {{ $app->date_time->format('h:i A') }}
                                </span>
                            </div>
                        </div>

                        {{-- الحالة --}}
                        @if($app->status == 'scheduled')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold ring-1 shrink-0 bg-blue-50 text-[#0f6afd] ring-blue-200/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0f6afd]"></span> موعد جديد
                            </span>
                        @elseif($app->status == 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold ring-1 shrink-0 bg-emerald-50 text-emerald-700 ring-emerald-200/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> تم القبول
                            </span>
                        @elseif($app->status == 'cancelled')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold ring-1 shrink-0 bg-rose-50 text-rose-600 ring-rose-200/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> تم الرفض
                            </span>
                        @endif
                    </div>

                    {{-- الملاحظات --}}
                    <div class="mt-3 flex items-start gap-2 text-[13px] text-slate-600 bg-slate-50 border border-slate-100 rounded-xl px-3.5 py-2.5 font-medium">
                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.5 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5L15.5 3Z"/><path d="M15 3v6h6"/></svg>
                        <span class="truncate">{{ $app->notes ?? 'لا توجد ملاحظات' }}</span>
                    </div>

                    {{-- الإجراءات --}}
                    @if($app->status == 'scheduled')
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            {{-- زر القبول --}}
                            <form action="{{ route('lawyer.appointments.approve', $app->id) }}" method="POST"
                                  onsubmit="this.querySelector('button').disabled=true;">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white hover:from-emerald-500 hover:to-emerald-700 rounded-xl text-xs font-bold transition-all shadow-sm shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    قبول
                                </button>
                            </form>

                            {{-- زر الرفض --}}
                            <form action="{{ route('lawyer.appointments.reject', $app->id) }}" method="POST"
                                  onsubmit="this.querySelector('button').disabled=true;">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-white text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition-all border border-rose-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    رفض
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- إذا لم يكن scheduled، سيظهر هذا النص --}}
                        <div class="mt-3 inline-flex items-center gap-1.5 text-slate-400 text-xs font-medium">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5M22 10l-7.5 7.5L13 16"/></svg>
                            تمت المعالجة
                        </div>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center gap-3 py-16">
                    <span class="flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 13V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m17 22 5-5M17 17l5 5"/></svg>
                    </span>
                    <p class="text-slate-400 text-sm font-bold">لا توجد مواعيد حالياً</p>
                    <p class="text-slate-300 text-xs">ستظهر طلبات الحجز الجديدة هنا</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- التقويم الأسبوعي (يمين) --}}
    @php
        $hourStart  = 8;
        $hourEnd    = 22;
        $hourHeight = 62;
        $hours      = range($hourStart, $hourEnd);

        // بيانات المواعيد للتقويم — يتم رسمها والتنقّل بينها عبر JavaScript بدون إعادة تحميل الصفحة
        $calData = $appointments->map(function ($app) {
            return [
                'name'   => $app->client->name ?? 'عميل',
                'y'      => (int) $app->date_time->year,
                'mo'     => (int) $app->date_time->month,
                'd'      => (int) $app->date_time->day,
                'h'      => (int) $app->date_time->hour,
                'mi'     => (int) $app->date_time->minute,
                'status' => $app->status,
            ];
        })->values();
    @endphp

    <div class="xl:col-span-7 xl:order-1 bg-white rounded-3xl border border-slate-200/70 shadow-[0_20px_50px_-30px_rgba(20,20,45,0.18)] overflow-hidden">
        {{-- رأس التقويم --}}
        <div class="px-6 py-5 flex flex-wrap justify-between items-center gap-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-11 h-11 rounded-2xl bg-[#0f6afd] text-white shrink-0">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/></svg>
                </span>
                <div>
                    <h3 class="text-[#0E2333] text-base font-black leading-tight">تقويم المواعيد الأسبوعي</h3>
                    <p id="cal-subtitle" class="text-slate-400 text-xs font-medium mt-0.5"></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" id="cal-today" class="px-4 py-2 rounded-xl bg-[#0f6afd] text-white text-xs font-black shadow-sm shadow-blue-200 hover:bg-blue-600 transition-all">اليوم</button>
                <button type="button" id="cal-prev" title="الأسبوع السابق" class="flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#0f6afd] transition-all"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>
                <button type="button" id="cal-next" title="الأسبوع التالي" class="flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-[#0f6afd] transition-all"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[560px] bg-[#EEF2F8]">
                {{-- صف أيام الأسبوع --}}
                <div class="flex border-b border-slate-200">
                    <div class="w-14 shrink-0"></div>
                    <div id="cal-day-headers" class="flex flex-1"></div>
                </div>

                {{-- شبكة الساعات --}}
                <div class="flex">
                    {{-- عمود الأوقات --}}
                    <div class="w-14 shrink-0">
                        @foreach($hours as $h)
                            <div class="relative" style="height: {{ $hourHeight }}px;">
                                <span class="absolute -top-2 left-0 right-0 text-center text-[10px] font-bold text-slate-400">
                                    {{ $h % 12 == 0 ? 12 : $h % 12 }}:00 {{ $h < 12 ? 'ص' : 'م' }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- أعمدة الأيام (تُرسم عبر JavaScript للتنقّل بدون إعادة تحميل) --}}
                    <div id="cal-day-columns" class="flex flex-1"></div>
                </div>
            </div>
        </div>
    </div>

    </div>
  </div>
</div>

{{-- التقويم الأسبوعي: رسم وتنقّل عبر JavaScript بدون إعادة تحميل الصفحة --}}
<script>
    window.__APPOINTMENTS__ = @json($calData);

    (function () {
        const HOUR_START = {{ $hourStart }};
        const HOUR_END = {{ $hourEnd }};
        const HOUR_HEIGHT = {{ $hourHeight }};
        const DAY_NAMES = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
        const MONTHS = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        const CAL_COLORS = {
            scheduled: 'bg-blue-50 border-[#0f6afd] text-blue-800',
            completed: 'bg-emerald-50 border-emerald-400 text-emerald-800',
        };
        let weekOffset = 0;

        function startOfWeekSaturday(date) {
            const d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            d.setDate(d.getDate() - ((d.getDay() + 1) % 7));
            return d;
        }
        function pad(n) { return String(n).padStart(2, '0'); }
        function fmtTime(h, mi) {
            const period = h < 12 ? 'ص' : 'م';
            const disp = h % 12 === 0 ? 12 : h % 12;
            return disp + ':' + pad(mi) + ' ' + period;
        }
        function esc(s) { const d = document.createElement('div'); d.textContent = (s == null ? '' : s); return d.innerHTML; }

        function render() {
            const today = new Date();
            const weekStart = startOfWeekSaturday(today);
            weekStart.setDate(weekStart.getDate() + weekOffset * 7);

            const subtitle = document.getElementById('cal-subtitle');
            if (subtitle) subtitle.textContent = MONTHS[weekStart.getMonth()] + ' ' + weekStart.getFullYear();

            const hourLines = Array.from({ length: HOUR_END - HOUR_START + 1 })
                .map(function () { return '<div class="border-b border-slate-200" style="height:' + HOUR_HEIGHT + 'px;"></div>'; })
                .join('');

            const headers = [];
            const columns = [];

            for (let i = 0; i < 7; i++) {
                const day = new Date(weekStart);
                day.setDate(day.getDate() + i);
                const isToday = day.getFullYear() === today.getFullYear() && day.getMonth() === today.getMonth() && day.getDate() === today.getDate();
                const isWeekend = (i === 0 || i === 6);

                headers.push(
                    '<div class="flex-1 text-center py-3 ' + (isWeekend ? 'bg-slate-50/60' : '') + '">' +
                        '<p class="text-[11px] font-bold text-slate-400">' + DAY_NAMES[i] + '</p>' +
                        '<span class="inline-flex items-center justify-center w-8 h-8 mt-1 rounded-full text-sm font-black ' +
                            (isToday ? 'bg-[#0f6afd] text-white shadow-sm shadow-blue-200' : 'text-[#0E2333]') + '">' +
                            pad(day.getDate()) +
                        '</span>' +
                    '</div>'
                );

                const dayEvents = (window.__APPOINTMENTS__ || []).filter(function (e) {
                    const start = e.h + e.mi / 60;
                    return e.status !== 'cancelled' &&
                        e.y === day.getFullYear() && e.mo === (day.getMonth() + 1) && e.d === day.getDate() &&
                        start >= HOUR_START && start <= HOUR_END;
                });

                const eventsHtml = dayEvents.map(function (e) {
                    const start = e.h + e.mi / 60;
                    const top = (start - HOUR_START) * HOUR_HEIGHT + 3;
                    const color = CAL_COLORS[e.status] || 'bg-slate-50 border-slate-400 text-slate-700';
                    return '<div class="absolute right-1.5 left-1.5 rounded-xl border-r-4 px-2.5 py-1.5 overflow-hidden shadow-sm ' + color + '" ' +
                        'style="top:' + top + 'px;height:' + (HOUR_HEIGHT - 6) + 'px;">' +
                        '<p class="text-[11px] font-black truncate leading-tight">' + esc(e.name) + '</p>' +
                        '<p class="flex items-center gap-1 text-[10px] font-semibold opacity-80 truncate mt-0.5">' +
                        '<svg class="w-2.5 h-2.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
                        fmtTime(e.h, e.mi) + '</p>' +
                        '</div>';
                }).join('');

                columns.push(
                    '<div class="flex-1 relative border-r border-slate-200 last:border-r-0 ' + (isWeekend ? 'bg-slate-50/40' : '') + '" ' +
                    'style="height:' + ((HOUR_END - HOUR_START + 1) * HOUR_HEIGHT) + 'px;">' + hourLines + eventsHtml + '</div>'
                );
            }

            const headersEl = document.getElementById('cal-day-headers');
            const columnsEl = document.getElementById('cal-day-columns');
            if (headersEl) headersEl.innerHTML = headers.join('');
            if (columnsEl) columnsEl.innerHTML = columns.join('');
        }

        function bind(id, fn) { const el = document.getElementById(id); if (el) el.addEventListener('click', fn); }
        bind('cal-today', function () { weekOffset = 0; render(); });
        bind('cal-prev', function () { weekOffset -= 1; render(); });
        bind('cal-next', function () { weekOffset += 1; render(); });

        render();
    })();
</script>

<script>
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.appointment-checkbox');
    const deleteBtn = document.getElementById('delete-btn');
    const deleteBtnText = document.getElementById('delete-btn-text');

    function toggleBtn() {
        const total = checkboxes.length;
        const checked = document.querySelectorAll('.appointment-checkbox:checked').length;
        
        if (checked === 0) {
            deleteBtn.classList.add('hidden');
        } else {
            deleteBtn.classList.remove('hidden');
            deleteBtnText.textContent = (checked === total) ? 'حذف الكل' : 'حذف المحدد';
        }

        if (total > 0) {
            selectAll.checked = (checked === total);
            selectAll.indeterminate = (checked > 0 && checked < total);
        }
    }

    selectAll.addEventListener('change', (e) => {
        checkboxes.forEach(cb => cb.checked = e.target.checked);
        toggleBtn();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', toggleBtn));
</script>
@endsection
