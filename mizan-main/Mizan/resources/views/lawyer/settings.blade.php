@extends('layouts.lawyer')

@section('title', 'الإعدادات ')
@section('lawyer_settings_active', 'nav-active')

@section('content')
<div id="settings-board" class="max-w-[1400px] mx-auto">
    <style>
        /* 1. إخفاء الـ Checkbox الفعلي */
        input[type="checkbox"].sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0; }
        /* 2. تنسيق الـ Toggle */
        input[type="checkbox"]:checked + div { background-color: #0f6afd !important; }
        input[type="checkbox"]:checked + div::after { transform: translateX(100%) !important; }

        /* 3. تنسيق التبويبات الأفقية */
        .settings-tab { display: flex !important; align-items: center !important; cursor: pointer !important; position: relative !important; white-space: nowrap !important; }
        .settings-tab i { color: #2563eb !important; }
        .active-tab-full { color: #1d4ed8 !important; }
        .active-tab-full i, .active-tab-full span { color: #1d4ed8 !important; font-weight: 700 !important; }
        .active-tab-full::after { content: ''; position: absolute; left: 0.5rem; right: 0.5rem; bottom: -1px; height: 2px; border-radius: 9999px; background: #2563eb; }
        /* إخفاء سهم الـ chevron الذي يضيفه الـ JS تلقائياً (غير مناسب للتبويبات الأفقية) */
        .settings-tab .fa-chevron-left { display: none !important; }
        /* إخفاء شريط التمرير لشريط التبويبات مع إبقاء إمكانية السحب */
        #settings-tabs-container { scrollbar-width: none !important; -ms-overflow-style: none !important; }
        #settings-tabs-container::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
        /* شريط تمرير بسيط ونظيف لحقول النص الطويلة */
        .clean-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .clean-scroll::-webkit-scrollbar { width: 6px; }
        .clean-scroll::-webkit-scrollbar-track { background: transparent; }
        .clean-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .clean-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    {{-- ====================== اللوحة البيضاء ====================== --}}
    <div class="rounded-[26px] border border-slate-200/70 bg-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.25)] overflow-hidden">

        {{-- ====================== الغلاف ====================== --}}
        <div class="relative overflow-hidden bg-gradient-to-l from-blue-600 via-blue-700 to-blue-800 px-6 sm:px-8 py-7">
            {{-- أيقونات قانونية بيضاء شفافة --}}
            <i class="fas fa-scale-balanced absolute left-10 top-3 text-white/10 text-5xl -rotate-12"></i>
            <i class="fas fa-gavel absolute left-1/3 bottom-1 text-white/10 text-4xl rotate-12"></i>
            <i class="fas fa-landmark absolute left-28 bottom-2 text-white/10 text-3xl"></i>
            <div class="relative">
                <h1 class="flex items-center gap-2.5 text-2xl font-black text-white">
                    <i class="fas fa-cog text-xl" style="color:#fff !important;"></i> الإعدادات
                </h1>
                <p class="mt-1 text-sm font-medium text-white/80">تعديل ملفك المهني وإعدادات حسابك</p>
            </div>
        </div>

        {{-- ====================== جسم اللوحة ====================== --}}
        <div class="px-5 sm:px-8 pb-8">
            <div class="grid grid-cols-1 lg:grid-cols-[300px_1fr] gap-6 lg:gap-8">

                {{-- ====================== بطاقة الحساب ====================== --}}
                <div class="mt-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 text-center">

                        {{-- الصورة --}}
                        <div class="relative inline-block group mx-auto mb-3">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-800 flex items-center justify-center text-white text-3xl font-bold mx-auto border-4 border-white shadow-lg ring-1 ring-slate-100">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('uploads/avatars/' . auth()->user()->avatar) }}"
                                         alt="Profile Picture"
                                         class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr(auth()->user()->name, 0, 1, 'UTF-8') }}
                                @endif
                            </div>
                            <label for="avatar_upload" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center ring-2 ring-white hover:from-blue-600 hover:to-blue-800 transition-all duration-300 shadow-md shadow-blue-600/30 cursor-pointer hover:scale-110">
                                <i class="fas fa-camera text-xs" style="color: #ffffff !important;"></i>
                                <input type="file" id="avatar_upload" name="avatar" class="hidden" accept="image/*" form="profile-form" onchange="this.form.submit()">
                            </label>
                        </div>

                        {{-- الاسم والتخصص --}}
                        <h3 class="font-black text-slate-800 text-lg leading-tight">{{ auth()->user()->name }}</h3>
                        <div class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-600">
                            <i class="fas fa-gavel text-[10px]"></i>
                            <span class="truncate max-w-[150px]">{{ auth()->user()->lawyerProfile->specialty->name ?? 'لم يحدد تخصص' }}</span>
                        </div>

                        {{-- الإحصائيات --}}
                        @php $current_rating = auth()->user()->lawyerProfile->rating ?? 0; @endphp
                        <div class="mt-5 space-y-1 border-t border-slate-200/70 pt-4 text-right">
                            <div class="flex items-center justify-between px-1 py-2">
                                <span class="flex items-center gap-1.5 text-[13px] text-slate-500">
                                    <i class="fas fa-star text-amber-400 text-xs"></i> التقييم
                                </span>
                                <span class="rounded-md bg-amber-50 px-2 py-0.5 text-xs font-bold text-amber-600">{{ number_format($current_rating, 1) }}</span>
                            </div>
                            <div class="flex items-center justify-between px-1 py-2">
                                <span class="flex items-center gap-1.5 text-[13px] text-slate-500">
                                    <i class="fas fa-award text-blue-500 text-xs"></i> سنوات الخبرة
                                </span>
                                <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-600">{{ auth()->user()->lawyerProfile->years_of_experience ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between px-1 py-2">
                                <span class="flex items-center gap-1.5 text-[13px] text-slate-500">
                                    <i class="fas fa-id-card text-emerald-500 text-xs"></i> رقم الرخصة
                                </span>
                                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-600 truncate max-w-[110px]">{{ auth()->user()->lawyerProfile->license_number ?? '—' }}</span>
                            </div>
                        </div>

                        {{-- زر حذف الحساب --}}
                        <button type="button"
                                onclick="document.getElementById('deleteAccountModal').classList.remove('hidden'); document.getElementById('deleteAccountModal').classList.add('flex');"
                                class="mt-4 w-full flex items-center justify-center gap-2 rounded-xl border border-red-100 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-all group">
                            <i class="fas fa-trash-alt text-xs text-red-400 group-hover:text-red-500"></i>
                            <span>حذف الحساب</span>
                        </button>
                    </div>
                </div>

                {{-- ====================== محتوى الإعدادات ====================== --}}
                <div class="pt-6">

                    {{-- رسائل النجاح والخطأ --}}
                    @if(session('success'))
                        <div class="alert-message mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-700 text-sm font-semibold">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                            </div>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-message mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3 text-red-700 text-sm font-semibold">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- شريط التبويبات الأفقي --}}
                    <div class="flex items-center gap-1 overflow-x-auto border-b border-slate-200 scrollbar-hide" id="settings-tabs-container">
                        <button class="settings-tab gap-2 px-4 py-3 text-sm font-bold text-slate-600" data-tab="profile">
                            <i class="fas fa-user-tie w-5 text-center"></i>
                            <span>الملف الشخصي</span>
                        </button>
                        <button class="settings-tab gap-2 px-4 py-3 text-sm font-bold text-slate-600" data-tab="security">
                            <i class="fas fa-lock w-5 text-center"></i>
                            <span>الأمان</span>
                        </button>
                        <button class="settings-tab gap-2 px-4 py-3 text-sm font-bold text-slate-600" data-tab="professional">
                            <i class="fas fa-briefcase w-5 text-center"></i>
                            <span>المعلومات المهنية</span>
                        </button>
                        <button class="settings-tab gap-2 px-4 py-3 text-sm font-bold text-slate-600" data-tab="financial">
                            <i class="fas fa-wallet w-5 text-center"></i>
                            <span>الإعدادات المالية</span>
                        </button>
                        <button data-tab="working-hours" class="settings-tab gap-2 px-4 py-3 text-sm font-bold text-slate-600">
                            <i class="fas fa-clock w-5 text-center"></i>
                            <span>أوقات العمل</span>
                        </button>
                        <button class="settings-tab gap-2 px-4 py-3 text-sm font-bold text-slate-600" data-tab="notifications">
                            <i class="fas fa-bell w-5 text-center"></i>
                            <span>الإشعارات</span>
                        </button>
                    </div>

                    {{-- ====================== الألواح ====================== --}}
                    <div class="pt-6">

                        {{-- ====================== تبويب الملف الشخصي ====================== --}}
                        <div id="tab-profile" class="settings-content hidden">
                            <form id="profile-form" action="{{ route('lawyer.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">الاسم الكامل</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-user text-sm"></i></div>
                                            <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">البريد الإلكتروني</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400"><i class="fas fa-envelope text-sm"></i></div>
                                            <input type="email" value="{{ auth()->user()->email }}" class="w-full pr-10 pl-4 py-2.5 border border-slate-100 bg-slate-50 text-slate-400 rounded-xl text-sm outline-none cursor-not-allowed" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">رقم الهاتف</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-phone text-sm"></i></div>
                                            <input type="tel" name="phone" value="{{ auth()->user()->phone }}" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">رقم رخصة المحاماة</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-id-card text-sm"></i></div>
                                            <input type="text" name="license_number" value="{{ auth()->user()->lawyerProfile->license_number ?? '' }}" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">عنوان المكتب</label>
                                    <div class="relative rounded-xl">
                                        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-blue-500"><i class="fas fa-map-marker-alt text-sm"></i></div>
                                        <textarea name="address" rows="2" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';" class="auto-grow w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 resize-none overflow-y-auto min-h-[80px] max-h-40 clean-scroll bg-white">{{ auth()->user()->lawyerProfile->address ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end border-t border-slate-100 pt-4">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all duration-300 hover:from-blue-600 hover:to-blue-800 hover:-translate-y-0.5 active:translate-y-0">
                                        <i class="fas fa-save text-sm" style="color: white !important;"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ====================== تبويب الأمان وكلمة السر ====================== --}}
                        <div id="tab-security" class="settings-content hidden">
                            <form id="password-form" action="{{ route('lawyer.settings.updatePassword') }}" method="POST">
                                @csrf @method('PUT')
                                {{-- صندوق النصيحة الأمنية التفاعلي --}}
                                {{-- نصيحة أمنية بتصميم تفاعلي --}}
                                <div class="mb-6 p-4 bg-blue-50/60 border border-blue-100 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4 group">
                                    <div class="relative w-10 h-10 flex items-center justify-center shrink-0 mt-0.5">
                                        <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-75"></div>
                                        <div class="relative w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-600/25">
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
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-key text-sm"></i></div>
                                            <input type="password" id="current_password" name="current_password" class="w-full pr-10 pl-10 py-2.5 border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-200' }} rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white" required>
                                            <button type="button" onclick="togglePassword('current_password', this)" class="absolute left-0 top-0 h-full px-3 flex items-center text-slate-400 hover:text-blue-500 focus:outline-none">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>
                                        </div>
                                        @error('current_password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        {{-- كلمة المرور الجديدة --}}
                                        <div>
                                            <label class="block text-sm font-semibold mb-2 text-slate-700">كلمة المرور الجديدة</label>
                                            <div class="relative rounded-xl">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-lock text-sm"></i></div>
                                                <input type="password" id="new_password" name="new_password" class="w-full pr-10 pl-10 py-2.5 border {{ $errors->has('new_password') ? 'border-red-500' : 'border-slate-200' }} rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white" required>
                                                <button type="button" onclick="togglePassword('new_password', this)" class="absolute left-0 top-0 h-full px-3 flex items-center text-slate-400 hover:text-blue-500 focus:outline-none">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            </div>
                                            <p id="password-error" class="text-red-500 text-xs mt-1 font-medium hidden"></p>
                                            @error('new_password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- تأكيد كلمة المرور --}}
                                        <div>
                                            <label class="block text-sm font-semibold mb-2 text-slate-700">تأكيد كلمة المرور الجديدة</label>
                                            <div class="relative rounded-xl">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-check-circle text-sm"></i></div>
                                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full pr-10 pl-10 py-2.5 border {{ $errors->has('new_password_confirmation') ? 'border-red-500' : 'border-slate-200' }} rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white" required>
                                                <button type="button" onclick="togglePassword('new_password_confirmation', this)" class="absolute left-0 top-0 h-full px-3 flex items-center text-slate-400 hover:text-blue-500 focus:outline-none">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end border-t border-slate-100 pt-4 mt-6">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all duration-300 hover:from-blue-600 hover:to-blue-800 hover:-translate-y-0.5 active:translate-y-0">
                                        <i class="fas fa-key text-sm" style="color: white !important;"></i> تحديث كلمة المرور
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ====================== تبويب المعلومات المهنية ====================== --}}
                        <div id="tab-professional" class="settings-content hidden">
                            <form action="{{ route('lawyer.settings.updateProfessionalInfo') }}" method="POST" class="space-y-6">
                                @csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                                <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">
                                <input type="hidden" name="license_number" value="{{ auth()->user()->lawyerProfile->license_number ?? '12345' }}">
                                <input type="hidden" name="address" value="{{ auth()->user()->lawyerProfile->address ?? 'العنوان الافتراضي' }}">

                                {{-- الحقلين بجانب بعضهما باستخدام Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="block w-full">
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">التخصص الرئيسي</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500">
                                                <i class="fas fa-gavel text-sm"></i>
                                            </div>
                                            <select name="specialty_id" class="w-full pr-10 pl-10 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white appearance-none cursor-pointer">
                                                <option value="">اختر التخصص...</option>
                                                @foreach(\App\Models\Specialty::all() as $specialty)
                                                    <option value="{{ $specialty->id }}" {{ (auth()->user()->lawyerProfile->specialty_id ?? null) == $specialty->id ? 'selected' : '' }}>
                                                        {{ $specialty->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="block w-full">
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">سنوات الخبرة</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500">
                                                <i class="fas fa-award text-sm"></i>
                                            </div>
                                            <input type="number" name="years_of_experience" value="{{ auth()->user()->lawyerProfile->years_of_experience ?? '0' }}" min="0" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white">
                                        </div>
                                    </div>
                                </div>

                                <div class="block w-full">
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">السيرة الذاتية والنبذة التعريفية</label>
                                    <div class="relative rounded-xl">
                                        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-blue-500"><i class="fas fa-align-right text-sm"></i></div>
                                        <textarea name="bio" rows="4" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';" class="auto-grow w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 resize-none overflow-y-auto min-h-[150px] max-h-80 clean-scroll bg-white">{{ auth()->user()->lawyerProfile->bio ?? '' }}</textarea>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1.5 mr-1">هذه النبذة ستظهر للعملاء في صفحة البحث والمقارنة العامة.</p>
                                </div>

                                <div class="block w-full">
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">المؤهلات الأكاديمية العلمية</label>
                                    <div class="relative rounded-xl">
                                        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-blue-500"><i class="fas fa-graduation-cap text-sm"></i></div>
                                        <textarea name="qualifications" rows="4" placeholder="اكتب كل مؤهل في سطر جديد.." oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';" class="auto-grow w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 resize-none overflow-y-auto min-h-[150px] max-h-80 clean-scroll bg-white">{{ auth()->user()->lawyerProfile->qualifications ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="block w-full">
                                    <label class="block text-sm font-semibold mb-2 text-slate-700">الخبرات المهنية والميدانية</label>
                                    <div class="relative rounded-xl">
                                        <div class="absolute top-0 right-0 flex items-start pt-3 pr-4 pointer-events-none text-blue-500"><i class="fas fa-briefcase text-sm"></i></div>
                                        <textarea name="experience" rows="4" placeholder="اكتب كل خبرة في سطر جديد.." oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';" class="auto-grow w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 resize-none overflow-y-auto min-h-[150px] max-h-80 clean-scroll bg-white">{{ auth()->user()->lawyerProfile->experience ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="flex justify-end border-t border-slate-100 pt-4 w-full">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all duration-300 hover:from-blue-600 hover:to-blue-800 hover:-translate-y-0.5">
                                        <i class="fas fa-save text-sm" style="color: white !important;"></i> <span>حفظ التغييرات</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ====================== تبويب الإعدادات المالية ====================== --}}
                        <div id="tab-financial" class="settings-content hidden">
                            <form action="{{ route('lawyer.settings.updateFinancial') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">اسم صاحب الحساب البنكي</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-user-circle text-sm"></i></div>
                                            <input type="text" name="account_name" value="{{ auth()->user()->lawyerProfile->account_name ?? '' }}" placeholder="الاسم الكامل" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">رقم الحساب البنكي</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-hashtag text-sm"></i></div>
                                            <input type="text" name="account_number" value="{{ auth()->user()->lawyerProfile->account_number ?? '' }}" placeholder="مثال: 123456789" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">اسم البنك</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-university text-sm"></i></div>
                                            <input type="text" name="bank_name" value="{{ auth()->user()->lawyerProfile->bank_name ?? '' }}" placeholder="مثال: مصرف الجمهورية" class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold mb-2 text-slate-700">رقم الحساب الدولي (IBAN)</label>
                                        <div class="relative rounded-xl">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-blue-500"><i class="fas fa-globe text-sm"></i></div>
                                            <input type="text" name="iban" value="{{ auth()->user()->lawyerProfile->iban ?? '' }}" placeholder="مثال: LY00000000..." class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm text-slate-800 bg-white">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end border-t border-slate-100 pt-4">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all hover:from-blue-600 hover:to-blue-800 hover:-translate-y-0.5">
                                        <i class="fas fa-save text-sm" style="color: white !important;"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ====================== تبويب اوقات العمل ====================== --}}
                        <div id="tab-working-hours" class="settings-content hidden">
                            <form id="working-hours-form" action="{{ route('lawyer.settings.updateWorkingHours') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @php
                                        $daysOrder = [6 => 'السبت', 0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة'];
                                        $workingHours = auth()->user()->lawyerWorkingHours ?? collect();
                                    @endphp
                                    @foreach($daysOrder as $index => $day)
                                        @php
                                            $dayData = $workingHours->firstWhere('day_of_week', $index);
                                            $startTime = ($dayData && $dayData->start_time) ? \Carbon\Carbon::parse($dayData->start_time)->format('H:i') : '';
                                            $endTime = ($dayData && $dayData->end_time) ? \Carbon\Carbon::parse($dayData->end_time)->format('H:i') : '';
                                        @endphp
                                        @if($index == 5)
                                            <div class="md:col-span-2 p-4 border border-slate-100 rounded-2xl bg-slate-50 shadow-sm flex items-center justify-between transition-all">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-200 text-slate-400"><i class="fas fa-moon"></i></div>
                                                    <div><div class="font-bold text-sm text-slate-600">{{ $day }}</div></div>
                                                </div>
                                                <span class="text-xs font-bold text-slate-600 bg-white px-4 py-2 rounded-full border border-slate-200">عطلة رسمية</span>
                                            </div>
                                        @else
                                            <div class="day-row p-4 border border-slate-100 rounded-2xl bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)] hover:border-blue-200 hover:shadow-md transition-all">
                                                <div class="font-bold text-sm text-slate-700 mb-3">{{ $day }}</div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <label class="flex items-center gap-2 text-xs cursor-pointer ml-2">
                                                        <input type="checkbox" name="days[{{ $index }}][active]" value="1" {{ ($dayData && $dayData->is_active) ? 'checked' : '' }} class="active-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                        <span class="text-slate-600">متاح</span>
                                                    </label>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-[10px] text-slate-400 font-bold">من</span>
                                                        <input type="time" name="days[{{ $index }}][start]" value="{{ $startTime }}" class="start-time text-xs border border-slate-200 rounded-lg px-2 py-1.5 outline-none focus:border-blue-500 w-[90px]">
                                                        <span class="text-[10px] text-slate-400 font-bold">إلى</span>
                                                        <input type="time" name="days[{{ $index }}][end]" value="{{ $endTime }}" class="end-time text-xs border border-slate-200 rounded-lg px-2 py-1.5 outline-none focus:border-blue-500 w-[90px]">
                                                    </div>
                                                </div>
                                                <div class="error-msg text-red-500 text-[10px] mt-2 hidden font-bold"></div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="mt-6 flex justify-end border-t border-slate-100 pt-4">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all duration-300 hover:from-blue-600 hover:to-blue-800 hover:-translate-y-0.5 active:translate-y-0">
                                        <i class="fas fa-save text-sm" style="color: white !important;"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ====================== تبويب الإشعارات  ====================== --}}
                        <div id="tab-notifications" class="settings-content hidden">
                            <form action="{{ route('lawyer.settings.updateNotifications') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-4">
                                    @php
                                        $notifications = [
                                            ['name' => 'app_notifications', 'label' => 'إشعارات التطبيق', 'desc' => 'التحكم في فتح او إغلاق كل الإشعارات'],
                                            ['name' => 'new_consultation', 'label' => 'طلبات الاستشارات', 'desc' => 'إشعار عند حجز عميل لإستشارات جديدة'],
                                            ['name' => 'new_bookings', 'label' => 'طلبات الحجوزات', 'desc' => 'إشعار عند طلب حجز عميل موعد جديد'],
                                            ['name' => 'new_messages', 'label' => 'رسائل المحادثات', 'desc' => 'إشعار عند استلام رسالة نصية جديدة من عميل']
                                        ];
                                    @endphp
                                    @foreach($notifications as $notif)
                                        <div class="flex items-center justify-between p-4 border border-slate-100 rounded-2xl bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)] hover:border-blue-200 hover:shadow-md transition-all">
                                            <div>
                                                <div class="font-bold text-sm text-slate-700">{{ $notif['label'] }}</div>
                                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $notif['desc'] }}</div>
                                            </div>
                                            <label for="{{ $notif['name'] }}" class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" id="{{ $notif['name'] }}" name="{{ $notif['name'] }}" value="1" class="sr-only peer" {{ (auth()->user()->notification_settings[$notif['name']] ?? true) ? 'checked' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:outline-none peer-checked:after:translate-x-full peer-checked:bg-[#0f6afd] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-8 flex justify-end border-t border-slate-100 pt-4">
                                    <button type="submit" class="px-8 py-3 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-600/25 flex items-center gap-2 transition-all duration-300 hover:from-blue-600 hover:to-blue-800 hover:-translate-y-0.5 active:translate-y-0">
                                        <i class="fas fa-save text-sm" style="color: white !important;"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- نافذة تأكيد حذف الحساب --}}
    <div id="deleteAccountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] {{ $errors->has('delete_password') ? 'flex' : 'hidden' }} items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-0 mx-auto shadow-2xl overflow-hidden transform transition-all">

            {{-- رأس النافذة الجديد --}}
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

            <form action="{{ route('lawyer.settings.deleteAccount') }}" method="POST" class="p-6">
                @csrf
                @method('DELETE')

                <div class="mb-5">
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 mb-5">
                        <i class="fas fa-info-circle text-amber-500 mt-0.5 text-sm"></i>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            سيتم حذف حسابك وجميع بياناتك بشكل دائم، بما في ذلك الاستشارات والمحادثات والمستندات. لا يمكن استرجاع هذه البيانات بعد الحذف.
                        </p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold mb-2 text-slate-700">أدخل كلمة المرور لتأكيد الحذف</label>
                        <input type="password" name="delete_password"
                               class="w-full p-3 border {{ $errors->has('delete_password') ? 'border-red-500 ring-1 ring-red-500' : 'border-slate-200' }} rounded-xl outline-none focus:ring-2 focus:ring-red-500/20"
                               placeholder="كلمة المرور الحالية" required>

                        {{-- رسالة الخطأ بنفس تصميم الكلاينت --}}
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
                            <i class="fas fa-trash-alt text-xs" style="color: white !important;"></i> تأكيد الحذف
                        </button>
                    </div>
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
        /* إخفاء أيقونة إظهار كلمة المرور في متصفحات كروم/إيدج الجديدة */
        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none !important;
        }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll(".settings-tab");
        const contents = document.querySelectorAll(".settings-content");
        const STORAGE_KEY = 'lawyer_settings_active_tab';

        // مسح الـ localStorage عند الريفرش العادي (إذا لم تكن هناك جلسة سيرفر)
        @if(!session()->has('tab'))
            localStorage.removeItem(STORAGE_KEY);
        @endif

        function activateTab(tabName, save = false) {
            let targetTabName = tabName || localStorage.getItem(STORAGE_KEY) || 'profile';

            // التحقق من وجود التبويب لتجنب الخطأ
            if (!document.getElementById(`tab-${targetTabName}`)) {
                targetTabName = 'profile';
            }

            tabs.forEach(btn => {
                const isActive = btn.getAttribute("data-tab") === targetTabName;

                btn.classList.remove("active-tab-full", "text-slate-600", "hover:bg-slate-50");

                if(isActive) {
                    btn.classList.add("active-tab-full");
                    if(!btn.querySelector('.fa-chevron-left')) {
                        const chevron = document.createElement('i');
                        chevron.className = 'fas fa-chevron-left absolute left-4 top-1/2 -translate-y-1/2 text-[10px]';
                        btn.style.position = 'relative';
                        btn.appendChild(chevron);
                    }
                } else {
                    btn.classList.add("text-slate-600", "hover:bg-slate-50");
                    const existingChevron = btn.querySelector('.fa-chevron-left');
                    if (existingChevron) existingChevron.remove();
                }
            });

            contents.forEach(content => {
                content.classList.toggle("hidden", content.id !== `tab-${targetTabName}`);
            });

            if (save) {
                localStorage.setItem(STORAGE_KEY, targetTabName);
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                const tabName = this.getAttribute("data-tab");
                activateTab(tabName, true);
            });
        });

        // المنطق عند تحميل الصفحة (Session -> Errors -> LocalStorage -> Default)
        @if(session()->has('tab'))
            activateTab("{{ session('tab') }}", true);
        @elseif($errors->any())
            @if($errors->has('account_number') || $errors->has('bank_name') || $errors->has('iban'))
                activateTab('financial', true);
            @elseif($errors->has('license_number') || $errors->has('specialty_id') || $errors->has('bio') || $errors->has('address'))
                activateTab('professional', true);
            @elseif($errors->has('current_password') || $errors->has('new_password'))
                activateTab('security', true);
            @else
                activateTab('profile', true);
            @endif
        @else
            activateTab(null, false);
        @endif

        // التحقق من كلمة المرور
        const form = document.getElementById('password-form');
        const newPass = document.getElementById('new_password');
        const confPass = document.getElementById('new_password_confirmation');
        const errDisplay = document.getElementById('password-error');

        if (form) {
            form.addEventListener('submit', function(e) {
                if (newPass.value.length < 8 || newPass.value !== confPass.value) {
                    e.preventDefault();
                    errDisplay.textContent = (newPass.value.length < 8) ? "كلمة المرور يجب أن تكون 8 أحرف على الأقل." : "كلمة المرور غير متطابقة.";
                    errDisplay.classList.remove('hidden');
                } else {
                    errDisplay.classList.add('hidden');
                }
            });
        }

        // منطق التحقق من أوقات العمل
        const workingForm = document.getElementById('working-hours-form');
        if (workingForm) {
            workingForm.addEventListener('submit', function(e) {
                let isValid = true;
                const days = document.querySelectorAll('.day-row');

                days.forEach(day => {
                    const cb = day.querySelector('.active-cb');
                    const start = day.querySelector('.start-time');
                    const end = day.querySelector('.end-time');
                    const err = day.querySelector('.error-msg');

                    err.classList.add('hidden');

                    if (cb.checked && (!start.value || !end.value)) {
                        isValid = false;
                        err.textContent = "يجب عليك تحديد وقت العمل (من - إلى).";
                        err.classList.remove('hidden');
                    } else if (!cb.checked && (start.value || end.value)) {
                        isValid = false;
                        err.textContent = "يجب تفعيل خيار 'متاح' لحفظ التوقيت.";
                        err.classList.remove('hidden');
                    }
                });

                if (!isValid) e.preventDefault();
            });
        }

        // ضبط ارتفاع حقول النص حسب المحتوى عند تحميل الصفحة (مع سقف أقصى يفعّل التمرير)
        document.querySelectorAll('textarea.auto-grow').forEach(function(ta) {
            ta.style.height = 'auto';
            ta.style.height = ta.scrollHeight + 'px';
        });

        // إخفاء التنبيهات
        const alerts = document.querySelectorAll('.alert-message');
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => { alert.remove(); }, 500);
            });
        }, 5000);
    });

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
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

</div>

@push('styles')
<style>
    /* الخط: كايرو (نفس تيم بقية الصفحات) — مع استثناء أيقونات FontAwesome حتى لا تختفي */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
    #settings-board, #settings-board *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]),
    #deleteAccountModal, #deleteAccountModal *:not(i):not(.fa):not(.fas):not(.far):not(.fab):not([class*="fa-"]) {
        font-family: 'Cairo', 'Tajawal', sans-serif !important;
    }
</style>
@endpush

@endsection
