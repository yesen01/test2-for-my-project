@extends('layout')

@section('title', 'Home Page')

@section('content')

<header class="main-header">
    <div class="logo">
        <h1>مركز كيان لطب وجراحة الأسنان</h1>
        <p>أفضل رعاية صحية لأسنانك</p>
    </div>

    <nav>
    <a href="#services">خدماتنا</a>
    <a href="#team">الفريق الطبي</a>
    <a href="#contact">اتصل بنا</a>

    {{-- إذا كان الزائر ضيفاً (غير مسجل دخول) --}}
    @guest
        <a href="{{ route('login') }}" style="background: rgba(255,255,255,0.2); border: 1px solid #fff;">تسجيل دخول</a>
        <a href="{{ route('Registration') }}" style="background: var(--accent); color: white;">إنشاء حساب</a>
    @endguest

    {{-- إذا كان المستخدم مسجلاً دخوله بالفعل --}}
    @auth
        <a href="{{ route('patient.dashboard') }}" style="background: var(--accent); color: white;">
            <i class="fa-solid fa-user"></i> لوحة التحكم
        </a>
    @endauth
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
            <h3>د. عاصم البرعصي</h3>
            <p>اختصاص تجميل وتقويم الأسنان</p>
        </div>
        <div class="card">
            <h3>د. محمد اسامة</h3>
            <p>أخصائي طب أسنان عام</p>
        </div>
    </div>
</section>

<section id="contact" class="contact">
    <h2>اتصل بنا</h2>
    <p>للتواصل معنا:</p>
    <strong dir="ltr" style="display: inline-block;">+218 92 242 1289</strong>
</section>

<footer>
    <p>© 2025 مركز كيان لطب الأسنان. جميع الحقوق محفوظة.</p>
</footer>

{{-- ===== CSS ===== --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap'); // خط عربي

:root{
    --primary: #0f766e;
    --accent: #14b8a6;
    --bg: #f4f6f9;
    --card: #ffffff;
    --text: #12202b;
    --muted: #5b6b7a;
    --radius: 12px;
    --container: 1100px;
}

*{box-sizing:border-box}
html,body{height:100%} /* للتأكد من أن الجسم والصفحة يأخذان كامل الارتفاع */

body {
    font-family: 'Cairo', Arial, sans-serif;
    margin: 0;
    padding-top: 100px;
    direction: rtl;
    background: linear-gradient(180deg, var(--bg) 0%, #f0fdfa 100%);
    color: var(--text);
    line-height: 1.6;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
}/* جعل اتجاه النص من اليمين لليسار */

/* Header */
.main-header {
    position: fixed;
    top: 0;
    width: 100%;

    background: rgba(15, 118, 110, 0.9); /* لون زيتي شفاف */
    padding: 14px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(6px);
    box-shadow: 0 8px 24px rgba(15, 118, 110, 0.15);
}/* خلفية شفافة مع تأثير الضبابية */

.logo h1 {/* شعار الموقع */
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: #fff;
}/* لون نص العنوان أبيض */

.logo p {/* وصف الشعار */
    margin: 0;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.92);
}/* لون نص الوصف أبيض شفاف */

nav {/* قائمة التنقل */
    display: flex;
    align-items: center;
    gap: 8px;
}/* تباعد بين الروابط */

nav a {/* روابط التنقل */
    color: #fff;
    margin: 0 6px;
    padding: 8px 12px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: transform .18s ease, background .18s ease, box-shadow .18s ease;/* تأثيرات الانتقال */
    outline: none;
}/* إزالة التسطير الافتراضي للروابط */

nav a:hover { /* تأثير التمرير فوق الرابط */
    background: rgba(255,255,255,0.12);
    transform: translateY(-3px);
}/* رفع الرابط قليلاً عند التمرير */

nav a:focus {/* تأثير التركيز على الرابط */
    box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.2);
}/* إضافة ظل عند التركيز */

/* Hero */
.hero {
    text-align: center;
    padding: 110px 20px;
    /* دمج تدرج زيتي مع خلفية الصورة */
background-image: linear-gradient(180deg, rgba(15, 118, 110, 0.5), rgba(0,0,0,0.2)), url('{{ asset("images/t2.jpeg") }}');    background-size: cover;
    background-position: center;
    color: white;
    box-shadow: inset 0 -80px 120px rgba(0,0,0,0.15);
}/* خلفية بطبقة تدرج لونية */

.hero h1 {/* عنوان الهيرو */
    margin: 0 0 10px;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: .3px;
}/* حجم الخط وتباعد الحروف */

.hero p {/* وصف الهيرو */
    margin: 0;
    font-size: 1.05rem;
    max-width: 820px;
    margin-inline: auto;
    color: rgba(255,255,255,0.95);
}/* لون نص الوصف أبيض شفاف */

/* Sections */
section {/* أقسام الصفحة */
    max-width: var(--container);
    margin-inline: auto;
    padding: 48px 20px;
}/* تحديد العرض الأقصى والمحاذاة الوسطى */

.section-title {
    text-align: center;
    color: var(--primary);
    margin-bottom: 26px;
    font-size: 1.45rem;
    font-weight: 700;
}/* عنوان الأقسام باللون الزيتي */

/* Cards layout */
.services,
.team {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
}/* تخطيط البطاقات في صفوف مع تباعد */

.card {
    background: var(--card);
    padding: 22px;
    width: 260px;
    border-radius: var(--radius);
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.08);
    text-align: center;
    transition: transform .22s ease, box-shadow .22s ease;
    color: var(--text);
    border: 1px solid rgba(15, 118, 110, 0.05);
}/* تصميم البطاقات */

.card h3 {/* عنوان البطاقة */
    margin-top: 0;
    margin-bottom: 8px;
    font-size: 1.05rem;
    color: var(--primary);
}/* لون عنوان البطاقة زيتي */

.card p {/* وصف البطاقة */
    margin: 0;
    color: var(--muted);
    font-size: 0.95rem;
}/* لون نص الوصف خافت */

.card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 22px 48px rgba(15, 118, 110, 0.15);
}/* تأثير التحويم على البطاقات */

/* Contact & Footer */
.contact {/* قسم الاتصال */
    background: #ffffff;
    text-align: center;
    padding: 40px 20px;
    border-radius: 14px;
    border: 1px solid rgba(15, 118, 110, 0.08);
}/* خلفية بيضاء مع حدود خفيفة */

.contact h2 { margin-top: 0; color: var(--primary); }/* عنوان قسم الاتصال */

footer {/* تذييل الصفحة */
    /* تدرج طولي زيتي للهوية البصرية */
    background: linear-gradient(90deg, var(--primary), #0d635d);
    color: white;
    text-align: center;
    padding: 18px;
    margin-top: 30px;
    border-radius: 8px;
}/* تدرج لوني في الخلفية */

/* Responsive */
@media (max-width: 900px) {/* شاشات التابلت */
    :root { --container: 95%; }
    body { padding-top: 90px; }
    .main-header { padding: 12px 18px; }
    .logo h1 { font-size: 1rem; }
    .hero { padding: 80px 16px; }
    .services, .team { gap: 16px; }
}/* تحسين التخطيط على الشاشات المتوسطة */

@media (max-width: 640px) {/* شاشات الهواتف */
    .services, .team { flex-direction: column; align-items: center; }
    .card { width: 92%; max-width: 420px; }
    .hero h1 { font-size: 1.6rem; }
    nav { gap: 6px; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    nav a { white-space: nowrap; }
}/* تحسين التنقل على الشاشات الصغيرة */
</style>

@endsection
