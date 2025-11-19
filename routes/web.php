<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;


Route::get('/', function () {
    return view('welcome');
}) -> name('home');


Route::get('/login',[AuthManager::class, 'login'] ) -> name('login');
Route::post('/login',[AuthManager::class, 'loginPost'] ) -> name('login.Post');
Route::get('/Registration',[AuthManager::class, 'Registration'] ) -> name('Registration');
Route::post('/Registration',[AuthManager::class, 'RegistrationPost'] ) -> name('Registration.Post');
route::get('/logout',[AuthManager::class, 'logout'] ) -> name('Logout');
