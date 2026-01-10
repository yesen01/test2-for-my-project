<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\User;
use App\Jobs\SendAppointmentReminder;
use Carbon\Carbon;

class AdminAppointmentController extends Controller
{
    // list all appointments and doctors for admin
    public function index()
    {
        $appointments = Appointment::with(['user','doctor'])->orderBy('date','desc')->get();
        $doctors = Doctor::all();

        $patients = User::where('role', 'patient')->get();

        $viewPrefix = auth()->user()->role === 'admin' ? 'admin' : 'reception';

        return view("{$viewPrefix}.appointments.index", compact('appointments','doctors','patients'));
        // return view('reception.appointments.index', compact('appointments','doctors','patients'));
        // return view('admin.appointments.index', compact('appointments','doctors','patients'));
    }

    // return available upcoming dates/times for a doctor (JSON)
    public function available(Doctor $doctor)
    {
        $today = \Carbon\Carbon::today();
        $days = [];

        // for the next 14 days, collect available slots
        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            $weekday = $date->dayOfWeek;

            foreach ($doctor->doctorSlots as $slot) {
                // weekly slot
                if (!is_null($slot->day_of_week) && (int)$slot->day_of_week === $weekday) {
                    $booked = $slot->isBookedOnDate($date);
                    $days[] = [
                        'date' => $date->toDateString(),
                        'time' => $slot->start_time ?? ($slot->start_at ? $slot->start_at->format('H:i') : null),
                        'slot_id' => $slot->id,
                        'booked' => $booked,
                    ];
                }

                // one-off slot matching the date
                if (is_null($slot->day_of_week) && $slot->start_at) {
                    if ($slot->start_at->isSameDay($date)) {
                        $booked = $slot->isBookedOnDate($date);
                        $days[] = [
                            'date' => $date->toDateString(),
                            'time' => $slot->start_at->format('H:i'),
                            'slot_id' => $slot->id,
                            'booked' => $booked,
                        ];
                    }
                }
            }
        }

        return response()->json($days);
    }

    // Admin books on behalf of a patient
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_slot_id' => 'required|exists:doctor_slots,id',
            'date' => 'required|date',
        ]);

        $patient = User::findOrFail($request->input('patient_id'));
        $slot = DoctorSlot::findOrFail($request->input('doctor_slot_id'));

        // prevent double-book for any patient on this slot/date
        $exists = Appointment::where('doctor_slot_id', $slot->id)
            ->where('date', \Carbon\Carbon::parse($request->input('date'))->toDateString())
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['slot' => 'The selected slot is already booked for this date.']);
        }

        Appointment::create([
            'user_id' => $patient->id,
            'doctor_id' => $slot->doctor_id,
            'doctor_slot_id' => $slot->id,
            'date' => \Carbon\Carbon::parse($request->input('date'))->toDateString(),
            'time' => $slot->start_time ?? ($slot->start_at ? $slot->start_at->format('H:i') : $request->input('time')),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Appointment created for patient.');
    }

    public function approve(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed']);
        return redirect()->back()->with('success', 'Appointment approved.');
    }

    // Show edit form for an appointment (admin)
    public function edit(Appointment $appointment)
    {
        $appointment->load(['user','doctor']);

        $viewPrefix = auth()->user()->role === 'admin' ? 'admin' : 'reception';

    return view("{$viewPrefix}.appointments.edit", compact('appointment'));
        // return view('admin.appointments.edit', compact('appointment'));
        // return view('reception.appointments.edit', compact('appointment'));
    }

    // Update appointment time/date by admin and mark as rescheduled
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $appointment->update([
            'date' => \Carbon\Carbon::parse($request->input('date'))->toDateString(),
            'time' => $request->input('time'),
            'status' => 'rescheduled',
        ]);

        $prefix = auth()->user()->role === 'admin' ? 'admin' : 'reception';
        return redirect()->route("{$prefix}.appointments.index")
            ->with('success', 'Appointment rescheduled; patient will be notified.');
        // return redirect()->route('admin.appointments.index')->with('success', 'Appointment rescheduled; patient will be notified.');
        // return redirect()->route('reception.appointments.index')->with('success', 'Appointment rescheduled; patient will be notified.');
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Appointment cancelled.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->back()->with('success', 'Appointment deleted.');
    }

    // Manual reminder: schedule/send reminder 1 hour before appointment
    public function manualRemind(Appointment $appointment)
    {
        $user = $appointment->user;
        if (!$user || !$user->email) {
            return redirect()->back()->with('error', 'No patient email available to send reminder.');
        }

        $dt = Carbon::parse($appointment->date . ' ' . $appointment->time);
        $sendAt = $dt->copy()->subHour();
        $now = Carbon::now();

        $job = new SendAppointmentReminder($appointment);
        if ($sendAt->lte($now)) {
            dispatch($job);
            $msg = 'Reminder sent immediately (appointment within an hour).';
        } else {
            dispatch($job)->delay($sendAt->diffInSeconds($now));
            $msg = 'Reminder scheduled to be sent 1 hour before the appointment.';
        }

        return redirect()->back()->with('success', $msg);
    }
}
