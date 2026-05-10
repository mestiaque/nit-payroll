@extends('printMasterBlank')

@section('title', 'Pay Slip Print')

@push('css')
<style>
    .page-break {
        page-break-after: always;
        break-after: page;
    }
</style>
@endpush

@section('contents')
    @foreach($employeesData as $data)
        @include('admin.documents.pay_slip_print', $data)

        @if(! $loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
@endsection
