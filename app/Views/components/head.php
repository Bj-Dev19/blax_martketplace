<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<meta name="csrf-header" content="<?= csrf_header() ?>">

<title><?= esc($title ?? 'Blax') ?></title>

<!-- Brand / Favicon -->
<link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32.png') ?>">
<link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>">
<link rel="manifest" href="<?= base_url('site.webmanifest') ?>">
<meta name="theme-color" content="#1B2A1E">

<?php
    $disableDarkTheme = $disableDarkTheme ?? false;
    $chromePath       = FCPATH . 'js/blax-chrome.js';
    $chromeVer        = is_file($chromePath) ? filemtime($chromePath) : '2.1.0';
?>
<!-- Theme switcher + header chrome handlers (prevents FOUC, wires drawers/dropdowns). -->
<!-- The flag is inlined because it is per-route; the behaviour itself lives in a cached -->
<!-- file so it stops riding in every page's HTML. Deliberately NOT deferred: it has to -->
<!-- apply the theme class and register its listeners before the body finishes parsing. -->
<script>window.BLAX_DISABLE_DARK_THEME = <?= $disableDarkTheme ? 'true' : 'false' ?>;</script>
<script src="<?= base_url('js/blax-chrome.js?v=' . $chromeVer) ?>"></script>

<script>
window.BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
(function() {
    window.getCsrfToken = function() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };
    window.getCsrfHeader = function() {
        const meta = document.querySelector('meta[name="csrf-header"]');
        return meta ? meta.getAttribute('content') : 'X-CSRF-TOKEN';
    };

    const originalFetch = window.fetch;
    window.fetch = function(input, init) {
        init = init || {};
        const method = (init.method || 'GET').toUpperCase();
        if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
            const token = window.getCsrfToken();
            const header = window.getCsrfHeader();
            if (token) {
                if (!init.headers) {
                    init.headers = {};
                }
                if (init.headers instanceof Headers) {
                    if (!init.headers.has(header)) {
                        init.headers.set(header, token);
                    }
                } else if (Array.isArray(init.headers)) {
                    if (!init.headers.some(([k]) => k.toLowerCase() === header.toLowerCase())) {
                        init.headers.push([header, token]);
                    }
                } else {
                    if (!init.headers[header]) {
                        init.headers[header] = token;
                    }
                }
            }
        }
        return originalFetch.call(this, input, init);
    };

    window.showToast = function(message, type = 'success', duration = 3500) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-16 sm:top-5 right-0 sm:right-5 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm w-full px-3 sm:px-4 transition-all';
            // Announce toasts to assistive tech; errors escalate to role="alert" below.
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'false');
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-surface-container-lowest text-on-surface border border-outline-variant/30 shadow-xl rounded-2xl p-3 flex items-center gap-3 transform translate-y-[-10px] opacity-0 transition-all duration-300 ease-out';
        
        let iconName = 'check_circle';
        let iconColor = 'text-green-600';
        if (type === 'error') {
            iconName = 'error';
            iconColor = 'text-red-600';
        } else if (type === 'warning') {
            iconName = 'warning';
            iconColor = 'text-amber-600';
        } else if (type === 'info') {
            iconName = 'info';
            iconColor = 'text-blue-600';
        }

        toast.innerHTML = `
            <span class="material-symbols-outlined shrink-0 ${iconColor} text-xl">${iconName}</span>
            <div class="blax-toast-message flex-1 text-xs font-semibold leading-tight"></div>
            <button type="button" class="shrink-0 p-1 text-outline hover:text-on-surface rounded-full transition-colors" aria-label="Close notification">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        `;

        // The message is assigned with textContent, never interpolated into the
        // HTML string above. Callers pass server-supplied text (data.message from
        // JSON responses, controller flash messages), so string-concatenating it
        // here was a stored-XSS sink executing in the merchant/admin session.
        const messageEl = toast.querySelector('.blax-toast-message');
        if (messageEl) {
            messageEl.textContent = (message === null || message === undefined) ? '' : String(message);
        }

        if (type === 'error') {
            toast.setAttribute('role', 'alert');
        }

        const closeBtn = toast.querySelector('button');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => dismissToast(toast));
        }

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-[-10px]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        const timer = setTimeout(() => dismissToast(toast), duration);

        function dismissToast(el) {
            clearTimeout(timer);
            el.classList.remove('translate-y-0', 'opacity-100');
            el.classList.add('translate-y-[-10px]', 'opacity-0');
            setTimeout(() => {
                if (el.parentNode) el.parentNode.removeChild(el);
            }, 300);
        }
    };
})();
</script>

