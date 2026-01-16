<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use App\Models\Appointment;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم المريض مع بيانات الأطباء والمواعيد المتاحة
     */
    public function index()
    {



        $user = Auth::user();

        // 1. جلب المواعيد الخاصة بالمستخدم مع ترتيبها من الأحدث (أو حسب رغبتك)
        $appointments = $user->appointments()
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // 2. جلب جميع الأطباء مع علاقة الـ Slots (مواعيد العمل)
        // استخدمنا with('doctorSlots') لتقليل استهلاك قاعدة البيانات (Eager Loading)
        $doctors = Doctor::with('doctorSlots')->get();

        // 3. بناء مصفوفة التوافر (Availability)
        $availability = [];
        foreach ($doctors as $doc) {
            // نضمن أن المفتاح الخاص بالطبيب موجود حتى لو لم يكن لديه مواعيد
            $availability[$doc->id] = [];

            foreach ($doc->doctorSlots as $slot) {
                // تنظيم المواعيد حسب يوم الأسبوع (مثل: Monday, Tuesday...)
                $availability[$doc->id][$slot->day_of_week][] = [
                    'start' => $slot->start_time,
                    'end'   => $slot->end_time,
                ];
            }
        }

        // 4. تمرير البيانات للـ View (تأكد أن المسار patient.dashboard مطابق لملفاتك)
        return view('patient.dashboard', compact('user', 'appointments', 'doctors', 'availability'));
    }
}
