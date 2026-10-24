@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap');

    body {
        font-family: 'Cairo', sans-serif !important;
      background-color: #f4f8fd !important;
        color: #111827 !important;
        min-height: 100vh;
    }

    /* ==========================================================================
       تنسيق شبكة المحامين
       ========================================================================== */
    .lawyers-grid { 
        padding: 0 0 60px 0;
        max-width: 1440px; 
        margin: 0 auto; 
    }

    .cards-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr); 
        gap: 22px; 
    }
    
    @media (max-width: 1280px) { 
        .cards-container { grid-template-columns: repeat(3, 1fr); gap: 20px; } 
    }
    @media (max-width: 900px) { 
        .cards-container { grid-template-columns: repeat(2, 1fr); gap: 18px; } 
    }
    @media (max-width: 600px) { 
        .cards-container { 
            grid-template-columns: 1fr !important; /* بطاقة واحدة بالعرض الكامل */
            gap: 12px !important; 
            padding: 0 4px !important;
        } 
        
        .lawyer-card {
            border-radius: 16px !important;
            padding: 12px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03) !important;
            display: flex !important;
            flex-direction: column !important;
            margin-bottom: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .lawyer-card::before { display: none !important; }
        .card-banner-img { display: none !important; }
        
        .card-body { 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: row !important; /* ترتيب أفقي */
            align-items: center !important;
            width: 100% !important;
            gap: 12px !important;
            box-sizing: border-box !important;
        }
        
        .avatar-wrapper { 
            width: 55px !important; 
            height: 55px !important; 
            margin: 0 !important; 
            border-radius: 50% !important;
            border: 2px solid #f8fafc !important;
            box-shadow: none !important;
            flex-shrink: 0 !important;
        }
        
        .name-col { 
            margin: 0 !important;
            flex-grow: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            overflow: hidden !important;
        }
        
        .name-text { 
            font-size: 15px !important; 
            margin-bottom: 4px !important; 
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            width: 100% !important;
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        
        .verified-badge { font-size: 11px !important; flex-shrink: 0 !important; }
        
        .specialization-tag { 
            font-size: 10px !important; 
            padding: 2px 8px !important; 
            border-radius: 20px !important;
            margin: 0 !important;
            display: inline-block !important;
            background: #eff6ff !important;
            color: #0061ff !important;
            white-space: nowrap !important;
        }
        
        /* إخفاء التفاصيل الزائدة */
        .bio-text { display: none !important; }
        .stats-row { display: none !important; }
        
        .footer-info { 
            display: block !important; 
            padding: 0 !important;
            margin: 0 !important;
            flex-shrink: 0 !important;
        }
        .footer-info .info-item { display: none !important; }
        
        .btn-wrapper { 
            margin: 0 !important;
            display: block !important;
        }
        
        .btn-primary-action { 
            display: inline-flex !important;
            padding: 8px 16px !important; 
            font-size: 12px !important; 
            border-radius: 12px !important; 
            background: #0061ff !important;
            color: #ffffff !important;
            white-space: nowrap !important;
            font-weight: 700 !important;
            align-items: center !important;
            justify-content: center !important;
        }
    }

    /* ==========================================================================
       شريط البحث - أطول ومضيء باللون الأزرق
       ========================================================================== */
    .filter-section {
        background: #ffffff;
        padding: 12px 18px;
        border-radius: 60px;
        box-shadow: 0 0 15px rgba(0, 97, 255, 0.25);
        border: 1px solid #0061ff;
        margin-bottom: 32px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        max-width: 1050px; /* تم زيادة طول الشريط هنا */
        margin-left: auto;
        margin-right: auto;
        transition: all 0.3s ease;
    }
    
    .filter-section:hover {
        box-shadow: 0 0 22px rgba(0, 97, 255, 0.35);
    }
    
    @media (max-width: 768px) {
        .lawyers-grid { padding: 12px 12px 80px 12px !important; }
        
        .filter-section {
            display: block !important; /* فرض Block بقوة لمنع الـ Flex */
            border-radius: 16px !important;
            padding: 16px !important;
            margin-bottom: 20px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
            background: #ffffff !important;
            height: auto !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .filter-input, .filter-select {
            display: block !important;
            width: 100% !important;
            height: 46px !important;
            margin-bottom: 10px !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            background: #f8fafc !important;
            padding: 0 16px !important;
            font-size: 14px !important;
            box-sizing: border-box !important;
            flex: none !important; /* إلغاء تمدد الـ Flexbox */
        }
        
        .btn-search {
            display: block !important;
            width: 100% !important;
            border-radius: 12px !important;
            height: 46px !important;
            margin-top: 4px !important;
            background: #0061ff !important;
            color: #ffffff !important;
            font-size: 15px !important;
            font-weight: bold !important;
            text-align: center !important;
            border: none !important;
            box-sizing: border-box !important;
            flex: none !important;
        }
        .btn-search::after {
            content: "البحث" !important;
            font-family: 'Cairo', sans-serif !important;
            margin-right: 6px !important;
        }
        .btn-search i { display: inline-block !important; }
    }

    .filter-input, .filter-select {
        flex: 1;
        min-width: 160px;
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 500;
        font-family: 'Cairo', sans-serif;
        border: 1px solid #e2e8f0;
        border-radius: 40px;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
        transition: all 0.2s ease;
    }

    .filter-input:focus, .filter-select:focus {
        border-color: #0061ff;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(0, 97, 255, 0.08);
    }

    /* زر البحث الجديد (أيقونة فقط) */
    .btn-search {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0061ff, #0052d6);
        color: #ffffff;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .btn-search:hover {
        background: linear-gradient(135deg, #0052d6, #0045b8);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 97, 255, 0.3);
    }

    /* ==========================================================================
       حالة عدم وجود نتائج (Empty State)
       ========================================================================== */
    .empty-state {
        grid-column: 1 / -1;
        background: #ffffff;
        border-radius: 28px;
        padding: 60px 20px;
        text-align: center;
        border: 1px dashed #cbd5e1;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }
    
    .empty-state i {
        font-size: 50px;
        color: #94a3b8;
        margin-bottom: 16px;
    }
    
    .empty-state h3 {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 24px;
    }
    
    .btn-reset {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: #f1f5f9;
        color: #334155;
        border-radius: 40px;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    
    .btn-reset:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* ==========================================================================
       تصميم الكرت 
       ========================================================================== */
    .lawyer-card {
        background: #ffffff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
        position: relative;
    }
    
    .lawyer-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #0061ff, #00c6ff, #0061ff);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lawyer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 45px -14px rgba(0, 97, 255, 0.15);
        border-color: rgba(0, 97, 255, 0.15);
    }
    
    .lawyer-card:hover::before {
        opacity: 1;
    }

    .card-banner-img { 
        height: 85px; 
        width: 100%; 
        object-fit: cover;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        transition: transform 0.5s ease;
    }
    .lawyer-card:hover .card-banner-img {
        transform: scale(1.02);
    }

    .card-body {
        padding: 0 18px 18px 18px;
        position: relative;
    }

    .avatar-wrapper {
        width: 95px; 
        height: 95px; 
        border-radius: 50%; 
        border: 4px solid #ffffff; 
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        margin-top: -48px; 
        margin-bottom: 12px;
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
        overflow: hidden;
        position: relative;
        z-index: 2;
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.12);
        transition: all 0.3s ease;
    }
    .lawyer-card:hover .avatar-wrapper {
        transform: scale(1.02);
        box-shadow: 0 15px 30px -12px rgba(0, 97, 255, 0.2);
    }
    .avatar-wrapper img {
        width: 100%; 
        height: 100%; 
        object-fit: cover;
    }
    .avatar-wrapper i {
        font-size: 42px;
        color: #94a3b8;
    }

    .name-col { margin-bottom: 10px; }

    .name-text { 
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 17px; 
        font-weight: 800; 
        color: #0f172a; 
        margin: 0 0 5px 0;
        line-height: 1.3;
        letter-spacing: -0.3px;
    }

    .verified-badge {
        color: #0061ff;
        font-size: 14px;
        flex-shrink: 0;
    }

    .specialization-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: linear-gradient(135deg, rgba(0, 97, 255, 0.08), rgba(0, 97, 255, 0.03));
        color: #0061ff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 40px;
        border: 1px solid rgba(0, 97, 255, 0.1);
    }

    .bio-text {
        font-size: 12px;
        color: #64748b; 
        line-height: 1.55;
        margin: 12px 0 14px;
        min-height: 38px; 
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .stats-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding: 10px 0;
        border-top: 1px solid #f0f2f5;
        border-bottom: 1px solid #f0f2f5;
    }
    .experience-stat {
        display: flex;
        align-items: center;
        gap: 5px;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 40px;
    }
    .experience-stat i { color: #0061ff; font-size: 12px; }
    .experience-stat b { color: #0f172a; font-weight: 800; font-size: 14px; }
    .experience-stat span { font-size: 11px; color: #64748b; font-weight: 500; }

    .stars-container {
        display: flex;
        gap: 2px;
        align-items: center;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 40px;
    }
    .stars-container i { font-size: 10px; }
    .stars-container .star-filled { color: #fbbf24; }
    .stars-container .star-empty { color: #e2e8f0; }
    .rating-number { margin-left: 4px; font-size: 11px; font-weight: 700; color: #0f172a; }

    .btn-wrapper { margin: 12px 0 8px; }
    .btn-primary-action {
        background: linear-gradient(135deg, #0061ff 0%, #0052d6 100%);
        color: #ffffff; 
        padding: 10px 16px; 
        font-size: 12px; 
        border-radius: 60px; 
        text-decoration: none; 
        font-weight: 700;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        box-shadow: 0 4px 12px rgba(0, 97, 255, 0.2);
    }
    .btn-primary-action:hover {
        background: linear-gradient(135deg, #0052d6 0%, #0045b8 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -6px rgba(0, 97, 255, 0.4);
    }

    .footer-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding-top: 6px;
    }
    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: #64748b;
        transition: all 0.2s;
        padding: 3px 0;
    }
    .info-item:hover { transform: translateX(3px); color: #0061ff; }
    .info-item i { font-size: 12px; color: #0061ff; width: 18px; text-align: center; }
    .info-item span { font-weight: 500; }
</style>

<div class="lawyers-grid" style="display: block; max-width: 1440px; margin: 0 auto;">
    
    {{-- شريط البحث --}}
    <form action="{{ route('client.lawyers.index') }}" method="GET" class="filter-section" autocomplete="off">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المحامي أو تخصصه..." class="filter-input">
        
        {{-- قائمة التخصصات الديناميكية المعدلة --}}
<div class="relative group">
    <button type="button" id="dropdownButton" class="filter-select w-64 flex justify-between items-center bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700">
        <span id="selectedSpecialtyText">
            @php
                $selected = $allSpecialties->firstWhere('id', request('specialty_id'));
            @endphp
            {{ $selected ? $selected->name : 'كل التخصصات' }}
        </span>
        <i class="fas fa-chevron-down text-xs"></i>
    </button>

    <div id="dropdownMenu" class="hidden absolute top-full right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-80 overflow-y-auto">
        <div class="p-2">
            <div onclick="selectSpecialty('all', 'كل التخصصات')" class="px-4 py-2 hover:bg-blue-50 cursor-pointer rounded-lg text-sm text-gray-700">كل التخصصات</div>
            @foreach($allSpecialties as $specialty)
                <div onclick="selectSpecialty('{{ $specialty->id }}', '{{ $specialty->name }}')" class="px-4 py-2 hover:bg-blue-50 cursor-pointer rounded-lg text-sm text-gray-700">
                    {{ $specialty->name }}
                </div>
            @endforeach
        </div>
    </div>
    <input type="hidden" name="specialty_id" id="specialty_id_input" value="{{ request('specialty_id', 'all') }}">
</div>

        <select name="sort" class="filter-select">
            <option value="all" {{ request('sort') == 'all' || !request('sort') ? 'selected' : '' }}>الترتيب الافتراضي</option>
            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>الأعلى تقييماً</option>
            <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>الأكثر خبرة</option>
        </select>

        <button type="submit" class="btn-search" title="بحث">
            <i class="fas fa-search"></i>
        </button>
    </form>

    {{-- شبكة الكروت مع حالة "لا يوجد نتائج" --}}
    <div class="cards-container">
        @forelse($lawyers as $lawyer)
            <div class="lawyer-card">
               <img src="{{ asset('images/photo_5828207686976212863_y.jpg') }}" class="card-banner-img" alt="banner">
                
                <div class="card-body">
                    <div class="avatar-wrapper">
                        @if($lawyer->avatar)
                            <img src="{{ asset('uploads/avatars/' . $lawyer->avatar) }}" alt="{{ $lawyer->name }}">
                        @elseif($lawyer->lawyerProfile && $lawyer->lawyerProfile->avatar)
                            <img src="{{ asset('uploads/avatars/' . $lawyer->lawyerProfile->avatar) }}" alt="{{ $lawyer->name }}">
                        @else
                            <i class="fas fa-user-tie"></i>
                        @endif
                    </div>

                    <div class="name-col">
                        <h2 class="name-text">
                            {{ $lawyer->name }}
                            <i class="fas fa-check-circle verified-badge" title="حساب موثق"></i>
                        </h2>
                        
                        {{-- عرض التخصص الديناميكي --}}
                        <div class="specialization-tag">
                            <i class="fas fa-gavel" style="font-size: 9px;"></i>
                            {{ $lawyer->lawyerProfile->specialty->name ?? 'محامي عام' }}
                        </div>
                    </div>

                    <p class="bio-text">
                        {{ Str::limit($lawyer->lawyerProfile->bio ?? 'لم يتم إضافة نبذة شخصية بعد.', 70, '...') }}
                    </p>

                    <div class="stats-row">
                        <div class="experience-stat">
                            <i class="fas fa-briefcase"></i>
                            <b>{{ $lawyer->lawyerProfile->years_of_experience ?? 5 }}</b>
                            <span>سنوات</span>
                        </div>
                        
                        <div class="stars-container">
                            @php
                                $rating = round($lawyer->lawyerProfile->rating ?? 5); 
                            @endphp
                            
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating)
                                    <i class="fas fa-star star-filled"></i>
                                @else
                                    <i class="fas fa-star star-empty"></i>
                                @endif
                            @endfor
                            <span class="rating-number">{{ number_format($lawyer->lawyerProfile->rating ?? 5, 1) }}</span>
                        </div>
                    </div>

                    <div class="footer-info">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $lawyer->lawyerProfile->location ?? 'طرابلس، ليبيا' }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone-alt"></i>
                            <span>{{ $lawyer->phone ?? '09XXX XXX' }}</span>
                        </div>
                        
                        <div class="btn-wrapper">
                            <a href="{{ route('client.lawyers.show', $lawyer->id) }}" class="btn-primary-action">
                                <i class="fas fa-user-circle"></i> عرض الملف الشخصي
                                <i class="fas fa-arrow-left" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-search-minus"></i>
                <h3>عذراً، لم نتمكن من العثور على أي محامي يطابق بحثك!</h3>
                <p>تأكد من الكلمات المدخلة أو جرب اختيار تخصص مختلف.</p>
                <a href="{{ route('client.lawyers.index') }}" class="btn-reset">
                    <i class="fas fa-sync-alt"></i> إلغاء الفلاتر وعرض الكل
                </a>
            </div>
        @endforelse
    </div>
</div>

 <script>
 // سكريبت اعادة ضبط الحقول لوضعها الافتراضي تلقائياً عند تحديث الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.search !== "") {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
<script>
    // تفعيل السكرول وفتح القائمة
    document.getElementById('dropdownButton').onclick = () => {
        document.getElementById('dropdownMenu').classList.toggle('hidden');
    };

    function selectSpecialty(id, name) {
        document.getElementById('specialty_id_input').value = id;
        document.getElementById('selectedSpecialtyText').innerText = name;
        document.getElementById('dropdownMenu').classList.add('hidden');
    }
</script>
@endsection