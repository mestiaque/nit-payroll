<!-- Loader Component - Multi Design (ERP/Payroll) -->

<script>
    (function () {
        if (window.location.href.indexOf('print') !== -1 || window.location.href.indexOf('Print') !== -1) {
            window.XLoaderOverride = true;
        }
    })();
</script>

<style>
    .x-loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.92);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    #x-inline-loader {
        display: none;
    }

    .x-loader-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .x-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .x-loader-text {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #35507d;
    }

    .x-loader-design {
        display: none;
    }

    .x-loader-design.active {
        display: block;
    }

    .x-loader-inline .x-loader-text {
        display: none;
    }

    /* Attendance Matrix */
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

    /* Biometric Wave */
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

    /* Ledger Flow */
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

    .x-ledger-row:last-child {
        margin-bottom: 0;
    }

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

    /* Payslip Pulse */
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

    .x-payslip::before {
        top: 16px;
    }

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

    .x-loader-inline .x-matrix {
        transform: scale(0.78);
        transform-origin: center;
    }

    .x-loader-inline .x-bio {
        transform: scale(0.78);
        transform-origin: center;
    }

    .x-loader-inline .x-ledger {
        transform: scale(0.78);
        transform-origin: center;
    }

    .x-loader-inline .x-payslip {
        transform: scale(0.78);
        transform-origin: center;
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

<!-- Full Page Loader -->
<div id="x-page-loader" class="x-loader-overlay">
    <div class="x-loader" data-loader-scope="full">
        <div class="x-loader-design x-design-attendance-matrix" data-loader-design="attendance-matrix">
            <div class="x-matrix">
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
            </div>
        </div>

        <div class="x-loader-design x-design-biometric-wave" data-loader-design="biometric-wave">
            <div class="x-bio"><div class="x-bio-ring r1"></div><div class="x-bio-ring r2"></div><div class="x-bio-ring r3"></div></div>
        </div>

        <div class="x-loader-design x-design-ledger-flow" data-loader-design="ledger-flow">
            <div class="x-ledger">
                <div class="x-ledger-row"></div><div class="x-ledger-row"></div><div class="x-ledger-row"></div><div class="x-ledger-row"></div>
            </div>
        </div>

        <div class="x-loader-design x-design-payslip-pulse" data-loader-design="payslip-pulse">
            <div class="x-payslip"><div class="x-payslip-coin"></div></div>
        </div>

        <div class="x-loader-text">Processing...</div>
    </div>
</div>

<!-- Inline Loader -->
<div id="x-inline-loader" class="x-loader-overlay" style="position: absolute; inset: 0; background: rgba(255, 255, 255, 0.82);">
    <div class="x-loader x-loader-inline" data-loader-scope="inline">
        <div class="x-loader-design x-design-attendance-matrix" data-loader-design="attendance-matrix">
            <div class="x-matrix">
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
                <div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div><div class="x-matrix-cell"></div>
            </div>
        </div>
        <div class="x-loader-design x-design-biometric-wave" data-loader-design="biometric-wave">
            <div class="x-bio"><div class="x-bio-ring r1"></div><div class="x-bio-ring r2"></div><div class="x-bio-ring r3"></div></div>
        </div>
        <div class="x-loader-design x-design-ledger-flow" data-loader-design="ledger-flow">
            <div class="x-ledger">
                <div class="x-ledger-row"></div><div class="x-ledger-row"></div><div class="x-ledger-row"></div><div class="x-ledger-row"></div>
            </div>
        </div>
        <div class="x-loader-design x-design-payslip-pulse" data-loader-design="payslip-pulse">
            <div class="x-payslip"><div class="x-payslip-coin"></div></div>
        </div>
    </div>
</div>

<script>
    const XLoader = {
        shown: false,
        currentDesign: 'attendance-matrix',
        allowedDesigns: ['attendance-matrix', 'biometric-wave', 'ledger-flow', 'payslip-pulse'],

        readSavedDesign: function () {
            try {
                const saved = localStorage.getItem('x_loader_design');
                return this.allowedDesigns.includes(saved) ? saved : this.currentDesign;
            } catch (e) {
                return this.currentDesign;
            }
        },

        setDesign: function (design) {
            const selected = this.allowedDesigns.includes(design) ? design : 'attendance-matrix';
            this.currentDesign = selected;

            try {
                localStorage.setItem('x_loader_design', selected);
            } catch (e) {}

            const scopes = document.querySelectorAll('[data-loader-scope]');
            scopes.forEach(function (scopeEl) {
                const variants = scopeEl.querySelectorAll('[data-loader-design]');
                variants.forEach(function (el) {
                    if (el.getAttribute('data-loader-design') === selected) {
                        el.classList.add('active');
                    } else {
                        el.classList.remove('active');
                    }
                });
            });
        },

        show: function () {
            if (this.shown || window.XLoaderOverride) return;
            this.shown = true;
            const loader = document.getElementById('x-page-loader');
            if (loader) loader.classList.add('active');
        },

        hide: function () {
            this.shown = false;
            const loader = document.getElementById('x-page-loader');
            if (loader) loader.classList.remove('active');
        },

        showInline: function (elementId) {
            const loader = document.getElementById('x-inline-loader');
            const target = document.getElementById(elementId);
            if (loader && target) {
                loader.style.display = 'flex';
                loader.style.position = 'absolute';
                loader.style.top = '0';
                loader.style.left = '0';
                loader.style.width = '100%';
                loader.style.height = '100%';
                loader.classList.add('active');
                target.style.position = 'relative';
                target.appendChild(loader);
            }
        },

        hideInline: function () {
            const loader = document.getElementById('x-inline-loader');
            if (loader) {
                loader.classList.remove('active');
                loader.style.display = 'none';
                document.body.appendChild(loader);
            }
        }
    };

    window.XLoader = XLoader;
    XLoader.setDesign(XLoader.readSavedDesign());

    window.addEventListener('storage', function (e) {
        if (e.key === 'x_loader_design' && e.newValue) {
            XLoader.setDesign(e.newValue);
        }
    });

    window.addEventListener('beforeunload', function () {
        if (window.XLoaderOverride) return;
        XLoader.show();
    });

    window.addEventListener('load', function () {
        setTimeout(function () {
            XLoader.hide();
        }, 350);
    });

    document.addEventListener('click', function (e) {
        if (window.XLoaderOverride) return;
        const target = e.target.closest('a');
        if (target) {
            const href = target.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript') && !target.classList.contains('no-loader') && !target.target) {
                XLoader.show();
            }
        }
    });

    document.addEventListener('submit', function (e) {
        if (window.XLoaderOverride) return;
        const target = e.target;
        if (!target.classList.contains('no-loader') && !target.classList.contains('ajax-form')) {
            XLoader.show();
        }
    });
</script>
