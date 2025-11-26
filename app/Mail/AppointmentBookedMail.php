<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentBookedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The booked appointment instance.
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
            ->subject('Your K-Derma Appointment Booking')
            ->view('emails.appointment-booked');
    }
}
