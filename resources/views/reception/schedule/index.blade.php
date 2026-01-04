<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>جدول الأطباء - HealthEase</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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

/* Fix Bootstrap RTL row overlap */
.main-content .row{
	margin-right:0;
	margin-left:0;
}

/* Table / Cards */
.table{
	background:#fff;
	border-radius:14px;
	overflow:hidden;
}
th, td{
	vertical-align:middle !important;
}

/* Doctor cell */
.doc-cell{
	display:flex;
	align-items:center;
	gap:.5rem;
}

/* Slot styles */
.slot-badge{
	padding:2px 6px;
	border-radius:5px;
	font-size:.8rem;
	display:inline-block;
}
.slot-weekly{
	background:#e7f1ff;
	color:#0d6efd;
}
.slot-free{
	background:#d4edda;
	color:#155724;
}
.slot-booked{
	background:#f8d7da;
	color:#721c24;
}
.slot-row{
	margin-bottom:4px;
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
			<i class="fa-solid fa-right-from-bracket ms-2"></i>
			تسجيل الخروج
		</button>
	</form>
</div>

<!-- Main Content -->
<div class="main-content">

	<header class="mb-4 d-flex justify-content-between align-items-center">
		<h3 class="mb-0">جدول الأطباء</h3>
	</header>

	<div class="table-responsive">
		<table class="table table-hover align-middle text-center">
			<thead class="table-light">
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
					<td class="doc-cell justify-content-center">
						{{ $doctor->name }}
					</td>

					@foreach(['sun','mon','tue','wed','thu','fri','sat'] as $day)
					<td>
						@php
							$dayMap = ['sun'=>0,'mon'=>1,'tue'=>2,'wed'=>3,'thu'=>4,'fri'=>5,'sat'=>6];
							$dayIndex = $dayMap[$day];

							$weeklySlots = $doctor->doctorSlots->filter(fn($s)=>
								!is_null($s->day_of_week) && (int)$s->day_of_week === $dayIndex
							);

							$oneOffSlots = $doctor->doctorSlots->filter(fn($s)=>
								is_null($s->day_of_week) && $s->start_at && (int)$s->start_at->dayOfWeek === $dayIndex
							);
						@endphp

						@if($weeklySlots->isNotEmpty())
							@foreach($weeklySlots as $s)
								<div class="slot-row">
									<span class="slot-badge slot-weekly">
										{{ $s->start_time }} @if($s->end_time)- {{ $s->end_time }}@endif
									</span>
									<small class="text-muted">أسبوعي</small>
								</div>
							@endforeach

						@elseif($oneOffSlots->isNotEmpty())
							@foreach($oneOffSlots as $s)
								@php $booked = $s->isBookedOnDate($s->start_at); @endphp
								<div class="slot-row">
									<span class="slot-badge {{ $booked ? 'slot-booked' : 'slot-free' }}">
										{{ $s->start_at->format('H:i') }}
									</span>
									<small class="text-muted">
										{{ $booked ? 'محجوز' : 'متاح' }}
									</small>
								</div>
							@endforeach

						@else
							<span class="text-danger">غير متاح</span>
						@endif
					</td>
					@endforeach
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

