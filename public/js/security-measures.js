/**
 * Security Measures for Bimmo Login & Register Pages
 * - Prevents Right-Click
 * - Blocks common DevTools shortcuts
 * - Detects DevTools opening
 * - Prevents Screen Capture/Print
 */

(function () {
    'use strict';

    // 1. Prevent Right-Click
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        return false;
    });

    // 2. Block Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }

        // Ctrl+Shift+I (Inspect)
        // Ctrl+Shift+J (Console)
        // Ctrl+Shift+C (Elements)
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
            e.preventDefault();
            return false;
        }

        // Ctrl+U (View Source)
        if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) {
            e.preventDefault();
            return false;
        }

        // Ctrl+S (Save)
        if (e.ctrlKey && (e.key === 's' || e.key === 'S' || e.keyCode === 83)) {
            e.preventDefault();
            return false;
        }

        // Ctrl+P (Print)
        if (e.ctrlKey && (e.key === 'p' || e.key === 'P' || e.keyCode === 80)) {
            e.preventDefault();
            return false;
        }
    });

    // 3. DevTools Detection via Debugger
    // This will pause the execution if DevTools is open
    setInterval(() => {
        const start = performance.now();
        debugger;
        const end = performance.now();
        if (end - start > 100) {
            // If it takes more than 100ms to execute debugger, DevTools is likely open
            // window.location.href = '/restricted'; // Uncomment to redirect
            console.warn('Akses Dibatasi: Developer Tools terdeteksi.');
        }
    }, 1000);

    // 4. DevTools Detection via Window Dimensions (Desktop)
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    if (!isMobile) {
        const threshold = 160;
        const checkDimensions = () => {
            const widthDiff = window.outerWidth - window.innerWidth;
            const heightDiff = window.outerHeight - window.innerHeight;

            if (widthDiff > threshold || heightDiff > threshold) {
                // If the difference is large, DevTools is likely docked
                document.body.innerHTML = `
                    <div style="display:flex;justify-content:center;align-items:center;height:100vh;flex-direction:column;font-family:sans-serif;text-align:center;padding:20px;background:#f8f9fa;">
                        <h1 style="color:#dc3545;margin-bottom:10px;">Akses Dibatasi</h1>
                        <p style="color:#6c757d;margin-bottom:20px;">Mohon tutup Developer Tools untuk melanjutkan.</p>
                        <button onclick="window.location.reload()" style="padding:10px 20px;background:#0d6efd;color:white;border:none;border-radius:5px;cursor:pointer;font-size:16px;transition:background 0.3s;">
                            Muat Ulang Halaman
                        </button>
                    </div>
                `;
            }
        };

        window.addEventListener('resize', checkDimensions);
        setTimeout(checkDimensions, 1000);
    }

})();
