<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Late Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 24px;">
        <tr>
            <td>
                <h1 style="font-size: 20px; margin-bottom: 8px; color: #111827;">Hi {{ $appointment->client->first_name ?? 'Client' }},</h1>
                <p style="font-size: 14px; color: #4b5563; margin-bottom: 16px;">
                    This is a reminder that your appointment <strong>{{ $appointment->appointment_number }}</strong> was scheduled for <strong>{{ $appointment->formatted_date_time }}</strong>.
                </p>

                <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 16px 0; border-radius: 4px;">
                    <p style="font-size: 14px; color: #92400e; margin: 0;">
                        <strong>⚠️ Important Notice:</strong> You are now 15 minutes late for your appointment. If you do not arrive soon, your appointment may be cancelled.
                    </p>
                </div>

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
                        <td style="padding: 4px 0; color: #6b7280;">Scheduled Date &amp; Time:</td>
                        <td style="padding: 4px 0;">{{ $appointment->formatted_date_time }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #6b7280;">Staff:</td>
                        <td style="padding: 4px 0;">{{ $appointment->staff ? $appointment->staff->name : 'To be assigned' }}</td>
                    </tr>
                </table>

                <p style="font-size: 14px; color: #4b5563; margin-top: 16px; margin-bottom: 8px;">
                    <strong>What you need to know:</strong>
                </p>
                <ul style="font-size: 13px; color: #6b7280; margin: 8px 0 16px 0; padding-left: 20px;">
                    <li style="margin-bottom: 4px;">Your appointment may be cancelled if you do not arrive soon.</li>
                    <li style="margin-bottom: 4px;">If you are running late, please contact the clinic immediately.</li>
                    <li style="margin-bottom: 4px;">You can reschedule or cancel your appointment through your client portal if needed.</li>
                </ul>

                <p style="font-size: 13px; color: #6b7280; margin-top: 24px;">
                    If you have any questions or need to reschedule, please contact us as soon as possible.
                </p>

                <p style="font-size: 13px; color: #9ca3af; margin-top: 24px;">
                    This email was sent automatically. Please do not reply directly to this message.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>

