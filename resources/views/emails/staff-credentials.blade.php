<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your K-Derma Staff Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
        .header {
            background: linear-gradient(120deg, #ec4899, #f472b6);
            padding: 32px;
            color: #fff;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 32px;
        }
        .credential-card {
            background: #fdf2f8;
            border-left: 4px solid #ec4899;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .credential-card p {
            margin: 0 0 12px 0;
            font-size: 15px;
        }
        .credential-card span {
            font-weight: bold;
            color: #be185d;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(120deg, #ec4899, #be185d);
            color: #fff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 999px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 20px 0;
        }
        .footer {
            padding: 24px;
            background: #f9fafb;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }
        .notice {
            background: #fef3c7;
            border-radius: 10px;
            padding: 16px;
            font-size: 14px;
            color: #92400e;
            border: 1px solid #fde68a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to K-Derma</h1>
            <p>Your staff account is ready</p>
        </div>
        <div class="content">
            <p>Hi {{ $user->full_name }},</p>
            <p>We've created your staff account for the K-Derma Booking System. Use the credentials below to sign in and start managing your appointments.</p>

            <div class="credential-card">
                <p><span>Login Email:</span> {{ $user->email }}</p>
                <p><span>Temporary Password:</span> {{ $plainPassword }}</p>
            </div>

            <p>Click the button below to access the staff portal:</p>
            <a href="{{ $loginUrl }}" class="cta-button" target="_blank" rel="noopener noreferrer">Sign in to K-Derma</a>

            <div class="notice">
                <strong>Security reminder:</strong> Please sign in as soon as possible and update your password from the profile settings page. This temporary password is valid for your first login only.
            </div>

            <p style="margin-top: 24px;">If you have any trouble signing in, please contact the administrator for assistance.</p>

            <p>Welcome aboard,<br>
            <strong>K-Derma Booking System</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} K-Derma Booking System. All rights reserved.<br>
            This is an automated message. Please do not reply.
        </div>
    </div>
</body>
</html>

