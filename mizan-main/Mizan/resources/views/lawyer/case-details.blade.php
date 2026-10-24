@extends('layouts.lawyer')

@section('title', 'تفاصيل القضية #' . ($case->case_number ?? $case->id))
@section('page_title', 'تفاصيل القضية')
@section('page_subtitle', 'إدارة شاملة لملف القضية رقم #' . ($case->case_number ?? $case->id))

@section('lawyer_cases_active', 'nav-active')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200/60 shadow-sm font-bold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200/60 shadow-sm font-bold flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-rose-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- زر العودة --}}
    <a href="{{ route('lawyer.my-cases') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
        <i class="fas fa-arrow-right"></i> العودة لقائمة القضايا
    </a>

    {{-- ========== بطاقة القضية الرئيسية ========== --}}
    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6 border-r-4 border-r-blue-600">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200">#{{ $case->case_number ?? $case->id }}</span>
                    @if($case->status == 'pending')
                        <span class="text-[10px] font-black bg-amber-50 text-amber-700 px-2 py-1 rounded-lg border border-amber-200"><i class="fas fa-hourglass-half ml-1"></i>قيد التحضير</span>
                    @elseif($case->status == 'open')
                        <span class="text-[10px] font-black bg-blue-50 text-blue-700 px-2 py-1 rounded-lg border border-blue-200"><i class="fas fa-spinner ml-1"></i>في المحكمة</span>
                    @elseif($case->status == 'closed')
                        <span class="text-[10px] font-black bg-emerald-50 text-emerald-700 px-2 py-1 rounded-lg border border-emerald-200"><i class="fas fa-check-circle ml-1"></i>مكتملة</span>
                    @endif
                    @if($case->user_id && $case->user_id != $case->lawyer_id)
                        <span class="text-[10px] font-bold bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg border border-emerald-200"><i class="fas fa-link ml-1"></i>مرتبط بموكل مسجل</span>
                    @endif
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-1">{{ $case->title }}</h2>
                <p class="text-sm text-slate-500">
                    <span class="font-bold">الموكل:</span> {{ $case->client_name }}
                    @if($case->opponent_name) <span class="mx-1 text-slate-300">|</span> <span class="font-bold">الخصم:</span> {{ $case->opponent_name }} @endif
                    @if($case->court_name) <span class="mx-1 text-slate-300">|</span> <span class="font-bold">المحكمة:</span> {{ $case->court_name }} @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 shrink-0">
                @if($case->claim_amount)
                <div class="text-center">
                    <p class="text-[10px] text-slate-500 font-bold">قيمة المطالبة</p>
                    <p class="text-lg font-black text-emerald-600">{{ number_format($case->claim_amount) }} <span class="text-xs">د.ل</span></p>
                </div>
                @endif
                @if($case->total_fees)
                <div class="text-center">
                    <p class="text-[10px] text-slate-500 font-bold">الأتعاب</p>
                    <p class="text-lg font-black text-slate-700">{{ number_format($case->total_fees) }} <span class="text-xs">د.ل</span></p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========== التبويبات ========== --}}
    <div x-data="{ activeTab: 'sessions' }" class="space-y-4">
        <div class="flex flex-wrap gap-2 bg-white rounded-xl p-2 border border-slate-200 shadow-sm">
            <button @click="activeTab = 'sessions'" :class="activeTab === 'sessions' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i> الجلسات <span class="text-xs bg-white/20 px-1.5 rounded">{{ $case->sessions->count() }}</span>
            </button>
            <button @click="activeTab = 'updates'" :class="activeTab === 'updates' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-bell"></i> التنبيهات <span class="text-xs bg-white/20 px-1.5 rounded">{{ $case->updates->count() }}</span>
            </button>
            <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-tasks"></i> المهام <span class="text-xs bg-white/20 px-1.5 rounded">{{ $case->tasks->count() }}</span>
            </button>
            <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2">
                <i class="fas fa-file-alt"></i> المستندات <span class="text-xs bg-white/20 px-1.5 rounded">{{ $case->caseDocuments->count() }}</span>
            </button>
        </div>

        {{-- ==================== تبويب الجلسات ==================== --}}
        <div x-show="activeTab === 'sessions'" x-cloak class="space-y-4">
            {{-- فورم إضافة جلسة --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 flex justify-between items-center cursor-pointer" onclick="document.getElementById('sessionForm').classList.toggle('hidden')">
                    <h3 class="text-sm font-black text-slate-700 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i> إضافة جلسة جديدة</h3>
                    <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                </div>
                <form id="sessionForm" action="{{ url('/lawyer/cases/'.$case->id.'/sessions') }}" method="POST" class="hidden p-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">تاريخ ووقت الجلسة *</label>
                            <input type="datetime-local" name="session_date" required class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">نوع الجلسة *</label>
                            <select name="session_type" required class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                                <option value="مرافعة">مرافعة</option>
                                <option value="نطق حكم">نطق حكم</option>
                                <option value="تحقيق">تحقيق</option>
                                <option value="استماع">استماع</option>
                                <option value="صلح">صلح</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">الحالة *</label>
                            <select name="status" id="sessionStatus" onchange="toggleSessionFields()" required class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                                <option value="scheduled">مجدولة (قادمة)</option>
                                <option value="completed">مكتملة</option>
                                <option value="postponed">مؤجلة</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-1 text-slate-700">المكان (المحكمة أو القاعة)</label>
                        <input type="text" name="location" placeholder="مثال: القاعة رقم 4" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                    </div>

                    {{-- الحقول الخاصة بالجلسة المكتملة والمؤجلة --}}
                    <div id="pastSessionFields" class="space-y-4 hidden">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                            <h4 class="text-sm font-bold text-slate-800 border-b border-slate-200 pb-2"><i class="fas fa-clipboard-list text-blue-500 ml-1"></i> مجريات الجلسة</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="flex items-center gap-2 text-sm font-bold text-slate-700 bg-white p-3 rounded-xl border border-slate-200 shadow-sm cursor-pointer">
                                    <input type="hidden" name="our_attendance" value="0">
                                    <input type="checkbox" name="our_attendance" value="1" checked class="rounded text-blue-600 w-4 h-4"> <span>حضرنا الجلسة</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-bold text-slate-700 bg-white p-3 rounded-xl border border-slate-200 shadow-sm cursor-pointer">
                                    <input type="hidden" name="opponent_attendance" value="0">
                                    <input type="checkbox" name="opponent_attendance" value="1" class="rounded text-blue-600 w-4 h-4"> <span>حضر الخصم</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm font-bold text-slate-700 bg-white p-3 rounded-xl border border-slate-200 shadow-sm cursor-pointer">
                                    <input type="hidden" name="expert_attendance" value="0">
                                    <input type="checkbox" name="expert_attendance" value="1" class="rounded text-blue-600 w-4 h-4"> <span>حضر الخبير</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-xs font-black mb-1 text-slate-700">ملخص عام للجلسة</label>
                                <textarea name="summary" rows="2" placeholder="ملخص ما جرى في الجلسة..." class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-medium focus:border-blue-500 focus:outline-none"></textarea>
                            </div>
                            <div id="completedOnlyFields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black mb-1 text-slate-700">أقوال ومذكرات الخصم</label>
                                    <textarea name="opponent_claims" rows="2" placeholder="ماذا قدم الخصم؟" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-medium focus:border-blue-500 focus:outline-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-black mb-1 text-slate-700">ردنا وموقف القاضي</label>
                                    <textarea name="our_response" rows="2" placeholder="بماذا رددنا؟ وما قرار القاضي؟" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-medium focus:border-blue-500 focus:outline-none"></textarea>
                                </div>
                            </div>
                            <div id="postponedOnlyFields" class="hidden">
                                <label class="block text-xs font-black mb-1 text-slate-700">سبب التأجيل بشكل دقيق</label>
                                <textarea name="postponement_reason" rows="2" placeholder="مثال: لغياب الخصم، لتقديم مذكرات..." class="w-full px-3 py-2 border-2 border-rose-200 bg-rose-50 rounded-xl text-sm font-medium focus:border-rose-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50/50 border border-blue-100 rounded-xl mt-4">
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-bold text-blue-800 mb-2"><i class="fas fa-forward text-blue-500 ml-1"></i> الخطوة القادمة</h4>
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">موعد الجلسة القادمة <span class="text-slate-400 font-medium">(إن تحدد)</span></label>
                            <input type="datetime-local" name="next_session_date" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">الهدف أو المطلوب للجلسة القادمة</label>
                            <input type="text" name="next_session_goal" placeholder="مثال: الرد على مذكرة الخصم" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-3 mt-4 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition-all">
                        <i class="fas fa-save ml-1"></i> حفظ الجلسة
                    </button>
                </form>
            </div>

            {{-- قائمة الجلسات --}}
            @forelse($case->sessions as $session)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="flex flex-wrap justify-between items-start gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $session->status == 'completed' ? 'bg-emerald-50 text-emerald-600' : ($session->status == 'postponed' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600') }}">
                                <i class="fas {{ $session->status == 'completed' ? 'fa-check-circle' : ($session->status == 'postponed' ? 'fa-pause-circle' : 'fa-calendar-check') }}"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-800">{{ $session->session_type }}</p>
                                <p class="text-xs text-slate-500">{{ $session->session_date->translatedFormat('l d F Y - h:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold px-2 py-1 rounded-lg {{ $session->status == 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($session->status == 'postponed' ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-blue-50 text-blue-600 border border-blue-200') }}">
                                {{ $session->status == 'completed' ? 'مكتملة' : ($session->status == 'postponed' ? 'مؤجلة' : 'مجدولة') }}
                            </span>
                            @if($session->location)
                                <span class="text-[10px] font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded-lg border border-slate-200"><i class="fas fa-map-marker-alt ml-1"></i>{{ $session->location }}</span>
                            @endif
                            <form action="{{ url('/lawyer/cases/'.$case->id.'/sessions/'.$session->id) }}" method="POST" onsubmit="return confirm('حذف هذه الجلسة؟')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-rose-400 hover:text-rose-600 text-xs"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </div>
                    @if($session->summary)
                        <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100 mb-3">{{ $session->summary }}</p>
                    @endif
                    @if($session->opponent_claims || $session->our_response)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        @if($session->opponent_claims)
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                            <p class="text-xs font-black text-slate-600 mb-1"><i class="fas fa-comment-dots ml-1 text-slate-400"></i> أقوال الخصم</p>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $session->opponent_claims }}</p>
                        </div>
                        @endif
                        @if($session->our_response)
                        <div class="p-3 rounded-xl border border-blue-100 bg-blue-50/30">
                            <p class="text-xs font-black text-blue-700 mb-1"><i class="fas fa-balance-scale ml-1 text-blue-500"></i> ردنا وموقف القاضي</p>
                            <p class="text-xs text-slate-700 leading-relaxed">{{ $session->our_response }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($session->postponement_reason)
                    <div class="bg-amber-50 p-3 rounded-xl border border-amber-200 text-xs text-amber-700 mb-3">
                        <span class="font-black"><i class="fas fa-exclamation-triangle ml-1"></i> سبب التأجيل:</span> {{ $session->postponement_reason }}
                    </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center text-slate-400 font-bold">
                    <i class="fas fa-calendar-times text-3xl mb-2 text-slate-300"></i>
                    <p>لا توجد جلسات مسجلة بعد</p>
                </div>
            @endforelse
        </div>

        {{-- ==================== تبويب التنبيهات ==================== --}}
        <div x-show="activeTab === 'updates'" x-cloak class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 cursor-pointer" onclick="document.getElementById('updateForm').classList.toggle('hidden')">
                    <h3 class="text-sm font-black text-slate-700 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i> إضافة تنبيه جديد</h3>
                </div>
                <form id="updateForm" action="{{ url('/lawyer/cases/'.$case->id.'/updates') }}" method="POST" class="hidden p-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">العنوان *</label>
                            <input type="text" name="title" required placeholder="عنوان التنبيه" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">النوع *</label>
                            <select name="update_type" required class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                                <option value="info">ℹ️ معلومة</option>
                                <option value="court_update">⚖️ تحديث المحكمة</option>
                                <option value="warning">⚠️ تنبيه</option>
                                <option value="urgent">🔴 عاجل</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-1 text-slate-700">المحتوى *</label>
                        <textarea name="content" rows="3" required placeholder="تفاصيل التنبيه..." class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-medium focus:border-blue-500 focus:outline-none"></textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
                        <input type="checkbox" name="is_pinned" value="1" class="rounded text-blue-600"> تثبيت في الأعلى
                    </label>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition-all">
                        <i class="fas fa-paper-plane ml-1"></i> إرسال التنبيه
                    </button>
                </form>
            </div>
            @forelse($case->updates as $update)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 {{ $update->is_pinned ? 'border-r-4 border-r-blue-500' : '' }}">
                    <div class="flex justify-between items-start gap-2">
                        <div class="flex items-center gap-2">
                            @if($update->update_type == 'urgent')
                                <span class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500"><i class="fas fa-exclamation-circle"></i></span>
                            @elseif($update->update_type == 'warning')
                                <span class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500"><i class="fas fa-exclamation-triangle"></i></span>
                            @elseif($update->update_type == 'court_update')
                                <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500"><i class="fas fa-gavel"></i></span>
                            @else
                                <span class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400"><i class="fas fa-info-circle"></i></span>
                            @endif
                            <div>
                                <p class="text-sm font-black text-slate-800">{{ $update->title }} @if($update->is_pinned)<i class="fas fa-thumbtack text-blue-500 text-xs mr-1"></i>@endif</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $update->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <form action="{{ url('/lawyer/cases/'.$case->id.'/updates/'.$update->id) }}" method="POST" onsubmit="return confirm('حذف؟')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-rose-400 hover:text-rose-600 text-xs"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $update->content }}</p>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center text-slate-400 font-bold">
                    <i class="fas fa-bell-slash text-3xl mb-2 text-slate-300"></i>
                    <p>لا توجد تنبيهات بعد</p>
                </div>
            @endforelse
        </div>

        {{-- ==================== تبويب المهام ==================== --}}
        <div x-show="activeTab === 'tasks'" x-cloak class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 cursor-pointer" onclick="document.getElementById('taskForm').classList.toggle('hidden')">
                    <h3 class="text-sm font-black text-slate-700 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i> إضافة مهمة جديدة للموكل</h3>
                </div>
                <form id="taskForm" action="{{ url('/lawyer/cases/'.$case->id.'/tasks') }}" method="POST" class="hidden p-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black mb-1 text-slate-700">عنوان المهمة *</label>
                            <input type="text" name="title" required placeholder="مثال: سداد رسوم الخبير" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">الأولوية *</label>
                            <select name="priority" required class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                                <option value="normal">عادية</option>
                                <option value="urgent">عاجلة</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">الوصف</label>
                            <textarea name="description" rows="2" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-medium focus:border-blue-500 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">الموعد النهائي</label>
                            <input type="date" name="due_date" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition-all">
                        <i class="fas fa-plus ml-1"></i> إضافة المهمة
                    </button>
                </form>
            </div>
            @forelse($case->tasks as $task)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex items-start gap-3 {{ $task->is_completed ? 'opacity-60' : '' }} {{ $task->priority == 'urgent' && !$task->is_completed ? 'border-r-4 border-r-red-400' : '' }}">
                    <div class="mt-1 {{ $task->is_completed ? 'text-emerald-500' : 'text-slate-300' }}">
                        <i class="fas {{ $task->is_completed ? 'fa-check-circle' : 'fa-circle' }} text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-slate-800 {{ $task->is_completed ? 'line-through' : '' }}">{{ $task->title }}</p>
                        @if($task->description)<p class="text-xs text-slate-500 mt-1">{{ $task->description }}</p>@endif
                        <div class="flex items-center gap-3 mt-2">
                            @if($task->due_date)
                                <span class="text-[10px] font-bold {{ $task->due_date->isPast() && !$task->is_completed ? 'text-red-600' : 'text-slate-500' }}">
                                    <i class="far fa-clock ml-1"></i> {{ $task->due_date->translatedFormat('d F Y') }}
                                </span>
                            @endif
                            @if($task->priority == 'urgent')
                                <span class="text-[10px] font-black text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-200">عاجل</span>
                            @endif
                            @if($task->is_completed)
                                <span class="text-[10px] font-bold text-emerald-600">✓ مكتملة</span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ url('/lawyer/cases/'.$case->id.'/tasks/'.$task->id) }}" method="POST" onsubmit="return confirm('حذف؟')" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-rose-400 hover:text-rose-600 text-xs"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center text-slate-400 font-bold">
                    <i class="fas fa-clipboard-list text-3xl mb-2 text-slate-300"></i>
                    <p>لا توجد مهام بعد</p>
                </div>
            @endforelse
        </div>

        {{-- ==================== تبويب المستندات ==================== --}}
        <div x-show="activeTab === 'documents'" x-cloak class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 cursor-pointer" onclick="document.getElementById('docForm').classList.toggle('hidden')">
                    <h3 class="text-sm font-black text-slate-700 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-500"></i> رفع مستند جديد</h3>
                </div>
                <form id="docForm" action="{{ url('/lawyer/cases/'.$case->id.'/documents') }}" method="POST" enctype="multipart/form-data" class="hidden p-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">اسم المستند</label>
                            <input type="text" name="document_name" placeholder="مثال: محضر جلسة 5 مايو" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black mb-1 text-slate-700">نوع المستند</label>
                            <select name="document_type" class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                                <option value="">— اختر —</option>
                                <option value="محضر جلسة">محضر جلسة</option>
                                <option value="مذكرة قانونية">مذكرة قانونية</option>
                                <option value="فاتورة">فاتورة</option>
                                <option value="عقد">عقد</option>
                                <option value="حكم">حكم</option>
                                <option value="تقرير خبير">تقرير خبير</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-1 text-slate-700">الملف *</label>
                        <input type="file" name="document_file" required class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-black mb-1 text-slate-700">وصف</label>
                        <input type="text" name="description" placeholder="وصف مختصر..." class="w-full px-3 py-2 border-2 border-slate-200 rounded-xl text-sm font-bold focus:border-blue-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition-all">
                        <i class="fas fa-upload ml-1"></i> رفع المستند
                    </button>
                </form>
            </div>
            @forelse($case->caseDocuments as $doc)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center border border-slate-200 text-red-500 shrink-0">
                            <i class="fas fa-file-pdf text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800">{{ $doc->document_name ?? basename($doc->file_path) }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($doc->document_type)<span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $doc->document_type }}</span>@endif
                                <span class="text-[10px] text-slate-400">{{ $doc->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700"><i class="fas fa-eye"></i></a>
                        <form action="{{ url('/lawyer/cases/'.$case->id.'/documents/'.$doc->id) }}" method="POST" onsubmit="return confirm('حذف؟')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-rose-400 hover:text-rose-600"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center text-slate-400 font-bold">
                    <i class="fas fa-file-upload text-3xl mb-2 text-slate-300"></i>
                    <p>لا توجد مستندات بعد</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

<script>
    function toggleSessionFields() {
        const status = document.getElementById('sessionStatus').value;
        const pastFields = document.getElementById('pastSessionFields');
        const completedFields = document.getElementById('completedOnlyFields');
        const postponedFields = document.getElementById('postponedOnlyFields');

        if (status === 'scheduled') {
            pastFields.classList.add('hidden');
        } else {
            pastFields.classList.remove('hidden');
            
            if (status === 'completed') {
                completedFields.classList.remove('hidden');
                postponedFields.classList.add('hidden');
            } else if (status === 'postponed') {
                completedFields.classList.add('hidden');
                postponedFields.classList.remove('hidden');
            }
        }
    }
    
    // Call once on load to set initial state
    document.addEventListener('DOMContentLoaded', toggleSessionFields);
</script>
@endsection
