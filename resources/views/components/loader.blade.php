<!-- Loader Component - X-Loader Style -->

<style>
    /* X-Loader Animation Styles */
    .x-loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .x-loader-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .x-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    
    .x-loader-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #7367f0;
        border-radius: 50%;
        animation: x-spin 1s linear infinite;
    }
    
    .x-loader-spinner.multicolor {
        border-top: 4px solid #7367f0;
        border-right: 4px solid #ff9f43;
        border-bottom: 4px solid #28c76f;
        border-left: 4px solid #ea5455;
    }
    
    .x-loader-text {
        color: #6e4b9b;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 1px;
    }
    
    .x-loader-dots {
        display: flex;
        gap: 8px;
    }
    
    .x-loader-dots span {
        width: 10px;
        height: 10px;
        background: #7367f0;
        border-radius: 50%;
        animation: x-bounce 1.4s infinite ease-in-out both;
    }
    
    .x-loader-dots span:nth-child(1) { animation-delay: -0.32s; }
    .x-loader-dots span:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes x-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes x-bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    
    /* Small Loader */
    .x-loader-sm .x-loader-spinner {
        width: 30px;
        height: 30px;
        border-width: 3px;
    }
    
    /* Large Loader */
    .x-loader-lg .x-loader-spinner {
        width: 60px;
        height: 60px;
        border-width: 5px;
    }
</style>

<!-- Full Page Loader -->
<div id="x-page-loader" class="x-loader-overlay">
    <div class="x-loader">
        <div class="x-loader-spinner multicolor"></div>
        <div class="x-loader-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="x-loader-text">LOADING...</div>
    </div>
</div>

<!-- Inline Loader (for smaller areas) -->
<div id="x-inline-loader" class="x-loader-overlay" style="position: relative; background: transparent;">
    <div class="x-loader x-loader-sm">
        <div class="x-loader-spinner"></div>
    </div>
</div>

<script>
    // X-Loader JavaScript Functions
    const XLoader = {
        shown: false,
        
        // Show full page loader
        show: function() {
            if (this.shown) return;
            this.shown = true;
            
            const loader = document.getElementById('x-page-loader');
            if (loader) {
                loader.classList.add('active');
            }
        },
        
        // Hide full page loader
        hide: function() {
            this.shown = false;
            
            const loader = document.getElementById('x-page-loader');
            if (loader) {
                loader.classList.remove('active');
            }
        },
        
        // Show inline loader
        showInline: function(elementId) {
            const loader = document.getElementById('x-inline-loader');
            const target = document.getElementById(elementId);
            if (loader && target) {
                loader.style.position = 'absolute';
                loader.style.top = '0';
                loader.style.left = '0';
                loader.style.width = '100%';
                loader.style.height = '100%';
                loader.style.background = 'rgba(255, 255, 255, 0.8)';
                loader.classList.add('active');
                target.style.position = 'relative';
                target.appendChild(loader);
            }
        },
        
        // Hide inline loader
        hideInline: function() {
            const loader = document.getElementById('x-inline-loader');
            if (loader) {
                loader.classList.remove('active');
            }
        }
    };
    
    // Show loader on page refresh/navigation (beforeunload)
    window.addEventListener('beforeunload', function() {
        XLoader.show();
    });
    
    // Hide loader when page fully loads
    window.addEventListener('load', function() {
        setTimeout(function() {
            XLoader.hide();
        }, 500);
    });
    
    // Show loader on link click (for navigation) - but not if it's a data request
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target) {
            const href = target.getAttribute('href');
            // Only trigger for actual page navigation, not AJAX
            if (href && !href.startsWith('#') && !href.startsWith('javascript') && !target.classList.contains('no-loader') && !target.target) {
                XLoader.show();
            }
        }
    });
    
    // Show loader on form submit (but not AJAX forms)
    document.addEventListener('submit', function(e) {
        const target = e.target;
        // Only for non-AJAX forms
        if (!target.classList.contains('no-loader') && !target.classList.contains('ajax-form')) {
            XLoader.show();
        }
    });
</script>
