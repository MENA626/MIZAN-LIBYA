@extends('layouts.lawyer')

@section('title', 'المحادثات')
@section('lawyer_chat_active', 'nav-active')

@section('content')
<div class="px-2 sm:px-4 md:px-8 pb-6 pt-2">
    <div id="chat-board" class="rounded-[26px] border border-slate-200/70 bg-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.25)] overflow-hidden flex flex-col" style="height: calc(100vh - 95px); min-height: 640px;">

        {{-- ====================== الغلاف العلوي ====================== --}}
        <div class="relative overflow-hidden bg-gradient-to-l from-blue-600 via-blue-700 to-blue-800 px-6 sm:px-8 py-5 shrink-0">
            <i class="fas fa-scale-balanced absolute left-10 top-1 text-6xl text-white/10 -rotate-12"></i>
            <i class="fas fa-gavel absolute left-1/3 -bottom-3 text-5xl text-white/10 rotate-12"></i>
            <i class="fas fa-landmark absolute left-28 bottom-1 text-4xl text-white/10"></i>
            <div class="relative">
                <h1 class="flex items-center gap-2.5 text-2xl font-black text-white">
                    <i class="fas fa-comments text-xl"></i> المحادثات
                </h1>
                <p class="mt-1 text-sm font-medium text-white/80">التواصل مع العملاء وإدارة الاستشارات</p>
            </div>
        </div>

        {{-- ====================== جسم المحادثة ====================== --}}
        <div x-data="{ showChat: false }" class="flex flex-col lg:flex-row w-full gap-4 p-4 flex-1 min-h-0">

            {{-- ====================== القائمة اليمنى: أرشيف العملاء الثابت ====================== --}}
            <div :class="showChat ? 'hidden lg:flex' : 'flex'" class="w-full lg:w-80 bg-white rounded-2xl border border-slate-100 flex-col h-full shrink-0 shadow-[0_1px_2px_rgba(15,23,42,0.04)] overflow-hidden">

                <div class="shrink-0 p-4 border-b border-slate-100 bg-white">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="flex items-center gap-2 text-[15px] font-black text-slate-800">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-600/25">
                                <i class="fas fa-users text-xs"></i>
                            </span>
                            العملاء
                        </h3>
                        <span id="clientsCount" class="text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full font-bold">0</span>
                    </div>
                    <div class="relative">
                        <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="searchInput" placeholder="بحث عن عميل..."
                               class="w-full pr-9 pl-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all bg-slate-50">
                    </div>
                </div>

                <div id="clientsList" class="flex-1 overflow-y-auto w-full custom-scrollbar relative">
                    {{-- التحميل الموضعي لقائمة العملاء --}}
                    <div class="absolute inset-0 flex items-center justify-center bg-white/80 z-10" id="clientsLoading">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </div>
            </div>

            {{-- ====================== منطقة المحادثة ====================== --}}
            <div :class="showChat ? 'flex' : 'hidden lg:flex'" class="flex-1 bg-white rounded-2xl border border-slate-100 flex-col h-full shadow-[0_1px_2px_rgba(15,23,42,0.04)] overflow-hidden min-w-0 relative">

                <div id="noClientSelected" class="flex-1 flex flex-col items-center justify-center bg-slate-50/50 w-full h-full">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-white border border-slate-100 shadow-sm rounded-2xl flex items-center justify-center mx-auto mb-5">
                            <i class="fas fa-comments text-3xl text-slate-300"></i>
                        </div>
                        <h4 class="font-black text-slate-700 text-xl mb-2">مساحة العميل</h4>
                        <p class="text-sm text-slate-400">اختر عميلاً من القائمة الجانبية لبدء المراسلة</p>
                    </div>
                </div>

                <div id="chatContainer" class="hidden flex-col h-full w-full relative">

                    <div class="shrink-0 p-4 border-b border-slate-100 bg-white z-10 w-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <button @click="showChat = false" class="lg:hidden w-9 h-9 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors shrink-0 border border-slate-200">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </button>
                                <div class="relative shrink-0">
                                    <div id="chatClientAvatar" class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold shadow-sm text-lg border-2 border-white ring-2 ring-slate-100 overflow-hidden">?</div>
                                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white"></div>
                                </div>
                                <div class="min-w-0">
                                    <h3 id="chatClientName" class="font-bold text-slate-800 text-base truncate">اختر عميلاً</h3>
                                    <p id="chatConsultationTitle" class="text-xs text-slate-500 truncate mt-1 font-medium"></p>
                                    <p id="typingIndicator" class="hidden items-center gap-1 text-xs text-blue-600 font-bold mt-1">
                                        <span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>
                                        <span class="mr-1">يكتب الآن</span>
                                    </p>
                                </div>
                            </div>

                            <button id="endConsultationBtn" onclick="endConsultationDirectly()" class="hidden px-5 py-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 shrink-0 min-w-[130px]">
                                <i class="fas fa-gavel text-sm"></i> <span class="hidden sm:inline">إنهاء الاستشارة</span>
                            </button>
                        </div>
                    </div>

                    <div id="chatMessages" class="flex-1 overflow-y-auto p-5 space-y-5 bg-slate-50/50 w-full custom-scrollbar relative">
                        {{-- مساحة الرسائل --}}
                    </div>

                    {{-- شريط الإرسال --}}
                    <div class="shrink-0 p-4 border-t border-slate-100 bg-white w-full">
                        <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-full border border-slate-200 focus-within:border-blue-400 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all">

                            <div class="relative">
                                <label for="fileInput" class="cursor-pointer w-10 h-10 rounded-full hover:bg-white hover:shadow-sm transition-all text-slate-500 flex items-center justify-center shrink-0">
                                    <i class="fas fa-paperclip text-base"></i>
                                </label>
                                <input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                            </div>

                            <input type="text" id="messageInput" placeholder="اكتب ردك للعميل هنا..."
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
</div>

