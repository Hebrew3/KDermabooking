<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Appointment Booking</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 24px;">
        <tr>
            <td>
                <h1 style="font-size: 20px; margin-bottom: 8px; color: #111827;">Hi {{ $appointment->client->first_name ?? 'Client' }},</h1>
                <p style="font-size: 14px; color: #4b5563; margin-bottom: 16px;">
                    Thank you for booking an appointment with <strong>K-Derma</strong>. Here are your booking details:
                </p>

                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #111827; margin-bottom: 16px;">
                    <tr>
                        <td style="padding: 4px 0; width: 40%; color: #6b7280;">Appointment Number:</td>
                        <td style="padding: 4px 0;"><strong>{{ $appointment->appointment_number }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #6b7280;">Service:</td>
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
                    <tr>
                        <td style="padding: 4px 0; color: #6b7280;">Total Amount:</td>
                        <td style="padding: 4px 0;">{{ $appointment->formatted_total }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #6b7280;">Status:</td>
                        <td style="padding: 4px 0;">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</td>
                    </tr>
                </table>

                @if($appointment->client_notes)
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px;">
                    <strong>Your Notes:</strong><br>
                    {{ $appointment->client_notes }}
                </p>
                @endif

                <p style="font-size: 13px; color: #6b7280; margin-top: 24px;">
                    If you need to reschedule or cancel this appointment, please log in to your K-Derma client portal.
                </p>

                <p style="font-size: 13px; color: #9ca3af; margin-top: 24px;">
                    This email was sent automatically. Please do not reply directly to this message.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
