<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مواعيد الطبيب</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body{
	background:#f4f6f9;
	overflow-x:hidden;
	font-family:Tahoma, sans-serif;
}
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
.main-content{
	margin-right:250px;
	padding:25px;
}
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
	<h4>HealthEase</h4>

	<a href="{{ route('reception.dashboard') }}">
		<i class="fa-solid fa-chart-line ms-2"></i> Dashboard
	</a>

	<a href="{{ route('reception.doctors.index') }}" class="active">
		<i class="fa-solid fa-user-doctor ms-2"></i> الأطباء
	</a>

	<a href="{{ route('reception.patients.index') }}">
		<i class="fa-solid fa-user ms-2"></i> المرضى
	</a>

	<a href="{{ route('reception.appointments.index') }}">
		<i class="fa-solid fa-calendar-check ms-2"></i> المواعيد
	</a>

	<a href="{{ route('reception.schedule.index') }}">
		<i class="fa-solid fa-clock ms-2"></i> جدول الأطباء
	</a>

	<form action="{{ route('logout') }}" method="POST">
		@csrf
		<button type="submit" class="text-danger">
			<i class="fa-solid fa-right-from-bracket ms-2"></i> تسجيل الخروج
		</button>
	</form>
</div>

<!-- Main Content -->
<div class="main-content">

	<h4 class="mb-4">
		مواعيد الطبيب: <span class="text-success">{{ $doctor->name }}</span>
	</h4>

	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<!-- Weekly Schedule -->
	<div class="card mb-4">
		<div class="card-body">
			<h6 class="fw-bold mb-3">➕ إعداد الجدول الأسبوعي (من السبت إلى الجمعة)</h6>

			<form method="POST" action="{{ route('reception.doctors.slots.store', $doctor) }}">
				@csrf

				@php
					$weekly = $slots->filter(function($s){ return !is_null($s->day_of_week); })->keyBy('day_of_week');
					$daysOrder = [6=>'السبت',0=>'الأحد',1=>'الإثنين',2=>'الثلاثاء',3=>'الأربعاء',4=>'الخميس',5=>'الجمعة'];
				@endphp

				<div class="table-responsive">
				<table class="table table-bordered text-center align-middle mb-3">
					<thead class="table-light">
						<tr>
							<th>اليوم</th>
							<th>نشط</th>
							<th>وقت البداية</th>
							<th>وقت النهاية</th>
						</tr>
					</thead>
					<tbody>
						@foreach($daysOrder as $dow => $label)
							@php $exist = $weekly->has($dow) ? $weekly->get($dow) : null; @endphp
							<tr>
								<td>{{ $label }}</td>
								<td>
									<input type="hidden" name="days[{{ $dow }}][enabled]" value="0">
									<input class="form-check-input" type="checkbox" name="days[{{ $dow }}][enabled]" value="1" {{ $exist ? 'checked' : '' }}>
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

				<div class="d-flex">
					<button class="btn btn-success ms-auto">
						<i class="fa-solid fa-save"></i> حفظ الجدول الأسبوعي
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

</body>
</html>

