@props(['btnClass' => 'bg-white/5 border-white/10 text-gray-300 hover:bg-white/10 hover:text-white'])

@php
    $userSettings = auth()->check() ? (auth()->user()->notification_settings ?? []) : [];

    // 1. المفتاح الرئيسي: إذا كان مغلقاً (false)، النتيجة دائماً فارغة
    $isGeneralEnabled = $userSettings['app_notifications'] ?? true;

    // 2. مصفوفة الكلمات المفتاحية
    $keywords = [
        'new_consultation' => ['طلب استشارة', 'طلب استشارة جديد', 'قبول طلب الاستشارة', 'رفض طلب الاستشارة'],
        'new_bookings'     => ['طلب حجز موعد', 'قبول طلب الحجز', 'رفض طلب الحجز'],
        'new_messages'     => ['رسالة', 'محادثة'],
        'new_verification' => ['طلب توثيق جديد'],
        'consultation_end' => ['إنهاء الاستشارة'],
        'new_dispute'      => ['بلاغ جديد', 'بلاغ جديد من عميل'],
        'payment_process'  => ['تمت عملية الدفع بنجاح', 'تم استلام الدفعة'],
    ];
    
    $notifications = collect();

    if ($isGeneralEnabled) {
        $notifications = auth()->check() 
            ? DB::table('notifications')
                ->where('user_id', auth()->id())
                ->where(function($query) use ($userSettings, $keywords) {
                    $query->where(function($subQuery) use ($userSettings, $keywords) {
                        // استخدام 1=1 بدلاً من 1=0 لتجنب مشاكل الـ Query التراكمي
                        $subQuery->whereRaw('1=1');
                        
                        foreach ($keywords as $settingKey => $words) {
                            if ($userSettings[$settingKey] ?? true) {
                                foreach ($words as $word) {
                                    $subQuery->orWhere('title', 'like', '%' . $word . '%')
                                             ->orWhere('body', 'like', '%' . $word . '%');
                                }
                            }
                        }
                    });
                })
                ->latest()
                ->get() 
            : collect();
    }
    
    $unreadCount = $notifications->where('is_read', 0)->count();
@endphp

{{-- بقية الكود كما هو تماماً دون أي تغيير --}}
<div class="relative" x-data="{ 
    open: false, 
    hasUnread: {{ $unreadCount > 0 ? 'true' : 'false' }},
    markAsRead() {
        if (this.hasUnread) {
            this.hasUnread = false; 
            fetch('{{ route('notifications.markAsRead') }}', { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
            });
        }
    }
}" @click.outside="open = false">

    <button @click.stop="open = !open; if(open) markAsRead()" class="relative w-10 h-10 rounded-full flex items-center justify-center transition-all {{ $btnClass }}">
        <i class="far fa-bell text-lg"></i>
        <span x-cloak x-show="hasUnread" class="absolute !top-1.5 !right-1.5 !left-auto !bottom-auto w-2.5 h-2.5 bg-blue-500 rounded-full animate-pulse z-[9999] border-2 border-slate-700 shadow-sm"></span>
    </button>

    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute left-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-[9999]">
        
        <div class="px-4 py-3 font-bold text-sm text-slate-800 border-b border-slate-50">التنبيهات</div>
        
        <div style="max-height: 300px !important;" class="overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 text-xs border-b border-slate-50 last:border-none relative group hover:bg-slate-50 transition-colors"
                     x-data="{ time: '{{ \Carbon\Carbon::parse($notification->created_at)->timestamp }}', formatted: '' }"
                     x-init="
                        let update = () => { 
                            let diff = Math.floor((new Date().getTime() / 1000) - time);
                            if (diff < 60) formatted = 'منذ ثوانٍ';
                            else if (diff < 3600) formatted = 'منذ ' + Math.floor(diff/60) + ' دقيقة';
                            else formatted = 'منذ ' + Math.floor(diff/3600) + ' ساعة';
                        };
                        update();
                        setInterval(update, 60000);
                     ">
                     
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" 
                          class="absolute left-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 group-hover:text-red-600 transition-all duration-200 hover:scale-125 p-1">
                            <i class="fas fa-times text-[14px]"></i>
                        </button>
                    </form>

                    <div class="flex justify-between items-start">
                        <p class="font-bold text-slate-900">{{ $notification->title }}</p>
                        <span class="text-[9px] text-slate-400 whitespace-nowrap ml-6" x-text="formatted"></span>
                    </div>
                    <p class="text-slate-500 mt-1 leading-relaxed">{{ $notification->body }}</p>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-xs text-slate-400">لا توجد تنبيهات</p>
            @endforelse
        </div>
    </div>
</div>