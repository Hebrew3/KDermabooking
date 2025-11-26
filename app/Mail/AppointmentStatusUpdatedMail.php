<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The appointment instance.
     */
    public Appointment $appointment;

    /**
     * The old status value.
     */
    public string $oldStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $oldStatus)
    {
        $this->appointment = $appointment->load(['service', 'staff', 'client']);
        $this->oldStatus = $oldStatus;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Your Appointment Status Has Been Updated')
            ->view('emails.appointment-status-updated');
    }
}
