<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your E-Ticket - Flowtix</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; color: #333; }
        .container { max-w-[600px] mx-auto bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100; margin: 0 auto; max-width: 600px; }
        .header { background: linear-gradient(135deg, #38b2ac, #319795); color: white; padding: 30px 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; text-transform: lowercase; letter-spacing: -1px; }
        .header p { margin: 10px 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 40px; text-align: center; }
        .qr-box { margin: 0 auto 30px; padding: 20px; border: 2px dashed #e2e8f0; border-radius: 16px; display: inline-block; background-color: #f8fafc; }
        .qr-box img { width: 200px; height: 200px; display: block; }
        .code-pill { background-color: #f1f5f9; padding: 8px 16px; border-radius: 20px; font-family: monospace; font-size: 16px; font-weight: bold; color: #475569; letter-spacing: 2px; }
        .details { margin-top: 30px; text-align: left; border-top: 1px solid #f1f5f9; padding-top: 20px; }
        .detail-row { margin-bottom: 15px; }
        .label { font-size: 11px; text-transform: uppercase; font-weight: bold; color: #94a3b8; letter-spacing: 1px; display: block; margin-bottom: 4px; }
        .value { font-size: 16px; font-weight: bold; color: #1e293b; }
        .footer { background-color: #f8fafc; text-align: center; padding: 20px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="text-transform: uppercase">{{ $event->title }}</h1>
            <p>{{ $event->date->format('l, d F Y - H:i') }} | {{ $event->location }}</p>
        </div>
        <div class="content">
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Hello, {{ $ticket->attendee_name }}!</h2>
            <p style="color: #64748b; margin-bottom: 30px;">Here is your official E-Ticket. Please prepare this QR code to be scanned at the entrance.</p>
            
            <div class="qr-box">
                <img src="{!! $message->embedData(base64_decode($qrCodeBase64), 'qrcode.svg', 'image/svg+xml') !!}" alt="QR Code">
                <div style="margin-top: 15px;">
                    <span class="code-pill">{{ $ticket->unique_code }}</span>
                </div>
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
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Flowtix. All rights reserved.<br>
            Please do not share your QR Code with anyone.
        </div>
    </div>
</body>
</html>
