<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>قائمة المواعيد - HealthEase</title>

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

        /* Main Content */
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
	<h4>مركز كيان لطب وجراحة الاسنان </h4>

    <a href="{{ route('reception.dashboard') }}"
       class="{{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line ms-2"></i>
        Dashboard
    </a>

    <a href="{{ route('reception.doctors.index') }}"
       class="{{ request()->routeIs('reception.doctors.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-doctor ms-2"></i>
        الأطباء
    </a>

    <a href="{{ route('reception.patients.index') }}"
       class="{{ request()->routeIs('reception.patients.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user ms-2"></i>
        المرضى
    </a>

    <a href="{{ route('reception.appointments.index') }}"
       class="{{ request()->routeIs('reception.appointments.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check ms-2"></i>
        المواعيد
    </a>

    <a href="{{ route('reception.schedule.index') }}"
       class="{{ request()->routeIs('reception.schedule.*') ? 'active' : '' }}">
        <i class="fa-solid fa-clock ms-2"></i>
        جدول الأطباء
    </a>

	<form action="{{ route('logout') }}" method="POST">
		@csrf
		<button type="submit" class="text-danger">
			<i class="fa-solid fa-right-from-bracket ms-2"></i> تسجيل الخروج
		</button>
	</form>
</div>


<!-- ✅ Main Content -->
<div class="main-content">
    <div class="container-fluid">

        <h3 class="mb-3">قائمة المواعيد</h3>

        <div class="mb-4">
            <h5>الأطباء</h5>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($doctors as $doctor)
                    <a href="{{ route('reception.appointments.index') }}?doctor={{ $doctor->id }}"
                       class="btn btn-outline-primary btn-sm">
                        {{ $doctor->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td>{{ optional($appointment->user)->name }}</td>
                    <td>{{ optional($appointment->doctor)->name }}</td>
                    <td>{{ $appointment->date }}</td>
                    <td>{{ $appointment->time }}</td>
                    <td>{{ $appointment->status }}</td>
                    <td>
                        <a href="{{ route('reception.appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('reception.appointments.approve', $appointment) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>

                        <form action="{{ route('reception.appointments.cancel', $appointment) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-warning btn-sm">Cancel</button>
                        </form>

                        <form action="{{ route('reception.appointments.manualRemind', $appointment->id) }}" method="POST" style="display:inline;">

                        @csrf
                         <button type="submit" class="btn btn-info" style="background-color: #00bcd4; border: none; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            Manual Remind
                        </button>
                        </form>

                        <form action="{{ route('reception.appointments.destroy', $appointment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete appointment?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
