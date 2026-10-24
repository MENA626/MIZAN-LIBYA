@extends('layouts.lawyer')

@section('title', 'إدارة الإستشارات ')

@section('lawyer_consultations_active', 'nav-active')
@section('content')
<div id="consultations-board" class="space-y-6">

    {{-- رسائل النجاح/الخطأ (نفس آلية الإخفاء التلقائي) --}}
    <div id="flash-messages">
        @if(session('success'))
            <div class="alert-message p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200/60 shadow-sm font-bold flex items-center gap-2 transition-all duration-500 ease-out">
                <i class="fas fa-check-circle text-emerald-500 text-base"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-message p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200/60 shadow-sm font-bold flex items-center gap-2 transition-all duration-500 ease-out">
                <i class="fas fa-exclamation-circle text-rose-500 text-base"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- ==========================================
       اللوحة الرئيسية (نفس تصميم مكتبة القضايا)
       ========================================== --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-[#F4F5FA] p-5 sm:p-7 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.25)]">

        {{-- الترويسة: العنوان --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-600/25">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h3 class="text-slate-900 text-2xl font-black leading-tight">إدارة الإستشارات</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">إدارة طلبات الإستشارة وتسعيرها</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white px-4 py-2 text-[13px] font-bold shadow-lg shadow-blue-600/25">
                <i class="fas fa-file-alt"></i> {{ $incomingConsultations->count() }} استشارة جديدة
            </span>
        </div>

        {{-- ==========================================
           قسم: إستشارات جديدة
           ========================================== --}}
        <h4 class="mb-4 mt-7 flex items-center gap-2 text-[15px] font-black text-slate-800">
            <i class="fas fa-inbox text-blue-600"></i> طلبات إستشارات جديدة
        </h4>

        <div class="space-y-4">
            @if($incomingConsultations && $incomingConsultations->count() > 0)
                @foreach($incomingConsultations as $consultation)
                    {{-- كرت استشارة جديدة --}}
                    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-5 rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition hover:border-slate-200 hover:shadow-md">

                        <div class="flex-1 space-y-3 w-full">
                            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 font-bold">{{ $consultation->legal_category ?? 'استشارة قانونية' }}</span>
                                @if($consultation->urgency_level)
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-500 font-bold">{{ $consultation->urgency_level }}</span>
                                @endif
                                <span class="text-slate-400 font-medium">{{ $consultation->created_at->diffForHumans() }}</span>
                            </div>

                            <h4 class="text-slate-800 font-bold text-base">{{ $consultation->title }}</h4>

                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl">
                                <p class="text-slate-600 text-sm leading-relaxed">
                                    <span class="font-bold text-slate-700 block mb-1 text-xs">تفاصيل الطلب:</span>
                                    {{ $consultation->description }}
                                </p>
                            </div>

                            <div class="text-xs text-slate-500 flex items-center gap-1.5">
                                <i class="fas fa-user text-slate-400"></i>
                                <span class="font-bold">{{ $consultation->client->name ?? 'عميل' }}</span>
                            </div>
                        </div>

                        <div class="w-full xl:w-auto p-3 bg-slate-50 rounded-2xl flex flex-wrap lg:flex-nowrap items-center gap-2 shrink-0">
                            <form action="{{ route('lawyer.consultations.review', $consultation->id) }}" method="POST" class="flex-1 lg:flex-none flex items-center gap-2 w-full sm:w-auto">
                                @csrf
                                <div class="relative flex-1 lg:w-24">
                                    <input type="number" name="price" required min="0" placeholder="الأتعاب"
                                           class="w-full lg:w-24 px-2 py-2 bg-white rounded-lg text-sm font-bold text-slate-800 outline-none border border-slate-200 focus:border-blue-500 text-center">
                                    <span class="absolute left-2 top-2.5 text-[11px] text-slate-400">د.ل</span>
                                </div>
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" class="flex-shrink-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-xs transition-colors whitespace-nowrap">
                                    <i class="fas fa-check text-xs"></i> اعتماد
                                </button>
                            </form>

                            <form action="{{ route('lawyer.consultations.review', $consultation->id) }}" method="POST" class="w-full lg:w-auto">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                {{-- قمنا بإزالة onclick التابع للمتصفح وأضفنا دالة خاصة بنا --}}
                                <button type="button" onclick="openRejectModal(this.closest('form'))" class="w-full px-4 py-2 bg-white hover:bg-rose-50 text-rose-500 rounded-lg font-bold text-xs transition-colors border border-slate-200 flex justify-center items-center gap-1">
                                    <i class="fas fa-times text-xs"></i> رفض
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- حالة عدم وجود استشارات جديدة --}}
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white py-12 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                        <i class="fas fa-inbox text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600">لا توجد طلبات إستشارات جديدة</p>
                </div>
            @endif
        </div>

        {{-- ==========================================
           قسم: الأرشيف
           ========================================== --}}
        <h4 class="mb-4 mt-8 flex items-center gap-2 text-[15px] font-black text-slate-800">
            <i class="fas fa-archive text-slate-400"></i> الأرشيف
        </h4>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            @if($archiveConsultations->isEmpty())
                <div class="py-12 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600">لا يوجد استشارات في الأرشيف</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">الاستشارة / الموكل</th>
                                <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">الأتعاب</th>
                                <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">التاريخ</th>
                                <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">الحالة</th>
                                <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($archiveConsultations as $archive)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="text-sm font-bold text-slate-800">{{ $archive->title }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $archive->client->name ?? 'عميل' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm font-bold text-emerald-600">
                                        {{ $archive->price ? $archive->price . ' د.ل' : '---' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-400">{{ $archive->created_at->format('Y-m-d') }}</td>
                                    <td class="px-5 py-3.5">
                                        @if($archive->status == 'paid')
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">مدفوعة</span>
                                        @elseif($archive->status == 'accepted')
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-blue-50 text-blue-600">بانتظار الدفع</span>
                                        @elseif($archive->status == 'rejected')
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-rose-50 text-rose-500">مرفوضة</span>
                                        @elseif($archive->status == 'completed')
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-green-50 text-green-600">مكتملة</span>
                                        @else
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 text-slate-500">{{ $archive->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if(in_array($archive->status, ['paid', 'active', 'completed']))
                                            <a href="{{ route('lawyer.chat') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[11px] font-bold transition-colors">
                                                <i class="fas fa-comment-dots"></i> محادثة
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-300 font-bold"><i class="fas fa-lock"></i> غير متاحة</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- نافذة الرفض المنبثقة (كما هي دون تعديل في المنطق) --}}
<div id="rejectModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl w-full max-w-sm p-8 shadow-2xl flex flex-col items-center text-center space-y-4">
        {{-- الأيقونة --}}
        <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mb-2">
            <i class="fas fa-exclamation text-rose-500 text-3xl"></i>
        </div>

        <h3 class="text-xl font-bold text-slate-800">رفض الاستشارة</h3>
        <p class="text-sm text-slate-500 leading-relaxed">
            هل أنت متأكد من رفض هذه الاستشارة؟ سيتم رفض الطلب وإرساله إلى الأرشيف.
        </p>

        {{-- الأزرار --}}
        <div class="flex gap-3 w-full pt-4">
            <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl font-bold text-sm transition-colors border border-slate-100">
                إلغاء
            </button>
            <button type="button" id="confirmRejectBtn" class="flex-1 px-4 py-3 bg-rose-500 hover:bg-rose-600 text-white rounded-xl font-bold text-sm transition-colors">
                نعم، تأكيد
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* الخط: كايرو (نفس تيم مكتبة القضايا) */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
    /* خط كايرو على كل العناصر ما عدا أيقونات FontAwesome حتى لا تختفي */
    #consultations-board, #consultations-board *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]),
    #rejectModal, #rejectModal *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]) {
        font-family: 'Cairo', 'Tajawal', sans-serif !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // 1. منطق المودال
    let currentForm = null;

    function openRejectModal(form) {
        currentForm = form;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        currentForm = null;
    }

    document.getElementById('confirmRejectBtn').addEventListener('click', function() {
        if(currentForm) {
            currentForm.submit();
        }
    });

    // 2. منطق إخفاء الرسائل بعد 5 ثوانٍ
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('.alert-message');
        messages.forEach(function(msg) {
            setTimeout(function() {
                msg.style.transition = "opacity 0.5s ease";
                msg.style.opacity = "0";
                setTimeout(function() {
                    msg.remove();
                }, 500);
            }, 5000); // 5 ثوانٍ
        });
    });
</script>
@endpush
@endsection
