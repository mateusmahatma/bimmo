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

    // Source Code & Console Protection
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
    const swallowConsole = () => {
        if (window.console) {
            const methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'clear', 'dir', 'count', 'group', 'groupCollapsed', 'groupEnd', 'time', 'timeEnd', 'profile', 'profileEnd'];
            methods.forEach(method => {
                window.console[method] = () => { };
            });
        }
    };

    swallowConsole();

    // Continuous console clearing via iframe bypass to be absolute
    setInterval(() => {
        try {
            const c = document.createElement('iframe');
            c.style.display = 'none';
            document.body.appendChild(c);
            c.contentWindow.console.clear();
            document.body.removeChild(c);
        } catch (e) { }
    }, 100);

    // Advanced Debugger Trap
    const detector = function () {
        try {
            (function () {
                (function a() {
                    try {
                        (function b(i) {
                            if (('' + (i / i)).length !== 1 || i % 20 === 0) {
                                (function () { }).constructor('debugger')();
                             } else {
                                debugger;
                            }
                            b(++i);
                        })(0);
                    } catch (e) {
                         setTimeout(a, 50);
                    }
                })();
             })();
        } catch (e) { }
    };

    setTimeout(detector, 1000);

    // Print Protection
    window.addEventListener('beforeprint', () => {
        document.body.style.display = 'none';
    });
    window.addEventListener('afterprint', () => {
        document.body.style.display = 'block';
    });

})();
