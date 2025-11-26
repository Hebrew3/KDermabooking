<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentLateReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The appointment instance.
     */
    public Appointment $appointment;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->load(['service', 'staff', 'client']);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Reminder: Your Appointment May Be Cancelled - K-Derma')
            ->view('emails.appointment-late-reminder');
    }
}

