<div dir="rtl" style="max-width:700px;margin:30px auto;font-family:Tahoma, Arial, sans-serif">

    <!-- زر الرجوع -->
    <div style="margin-bottom:20px">
        <a href="{{ route('patient.appointments') }}"
           style="padding:8px 12px;background:#6c757d;color:#fff;text-decoration:none;border-radius:4px;display:inline-block">
           ⬅ رجوع
        </a>
    </div>

    <h1 style="text-align:center;margin-bottom:20px">تعديل الموعد</h1>

    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin-bottom:15px">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin-bottom:15px">
            <ul style="margin:0;padding-left:20px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('patient.appointments.update', $appointment) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div style="margin-bottom:12px">
            <label for="date">التاريخ:</label>
            <input id="date" type="date" name="date" value="{{ old('date', $appointment->date) }}" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px">
            @error('date')<div style="color:#c0392b;margin-top:6px;font-size:90%">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:12px">
            <label for="time">الوقت:</label>
            <input id="time" type="time" name="time" value="{{ old('time', $appointment->time) }}" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px">
            @error('time')<div style="color:#c0392b;margin-top:6px;font-size:90%">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:12px">
            <label for="notes">ملاحظات:</label>
            <textarea id="notes" name="notes" rows="4" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px">{{ old('notes', $appointment->notes) }}</textarea>
            @error('notes')<div style="color:#c0392b;margin-top:6px;font-size:90%">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;gap:8px;justify-content:flex-end">
            <a href="{{ route('patient.appointments') }}" style="padding:8px 12px;background:#6c757d;color:#fff;text-decoration:none;border-radius:4px">إلغاء</a>
            <button type="submit" style="padding:8px 12px;background:#007bff;color:#fff;border:none;border-radius:4px">حفظ التعديلات</button>
        </div>
    </form>
</div>
