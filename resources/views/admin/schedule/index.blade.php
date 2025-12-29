<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>جدول الأطباء - HealthEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>

        body{
    background:#f4f6f9;
    overflow-x:hidden;
    font-family:Tahoma, sans-serif;
}

/* Sidebar */
.sidebar{
    width:250px;
    min-height:100vh;
    background:#0f766e;
    color:#fff;
    position:fixed;
    top:0;
    right:0;
}
.sidebar h4{
    padding:20px;
    border-bottom:1px solid rgba(255,255,255,.2);
    margin:0;
}
.sidebar a,
.sidebar button{
    color:#fff;
    text-decoration:none;
    padding:12px 20px;
    display:block;
    width:100%;
    background:none;
    border:none;
    text-align:right;
}
.sidebar a:hover,
.sidebar a.active,
.sidebar button:hover{
    background:rgba(255,255,255,.15);
}

/* Content */
.main-content{
    margin-right:250px;
    padding:25px;
}

/* Cards */
.dashboard-card{
    border-radius:14px;
    border:none;
    box-shadow:0 4px 10px rgba(0,0,0,.05);
}
.icon-box{
    width:55px;
    height:55px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:22px;
}
        .doc-cell {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .doc-cell img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .slot-badge {
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 0.8rem;
            display: inline-block;
        }
        .slot-weekly {
            background-color: #e7f1ff;
            color: #0d6efd;
        }
        .slot-free {
            background-color: #d4edda;
            color: #155724;
        }
        .slot-booked {
            background-color: #f8d7da;
            color: #721c24;
        }
        .slot-row {
            margin-bottom: 4px;
        }
        th, td {
            vertical-align: middle !important;
        }
        .actions button {
            margin: 0 2px;
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
<div class="sidebar">
    <h4>HealthEase</h4>

    <a href="{{ route('admin.dashboard') }}"
       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line ms-2"></i>
        Dashboard
    </a>

    <a href="{{ route('admin.doctors.index') }}"
       class="{{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-doctor ms-2"></i>
        الأطباء
    </a>

    <a href="{{ route('admin.patients.index') }}"
       class="{{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user ms-2"></i>
        المرضى
    </a>

    <a href="{{ route('admin.appointments.index') }}"
       class="{{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check ms-2"></i>
        المواعيد
    </a>

    <a href="{{ route('admin.schedule.index') }}"
       class="{{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
        <i class="fa-solid fa-clock ms-2"></i>
        جدول الأطباء
    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-danger">
            <i class="fa-solid fa-right-from-bracket ms-2"></i>
            تسجيل الخروج
        </button>
    </form>
</div>
<div class="container py-4">
    <header class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0">جدول الأطباء</h3>
        </div>
    </header>

    <main class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>الطبيب</th>
                    <th>الأحد</th>
                    <th>الإثنين</th>
                    <th>الثلاثاء</th>
                    <th>الأربعاء</th>
                    <th>الخميس</th>
                    <th>الجمعة</th>
                    <th>السبت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $doctor)
                <tr>
                    <td>#{{ $doctor->id }}</td>
                    <td class="doc-cell">
                        <span>{{ $doctor->name }}</span>
                    </td>

                    @foreach(['sun','mon','tue','wed','thu','fri','sat'] as $day)
                        <td>
                            @php
                                // map day string to numeric dayOfWeek (Carbon style, 0=Sunday)
                                $dayMap = ['sun'=>0,'mon'=>1,'tue'=>2,'wed'=>3,'thu'=>4,'fri'=>5,'sat'=>6];
                                $dayIndex = $dayMap[$day];

                                // prefer doctorSlots (weekly slots with day_of_week)
                                $weeklySlots = $doctor->doctorSlots->filter(function($s) use($dayIndex){
                                    return !is_null($s->day_of_week) && (int)$s->day_of_week === $dayIndex;
                                });

                                // also look for one-off slots on the same weekday (may be multiple)
                                $oneOffSlots = $doctor->doctorSlots->filter(function($s) use($dayIndex){
                                    return is_null($s->day_of_week) && $s->start_at && (int)$s->start_at->dayOfWeek === $dayIndex;
                                });
                            @endphp

                            @if($weeklySlots->isNotEmpty())
                                @foreach($weeklySlots as $s)
                                    <div class="slot-row">
                                        <span class="slot-badge slot-weekly">{{ $s->start_time }} @if($s->end_time)- {{ $s->end_time }}@endif</span>
                                        <small class="text-muted">أسبوعي</small>
                                    </div>
                                @endforeach
                            @elseif($oneOffSlots->isNotEmpty())
                                @foreach($oneOffSlots as $s)
                                    @php $booked = $s->isBookedOnDate($s->start_at); @endphp
                                    <div class="slot-row">
                                        <span class="slot-badge {{ $booked ? 'slot-booked' : 'slot-free' }}">{{ $s->start_at->format('H:i') }}</span>
                                        <small class="text-muted">{{ $booked ? 'محجوز' : 'متاح' }}</small>
                                    </div>
                                @endforeach
                            @else
                                {{-- fall back to other representations if present --}}
                                @php
                                    $time = 'NA';
                                    if (isset($doctor->schedule) && is_array($doctor->schedule) && array_key_exists($day, $doctor->schedule)) {
                                        $time = $doctor->schedule[$day];
                                    } elseif (isset($doctor->schedules) && $doctor->schedules instanceof \Illuminate\Support\Collection) {
                                        $slot = $doctor->schedules->firstWhere('day', $day);
                                        $time = $slot->time ?? 'NA';
                                    } else {
                                        $time = $doctor->{$day} ?? 'NA';
                                    }
                                @endphp

                                @if($time === 'NA' || empty($time))
                                    <span class="text-danger">غير متاح</span>
                                @else
                                    <span class="slot-badge slot-weekly">{{ $time }}</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
