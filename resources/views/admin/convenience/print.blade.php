@extends('printMaster')

@section('title')
Convenience List
@endsection

@push('css')
<style>
    .filter-meta {
        margin: 8px 0 12px 0;
        font-size: 12px;
    }
    .filter-meta td {
        padding: 4px 6px;
    }
    .text-right {
        text-align: right;
    }
</style>
@endpush

@section('contents')
<table class="filter-meta">
    <tbody>
        <tr>
            <td><strong>Employee ID:</strong> {{ $filters['employee_id'] ?: 'All' }}</td>
            <td><strong>Type:</strong> {{ $filters['type'] ?: 'All' }}</td>
            <td><strong>Status:</strong> {{ $filters['status'] ?: 'All' }}</td>
            @if($hasPaymentStatusColumn)
                <td><strong>Payment:</strong> {{ $filters['payment_status'] ?: 'All' }}</td>
            @endif
        </tr>
        <tr>
            <td colspan="{{ $hasPaymentStatusColumn ? 4 : 3 }}"><strong>Date Range:</strong> {{ $filters['date_from'] ?: 'Any' }} to {{ $filters['date_to'] ?: 'Any' }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Status</th>
            @if($hasPaymentStatusColumn)
                <th>Payment</th>
            @endif
            <th>Remark</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($requests as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->user->name ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->type)) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                <td>{{ ucfirst($item->status) }}</td>
                @if($hasPaymentStatusColumn)
                    <td>{{ $item->payment_status ? ucfirst($item->payment_status) : '--' }}</td>
                @endif
                <td>{{ $item->admin_remark ?: '--' }}</td>
                <td>{{ $item->created_at?->format('d M Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $hasPaymentStatusColumn ? 8 : 7 }}" style="text-align: center;">No convenience request found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
