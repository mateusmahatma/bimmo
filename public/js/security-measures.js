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

    let isLocked = false;
    const threshold = 160;

    // --- 1. UI Protection Helpers ---

    const hideContent = () => {
        document.body.classList.add('protection-active');
        const overlay = document.getElementById('protection-overlay');
        if (overlay) overlay.style.display = 'flex';
    };

    const showContent = () => {
        if (isLocked) return; // Never show content again if locked by capture
        document.body.classList.remove('protection-active');
        const overlay = document.getElementById('protection-overlay');
        if (overlay) overlay.style.display = 'none';
    };

    const clearClipboard = () => {
        try {
            const dummy = document.createElement("input");
            dummy.style.position = 'fixed';
            dummy.style.opacity = '0';
            document.body.appendChild(dummy);
            dummy.value = "KONTEN DILINDUNGI - Bimmo - " + new Date().getTime();
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
        } catch (err) { }
    };

    // --- 2. Input & Shortcut Protection ---

    // Prevent Right-Click
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        return false;
    });

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        // Blurring for Modifier Combinations (Preemptive)
        // Block Ctrl+Shift or Cmd+Shift (Mac)
        const isModCombo = (e.ctrlKey && e.shiftKey) || (e.metaKey && e.shiftKey);

        if (isModCombo) {
            hideContent();
        }

        // F12, Ctrl+Shift+I/J/C, Ctrl+U, Ctrl+S, Ctrl+P, PrintScreen
        if (
            e.keyCode === 123 ||
            (e.ctrlKey && e.shiftKey && [73, 74, 67].includes(e.keyCode)) ||
            (e.ctrlKey && [85, 83, 80].includes(e.keyCode)) ||
            e.key === 'PrintScreen' || e.keyCode === 44
        ) {
            isLocked = true;
            hideContent();
            clearClipboard();
            e.preventDefault();
            return false;
        }
    });

    document.addEventListener('keyup', (e) => {
        // Show content again if only modifier keys were pressed and no capture was triggered
        if (['Meta', 'Shift', 'Control', 'Alt'].includes(e.key)) {
            setTimeout(() => {
                if (!isLocked && document.hasFocus()) {
                    showContent();
                }
            }, 500);
        }

        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            isLocked = true;
            hideContent();
        }

        // Win Key (Win+Shift+S attempt)
        if (e.key === 'Meta' || e.key === 'OS' || e.key === 'Windows') {
            clearClipboard();
        }
    });

    // --- 3. Focus & Visibility Protection ---

    window.addEventListener('blur', hideContent);
    window.addEventListener('focus', showContent);

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            hideContent();
            document.title = 'Bimmo - Protected';
        } else {
            if (!isLocked) {
                showContent();
                document.title = 'Bimmo';
            }
        }
    });

    // --- 4. DevTools Detection (Aggressive) ---

    const blockAccess = () => {
        isLocked = true;
        hideContent();
        // Specific message for DevTools
        const overlay = document.getElementById('protection-overlay');
        if (overlay) {
            const subtitle = overlay.querySelector('p');
            if (subtitle) subtitle.innerText = "Developer Tools terdeteksi. Silakan tutup Developer Tools untuk melanjutkan.";
        }
    };

    const checkDevTools = () => {
        const widthDiff = window.outerWidth - window.innerWidth > threshold;
        const heightDiff = window.outerHeight - window.innerHeight > threshold;

        if (widthDiff || heightDiff) {
            blockAccess();
        }
    };

    window.addEventListener('resize', checkDevTools);
    setInterval(checkDevTools, 1000);

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

    // Continuous console clearing
    setInterval(() => console.clear(), 100);

    // Mobile Multi-finger Touch Detection
    document.addEventListener('touchstart', (e) => {
        if (e.touches.length > 2) { // 3 or more fingers
            hideContent();
            clearClipboard();
            isLocked = true;
        }
    }, { passive: true });

    // Print Protection
    window.addEventListener('beforeprint', () => {
        document.body.style.display = 'none';
    });
    window.addEventListener('afterprint', () => {
        document.body.style.display = 'block';
    });

})();
