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

        // جلب الأطباء مع علاقة المواعيد
    $doctors = \App\Models\Doctor::with('doctorSlots')->get();

    $availability = [];

    foreach ($doctors as $doctor) {
        foreach ($doctor->doctorSlots as $slot) {
            // نتحقق إذا كان الموعد أسبوعي (يحتوي على day_of_week)
            if ($slot->day_of_week !== null) {
                $availability[$doctor->id][$slot->day_of_week][] = [
                    'start' => $slot->start_time, // الحقل المخزن في الكنترولر الخاص بك
                    'end'   => $slot->end_time,   // الحقل المخزن في الكنترولر الخاص بك
                ];
            }
        }
    }

    // تأكد من إرسال المتغيرين للملف
    return view('patient.dashboard', compact('doctors', 'availability'));

    }




    // حفظ موعد جديد
 public function store(Request $request)
{
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'day'       => 'nullable|integer|min:0|max:6',
        'time'      => 'required|string',
    ]);

    $date = null;
    if ($request->filled('day')) {
        $day = (int) $request->input('day');
        $today = \Carbon\Carbon::now();
        $candidate = $today->copy()->startOfDay();
        $daysToAdd = ($day - $candidate->dayOfWeek + 7) % 7;

        if ($daysToAdd === 0) {
            $chosenDateTime = $candidate->copy()->setTimeFromTimeString($request->input('time'));
            if ($chosenDateTime->lte(\Carbon\Carbon::now())) {
                $daysToAdd = 7;
            }
        }
        $date = $candidate->addDays($daysToAdd)->toDateString();
    } else {
        $date = $request->input('date');
    }

    // 1. منع تكرار نفس الموعد (طبيب + تاريخ + وقت) من قبل أي مريض
    $isBooked = Appointment::where('doctor_id', $request->input('doctor_id'))
        ->where('date', $date)
        ->where('time', $request->input('time'))
        ->where('status', '!=', 'cancelled')
        ->exists();

    if ($isBooked) {
        return redirect()->back()->withErrors(['error' => 'عذراً، هذا الوقت تم حجزه مسبقاً، يرجى اختيار وقت آخر.']);
    }

    // 2. منع المريض الحالي من حجز أكثر من موعد مع نفس الطبيب في نفس اليوم
    $hasAppointmentToday = Auth::user()->appointments()
        ->where('doctor_id', $request->input('doctor_id'))
        ->where('date', $date)
        ->where('status', '!=', 'cancelled')
        ->exists();

    if ($hasAppointmentToday) {
        return redirect()->back()->withErrors(['error' => 'لديك حجز مسبق مع هذا الطبيب في نفس اليوم المختار. لا يمكنك حجز أكثر من موعد يومياً.']);
    }

    // إنشاء الموعد
    Auth::user()->appointments()->create([
        'doctor_id' => $request->input('doctor_id'),
        'date'      => $date,
        'time'      => $request->input('time'),
        'notes'     => $request->input('notes'),
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
    // حذف الموعد مباشرة
    $appointment->delete();

    return redirect()->back()->with('success', 'تم حذف الموعد نهائياً بنجاح.');
}

    public function edit(Appointment $appointment)
{
    // 1. التحقق من أن الموعد يخص المريض الحالي
    if ($appointment->user_id !== auth()->id()) {
        abort(403);
    }

    // 2. التحقق من أن الموعد ليس قديماً أو منتهياً
    $appointmentDateTime = \Carbon\Carbon::parse($appointment->date . ' ' . $appointment->time);
    if ($appointmentDateTime->isPast()) {
        return redirect()->route('patient.appointments')
                         ->with('error', 'عذراً، لا يمكن تعديل موعد قديم أو منتهي.');
    }

    // 3. بناء مصفوفة الإتاحة (Availability) باستخدام نفس مسميات دالة index
    $availability = [];

    // جلب الدكتور مع علاقة doctorSlots (نفس المستخدمة في الحجز)
    $doctor = Doctor::with('doctorSlots')->find($appointment->doctor_id);

    if ($doctor && $doctor->doctorSlots) {
        foreach ($doctor->doctorSlots as $slot) {
            // نستخدم day_of_week بدلاً من day كما في دالة index
            if ($slot->day_of_week !== null) {
                $availability[$doctor->id][$slot->day_of_week][] = [
                    'start' => $slot->start_time,
                    'end'   => $slot->end_time,
                ];
            }
        }
    }

    return view('patient.edit', compact('appointment', 'availability'));
}


////////////////////////
    public function update(Request $request, Appointment $appointment)
{
    // 1. التأكد من أن الموعد يخص المريض المسجل حالياً
    if ($appointment->user_id !== auth()->id()) {
        abort(403);
    }

    // 2. التحقق من البيانات المرسلة (نتوقع 'day' بدلاً من 'date')
    $request->validate([
        'day'   => 'required|integer|min:0|max:6',
        'time'  => 'required',
        'notes' => 'nullable|string',
    ]);

    // 3. منطق تحويل "اليوم" (0-6) إلى "تاريخ حقيقي" (Date)
    $day = (int) $request->input('day');
    $today = \Carbon\Carbon::now();
    $candidate = $today->copy()->startOfDay();

    // حساب الفرق بين اليوم الحالي واليوم المختار
    $daysToAdd = ($day - $candidate->dayOfWeek + 7) % 7;

    // إذا كان اليوم المختار هو "اليوم" ولكن الوقت المختار قد مضى، نجعله للأسبوع القادم
    if ($daysToAdd === 0) {
        $chosenDateTime = $candidate->copy()->setTimeFromTimeString($request->input('time'));
        if ($chosenDateTime->lte(\Carbon\Carbon::now())) {
            $daysToAdd = 7;
        }
    }

    $newDate = $candidate->addDays($daysToAdd)->toDateString();

    // 4. تحديث البيانات في قاعدة البيانات
    $appointment->update([
        'date'  => $newDate,
        'time'  => $request->input('time'),
        'notes' => $request->input('notes'),
    ]);

    // 5. التوجيه لصفحة المواعيد مع رسالة نجاح
    // ملاحظة: استخدمت route('patient.appointments') لأنها هي المستخدمة في زر الرجوع عندك
    return redirect()->route('patient.appointments')->with('success', 'تم تحديث الموعد بنجاح إلى تاريخ ' . $newDate);
}

    // Patient accepts a rescheduled appointment proposed by admin
    public function acceptReschedule(Appointment $appointment)
    {
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        // mark as confirmed
        $appointment->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'تم قبول الموعد المعدل.');
    }





}
