<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); padding: 30px; text-align: center; color: white; }
        .content { padding: 40px 30px; text-align: center; }
        .otp-box { background: #f8f9fa; border: 2px dashed #667eea; border-radius: 8px; padding: 20px; margin: 30px 0; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #667eea; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Kwikster CMS</h1>
            <p style="margin: 10px 0 0 0;">Login Verification</p>
        </div>
        <div class="content">
            <h2 style="color: #333; margin-top: 0;">Hello {{ $userName }}!</h2>
            <p style="color: #666; font-size: 16px;">Your One-Time Password (OTP) for login is:</p>
            <div class="otp-box">{{ $otp }}</div>
            <p style="color: #666; font-size: 14px;">This OTP is valid for 10 minutes.</p>
            <p style="color: #999; font-size: 13px; margin-top: 30px;">If you didn't request this, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Kwikster CMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
