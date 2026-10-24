@extends('layouts.admin')

@section('title', 'تفاصيل القضية')
@section('page_title', 'تفاصيل القضية')
@section('page_subtitle', 'مراجعة القضية والمستندات')

@section('admin_cases_active', 'nav-active')

@section('content')
<div class="space-y-6">

    {{-- رأس الصفحة --}}
    <div class="flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.all-cases') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:text-slate-700 hover:border-slate-300 transition-colors shadow-sm">
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
            <h1 class="text-lg font-bold text-slate-800">#C-{{ str_pad($case->id, 3, '0', STR_PAD_LEFT) }} - {{ $case->title ?? 'بدون عنوان' }}</h1>
        </div>
    </div>

    <div class="space-y-5">

        {{-- معلومات القضية (عرض كامل) — والسجل بداخلها --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-info-circle text-sm"></i></span>
                <h3 class="text-[15px] font-bold text-slate-800">معلومات القضية</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-4 gap-y-5">
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 mb-1">رقم القضية</p>
                        <p class="font-bold text-slate-800 text-sm">{{ $case->case_number ?? '#C-'.str_pad($case->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 mb-1">الحالة</p>
                        @if($case->status == 'pending')
                            <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-100">قيد المراجعة</span>
                        @elseif($case->status == 'active')
                            <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-100">قيد المعالجة</span>
                        @elseif($case->status == 'closed' || $case->status == 'completed')
                            <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">مكتملة</span>
                        @else
                            <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200">{{ $case->status }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 mb-1">تاريخ الرفع</p>
                        <p class="font-bold text-slate-800 text-sm">{{ $case->filing_date ? $case->filing_date->format('d M Y') : $case->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 mb-1">آخر تحديث</p>
                        <p class="font-bold text-slate-800 text-sm">{{ $case->updated_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 mb-1">النوع</p>
                        <p class="font-bold text-slate-800 text-sm">{{ $case->case_type ?? 'قضية' }}</p>
                    </div>
                </div>

                @if($case->details || $case->description)
                <div class="border-t border-slate-100 pt-4 mt-5">
                    <p class="text-[11px] font-medium text-slate-500 mb-1.5">تفاصيل القضية</p>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $case->details ?? $case->description }}</p>
                </div>
                @endif

                {{-- السجل بداخل بطاقة معلومات القضية --}}
                <div class="border-t border-slate-100 pt-5 mt-5">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><i class="fas fa-history text-sm"></i></span>
                        <h3 class="text-[15px] font-bold text-slate-800">السجل</h3>
                    </div>
                    <div class="relative space-y-5 before:absolute before:top-1 before:bottom-1 before:right-[13px] before:w-px before:bg-slate-100">
                        <div class="relative flex gap-3">
                            <span class="relative z-10 w-7 h-7 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center ring-4 ring-white shrink-0"><i class="fas fa-user-check text-[11px]"></i></span>
                            <div>
                                <p class="text-slate-700 text-[13px] font-semibold">تم إنشاء القضية</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $case->created_at->format('d M Y - H:i') }}</p>
                            </div>
                        </div>
                        @if($case->updated_at->gt($case->created_at))
                        <div class="relative flex gap-3">
                            <span class="relative z-10 w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center ring-4 ring-white shrink-0"><i class="fas fa-sync-alt text-[11px]"></i></span>
                            <div>
                                <p class="text-slate-700 text-[13px] font-semibold">آخر تحديث للحالة</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $case->updated_at->format('d M Y - H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- الأطراف (عرض كامل) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center"><i class="fas fa-users text-sm"></i></span>
                <h3 class="text-[15px] font-bold text-slate-800">الأطراف</h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- الموكل --}}
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-user text-xs"></i></span>
                            <p class="text-[11px] font-semibold text-slate-500">الموكل</p>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">{{ $case->client_name ?? 'غير محدد' }}</p>
                        <p class="text-[13px] text-slate-600 mt-1.5 flex items-center gap-1.5"><i class="fas fa-phone-alt text-slate-400 text-xs"></i> {{ $case->client_phone ?? 'بدون هاتف' }}</p>
                    </div>
                    {{-- المحامي --}}
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center"><i class="fas fa-user-tie text-xs"></i></span>
                            <p class="text-[11px] font-semibold text-slate-500">المحامي</p>
                        </div>
                        <p class="font-bold text-slate-800 text-sm">{{ $case->lawyer->name ?? 'غير محدد' }}</p>
                        <p class="text-[13px] text-slate-600 mt-1.5 flex items-center gap-1.5"><i class="fas fa-phone-alt text-slate-400 text-xs"></i> {{ $case->lawyer->phone ?? 'بدون هاتف' }}</p>
                        <p class="text-[13px] text-slate-600 mt-1 flex items-center gap-1.5"><i class="fas fa-envelope text-slate-400 text-xs"></i> {{ $case->lawyer->email ?? 'بدون بريد' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection