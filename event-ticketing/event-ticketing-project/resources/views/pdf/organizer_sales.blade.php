<!DOCTYPE html>
<html>
<head>
    <title>Organizer Sales Report</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; font-family: sans-serif; }
        th { background-color: #f4f4f4; }
        h1 { font-size: 20px; margin-bottom: 5px; font-family: sans-serif; }
        .summary { margin-bottom: 20px; font-size: 14px; font-family: sans-serif; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>Sales & Payouts Report</h1>
    <div class="summary">
        <p><strong>Total Revenue:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p><strong>Total Tickets Sold:</strong> {{ number_format($totalTicketsSold) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th class="text-center">Tickets Sold</th>
                <th class="text-right">Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData as $data)
            <tr>
                <td>{{ $data['event']->title }}</td>
                <td>{{ $data['event']->date->format('M d, Y') }}</td>
                <td class="text-center">{{ $data['tickets_sold'] }}</td>
                <td class="text-right">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
