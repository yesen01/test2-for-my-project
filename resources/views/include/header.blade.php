<header class="main-header">
    <div class="logo">
        <a href="/" class="logo-link">Kayan Dental Clinic</a>
    </div>

    <nav class="nav-links">
        <a href="/">Home</a>
        <a href="/about">About</a>
        <a href="/services">Services</a>
        <a href="/contact">Contact</a>

        @auth
            <a href="{{ route('Logout') }}">Logout</a>
        @endauth

        @guest
            <a href="{{ route('Login') }}">Login</a>
        @endguest
    </nav>
</header>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .main-header {
        width: 100%;
        background: #3490dc;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 100%;
        overflow: hidden;
    }

    .logo-link {
        color: white;
        text-decoration: none;
        font-size: 22px;
        font-weight: bold;
        white-space: nowrap;
    }

    .nav-links {
        display: flex;
        flex-wrap: wrap;
        gap: 40px; /* المسافة بين جميع الروابط */
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        white-space: nowrap;
    }

    .nav-links a:hover {
        text-decoration: underline;
    }

    /* للهواتف */
    @media (max-width: 600px) {
        .main-header {
            flex-direction: column;
            gap: 10px;
            text-align: center;
            padding: 10px 20px;
        }

        .nav-links {
            gap: 15px; /* المسافة بين الروابط أصغر على الهواتف */
            flex-direction: column;
        }
    }
</style>
