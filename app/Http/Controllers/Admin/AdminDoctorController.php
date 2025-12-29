<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;

class AdminDoctorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Doctor::create(['name' => $request->input('name')]);

        return redirect()->back()->with('success', 'Doctor added.');
    }

    public function destroy(\App\Models\Doctor $doctor)
    {
        // delete doctor (slots cascade)
        $doctor->delete();

        return redirect()->back()->with('success', 'Doctor deleted.');
    }
}
