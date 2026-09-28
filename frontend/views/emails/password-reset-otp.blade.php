<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Password Reset OTP</h2>
    <p>Hello {{ $user->name }},</p>
    <p>You requested a password reset. Use this 6-digit OTP code to verify your request:</p>
    <div style="font-size: 28px; font-weight: 700; letter-spacing: 6px; padding: 16px 20px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; display: inline-block; margin: 12px 0;">
        {{ $otp }}
    </div>
    <p>This code expires in 10 minutes.</p>
    <p>If you did not request this reset, you can ignore this email.</p>
</body>
</html>