{{-- ====================== المودالات ====================== --}}
<div id="alertModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[1000] hidden items-center justify-center p-4 w-full h-full">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all text-center p-6 relative">
        <div id="alertIconContainer" class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-orange-50 text-orange-500">
            <i id="alertIcon" class="fas fa-exclamation-triangle text-xl"></i>
        </div>
        <h3 id="alertTitle" class="text-lg font-bold text-slate-800 mb-2">تنبيه</h3>
        <p id="alertMessage" class="text-sm text-slate-600 mb-6 leading-relaxed"></p>
        <button onclick="closeAlertModal()" class="w-full py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all shadow-sm">
            حسناً، فهمت
        </button>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[1000] hidden items-center justify-center p-4 w-full h-full">
    <div class="bg-white rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all text-center p-6 relative">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-rose-50 text-rose-600">
            <i class="fas fa-exclamation-circle text-xl"></i>
        </div>
        <h3 id="confirmTitle" class="text-lg font-bold text-slate-800 mb-2">تأكيد الإجراء</h3>
        <p id="confirmMessage" class="text-sm text-slate-600 mb-6 leading-relaxed"></p>
        <div class="flex gap-3 w-full">
            <button onclick="closeConfirmModal()" class="flex-1 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-all border border-slate-200">
                إلغاء
            </button>
            <button id="confirmActionBtn" class="flex-1 py-2.5 bg-rose-600 text-white rounded-xl text-sm font-bold hover:bg-rose-700 transition-all shadow-sm flex items-center justify-center gap-2">
                نعم، تأكيد
            </button>
        </div>
    </div>
</div>

