<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        $channels = ['mail', 'database'];
        // if Vonage/nexmo is configured, try to add it
        if (env('SMS_ENABLED') === 'true' && env('SMS_PROVIDER') === 'twilio') {
            // we still send SMS from the job; keep channels as mail+database
        }
        return $channels;
    }

    public function toMail($notifiable)
    {
        $doc = optional($this->appointment->doctor)->name;
        $date = \Carbon\Carbon::parse($this->appointment->date)->isoFormat('D MMMM YYYY');
        $time = \Carbon\Carbon::parse($this->appointment->time)->format('H:i');

        return (new MailMessage)
                    ->subject("تذكير بموعدك مع {$doc}")
                    ->line("لديك موعد مع {$doc} بتاريخ {$date} الساعة {$time}.")
                    ->line('الرجاء الحضور قبل الموعد بخمس دقائق.')
                    ->line('مركز كيان');
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'doctor' => optional($this->appointment->doctor)->name,
            'date' => $this->appointment->date,
            'time' => $this->appointment->time,
            'message' => 'تذكير بموعد قادم',
        ];
    }
}
