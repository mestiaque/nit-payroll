@extends('printMasterBlank')

@section('title', 'Document Print Preview')


@push('css')
<style>
    .company-head {
        text-align: center;
        margin-bottom: 12px;
    }
    .company-head h3 {
        margin: 0 0 4px;
    }
    .section-title {
        margin: 10px 0 6px;
        padding: 4px 6px;
        border-left: 4px solid #333;
        background: #f1f3f5;
        font-size: 14px;
    }
    .employee-block {
        page-break-after: always;
        break-after: page;
    }
    .employee-block:last-child {
        page-break-after: auto;
        break-after: auto;
    }
    .meta {
        margin-bottom: 8px;
        font-size: 12px;
    }
    .id-card-sheet {
        width: 5.4cm;
        margin: 0 auto;
    }
    .id-card-side {
        border: 1px solid #222;
        width: 5.4cm;
        height: 8.56cm;
        margin: 0 auto 6mm;
        padding: 1mm;
        box-sizing: border-box;
        background: #fff;
        overflow: hidden;
    }
    .id-card-back {
        transform: rotate(180deg);
        transform-origin: center;
    }
    .id-card-logo-wrap {
        text-align: center;
    }
    .id-card-logo {
        width: 16mm;
        height: 8mm;
        object-fit: contain;
    }
    .id-card-company {
        margin: 0.2mm 0 0.5mm;
        text-align: center;
        font-size: 3.9mm;
    }
    .id-card-address {
        margin: 0;
        text-align: center;
        font-size: 2.2mm;
    }
    .id-card-strip {
        margin-top: 1.2mm;
        background: #ec682c91;
        text-align: center;
        font-size: 3mm;
        font-weight: 700;
        padding: 0.7mm;
    }
    .id-card-strip-bottom {
        margin-top: 2mm;
        font-size: 3mm;
    }
    .id-card-photo-wrap {
        text-align: center;
        margin: 1mm 0;
    }
    .id-card-photo {
        width: 17mm;
        height: 20mm;
        object-fit: cover;
        border: 1px solid #666;
    }
    .id-card-info {
        width: 100%;
        border-collapse: collapse;
        font-size: 2.9mm !important;
        line-height: 1.25;
    }
    .id-card-info td {
        padding: 0;
        vertical-align: top;
        font-size: 2.6mm !important;
    }
    .id-card-info td:first-child {
        width: 28%;
        white-space: nowrap;
    }
    .id-sign-row {
        margin-top: 3mm;
        display: flex;
        justify-content: space-between;
        gap: 6mm;
    }
    .id-sign-line {
        width: 20mm;
        border-top: 1px dotted #222;
        margin-bottom: 0.5mm;
    }
    .id-sign-label {
        font-size: 2mm;
        text-align: center;
    }
    .id-back-head {
        margin: 2mm 0 0.8mm;
        text-align: center;
        font-size: 3.6mm;
        font-weight: 700;
    }
    .id-back-company {
        margin: 1.5mm 0 0.8mm;
        text-align: center;
        font-size: 3.8mm;
    }
    .id-back-text {
        margin: 0;
        text-align: center;
        font-size: 3.2mm;
        line-height: 1.25;
    }
    .mini-note {
        font-size: 11px;
        color: #444;
    }
    .two-col {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .two-col th,
    .two-col td {
        border: 1px solid #666;
        padding: 4px 6px;
        vertical-align: top;
        font-size: 12px;
    }
    .two-col th {
        width: 34%;
        background: #f8f9fa;
    }
    .letter-box {
        border: 1px solid #777;
        padding: 10px 12px;
        margin-top: 8px;
    }
    .letter-grid {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .letter-grid td {
        padding: 2px 4px;
        vertical-align: top;
        font-size: 12px;
    }
    .letter-grid .k {
        width: 26%;
        font-weight: 600;
    }
    .letter-grid .s {
        width: 3%;
        text-align: center;
    }
    .letter-grid .v {
        width: 21%;
    }
    .salary-table {
        width: 100%;
        border-collapse: collapse;
        margin: 6px 0 10px;
    }
    .salary-table th,
    .salary-table td {
        border: none !important;
        padding: 1px 1px;
        font-size: 12px;
    }
    .salary-table th {
        background: #f8f9fa;
    }
    .clauses {
        margin: 0;
        padding-left: 16px;
    }
    .clauses li {
        margin-bottom: 6px;
        font-size: 12px;
    }
    .sign-row {
        margin-top: 18px;
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }
    td{border:none !important;}
</style>
@endpush

@section('contents')

@if(count($employeesData) === 0)
    <div class="text-center py-5">
        <p class="text-muted">No employees found for the selected criteria.</p>
    </div>
@else
    @foreach($employeesData as $index => $data)
        <div class="employee-block">
            @if($data['hasTrailingEndif'])
                @if($data['employee'])
                    @include($data['template'], $data['vars'])
                @endif
            @else
                @include($data['template'], $data['vars'])
            @endif
        </div>
    @endforeach
@endif

@endsection
