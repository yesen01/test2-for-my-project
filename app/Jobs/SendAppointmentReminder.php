<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Appointment $appointment;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->appointment->user;
            if (!$user) {
                return;
            }

            // 1) in-app (database) + email notification via Notification
            try {
                $user->notify(new AppointmentReminderNotification($this->appointment));
            } catch (\Exception $e) {
                // fallback to mail if notifications fail
                if ($user->email) {
                    Mail::to($user->email)->send(new \App\Mail\AppointmentReminder($this->appointment));
                }
            }

            // 2) SMS (via Twilio) if configured and user has phone
            if (!empty($user->phone) && env('SMS_ENABLED') === 'true' && env('SMS_PROVIDER') === 'twilio') {
                $this->sendSmsViaTwilio($user->phone, $this->buildSmsMessage());
            }
        }

        protected function buildSmsMessage(): string
        {
            $doc = optional($this->appointment->doctor)->name;
            $date = Carbon::parse($this->appointment->date)->format('Y-m-d');
            $time = Carbon::parse($this->appointment->time)->format('H:i');
            return "تذكير: لديك موعد مع {$doc} بتاريخ {$date} الساعة {$time} - مركز كيان";
        }

        protected function sendSmsViaTwilio(string $to, string $message): void
        {
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_FROM');

            if (!$sid || !$token || !$from) {
                return;
            }

            $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

            $data = http_build_query([
                'From' => $from,
                'To' => $to,
                'Body' => $message,
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $result = curl_exec($ch);
            curl_close($ch);
        }
    }
}
