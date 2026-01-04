<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تذكير بالموعد</title>
</head>
<body>
    <p>مرحبًا {{ optional($appointment->user)->name }},</p>

    <p>هذا تذكير بموعدك القادم مع الدكتور {{ optional($appointment->doctor)->name }}.</p>

    <ul>
        <li>التاريخ: {{ \Carbon\Carbon::parse($appointment->date)->isoFormat('D MMMM YYYY') }}</li>
        <li>الوقت: {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</li>
    </ul>

    <p>الرجاء الحضور قبل الموعد بخمس دقائق. إذا أردت تغيير الموعد، يمكنك تسجيل الدخول إلى حسابك أو التواصل مع المركز.</p>

    <p>مع تحياتي،<br>مركز كيان</p>
</body>
</html>
