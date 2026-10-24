@extends('layouts.app')

@section('title', 'الملف الشخصي للمحامي')
@section('page_title', 'الملف الشخصي')
@section('page_subtitle', $lawyer->name . ' - ' . ($lawyer->lawyerProfile->specialty ?? 'قانون تجاري'))
@section('client_lawyers_active', 'nav-active')

@section('content')
<style>
    /* ==========================================================================
       تصميم احترافي عصري أنيق (Premium Minimalist)
       الخط الأساسي: Cairo  |  اللون الأساسي: #0061ff
       ========================================================================== */

    .profile-container {
        font-family: 'Cairo', sans-serif;
    }
    @media (min-width: 1024px) {
        .profile-container {
            grid-template-columns: 480px minmax(0, 1fr) !important;
        }
    }

    /* البطاقة الرئيسية */
    .lawyer-profile-card {
        background: #ffffff;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e9edf2;
    }

    /* البانر الأزرق الأنيق */
    .profile-banner {
        position: relative;
        overflow: hidden;
    }
    .banner-ico {
        position: absolute;
        color: rgba(255, 255, 255, 0.16);
        animation: banner-floaty 6s ease-in-out infinite;
    }
    @keyframes banner-floaty {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-9px) rotate(6deg); }
    }
    @media (prefers-reduced-motion: reduce) {
        .banner-ico { animation: none; }
    }

    .profile-avatar-wrapper {
        width: 128px;
        height: 128px;
        border-radius: 50%;
        padding: 0;
        background: #ffffff;
        border: 4px solid #ffffff;
        margin: 24px auto 16px auto;
        box-shadow: 0 2px 10px -4px rgba(2, 32, 71, 0.18);
    }
    .profile-avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #eef2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .profile-avatar-inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-avatar-inner i {
        font-size: 52px;
        color: #94a3b8;
    }

    .profile-name-text {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 23px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
    }
    .verified-badge {
        color: #0061ff;
        font-size: 17px;
    }

    .specialization-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        background: #f1f5fb;
        color: #0052da;
        font-size: 13px;
        font-weight: 700;
        padding: 7px 18px;
        border-radius: 40px;
        margin-top: 12px;
        border: 1px solid #e3e9f2;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
    }

    /* الأزرار */
    .btn-profile-action {
        background: #0061ff;
        color: #ffffff;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 700;
        border-radius: 12px;
        text-decoration: none;
        transition: background 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }
    .btn-profile-action:hover {
        background: #0052da;
    }

    .btn-profile-secondary {
        background: #ffffff;
        color: #475569;
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #e9edf2;
        cursor: pointer;
    }
    .btn-profile-secondary:hover {
        background: #f6f7f9;
        color: #0061ff;
        border-color: #d6e0ee;
    }

    /* بطاقات الإحصائيات */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 12px;
        margin: 24px 0;
    }
    @media (min-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    .stat-card {
        border-radius: 10px;
        padding: 9px 8px;
        background: #f6f7f9;
        border: 1px solid #e9edf2;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
    }
    .stat-text { text-align: right; }

    .stat-card-exp .stat-icon { color: #0061ff; }
    .stat-card-rating .stat-icon { color: #eab308; }
    .stat-card-time .stat-icon { color: #059669; }

    .stat-value {
        font-size: 16px;
        color: #0f172a;
        font-weight: 800;
        line-height: 1;
    }
    .stat-label {
        font-size: 9.5px;
        color: #64748b;
        font-weight: 700;
        margin-top: 3px;
    }
    .stat-icon {
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #fff;
        border: 1px solid #e9edf2;
    }

    /* معلومات الاتصال */
    .contact-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
        margin: 26px 0 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #0061ff;
    }
    .contact-title i { color: #0061ff; }
    .contact-grid { margin: 0 0 4px; display: grid; grid-template-columns: 1fr; gap: 12px; }
    .contact-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 15px 16px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e9edf2;
        transition: border-color .2s ease;
    }
    .contact-row:hover {
        border-color: #d6e0ee;
    }
    .contact-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0061ff;
        font-size: 16px;
        flex-shrink: 0;
        background: #f4f7fb;
        border: 1px solid #e3e9f2;
    }
    .contact-info { flex: 1; min-width: 0; }
    .contact-label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .contact-value {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        unicode-bidi: plaintext;
        word-break: break-word;
    }


    /* حاويات المعلومات الجانبية */
    .info-card {
        background: #ffffff;
        border-radius: 22px;
        box-shadow: 0 14px 40px -26px rgba(2, 32, 71, 0.22), 0 2px 10px -6px rgba(2, 32, 71, 0.05);
        border: 1px solid #eef2f8;
        overflow: hidden;
    }
    .info-card-header {
        padding: 24px 24px 0 24px;
    }
    .info-card-body {
        padding: 20px 24px 24px 24px;
    }

    /* قائمة الخبرات والمؤهلات */
    .list-item-clean {
        display: flex;
        gap: 14px;
        padding: 15px 16px;
        background: #f8fafc;
        border-radius: 14px;
        margin-bottom: 12px;
        border: 1px solid #eef2f8;
        transition: all 0.2s ease;
    }
    .list-item-clean:hover { border-color: #d9e8ff; transform: translateX(-2px); }

    /* نماذج الإدخال */
    .form-input {
        width: 100%;
        padding: 13px 16px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #334155;
        outline: none;
    }
    .form-input:focus {
        border-color: #0061ff;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(0, 97, 255, 0.10);
    }

    /* إقرار الموافقة */
    .agreement-box {
        background: #fff8e6;
        border: 1.5px solid rgba(245, 158, 11, 0.6);
        border-radius: 16px;
        padding: 18px;
        transition: all 0.2s ease;
    }
    .agreement-box:hover {
        border-color: #f59e0b;
        background: #fff3d6;
    }
    .agreement-label-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        cursor: pointer;
    }
    .agreement-checkbox {
        appearance: none;
        width: 22px;
        height: 22px;
        border: 2px solid #f59e0b;
        border-radius: 6px;
        background-color: #fff;
        margin-top: 2px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .agreement-checkbox:checked {
        background-color: #f59e0b;
        border-color: #f59e0b;
    }
    .agreement-checkbox:checked::after {
        content: '✔';
        position: absolute;
        color: white;
        font-size: 14px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .agreement-text {
        color: #92400e;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.7;
    }

    /* ==========================================================================
       تحسينات الهاتف المحمول فقط
       ========================================================================== */
    @media (max-width: 768px) {
        .profile-container { gap: 16px; }

        .lawyer-profile-card > div { padding: 0 16px 20px 16px !important; }
        .profile-avatar-wrapper { width: 108px; height: 108px; margin-top: 20px; }
        .profile-avatar-inner i { font-size: 42px; }
        .profile-name-text { font-size: 19px; }
        .specialization-tag { font-size: 11px; padding: 5px 14px; margin-top: 8px; }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 8px;
            margin: 20px 0;
        }
        .stat-card { padding: 12px 4px; border-radius: 14px; }
        .stat-icon { font-size: 17px; }
        .stat-value { font-size: 16px; margin-top: 2px; }
        .stat-label { font-size: 9.5px; line-height: 1.2; margin-top: 4px; }

        .btn-profile-action { font-size: 13px; padding: 11px 16px; }
        .btn-profile-secondary { font-size: 12px; padding: 11px 8px; gap: 4px; }

        .contact-grid { margin: 16px 0; }
        .contact-row { padding: 11px 0; gap: 12px; }
        .contact-icon { width: 36px; height: 36px; font-size: 14px; border-radius: 11px; }
        .contact-value { font-size: 13px; }

        .info-card-header { padding: 16px 16px 0 16px; }
        .info-card-body { padding: 16px; }

        .list-item-clean { padding: 12px; flex-direction: column; gap: 8px; }
        .form-input { padding: 12px 14px; font-size: 13px; }
    }
</style>
<style>
    /* بطاقات مسطّحة بسيطة */
    .stat-card-interactive { cursor: default; }
    .exp-card { background: #f6f7f9; border: 1px solid #e9edf2; }
    .rate-card { background: #f6f7f9; border: 1px solid #e9edf2; }
</style>

<div class="profile-container grid grid-cols-1 gap-8 animate-fade-in pb-12">

{{-- ====================== العمود الأيمن (البطاقة الشخصية) ====================== --}}
<div class="lg:col-span-1">
    {{-- الكرت الرئيسي --}}
    <div class="lawyer-profile-card">

        {{-- 1. البانر الأزرق --}}
        <div class="profile-banner" style="height: 130px; background: linear-gradient(120deg, #00337a, #0061ff);">
            <i class="fas fa-balance-scale banner-ico" style="top: 16px; right: 34px; font-size: 32px; animation-delay: 0s;"></i>
            <i class="fas fa-gavel banner-ico" style="top: 58px; left: 48px; font-size: 34px; animation-delay: 1.2s;"></i>
            <i class="fas fa-landmark banner-ico" style="top: 20px; left: 42%; font-size: 24px; animation-delay: 0.6s;"></i>
            <i class="fas fa-book banner-ico" style="bottom: 12px; right: 36%; font-size: 20px; animation-delay: 2s;"></i>
            <i class="fas fa-scroll banner-ico" style="bottom: 14px; left: 130px; font-size: 22px; animation-delay: 2.6s;"></i>
            <i class="fas fa-shield-alt banner-ico" style="top: 30px; right: 130px; font-size: 18px; animation-delay: 0.9s;"></i>
            <i class="fas fa-file-contract banner-ico" style="top: 86px; right: 92px; font-size: 18px; animation-delay: 1.8s;"></i>
            <i class="fas fa-handshake banner-ico" style="top: 22px; left: 132px; font-size: 20px; animation-delay: 3s;"></i>
            <i class="fas fa-stamp banner-ico" style="bottom: 44px; left: 70px; font-size: 18px; animation-delay: 2.2s;"></i>
            <i class="fas fa-feather banner-ico" style="top: 92px; left: 30%; font-size: 16px; animation-delay: 1.5s;"></i>
            <i class="fas fa-key banner-ico" style="top: 12px; right: 24%; font-size: 16px; animation-delay: 3.4s;"></i>
        </div>

        {{-- 2. حاوية المحتوى --}}
        <div style="padding: 0 28px 30px 28px;">

            {{-- حاوية الصورة --}}
            <div style="position: relative; z-index: 2; margin-top: -84px; margin-bottom: 18px; display: flex; justify-content: center;">
                <div class="profile-avatar-wrapper" style="margin: 0;">
                    <div class="profile-avatar-inner">
                        @if($lawyer->avatar || ($lawyer->lawyerProfile && $lawyer->lawyerProfile->avatar))
                            <img src="{{ asset('uploads/avatars/' . ($lawyer->avatar ?? $lawyer->lawyerProfile->avatar)) }}"
                                 alt="{{ $lawyer->name }}">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-bottom: 16px;">
                <h2 class="profile-name-text">
                    {{ $lawyer->name }}
                    <i class="fas fa-circle-check verified-badge"></i>
                </h2>

                <div class="specialization-tag">
                    <i class="fas fa-gavel"></i>
                    {{ $lawyer->lawyerProfile->specialty->name ?? 'محامي عام' }}
                </div>
            </div>

            {{-- الإحصائيات (تفاعلية) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 9px; margin: 16px 0;">
                <div class="stat-card stat-card-exp stat-card-interactive exp-card">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-text">
                        <div class="stat-value">
                            {{ $lawyer->lawyerProfile->experience_years ?? 8 }}
                        </div>
                        <div class="stat-label">سنوات الخبرة</div>
                    </div>
                </div>

                <div class="stat-card stat-card-rating stat-card-interactive rate-card">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                    <div class="stat-text">
                        <div class="stat-value">
                            {{ number_format($lawyer->lawyerProfile->rating ?? 3.4, 1) }}
                        </div>
                        <div class="stat-label">التقييم</div>
                    </div>
                </div>
            </div>

             {{-- قسم السيرة الذاتية والخبرات --}}
            <div style="margin: 24px 0 16px;">
                <button onclick="showProfileTab('info-section')" id="btn-info-section" class="btn-profile-action" style="width: 100%; justify-content: center; margin-bottom: 12px;">
                    <i class="fas fa-user-tie"></i> السيرة الذاتية والخبرات
                </button>
                {{-- قسم حجز موعد  --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <button onclick="showProfileTab('booking-section')" id="btn-booking-section" class="btn-profile-secondary" style="width: 100%;">
                        <i class="fas fa-calendar-day"></i> حجز موعد
                    </button>
                    {{-- قسم الاستشارة  --}}
                    <button onclick="showProfileTab('consultation-section')" id="btn-consultation-section" class="btn-profile-secondary" style="width: 100%;">
                        <i class="fas fa-comment-dots"></i> استشارة
                    </button>
                </div>
            </div>

          {{-- قسم معلومات التواصل --}}
            <div class="contact-title"><i class="fas fa-address-book"></i> معلومات التواصل</div>
            <div class="contact-grid">
                <div class="contact-row">
                    <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="contact-info">
                        <div class="contact-label">هاتف</div>
                        <div class="contact-value">{{ $lawyer->phone ?? '091 234 5678' }}</div>
                    </div>
                </div>
                <div class="contact-row">
                    <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
                    <div class="contact-info">
                        <div class="contact-label">واتساب</div>
                        <div class="contact-value">{{ str_replace(' ', '', $lawyer->whatsapp ?? '0912345678') }}</div>
                    </div>
                </div>
                <div class="contact-row">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-info">
                        <div class="contact-label">البريد الإلكتروني</div>
                        <div class="contact-value">{{ $lawyer->email ?? 'lawyer@example.com' }}</div>
                    </div>
                </div>
                <div class="contact-row">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-info">
                        <div class="contact-label">العنوان</div>
                        <div class="contact-value">{{ $lawyer->lawyerProfile->address ?? 'طرابلس، ليبيا' }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

    {{-- ====================== العمود الأيسر ====================== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- صندوق التتبع وإشعار حالة الاستشارة من السحابة --}}
        @php
            $latestConsultation = \App\Models\PaidConsultation::where('user_id', auth()->id())
                ->where('lawyer_id', $lawyer->id)
                ->orderBy('created_at', 'desc')
                ->first();
        @endphp

        @if($latestConsultation)
            <div class="info-card">
                <div class="info-card-body">
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100">
                        <h4 class="text-slate-800 text-sm font-bold flex items-center gap-2">
                            <i class="fas fa-info-circle text-[#0061ff]"></i> حالة طلب استشارتك الأخير مع هذا المحامي
                        </h4>
                        <span class="text-[10px] text-slate-500 bg-slate-100 px-3 py-1 rounded-full">مزامنة سحابية</span>
                    </div>

                    @if($latestConsultation->status === 'pending')
                        <div class="p-4 bg-amber-50/50 border border-amber-100 rounded-xl flex items-center gap-3 text-slate-700 text-sm font-semibold">
                            <i class="fas fa-circle-notch fa-spin text-amber-500"></i>
                            <span>تم استلام وقائع النزاع بنجاح، وجاري دراستها وتحديد أتعابها من قبل المحامي...</span>
                        </div>
                    @elseif($latestConsultation->status === 'accepted')
                        <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl flex justify-between items-center flex-wrap gap-3 w-full">
                            <div class="space-y-1">
                                <p class="text-emerald-700 text-sm font-bold flex items-center gap-1.5">
                                    <i class="fas fa-check-circle"></i> تم قبول طلب الاستشارة وتحديد التكلفة بنجاح!
                                </p>
                                <p class="text-slate-500 text-xs font-medium">الرسوم المعتمدة: <strong>{{ number_format($latestConsultation->price, 2) }} د.ل</strong></p>
                            </div>
                        </div>
                    @elseif($latestConsultation->status === 'rejected')
                        <div class="p-4 bg-rose-50/50 border border-rose-100 rounded-xl flex items-start gap-3 w-full">
                            <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs shrink-0 mt-0.5">
                                <i class="fas fa-user-times"></i>
                            </span>
                            <div class="space-y-1">
                                <p class="text-slate-700 text-xs font-bold">يعتذر المحامي عن قبول طلب هذه الاستشارة</p>
                                <p class="text-slate-500 text-[11px] leading-relaxed font-medium">
                                    عذراً، يبدو أن تفاصيل وقائع مشكلتك خارج النطاق التخصصي الدقيق للمكتب حالياً. نعتذر منك وننصحك بتصفح دليل منصة الميزان واختيار محامٍ آخر يتوافق مع مجال قضيتك لنيل الدعم السليم.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- تبويب السيرة الذاتية --}}
        <div id="info-section" class="space-y-6">

            <div class="info-card">
                <div class="info-card-header">
                    <h3 class="text-slate-800 text-lg font-bold flex items-center gap-3">
                        <i class="fas fa-user-graduate text-[#0061ff]"></i> السيرة الذاتية
                    </h3>
                </div>
                <div class="info-card-body mt-2">
                    @if(optional($lawyer->lawyerProfile)->bio)
                        <p class="text-slate-600 text-sm leading-loose font-medium">{{ $lawyer->lawyerProfile->bio }}</p>
                    @else
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-100">
                            <i class="fas fa-edit text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-500 font-semibold text-sm">لم يتم إضافة تفاصيل السيرة الذاتية بعد.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <h3 class="text-slate-800 text-lg font-bold flex items-center gap-3">
                        <i class="fas fa-graduation-cap text-[#10b981]"></i> المؤهلات الأكاديمية
                    </h3>
                </div>
                <div class="info-card-body mt-2">
                    @php
                        $qualifications = $lawyer->lawyerProfile->qualifications ?? null;
                        $qualArray = [];
                        if($qualifications) {
                            if(is_string($qualifications)) {
                                $qualArray = explode("\n", str_replace(["-", "*", "•"], "\n", $qualifications));
                                $qualArray = array_filter(array_map('trim', $qualArray));
                            } elseif(is_array($qualifications)) {
                                $qualArray = $qualifications;
                            }
                        }
                    @endphp

                    @if(count($qualArray) > 0)
                        @foreach($qualArray as $qual)
                            <div class="list-item-clean">
                                <i class="fas fa-certificate text-[#10b981] mt-1 text-sm"></i>
                                <div class="text-slate-700 text-sm font-semibold leading-relaxed">{{ $qual }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-100">
                            <i class="fas fa-graduation-cap text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-500 font-semibold text-sm">لم يتم إضافة المؤهلات الأكاديمية بعد.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <h3 class="text-slate-800 text-lg font-bold flex items-center gap-3">
                        <i class="fas fa-briefcase text-[#0061ff]"></i> الخبرات المهنية
                    </h3>
                </div>
                <div class="info-card-body mt-2">
                    @php
                        $experience = $lawyer->lawyerProfile->experience ?? null;
                        $experienceArray = [];
                        if($experience) {
                            if(is_string($experience)) {
                                $experienceArray = explode("\n", str_replace(["-", "*", "•"], "\n", $experience));
                                $experienceArray = array_filter(array_map('trim', $experienceArray));
                            } elseif(is_array($experience)) {
                                $experienceArray = $experience;
                            }
                        }
                    @endphp
                    @if(count($experienceArray) > 0)
                        @foreach($experienceArray as $exp)
                            <div class="list-item-clean">
                                <i class="fas fa-check-circle text-[#0061ff] mt-1 text-sm"></i>
                                <div class="text-slate-700 text-sm font-semibold leading-relaxed">{{ $exp }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-100">
                            <i class="fas fa-briefcase-slash text-slate-300 text-3xl mb-3 block"></i>
                            <p class="text-slate-500 font-semibold text-sm">لا توجد خبرات مهنية مسجلة حالياً.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- تبويب حجز الموعد --}}
        <div id="booking-section" class="hidden">
            <div class="info-card">
                <div class="info-card-body">
                    <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100">
                        <h3 class="text-slate-800 text-xl font-bold flex items-center gap-3">
                            <i class="fas fa-calendar-plus text-[#0061ff]"></i> حجز موعد مكتبي
                        </h3>
                        <button onclick="showProfileTab('info-section')" class="btn-profile-secondary px-5 py-2">إلغاء</button>
                    </div>

                    {{-- قسم أوقات العمل --}}
                    <div style="margin-bottom: 24px; background: #f6f9ff; border: 1px solid #dbe7fb; border-radius: 16px; padding: 20px;">
                        <h3 style="font-size: 15px; font-weight: 800; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-clock" style="color: #0061ff;"></i> أوقات العمل المتاحة
                        </h3>

                        @php
                            $daysMap = [6 => 'السبت', 0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة'];

                            $formatTime = function($time) {
                                return str_replace(['AM', 'PM'], ['ص', 'م'], \Carbon\Carbon::parse($time)->format('g:i A'));
                            };

                            // جلب وترتيب الأيام وتقسيمها إلى أسطر كل سطر به يومان
                            $workingHours = $lawyer->lawyerWorkingHours()->where('is_active', true)->get()->sortBy(function($s) {
                                return $s->day_of_week == 6 ? -1 : $s->day_of_week;
                            })->chunk(2);
                        @endphp

                        <div style="display: grid; gap: 10px;">
                            @foreach($workingHours as $row)
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    @foreach($row as $schedule)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 11px 14px; background: #ffffff; border-radius: 12px; border: 1px solid #e6eefb;">
                                            <span style="font-weight: 700; color: #475569; font-size: 12.5px;">{{ $daysMap[$schedule->day_of_week] }}</span>
                                            <span style="color: #1d4ed8; font-weight: 600; font-size: 11px; white-space: nowrap;">
                                                من&nbsp;{{ $formatTime($schedule->start_time) }}
                                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                                إلى&nbsp;{{ $formatTime($schedule->end_time) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <form id="appointmentForm" action="{{ route('client.appointments.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="lawyer_id" value="{{ $lawyer->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">تاريخ الموعد</label>
                                <input type="date" name="appointment_date" required min="{{ date('Y-m-d') }}" class="form-input">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">التوقيت المفضل</label>
                                <input type="time" name="appointment_time" required class="form-input">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">ملاحظات إضافية (اختياري)</label>
                            <textarea name="notes" rows="4" placeholder="يرجى كتابة سبب طلب المقابلة باختصار..." class="form-input resize-none"></textarea>
                        </div>

                        <div class="agreement-box">
                            <label class="agreement-label-wrapper">
                                <input type="checkbox" required class="agreement-checkbox">
                                <span class="agreement-text">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    يلتزم المحامي بالحضور في الموعد المحدد وتخصيص الوقت اللازم لتقديم الخدمة، لذا نأمل التزامكم بالحضور في الوقت المتفق عليه لضمان الاستفادة الكاملة من الموعد.
                                </span>
                            </label>
                        </div>

                        <button type="submit" id="appointmentSubmitBtn" class="btn-profile-action w-full py-4 text-base mt-2">
                            <i class="fas fa-check-circle"></i> تأكيد وإرسال طلب الحجز
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- تبويب الاستشارة --}}
        <div id="consultation-section" class="hidden">
            <div class="info-card">
                <div class="info-card-body">
                    <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100">
                        <h3 class="text-slate-800 text-xl font-bold flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar text-[#0061ff]"></i> طلب استشارة قانونية
                        </h3>
                        <button onclick="showProfileTab('info-section')" class="btn-profile-secondary px-5 py-2">إلغاء</button>
                    </div>

                    {{-- 🌟 التعديل السحري هنا: توجيه المسار إلى consultations.store لإرسال البيانات بنجاح للسحابة --}}
                    <form id="consultationForm" action="{{ route('consultations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="lawyer_id" value="{{ $lawyer->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">عنوان الاستشارة</label>
                                <input type="text" name="title" required placeholder="مثال: استشارة بخصوص نزاع تجاري" class="form-input">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">تصنيف القضية</label>
                                <select name="legal_category" required class="form-input">
                                    <option value="" disabled selected>اختر التصنيف...</option>
                                    <option value="قضايا الشركات">قضايا الشركات</option>
                                    <option value="القضايا المدنية">القضايا المدنية</option>
                                    <option value="الأحوال الشخصية">الأحوال الشخصية</option>
                                    <option value="القضايا الجنائية">القضايا الجنائية</option>
                                    <option value="أخرى">أخرى</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">صفتك القانونية</label>
                                <select name="client_role" required class="form-input">
                                    <option value="مدعي / شاكي">مدعي</option>
                                    <option value="مدعى عليه / مشكو في حقه">مدعى عليه</option>
                                    <option value="شاهد">شاهد</option>
                                    <option value="طرف ثالث / مستفسر فقط">مستفسر</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">الطرف الخصم</label>
                                <input type="text" name="opponent_name" placeholder="اسم الطرف الآخر إن وجد" class="form-input">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">درجة الاستعجال</label>
                                <select name="urgency_level" required class="form-input">
                                    <option value="عادية">عادية</option>
                                    <option value="مستعجلة">مستعجلة</option>
                                    <option value="طارئة جداً">طارئة جداً</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">هل توجد دعوى مقامة حالياً؟</label>
                                <select name="has_existing_case" required class="form-input bg-white">
                                    <option value="لا">لا</option>
                                    <option value="نعم">نعم</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">المحكمة أو الموعد (إن وجد)</label>
                                <input type="text" name="court_or_deadline" placeholder="مثال: محكمة طرابلس / جلسة الأحد" class="form-input bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">تفاصيل الاستشارة</label>
                            <textarea name="description" rows="5" required placeholder="يرجى كتابة كافة التفاصيل المتعلقة باستشارتك بوضوح..." class="form-input resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">المستندات الداعمة (اختياري)</label>
                            <input type="file" name="attachment" class="w-full text-sm file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#eff6ff] file:text-[#0061ff] hover:file:bg-[#dbeafe] file:transition-colors border border-dashed border-slate-300 p-4 rounded-xl cursor-pointer">
                        </div>

                        <div class="agreement-box">
                            <label class="agreement-label-wrapper">
                                <input type="checkbox" name="terms_agreement" required class="agreement-checkbox">
                                <span class="agreement-text">
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                    أُقر وأتعهد بأن جميع البيانات والمعلومات والمستندات المقدمة مني صحيحة ودقيقة وكاملة، وأتحمل كامل المسؤولية القانونية عن صحتها. كما أُدرك وأوافق على أن تقديم هذا الطلب أو إرساله لا يُنشئ أي علاقة تمثيل قانوني أو تعاقدية مع المحامي، وأن التمثيل القانوني الفعلي لا يبدأ إلا بعد مراجعة الطلب والموافقة عليه من قبل المحامي، وإتمام جميع الإجراءات المالية والاتفاقات اللازمة وفقاً للشروط المعتمدة.
                                </span>
                            </label>
                        </div>

                        <button type="submit" id="submitBtn" class="btn-profile-action w-full py-4 text-base mt-2">
                            <i class="fas fa-paper-plane"></i> إرسال الطلب للمراجعة
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="successModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center px-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="showProfileTab('info-section')"></div>
    <div class="relative bg-white rounded-3xl p-8 text-center max-w-sm w-full shadow-2xl border border-slate-100">
        <div class="w-20 h-20 bg-[#ecfdf5] text-[#10b981] rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-check-circle text-4xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2">تم الإرسال بنجاح!</h3>
        <p class="text-slate-500 text-sm mb-6 font-medium">سيتم إشعارك فور تأكيد المحامي للموعد.</p>
        <button onclick="showProfileTab('info-section')" class="btn-profile-action w-full py-3 justify-center">حسناً، فهمت</button>
    </div>
</div>

<div id="consultationSuccessModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center px-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="showProfileTab('info-section')"></div>
    <div class="relative bg-white rounded-3xl p-8 text-center max-w-sm w-full shadow-2xl border border-slate-100">
        <div class="w-20 h-20 bg-[#eff6ff] text-[#0061ff] rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-paper-plane text-4xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2">تم الإرسال بنجاح!</h3>
        <p class="text-slate-500 text-sm mb-6 font-medium">جاري تحويل طلبك للمحامي لتقييمه والرد عليك.</p>
        <button onclick="window.location.reload();" class="btn-profile-action w-full py-3 justify-center">حسناً، فهمت</button>
    </div>
</div>

<div id="statusToast" class="hidden fixed top-6 left-1/2 -translate-x-1/2 z-[250] transition-all duration-300 transform -translate-y-10 opacity-0">
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-xl bg-white border border-rose-100 text-rose-600">
        <span>⚠️</span>
        <div id="toastText" class="font-bold text-sm"></div>
    </div>
</div>

<script>
    function showProfileTab(tabId) {
        // إخفاء كل الأقسام والـ Modals
        document.getElementById('info-section').classList.add('hidden');
        document.getElementById('booking-section').classList.add('hidden');
        document.getElementById('consultation-section').classList.add('hidden');
        document.getElementById('successModal').classList.add('hidden');
        document.getElementById('consultationSuccessModal').classList.add('hidden');

        // إعادة تعيين حالة الأزرار عند التنقل بين التبويبات (لحل مشكلة التعليق)
        const appointmentBtn = document.getElementById('appointmentSubmitBtn');
        if(appointmentBtn) {
            appointmentBtn.disabled = false;
            appointmentBtn.innerHTML = '<i class="fas fa-check-circle"></i> تأكيد وإرسال طلب الحجز';
        }

        const consultBtn = document.getElementById('submitBtn'); // تأكد أن ID زر الاستشارة هو submitBtn
        if(consultBtn) {
            consultBtn.disabled = false;
            consultBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الطلب للمراجعة';
        }

        const btnInfo = document.getElementById('btn-info-section');
        const btnBooking = document.getElementById('btn-booking-section');
        const btnConsult = document.getElementById('btn-consultation-section');

        [btnInfo, btnBooking, btnConsult].forEach(btn => {
            if(btn) {
                btn.className = "btn-profile-secondary w-full";
            }
        });

        document.getElementById(tabId).classList.remove('hidden');

        if(tabId === 'info-section') {
            btnInfo.className = "btn-profile-action w-full";
        } else if(tabId === 'booking-section') {
            btnBooking.className = "btn-profile-action w-full";
        } else if(tabId === 'consultation-section') {
            btnConsult.className = "btn-profile-action w-full";
        }
    }

    function showToast(message) {
        const toast = document.getElementById('statusToast');
        document.getElementById('toastText').innerText = message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('-translate-y-10', 'opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 5000);
    }

    document.getElementById('appointmentForm').onsubmit = async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('appointmentSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الإرسال...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });
            const result = await response.json();
            if (result.success) {
                form.reset();
                document.getElementById('successModal').classList.remove('hidden');
                // إعادة الزر لوضعه الطبيعي في حالة النجاح
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> تأكيد وإرسال طلب الحجز';
            } else {
                showToast(result.message || 'فشل الإرسال');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> تأكيد وإرسال طلب الحجز';
            }
        } catch (error) {
            showToast('حدث خطأ في النظام');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> تأكيد وإرسال طلب الحجز';
        }
    };

    document.getElementById('consultationForm').onsubmit = async function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الإرسال...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });
            if (response.ok) {
                form.reset();
                document.getElementById('consultationSuccessModal').classList.remove('hidden');
                // إعادة الزر لوضعه الطبيعي في حالة النجاح
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الطلب للمراجعة';
            } else {
                const result = await response.json();
                showToast(result.message || 'يرجى التحقق من البيانات');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الطلب للمراجعة';
            }
        } catch (error) {
            showToast('خطأ في الاتصال بقاعدة البيانات السحابية');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الطلب للمراجعة';
        }
    };
</script>
@endsection
