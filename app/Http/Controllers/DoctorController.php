<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function times(Doctor $doctor)
    {
        $raw = $doctor->available_times ?? '';
        $times = array_values(array_filter(array_map('trim', explode(',', $raw)), function($t) { return $t !== ''; }));

        return response()->json([
            'times' => $times,
        ]);

        $doctor = Auth::user();

        return view('doctor.dashboard', compact('doctor'));

    }
}
