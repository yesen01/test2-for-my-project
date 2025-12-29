<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>قائمة المواعيد - HealthEase</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

	<style>
		body { background:#f4f6f9; font-family: Tahoma, sans-serif; }
		.container { padding-top:20px; }

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

<div class="container py-4">
	<h3 class="mb-3">قائمة المواعيد</h3>

	<div class="mb-4">
		<h5>الأطباء</h5>
		<div class="d-flex gap-2 flex-wrap">
			@foreach($doctors as $doctor)
				<button class="btn btn-outline-primary btn-sm show-doctor" data-doctor-id="{{ $doctor->id }}">{{ $doctor->name }}</button>
			@endforeach
		</div>
	</div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th>Patient Name</th>
				<th>Doctor Name</th>
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
				<td>{{ $appointment->status ?? 'pending' }}</td>
				<td>
					<form action="{{ route('admin.appointments.approve', $appointment) }}" method="POST" style="display:inline">
						@csrf
						<button class="btn btn-success btn-sm" type="submit">Approve</button>
					</form>

					<form action="{{ route('admin.appointments.cancel', $appointment) }}" method="POST" style="display:inline">
						@csrf
						<button class="btn btn-warning btn-sm" type="submit">Cancel</button>
					</form>

					<form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" style="display:inline">
						@csrf
						@method('DELETE')
						<button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Delete appointment?')">Delete</button>
					</form>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<!-- Booking Modal -->
	<div class="modal fade" id="bookingModal" tabindex="-1">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Create Appointment for <span id="modalDoctorName"></span></h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
		  </div>
		  <form id="adminBookingForm" method="POST" action="{{ route('admin.appointments.admin.store') }}">
		  @csrf
		  <div class="modal-body">
				<input type="hidden" name="doctor_slot_id" id="modal_slot_id">
				<div class="mb-3">
					<label>Patient</label>
					<select name="patient_id" class="form-control" required>
						<option value="">-- اختر مريض --</option>
						@foreach($patients as $p)
							<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label>Choose Date/Time</label>
					<div id="availableTimes">
						<p class="text-muted">Pick a doctor to load availability.</p>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary">Create Appointment</button>
		  </div>
		  </form>
		</div>
	  </div>
	</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function(){
		const buttons = document.querySelectorAll('.show-doctor');
		const modalEl = document.getElementById('bookingModal');
		const modal = new bootstrap.Modal(modalEl);
		const form = document.getElementById('adminBookingForm');

		buttons.forEach(btn => {
			btn.addEventListener('click', async () => {
				const id = btn.dataset.doctorId;
				// fetch availability
				const res = await fetch(`{{ url('/admin/appointments/doctor') }}/${id}/available`);
				const data = await res.json();
				const container = document.getElementById('availableTimes');
				container.innerHTML = '';

				if (data.length === 0) {
					container.innerHTML = '<p class="text-muted">No available slots in next 14 days.</p>';
				} else {
					// group by date
					const byDate = {};
					data.forEach(item => {
						if (!byDate[item.date]) byDate[item.date] = [];
						byDate[item.date].push(item);
					});

					for (const date in byDate) {
						const box = document.createElement('div');
						box.className = 'mb-2';
						const header = document.createElement('div');
						header.innerHTML = `<strong>${date}</strong>`;
						box.appendChild(header);
						byDate[date].forEach(slot => {
							const item = document.createElement('div');
							item.className = 'form-check';
							item.innerHTML = `<input class="form-check-input" type="radio" name="slot_radio" value="${slot.slot_id}||${slot.date}||${slot.time}" id="slot_${slot.slot_id}_${slot.date}" ${slot.booked ? 'disabled' : ''}>
								<label class="form-check-label" for="slot_${slot.slot_id}_${slot.date}">${slot.time} ${slot.booked ? '(booked)' : ''}</label>`;
							box.appendChild(item);
						});
						container.appendChild(box);
					}

					// attach change listeners to radios so we set hidden inputs immediately
					const radios = container.querySelectorAll('input[name="slot_radio"]');
					radios.forEach(r => r.addEventListener('change', function(){
						const parts = this.value.split('||');
						const hidden = document.getElementById('modal_slot_id');
						hidden.value = parts[0];

						let dateInput = form.querySelector('input[name="date"]');
						if (!dateInput) {
							dateInput = document.createElement('input');
							dateInput.type = 'hidden';
							dateInput.name = 'date';
							form.appendChild(dateInput);
						}
						dateInput.value = parts[1];

						let timeInput = form.querySelector('input[name="time"]');
						if (!timeInput) {
							timeInput = document.createElement('input');
							timeInput.type = 'hidden';
							timeInput.name = 'time';
							form.appendChild(timeInput);
						}
						timeInput.value = parts[2];
					}));
				}

				// set modal title
				document.getElementById('modalDoctorName').textContent = btn.textContent.trim();
				modal.show();
			});
		});
	});
</script>

</body>
</html>
