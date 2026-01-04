<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function build()
    {
        $subject = 'تذكير بموعدك لدى ' . optional($this->appointment->doctor)->name;
        return $this->subject($subject)
                    ->view('emails.appointment_reminder')
                    ->with(['appointment' => $this->appointment]);
    }
}