<!-- Preconnect & DNS-Prefetch for Google Fonts to accelerate typography paint -->
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php
    $cssPath = FCPATH . 'css/tailwind.css';
    $cssVer = is_file($cssPath) ? filemtime($cssPath) : '2.1.0';
    $jsPath = FCPATH . 'js/blax-ui.js';
    $jsVer = is_file($jsPath) ? filemtime($jsPath) : '2.1.0';
?>
<link rel="stylesheet" href="<?= base_url('css/tailwind.css?v=' . $cssVer) ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script src="<?= base_url('js/blax-ui.js?v=' . $jsVer) ?>" defer></script>

<style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; scroll-behavior: smooth; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .fill-icon { font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
        .glass-panel { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(229, 231, 235, 0.5); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(229, 231, 235, 0.5); }
        .glassmorphism { backdrop-filter: blur(16px); background-color: rgba(255, 255, 255, 0.8); border: 1px solid rgba(255, 255, 255, 0.3); }
        .card-elevated { background-color: #ffffff; border: 1px solid rgba(226, 232, 240, 0.9); box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04); }
        .card-elevated:hover { border-color: rgba(37, 99, 235, 0.4); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08); }
        .bg-pattern { background-color: #f7f9fb; background-image: radial-gradient(at 15% 20%, rgba(37,99,235,0.08) 0px, transparent 50%), radial-gradient(at 85% 15%, rgba(0,116,166,0.06) 0px, transparent 50%), radial-gradient(at 70% 90%, rgba(0,74,198,0.07) 0px, transparent 50%); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(115, 118, 134, 0.3); border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .metric-card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .soft-shadow { box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05); }
        .active-nav-link { background-color: #d5e0f8; color: #586377; font-weight: 600; transform: translateX(4px); }
        .badge { background-color: #004ac6; color: #ffffff; font-size: 11px; line-height: 1; padding: 3px 6px; border-radius: 9999px; font-weight: 600; }
        .sidebar-item:hover { background-color: #e6e8ea; }
        .order-table-row:hover { background-color: #eceef0; }
        .delay-150ms { transition-delay: 150ms; }

        /* Bulletproof Navigation & Profile Dropdown Positioning */
        #profile-dropdown,
        #tenant-profile-dropdown,
        #admin-profile-quickmenu {
            position: absolute !important;
            top: calc(100% + 0.5rem) !important;
            right: 0 !important;
            left: auto !important;
            min-width: 240px !important;
            width: max-content !important;
            max-width: calc(100vw - 1.5rem) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.15) !important;
            transform-origin: top right !important;
            z-index: 9999 !important;
            white-space: nowrap !important;
        }
        /* Mobile: fix notification panels within screen bounds so they never clip horizontally */
        @media (max-width: 639.98px) {
            #notif-panel,
            #admin-notif-panel,
            #notif-dropdown {
                position: fixed !important;
                top: 4.25rem !important;
                left: 0.75rem !important;
                right: 0.75rem !important;
                width: auto !important;
                max-width: 24rem !important;
                margin-left: auto !important;
                margin-right: auto !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2) !important;
                transform-origin: top center !important;
                z-index: 9999 !important;
            }
        }

        /* Desktop / Tablet: position relative to notification bell button */
        @media (min-width: 640px) {
            #notif-panel,
            #admin-notif-panel,
            #notif-dropdown {
                position: absolute !important;
                top: calc(100% + 0.5rem) !important;
                right: 0 !important;
                left: auto !important;
                width: 20rem !important;
                max-width: calc(100vw - 1.5rem) !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.15) !important;
                transform-origin: top right !important;
                z-index: 9999 !important;
            }
        }
        #profile-dropdown a,
        #profile-dropdown button,
        #tenant-profile-dropdown a,
        #tenant-profile-dropdown button,
        #admin-profile-quickmenu a,
        #admin-profile-quickmenu button {
            white-space: nowrap !important;
        }
        
        /* Dark Mode — Inline Critical Overrides (prevents FOUC) */
        html.dark { color-scheme: dark; }
        html.dark body { background-color: #0b0f19 !important; color: #e2e8f0 !important; }
        html.dark .glass-panel, html.dark .glass-card, html.dark .glassmorphism { background-color: rgba(21, 31, 50, 0.85) !important; border-color: rgba(51, 65, 85, 0.5) !important; }
        html.dark .card-elevated { background-color: #151f32 !important; border-color: rgba(51, 65, 85, 0.6) !important; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.5) !important; }
        html.dark .card-elevated:hover { border-color: rgba(59, 130, 246, 0.5) !important; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.6) !important; }
        html.dark .bg-pattern { background-color: #0b0f19 !important; background-image: radial-gradient(at 15% 20%, rgba(59,130,246,0.12) 0px, transparent 50%), radial-gradient(at 85% 15%, rgba(14,165,233,0.10) 0px, transparent 50%), radial-gradient(at 70% 90%, rgba(37,99,235,0.10) 0px, transparent 50%) !important; }
        
        /* Semantic Surface Backgrounds */
        html.dark .bg-background                { background-color: #0b0f19 !important; }
        html.dark .bg-surface                   { background-color: #111827 !important; }
        html.dark .bg-surface-container-lowest  { background-color: #151f32 !important; }
        html.dark .bg-surface-container-low     { background-color: #1e293b !important; }
        html.dark .bg-surface-container         { background-color: #222f46 !important; }
        html.dark .bg-surface-container-high    { background-color: #293854 !important; }
        html.dark .bg-surface-container-highest { background-color: #324466 !important; }
        html.dark .bg-surface-variant           { background-color: #222f46 !important; }
        html.dark .bg-surface-dim               { background-color: #0f172a !important; }
        html.dark .bg-surface-bright            { background-color: #1e293b !important; }

        /* Opacity variants for semantic surfaces */
        html.dark .bg-surface-container-lowest\/95 { background-color: rgba(21, 31, 50, 0.95) !important; }
        html.dark .bg-surface-container-lowest\/90 { background-color: rgba(21, 31, 50, 0.90) !important; }
        html.dark .bg-surface-container-lowest\/80 { background-color: rgba(21, 31, 50, 0.80) !important; }
        html.dark .bg-surface-container-low\/90    { background-color: rgba(30, 41, 59, 0.90) !important; }
        html.dark .bg-surface-container-low\/80    { background-color: rgba(30, 41, 59, 0.80) !important; }
        html.dark .bg-surface-container-low\/70    { background-color: rgba(30, 41, 59, 0.70) !important; }
        html.dark .bg-surface-container-low\/60    { background-color: rgba(30, 41, 59, 0.60) !important; }
        html.dark .bg-surface-container-low\/50    { background-color: rgba(30, 41, 59, 0.50) !important; }
        html.dark .bg-surface-container-low\/40    { background-color: rgba(30, 41, 59, 0.40) !important; }

        /* Semantic Text Colors */
        html.dark .text-on-surface             { color: #f1f5f9 !important; }
        html.dark .text-on-surface-variant     { color: #94a3b8 !important; }
        html.dark .text-outline                { color: #64748b !important; }
        html.dark .text-outline-variant        { color: #475569 !important; }
        html.dark .text-label-sm               { color: #94a3b8 !important; }
        html.dark .text-on-surface-variant\/70 { color: rgba(148, 163, 184, 0.7) !important; }
        html.dark .text-on-surface-variant\/60 { color: rgba(148, 163, 184, 0.6) !important; }

        /* Semantic Borders & Dividers */
        html.dark .border-outline-variant,
        html.dark .border-outline-variant\/10,
        html.dark .border-outline-variant\/15,
        html.dark .border-outline-variant\/20,
        html.dark .border-outline-variant\/25,
        html.dark .border-outline-variant\/30,
        html.dark .border-outline-variant\/40,
        html.dark .border-outline-variant\/50,
        html.dark .border-outline-variant\/60 {
            border-color: rgba(51, 65, 85, 0.6) !important;
        }
        html.dark .border-surface-container-lowest { border-color: #151f32 !important; }
        html.dark .divide-outline-variant\/10 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-outline-variant\/15 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-outline-variant\/20 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-outline-variant\/30 > :not([hidden]) ~ :not([hidden]) {
            border-color: rgba(51, 65, 85, 0.45) !important;
        }

        /* Critical text remaps to prevent dark-on-dark flash */
        html.dark .text-slate-900 { color: #f1f5f9 !important; }
        html.dark .text-slate-800 { color: #e2e8f0 !important; }
        html.dark .text-slate-700 { color: #cbd5e1 !important; }
        html.dark .text-slate-600, html.dark .text-slate-500 { color: #94a3b8 !important; }
        html.dark .text-gray-900 { color: #f9fafb !important; }
        html.dark .text-gray-800 { color: #e5e7eb !important; }
        html.dark .text-gray-700 { color: #d1d5db !important; }

        /* Critical bg remaps */
        html.dark .bg-white { background-color: #151f32 !important; }
        html.dark .bg-slate-50 { background-color: #111827 !important; }
        html.dark .bg-slate-100 { background-color: #1e293b !important; }
        html.dark .bg-white\/90 { background-color: rgba(21,31,50,0.90) !important; }
        html.dark .bg-white\/95 { background-color: rgba(21,31,50,0.95) !important; }

        /* Critical border remaps */
        html.dark .border-slate-200, html.dark .border-slate-200\/80, html.dark .border-slate-200\/90 { border-color: rgba(51,65,85,0.5) !important; }
        html.dark .border-slate-100 { border-color: rgba(51,65,85,0.3) !important; }

        /* Gradient remaps */
        html.dark .from-white { --tw-gradient-from: #151f32 !important; }
        html.dark .via-white, html.dark .via-white\/90, html.dark .via-white\/95 { --tw-gradient-via: #151f32 !important; }

        /* Form Controls */
        html.dark select, html.dark input:not([type="checkbox"]):not([type="radio"]), html.dark textarea {
            background-color: #151f32 !important;
            color: #f1f5f9 !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
        }

        /* Responsive utilities */
        .mobile-nav-open { overflow: hidden; }
        .drawer-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 45; opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
        .drawer-backdrop.active { opacity: 1; pointer-events: auto; }
        .mobile-drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 300px; max-width: 85vw; background: #ffffff; z-index: 50; transform: translateX(100%); transition: transform 0.3s ease; box-shadow: -4px 0 24px rgba(0,0,0,0.12); overflow-y: auto; }
        html.dark .mobile-drawer { background: #111827; border-left: 1px solid rgba(51, 65, 85, 0.5); }
        .mobile-drawer.open { transform: translateX(0); }
        .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 35; opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
        .sidebar-overlay.active { opacity: 1; pointer-events: auto; }
        @media (max-width: 767px) {
            #tenant-sidebar.mobile-open,
            #admin-sidebar.mobile-open {
                display: flex !important;
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                z-index: 50 !important;
                transform: translateX(0) !important;
                box-shadow: 4px 0 24px rgba(0,0,0,0.2) !important;
            }
            .responsive-table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .responsive-table table { min-width: 640px; }
            .responsive-table th, .responsive-table td { padding-left: 12px; padding-right: 12px; white-space: nowrap; }
        }
        .mobile-profile-tabs { display: flex; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none; gap: 4px; padding: 4px; }
        .mobile-profile-tabs::-webkit-scrollbar { display: none; }

        /* Table responsive wrapper - mobile horizontal scroll */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-responsive table { min-width: 640px; }

        /* Responsive max-width container (capped at 1200px and centered) */
        .site-container { width: 100%; max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; }
        @media (min-width: 640px) { .site-container { padding-left: 1.5rem; padding-right: 1.5rem; } }
        @media (min-width: 1024px) { .site-container { padding-left: 2rem; padding-right: 2rem; } }

        /* Dropdown tap helpers */
        .dropdown-menu { opacity: 0; visibility: hidden; pointer-events: none; transition: opacity 0.15s ease, visibility 0.15s ease, transform 0.15s ease; }
        .dropdown-menu.open { opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; }
        @media (hover: hover) and (pointer: fine) {
            .dropdown-toggle:hover + .dropdown-menu,
            .dropdown-menu:hover { opacity: 1; visibility: visible; pointer-events: auto; }
        }
        .profile-dropdown-toggle { display: inline-flex; }

        /* Off-screen render skipping for the long-scroll card grids.
         * content-visibility: auto lets the browser skip layout and painting of cards that are
         * far below the viewport, which is where the catalog and shop grids spend their time.
         * contain-intrinsic-size gives the skipped region a plausible box so the scrollbar does
         * not jump; the "auto" form remembers the real size once it has been rendered and is
         * simply ignored by engines that predate it.
         * Deliberately limited to self-contained card grids, where every absolutely positioned
         * child is anchored to its own relative card, so the containment can never reposition a
         * badge or dropdown. Never put this on a sticky/fixed ancestor. */
        .cv-skip {
            content-visibility: auto;
            contain-intrinsic-size: 600px;
            contain-intrinsic-size: auto 600px;
        }
    </style>