@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center mb-6">
        <a href="{{ url('/admin/finance') }}" class="text-slate-500 hover:text-blue-700 transition mr-4 bg-white p-2 rounded-full border border-slate-200 shadow-sm">
    <i class="fas fa-arrow-right"></i>
</a>
        <h2 class="text-2xl font-bold text-black flex items-center">
            <i class="fas fa-comments mr-2 text-blue-700"></i> سجل محادثة النزاع
        </h2>
    </div>
    
    <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-200 h-[500px] overflow-y-auto">
        @forelse($messages as $message)
            <div class="text-center my-4">
                <span class="text-[10px] text-slate-500 bg-slate-100 px-3 py-1 rounded-full uppercase tracking-widest">
                    {{ $message->created_at->translatedFormat('l, Y/m/d') }}
                </span>
            </div>

            <!-- تم تغيير لون المحامي إلى blue-50 و border-blue-100 -->
            <div class="mb-4 p-3 rounded-lg border {{ $message->sender_id == $chatSession->user_id ? 'bg-sky-50 ml-10 border-sky-100' : 'bg-blue-50 mr-10 border-blue-100' }}">
                <div class="flex justify-between items-center mb-1">
                    <p class="text-xs font-bold text-slate-700">
                        {{ $message->sender_id == $chatSession->user_id ? 'العميل:' : 'المحامي:' }} 
                        {{ $message->sender->name ?? 'غير معروف' }}
                    </p>
                    <span class="text-[10px] text-slate-400 bg-white px-2 py-0.5 rounded border border-slate-200">
                        {{ str_replace(['AM', 'PM'], ['ص', 'م'], $message->created_at->format('h:i A')) }}
                    </span>
                </div>
                
                <p class="text-sm text-slate-800 mt-1">{{ $message->message_text }}</p>
                
                @if($message->hasAttachment())
                    <div class="mt-2">
                        <a href="{{ $message->attachment_url }}" target="_blank" class="text-xs text-sky-600 underline flex items-center">
                            <i class="fas fa-paperclip mr-1"></i> تحميل المرفق ({{ $message->formatted_attachment_size }})
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-center text-slate-400 py-20">لا توجد رسائل في هذه المحادثة.</p>
        @endforelse
    </div>
</div>
@endsection