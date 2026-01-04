<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>مواعيدي</title>

<style>
:root{
--bg:#f6f7fb;--card:#fff;--muted:#6b7280;
--accent:#2563eb;--danger:#ef4444;
}
body{
font-family:tahoma;background:var(--bg);
margin:0;padding:24px
}
.container{max-width:840px;margin:auto}
h1{text-align:center}

/* ===== بطاقة المواعيد ===== */
.card{
background:var(--card);
padding:18px;border-radius:14px;
box-shadow:0 6px 18px rgba(0,0,0,.05)
}

.appt{
border:1px solid #e6e7eb;
padding:14px 50px 14px 14px; /* مساحة للقائمة */
border-radius:12px;
margin-bottom:12px;
position:relative
}


.meta{display:flex;gap:12px;align-items:center}
.date{font-weight:700}
.time{color:var(--accent);font-weight:700}
.muted{color:var(--muted)}

/* ===== قائمة النقاط لكل موعد ===== */
.controls-appt{
position:absolute;
top:14px;
left:14px; /* ثابت وما يدخلش في النص */
}


.menu-btn{
background:none;
border:none;
font-size:22px;
cursor:pointer;
color:#555
}

.dropdown-appt{
display:none;
position:absolute;
left:0;
top:34px;
background:#fff;
border:1px solid #ddd;
border-radius:10px;
min-width:150px;
box-shadow:0 6px 18px rgba(0,0,0,.08);
z-index:50
}

.dropdown-appt a, .dropdown-appt button{
display:block;
width:100%;
padding:10px;
background:none;
border:none;
text-align:right;
cursor:pointer;
font-weight:600;
color:#111;
text-decoration:none
}

.dropdown-appt a:hover{background:#f3f4f6}
.dropdown-appt .danger{color:var(--danger)}

.controls-appt.open .dropdown-appt{display:block}

/* ===== قائمة التحكم الثابتة أعلى اليمين ===== */
.controls-fixed{
position:fixed;
top:16px;
right:16px;
z-index:1000;
}

.menu-button{
background:#fff;
border:1px solid #e5e7eb;
padding:8px 12px;
border-radius:10px;
cursor:pointer;
font-size:1.1rem;
color:var(--accent);
box-shadow:0 4px 12px rgba(0,0,0,0.05);
}

.menu-button:focus{
outline:2px solid rgba(37,99,235,0.25);
}

.dropdown-fixed{
position:absolute;
right:0;
top:calc(100% + 8px);
background:var(--card);
border:1px solid #e6e7eb;
border-radius:10px;
box-shadow:0 6px 18px rgba(20,20,50,0.08);
min-width:180px;
display:none;
padding:8px;
}

.controls-fixed.open .dropdown-fixed{display:block}

.dropdown-fixed a{
display:block;
padding:10px 12px;
border-radius:8px;
color:var(--accent);
text-decoration:none;
font-weight:600;
}

.dropdown-fixed a:hover{
background:#f3f4f6;
}

</style>
</head>

<body>

<!-- قائمة التحكم الثابتة -->
<div class="controls-fixed" id="controlsFixed">
    <button class="menu-button" id="menuButton" aria-haspopup="true" aria-expanded="false" aria-label="قائمة التحكم">
        ☰
    </button>

    <div class="dropdown-fixed" role="menu" aria-label="قائمة التحكم">
        <a href="{{ route('patient.dashboard') }}">⬅ رجوع للحجز</a>
    </div>
</div>

<div class="container">
<h1>📅 مواعيدي</h1>

<div class="card">

@if($appointments->isEmpty())
<p class="muted" style="text-align:center">لا توجد مواعيد</p>
@else

@foreach($appointments as $appointment)
<div class="appt">

    <!-- قائمة النقاط لكل موعد -->
    <div class="controls-appt">
        <button class="menu-btn">⋮</button>
        <div class="dropdown-appt">

            <!-- تعديل -->
            <a href="{{ route('patient.appointments.edit', $appointment) }}">
                ✏️ تعديل الموعد
            </a>

            @if($appointment->status === 'rescheduled')
                <form method="POST" action="{{ route('patient.appointments.accept', $appointment) }}" style="margin-top:6px">
                    @csrf
                    <button type="submit" class="danger">✅ قبول الموعد المعدل</button>
                </form>
            @endif

            <!-- إلغاء -->
            <form method="POST"
                  action="{{ route('patient.appointments.destroy', $appointment) }}"
                  onsubmit="return confirm('هل أنت متأكد من إلغاء الموعد؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger">
                    ❌ إلغاء الحجز
                </button>
            </form>

        </div>
    </div>

    <div class="meta">
        <div class="date">
            📆 {{ \Carbon\Carbon::parse($appointment->date)->isoFormat('D MMMM YYYY') }}
        </div>
        <div class="time">
            ⏰ {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
        </div>
    </div>

    @if($appointment->notes)
        <div class="muted">ملاحظات: {{ $appointment->notes }}</div>
    @endif

</div>
@endforeach

@endif

</div>
</div>

<script>
/* قائمة النقاط لكل موعد */
document.querySelectorAll('.menu-btn').forEach(btn=>{
    btn.addEventListener('click', e=>{
        e.stopPropagation();
        const controls = btn.parentElement;
        document.querySelectorAll('.controls-appt').forEach(c=>{
            if(c!==controls) c.classList.remove('open');
        });
        controls.classList.toggle('open');
    });
});

document.addEventListener('click', ()=>{
    document.querySelectorAll('.controls-appt').forEach(c=>c.classList.remove('open'));
});

/* قائمة التحكم الثابتة */
(function(){
    const controls = document.getElementById('controlsFixed');
    const button = document.getElementById('menuButton');

    button.addEventListener('click', function(e){
        e.stopPropagation();
        const isOpen = controls.classList.toggle('open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', function(){
        controls.classList.remove('open');
        button.setAttribute('aria-expanded', 'false');
    });

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            controls.classList.remove('open');
            button.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>

</body>
</html>
