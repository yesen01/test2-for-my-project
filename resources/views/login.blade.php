@extends("layout")
@section('title','Login')
@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset("images/background-login.jpg") }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
    }

    .form-box {
        width: 400px;
        padding: 30px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    /* تنسيق زر الرجوع */
    .back-link {
        display: block;
        text-align: right;
        margin-bottom: 15px;
        text-decoration: none;
        color: #0f766e;
        font-weight: bold;
        font-size: 14px;
        transition: 0.3s;
    }

    .back-link:hover {
        color: #0d635d;
        transform: translateX(5px);
    }

    h2 {
        text-align: center;
        color: #0f766e;
        margin-bottom: 25px;
        font-weight: bold;
    }

    input, button {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        box-sizing: border-box;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    button {
        background: #0f766e;
        color: white;
        border: none;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: #0d635d;
        transform: translateY(-2px);
    }

    .error, .success {
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 14px;
        text-align: right;
    }

    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    .register-link {
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
    }

    .register-link a {
        color: #0f766e;
        text-decoration: none;
        font-weight: bold;
    }

    .forgot-password {
    font-size: 13px;
    color: #64748b; /* لون رمادي هادئ */
    text-decoration: none;
    transition: 0.3s;
}

.forgot-password:hover {
    color: #0f766e; /* يتحول للزيتي عند التمرير */
    text-decoration: underline;
}
</style>

<div class="form-box">
    <a href="{{ url('/') }}" class="back-link">
        <i class="fa-solid fa-arrow-right"></i> رجوع للرئيسية
    </a>

    <h2>تسجيل الدخول</h2>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                • {{ $err }} <br>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.Post') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <div style="text-align: left; margin-top: -10px; margin-bottom: 20px;">
    <a href="{{ route('password.request') }}" class="forgot-password">نسيت كلمة المرور؟</a>
</div>
        <button type="submit">تسجيل الدخول</button>
    </form>

    <div class="register-link">
        ليس لديك حساب؟ <a href="{{ url('/Registration') }}">سجل الآن</a>
    </div>
</div>

@endsection
