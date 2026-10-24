{{-- resources/views/admin/settings.blade.php --}}
@extends('layouts.admin')

@section('title', 'الإعدادات')
@section('page_title', 'الإعدادات')
@section('page_subtitle', 'تعديل معلومات حسابك')

@section('admin_settings_active', 'nav-active')

@section('content')
<style>
    input[type="checkbox"].sr-only {
        position: absolute; width: 1px; height: 1px; padding: 0;
        margin: -1px; overflow: hidden; clip: rect(0,0,0,0);
        white-space: nowrap; border-width: 0;
    }
    .settings-tab.tab-active {
        color: #3b82f6 !important;
        background: #eff6ff !important;
    }
    .settings-tab.tab-active i {
        color: #3b82f6 !important;
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 max-w-[1400px]">
    
    {{-- القائمة الجانبية --}}
<div class="lg:col-span-1">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden sticky top-28">
        
        <div class="p-5 text-center border-b border-slate-100">
            <div class="relative inline-block">
                {{-- أيقونة المستخدم دائرية --}}
                <div class="w-20 h-20 rounded-full overflow-hidden bg-slate-800 flex items-center justify-center mx-auto border-2 border-white shadow-sm">
                    <i class="fas fa-user-tie text-slate-400 text-3xl"></i>
                </div>
            </div>
            <h3 class="font-semibold text-slate-800 mt-2.5 text-sm">{{ auth()->user()->name }}</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ auth()->user()->email }}</p>
            <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[11px] font-medium">
                <i class="fas fa-shield-alt text-[9px]"></i> مدير النظام
            </span>
        </div>
        
        <div class="p-2.5 space-y-0.5">
            <button type="button" class="settings-tab w-full text-right px-3.5 py-2.5 rounded-lg transition-colors flex items-center gap-3 text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="profile">
                <i class="fas fa-user-cog w-4 text-center text-blue-500 text-xs"></i>
                <span>الملف الشخصي</span>
            </button>
            <button type="button" class="settings-tab w-full text-right px-3.5 py-2.5 rounded-lg transition-colors flex items-center gap-3 text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="security">
                <i class="fas fa-shield-alt w-4 text-center text-blue-500 text-xs"></i>
                <span>الامان وكلمة المرور</span>
            </button>
            <button type="button" class="settings-tab w-full text-right px-3.5 py-2.5 rounded-lg transition-colors flex items-center gap-3 text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="notifications">
                <i class="fas fa-bell w-4 text-center text-blue-500 text-xs"></i>
                <span>الإشعارات</span>
            </button>

            {{-- فاصل --}}
            <div class="my-2 border-t border-slate-100"></div>

            {{-- زر حذف الحساب --}}
            <button type="button" 
                    onclick="document.getElementById('deleteAccountModal').classList.remove('hidden'); document.getElementById('deleteAccountModal').classList.add('flex');" 
                    class="w-full text-right px-3.5 py-2.5 rounded-lg transition-colors flex items-center gap-3 text-sm font-medium text-red-500 hover:bg-red-50 group">
                <i class="fas fa-trash-alt w-4 text-center text-red-400 group-hover:text-red-500 text-xs"></i>
                <span>حذف الحساب</span>
            </button>
        </div>
    </div>
