<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        .otp-code {
            background: #f4f4f4;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            letter-spacing: 6px;
            font-weight: bold;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>🔐 Secure Login: Your OTP Verification Code</h1>
    <p>Hello! 👋</p>
    <p>Thank you for using our service.</p>
    <p>Here is your secure verification code:</p>
    <div class="otp-code">{{ $otp }}</div>
    <p><strong>This code will expire in 5 minutes.</strong></p>
    <p>If you did not request this code, please secure your account:</p>
    <p>
        <a href="{{ $secureAccountUrl }}" style="background: #007bff; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Secure My Account
        </a>
    </p>
    <div class="footer">
        <p>Need help? Contact our support team.</p>
        <p>Best regards,<br>The Sic Mundus Creatus Team</p>
    </div>
</body>
</html>