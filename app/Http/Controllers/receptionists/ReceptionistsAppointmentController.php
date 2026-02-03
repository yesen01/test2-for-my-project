<?php

namespace App\Http\Controllers\Receptionists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\User;
use Carbon\Carbon;

class ReceptionistsAppointmentController extends Controller
{

public function __construct()
    {
        // هذا السطر يخبر لارافل: "اسمح فقط للأدمن وموظف الاستقبال"
        $this->middleware('auth');
        // إذا كنت تستخدم نظام الأدوار (Spatie أو نظام مخصص):
        // $this->middleware('role:admin,receptionist');
    }
    /**
     * عرض قائمة المواعيد والأطباء لموظف الاستقبال
     */
    public function index()
    {
        $appointments = Appointment::with(['user', 'doctor'])->orderBy('date', 'desc')->get();
        $doctors = Doctor::all();
        $patients = User::where('role', 'patient')->get();

        return view('receptionists.appointments.index', compact('appointments', 'doctors', 'patients'));
    }

    /**
     * جلب المواعيد المتاحة للطبيب (JSON)
     */
    public function available(Doctor $doctor)
    {
        $today = Carbon::today();
        $days = [];

        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            $weekday = $date->dayOfWeek;

            foreach ($doctor->doctorSlots as $slot) {
                if (!is_null($slot->day_of_week) && (int)$slot->day_of_week === $weekday) {
                    $booked = $slot->isBookedOnDate($date);
                    $days[] = [
                        'date' => $date->toDateString(),
                        'time' => $slot->start_time ?? ($slot->start_at ? $slot->start_at->format('H:i') : null),
                        'slot_id' => $slot->id,
                        'booked' => $booked,
                    ];
                }

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

    /**
     * حجز موعد جديد نيابة عن مريض
     */
    public function store(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:users,id',
        'doctor_slot_id' => 'required|exists:doctor_slots,id',
        'date' => 'required|date',
    ]);

    $slot = DoctorSlot::findOrFail($request->input('doctor_slot_id'));
    $dateFormatted = \Carbon\Carbon::parse($request->input('date'))->toDateString();

    // 1. التحقق من أن هذا "الslot" المحدد لم يحجزه مريض آخر في هذا التاريخ
    $slotExists = Appointment::where('doctor_slot_id', $slot->id)
        ->where('date', $dateFormatted)
        ->where('status', '!=', 'cancelled')
        ->exists();

    if ($slotExists) {
        return redirect()->back()->withErrors(['slot' => 'عذراً، هذا الوقت محجوز مسبقاً لمريض آخر.']);
    }

    // 2. التحقق من أن المريض المختار ليس لديه حجز آخر مع نفس الطبيب في نفس اليوم
    $patientHasAppointmentToday = Appointment::where('user_id', $request->input('patient_id'))
        ->where('doctor_id', $slot->doctor_id)
        ->where('date', $dateFormatted)
        ->where('status', '!=', 'cancelled')
        ->exists();

    if ($patientHasAppointmentToday) {
        return redirect()->back()->withErrors(['patient_id' => 'هذا المريض لديه حجز بالفعل مع هذا الطبيب في نفس اليوم المحدد.']);
    }

    // إنشاء الموعد وتأكيده
    Appointment::create([
        'user_id' => $request->input('patient_id'),
        'doctor_id' => $slot->doctor_id,
        'doctor_slot_id' => $slot->id,
        'date' => $dateFormatted,
        'time' => $slot->start_time ?? ($slot->start_at ? $slot->start_at->format('H:i') : $request->input('time')),
        'status' => 'confirmed',
    ]);

    return redirect()->back()->with('success', 'تم إنشاء الموعد وتأكيده بنجاح.');
}
    /**
     * قبول/تأكيد الموعد
     */
    public function approve(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed']);
        return redirect()->back()->with('success', 'تم تأكيد الموعد بنجاح.');
    }

    /**
     * صفحة تعديل الموعد
     */
    public function edit(Appointment $appointment)
    {
        $appointment->load(['user', 'doctor']);
        return view('receptionists.appointments.edit', compact('appointment'));
    }

    /**
     * تحديث الموعد وتفعيل جرس الإشعارات عند المريض
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $appointment->update([
            'date' => Carbon::parse($request->input('date'))->toDateString(),
            'time' => $request->input('time'),
            'status' => 'rescheduled', // تظهر كنقطة حمراء في جرس المريض
        ]);

        return redirect()->route('receptionists.appointments.index')
            ->with('success', 'تم تعديل الموعد؛ سيظهر تنبيه للمريض في لوحة التحكم.');
    }

    /**
     * إلغاء الموعد
     */
    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'تم إلغاء الموعد.');
    }

    /**
     * حذف نهائي
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->back()->with('success', 'تم حذف الموعد من السجلات.');
    }

    /**
     * إرسال تنبيه يدوي يظهر في "الجرس" عند المريض
     */
    public function manualRemind(Appointment $appointment)
    {
        // تغيير الحالة لـ 'reminded' ليتم التقاطها بواسطة الكود الذي وضعناه في الـ Blade
        $appointment->update([
            'status' => 'reminded'
        ]);

        return redirect()->back()->with('success', 'تم إرسال التنبيه! سيظهر الآن في جرس الإشعارات لدى المريض.');
    }


}
