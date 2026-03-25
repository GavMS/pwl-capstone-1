<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Flowtix Account Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #555555; color: #ffffff; padding: 32px 40px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0; font-size: 13px; opacity: 0.7; }
        .body { padding: 36px 40px; }
        .body p { color: #555555; font-size: 14px; line-height: 1.7; margin: 0 0 16px; }
        .credentials-box { background: #f4f4f4; border-radius: 12px; padding: 20px 24px; margin: 24px 0; }
        .credentials-box p { margin: 6px 0; font-size: 14px; color: #444444; }
        .credentials-box span { font-weight: 800; color: #333333; font-family: monospace; font-size: 15px; }
        .btn { display: inline-block; margin-top: 8px; padding: 12px 28px; background: #555555; color: #ffffff; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; }
        .footer { padding: 20px 40px; border-top: 1px solid #f0f0f0; font-size: 12px; color: #aaaaaa; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>flowtix.</h1>
        <p>Your account credentials</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $user->name }}</strong>,</p>
        <p>An account has been created for you on <strong>Flowtix</strong>. Here are your login credentials:</p>

        <div class="credentials-box">
            <p>Username: <span>{{ $user->username }}</span></p>
            <p>Temporary Password: <span>{{ $plainPassword }}</span></p>
            <p>Role: <span>{{ ucfirst($user->role) }}</span></p>
        </div>

        <p>Please log in and change your password immediately using the <strong>Forgot Password</strong> feature.</p>

        <a href="{{ url('/login') }}" class="btn">Login to Flowtix</a>

        <p style="margin-top: 24px; font-size: 12px; color: #aaaaaa;">
            If you did not expect this email, please ignore it or contact your administrator.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Flowtix. All rights reserved.
    </div>
</div>
</body>
</html>
