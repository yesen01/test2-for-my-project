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

    /* تنسيق رسائل الخطأ والنجاح */
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: right;
    }
    .alert-danger {
        background-color: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
</style>

<div class="form-box">
    <h2>استعادة كلمة المرور</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <p>أدخل بريدك الإلكتروني وسنرسل لك رابطاً لتعيين كلمة مرور جديدة.</p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required>
        <button type="submit">إرسال رابط الاستعادة</button>
    </form>

    <a href="{{ route('login') }}" class="back-link">العودة لتسجيل الدخول</a>
</div>

@endsection
