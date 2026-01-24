<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\AdminReceptionistController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminDoctorSlotController;
use App\Http\Controllers\Admin\AdminPatientController;
use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Receptionists\ReceptionistsAppointmentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // ضروري للتعامل مع جدول التوكنات





/*
|--------------------------------------------------------------------------
| الصفحة الرئيسية
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| نظام استعادة كلمة المرور (Forgot Password)
|--------------------------------------------------------------------------
*/
// 1. عرض صفحة طلب الرابط
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// 2. معالجة إرسال الإيميل (تم دمج المنطق هنا ليعمل مباشرة مع Mailtrap)
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['success' => 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني. تفقد Mailtrap.'])
        : back()->withErrors(['email' => 'لم نتمكن من العثور على مستخدم بهذا البريد الإلكتروني.']);
})->name('password.email');




// 3. عرض صفحة تعيين كلمة المرور الجديدة (هذا المسار الذي كان ناقصاً)
Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');



// 4. معالجة تعيين كلمة المرور الجديدة
// 1. مسار عرض الصفحة (GET) - تم تعديله للتحقق من الصلاحية قبل فتح الصفحة
Route::get('/reset-password/{token}', function (Request $request, $token) {
    // التحقق هل يوجد طلب استعادة فعال لهذا الإيميل في قاعدة البيانات
    $tokenExists = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    // إذا لم يجد التوكن (يعني تم استخدامه وحذفه سابقاً)
    if (!$tokenExists || !Hash::check($token, $tokenExists->token)) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'هذا الرابط منتهي الصلاحية أو تم استخدامه مسبقاً، يرجى طلب رابط جديد.']);
    }

    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');


// 2. مسار تحديث كلمة المرور (POST) - كما هو مع التأكيد على الحذف
Route::post('/reset-password', function (Request $request) {
    // التحقق من صحة المدخلات
    $request->validate([
        'token' => 'required',
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    // التحقق من وجود التوكن ومطابقته
    $tokenData = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
        return redirect()->route('password.request')
            ->withErrors(['email' => 'عذراً، هذا الرابط لم يعد صالحاً.']);
    }

    // تحديث كلمة مرور المستخدم
    $user = User::where('email', $request->email)->first();

    if ($user) {
        $user->password = Hash::make($request->password);
        $user->save();

        // حذف التوكن فوراً لضمان عدم تكرار الاستخدام
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'تم تحديث كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.');
    }

    return back()->withErrors(['email' => 'حدث خطأ غير متوقع، حاول مرة أخرى.']);
})->name('password.update');



