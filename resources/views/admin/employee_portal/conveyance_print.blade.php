<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ websiteTitle('Conveyance Print') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #222; }
        .header { margin-bottom: 16px; }
        .meta { margin-bottom: 18px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; font-size: 13px; text-align: left; }
        th { background: #f3f3f3; }
        .text-right { text-align: right; }
        @media print {
            body { margin: 10px; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Conveyance Report</h2>
        <div class="meta">
            <div>Type: {{ $filters['type'] ?: 'All' }}</div>
            <div>Status: {{ $filters['status'] ?: 'All' }}</div>
            <div>Date Range: {{ $filters['date_from'] ?: 'Any' }} to {{ $filters['date_to'] ?: 'Any' }}</div>
            <div>Printed At: {{ now()->format('d M Y h:i A') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Reason</th>
                <th>Admin Remark</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $request->type)) }}</td>
                    <td class="text-right">{{ number_format($request->amount, 2) }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                    <td>{{ $request->payment_status ? ucfirst($request->payment_status) : '--' }}</td>
                    <td>{{ $request->reason ?: '--' }}</td>
                    <td>{{ $request->admin_remark ?: '--' }}</td>
                    <td>{{ $request->created_at?->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">No conveyance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
