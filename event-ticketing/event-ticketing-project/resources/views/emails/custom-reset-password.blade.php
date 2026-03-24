<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Flowtix</title>
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
            margin-bottom: 25px;
        }
        p {
            color: #555555;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        a.button {
            display: inline-block;
            background-color: #555555;
            color: #ffffff;
            font-weight: bold;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            margin-bottom: 30px;
            margin-top: 10px;
            width: 80%;
            box-sizing: border-box;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #999999;
            text-align: center;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .footer-link {
            color: #555555;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <span class="logo">Flowtix</span>
    </div>
    
    <div class="container">
        <h1>Reset Password</h1>
        
        <p style="text-align: left;">Hello {{ $user->name ?? 'User' }},</p>
        
        <p style="text-align: left;">
            We received a request to reset your password for your Flowtix account. 
            Click the button below to choose a new password.
        </p>

        <a href="{{ $url }}" class="button">Reset Password</a>

        <p style="text-align: left; font-size: 14px; color: #777777;">
            This link will expire in 60 minutes. If you did not request a password reset, you can safely ignore this email.
        </p>
        
        <p style="text-align: left; font-size: 14px; margin-bottom: 0;">
            Regards,<br>
            <strong style="color: #444444;">The Flowtix Team</strong>
        </p>
    </div>

    <div class="footer">
        <p style="margin-bottom: 15px;">
            If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
        </p>
        <p style="margin-top: 0;">
            <a href="{{ $url }}" class="footer-link">{{ $url }}</a>
        </p>
        <p style="margin-top: 30px;">
            &copy; {{ date('Y') }} Flowtix. All rights reserved.
        </p>
    </div>
</body>
</html>
