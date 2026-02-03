<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إعداد مواعيد الطبيب</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
    body {
        background: #f4f6f9;
        font-family: Tahoma, sans-serif;
        padding: 20px;
    }
    /* تم إزالة تنسيقات Sidebar القديمة وتعديل المحتوى الرئيسي */
    .main-content {
        max-width: 1100px; /* جعل العرض مريح للعين */
        margin: 0 auto;   /* توسيط المحتوى في الشاشة */
    }
    .card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,.08);
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .btn-back {
        background-color: #fff;
        color: #0f766e;
        border: 2px solid #0f766e;
        font-weight: bold;
        transition: all 0.3s;
    }
    .btn-back:hover {
        background-color: #0f766e;
        color: #fff;
    }
</style>
</head>

<body>

<div class="main-content">

    <div class="page-header">
        <h4 class="mb-0">
            <i class="fa-solid fa-calendar-days text-success me-2"></i>
            مواعيد الطبيب: <span class="text-success">{{ $doctor->name }}</span>
        </h4>

        <a href="{{ route('admin.doctors.index') }}" class="btn btn-back shadow-sm">
            <i class="fa-solid fa-arrow-right ms-2"></i> رجوع لقائمة الأطباء
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-4 text-secondary">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> إعداد الجدول الأسبوعي (من السبت إلى الجمعة)
            </h6>

            <form method="POST" action="{{ route('admin.doctors.slots.store', $doctor) }}">
                @csrf

                @php
                    $weekly = $slots->filter(function($s){ return !is_null($s->day_of_week); })->keyBy('day_of_week');
                    $daysOrder = [6=>'السبت',0=>'الأحد',1=>'الإثنين',2=>'الثلاثاء',3=>'الأربعاء',4=>'الخميس',5=>'الجمعة'];
                @endphp

                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle mb-4">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th style="width: 15%;">اليوم</th>
                                <th style="width: 10%;">نشط</th>
                                <th>وقت البداية</th>
                                <th>وقت النهاية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daysOrder as $dow => $label)
                                @php $exist = $weekly->has($dow) ? $weekly->get($dow) : null; @endphp
                                <tr>
                                    <td class="fw-bold text-dark">{{ $label }}</td>
                                    <td>
                                        <input type="hidden" name="days[{{ $dow }}][enabled]" value="0">
                                        <input class="form-check-input" type="checkbox" name="days[{{ $dow }}][enabled]" value="1" {{ $exist ? 'checked' : '' }} style="width: 1.2em; height: 1.2em; cursor:pointer;">
                                    </td>
                                    <td>
                                        <input type="time" name="days[{{ $dow }}][start_time]" class="form-control" value="{{ old('days.'.$dow.'.start_time', $exist?->start_time) }}">
                                    </td>
                                    <td>
                                        <input type="time" name="days[{{ $dow }}][end_time]" class="form-control" value="{{ old('days.'.$dow.'.end_time', $exist?->end_time) }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex border-top pt-3">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold shadow-sm ms-auto">
                        <i class="fa-solid fa-save me-2"></i> حفظ الجدول بالكامل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
