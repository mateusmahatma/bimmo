@php
    $type = session('success') ? 'success' : (session('error') || $errors->any() ? 'error' : null);
    $message = session('success') ?? session('error') ?? ($errors->any() ? implode(', ', $errors->all()) : null);
    $icon = $type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    $title = $type === 'success' ? 'Berhasil' : 'Gagal';
@endphp

@if($type)
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div id="liveToast" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" 
         style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 12px; min-width: 320px;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center p-3">
                <div class="toast-icon-wrapper d-flex align-items-center justify-content-center me-3" 
                     style="width: 40px; height: 40px; background: {{ $type === 'success' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $type === 'success' ? '#10b981' : '#ef4444' }}; border-radius: 10px; font-size: 1.25rem;">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold" style="color: #1f2937; font-size: 0.95rem;">{{ $title }}</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.4;">{{ $message }}</p>
                </div>
            </div>
            <button type="button" class="btn-close ms-auto me-2 mt-3" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    #liveToast {
        animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    @keyframes toastSlideIn {
        from { transform: translateY(-100%) translateX(0); opacity: 0; }
        to { transform: translateY(0) translateX(0); opacity: 1; }
    }

    [data-bs-theme="dark"] #liveToast {
        background: rgba(31, 41, 55, 0.9) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    [data-bs-theme="dark"] .toast-icon-wrapper {
        background: {{ $type === 'success' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }} !important;
    }
    
    [data-bs-theme="dark"] h6 {
        color: #f3f4f6 !important;
    }
    
    [data-bs-theme="dark"] .text-muted {
        color: #9ca3af !important;
    }
    
    [data-bs-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastElement = document.getElementById('liveToast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement, { 
                delay: 4000,
                autohide: true
            });
            toast.show();
        }
    });
</script>
@endif
