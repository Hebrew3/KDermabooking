<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 24px;">
        <tr>
            <td>
                <h1 style="font-size: 20px; margin-bottom: 8px; color: #111827;">Hi {{ $appointment->client->first_name ?? 'Client' }},</h1>
                <p style="font-size: 14px; color: #4b5563; margin-bottom: 16px;">
                    The status of your appointment <strong>{{ $appointment->appointment_number }}</strong> has changed.
                </p>

                <p style="font-size: 14px; color: #4b5563; margin-bottom: 8px;">
                    <strong>Previous status:</strong> {{ ucfirst(str_replace('_', ' ', $oldStatus)) }}<br>
                    <strong>New status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                </p>

                @php($newStatus = $appointment->status)
                <p style="font-size: 13px; color: #6b7280; margin-bottom: 16px;">
                    @switch($newStatus)
                        @case('pending')
                            We have received your booking and it is waiting for confirmation.
                            @break
                        @case('confirmed')
                            Your appointment is confirmed. Please arrive a few minutes early on the scheduled date and time.
                            <br><br>
                            <strong>Important Reminder:</strong> Please note that we can only wait for you for up to <strong>15 minutes</strong> after your scheduled appointment time. If you arrive later than 15 minutes, your appointment may be cancelled or rescheduled. We appreciate your understanding and punctuality.
                            @break
                        @case('in_progress')
                            Your appointment is currently in progress at the clinic.
                            @break
                        @case('completed')
                            Your appointment has been completed. Thank you for visiting K-Derma.
                            @break
                        @case('cancelled')
                            Your appointment has been cancelled. If this was unexpected, please contact the clinic or book a new appointment.
                            @break
                        @case('no_show')
                            You were marked as a no-show for this appointment. Please contact the clinic if you need assistance scheduling again.
                            @break
                        @default
                            The appointment status has been updated in our system.
                    @endswitch
                </p>

                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #111827; margin-bottom: 16px;">
                    <tr>
                        <td style="padding: 4px 0; width: 40%; color: #6b7280;">Service:</td>
                        <td style="padding: 4px 0;"><strong>{{ $appointment->service->name }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #6b7280;">Date &amp; Time:</td>
                        <td style="padding: 4px 0;">{{ $appointment->formatted_date_time }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #6b7280;">Staff:</td>
                        <td style="padding: 4px 0;">{{ $appointment->staff ? $appointment->staff->name : 'To be assigned' }}</td>
                    </tr>
                </table>

                <p style="font-size: 13px; color: #6b7280; margin-top: 24px;">
                    You can view full details or reschedule/cancel (if allowed) from your K-Derma client portal.
                </p>

                <p style="font-size: 13px; color: #9ca3af; margin-top: 24px;">
                    This email was sent automatically. Please do not reply directly to this message.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
