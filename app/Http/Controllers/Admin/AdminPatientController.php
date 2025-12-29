<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminPatientController extends Controller
{
    // List patients (paginated)
    public function index(Request $request)
    {
        // eager-load the patient's latest appointment and its doctor (if any)
        $patients = User::where('role', 'patient')
            ->with('latestAppointment.doctor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.patients.index', compact('patients'));
    }

    // Delete a patient
    public function destroy(User $patient)
    {
        if ($patient->role !== 'patient') {
            return redirect()->back()->with('error', 'لا يمكن حذف هذا المستخدم');
        }

        $patient->delete();

        return redirect()->route('admin.patients.index')->with('success', 'تم حذف المريض بنجاح');
    }
}
