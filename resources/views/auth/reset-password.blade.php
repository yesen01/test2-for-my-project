@extends("layout")
@section('title','إعادة تعيين كلمة المرور')
@section('content')

<style>
    body {
        font-family: 'Cairo', sans-serif;
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset("images/background-login.jpg") }}');
        background-size: cover;
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
    }
    h2 { text-align: center; color: #0f766e; margin-bottom: 25px; }
    input, button { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
    button { background: #0f766e; color: white; border: none; font-weight: bold; cursor: pointer; }
    button:hover { background: #0d635d; }
</style>

<div class="form-box">
    <h2>تعيين كلمة مرور جديدة</h2>

    <form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-row">
    <label>البريد الإلكتروني</label>
    <input type="email"
           name="email"
           value="{{ request()->email }}"
           readonly
           style="background-color: #f1f5f9; color: #475569; cursor: not-allowed; border: 1px solid #cbd5e1; font-weight: bold;"
           required>
</div>

    <div class="form-row">
        <label>كلمة المرور الجديدة</label>
        <input type="password" name="password" placeholder="أدخل كلمة المرور الجديدة" required>
    </div>

    <div class="form-row">
        <label>تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" placeholder="أعد كتابة كلمة المرور" required>
    </div>

    <button type="submit" class="btn">تحديث كلمة المرور</button>
</form>
</div>

@endsection
