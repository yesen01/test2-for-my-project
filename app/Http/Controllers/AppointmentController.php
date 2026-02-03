<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\DoctorSlot;

class AppointmentController extends Controller
{
    // Book by slot
    public function store(Request $request)
    {
        $request->validate([
            'doctor_slot_id' => 'required|exists:doctor_slots,id',
            'date' => 'nullable|date|after_or_equal:today'
        ]);

        $slot = DoctorSlot::findOrFail($request->input('doctor_slot_id'));

        // Determine booking date:
        if (!is_null($slot->day_of_week)) {
            // weekly slot requires a date provided by the patient
            if (!$request->filled('date')) {
                return redirect()->back()->withErrors(['date' => 'Please select a date for this weekly slot.']);
            }

            $date = \Carbon\Carbon::parse($request->input('date'));
            if ($date->dayOfWeek !== (int) $slot->day_of_week) {
                return redirect()->back()->withErrors(['date' => 'Chosen date does not match the slot weekday.']);
            }
        } else {
            // one-off slot: use slot->start_at date
            if ($slot->start_at) {
                $date = $slot->start_at->copy();
            } else {
                // Fallback: require date
                if (!$request->filled('date')) {
                    return redirect()->back()->withErrors(['date' => 'Please select a date.']);
                }
                $date = \Carbon\Carbon::parse($request->input('date'));
            }
        }

        // determine time to save
        if (!empty($slot->start_time)) {
            $time = $slot->start_time;
        } elseif (!empty($slot->start_at)) {
            $time = $slot->start_at->format('H:i');
        } else {
            $time = $request->input('time', null);
        }

        // prevent booking if slot already taken for that date
        $booked = \App\Models\Appointment::where('doctor_slot_id', $slot->id)
            ->where('date', $date->toDateString())
            ->exists();

        if ($booked) {
            return redirect()->back()->withErrors(['slot' => 'هذا الموعد محجوز بالفعل.']);
        }

        DB::transaction(function () use ($slot, $date, $time) {
            // double-check inside transaction
            $already = \App\Models\Appointment::where('doctor_slot_id', $slot->id)
                ->where('date', $date->toDateString())
                ->exists();
            if ($already) {
                throw new \Exception('Slot already booked');
            }

            Auth::user()->appointments()->create([
                'doctor_id' => $slot->doctor_id,
                'doctor_slot_id' => $slot->id,
                'date' => $date->toDateString(),
                'time' => $time,
                'status' => 'pending'
            ]);
        });

        return redirect()->back()->with('success', 'Appointment booked.');
    }


    public function destroy(Appointment $appointment)
{
    // التأكد من أن الموعد يخص المريض الحالي وليس قديماً
    if ($appointment->patient_id !== auth()->id()) {
        abort(403);
    }

    $appointmentDateTime = \Carbon\Carbon::parse($appointment->date . ' ' . $appointment->time);
    if ($appointmentDateTime->isPast()) {
        return redirect()->back()->with('error', 'لا يمكن إلغاء موعد قديم منتهي.');
    }

    $appointment->delete();

    return redirect()->route('patient.appointments')->with('success', 'تم إلغاء الحجز بنجاح.');
}

public function acceptNotification(Appointment $appointment)
{
    // التأكد أن الموعد يخص المريض
    if ($appointment->patient_id !== auth()->id()) {
        abort(403);
    }

    // تغيير الحالة إلى مؤكد ليختفي من الإشعارات
    $appointment->update([
        'status' => 'confirmed'
    ]);

    return redirect()->back()->with('success', 'تم تأكيد الموعد وإزالة التنبيه.');
}
}
