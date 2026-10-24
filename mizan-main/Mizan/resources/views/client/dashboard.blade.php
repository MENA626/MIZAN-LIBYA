@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page_title', 'لوحة التحكم')

@section('client_dashboard_active', 'nav-active')

@section('content')

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ميزان | خيطك نحو الحل الواضح</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet" />

  <style>
    /* =========================================
       Mizan — Cinematic minimalist (pure white / blue)
       ========================================= */
    :root {
      --background: 0 0% 100%;     
      --foreground: 0 0% 9%;       
      --card: 0 0% 100%;
      --secondary: 220 22% 96%;
      --primary: 217 99% 53%;      
      --primary-foreground: 0 0% 100%;
      --muted-foreground: 220 9% 46%;
      --border: 220 16% 91%;
      --font-sans: 'Cairo', sans-serif;
      --font-serif: 'Tajawal', sans-serif;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      font-family: var(--font-sans);
      color: hsl(var(--foreground));
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
      min-height: 100vh;
      background: radial-gradient(120% 78% at 50% -8%, hsl(0 0% 100%) 0%, hsl(216 26% 97.5%) 46%, hsl(214 22% 95%) 100%);
      background-attachment: fixed;
    }
    h1, h2, h3 { font-family: var(--font-serif); font-weight: 800; line-height: 1.1; }

    .surface { background: linear-gradient(to bottom, hsl(0 0% 100%), hsl(220 26% 98.5%)); box-shadow: 0 1px 2px rgba(16,24,40,0.04), 0 16px 40px -22px rgba(16,24,40,0.18); }
    .surface:hover { box-shadow: 0 1px 2px rgba(16,24,40,0.05), 0 28px 64px -26px rgba(16,24,40,0.22); }

    @media (max-width: 768px) { body { background-attachment: scroll; } }

    .intro-thread { position: absolute; top: 0; left: 0; width: 100%; height: 100vh; pointer-events: none; z-index: 0; }
    .intro-thread svg { width: 100%; height: 100%; }
    .wrap { position: relative; z-index: 10; }

    .hero {
      min-height: 100vh; width: 100%; display: flex; align-items: center;
      padding: 6rem clamp(1.5rem, 6vw, 5rem) 2rem; position: relative; overflow: hidden;
    }
    
    .hero-splashes { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
    .splash-1 { position: absolute; top: -20%; right: -15%; width: 70vw; height: 70vw; background: radial-gradient(circle, hsla(217, 99%, 53%, 0.12) 0%, transparent 65%); filter: blur(100px); }
    .splash-2 { position: absolute; bottom: -10%; left: -10%; width: 50vw; height: 50vw; background: radial-gradient(circle, hsla(210, 100%, 65%, 0.08) 0%, transparent 65%); filter: blur(90px); }
    .splash-3 { position: absolute; top: 20%; right: 25%; width: 45vw; height: 45vw; background: radial-gradient(circle, hsla(217, 99%, 53%, 0.06) 0%, transparent 60%); filter: blur(80px); }

    .hero-ghost { position: absolute; left: 0; right: 0; top: 9%; text-align: center; font-family: var(--font-serif); font-weight: 800; line-height: 1; letter-spacing: -0.04em; font-size: 23vw; color: hsl(var(--foreground) / 0.035); pointer-events: none; user-select: none; z-index: 0; }
    @media (max-width: 767px) { .hero-ghost { top: 14%; font-size: 34vw; } }
    
    .hero-grid { width: 100%; max-width: 80rem; margin: 0 auto; display: grid; grid-template-columns: 1fr; gap: 2.5rem; align-items: center; z-index: 10; }
    .hero-text { text-align: center; position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; }
    
    .hero-badge {
      display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem;
      border-radius: 9999px; background: rgba(15, 106, 253, 0.08); border: 1px solid rgba(15, 106, 253, 0.2);
      color: hsl(var(--primary)); font-weight: 700; font-size: 0.95rem; margin-bottom: 1.5rem; backdrop-filter: blur(8px);
    }
    .pulse-dot {
      width: 8px; height: 8px; border-radius: 50%; background: hsl(var(--primary));
      box-shadow: 0 0 0 0 rgba(15, 106, 253, 0.4); animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(15, 106, 253, 0.4); }
      70% { box-shadow: 0 0 0 8px rgba(15, 106, 253, 0); }
      100% { box-shadow: 0 0 0 0 rgba(15, 106, 253, 0); }
    }
    
    .hero h1 { position: relative; z-index: 10; font-size: clamp(2.5rem, 6.5vw, 6rem); line-height: 1.2; margin-bottom: 1.2rem; letter-spacing: -0.02em; color: hsl(var(--foreground)); text-shadow: 0 15px 40px rgba(29, 78, 216, 0.08); white-space: nowrap; }
    .hero h1 .text-blue { color: hsl(var(--primary)); text-shadow: 0 0 45px hsla(217, 99%, 53%, 0.35); display: inline-block; }
    
    .hero-sub { position: relative; z-index: 10; font-size: clamp(1.4rem, 3.5vw, 1.8rem); color: hsl(var(--muted-foreground)); font-weight: 300; line-height: 1.7; max-width: 40rem; margin: 0 auto; white-space: normal; }
    .hero-sub .hl { color: hsl(var(--foreground)); font-weight: 700; }
    
    .hero-cta-wrap { margin-top: 3rem; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }
    .hero .hint { font-size: 0.9rem; color: hsl(var(--muted-foreground)); letter-spacing: 0.02em; font-weight: 500; }
    
    .btn-hero { 
      display: inline-flex; align-items: center; gap: 0.75rem; border: none; border-radius: 9999px; 
      background: hsl(var(--primary)); color: #fff; padding: 1.1rem 3rem; font-family: var(--font-serif); 
      font-size: 1.25rem; font-weight: 800; cursor: pointer; transition: all .3s ease; 
      box-shadow: 0 20px 55px -18px rgba(15,106,253,0.55); 
    }
    .btn-hero svg { transition: transform 0.3s ease; }
    .btn-hero:hover { background: hsl(217 99% 48%); transform: translateY(-3px) scale(1.02); box-shadow: 0 25px 60px -15px rgba(15,106,253,0.65); }
    .btn-hero:hover svg { transform: translateX(-6px); } 
    
    .hero-maze { position: relative; }
    .hero-maze .glow { position: absolute; inset: 0; background: hsl(var(--primary) / 0.06); filter: blur(110px); border-radius: 9999px; pointer-events: none; }
    .hero-maze svg { position: relative; width: 100%; max-width: 34rem; height: auto; margin: 0 auto; display: block; }
    
    @keyframes gentleFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }
    #mazeHero { animation: gentleFloat 6s ease-in-out infinite; }

    @media (min-width: 768px) { 
      .hero-grid { grid-template-columns: 1fr 1fr; gap: 2rem; position: static; } 
      .hero-text { text-align: right; align-items: flex-start; grid-column: 2; padding-right: 2rem; }
      .hero-sub { margin: 0 0 0 auto; text-align: right; }
      .hero-cta-wrap { align-items: flex-start; }
      
      .hero-maze { 
        position: absolute; right: -3px; top: 55%; width: 38vw; max-width: 700px;
        z-index: 5; transform: translateY(-50%); 
      }
      .hero-maze.reveal.in-view { transform: translateY(-50%); }
      .hero-maze svg { max-width: none; width: 100%; margin: 0; } 
    }
    
    @media (max-width: 480px) {
      .hero h1 { white-space: normal; font-size: clamp(2.5rem, 9vw, 3.5rem); }
    }

    .reveal { opacity: 0; transform: translateY(40px); transition: opacity 1s cubic-bezier(.16,1,.3,1), transform 1s cubic-bezier(.16,1,.3,1); }
    .reveal.in-view { opacity: 1; transform: none; }

    .journey { position: relative; width: 100%; padding: 3rem clamp(1.5rem, 5vw, 3rem) clamp(8rem, 16vw, 12rem); }
    .journey-head { max-width: 48rem; margin: 0 auto clamp(6rem, 10vw, 10rem); text-align: center; }
    .journey-head .eyebrow { color: hsl(var(--primary)); font-weight: 700; font-size: 0.8rem; letter-spacing: 0.3em; margin-bottom: 1.5rem; }
    .journey-head h2 { font-size: clamp(2.25rem, 6vw, 3.75rem); margin-bottom: 1.5rem; }
    .journey-head p { font-size: clamp(1rem, 2.2vw, 1.25rem); color: hsl(var(--muted-foreground)); font-weight: 300; line-height: 1.7; }
    .connector { width: 1px; height: 5rem; margin: 3rem auto 0; background: linear-gradient(to bottom, hsl(var(--primary) / 0.2), hsl(var(--primary) / 0.4), hsl(var(--primary) / 0.7)); }

    .journey-inner { position: relative; max-width: 64rem; margin: 0 auto; }
    .journey-path { position: absolute; top: 0; bottom: 0; left: 50%; transform: translateX(-50%); width: min(60%, 300px); pointer-events: none; z-index: 0; }
    .journey-path svg { width: 100%; height: 100%; }

    .stages { position: relative; z-index: 10; display: flex; flex-direction: column; gap: clamp(7rem, 12vw, 9rem); }
    .stage { display: flex; align-items: center; gap: clamp(1.5rem, 4vw, 3rem); }
    .stage .caption { flex: 1; }
    .stage .head { display: flex; align-items: baseline; gap: 1rem; margin-bottom: 0.75rem; }
    .stage .num { font-family: var(--font-serif); font-weight: 800; font-size: clamp(3rem, 7vw, 4.5rem); color: var(--accent, hsl(var(--primary))); opacity: 0.16; line-height: 0.9; transition: opacity .2s linear; }
    .stage .title { font-size: clamp(1.5rem, 4vw, 2.25rem); }
    .stage .desc { font-size: clamp(1rem, 2vw, 1.125rem); color: hsl(var(--muted-foreground)); font-weight: 300; line-height: 1.7; max-width: 28rem; }
    .stage .node { flex-shrink: 0; position: relative; display: flex; align-items: center; justify-content: center; width: 4rem; height: 4rem; }
    .stage .node .glow { position: absolute; width: 5rem; height: 5rem; border-radius: 9999px; filter: blur(10px); background: var(--accent, hsl(var(--primary))); opacity: 0; }
    .stage .node-inner { position: relative; width: 4rem; height: 4rem; transition: transform .2s ease-out; }
    .stage .node .ring, .stage .node .ring-active { position: absolute; inset: 0; border-radius: 9999px; display: flex; align-items: center; justify-content: center; }
    .stage .node .ring { background: hsl(var(--background)); border: 1.5px solid var(--accent, hsl(var(--primary))); color: var(--accent, hsl(var(--primary))); box-shadow: 0 1px 3px rgba(0,0,0,.06); }
    .stage .node .ring-active { background: var(--accent, hsl(var(--primary))); color: #fff; opacity: 0; box-shadow: 0 4px 12px rgba(0,0,0,.14); }
    .stage .node .ring svg, .stage .node .ring-active svg { width: 2rem; height: 2rem; }
    .stage .illus { flex: 1; display: flex; align-items: center; justify-content: center; }
    .stage .illus svg { width: 100%; max-width: 20rem; height: auto; }
    .stage .illus-m { display: none; }

    .traveller-track { position: absolute; top: 0; bottom: 0; left: 50%; transform: translateX(-50%); width: min(60%, 300px); pointer-events: none; z-index: 20; }
    .traveller { position: absolute; top: 0; left: 50%; transform: translate(-50%, -50%); transition: top 1.2s cubic-bezier(.22,.61,.36,1), left 1.2s cubic-bezier(.22,.61,.36,1); }
    .traveller .t-glow { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 5rem; height: 5rem; border-radius: 9999px; filter: blur(18px); opacity: 0.6; background: var(--tc, hsl(var(--primary))); }
    .traveller .t-disc { position: relative; width: 2.9rem; height: 2.9rem; border-radius: 9999px; background: var(--tc, hsl(var(--primary))); display: flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px rgba(0,0,0,.18); }
    .traveller .t-disc svg { width: 1.1rem; height: 2rem; color: #fff; }

    @media (min-width: 768px) {
      .stage.right { flex-direction: row; text-align: right; }
      .stage.right .caption { padding-left: clamp(2rem, 5vw, 5rem); text-align: left; }
      .stage.right .head { flex-direction: row; justify-content: flex-start; }
      .stage.left { flex-direction: row-reverse; text-align: left; }
      .stage.left .caption { padding-right: clamp(2rem, 5vw, 5rem); text-align: right; }
      .stage.left .head { flex-direction: row-reverse; justify-content: flex-start; }
      .stage.left .desc { margin-left: 0; margin-right: 0; }
    }
    @media (max-width: 767px) {
      .stage { flex-direction: row-reverse; text-align: right; }
      .stage .illus { display: none; }
      .stage .illus-m { display: block; width: 11rem; max-width: 60%; margin-bottom: 1.5rem; margin-inline-start: auto; }
      .stage .illus-m svg { width: 100%; height: auto; }
      .stage .head { flex-direction: row-reverse; justify-content: flex-end; }
      .stage .desc { margin-right: auto; }
    }

    .summit {
      width: 100%; display: flex; flex-direction: column; align-items: center; text-align: center;
      padding: 0 clamp(1.5rem, 6vw, 6rem); position: relative; overflow: hidden; margin-top: -1px;
      background: linear-gradient(to bottom, hsl(222 58% 10%), hsl(225 82% 6%) 55%, #03050d); color: #fff;
    }
    .summit .thread-in { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 2px; height: 7rem; background: linear-gradient(to bottom, rgba(255,255,255,0), hsl(213 100% 72%)); }
    .summit .reveal { padding: 26vh 0 6rem; transform: translateY(44px) scale(0.86); filter: blur(18px); transition: opacity 1.2s cubic-bezier(.16,1,.3,1), transform 1.2s cubic-bezier(.16,1,.3,1), filter 1.2s cubic-bezier(.16,1,.3,1); }
    .summit .reveal.in-view { transform: none; filter: blur(0); }
    .summit .burst { position: absolute; top: 30vh; left: 50%; transform: translateX(-50%) scale(0.35); width: min(88vw, 600px); height: min(60vh, 600px); border-radius: 9999px; filter: blur(8px); background: radial-gradient(circle, hsl(205 100% 88% / 0.55) 0%, hsl(213 100% 64% / 0.30) 34%, transparent 68%); pointer-events: none; z-index: 10; opacity: 0; transition: opacity 1.4s cubic-bezier(.16,1,.3,1), transform 1.4s cubic-bezier(.16,1,.3,1); }
    .summit .reveal.in-view ~ .burst { opacity: 1; transform: translateX(-50%) scale(1); }
    .summit .flash { position: absolute; inset: 0; pointer-events: none; z-index: 40; opacity: 0; background: radial-gradient(circle at 50% 40%, hsl(204 100% 95% / 0.95), hsl(213 100% 70% / 0.35) 38%, transparent 62%); }
    @keyframes summitFlash { 0% { opacity: 0; } 32% { opacity: 0.92; } 100% { opacity: 0; } }
    .summit .reveal.in-view ~ .flash { animation: summitFlash 1.7s cubic-bezier(.16,1,.3,1) forwards; }
    .summit .marker { display: flex; flex-direction: column; align-items: center; gap: 1rem; margin-bottom: 2rem; color: rgba(255,255,255,0.9); font-weight: 700; }
    .summit .marker .line { width: 1px; height: 3.5rem; background: linear-gradient(to bottom, rgba(255,255,255,0.3), #fff); }
    
    .summit .marker .final-num {
      font-family: var(--font-serif);
      font-size: clamp(3rem, 7vw, 4.5rem);
      font-weight: 800;
      color: hsl(213 100% 72%);
      line-height: 0.9;
      margin-top: 1rem;
      opacity: 0.3;
    }

    .summit h2 { font-size: clamp(4rem, 12vw, 9rem); margin-bottom: 2rem; color: hsl(213 100% 72%); text-shadow: 0 0 42px hsl(214 100% 60% / 0.7), 0 0 95px hsl(214 100% 55% / 0.45); }
    .summit p { 
      font-size: clamp(1.35rem, 3vw, 1.875rem); color: rgba(255,255,255,0.85); font-weight: 300; 
      max-width: 38rem; line-height: 1.7; margin: 0 auto 4rem auto; text-align: center;
    }
    .summit .glow { position: absolute; bottom: 6%; left: 50%; transform: translateX(-50%); width: min(95vw, 58vw); height: min(60vh, 56vh); border-radius: 9999px; background: hsl(214 100% 56% / 0.20); filter: blur(130px); pointer-events: none; z-index: 0; }
    .summit .beam { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 42vw; height: 70vh; background: linear-gradient(to bottom, hsl(213 100% 62% / 0.15), transparent); filter: blur(90px); pointer-events: none; z-index: 0; }
    
    .site-footer { position: relative; z-index: 20; width: 100%; max-width: 64rem; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 2rem; text-align: center; padding-bottom: 4rem; color: rgba(255,255,255,0.7); }
    .site-footer .brand { font-family: var(--font-serif); font-size: 1.875rem; font-weight: 700; color: #fff; }
    .site-footer nav { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.75rem 2rem; font-size: 0.875rem; font-weight: 300; }
    .site-footer nav a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color .3s; cursor: pointer; }
    .site-footer nav a:hover { color: #fff; }
    .site-footer .copy { font-size: 0.75rem; color: rgba(255,255,255,0.4); letter-spacing: 0.02em; }

    /* =========================================
       تصميم النوافذ المنبثقة (Modals) الزجاجية
       ========================================= */
    .glass-modal {
      position: fixed; inset: 0; z-index: 9999;
      display: flex; align-items: center; justify-content: center;
      opacity: 0; visibility: hidden; transition: all 0.3s ease;
    }
    .glass-modal.is-open { opacity: 1; visibility: visible; }
    
    .glass-modal-overlay {
      position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4);
      backdrop-filter: blur(8px); cursor: pointer;
    }
    
    .glass-modal-container {
      position: relative; width: 90%; max-width: 650px; max-height: 85vh;
      background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 24px;
      padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
      overflow-y: auto; transform: translateY(20px) scale(0.98); transition: transform 0.3s ease;
      text-align: right; direction: rtl;
    }
    .glass-modal.is-open .glass-modal-container { transform: translateY(0) scale(1); }
    
    .glass-modal-header {
      display: flex; justify-content: space-between; align-items: center;
      border-bottom: 1px solid rgba(15, 23, 42, 0.1);
      padding-bottom: 1rem; margin-bottom: 1.5rem;
    }
    .glass-modal-title { font-family: var(--font-serif); font-size: 1.8rem; font-weight: 800; color: hsl(var(--primary)); }
    
    .glass-modal-close {
      background: none; border: none; font-size: 2rem; color: var(--muted-foreground);
      cursor: pointer; line-height: 1; transition: color 0.2s; padding: 0;
    }
    .glass-modal-close:hover { color: hsl(var(--foreground)); }
    
    .glass-modal-content { font-size: 1.1rem; line-height: 1.8; color: var(--foreground); }
    .glass-modal-content h3 { color: hsl(var(--primary)); margin-top: 1.5rem; margin-bottom: 0.5rem; font-size: 1.25rem; font-family: var(--font-serif); font-weight: 700; }
    .glass-modal-content p, .glass-modal-content ul { margin-bottom: 1rem; color: #475569; }
    .glass-modal-content ul { padding-right: 1.5rem; }
    .glass-modal-content li { margin-bottom: 0.5rem; }

    .glass-modal-container::-webkit-scrollbar { width: 6px; }
    .glass-modal-container::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 10px; }
    .glass-modal-container::-webkit-scrollbar-thumb { background: rgba(15, 106, 253, 0.5); border-radius: 10px; }
  </style>
</head>
<body>
  <div class="intro-thread">
    <svg viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M 50 58 C 50 74, 50 86, 50 100" stroke="hsl(var(--primary) / 0.1)" stroke-width="0.5" stroke-linecap="round" />
      <path id="introPath" d="M 50 58 C 50 74, 50 86, 50 100" stroke="hsl(var(--primary))" stroke-width="0.5" stroke-linecap="round" />
    </svg>
  </div>

  <div class="wrap">
    <section class="hero">
      <div class="hero-splashes" aria-hidden="true">
        <div class="splash-1"></div>
        <div class="splash-2"></div>
        <div class="splash-3"></div>
      </div>
      <span class="hero-ghost" aria-hidden="true">ميزان</span>
      <div class="hero-grid">
        <div class="hero-maze reveal in-view">
          <span class="glow"></span>
          <div id="mazeHero" aria-hidden="true"></div>
        </div>
        <div class="hero-text reveal in-view">
          <div class="hero-badge">
            <span class="pulse-dot"></span>
            مستشارك القانوني الذكي
          </div>
          <h1>في <span class="text-blue">متاهات</span> القانون.</h1>
          <p class="hero-sub">نضيء لك المسار القانوني ونختصر عليك المسافات، لتصل إلى <span class="hl">حقوقك بوضوح وأمان.</span></p>
          <div class="hero-cta-wrap">
            <a href="#journey" class="btn-hero" style="text-decoration:none;">
              اكتشف مسار الحل
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
              </svg>
            </a>
            <span class="hint">مرر للأسفل لاستكشاف التفاصيل</span>
          </div>
        </div>
      </div>
    </section>

    <section class="journey" id="journey">
      <div class="journey-head reveal">
        <div class="eyebrow">الرحلة</div>
        <h2>رحلتك داخل ميزان</h2>
        <p>من عقدة الحيرة الأولى، خطوة بخطوة، حتى تصل إلى الحل الواضح.</p>
        <div class="connector"></div>
      </div>

      <div class="journey-inner">
        <div class="journey-path">
          <svg viewBox="0 0 100 660" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path id="journeyBg" d="M 50 0 C 50 40, 78 70, 78 110 C 78 150, 22 180, 22 220 C 22 260, 78 290, 78 330 C 78 370, 22 400, 22 440 C 22 480, 78 510, 78 550 C 78 590, 50 620, 50 660" stroke="hsl(var(--primary) / 0.15)" stroke-width="1.5" stroke-linecap="round" />
            <path id="journeyDraw" d="M 50 0 C 50 40, 78 70, 78 110 C 78 150, 22 180, 22 220 C 22 260, 78 290, 78 330 C 78 370, 22 400, 22 440 C 22 480, 78 510, 78 550 C 78 590, 50 620, 50 660" stroke="hsl(var(--primary))" stroke-width="1.5" stroke-linecap="round" />
          </svg>
        </div>
        <div class="traveller-track"><div class="traveller" id="traveller"><span class="t-glow"></span><span class="t-disc"><svg viewBox="0 0 40 80" fill="currentColor" aria-hidden="true"><circle cx="20" cy="12" r="7"/><path d="M20 20 L20 48 M20 26 L8 40 M20 26 L33 38 M20 48 L9 72 M20 48 L31 72" stroke="currentColor" stroke-width="5" stroke-linecap="round" fill="none"/></svg></span></div></div>
        <div class="stages" id="stages"></div>
      </div>
    </section>

    <section class="summit" id="summit">
      <div class="thread-in"></div>
      <div class="beam"></div>
      <div class="reveal repeat-anim" style="position:relative;z-index:20;width:100%;display:flex;flex-direction:column;align-items:center;text-align:center;">
        
        <div class="marker">
          <span class="line"></span>
          <span class="final-num">10</span>
        </div>
        
        <h2>قضيتك حُلت.</h2>
        <p>هنا تنتهي الحيرة وتبدأ الطمأنينة. في ميزان، نحن لا نقدم مجرد استشارات، بل نصنع حلاً نهائياً يُعيد لك حقوقك وينهي كل تعقيد قانوني واجهته.</p>
        
      </div>
      <div class="burst"></div>
      <div class="glow"></div>
      <footer class="site-footer">
        <div class="brand">ميزان</div>
        <nav>
          <a onclick="openModal('privacy')">سياسة الخصوصية</a>
          <a onclick="openModal('terms')">الشروط والأحكام</a>
          <a onclick="openModal('contact')">تواصل معنا</a>
          <a onclick="openModal('about')">عن ميزان</a>
        </nav>
        <div class="copy">© 2026 ميزان. جميع الحقوق محفوظة.</div>
      </footer>
      <div class="flash"></div>
    </section>
  </div>

  <!-- النافذة المنبثقة (Modal) المتداخلة -->
  <div class="glass-modal" id="infoModal" aria-hidden="true">
    <div class="glass-modal-overlay" id="modalOverlay" tabindex="-1"></div>
    <div class="glass-modal-container" role="dialog" aria-modal="true">
      <header class="glass-modal-header">
        <h2 class="glass-modal-title" id="modalTitle">العنوان</h2>
        <button class="glass-modal-close" aria-label="إغلاق" onclick="closeModal()">&times;</button>
      </header>
      <main class="glass-modal-content" id="modalContent">
        <!-- سيتم حقن المحتوى هنا عبر الـ JS -->
      </main>
    </div>
  </div>

  <script>
    var STAGES = [
  {
  t: "بداية المتاهة",
  d: '<div style="direction: rtl; text-align: right; color: #000;">تبدأ رحلتك عندما تواجه مشكلة قانونية وتجد نفسك حائرًا أمام القوانين الليبية والمصطلحات القانونية المعقدة. تتداخل أمامك الإجراءات، وتكثر التساؤلات، ويصعب عليك تحديد الخطوة الصحيحة التي يجب اتخاذها، فتشعر وكأنك تسير داخل متاهة قانونية لا تعرف من أين تبدأ ولا إلى أين تتجه.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 18 C40 26 24 40 50 46 M16 44 C42 36 26 24 50 16 M12 32 C44 30 22 34 52 32"/></svg>'
},



 {
t: "المساعد الذكي",
d: '<div style="direction: rtl; text-align: right; color: #000;">تطرح سؤالك  فيتولى مساعد ميزان الذكي تحليل سؤالك بدقة وفهم تفاصيله، ثم يقدّم لك إجابات قانونية واضحة وموثوقة مستندة إلى مصادر معتمدة.</div>',
i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16 H52 V42 H30 L20 52 V42 H12 Z"/><path d="M24 26 L30 32 L40 22"/></svg>'
},



      {
  t: "إجابات شافية",
  d: '<div style="direction: rtl; text-align: right; color: #000;">تبدأ الصورة بالاتضاح أمامك، وتحصل على معلومات قانونية دقيقة وواضحة، وتزول الشكوك تدريجيًا بفضل إجابات موثوقة ومفسّرة بلغة بسيطة تساعدك على الفهم واتخاذ القرار.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="32" cy="32" r="10"/><path d="M32 8 V16 M32 48 V56 M8 32 H16 M48 32 H56 M15 15 L21 21 M49 49 L43 43 M49 15 L43 21 M15 49 L21 43"/></svg>'
},


    

{
  t: "المحامي المناسب",
  d: '<div style="direction: rtl; text-align: right; color: #000;">إذا كانت مشكلتك لا تقتصر على استشارة فقط وتحتاج إلى تدخل مختص، توفر لك المنصة الوصول إلى المحامي الأنسب وفقًا لنوع قضيتك وتخصصه القانوني، بما يضمن التعامل مع حالتك بالشكل الصحيح.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="32" cy="32" r="22" stroke-dasharray="4 5"/><circle cx="32" cy="32" r="10"/><path d="M32 14 V24 M32 40 V50 M14 32 H24 M40 32 H50"/><circle cx="32" cy="32" r="3" fill="currentColor" stroke="none"/></svg>'
},



 {
  t: "حجز الموعد",
  d: '<div style="direction: rtl; text-align: right; color: #000;">يمكنك حجز موعد مع المحامي بكل سهولة، سواء كنت بحاجة إلى استشارة أو كنت ترغب في فتح قضية. يتيح لك النظام اختيار الوقت المناسب والالتقاء بالمحامي لاتخاذ الخطوة القانونية التالية.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="12" y="16" width="40" height="36" rx="3"/><path d="M12 26 H52 M22 12 V20 M42 12 V20"/><path d="M28 38 L31 41 L38 33"/></svg>'
},



    
{
  t: "جلسة الاستشارة",
  d: '<div style="direction: rtl; text-align: right; color: #000;">يمكنك طلب استشارة قانونية مدفوعة بكل سهولة عبر المنصة، حيث تتيح لك التواصل المباشر مع المحامي بسرية وسرعة، وفق قيمة يحددها المحامي حسب طبيعة الاستشارة.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 14 H40 V32 H22 L14 40 V32 H10 Z"/><path d="M28 26 H54 V42 H46 V50 L38 42 H28 Z"/></svg>'
},



 {
  t: "متابعة القضية",
  d: '<div style="direction: rtl; text-align: right; color: #000;">في حال الاتفاق مع المحامي وفتح ملف قضيتك، ستتمكن من متابعة قضيتك رقمياً بشكل مباشر مع المحامي، دون الحاجة إلى التواصل التقليدي، وبشفافية كاملة في جميع مراحل الإجراءات.</div>',
  i: '<svg viewBox="0 0 64 64" fill="currentColor" stroke="none"><polygon points="32,46 50,37 50,46 32,55" opacity="0.3"/><polygon points="14,37 32,28 32,37 14,46" opacity="0.5"/><polygon points="32,28 50,19 50,28 32,37" opacity="0.7"/><polygon points="14,19 32,10 32,19 14,28" opacity="0.9"/></svg>'
},


{
  t: "الخزنة الذكية ",
  d: '<div style="direction: rtl; text-align: right; color: #000;">انسَ مشكلة ضياع مستنداتك القانونية المهمة وقت حاجتك إليها. مع الخزنة الرقمية، يمكنك حفظ جميع مستنداتك في مكان واحد آمن، مع إمكانية الطباعة والمشاركة، بالإضافة إلى تلخيص أهم مستنداتك باستخدام تقنيات الذكاء الاصطناعي.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="14" y="12" width="10" height="40"/><rect x="27" y="12" width="10" height="40"/><path d="M40 14 L50 16 L44 54 L34 52 Z"/></svg>'
},


{
  t: "الدفع الإلكتروني",
  d: '<div style="direction: rtl; text-align: right; color: #000;">يمكنك دفع أقساط قضيتك أو استشارتك القانونية بكل سهولة عبر بطاقات الدفع الليبية وأنت في منزلك، من خلال نظام آمن وسريع يسهّل عليك إتمام جميع عمليات الدفع دون أي تعقيد.</div>',
  i: '<svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M32 8 L52 16 V32 C52 44 43 52 32 56 C21 52 12 44 12 32 V16 Z"/><path d="M25 32 L30 37 L40 26"/></svg>'
},
  
    ];

    var STAGE_HSL = [[218,52,43],[214,92,54],[215,93,50],[216,95,46],[217,96,43],[218,95,40],[219,92,36],[221,86,32],[223,80,28],[226,76,23]];
    var NEUTRAL = '#c2cad6';
    var NEUTRAL_D = '#9aa6b8';
    var SHADOW = 'rgba(15,23,42,0.06)';
    function hslStr(a) { return 'hsl(' + a[0] + ' ' + a[1] + '% ' + a[2] + '%)'; }
    function lightenHsl(a, dl) { return 'hsl(' + a[0] + ' ' + a[1] + '% ' + Math.max(0, Math.min(100, a[2] + dl)) + '%)'; }
    function stageColor(i) { return hslStr(STAGE_HSL[i]); }
    function travColor(p) {
      var n = STAGE_HSL.length - 1, x = Math.max(0, Math.min(1, p)) * n;
      var i = Math.floor(x), f = x - i; if (i >= n) { i = n - 1; f = 1; }
      var a = STAGE_HSL[i], b = STAGE_HSL[i + 1];
      return 'hsl(' + (a[0] + (b[0] - a[0]) * f) + ' ' + (a[1] + (b[1] - a[1]) * f) + '% ' + (a[2] + (b[2] - a[2]) * f) + '%)';
    }
    function interp(p, xs, ys) {
      if (p <= xs[0]) return ys[0];
      for (var k = 1; k < xs.length; k++) { if (p <= xs[k]) { var f = (p - xs[k - 1]) / (xs[k] - xs[k - 1]); return ys[k - 1] + (ys[k] - ys[k - 1]) * f; } }
      return ys[ys.length - 1];
    }

    var D2R = Math.PI / 180;
    var POSES = {
      stand: { uaL: -14, faL: -10, uaR: 14, faR: 10, thL: -5, snL: -4, thR: 5, snR: 4, lean: 0 },
      walk: { uaL: 26, faL: 40, uaR: -24, faR: -34, thL: -26, snL: -36, thR: 28, snR: 22, lean: 5 },
      talk: { uaL: -12, faL: -8, uaR: 52, faR: 78, thL: -6, snL: -5, thR: 6, snR: 5, lean: 2 },
      cheer: { uaL: -148, faL: -166, uaR: 148, faR: 166, thL: -14, snL: -18, thR: 14, snR: 18, lean: 0 },
      sit: { uaL: 34, faL: 60, uaR: 26, faR: 54, thL: 74, snL: 8, thR: 80, snR: 10, lean: 6 },
      look: { uaL: -18, faL: -28, uaR: 20, faR: 14, thL: -5, snL: -4, thR: 5, snR: 4, lean: -4 },
      jump_clear: { uaL: -145, faL: -145, uaR: 145, faR: 145, thL: -45, snL: -15, thR: 45, snR: 15, lean: -5 }
    };
    function fseg(sx, sy, len, ang) { return [sx + len * Math.sin(ang * D2R), sy + len * Math.cos(ang * D2R)]; }
    function fig(x, y, scale, fill, dir, pose) {
      var P = POSES[pose] || POSES.stand;
      var neck = [0, -66], pelvis = [0, -34], shL = [-3, -62], shR = [3, -62], hipL = [-5, -34], hipR = [5, -34];
      var eL = fseg(shL[0], shL[1], 15, P.uaL), wL = fseg(eL[0], eL[1], 14, P.faL);
      var eR = fseg(shR[0], shR[1], 15, P.uaR), wR = fseg(eR[0], eR[1], 14, P.faR);
      var kL = fseg(hipL[0], hipL[1], 18, P.thL), aL = fseg(kL[0], kL[1], 18, P.snL);
      var kR = fseg(hipR[0], hipR[1], 18, P.thR), aR = fseg(kR[0], kR[1], 18, P.snR);
      function ln(a, b, w) { return '<line x1="' + a[0] + '" y1="' + a[1] + '" x2="' + b[0] + '" y2="' + b[1] + '" stroke="' + fill + '" stroke-width="' + w + '" stroke-linecap="round"/>'; }
      return '<g transform="translate(' + x + ' ' + y + ') scale(' + (scale * (dir || 1)) + ' ' + scale + ') rotate(' + P.lean + ')">'
        + ln(hipL, kL, 13) + ln(kL, aL, 12) + ln(hipR, kR, 13) + ln(kR, aR, 12)
        + ln(pelvis, neck, 19) + ln(shL, eL, 11) + ln(eL, wL, 10) + ln(shR, eR, 11) + ln(eR, wR, 10)
        + '<circle cx="' + neck[0] + '" cy="' + (neck[1] - 15) + '" r="15" fill="' + fill + '"/></g>';
    }

    function walkerShape(fill) {
      return '<path d="M0 -44 L-11 -22 L-20 -2" fill="none" stroke="' + fill + '" stroke-width="11" stroke-linecap="round" stroke-linejoin="round"/>'
        + '<path d="M2 -44 L14 -24 L24 -3" fill="none" stroke="' + fill + '" stroke-width="12.5" stroke-linecap="round" stroke-linejoin="round"/>'
        + '<path d="M-9 -45 L-8 -84 Q-8 -91 -1 -91 L5 -91 Q12 -91 11 -84 L10 -45 Z" fill="' + fill + '"/>'
        + '<path d="M-1 -82 L-10 -70 L-16 -55" fill="none" stroke="' + fill + '" stroke-width="8.5" stroke-linecap="round" stroke-linejoin="round"/>'
        + '<path d="M3 -82 L12 -71 L20 -60" fill="none" stroke="' + fill + '" stroke-width="8.5" stroke-linecap="round" stroke-linejoin="round"/>'
        + '<rect x="-1" y="-97" width="6" height="9" fill="' + fill + '"/>'
        + '<circle cx="2.5" cy="-104" r="11" fill="' + fill + '"/>';
    }

    function mazeSvg() {
      var CM = { cx: 478, cy: 312, r0: 70, step: 24, count: 11, gapW: 22 };
      function cpolar(cx, cy, r, deg) {
        var a = deg * Math.PI / 180;
        return { x: +(cx + r * Math.cos(a)).toFixed(1), y: +(cy + r * Math.sin(a)).toFixed(1) };
      }
      function ringGapPath(r, gapCenter, gapW) {
        var s = cpolar(CM.cx, CM.cy, r, gapCenter + gapW / 2);
        var e = cpolar(CM.cx, CM.cy, r, gapCenter - gapW / 2 + 360);
        return 'M ' + s.x + ' ' + s.y + ' A ' + r + ' ' + r + ' 0 1 1 ' + e.x + ' ' + e.y;
      }
      var rings = '', spokes = '';
      for (var i = 0; i < CM.count; i++) {
        var r = CM.r0 + i * CM.step;
        var isOuter = i === CM.count - 1;
        var aligned = i >= CM.count - 3;
        rings += '<path d="' + ringGapPath(r, aligned ? 210 : 120 + ((i * 31) % 120), isOuter ? 54 : aligned ? 44 : CM.gapW) + '"/>';
        if (i < CM.count - 1 && i < CM.count - 4) {
          var rNext = CM.r0 + (i + 1) * CM.step;
          var angs = i % 2 === 0 ? [130 + ((i * 37) % 110)] : [110 + ((i * 23) % 60), 200 + ((i * 17) % 50)];
          for (var k = 0; k < angs.length; k++) {
            var a = cpolar(CM.cx, CM.cy, r, angs[k]);
            var b = cpolar(CM.cx, CM.cy, rNext, angs[k]);
            spokes += '<path d="M ' + a.x + ' ' + a.y + ' L ' + b.x + ' ' + b.y + '"/>';
          }
        }
      }
      var rOut = CM.r0 + (CM.count - 1) * CM.step;
      var funAngs = [196, 224];
      for (var fi = 0; fi < funAngs.length; fi++) {
        var fa = cpolar(CM.cx, CM.cy, rOut, funAngs[fi]);
        var fb = cpolar(CM.cx, CM.cy, rOut + 56, funAngs[fi]);
        spokes += '<path d="M ' + fa.x + ' ' + fa.y + ' L ' + fb.x + ' ' + fb.y + '"/>';
      }

      function thoughtShape(type) {
        if (type === 'marks') {
          return '<text x="-2" y="-122" font-size="36" font-weight="800" fill="rgba(11,18,32,0.55)" font-family="Tajawal, sans-serif">؟</text>'
            + '<text x="22" y="-138" font-size="24" font-weight="800" fill="rgba(11,18,32,0.4)" font-family="Tajawal, sans-serif">؟</text>'
            + '<text x="-24" y="-146" font-size="18" font-weight="800" fill="rgba(11,18,32,0.3)" font-family="Tajawal, sans-serif">؟</text>';
        }
        if (type === 'scribble') {
          return '<path d="M-16 -126 C-28 -136 -8 -150 2 -141 C12 -133 -10 -126 -3 -118 C4 -111 18 -120 13 -132" fill="none" stroke="rgba(11,18,32,0.5)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>';
        }
        return '<path d="M-26 -170 Q-26 -182 -14 -182 L14 -182 Q26 -182 26 -170 L26 -156 Q26 -144 14 -144 L-2 -144 L-8 -133 L-11 -144 L-14 -144 Q-26 -144 -26 -156 Z" fill="rgba(11,18,32,0.85)"/>'
          + '<text x="0" y="-156" font-size="22" font-weight="800" fill="#fff" text-anchor="middle" font-family="Tajawal, sans-serif">؟</text>';
      }
      var FIGS = [
        { x: 244, y: 176, s: 0.34, think: 'scribble' },
        { x: 178, y: 138, s: 0.34, think: 'bubble' },
        { x: 130, y: 90, s: 0.29, think: 'marks' }
      ];
      var DARK = 'hsl(222 47% 11%)';
      var PRIMARY = 'hsl(217 99% 53%)';
      var person = '';
      for (var p = 0; p < FIGS.length; p++) {
        var f = FIGS[p];
        var isCenter = (p === 1);
        var pColor = isCenter ? PRIMARY : DARK;
        person += '<ellipse cx="' + (f.x - 12 * f.s) + '" cy="' + (f.y + 4) + '" rx="' + (34 * f.s) + '" ry="' + (5 * f.s) + '" fill="rgba(11,18,32,0.16)" filter="url(#mazeblur)"/>'
          + '<g transform="translate(' + f.x + ' ' + f.y + ') scale(' + f.s + ')">' 
          + (isCenter ? '<g filter="url(#glowCenter)">' : '')
          + walkerShape(pColor)
          + (isCenter ? '</g>' : '')
          + thoughtShape(f.think)
          + '</g>';
      }
      return '<svg viewBox="0 0 480 620" fill="none" aria-hidden="true">'
        + '<defs>'
        + '<linearGradient id="mazefill" x1="0" y1="0" x2="1" y2="1">'
        + '<stop offset="0" stop-color="hsl(214 95% 60%)"/><stop offset="1" stop-color="hsl(216 92% 40%)"/>'
        + '</linearGradient>'
        + '<filter id="glowCenter" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="8" result="blur" /><feComposite in="SourceGraphic" in2="blur" operator="over"/></filter>'
        + '<filter id="mazeblur" x="-60%" y="-60%" width="220%" height="220%"><feGaussianBlur stdDeviation="3.2"/></filter>'
        + '<radialGradient id="mazesun" cx="478" cy="312" r="104" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="hsl(205 100% 97%)"/><stop offset="0.5" stop-color="hsl(211 100% 80%)"/><stop offset="1" stop-color="hsl(216 95% 56%)"/></radialGradient>'
        + '</defs>'
        + '<circle cx="478" cy="312" r="104" fill="hsl(210 100% 80%)" opacity="0.22" filter="url(#mazeblur)"/>'
        + '<circle cx="478" cy="312" r="50" fill="url(#mazesun)"/>'
        + '<g stroke="url(#mazefill)" stroke-width="12" fill="none" stroke-linecap="round" stroke-linejoin="round">' + rings + spokes + '</g>'
        + person
        + '</svg>';
    }
    document.getElementById('mazeHero').innerHTML = mazeSvg();

    function illus(idx, uid) {
      var hsl = STAGE_HSL[idx];
      var c = hslStr(hsl);
      var cL = lightenHsl(hsl, 13);
      var cD = lightenHsl(hsl, -9);
      var gid = 'grad-' + uid, ghid = 'gradh-' + uid;
      var defs = '<defs>'
        + '<linearGradient id="' + gid + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + cL + '"/><stop offset="1" stop-color="' + c + '"/></linearGradient>'
        + '<linearGradient id="' + ghid + '" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' + cL + '"/><stop offset="1" stop-color="' + cD + '"/></linearGradient>'
        + '</defs>';
      var G = 'url(#' + gid + ')', GH = 'url(#' + ghid + ')';
      var calDays = '';
      for (var d = 1; d <= 20; d++) {
        var col = (d - 1) % 5, row = Math.floor((d - 1) / 5);
        var cxn = 86 + col * 42, cyn = 108 + row * 25;
        if (d === 12) {
          calDays += '<circle cx="' + cxn + '" cy="' + (cyn - 4) + '" r="12" fill="' + G + '"/>'
            + '<text x="' + cxn + '" y="' + cyn + '" text-anchor="middle" font-size="13" font-weight="800" fill="#fff" font-family="Tajawal, sans-serif">' + d + '</text>';
        } else {
          calDays += '<text x="' + cxn + '" y="' + cyn + '" text-anchor="middle" font-size="13" font-weight="600" fill="' + NEUTRAL_D + '" font-family="Tajawal, sans-serif">' + d + '</text>';
        }
      }
      var scenes = [
        '<ellipse cx="160" cy="206" rx="56" ry="9" fill="' + SHADOW + '"/>' + fig(160, 202, 0.88, G, 1, 'look') + '<path d="M110 100 C92 82 130 64 138 88 C144 108 106 102 120 120 C136 140 178 122 166 96 C159 78 200 76 200 100 C200 120 172 124 182 108" fill="none" stroke="' + GH + '" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>' + '<path d="M128 86 C144 76 154 92 142 100" fill="none" stroke="' + c + '" stroke-width="3" stroke-linecap="round" opacity="0.7"/>' + '<text x="206" y="70" font-size="30" font-weight="800" fill="' + c + '" font-family="Tajawal, sans-serif">؟</text>' + '<text x="92" y="92" font-size="20" font-weight="800" fill="' + NEUTRAL_D + '" font-family="Tajawal, sans-serif">؟</text>',
        
        /* 1- صندوق الـ AI العادي + الشخص واقف أمامه */
        '<ellipse cx="160" cy="206" rx="122" ry="9" fill="' + SHADOW + '"/>' + '<path d="M96 56 V42" stroke="' + c + '" stroke-width="5" stroke-linecap="round"/>' + '<circle cx="96" cy="38" r="6" fill="' + c + '"/>' + '<rect x="40" y="56" width="112" height="92" rx="24" fill="' + G + '"/>' + '<rect x="40" y="56" width="112" height="40" rx="24" fill="#fff" fill-opacity="0.14"/>' + '<text x="96" y="118" text-anchor="middle" font-size="46" font-weight="800" fill="#fff" font-family="Tajawal, sans-serif">AI</text>' + fig(258, 202, 0.76, G, -1, 'talk') + '<g stroke="' + cD + '" stroke-width="2.6" fill="none" stroke-linecap="round" opacity="0.85"><path d="M242 132 q-7 8 0 16"/><path d="M235 127 q-12 13 0 26"/></g>' + '<rect x="176" y="92" width="44" height="30" rx="9" fill="#fff" stroke="' + cD + '" stroke-width="2.5"/>' + '<path d="M180 122 L176 134 L192 122 Z" fill="#fff" stroke="' + cD + '" stroke-width="2.5"/>' + '<g stroke="' + cD + '" stroke-width="3" stroke-linecap="round"><path d="M184 102 H212 M184 112 H204"/></g>' + '<g fill="' + cD + '"><circle cx="166" cy="104" r="4"/><circle cx="154" cy="100" r="3"/><circle cx="144" cy="98" r="2.2"/></g>',
        
        /* 2- نفس صندوق الـ AI بالضضضضبط + الشخصية (نفس اللون والحجم والاتجاه) لكن في وضعية قفز فرح مع علامة صح */
        '<ellipse cx="160" cy="206" rx="122" ry="9" fill="' + SHADOW + '"/>' + 
        '<path d="M110 130 L150 170 L230 90" fill="none" stroke="' + cL + '" stroke-width="24" stroke-linecap="round" stroke-linejoin="round" opacity="0.25"/>' +
        '<path d="M96 56 V42" stroke="' + c + '" stroke-width="5" stroke-linecap="round"/>' + 
        '<circle cx="96" cy="38" r="6" fill="' + c + '"/>' + 
        '<rect x="40" y="56" width="112" height="92" rx="24" fill="' + G + '"/>' + 
        '<rect x="40" y="56" width="112" height="40" rx="24" fill="#fff" fill-opacity="0.14"/>' + 
        '<text x="96" y="118" text-anchor="middle" font-size="46" font-weight="800" fill="#fff" font-family="Tajawal, sans-serif">AI</text>' + 
        '<circle cx="138" cy="138" r="14" fill="#fff" opacity="0.95" filter="drop-shadow(0 4px 6px rgba(0,0,0,0.1))"/>' +
        '<path d="M133 138 L137 142 L144 134" fill="none" stroke="' + c + '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>' +
        fig(258, 175, 0.76, G, -1, 'jump_clear') + 
        '<path d="M245 200 v8 M258 198 v12 M271 200 v8" stroke="' + cL + '" stroke-width="3" stroke-linecap="round"/>',

        '<ellipse cx="160" cy="208" rx="128" ry="9" fill="' + SHADOW + '"/>' + '<path d="M113 60 L183 26 L253 60 Z" fill="' + NEUTRAL_D + '" fill-opacity="0.4"/>' + '<rect x="111" y="60" width="144" height="8" rx="2" fill="' + NEUTRAL_D + '" fill-opacity="0.5"/>' + '<g fill="' + NEUTRAL + '" fill-opacity="0.6"><rect x="123" y="70" width="11" height="58"/><rect x="151" y="70" width="11" height="58"/><rect x="204" y="70" width="11" height="58"/><rect x="232" y="70" width="11" height="58"/></g>' + '<rect x="111" y="128" width="144" height="8" rx="2" fill="' + NEUTRAL_D + '" fill-opacity="0.5"/>' + fig(150, 190, 0.58, NEUTRAL, 1, 'stand') + '<rect x="158" y="160" width="20" height="15" rx="2.5" fill="' + NEUTRAL_D + '"/><path d="M164 160 q4 -5 8 0" fill="none" stroke="' + NEUTRAL_D + '" stroke-width="2"/>' + fig(216, 190, 0.64, G, 1, 'stand') + '<rect x="190" y="158" width="22" height="16" rx="2.5" fill="' + cD + '"/><path d="M197 158 q5 -5 9 0" fill="none" stroke="' + cD + '" stroke-width="2.2"/>' + '<circle cx="216" cy="60" r="14" fill="' + GH + '"/>' + '<circle cx="216" cy="60" r="14" fill="none" stroke="#fff" stroke-opacity="0.3" stroke-width="2"/>' + '<path d="M209 60 L214 65 L223 54" fill="none" stroke="#fff" stroke-width="3.6" stroke-linecap="round" stroke-linejoin="round"/>' + '<ellipse cx="66" cy="210" rx="28" ry="7" fill="' + SHADOW + '"/>' + fig(66, 210, 0.8, NEUTRAL_D, 1, 'stand'),
        '<ellipse cx="160" cy="208" rx="108" ry="9" fill="' + SHADOW + '"/>' + '<g fill="' + cD + '"><rect x="96" y="30" width="7" height="22" rx="3.5"/><rect x="217" y="30" width="7" height="22" rx="3.5"/></g>' + '<rect x="60" y="42" width="200" height="160" rx="14" fill="#fff" stroke="' + NEUTRAL + '" stroke-width="3"/>' + '<path d="M60 56 a14 14 0 0 1 14 -14 H246 a14 14 0 0 1 14 14 V78 H60 Z" fill="' + G + '"/>' + '<text x="160" y="68" text-anchor="middle" font-size="16" font-weight="800" fill="#fff" font-family="Tajawal, sans-serif">يونيو ٢٠٢٦</text>' + '<path d="M60 90 H260" stroke="' + NEUTRAL + '" stroke-opacity="0.6" stroke-width="2"/>' + calDays,
        '<ellipse cx="160" cy="208" rx="132" ry="9" fill="' + SHADOW + '"/>' + fig(86, 188, 0.66, G, 1, 'sit') + fig(234, 188, 0.66, NEUTRAL, -1, 'sit') + '<path d="M96 150 L224 150 L240 166 L80 166 Z" fill="' + GH + '"/>' + '<rect x="80" y="166" width="160" height="9" fill="' + cD + '"/>' + '<g fill="' + cD + '"><rect x="92" y="175" width="9" height="30"/><rect x="219" y="175" width="9" height="30"/></g>' + '<g transform="rotate(-7 150 150)"><rect x="126" y="134" width="38" height="26" rx="2" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2"/><path d="M132 142 H158 M132 148 H154 M132 154 H156" stroke="' + NEUTRAL_D + '" stroke-width="1.8" stroke-linecap="round"/></g>' + '<g transform="rotate(9 178 152)"><rect x="160" y="138" width="34" height="24" rx="2" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2"/><path d="M166 146 H188 M166 152 H184" stroke="' + NEUTRAL_D + '" stroke-width="1.8" stroke-linecap="round"/></g>' + '<g><rect x="50" y="56" width="58" height="30" rx="8" fill="#fff" stroke="' + cD + '" stroke-width="2"/><path d="M66 86 L62 98 L80 86 Z" fill="#fff" stroke="' + cD + '" stroke-width="2"/><g stroke="' + cD + '" stroke-width="2.6" stroke-linecap="round"><path d="M60 68 H98 M60 77 H88"/></g></g>' + '<g><rect x="206" y="54" width="58" height="30" rx="8" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2"/><path d="M248 84 L252 96 L234 84 Z" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2"/><g stroke="' + NEUTRAL_D + '" stroke-width="2.6" stroke-linecap="round"><path d="M216 66 H254 M226 75 H254"/></g></g>',
        '<ellipse cx="160" cy="208" rx="120" ry="9" fill="' + SHADOW + '"/>' + '<rect x="74" y="74" width="84" height="116" rx="4" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2" transform="rotate(-8 116 132)"/>' + '<rect x="82" y="68" width="84" height="116" rx="4" fill="#fff" stroke="' + NEUTRAL + '" stroke-width="2" transform="rotate(-3 124 126)"/>' + '<path d="M150 66 L252 66 Q260 66 260 74 L260 192 L150 192 Z" fill="' + cL + '"/>' + '<rect x="162" y="74" width="86" height="110" rx="3" fill="#fff" stroke="' + NEUTRAL + '" stroke-width="1.6"/>' + '<rect x="158" y="80" width="86" height="110" rx="3" fill="#fff" stroke="' + NEUTRAL + '" stroke-width="1.6"/>' + '<rect x="154" y="86" width="86" height="104" rx="3" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2"/>' + '<g stroke="' + NEUTRAL_D + '" stroke-width="2.4" stroke-linecap="round" opacity="0.6"><path d="M162 100 H230 M162 110 H234 M162 120 H218 M162 130 H230 M162 140 H210 M162 150 H226 M162 160 H204"/></g>' + '<circle cx="214" cy="166" r="14" fill="none" stroke="' + cD + '" stroke-width="2.5" opacity="0.7"/>' + '<path d="M207 166 L212 171 L221 160" fill="none" stroke="' + cD + '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" opacity="0.8"/>' + '<path d="M150 66 L150 192 L118 192 Q110 192 110 184 L110 88 Q110 80 118 80 L150 80 Z" fill="' + G + '"/>' + '<rect x="116" y="104" width="28" height="42" rx="3" fill="#fff" fill-opacity="0.9"/>' + '<g stroke="' + cD + '" stroke-width="2.4" stroke-linecap="round"><path d="M122 116 H138 M122 125 H134 M122 134 H138"/></g>' + '<rect x="166" y="56" width="42" height="26" rx="2" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2" transform="rotate(-13 187 69)"/>' + '<rect x="196" y="54" width="42" height="26" rx="2" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2" transform="rotate(8 217 67)"/>',
        '<ellipse cx="160" cy="206" rx="106" ry="9" fill="' + SHADOW + '"/>' + '<rect x="92" y="64" width="118" height="150" rx="8" fill="#fff" stroke="' + NEUTRAL + '" stroke-width="3" transform="rotate(-9 151 139)"/>' + '<rect x="100" y="58" width="118" height="150" rx="8" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="3" transform="rotate(-3 159 133)"/>' + '<rect x="100" y="50" width="118" height="150" rx="8" fill="#fff" stroke="' + cD + '" stroke-width="3" transform="rotate(5 159 125)"/>' + '<g stroke="' + c + '" stroke-width="3.4" stroke-linecap="round" opacity="0.85" transform="rotate(5 159 125)"><path d="M118 82 H200 M118 100 H194 M118 118 H200 M118 136 H186 M118 154 H196"/></g>' + '<g stroke="' + GH + '" stroke-width="3.6" stroke-linecap="round"><path d="M222 60 V78 M213 69 H231"/></g>' + '<circle cx="222" cy="69" r="2.6" fill="' + c + '"/>',
        '<ellipse cx="160" cy="204" rx="96" ry="9" fill="' + SHADOW + '"/>' + '<rect x="116" y="56" width="96" height="48" rx="6" fill="' + cL + '" stroke="' + cD + '" stroke-width="2.5" transform="rotate(-13 164 80)"/>' + '<rect x="126" y="64" width="96" height="48" rx="6" fill="#fff" stroke="' + NEUTRAL_D + '" stroke-width="2.5" transform="rotate(-4 174 88)"/>' + '<g transform="rotate(-4 174 88)"><circle cx="174" cy="88" r="12" fill="none" stroke="' + c + '" stroke-width="3"/><text x="174" y="94" text-anchor="middle" font-size="16" font-weight="800" fill="' + c + '" font-family="Tajawal, sans-serif">$</text></g>' + '<rect x="84" y="108" width="152" height="86" rx="14" fill="' + G + '"/>' + '<path d="M84 130 H236" stroke="#fff" stroke-opacity="0.18" stroke-width="2"/>' + '<rect x="182" y="142" width="54" height="34" rx="10" fill="' + cD + '"/>' + '<circle cx="209" cy="159" r="7" fill="#fff" fill-opacity="0.9"/>'
      ];
      return '<svg viewBox="0 0 320 220" fill="none" aria-hidden="true">' + defs + scenes[idx] + '</svg>';
    }

    var stagesEl = document.getElementById('stages');
    var stageRefs = [];
    STAGES.forEach(function (s, idx) {
      var isRight = idx % 2 === 0;
      var accent = stageColor(idx);
      var el = document.createElement('div');
      el.className = 'stage reveal ' + (isRight ? 'right' : 'left');
      el.style.setProperty('--accent', accent);
      var num = String(idx + 1).padStart(2, '0');
      el.innerHTML =
        '<div class="caption">' +
          '<div class="illus-m" aria-hidden="true">' + illus(idx, 'm' + idx) + '</div>' +
          '<div class="head"><span class="num">' + num + '</span><h3 class="title">' + s.t + '</h3></div>' +
          '<p class="desc">' + s.d + '</p>' +
        '</div>' +
        '<div class="node">' +
          '<span class="glow"></span>' +
          '<div class="node-inner">' +
            '<span class="ring" aria-hidden="true">' + s.i + '</span>' +
            '<span class="ring-active" aria-hidden="true">' + s.i + '</span>' +
          '</div>' +
        '</div>' +
        '<div class="illus" aria-hidden="true">' + illus(idx, 'd' + idx) + '</div>';
      stagesEl.appendChild(el);
      stageRefs.push({
        accent: accent,
        num: el.querySelector('.num'),
        glow: el.querySelector('.node .glow'),
        inner: el.querySelector('.node-inner'),
        ringActive: el.querySelector('.ring-active'),
      });
    });

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('in-view');
          if (!e.target.classList.contains('repeat-anim')) { io.unobserve(e.target); }
        } else {
          if (e.target.classList.contains('repeat-anim')) { e.target.classList.remove('in-view'); }
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });
    document.querySelectorAll('.reveal').forEach(function (n) { io.observe(n); });

    var introPath = document.getElementById('introPath');
    var journeyDraw = document.getElementById('journeyDraw');
    var journeySection = document.getElementById('journey');
    var traveller = document.getElementById('traveller');
    var introLen = introPath.getTotalLength();
    var journeyLen = journeyDraw.getTotalLength();
    var N1 = STAGES.length - 1;

    introPath.style.strokeDasharray = introLen;
    journeyDraw.style.strokeDasharray = journeyLen;
    introPath.style.strokeDashoffset = introLen;
    journeyDraw.style.strokeDashoffset = journeyLen;

    function clamp(v) { return Math.max(0, Math.min(1, v)); }

    function onScroll() {
      var sy = window.scrollY;
      var vh = window.innerHeight;

      introPath.style.strokeDashoffset = introLen * (1 - clamp(sy / vh));

      var rect = journeySection.getBoundingClientRect();
      var top = rect.top + sy;
      var h = rect.height;
      var jp = clamp((sy - top + vh / 2) / h);
      journeyDraw.style.strokeDashoffset = journeyLen * (1 - jp);

      var tLeft = interp(jp, [0, 0.167, 0.333, 0.5, 0.667, 0.833, 1], [50, 78, 22, 78, 22, 78, 50]);
      traveller.style.top = (1 + jp * 98) + '%';
      traveller.style.left = tLeft + '%';
      traveller.style.setProperty('--tc', travColor(jp));

      for (var i = 0; i < stageRefs.length; i++) {
        var r = stageRefs[i];
        var t = i / N1;
        var d = Math.abs(jp - t);
        var close = Math.max(0, 1 - d / 0.09);
        var act = Math.max(0, 1 - d / 0.06);
        r.num.style.opacity = (0.16 + 0.84 * close).toFixed(3);
        r.num.style.textShadow = close > 0.04 ? ('0 0 ' + (18 * close).toFixed(1) + 'px ' + r.accent) : 'none';
        r.glow.style.opacity = (0.85 * close).toFixed(3);
        r.ringActive.style.opacity = act.toFixed(3);
        r.inner.style.transform = 'scale(' + (1 + 0.14 * act).toFixed(3) + ')';
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    onScroll();

    /* =========================================
       سكريبت تفعيل النوافذ المنبثقة (Modals)
       ========================================= */
    var MODAL_DATA = {
    


   privacy: {
  title: "سياسة الخصوصية",
  content: `
    <h3>1. مقدمة</h3>
    <p>في منصة ميزان، نلتزم التزامًا كاملًا بحماية خصوصية المستخدمين وسرية بياناتهم، إدراكًا لحساسية المعلومات القانونية وأهمية الحفاظ عليها بأعلى درجات الأمان.</p>

    <h3>2. سرية البيانات</h3>
    <p>جميع البيانات التي يقوم المستخدم بإدخالها أو رفعها داخل المنصة تُعد ملكًا حصريًا له، ويتم التعامل معها بسرية تامة. لا يتم الاطلاع عليها أو مشاركتها أو استخدامها لأي غرض خارج نطاق تقديم الخدمة، تحت أي ظرف من الظروف.</p>

    <h3>3. المراسلات والتواصل</h3>
    <p>المراسلات بين المستخدمين والمحامين داخل المنصة تتم في بيئة خاصة ومشفّرة. لا يحق لأي طرف غير معني بالجلسة الاطلاع على هذه المراسلات، بما في ذلك إدارة المنصة، وذلك حفاظًا على الخصوصية التامة.</p>

    <h3>4. الاستثناءات القانونية</h3>
    <p>لا يتم كشف أي بيانات أو مراسلات إلا في حالات محددة وموثقة قانونيًا، مثل وجود بلاغ رسمي أو طلب قانوني ملزم، وفي هذه الحالة يتم التعامل مع البيانات بالقدر اللازم فقط وبإشراف الإدارة المختصة لضمان حماية الحقوق والعدالة.</p>

    <h3>5. التزام المنصة</h3>
    <p>تلتزم منصة ميزان بتطبيق أعلى معايير الحماية التقنية والتنظيمية لضمان سرية البيانات، ومنع أي وصول غير مصرح به، مع مراجعة مستمرة لأنظمة الأمان.</p>
  `
},




   terms: {
  title: "الشروط والأحكام",
  content: `
    <p>أهلاً بك في منصة ميزان. باستخدامك للمنصة وخدماتها، فإنك تقر وتوافق على الالتزام بالشروط والأحكام التالية:</p>

    <h3>1. طبيعة الخدمة</h3>
    <p>الخدمات المقدمة عبر المساعد الذكي تُعد خدمات استرشادية أولية، ولا تُغني بأي حال من الأحوال عن الاستشارة القانونية النهائية المقدمة من المحامي المختص والمعتمد.</p>

    <h3>2. دقة المعلومات</h3>
    <p>يلتزم المستخدم بتقديم معلومات صحيحة ودقيقة عند استخدام خدمات المنصة، بما في ذلك حجز المواعيد أو رفع المستندات، ويتحمل المسؤولية الكاملة عن أي بيانات غير صحيحة.</p>

    <h3>3. الاستخدام المشروع</h3>
    <p>يُحظر استخدام المنصة لأي أغراض غير قانونية أو مخالفة للأنظمة المعمول بها، كما يُمنع رفع أي ملفات تحتوي على فيروسات أو برمجيات خبيثة أو أي محتوى ضار.</p>

    <h3>4. التعديلات على الشروط</h3>
    <p>تحتفظ منصة ميزان بحق تعديل أو تحديث هذه الشروط والأحكام في أي وقت بما يتماشى مع التطويرات التقنية أو القانونية، ويتم إشعار المستخدمين بأي تغييرات جوهرية عبر المنصة.</p>

    <h3>5. قبول الشروط</h3>
    <p>يُعد استمرارك في استخدام المنصة بعد أي تحديث بمثابة موافقة صريحة على الشروط والأحكام المعدلة.</p>
  `
},


contact: {
  title: "تواصل معنا",
  content: `
    <p>نحن هنا لخدمتك في أي وقت. يمكنك التواصل معنا عبر الاتصال الهاتفي أو عبر واتساب على الرقم التالي:</p>

    <ul>
      <li><strong>البريد الإلكتروني:</strong> mizan@mizan.ly</li>
      <li><strong>رقم الهاتف / واتساب:</strong> 218911557203+</li>
      <li><strong>أوقات العمل:</strong> من الأحد إلى الخميس، من 9:00 صباحاً حتى 5:00 مساءً.</li>
    </ul>

    <p>نسعد دائماً بتواصلك واستقبال استفساراتك وتقديم المساعدة لك في أي وقت.</p>
  `
},


     about: {
  title: "عن ميزان",
  content: `
    <p><strong>ميزان</strong> هو نظام قانوني ذكي متكامل يهدف إلى تطوير تجربة الوصول إلى الخدمات القانونية عبر رقمنة الإجراءات وتسهيلها لكل من المستخدمين والمحامين.</p>

    <p>يجمع النظام بين تقنيات الذكاء الاصطناعي المتقدمة (بما في ذلك المعالجة اللغوية الطبيعية وتقنيات RAG) وبين الخبرة القانونية، لتقديم تجربة رقمية ذكية تشمل مساعد قانوني تفاعلي، مكتبة مستندات آمنة، ونظام حجز وإدارة مواعيد سلس.</p>

    <p>تسعى منصة ميزان إلى جعل القانون أكثر وضوحاً وسهولة، من خلال تقليل التعقيد، وتوفير أدوات ذكية تساعد المستخدم على فهم وضعه القانوني واتخاذ القرار المناسب بثقة وشفافية.</p>

    <p>رؤيتنا هي بناء منظومة قانونية رقمية حديثة تجعل الخدمات القانونية أقرب، أوضح، وأكثر أماناً للجميع.</p>
  `
}
    };

    function openModal(type) {
      var data = MODAL_DATA[type];
      if(!data) return;
      document.getElementById('modalTitle').innerHTML = data.title;
      document.getElementById('modalContent').innerHTML = data.content;
      document.getElementById('infoModal').classList.add('is-open');
      document.body.style.overflow = 'hidden'; 
    }

    function closeModal() {
      document.getElementById('infoModal').classList.remove('is-open');
      document.body.style.overflow = ''; 
    }

    document.getElementById('modalOverlay').addEventListener('click', closeModal);
  </script>
</body>
</html>

@endsection