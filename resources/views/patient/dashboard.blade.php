@php
use Carbon\Carbon;
$nowDate = Carbon::now()->toDateString();
@endphp

<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>حجز موعد - مركز كيان</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --accent: #0f766e; /* اللون الزيتي لمركز كيان */
            --accent-hover: #0d635d;
            --muted: #64748b;
            --bg: #f3f4f6;
            --card: #ffffff;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--bg);
            padding: 0;
            margin: 0;
            color: #1e293b;
        }

        /* القائمة الجانبية المحدثة */
        #sideMenu {
            position: fixed;
            top: 0;
            right: 0;
            width: 280px;
            height: 100%;
            background: var(--card);
            padding: 30px 20px;
            box-shadow: -10px 0 30px rgba(0,0,0,0.05);
            transform: translateX(100%);
            transition: transform .3s ease;
            z-index: 1000;
        }

        #sideMenu.open { transform: translateX(0); }

        #overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 118, 110, 0.2);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .3s;
            z-index: 999;
        }

        #overlay.show { opacity: 1; pointer-events: auto; }

        /* الأزرار والحقول */
        #menuBtn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--accent);
            color: #fff;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .container { max-width: 700px; margin: 60px auto; padding: 0 20px; }

        .card {
            background: var(--card);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            border: 1px solid rgba(15, 118, 110, 0.05);
        }

        h2 { color: var(--accent); font-weight: 700; margin-bottom: 25px; text-align: center; }

        .form-row { margin-bottom: 18px; }

        label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: #475569; }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            transition: 0.3s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.1); }

        input[readonly] { background: #f8fafc; color: #64748b; cursor: not-allowed; }

        button.btn {
            background: var(--accent);
            color: #fff;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(15, 118, 110, 0.2);
        }

        button.btn:hover { background: var(--accent-hover); transform: translateY(-2px); }

        .msg-success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; text-align: center; }

        nav ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            color: #475569;
            font-weight: 600;
            transition: 0.3s;
        }

        nav ul li a:hover, nav ul li a.active {
            background: rgba(15, 118, 110, 0.1);
            color: var(--accent);
        }

        .logout-btn {
            color: var(--danger);
            border: 1px solid #fee2e2;
            background: #fff;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
        }

        @media(max-width: 600px) { .container { margin-top: 80px; } }
    </style>
</head>

<body>

<button id="menuBtn"><i class="fa-solid fa-bars"></i></button>
<div id="overlay" onclick="toggleMenu(false)"></div>

<div id="sideMenu">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="width: 60px; height: 60px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: white; font-size: 1.5rem;">
            <i class="fa-solid fa-user-injured"></i>
        </div>
        <h3 style="margin: 0; font-size: 1.1rem; color: var(--accent);">{{ Auth::user()->name }}</h3>
    </div>

    <nav>
        <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:10px">
            <li>
                <a href="{{ route('patient.dashboard') }}" class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-plus"></i> حجز موعد
                </a>
            </li>
            <li>
                <a href="{{ route('patient.appointments') }}" class="{{ request()->routeIs('patient.appointments') ? 'active' : '' }}">
                    <i class="fa-solid fa-notes-medical"></i> مواعيدي
                </a>
            </li>
        </ul>
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
        </button>
    </form>
</div>

<div class="container">
    <div class="card">
        <h2><i class="fa-solid fa-file-medical" style="margin-left: 10px;"></i>حجز موعد جديد</h2>

        @if(session('success'))
            <div class="msg-success"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('patient.dashboard.store') }}" novalidate>
            @csrf

            <div class="form-row">
                <label><i class="fa-solid fa-user"></i> الاسم الكامل</label>
                <input value="{{ Auth::user()->name }}" readonly>
            </div>

            <div class="form-row">
                <label><i class="fa-solid fa-envelope"></i> البريد الإلكتروني</label>
                <input value="{{ Auth::user()->email }}" readonly>
            </div>

            <div class="form-row">
                <label for="doctor_id"><i class="fa-solid fa-stethoscope"></i> اختر الطبيب</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">-- اضغط للاختيار --</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                            د. {{ $doc->name }} @isset($doc->specialty) ({{ $doc->specialty }}) @endisset
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')<div style="color:var(--danger); font-size:12px; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <label for="day"><i class="fa-solid fa-calendar-day"></i> اليوم المتاح</label>
                <select id="day" name="day" required>
                    <option value="">اختر الطبيب أولاً</option>
                </select>
                @error('day')<div style="color:var(--danger); font-size:12px; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <label for="time"><i class="fa-solid fa-clock"></i> الساعة</label>
                <select id="time" name="time" required>
                    <option value="">اختر اليوم أولاً</option>
                </select>
                @error('time')<div style="color:var(--danger); font-size:12px; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <label for="notes"><i class="fa-solid fa-comment-medical"></i> ملاحظات أو شكوى</label>
                <textarea id="notes" name="notes" rows="3" placeholder="أدخل أي ملاحظات إضافية هنا...">{{ old('notes') }}</textarea>
            </div>

            <div style="margin-top:25px;">
                <button class="btn" type="submit">تأكيد الحجز الآن</button>
                <p style="text-align: center; font-size: 12px; color: var(--muted); margin-top: 15px;">
                    <i class="fa-solid fa-info-circle"></i> تاريخ اليوم: {{ $nowDate }}
                </p>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMenu(force) {
        const menu = document.getElementById('sideMenu'), overlay = document.getElementById('overlay');
        const open = typeof force === 'boolean' ? force : !menu.classList.contains('open');
        menu.classList.toggle('open', open);
        overlay.classList.toggle('show', open);
    }
    document.getElementById('menuBtn').addEventListener('click', () => toggleMenu());

    // JS للتعامل مع المواعيد (بقي كما هو مع تحسين المظهر)
    (function(){
        // استبدل السطر القديم بهذا السطر
        const availability = @json($availability ?? []);
        const dayLabels = {0:'الأحد',1:'الإثنين',2:'الثلاثاء',3:'الأربعاء',4:'الخميس',5:'الجمعة',6:'السبت'};
        const daysOrder = [6,0,1,2,3,4,5];

        const doctorSel = document.getElementById('doctor_id');
        const daySel = document.getElementById('day');
        const timeSel = document.getElementById('time');

        function clearSelect(sel, text) {
            sel.innerHTML = `<option value="">${text}</option>`;
        }

        doctorSel.addEventListener('change', function() {
            if (!this.value) { clearSelect(daySel, 'اختر الطبيب'); return; }
            clearSelect(daySel, 'اختر اليوم');
            const avail = availability[this.value] || {};
            daysOrder.forEach(dow => {
                if (avail[dow]) {
                    const opt = document.createElement('option');
                    opt.value = dow;
                    opt.textContent = dayLabels[dow];
                    daySel.appendChild(opt);
                }
            });
        });

        daySel.addEventListener('change', function() {
            const docId = doctorSel.value;
            if (!docId || !this.value) { clearSelect(timeSel, 'اختر اليوم أولاً'); return; }
            clearSelect(timeSel, 'اختر الساعة');
            const ranges = availability[docId][this.value] || [];
            ranges.forEach(r => {
                // توليد الساعات (تبسيط للكود السابق)
                let start = parseInt(r.start.split(':')[0]);
                let end = parseInt(r.end.split(':')[0]);
                for(let i=start; i<=end; i++) {
                    const t = i.toString().padStart(2, '0') + ':00';
                    const opt = document.createElement('option');
                    opt.value = t; opt.textContent = t;
                    timeSel.appendChild(opt);
                }
            });
        });
    })();
</script>

</body>
</html>
