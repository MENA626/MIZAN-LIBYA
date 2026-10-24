{{-- resources/views/shared/ai-assistant.blade.php --}}
@php
    $layout = 'layouts.app'; 
    if (Request::is('lawyer*')) {
        $layout = 'layouts.lawyer'; 
    } elseif (Request::is('admin*')) {
        $layout = 'layouts.admin'; 
    }
@endphp

@extends($layout)

@section('title', 'مساعد ميزان الذكي')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');

        :root {
            --navbar-height: 80px;    

            --primary: #2563eb;       
            --primary-hover: #1d4ed8;  
            --text-dark: #0f172a;     
            --text-muted: #64748b;
            --bg-outer: #f1f5f9;      
            --bg-card: #ffffff;       
            --border-color: #e2e8f0;  
        }

        /* Lock parent to prevent page scroll */
        html, body {
            overflow: hidden !important;
            background-color: var(--bg-outer) !important;
            font-family: 'Cairo', sans-serif;
            margin: 0; padding: 0;
        }
        
        @if($layout === 'layouts.app')
        main.page-content,
        main.flex-1 {
            overflow: hidden !important;
            padding: 0 !important; 
            margin: 0 !important;
            background-color: var(--bg-outer) !important;
            height: calc(100vh - var(--navbar-height)) !important;
        }
        @else
        .page-content {
            height: calc(100vh - 110px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        @endif

        * { box-sizing: border-box; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* =========================================
           1. LANDING VIEW (شاشة البداية)
           ========================================= */
        #landingView {
            display: flex;
            align-items: center;
            justify-content: space-between;
            @if($layout === 'layouts.app')
            height: calc(100vh - var(--navbar-height));
            @else
            height: 100%;
            @endif
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 4%;
            position: relative;
            z-index: 10;
        }

        .landing-content {
            flex: 0.9; max-width: 600px; z-index: 5; transform: translateY(-30px);
        }

        .badge-pill {
            background: #eff6ff; color: var(--primary); padding: 8px 16px; border-radius: 9999px;
            font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;
            margin-bottom: 24px; border: 1px solid #bfdbfe;
        }

        .landing-content h1 {
            font-size: 60px; font-weight: 900; color: var(--text-dark); line-height: 1.15;
            margin: 0 0 24px 0; letter-spacing: -1.5px;
        }

        .landing-content p {
            font-size: 18px; color: var(--text-muted); line-height: 1.8; margin: 0 0 40px 0; font-weight: 600;
        }

        .start-action-btn {
            background: var(--primary); color: #ffffff; border: none; padding: 18px 40px;
            border-radius: 16px; font-size: 18px; font-weight: 700; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3); display: inline-flex; align-items: center; gap: 12px;
            font-family: 'Cairo', sans-serif;
        }
        .start-action-btn:hover { background: var(--primary-hover); transform: translateY(-3px); box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4); }

        .landing-visuals {
            flex: 1.3; position: relative; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 1;
        }

        .glow-blob { position: absolute; border-radius: 50%; filter: blur(130px); z-index: 0; opacity: 0.55; pointer-events: none; }
        .glow-1 { width: 700px; height: 700px; background: #2563eb; top: -5%; right: -5%; }
        .glow-2 { width: 600px; height: 600px; background: #60a5fa; bottom: -5%; left: 5%; }
        .glow-3 { width: 500px; height: 500px; background: #818cf8; top: 35%; left: 20%; opacity: 0.4; } 
        .glow-4 { width: 900px; height: 900px; background: rgba(59, 130, 246, 0.25); top: 50%; left: 50%; transform: translate(-50%, -50%); filter: blur(160px); }

        .floating-bubble {
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            padding: 14px 20px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            position: absolute; font-size: 13px; font-weight: 600; color: var(--text-dark);
            border: 1px solid rgba(255, 255, 255, 1); display: flex; flex-direction: column; gap: 6px;
            animation: float 6s ease-in-out infinite; line-height: 1.6;
        }

        .floating-bubble .sender { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); font-weight: 700; }
        .floating-bubble .sender.ai-sender { color: var(--primary); }
        .floating-bubble .code-block { background: #f1f5f9; padding: 12px; border-radius: 12px; font-size: 12px; color: #0ea5e9; font-family: Tahoma, sans-serif; line-height: 1.5; margin-top: 4px; border: 1px solid #e2e8f0;}

        .bubble-1 { top: 18%; left: 20%; animation-delay: 0s; max-width: 280px; z-index: 2; border-radius: 20px 20px 20px 4px; }
        .bubble-2 { top: 22%; right: 8%; animation-delay: 1.5s; max-width: 310px; z-index: 1; border-radius: 20px 20px 4px 20px; }
        .bubble-3 { top: 42%; left: 2%; animation-delay: 3s; max-width: 270px; z-index: 3; border-radius: 20px 20px 20px 4px; }
        .bubble-4 { top: 38%; left: 30%; animation-delay: 0.8s; max-width: 380px; z-index: 5; box-shadow: 0 25px 50px rgba(0,0,0,0.12); border-radius: 20px; padding: 18px 24px; }
        .bubble-5 { top: 58%; right: 4%; animation-delay: 2.2s; max-width: 300px; z-index: 2; border-radius: 20px 20px 4px 20px; }
        .bubble-6 { top: 68%; left: 16%; animation-delay: 1.2s; max-width: 330px; z-index: 4; border-radius: 20px 20px 20px 4px; }
        .bubble-7 { top: 74%; right: 28%; animation-delay: 4s; max-width: 290px; z-index: 3; background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 20px 40px rgba(37,99,235,0.25); border-radius: 20px 20px 4px 20px; }
        .bubble-7 .sender { color: rgba(255,255,255,0.8); }

        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }

        /* =========================================
           2. CHAT INTERFACE
           ========================================= */
        #chatbotContainer {
            @if($layout === 'layouts.app')
            height: calc(100vh - var(--navbar-height));
            padding: 0px 16px 16px 16px;
            @else
            height: 100%;
            border-radius: 20px;
            padding: 0; /* Remove extra padding for lawyer/admin layout */
            @endif
            display: flex; gap: 16px; width: 100%; 
            background: var(--bg-outer); 
            opacity: 0; transition: opacity 0.5s ease;
            max-width: 100%; margin: 0;
            direction: rtl; 
        }

        /* الجانب الأيمن: القائمة والأزرار */
        .assistant-sidebar {
            width: 340px; 
            display: flex; flex-direction: column; gap: 16px;
            z-index: 5; height: 100%;
        }

        /* بطاقة الأرشيف */
        .history-card {
            background: var(--bg-card);
            border-radius: 24px;
            padding: 20px;
            flex: 1; 
            display: flex; flex-direction: column;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .sidebar-header { margin-bottom: 20px; }
        .sidebar-header h3 { font-size: 18px; font-weight: 800; color: var(--text-dark); margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;}
        
        .search-wrapper { position: relative; width: 100%; }
        .search-input {
            width: 100%; background: var(--bg-outer); border: 1px solid transparent;
            padding: 12px 40px 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; outline: none; transition: all 0.2s;
        }
        .search-input:focus { border-color: var(--primary); background: #ffffff; box-shadow: 0 0 0 4px #eff6ff; }
        .search-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }

        .conversations-list { flex: 1; overflow-y: auto; padding-right: 4px; display: flex; flex-direction: column; gap: 10px; }
        
        .conv-item {
            display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; 
            border-radius: 14px; background: var(--bg-card);
            cursor: pointer; transition: all 0.2s ease; 
            border: 1px solid var(--border-color);
        }
        .conv-item:hover { border-color: #cbd5e1; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .conv-item.active { background: #eff6ff; border-color: #bfdbfe; box-shadow: 0 2px 10px rgba(37, 99, 235, 0.05); }
        .conv-item.active .conv-title { color: var(--primary); }
        
        .conv-icon { color: var(--text-muted); font-size: 16px; margin-top: 2px; }
        .conv-item.active .conv-icon { color: var(--primary); }
        
        .conv-content-inner { flex: 1; overflow: hidden; }
        .conv-title { font-weight: 700; font-size: 13px; color: var(--text-dark); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0; }
        .conv-date { font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 600; }

        .conv-delete {
            background: transparent; color: #cbd5e1; width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s; flex-shrink: 0;
        }
        .conv-delete:hover { color: #ef4444; background: #fee2e2; }

        /* بطاقة إضافة استشارة */
        .new-chat-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover)); 
            border-radius: 24px; padding: 20px; color: #ffffff; cursor: pointer; 
            transition: all 0.3s ease; display: flex; flex-direction: column; gap: 12px;
            box-shadow: 0 10px 30px rgba(37,99,235,0.2);
            border: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            margin-bottom: 40px; 
        }
        .new-chat-card:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(37,99,235,0.3); }
        .new-chat-card-header { display: flex; align-items: center; gap: 12px; }
        .new-chat-card-icon { width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .new-chat-card h4 { margin: 0; font-size: 18px; font-weight: 800; }
        .new-chat-card p { margin: 0; font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9); line-height: 1.5; }

        /* الجانب الأيسر: منطقة الشات الأساسية */
        .chat-main { 
            flex: 1; display: flex; flex-direction: column; overflow: hidden; 
            background: var(--bg-card); 
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid var(--border-color);
            height: 100%;
        }

        .chat-header { 
            padding: 15px 30px; align-items: center; display: flex; justify-content: space-between; 
            border-bottom: 1px solid var(--border-color);
        }
        
        .header-title.disclaimer-text { 
            display: inline-flex; align-items: center; gap: 10px; 
            font-weight: 700; font-size: 13px; color: #ea580c; 
            background: #fff7ed; 
            padding: 8px 18px; border-radius: 12px;
            border: 1px solid #ffedd5; 
            font-family: 'Cairo', sans-serif;
            letter-spacing: 0.2px;
        }
        .header-title.disclaimer-text i { color: #f97316; font-size: 16px; }

        #dynamicZone {
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden;
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 1;
            transform: translateY(0);
        }
        #dynamicZone.fade-out {
            opacity: 0;
            transform: translateY(15px);
        }

        .messages-wrapper {
            flex: 1; overflow-y: auto !important; padding: 30px; display: flex; flex-direction: column; gap: 24px; scroll-behavior: smooth;
        }

        .msg-row { display: flex; flex-direction: column; width: 100%; gap: 6px; }
        
        .msg-user {
            align-self: flex-start; 
            background: var(--primary); 
            color: #ffffff; 
            padding: 16px 20px; 
            border-radius: 20px 20px 4px 20px; 
            max-width: 75%; 
            font-size: 15px; 
            font-weight: 600; 
            line-height: 1.8; 
            border: none; 
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2); 
        }
        
        .msg-ai {
            align-self: flex-end; background: #f8fafc; color: var(--text-dark); 
            padding: 16px 20px; border-radius: 20px 20px 20px 4px; max-width: 85%; width: fit-content; font-size: 15px; font-weight: 600; line-height: 1.8;
            border: 1px solid var(--border-color);
        }
        
        .msg-ai .sender-badge {
            display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: var(--primary); margin-bottom: 8px;
        }
        
        .msg-time {
            font-size: 11px; font-weight: 600; opacity: 0.7; margin-top: 8px;
            display: flex; align-items: center; gap: 4px;
        }

        .source-link {
            display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; background: var(--bg-card); color: var(--text-dark); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; transition: all 0.2s;
        }
        .source-link:hover { color: var(--primary); border-color: var(--primary); }

        /* القائمة المنسدلة للمراجع القانونية */
        .sources-dropdown-wrapper {
            position: relative;
            display: inline-block;
            margin-top: 12px;
        }
        .sources-dropdown-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #eff6ff, #f0f9ff);
            color: var(--primary); border: 1px solid #bfdbfe;
            padding: 10px 18px; border-radius: 12px;
            font-size: 13px; font-weight: 700;
            cursor: pointer; transition: all 0.25s ease;
            font-family: 'Cairo', sans-serif;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
        }
        .sources-dropdown-btn:hover {
            background: linear-gradient(135deg, #dbeafe, #e0f2fe);
            border-color: #93c5fd;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            transform: translateY(-1px);
        }
        .sources-dropdown-btn .chevron-icon {
            transition: transform 0.25s ease;
            font-size: 10px;
        }
        .sources-dropdown-btn.open .chevron-icon {
            transform: rotate(180deg);
        }
        .sources-dropdown-menu {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            right: 0;
            min-width: 320px;
            max-width: 400px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            z-index: 100;
            padding: 8px;
            animation: dropdownSlide 0.25s ease;
            max-height: 300px;
            overflow-y: auto;
        }
        .sources-dropdown-menu.show {
            display: block;
        }
        @keyframes dropdownSlide {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .source-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 10px;
            text-decoration: none; color: var(--text-dark);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .source-item:hover {
            background: #f0f9ff;
            border-color: #e0f2fe;
            color: var(--primary);
        }
        .source-item-icon {
            width: 36px; height: 36px;
            background: #eff6ff; color: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }
        .source-item-info {
            flex: 1; overflow: hidden;
        }
        .source-item-law {
            font-size: 12px; font-weight: 700; color: var(--text-dark);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            line-height: 1.4;
        }
        .source-item-article {
            font-size: 11px; font-weight: 600; color: var(--text-muted);
            line-height: 1.3;
        }
        .source-item-arrow {
            color: #cbd5e1; font-size: 10px; flex-shrink: 0;
            transition: color 0.2s;
        }
        .source-item:hover .source-item-arrow { color: var(--primary); }
        .sources-dropdown-header {
            padding: 8px 14px 4px;
            font-size: 11px; font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-area-container {
            display: flex; flex-direction: column; align-items: center; 
            padding: 0 20px 60px; 
            background: var(--bg-card); width: 100%;
        }
        
        .input-card-pill {
            background: var(--bg-card); 
            border-radius: 50px; 
            border: 1px solid var(--border-color); 
            width: 85%; max-width: 900px; 
            height: 68px; 
            box-shadow: 0 4px 25px rgba(0,0,0,0.05); 
            padding: 8px 10px 8px 24px; 
            transition: all 0.3s ease; 
            display: flex; align-items: center; gap: 12px;
        }
        .input-card-pill:focus-within {
            border-color: var(--primary);
            box-shadow: 0 8px 30px rgba(37,99,235,0.08);
        }
        
        .chat-input {
            flex: 1; border: none; padding: 0; font-family: 'Cairo', sans-serif; 
            font-size: 16px; 
            background: transparent; outline: none; font-weight: 600; color: var(--text-dark);
            height: 100%;
        }
        
        .send-btn { 
            background: var(--primary); color: #ffffff; border: none; cursor: pointer; transition: all 0.2s ease; 
            width: 50px; height: 50px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
        } 
        .send-btn:hover { background: var(--primary-hover); transform: scale(1.05); box-shadow: 0 4px 12px rgba(37,99,235,0.3); }

        .chat-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; }
        .chat-empty-state .icon { width: 70px; height: 70px; background: var(--bg-outer); color: var(--primary); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; }
        .chat-empty-state h2 { font-size: 22px; font-weight: 800; color: var(--text-dark); margin: 0 0 10px; }
        .chat-empty-state p { font-size: 14px; color: var(--text-muted); font-weight: 600; max-width: 400px; margin-bottom: 24px;}
        
        .suggestions-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 30px;}
        .suggest-chip {
            background: var(--bg-outer); border: 1px solid var(--border-color); padding: 10px 18px;
            border-radius: 20px; font-size: 13px; font-weight: 700; color: var(--text-dark);
            cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .suggest-chip:hover { border-color: var(--primary); background: #eff6ff; color: var(--primary); transform: translateY(-2px);}

        .typing-indicator { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 700; color: var(--primary); }
        .dot { width: 6px; height: 6px; background: var(--primary); border-radius: 50%; animation: bounce 1.4s infinite ease-in-out; }
        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }

        #deleteModal { position: fixed !important; inset: 0 !important; z-index: 9999 !important; display: none; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }
        .custom-delete-modal-box { background-color: #ffffff !important; border-radius: 24px !important; padding: 32px !important; width: 100% !important; max-width: 380px !important; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important; text-align: center !important; }

        .mobile-toggle-btn, .mobile-close-btn, .assistant-sidebar-overlay { display: none; }

        @media (max-width: 1024px) {
            html, body { overflow: hidden !important; height: 100dvh !important; }
            main.page-content, main.flex-1 { height: calc(100dvh - var(--navbar-height)) !important; overflow: hidden !important;}
            
            #landingView { flex-direction: column; text-align: center; padding: 20px; justify-content: center; gap: 20px; min-height: calc(100dvh - var(--navbar-height)); height: auto;}
            .landing-content { transform: translateY(0); max-width: 100%; }
            .landing-content h1 { font-size: 28px; margin-bottom: 16px; line-height: 1.3;}
            .landing-content p { font-size: 14px; margin-bottom: 30px; }
            .start-action-btn { font-size: 15px; padding: 14px 28px; border-radius: 12px; width: 100%; justify-content: center; }
            .landing-visuals { display: none; }
            
            #chatbotContainer { height: calc(100dvh - var(--navbar-height)); position: relative; top: 0; padding: 0; gap: 0; background: var(--bg-outer); }
            
            .chat-main { border-radius: 0; border: none; box-shadow: none; background: transparent; }
            
            .assistant-sidebar { position: fixed !important; top: 0 !important; right: -320px !important; bottom: 0 !important; width: 300px !important; z-index: 1000 !important; transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; padding: 20px 16px; background: #ffffff; box-shadow: -8px 0 30px rgba(0,0,0,0.1); height: 100dvh;}
            .sidebar-open .assistant-sidebar { right: 0 !important; }
            .sidebar-open .assistant-sidebar-overlay { display: block; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 999; }
            
            .chat-header { padding: 12px 16px; background: #ffffff; border-bottom: 1px solid rgba(0,0,0,0.05); }
            .header-title.disclaimer-text { font-size: 10px; padding: 6px 10px; border-radius: 8px; gap: 6px; flex: 1; margin-left: 12px; line-height: 1.4; white-space: normal;}
            
            .messages-wrapper { padding: 16px 12px 24px; gap: 16px; }
            .msg-user { max-width: 90%; font-size: 14px; padding: 12px 16px; border-radius: 18px 18px 4px 18px; }
            .msg-ai { max-width: 92%; font-size: 14px; padding: 14px 16px; border-radius: 18px 18px 18px 4px; }
            
            .input-area-container { padding: 0 12px 16px; background: transparent; }
            .input-card-pill { width: 100%; border-radius: 24px; height: 56px; padding: 6px 8px 6px 16px; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
            .chat-input { font-size: 14px; }
            .send-btn { width: 44px; height: 44px; font-size: 18px; }
            
            .mobile-toggle-btn { display: flex; align-items: center; justify-content: center; background: #eff6ff; border: none; width: 38px; height: 38px; border-radius: 10px; cursor: pointer; color: var(--primary); font-size: 16px; flex-shrink: 0; }
            .mobile-close-btn { display: flex; align-items: center; justify-content: center; background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 10px; cursor: pointer; color: var(--text-dark); font-size: 16px; position: absolute; left: 16px; top: 16px; z-index: 10;}
            
            .history-card { border: none; box-shadow: none; padding: 0; background: transparent;}
            .new-chat-card { padding: 16px; border-radius: 20px; margin-bottom: 20px;}
            .new-chat-card h4 { font-size: 16px;}
            .new-chat-card p { font-size: 12px;}
            
            .chat-empty-state h2 { font-size: 18px; }
            .chat-empty-state p { font-size: 13px; margin-bottom: 16px;}
            .suggest-chip { font-size: 11px; padding: 8px 12px; }
            .suggestions-row { gap: 8px; }
        }
    </style>

<div id="landingView">
    <div class="landing-content">
        <div class="badge-pill">
            <i class="fas fa-sparkles"></i> مدعوم بأحدث نماذج الذكاء الاصطناعي
        </div>
        <h1>احصل على إجابات قانونية موثوقة خلال ثوانٍ.</h1>
        <p> سواء كنت تواجه مسألة قانونية معقدة، أو تبحث عن تفسير لنص قانوني، أو تحتاج إلى توجيه أولي حول حقوقك والتزاماتك، فإن مساعد ميزان الذكي يوفر لك إجابات قانونية واضحة تستند إلى أحكام القانون الليبي النافذ.</p>
        <button class="start-action-btn" id="startConsultationBtn">
            ابدأ استشارة الآن <i class="fas fa-arrow-left"></i>
        </button>
    </div>
    
    <div class="landing-visuals">
        <div class="glow-blob glow-1"></div>
        <div class="glow-blob glow-2"></div>
        <div class="glow-blob glow-3"></div>
        <div class="glow-blob glow-4"></div>
        
        <div class="floating-bubble bubble-1">
            <div class="sender"><i class="fas fa-user-circle"></i> </div>
           شن العقوبة القانونية في جريمة خيانة الأمانة؟
        </div>
        <div class="floating-bubble bubble-2">
            <div class="sender"><i class="fas fa-user-circle"></i> </div>
         واحد بنى في أرضي بدون إذني، شنو الإجراء القانوني؟
        </div>
        <div class="floating-bubble bubble-3">
            <div class="sender"><i class="fas fa-user-circle"></i> </div>
 كيف نسجل علامة تجارية في ليبيا؟
        </div>
        <div class="floating-bubble bubble-4">
            <div class="sender ai-sender"><i class="fas fa-robot"></i> ميزان AI</div>
بناءً على المعلومات التي قدمتها
            <div class="code-block">" من حقك المطالبة بحقوقك قانونياً، والأفضل الاحتفاظ بكل المستندات والمراسلات المتعلقة بالحالة.."</div>
        </div>
        <div class="floating-bubble bubble-5">
            <div class="sender"><i class="fas fa-user-circle"></i> </div>
         هل يحق لصاحب العمل يفصل الموظف بدون سبب؟
        </div>
        <div class="floating-bubble bubble-6">
            <div class="sender"><i class="fas fa-user-circle"></i> </div>
        كيف نرفع بلاغ في جريمة احتيال إلكتروني؟
        </div>
        <div class="floating-bubble bubble-7">
            <div class="sender"><i class="fas fa-user-circle"></i> </div>
      بعد الطلاق، كيف يتم تحديد حضانة الأطفال؟
        </div>
    </div>
</div>

<div class="chatbot-container" id="chatbotContainer" style="display: none;">
    <div class="assistant-sidebar-overlay" id="sidebarOverlay"></div>
    
    <aside class="assistant-sidebar" id="assistantSidebar">
        <button class="mobile-close-btn" id="mobileSidebarClose"><i class="fas fa-times"></i></button>
        
        <div class="history-card">
            <div class="sidebar-header">
                <h3>سجل الاستشارات</h3>
                <div class="search-wrapper" style="margin-top: 16px;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="sidebarSearch" class="search-input" placeholder="ابحث في السجل..." oninput="filterConversations()">
                </div>
            </div>
            
            <div class="conversations-list" id="convList"></div>
        </div>

        <div class="new-chat-card" id="newChatBtn" onclick="createNewChat()">
            <div class="new-chat-card-header">
                <div class="new-chat-card-icon"><i class="fas fa-plus"></i></div>
                <div>
                    <h4>إضافة استشارة</h4>
                </div>
            </div>
            <p>ابدأ محادثة جديدة مع الذكاء الاصطناعي الخاص بميزان واستكشف الإجابات والنماذج.</p>
        </div>
    </aside>

    <div class="chat-main" id="chatMainArea">
        <div class="chat-header">
            <div class="header-title disclaimer-text">
                <i class="fas fa-exclamation-circle"></i> تنويه: كل هذه الاستشارات إرشادية لا تغني عن استشارة محامي
            </div>
            <button class="mobile-toggle-btn" id="mobileSidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div id="dynamicZone"></div>
    </div>
</div>

{{-- نافذة تأكيد الحذف --}}
<div id="deleteModal">
    <div class="custom-delete-modal-box">
        <div style="width:60px; height:60px; background:#fee2e2; color:#ef4444; border-radius:18px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px;">
            <i class="fas fa-trash-can"></i>
        </div>
        <h4 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">حذف هذه الاستشارة؟</h4>
        <p style="font-size: 14px; color: #64748b; font-weight: 600; margin-bottom: 24px;">سيتم حذف السجل بالكامل ولا يمكن التراجع.</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="confirmDeleteBtn" style="background: #ef4444; color: white; border: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 12px; cursor: pointer;">تأكيد الحذف</button>
            <button onclick="closeDeleteModal()" style="background: #f1f5f9; color: #475569; border: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 12px; cursor: pointer;">إلغاء</button>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // دمج ذكي لمنطق الكود الخاص بك مع قوالب التصميم
    // ==========================================

    let conversations = [];
    let currentId = null;
    let deleteTargetId = null; 
    let searchQuery = ""; 
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function formatAiResponse(text) {
        if (!text) return '';
        let cleaned = text.replace(/###\s+/g, ''); 
        cleaned = cleaned.replace(/##\s+/g, '');  
        cleaned = cleaned.replace(/\*\*/g, '');    
        return escapeHtml(cleaned);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
    }

    async function loadConversations() {
        try {
            const res = await fetch(window.location.origin + '/ai-briefs');
            conversations = await res.json();
            
            if (conversations.length > 0) {
                currentId = conversations[0].id;
                await fetchActiveMessages(currentId);
            }
            renderSidebar();
            renderCurrentChat();
        } catch (err) {
            console.error('خطأ في استرجاع السجل:', err);
        }
    }

    async function fetchActiveMessages(id) {
        const conv = conversations.find(c => c.id === id);
        if (!conv || (conv.messages && conv.messages.length > 0)) return;

        try {
            const res = await fetch(window.location.origin + `/ai-briefs/${id}/messages`);
            conv.messages = await res.json();
            if (id === currentId) renderCurrentChat();
        } catch (err) {
            console.error('خطأ في جلب الرسائل:', err);
        }
    }

    function filterConversations() {
        const input = document.getElementById('sidebarSearch');
        searchQuery = input ? input.value.trim().toLowerCase() : "";
        renderSidebar();
    }

    function createNewChat() {
        const searchInput = document.getElementById('sidebarSearch');
        if (searchInput) { searchInput.value = ""; searchQuery = ""; }

        currentId = 'temp_' + Date.now(); 
        const temporaryChat = {
            id: currentId,
            title: 'استشارة جديدة',
            created_at: new Date().toISOString(),
            messages: []
        };
        
        conversations.unshift(temporaryChat);
        
        const container = document.getElementById('chatbotContainer');
        if (container) container.classList.remove('sidebar-open');
        
        const dynamicZone = document.getElementById('dynamicZone');
        if(dynamicZone) dynamicZone.classList.add('fade-out');

        setTimeout(() => {
            renderSidebar();
            renderCurrentChat();
            if(dynamicZone) dynamicZone.classList.remove('fade-out');
        }, 150);
        
        // الجلسة لا تحفظ في قاعدة البيانات إلا عند إرسال أول رسالة (لتجنب حفظ استشارات فارغة)
    }

    function openDeleteModal(id, event) {
        if (event) { event.preventDefault(); event.stopPropagation(); }
        deleteTargetId = id;
        const modal = document.getElementById('deleteModal');
        if (modal) { modal.style.display = 'flex'; }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) { modal.style.display = 'none'; }
        deleteTargetId = null;
    }

    async function executeDelete() {
        if (!deleteTargetId) return;
        const idToDelete = deleteTargetId;
        closeDeleteModal();

        conversations = conversations.filter(c => c.id !== idToDelete);
        if (currentId === idToDelete) {
            currentId = conversations.length ? conversations[0].id : null;
            if (currentId) await fetchActiveMessages(currentId);
        }
        renderSidebar();
        renderCurrentChat();

        try {
            await fetch(window.location.origin + `/ai-briefs/${idToDelete}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
        } catch (err) {
            console.error('حدث خطأ أثناء الحذف:', err);
        }
    }

    function switchConversation(id) {
        if (currentId === id) return;
        
        const dynamicZone = document.getElementById('dynamicZone');
        if(dynamicZone) dynamicZone.classList.add('fade-out');

        setTimeout(() => {
            currentId = id;
            const container = document.getElementById('chatbotContainer');
            if (container) container.classList.remove('sidebar-open');
            
            renderSidebar();
            renderCurrentChat(); 
            fetchActiveMessages(id);
            if(dynamicZone) dynamicZone.classList.remove('fade-out');
        }, 200); 
    }

    function renderSidebar() {
        const container = document.getElementById('convList');
        if (!container) return;

        const filteredConvs = conversations.filter(conv => conv.title.toLowerCase().includes(searchQuery));

        if (!filteredConvs.length) {
            container.innerHTML = `<div style="text-align:center; padding: 40px 20px; color: #94a3b8; font-weight: 600; font-size: 13px;"><i class="fas fa-inbox" style="font-size:32px; color:#cbd5e1; margin-bottom: 12px; display:block;"></i>لا توجد استشارات</div>`;
            return;
        }

        let html = '';
        filteredConvs.forEach(conv => {
            const date = new Date(conv.created_at);
            const dateLabel = `${date.getDate()}/${date.getMonth() + 1}`;
            const isActive = conv.id === currentId;

            html += `
                <div class="conv-item ${isActive ? 'active' : ''}" onclick="switchConversation('${conv.id}')">
                    <div class="conv-icon"><i class="far fa-message"></i></div>
                    <div class="conv-content-inner">
                        <div class="conv-title">${escapeHtml(conv.title)}</div>
                        <div class="conv-date">${dateLabel}</div>
                    </div>
                    <button class="conv-delete" onclick="openDeleteModal('${conv.id}', event)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>`;
        });
        container.innerHTML = html;
    }

    function showStartScreen(container) {
        const renderInputBox = () => `
        <div class="input-area-container">
            <div class="input-card-pill">
                <input type="text" id="startInput" class="chat-input" placeholder="اسأل أو ابحث عن أي شيء...">
                <button id="startSend" class="send-btn">
                    <i class="fas fa-paper-plane" style="transform: scaleX(-1); margin-right: 2px;"></i>
                </button>
            </div>
        </div>`;

        container.innerHTML = `
            <div class="chat-empty-state">
                <div class="icon"><i class="fas fa-robot"></i></div>
                <h2>كيف يمكننا مساعدتك اليوم؟</h2>
                <p>استشر في الأنظمة واللوائح والعقود</p>
                <div class="suggestions-row">

                </div>
            </div>
            ${renderInputBox()}
        `;

        const startInput = document.getElementById('startInput');
        const startSend = document.getElementById('startSend');

        const sendQuery = async (query) => {
            if (!query) return;
            if (!currentId || currentId.startsWith('temp_') || !conversations.find(c => c.id === currentId)) {
                createNewChat();
            }
            sendMessage(query);
        };

        if (startSend) startSend.onclick = () => sendQuery(startInput.value.trim());
        if (startInput) startInput.onkeypress = e => { if (e.key === 'Enter') sendQuery(startInput.value.trim()); };
        
        document.querySelectorAll('.suggest-chip').forEach(chip => {
            chip.onclick = () => sendQuery(chip.getAttribute('data-q'));
        });
    }

    function renderCurrentChat() {
        const container = document.getElementById('dynamicZone');
        if (!container) return;
        
        const activeConv = conversations.find(c => c.id === currentId);

        if (!activeConv || !activeConv.messages || !activeConv.messages.length) {
            showStartScreen(container);
            return;
        }

        const renderInputBox = () => `
        <div class="input-area-container">
            <div class="input-card-pill">
                <input type="text" id="chatInput" class="chat-input" placeholder="اكتب استفسارك هنا...">
                <button id="sendBtn" class="send-btn">
                    <i class="fas fa-paper-plane" style="transform: scaleX(-1); margin-right: 2px;"></i>
                </button>
            </div>
        </div>`;

        let messagesHtml = `<div class="messages-wrapper" id="msgWrapper">`;
        
        activeConv.messages.forEach(msg => {
            const isUser = msg.sender === 'user'; 
            const time = new Date(msg.created_at || new Date()).toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' });
            let sourceHtml = '';
            
            // بناء القائمة المنسدلة للمراجع القانونية
            if (!isUser) {
                const sources = msg.source_links || [];
                // التوافق مع الروابط القديمة
                if (sources.length === 0 && msg.source_link) {
                    try {
                        const parsed = JSON.parse(msg.source_link);
                        if (Array.isArray(parsed)) {
                            sources.push(...parsed);
                        } else {
                            sources.push({ law_name: 'المرجع القانوني', article_number: '', url: msg.source_link });
                        }
                    } catch(e) {
                        if (msg.source_link.startsWith('http') || msg.source_link.startsWith('/')) {
                            sources.push({ law_name: 'المرجع القانوني', article_number: '', url: msg.source_link });
                        }
                    }
                }

                if (sources.length > 0) {
                    const dropdownId = 'srcDrop_' + Math.random().toString(36).substr(2, 9);
                    let itemsHtml = '';
                    sources.forEach(src => {
                        const articleLabel = src.article_number ? `مادة رقم (${escapeHtml(src.article_number)})` : '';
                        itemsHtml += `
                            <a href="${src.url}" target="_blank" class="source-item">
                                <div class="source-item-icon"><i class="fas fa-scale-balanced"></i></div>
                                <div class="source-item-info">
                                    <div class="source-item-law">${escapeHtml(src.law_name || 'مرجع قانوني')}</div>
                                    ${articleLabel ? `<div class="source-item-article">${articleLabel}</div>` : ''}
                                </div>
                                <i class="fas fa-arrow-up-right-from-square source-item-arrow"></i>
                            </a>`;
                    });

                    sourceHtml = `<br>
                        <div class="sources-dropdown-wrapper">
                            <button class="sources-dropdown-btn" onclick="toggleSourcesDropdown('${dropdownId}', this, event)">
                                <i class="fas fa-bookmark"></i>
                                المراجع القانونية (${sources.length})
                                <i class="fas fa-chevron-up chevron-icon"></i>
                            </button>
                            <div class="sources-dropdown-menu" id="${dropdownId}">
                                <div class="sources-dropdown-header">المراجع المستخرجة من قاعدة البيانات</div>
                                ${itemsHtml}
                            </div>
                        </div>`;
                }
            }

            const finalMessageContent = isUser ? escapeHtml(msg.message) : formatAiResponse(msg.message);

            if (isUser) {
                messagesHtml += `<div class="msg-row"><div class="msg-user"><div style="white-space: pre-line;">${finalMessageContent}</div><div class="msg-time"><i class="far fa-clock"></i> ${time}</div></div></div>`;
            } else {
                messagesHtml += `<div class="msg-row"><div class="msg-ai"><div class="sender-badge"><i class="fas fa-robot"></i> ميزان AI</div><div style="white-space: pre-line;">${finalMessageContent}</div>${sourceHtml}<div class="msg-time"><i class="far fa-clock"></i> ${time}</div></div></div>`;
            }
        });

        messagesHtml += `</div>${renderInputBox()}`;

        container.innerHTML = messagesHtml;
        const wrapper = document.getElementById('msgWrapper');
        if (wrapper) wrapper.scrollTop = wrapper.scrollHeight;

        const input = document.getElementById('chatInput');
        const send = document.getElementById('sendBtn');
        const handler = () => { const text = input.value.trim(); if (!text) return; sendMessage(text); input.value = ''; };
        
        if (send) send.onclick = handler;
        if (input) { input.onkeypress = e => { if (e.key === 'Enter') handler(); }; input.focus(); }
    }

    function showTyping() {
        const wrapper = document.getElementById('msgWrapper');
        if (!wrapper) return;
        
        // استخدام تصميم فقاعة الميزان ليتناسب مع الواجهة الأنيقة مع الحفاظ على المنطق
        const typingHtml = `
            <div id="typingMsg" class="msg-row">
                <div class="msg-ai">
                    <div class="sender-badge"><i class="fas fa-robot"></i> ميزان AI</div>
                    <div class="typing-indicator">
                        <span>جاري الرد</span>
                        <span class="dot" style="margin-right:4px;"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>`;
            
        wrapper.insertAdjacentHTML('beforeend', typingHtml);
        wrapper.scrollTop = wrapper.scrollHeight;
    }

    function hideTyping() { 
        const el = document.getElementById('typingMsg'); 
        if (el) el.remove(); 
    }

    async function sendMessage(question) {
        const activeConv = conversations.find(c => c.id === currentId);
        if (!activeConv) return;

        // إذا كانت الاستشارة جديدة ولم تحفظ في الداتا بيز بعد، نقوم بحفظها الآن
        if (currentId.startsWith('temp_')) {
            try {
                const title = question.slice(0, 22) + (question.length > 22 ? '...' : '');
                const res = await fetch(window.location.origin + '/ai-briefs', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ title: title })
                });
                const newBrief = await res.json();
                activeConv.id = newBrief.id;
                activeConv.title = newBrief.title;
                currentId = newBrief.id;
            } catch (err) {
                console.error('خطأ في إنشاء الجلسة:', err);
            }
        }

        if (!activeConv.messages) activeConv.messages = [];
        activeConv.messages.push({ sender: 'user', message: question, created_at: new Date().toISOString() });
        
        renderCurrentChat();
        showTyping(); // إظهار مؤشر التحميل

        if (activeConv.messages.length === 1 || activeConv.title === 'استشارة جديدة') {
            activeConv.title = question.slice(0, 18) + '...';
            renderSidebar();
        }

        try {
            const res = await fetch(window.location.origin + '/ask-ai', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ 
                    question: question,
                    brief_id: currentId,
                    history: activeConv.messages.map(m => ({ role: m.sender, content: m.message }))
                })
            });

            // معالجة الأخطاء من السيرفر قبل تحويلها لـ JSON
            if (!res.ok) {
                throw new Error(`Server returned ${res.status}`);
            }

            const data = await res.json();
            
            // إخفاء مؤشر التحميل مباشرة بعد استلام الرد
            hideTyping();

            if (data.success) {
                activeConv.messages.push({ 
                    sender: 'ai', 
                    message: data.final_legal_answer, 
                    source_link: data.source_link, 
                    source_links: data.source_links || [],
                    created_at: new Date().toISOString() 
                });
            } else {
                activeConv.messages.push({ sender: 'ai', message: 'عذراً، حدث خطأ: ' + (data.error || 'يرجى المحاولة مجدداً.'), created_at: new Date().toISOString() });
            }
            renderCurrentChat();
            
        } catch (err) {
            console.error('Fetch Error:', err);
            // إخفاء مؤشر التحميل دائماً حتى لو حدث خطأ في الاتصال
            hideTyping();
            activeConv.messages.push({ sender: 'ai', message: 'تعذر الاتصال بالخادم. تأكد من اتصالك بالإنترنت أو حاول مجدداً.', created_at: new Date().toISOString() });
            renderCurrentChat();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const startBtn = document.getElementById('startConsultationBtn');
        const landingView = document.getElementById('landingView');
        const chatContainer = document.getElementById('chatbotContainer');

        if(startBtn) {
            startBtn.addEventListener('click', () => {
                landingView.style.display = 'none';
                chatContainer.style.display = 'flex';
                setTimeout(() => { chatContainer.style.opacity = '1'; }, 10);
                if(conversations.length === 0 || !currentId) { createNewChat(); }
            });
        }

        const btn = document.getElementById('newChatBtn');
        if (btn) {
            btn.onclick = (e) => {
                e.preventDefault();
                createNewChat();
            };
        }
        
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) { confirmDeleteBtn.onclick = executeDelete; }

        const mobileToggle = document.getElementById('mobileSidebarToggle');
        const mobileClose = document.getElementById('mobileSidebarClose');
        const overlay = document.getElementById('sidebarOverlay');
        const container = document.getElementById('chatbotContainer');
        
        if (mobileToggle && container) {
            mobileToggle.addEventListener('click', () => {
                container.classList.add('sidebar-open');
            });
        }
        if (mobileClose && container) {
            mobileClose.addEventListener('click', () => {
                container.classList.remove('sidebar-open');
            });
        }
        if (overlay && container) {
            overlay.addEventListener('click', () => {
                container.classList.remove('sidebar-open');
            });
        }
        
        loadConversations();
    });

    // دالة فتح/إغلاق القائمة المنسدلة للمراجع القانونية
    function toggleSourcesDropdown(dropdownId, btn, event) {
        if (event) { event.preventDefault(); event.stopPropagation(); }
        
        // إغلاق أي قائمة منسدلة مفتوحة أخرى
        document.querySelectorAll('.sources-dropdown-menu.show').forEach(menu => {
            if (menu.id !== dropdownId) {
                menu.classList.remove('show');
                menu.closest('.sources-dropdown-wrapper')?.querySelector('.sources-dropdown-btn')?.classList.remove('open');
            }
        });

        const menu = document.getElementById(dropdownId);
        if (menu) {
            menu.classList.toggle('show');
            btn.classList.toggle('open');
        }
    }

    // إغلاق القوائم المنسدلة عند الضغط خارجها
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.sources-dropdown-wrapper')) {
            document.querySelectorAll('.sources-dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
                menu.closest('.sources-dropdown-wrapper')?.querySelector('.sources-dropdown-btn')?.classList.remove('open');
            });
        }
    });

</script>
@endsection