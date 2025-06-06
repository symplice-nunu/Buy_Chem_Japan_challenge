<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            color: #333333;
            font-size: 24px;
            margin: 0 0 20px;
            text-align: center;
        }
        .verification-code {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 4px;
            margin: 10px 0;
        }
        .info {
            color: #666666;
            font-size: 14px;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            color: #999999;
            font-size: 12px;
        }
        .warning {
            color: #dc3545;
            font-size: 13px;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email Address</h1>
        </div>
        
        <p>Hello,</p>
        
        <p>Thank you for registering with Buy Chem Japan. To complete your registration, please use the verification code below:</p>
        
        <div class="verification-code">
            <div class="code">{{ $verificationCode }}</div>
        </div>
        
        <p class="info">
            This verification code will expire in 24 hours.<br>
            If you didn't request this code, please ignore this email.
        </p>
        
        <div class="warning">
            For security reasons, please do not share this code with anyone.
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Buy Chem Japan. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 