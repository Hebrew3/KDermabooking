<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Contact Form Submission - K-Derma</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #ec4899;
            margin-bottom: 5px;
        }
        .field-value {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ec4899;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>K-Derma Contact Form</h1>
        <p>New message received from website</p>
    </div>
    
    <div class="content">
        <div class="field">
            <div class="field-label">Name:</div>
            <div class="field-value">{{ $name }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">Email:</div>
            <div class="field-value">{{ $email }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">Message:</div>
            <div class="field-value">{{ $message }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>This message was sent from the K-Derma website contact form.</p>
        <p>Please respond directly to: {{ $email }}</p>
    </div>
</body>
</html>
