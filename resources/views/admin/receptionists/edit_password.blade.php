<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body{ background:#f4f6f9; font-family:Tahoma, sans-serif; }
        .main-content{ padding:30px; }
        .card { border-radius: 14px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-save { background: #0f766e; color: white; border: none; }
        .btn-save:hover { background: #0d635d; color: white; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <a href="{{ route('admin.receptionists.index') }}" class="btn btn-link text-dark mb-3">
                    <i class="fa-solid fa-arrow-right"></i> العودة للقائمة
                </a>

                <div class="card p-4">
                    <h5 class="mb-4">تغيير كلمة مرور الموظف: <span class="text-primary">{{ $receptionist->name }}</span></h5>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.receptionist.updatePassword', $receptionist->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" name="password" class="form-control" placeholder="أدخل 6 أرقام أو حروف على الأقل" required>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-save">حفظ كلمة المرور الجديدة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
