<?php

namespace App\Http\Controllers\Receptionists;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ReceptionistsPatientController extends Controller
{
    // List patients (paginated)
    public function index(Request $request)
    {
        // eager-load the patient's appointments and their doctors (so we can show all doctors)
        $patients = User::where('role', 'patient')
            ->with('appointments.doctor')
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
