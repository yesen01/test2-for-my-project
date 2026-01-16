@extends("layout")
@section('title','Registration')
@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("images/background-login.jpg") }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        background: rgba(255, 255, 255, 0.95);
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        width: 420px;
        text-align: center;
        position: relative; /* ضروري لتموضع زر الرجوع إذا أردته مطلقاً */
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

    .container h2 {
        color: #0f766e;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: bold;
    }

    .container input {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: 0.3s;
    }

    .container input:focus {
        border-color: #0f766e;
        box-shadow: 0 0 5px rgba(15, 118, 110, 0.3);
    }

    .container button {
        background: #0f766e;
        color: #fff;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 15px;
        transition: 0.3s;
    }

    .container button:hover {
        background: #0d635d;
        transform: translateY(-2px);
    }

    .container p {
        margin-top: 15px;
        font-size: 14px;
        color: #555;
    }

    .container p a {
        color: #0f766e;
        text-decoration: none;
        font-weight: bold;
    }

    .success, .error {
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 10px;
        text-align: right;
    }

    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="container">
    <a href="{{ url('/') }}" class="back-link">
        <i class="fa-solid fa-arrow-right"></i> رجوع للرئيسية
    </a>

    <h2>تسجيل مركز كيان</h2>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                • {{ $error }} <br>
            @endforeach
        </div>
    @endif

    <form action="{{ route('Registration.Post') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="الاسم الكامل" required>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="text" name="phone" placeholder="رقم الهاتف" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <input type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
        <button type="submit">تسجيل</button>
    </form>

    <p>هل لديك حساب بالفعل؟ <a href="{{ url('/login') }}">سجل دخولك هنا</a></p>
</div>

@endsection
