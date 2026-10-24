@extends('layouts.admin')

@section('title', 'الإدارة المالية والبلاغات')
@section('page_title', 'الإدارة المالية والبلاغات')
@section('page_subtitle', 'الأرباح وطلبات السحب والبلاغات')

@section('admin_finance_active', 'nav-active')

@section('content')
<div class="space-y-6">

    {{-- رسائل النظام --}}
    @if(session('success'))
        <div class="flash-message p-3.5 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 font-semibold flex items-center gap-2 transition-opacity duration-500">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash-message p-3.5 text-sm text-red-700 rounded-xl bg-red-50 border border-red-100 font-semibold flex items-center gap-2 transition-opacity duration-500">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- بطاقة أرباح المنصة --}}
    <div class="relative overflow-hidden rounded-2xl p-6 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-800 text-white shadow-xl shadow-blue-900/20 ring-1 ring-white/10 flex justify-between items-center">
        <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-sky-300/20 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-8 w-44 h-44 rounded-full bg-indigo-400/20 blur-2xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/0 to-white/10 pointer-events-none"></div>
        <div class="relative">
            <p class="text-blue-100/80 text-xs font-medium flex items-center gap-1.5"><i class="fas fa-coins"></i> أرباح المنصة (عمولات 10%)</p>
            <p class="text-3xl font-bold mt-1.5 tracking-tight drop-shadow-sm">{{ number_format($platformRevenue, 2) }} <span class="text-base font-medium text-blue-100/70">د.ل</span></p>
        </div>
        <div class="relative w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center ring-1 ring-white/15">
            <i class="fas fa-chart-line text-xl"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- طلبات السحب --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fas fa-hand-holding-usd text-sm"></i></span>
                    <h3 class="text-[15px] font-bold text-slate-800">طلبات السحب</h3>
                </div>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full ring-1 bg-emerald-50 text-emerald-600 ring-emerald-100">{{ $withdrawalRequests->count() }} طلب</span>
            </div>
            <div class="divide-y divide-slate-50 max-h-[460px] overflow-y-auto">
                @forelse($withdrawalRequests as $req)
                <div class="p-4 hover:bg-slate-50/60 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{ $req->user->name }}</p>
                            <p class="text-[13px] text-slate-600 mt-0.5">المبلغ: <strong class="text-emerald-600">{{ number_format($req->amount, 2) }} د.ل</strong></p>
                        </div>
                        <span class="text-xs text-slate-500">{{ $req->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 text-[13px] text-slate-700 mb-3 space-y-1 border border-slate-100">
                        <p class="flex items-center gap-1.5">
                            <i class="fas fa-landmark text-slate-400 text-xs"></i>
                            <span><strong class="text-slate-700">البنك:</strong> {{ $req->user->lawyerProfile->bank_name ?? '—' }}</span>
                        </p>
                        <p><strong class="text-slate-700">الاسم:</strong> {{ $req->user->lawyerProfile->account_name ?? '—' }}</p>
                        <p><strong class="text-slate-700">رقم الحساب:</strong> {{ $req->user->lawyerProfile->account_number ?? '—' }}</p>
                    </div>
                    <form id="form-withdraw-{{ $req->id }}" action="{{ route('admin.finance.withdrawals.approve', $req->id) }}" method="POST">
                        @csrf
                        <button type="button" onclick="openWithdrawModal(document.getElementById('form-withdraw-{{ $req->id }}'))"
                            class="w-full inline-flex items-center justify-center gap-1.5 bg-gradient-to-l from-blue-600 to-blue-500 text-white py-2.5 rounded-xl text-xs font-semibold hover:from-blue-700 hover:to-blue-600 active:scale-[0.99] transition-all shadow-sm shadow-blue-600/20">
                            <i class="fas fa-check-circle"></i> تأكيد التحويل
                        </button>
                    </form>
                </div>
                @empty
                <div class="p-12 text-center text-slate-400">
                    <i class="fas fa-check-circle text-2xl block mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">لا توجد طلبات سحب</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- البلاغات --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fas fa-exclamation-triangle text-sm"></i></span>
                    <h3 class="text-[15px] font-bold text-slate-800">البلاغات</h3>
                </div>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full ring-1 bg-amber-50 text-amber-600 ring-amber-100">{{ $disputes->count() }} بلاغ</span>
            </div>
            <div class="divide-y divide-slate-50 max-h-[460px] overflow-y-auto">
                @forelse($disputes as $dispute)
                <div class="p-4 hover:bg-slate-50/60 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">بلاغ ضد المحامي: {{ $dispute->lawyer->name }}</p>
                            <p class="text-[13px] text-slate-600 mt-0.5">مقدم من العميل: {{ $dispute->user->name }}</p>
                        </div>
                        <span class="text-xs text-slate-500">{{ $dispute->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-[13px] text-slate-700 mb-3">
                        <p class="font-semibold text-amber-700 mb-1">السبب:</p>
                        <p class="leading-relaxed">{{ $dispute->reason }}</p>
                    </div>

                    @if($dispute->disputable_type === 'App\Models\PaidConsultation' && $dispute->disputable_id)
                        <a href="{{ route('admin.disputes.view-chat', $dispute->disputable_id) }}" target="_blank"
                           class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-50 text-blue-600 border border-blue-100 py-2.5 rounded-xl text-xs font-semibold hover:bg-blue-100 transition-colors mb-2">
                            <i class="fas fa-comments"></i> عرض سجل المحادثة للنزاع
                        </a>
                    @endif

                    <button type="button" onclick="document.getElementById('resolve-form-{{ $dispute->id }}').classList.toggle('hidden')"
                        class="w-full inline-flex items-center justify-center gap-1.5 border border-slate-200 text-slate-700 py-2.5 rounded-xl text-xs font-semibold hover:bg-slate-50 transition-colors">
                        <i class="fas fa-gavel"></i> الفصل في البلاغ
                    </button>

                    <form id="resolve-form-{{ $dispute->id }}" action="{{ route('admin.finance.disputes.resolve', $dispute->id) }}" method="POST" class="hidden bg-slate-50 p-3 rounded-xl border border-slate-200 mt-2 space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">القرار:</label>
                            <select name="decision" class="w-full p-2 border border-slate-200 rounded-lg text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" required>
                                <option value="">اختر القرار...</option>
                                <option value="refund_client">إرجاع المبلغ للعميل</option>
                                <option value="release_to_lawyer">رفض البلاغ وتحرير الأتعاب</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات:</label>
                            <textarea name="admin_notes" rows="2" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-600 text-white py-2.5 rounded-xl font-semibold text-xs hover:bg-amber-700 transition-colors">
                            تنفيذ القرار
                        </button>
                    </form>
                </div>
                @empty
                <div class="p-12 text-center text-slate-400">
                    <i class="fas fa-check-circle text-2xl block mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">لا توجد بلاغات</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- نافذة تأكيد السحب المنبثقة --}}
<div id="confirmWithdrawModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div class="relative bg-white rounded-3xl w-full max-w-sm p-8 shadow-2xl flex flex-col items-center text-center space-y-4">
        <button type="button" onclick="closeWithdrawModal()" class="absolute top-4 left-4 w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mb-1">
            <i class="fas fa-hand-holding-usd text-emerald-500 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800">تأكيد تحويل المبلغ</h3>
        <p class="text-sm text-slate-500 leading-relaxed">هل أنت متأكد من إتمام عملية التحويل؟ سيتم تغيير حالة الطلب إلى "مكتمل" وإبلاغ المحامي.</p>
        <div class="flex gap-3 w-full pt-2">
            <button type="button" onclick="closeWithdrawModal()" class="flex-1 px-4 py-3 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl font-bold text-sm transition-colors border border-slate-100">إلغاء</button>
            <button type="button" id="confirmWithdrawBtn" class="flex-1 px-4 py-3 bg-gradient-to-l from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-xl font-bold text-sm transition-colors shadow-sm shadow-blue-600/20">نعم، تأكيد التحويل</button>
        </div>
    </div>
</div>

<script>
    let activeWithdrawForm = null;

    function openWithdrawModal(form) {
        activeWithdrawForm = form;
        document.getElementById('confirmWithdrawModal').classList.remove('hidden');
    }

    function closeWithdrawModal() {
        document.getElementById('confirmWithdrawModal').classList.add('hidden');
        activeWithdrawForm = null;
    }

    document.getElementById('confirmWithdrawBtn').addEventListener('click', function () {
        if (activeWithdrawForm) activeWithdrawForm.submit();
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.flash-message').forEach(function (msg) {
            setTimeout(function () {
                msg.style.opacity = '0';
                setTimeout(function () { msg.remove(); }, 500);
            }, 5000);
        });
    });
</script>
@endsection