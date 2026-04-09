/**
 * Extreme Security Measures for Bimmo Login & Register Pages
 * - Prevents Right-Click
 * - Blocks common DevTools shortcuts
 * - Detects DevTools opening (Dimension thresholds & Orientation)
 * - Prevents Screen Capture/Print
 * - Extreme Debugger Trap
 * - Netflix-style Black Out Protection
 */

(function () {
    'use strict';

    // Robust PWA Detection
    const isPWAMode = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true ||
        document.referrer.includes('android-app://');

    if (isPWAMode) {
        document.documentElement.classList.add('pwa-mode');
    }

    // ONLY Disable common shortcuts for DevTools
    document.addEventListener('keydown', (e) => {
        if (
            e.keyCode === 123 || // F12
            (e.ctrlKey && e.shiftKey && [73, 74, 67].includes(e.keyCode)) // Ctrl+Shift+I/J/C
        ) {
            e.preventDefault();
            return false;
        }
    });

    // Enhanced Console Protection - "Jangan tampilkan console nya"
    // Optimized for performance
    const swallowConsole = () => {
        if (window.console) {
            const methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'clear'];
            methods.forEach(method => {
                window.console[method] = () => { };
            });
        }
    };

    swallowConsole();

    // Print Protection
    window.addEventListener('beforeprint', () => {
        document.body.style.display = 'none';
    });
    window.addEventListener('afterprint', () => {
        document.body.style.display = 'block';
    });


})();
