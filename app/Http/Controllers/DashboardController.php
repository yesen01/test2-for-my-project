<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // نحجز المواعيد المرتبطة باليوزر
        $appointments = $user->appointments()->orderBy('date', 'asc')->get();

        return view('dashboard', compact('user', 'appointments'));
    }
}
