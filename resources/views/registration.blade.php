@extends("layout")
@section('title','Registration')
@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #e6f2ff, #ffffff);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: #fff;
        padding: 30px 40px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        width: 400px;
        text-align: center;
    }
    .container h2 {
        color: #0077b6;
        margin-bottom: 20px;
        font-size: 24px;
    }
    .container input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #cce0ff;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
    }
    .container input:focus {
        border-color: #0077b6;
        box-shadow: 0 0 5px rgba(0, 119, 182, 0.3);
    }
    .container button {
        background: #0077b6;
        color: #fff;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
    }
    .container button:hover {
        background: #0096c7;
    }
    .success, .error {
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        margin-top: 15px;
    }
    .success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="container">
    <h2>تسجيل مركز كايان</h2>

    {{-- عرض رسائل النجاح --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    {{-- عرض رسائل الخطأ --}}
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    {{-- عرض أخطاء الفالديشن --}}
    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                • {{ $error }} <br>
            @endforeach
        </div>
    @endif

    <form action="{{ route('Registration.Post') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="اسم الكامل" required>
        <input type="email" name="email" placeholder="البريد الاكتروني" required>
        <input type="text" name="phone" placeholder="رقم الهاتف" required>
        <input type="password" name="password" placeholder=" كلمة المرور" required>
        <input type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required>
        <button type="submit">تسجيل</button>
    </form>

    <p>هل لديك حساب بالفعل؟<a href="{{ url('/login') }}">سجل دخولك هنا</a></p>

</div>

@endsection
