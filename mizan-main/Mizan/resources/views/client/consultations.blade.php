@extends('layouts.app')

@section('title', 'استشاراتي والمحادثات')
@section('page_title', 'استشاراتي')
@section('page_subtitle', 'إدارة استشاراتك القانونية والتواصل مع المحامين')
@section('client_consultations_active', 'nav-active')

@section('content')
<div x-data="{ showChat: false }" class="flex flex-col w-full gap-4 px-2 sm:px-4 md:px-8 pb-6 overflow-hidden" style="height: calc(100vh - 110px); min-height: 600px;">

    {{-- ====================== صف المحادثة والقائمة ====================== --}}
    <div class="flex flex-col lg:flex-row w-full gap-4 flex-1 min-h-0 overflow-hidden">

    {{-- ====================== القائمة اليمنى ====================== --}}
    <div :class="showChat ? 'hidden lg:flex' : 'flex'" class="w-full lg:w-80 bg-white rounded-2xl border border-slate-200 flex-col h-full shrink-0 shadow-sm overflow-hidden">
        
        <div class="shrink-0 p-4 border-b border-slate-100 bg-white">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-slate-800 text-sm">استشاراتي</h3>
                <span id="consultationsCount" class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-bold">{{ $consultations->count() }}</span>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="بحث في الاستشارات..." 
                       class="w-full pr-9 pl-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all bg-slate-50">
            </div>
        </div>
        
        <div id="consultationsList" class="flex-1 overflow-y-auto w-full custom-scrollbar min-h-0">
            @forelse($consultations as $consultation)
            @php
                $avatar = $consultation->lawyer->avatar ?? null;
                $avatarUrl = '';
                if ($avatar) {
                    if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                        $avatarUrl = $avatar;
                    } else {
                        $fileName = basename($avatar);
                        $avatarUrl = asset('uploads/avatars/' . $fileName);
                    }
                }
                $lawyerName = $consultation->lawyer->name ?? 'المحامي';
                $initialLetter = mb_substr($lawyerName, 0, 1, 'UTF-8');
                $isClosed = in_array($consultation->status, ['completed', 'closed']);
            @endphp
            
            <div class="consultation-item flex flex-col p-4 cursor-pointer transition-all border-b border-slate-50 last:border-0 relative group {{ $isClosed ? 'bg-slate-50/60 hover:bg-slate-100/60' : 'hover:bg-slate-50' }}" 
                 data-id="{{ $consultation->id }}"
                 data-status="{{ $consultation->status }}"
                 data-lawyer-name="{{ $lawyerName }}"
                 data-lawyer-initial="{{ $initialLetter }}"
                 data-lawyer-avatar="{{ $avatarUrl }}"
                 onclick="selectConsultation(this, {{ $consultation->id }})">

                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" class="w-11 h-11 rounded-xl object-cover shadow-sm border border-slate-100 {{ $isClosed ? 'opacity-60 grayscale' : '' }}" alt="lawyer avatar" onerror="this.outerHTML=`<div class='w-11 h-11 rounded-xl {{ $isClosed ? 'bg-slate-300 text-slate-500' : 'bg-slate-800 text-white' }} flex items-center justify-center font-medium text-sm'>{{ $initialLetter }}</div>`">
                        @else
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center font-medium text-sm {{ $isClosed ? 'bg-slate-300 text-slate-500' : 'bg-slate-800 text-white' }}">
                                {{ $initialLetter }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0 pr-1">
                        <div class="flex justify-between items-center mb-0.5">
                            <h4 class="font-semibold text-sm truncate {{ $isClosed ? 'text-slate-400' : 'text-slate-800' }}">{{ $lawyerName }}</h4>
                            <span class="text-[10px] text-slate-400 font-medium ml-4">{{ $consultation->created_at->format('H:i') }}</span>
                        </div>
                        <p class="text-xs truncate w-11/12 {{ $isClosed ? 'text-slate-400' : 'text-slate-500' }}">{{ $consultation->title }}</p>
                    </div>
                </div>

                {{-- الحالات بتصميم جديد وأنيق --}}
                <div class="mt-3 flex gap-2 status-badge-container">
                    @if($consultation->status === 'accepted')
                        <a href="{{ route('payment.checkout', $consultation->id) }}" class="group/pay flex w-full items-center justify-between gap-2 rounded-xl bg-blue-600 px-3.5 py-2.5 text-white shadow-md shadow-blue-600/25 transition-all hover:bg-blue-700" onclick="event.stopPropagation()">
                            <span class="flex items-center gap-2 text-[12px] font-bold">
                                <i class="fas fa-credit-card"></i> ادفع الآن
                            </span>
                            <span class="rounded-lg bg-white/20 px-2 py-0.5 text-[12px] font-black">{{ $consultation->price }} د.ل</span>
                        </a>
                    @elseif($consultation->status === 'paid' || $consultation->status === 'active')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-bold text-emerald-600">
                            <i class="fas fa-circle-check text-emerald-500"></i> نشطة ومدفوعة
                        </span>
                    @elseif($consultation->status === 'pending')
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-[11px] font-bold text-amber-600">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                            </span>
                            قيد المراجعة
                        </span>
                    @elseif($consultation->status === 'rejected')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1.5 text-[11px] font-bold text-rose-500">
                            <i class="fas fa-ban"></i> مرفوضة
                        </span>
                    @elseif($consultation->status === 'completed' || $consultation->status === 'closed')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-bold text-slate-500">
                            <i class="fas fa-lock text-slate-400"></i> مغلقة ومكتملة
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-400 text-sm">لا توجد استشارات بعد</div>
            @endforelse
        </div>
    </div>
    
    {{-- ====================== منطقة المحادثة ====================== --}}
    <div :class="showChat ? 'flex' : 'hidden lg:flex'" class="flex-1 bg-white rounded-2xl border border-slate-200 flex-col h-full shadow-sm overflow-hidden min-w-0 relative">

        <div id="noConsultationSelected" class="flex-1 flex flex-col items-center justify-center bg-slate-50/50 w-full h-full">
            <div class="text-center">
                <div class="w-20 h-20 bg-white border border-slate-100 shadow-sm rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-gavel text-3xl text-slate-300"></i>
                </div>
                <h4 class="font-bold text-slate-700 text-xl mb-2">المساحة القانونية</h4>
                <p class="text-sm text-slate-400">الرجاء اختيار استشارة من الأرشيف الجانبي لبدء المراسلة</p>
            </div>
        </div>
        
        <div id="chatContainer" class="hidden flex-col flex-1 min-h-0 w-full relative">
            
            <div class="relative shrink-0 overflow-hidden border-b border-blue-700/30 bg-gradient-to-l from-blue-700 via-blue-600 to-blue-500 px-5 py-4 z-10 w-full">
                <div class="pointer-events-none absolute -right-10 -top-14 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                <i class="fas fa-scale-balanced pointer-events-none absolute -left-3 top-1/2 -translate-y-1/2 text-[6rem] text-white/[0.06]"></i>
                <div class="relative flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="showChat = false; closeChatSession()" class="lg:hidden w-9 h-9 rounded-lg bg-white/15 ring-1 ring-inset ring-white/25 flex items-center justify-center text-white hover:bg-white/25 transition-colors shrink-0">
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                        <div class="relative shrink-0">
                            <div id="chatLawyerAvatar" class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold shadow-sm text-lg border-2 border-white/40 ring-2 ring-white/20 overflow-hidden">?</div>
                            <div class="absolute bottom-0 left-0 h-3.5 w-3.5 rounded-full border-2 border-blue-600 bg-emerald-400"></div>
                        </div>
                        <div class="min-w-0">
                            <h3 id="chatLawyerName" class="font-bold text-white text-base truncate">اختر استشارة</h3>
                            <p id="chatConsultationTitle" class="text-xs text-blue-100/90 truncate mt-0.5 font-medium"></p>
                            <p id="typingIndicator" class="hidden items-center gap-1 text-xs font-bold text-white mt-1">
                                <span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>
                                <span class="mr-1">يكتب الآن</span>
                            </p>
                        </div>
                    </div>
                    <span id="chatStatusBadge" class="hidden items-center gap-2 rounded-full bg-white/95 px-3.5 py-1.5 text-[11px] font-bold shadow-sm shrink-0">
                        <span class="relative flex h-2 w-2">
                            <span class="ping-dot absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"></span>
                            <span class="dot-core relative inline-flex h-2 w-2 rounded-full"></span>
                        </span>
                        <span class="badge-text"></span>
                    </span>
                </div>
            </div>
            
            <div id="chatMessages" class="flex-1 overflow-y-auto p-5 space-y-5 bg-slate-50/50 w-full custom-scrollbar relative">
                {{-- مساحة الرسائل --}}
            </div>
            
            {{-- شريط الإرسال --}}
            <div class="shrink-0 p-4 border-t border-slate-100 bg-white w-full">
                <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-full border border-slate-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                    
                    <div class="relative">
                        <label for="fileInput" class="cursor-pointer w-10 h-10 rounded-full hover:bg-white hover:shadow-sm transition-all text-slate-500 flex items-center justify-center shrink-0">
                            <i class="fas fa-paperclip text-base"></i>
                        </label>
                        <input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                    </div>
                    
                    <input type="text" id="messageInput" placeholder="اكتب رسالتك الموجهة للمحامي هنا..." 
                           class="flex-1 bg-transparent px-3 py-2 focus:outline-none text-sm text-slate-700 placeholder-slate-400 font-medium w-full" disabled>
                           
                    <button id="sendBtn" class="px-8 h-10 rounded-full bg-blue-600 hover:bg-blue-700 transition-all text-white flex items-center justify-center gap-2 shrink-0 opacity-50 cursor-not-allowed shadow-md shadow-blue-500/20 font-bold text-sm" disabled>
                        <span>إرسال</span>
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                    
                </div>
                
                <div id="filePreviewContainer" class="hidden mt-3 flex flex-wrap gap-2"></div>
            </div>
        </div>
    </div>
    </div>
</div>

{{-- ====================== جميع المودالات ====================== --}}

{{-- نافذة الرضا --}}
<div id="feedbackModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all text-center p-6 relative">
        <button onclick="closeFeedbackModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all">
            <i class="fas fa-times text-sm"></i>
        </button>
        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-question-circle text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">كيف كانت تجربتك؟</h3>
        <p class="text-sm text-slate-500 mb-6">هل كانت الاستشارة القانونية مفيدة لك؟</p>
        <div class="flex gap-3">
            <button onclick="handleFeedback(true)" class="flex-1 py-3 bg-emerald-50 text-emerald-700 rounded-xl font-bold hover:bg-emerald-100 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-thumbs-up"></i> مفيدة
            </button>
            <button onclick="handleFeedback(false)" class="flex-1 py-3 bg-rose-50 text-rose-700 rounded-xl font-bold hover:bg-rose-100 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-thumbs-down"></i> غير مفيدة
            </button>
        </div>
    </div>
</div>

{{-- نافذة التقييم --}}
<div id="ratingModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all text-center p-6 relative">
        <button onclick="closeRatingModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all">
            <i class="fas fa-times text-sm"></i>
        </button>
        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-star text-2xl"></i>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">تقييم المحامي</h3>
        <p class="text-sm text-slate-500 mb-6">تقييمك يساعد في تحسين جودة الخدمات المقدمة</p>
        <div class="flex justify-center gap-2 mb-6" dir="ltr">
            <i class="star-rating fas fa-star text-3xl text-slate-200 cursor-pointer hover:text-amber-400 transition-all" data-value="1" onclick="setRating(1)"></i>
            <i class="star-rating fas fa-star text-3xl text-slate-200 cursor-pointer hover:text-amber-400 transition-all" data-value="2" onclick="setRating(2)"></i>
            <i class="star-rating fas fa-star text-3xl text-slate-200 cursor-pointer hover:text-amber-400 transition-all" data-value="3" onclick="setRating(3)"></i>
            <i class="star-rating fas fa-star text-3xl text-slate-200 cursor-pointer hover:text-amber-400 transition-all" data-value="4" onclick="setRating(4)"></i>
            <i class="star-rating fas fa-star text-3xl text-slate-200 cursor-pointer hover:text-amber-400 transition-all" data-value="5" onclick="setRating(5)"></i>
        </div>
        <input type="hidden" id="ratingValue" value="0">
        <button id="submitRatingBtn" onclick="submitRatingAndComplete()" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all flex justify-center items-center gap-2">
            إرسال التقييم
        </button>
    </div>
</div>

{{-- نافذة الإبلاغ --}}
<div id="disputeModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl transform transition-all relative">
        <button onclick="closeDisputeModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all z-10">
            <i class="fas fa-times text-sm"></i>
        </button>
        <div class="p-6">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-lg mb-2">الإبلاغ عن مشكلة</h3>
                <p class="text-sm text-slate-500">يرجى توضيح سبب عدم رضاك؛ حيث سيقوم فريق المنصة بمراجعة المحادثة واتخاذ الإجراءات اللازمة</p>
            </div>
            <textarea id="disputeReason" rows="4" placeholder="اكتب تفاصيل المشكلة بوضوح..." 
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-rose-400 resize-none"></textarea>
            <div class="flex gap-3 mt-6">
                <button onclick="closeDisputeModal()" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-all">
                    إلغاء
                </button>
                <button id="submitDisputeBtn" onclick="submitDisputeAndComplete()" class="flex-1 py-3 bg-rose-600 text-white rounded-xl font-bold hover:bg-rose-700 transition-all flex justify-center items-center gap-2">
                    إرسال البلاغ
                </button>
            </div>
        </div>
    </div>
</div>

{{-- مودال التنبيهات --}}
<div id="alertModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[200] hidden items-center justify-center p-4 w-full h-full">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all text-center p-6">
        <div id="alertIconContainer" class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-orange-50 text-orange-500">
            <i id="alertIcon" class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <h3 id="alertTitle" class="text-lg font-bold text-slate-800 mb-2">تنبيه</h3>
        <p id="alertMessage" class="text-sm text-slate-600 mb-6 leading-relaxed"></p>
        <button onclick="closeAlertModal()" class="w-full py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all shadow-sm">
            حسناً، فهمت
        </button>
    </div>
</div>

{{-- مودال التأكيد --}}
<div id="confirmModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[200] hidden items-center justify-center p-4 w-full h-full">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all text-center p-6">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-rose-50 text-rose-600">
            <i class="fas fa-exclamation-circle text-xl"></i>
        </div>
        <h3 id="confirmTitle" class="text-lg font-bold text-slate-800 mb-2">تأكيد الإجراء</h3>
        <p id="confirmMessage" class="text-sm text-slate-600 mb-6 leading-relaxed"></p>
        <div class="flex gap-3 w-full">
            <button onclick="closeConfirmModal()" class="flex-1 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all border border-slate-200">
                إلغاء
            </button>
            <button id="confirmActionBtn" class="flex-1 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition-all shadow-sm flex justify-center items-center gap-2">
                نعم، تأكيد
            </button>
        </div>
    </div>
</div>

{{-- مودال عرض الملفات الفوري --}}
<div id="fileViewModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[1000] hidden items-center justify-center p-4 w-full h-full">
    <div class="bg-white rounded-2xl w-full max-w-2xl h-auto flex flex-col shadow-2xl">
        <div class="flex justify-between items-center p-4 border-b shrink-0 rounded-t-2xl">
            <h3 id="viewFileName" class="font-bold text-lg truncate max-w-[300px]"></h3>
            <div class="flex gap-2">
                <button onclick="downloadCurrentFile()" class="text-gray-600 hover:text-blue-600 transition-colors p-2 rounded-lg hover:bg-gray-100" title="تحميل">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </button>
                <button onclick="closeFileModal()" class="text-gray-600 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-gray-100" title="إغلاق">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        <div id="fileViewer" class="flex-1 overflow-auto p-4 bg-gray-50 rounded-b-2xl" style="min-height: 500px; max-height: 70vh;"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<script>
    // ====================== المتغيرات العامة ======================
    let currentSessionId = null;
    let currentConsultationId = null;
    let currentConsultationStatus = null;
    let supabaseClient = null;
    let currentSubscription = null;
    let confirmCallback = null;
    let currentFileUrl = '';
    let currentFileName = '';
    let currentSelectedFile = null;
    let isChatActive = true;
    let currentFetchController = null; 
    
    // ذاكرة فورية سريعة جداً (Cache)
    const chatCache = {}; 
    const consultationToSessionMap = {};
    
    const MY_ID = {{ Auth::id() ?? 0 }};
    const CSRF_TOKEN = "{{ csrf_token() }}";
    let typingHideTimeout = null;   // مؤقّت إخفاء مؤشر "يكتب الآن"
    let typingSentAt = 0;           // throttle لإرسال إشعار الكتابة

    const supabaseUrl = "{{ env('SUPABASE_URL') }}";
    const supabaseAnonKey = "{{ env('SUPABASE_ANON_KEY') }}";
    
    if (supabaseUrl && supabaseAnonKey) {
        supabaseClient = supabase.createClient(supabaseUrl, supabaseAnonKey);
    }

    // ====================== المودالات (إغلاق وتأكيد فوري) ======================
    function closeAlertModal() { document.getElementById('alertModal').classList.replace('flex', 'hidden'); }
    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.replace('flex', 'hidden');
        confirmCallback = null;
    }
    function closeFileModal() { 
        document.getElementById('fileViewModal').classList.replace('flex', 'hidden'); 
        const viewer = document.getElementById('fileViewer');
        if (viewer) viewer.innerHTML = '';
    }
    function downloadCurrentFile() { if (currentFileUrl) window.location.href = currentFileUrl; }

    const confirmBtn = document.getElementById('confirmActionBtn');
    if (confirmBtn) {
        confirmBtn.onclick = function() { 
            if (confirmCallback) {
                confirmCallback(); 
            }
            closeConfirmModal(); 
        };
    }

    function showCustomAlert(title, message, type = 'warning') {
        document.getElementById('alertTitle').innerText = title; 
        document.getElementById('alertMessage').innerText = message;
        const iconContainer = document.getElementById('alertIconContainer'); 
        const icon = document.getElementById('alertIcon');
        
        if (type === 'error') { 
            iconContainer.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-rose-50 text-rose-600'; 
            icon.className = 'fas fa-times-circle text-xl'; 
        } else if (type === 'info') { 
            iconContainer.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-emerald-50 text-emerald-600'; 
            icon.className = 'fas fa-check-circle text-xl'; 
        } else { 
            iconContainer.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-orange-50 text-orange-500'; 
            icon.className = 'fas fa-exclamation-triangle text-xl'; 
        }
        document.getElementById('alertModal').classList.replace('hidden', 'flex');
    }

    function showCustomConfirm(title, message, callback) {
        document.getElementById('confirmTitle').innerText = title; 
        document.getElementById('confirmMessage').innerText = message; 
        confirmCallback = callback;
        document.getElementById('confirmModal').classList.replace('hidden', 'flex');
    }

    // ====================== عرض الملفات الفوري ======================
    function getFileIcon(fileType) {
        if (fileType === 'pdf') {
            return `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileType)) {
            return `<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`;
        } else {
            return `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>`;
        }
    }

    function viewFileAttachment(fileUrl, fileName, fileType) {
        currentFileUrl = fileUrl;
        currentFileName = fileName;
        document.getElementById('viewFileName').innerText = fileName;
        const viewer = document.getElementById('fileViewer');
        
        if (fileType === 'pdf') {
            viewer.innerHTML = `<div class="flex justify-center items-center h-full w-full bg-gray-100 p-4"><iframe src="${fileUrl}" class="w-full h-full rounded-lg shadow-lg" style="min-height: 500px;"></iframe></div>`;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileType)) {
            viewer.innerHTML = `<div class="flex justify-center items-center h-full w-full bg-gray-100"><div class="relative flex items-center justify-center w-full h-full p-4"><div id="image-loading" class="absolute inset-0 flex items-center justify-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div></div><img src="${fileUrl}" class="max-w-full max-h-full rounded-lg shadow-lg object-contain" style="max-width: 90%; max-height: 90%; aspect-ratio: 1/1;" onload="document.getElementById('image-loading')?.remove();" onerror="this.onerror=null; document.getElementById('image-loading').innerHTML='<p class=\\'text-red-500\\'>فشل تحميل الصورة</p>';"></div></div>`;
        } else {
            viewer.innerHTML = `<div class="flex flex-col items-center justify-center h-full w-full bg-gray-100"><div class="text-center p-8"><p class="text-gray-600 mb-4">لا يمكن معاينة هذا النوع من الملفات</p><button onclick="downloadCurrentFile()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">تحميل الملف</button></div></div>`;
        }
        document.getElementById('fileViewModal').classList.remove('hidden');
        document.getElementById('fileViewModal').classList.add('flex');
    }

    // ====================== دوال التقييم ======================
    function openFeedbackModal() { document.getElementById('feedbackModal').classList.replace('hidden', 'flex'); }
    function closeFeedbackModal() { document.getElementById('feedbackModal').classList.replace('flex', 'hidden'); }

    function handleFeedback(isHelpful) {
        closeFeedbackModal();
        if (isHelpful) openRatingModal();
        else openDisputeModal();
    }

    function openRatingModal() {
        resetRatingStars();
        document.getElementById('ratingModal').classList.replace('hidden', 'flex');
    }

    function closeRatingModal() { document.getElementById('ratingModal').classList.replace('flex', 'hidden'); }

    function resetRatingStars() {
        document.getElementById('ratingValue').value = "0";
        document.querySelectorAll('.star-rating').forEach(star => {
            star.classList.replace('text-amber-400', 'text-slate-200');
        });
    }

    function setRating(rating) {
        document.getElementById('ratingValue').value = rating;
        document.querySelectorAll('.star-rating').forEach((star, index) => {
            if (index < rating) star.classList.replace('text-slate-200', 'text-amber-400');
            else star.classList.replace('text-amber-400', 'text-slate-200');
        });
    }

    function submitRatingAndComplete() {
        const rating = document.getElementById('ratingValue').value;
        if (rating === "0") {
            showCustomAlert('تنبيه', 'الرجاء اختيار تقييم للمحامي قبل الإرسال', 'warning');
            return;
        }
        closeRatingModal();
        showCustomAlert('شكراً لك', 'تم حفظ تقييمك بنجاح', 'info');
        updateConsultationUIAfterAction('completed'); 
        
        fetch('/api/chat/submit-satisfaction', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN 
            },
            body: JSON.stringify({ session_id: currentSessionId, satisfied: true, rating: rating })
        }).catch(e => console.log(e));
    }

    function openDisputeModal() { document.getElementById('disputeModal').classList.replace('hidden', 'flex'); }
    function closeDisputeModal() { 
        document.getElementById('disputeModal').classList.replace('flex', 'hidden'); 
        document.getElementById('disputeReason').value = '';
    }

    function submitDisputeAndComplete() {
        const reason = document.getElementById('disputeReason').value.trim();
        if (reason.length < 10) {
            showCustomAlert('تنبيه', 'الرجاء كتابة سبب واضح لا يقل عن 10 أحرف', 'warning');
            return;
        }
        closeDisputeModal();
        showCustomAlert('تم الإبلاغ', 'تم إرسال البلاغ للإدارة', 'info');
        updateConsultationUIAfterAction('disputed'); 
        
        fetch('/api/chat/submit-satisfaction', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN 
            },
            body: JSON.stringify({ session_id: currentSessionId, satisfied: false, dispute_reason: reason })
        }).catch(e => console.log(e));
    }

    function updateConsultationUIAfterAction(newStatus) {
        const item = document.querySelector(`.consultation-item[data-id="${currentConsultationId}"]`);
        if(item) {
            item.setAttribute('data-status', newStatus);
            const badgeContainer = item.querySelector('.status-badge-container');
            if(badgeContainer) {
                badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-[11px] font-bold text-slate-500"><i class="fas fa-lock text-slate-400"></i> مغلقة ومكتملة</span>`;
            }
            // تطبيق حالة "مغلقة" البصرية على البطاقة بالكامل لتطابق العرض من الخادم
            item.classList.remove('hover:bg-slate-50');
            item.classList.add('bg-slate-50/60', 'hover:bg-slate-100/60');
            const nameEl = item.querySelector('h4');
            if (nameEl) { nameEl.classList.remove('text-slate-800'); nameEl.classList.add('text-slate-400'); }
            const titleEl = item.querySelector('p');
            if (titleEl) { titleEl.classList.remove('text-slate-500'); titleEl.classList.add('text-slate-400'); }
            const avatarBox = item.querySelector('.relative.shrink-0 > div, .relative.shrink-0 > img');
            if (avatarBox) {
                if (avatarBox.tagName === 'IMG') {
                    avatarBox.classList.add('opacity-60', 'grayscale');
                } else {
                    avatarBox.classList.remove('bg-slate-800', 'text-white');
                    avatarBox.classList.add('bg-slate-300', 'text-slate-500');
                }
            }
        }
        document.getElementById('messageInput').disabled = true;
        document.getElementById('sendBtn').disabled = true;
        document.getElementById('fileInput').disabled = true;
    }

    // ====================== العزل، التحكم في الطلبات والكاش ======================
    function updateLawyerInfo(element) {
        document.getElementById('chatLawyerName').innerText = element.getAttribute('data-lawyer-name') || 'المحامي';
        document.getElementById('chatConsultationTitle').innerText = element.querySelector('p')?.innerText || '';
        
        const avatarContainer = document.getElementById('chatLawyerAvatar');
        const avatarUrl = element.getAttribute('data-lawyer-avatar');
        const initial = element.getAttribute('data-lawyer-initial');

        if (avatarUrl && avatarUrl !== 'null') {
            avatarContainer.innerHTML = `<img src="${avatarUrl}" class="w-full h-full object-cover rounded-full">`;
            avatarContainer.classList.replace('bg-slate-800', 'bg-white');
        } else {
            avatarContainer.innerHTML = initial;
            avatarContainer.classList.replace('bg-white', 'bg-slate-800');
        }

        // تحديث شارة الحالة داخل رأس المحادثة الأزرق
        const statusBadge = document.getElementById('chatStatusBadge');
        if (statusBadge) {
            const status = element.getAttribute('data-status');
            const isClosedStatus = (status === 'completed' || status === 'closed');
            const badgeText = statusBadge.querySelector('.badge-text');
            const dotCore = statusBadge.querySelector('.dot-core');
            const dotPing = statusBadge.querySelector('.ping-dot');
            statusBadge.classList.remove('hidden');
            statusBadge.classList.add('inline-flex');
            if (isClosedStatus) {
                badgeText.innerText = 'مغلقة ومكتملة';
                statusBadge.classList.add('text-slate-600');
                statusBadge.classList.remove('text-emerald-700');
                dotCore.classList.add('bg-slate-400');
                dotCore.classList.remove('bg-emerald-500');
                dotPing.classList.add('hidden');
            } else {
                badgeText.innerText = 'نشطة ومدفوعة';
                statusBadge.classList.add('text-emerald-700');
                statusBadge.classList.remove('text-slate-600');
                dotCore.classList.add('bg-emerald-500');
                dotCore.classList.remove('bg-slate-400');
                dotPing.classList.remove('hidden');
                dotPing.classList.add('bg-emerald-400');
            }
        }
    }

    function closeChatSession() {
        if (currentFetchController) {
            currentFetchController.abort(); // إلغاء أي طلب تحميل قديم فوراً
        }
        if (currentSubscription) {
            supabaseClient.removeChannel(currentSubscription);
            currentSubscription = null;
        }
        currentSessionId = null;
        currentConsultationId = null;
        isChatActive = false;
        hideTypingIndicator();
        document.getElementById('chatMessages').innerHTML = '';
        if(window.removeSelectedFile) window.removeSelectedFile(); // تصفير الملف لو موجود
    }

    async function selectConsultation(element, consultationId) {
        const status = element.getAttribute('data-status');
        const isActive = (status === 'paid' || status === 'active');
        const isClosed = (status === 'completed' || status === 'closed');
        
        if (!isActive && !isClosed) {
            showCustomAlert('غير متاح', 'هذه الاستشارة غير متاحة للمحادثة حالياً.', 'warning');
            return;
        }

        // إلغاء وعزل فوري للمحادثة السابقة
        if (currentFetchController) currentFetchController.abort();
        if (currentSubscription) { supabaseClient.removeChannel(currentSubscription); currentSubscription = null; }
        currentSessionId = null;   // امنع الإرسال قبل تجهيز الجلسة الجديدة
        hideTypingIndicator();
        document.getElementById('chatMessages').innerHTML = ''; 
        if(window.removeSelectedFile) window.removeSelectedFile(); // تصفير الملف
        
        currentConsultationId = consultationId;
        currentConsultationStatus = isClosed ? 'closed' : 'active';
        isChatActive = !isClosed;
        
        document.getElementById('noConsultationSelected').classList.add('hidden');
        document.getElementById('chatContainer').classList.replace('hidden', 'flex');
        
        const msgInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const fileInput = document.getElementById('fileInput');
        
        if (isClosed) {
            msgInput.disabled = true; msgInput.placeholder = 'هذه المحادثة مغلقة';
            fileInput.disabled = true;
            sendBtn.disabled = true; sendBtn.classList.add('opacity-50');
        } else {
            msgInput.disabled = false; msgInput.placeholder = 'اكتب رسالتك...';
            fileInput.disabled = false;
            sendBtn.disabled = false; sendBtn.classList.remove('opacity-50');
        }
        
        updateLawyerInfo(element);
        document.querySelectorAll('.consultation-item').forEach(item => item.classList.remove('bg-slate-50', 'border-r-2', 'border-blue-600'));
        element.classList.add('bg-slate-50', 'border-r-2', 'border-blue-600');
        
        const alpineDiv = document.querySelector('[x-data]');
        if (alpineDiv && alpineDiv.__x) alpineDiv.__x.$data.showChat = true;
        
        if (consultationToSessionMap[consultationId]) {
            currentSessionId = consultationToSessionMap[consultationId];
            loadMessages(currentSessionId);
            if (!isClosed) subscribeToChatRealtime(currentSessionId);
            return;
        }

        try {
            // إعداد متحكم إلغاء الطلبات
            currentFetchController = new AbortController();
            const response = await fetch('/api/chat/start', { 
                method: 'POST', 
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN 
                }, 
                body: JSON.stringify({ consultation_id: consultationId }),
                signal: currentFetchController.signal
            });
            
            const data = await response.json();
            if (data.success) {
                currentSessionId = data.conversation_id || data.session_id || data.id;
                consultationToSessionMap[consultationId] = currentSessionId; 
                
                if (currentConsultationId === consultationId) {
                    await loadMessages(currentSessionId);
                    if (!isClosed) subscribeToChatRealtime(currentSessionId);
                }
            }
        } catch(e) {
            if(e.name !== 'AbortError') console.error(e);
        }
    }

    async function loadMessages(sessionId) {
        const container = document.getElementById('chatMessages');
        
        // جلب فوري من الكاش (0 ثانية)
        if (chatCache[sessionId]) {
            if (currentSessionId === sessionId) {
                renderMessagesList(chatCache[sessionId]);
            }
            return;
        }

        // إلغاء أي طلب رسائل قديم
        if (currentFetchController) currentFetchController.abort();
        currentFetchController = new AbortController();

        // مؤشر تحميل أنيق وخفيف في المنتصف فقط
        container.innerHTML = `<div class="flex items-center justify-center w-full h-full"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>`;

        try {
            const response = await fetch(`/api/chat/messages/${sessionId}`, { signal: currentFetchController.signal });
            const data = await response.json();
            if (data.success) {
                chatCache[sessionId] = data.data; 
                if (currentSessionId === sessionId) {
                    renderMessagesList(data.data);
                }
            }
        } catch(e) {
            if (e.name !== 'AbortError' && currentSessionId === sessionId) {
                container.innerHTML = `<div class="text-center py-10 w-full text-rose-500">حدث خطأ في تحميل الرسائل</div>`;
            }
        }
    }

    function renderMessagesList(messagesArray) {
        const container = document.getElementById('chatMessages');
        container.innerHTML = '';
        if (!messagesArray || messagesArray.length === 0) { 
            container.innerHTML = `<div class="text-center py-10 w-full"><p class="text-sm text-slate-400 font-medium">لا توجد رسائل سابقة.</p></div>`; 
            return; 
        }
        messagesArray.forEach(msg => appendSingleMessage(msg, false));
        container.scrollTop = container.scrollHeight;
    }

    function appendSingleMessage(msg, saveToCache = true) {
        const container = document.getElementById('chatMessages'); 
        if (!container) return;

        // منع التكرار المطلق
        if (msg.id && container.querySelector(`[data-msg-id="${msg.id}"]`)) return;

        const emptyMessage = container.querySelector('.text-center'); 
        if (emptyMessage && emptyMessage.innerText.includes('لا توجد رسائل')) emptyMessage.remove();
        
        if (saveToCache && currentSessionId && !msg.is_temp) {
            if(!chatCache[currentSessionId]) chatCache[currentSessionId] = [];
            if(!chatCache[currentSessionId].find(m => m.id === msg.id)) {
                chatCache[currentSessionId].push(msg);
            }
        }

        const isUser = msg.is_user; 
        const alignmentClass = isUser ? 'justify-start' : 'justify-end';
        const bubbleStyle = isUser ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm' : 'bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-sm';
        const textDir = isUser ? 'text-right' : 'text-left';
        const marginSide = isUser ? 'pr-2' : 'pl-2';

        let attachmentsHtml = '';
        // نعرض المرفق إذا كان موجودًا (سواء من السيرفر أو نعرض اسم مبدئي للرسائل المؤقتة)
        if (msg.attachment_path || msg.attachment_name) {
            const fileType = msg.attachment_type || 'file';
            const fileName = msg.attachment_name || 'ملف مرفق';
            const fileUrl = msg.file_url || (msg.attachment_path ? `/api/chat/preview/${encodeURIComponent(msg.attachment_path)}` : '#');
            
            // في حالة الرسالة المؤقتة لا نضع رابط للضغط
            const clickAction = msg.is_temp ? '' : `onclick="viewFileAttachment('${fileUrl}', '${fileName}', '${fileType}')" class="cursor-pointer inline-flex items-center gap-2 ${isUser ? 'bg-blue-700/50 hover:bg-blue-700' : 'bg-gray-100 hover:bg-gray-200'} rounded-lg p-2 transition"`;
            const staticClass = msg.is_temp ? `class="inline-flex items-center gap-2 ${isUser ? 'bg-blue-700/50' : 'bg-gray-100'} rounded-lg p-2"` : '';

            attachmentsHtml = `
                <div class="mt-2 pt-2 border-t ${isUser ? 'border-blue-500/50' : 'border-gray-200'}">
                    <div ${clickAction} ${staticClass}>
                        ${getFileIcon(fileType)}
                        <span class="text-xs font-medium">${fileName}</span>
                    </div>
                </div>
            `;
        }

        const isTemp = msg.is_temp ? 'opacity-50' : '';
        const msgIdAttr = msg.id && !msg.is_temp ? `data-msg-id="${msg.id}"` : '';

        const msgHtml = `
            <div ${msgIdAttr} class="flex ${alignmentClass} animate-fade-in mb-3 w-full ${isTemp}">
                <div class="max-w-[75%] flex flex-col">
                    <span class="text-[11px] text-slate-500 font-bold mb-1.5 ${textDir} ${marginSide}">${isUser ? 'أنت' : escapeHtml(msg.sender_name)}</span>
                    <div class="${bubbleStyle} p-3.5 shadow-sm relative">
                        <p class="text-sm leading-relaxed">${escapeHtml(msg.message || '')}</p>
                        ${attachmentsHtml}
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1.5 font-medium ${textDir} ${marginSide}">${msg.time}</span>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', msgHtml); 
        container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
    }

    // ====================== إضافة استقبال وإدارة الملفات الجديدة ======================
    window.removeSelectedFile = function() {
        currentSelectedFile = null;
        document.getElementById('fileInput').value = '';
        const container = document.getElementById('filePreviewContainer');
        container.innerHTML = '';
        container.classList.add('hidden');
    };

    document.addEventListener('DOMContentLoaded', function() {
        
        // التقاط الملف عند تحديده
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            currentSelectedFile = file;
            
            const container = document.getElementById('filePreviewContainer');
            const isImage = file.type.startsWith('image/');
            const icon = isImage ? 'fa-image' : 'fa-file-alt';
            
            container.innerHTML = `
                <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-100 animate-fade-in shadow-sm">
                    <i class="fas ${icon} text-sm"></i>
                    <span class="text-xs font-bold truncate max-w-[150px]">${file.name}</span>
                    <button onclick="removeSelectedFile()" class="ml-1 text-blue-400 hover:text-rose-500 transition-colors focus:outline-none" title="إلغاء الملف">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.classList.remove('hidden');
        });

        document.getElementById('sendBtn').addEventListener('click', sendMessage);
        document.getElementById('messageInput').addEventListener('keypress', function(e) { 
            if (e.key === 'Enter') { e.preventDefault(); sendMessage(); } 
        });
        document.getElementById('messageInput').addEventListener('input', sendTypingSignal);
    });

    // ====================== إرسال الرسالة (تم تطويرها لدعم FormData والملفات) ======================
    async function sendMessage() {
        const input = document.getElementById('messageInput');
        const rawMessage = input.value.trim();
        
        // التحقق من وجود نص أو ملف وجاهزية الجلسة
        if ((!rawMessage && !currentSelectedFile) || currentConsultationStatus === 'closed') return;
        if (!currentSessionId) return;   // لا ترسل قبل أن تجهز الجلسة
        
        const currentTime = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        
        // رسالة وهمية لتأكيد الإرسال فوراً في الواجهة (Optimistic UI)
        const tempMsg = { 
            message: rawMessage || (currentSelectedFile ? 'جاري إرسال المرفق...' : ''), 
            is_user: true, 
            time: currentTime, 
            sender_name: 'أنت',
            is_temp: true 
        };
        
        if (currentSelectedFile) {
            tempMsg.attachment_name = currentSelectedFile.name;
            tempMsg.attachment_type = currentSelectedFile.name.split('.').pop().toLowerCase();
        }
        
        appendSingleMessage(tempMsg);
        
        const textToSend = rawMessage;
        const fileToSend = currentSelectedFile;
        
        // تفريغ المدخلات فوراً
        input.value = '';
        if(window.removeSelectedFile) window.removeSelectedFile(); 
        
        // بث النص فوراً عبر Realtime للسرعة (الملفات لا تبث إلا بعد الرفع للسيرفر)
        if (currentSubscription && textToSend && !fileToSend) {
            currentSubscription.send({
                type: 'broadcast', event: 'instant_message',
                payload: { sender_id: MY_ID, message: textToSend, time: currentTime }
            });
        }
        
        // تجهيز البيانات عبر FormData لضمان رفع الملفات بنجاح
        const formData = new FormData();
        formData.append('session_id', currentSessionId);
        if (textToSend) formData.append('message', textToSend);
        if (fileToSend) formData.append('attachment', fileToSend); // الـ Backend غالباً يستقبل 'attachment'
        
        try {
            const response = await fetch('/api/chat/send-message', {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                    // ملاحظة هامة: لا تضعي Content-Type مع FormData إطلاقاً، المتصفح سيتكفل بها
                },
                body: formData
            });
            
            const data = await response.json();
            
            // حذف الرسالة المؤقتة الشفافة من الواجهة
            const container = document.getElementById('chatMessages');
            const tempElements = container.querySelectorAll('.opacity-50');
            if(tempElements.length > 0) {
                tempElements[tempElements.length - 1].remove();
            }

            // رسم الرسالة الحقيقية القادمة من السيرفر (بها روابط الملفات الصحيحة)
            if (data.success && data.message) {
                 appendSingleMessage(data.message);
                 
                 // إذا كان هناك ملف، نقوم ببث الرسالة كاملة الآن للمحامي ليراها بملحقاتها
                 if (currentSubscription && fileToSend) {
                     currentSubscription.send({
                         type: 'broadcast', event: 'instant_message',
                         payload: { 
                             sender_id: MY_ID, 
                             message: data.message.message, 
                             time: currentTime,
                             attachment_path: data.message.attachment_path,
                             attachment_name: data.message.attachment_name,
                             attachment_type: data.message.attachment_type
                         }
                     });
                 }
            }
            
        } catch(e) {
            console.error("خطأ في الإرسال:", e);
        }
    }

    // ====================== الـ Realtime والتقييم المباغت ======================
    function subscribeToChatRealtime(sessionId) {
        if (!supabaseClient) return;

        if (currentSubscription) {
            supabaseClient.removeChannel(currentSubscription);
            currentSubscription = null;
        }

        currentSubscription = supabaseClient
            .channel(`realtime-chat-${sessionId}`, { config: { broadcast: { self: false } } })
            .on('broadcast', { event: 'instant_message' }, (payload) => {
                const data = payload.payload || {};
                if (parseInt(data.sender_id) === parseInt(MY_ID)) return;   // حارس إضافي (القناة self:false أصلاً)
                if (currentSessionId !== sessionId) return;
                hideTypingIndicator();
                appendSingleMessage({
                    id: 'rt_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7),
                    message: data.message,
                    is_user: false,
                    time: data.time,
                    sender_name: document.getElementById('chatLawyerName')?.innerText || 'المحامي',
                    attachment_path: data.attachment_path,
                    attachment_name: data.attachment_name,
                    attachment_type: data.attachment_type,
                    file_url: data.attachment_path ? `/api/chat/preview/${encodeURIComponent(data.attachment_path)}` : null
                });
            })
            .on('broadcast', { event: 'typing' }, (payload) => {
                const data = payload.payload || {};
                if (parseInt(data.sender_id) === parseInt(MY_ID)) return;
                if (currentSessionId === sessionId) showTypingIndicator();
            })
            .on('broadcast', { event: 'lawyer_closed_consultation' }, (payload) => {
                if (payload.payload && payload.payload.session_id === currentSessionId) {
                    isChatActive = false;
                    document.getElementById('messageInput').disabled = true;
                    document.getElementById('messageInput').placeholder = 'هذه المحادثة مغلقة (بانتظار التقييم)';
                    document.getElementById('sendBtn').disabled = true;
                    document.getElementById('sendBtn').classList.add('opacity-50');
                    document.getElementById('fileInput').disabled = true;
                    openFeedbackModal();
                }
            })
            .subscribe((status) => {
                // إعادة اشتراك تلقائية عند انقطاع القناة (طالما المحادثة لا تزال مفتوحة ونشطة)
                if (['CHANNEL_ERROR', 'TIMED_OUT', 'CLOSED'].includes(status)) {
                    setTimeout(() => {
                        if (currentSessionId === sessionId && isChatActive) {
                            subscribeToChatRealtime(sessionId);
                        }
                    }, 2500);
                }
            });
    }

    // ====================== مؤشر "يكتب الآن" ======================
    function showTypingIndicator() {
        const el = document.getElementById('typingIndicator');
        if (!el) return;
        el.classList.remove('hidden');
        el.classList.add('flex');
        clearTimeout(typingHideTimeout);
        typingHideTimeout = setTimeout(hideTypingIndicator, 2500);
    }

    function hideTypingIndicator() {
        const el = document.getElementById('typingIndicator');
        if (!el) return;
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    function sendTypingSignal() {
        if (!currentSubscription || !isChatActive) return;
        const now = Date.now();
        if (now - typingSentAt < 1500) return;   // throttle
        typingSentAt = now;
        currentSubscription.send({
            type: 'broadcast', event: 'typing',
            payload: { sender_id: MY_ID }
        });
    }

    // إعادة الاتصال تلقائياً عند عودة الإنترنت
    window.addEventListener('online', () => {
        if (currentSessionId && isChatActive) {
            delete chatCache[currentSessionId];
            loadMessages(currentSessionId);
            subscribeToChatRealtime(currentSessionId);
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }

    /* مؤشر "يكتب الآن" */
    .typing-dot {
        width: 6px; height: 6px; border-radius: 9999px;
        background-color: #ffffff; display: inline-block;
        animation: typingBounce 1.2s infinite ease-in-out;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.15s; }
    .typing-dot:nth-child(3) { animation-delay: 0.30s; }
    @keyframes typingBounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-4px); opacity: 1; }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
    
    #fileViewer {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f3f4f6;
    }
    #fileViewer iframe,
    #fileViewer img {
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    #confirmModal, #alertModal, #fileViewModal {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;
    }
    #fileViewModal .bg-white {
        position: relative;
        z-index: 10000;
        width: 90%;
        max-width: 800px;
        margin: auto;
    }
</style>
@endsection