/*
|--------------------------------------------------------------------------
| المصادقة (Authentication)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthManager::class, 'login'])->name('login');
Route::post('/login', [AuthManager::class, 'loginPost'])->name('login.Post');

Route::get('/Registration', [AuthManager::class, 'Registration'])->name('Registration');
Route::post('/Registration', [AuthManager::class, 'RegistrationPost'])->name('Registration.Post');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| لوحة تحكم الأدمن (Admin Dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.') // هذا السطر يضيف "admin." تلقائياً قبل كل اسم مسار بالداخل
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // صفحة عرض الأطباء
        Route::get('/doctors', function () {
            $doctors = \App\Models\Doctor::all();
            return view('admin.doctors.index', compact('doctors'));
        })->name('doctors.index'); // الاسم الكامل سيصبح admin.doctors.index

        // عمليات الأطباء والمواعيد
        Route::post('/doctors', [AdminDoctorController::class, 'store'])->name('doctors.store');
        Route::delete('/doctors/{doctor}', [AdminDoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::get('/doctors/{doctor}/slots', [AdminDoctorSlotController::class, 'index'])->name('doctors.slots.index');
        Route::post('/doctors/{doctor}/slots', [AdminDoctorSlotController::class, 'store'])->name('doctors.slots.store');
        Route::delete('/slots/{slot}', [AdminDoctorSlotController::class, 'destroy'])->name('doctors.slots.destroy');


        // صفحة الجدول الزمني للأدمن
        Route::get('/schedule', [AdminScheduleController::class, 'index'])->name('schedule.index');

        // مسارات إدارة المرضى للأدمن
        Route::get('/patients', [AdminPatientController::class, 'index'])->name('patients.index');
        Route::delete('/patients/{patient}', [AdminPatientController::class, 'destroy'])->name('patients.destroy');

        // مسارات إدارة موظفي الاستقبال للأدمن
        Route::get('/receptionists', [AdminDashboardController::class, 'receptionistsIndex'])->name('receptionists.index');
        Route::post('/receptionists/add', [AdminDashboardController::class, 'addReceptionist'])->name('receptionists.add');
        Route::delete('/receptionists/{user}/delete', [AdminDashboardController::class, 'deleteReceptionist'])->name('receptionists.delete');


        // مسارات تعديل كلمة مرور موظف الاستقبال للأدمن
        Route::get('/receptionists/{id}/password', [AdminReceptionistController::class, 'editPassword'])->name('receptionist.editPassword');
        Route::put('/receptionists/{id}/password', [AdminReceptionistController::class, 'updatePassword'])->name('receptionist.updatePassword');


        // مسارات إدارة المواعيد للأدمن
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}/edit', [AdminAppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('appointments.update');
        Route::post('/appointments/{appointment}/approve', [AdminAppointmentController::class, 'approve'])->name('appointments.approve');
        Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

        Route::post('/appointments/{appointment}/manual-remind', [AdminAppointmentController::class, 'manualRemind'])->name('appointments.manual_remind');



    });

/*
|--------------------------------------------------------------------------
| لوحة تحكم موظف الاستقبال (Receptionist)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('reception')
    ->name('reception.')
    ->group(function () {

        // لوحة التحكم (Dashboard)
        Route::get('/dashboard', [AdminDashboardController::class, 'receptionDashboard'])->name('dashboard');

        // الأطباء (Doctors)
        Route::get('/doctors', function () {
            $doctors = \App\Models\Doctor::all();
            return view('reception.doctors.index', compact('doctors'));
        })->name('doctors.index');

        Route::post('/doctors', [AdminDoctorController::class, 'store'])->name('doctors.store');
        Route::delete('/doctors/{doctor}', [AdminDoctorController::class, 'destroy'])->name('doctors.destroy');
        // مسارات فترات عمل الأطباء لموظف الاستقبال
        Route::get('/doctors/{doctor}/slots', [AdminDoctorSlotController::class, 'index'])->name('doctors.slots.index');
        Route::post('/doctors/{doctor}/slots', [AdminDoctorSlotController::class, 'store'])->name('doctors.slots.store');
        Route::delete('/slots/{slot}', [AdminDoctorSlotController::class, 'destroy'])->name('doctors.slots.destroy');

        // المرضى (هنا حل المشكلة الحالية)
        Route::get('/patients', [AdminPatientController::class, 'index'])->name('patients.index');
        Route::delete('/patients/{patient}', [AdminPatientController::class, 'destroy'])->name('patients.destroy');

        // المواعيد (Appointments)
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}/edit', [AdminAppointmentController::class, 'edit'])->name('appointments.edit');
        Route::put('/appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('appointments.update');
        Route::post('/appointments/{appointment}/approve', [AdminAppointmentController::class, 'approve'])->name('appointments.approve');
        Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

        Route::get('/schedule', [AdminScheduleController::class, 'index'])->name('schedule.index');



        // التذكير اليدوي
        Route::post('/appointments/{appointment}/manual-remind', [AdminAppointmentController::class, 'manualRemind'])
    ->name('appointments.manualRemind');

    });

/*
|--------------------------------------------------------------------------
| لوحة تحكم المريض (Patient Dashboard)
|--------------------------------------------------------------------------
*/
// مسارات لوحة تحكم المريض
Route::middleware('auth')->group(function () {

    // داشبورد المريض (حجز)
    Route::get('/patient/dashboard', [PatientAppointmentController::class, 'index'])
        ->name('patient.dashboard');

    // حفظ موعد
    Route::post('/patient/dashboard', [PatientAppointmentController::class, 'store'])
        ->name('patient.dashboard.store');

    // صفحة مواعيدي
    Route::get('/patient/appointments', [PatientAppointmentController::class, 'myAppointments'])
        ->name('patient.appointments');

    // إلغاء موعد
    Route::delete('/patient/appointments/{appointment}', [PatientAppointmentController::class, 'destroy'])
        ->name('patient.appointments.destroy');

        // صفحة تعديل (نستعملها لاحقاً)
    Route::get('/patient/appointments/{appointment}/edit', [PatientAppointmentController::class, 'edit'])
        ->name('patient.appointments.edit');

        Route::put('/patient/appointments/{appointment}', [PatientAppointmentController::class, 'update'])
    ->name('patient.appointments.update');

        // patient accepts an admin reschedule
        Route::post('/patient/appointments/{appointment}/accept', [PatientAppointmentController::class, 'acceptReschedule'])
            ->name('patient.appointments.accept');
});


/*
|--------------------------------------------------------------------------
| مسارات عامة للطبيب
|--------------------------------------------------------------------------
*/
Route::get('/doctors', [DoctorController::class, 'index'])->name('patient.doctors.index');
Route::get('/doctor-times/{doctor}', [DoctorController::class, 'times']);
