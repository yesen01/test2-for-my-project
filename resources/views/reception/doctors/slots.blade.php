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
        :root { --accent: #0f766e; --accent-hover: #0d635d; --bg: #f3f4f6; --card: #ffffff; --danger: #ef4444; }
        body { font-family: 'Cairo', sans-serif; background: var(--bg); color: #1e293b; margin: 0; }
        .container { max-width: 700px; margin: 60px auto; padding: 0 20px; }
        .card { background: var(--card); padding: 30px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); }
        h2 { color: var(--accent); text-align: center; }
        .form-row { margin-bottom: 18px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; outline: none; }
        button.btn { background: var(--accent); color: #fff; width: 100%; padding: 14px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>حجز موعد جديد</h2>

        <form method="POST" action="{{ route('patient.dashboard.store') }}">
            @csrf

            <div class="form-row">
                <label>اختر الطبيب</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">-- اختر طبيباً --</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <label>اليوم المتاح</label>
                <select id="day" name="day" required>
                    <option value="">اختر الطبيب أولاً</option>
                </select>
            </div>

            <div class="form-row">
                <label>الساعة</label>
                <select id="time" name="time" required>
                    <option value="">اختر اليوم أولاً</option>
                </select>
            </div>

            <button class="btn" type="submit">تأكيد الحجز</button>
        </form>
    </div>
</div>

<script>
    (function(){
        // جلب البيانات التي جهزناها في الـ Controller
        const availability = @json($availability ?? []);
        const dayLabels = {0:'الأحد',1:'الإثنين',2:'الثلاثاء',3:'الأربعاء',4:'الخميس',5:'الجمعة',6:'السبت'};
        const daysOrder = [6,0,1,2,3,4,5]; // يبدأ من السبت كما في الـ Admin

        const doctorSel = document.getElementById('doctor_id');
        const daySel = document.getElementById('day');
        const timeSel = document.getElementById('time');

        function clearSelect(sel, text) {
            sel.innerHTML = `<option value="">${text}</option>`;
        }

        doctorSel.addEventListener('change', function() {
            clearSelect(daySel, 'اختر اليوم');
            clearSelect(timeSel, 'اختر اليوم أولاً');
            const docId = this.value;
            if (!docId || !availability[docId]) return;

            daysOrder.forEach(dow => {
                if (availability[docId][dow]) {
                    const opt = document.createElement('option');
                    opt.value = dow;
                    opt.textContent = dayLabels[dow];
                    daySel.appendChild(opt);
                }
            });
        });

        daySel.addEventListener('change', function() {
            clearSelect(timeSel, 'اختر الساعة');
            const docId = doctorSel.value;
            const dow = this.value;
            if (!docId || dow === "" || !availability[docId][dow]) return;

            availability[docId][dow].forEach(slot => {
                // تقسيم الوقت (مثلاً 09:00 إلى 9)
                let start = parseInt(slot.start.split(':')[0]);
                let end = parseInt(slot.end.split(':')[0]);

                for(let i = start; i < end; i++) {
                    const t = i.toString().padStart(2, '0') + ':00';
                    const opt = document.createElement('option');
                    opt.value = t;
                    opt.textContent = t;
                    timeSel.appendChild(opt);
                }
            });
        });

        // سطر للتأكد في الـ Console
        console.log("البيانات المحملة:", availability);
    })();
</script>
</body>
</html>
