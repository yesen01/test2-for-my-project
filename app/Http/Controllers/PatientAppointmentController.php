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

        $doctors = Doctor::all();

        return view('patient.dashboard', compact('appointments','doctors'));
        $appointments = auth()->user()->appointments()->orderBy('date','asc')->get();

        return view('patient.appointments.index', compact('appointments'));
    }

    // حفظ موعد جديد
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date|after_or_equal:today',
            'time'      => 'required|string',
        ]);

        Auth::user()->appointments()->create(
            $request->only(['date','time','notes','doctor_id'])
        );

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
