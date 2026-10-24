@extends('layouts.admin')

@section('title', 'توثيق المحامين')
@section('page_title', 'توثيق المحامين')
@section('page_subtitle', 'مراجعة طلبات التوثيق')

@section('admin_verification_active', 'nav-active')

@section('content')
<div class="space-y-5">
    
    @if(session('success'))
    <div class="js-alert p-3.5 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 font-semibold flex items-center gap-2">
        <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

    {{-- الإحصائيات --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-sm transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-medium mb-1">بانتظار المراجعة</p>
                    <p id="stat-pending-count" class="text-slate-800 text-2xl font-bold">{{ $stats['pending_count'] ?? 0 }}</p>
                    <p class="text-xs text-amber-600 font-medium mt-1.5">
                        <i class="fas fa-clock text-[10px]"></i> معلق
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-sm transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-medium mb-1">تم التوثيق هذا الشهر</p>
                    <p id="stat-approved-this-month" class="text-slate-800 text-2xl font-bold">{{ $stats['approved_this_month'] ?? 0 }}</p>
                    <p class="text-xs text-emerald-600 font-medium mt-1.5">
                        <i class="fas fa-check text-[10px]"></i> هذا الشهر
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-sm transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-medium mb-1">إجمالي المحامين</p>
                    <p id="stat-total-lawyers" class="text-slate-800 text-2xl font-bold">{{ $stats['total_lawyers'] ?? 0 }}</p>
                    <p id="stat-active-badge" class="text-xs text-blue-600 font-medium mt-1.5">
                        <i class="fas fa-user-check text-[10px]"></i> النشط: {{ $stats['active_lawyers'] ?? 0 }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-gavel text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- البحث --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="w-full relative">
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" id="adminSearchInput" placeholder="ابحث باسم المحامي أو رقم القيد..." 
                   class="w-full pr-10 pl-10 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors font-medium">
            
            <div id="searchSpinner" class="absolute left-4 top-1/2 transform -translate-y-1/2 hidden">
                <i class="fas fa-spinner fa-spin text-blue-600 text-sm"></i>
            </div>
        </div>
    </div>

    {{-- قائمة الطلبات --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-slate-800 text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-id-card text-blue-600"></i>
                طلبات التوثيق
            </h3>
            <span id="pendingBadgeCount" class="text-[11px] font-medium px-2.5 py-1 rounded-md bg-amber-50 text-amber-600">
                {{ $pendingLawyers->count() }} طلب
            </span>
        </div>
        
        <div class="divide-y divide-slate-50" id="lawyersList">
            @forelse($pendingLawyers as $lawyer)
                <div class="p-5 hover:bg-slate-50/50 transition-colors lawyer-row">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-lg bg-slate-800 flex items-center justify-center text-white text-sm font-bold">
                                {{ mb_substr($lawyer->name, 0, 1, 'utf-8') }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-semibold text-slate-800 text-sm">{{ $lawyer->name }}</h4>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 font-medium">معلّق</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 mt-1">
                                    <span><i class="fas fa-hashtag ml-1 text-slate-500"></i> {{ $lawyer->lawyerProfile->license_number ?? '—' }}</span>
                                    <span><i class="fas fa-phone-alt ml-1 text-slate-500"></i> {{ $lawyer->phone ?? '—' }}</span>
                                    <span><i class="fas fa-envelope ml-1 text-slate-500"></i> {{ $lawyer->email }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
    <button type="button" 
            onclick="openApproveModal({{ $lawyer->id }}, '{{ $lawyer->name }}')" 
            class="px-3.5 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-1.5">
        <i class="fas fa-check text-[11px]"></i> اعتماد
    </button>
    
    <button type="button" onclick="openRejectModal({{ $lawyer->id }}, '{{ $lawyer->name }}')" class="px-3.5 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-lg text-xs font-semibold hover:bg-rose-600 hover:text-white transition-colors flex items-center gap-1.5">
        <i class="fas fa-times text-[11px]"></i> رفض
    </button>
</div>
</div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400"><i class="fas fa-paperclip ml-1 text-xs"></i> المرفقات:</span>
                            @if($lawyer->lawyerProfile && $lawyer->lawyerProfile->syndicate_card_path)
                                <button onclick="previewDocument('{{ $lawyer->name }}', '{{ asset($lawyer->lawyerProfile->syndicate_card_path) }}')" class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md border border-blue-100 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-eye text-[11px]"></i> معاينة البطاقة
                                </button>
                            @else
                                <span class="text-xs text-rose-500 font-medium bg-rose-50 px-2 py-1 rounded-md">لم يرفع المستند</span>
                            @endif
                        </div>
                        <span class="text-[11px] text-slate-400 bg-slate-50 px-2 py-1 rounded-md"><i class="far fa-calendar-alt ml-1"></i> {{ $lawyer->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-400" id="noLawyersMessage">
                    <i class="fas fa-check-circle text-xl block mb-1.5 text-slate-300"></i>
                    <p class="text-xs font-medium">لا توجد طلبات معلقة</p>
                </div>
            @endforelse
            
            <div id="noResultsMessage" class="p-12 text-center text-slate-400 hidden">
                <i class="fas fa-search text-xl block mb-1.5 text-slate-300"></i>
                <p class="text-xs font-medium">لا توجد نتائج</p>
            </div>
        </div>
        
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
            <p class="text-xs text-slate-400">الإجمالي: <span id="footerTotalCount">{{ $pendingLawyers->count() }}</span> طلب</p>
        </div>
    </div>
</div>

{{-- معاينة المستند --}}
<div id="docPreviewModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full shadow-xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200">
        <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center">
            <h3 id="modalDocTitle" class="font-semibold text-slate-800 text-sm">معاينة المستند</h3>
            <button onclick="closePreviewModal()" class="text-slate-400 hover:text-rose-600 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-5 bg-slate-50 flex justify-center max-h-[70vh] overflow-y-auto">
            <img id="modalDocImage" src="" alt="المستند" class="rounded-lg max-w-full h-auto object-contain">
        </div>
        <div class="px-5 py-3.5 border-t border-slate-100 flex justify-end">
            <button onclick="closePreviewModal()" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-xs font-semibold">إغلاق</button>
        </div>
    </div>
</div>

{{-- نافذة الاعتماد --}}
<div id="approveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 hidden transition-opacity duration-300">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl transform transition-all duration-300 scale-95 border border-slate-200">
        
        <div class="flex justify-center mb-6">
            <div class="p-4 bg-emerald-50 rounded-full">
                <i class="fas fa-check-circle text-4xl text-emerald-500"></i>
            </div>
        </div>

        <h3 class="text-xl font-bold text-center text-slate-800 mb-2">تأكيد توثيق المحامي</h3>
        <p class="text-sm text-center text-slate-500 mb-8">
            هل أنت متأكد من اعتماد توثيق المحامي: 
            <span id="approveLawyerName" class="font-bold text-slate-800"></span>؟
        </p>

        <div class="flex gap-3">
            <button type="button" onclick="closeApproveModal()" class="flex-1 py-3 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">إلغاء</button>
            <form id="approveForm" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-3 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all">تأكيد الاعتماد</button>
            </form>
        </div>
    </div>
</div>
{{-- نافذة  الرفضضض --}}
<div id="rejectReasonModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-xl w-full shadow-xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200">
        <div class="px-5 py-3.5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                <i class="fas fa-times-circle text-rose-500"></i>
                رفض طلب: <span id="rejectLawyerName" class="text-slate-800"></span>
            </h3>
            <button onclick="closeRejectModal()" class="text-slate-400 hover:text-rose-600 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="rejectForm" action="" method="POST">
            @csrf
            <div class="p-5 space-y-4">
                <p class="text-xs text-slate-500">سيتم حذف الحساب المعلق ليتمكن المحامي من إعادة التسجيل.</p>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">سبب الرفض:</label>
                    <select onchange="document.getElementById('rejection_note').value = this.value" class="w-full p-2.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-blue-500 bg-white font-medium">
                        <option value="">اختر سبب...</option>
                        <option value="صورة البطاقة غير واضحة">صورة البطاقة غير واضحة</option>
                        <option value="البطاقة منتهية الصلاحية">البطاقة منتهية الصلاحية</option>
                        <option value="رقم القيد لا يطابق البطاقة">رقم القيد لا يطابق البطاقة</option>
                        <option value="رقم القيد غير مسجل بالنقابة">رقم القيد غير مسجل</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">ملاحظة:</label>
                    <textarea id="rejection_note" name="rejection_reason" rows="3" required placeholder="اكتب الملاحظة..." class="w-full p-2.5 border border-slate-200 rounded-lg text-xs font-medium focus:outline-none focus:border-blue-500 resize-none"></textarea>
                </div>
            </div>
            <div class="px-5 py-3.5 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-200 transition-colors">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-semibold">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>

<script>
    let searchTimeout;

    // --- دالة البحث المباشر ---
    function doLiveSearch() {
        const query = document.getElementById('adminSearchInput').value.trim();
        const spinner = document.getElementById('searchSpinner');
        const container = document.getElementById('lawyersList');
        const noResultsMessage = document.getElementById('noResultsMessage');

        const url = new URL(window.location.href);
        if (query) url.searchParams.set('search', query);
        else url.searchParams.delete('search');

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('stat-pending-count').textContent = data.stats.pending_count;
            document.getElementById('stat-approved-this-month').textContent = data.stats.approved_this_month;
            document.getElementById('stat-total-lawyers').textContent = data.stats.total_lawyers;
            document.getElementById('stat-active-badge').innerHTML = `<i class="fas fa-user-check text-[10px]"></i> النشط: ${data.stats.active_lawyers}`;
            document.getElementById('pendingBadgeCount').textContent = `${data.count} طلب`;
            document.getElementById('footerTotalCount').textContent = data.count;

            const oldRows = container.querySelectorAll('.lawyer-row, #noLawyersMessage');
            oldRows.forEach(row => row.remove());

            if (data.lawyers.length === 0) {
                noResultsMessage.classList.remove('hidden');
            } else {
                noResultsMessage.classList.add('hidden');
                
                data.lawyers.forEach(lawyer => {
                    const firstChar = lawyer.name.charAt(0);
                    const license = (lawyer.lawyer_profile && lawyer.lawyer_profile.license_number) ? lawyer.lawyer_profile.license_number : '—';
                    const phone = lawyer.phone ? lawyer.phone : '—';
                    const dateFormatted = lawyer.created_at ? new Date(lawyer.created_at).toISOString().split('T')[0] : '—';

                    let documentButtonHtml = `<span class="text-xs text-rose-500 font-medium bg-rose-50 px-2 py-1 rounded-md">لم يرفع المستند</span>`;
                    if (lawyer.lawyer_profile && lawyer.lawyer_profile.syndicate_card_path) {
                        const assetUrl = window.location.origin + '/' + lawyer.lawyer_profile.syndicate_card_path;
                        documentButtonHtml = `
                            <button onclick="previewDocument('${lawyer.name}', '${assetUrl}')" class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md border border-blue-100 font-medium flex items-center gap-1.5">
                                <i class="fas fa-eye text-[11px]"></i> معاينة البطاقة
                            </button>
                        `;
                    }

                    const newRow = `
                        <div class="p-5 hover:bg-slate-50/50 transition-colors lawyer-row">
                            <div class="flex flex-wrap justify-between items-start gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-lg bg-slate-800 flex items-center justify-center text-white text-sm font-bold">
                                        ${firstChar}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-semibold text-slate-800 text-sm">${lawyer.name}</h4>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 font-medium">معلّق</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 mt-1">
                                            <span><i class="fas fa-hashtag ml-1 text-slate-500"></i> ${license}</span>
                                            <span><i class="fas fa-phone-alt ml-1 text-slate-500"></i> ${phone}</span>
                                            <span><i class="fas fa-envelope ml-1 text-slate-500"></i> ${lawyer.email}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openApproveModal(${lawyer.id}, '${lawyer.name}')" class="px-3.5 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-check text-[11px]"></i> اعتماد
                                    </button>
                                    <button type="button" onclick="openRejectModal(${lawyer.id}, '${lawyer.name}')" class="px-3.5 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-lg text-xs font-semibold hover:bg-rose-600 hover:text-white transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-times text-[11px]"></i> رفض
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400"><i class="fas fa-paperclip ml-1 text-xs"></i> المرفقات:</span>
                                    ${documentButtonHtml}
                                </div>
                                <span class="text-[11px] text-slate-400 bg-slate-50 px-2 py-1 rounded-md"><i class="far fa-calendar-alt ml-1"></i> ${dateFormatted}</span>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', newRow);
                });
            }
            if (spinner) spinner.classList.add('hidden');
        })
        .catch(error => {
            console.error('خطأ:', error);
            if (spinner) spinner.classList.add('hidden');
        });
    }

    // --- دوال التحكم في النوافذ المنبثقة ---

    // نافذة الاعتماد
    function openApproveModal(lawyerId, lawyerName) {
        const modal = document.getElementById('approveModal');
        document.getElementById('approveLawyerName').textContent = lawyerName;
        document.getElementById('approveForm').action = `/admin/lawyers/${lawyerId}/approve`;
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); modal.querySelector('div').classList.remove('scale-95'); }, 10);
    }
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // نافذة الرفض
    function openRejectModal(lawyerId, lawyerName) {
        const modal = document.getElementById('rejectReasonModal');
        document.getElementById('rejectLawyerName').textContent = lawyerName;
        document.getElementById('rejectForm').action = `/admin/lawyers/${lawyerId}/reject`;
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); modal.querySelector('div').classList.remove('scale-95'); }, 10);
    }
    function closeRejectModal() {
        const modal = document.getElementById('rejectReasonModal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
        document.getElementById('rejection_note').value = '';
    }

    // نافذة المعاينة
    function previewDocument(name, imageUrl) {
        const modal = document.getElementById('docPreviewModal');
        document.getElementById('modalDocTitle').textContent = 'بطاقة: ' + name;
        document.getElementById('modalDocImage').src = imageUrl;
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);
    }
    function closePreviewModal() {
        const modal = document.getElementById('docPreviewModal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // --- تهيئة الصفحة ---
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('adminSearchInput');
        const spinner = document.getElementById('searchSpinner');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (spinner) spinner.classList.remove('hidden');
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(doLiveSearch, 400); 
            });
        }

        // إخفاء التنبيه التلقائي
        const alertBox = document.querySelector('.js-alert');
        if (alertBox) {
            setTimeout(() => {
                alertBox.style.transition = 'opacity 0.5s ease';
                alertBox.style.opacity = '0';
                setTimeout(() => { alertBox.remove(); }, 500);
            }, 5000);
        }
    });
</script>
@endsection