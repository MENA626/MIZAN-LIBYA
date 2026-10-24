@extends('layouts.admin')

@section('title', 'لوحة التحكم')
@section('page_title', 'لوحة التحكم')
@section('page_subtitle', 'مرحباً بعودتك، إليك ملخص النشاطات')

@section('admin_dashboard_active', 'nav-active')

@section('content')
<div class="space-y-6">

    {{-- البطاقات الإحصائية + بطاقة الأرباح --}}
    <div class="flex flex-col lg:flex-row gap-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">

            {{-- المستخدمون --}}
            <div class="group relative overflow-hidden rounded-2xl p-5 border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-users absolute -bottom-4 -left-3 text-[88px] text-blue-600 opacity-10 group-hover:opacity-[0.16] transition-all pointer-events-none"></i>
                <div class="relative">
                    <p class="text-[13px] font-semibold mb-1 text-slate-600">المستخدمون</p>
                    <p class="text-[28px] font-bold text-slate-900 leading-tight tracking-tight">{{ number_format($totalUsers) }}</p>
                    <p class="text-[13px] font-semibold mt-1.5 text-emerald-600"><i class="fas fa-arrow-up text-[10px]"></i> نشط</p>
                </div>
            </div>

            {{-- المحامون --}}
            <div class="group relative overflow-hidden rounded-2xl p-5 border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-user-tie absolute -bottom-4 -left-3 text-[88px] text-violet-600 opacity-10 group-hover:opacity-[0.16] transition-all pointer-events-none"></i>
                <div class="relative">
                    <p class="text-[13px] font-semibold mb-1 text-slate-600">المحامون</p>
                    <p class="text-[28px] font-bold text-slate-900 leading-tight tracking-tight">{{ number_format($totalLawyers) }}</p>
                    <p class="text-[13px] font-semibold mt-1.5 text-amber-600">
                        @if($pendingLawyers > 0)
                            <i class="fas fa-clock text-[10px]"></i> {{ $pendingLawyers }} بانتظار التوثيق
                        @else
                            جميعهم موثقون
                        @endif
                    </p>
                </div>
            </div>

            {{-- القضايا --}}
            <div class="group relative overflow-hidden rounded-2xl p-5 border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-folder-open absolute -bottom-4 -left-3 text-[88px] text-blue-600 opacity-10 group-hover:opacity-[0.16] transition-all pointer-events-none"></i>
                <div class="relative">
                    <p class="text-[13px] font-semibold mb-1 text-slate-600">القضايا</p>
                    <p class="text-[28px] font-bold text-slate-900 leading-tight tracking-tight">{{ number_format($totalCases) }}</p>
                    <p class="text-[13px] font-semibold mt-1.5 text-slate-600">الإجمالي</p>
                </div>
            </div>
        </div>

        {{-- بطاقة أرباح المنصة --}}
        <div class="lg:w-[300px] shrink-0">
            <div class="relative h-full min-h-[120px] overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-800 shadow-xl shadow-blue-900/30 ring-1 ring-white/10">
                <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-sky-300/20 blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -right-8 w-44 h-44 rounded-full bg-indigo-400/20 blur-2xl pointer-events-none"></div>
                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/0 to-white/10 pointer-events-none"></div>
                <div class="relative p-5 flex flex-col justify-between h-full text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-blue-100/80 mb-1.5 flex items-center gap-1.5"><i class="fas fa-coins"></i> أرباح المنصة</p>
                            <p class="text-[28px] font-bold leading-none tracking-tight drop-shadow-sm">{{ number_format($totalCommissions, 0) }} <span class="text-sm font-medium text-blue-100/80">د.ل</span></p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center ring-1 ring-white/15">
                            <i class="fas fa-wallet text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-blue-100/70 mt-4 flex items-center gap-1.5"><i class="fas fa-chart-line"></i> إجمالي العمولات</p>
                </div>
            </div>
        </div>
    </div>

    {{-- طلبات التوثيق + الرسم البياني --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- آخر طلبات التوثيق --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-id-card text-sm"></i></span>
                    <h3 class="text-[15px] font-bold text-slate-800">آخر طلبات التوثيق</h3>
                </div>
                <a href="{{ route('admin.verification') }}" class="text-xs font-semibold text-blue-600 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($pendingVerifications as $lawyer)
                <div class="px-5 py-3.5 flex items-center justify-between hover:bg-slate-50/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-user text-sm"></i></div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{ $lawyer->name }}</p>
                            <p class="text-[13px] text-slate-500">{{ $lawyer->lawyerProfile?->specialty?->name ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-100">بانتظار المراجعة</span>
                </div>
                @empty
                <div class="text-center py-10 text-slate-400">
                    <i class="fas fa-check-circle text-2xl block mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">لا توجد طلبات جديدة</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- الأرباح (رسم بياني) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-chart-line text-sm"></i></span>
                    <h3 class="text-[15px] font-bold text-slate-800">الأرباح</h3>
                </div>
                <span class="text-xs text-slate-500">آخر 4 أسابيع</span>
            </div>
            <div class="p-5">
                <canvas id="earningsChart" height="210" style="max-height: 210px; width: 100%;"></canvas>
            </div>
        </div>
    </div>

    {{-- آخر القضايا --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-folder-open text-sm"></i></span>
                <h3 class="text-[15px] font-bold text-slate-800">آخر القضايا</h3>
            </div>
            <a href="{{ route('admin.all-cases') }}" class="text-xs font-semibold text-blue-600 hover:underline">عرض الكل</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">رقم القضية</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">الموكل</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">المحامي</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">التاريخ</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($latestCases as $case)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-sm font-bold text-blue-600">{{ $case->case_number ?? '#C-'.str_pad($case->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-5 py-3.5 text-sm font-medium text-slate-800">{{ $case->client_name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-sm text-slate-700">{{ $case->lawyer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-sm text-slate-600">{{ $case->created_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($case->status == 'pending')
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-100">قيد المراجعة</span>
                            @elseif($case->status == 'active')
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-100">قيد المعالجة</span>
                            @elseif($case->status == 'closed' || $case->status == 'completed')
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">مكتملة</span>
                            @else
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200">{{ $case->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-400 text-sm font-semibold">لا توجد قضايا</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- سكريبت الرسم البياني --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const el = document.getElementById('earningsChart');
        if (!el) return;
        const ctx = el.getContext('2d');

        const grad = ctx.createLinearGradient(0, 0, 0, 210);
        grad.addColorStop(0, 'rgba(37, 99, 235, 0.18)');
        grad.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['الأسبوع 4', 'الأسبوع 3', 'الأسبوع 2', 'الأسبوع الحالي'],
                datasets: [{
                    label: 'الأرباح (د.ل)',
                    data: [{{ $week4 }}, {{ $week3 }}, {{ $week2 }}, {{ $week1 }}],
                    borderColor: '#2563eb',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        rtl: true, backgroundColor: '#fff', titleColor: '#0f172a', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 8,
                        titleFont: { family: 'Cairo', size: 11, weight: '600' }, bodyFont: { family: 'Cairo', size: 11 }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#475569', font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { color: '#475569', font: { family: 'Cairo', size: 11 } } }
                }
            }
        });
    })();
</script>
@endsection