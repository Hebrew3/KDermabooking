<?php

namespace App\Console\Commands;

use App\Mail\AppointmentLateReminderMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendLateAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-late-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to clients who are 15 minutes late for their appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find appointments that are:
        // 1. Pending status only
        // 2. Scheduled for today
        // 3. 15 minutes past their scheduled time
        // 4. Late reminder not yet sent
        $lateAppointments = Appointment::where('status', 'pending')
            ->whereDate('appointment_date', $now->toDateString())
            ->whereNotNull('appointment_time')
            ->whereNotNull('client_id')
            ->get()
            ->filter(function ($appointment) use ($now) {
                // Calculate appointment datetime
                $appointmentDateTime = $this->getAppointmentDateTime($appointment);
                if (!$appointmentDateTime) {
                    return false;
                }

                // Check if appointment is 15 minutes late or more
                // diffInMinutes returns negative if appointment is in the past
                $minutesLate = $now->diffInMinutes($appointmentDateTime, false);
                
                // Appointment is late if it's 15 minutes or more past (with 1 minute tolerance to avoid duplicates)
                // We check between 14-16 minutes to catch it when it's exactly 15 minutes late
                if ($minutesLate <= -14 && $minutesLate >= -16) {
                    // Check if late reminder has already been sent
                    $reminderSent = $appointment->reminder_sent ?? [];
                    if (!isset($reminderSent['late_reminder_sent_at'])) {
                        return true;
                    }
                }
                
                return false;
            });

        if ($lateAppointments->isEmpty()) {
            $this->info('No late appointments found.');
            return 0;
        }

        $this->info("Found {$lateAppointments->count()} late appointment(s). Sending reminders...");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($lateAppointments as $appointment) {
            $client = $appointment->client;
            
            if (!$client || !$client->email) {
                $this->warn("Skipping appointment {$appointment->appointment_number}: No client email found.");
                $failedCount++;
                continue;
            }

            try {
                Mail::to($client->email)->send(new AppointmentLateReminderMail($appointment));
                
                // Mark late reminder as sent
                $reminderSent = $appointment->reminder_sent ?? [];
                $reminderSent['late_reminder_sent_at'] = $now->toDateTimeString();
                $appointment->update(['reminder_sent' => $reminderSent]);
                
                $this->info("✓ Sent late reminder to {$client->email} for appointment {$appointment->appointment_number}");
                $sentCount++;
                
                Log::info("Late appointment reminder sent", [
                    'appointment_id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'client_email' => $client->email,
                ]);
            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder for appointment {$appointment->appointment_number}: " . $e->getMessage());
                $failedCount++;
                
                Log::error("Failed to send late appointment reminder", [
                    'appointment_id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'client_email' => $client->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Completed: {$sentCount} sent, {$failedCount} failed.");
        return 0;
    }

    /**
     * Get the appointment datetime as Carbon instance.
     */
    private function getAppointmentDateTime(Appointment $appointment): ?Carbon
    {
        try {
            $dateValue = $appointment->getAttribute('appointment_date');
            if (!$dateValue) {
                return null;
            }

            $appointmentDate = $dateValue instanceof Carbon ? $dateValue : Carbon::parse($dateValue);
            
            if (empty($appointment->appointment_time)) {
                return null;
            }

            // Try different time formats
            $timeFormats = ['H:i:s', 'H:i', 'h:i A', 'h:i:s A'];
            $time = null;
            
            foreach ($timeFormats as $format) {
                try {
                    $time = Carbon::createFromFormat($format, $appointment->appointment_time);
                    break;
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($time) {
                return $appointmentDate->setTime($time->hour, $time->minute, $time->second ?? 0);
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Error parsing appointment datetime", [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

