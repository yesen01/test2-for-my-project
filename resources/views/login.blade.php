@extends("layout")
@section('title','Login')
@section('content')

<style>
    body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
}

.form-box {
    width: 400px;
    margin: 100px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
}

input, button {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    box-sizing: border-box; /* يمنع خروجهم برا البوكس */
}

button {
    background: #3490dc;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #2779bd;
}

.error, .success {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    font-weight: bold;
}

.error { background: #f8d7da; color: #721c24; }
.success { background: #d4edda; color: #155724; }

</style>

<div class="form-box">
    <h2>تسجيل الدخول</h2>

    {{-- عرض رسائل النجاح --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    {{-- عرض رسائل الخطأ --}}
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    {{-- عرض أخطاء الفالديشن --}}
    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                • {{ $err }} <br>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.Post') }}" method="POST">
        @csrf

        <input type="email" name="email" placeholder="بريد الاكتروني " required>

        <input type="password" name="password" placeholder="كلمة المرور" required>

        <button type="submit">تسجيل الدخول</button>
    </form>
</div>

@endsection
