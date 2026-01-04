<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تعديل الموعد</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h4>تعديل موعد للمريض: {{ optional($appointment->user)->name }}</h4>

    <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-2">
            <label class="form-label">التاريخ</label>
            <input type="date" name="date" value="{{ $appointment->date }}" class="form-control" required />
        </div>

        <div class="mb-2">
            <label class="form-label">الوقت (HH:MM)</label>
            <input type="text" name="time" value="{{ $appointment->time }}" class="form-control" required />
        </div>

        <div class="mb-2">
            <button class="btn btn-primary">حفظ التغيير وارسال إشعار للمريض</button>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>

</div>

</body>
</html>