</div>

    {{-- ====================== محتوى الإعدادات ====================== --}}
    <div class="lg:col-span-2">
    
    {{-- رسائل النجاح والخطأ --}}
    @if(session('success'))
        <div class="alert-message mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-700 text-sm font-semibold">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle text-emerald-500"></i>
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-message mb-5 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 text-red-700 text-sm font-semibold">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            {{ session('error') }}
        </div>
    @endif

        {{-- ====================== تبويب الملف الشخصي ====================== --}}
        <div id="tab-profile" class="settings-content hidden">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-slate-800 text-lg font-bold flex items-center gap-2">
                <i class="fas fa-user text-blue-600 text-base"></i>
                الملف الشخصي
            </h3>
            <p class="text-xs text-slate-400 mt-1">تحديث بياناتك الشخصية وصورة حسابك</p>
        </div>
                
                <form id="profile-form" action="{{ route('admin.settings.profile') }}" method="POST" class="p-5">
                    @csrf
                    @method('PUT')

                    <div class="mb-6 flex flex-col items-center">
                        {{-- تم تثبيت الأيقونة وإلغاء زر الرفع --}}
                        <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-800 flex items-center justify-center border-2 border-white shadow-sm">
                            <i class="fas fa-user-tie text-slate-400 text-4xl"></i>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2 font-medium">صورة الحساب الرسمية للإدارة</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-slate-600">الاسم الكامل</label>
                            <div class="relative">
                                <i class="fas fa-user absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                                <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full pr-9 pl-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 text-slate-600">البريد الإلكتروني</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                                <input type="email" value="{{ auth()->user()->email }}" class="w-full pr-9 pl-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium bg-slate-50 text-slate-400 cursor-not-allowed" readonly>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 mr-1">
                                <i class="fas fa-lock text-[9px] ml-0.5"></i> لايمكن تعديل البريد الاكتروني 
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-save text-[11px]"></i> حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

{{-- ====================== تبويب الأمان وكلمة المرور ====================== --}}
<div id="tab-security" class="settings-content hidden">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-slate-800 text-lg font-bold flex items-center gap-2">
                <i class="fas fa-shield-alt text-blue-600 text-base"></i>
                الأمان وكلمة المرور
            </h3>
            <p class="text-xs text-slate-400 mt-1">حافظ على أمان حسابك بتحديث كلمة المرور بانتظام</p>
        </div>

        <form action="{{ route('admin.settings.password') }}" method="POST" id="password-form" class="p-5">
            @csrf
            @method('PUT')

            {{-- نصيحة أمنية بتصميم تفاعلي --}}
            <div class="mb-6 p-4 bg-white border border-blue-100 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4 group">
                <div class="relative w-10 h-10 flex items-center justify-center shrink-0 mt-0.5">
                    <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-75"></div>
                    <div class="relative w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200">
                        <i class="fas fa-shield-halved text-white text-sm"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        نصيحة أمنية 
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    </p>
                    <p class="text-[13px] text-slate-500 mt-1 leading-relaxed">
                        لتعزيز حماية حسابك، نوصي باستخدام كلمة مرور فريدة تتضمن <span class="font-semibold text-blue-600">أحرفاً، أرقاماً، ورموزاً خاصة</span>.
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                {{-- كلمة المرور الحالية --}}
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-slate-600">كلمة المرور الحالية</label>
                    <div class="relative w-full">
                        <i class="fas fa-lock absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                        <input type="password" id="current_password" name="current_password" class="w-full pr-9 pl-10 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500" placeholder="كلمة المرور الحالية" required>
                        <button type="button" onclick="togglePassword('current_password', this)" class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-slate-400 hover:text-blue-600 focus:outline-none z-10">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الحقول الجديدة --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-600">كلمة المرور الجديدة</label>
                        <div class="relative w-full">
                            <i class="fas fa-key absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                            <input type="password" id="password" name="password" class="w-full pr-9 pl-10 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500" placeholder="كلمة المرور الجديدة" required>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-slate-400 hover:text-blue-600 focus:outline-none z-10">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                        {{-- الرسالتان تحت حقل كلمة المرور الجديدة --}}
                        <p id="error-length" class="text-xs text-red-500 mt-1 hidden">كلمة المرور يجب أن تكون 8 أحرف على الأقل.</p>
                        <p id="error-match" class="text-xs text-red-500 mt-1 hidden">كلمة المرور غير متطابقة.</p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-slate-600">تأكيد كلمة المرور</label>
                        <div class="relative w-full">
                            <i class="fas fa-check-double absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 text-xs"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full pr-9 pl-10 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500" placeholder="أعد إدخال كلمة المرور" required>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-slate-400 hover:text-blue-600 focus:outline-none z-10">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-4 mt-5">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-key text-[11px]"></i> تحديث كلمة المرور
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ====================== تبويب الإشعارات ====================== --}}
        <div id="tab-notifications" class="settings-content hidden">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-slate-800 text-lg font-bold flex items-center gap-2">
                <i class="fas fa-bell text-blue-600 text-base"></i>
                الإشعارات
            </h3>
            <p class="text-xs text-slate-400 mt-1">تحكم في التنبيهات التي ترغب باستلامها</p>
                </div>

                <form action="{{ route('admin.settings.notifications') }}" method="POST" class="p-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2.5">
                        @php
                            $notifications = [
                                ['name' => 'app_notifications', 'label' => 'إشعارات المنصة', 'desc' => 'الإشعارات داخل لوحة التحكم', 'icon' => 'fa-bell'],
                                ['name' => 'new_cases_alerts', 'label' => 'القضايا الجديدة', 'desc' => 'تنبيه عند رفع قضية جديدة', 'icon' => 'fa-gavel'],
                                ['name' => 'lawyer_verifications_alerts', 'label' => 'طلبات التوثيق', 'desc' => 'تنبيه عند طلب توثيق حساب محامي جديد', 'icon' => 'fa-id-card'],
                                ['name' => 'finance_alerts', 'label' => 'تنبيهات مالية', 'desc' => 'السحوبات والبلاغات المالية', 'icon' => 'fa-coins'],
                            ];
                        @endphp

                        @foreach($notifications as $notif)
                            <div class="flex items-center justify-between p-3.5 border border-slate-100 rounded-lg bg-white hover:border-slate-200 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                        <i class="fas {{ $notif['icon'] }} text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm text-slate-700">{{ $notif['label'] }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $notif['desc'] }}</div>
                                    </div>
                                </div>
                                
                                <label for="{{ $notif['name'] }}" class="relative inline-flex items-center cursor-pointer shrink-0 mr-2">
                                    <input type="checkbox" 
                                           id="{{ $notif['name'] }}" 
                                           name="{{ $notif['name'] }}" 
                                           value="1" 
                                           class="sr-only peer" 
                                           {{ (auth()->user()->notification_settings[$notif['name']] ?? true) ? 'checked' : '' }}>
                                    
                                    <div class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 transition-colors relative
                                                after:content-[''] after:absolute after:top-[2px] after:right-[2px] 
                                                peer-checked:after:right-[18px] after:bg-white after:rounded-full 
                                                after:h-4 after:w-4 after:transition-all after:shadow-sm">
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-4 mt-5">
                        <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-save text-[11px]"></i> حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- نافذة تأكيد حذف الحساب --}}
