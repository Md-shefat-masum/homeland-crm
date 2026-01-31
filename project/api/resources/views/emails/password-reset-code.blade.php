<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .code-box {
            background-color: #fff;
            border: 2px dashed #4CAF50;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 4px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Verification Code</h2>
        
        <p>Hello {{ $user->name }},</p>
        
        <p>You have requested to reset your password. Please use the following verification code:</p>
        
        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>
        
        <p><strong>This code will expire in {{ $expires_in_minutes }} minutes.</strong></p>
        
        <p>If you did not request this password reset, please ignore this email or contact support if you have concerns.</p>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} CRM Homland. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

