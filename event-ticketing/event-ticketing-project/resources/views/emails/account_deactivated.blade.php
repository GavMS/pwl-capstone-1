<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Flowtix Account Has Been Deactivated</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #555555; color: #ffffff; padding: 32px 40px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0; font-size: 13px; opacity: 0.7; }
        .body { padding: 36px 40px; }
        .body p { color: #555555; font-size: 14px; line-height: 1.7; margin: 0 0 16px; }
        .alert-box { background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px 24px; margin: 24px 0; border-left: 4px solid #ef4444; }
        .alert-box p { margin: 0; color: #b91c1c; font-weight: 600; font-size: 14px; }
        .footer { padding: 20px 40px; border-top: 1px solid #f0f0f0; font-size: 12px; color: #aaaaaa; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>flowtix.</h1>
        <p>Account status update</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $user->name }}</strong>,</p>
        <p>We are writing to inform you that your <strong>Flowtix</strong> account has been deactivated by an administrator.</p>

        <div class="alert-box">
            <p>You will no longer be able to log in to Flowtix or access your dashboard using this account.</p>
        </div>

        <p>If you believe this is a mistake or require further assistance, please contact your administrator for more information.</p>

        <p style="margin-top: 24px; font-size: 12px; color: #aaaaaa;">
            This is an automated message. Please do not reply directly to this email.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Flowtix. All rights reserved.
    </div>
</div>
</body>
</html>