<div id="deleteAccountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] {{ $errors->has('delete_password') ? 'flex' : 'hidden' }} items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-0 mx-auto shadow-2xl overflow-hidden transform transition-all">
        
        <div class="p-6 pb-4 bg-red-50 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">حذف الحساب نهائياً</h3>
                    <p class="text-xs text-red-600/70 mt-0.5">سيتم حذف بياناتك. لا يمكن التراجع عن هذا الإجراء.</p>
                </div>
            </div>
        </div>
        
        {{-- تأكد من تحديث المسار إلى المسار الصحيح للأدمن --}}
        <form action="{{ route('admin.settings.deleteAccount') }}" method="POST" class="p-6">
            @csrf
            @method('DELETE')
            
            <div class="mb-5">
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 mb-5">
                    <i class="fas fa-info-circle text-amber-500 mt-0.5 text-sm"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        سيتم حذف حسابك وجميع بياناتك المرتبطة به بشكل دائم. لا يمكن استرجاع هذه البيانات بعد الحذف.
                    </p>
                </div>
            
                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2 text-slate-700">أدخل كلمة المرور لتأكيد الحذف</label>
                    <input type="password" name="delete_password" 
                           class="w-full p-3 border {{ $errors->has('delete_password') ? 'border-red-500 ring-1 ring-red-500' : 'border-slate-200' }} rounded-xl outline-none focus:ring-2 focus:ring-red-500/20" 
                           placeholder="كلمة المرور الحالية" required>
                    
                    @if ($errors->has('delete_password'))
                        <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('delete_password') }}
                        </p>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="document.getElementById('deleteAccountModal').classList.add('hidden'); document.getElementById('deleteAccountModal').classList.remove('flex');" 
                            class="flex-1 py-3 px-4 bg-slate-100 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-200 transition-all">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="flex-1 py-3 px-4 bg-red-500 text-white rounded-xl font-semibold text-sm hover:bg-red-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-500/20">
                        <i class="fas fa-trash-alt text-xs"></i> تأكيد الحذف
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* إخفاء أيقونة العين الافتراضية للمتصفح */
    input::-ms-reveal,
    input::-ms-clear {
        display: none !important;
    }

    input::-webkit-contacts-auto-fill-button {
        visibility: hidden !important;
        display: none !important;
    }

    input[type="password"]::-webkit-reveal {
        display: none !important;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. الجزء الخاص بالتبويبات
    const tabs = document.querySelectorAll(".settings-tab");
    const contents = document.querySelectorAll(".settings-content");
    const STORAGE_KEY = 'admin_settings_active_tab';

    // مسح الـ localStorage عند الريفرش العادي فقط
    if (performance.getEntriesByType("navigation")[0].type === 'reload') {
        localStorage.removeItem(STORAGE_KEY);
    }

    function activateTab(tabName) {
        let targetTabName = tabName || 'profile';
        const targetContent = document.getElementById(`tab-${targetTabName}`);
        
        if (!targetContent) { targetTabName = 'profile'; }
        
        tabs.forEach(btn => {
            const isActive = btn.getAttribute("data-tab") === targetTabName;
            btn.classList.toggle("tab-active", isActive);
            
            const existingChevron = btn.querySelector('.fa-chevron-left');
            if (isActive && !existingChevron) {
                const chevron = document.createElement('i');
                chevron.className = 'fas fa-chevron-left mr-auto text-[10px] opacity-60 text-blue-600';
                btn.appendChild(chevron);
            } else if (!isActive && existingChevron) {
                existingChevron.remove();
            }
        });

        contents.forEach(content => {
            content.classList.toggle("hidden", content.id !== `tab-${targetTabName}`);
        });
    }

    const savedTab = localStorage.getItem(STORAGE_KEY);
    activateTab(savedTab);

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            const tabName = this.getAttribute("data-tab");
            localStorage.setItem(STORAGE_KEY, tabName);
            activateTab(tabName);
        });
    });

    // عند إرسال أي فورم، احفظ التبويب الحالي في localStorage قبل الريلود
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const activeTabBtn = document.querySelector('.settings-tab.tab-active');
            if (activeTabBtn) {
                localStorage.setItem(STORAGE_KEY, activeTabBtn.getAttribute('data-tab'));
            }
        });
    });

    // 2. التحقق من كلمة المرور (Client-side validation)
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const errorLength = document.getElementById('error-length');
            const errorMatch = document.getElementById('error-match');

            // إعادة ضبط الأخطاء
            if (errorLength) errorLength.classList.add('hidden');
            if (errorMatch) errorMatch.classList.add('hidden');

            let isValid = true;

            // 1. فحص الطول أولاً
            if (password.length < 8) {
                if (errorLength) errorLength.classList.remove('hidden');
                isValid = false;
            } 
            // 2. فحص التطابق: لا نفحصه إلا إذا كان الطول صحيحاً (>= 8)
            else if (password !== confirm) {
                if (errorMatch) errorMatch.classList.remove('hidden');
                isValid = false;
            }

            // إذا كانت هناك أخطاء، نمنع الإرسال
            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // 3. إخفاء التنبيهات تلقائياً
    const alerts = document.querySelectorAll('.alert-message, .alert-success, .alert-error');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => { alert.remove(); }, 500);
            });
        }, 5000); 
    }
});

// دالة إظهار وإخفاء كلمة المرور
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection