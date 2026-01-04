<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إدارة الأطباء</title>

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
	<h4>مركز كيان لطب وجراحة الاسنان </h4>

	<a href="{{ route('reception.dashboard') }}">
		<i class="fa-solid fa-chart-line ms-2"></i> Dashboard
	</a>

	<a href="{{ route('reception.doctors.index') }}" class="active">
		<i class="fa-solid fa-user-doctor ms-2"></i> الأطباء
	</a>

	<a href="{{ route('reception.patients.index') }}">
		<i class="fa-solid fa-user ms-2"></i> المرضى
	</a>

		<a href="{{ route('admin.receptionists.index') }}" class="{{ request()->routeIs('admin.receptionists.*') ? 'active' : '' }}">
		<i class="fa-solid fa-users ms-2"></i>
		موظفو الاستقبال
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

	<h4 class="mb-4">إدارة الأطباء</h4>

	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<!-- Add Doctor -->
	<div class="card mb-4">
		<div class="card-body">
			<h6 class="mb-3 fw-bold">➕ إضافة طبيب</h6>
			<form method="POST" action="{{ route('admin.doctors.store') }}">
				@csrf
				<div class="mb-3">
					<label class="form-label">اسم الطبيب</label>
					<input type="text" name="name" class="form-control" required>
				</div>
				<button class="btn btn-success">
					<i class="fa-solid fa-plus"></i> إضافة
				</button>
			</form>
		</div>
	</div>

	<!-- Doctors List -->
	<div class="card">
		<div class="card-body">
			<h6 class="mb-3 fw-bold">📋 قائمة الأطباء</h6>

			<ul class="list-group">
				@foreach($doctors as $doctor)
					<li class="list-group-item d-flex justify-content-between align-items-center">
						<strong>{{ $doctor->name }}</strong>

						<div class="d-flex gap-2">
							<a href="{{ route('reception.doctors.slots.index', $doctor) }}"
							   class="btn btn-sm btn-outline-secondary">
								<i class="fa-solid fa-clock"></i> إدارة المواعيد
							</a>

							<form method="POST" action="{{ route('reception.doctors.destroy', $doctor) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطبيب؟');">
								@csrf
								@method('DELETE')
								<button type="submit" class="btn btn-sm btn-danger">
									<i class="fa-solid fa-trash"></i> حذف
								</button>
							</form>
						</div>
					</li>
				@endforeach
			</ul>
		</div>
	</div>

</div>

</body>
</html>

