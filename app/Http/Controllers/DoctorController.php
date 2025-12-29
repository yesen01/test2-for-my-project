<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorController extends Controller
{

    /**
     * Dashboard الطبيب (Blade)
     */
    public function dashboard()
    {
        $doctor = Auth::user();

        return view('doctor.dashboard', compact('doctor'));
    }

    // Patient-facing list of doctors
    public function index()
    {
        $doctors = Doctor::all();
        return view('patient.doctors.index', compact('doctors'));
    }

    // Show a doctor's weekly slots
    public function show(Doctor $doctor)
    {
        $slots = $doctor->doctorSlots()->orderBy('day_of_week')->orderBy('start_time')->get();
        return view('patient.doctors.show', compact('doctor','slots'));
    }

    /**
     * API: إرجاع أوقات الطبيب (لـ Flutter / Patient)
     */
    public function times(Doctor $doctor)
    {
        $raw = $doctor->available_times ?? '';

        $times = array_values(
            array_filter(
                array_map('trim', explode(',', $raw))
            )
        );

        return response()->json([
            'times' => $times,
        ]);


    }
}
