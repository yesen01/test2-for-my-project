<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // أو الموديل الذي تستخدمه للموظفين
use Illuminate\Support\Facades\Hash;

class AdminReceptionistController extends Controller
{
    // عرض صفحة التعديل
    public function editPassword($id)
    {
        $receptionist = User::findOrFail($id);
        return view('admin.receptionists.edit_password', compact('receptionist'));
    }

    // تنفيذ عملية التحديث
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed', // 'confirmed' تتطلب حقل password_confirmation في النموذج
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'تم تحديث كلمة المرور بنجاح للموظف: ' . $user->name);
    }
}
