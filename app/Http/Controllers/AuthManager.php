<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthManager extends Controller
{
    function login()
    {
        return view('login');
    }
    function registration()
    {
        return view('registration');
    }

    function loginPost(Request $req) {
    $req->validate([
        'email'=>'required|email',
        'password'=>'required'
    ]);

    // أولاً نشوف هل الايميل موجود في قاعدة البيانات
    $user = User::where('email', $req->email)->first();

    if (!$user) {
        // الايميل مش موجود
        return redirect()->route('login')->with('error', '   uncroect email ');
    }

    // تحقق من الباسورد
    if (!Hash::check($req->password, $user->password)) {
        return redirect()->route('login')->with('error', 'uncroect password ');
    }

    // تسجيل الدخول
    Auth::login($user);

    // توجيه حسب role
    switch ($user->role) {
    case 'admin':
        return redirect()->route('admin.dashboard');

    case 'reception':
        return redirect()->route('reception.dashboard');

    default:
        return redirect()->route('patient.dashboard');
}




    }

    function RegistrationPost(Request $req)
    {
        $req->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['name'] = $req->name;
        $data['email'] = $req->email;
        $data['password'] = Hash::make($req->password);
        $data['role'] = 'patient';

        $user = User::create($data);

        if (! $user) {
            if ($req->expectsJson()) {
                return response()->json(['message' => 'Registration failed'], 500);
            }
            return redirect()->route('registration')->with('error', 'Registration failed, try again');
        }

        // If request expects JSON (AJAX), return JSON response
        if ($req->expectsJson()) {
            return response()->json([
                'message' => 'You have registered successfully',
                'token' => null,
            ], 201);
        }

        return redirect()->route('login')->with('success', 'You have registered successfully');
    }

    function logout()
    {
        session()->flush();
        Auth::logout();
        return redirect(route('login'));
    }
}
