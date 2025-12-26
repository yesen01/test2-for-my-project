@extends('layout')
@section('title', "Home Page")

@section('content')
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عيادة كيان لطب الأسنان</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; direction: rtl; background-color: #f7f7f7; }
        header { background-color: #0077b6; color:white; padding:20px; text-align:center; }
        nav { display:flex; justify-content:center; background-color:#023e8a; padding:10px 0; }
        nav a { color:white; text-decoration:none; margin:0 15px; font-weight:bold; }
        nav a:hover { text-decoration: underline; }
        .hero { text-align:center; padding:60px 20px;
                background-image:url('https://images.unsplash.com/photo-1588776814546-859414a0f3f0');
                background-size:cover; background-position:center; color:white; }
        .hero h1 { font-size:48px; margin-bottom:20px; }
        .hero p { font-size:20px; }
        section { padding:40px 20px; }
        .services, .team { display:flex; justify-content:center; flex-wrap:wrap; gap:20px; }
        .card { background-color:white; padding:20px; width:250px; border-radius:10px; box-shadow:0 4px 6px rgba(0,0,0,0.1); text-align:center; }
        .card h3 { margin-bottom:10px; color:#023e8a; }
        footer { background-color:#023e8a; color:white; text-align:center; padding:20px; margin-top:40px; }
        #contact p { font-size:20px; margin-top:20px; color:#0077b6; }
        #contact strong { font-size:22px; }
    </style>
</head>
<body>

<header>
    <h1>عيادة كيان لطب الأسنان</h1>
    <p>أفضل رعاية صحية لأسنانك</p>
</header>

<nav>
    <a href="#services">خدماتنا</a>
    <a href="#team">الفريق الطبي</a>
    <a href="#contact">اتصل بنا</a>
</nav>

<!-- Hero Section -->
<section class="hero">
    <h1>ابتسامتك تهمنا!</h1>
    <p>نقدم أفضل خدمات طب الأسنان من علاج وتسوية وزراعة بأسلوب احترافي.</p>
</section>

<!-- Services Section -->
<section id="services">
    <h2 style="text-align:center; color:#023e8a;">خدماتنا</h2>
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

<!-- Team Section -->
<section id="team">
    <h2 style="text-align:center; color:#023e8a;">الفريق الطبي</h2>
    <div class="team">
        <div class="card">
            <h3>د. إبراهيم الجروشي</h3>
            <p>اختصاص جراحة وزراعة الأسنان</p>
        </div>
        <div class="card">
            <h3>د. ليلى محمد</h3>
            <p>اختصاص تجميل الأسنان وتقويم</p>
        </div>
        <div class="card">
            <h3>د. سامي علي</h3>
            <p>أخصائي طب أسنان عام</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" style="background-color:#f0f0f0; text-align:center; padding:40px;">
    <h2 style="color:#023e8a;">اتصل بنا</h2>
    <p>للتواصل معنا، اتصل على الرقم:</p>
    <strong>+218 91 234 5678</strong>
</section>

<footer>
    <p>© 2025 عيادة كيان لطب الأسنان. جميع الحقوق محفوظة.</p>
</footer>

</body>
</html>
@endsection
