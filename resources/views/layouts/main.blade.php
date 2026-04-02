@extends('layouts.app')

@section('body')

<div class="container-fluid py-3">
    <div class="row">
        {{-- DESKTOP SIDEBAR --}}
        <aside class="col-md-2 bg-light border-end d-none d-md-block">
            @include('layouts.sidebar', ['prefix' => 'desktop_'])
        </aside>

        {{-- MOBILE OFFCANVAS SIDEBAR --}}
        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                @include('layouts.sidebar', ['prefix' => 'mobile_'])
            </div>
        </div>

        <main class="col-md-10 ms-sm-auto px-md-4 position-relative" style="min-height: 100vh; border-radius: 0 !important;">
            <!-- Loading Overlay (YouTube Style) -->
            <div id="top-progress-bar"></div>

            {{-- MOBILE TOP APP BAR --}}
            <div class="d-md-none py-2 mb-2 border-bottom d-flex align-items-center justify-content-center bg-white shadow-sm" style="position: sticky; top: 0; z-index: 1020;">
                <img src="{{ asset('img/bimmo_light.png') }}" class="sidebar-logo" alt="BIMMO" style="height: 25px;">
            </div>



            <div id="spa-container">
                @yield('container')
            </div>
        </main>
    </div>
</div>

{{-- BOTTOM NAVIGATION FOR MOBILE --}}
@include('layouts.bottom-nav')

<style>
    @media (max-width: 767.98px) {
        main {
            padding-bottom: 90px !important; /* Space for bottom nav */
        }
    }
</style>

