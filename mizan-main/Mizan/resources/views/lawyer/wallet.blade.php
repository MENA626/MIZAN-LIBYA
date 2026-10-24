@extends('layouts.lawyer')

@section('title', 'المحفظة المالية')

@section('lawyer_wallet_active', 'nav-active')

@section('content')
<div id="wallet-board" class="space-y-6">

    {{-- رسائل النجاح/الخطأ (نفس المنطق) --}}
    @if(session('success'))
    <div class="flash-message p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200/60 shadow-sm font-bold flex items-center gap-2 transition-opacity duration-500">
        <i class="fas fa-check-circle text-emerald-500 text-base"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="flash-message p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200/60 shadow-sm font-bold flex items-center gap-2 transition-opacity duration-500">
        <i class="fas fa-exclamation-circle text-rose-500 text-base"></i> {{ session('error') }}
    </div>
@endif

    {{-- ==========================================
       اللوحة الرئيسية (نفس تصميم مكتبة القضايا)
       ========================================== --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-[#F4F5FA] p-5 sm:p-7 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.25)]">

        {{-- الترويسة --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-600/25">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <div>
                    <h3 class="text-slate-900 text-2xl font-black leading-tight">المحفظة المالية</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">إدارة رصيدك وسحب الأرباح</p>
                </div>
            </div>
            <button id="withdrawBtn" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white px-5 py-2.5 text-[13px] font-bold shadow-lg shadow-blue-600/25 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                <i class="fas fa-arrow-down"></i> طلب سحب
            </button>
        </div>

        {{-- ==========================================
           كروت الرصيد
           ========================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-7">

            {{-- الرصيد المتاح — كرت رئيسي مميّز --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-5 text-white shadow-lg shadow-blue-700/30">
                <div class="absolute -left-8 -top-8 w-28 h-28 rounded-full bg-white/10"></div>
                <div class="absolute -left-3 bottom-2 w-16 h-16 rounded-full bg-white/5"></div>
                <div class="relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center mb-3">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                    <p class="text-blue-100 text-xs font-medium mb-1">الرصيد المتاح</p>
                    <h3 class="text-2xl font-black text-white">{{ number_format($actualAvailable ?? 0, 2) }} <span class="text-sm font-bold">د.ل</span></h3>

                    <div class="mt-3 pt-2.5 border-t border-white/15 flex items-center justify-between">
                        <span class="text-blue-100 text-xs">الحساب البنكي:</span>
                        @if($profile && $profile->bank_name && $profile->account_number)
                            <span class="inline-flex items-center gap-1 text-emerald-200 text-xs font-bold"><i class="fas fa-check-circle"></i> مرتبط</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-amber-200 text-xs font-bold"><i class="fas fa-exclamation-circle"></i> غير مكتمل</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- قيد السحب --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] hover:border-slate-200 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                    <i class="fas fa-clock"></i>
                </div>
                <p class="text-slate-400 text-xs font-medium mb-1">قيد السحب</p>
                <h3 class="text-2xl font-black text-slate-800">{{ number_format($pendingWithdrawals ?? 0, 2) }} <span class="text-sm text-slate-400 font-bold">د.ل</span></h3>
            </div>

            {{-- تم سحبها --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] hover:border-slate-200 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p class="text-slate-400 text-xs font-medium mb-1">تم سحبها</p>
                <h3 class="text-2xl font-black text-emerald-600">{{ number_format($completedWithdrawals ?? 0, 2) }} <span class="text-sm text-slate-400 font-bold">د.ل</span></h3>
            </div>

            {{-- رصيد معلق --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] hover:border-slate-200 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                    <i class="fas fa-lock"></i>
                </div>
                <p class="text-slate-400 text-xs font-medium mb-1">رصيد معلق</p>
                <h3 class="text-2xl font-black text-amber-600">{{ number_format($escrowBalance ?? 0, 2) }} <span class="text-sm text-slate-400 font-bold">د.ل</span></h3>
            </div>
        </div>

        {{-- إجمالي الأرباح --}}
        <div class="mt-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-lg"></i>
                </div>
                <div>
                    <p class="text-slate-800 font-bold">إجمالي الأرباح</p>
                    <p class="text-slate-400 text-xs mt-0.5">جميع الحركات المالية</p>
                </div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 px-6 py-2.5">
                <h3 class="text-2xl font-black text-blue-600">{{ number_format(($actualAvailable + $completedWithdrawals + $pendingWithdrawals) ?? 0, 2) }} <span class="text-sm text-slate-400 font-bold">د.ل</span></h3>
            </div>
        </div>

        {{-- ==========================================
           سجل العمليات
           ========================================== --}}
        <h4 class="mb-4 mt-8 flex items-center gap-2 text-[15px] font-black text-slate-800">
            <i class="fas fa-history text-blue-600"></i> سجل العمليات
        </h4>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">التاريخ</th>
                            <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">النوع</th>
                            <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">الوصف</th>
                            <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">المبلغ</th>
                            <th class="text-right px-5 py-3 text-xs font-bold text-slate-500">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-slate-400">{{ $tx->created_at->format('Y-m-d') }}</td>
                            <td class="px-5 py-3.5 text-sm font-bold">
                                @if($tx->type === 'credit')
                                    <span class="inline-flex items-center gap-1 text-emerald-600"><i class="fas fa-plus text-xs"></i> إيداع</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-rose-500"><i class="fas fa-minus text-xs"></i> سحب</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $tx->description }}</td>
                            <td class="px-5 py-3.5 text-sm font-bold {{ $tx->type === 'credit' ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} د.ل
                            </td>
                            <td class="px-5 py-3.5">
                                @if($tx->status === 'completed')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">مكتملة</span>
                                @elseif($tx->status === 'available')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-blue-50 text-blue-600">متاح</span>
                                @elseif($tx->status === 'escrow')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-50 text-amber-600">محفوظ</span>
                                @elseif($tx->status === 'frozen')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-rose-50 text-rose-500">مجمّد</span>
                                @elseif($tx->status === 'pending')
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-purple-50 text-purple-600">قيد المراجعة</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400 text-sm font-bold">
                                <span class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                                    <i class="fas fa-receipt text-xl"></i>
                                </span>
                                لا توجد حركات مالية
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-400 font-medium">
                    عرض {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} من {{ $transactions->total() }}
                </p>
                <nav class="flex items-center gap-1.5">
                    {{-- السابق --}}
                    @if($transactions->onFirstPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-300 cursor-not-allowed">
                            <i class="fas fa-chevron-right text-[11px]"></i>
                        </span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:border-slate-300 transition">
                            <i class="fas fa-chevron-right text-[11px]"></i>
                        </a>
                    @endif

                    {{-- أرقام الصفحات (دواير) --}}
                    @foreach($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        @if($page == $transactions->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white text-sm font-bold shadow-lg shadow-blue-600/25">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 hover:border-slate-300 transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- التالي --}}
                    @if($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:border-slate-300 transition">
                            <i class="fas fa-chevron-left text-[11px]"></i>
                        </a>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-50 text-slate-300 cursor-not-allowed">
                            <i class="fas fa-chevron-left text-[11px]"></i>
                        </span>
                    @endif
                </nav>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- ==========================================
   نافذة طلب السحب (نفس المنطق والـ IDs)
   ========================================== --}}
<div id="withdrawModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden items-center justify-center transition-all p-4">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                <i class="fas fa-arrow-down text-blue-600"></i> طلب سحب
            </h3>
            <button onclick="closeWithdrawModal()" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 p-4 mb-5 text-white shadow-lg shadow-blue-700/25">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100 text-xs font-medium">الرصيد المتاح:</p>
                    <p class="text-white text-xl font-black mt-0.5" id="availableBalance">{{ number_format($actualAvailable ?? 0, 2) }} <span class="text-sm font-bold">د.ل</span></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fas fa-wallet text-white"></i>
                </div>
            </div>
        </div>

        <form id="withdrawForm" action="{{ route('lawyer.wallet.withdraw') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold mb-1.5 text-slate-700">المبلغ (د.ل)</label>
                <input type="number" name="amount" id="withdrawAmount" placeholder="مثال: 150" required min="1" max="{{ $actualAvailable ?? 0 }}" step="0.5"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-sm transition-colors">
                <p class="text-xs text-slate-400 mt-1.5">الحد الأدنى: 1.00 د.ل</p>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1.5 text-slate-700">طريقة التحويل</label>
                <div class="px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-600 text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-university text-blue-600"></i> تحويل بنكي
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeWithdrawModal()" class="flex-1 py-2.5 border border-slate-100 bg-slate-50 text-slate-700 rounded-xl font-bold hover:bg-slate-100 text-sm transition-colors">إلغاء</button>
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-blue-600/25 text-sm transition-all">
                    <i class="fas fa-paper-plane text-xs"></i> إرسال الطلب
                </button>
            </div>
        </form>

        <p class="text-center text-xs text-slate-400 mt-4 pt-3 border-t border-slate-100">
            <i class="fas fa-clock text-amber-500"></i> مدة المراجعة: حوالي 48 ساعة عمل
        </p>
    </div>
</div>

@push('styles')
<style>
    /* الخط: كايرو (نفس تيم مكتبة القضايا) — مع استثناء أيقونات FontAwesome حتى لا تختفي */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
    #wallet-board, #wallet-board *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]),
    #withdrawModal, #withdrawModal *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]) {
        font-family: 'Cairo', 'Tajawal', sans-serif !important;
    }
</style>
@endpush

<script>
    const withdrawModal = document.getElementById('withdrawModal');

    function openWithdrawModal() {
        const currentBalance = {{ $actualAvailable ?? 0 }};
        if (currentBalance < 1) {
            alert('الرصيد المتاح غير كافٍ لإجراء سحب.');
            return;
        }
        withdrawModal.classList.remove('hidden');
        withdrawModal.classList.add('flex');
    }

    function closeWithdrawModal() {
        withdrawModal.classList.add('hidden');
        withdrawModal.classList.remove('flex');
    }

    document.getElementById('withdrawBtn').addEventListener('click', openWithdrawModal);

    withdrawModal.addEventListener('click', (e) => {
        if (e.target === withdrawModal) closeWithdrawModal();
    });

    // منطق إخفاء رسائل النجاح/الخطأ بعد 5 ثوانٍ
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('.flash-message');
        messages.forEach(function(msg) {
            setTimeout(function() {
                // نبدأ عملية التلاشي
                msg.style.opacity = "0";
                
                // إزالة العنصر بعد انتهاء مدة التلاشي (0.5 ثانية)
                setTimeout(function() {
                    msg.remove();
                }, 500);
            }, 5000); // 5000 ميلي ثانية = 5 ثوانٍ
        });
    });
</script>
@endsection
