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
            return view('admin.doctors.index');
        })->name('doctors.index');

        Route::get('/patients', function () {
            return view('admin.patients.index');
        })->name('patients.index');

        Route::get('/appointments', function () {
            return view('admin.appointments.index');
        })->name('appointments.index');

        Route::get('/schedule', function () {
            return view('admin.schedule.index');
        })->name('schedule.index');

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

/*
|--------------------------------------------------------------------------
| Default dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');



