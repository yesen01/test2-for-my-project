<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة تحكم الأدمن</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

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

<!-- Main Content -->
<div class="main-content">

    <h4 class="mb-4">
        مرحباً، {{ auth()->user()->name }}
    </h4>

    <div class="row g-4">



        <!-- Doctors -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-success me-3">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <small class="text-muted">الأطباء</small>
                        <h4 class="fw-bold">{{ $doctors ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-info me-3">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <small class="text-muted">المرضى</small>
                        <h4 class="fw-bold">{{ $patients ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments -->
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-warning me-3">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <small class="text-muted">المواعيد</small>
                        <h4 class="fw-bold">{{ $appointments ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>
