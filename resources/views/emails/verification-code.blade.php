<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - K-Derma</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: linear-gradient(135deg, #ffffff 0%, #fef7f7 100%);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #fce7f3;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ec4899;
            margin-bottom: 10px;
        }
        .code-box {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #fce7f3;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">K-Derma</div>
            <h1 style="color: #333; margin: 0;">Email Verification</h1>
        </div>

        <p style="font-size: 16px; color: #555;">
            @if($userName)
                Hello <strong>{{ $userName }}</strong>,
            @else
                Hello,
            @endif
        </p>

        <p style="font-size: 16px; color: #555;">
            Thank you for registering with K-Derma! To complete your registration and verify your email address, please use the verification code below:
        </p>

        <div class="code-box">
            <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Your Verification Code</p>
            <div class="code">{{ $verificationCode }}</div>
            <p style="margin: 10px 0 0 0; font-size: 12px; opacity: 0.9;">This code will expire in 15 minutes</p>
        </div>

        <p style="font-size: 14px; color: #666;">
            Enter this code on the verification page to activate your account. If you didn't create an account with K-Derma, please ignore this email.
        </p>

        <p style="font-size: 14px; color: #666; margin-top: 20px;">
            <strong>Note:</strong> For security reasons, this code will expire in 15 minutes. If you need a new code, you can request one from the verification page.
        </p>

        <div class="footer">
            <p style="margin: 0;">© {{ date('Y') }} K-Derma. All rights reserved.</p>
            <p style="margin: 5px 0 0 0;">This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>

