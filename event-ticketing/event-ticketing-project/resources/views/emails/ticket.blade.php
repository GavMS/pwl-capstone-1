<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Your E-Ticket - Flowtix</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #111;
        }

        .container {
            margin: 0 auto;
            max-width: 600px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e5e5e5;
        }

        .header {
            background: #111111;
            color: white;
            padding: 28px 40px;
            text-align: center;
        }

        .header .brand {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #aaaaaa;
            margin-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
        }

        .header p {
            margin: 8px 0 0;
            font-size: 13px;
            color: #cccccc;
        }

        .content {
            padding: 36px 40px;
            text-align: center;
        }

        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 6px;
        }

        .subtext {
            font-size: 13px;
            color: #555555;
            margin-bottom: 28px;
        }

        .qr-box {
            margin: 0 auto 20px;
            padding: 16px;
            border: 2px dashed #dddddd;
            border-radius: 12px;
            display: inline-block;
            background-color: #fafafa;
        }

        .qr-box img {
            width: 200px;
            height: 200px;
            display: block;
        }

        .code-pill {
            background-color: #f1f1f1;
            padding: 8px 18px;
            border-radius: 20px;
            font-family: monospace;
            font-size: 15px;
            font-weight: bold;
            color: #333333;
            letter-spacing: 2px;
        }

        .details {
            margin-top: 28px;
            text-align: left;
            border-top: 1px solid #eeeeee;
            padding-top: 20px;
        }

        .detail-row {
            margin-bottom: 14px;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            color: #999999;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 3px;
        }

        .value {
            font-size: 15px;
            font-weight: bold;
            color: #111111;
        }

        .pdf-note {
            margin-top: 24px;
            padding: 12px 16px;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 12px;
            color: #666666;
        }

        .footer {
            background-color: #f9f9f9;
            text-align: center;
            padding: 18px;
            font-size: 11px;
            color: #999999;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <p class="brand">Flowtix</p>
            <h1>{{ $event->title }}</h1>
            <p>{{ $event->date->format('l, d F Y - H:i') }} &bull; {{ $event->location }}</p>
        </div>
        <div class="content">
            <p class="greeting">Hello, {{ $ticket->attendee_name }}!</p>
            <p class="subtext">Your ticket has been confirmed. The complete ticket PDF is attached to this email.</p>

            {{-- QR Code — loaded externally to bypass email client SVG blocks --}}
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($ticket->unique_code) }}" alt="Ticket QR Code" style="width: 200px; height: 200px; display: block;">
            </div>

            <div style="margin-top: 12px; margin-bottom: 24px;">
                <span class="code-pill">{{ $ticket->unique_code }}</span>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="label">Ticket Type</span>
                    <span class="value">{{ $ticket->eventTicketType->ticketType->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Attendee Name</span>
                    <span class="value">{{ $ticket->attendee_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">ID Number (KTP/Passport)</span>
                    <span class="value">{{ $ticket->attendee_id_card }}</span>
                </div>
            </div>

            <div class="pdf-note">
                📄 Your ticket PDF is attached to this email. Save it to your device for offline access at the event entrance.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Flowtix. All rights reserved.<br>
            Please do not share your Ticket Code with anyone.
        </div>
    </div>
</body>

</html>