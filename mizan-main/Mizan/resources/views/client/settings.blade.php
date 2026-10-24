@extends('layouts.app')

@section('title', 'الإعدادات ')
@section('page_title', 'الإعدادات')
@section('page_subtitle', 'تعديل بياناتك الشخصية وإعدادات الحساب')

@section('client_settings_active', 'nav-active')

@section('content')
<style>
    /* Card styling */
    .settings-card {
        background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
        border: 1px solid rgba(15, 106, 253, 0.12);
        border-radius: 1rem;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .settings-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(15, 106, 253, 0.08);
        background: linear-gradient(135deg, rgba(15, 106, 253, 0.04) 0%, transparent 100%);
    }
    .settings-card-body {
        padding: 1.5rem;
    }

    /* Input styling */
    .s-input {
        width: 100%;
        padding: 0.625rem 2.5rem 0.625rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
        background: #fff;
        font-family: 'Cairo', sans-serif;
    }
    .s-input:focus {
        border-color: #0f6afd;
        box-shadow: 0 0 0 4px rgba(15, 106, 253, 0.08);
    }
    .s-input[readonly] {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }

    /* Icon inside input */
    .s-input-icon {
        position: absolute;
        top: 50%;
        right: 0.875rem;
        transform: translateY(-50%);
        color: #0f6afd;
        font-size: 0.875rem;
        pointer-events: none;
    }

    /* Save button */
    .s-btn-save {
        padding: 0.75rem 2rem;
        background: linear-gradient(135deg, #0f6afd 0%, #0052d6 100%);
        color: #fff;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(15, 106, 253, 0.25);
    }
    .s-btn-save:hover {
        background: linear-gradient(135deg, #0052d6 0%, #003fa3 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(15, 106, 253, 0.35);
    }

    /* Sidebar tab active */
    .settings-tab.tab-active {
        color: #0f6afd !important;
        background: rgba(15, 106, 253, 0.06) !important;
    }
    .settings-tab.tab-active i {
        color: #0f6afd !important;
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-4 md:px-8 pb-8 max-w-[1400px] mx-auto">
    
    {{-- ====================== Sidebar ====================== --}}
<div class="lg:col-span-1">
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden lg:sticky lg:top-28 shadow-sm">
        
        {{-- بيانات المستخدم --}}
        <div class="p-6 text-center border-b border-slate-100 bg-gradient-to-b from-slate-50/80 to-white">
            <div class="relative inline-block group">
                {{-- الصورة الشخصية مع تدرج لوني فاقع وظل متوهج --}}
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-blue-700 via-blue-500 to-blue-300 flex items-center justify-center text-white text-3xl font-bold mx-auto border-4 border-white shadow-[0_0_20px_rgba(37,99,235,0.3)] transition-transform duration-300 group-hover:scale-105">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('uploads/avatars/' . auth()->user()->avatar) }}" 
                             alt="Profile Picture" 
                             class="w-full h-full object-cover">
                    @else
                        {{ mb_substr(auth()->user()->name, 0, 1, 'UTF-8') }}
                    @endif
                </div>
                
                {{-- نموذج رفع الصورة المخفي --}}
                <form id="avatar-form" action="{{ route('client.settings.profile') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    @method('PUT')
                    <input type="file" id="avatar_upload" name="avatar" accept="image/*" onchange="this.form.submit()">
                </form>

                {{-- زر الكاميرا المعدل لفتح الـ input داخل النموذج --}}
                <label for="avatar_upload" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-blue-400 text-white flex items-center justify-center hover:shadow-lg transition-all duration-300 shadow-md cursor-pointer hover:scale-110 border-2 border-white">
                    <i class="fas fa-camera text-[10px]"></i>
                </label>
            </div>
            <h3 class="font-bold text-slate-800 mt-3 text-lg">{{ auth()->user()->name }}</h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->email }}</p>
        </div>
        
        {{-- قائمة التبويبات --}}
        <div class="p-3 flex overflow-x-auto lg:block lg:space-y-1 snap-x scrollbar-hide border-b lg:border-none border-slate-100">
            <button type="button" class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-50" data-tab="profile">
                <i class="fas fa-user w-5 text-center text-blue-600 text-xs"></i>
                <span>الملف الشخصي</span>
            </button>
            <button type="button" class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-50" data-tab="security">
                <i class="fas fa-shield-alt w-5 text-center text-blue-600 text-xs"></i>
                <span>الأمان وكلمة المرور</span>
            </button>
            <button type="button" class="settings-tab flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-50" data-tab="notifications">
                <i class="fas fa-bell w-5 text-center text-blue-600 text-xs"></i>
                <span>الإشعارات</span>
            </button>
        </div>
        
        {{-- حذف الحساب + تسجيل الخروج --}}
        <div class="p-3 border-t border-slate-100 flex lg:block overflow-x-auto lg:space-y-1 snap-x scrollbar-hide">
            <button type="button" onclick="document.getElementById('deleteAccountModal').classList.remove('hidden'); document.getElementById('deleteAccountModal').classList.add('flex');" class="flex-shrink-0 snap-center lg:w-full text-center lg:text-right px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition-all flex items-center justify-center lg:justify-start gap-2 lg:gap-3 text-sm font-semibold">
                <i class="fas fa-trash-alt w-5 text-center text-red-400 text-xs"></i>
                <span>حذف الحساب</span>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-right px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 transition-all flex items-center gap-3 text-sm font-semibold">
                    <i class="fas fa-sign-out-alt w-5 text-center text-slate-400 text-xs"></i>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
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
    <div class="settings-card">
        <div class="settings-card-header">
            <h3 class="text-slate-800 text-lg font-bold flex items-center gap-2">
                <i class="fas fa-user text-blue-600 text-base"></i>
                الملف الشخصي
            </h3>
            <p class="text-xs text-slate-400 mt-1">تحديث بياناتك الشخصية وصورة حسابك</p>
        </div>
        
        <form id="profile-form" action="{{ route('client.settings.profile') }}" method="POST" enctype="multipart/form-data" class="settings-card-body">
            @csrf
            @method('PUT')

            {{-- صورة الملف الشخصي --}}
            <div class="mb-8 flex flex-col items-center">
                <div class="relative group">
                    <div class="w-28 h-28 rounded-full overflow-hidden bg-gradient-to-tr from-blue-600 to-blue-400 flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-xl transition-transform duration-300 group-hover:scale-105">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('uploads/avatars/' . auth()->user()->avatar) }}" 
                                 alt="Profile" 
                                 class="w-full h-full object-cover">
                        @else
                            {{ mb_substr(auth()->user()->name, 0, 1, 'UTF-8') }}
                        @endif
                    </div>
                    {{-- زر الكاميرا --}}
                    <label for="avatar_upload" class="absolute bottom-0 right-1 w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-all duration-300 shadow-lg cursor-pointer hover:scale-110 border-2 border-white">
                        <i class="fas fa-camera text-sm"></i>
                        <input type="file" id="avatar_upload" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
                    </label>
                </div>
                <p class="text-xs text-slate-400 mt-3">اضغط على أيقونة الكاميرا لتغيير الصورة</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                {{-- الاسم الكامل --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">الاسم الكامل</label>
                    <div class="relative">
                        <i class="fas fa-user s-input-icon text-blue-600"></i>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" class="s-input" placeholder="أدخل اسمك الكامل">
                    </div>
                </div>
                {{-- رقم الهاتف --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">رقم الهاتف</label>
                    <div class="relative">
                        <i class="fas fa-phone s-input-icon text-blue-600"></i>
                        <input type="tel" name="phone" value="{{ auth()->user()->phone }}" class="s-input" placeholder="09XXXXXXXX">
                    </div>
                </div>
            </div>

            {{-- البريد الإلكتروني (للقراءة فقط) --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-slate-700">البريد الإلكتروني</label>
                <div class="relative">
                    <i class="fas fa-envelope s-input-icon text-blue-600"></i>
                    <input type="email" value="{{ auth()->user()->email }}" class="s-input" readonly>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5 mr-1">
                    <i class="fas fa-lock text-[9px] ml-1"></i>
                    لا يمكن تعديل البريد الإلكتروني
                </p>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button type="submit" class="s-btn-save">
                    <i class="fas fa-save text-sm"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ====================== تبويب الأمان وكلمة المرور ====================== --}}
<div id="tab-security" class="settings-content hidden">
    <div class="settings-card">
        <div class="settings-card-header">
            <h3 class="text-slate-800 text-lg font-bold flex items-center gap-2">
                <i class="fas fa-shield-alt text-blue-600 text-base"></i>
                الأمان وكلمة المرور
            </h3>
            <p class="text-xs text-slate-400 mt-1">حافظ على أمان حسابك بتحديث كلمة المرور بانتظام</p>
        </div>

        <form action="{{ route('client.settings.password') }}" method="POST" class="settings-card-body">
            @csrf

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

            <div class="space-y-5">
                {{-- كلمة المرور الحالية --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">كلمة المرور الحالية</label>
                    <div class="relative">
                        <i class="fas fa-lock s-input-icon text-blue-600"></i>
                        <input type="password" id="current_password" name="current_password" class="s-input pr-10 @error('current_password') border-red-500 @enderror" placeholder="أدخل كلمة المرور الحالية" required>
                        <button type="button" onclick="togglePassword('current_password', this)" class="absolute left-3 top-3 text-slate-400 hover:text-blue-600 focus:outline-none">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1.5 mr-1"><i class="fas fa-exclamation-circle ml-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- الحقلان الجديدان في نفس السطر --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- كلمة المرور الجديدة --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">كلمة المرور الجديدة</label>
                        <div class="relative">
                            <i class="fas fa-key s-input-icon text-blue-600"></i>
                            <input type="password" id="password" name="password" class="s-input pr-10 @error('password') border-red-500 @enderror" placeholder="كلمة المرور الجديدة" required>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute left-3 top-3 text-slate-400 hover:text-blue-600 focus:outline-none">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p id="password-error" class="text-xs text-red-500 mt-1.5 mr-1 hidden"><i class="fas fa-exclamation-circle ml-1"></i>كلمة المرور يجب أن تكون 8 أحرف على الأقل.</p>
                        @error('password')
                            <p class="text-xs text-red-500 mt-1.5 mr-1"><i class="fas fa-exclamation-circle ml-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- تأكيد كلمة المرور --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">تأكيد كلمة المرور الجديدة</label>
                        <div class="relative">
                            <i class="fas fa-check-double s-input-icon text-blue-600"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="s-input pr-10" placeholder="أعد إدخال كلمة المرور" required>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute left-3 top-3 text-slate-400 hover:text-blue-600 focus:outline-none">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-5 mt-6">
                <button type="submit" class="s-btn-save">
                    <i class="fas fa-key text-sm"></i> تحديث كلمة المرور
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ====================== تبويب الإشعارات ====================== --}}
<div id="tab-notifications" class="settings-content hidden">
    <div class="settings-card">
        <div class="settings-card-header">
            <h3 class="text-slate-800 text-lg font-bold flex items-center gap-2">
                <i class="fas fa-bell text-blue-600 text-base"></i>
                الإشعارات
            </h3>
            <p class="text-xs text-slate-400 mt-1">تحكم في التنبيهات التي ترغب باستلامها</p>
        </div>

        <form action="{{ route('client.settings.notifications') }}" method="POST" class="settings-card-body">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @php
                    $notifications = [
                        ['name' => 'app_notifications', 'label' => 'إشعارات التطبيق', 'desc' => 'التحكم الرئيسي في فتح أو إغلاق كل الإشعارات', 'icon' => 'fa-bell'],
                        ['name' => 'new_consultation', 'label' => 'تحديثات الاستشارات', 'desc' => 'إشعار عند قبول او رفض استشارتك مع المحاميين وعمليات الدفع', 'icon' => 'fa-file-alt'],
                        ['name' => 'new_bookings', 'label' => 'تحديثات المواعيد', 'desc' => 'إشعار عند قبول او رفض مواعيدك مع المحامين', 'icon' => 'fa-calendar-check'],
                        ['name' => 'new_messages', 'label' => 'رسائل المحادثات', 'desc' => 'إشعار عند استلام رسالة نصية جديدة من المحامي', 'icon' => 'fa-comment-dots'],
                    ];
                @endphp

                @foreach($notifications as $notif)
                    <div class="flex items-center justify-between p-4 border border-slate-100 rounded-xl bg-white hover:border-blue-600/20 hover:shadow-sm transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-colors">
                                <i class="fas {{ $notif['icon'] }} text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="font-bold text-sm text-slate-700">{{ $notif['label'] }}</div>
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
                            
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:-translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all transition-colors">
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-5 mt-6">
                <button type="submit" class="s-btn-save">
                    <i class="fas fa-save text-sm"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
{{-- نافذة تأكيد حذف الحساب --}}
<div id="deleteAccountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] {{ $errors->has('delete_password') ? 'flex' : 'hidden' }} items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-0 mx-auto shadow-2xl overflow-hidden transform transition-all">
        
        {{-- رأس النافذة --}}
        <div class="p-6 pb-4 bg-red-50 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">حذف الحساب نهائياً</h3>
                    <p class="text-xs text-red-600/70 mt-0.5">هذا الإجراء لا يمكن التراجع عنه</p>
                </div>
            </div>
        </div>

        <form action="{{ route('client.settings.deleteAccount') }}" method="POST" class="p-6" autocomplete="off">
            @csrf
            @method('DELETE')

            <div class="mb-5">
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 mb-5">
                    <i class="fas fa-info-circle text-amber-500 mt-0.5 text-sm"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        سيتم حذف حسابك وجميع بياناتك بشكل دائم، بما في ذلك الاستشارات والمحادثات والمستندات. لا يمكن استرجاع هذه البيانات بعد الحذف.
                    </p>
                </div>

                <label class="block text-sm font-semibold mb-2 text-slate-700">أدخل كلمة المرور لتأكيد الحذف</label>
                <div class="relative">
                    <i class="fas fa-lock s-input-icon" style="color: {{ $errors->has('delete_password') ? '#ef4444' : '#ef4444' }};"></i>
                    <input type="password" name="delete_password" 
                           autocomplete="off" 
                           class="s-input {{ $errors->has('delete_password') ? 'border-red-500 focus:ring-red-500' : '' }}" 
                           style="border-color: {{ $errors->has('delete_password') ? '#ef4444' : '#fecaca' }};" 
                           placeholder="كلمة المرور الحالية" required>
                </div>

                {{-- رسالة الخطأ مطابقة لتصميم المحامي --}}
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
        </form>
    </div>
</div>

<style>
    /* إخفاء أيقونة العين الافتراضية للمتصفح لمنع التكرار */
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
    const tabs = document.querySelectorAll(".settings-tab");
    const contents = document.querySelectorAll(".settings-content");
    const STORAGE_KEY = 'admin_settings_active_tab';

    function activateTab(tabName, save = false) {
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

        if (save) {
            localStorage.setItem(STORAGE_KEY, targetTabName);
        }
    }

    // المنطق الجديد:
    // 1. إذا كان فيه session (معناه في تحديث صار)، افتح التبويب اللي في الـ session.
    // 2. إذا مافيش session (معناه ريلود عادي)، تجاهل localStorage وافتح 'profile'.
    
    @if(session()->has('tab'))
        activateTab("{{ session('tab') }}", true);
    @elseif($errors->any())
        @if($errors->has('current_password') || $errors->has('password')) 
            activateTab('security', true);
        @else 
            activateTab('profile', true); 
        @endif
    @else
        // هنا التغيير: عند الريلود العادي، نفتح profile فقط، ونمسح التبويب القديم من الذاكرة
        localStorage.removeItem(STORAGE_KEY);
        activateTab('profile', false);
    @endif

    // عند النقر على التبويبات (هنا يشتغل التخزين العادي)
    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            activateTab(this.getAttribute("data-tab"), true);
        });
    });

    // 2. التحقق من كلمة المرور قبل الإرسال
    const passwordForm = document.querySelector('form[action*="password"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPass = document.getElementById('password');
            const confPass = document.getElementById('password_confirmation');
            const errDisplay = document.getElementById('password-error');

            if (newPass.value.length < 8 || newPass.value !== confPass.value) {
                e.preventDefault(); 
                errDisplay.textContent = (newPass.value.length < 8) ? "كلمة المرور يجب أن تكون 8 أحرف على الأقل." : "كلمة المرور غير متطابقة.";
                errDisplay.classList.remove('hidden');
            } else {
                errDisplay.classList.add('hidden');
            }
        });
    }

    // 3. إخفاء التنبيهات تلقائياً (كما طلبت)
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

function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection