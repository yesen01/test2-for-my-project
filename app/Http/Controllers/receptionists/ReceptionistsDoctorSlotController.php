<?php

namespace App\Http\Controllers\Receptionists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DoctorSlot;

class ReceptionistsDoctorSlotController extends Controller
{
    public function index(Doctor $doctor)
    {
        $slots = $doctor->doctorSlots()->orderBy('day_of_week')->orderBy('start_time')->get();
        return view('reception.doctors.slots', compact('doctor','slots'));

    }



    public function store(Request $request, Doctor $doctor)
    {
        // Accept either a single date slot OR a weekly schedule via `days` array
        $request->validate([
            'date' => 'nullable|date',
            'days' => 'nullable|array',
            'start_time' => 'nullable', // used for one-off date
            'end_time' => 'nullable',
        ]);

        // If a specific date is provided, create a one-off slot for that date
        if ($request->filled('date')) {
            $startAt = \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('start_time'));
            $endAt = $request->filled('end_time') ? \Carbon\Carbon::parse($request->input('date') . ' ' . $request->input('end_time')) : null;

            DoctorSlot::create([
                'doctor_id' => $doctor->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
            ]);

            return redirect()->back()->with('success', 'One-off slot added.');
        }

        // Otherwise handle weekly schedule sent as `days[dow][enabled,start_time,end_time]`
        $days = $request->input('days', []);
        if (!empty($days)) {
            // remove existing weekly slots for this doctor (preserve one-off slots where day_of_week is null)
            DoctorSlot::where('doctor_id', $doctor->id)->whereNotNull('day_of_week')->delete();

            foreach ($days as $dow => $data) {
                if (empty($data) || empty($data['enabled'])) {
                    continue;
                }

                $start_time = $data['start_time'] ?? null;
                if (empty($start_time)) {
                    continue; // skip invalid entry
                }
                $end_time = $data['end_time'] ?? null;

                // compute the next calendar date for this weekday (0=Sun..6=Sat)
                $nextDate = \Carbon\Carbon::now()->startOfDay();
                while ($nextDate->dayOfWeek !== (int) $dow) {
                    $nextDate->addDay();
                }

                $startAt = \Carbon\Carbon::parse($nextDate->format('Y-m-d') . ' ' . $start_time);
                $endAt = $end_time ? \Carbon\Carbon::parse($nextDate->format('Y-m-d') . ' ' . $end_time) : null;

                DoctorSlot::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => (int) $dow,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                ]);
            }

            return redirect()->back()->with('success', 'Weekly slots updated.');
        }

        return redirect()->back()->withErrors(['weekdays' => 'Please provide a date or at least one weekday.']);
    }

    public function destroy(DoctorSlot $slot)
    {
        $slot->delete();
        return redirect()->back()->with('success', 'Slot deleted.');
    }
}
