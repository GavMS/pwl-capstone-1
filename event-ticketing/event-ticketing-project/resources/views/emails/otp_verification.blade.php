<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification - Flowtix</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            margin: 0 auto;
            text-align: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .logo {
            font-size: 30px;
            font-weight: bold;
            color: #555555;
            margin-bottom: 30px;
            text-align: center;
            display: block;
        }
        h1 {
            color: #444444;
            font-size: 24px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 10px;
        }
        p {
            color: #555555;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .otp-box {
            background-color: #f3f4f6;
            border-radius: 16px;
            padding: 24px 32px;
            margin: 24px 0;
            display: inline-block;
            width: 80%;
            box-sizing: border-box;
        }
        .otp-label {
            font-size: 13px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            color: #333333;
            letter-spacing: 10px;
            margin: 0;
        }
        .expiry-note {
            font-size: 13px;
            color: #999999;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <span class="logo">Flowtix</span>
    </div>

    <div class="container">
        <h1>Verify Your Email</h1>

        <p style="text-align: left;">Hello {{ $userName }},</p>

        <p style="text-align: left;">
            Thanks for signing up! Please use the OTP code below to verify your email address and complete your registration.
        </p>

        <div class="otp-box">
            <p class="otp-label">Your OTP Code</p>
            <p class="otp-code">{{ $otpCode }}</p>
        </div>

        <p class="expiry-note">This code will expire in <strong>10 minutes</strong>.</p>

        <p style="text-align: left; font-size: 14px; color: #777777; margin-top: 20px;">
            If you did not create a Flowtix account, you can safely ignore this email.
        </p>

        <p style="text-align: left; font-size: 14px; margin-bottom: 0;">
            Regards,<br>
            <strong style="color: #444444;">The Flowtix Team</strong>
        </p>
    </div>

    <div class="footer">
        <p style="margin-top: 30px;">
            &copy; {{ date('Y') }} Flowtix. All rights reserved.
        </p>
    </div>
</body>
</html>