@if(auth()->check())
    <script>
        function updateLanguage(lang) {
            fetch("{{ route('user.update.language') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ language: lang })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                } else {
                    alert('Gagal mengubah bahasa.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan.');
            });
        }
    </script>
    <!-- YouTube Style Top Progress Bar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.getElementById('top-progress-bar');
            let progressInterval;

            function startTopLoad() {
                if (!progressBar) return;
                clearInterval(progressInterval);
                progressBar.style.width = '0%';
                progressBar.style.opacity = '1';
                progressBar.style.display = 'block';
                
                let width = 0;
                progressInterval = setInterval(() => {
                    if (width >= 90) {
                        clearInterval(progressInterval);
                    } else {
                        width += Math.random() * 5;
                        progressBar.style.width = width + '%';
                    }
                }, 200);
            }

            function endTopLoad() {
                if (!progressBar) return;
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.style.opacity = '0';
                    setTimeout(() => {
                        progressBar.style.width = '0%';
                        progressBar.style.display = 'none';
                    }, 300);
                }, 200);
            }

            // Global SPA & Page Load handling
            async function fetchAndReplaceSPA(url, pushHistory = true) {
                startTopLoad();
                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-SPA-Navigation': 'true',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        if (response.status === 401 || response.status === 419) {
                            window.location.href = '/login';
                            return;
                        }
                        window.location.href = url;
                        return;
                    }

                    const html = await response.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    document.title = doc.title;
                    const currentContainer = document.getElementById('spa-container');
                    const newContainer = doc.getElementById('spa-container');
                    if (currentContainer && newContainer) {
                        currentContainer.innerHTML = newContainer.innerHTML;
                    } else {
                        window.location.href = url;
                        return;
                    }

                    // Update sidebars
                    ['.sidebar', '#mobileSidebar .offcanvas-body'].forEach(selector => {
                        const el = document.querySelector(selector);
                        const newEl = doc.querySelector(selector);
                        if (el && newEl) el.innerHTML = newEl.innerHTML;
                    });

                    // 5. Scripts (load scripts from the new page)
                    const newScripts = doc.querySelectorAll('script');
                    const scriptPromises = [];
                    
                    newScripts.forEach(oldScript => {
                        if (oldScript.innerHTML.includes('Extreme Source Code Protection') || 
                            oldScript.innerHTML.includes('fetchAndReplaceSPA') ||
                            oldScript.innerHTML.includes('serviceWorker')) {
                            return; 
                        }
                        
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        
                        if (oldScript.src) {
                            // Page-specific scripts often have ?v= or are in the js/ folder
                            // We force re-execution by removing the old one and adding a new one with a timestamp
                            const isPageScript = oldScript.src.includes('?v=') || oldScript.src.includes('/js/');
                            const existingScript = document.querySelector(`script[src^="${oldScript.src.split('?')[0]}"]`);
                            
                            if (isPageScript || !existingScript) {
                                if (existingScript) existingScript.remove();
                                
                                const p = new Promise((resolve) => {
                                    newScript.onload = resolve;
                                    newScript.onerror = resolve;
                                });
                                scriptPromises.push(p);
                                
                                // Add timestamp to force re-execution if it's a page script
                                if (isPageScript) {
                                    const connector = oldScript.src.includes('?') ? '&' : '?';
                                    newScript.src = oldScript.src + connector + 'spa_ts=' + Date.now();
                                }
                                
                                document.body.appendChild(newScript);
                            }
                        } else {
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            document.body.appendChild(newScript);
                            // Brief delay before cleanup for inline execution
                            setTimeout(() => { if(newScript.parentNode) newScript.parentNode.removeChild(newScript); }, 60);
                        }
                    });

                    // 6. Sync head styles (for visual consistency like after reload)
                    const currentHeadLinks = Array.from(document.querySelectorAll('head link[rel="stylesheet"]')).map(l => l.href.split('?')[0]);
                    const newHeadStyles = doc.querySelectorAll('head link[rel="stylesheet"], head style');
                    newHeadStyles.forEach(tag => {
                        if (tag.tagName === 'LINK') {
                            const baseUrl = tag.href.split('?')[0];
                            if (!currentHeadLinks.includes(baseUrl)) {
                                const newLink = document.createElement('link');
                                Array.from(tag.attributes).forEach(attr => newLink.setAttribute(attr.name, attr.value));
                                document.head.appendChild(newLink);
                            }
                        } else if (tag.tagName === 'STYLE') {
                            const newStyle = document.createElement('style');
                            Array.from(tag.attributes).forEach(attr => newStyle.setAttribute(attr.name, attr.value));
                            newStyle.textContent = tag.textContent;
                            document.head.appendChild(newStyle);
                        }
                    });

                    // Wait for all newly added external scripts to load before triggering page-loaded
                    await Promise.all(scriptPromises);

                    if (pushHistory) window.history.pushState({ spaUrl: url }, doc.title, url);
                    window.scrollTo(0, 0);
                    
                    endTopLoad();

                    // Use requestAnimationFrame to ensure the DOM has settled before dispatching the load event
                    requestAnimationFrame(() => {
                        setTimeout(() => {
                            // Cleanup any lingering Bootstrap modal artifacts
                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';

                            document.dispatchEvent(new Event('spa:page-loaded'));
                            // Fallback for some components that specifically need a slightly later trigger
                            window.dispatchEvent(new Event('resize'));
                        }, 50);
                    });

                } catch (err) {
                    console.error('SPA Error:', err);
                    window.location.href = url;
                }
            }

            document.body.addEventListener('click', function(e) {
                if (e.defaultPrevented) return;
                const link = e.target.closest('a');
                if (!link) return;
                const href = link.getAttribute('href');
                const target = link.getAttribute('target');
                
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.includes('mailto:') || href.includes('tel:') || target === '_blank' || link.hasAttribute('data-bs-toggle') || link.classList.contains('no-spa') || href.includes('logout')) {
                    if (!link.hasAttribute('data-bs-toggle') && href && !href.startsWith('#')) {
                        // Regular links that aren't SPA
                        startTopLoad();
                    }
                    return;
                }
                
                try {
                    const url = new URL(href, window.location.origin);
                    const isSameOrigin = url.origin === window.location.origin;
                    const isSameHost = url.hostname === window.location.hostname;
                    
                    // If it's the same host (even if port/protocol was weirdly different in the link attributes)
                    if (isSameOrigin || isSameHost) {
                        e.preventDefault();
                        fetchAndReplaceSPA(url.href, true);
                        
                        const offcanvasEl = document.getElementById('mobileSidebar');
                        if (offcanvasEl && offcanvasEl.classList.contains('show')) {
                            bootstrap.Offcanvas.getInstance(offcanvasEl)?.hide();
                        }
                    }
                } catch (err) {}
            });

            window.addEventListener('popstate', (e) => {
                if (e.state?.spaUrl) fetchAndReplaceSPA(location.href, false);
                else window.location.reload();
            });

            document.body.addEventListener('submit', (e) => {
                if (!e.defaultPrevented && e.target.getAttribute('target') !== '_blank') startTopLoad();
            });

            window.addEventListener('pageshow', (e) => { if (e.persisted) endTopLoad(); });
            
            // Initial end load in case script loads after page partially ready
            endTopLoad();
        });
    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
@endif
@endsection