{{-- مودال عرض الملفات (نظيف وبعيد عن الحواف) --}}
<div id="fileViewModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[1000] hidden items-center justify-center p-4 sm:p-8 w-full h-full">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">
        <div class="flex justify-between items-center p-4 border-b border-slate-100 shrink-0 bg-white">
            <h3 id="viewFileName" class="font-bold text-slate-800 text-base truncate max-w-[70%]"></h3>
            <div class="flex gap-2">
                <button onclick="downloadCurrentFile()" class="text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors w-9 h-9 flex items-center justify-center rounded-lg" title="تحميل الملف">
                    <i class="fas fa-download"></i>
                </button>
                <button onclick="closeFileModal()" class="text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors w-9 h-9 flex items-center justify-center rounded-lg" title="إغلاق">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        {{-- منطقة العرض مع Padding لمنع التصاق الملف بالحواف --}}
        <div id="fileViewer" class="flex-1 overflow-auto p-4 sm:p-8 bg-slate-50 flex items-center justify-center w-full h-full custom-scrollbar">
            {{-- سيتم وضع الصورة أو الـ iframe هنا برمجياً --}}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<script>
    // ====================== المتغيرات العامة ======================
    const MY_ID = parseInt({{ Auth::id() ?? 0 }});
    const CSRF_TOKEN = '{{ csrf_token() }}';

    let currentSessionId = null;
    let conversations = [];
    let searchTerm = '';
    let confirmCallback = null;
    let supabaseClient = null;
    let currentFileUrl = '';
    let currentFileName = '';
    let currentSelectedFile = null;
    let isChatActive = true;
    let currentFetchController = null; // 🛡️ أداة العزل ومنع التداخل

    // ⚡ نظام الكاش الفائق
    const chatCache = {};

    // 🌐 الاشتراكات اللحظية: قناة لكل محادثة (تبقى مفتوحة دائماً للتحديث الفوري)
    const channels = {};            // conversationId -> supabase channel
    let typingHideTimeout = null;   // إخفاء مؤشر "يكتب الآن"
    let typingSentAt = 0;           // throttle لإرسال إشعار الكتابة

    const supabaseUrl = "{{ env('SUPABASE_URL') }}";
    const supabaseAnonKey = "{{ env('SUPABASE_ANON_KEY') }}";

    if (supabaseUrl && supabaseAnonKey) {
        supabaseClient = supabase.createClient(supabaseUrl, supabaseAnonKey, {
            realtime: { params: { eventsPerSecond: 20 } }
        });
    }

    // ====================== المودالات الأساسية ======================
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
        confirmBtn.onclick = async function() { 
            if (confirmCallback) {
                const originalHtml = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التنفيذ...';
                confirmBtn.disabled = true;
                await confirmCallback(); 
                confirmBtn.innerHTML = originalHtml;
                confirmBtn.disabled = false;
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
            iconContainer.className = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-blue-50 text-blue-600';
            icon.className = 'fas fa-info-circle text-xl';
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

    // ====================== عرض الملفات (أنيق وبعيد عن الحواف) ======================
    function getFileIcon(fileType) {
        if (fileType === 'pdf') {
            return `<i class="fas fa-file-pdf text-rose-500 text-lg"></i>`;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileType)) {
            return `<i class="fas fa-file-image text-emerald-500 text-lg"></i>`;
        } else {
            return `<i class="fas fa-file-alt text-blue-500 text-lg"></i>`;
        }
    }

    function viewFileAttachment(fileUrl, fileName, fileType) {
        currentFileUrl = fileUrl;
        currentFileName = fileName;
        document.getElementById('viewFileName').innerText = fileName;
        const viewer = document.getElementById('fileViewer');
        
        // مساحة العرض نظيفة جداً ولا تلتصق بالحواف (استخدمنا object-contain)
        if (fileType === 'pdf') {
            viewer.innerHTML = `<iframe src="${fileUrl}" class="w-full h-full min-h-[60vh] rounded-xl shadow border border-slate-200 bg-white"></iframe>`;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(fileType)) {
            viewer.innerHTML = `
                <div class="relative flex items-center justify-center w-full h-full bg-white rounded-xl shadow border border-slate-200 p-2">
                    <div id="image-loading" class="absolute inset-0 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                    </div>
                    <img src="${fileUrl}" class="max-w-full max-h-[70vh] object-contain rounded-lg" onload="document.getElementById('image-loading')?.remove();" onerror="this.onerror=null; document.getElementById('image-loading').innerHTML='<p class=\\'text-rose-500 font-bold\\'>فشل تحميل الصورة</p>';">
                </div>`;
        } else {
            viewer.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full w-full bg-white rounded-xl shadow border border-slate-200">
                    <div class="text-center p-8">
                        <i class="fas fa-file-download text-5xl text-slate-300 mb-4"></i>
                        <p class="text-slate-600 mb-4 font-medium">هذا الملف يتطلب التحميل لعرضه</p>
                        <button onclick="downloadCurrentFile()" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition font-bold shadow-sm">
                            <i class="fas fa-download ml-2"></i>تحميل الملف
                        </button>
                    </div>
                </div>`;
        }
        document.getElementById('fileViewModal').classList.replace('hidden', 'flex');
    }

    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const previewContainer = document.getElementById('filePreviewContainer');
        if (!previewContainer) return;
        if (files.length === 0) { cancelFileSelection(); return; }
        
        currentSelectedFile = files[0];
        previewContainer.classList.remove('hidden');
        previewContainer.innerHTML = '';
        
        const file = files[0];
        const previewDiv = document.createElement('div');
        previewDiv.className = 'bg-white border border-slate-200 rounded-xl p-2 flex items-center gap-3 shadow-sm';
        const ext = file.name.split('.').pop().toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewDiv.innerHTML = `<img src="${e.target.result}" class="w-10 h-10 object-cover rounded-lg border border-slate-100"><div class="flex-1"><p class="text-xs font-bold text-slate-700 truncate max-w-[150px]">${file.name}</p><p class="text-[10px] text-slate-500">${(file.size / 1024).toFixed(1)} KB</p></div><button onclick="cancelFileSelection()" class="text-rose-500 hover:bg-rose-50 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-times"></i></button>`;
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.innerHTML = `<div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">${getFileIcon(ext)}</div><div class="flex-1"><p class="text-xs font-bold text-slate-700 truncate max-w-[150px]">${file.name}</p><p class="text-[10px] text-slate-500">${(file.size / 1024).toFixed(1)} KB</p></div><button onclick="cancelFileSelection()" class="text-rose-500 hover:bg-rose-50 w-8 h-8 rounded-lg flex items-center justify-center transition"><i class="fas fa-times"></i></button>`;
        }
        previewContainer.appendChild(previewDiv);
    });

    function cancelFileSelection() {
        currentSelectedFile = null;
        if (document.getElementById('fileInput')) document.getElementById('fileInput').value = '';
        const previewContainer = document.getElementById('filePreviewContainer');
        if (previewContainer) { previewContainer.classList.add('hidden'); previewContainer.innerHTML = ''; }
    }

    async function uploadFile(file) {
        if (!currentSessionId) return false;
        if (file.size > 10 * 1024 * 1024) {
            showCustomAlert('خطأ', 'حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت.', 'error');
            return false;
        }
        
        const formData = new FormData();
        formData.append('session_id', currentSessionId);
        formData.append('file', file);
        formData.append('message', '');
        
        const currentTime = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        const tempId = Date.now();
        
        appendSingleMessage({ id: tempId, message: `⏳ جاري الرفع: ${file.name}...`, is_user: true, time: currentTime, sender_name: 'أنت', is_temp: true });
        
        try {
            const response = await fetch('/api/chat/lawyer/send', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' },
                body: formData
            });
            const data = await response.json();
            
            const tempMsg = document.querySelector(`[data-temp-id="${tempId}"]`);
            if (tempMsg) tempMsg.remove();
            
            if (data.success) {
                appendSingleMessage({
                    id: data.message.id,
                    message: data.message.message,
                    is_user: true,
                    time: currentTime,
                    sender_name: 'أنت',
                    attachment_path: data.message.attachment_path,
                    attachment_name: data.message.attachment_name,
                    attachment_type: data.message.attachment_type,
                    attachment_size: data.message.attachment_size,
                    file_url: data.message.file_url
                });
                
                const ch = channels[currentSessionId];
                if (ch) {
                    ch.send({
                        type: 'broadcast', event: 'instant_message',
                        payload: { sender_id: MY_ID, message: data.message.message, time: currentTime, attachment_path: data.message.attachment_path, attachment_name: data.message.attachment_name, attachment_type: data.message.attachment_type }
                    });
                }
                return true;
            } else {
                showCustomAlert('خطأ', data.message || 'فشل في رفع الملف', 'error');
                return false;
            }
        } catch (error) {
            const tempMsg = document.querySelector(`[data-temp-id="${tempId}"]`);
            if (tempMsg) tempMsg.remove();
            showCustomAlert('خطأ', 'حدث خطأ أثناء الرفع', 'error');
            return false;
        }
    }

    // ====================== العزل، التحكم في الطلبات والكاش (المحامي) ======================
    function appendSingleMessage(msg, saveToCache = true) {
        const container = document.getElementById('chatMessages');
        if (!container) return;
        
        if (msg.id && container.querySelector(`[data-msg-id="${msg.id}"]`)) return; // 🔒 عزل تام لمنع التكرار

        const emptyMessage = container.querySelector('.text-center');
        if (emptyMessage && emptyMessage.innerText.includes('لا توجد رسائل')) emptyMessage.remove();
        
        if (saveToCache && currentSessionId) {
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
        if (msg.attachment_path && !msg.is_temp) {
            const fileType = msg.attachment_type || 'file';
            const fileName = msg.attachment_name || 'ملف مرفق';
            const fileUrl = msg.file_url || `/api/chat/lawyer/preview/${encodeURIComponent(msg.attachment_path)}`;
            attachmentsHtml = `<div class="mt-2 pt-2 border-t ${isUser ? 'border-blue-500/50' : 'border-slate-100'}"><div onclick="viewFileAttachment('${fileUrl}', '${fileName}', '${fileType}')" class="inline-flex items-center gap-2 ${isUser ? 'bg-blue-700/50 hover:bg-blue-700' : 'bg-slate-50 hover:bg-slate-100'} rounded-lg p-2 cursor-pointer transition border border-transparent shadow-sm w-full"><div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center shrink-0">${getFileIcon(fileType)}</div><div class="flex-1 min-w-0"><p class="text-xs font-bold truncate">${fileName}</p></div></div></div>`;
        }
        
        const isTemp = msg.is_temp ? 'opacity-50' : '';
        const tempAttr = msg.id && msg.is_temp ? `data-temp-id="${msg.id}"` : '';
        const msgIdAttr = msg.id && !msg.is_temp ? `data-msg-id="${msg.id}"` : '';
        
        const msgHtml = `
            <div ${msgIdAttr} ${tempAttr} class="flex ${alignmentClass} animate-fade-in mb-3 w-full ${isTemp}">
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

    // ====================== النظام اللحظي (Realtime) ======================
    // قناة دائمة لكل محادثة: تلتقط الرسائل فوراً حتى لو المحادثة غير مفتوحة
    function ensureChannel(conv) {
        if (!supabaseClient || !conv || channels[conv.id]) return;
        const id = conv.id;
        const ch = supabaseClient.channel(`realtime-chat-${id}`, {
            config: { broadcast: { self: false } }
        });
        ch.on('broadcast', { event: 'instant_message' }, (p) => handleIncomingMessage(id, p.payload || {}));
        ch.on('broadcast', { event: 'typing' }, (p) => handleTyping(id, p.payload || {}));
        ch.subscribe((status) => {
            // إعادة اتصال تلقائية عند انقطاع القناة
            if (status === 'CHANNEL_ERROR' || status === 'TIMED_OUT' || status === 'CLOSED') {
                if (channels[id]) {
                    try { supabaseClient.removeChannel(ch); } catch (e) {}
                    delete channels[id];
                    setTimeout(() => {
                        const c = conversations.find(x => x.id === id);
                        if (c) ensureChannel(c);
                    }, 2500);
                }
            }
        });
        channels[id] = ch;
    }

    function handleIncomingMessage(sessionId, data) {
        // القناة self:false لا تُعيد بثّ رسائلي، وهذا حارس إضافي
        if (parseInt(data.sender_id) === MY_ID) return;

        const conv = conversations.find(c => c.id === sessionId);

        if (currentSessionId === sessionId) {
            hideTypingIndicator();
            appendSingleMessage({
                id: 'rt_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7),
                message: data.message,
                is_user: false,
                time: data.time,
                sender_name: (conv && conv.client && conv.client.name) || document.getElementById('chatClientName')?.innerText || 'العميل',
                attachment_path: data.attachment_path,
                attachment_name: data.attachment_name,
                attachment_type: data.attachment_type,
                file_url: data.attachment_path ? `/api/chat/lawyer/preview/${encodeURIComponent(data.attachment_path)}` : null
            });
        } else if (conv) {
            conv.unread = (conv.unread || 0) + 1; // مؤشر غير مقروء
        }

        // تحديث وقت آخر رسالة ورفع المحادثة لأعلى القائمة
        if (conv) {
            conv.last_message_time = data.time || conv.last_message_time;
            conversations = [conv, ...conversations.filter(c => c.id !== sessionId)];
        }
        renderClientsList();
    }

    function handleTyping(sessionId, data) {
        if (parseInt(data.sender_id) === MY_ID) return;
        if (currentSessionId !== sessionId) return;
        showTypingIndicator();
    }

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
        if (!currentSessionId || !isChatActive) return;
        const ch = channels[currentSessionId];
        if (!ch) return;
        const now = Date.now();
        if (now - typingSentAt < 1500) return; // throttle لتقليل عدد الإشعارات
        typingSentAt = now;
        try { ch.send({ type: 'broadcast', event: 'typing', payload: { sender_id: MY_ID } }); } catch (e) {}
    }
    
    async function sendMessage() {
        const input = document.getElementById('messageInput');
        const rawMessage = input.value.trim();
        
        if (!rawMessage && !currentSelectedFile) return;
        if (!currentSessionId || !isChatActive) return;
        
        const currentTime = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
        
        if (rawMessage) {
            appendSingleMessage({ message: rawMessage, is_user: true, time: currentTime, sender_name: 'أنت' });

            // بث لحظي فوري للعميل
            const ch = channels[currentSessionId];
            if (ch) {
                try {
                    ch.send({
                        type: 'broadcast', event: 'instant_message',
                        payload: { sender_id: MY_ID, message: rawMessage, time: currentTime }
                    });
                } catch (err) {}
            }

            // إرسال سريع: المظهر فوري، والمزامنة مع الخادم في الخلفية (تصحيح القائمة عند الفشل فقط)
            fetch('/api/chat/lawyer/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ session_id: currentSessionId, message: rawMessage })
            }).then(res => { if (!res.ok) loadConversations(); }).catch(() => loadConversations());

            input.value = '';

            // تحديث القائمة محلياً ورفع المحادثة لأعلى (أسرع من إعادة التحميل الكامل)
            const conv = conversations.find(c => c.id === currentSessionId);
            if (conv) {
                conv.last_message_time = currentTime;
                conversations = [conv, ...conversations.filter(c => c.id !== currentSessionId)];
                renderClientsList();
            }
        }
        
        if (currentSelectedFile) {
            const success = await uploadFile(currentSelectedFile);
            if (success) cancelFileSelection();
        }
        input.focus();
    }

    async function loadConversations() {
        try {
            const response = await fetch('/api/chat/lawyer/conversations');
            const data = await response.json();
            
            if (data.success) {
                // الحفاظ على عدّاد غير المقروء عبر عمليات إعادة التحميل
                const prevUnread = {};
                conversations.forEach(c => { if (c.unread) prevUnread[c.id] = c.unread; });

                conversations = data.data.map(c => ({
                    ...c,
                    unread: (c.id === currentSessionId) ? 0 : (prevUnread[c.id] || 0)
                }));

                // تنظيف قنوات المحادثات التي لم تعد موجودة (منع تسرّب الاشتراكات)
                const activeIds = new Set(conversations.map(c => c.id));
                Object.keys(channels).forEach(idStr => {
                    const id = parseInt(idStr);
                    if (!activeIds.has(id)) {
                        try { supabaseClient.removeChannel(channels[id]); } catch (e) {}
                        delete channels[id];
                    }
                });

                conversations.forEach(ensureChannel); // اشتراك لحظي لكل المحادثات
                renderClientsList();
                document.getElementById('clientsCount').innerText = conversations.length;
                document.getElementById('clientsLoading')?.remove();
            }
        } catch(error) {}
    }
    
    function renderClientsList() {
        const container = document.getElementById('clientsList');
        if (!container) return;
        let filtered = conversations;
        if (searchTerm) {
            filtered = conversations.filter(conv => 
                conv.client?.name?.toLowerCase().includes(searchTerm.toLowerCase()) || 
                conv.consultation_title?.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }
        
        if (!filtered.length) {
            container.innerHTML = '<div class="text-center py-10 text-slate-400 text-sm font-medium">لا توجد محادثات.</div>';
            return;
        }
        
        container.innerHTML = filtered.map(conv => {
            const avatarUrl = conv.client?.avatar || '';
            const initial = conv.client?.initial || 'ع';
            const unread = conv.unread || 0;
            const isActive = currentSessionId === conv.id;
            const avatarHtml = avatarUrl 
                ? `<img src="${avatarUrl}" class="w-11 h-11 rounded-xl object-cover shadow-sm border border-slate-100" onerror="this.outerHTML='<div class=\\'w-11 h-11 rounded-xl bg-slate-800 flex items-center justify-center text-white font-bold text-sm\\'>${initial}</div>'">`
                : `<div class="w-11 h-11 rounded-xl bg-slate-800 flex items-center justify-center text-white font-bold text-sm">${initial}</div>`;
            const unreadBadge = unread > 0
                ? `<span class="shrink-0 min-w-[20px] h-5 px-1.5 bg-blue-600 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center shadow-sm">${unread > 99 ? '99+' : unread}</span>`
                : '';
            const nameClass = unread > 0 ? 'font-extrabold text-slate-900' : 'font-bold text-slate-800';
            const titleClass = unread > 0 ? 'text-slate-700 font-semibold' : 'text-slate-500';

            return `
                <div class="client-item flex items-center gap-3 p-4 hover:bg-slate-50 cursor-pointer transition-all border-b border-slate-50 last:border-0 w-full ${isActive ? 'bg-blue-50/50 border-r-4 border-blue-600' : ''}" 
                     onclick="selectClient(${conv.id}, '${avatarUrl}')">
                    <div class="relative shrink-0">${avatarHtml}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="${nameClass} text-sm truncate">${escapeHtml(conv.client?.name || 'عميل')}</h4>
                            <span class="text-[10px] text-slate-400 font-medium">${conv.last_message_time || ''}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs ${titleClass} truncate">${escapeHtml(conv.consultation_title || 'استشارة')}</p>
                            ${unreadBadge}
                        </div>
                    </div>
                </div>`;
        }).join('');
    }
    
    function selectClient(conversationId, avatarUrl) {
        if (currentFetchController) currentFetchController.abort(); // 🛡️ إلغاء التحميل السابق فوراً
        document.getElementById('chatMessages').innerHTML = ''; // تفريغ الشات فوراً
        hideTypingIndicator();

        currentSessionId = conversationId;
        const selectedConv = conversations.find(c => c.id === conversationId);
        if (selectedConv) selectedConv.unread = 0; // مسح مؤشر غير المقروء عند فتح المحادثة
        renderClientsList();
        
        document.getElementById('noClientSelected').classList.add('hidden');
        document.getElementById('chatContainer').classList.remove('hidden');
        document.getElementById('chatContainer').classList.add('flex');
        
        const mainContainer = document.querySelector('[x-data]');
        if (mainContainer && mainContainer.__x) mainContainer.__x.$data.showChat = true;
        
        const conv = conversations.find(c => c.id === conversationId);
        if (conv) {
            document.getElementById('chatClientName').innerText = conv.client?.name || 'عميل';
            document.getElementById('chatConsultationTitle').innerText = conv.consultation_title || 'استشارة';
            
            const avatarContainer = document.getElementById('chatClientAvatar');
            const initial = conv.client?.initial || 'ع';
            if (avatarUrl && avatarUrl !== 'null' && avatarUrl.trim() !== '') {
                avatarContainer.innerHTML = `<img src="${avatarUrl}" class="w-full h-full object-cover rounded-full" onerror="this.outerHTML='${initial}'">`;
                avatarContainer.classList.replace('bg-slate-800', 'bg-white');
            } else {
                avatarContainer.innerHTML = initial;
                avatarContainer.classList.replace('bg-white', 'bg-slate-800');
            }
            
            if (conv.status !== 'closed') {
                document.getElementById('endConsultationBtn').classList.remove('hidden');
                document.getElementById('messageInput').disabled = false;
                document.getElementById('sendBtn').disabled = false;
                document.getElementById('sendBtn').classList.remove('opacity-50', 'cursor-not-allowed');
                document.getElementById('messageInput').placeholder = 'اكتب ردك للعميل هنا...';
                isChatActive = true;
            } else {
                document.getElementById('endConsultationBtn').classList.add('hidden');
                document.getElementById('messageInput').disabled = true;
                document.getElementById('sendBtn').disabled = true;
                document.getElementById('sendBtn').classList.add('opacity-50', 'cursor-not-allowed');
                document.getElementById('messageInput').placeholder = 'المحادثة مغلقة ومكتملة.';
                isChatActive = false;
            }
        }
        if (conv) ensureChannel(conv);
        loadMessages(conversationId);
    }
    
    async function loadMessages(sessionId) {
        const container = document.getElementById('chatMessages');
        
        // ⚡ كاش فوري: لو المحادثة محفوظة تفتح بـ 0 ثانية
        if (chatCache[sessionId]) {
            if (currentSessionId === sessionId) {
                renderMessagesList(chatCache[sessionId]);
            }
            return;
        }

        // إعداد أداة الإلغاء للطلبات الجديدة
        currentFetchController = new AbortController();
        
        // ⏳ تحميل ذكي في المنتصف فقط
        container.innerHTML = `<div class="flex items-center justify-center w-full h-full"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>`;

        try {
            const response = await fetch(`/api/chat/lawyer/messages/${sessionId}`, { signal: currentFetchController.signal });
            const data = await response.json();
            if (data.success) {
                chatCache[sessionId] = data.data; // الحفظ في الكاش السريع
                if (currentSessionId === sessionId) {
                    renderMessagesList(data.data);
                }
            }
        } catch(e) {
            if (e.name !== 'AbortError' && currentSessionId === sessionId) {
                container.innerHTML = `<div class="text-center py-10 w-full text-rose-500 font-bold">حدث خطأ في تحميل الرسائل</div>`;
            }
        }
    }

    function renderMessagesList(messagesArray) {
        const container = document.getElementById('chatMessages');
        container.innerHTML = '';
        if (!messagesArray || messagesArray.length === 0) {
            container.innerHTML = `<div class="text-center py-10 w-full"><p class="text-sm text-slate-400 font-medium">لا توجد رسائل سابقة.</p><p class="text-xs text-slate-400 mt-1">المحادثة محمية وتتمتع بالسرية التامة.</p></div>`;
            return;
        }
        messagesArray.forEach(msg => appendSingleMessage(msg, false));
        container.scrollTop = container.scrollHeight;
    }

    // ✅ دالة إنهاء الاستشارة المحدثة السريعة
    function endConsultationDirectly() {
        showCustomConfirm('إنهاء الاستشارة', 'هل أنت متأكد من إنهاء هذه الاستشارة؟ سيتم إغلاق المحادثة وتحويل الأتعاب إلى محفظتك.', async () => {
            try {
                // إغلاق الواجهة فوراً (Optimistic UI)
                const conv = conversations.find(c => c.id === currentSessionId);
                if (conv) conv.status = 'closed';
                isChatActive = false;
                
                document.getElementById('endConsultationBtn').classList.add('hidden');
                document.getElementById('messageInput').disabled = true;
                document.getElementById('sendBtn').disabled = true;
                document.getElementById('sendBtn').classList.add('opacity-50', 'cursor-not-allowed');
                document.getElementById('messageInput').placeholder = 'المحادثة مغلقة ومكتملة.';
                selectClient(currentSessionId, null);

                const response = await fetch('/api/chat/lawyer/end-consultation', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ session_id: currentSessionId })
                });
                const data = await response.json();
                
                if (data.success) {
                    const ch = channels[currentSessionId];
                    if (ch) {
                        ch.send({
                            type: 'broadcast',
                            event: 'lawyer_closed_consultation',
                            payload: { session_id: currentSessionId }
                        });
                    }
                    showCustomAlert('تم الإنهاء', 'تم إنهاء الاستشارة وتحويل الأتعاب بنجاح.', 'info');
                }
            } catch(e) {
                console.error(e);
            }
        });
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // ====================== التهيئة ======================
    document.getElementById('searchInput')?.addEventListener('input', (e) => {
        searchTerm = e.target.value;
        renderClientsList();
    });
    
    document.getElementById('sendBtn')?.addEventListener('click', sendMessage);
    document.getElementById('messageInput')?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    // إشعار "يكتب الآن" للطرف الآخر أثناء الكتابة
    document.getElementById('messageInput')?.addEventListener('input', sendTypingSignal);

    // إعادة الاتصال تلقائياً عند عودة الإنترنت: تحديث القائمة والرسائل الحالية
    window.addEventListener('online', () => {
        loadConversations();
        if (currentSessionId) {
            delete chatCache[currentSessionId];
            loadMessages(currentSessionId);
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        loadConversations();
    });
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
    .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
    .client-item { transition: all 0.2s ease; }
    .client-item:hover { transform: translateX(-2px); }
    /* مؤشر "يكتب الآن" */
    #typingIndicator .typing-dot { width: 6px; height: 6px; border-radius: 9999px; background-color: #2563eb; display: inline-block; animation: typingBlink 1.2s infinite ease-in-out; }
    #typingIndicator .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    #typingIndicator .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBlink { 0%, 60%, 100% { opacity: 0.25; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-2px); } }
    /* فرض خط Cairo مع استثناء أيقونات FontAwesome حتى لا تختفي */
    #chat-board, #chat-board *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]),
    #alertModal *:not(i):not([class*="fa-"]),
    #confirmModal *:not(i):not([class*="fa-"]),
    #fileViewModal *:not(i):not([class*="fa-"]) {
        font-family: 'Cairo','Tajawal',sans-serif !important;
    }
</style>
@endsection
