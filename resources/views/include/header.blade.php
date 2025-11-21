<header class="main-header">
    <div class="logo">
        <h1>عيادة كيان لطب الأسنان</h1>
    <p>أفضل رعاية صحية لأسنانك</p>
    </div>

    <nav>

         <a href="#services">خدماتنا</a>
    <a href="#team">الفريق الطبي</a>
    <a href="#contact">اتصل بنا</a>
      @auth
        <a href="{{ route('Logout') }}">تسجيل خروج </a>
    @endauth

    @guest
        <a href="{{ route('Registration') }}">تسجيل دخول</a>
    @endguest
</nav>
</header>

<style>

    nav { display:flex; justify-content:center; background-color:#023e8a; padding:10px 0; }
        nav a { color:white; text-decoration:none; margin:0 15px; font-weight:bold; }
        nav a:hover { text-decoration: underline; }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* الهيدر المثبّت فوق */
    .main-header {
        width: 100%;
        background: #023e8a;
        padding: 20px 50px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #2779bd;

        position: fixed;   /* 👈 أهم سطر */
        top: 0;
        left: 0;
        z-index: 1000;
    }

    /* باش المحتوى ما يتغطّاش بالهيدر */
    body {
        padding-top: 90px; /* 👈 عدّلها حسب حجم الهيدر */
    }

    .logo-link {
        color: white;
        text-decoration: none;
        font-size: 24px;
        font-weight: bold;
        white-space: nowrap;
    }

    .nav-links {
        display: flex;
        gap: 50px;
    }

    .nav-links a {
        color: white;
        font-size: 18px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s;
    }

    .nav-links a:hover {
        opacity: 0.8;
    }
</style>
