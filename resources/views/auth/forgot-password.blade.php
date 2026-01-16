@extends("layout")
@section('title','استعادة كلمة المرور')
@section('content')

<style>
    body {
        font-family: 'Cairo', Arial, sans-serif;
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset("images/background-login.jpg") }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
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
        text-align: center;
    }

    h2 { color: #0f766e; margin-bottom: 20px; font-weight: bold; }

    p { font-size: 14px; color: #64748b; margin-bottom: 20px; }

    input, button {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-sizing: border-box;
    }

    button {
        background: #0f766e;
        color: white;
        border: none;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover { background: #0d635d; transform: translateY(-2px); }

    .back-link {
        display: block;
        margin-top: 10px;
        color: #0f766e;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
    }
</style>

<div class="form-box">
    <h2>استعادة كلمة المرور</h2>
    <p>أدخل بريدك الإلكتروني وسنرسل لك رابطاً لتعيين كلمة مرور جديدة.</p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <button type="submit">إرسال رابط الاستعادة</button>
    </form>

    <a href="{{ route('login') }}" class="back-link">العودة لتسجيل الدخول</a>
</div>

@endsection
