<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAppointments = Appointment::count();
        $completedAppointments = Appointment::where('status','completed')->count();
        $noShowAppointments = Appointment::where('status','no_show')->count();
        $noShowRate = $totalAppointments > 0 ? round(($noShowAppointments / $totalAppointments) * 100,2) : 0;

        $receptionists = User::where('role','reception')->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalAppointments', 'completedAppointments', 'noShowRate', 'receptionists'
        ));
    }

    // إضافة Receptionist جديد
    public function addReceptionist(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);

        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>'reception'
        ]);

        return redirect()->back()->with('success','تم إضافة موظف الاستقبال بنجاح');
    }

    // حذف Receptionist
    public function deleteReceptionist(User $user)
    {
        if($user->role === 'reception'){
            $user->delete();
            return redirect()->back()->with('success','تم حذف موظف الاستقبال بنجاح');
        }
        return redirect()->back()->with('error','لا يمكن حذف هذا المستخدم');
    }
}
