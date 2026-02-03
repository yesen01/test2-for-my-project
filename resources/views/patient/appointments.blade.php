<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مواعيدي - مركز كيان</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg: #f3f4f6;
            --card: #ffffff;
            --muted: #64748b;
            --accent: #0f766e;
            --accent-hover: #0d635d;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--bg);
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* ===== الشريط العلوي ===== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 25px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-name {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.2rem;
            margin: 0;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* ===== نظام الإشعارات (الجرس) ===== */
        .notif-wrapper {
            position: relative;
            cursor: pointer;
            color: var(--accent);
            font-size: 1.3rem;
        }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .dropdown-notif {
            display: none;
            position: absolute;
            left: 0;
            top: 40px;
            background: white;
            width: 280px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 2000;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .notif-wrapper.open .dropdown-notif { display: block; animation: fadeIn 0.3s ease; }

        .notif-header {
            padding: 12px;
            font-weight: bold;
            background: #f8fafc;
            border-bottom: 1px solid #eee;
            color: var(--accent);
            font-size: 0.9rem;
        }

        .notif-item {
            padding: 12px;
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.8rem;
            line-height: 1.5;
            color: #334155;
            transition: 0.2s;
            text-decoration: none;
            text-align: right;
        }

        .notif-item:hover { background: #f0fdfa; }

        .notif-empty {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            font-size: 0.85rem;
        }

        /* ===== القوائم والأزرار ===== */
        .controls-appt, .controls-fixed { position: relative; }

        .menu-btn {
            background: #f8fafc;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .menu-btn:hover { background: #e2e8f0; }

        .dropdown-appt, .dropdown-fixed {
            display: none;
            position: absolute;
            left: 0;
            top: 40px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            min-width: 190px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 1100;
        }

        .dropdown-appt a, .dropdown-appt button, .dropdown-fixed a, .dropdown-fixed button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 12px 15px;
            background: none;
            border: none;
            text-align: right;
            cursor: pointer;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .controls-appt.open .dropdown-appt,
        .controls-fixed.open .dropdown-fixed { display: block; animation: fadeIn 0.2s ease; }

        /* ===== الحاوية والبطاقات ===== */
        .container { max-width: 800px; margin: 30px auto; padding: 0 15px; }
        .card { background: var(--card); padding: 20px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
        .appt { border: 1px solid #eef2f6; padding: 16px; border-radius: 12px; margin-bottom: 15px; position: relative; background: #fff; }
        .meta { display: flex; flex-wrap: wrap; gap: 15px; align-items: center; margin-bottom: 8px; }
        .time { color: var(--accent); font-weight: 700; background: rgba(15, 118, 110, 0.08); padding: 4px 10px; border-radius: 8px; }

        .status-badge { font-size: 0.75rem; padding: 4px 12px; border-radius: 20px; font-weight: 600; }
        .status-rescheduled { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        .status-reminded { background: #e0f2fe; color: #0369a1; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

    <header class="top-bar">
        <h2 class="brand-name">مركز كيان لطب وجراحة الاسنان</h2>

        <div class="top-actions">
            <div class="notif-wrapper" id="notifWrapper">
                <i class="fa-solid fa-bell"></i>

                @php
                    // جمع المواعيد التي تحتاج تنبيه (تعديل أو تذكير يدوي)
                    $notifs = $appointments->whereIn('status', ['rescheduled', 'reminded']);
                    $count = $notifs->count();
                @endphp

                @if($count > 0)
                    <span class="notif-badge">{{ $count }}</span>
                @endif

                <div class="dropdown-notif" id="notifDropdown">
                    <div class="notif-header">التنبيهات</div>
                    <div class="notif-body">
                        @forelse($notifs as $notif)
                <form action="{{ route('patient.appointments.accept', $notif->id) }}" method="POST" id="accept-notif-{{ $notif->id }}">
                    @csrf
                    <a href="javascript:void(0)" onclick="document.getElementById('accept-notif-{{ $notif->id }}').submit();" class="notif-item">
                        @if($notif->status === 'reminded')
                            <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent)"></i>
                            <div>
                                <strong>تذكير بالموعد</strong>
                                <p>نذكرك بموعدك القادم في {{ $notif->date }} الساعة {{ $notif->time }}</p>
                            </div>
                        @else
                            <i class="fa-solid fa-calendar-day" style="color: var(--warning)"></i>
                            <div>
                                <strong>تعديل موعد</strong>
                                <p>تم اقتراح موعد جديد: {{ $notif->date }} الساعة {{ $notif->time }}</p>
                            </div>
                        @endif
                    </a>
                </form>
                @empty
                <div class="notif-empty">لا توجد تنبيهات جديدة</div>
                @endforelse
                    </div>
                </div>
            </div>

            <div class="controls-fixed" id="controlsFixed">
                <button class="menu-btn" id="menuButton">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="dropdown-fixed">
                    <a href="{{ route('patient.dashboard') }}"><i class="fa-solid fa-plus"></i> حجز موعد جديد</a>
                    <hr style="margin: 5px 0; border: 0; border-top: 1px solid #eee;">

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" style="color: var(--danger); font-weight: bold;">
                           <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </header>


    <div class="container">
        <h1>📅 مواعيدي</h1>
        <div class="card">
            @if($appointments->isEmpty())
                <div style="text-align: center; padding: 40px 0;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                    <p class="muted">لا توجد مواعيد مسجلة حالياً</p>
                    <a href="{{ route('patient.dashboard') }}" style="color: var(--accent); text-decoration: none; font-weight: bold;">اضغط هنا لحجز موعدك</a>
                </div>
            @else
                @foreach($appointments as $appointment)
    @php
        // دمج التاريخ والوقت في كائن واحد للمقارنة
        $appointmentDateTime = \Carbon\Carbon::parse($appointment->date . ' ' . $appointment->time);//إنشاء كائن تاريخ ووقت من تاريخ ووقت الموعد//
        $isPast = $appointmentDateTime->isPast();//تحقق مما إذا كان الموعد قد مضى//
    @endphp

    <div class="appt" style="{{ $isPast ? 'opacity: 0.7; background: #f9fafb;' : '' }}">

        {{-- إخفاء زر القائمة الثلاثية إذا كان الموعد قد مضى --}}
        @if(!$isPast)
        <div style="position: absolute; left: 16px; top: 16px;">
            <div class="controls-appt">
                <button class="menu-btn appt-menu-trigger">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>

                <div class="dropdown-appt">
                    <a href="{{ route('patient.appointments.edit', $appointment) }}">
                        <i class="fa-solid fa-pen-to-square"></i> تعديل الموعد
                    </a>
                    <form action="{{ route('patient.appointments.destroy', $appointment) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الموعد نهائياً؟');">
                @csrf
                @method('DELETE')
                <button type="submit" style="color: var(--danger); width: 100%; border-top: 1px solid #f1f5f9;">
                    <i class="fa-solid fa-trash-can"></i> حذف الموعد
                </button>
            </form>
                </div>
            </div>
        </div>
        @endif

        <div class="meta">
            <div class="date"><i class="fa-regular fa-calendar-check" style="color: var(--accent)"></i> {{ $appointment->date }}</div>
            <div class="time"><i class="fa-regular fa-clock"></i> {{ $appointment->time }}</div>

            {{-- تعديل عرض الحالة بناءً على الوقت --}}
            @if($isPast)
                <span class="status-badge" style="background: #e2e8f0; color: #475569;">
                    <i class="fa-solid fa-check-double"></i> موعد منتهي
                </span>
            @elseif($appointment->status === 'rescheduled')
                <span class="status-badge status-rescheduled">موعد مقترح (بإنتظار قبولك)</span>
            @elseif($appointment->status === 'reminded')
                <span class="status-badge status-reminded">تذكير من الاستقبال</span>
            @elseif($appointment->status === 'confirmed')
                <span class="status-badge status-confirmed">مؤكد</span>
            @else
                <span class="status-badge" style="background: #f1f5f9;">قيد الانتظار</span>
            @endif
        </div>
    </div>
@endforeach
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('click', function (e) {
            const menuButton = document.getElementById('menuButton');
            const controlsFixed = document.getElementById('controlsFixed');
            const notifWrapper = document.getElementById('notifWrapper');

            // 1. فتح/إغلاق الإشعارات (الجرس)
            if (notifWrapper && notifWrapper.contains(e.target)) {
                e.stopPropagation();
                notifWrapper.classList.toggle('open');
                controlsFixed.classList.remove('open');
                document.querySelectorAll('.controls-appt').forEach(el => el.classList.remove('open'));
                return;
            }

            // 2. فتح/إغلاق القائمة العلوية
            if (menuButton && menuButton.contains(e.target)) {
                e.stopPropagation();
                controlsFixed.classList.toggle('open');
                notifWrapper.classList.remove('open');
                document.querySelectorAll('.controls-appt').forEach(el => el.classList.remove('open'));
                return;
            }

            // 3. فتح/إغلاق خيارات الموعد
            const apptTrigger = e.target.closest('.appt-menu-trigger');
            if (apptTrigger) {
                e.stopPropagation();
                const parent = apptTrigger.closest('.controls-appt');
                document.querySelectorAll('.controls-appt, .controls-fixed, .notif-wrapper').forEach(el => {
                    if (el !== parent) el.classList.remove('open');
                });
                parent.classList.toggle('open');
                return;
            }

            // إغلاق كل شيء عند الضغط في أي مكان آخر
            if(controlsFixed) controlsFixed.classList.remove('open');
            if(notifWrapper) notifWrapper.classList.remove('open');
            document.querySelectorAll('.controls-appt').forEach(el => el.classList.remove('open'));
        });
    </script>
</body>
</html>
