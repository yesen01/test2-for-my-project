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
        return view('Registration');
    }

    function LoginPost(Request $req)
    {
        $req->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        $credentials = $req->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('home'));
    }
    return redirect(route('login'))-> with('error', 'Login details are not valid');
    }

    function RegistrationPost(Request $req)
    {
        $req->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required'
        ]);
        $data['name']= $req->name;
        $data['email']= $req->email;
        $data['password']= Hash::make($req->password);

        $user = User::create($data);

        if (!$user) {
            return redirect(route('Registration'))-> with('error', 'resgistration failf, try again');
        }
            return redirect(route('login'))-> with('success', 'You have registered successfully');


    }

    function logout()
    {
        session()->flush();
        Auth::logout();
        return redirect(route('login'));
    }
}
