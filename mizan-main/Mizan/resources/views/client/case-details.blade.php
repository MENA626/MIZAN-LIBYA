@extends('layouts.app')

@section('title', 'تتبع القضية #' . ($case->case_number ?? $case->id))

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    .font-cairo { font-family: 'Cairo', sans-serif !important; }
    .animate-gradient-bg { background: linear-gradient(270deg, #0f172a, #1e293b, #0f172a); background-size: 400% 400%; animation: gradient-move 10s ease infinite; }
    @keyframes gradient-move { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .wave-fluid { position: absolute; top: -60%; right: -20%; width: 150%; height: 200%; background: rgba(255, 255, 255, 0.04); border-radius: 40%; animation: wave-inside 15s infinite linear; pointer-events: none; }
    @keyframes wave-inside { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .animate-float { animation: float 6s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
</style>

<div class="min-h-screen bg-[#f0f4f8] py-8 px-4 sm:px-6 lg:px-12 w-full font-cairo" dir="rtl">
    <div class="space-y-6">

        {{-- زر العودة --}}
        <a href="{{ url('/client/my-case') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-right"></i> العودة لقائمة القضايا
        </a>




        {{-- ================= الهيدر الحركي ================= --}}
        <div class="relative w-full bg-[#0f172a] rounded-3xl p-8 mb-4 overflow-hidden animate-gradient-bg shadow-2xl border border-slate-700">
            <div class="wave-fluid"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-lg animate-float">
                        <i class="fas fa-balance-scale-right text-4xl text-[#3b82f6]"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-white mb-2">متابعة ملف القضية</h1>
                        <p class="text-blue-200 text-sm font-medium">تُمكّنكم هذه الخدمة من متابعة كافة ما يتعلق بالقضية المفتوحة مع المحامي المكلّف بها، والاطلاع على المستجدات والإجراءات والمستندات والمراسلات المرتبطة بالقضية.</p>
                    </div>
                </div>
            </div>
        </div>



        

        {{-- ================= شريط التنبيهات العاجلة ================= --}}
        @if($urgentUpdates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($urgentUpdates->take(4) as $alert)
                <div class="backdrop-blur-sm rounded-xl p-3 flex items-center gap-3 shadow-sm
                    {{ $alert->update_type == 'urgent' ? 'bg-red-50/80 border border-red-200' : ($alert->update_type == 'court_update' ? 'bg-[#eff6ff] border border-[#bfdbfe]' : 'bg-amber-50/80 border border-amber-200') }}">
                    @if($alert->update_type == 'urgent')
                        <span class="w-2.5 h-2.5 bg-red-500 rounded-full shrink-0 animate-pulse"></span>
                        <p class="text-sm font-medium text-red-700">{{ $alert->title }}: {{ \Str::limit($alert->content, 80) }}</p>
                    @elseif($alert->update_type == 'court_update')
                        <i class="fas fa-info-circle text-[#3b82f6] text-lg"></i>
                        <p class="text-xs font-semibold text-[#1e3a8a]">{{ $alert->title }}: <span class="font-medium text-[#2563eb]">{{ \Str::limit($alert->content, 80) }}</span></p>
                    @else
                        <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
                        <p class="text-sm font-medium text-amber-700">{{ $alert->title }}: {{ \Str::limit($alert->content, 80) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        {{-- ================= هيدر القضية الشامل ================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-r-4 border-r-[#1d4ed8]">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    @if($case->status == 'open')
                        <span class="px-3 py-1 bg-green-50 text-green-600 rounded-md text-xs font-bold"><i class="fas fa-circle text-[8px] ml-1"></i> قضية نشطة</span>
                    @elseif($case->status == 'pending')
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-md text-xs font-bold"><i class="fas fa-hourglass-half text-[10px] ml-1"></i> قيد التحضير</span>
                    @elseif($case->status == 'closed')
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-md text-xs font-bold"><i class="fas fa-check-circle text-[10px] ml-1"></i> مكتملة</span>
                    @endif
                    @if($case->court_name)
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-md text-xs font-bold"><i class="fas fa-gavel text-[10px] ml-1"></i> {{ $case->court_name }}</span>
                    @endif
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-[#0f172a] tracking-tight mb-2">{{ $case->case_type ?? 'تفاصيل القضية' }}</h1>
                <p class="text-sm text-slate-500 font-medium">
                    المدعي: <span class="font-semibold text-slate-800">{{ $case->client_name }}</span>
                    @if($case->opponent_name)
                        <span class="mx-2 text-slate-300">|</span>
                        المدعى عليه: <span class="font-semibold text-slate-800">{{ $case->opponent_name }}</span>
                    @endif
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-6 bg-[#f8fafc] p-4 rounded-xl border border-slate-100 shrink-0 w-full lg:w-auto">
                @if($case->claim_amount)
                <div>
                    <p class="text-xs text-slate-500 font-semibold mb-1">قيمة المطالبة</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($case->claim_amount) }} <span class="text-xs text-green-500 font-semibold">د.ل</span></p>
                </div>
                <div class="hidden lg:block w-px h-10 bg-slate-200"></div>
                @endif
                <div>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                        <span class="font-bold text-slate-700">{{ $case->case_number ?? $case->id }}</span>
                        <span class="text-slate-300">•</span>
                        <span class="bg-[#eff6ff] text-[#1d4ed8] text-xs font-bold px-3 py-1 rounded-lg">{{ $case->case_category ?? $case->case_type }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= التوزيع الشبكي (12 عمود) ================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            
            {{-- ================= العمود الأيمن (3 أعمدة) ================= --}}
            <div class="xl:col-span-3 space-y-6">

                {{-- الملخص المالي والدفع --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h2 class="text-sm font-bold text-[#0f172a] mb-4"><i class="fas fa-wallet text-[#3b82f6] ml-2"></i> الملخص المالي</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-xs text-slate-500 font-bold">إجمالي الأتعاب</span>
                            <span class="text-sm font-black text-slate-800">{{ number_format($case->total_fees, 2) }} د.ل</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-xs text-slate-500 font-bold">المدفوع</span>
                            <span class="text-sm font-black text-emerald-600">{{ number_format($case->paid_fees, 2) }} د.ل</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-500 font-bold">المتبقي</span>
                            <span class="text-sm font-black text-rose-600">{{ number_format($case->total_fees - $case->paid_fees, 2) }} د.ل</span>
                        </div>
                        
                        @if(($case->total_fees - $case->paid_fees) > 0)
                            <button type="button" onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="w-full py-2.5 mt-2 bg-[#0f172a] hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-credit-card"></i> دفع الأتعاب
                            </button>
                        @else
                            <div class="w-full py-2.5 mt-2 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-bold flex items-center justify-center gap-2 border border-emerald-100">
                                <i class="fas fa-check-circle"></i> تم سداد الأتعاب بالكامل
                            </div>
                        @endif
                    </div>
                </div>

                {{-- سجل المدفوعات السابقة --}}
                @php
                    $clientPayments = \App\Models\Transaction::where('reference_type', \App\Models\LegalCase::class)
                        ->where('reference_id', $case->id)
                        ->where('user_id', $case->user_id)
                        ->where('type', 'payment')
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp
                
                @if($clientPayments->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h2 class="text-sm font-bold text-[#0f172a] mb-4"><i class="fas fa-history text-slate-400 ml-2"></i> سجل المدفوعات السابقة</h2>
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($clientPayments as $payment)
                        <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ number_format($payment->amount, 2) }} د.ل</p>
                                <p class="text-[10px] text-slate-500 mt-1"><i class="far fa-clock ml-1"></i>{{ $payment->created_at->translatedFormat('d F Y - h:i A') }}</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center border border-emerald-200 shrink-0">
                                <i class="fas fa-check text-emerald-600 text-xs"></i>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- أجندة القضية --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-sm font-bold text-[#0f172a]"><i class="far fa-calendar-alt text-[#3b82f6] ml-2"></i> أجندة القضية</h2>
                    </div>
                    <div class="space-y-3">
                        @php
                            $scheduledSessions = $case->sessions->where('status', 'scheduled')->sortBy('session_date')->take(3);
                        @endphp
                        @forelse($scheduledSessions as $upcoming)
                            <div class="flex gap-3 items-start">
                                <div class="w-2 h-2 rounded-full bg-[#3b82f6] mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $upcoming->session_type }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5"><i class="far fa-clock ml-1"></i> {{ $upcoming->session_date->translatedFormat('d F Y • h:i A') }}</p>
                                    @if($upcoming->location)
                                        <p class="text-xs text-slate-400 mt-0.5"><i class="fas fa-map-marker-alt ml-1"></i> {{ $upcoming->location }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">لا توجد جلسات مجدولة قادمة</p>
                        @endforelse
                    </div>
                </div>

                {{-- تواصل مع المحامي --}}
                @if($case->lawyer)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h2 class="text-sm font-bold text-[#0f172a] mb-4"><i class="fas fa-user-tie text-[#3b82f6] ml-2"></i> تواصل مع المحامي</h2>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            @if($case->lawyer->avatar)
                                <img src="{{ asset('uploads/avatars/'.$case->lawyer->avatar) }}" class="w-10 h-10 rounded-full" alt="">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($case->lawyer->name) }}&background=0f172a&color=fff&font-family=Cairo" class="w-10 h-10 rounded-full" alt="">
                            @endif
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $case->lawyer->name }}</p>
                                @if($case->lawyer->lawyerProfile && $case->lawyer->lawyerProfile->specialization)
                                    <p class="text-[10px] text-slate-500">محامي معتمد</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-100">
                            @if($case->lawyer->phone)
                                <p><i class="fas fa-phone-alt ml-2 text-[#3b82f6]"></i> {{ $case->lawyer->phone }}</p>
                            @endif
                            @if($case->lawyer->email)
                                <p><i class="fas fa-envelope ml-2 text-[#3b82f6]"></i> {{ $case->lawyer->email }}</p>
                            @endif
                            @if($case->lawyer->lawyerProfile && $case->lawyer->lawyerProfile->address)
                                <p><i class="fas fa-map-marker-alt ml-2 text-[#3b82f6]"></i> {{ $case->lawyer->lawyerProfile->address }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- الفريق الموكل --}}
                @if($case->lawyer)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h2 class="text-sm font-bold text-[#0f172a] mb-4"><i class="fas fa-users text-slate-400 ml-2"></i> الفريق الموكل</h2>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-3">
                                @if($case->lawyer->avatar)
                                    <img src="{{ asset('uploads/avatars/'.$case->lawyer->avatar) }}" class="w-10 h-10 rounded-full border border-slate-200" alt="">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($case->lawyer->name) }}&background=0f172a&color=fff&font-family=Cairo" class="w-10 h-10 rounded-full border border-slate-200" alt="">
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $case->lawyer->name }}</p>
                                    <p class="text-xs text-slate-500">المترافع الرئيسي</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- ================= العمود الأوسط (6 أعمدة) ================= --}}
            <div class="xl:col-span-6 space-y-6">
                
                {{-- تقارير جميع الجلسات --}}
@if($case->sessions->count() > 0)
<div class="space-y-6">
    <h2 class="text-xl font-bold text-[#0f172a] flex items-center gap-2 mb-4">
        <i class="fas fa-file-signature text-[#3b82f6]"></i> تقارير الجلسات المفصلة
    </h2>
    
    @foreach($case->sessions as $sessionItem)
    <div class="bg-white rounded-2xl shadow-sm border {{ $sessionItem->status == 'completed' ? 'border-emerald-200' : ($sessionItem->status == 'postponed' ? 'border-amber-200' : 'border-blue-200') }} overflow-hidden">
        <div onclick="document.getElementById('session-{{ $sessionItem->id }}').classList.toggle('hidden'); document.getElementById('icon-{{ $sessionItem->id }}').classList.toggle('fa-chevron-up'); document.getElementById('icon-{{ $sessionItem->id }}').classList.toggle('fa-chevron-down');" class="{{ $sessionItem->status == 'completed' ? 'bg-emerald-50 hover:bg-emerald-100' : ($sessionItem->status == 'postponed' ? 'bg-amber-50 hover:bg-amber-100' : 'bg-blue-50 hover:bg-blue-100') }} p-4 flex justify-between items-center border-b border-slate-100 cursor-pointer transition-colors">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas {{ $sessionItem->status == 'completed' ? 'fa-check-circle text-emerald-600' : ($sessionItem->status == 'postponed' ? 'fa-pause-circle text-amber-600' : 'fa-calendar-check text-blue-600') }}"></i> 
                {{ $sessionItem->session_type }}
            </h3>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-600 bg-white px-3 py-1.5 rounded-md shadow-sm border border-slate-200">{{ $sessionItem->session_date->translatedFormat('d F Y') }}</span>
                <span class="text-xs font-bold text-white px-3 py-1.5 rounded-md shadow-sm {{ $sessionItem->status == 'completed' ? 'bg-emerald-600' : ($sessionItem->status == 'postponed' ? 'bg-amber-600' : 'bg-blue-600') }}">
                    {{ $sessionItem->status == 'completed' ? 'مكتملة' : ($sessionItem->status == 'postponed' ? 'مؤجلة' : 'مجدولة') }}
                </span>
                <i id="icon-{{ $sessionItem->id }}" class="fas {{ $loop->first ? 'fa-chevron-up' : 'fa-chevron-down' }} text-slate-400"></i>
            </div>
        </div>

        <div id="session-{{ $sessionItem->id }}" class="p-6 space-y-6 {{ $loop->first ? '' : 'hidden' }}">
            {{-- تفاصيل المكان --}}
            @if($sessionItem->location)
            <div class="mb-4 bg-slate-50 border border-slate-200 rounded-xl p-3 flex items-center gap-3">
                <div class="bg-white w-8 h-8 rounded-full flex items-center justify-center shadow-sm border border-slate-100">
                    <i class="fas fa-map-marker-alt text-slate-500"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-400 mb-0.5">مكان الجلسة</h4>
                    <p class="text-sm font-bold text-slate-700">{{ $sessionItem->location }}</p>
                </div>
            </div>
            @endif

            {{-- شارات الحضور --}}
            @if($sessionItem->status != 'scheduled')
            <div>
                <div class="flex flex-wrap gap-3 mb-4">
                    <span class="text-xs font-bold px-3 py-1.5 {{ $sessionItem->our_attendance ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-50 text-slate-600 border-slate-200' }} rounded-lg border flex items-center gap-2">
                        <i class="fas {{ $sessionItem->our_attendance ? 'fa-check text-green-600' : 'fa-times text-slate-400' }}"></i> {{ $sessionItem->our_attendance ? 'حضرنا' : 'لم نحضر' }}
                    </span>
                    <span class="text-xs font-bold px-3 py-1.5 {{ $sessionItem->opponent_attendance ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-50 text-slate-600 border-slate-200' }} rounded-lg border flex items-center gap-2">
                        <i class="fas {{ $sessionItem->opponent_attendance ? 'fa-check text-green-600' : 'fa-times text-slate-400' }}"></i> {{ $sessionItem->opponent_attendance ? 'حضر الخصم' : 'غاب الخصم' }}
                    </span>
                    @if(!is_null($sessionItem->expert_attendance))
                    <span class="text-xs font-bold px-3 py-1.5 {{ $sessionItem->expert_attendance ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-50 text-slate-600 border-slate-200' }} rounded-lg border flex items-center gap-2">
                        <i class="fas {{ $sessionItem->expert_attendance ? 'fa-check text-green-600' : 'fa-times text-slate-400' }}"></i> {{ $sessionItem->expert_attendance ? 'حضر الخبير' : 'غاب الخبير' }}
                    </span>
                    @endif
                </div>
            </div>
            @endif

            {{-- ملخص الجلسة --}}
            @if($sessionItem->summary)
            <div>
                <h4 class="text-sm font-bold text-slate-500 mb-2 flex items-center gap-2"><i class="fas fa-thumbtack text-slate-400"></i> ملخص الجلسة</h4>
                <p class="text-sm text-slate-700 leading-relaxed bg-[#f8fafc] p-4 rounded-xl border border-slate-100 text-justify">
                    {{ $sessionItem->summary }}
                </p>
            </div>
            @endif

            {{-- أقوال الخصم وردنا --}}
            @if($sessionItem->opponent_claims || $sessionItem->our_response)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($sessionItem->opponent_claims)
                <div class="p-4 rounded-xl border border-slate-100 shadow-sm relative overflow-hidden bg-white">
                    <div class="absolute top-0 right-0 w-1 h-full bg-slate-400"></div>
                    <h4 class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-2"><i class="fas fa-comment-dots text-slate-400"></i> أقوال الخصم</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $sessionItem->opponent_claims }}</p>
                </div>
                @endif
                @if($sessionItem->our_response)
                <div class="p-4 rounded-xl border border-blue-100 shadow-sm relative overflow-hidden bg-[#eff6ff]/30">
                    <div class="absolute top-0 right-0 w-1 h-full bg-[#3b82f6]"></div>
                    <h4 class="text-xs font-bold text-[#1d4ed8] mb-2 flex items-center gap-2"><i class="fas fa-balance-scale text-[#3b82f6]"></i> ردنا وموقف القاضي</h4>
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $sessionItem->our_response }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- سبب التأجيل --}}
            @if($sessionItem->postponement_reason)
            <div class="bg-amber-50 p-4 rounded-xl border border-amber-200 flex gap-4 items-start shadow-sm">
                <div class="bg-white p-2 rounded-full shadow-sm border border-amber-100 shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-800">سبب التأجيل</h4>
                    <p class="text-sm text-amber-700 mt-1.5 leading-relaxed">{{ $sessionItem->postponement_reason }}</p>
                </div>
            </div>
            @endif

            {{-- الإجراءات القادمة للجلسة --}}
            @if($sessionItem->next_session_goal)
            <div class="bg-[#f8fafc] border border-slate-200 p-4 rounded-xl space-y-2">
                <h4 class="text-sm font-bold text-[#0f172a] flex items-center gap-2">
                    <i class="fas fa-shield-alt text-[#3b82f6]"></i> الهدف القادم: 
                    <span class="text-slate-600 text-xs font-medium">{{ $sessionItem->next_session_goal }}</span>
                </h4>
                
                @if($sessionItem->next_session_date)
                <div class="text-xs font-bold text-blue-600 flex items-center gap-2 mt-1">
                    <i class="fas fa-calendar-alt"></i>
                    <span>موعد الجلسة القادمة: {{ $sessionItem->next_session_date->translatedFormat('d F Y') }}</span>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center">
    <i class="fas fa-file-signature text-3xl text-slate-300 mb-3"></i>
    <p class="text-sm font-bold text-slate-500">لم يتم تسجيل أي جلسة بعد</p>
    <p class="text-xs text-slate-400 mt-1">ستظهر تفاصيل الجلسات هنا عندما يضيفها المحامي</p>
</div>
@endif

                {{-- تنبيهات المحامي --}}
                @if($case->updates->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h2 class="text-sm font-bold text-[#0f172a] mb-4 flex items-center gap-2"><i class="fas fa-bell text-[#3b82f6] ml-1"></i> تنبيهات المحامي</h2>
                    <div class="space-y-3">
                        @foreach($case->updates->take(5) as $update)
                        <div class="p-3 rounded-xl border {{ $update->update_type == 'urgent' ? 'border-red-200 bg-red-50/30' : ($update->update_type == 'warning' ? 'border-amber-200 bg-amber-50/30' : 'border-slate-100 bg-slate-50/50') }}">
                            <div class="flex items-center gap-2 mb-1">
                                @if($update->update_type == 'urgent')
                                    <i class="fas fa-exclamation-circle text-red-500 text-xs"></i>
                                @elseif($update->update_type == 'warning')
                                    <i class="fas fa-exclamation-triangle text-amber-500 text-xs"></i>
                                @elseif($update->update_type == 'court_update')
                                    <i class="fas fa-gavel text-blue-500 text-xs"></i>
                                @else
                                    <i class="fas fa-info-circle text-slate-400 text-xs"></i>
                                @endif
                                <p class="text-xs font-black text-slate-800">{{ $update->title }}</p>
                                <span class="text-[10px] text-slate-400 mr-auto">{{ $update->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $update->content }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- ================= العمود الأيسر (3 أعمدة) ================= --}}
            <div class="xl:col-span-3 space-y-6">

                
                {{-- المهام المطلوبة --}}
@if($case->tasks->count() > 0)
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="bg-slate-50 p-4 border-b border-slate-100 flex justify-between items-center">
        <h2 class="text-sm font-bold text-[#0f172a]"><i class="fas fa-tasks text-slate-400 ml-2"></i> المهام المطلوبة منك!</h2>
        @if($case->tasks->where('priority', 'urgent')->where('is_completed', false)->count() > 0)
            <span class="text-[10px] bg-red-100 text-red-600 font-bold px-2 py-1 rounded-md">عاجل</span>
        @endif
    </div>
    <div class="p-4 space-y-3">
        @foreach($case->tasks as $task)
        <label class="flex items-start gap-3 p-3 rounded-xl {{ $task->priority == 'urgent' && !$task->is_completed ? 'bg-red-50 border border-red-100' : 'border border-slate-100' }} {{ $task->is_completed ? 'opacity-60' : '' }} task-label" data-task-id="{{ $task->id }}">
            
            <input type="checkbox" 
                   class="mt-1 w-4 h-4 text-[#3b82f6] rounded {{ $task->priority == 'urgent' ? 'border-red-300' : 'border-slate-300' }} task-checkbox"
                   {{ $task->is_completed ? 'checked disabled' : '' }} 
                   onclick="{{ $task->is_completed ? 'return false;' : '' }}" 
                   data-task-id="{{ $task->id }}">
            
            <div>
                <p class="text-sm font-bold text-slate-800 {{ $task->is_completed ? 'line-through' : '' }}">{{ $task->title }}</p>
                @if($task->description)
                    <p class="text-[11px] text-slate-500 mt-1">{{ $task->description }}</p>
                @endif
                @if($task->due_date)
                    <p class="text-[11px] {{ $task->due_date->isPast() && !$task->is_completed ? 'text-red-600 font-semibold' : 'text-slate-500' }} mt-1">
                        <i class="far fa-clock"></i> الموعد النهائي: {{ $task->due_date->translatedFormat('d F Y') }}
                    </p>
                @endif
            </div>
        </label>
        @endforeach
    </div>
</div>
@endif

                {{-- مستندات القضية --}}
                @if($case->caseDocuments->count() > 0 || $case->file_path)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-sm font-bold text-[#0f172a]"><i class="fas fa-server text-[#3b82f6] ml-2"></i> مستندات القضية</h2>
                        <span class="text-[10px] text-[#1d4ed8] font-bold bg-[#eff6ff] px-2 py-1 rounded-md border border-[#bfdbfe]">{{ $case->caseDocuments->count() + ($case->file_path ? 1 : 0) }} ملفات</span>
                    </div>
                    <div class="space-y-3">
                        {{-- المستند الأساسي المرفق مع القضية --}}
                        @if($case->file_path)
                        <a href="{{ Storage::url($case->file_path) }}" target="_blank" class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:border-[#3b82f6] transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center border border-slate-200 text-red-500 shadow-sm shrink-0">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-slate-800 truncate">مستند الدعوى الأساسي</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">مرفق القضية الأساسي</p>
                                </div>
                            </div>
                            <div class="bg-white p-2 rounded-full border border-slate-200 group-hover:border-[#3b82f6] transition">
                                <i class="fas fa-cloud-download-alt text-slate-400 group-hover:text-[#3b82f6]"></i>
                            </div>
                        </a>
                        @endif

                        {{-- المستندات الإضافية --}}
                        @foreach($case->caseDocuments as $doc)
                        <a href="{{ url('/client/my-case/documents/'.$doc->id.'/download') }}" class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:border-[#3b82f6] transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center border border-slate-200 text-red-500 shadow-sm shrink-0">
                                    <i class="fas fa-file-pdf text-lg"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $doc->document_name ?? basename($doc->file_path) }}</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">{{ $doc->document_type ?? 'مستند إضافي' }} • {{ $doc->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="bg-white p-2 rounded-full border border-slate-200 group-hover:border-[#3b82f6] transition">
                                <i class="fas fa-cloud-download-alt text-slate-400 group-hover:text-[#3b82f6]"></i>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- نافذة الدفع --}}
@if(($case->total_fees - $case->paid_fees) > 0)
<div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/75 backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('paymentModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-right transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="{{ route('client.my-case.checkout', $case->id) }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-extrabold text-slate-800" id="modal-title"><i class="fas fa-credit-card text-blue-600 ml-2"></i> دفع أتعاب القضية</h3>
                        <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 mb-5">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-600">المبلغ المتبقي:</span>
                            <span class="text-lg font-black text-rose-600">{{ number_format($case->total_fees - $case->paid_fees, 2) }} د.ل</span>
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-bold text-slate-700 mb-2">المبلغ المراد دفعه الآن (د.ل) <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount" id="amount" step="0.01" min="1" max="{{ $case->total_fees - $case->paid_fees }}" value="{{ $case->total_fees - $case->paid_fees }}" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-lg font-black text-slate-800 focus:border-blue-500 focus:ring-0 transition-colors" required>
                        <p class="mt-2 text-xs text-slate-500"><i class="fas fa-info-circle text-blue-500 ml-1"></i> يمكنك دفع كامل المبلغ المتبقي، أو إدخال جزء منه للدفع بالتقسيط.</p>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-row-reverse gap-3">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition-all flex items-center justify-center gap-2">
                        تأكيد الدفع <i class="fas fa-arrow-left text-xs ml-1"></i>
                    </button>
                    <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-50 transition-colors">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // تحديث حالة المهام عبر AJAX
    document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const label = this.closest('.task-label');
            const titleEl = label.querySelector('.text-sm.font-bold');
            
            fetch(`/client/my-case/tasks/${taskId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.is_completed) {
                        label.classList.add('opacity-60');
                        titleEl.classList.add('line-through');
                    } else {
                        label.classList.remove('opacity-60');
                        titleEl.classList.remove('line-through');
                    }
                }
            })
            .catch(() => {
                // التراجع في حالة فشل الطلب
                this.checked = !this.checked;
            });
        });
    });
</script>
@endpush
@endsection
