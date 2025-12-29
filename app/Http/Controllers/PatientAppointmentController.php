<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Doctor;

class PatientAppointmentController extends Controller
{
    // صفحة لوحة المريض + الحجز
    public function index()
    {
        $appointments = Auth::user()
            ->appointments()
            ->orderBy('date','asc')
            ->get();
        // eager load slots for the dashboard to avoid N+1 when rendering availability
        $doctors = Doctor::with('doctorSlots')->get();

        return view('patient.dashboard', compact('appointments','doctors'));
    }

    // حفظ موعد جديد
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day'       => 'nullable|integer|min:0|max:6',
            'time'      => 'required|string',
        ]);

        // if a weekday was chosen, compute the next calendar date for that weekday
        $date = null;
        if ($request->filled('day')) {
            $day = (int) $request->input('day');
            $today = \Carbon\Carbon::now();
            $candidate = $today->copy()->startOfDay();
            $daysToAdd = ($day - $candidate->dayOfWeek + 7) % 7;

            // if the chosen day is today, ensure the chosen time is in the future
            if ($daysToAdd === 0) {
                $chosenTime = \Carbon\Carbon::createFromFormat('H:i', $request->input('time'));
                $nowTime = \Carbon\Carbon::now();
                $chosenDateTime = $candidate->copy()->setTimeFromTimeString($request->input('time'));
                if ($chosenDateTime->lte($nowTime)) {
                    $daysToAdd = 7; // schedule next week
                }
            }

            $date = $candidate->addDays($daysToAdd)->toDateString();
        }

        // if date computed or provided, create appointment
        Auth::user()->appointments()->create([
            'doctor_id' => $request->input('doctor_id'),
            'date' => $date ?? $request->input('date'),
            'time' => $request->input('time'),
            'notes' => $request->input('notes'),
        ]);

        return redirect()->back()->with('success', 'تم حجز الموعد بنجاح!');
    }

    // صفحة مواعيدي (منفصلة)
    public function myAppointments()
    {
        $appointments = Appointment::where('user_id', Auth::id())
            ->orderBy('date', 'asc')
            ->get();

        return view('patient.appointments', compact('appointments'));
    }

    // إلغاء موعد
    public function destroy(Appointment $appointment)
    {
        if (Auth::id() !== $appointment->user_id) {
            abort(403);
        }

        $appointment->delete();

        return redirect()->back()->with('success', 'تم إلغاء الموعد بنجاح');
    }

    public function edit(Appointment $appointment)
{
    if ($appointment->user_id !== auth()->id()) {
        abort(403);
    }

    return view('patient.edit', compact('appointment'));
}



////////////////////////
    public function update(Request $request, Appointment $appointment)
{
    if ($appointment->user_id !== auth()->id()) {
        abort(403);
    }

    // التحقق من البيانات
    $request->validate([
        'date' => 'required|date',
        'time' => 'required',
        'notes' => 'nullable|string',
    ]);

    // تحديث الموعد
    $appointment->update($request->only('date', 'time', 'notes'));

    // وضع رسالة نجاح في الجلسة
    return redirect()->back()->with('success', 'تم تحديث الموعد بنجاح');
}


}
