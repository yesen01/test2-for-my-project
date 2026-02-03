@extends('layout')

@section('title', 'تعديل الموعد - مركز كيان')

@section('content')
<div dir="rtl" style="max-width:600px; margin:50px auto; font-family: 'Cairo', sans-serif; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">

    <div style="margin-bottom:25px">
        <a href="{{ route('patient.appointments') }}"
           style="padding:10px 18px; background:#f1f5f9; color:#475569; text-decoration:none; border-radius:10px; display:inline-flex; align-items:center; gap:8px; font-weight:bold; transition: 0.3s;">
           <i class="fa-solid fa-arrow-right"></i> رجوع للمواعيد
        </a>
    </div>

    <h1 style="text-align:center; margin-bottom:10px; color: #0f766e; font-weight: 700;">
        <i class="fa-solid fa-calendar-check"></i> تعديل الموعد
    </h1>
    <p style="text-align: center; color: #64748b; margin-bottom: 30px;">الطبيب: <strong>{{ $appointment->doctor->name }}</strong></p>

    <form action="{{ route('patient.appointments.update', $appointment) }}" method="POST" id="editAppointmentForm">
        @csrf
        @method('PUT')

        {{-- نستخدم ID الطبيب المختار مسبقاً --}}
        <input type="hidden" id="doctor_id" value="{{ $appointment->doctor_id }}">

        <div style="margin-bottom:20px">
            <label for="day" style="display:block; margin-bottom:8px; font-weight:600; color:#334155;">
                <i class="fa-solid fa-calendar-day"></i> اليوم المتاح لدى الطبيب:
            </label>
            <select id="day" name="day" required
                    style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:10px; font-family:inherit; outline:none; transition:0.3s; background: white;">
                <option value="">-- جارٍ تحميل الأيام --</option>
            </select>
        </div>

        <div style="margin-bottom:20px">
            <label for="time" style="display:block; margin-bottom:8px; font-weight:600; color:#334155;">
                <i class="fa-solid fa-clock"></i> الساعة المتاحة:
            </label>
            <select id="time" name="time" required
                    style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:10px; font-family:inherit; outline:none; transition:0.3s; background: white;">
                <option value="">اختر اليوم أولاً</option>
            </select>
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end">
            <button type="submit" id="submitBtn"
                    style="padding:12px 25px; background:#0f766e; color:#fff; border:none; border-radius:10px; font-weight:bold; cursor:pointer; transition:0.3s; box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);">
                <i class="fa-solid fa-floppy-disk"></i> حفظ التعديلات
            </button>
        </div>
    </form>
</div>

<script>
    (function(){
        // جلب البيانات القادمة من الكنترولر
        const availability = @json($availability ?? []);//تحويل مصفوفة التوفر إلى JSONمن php إلى جافا سكريبت
        const dayLabels = {0:'الأحد',1:'الإثنين',2:'الثلاثاء',3:'الأربعاء',4:'الخميس',5:'الجمعة',6:'السبت'};//تسمية الأيام
        const daysOrder = [6,0,1,2,3,4,5];//ترتيب الأيام من السبت إلى الجمعة

        const docId = document.getElementById('doctor_id').value;//هنا عشان نعرف من الدكتور المختار
        const daySel = document.getElementById('day');
        const timeSel = document.getElementById('time');

        function clearSelect(sel, text) {
            sel.innerHTML = `<option value="">${text}</option>`;//مسح الخيارات السابقة وإضافة خيار افتراضي
        }

        // تشغيل تعبئة الأيام فوراً
        if (docId && availability[docId]) {//التحقق من وجود مواعيد للطبيب
            clearSelect(daySel, '-- اختر اليوم --');
            const docData = availability[docId];

            daysOrder.forEach(dow => {//ترتيب الأيام
                if (docData[dow]) {//التحقق من وجود مواعيد في هذا اليوم
                    const opt = document.createElement('option');
                    opt.value = dow;
                    opt.textContent = dayLabels[dow];
                    // تحديد يوم الموعد الحالي كخيار افتراضي
                    if(dow == "{{ $appointment->day }}") opt.selected = true;
                    daySel.appendChild(opt);
                }
            });

            // تحفيز حدث التغيير لتعبئة الساعات تلقائياً
            daySel.dispatchEvent(new Event('change'));
        } else {
            clearSelect(daySel, 'عذراً، لا توجد مواعيد عمل مسجلة لهذا الطبيب.');
        }

        // نظام تعبئة الساعات (نفس نظام الحجز)
        daySel.addEventListener('change', function() {
            clearSelect(timeSel, '-- اختر الساعة --');//مسح الخيارات السابقة
            const dow = this.value;

            if (docId && dow !== "" && availability[docId][dow]) {//التحقق من وجود مواعيد للطبيب في هذا اليوم
                const ranges = availability[docId][dow];//جلب فترات التوفر
                ranges.forEach(r => {
                    let start = parseInt(r.start.split(':')[0]);
                    let end = parseInt(r.end.split(':')[0]);

                    for(let i = start; i < end; i++) {
                        const t = i.toString().padStart(2, '0') + ':00';
                        const opt = document.createElement('option');
                        opt.value = t;
                        opt.textContent = t;
                        // تحديد ساعة الموعد الحالي كخيار افتراضي
                        if(t === "{{ substr($appointment->time, 0, 5) }}") opt.selected = true;//هذي ساعة الموعد الحالي يلي مختاره المريض
                        timeSel.appendChild(opt);
                    }
                });
            }
        });
    })();
</script>
@endsection
