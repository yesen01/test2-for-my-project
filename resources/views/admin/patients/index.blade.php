<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>إدارة المرضى</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    overflow-x:hidden;
    font-family:Tahoma, sans-serif;
    margin:0;
}

/* Sidebar */
.sidebar{
    width:250px;
    height:100vh;
    background:#0f766e;
    color:#fff;
    position:fixed;
    top:0;
    right:0;
    z-index:1000;
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

/* Main Content */
.main-content{
    margin-right:250px;
    padding:30px;
    min-height:100vh;
}

/* Fix Bootstrap RTL row */
.main-content .row{
    margin-right:0;
    margin-left:0;
}

/* Cards */
.card{
    border-radius:14px;
    border:none;
    box-shadow:0 4px 10px rgba(0,0,0,.05);
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>مركز كيان لطب وجراحة الاسنان </h4>

    <a href="{{ route('admin.dashboard') }}"
       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line ms-2"></i>
        لوحة التحكم
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

        <a href="{{ route('admin.receptionists.index') }}" class="{{ request()->routeIs('admin.receptionists.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users ms-2"></i>
        موظفو الاستقبال
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

<!-- Main Content -->
<div class="main-content">

    <h3 class="mb-4">المرضى</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الطبيب المشرف</th>
                            <th>وقت الحجز</th>
                            <th>تاريخ التسجيل</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($patients as $patient)
        @php
            // جلب المواعيد الخاصة بهذا المريض فقط
            $appointments = $patient->appointments;
            // جلب الأطباء الفريدين الذين لديهم مواعيد مع هذا المريض
            $assignedDoctors = $appointments->pluck('doctor')->filter()->unique('id');
        @endphp

        @if($assignedDoctors->isEmpty())
            {{-- حالة المريض الذي ليس لديه أي حجز --}}
            <tr>
                <td>{{ $patient->id }}</td>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->email }}</td>
                <td><span class="text-muted">لا يوجد طبيب</span></td>
                <td><span class="text-danger">لا يوجد حجز بعد</span></td>
                <td>{{ optional($patient->created_at)->format('Y-m-d') }}</td>
                <td>
                    {{-- تغيير المسار إلى admin --}}
                    <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}" onsubmit="return confirm('هل أنت متأكد من حذف المريض؟');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-trash"></i> حذف
                        </button>
                    </form>
                </td>
            </tr>
        @else
            {{-- حالة المريض الذي لديه مواعيد --}}
            @foreach($assignedDoctors as $d)
                @php
                    $times = $appointments->where('doctor_id', $d->id)->pluck('time')->filter()->unique();
                @endphp
                <tr>
                    <td>{{ $patient->id }}</td>
                    <td>{{ $patient->name }}</td>
                    <td>{{ $patient->email }}</td>
                    <td><span class="badge bg-info text-dark">{{ $d->name }}</span></td>
                    <td>{{ $times->isNotEmpty() ? $times->join(' , ') : '-' }}</td>
                    <td>{{ optional($patient->created_at)->format('Y-m-d') }}</td>
                    <td>
                        @if($loop->first) {{-- عرض زر الحذف مرة واحدة فقط للمريض --}}
                            {{-- تغيير المسار إلى admin --}}
                            <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}" onsubmit="return confirm('هل أنت متأكد من حذف المريض؟');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i> حذف
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    @empty
        <tr>
            <td colspan="7" class="text-muted p-4 text-center">لا يوجد مرضى مسجلين في النظام</td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $patients->links() }}
            </div>

        </div>
    </div>

</div>

</body>
</html>
