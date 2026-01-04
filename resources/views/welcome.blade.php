@extends('layout')

@section('title', 'Home Page')

@section('content')

<header class="main-header">
    <div class="logo">
        <h1>عيادة كيان لطب الأسنان</h1>
        <p>أفضل رعاية صحية لأسنانك</p>
    </div>

    <nav>
        <a href="#services">خدماتنا</a>
        <a href="#team">الفريق الطبي</a>
        <a href="#contact">اتصل بنا</a>

        @guest
            <a href="{{ route('Registration') }}">تسجيل دخول</a>
        @endguest
    </nav>
</header>

<section class="hero">
    <h1>ابتسامتك تهمنا!</h1>
    <p>نقدم أفضل خدمات طب الأسنان من علاج وتسوية وزراعة بأسلوب احترافي.</p>
</section>

<section id="services">
    <h2 class="section-title">خدماتنا</h2>
    <div class="services">
        <div class="card">
            <h3>جراحة الأسنان</h3>
            <p>نقوم بكل العمليات الجراحية الدقيقة لأسنانك بأمان واحترافية.</p>
        </div>
        <div class="card">
            <h3>زراعة الأسنان</h3>
            <p>استعادة ابتسامتك بأسنان طبيعية تدوم طويلاً.</p>
        </div>
        <div class="card">
            <h3>تجميل الأسنان</h3>
            <p>تبييض وتقويم الأسنان لتحصل على ابتسامة جذابة.</p>
        </div>
    </div>
</section>

<section id="team">
    <h2 class="section-title">الفريق الطبي</h2>
    <div class="team">
        <div class="card">
            <h3>د. إبراهيم الجروشي</h3>
            <p>اختصاص جراحة وزراعة الأسنان</p>
        </div>
        <div class="card">
            <h3>د. ليلى محمد</h3>
            <p>اختصاص تجميل وتقويم الأسنان</p>
        </div>
        <div class="card">
            <h3>د. سامي علي</h3>
            <p>أخصائي طب أسنان عام</p>
        </div>
    </div>
</section>

<section id="contact" class="contact">
    <h2>اتصل بنا</h2>
    <p>للتواصل معنا:</p>
    <strong>+218 91 234 5678</strong>
</section>

<footer>
    <p>© 2025 عيادة كيان لطب الأسنان. جميع الحقوق محفوظة.</p>
</footer>

{{-- ===== CSS ===== --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');

:root{
    --primary: #023e8a;
    --accent: #00b4d8;
    --bg: #f7f9ff;
    --card: #ffffff;
    --text: #12202b;
    --muted: #5b6b7a;
    --radius: 12px;
    --container: 1100px;
}

*{box-sizing:border-box}
html,body{height:100%}

body {
    font-family: 'Cairo', Arial, sans-serif;
    margin: 0;
    padding-top: 100px; /* header offset */
    direction: rtl;
    background: linear-gradient(180deg, var(--bg) 0%, #eef6ff 100%);
    color: var(--text);
    line-height: 1.6;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
}

/* Header */
.main-header {
    position: fixed;
    top: 0;
    width: 100%;
    background: rgba(2,62,138,0.85);
    padding: 14px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(6px);
    box-shadow: 0 8px 24px rgba(2,62,138,0.12);
}

.logo h1 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: #fff;
}

.logo p {
    margin: 0;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.92);
}

nav {
    display: flex;
    align-items: center;
    gap: 8px;
}

nav a {
    color: #fff;
    margin: 0 6px;
    padding: 8px 12px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
    outline: none;
}

nav a:hover {
    background: rgba(255,255,255,0.08);
    transform: translateY(-3px);
}

nav a:focus {
    box-shadow: 0 0 0 4px rgba(2,62,138,0.12);
}

/* Hero */
.hero {
    text-align: center;
    padding: 110px 20px;
    background-image: linear-gradient(180deg, rgba(2,62,138,0.45), rgba(0,0,0,0.12)), url('https://images.unsplash.com/photo-1588776814546-859414a0f3f0?q=80&w=1600&auto=format&fit=crop&s=0c9b0c0b6c0f8e0d');
    background-size: cover;
    background-position: center;
    color: white;
    box-shadow: inset 0 -80px 120px rgba(0,0,0,0.15);
}

.hero h1 {
    margin: 0 0 10px;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: .3px;
}

.hero p {
    margin: 0;
    font-size: 1.05rem;
    max-width: 820px;
    margin-inline: auto;
    color: rgba(255,255,255,0.95);
}

/* Sections */
section {
    max-width: var(--container);
    margin-inline: auto;
    padding: 48px 20px;
}

.section-title {
    text-align: center;
    color: var(--primary);
    margin-bottom: 26px;
    font-size: 1.45rem;
    font-weight: 700;
}

/* Cards layout */
.services,
.team {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
}

.card {
    background: var(--card);
    padding: 22px;
    width: 260px;
    border-radius: var(--radius);
    box-shadow: 0 10px 30px rgba(18,38,63,0.06);
    text-align: center;
    transition: transform .22s ease, box-shadow .22s ease;
    color: var(--text);
    border: 1px solid rgba(3,46,89,0.04);
}

.card h3 {
    margin-top: 0;
    margin-bottom: 8px;
    font-size: 1.05rem;
    color: var(--primary);
}

.card p {
    margin: 0;
    color: var(--muted);
    font-size: 0.95rem;
}

.card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 22px 48px rgba(18,38,63,0.12);
}

/* Contact & Footer */
.contact {
    background: #ffffff;
    text-align: center;
    padding: 40px 20px;
    border-radius: 14px;
    border: 1px solid rgba(2,62,138,0.06);
}

.contact h2 { margin-top: 0; color: var(--primary); }

footer {
    background: linear-gradient(90deg, var(--primary), #005f9e);
    color: white;
    text-align: center;
    padding: 18px;
    margin-top: 30px;
    border-radius: 8px;
}

/* Responsive */
@media (max-width: 900px) {
    :root { --container: 95%; }
    body { padding-top: 90px; }
    .main-header { padding: 12px 18px; }
    .logo h1 { font-size: 1rem; }
    .hero { padding: 80px 16px; }
    .services, .team { gap: 16px; }
}

@media (max-width: 640px) {
    .services, .team { flex-direction: column; align-items: center; }
    .card { width: 92%; max-width: 420px; }
    .hero h1 { font-size: 1.6rem; }
    nav { gap: 6px; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    nav a { white-space: nowrap; }
}
</style>

@endsection
