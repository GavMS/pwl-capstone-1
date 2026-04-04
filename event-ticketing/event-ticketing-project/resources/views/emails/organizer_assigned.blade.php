<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>You've Been Assigned to an Event – Flowtix</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #555555; color: #ffffff; padding: 32px 40px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0; font-size: 13px; opacity: 0.7; }
        .body { padding: 36px 40px; }
        .body p { color: #555555; font-size: 14px; line-height: 1.7; margin: 0 0 16px; }
        .event-box { background: #f4f4f4; border-radius: 12px; padding: 20px 24px; margin: 24px 0; }
        .event-box p { margin: 6px 0; font-size: 14px; color: #444444; }
        .event-box span { font-weight: 800; color: #333333; }
        .btn { display: inline-block; margin-top: 8px; padding: 12px 28px; background: #555555; color: #ffffff; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; }
        .footer { padding: 20px 40px; border-top: 1px solid #f0f0f0; font-size: 12px; color: #aaaaaa; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>flowtix.</h1>
        <p>Event assignment notification</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $organizer->name }}</strong>,</p>
        <p>You have been assigned as the organizer for the following event on <strong>Flowtix</strong>:</p>

        <div class="event-box">
            <p>Event: <span>{{ $event->title }}</span></p>
            <p>Date: <span>{{ $event->date->format('d M Y, H:i') }}</span></p>
            <p>Location: <span>{{ $event->location }}, {{ $event->city }}</span></p>
            <p>Status: <span>{{ ucfirst($event->status) }}</span></p>
        </div>

        <p>Please log in to your Flowtix account to review and manage the event details.</p>

        <a href="{{ url('/login') }}" class="btn">Go to Flowtix</a>

        <p style="margin-top: 24px; font-size: 12px; color: #aaaaaa;">
            If you did not expect this email, please contact your administrator.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Flowtix. All rights reserved.
    </div>
</div>
</body>
</html>
