<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DoctorSlot;

class AdminScheduleController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('doctorSlots')->get();

        // next 7 days
        $dates = [];
        $start = \Carbon\Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            $d = $start->copy()->addDays($i);
            $dates[] = $d;
        }

        return view('admin.schedule.index', compact('doctors','dates'));
    }

    // Toggle a one-off slot for a doctor on a specific date/time
    public function toggle(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $dt = \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('time'));

        // find one-off slot with same start_at
        $slot = $doctor->doctorSlots()->whereNull('day_of_week')
            ->whereDate('start_at', $dt->toDateString())
            ->whereTime('start_at', $dt->format('H:i'))->first();

        if ($slot) {
            // delete slot (toggle off)
            $slot->delete();
            return response()->json(['status' => 'deleted']);
        }

        // create one-off slot
        $new = DoctorSlot::create([
            'doctor_id' => $doctor->id,
            'start_at' => $dt->toDateTimeString(),
            'end_at' => null,
        ]);

        return response()->json(['status' => 'created', 'slot_id' => $new->id]);
    }
}
