<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            color: #111827;
            padding: 24px;
        }
        .ticket {
            max-width: 560px;
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .header {
            background: #0f766e;
            padding: 28px 32px;
            text-align: center;
        }
        .header .label {
            font-size: 9px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #99f6e4;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 4px;
        }
        .header .type {
            font-size: 11px;
            color: #ccfbf1;
        }
        .dashed {
            border-top: 2px dashed #e5e7eb;
            margin: 0 16px;
        }
        .qr-section {
            text-align: center;
            padding: 24px 16px 16px;
        }
        .qr-section img {
            width: 160px;
            height: 160px;
            display: inline-block;
        }
        .unique-code {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            letter-spacing: 2px;
            margin-top: 10px;
        }
        .qr-hint {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .details {
            padding: 8px 28px 28px;
        }
        table.detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.detail-table tr {
            border-bottom: 1px solid #f3f4f6;
        }
        table.detail-table tr:last-child {
            border-bottom: none;
        }
        table.detail-table td {
            padding: 10px 0;
            font-size: 12px;
        }
        table.detail-table td.lbl {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            width: 42%;
        }
        table.detail-table td.val {
            font-weight: bold;
            color: #111827;
            text-align: right;
        }
        .footer {
            background: #f9fafb;
            text-align: center;
            padding: 14px;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <p class="label">E-Ticket</p>
            <h1>{{ $event?->title ?? 'Event' }}</h1>
            <p class="type">{{ $ticket->eventTicketType?->ticketType?->name ?? 'Ticket' }}</p>
        </div>

        <div class="dashed"></div>

        <div class="qr-section">
            <img src="{{ $qrDataUri }}" alt="Ticket QR Code">
            <p class="unique-code">{{ $ticket->unique_code }}</p>
            <p class="qr-hint">Scan this QR Code at the event entrance</p>
        </div>

        <div class="dashed"></div>

        <div class="details">
            <table class="detail-table">
                @if($event)
                <tr>
                    <td class="lbl">Date &amp; Time</td>
                    <td class="val">{{ $event->date->format('d F Y') }} — {{ $event->date->format('H:i') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Location</td>
                    <td class="val">{{ $event->location }}, {{ $event->city }}</td>
                </tr>
                @endif
                <tr>
                    <td class="lbl">Ticket Price</td>
                    <td class="val">{{ ($ticket->eventTicketType?->price ?? 0) == 0 ? 'Free' : 'Rp' . number_format($ticket->eventTicketType?->price ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Status</td>
                    <td class="val">{{ ucfirst($ticket->status) }}</td>
                </tr>
                <tr>
                    <td class="lbl">Ticket Holder</td>
                    <td class="val">{{ $ticket->attendee_name ?? $ticket->user?->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Flowtix &mdash; Do not share your ticket code with anyone.
        </div>
    </div>
</body>
</html>
