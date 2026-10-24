@extends('layouts.admin')

@section('title', 'قضايا النظام')
@section('page_title', 'قضايا النظام')
@section('page_subtitle', 'جميع قضايا المنصّة')
@section('admin_cases_active', 'nav-active')

@section('content')
<div class="space-y-5">

    {{-- رسالة النجاح --}}
    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 text-sm font-semibold">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

  
    {{-- الإحصائيات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="relative overflow-hidden bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm shadow-slate-200/40 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-folder-open absolute -left-2 -bottom-3 text-6xl text-blue-50"></i>
            <div class="relative flex justify-between items-start">
                <div><p class="text-slate-500 text-xs font-medium mb-1">إجمالي القضايا</p><p class="text-slate-800 text-2xl font-bold">{{ $totalCases }}</p></div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="fas fa-folder-open text-lg"></i></div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm shadow-slate-200/40 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-clock absolute -left-2 -bottom-3 text-6xl text-amber-50"></i>
            <div class="relative flex justify-between items-start">
                <div><p class="text-slate-500 text-xs font-medium mb-1">قيد المراجعة</p><p class="text-slate-800 text-2xl font-bold">{{ $pendingCases }}</p></div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600"><i class="fas fa-clock text-lg"></i></div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm shadow-slate-200/40 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-check-circle absolute -left-2 -bottom-3 text-6xl text-emerald-50"></i>
            <div class="relative flex justify-between items-start">
                <div><p class="text-slate-500 text-xs font-medium mb-1">مكتملة</p><p class="text-slate-800 text-2xl font-bold">{{ $completedCases }}</p></div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600"><i class="fas fa-check-circle text-lg"></i></div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm shadow-slate-200/40 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-chart-line absolute -left-2 -bottom-3 text-6xl text-violet-50"></i>
            <div class="relative flex justify-between items-start">
                <div><p class="text-slate-500 text-xs font-medium mb-1">متوسط الإنجاز</p><p class="text-slate-800 text-2xl font-bold">14 <span class="text-sm text-slate-400">يوم</span></p></div>
                <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600"><i class="fas fa-chart-line text-lg"></i></div>
            </div>
        </div>
    </div>

    {{-- البحث والفلترة --}}
    <div class="bg-white rounded-2xl p-4 flex flex-wrap justify-between items-center gap-3 border border-slate-200/80 shadow-sm shadow-slate-200/40">
        <div class="flex-1 min-w-[200px] relative">
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" id="searchInput" placeholder="ابحث عن قضية..."
                   class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all font-medium">
        </div>
        <div class="flex items-center gap-2">
            <span class="hidden md:inline-flex items-center gap-1.5 text-[12px] font-semibold text-slate-400">
                <i class="fas fa-filter text-xs"></i> تصفية:
            </span>
            <select id="statusFilter" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-white text-slate-700 transition-all">
                <option value="all">جميع الحالات</option>
                <option value="pending">قيد المراجعة</option>
                <option value="active">قيد المعالجة</option>
                <option value="closed">مكتملة</option>
            </select>
            <select id="sortFilter" class="px-3 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 bg-white text-slate-700 transition-all">
                <option value="date">الأحدث</option>
                <option value="date_asc">الأقدم</option>
                <option value="status">حسب الحالة</option>
            </select>
        </div>
    </div>

    {{-- جدول القضايا --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">رقم القضية</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">الموكل</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">المحامي</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">النوع</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">التاريخ</th>
                        <th class="text-right px-5 py-3 text-[13px] font-bold text-slate-700">الحالة</th>
                        <th class="text-center px-5 py-3 text-[13px] font-bold text-slate-700">إجراءات</th>
                    </tr>
                </thead>
                <tbody id="casesTableBody" class="divide-y divide-slate-50">
                    @forelse($cases as $case)
                    <tr class="hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.cases.show', $case->id) }}'">
                        <td class="px-5 py-3.5">
                            <span dir="ltr" class="inline-block text-sm font-bold text-blue-600">{{ $case->case_number ?? '#C-'.str_pad($case->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-sm font-semibold text-slate-800">{{ $case->client_name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-sm text-slate-600">{{ $case->lawyer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[11px] font-semibold">{{ $case->case_type ?? 'قضية' }}</span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                <i class="far fa-calendar text-slate-400 text-xs"></i> {{ $case->created_at->format('Y-m-d') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
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
                        <td class="px-5 py-3.5">
                            <div class="flex justify-center gap-2" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.cases.show', $case->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[11px] font-bold transition-all border border-blue-200 hover:border-blue-600">
                                    <i class="fas fa-eye text-[10px]"></i> عرض
                                </a>
                                <button type="button"
                                    onclick="openArchiveModal('{{ route('admin.cases.archive', $case->id) }}', '{{ $case->case_number ?? '#C-'.str_pad($case->id, 3, '0', STR_PAD_LEFT) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white rounded-lg text-[11px] font-bold transition-all border border-amber-200 hover:border-amber-600">
                                    <i class="fas fa-archive text-[10px]"></i> أرشفة
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400">
                            <i class="fas fa-folder-open text-xl block mb-1.5 text-slate-300"></i>
                            لا توجد قضايا مسجلة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50">
            {{ $cases->links() }}
        </div>
    </div>
</div>

{{-- نافذة تأكيد الأرشفة --}}
<div id="archiveModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeArchiveModal()"></div>
    <div class="relative w-full max-w-[400px] bg-white rounded-3xl shadow-2xl shadow-slate-900/20 overflow-hidden">
        <button type="button" onclick="closeArchiveModal()" class="absolute top-4 left-4 w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        <div class="px-7 pt-9 pb-6 text-center">
            <div class="mx-auto mb-5 w-[72px] h-[72px] rounded-full bg-amber-50 flex items-center justify-center ring-8 ring-amber-50/40">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                    <i class="fas fa-archive text-2xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">أرشفة القضية</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                هل أنت متأكد من أرشفة القضية
                <span id="archiveCaseNumber" dir="ltr" class="inline-block font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md align-middle"></span>
                ؟
                <br>
                يمكنك استرجاعها لاحقًا من الأرشيف.
            </p>
        </div>
        <form id="archiveForm" method="POST" class="flex gap-3 px-7 pb-7">
            @csrf
            @method('PATCH')
            <button type="button" onclick="closeArchiveModal()" class="flex-1 px-4 py-3 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                إلغاء
            </button>
            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-l from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/30">
                <i class="fas fa-archive"></i> نعم، أرشفة
            </button>
        </form>
    </div>
</div>

<script>
    // ===== نافذة الأرشفة المنبثقة =====
    function openArchiveModal(action, caseNumber) {
        const modal = document.getElementById('archiveModal');
        document.getElementById('archiveForm').action = action;
        document.getElementById('archiveCaseNumber').innerText = caseNumber;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeArchiveModal() {
        const modal = document.getElementById('archiveModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeArchiveModal();
    });

    // ===== البحث والفرز =====
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const tableBody = document.getElementById('casesTableBody');

    function filterAndSortCases() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const sortValue = sortFilter.value;

        const rows = Array.from(document.querySelectorAll('#casesTableBody tr'));

        let filteredRows = rows.filter(row => {
            if (!row.cells || row.cells.length < 6) return true;
            const caseNumber = row.cells[0]?.innerText.toLowerCase() || '';
            const clientName = row.cells[1]?.innerText.toLowerCase() || '';
            const lawyerName = row.cells[2]?.innerText.toLowerCase() || '';
            const status = row.cells[5]?.innerText.toLowerCase() || '';

            const matchesSearch = searchTerm === '' ||
                caseNumber.includes(searchTerm) ||
                clientName.includes(searchTerm) ||
                lawyerName.includes(searchTerm);

            const matchesStatus = statusValue === 'all' ||
                (statusValue === 'pending' && status.includes('مراجعة')) ||
                (statusValue === 'active' && status.includes('معالجة')) ||
                (statusValue === 'closed' && status.includes('مكتملة'));

            return matchesSearch && matchesStatus;
        });

        filteredRows.sort((a, b) => {
            if (!a.cells || !b.cells) return 0;
            if (sortValue === 'date') {
                return (b.cells[4]?.innerText || '').localeCompare(a.cells[4]?.innerText || '');
            } else if (sortValue === 'date_asc') {
                return (a.cells[4]?.innerText || '').localeCompare(b.cells[4]?.innerText || '');
            } else if (sortValue === 'status') {
                return (a.cells[5]?.innerText || '').localeCompare(b.cells[5]?.innerText || '');
            }
            return 0;
        });

        filteredRows.forEach(row => tableBody.appendChild(row));
    }

    searchInput.addEventListener('input', filterAndSortCases);
    statusFilter.addEventListener('change', filterAndSortCases);
    sortFilter.addEventListener('change', filterAndSortCases);
</script>
@endsection