@extends('layouts.lawyer')

@section('title', 'لوحة التحكم')
@section('page_title', 'لوحة التحكم')


@section('lawyer_dashboard_active', 'nav-active')

@section('content')

<div class="lawyer-dashboard-root space-y-6 max-w-[1400px] mx-auto" style="font-family: 'Cairo', sans-serif;">

    {{-- بانر الترحيب --}}
    <div class="relative overflow-hidden rounded-3xl p-7 text-white bg-gradient-to-br from-blue-800 via-blue-600 to-blue-900 shadow-xl shadow-blue-900/25">
        <div class="absolute -top-16 -left-16 w-56 h-56 rounded-full" style="background: radial-gradient(circle, rgba(56,189,248,0.5), transparent 70%);"></div>
        <div class="absolute -bottom-24 right-10 w-72 h-72 rounded-full" style="background: radial-gradient(circle, rgba(37,99,235,0.55), transparent 70%);"></div>
        <i class="fas fa-balance-scale absolute -bottom-4 left-10 text-[120px] text-white/[0.06]"></i>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-white/15 backdrop-blur px-3 py-1 rounded-full mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span> متصل الآن
                </span>
                <h1 class="text-3xl font-extrabold leading-tight">
                    مرحباً بعودتك<span class="text-slate-200">{{ isset(Auth::user()->name) ? '، أ. ' . Auth::user()->name : '' }}</span>
                </h1>
                <p class="text-sm text-white/75 font-medium mt-2">إليك ملخص نشاطاتك القانونية لهذا اليوم</p>
            </div>
            <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2.5 rounded-2xl border border-white/10 self-start sm:self-auto">
                <i class="fas fa-calendar-day text-cyan-200"></i>
                <span class="text-sm font-bold whitespace-nowrap">{{ now()->translatedFormat('l، j F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- رسالة نجاح --}}
    @if(session('success'))
        <div id="dashSuccessAlert" class="p-4 bg-gradient-to-l from-emerald-50 to-white border border-emerald-200 border-r-4 border-r-emerald-500 rounded-2xl flex items-center gap-3 text-emerald-700 text-sm font-semibold shadow-sm transition-all duration-500">
            <span class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle"></i>
            </span>
            {{ session('success') }}
        </div>
        <script>
            (function () {
                var el = document.getElementById('dashSuccessAlert');
                if (!el) return;
                setTimeout(function () {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(function () { el.style.display = 'none'; }, 500);
                }, 4000);
            })();
        </script>
    @endif

    {{-- كروت الإحصائيات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- الرصيد --}}
        <div class="relative rounded-2xl p-5 overflow-hidden text-white shadow-xl shadow-blue-900/30 bg-gradient-to-br from-blue-800 via-blue-600 to-blue-900">
            <div class="absolute inset-0" style="background: radial-gradient(circle at 78% 60%, rgba(56,189,248,0.6), transparent 55%);"></div>
            <div class="absolute inset-0" style="background: radial-gradient(circle at 18% 18%, rgba(99,102,241,0.4), transparent 45%);"></div>
            <div class="relative flex flex-col justify-between h-full min-h-[150px]">
                <div class="flex justify-between items-start">
                    <div class="flex items-center">
                        <span class="w-7 h-7 rounded-full bg-white/95"></span>
                        <span class="w-7 h-7 rounded-full bg-cyan-300/80 -mr-3"></span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-3xl font-extrabold tracking-tight">
                        {{ number_format($currentBalance, 0) }} <span class="text-base font-semibold text-white/70">د.ل</span>
                    </p>
                    <p class="text-xs text-white/70 font-medium mt-1">الرصيد الإجمالي</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold mt-3 bg-white/15 px-2 py-0.5 rounded-full">
                        <i class="fas fa-arrow-up text-[9px]"></i> +{{ number_format($thisMonthEarnings, 0) }} د.ل هذا الشهر
                    </span>
                </div>
            </div>
        </div>

        {{-- القضايا --}}
        <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm hover:shadow-xl hover:shadow-slate-200/60 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <i class="fas fa-folder-open absolute -bottom-3 left-3 text-[110px] text-blue-500/[0.07] pointer-events-none"></i>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-semibold mb-1.5 tracking-wide">القضايا النشطة</p>
                    <p class="text-slate-900 text-2xl font-extrabold">{{ $activeCasesCount }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-2">ملفات جارية</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
                    <i class="fas fa-folder-open text-lg"></i>
                </div>
            </div>
        </div>

        {{-- الجلسات --}}
        <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm hover:shadow-xl hover:shadow-slate-200/60 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <i class="fas fa-calendar-check absolute -bottom-3 left-3 text-[110px] text-sky-500/[0.07] pointer-events-none"></i>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-semibold mb-1.5 tracking-wide">الجلسات القريبة</p>
                    <p class="text-slate-900 text-2xl font-extrabold">{{ $upcomingSessionsCount }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-2">خلال 48 ساعة</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center text-white shadow-lg shadow-sky-600/30">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
            </div>
        </div>

        {{-- الطلبات المعلقة --}}
        <div class="group relative bg-white rounded-2xl p-5 border border-slate-200/70 shadow-sm hover:shadow-xl hover:shadow-slate-200/60 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <i class="fas fa-clock absolute -bottom-3 left-3 text-[110px] text-amber-500/[0.08] pointer-events-none"></i>
            <div class="relative flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-semibold mb-1.5 tracking-wide">طلبات معلقة</p>
                    <p class="text-slate-900 text-2xl font-extrabold">{{ $pendingConsultations->count() }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-2">تنتظر التسعير</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
        </div>
    </div>

{{-- تبويبات الإستشارات --}}
<div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3 bg-gradient-to-l from-blue-50/40 to-white">
        <h3 class="text-slate-900 text-sm font-bold flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-file-alt"></i></span>
            آخر الإستشارات
        </h3>
        {{-- رابط عرض الكل --}}
        <a href="{{ route('lawyer.consultations') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
            عرض الكل <i class="fas fa-arrow-left text-[9px]"></i>
        </a>
    </div>
    

    {{-- شريط التبويبات --}}
    <div class="px-5 py-3 border-b border-slate-100 flex flex-nowrap overflow-x-auto lg:flex-wrap gap-2 scrollbar-hide bg-slate-50/40" id="dashboard-tabs">
        <button onclick="switchTab('pending')" id="btn-pending" class="tab-btn px-4 py-2.5 text-xs font-bold rounded-xl flex-shrink-0 flex items-center gap-1.5 transition-all bg-gradient-to-l from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-600/25">
            <i class="fas fa-inbox text-sm"></i> آخر الإستشارات الواردة ({{ $pendingConsultations->count() }})
        </button>
        <button onclick="switchTab('accepted')" id="btn-accepted" class="tab-btn px-4 py-2.5 text-xs font-bold rounded-xl flex-shrink-0 flex items-center gap-1.5 transition-all text-slate-400 hover:text-slate-600 hover:bg-slate-100">
            <i class="fas fa-check text-sm"></i> الإستشارات المقبولة ({{ $acceptedConsultations->count() }})
        </button>
        <button onclick="switchTab('rejected')" id="btn-rejected" class="tab-btn px-4 py-2.5 text-xs font-bold rounded-xl flex-shrink-0 flex items-center gap-1.5 transition-all text-slate-400 hover:text-slate-600 hover:bg-slate-100">
            <i class="fas fa-times text-sm"></i> الإستشارات المرفوضة ({{ $rejectedConsultations->count() }})
        </button>
    </div>

    <div class="p-5">
    {{-- الإستشارات الواردة --}}
    <div id="tab-pending" class="tab-content divide-y divide-slate-100">
        @if($pendingConsultations && $pendingConsultations->count() > 0)
            @foreach($pendingConsultations as $consult)
            <div class="py-5 first:pt-0 last:pb-0 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-5">
                <div class="flex-1 space-y-3.5 w-full">
                    <div class="flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-bold">التصنيف: {{ $consult->legal_category ?? 'عام' }}</span>
                        <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-500 font-bold">الاستعجال: {{ $consult->urgency_level ?? 'عادية' }}</span>
                        <span class="text-slate-400 font-medium"><i class="fas fa-clock text-[10px] ml-0.5"></i> {{ $consult->created_at ? $consult->created_at->diffForHumans() : 'الآن' }}</span>
                    </div>
                    <h4 class="text-slate-900 font-bold text-lg">{{ $consult->title ?? 'طلب استشارة قانونية' }}</h4>
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl">
                        <p class="text-slate-600 text-sm leading-relaxed">
                            <span class="font-bold text-slate-700 block mb-1.5 text-xs">تفاصيل وقائع المشكلة وسرد الأحداث:</span>
                            {{ $consult->description ?? 'لا توجد تفاصيل إضافية.' }}
                        </p>
                    </div>
                    {{-- معلومات إضافية منسقة --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs font-medium">
                        <div class="flex items-center gap-2 bg-white p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors">
                            <span class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center shrink-0"><i class="fas fa-user text-slate-400 text-[11px]"></i></span>
                            <span class="text-slate-500">اسم الموكل:</span>
                            <span class="text-slate-800 font-bold">{{ $consult->client->name ?? 'عميل المنصة' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors">
                            <span class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center shrink-0"><i class="fas fa-tag text-slate-400 text-[11px]"></i></span>
                            <span class="text-slate-500">الصفة القانونية:</span>
                            <span class="text-slate-800 font-bold">{{ $consult->client_role ?? 'غير محددة' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors">
                            <span class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center shrink-0"><i class="fas fa-user-slash text-slate-400 text-[11px]"></i></span>
                            <span class="text-slate-500">الطرف الخصم بالنزاع:</span>
                            <span class="text-slate-800 font-bold">{{ $consult->opponent_name ?? 'لا يوجد خصم' }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white p-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors">
                            <span class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center shrink-0"><i class="fas fa-building text-slate-400 text-[11px]"></i></span>
                            <span class="text-slate-500">هل توجد دعوى مقامة حالياً:</span>
                            <span class="text-slate-800 font-bold">
                                {{ (isset($consult->has_existing_case) && ($consult->has_existing_case === '1' || $consult->has_existing_case === 'نعم')) ? 'نعم، مرفوعة حالياً' : 'لا، مجرد استشارة' }}
                            </span>
                        </div>
                        @if(isset($consult->court_or_deadline) && $consult->court_or_deadline && $consult->court_or_deadline !== 'لا يوجد')
                            <div class="flex items-center gap-2 bg-amber-50/60 p-3 rounded-xl border border-amber-100 md:col-span-2">
                                <span class="w-7 h-7 rounded-lg bg-amber-100/70 flex items-center justify-center shrink-0"><i class="fas fa-gavel text-amber-500 text-[11px]"></i></span>
                                <span class="text-amber-800">اسم المحكمة أو تاريخ الجلسة القادمة:</span>
                                <span class="text-amber-950 font-bold">{{ $consult->court_or_deadline }}</span>
                            </div>
                        @endif
                        @if($consult->attachment)
                            <div class="flex items-center justify-between bg-emerald-50/60 p-3 rounded-xl border border-emerald-100 md:col-span-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100/70 flex items-center justify-center shrink-0"><i class="fas fa-paperclip text-emerald-500 text-[11px]"></i></span>
                                    <span class="text-emerald-800">المستندات والوثائق الداعمة:</span>
                                    <span class="text-emerald-950 font-bold">مرفقات متاحة</span>
                                </div>
                                <a href="{{ asset('storage/' . $consult->attachment) }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg transition-colors shadow-sm">تحميل الملف</a>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- أزرار التحكم --}}
                <div class="w-full xl:w-auto p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-wrap lg:flex-nowrap items-center gap-2 shrink-0">
                    <form action="{{ route('lawyer.consultations.review', $consult->id) }}" method="POST" class="flex-1 lg:flex-none flex items-center gap-2">
                        @csrf
                        <div class="relative flex-1 lg:w-24">
                            <input type="text" name="price" required placeholder="المبلغ" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="w-full lg:w-24 px-2 py-2.5 bg-white rounded-xl text-sm font-bold text-slate-800 border border-slate-200 text-center focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400 transition">
                            <span class="absolute left-2 top-3 text-[11px] text-slate-400">د.ل</span>
                        </div>
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="flex-shrink-0 px-4 py-2.5 bg-gradient-to-l from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold text-xs whitespace-nowrap shadow-lg shadow-blue-600/25 transition-all hover:-translate-y-0.5">اعتماد</button>
                    </form>
                    <form action="{{ route('lawyer.consultations.review', $consult->id) }}" method="POST" class="w-full lg:w-auto">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="w-full px-4 py-2.5 bg-white text-red-500 hover:bg-red-50 hover:border-red-200 rounded-xl font-bold text-xs border border-slate-200 transition-colors">رفض</button>
                    </form>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-12 text-slate-400">
                <span class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3"><i class="fas fa-inbox text-2xl text-slate-300"></i></span>
                <p class="text-sm font-semibold">لا توجد إستشارات جديدة</p>
            </div>
        @endif
    </div>

        {{-- المقبولة --}}
        <div id="tab-accepted" class="tab-content hidden space-y-3">
            @if($acceptedConsultations && $acceptedConsultations->count() > 0)
                @foreach($acceptedConsultations as $consult)
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white px-4 py-4 rounded-2xl border border-slate-100 hover:border-emerald-200 hover:shadow-sm transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fas fa-check"></i></div>
                            <div>
                                <h4 class="text-slate-900 font-bold text-sm">{{ $consult->title }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $consult->client->name ?? 'عميل' }} | الصفة: {{ $consult->client_role ?? 'غير محددة' }}</p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100">
                            {{ number_format($consult->price, 0) }} د.ل
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-12 text-slate-400">
                    <span class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3"><i class="fas fa-check-circle text-2xl text-slate-300"></i></span>
                    <p class="text-sm font-semibold">لا توجد استشارات مقبولة</p>
                </div>
            @endif
        </div>

        {{-- المرفوضة --}}
        <div id="tab-rejected" class="tab-content hidden space-y-3">
            @if($rejectedConsultations && $rejectedConsultations->count() > 0)
                @foreach($rejectedConsultations as $consult)
                    <div class="flex justify-between items-center bg-white px-4 py-4 rounded-2xl border border-slate-100 hover:border-red-100 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-400 flex items-center justify-center text-sm"><i class="fas fa-times"></i></div>
                            <div>
                                <h4 class="text-slate-500 font-medium text-sm line-through">{{ $consult->title }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5">الموكل: {{ $consult->client->name ?? 'عميل' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-red-50 text-red-500 text-[11px] font-bold">مرفوضة</span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-12 text-slate-400">
                    <span class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3"><i class="fas fa-folder text-2xl text-slate-300"></i></span>
                    <p class="text-sm font-semibold">لا توجد إستشارات مرفوضة</p>
                </div>
            @endif
        </div>
    </div>
</div>

    {{-- المواعيد والرسم البياني --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 pt-8">

    {{-- مواعيد اليوم --}}
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-gradient-to-l from-blue-50/40 to-white">
            <h3 class="text-slate-900 text-sm font-bold flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fas fa-clock"></i></span>
                مواعيد اليوم
            </h3>
            <a href="{{ route('lawyer.appointments') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[9px]"></i></a>
        </div>
        <div class="divide-y divide-slate-50">
            {{-- قمنا بفرز المواعيد هنا بناءً على وقت الإنشاء (created_at) من الأحدث للأقدم --}}
            @php
                $sortedAppointments = $todayAppointments->sortByDesc('created_at');
            @endphp

            @if($sortedAppointments && $sortedAppointments->count() > 0)
                @foreach($sortedAppointments->take(3) as $appointment)
                    <div class="group relative px-5 py-4 flex items-center justify-between hover:bg-blue-50/40 transition-colors">
                        <span class="absolute right-0 top-0 h-full w-1 bg-gradient-to-b from-blue-500 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center shadow-md shadow-blue-500/25">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-sm">{{ $appointment->client->name ?? 'عميل' }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $appointment->notes ?? 'موعد' }}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-2.5 py-1.5 rounded-lg">
                                <i class="fas fa-clock text-[10px]"></i>
                                {{ \Carbon\Carbon::parse($appointment->date_time)->format('h:i A') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-10 text-slate-400">
                    <span class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-2"><i class="fas fa-calendar text-xl text-blue-300"></i></span>
                    <p class="text-xs font-semibold">لا توجد مواعيد لليوم</p>
                </div>
            @endif
        </div>
    </div>

        {{-- الرسم البياني --}}
        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-slate-900 text-sm font-bold flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-chart-line"></i></span>
                    الأرباح الشهرية
                </h3>
                <span class="text-xs text-blue-600 font-semibold bg-blue-50 px-2.5 py-1 rounded-lg">آخر 4 أسابيع</span>
            </div>
            <canvas id="earningsChart" width="400" height="210" style="max-height: 210px; width: 100%;"></canvas>
        </div>
    </div>

    {{-- آخر القضايا --}}
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-gradient-to-l from-blue-50/40 to-white">
            <h3 class="text-slate-900 text-sm font-bold flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-folder-open"></i></span>
                آخر القضايا
            </h3>
            <a href="{{ url('/lawyer/my-cases') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[9px]"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] border-separate border-spacing-0">
                <thead>
                    <tr class="bg-slate-50/70">
                        <th class="text-right px-5 py-3 text-[11px] font-bold text-slate-400 tracking-wide border-b border-slate-100">القضية</th>
                        <th class="text-right px-5 py-3 text-[11px] font-bold text-slate-400 tracking-wide border-b border-slate-100">الموكل</th>
                        <th class="text-right px-5 py-3 text-[11px] font-bold text-slate-400 tracking-wide border-b border-slate-100">الموضوع</th>
                        <th class="text-right px-5 py-3 text-[11px] font-bold text-slate-400 tracking-wide border-b border-slate-100">التاريخ</th>
                        <th class="text-right px-5 py-3 text-[11px] font-bold text-slate-400 tracking-wide border-b border-slate-100">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @if($latestCases && $latestCases->count() > 0)
                        @foreach($latestCases as $case)
                            @php
                                $isActive = strtolower($case->status) == 'active' || empty($case->status);
                                $caseStatusLabels = [
                                    'active' => 'جارية',
                                    'in_progress' => 'جارية',
                                    'pending' => 'قيد الانتظار',
                                    'postponed' => 'مؤجلة',
                                    'closed' => 'مغلقة',
                                    'completed' => 'منتهية',
                                    'rejected' => 'مرفوضة',
                                    'cancelled' => 'ملغاة',
                                ];
                                $statusLabel = $caseStatusLabels[strtolower($case->status ?? '')] ?? $case->status;
                            @endphp
                            <tr class="group hover:bg-blue-50/40 transition-colors">
                                <td class="px-5 py-3.5 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-9 h-9 rounded-xl flex items-center justify-center text-white shadow-sm shrink-0 bg-gradient-to-br {{ $isActive ? 'from-blue-500 to-blue-700 shadow-blue-500/25' : 'from-amber-400 to-amber-600 shadow-amber-500/25' }}">
                                            <i class="fas fa-folder-open text-sm"></i>
                                        </span>
                                        <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-2 py-1 rounded-md">#C-00{{ $case->id }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm font-bold text-slate-800 border-b border-slate-50">{{ $case->client->name ?? 'عميل' }}</td>
                                <td class="px-5 py-3.5 text-sm text-slate-500 border-b border-slate-50">{{ Str::limit($case->legal_details, 45, '...') ?? 'قضية' }}</td>
                                <td class="px-5 py-3.5 text-sm text-slate-400 whitespace-nowrap border-b border-slate-50">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fas fa-calendar-day text-[11px] text-slate-300"></i>
                                        {{ $case->created_at ? $case->created_at->format('d/m/Y') : 'اليوم' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 border-b border-slate-50">
                                    @if($isActive)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-100"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>جارية</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-full bg-amber-50 text-amber-600 border border-amber-100"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $statusLabel }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center py-10 text-slate-400 text-sm font-semibold">لا توجد قضايا مسجلة</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- سكربت التنقل بين التبويبات --}}
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        const colors = {
            pending: ['bg-gradient-to-l', 'from-blue-600', 'to-blue-700', 'text-white', 'shadow-lg', 'shadow-blue-600/25'],
            accepted: ['bg-emerald-600', 'text-white', 'shadow-lg', 'shadow-emerald-600/25'],
            rejected: ['bg-red-600', 'text-white', 'shadow-lg', 'shadow-red-600/25']
        };

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn px-4 py-2.5 text-xs font-bold rounded-xl flex-shrink-0 flex items-center gap-1.5 transition-all text-slate-400 hover:text-slate-600 hover:bg-slate-100';
        });

        const activeBtn = document.getElementById('btn-' + tabName);
        activeBtn.classList.remove('text-slate-400', 'hover:text-slate-600', 'hover:bg-slate-100');
        activeBtn.classList.add(...colors[tabName]);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const earningsGrad = ctx.createLinearGradient(0, 0, 0, 210);
    earningsGrad.addColorStop(0, '#06b6d4');
    earningsGrad.addColorStop(1, '#2563eb');
    const earningsGradLast = ctx.createLinearGradient(0, 0, 0, 210);
    earningsGradLast.addColorStop(0, '#3b82f6');
    earningsGradLast.addColorStop(1, '#1e3a8a');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['الأسبوع 4', 'الأسبوع 3', 'الأسبوع 2', 'الأسبوع الحالي'],
            datasets: [{
                label: 'الأرباح (د.ل)',
                data: {!! json_encode($weeklyEarnings ?? [0, 0, 0, 0]) !!},
                backgroundColor: function(c) {
                    return c.dataIndex === c.dataset.data.length - 1 ? earningsGradLast : earningsGrad;
                },
                hoverBackgroundColor: function(c) {
                    return c.dataIndex === c.dataset.data.length - 1 ? earningsGradLast : earningsGrad;
                },
                borderRadius: 10,
                borderSkipped: false,
                barPercentage: 0.55,
                categoryPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    rtl: true,
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    borderColor: '#1e293b',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    titleFont: { family: 'Cairo', size: 11, weight: '600' },
                    bodyFont: { family: 'Cairo', size: 11 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#f1f5f9', drawTicks: false },
                    ticks: { color: '#94a3b8', font: { size: 10 }, padding: 8 }
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Cairo', size: 10 } }
                }
            }
        }
    });
</script>
@endsection
