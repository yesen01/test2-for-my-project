<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إدارة موظفي الاستقبال</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
body{ background:#f4f6f9; overflow-x:hidden; font-family:Tahoma, sans-serif; margin:0; }
.sidebar{ width:250px; height:100vh; background:#0f766e; color:#fff; position:fixed; top:0; right:0; z-index:1000; }
.sidebar h4{ padding:20px; border-bottom:1px solid rgba(255,255,255,.2); margin:0; }
.sidebar a, .sidebar button{ color:#fff; text-decoration:none; padding:12px 20px; display:block; width:100%; background:none; border:none; text-align:right; }
.sidebar a:hover, .sidebar a.active, .sidebar button:hover{ background:rgba(255,255,255,.15); }
.main-content{ margin-right:250px; min-height:100vh; padding:30px; }
.card{ border-radius:14px; }
</style>
</head>
<body>

<div class="sidebar">
    <h4>مركز كيان لطب وجراحة الاسنان </h4>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line ms-2"></i>
        لوحة التحكم
    </a>

    <a href="{{ route('admin.doctors.index') }}" class="{{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-doctor ms-2"></i>
        الأطباء
    </a>

    <a href="{{ route('admin.patients.index') }}" class="{{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user ms-2"></i>
        المرضى
    </a>

    <a href="{{ route('admin.receptionists.index') }}" class="{{ request()->routeIs('admin.receptionists.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users ms-2"></i>
        موظفو الاستقبال
    </a>

    <a href="{{ route('admin.appointments.index') }}" class="{{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check ms-2"></i>
        المواعيد
    </a>

    <a href="{{ route('admin.schedule.index') }}" class="{{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
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

<div class="main-content">
    <h4 class="mb-4">ادارة موظفي الاستقبال</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">موظفو الاستقبال</h5>
            <small class="text-muted">إدارة حسابات موظفي الاستقبال</small>
        </div>

        <div class="row">
            <div class="col-md-5">
                <form action="{{ route('admin.receptionists.add') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">الاسم</label>
                        <input name="name" class="form-control" required />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input name="email" type="email" class="form-control" required />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">كلمة المرور</label>
                        <input name="password" type="password" class="form-control" required minlength="6" />
                    </div>
                    <button class="btn btn-success">إضافة</button>
                </form>
            </div>

            <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>البريد</th>
                <th>كلمة المرور</th>
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receptionists as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->email }}</td>
                    <td>
                        <a href="{{ route('admin.receptionist.editPassword', $r->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-key ms-1"></i>
                            تعيين كلمة مرور
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('admin.receptionists.delete', $r) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟');" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash ms-1"></i>
                                حذف
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">لا يوجد موظفون بعد</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
