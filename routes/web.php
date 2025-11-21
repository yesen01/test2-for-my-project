<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;


Route::get('/', function () {
    return view('welcome');
}) -> name('home');
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ContactController;

// فورم حجز موعد
Route::post('/appointment-submit', [AppointmentController::class, 'submit'])
    ->name('appointment.submit')
    ->middleware('auth'); // 👈 هذا يخلي الفورم يطلب تسجيل دخول
// فورم اتصل بنا
Route::post('/contact-submit', [ContactController::class, 'submit'])->name('contact.submit');


Route::get('/login',[AuthManager::class, 'login'] ) -> name('login');
Route::post('/login',[AuthManager::class, 'loginPost'] ) -> name('login.Post');
Route::get('/Registration',[AuthManager::class, 'Registration'] ) -> name('Registration');
Route::post('/Registration',[AuthManager::class, 'RegistrationPost'] ) -> name('Registration.Post');
route::get('/logout',[AuthManager::class, 'logout'] ) -> name('Logout');

// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');

// Reception Dashboard
Route::get('/reception/dashboard', function () {
    return view('reception.dashboard');
})->name('reception.dashboard')->middleware('auth');

// Patient Dashboard
Route::get('/patient/dashboard', function () {
    return view('patient.dashboard');
})->name('patient.dashboard')->middleware('auth');

