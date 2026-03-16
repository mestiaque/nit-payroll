@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Loader Design Selector') }}</title>
@endsection

@push('css')
<style>
    .loader-page-wrap {
        padding: 8px 0 16px;
    }
    .loader-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }
    .loader-card {
        background: #fff;
        border: 1px solid #e5ebf5;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 4px 14px rgba(27, 53, 95, 0.06);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .loader-card.active {
        border-color: #3559e6;
        box-shadow: 0 0 0 2px rgba(53, 89, 230, 0.15);
    }
    .loader-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .loader-title {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #1f335f;
    }
    .loader-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 999px;
        background: #eff3ff;
        color: #3559e6;
        font-weight: 600;
    }
    .loader-stage {
        height: 130px;
        border-radius: 10px;
        background: #f7f9fd;
        border: 1px dashed #d8e0ee;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .loader-actions {
        display: flex;
        gap: 8px;
    }
    .loader-note {
        color: #60718e;
        font-size: 12px;
    }

    .x-matrix {
        display: grid;
        grid-template-columns: repeat(4, 14px);
        gap: 6px;
    }
    .x-matrix-cell {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        background: #cfdbff;
        animation: x-matrix-pulse 1.2s infinite ease-in-out;
    }
    .x-matrix-cell:nth-child(4n+1) { animation-delay: 0s; }
    .x-matrix-cell:nth-child(4n+2) { animation-delay: 0.15s; }
    .x-matrix-cell:nth-child(4n+3) { animation-delay: 0.3s; }
    .x-matrix-cell:nth-child(4n+4) { animation-delay: 0.45s; }

    .x-bio {
        width: 86px;
        height: 86px;
        position: relative;
    }
    .x-bio-ring {
        position: absolute;
        border-radius: 50%;
        border: 3px solid;
        border-color: #2e67db #2e67db transparent transparent;
        animation: x-bio-spin 1.4s linear infinite;
    }
    .x-bio-ring.r1 { inset: 6px; }
    .x-bio-ring.r2 {
        inset: 16px;
        border-color: #08a88a #08a88a transparent transparent;
        animation-duration: 1.1s;
        animation-direction: reverse;
    }
    .x-bio-ring.r3 {
        inset: 26px;
        border-color: #7c4dff #7c4dff transparent transparent;
        animation-duration: 0.9s;
    }

    .x-ledger {
        width: 126px;
        padding: 10px 10px 9px;
        border-radius: 8px;
        background: #eef3ff;
        box-shadow: inset 0 0 0 1px #d3ddf8;
    }
    .x-ledger-row {
        height: 6px;
        border-radius: 999px;
        background: #d2ddf8;
        margin-bottom: 7px;
        overflow: hidden;
        position: relative;
    }
    .x-ledger-row:last-child { margin-bottom: 0; }
    .x-ledger-row::after {
        content: '';
        position: absolute;
        top: 0;
        left: -45%;
        width: 45%;
        height: 100%;
        background: linear-gradient(90deg, #4f67ff, #1ec5ff, #23bf8f);
        animation: x-ledger-flow 1.4s ease-in-out infinite;
    }
    .x-ledger-row:nth-child(2)::after { animation-delay: 0.16s; }
    .x-ledger-row:nth-child(3)::after { animation-delay: 0.32s; }
    .x-ledger-row:nth-child(4)::after { animation-delay: 0.48s; }

    .x-payslip {
        width: 102px;
        height: 72px;
        border-radius: 10px;
        background: #f4f8ff;
        border: 1px solid #d1dcf8;
        position: relative;
        animation: x-slip-breathe 1.6s ease-in-out infinite;
        box-shadow: 0 6px 14px rgba(48, 74, 130, 0.1);
    }
    .x-payslip::before,
    .x-payslip::after {
        content: '';
        position: absolute;
        left: 10px;
        right: 10px;
        border-radius: 99px;
        background: #cad8fa;
        height: 6px;
    }
    .x-payslip::before { top: 16px; }
    .x-payslip::after {
        top: 30px;
        right: 28px;
    }
    .x-payslip-coin {
        position: absolute;
        right: 10px;
        bottom: 10px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, #ffe88a, #f3b500);
        box-shadow: 0 0 0 0 rgba(243, 181, 0, 0.55);
        animation: x-coin-pulse 1.3s ease-out infinite;
    }

    @keyframes x-matrix-pulse {
        0%, 100% { opacity: 0.25; transform: scale(0.88); }
        50% { opacity: 1; transform: scale(1); background: #4f67ff; }
    }
    @keyframes x-bio-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes x-ledger-flow {
        0% { left: -45%; }
        100% { left: 100%; }
    }
    @keyframes x-slip-breathe {
        0%, 100% { transform: translateY(0) scale(0.98); }
        50% { transform: translateY(-2px) scale(1); }
    }
    @keyframes x-coin-pulse {
        0% { box-shadow: 0 0 0 0 rgba(243, 181, 0, 0.55); }
        100% { box-shadow: 0 0 0 12px rgba(243, 181, 0, 0); }
    }
</style>
@endpush

@section('contents')
<div class="breadcrumb-area">
    <h1>Loader Design Selector</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item">Settings</li>
        <li class="item">Loader Designs</li>
    </ol>
</div>

<div class="loader-page-wrap">
    <div class="alert alert-info">
        এক পেজে ৪টা loader preview দেওয়া আছে। `Select` চাপলে পুরো সাইটে ওই design active হবে.
    </div>

    <div class="loader-grid" id="loaderDesignGrid">
        <div class="loader-card" data-design="attendance-matrix">
            <div class="loader-head">
                <h6 class="loader-title">Attendance Matrix</h6>
                <span class="loader-badge">Attendance</span>
            </div>
            <div class="loader-stage">
                <div class="x-matrix">
                    <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                    <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                    <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                    <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                </div>
            </div>
            <div class="loader-actions">
                <button class="btn btn-primary btn-sm" onclick="selectLoaderDesign('attendance-matrix')">Select</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="previewLoaderDesign('attendance-matrix')">Preview</button>
            </div>
        </div>

        <div class="loader-card" data-design="biometric-wave">
            <div class="loader-head">
                <h6 class="loader-title">Biometric Wave</h6>
                <span class="loader-badge">Biometric</span>
            </div>
            <div class="loader-stage">
                <div class="x-bio"><div class="x-bio-ring r1"></div><div class="x-bio-ring r2"></div><div class="x-bio-ring r3"></div></div>
            </div>
            <div class="loader-actions">
                <button class="btn btn-primary btn-sm" onclick="selectLoaderDesign('biometric-wave')">Select</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="previewLoaderDesign('biometric-wave')">Preview</button>
            </div>
        </div>

        <div class="loader-card" data-design="ledger-flow">
            <div class="loader-head">
                <h6 class="loader-title">Ledger Flow</h6>
                <span class="loader-badge">Payroll</span>
            </div>
            <div class="loader-stage">
                <div class="x-ledger">
                    <div class="x-ledger-row"></div><div class="x-ledger-row"></div><div class="x-ledger-row"></div><div class="x-ledger-row"></div>
                </div>
            </div>
            <div class="loader-actions">
                <button class="btn btn-primary btn-sm" onclick="selectLoaderDesign('ledger-flow')">Select</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="previewLoaderDesign('ledger-flow')">Preview</button>
            </div>
        </div>

        <div class="loader-card" data-design="payslip-pulse">
            <div class="loader-head">
                <h6 class="loader-title">Payslip Pulse</h6>
                <span class="loader-badge">Finance</span>
            </div>
            <div class="loader-stage">
                <div class="x-payslip"><div class="x-payslip-coin"></div></div>
            </div>
            <div class="loader-actions">
                <button class="btn btn-primary btn-sm" onclick="selectLoaderDesign('payslip-pulse')">Select</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="previewLoaderDesign('payslip-pulse')">Preview</button>
            </div>
        </div>
    </div>

    <p class="loader-note mt-3 mb-0">Current selection browser localStorage key: <code>x_loader_design</code></p>
</div>
@endsection

@push('js')
<script>
    function markSelectedCard(design) {
        document.querySelectorAll('#loaderDesignGrid .loader-card').forEach(function (card) {
            card.classList.toggle('active', card.getAttribute('data-design') === design);
        });
    }

    function selectLoaderDesign(design) {
        try {
            localStorage.setItem('x_loader_design', design);
        } catch (e) {}

        if (window.XLoader && typeof window.XLoader.setDesign === 'function') {
            window.XLoader.setDesign(design);
        }

        markSelectedCard(design);
    }

    function previewLoaderDesign(design) {
        if (window.XLoader && typeof window.XLoader.setDesign === 'function') {
            window.XLoader.setDesign(design);
            window.XLoader.show();
            setTimeout(function () {
                window.XLoader.hide();
            }, 1200);
        }

        markSelectedCard(design);
    }

    (function initLoaderSelector() {
        var selected = 'attendance-matrix';

        try {
            selected = localStorage.getItem('x_loader_design') || selected;
        } catch (e) {}

        markSelectedCard(selected);
    })();
</script>
@endpush
