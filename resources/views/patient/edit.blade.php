@extends('layout')

@section('title', 'تعديل الموعد - مركز كيان')

@section('content')
<div dir="rtl" style="max-width:600px; margin:50px auto; font-family: 'Cairo', Tahoma, sans-serif; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">

    <div style="margin-bottom:25px">
        <a href="{{ route('patient.appointments') }}"
           style="padding:10px 18px; background:#f1f5f9; color:#475569; text-decoration:none; border-radius:10px; display:inline-flex; align-items:center; gap:8px; font-weight:bold; transition: 0.3s;">
           <i class="fa-solid fa-arrow-right"></i> رجوع للمواعيد
        </a>
    </div>

    <h1 style="text-align:center; margin-bottom:30px; color: #0f766e; font-weight: 700;">
        <i class="fa-solid fa-calendar-day"></i> تعديل الموعد
    </h1>

    {{-- رسائل النجاح --}}
    @if(session('success'))
        <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:10px; margin-bottom:20px; text-align:center; font-weight:600; border: 1px solid #bbf7d0;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- رسائل الخطأ --}}
    @if($errors->any())
        <div style="background:#fef2f2; color:#991b1b; padding:12px; border-radius:10px; margin-bottom:20px; border: 1px solid #fecaca;">
            <ul style="margin:0; padding-right:20px;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 14px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('patient.appointments.update', $appointment) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div style="margin-bottom:20px">
            <label for="date" style="display:block; margin-bottom:8px; font-weight:600; color:#334155;">التاريخ:</label>
            <div style="position:relative;">
                <input id="date" type="date" name="date" value="{{ old('date', $appointment->date) }}" required
                       style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:10px; font-family:inherit; outline:none; transition:0.3s;"
                       onfocus="this.style.borderColor='#0f766e'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <div style="margin-bottom:20px">
            <label for="time" style="display:block; margin-bottom:8px; font-weight:600; color:#334155;">الوقت:</label>
            <input id="time" type="time" name="time" value="{{ old('time', $appointment->time) }}" required
                   style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:10px; font-family:inherit; outline:none; transition:0.3s;"
                   onfocus="this.style.borderColor='#0f766e'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <div style="margin-bottom:25px">
            <label for="notes" style="display:block; margin-bottom:8px; font-weight:600; color:#334155;">ملاحظات إضافية:</label>
            <textarea id="notes" name="notes" rows="4" placeholder="اكتب أي ملاحظات للطبيب هنا..."
                      style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:10px; font-family:inherit; outline:none; transition:0.3s; resize:none;"
                      onfocus="this.style.borderColor='#0f766e'" onblur="this.style.borderColor='#e2e8f0'">{{ old('notes', $appointment->notes) }}</textarea>
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end">
            <a href="{{ route('patient.appointments') }}"
               style="padding:12px 20px; background:#f1f5f9; color:#475569; text-decoration:none; border-radius:10px; font-weight:bold; transition:0.3s;">
               إلغاء
            </a>
            <button type="submit"
                    style="padding:12px 25px; background:#0f766e; color:#fff; border:none; border-radius:10px; font-weight:bold; cursor:pointer; transition:0.3s; box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);">
                <i class="fa-solid fa-floppy-disk"></i> حفظ التعديلات
            </button>
        </div>
    </form>
</div>

<style>
    /* تحسين مظهر الأزرار عند التمرير */
    button:hover { background: #0d635d !important; transform: translateY(-2px); }
    a:hover { background: #e2e8f0 !important; }
</style>
@endsection
