<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\AdminReceptionistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Receptionists\ReceptionistsAppointmentController; // تأكد من وجود هذا السطر في الأعلى




// تأكد أن الرابط يستخدم DashboardController ودالة index
Route::get('/dashboard', [DashboardController::class, 'index'])->name('patient.dashboard');

Route::post('/reception/appointments/{appointment}/remind', [ReceptionistsAppointmentController::class, 'manualRemind'])
    ->name('receptionists.appointments.manualRemind');



Route::post('/forgot-password', function (Request $request) {
    // 1. التحقق من صحة الإيميل المدخل
    $request->validate(['email' => 'required|email']);

    // 2. إرسال رابط إعادة التعيين عبر نظام Laravel الأساسي
    $status = Password::sendResetLink(
        $request->only('email')
    );

    // 3. التحقق من حالة الإرسال
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['success' => 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني.'])
        : back()->withErrors(['email' => 'لم نتمكن من العثور على مستخدم بهذا البريد الإلكتروني.']);
})->name('password.email');




// 1. مسار عرض صفحة "نسيت كلمة المرور"
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// 2. مسار استقبال البريد الإلكتروني (الذي تسبب في الخطأ)
Route::post('/forgot-password', function (Request $request) {
    // هنا سيتم وضع منطق إرسال الإيميل لاحقاً
    return back()->with('success', 'إذا كان البريد مسجلاً لدينا، فستصلك رسالة قريباً.');
})->name('password.email');



// Receptionist password edit/update routes
Route::get('/admin/receptionist/{id}/password', [AdminReceptionistController::class, 'editPassword'])->name('admin.receptionist.editPassword');
Route::put('/admin/receptionist/{id}/password', [AdminReceptionistController::class, 'updatePassword'])->name('admin.receptionist.updatePassword');




Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');

        // Receptionists management page
        Route::get('/receptionists', [AdminDashboardController::class,'receptionistsIndex'])->name('receptionists.index');
        // Receptionists
        Route::post('/receptionists/add', [AdminDashboardController::class,'addReceptionist'])->name('receptionists.add');
        Route::delete('/receptionists/{user}/delete', [AdminDashboardController::class,'deleteReceptionist'])->name('receptionists.delete');

        // ✅ NEW – Sidebar Pages
        Route::get('/departments', function () {
            return view('admin.departments.index');
        })->name('departments.index');

        Route::get('/doctors', function () {
            $doctors = \App\Models\Doctor::all();
            return view('admin.doctors.index', compact('doctors'));
        })->name('doctors.index');

        // Admin doctors actions
        Route::post('/doctors', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'store'])->name('doctors.store');
        Route::delete('/doctors/{doctor}', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::get('/doctors/{doctor}/slots', [\App\Http\Controllers\Admin\AdminDoctorSlotController::class, 'index'])->name('doctors.slots.index');
        Route::post('/doctors/{doctor}/slots', [\App\Http\Controllers\Admin\AdminDoctorSlotController::class, 'store'])->name('doctors.slots.store');
        Route::delete('/slots/{slot}', [\App\Http\Controllers\Admin\AdminDoctorSlotController::class, 'destroy'])->name('doctors.slots.destroy');

        Route::get('/patients', [\App\Http\Controllers\Admin\AdminPatientController::class, 'index'])->name('patients.index');
        Route::delete('/patients/{patient}', [\App\Http\Controllers\Admin\AdminPatientController::class, 'destroy'])->name('patients.destroy');

        Route::get('/appointments', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}/edit', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'update'])->name('appointments.update');
        Route::post('/appointments/{appointment}/manual-remind', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'manualRemind'])->name('appointments.manual_remind');
        Route::get('/appointments/doctor/{doctor}/available', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'available'])->name('appointments.doctor.available');
        Route::post('/appointments/admin/store', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'store'])->name('appointments.admin.store');
        Route::post('/appointments/{appointment}/approve', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'approve'])->name('appointments.approve');
        Route::post('/appointments/{appointment}/cancel', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::delete('/appointments/{appointment}', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

        Route::get('/schedule', [\App\Http\Controllers\Admin\AdminScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/schedule/{doctor}/toggle', [\App\Http\Controllers\Admin\AdminScheduleController::class, 'toggle'])->name('schedule.toggle');

    });




/*
|--------------------------------------------------------------------------
| الصفحة الرئيسية
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthManager::class, 'login'])->name('login');
Route::post('/login', [AuthManager::class, 'loginPost'])->name('login.Post');

Route::get('/Registration', [AuthManager::class, 'Registration'])->name('Registration');
Route::post('/Registration', [AuthManager::class, 'RegistrationPost'])->name('Registration.Post');

Route::get('/logout', [AuthManager::class, 'logout'])->name('Logout');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // إعادة توجيه بعد تسجيل الخروج
})->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboards
|--------------------------------------------------------------------------
*/


// Reception area routes (accessible to authenticated reception users)
Route::middleware('auth')
    ->prefix('reception')
    ->name('reception.')
    ->group(function () {

        // dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'receptionDashboard'])
            ->name('dashboard');

        // basic pages mapped to admin controllers but without Admin middleware
        Route::get('/doctors', function () {
            $doctors = \App\Models\Doctor::all();
            return view('reception.doctors.index', compact('doctors'));
        })->name('doctors.index');

        Route::get('/patients', [\App\Http\Controllers\Admin\AdminPatientController::class, 'index'])
            ->name('patients.index');


        // allow reception to perform doctor management actions (store/destroy/slots)
        Route::post('/doctors', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'store'])
            ->name('doctors.store');
        Route::delete('/doctors/{doctor}', [\App\Http\Controllers\Admin\AdminDoctorController::class, 'destroy'])
            ->name('doctors.destroy');
        Route::get('/doctors/{doctor}/slots', [\App\Http\Controllers\Admin\AdminDoctorSlotController::class, 'index'])
            ->name('doctors.slots.index');
        Route::post('/doctors/{doctor}/slots', [\App\Http\Controllers\Admin\AdminDoctorSlotController::class, 'store'])
            ->name('doctors.slots.store');
        Route::delete('/slots/{slot}', [\App\Http\Controllers\Admin\AdminDoctorSlotController::class, 'destroy'])
            ->name('doctors.slots.destroy');

        Route::get('/appointments', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'index'])
            ->name('appointments.index');
        Route::get('/appointments/{appointment}/edit', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'edit'])
            ->name('appointments.edit');
        Route::put('/appointments/{appointment}', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'update'])
            ->name('appointments.update');
        Route::post('/appointments/{appointment}/manual-remind', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'manualRemind'])
            ->name('appointments.manual_remind');
        Route::get('/appointments/doctor/{doctor}/available', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'available'])
            ->name('appointments.doctor.available');
        Route::post('/appointments/admin/store', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'store'])
            ->name('appointments.admin.store');
        Route::post('/appointments/{appointment}/approve', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'approve'])
            ->name('appointments.approve');
        Route::post('/appointments/{appointment}/cancel', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'cancel'])
            ->name('appointments.cancel');
        Route::delete('/appointments/{appointment}', [\App\Http\Controllers\Admin\AdminAppointmentController::class, 'destroy'])
            ->name('appointments.destroy');

        // allow reception to delete patients
        Route::delete('/patients/{patient}', [\App\Http\Controllers\Admin\AdminPatientController::class, 'destroy'])
            ->name('patients.destroy');

        Route::get('/schedule', [\App\Http\Controllers\Admin\AdminScheduleController::class, 'index'])
            ->name('schedule.index');

    });

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // داشبورد المريض (حجز)
    Route::get('/patient/dashboard', [PatientAppointmentController::class, 'index'])
        ->name('patient.dashboard');

    // حفظ موعد
    Route::post('/patient/dashboard', [PatientAppointmentController::class, 'store'])
        ->name('patient.dashboard.store');

    // صفحة مواعيدي
    Route::get('/patient/appointments', [PatientAppointmentController::class, 'myAppointments'])
        ->name('patient.appointments');

    // إلغاء موعد
    Route::delete('/patient/appointments/{appointment}', [PatientAppointmentController::class, 'destroy'])
        ->name('patient.appointments.destroy');

        // صفحة تعديل (نستعملها لاحقاً)
    Route::get('/patient/appointments/{appointment}/edit', [PatientAppointmentController::class, 'edit'])
        ->name('patient.appointments.edit');

        Route::put('/patient/appointments/{appointment}', [PatientAppointmentController::class, 'update'])
    ->name('patient.appointments.update');

        // patient accepts an admin reschedule
        Route::post('/patient/appointments/{appointment}/accept', [PatientAppointmentController::class, 'acceptReschedule'])
            ->name('patient.appointments.accept');
});

/*
|--------------------------------------------------------------------------
| Doctor
|--------------------------------------------------------------------------
*/
Route::get('/doctor-times/{doctor}', [DoctorController::class, 'times']);

// Patient doctor listing and show
Route::get('/doctors', [\App\Http\Controllers\DoctorController::class, 'index'])->name('patient.doctors.index');
Route::get('/doctors/{doctor}', [\App\Http\Controllers\DoctorController::class, 'show'])->name('patient.doctors.show');

// Appointment booking by slot
Route::post('/appointments', [\App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Default dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');



