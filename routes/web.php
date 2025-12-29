<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Middleware\AdminMiddleware;


Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');

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


Route::get('/reception/dashboard', function () {
    return view('reception.dashboard');
})->name('reception.dashboard')->middleware('auth');

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



