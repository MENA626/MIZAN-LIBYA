@extends('layouts.lawyer')

@section('title', 'إدارة القضايا')
@section('lawyer_cases_active', 'nav-active')

@section('content')
<div class="space-y-6">

    {{-- عرض رسائل النجاح أو الأخطاء --}}
    @if(session('success'))
    <div class="flash-message p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200/60 shadow-sm font-bold flex items-center gap-2 transition-all duration-500 ease-out">
        <i class="fas fa-check-circle text-emerald-500 text-base"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="flash-message p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200/60 shadow-sm font-bold flex items-center gap-2 transition-all duration-500 ease-out">
        <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
        {{ session('error') }}
    </div>
@endif

    {{-- إخفاء رسائل النجاح/الخطأ تلقائيًا بعد 5 ثوانٍ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.flash-message').forEach(function (el) {
                setTimeout(function () {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(function () { el.remove(); }, 500);
                }, 5000);
            });
        });
    </script>

    {{-- ==========================================
       اللوحة الرئيسية (تصميم نظيف)
       ========================================== --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-[#F4F5FA] p-5 sm:p-7 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.25)]">

        {{-- الترويسة: العنوان --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <i class="fas fa-folder-open text-lg"></i>
                </div>
                <div>
                    <h3 class="text-slate-900 text-2xl font-black leading-tight">مكتبة القضايا</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">جميع ملفات قضاياك في مكان واحد</p>
                </div>
            </div>
            <div class="relative w-full sm:w-72">
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="text" id="caseSearchInput" autocomplete="off" placeholder="ابحث بالموكل، رقم القضية، النوع، المحكمة..."
                       class="w-full rounded-full border border-slate-200 bg-white py-2.5 pr-10 pl-4 text-[13px] font-medium text-slate-700 placeholder:text-slate-400 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10">
            </div>
        </div>

        {{-- شريط التبويبات + زر الإضافة --}}
        <div class="mt-6 flex flex-wrap items-center gap-2 border-b border-slate-100 pb-5">
            <div class="flex flex-wrap items-center gap-1.5" id="caseTabs">
                <button type="button" class="case-tab inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-[13px] font-bold transition" data-status="">
                    <span class="tab-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                    كل القضايا
                    <span class="tab-count rounded-full px-1.5 py-0.5 text-[11px] font-black"></span>
                </button>
                <button type="button" class="case-tab inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-[13px] font-bold transition" data-status="pending">
                    <span class="tab-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                    قيد النظر
                    <span class="tab-count rounded-full px-1.5 py-0.5 text-[11px] font-black"></span>
                </button>
                <button type="button" class="case-tab inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-[13px] font-bold transition" data-status="open">
                    <span class="tab-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                    نشطة ومستمرة
                    <span class="tab-count rounded-full px-1.5 py-0.5 text-[11px] font-black"></span>
                </button>
                <button type="button" class="case-tab inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-[13px] font-bold transition" data-status="closed">
                    <span class="tab-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                    مكتملة
                    <span class="tab-count rounded-full px-1.5 py-0.5 text-[11px] font-black"></span>
                </button>
                <button type="button" class="case-tab inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-[13px] font-bold transition" data-status="archived">
                    <span class="tab-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                    مؤرشفة
                    <span class="tab-count rounded-full px-1.5 py-0.5 text-[11px] font-black"></span>
                </button>
            </div>
            <button type="button" id="openAddCaseBtn"
                    class="mr-auto inline-flex items-center gap-2 rounded-full bg-blue-600 hover:bg-blue-700 px-4 py-2 text-[13px] font-bold text-white shadow-sm transition-all duration-200">
                <i class="fas fa-plus-circle text-white"></i>
                <span class="text-white">فتح ملف قضية جديدة</span>
            </button>
        </div>

        {{-- عنوان القسم --}}
        <h4 class="mb-4 mt-6 text-[15px] font-black text-slate-800">قائمة القضايا</h4>

        {{-- ==========================================
           نظام الكروت (تصميم نظيف)
           ========================================== --}}
        <div class="grid min-h-[540px] auto-rows-min grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($cases as $case)
                {{-- كرت القضية --}}
                <div class="case-card flex flex-col rounded-2xl border border-slate-100 bg-white p-4 shadow-[0_1px_2px_rgba(15,23,42,0.04)] hover:border-slate-200 hover:shadow-md transition-all duration-300"
                     data-status="{{ $case->status ?? '' }}"
                     data-search="{{ \Illuminate\Support\Str::lower(trim(($case->client_name ?? '').' '.($case->case_number ?? $case->id).' '.($case->case_type ?? '').' '.($case->court_name ?? '').' '.($case->case_category ?? ''))) }}">

                    {{-- رأس الكرت: الموكل + رقم القضية + الحالة --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 shrink-0 rounded-full bg-blue-600 text-white flex items-center justify-center">
                                <i class="fas fa-balance-scale text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="truncate text-[14px] font-black text-slate-900">{{ $case->client_name }}</div>
                                <div class="font-mono text-[11px] text-slate-400">{{ $case->case_number ?? $case->id }}</div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if(($case->status ?? '') == 'pending')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold bg-amber-50 text-amber-700 ring-1 ring-amber-200/70">
                                    <i class="far fa-clock text-amber-500"></i> قيد النظر
                                </span>
                            @elseif(($case->status ?? '') == 'open')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/70">
                                    <i class="fas fa-spinner fa-spin text-emerald-500"></i> نشطة ومستمرة
                                </span>
                            @elseif(($case->status ?? '') == 'closed')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200/70">
                                    <i class="fas fa-check-circle text-indigo-500"></i> مكتملة
                                </span>
                            @elseif(($case->status ?? '') == 'archived')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                                    <i class="fas fa-archive text-slate-400"></i> مؤرشفة
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold bg-slate-50 text-slate-700 ring-1 ring-slate-200">
                                    {{ $case->status ?? 'غير محدد' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- صندوقا المعلومات: التصنيف + تاريخ القيد --}}
                    <div class="mt-4 grid grid-cols-2 gap-2.5">
                        <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                            <div class="mb-1 flex items-center gap-1.5 text-[11px] font-medium text-slate-400">
                                <i class="fas fa-tag text-[11px] text-indigo-500"></i> التصنيف
                            </div>
                            <div class="truncate text-[13px] font-bold text-slate-800">{{ $case->case_category ?? 'تصنيف غير محدد' }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-100">
                            <div class="mb-1 flex items-center gap-1.5 text-[11px] font-medium text-slate-400">
                                <i class="far fa-calendar-alt text-[11px] text-blue-500"></i> تاريخ القيد
                            </div>
                            <div class="truncate text-[13px] font-bold text-slate-800">
                                {{ $case->filing_date ? \Carbon\Carbon::parse($case->filing_date)->translatedFormat('d M Y') : 'غير محدد' }}
                            </div>
                        </div>
                    </div>

                    {{-- العنوان والوصف --}}
                    <h3 class="mt-4 text-[15px] font-black leading-snug text-slate-900 break-words">
                        {{ $case->case_type ?? 'دعوى غير مسماة' }}
                    </h3>
                    <p class="mt-1.5 flex items-center gap-1.5 text-[12.5px] leading-relaxed text-slate-500 flex-wrap">
                        <i class="fas fa-gavel text-slate-400 text-[12px]"></i>
                        <span class="truncate">محكمة: {{ $case->court_name ?? 'غير مدرج' }}</span>
                        @if($case->user_id && $case->user_id != $case->lawyer_id)
                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-100" title="مرتبط بحساب: {{ $case->client->name ?? $case->client_name }}">
                                <i class="fas fa-link text-[9px]"></i> مرتبط: {{ $case->client->name ?? $case->client_name }}
                            </span>
                        @endif
                    </p>

                    {{-- الأزرار --}}
                    <div class="mt-4 flex items-center gap-2 border-t border-slate-50 pt-3">
                        <a href="{{ url('/lawyer/cases/'.$case->id) }}" title="عرض الملف"
                           class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-[12.5px] font-bold text-slate-700 hover:bg-slate-50 transition-all duration-200">
                            <i class="fas fa-eye text-[12px]"></i> عرض التفاصيل
                        </a>
                        <button type="button" onclick='openEditCaseModal(@json($case))' title="تعديل"
                                class="inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-3.5 py-2 text-[12.5px] font-bold text-white hover:bg-blue-700 transition-all duration-200">
                            <i class="fas fa-pen text-[12px]"></i> تعديل
                        </button>

                        <form action="{{ url('/lawyer/cases/'.$case->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف ملف هذه القضية نهائياً؟')" class="inline mr-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="حذف"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all duration-200">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                {{-- حالة عدم وجود قضايا --}}
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white py-12 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                        <i class="fas fa-folder-open text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600">لا توجد قضايا</p>
                </div>
            @endforelse

            {{-- حالة عدم وجود نتائج للبحث (تظهر عبر JS) --}}
            <div id="noSearchResults" class="hidden col-span-full rounded-2xl border border-dashed border-slate-200 bg-white py-12 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                    <i class="fas fa-search text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-600">لا توجد قضايا مطابقة لبحثك</p>
                <p class="mt-1 text-xs text-slate-400">جرّب كلمة بحث أخرى</p>
            </div>

            {{-- حالة عدم وجود قضايا في الصفحة الحالية (تظهر عبر JS) --}}
            <div id="noPageResults" class="hidden col-span-full rounded-2xl border border-dashed border-slate-200 bg-white py-12 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                    <i class="fas fa-folder-open text-xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-600">لا توجد قضايا</p>
            </div>
        </div>

        {{-- شريط الترقيم (Pagination) — يعمل بدون إعادة تحميل، والأرقام تظهر دائمًا --}}
        <div class="mt-6 flex flex-wrap justify-between items-center gap-3 border-t border-slate-100 pt-5">
            <p class="text-xs text-slate-400 font-medium">عرض <span id="shownCount" class="font-bold text-slate-700">0</span> من إجمالي <span id="totalCount" class="font-bold text-slate-700">0</span> قضية</p>
            <div id="paginationBar" class="flex items-center gap-1.5"></div>
        </div>
    </div>
</div>

{{-- ====================== Modal إضافة/تعديل قضية (كما هو دون تعديل) ====================== --}}
<div id="caseModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center transition-all" style="display: none;">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto mx-4 shadow-2xl border border-slate-100 animate-fadeInUp">
        
        <div class="sticky top-0 bg-white/90 backdrop-blur-md border-b border-slate-200 px-8 py-5 flex justify-between items-center z-10 shadow-sm">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-gavel"></i>
                </div>
                إضافة ملف قضية جديد
            </h3>
            <button type="button" onclick="closeCaseModal()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <form id="caseForm" action="{{ url('/lawyer/cases') }}" method="POST" class="px-8 pb-8 pt-5 space-y-6" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>

            {{-- قسم: بيانات الموكل --}}
            <div class="space-y-5">
                <h4 class="text-base font-bold text-slate-800 flex items-center gap-2 bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                    <i class="fas fa-user text-blue-500 ml-1"></i> 1. بيانات الموكل
                </h4>

                {{-- ربط الموكل بحساب مسجل (أولاً) --}}
                <div class="px-2">
                    <label class="block text-sm font-bold mb-2 text-slate-700">
                        <i class="fas fa-link text-emerald-500 ml-1"></i> ربط القضية بحساب موكل مسجل بالنظام <span class="text-slate-400 font-medium text-xs">(اختياري)</span>
                    </label>
                    <div class="flex gap-3">
                        <input type="email" id="searchClientEmail" placeholder="أدخل البريد الإلكتروني للموكل للبحث..." 
                               class="flex-1 px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all text-right" dir="rtl">
                        <button type="button" id="searchClientBtn" class="px-6 py-3 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl font-bold text-sm transition-colors border border-emerald-200 flex items-center gap-2">
                            <i class="fas fa-search"></i> بحث وربط
                        </button>
                    </div>
                    
                    {{-- حاوية نتيجة البحث --}}
                    <div id="clientSearchResult" class="mt-3 hidden">
                        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-white text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100">
                                    <i class="fas fa-user-check text-lg"></i>
                                </div>
                                <div>
                                    <p id="foundClientName" class="text-sm font-bold text-slate-800"></p>
                                    <p id="foundClientEmail" class="text-xs text-slate-500 mt-0.5"></p>
                                </div>
                            </div>
                            <button type="button" id="removeLinkedClientBtn" class="text-xs text-rose-600 hover:text-white font-bold bg-white hover:bg-rose-500 px-3 py-1.5 rounded-lg border border-rose-200 hover:border-rose-500 transition-colors">
                                <i class="fas fa-unlink ml-1"></i> إلغاء الربط
                            </button>
                        </div>
                    </div>
                    <p id="clientSearchError" class="text-xs font-bold text-rose-500 mt-2 hidden"><i class="fas fa-exclamation-circle ml-1"></i> <span id="clientSearchErrorText"></span></p>

                    <input type="hidden" name="client_user_id" id="clientUserId" value="">
                    <p class="text-xs text-slate-400 mt-2 font-medium"><i class="fas fa-info-circle ml-1"></i> عند ربط موكل مسجل، ستظهر هذه القضية تلقائياً في صفحة "قضيتي" الخاصة به ليتمكن من متابعتها.</p>
                </div>

                {{-- الاسم ورقم الهاتف --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-2">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">
                            اسم الموكل الكامل <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="client_name" id="clientName" placeholder="أدخل اسم الموكل ثلاثياً" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">
                            رقم هاتف الموكل <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="client_phone" id="clientPhone" placeholder="09XXXXXXXX" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200 text-right">
                    </div>
                </div>
            </div>

            <div class="h-px bg-slate-100 w-full my-6"></div>

            {{-- قسم: بيانات القضية --}}
            <div class="space-y-5">
                <h4 class="text-base font-bold text-slate-800 flex items-center gap-2 bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                    <i class="fas fa-folder-open text-blue-500 ml-1"></i> 2. بيانات الدعوى الأساسية
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-2">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">نوع القضية <span class="text-rose-500">*</span></label>
                        <select name="case_type" id="caseType" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                            <option value="" disabled selected>— اختر نوع النزاع —</option>
                            @isset($categories)
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">تصنيف القضية</label>
                        <select name="case_category" id="caseCategory" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                            <option value="">— اختر التصنيف —</option>
                            <option value="تجارية">تجارية</option>
                            <option value="مدنية">مدنية</option>
                            <option value="جنائية">جنائية</option>
                            <option value="إدارية">إدارية</option>
                            <option value="أحوال شخصية">أحوال شخصية</option>
                            <option value="عقارية">عقارية</option>
                            <option value="عمالية">عمالية</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-2 mt-2">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">اسم المحكمة المرفوع بها الدعوى</label>
                        <input type="text" name="court_name" id="courtName" placeholder="مثال: محكمة الاستئناف طرابلس" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">تاريخ الرفع أو التوكيل <span class="text-rose-500">*</span></label>
                        <input type="date" name="filing_date" id="caseDate" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-2 mt-2">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">اسم الخصم (الطرف الآخر)</label>
                        <input type="text" name="opponent_name" id="opponentName" placeholder="اسم المدعى عليه أو الطرف الآخر" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">حالة القضية الحالية <span class="text-rose-500">*</span></label>
                        <select name="status" id="caseStatus" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                            <option value="pending">قيد المراجعة والتحضير</option>
                            <option value="open">نشطة وقيد المعالجة في المحكمة</option>
                            <option value="closed">مكتملة وصدر بها حكم</option>
                            <option value="archived">ملغية / شطب الدعوى / حفظ</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="h-px bg-slate-100 w-full my-6"></div>

            {{-- قسم: المعلومات المالية والتفاصيل --}}
            <div class="space-y-5">
                <h4 class="text-base font-bold text-slate-800 flex items-center gap-2 bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                    <i class="fas fa-wallet text-emerald-500 ml-1"></i> 3. التفاصيل المالية والوقائع
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-2">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">قيمة المطالبة (د.ل)</label>
                        <input type="number" name="claim_amount" id="claimAmount" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">إجمالي الأتعاب (د.ل)</label>
                        <input type="number" name="total_fees" id="totalFees" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 text-slate-700">المبلغ المدفوع (د.ل)</label>
                        <input type="number" name="paid_fees" id="paidFees" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium text-slate-800 shadow-sm transition-all duration-200">
                    </div>
                </div>
            </div>
            
            {{-- تفاصيل الدعوى --}}
            <div class="px-2">
                <label class="block text-sm font-bold mb-2 text-slate-700">
                    <i class="fas fa-align-right ml-1 text-slate-400"></i> وقائع وتفاصيل القضية <span class="text-rose-500">*</span>
                </label>
                <textarea name="details" id="caseDetails" rows="5" placeholder="اكتب ملخص وقائع المشكلة القانونية والدفوع..." required
                          class="w-full px-4 py-4 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-sm font-medium leading-relaxed text-slate-800 shadow-sm transition-all duration-200"></textarea>
            </div>

            {{-- رفع مرفقات --}}
            <div class="px-2 pb-4">
                <label class="block text-sm font-bold mb-2 text-slate-700">
                    <i class="fas fa-paperclip ml-1 text-slate-400"></i> إرفاق ملفات القضية الأساسية <span class="text-slate-400 font-medium text-xs">(اختياري)</span>
                </label>
                <div id="uploadWrapper" class="relative w-full px-6 py-8 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 text-center hover:bg-slate-100 hover:border-blue-500 transition-all duration-300 cursor-pointer group">
                    <input type="file" id="caseFiles" name="attachments" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div id="uploadDefaultState" class="space-y-3 pointer-events-none">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto shadow-sm border border-slate-200 group-hover:border-blue-200 group-hover:bg-blue-50 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">اسحب وأفلت الملفات هنا أو اضغط للاختيار</p>
                            <p class="text-xs text-slate-500 mt-1 font-medium">الامتدادات المدعومة: PDF, PNG, JPG (الحد الأقصى 5MB)</p>
                        </div>
                    </div>
                    <div id="uploadSuccessState" class="hidden space-y-3 animate-scaleUp pointer-events-none">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 shadow-sm border border-emerald-200">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-emerald-700">تم اختيار الملف بنجاح</p>
                            <p id="fileNameDisplay" class="text-xs text-slate-600 font-medium bg-white px-3 py-1.5 border border-slate-200 rounded-lg inline-block max-w-[90%] truncate shadow-sm mt-2"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-4 pt-6 border-t border-slate-100 bg-slate-50/50 -mx-8 -mb-8 p-8 rounded-b-2xl">
                <button type="button" onclick="closeCaseModal()" class="flex-1 py-3.5 border border-slate-300 text-slate-700 rounded-xl font-bold text-sm hover:bg-white hover:border-slate-400 shadow-sm hover:shadow transition-all duration-200">
                    إلغاء الأمر
                </button>
                <button type="submit" class="flex-[2] py-3.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-check-circle ml-1"></i>
                    حفظ وتأكيد ملف القضية
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleUp {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .animate-scaleUp { animation: scaleUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('openAddCaseBtn');
        if (openBtn) openBtn.onclick = () => openAddCaseModal();

        // البحث الفوري + تصفية حسب الحالة (تبويبات)
        const searchInput = document.getElementById('caseSearchInput');
        const noRes = document.getElementById('noSearchResults');
        const caseTabs = document.querySelectorAll('.case-tab');
        let activeStatus = '';

        function styleTabs() {
            caseTabs.forEach(function(tab) {
                const isActive = (tab.getAttribute('data-status') || '') === activeStatus;
                const dot = tab.querySelector('.tab-dot');
                const badge = tab.querySelector('.tab-count');
                if (isActive) {
                    tab.classList.add('bg-white', 'text-slate-900', 'ring-1', 'ring-slate-200', 'shadow-sm');
                    tab.classList.remove('text-slate-500');
                    if (dot) dot.classList.remove('hidden');
                    if (badge) { badge.classList.add('bg-emerald-50', 'text-emerald-700'); badge.classList.remove('bg-slate-100', 'text-slate-400'); }
                } else {
                    tab.classList.remove('bg-white', 'text-slate-900', 'ring-1', 'ring-slate-200', 'shadow-sm');
                    tab.classList.add('text-slate-500');
                    if (dot) dot.classList.add('hidden');
                    if (badge) { badge.classList.remove('bg-emerald-50', 'text-emerald-700'); badge.classList.add('bg-slate-100', 'text-slate-400'); }
                }
            });
        }

        function updateTabCounts() {
            const cards = document.querySelectorAll('.case-card');
            const counts = { '': cards.length, pending: 0, open: 0, closed: 0, archived: 0 };
            cards.forEach(function(card) {
                const st = card.getAttribute('data-status') || '';
                if (counts[st] !== undefined) counts[st]++;
            });
            caseTabs.forEach(function(tab) {
                const s = tab.getAttribute('data-status') || '';
                const badge = tab.querySelector('.tab-count');
                if (badge) badge.textContent = (counts[s] !== undefined ? counts[s] : 0);
            });
        }

        // ====== الترقيم بدون إعادة تحميل ======
        const PER_PAGE = 6;
        const MIN_PAGES = 3;
        let currentPage = 1;
        const paginationBar = document.getElementById('paginationBar');
        const noPage = document.getElementById('noPageResults');
        const shownCount = document.getElementById('shownCount');
        const totalCount = document.getElementById('totalCount');

        function getMatching() {
            const term = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const matching = [];
            document.querySelectorAll('.case-card').forEach(function(card) {
                const hay = card.getAttribute('data-search') || '';
                const st = card.getAttribute('data-status') || '';
                const ok = hay.indexOf(term) !== -1 && (activeStatus === '' || st === activeStatus);
                if (ok) matching.push(card); else card.style.display = 'none';
            });
            return matching;
        }

        function navBtn(nav, disabled, icon) {
            const cls = disabled
                ? 'flex h-9 w-9 items-center justify-center rounded-full text-slate-300 ring-1 ring-slate-200 opacity-40 cursor-not-allowed'
                : 'flex h-9 w-9 items-center justify-center rounded-full text-slate-400 ring-1 ring-slate-200 transition hover:bg-white';
            return '<button type="button" data-nav="' + nav + '" ' + (disabled ? 'disabled' : '') + ' class="' + cls + '">' + icon + '</button>';
        }

        function renderPagination(totalPages) {
            if (!paginationBar) return;
            let html = navBtn('prev', currentPage <= 1, '<i class="fas fa-chevron-right text-xs"></i>');
            for (let p = 1; p <= totalPages; p++) {
                const active = p === currentPage;
                html += '<button type="button" data-page="' + p + '" class="page-num flex h-9 w-9 items-center justify-center rounded-full text-[13px] font-bold transition ' +
                    (active ? 'bg-blue-600 text-white' : 'text-slate-600 ring-1 ring-slate-200 hover:bg-white') + '">' + p + '</button>';
            }
            html += navBtn('next', currentPage >= totalPages, '<i class="fas fa-chevron-left text-xs"></i>');
            paginationBar.innerHTML = html;
            paginationBar.querySelectorAll('.page-num').forEach(function(b) {
                b.addEventListener('click', function() { currentPage = parseInt(this.getAttribute('data-page'), 10); render(); });
            });
            const prevBtn = paginationBar.querySelector('[data-nav="prev"]:not([disabled])');
            const nextBtn = paginationBar.querySelector('[data-nav="next"]:not([disabled])');
            if (prevBtn) prevBtn.addEventListener('click', function() { currentPage--; render(); });
            if (nextBtn) nextBtn.addEventListener('click', function() { currentPage++; render(); });
        }

        function render() {
            const matching = getMatching();
            const total = matching.length;
            const totalCards = document.querySelectorAll('.case-card').length;
            const totalPages = Math.max(MIN_PAGES, Math.ceil(total / PER_PAGE) || 1);
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            const start = (currentPage - 1) * PER_PAGE;
            matching.forEach(function(card, i) {
                card.style.display = (i >= start && i < start + PER_PAGE) ? '' : 'none';
            });
            const pageCount = matching.slice(start, start + PER_PAGE).length;
            const term = searchInput ? searchInput.value.trim() : '';
            const showNoRes = (total === 0 && term !== '' && totalCards > 0);
            const showNoPage = !showNoRes && pageCount === 0 && totalCards > 0;
            if (noRes) noRes.classList.toggle('hidden', !showNoRes);
            if (noPage) noPage.classList.toggle('hidden', !showNoPage);
            if (shownCount) shownCount.textContent = pageCount;
            if (totalCount) totalCount.textContent = totalCards;
            renderPagination(totalPages);
        }

        if (searchInput) searchInput.addEventListener('input', function() { currentPage = 1; render(); });
        caseTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                activeStatus = tab.getAttribute('data-status') || '';
                currentPage = 1;
                styleTabs();
                render();
            });
        });

        updateTabCounts();
        styleTabs();
        render();

        const fileInput = document.getElementById('caseFiles');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    document.getElementById('fileNameDisplay').textContent = this.files[0].name;
                    document.getElementById('uploadDefaultState').classList.add('hidden');
                    document.getElementById('uploadSuccessState').classList.remove('hidden');
                    document.getElementById('uploadWrapper').classList.remove('border-slate-300', 'bg-slate-50');
                    document.getElementById('uploadWrapper').classList.add('border-emerald-400', 'bg-emerald-50/40');
                } else {
                    resetUploadZone();
                }
            });
        }

        // معالجة البحث عن الموكل
        const searchBtn = document.getElementById('searchClientBtn');
        const emailInput = document.getElementById('searchClientEmail');
        
        if (searchBtn && emailInput) {
            searchBtn.addEventListener('click', async function() {
                const email = emailInput.value.trim();
                const errorEl = document.getElementById('clientSearchError');
                const errorText = document.getElementById('clientSearchErrorText');
                
                if (!email) {
                    errorText.textContent = 'يرجى إدخال البريد الإلكتروني للبحث';
                    errorEl.classList.remove('hidden');
                    return;
                }
                
                errorEl.classList.add('hidden');
                
                try {
                    // إضافة توكن الحماية CSRF
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                                      || document.querySelector('input[name="_token"]').value;
                                      
                    const response = await fetch("{{ route('lawyer.cases.search-client') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: email })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // عرض النتيجة وربط المعرف
                        document.getElementById('clientUserId').value = data.client.id;
                        document.getElementById('foundClientName').textContent = data.client.name;
                        document.getElementById('foundClientEmail').textContent = data.client.email;
                        document.getElementById('clientSearchResult').classList.remove('hidden');
                        
                        // تعبئة حقول الموكل تلقائياً إذا كانت فارغة
                        if (!document.getElementById('clientName').value) document.getElementById('clientName').value = data.client.name;
                        if (!document.getElementById('clientPhone').value && data.client.phone) document.getElementById('clientPhone').value = data.client.phone;
                    } else {
                        errorText.textContent = data.message || 'حدث خطأ في البحث';
                        errorEl.classList.remove('hidden');
                        document.getElementById('clientSearchResult').classList.add('hidden');
                        document.getElementById('clientUserId').value = '';
                    }
                } catch (error) {
                    errorText.textContent = 'حدث خطأ في الاتصال بالخادم';
                    errorEl.classList.remove('hidden');
                }
            });
            
            // إلغاء الربط
            document.getElementById('removeLinkedClientBtn').addEventListener('click', function() {
                document.getElementById('clientUserId').value = '';
                document.getElementById('clientSearchResult').classList.add('hidden');
                emailInput.value = '';
            });
        }
    });
    
    const modal = document.getElementById('caseModal');
    const modalTitle = document.getElementById('modalTitle');
    const caseForm = document.getElementById('caseForm');
    const methodField = document.getElementById('methodField');

    function resetUploadZone() {
        document.getElementById('uploadDefaultState').classList.remove('hidden');
        document.getElementById('uploadSuccessState').classList.add('hidden');
        const wrapper = document.getElementById('uploadWrapper');
        wrapper.classList.remove('border-emerald-400', 'bg-emerald-50/40');
        wrapper.classList.add('border-slate-300', 'bg-slate-50');
        document.getElementById('caseFiles').value = '';
    }
    
    function openAddCaseModal() {
        modalTitle.innerHTML = '<i class="fas fa-gavel text-[#3b82f6]"></i> إضافة قضية جديدة';
        caseForm.action = "{{ url('/lawyer/cases') }}";
        methodField.innerHTML = ''; 
        
        document.getElementById('clientName').value = '';
        document.getElementById('clientPhone').value = '';
        document.getElementById('clientUserId').value = '';
        document.getElementById('searchClientEmail').value = '';
        document.getElementById('clientSearchResult').classList.add('hidden');
        document.getElementById('clientSearchError').classList.add('hidden');
        document.getElementById('caseType').value = '';
        document.getElementById('caseStatus').value = 'pending';
        document.getElementById('caseDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('caseDetails').value = '';
        document.getElementById('caseCategory').value = '';
        document.getElementById('courtName').value = '';
        document.getElementById('opponentName').value = '';
        document.getElementById('claimAmount').value = '';
        document.getElementById('totalFees').value = '';
        document.getElementById('paidFees').value = '';
        
        resetUploadZone();
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }
    
    function openEditCaseModal(caseData) {
        modalTitle.innerHTML = '<i class="fas fa-edit text-[#3b82f6]"></i> تعديل بيانات ملف القضية';
        caseForm.action = "{{ url('/lawyer/cases') }}/" + caseData.id;
        methodField.innerHTML = '@method("PUT")';
        
        document.getElementById('clientName').value = caseData.client_name || '';
        document.getElementById('clientPhone').value = caseData.client_phone || '';
        document.getElementById('caseType').value = caseData.case_type || '';
        document.getElementById('caseStatus').value = caseData.status || 'pending';
        document.getElementById('caseDate').value = caseData.filing_date ? caseData.filing_date.split('T')[0] : '';
        document.getElementById('caseDetails').value = caseData.details || '';
        document.getElementById('caseCategory').value = caseData.case_category || '';
        document.getElementById('courtName').value = caseData.court_name || '';
        document.getElementById('opponentName').value = caseData.opponent_name || '';
        document.getElementById('claimAmount').value = caseData.claim_amount || '';
        document.getElementById('totalFees').value = caseData.total_fees || '';
        document.getElementById('paidFees').value = caseData.paid_fees || '';
        
        // ربط الموكل
        const userIdInput = document.getElementById('clientUserId');
        const searchResult = document.getElementById('clientSearchResult');
        document.getElementById('searchClientEmail').value = '';
        document.getElementById('clientSearchError').classList.add('hidden');
        
        if (caseData.user_id && caseData.user_id != caseData.lawyer_id && caseData.client) {
            userIdInput.value = caseData.user_id;
            document.getElementById('foundClientName').textContent = caseData.client.name;
            document.getElementById('foundClientEmail').textContent = caseData.client.email;
            searchResult.classList.remove('hidden');
        } else {
            userIdInput.value = '';
            searchResult.classList.add('hidden');
        }
        
        resetUploadZone();
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }
    
    function closeCaseModal() {
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeCaseModal();
    });

    // منطق إخفاء رسائل النجاح والخطأ تلقائياً بعد 5 ثوانٍ
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.flash-message');
    
    messages.forEach(function(msg) {
        setTimeout(function() {
            // إخفاء العنصر تدريجياً (تلاشي)
            msg.style.opacity = "0";
            msg.style.transform = "translateY(-10px)"; // حركة بسيطة للأعلى عند الاختفاء
            
            // إزالة العنصر نهائياً من الصفحة بعد انتهاء وقت التلاشي
            setTimeout(function() {
                msg.remove();
            }, 500); // 500 ميلي ثانية تطابق مدة الـ duration في كلاس Tailwind
        }, 5000); // 5000 ميلي ثانية = 5 ثوانٍ
    });
});
</script>
@endsection
