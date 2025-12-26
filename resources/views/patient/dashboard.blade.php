@php
use Carbon\Carbon;
$nowDate = Carbon::now()->toDateString();
@endphp

<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>لوحة المريض</title>

<style>
:root{--accent:#2563eb;--muted:#6b7280;--bg:#f6f7fb}
*{box-sizing:border-box}
body{font-family:tahoma,system-ui,Segoe UI,Arial;background:var(--bg);padding:24px;margin:0;color:#111}
.container{max-width:920px;margin:24px auto;display:flex;gap:20px;align-items:flex-start}
.card{background:#fff;padding:22px;border-radius:12px;box-shadow:0 6px 20px rgba(3,7,18,0.06);flex:1}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
h2{margin:0;font-size:20px}
.form-row{margin-top:12px}
label{display:block;font-size:13px;color:var(--muted);margin-bottom:6px}
input,select,textarea{width:100%;padding:10px;border:1px solid #e6e9ef;border-radius:8px;background:#fff;font-size:14px}
input[disabled],input[readonly],textarea[readonly]{background:#f3f4f6;color:#374151}
select{appearance:none;background-image:none}
button.btn{background:var(--accent);color:#fff;padding:10px 16px;border:none;border-radius:10px;cursor:pointer}
.msg{padding:10px;border-radius:8px;margin-bottom:12px}
.msg-success{background:#ecfdf5;color:#065f46}
.msg-error{background:#fff1f2;color:#9f1239;border:1px solid rgba(159,18,57,0.08)}
.error-text{color:#b91c1c;font-size:13px;margin-top:6px}

/* side menu */
#menuBtn{position:fixed;top:18px;right:18px;background:var(--accent);color:#fff;border:none;padding:10px 12px;border-radius:8px;cursor:pointer;z-index:40}
#sideMenu{
position:fixed;
top:0;
right:0;
width:260px;
height:100%;
background:#fff;
padding:20px;
box-shadow:-8px 0 24px rgba(3,7,18,0.08);
transform:translateX(100%); /* مخفية */
transition:transform .25s ease;
z-index:50;
}

#sideMenu.open{
transform:translateX(0); /* ظاهرة */
}

#overlay{position:fixed;inset:0;background:rgba(3,7,18,0.4);opacity:0;pointer-events:none;transition:opacity .2s}
#overlay.show{opacity:1;pointer-events:auto;}

/* responsive */
@media(max-width:720px){
.container{
padding:12px;
flex-direction:column
}

#sideMenu{
width:100%;
}
}

#sideMenu.open{right:0}

</style>
</head>

<body>

<button id="menuBtn" aria-controls="sideMenu" aria-expanded="false">☰</button>
<div id="overlay" onclick="toggleMenu(false)"></div>

<div id="sideMenu" aria-hidden="true" role="navigation" aria-label="قائمة المريض" tabindex="-1" onkeydown="if(event.key==='Escape')toggleMenu(false)">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <h3 style="margin:0">القائمة</h3>
        <button onclick="toggleMenu(false)" aria-label="إغلاق القائمة" style="background:transparent;border:none;font-size:18px;cursor:pointer">✕</button>
    </div>

    <div style="margin:12px 0;color:var(--muted);font-size:14px">
        مرحباً، <strong>{{ Auth::user()->name }}</strong>
    </div>

    <nav>
        <ul style="list-style:none;padding:0;margin:12px 0 0 0;display:flex;flex-direction:column;gap:8px">
            <li>
                <a href="{{ route('patient.dashboard') }}"
                     @if(request()->routeIs('patient.dashboard')) aria-current="page" style="display:block;padding:8px 10px;border-radius:8px;background:rgba(37,99,235,0.08);color:var(--accent);font-weight:600;text-decoration:none" @else style="display:block;padding:8px 10px;border-radius:8px;color:inherit;text-decoration:none" @endif>
                    حجز موعد
                </a>
            </li>
            <li>
                <a href="{{ route('patient.appointments') }}"
                     @if(request()->routeIs('patient.appointments')) aria-current="page" style="display:block;padding:8px 10px;border-radius:8px;background:rgba(37,99,235,0.08);color:var(--accent);font-weight:600;text-decoration:none" @else style="display:block;padding:8px 10px;border-radius:8px;color:inherit;text-decoration:none" @endif>
                    مواعيدي
                </a>
            </li>
            <li>

            </li>
        </ul>
    </nav>

    <div style="margin-top:14px;border-top:1px solid #f1f5f9;padding-top:12px">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="width:100%;padding:10px;border-radius:8px;border:1px solid #eee;background:#fff;cursor:pointer">تسجيل الخروج</button>
        </form>
    </div>
</div>

<div class="container">
    <div class="card" role="region" aria-labelledby="bookHeading">
        <div class="header">
            <h2 id="bookHeading">حجز موعد</h2>
            <small style="color:var(--muted)">{{ Auth::user()->name }}</small>
        </div>

        @if(session('success'))
            <div class="msg msg-success" role="status">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="msg msg-error">
                تحقق من الحقول المدخلة
            </div>
        @endif

        <form method="POST" action="{{ route('patient.dashboard.store') }}" novalidate>
            @csrf

            <div class="form-row">
                <label for="name">الاسم</label>
                <input id="name" value="{{ Auth::user()->name }}" readonly aria-readonly="true">
            </div>

            <div class="form-row">
                <label for="email">البريد</label>
                <input id="email" value="{{ Auth::user()->email }}" readonly aria-readonly="true">
            </div>

            <div class="form-row">
                <label for="date">التاريخ</label>
                <input id="date" type="date" name="date" min="{{ $nowDate }}" value="{{ old('date') }}" required>
                @error('date')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <label for="doctor_id">الطبيب</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">اختر الطبيب</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }}{{ isset($doc->specialty) ? ' — '.$doc->specialty : '' }}
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <label for="time">الوقت</label>
                <select id="time" name="time" required>
                    @foreach(['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00'] as $t)
                        <option value="{{ $t }}" {{ old('time') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                @error('time')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <label for="notes">ملاحظات</label>
                <textarea id="notes" name="notes" rows="4" placeholder="أدخل أي ملاحظات..." >{{ old('notes') }}</textarea>
                @error('notes')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div style="margin-top:16px;display:flex;gap:10px;align-items:center">
                <button class="btn" type="submit">حجز الموعد</button>
                <small style="color:var(--muted)">الحد الأدنى للتاريخ: {{ $nowDate }}</small>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMenu(force){
    const menu=document.getElementById('sideMenu'), overlay=document.getElementById('overlay'), btn=document.getElementById('menuBtn');
    const isOpen = menu.classList.contains('open');
    const open = typeof force === 'boolean' ? force : !isOpen;
    menu.classList.toggle('open', open);
    overlay.classList.toggle('show', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
}
document.getElementById('menuBtn').addEventListener('click',()=>toggleMenu());
</script>

</body>
</html>
