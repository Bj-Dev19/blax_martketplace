/**
 * Blax chrome behaviour: theme switching plus the early delegated click/keydown
 * handlers for the header chrome (theme toggle, notification panels, profile
 * dropdowns, mobile drawers and sidebars) on every surface.
 *
 * This used to be ~240 lines of inline script inside app/Views/components/head.php.
 * Extracting it means it ships once and is cached across navigations instead of being
 * re-sent inside every page's HTML.
 *
 * Loaded WITHOUT defer, exactly where the inline block used to sit, so execution order
 * is unchanged: it still runs before the body is parsed, which is what keeps the
 * theme class applied before first paint (no FOUC) and the listeners registered before
 * the first click can land.
 *
 * Reads two globals that head.php sets just before this file loads:
 *   window.BLAX_DISABLE_DARK_THEME  bool  - routes that pin themselves to light mode
 */
(function () {
    'use strict';

    // Routes that opt out of dark mode entirely get inert no-ops and nothing else.
    // Matches the previous inline block exactly, which bailed out with `return` before
    // registering any listeners - those pages wire their own chrome elsewhere.
    if (window.BLAX_DISABLE_DARK_THEME) {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
        window.setBlaxTheme = function () {};
        window.toggleBlaxTheme = function () {};
        window.updateThemeToggleIcons = function () {};
        return;
    }

    window.updateThemeToggleIcons = function () {
        const isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('.theme-toggle-btn').forEach(function (btn) {
            const icon = btn.querySelector('.theme-toggle-icon, .material-symbols-outlined');
            const text = btn.querySelector('.theme-toggle-text, .theme-mode-label');
            if (icon) {
                icon.textContent = isDark ? 'light_mode' : 'dark_mode';
            }
            if (text) {
                text.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            }
            btn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
            btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
        });
    };

    window.setBlaxTheme = function (theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            try { localStorage.setItem('blax_theme', 'dark'); } catch (e) {}
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            try { localStorage.setItem('blax_theme', 'light'); } catch (e) {}
        }
        window.updateThemeToggleIcons();
        window.dispatchEvent(new CustomEvent('blax:theme-changed', { detail: { theme: theme } }));
    };

    window.toggleBlaxTheme = function () {
        const isDark = document.documentElement.classList.contains('dark');
        window.setBlaxTheme(isDark ? 'light' : 'dark');
    };

    // Early click listener for immediate responsiveness (Theme, Notifications, Profile)
    function wireChrome() {
        document.addEventListener('click', function (e) {
            // 1. Theme toggle
            const themeBtn = e.target.closest('.theme-toggle-btn');
            if (themeBtn) {
                e.preventDefault();
                e.stopPropagation();
                window.toggleBlaxTheme();
                return;
            }

            // 2. Notification toggle
            const notifBtn = e.target.closest('#notif-dropdown-toggle, #notif-toggle, #admin-notif-toggle');
            if (notifBtn) {
                e.preventDefault();
                e.stopPropagation();
                const panel = (notifBtn.parentElement ? notifBtn.parentElement.querySelector('#notif-dropdown, #notif-panel, #admin-notif-panel') : null)
                    || document.getElementById('notif-panel')
                    || document.getElementById('admin-notif-panel')
                    || document.getElementById('notif-dropdown');
                if (panel) {
                    const isHidden = panel.classList.contains('hidden');
                    document.querySelectorAll('#profile-dropdown, #tenant-profile-dropdown').forEach(p => p.classList.remove('open'));
                    document.querySelectorAll('#admin-profile-quickmenu').forEach(p => p.classList.add('hidden'));
                    if (isHidden) {
                        panel.classList.remove('hidden');
                        notifBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        panel.classList.add('hidden');
                        notifBtn.setAttribute('aria-expanded', 'false');
                    }
                }
                return;
            }

            // 3. Profile dropdown toggle
            const profileBtn = e.target.closest('#profile-dropdown-toggle, #tenant-profile-toggle, #admin-profile-toggle');
            if (profileBtn) {
                e.preventDefault();
                e.stopPropagation();
                const custDropdown = document.getElementById('profile-dropdown');
                const tenantDropdown = document.getElementById('tenant-profile-dropdown');
                const adminMenu = document.getElementById('admin-profile-quickmenu');

                document.querySelectorAll('#notif-dropdown, #notif-panel, #admin-notif-panel').forEach(p => p.classList.add('hidden'));

                if ((profileBtn.id === 'profile-dropdown-toggle' || profileBtn.matches('#profile-dropdown-toggle')) && custDropdown) {
                    const isOpen = custDropdown.classList.contains('open');
                    custDropdown.classList.toggle('open', !isOpen);
                    profileBtn.setAttribute('aria-expanded', (!isOpen).toString());
                } else if ((profileBtn.id === 'tenant-profile-toggle' || profileBtn.matches('#tenant-profile-toggle')) && tenantDropdown) {
                    const isOpen = tenantDropdown.classList.contains('open');
                    tenantDropdown.classList.toggle('open', !isOpen);
                    profileBtn.setAttribute('aria-expanded', (!isOpen).toString());
                } else if ((profileBtn.id === 'admin-profile-toggle' || profileBtn.matches('#admin-profile-toggle')) && adminMenu) {
                    const isHidden = adminMenu.classList.contains('hidden');
                    adminMenu.classList.toggle('hidden', !isHidden);
                    profileBtn.setAttribute('aria-expanded', isHidden.toString());
                }
                return;
            }

            // 4. Mobile Menu / Drawer / Sidebar Toggles (Customer, Tenant, Admin)
            const custMenuBtn = e.target.closest('#mobile-menu-toggle');
            if (custMenuBtn) {
                e.preventDefault();
                e.stopPropagation();
                openElement('mobile-drawer', 'open');
                openElement('mobile-drawer-backdrop', 'active');
                document.body.classList.add('mobile-nav-open');
                return;
            }
            const custCloseBtn = e.target.closest('#mobile-menu-close, #mobile-drawer-backdrop');
            if (custCloseBtn) {
                e.preventDefault();
                e.stopPropagation();
                closeElement('mobile-drawer', 'open');
                closeElement('mobile-drawer-backdrop', 'active');
                document.body.classList.remove('mobile-nav-open');
                return;
            }

            const tenantMenuBtn = e.target.closest('#sidebar-toggle, #tenant-sidebar-toggle');
            if (tenantMenuBtn) {
                e.preventDefault();
                e.stopPropagation();
                const sidebar = document.getElementById('tenant-sidebar');
                const overlay = document.getElementById('tenant-sidebar-overlay');
                if (sidebar && overlay) {
                    sidebar.classList.add('mobile-open');
                    sidebar.classList.remove('hidden');
                    sidebar.classList.add('flex', 'z-50');
                    overlay.classList.add('active');
                    document.body.classList.add('mobile-nav-open');
                }
                return;
            }
            const tenantCloseBtn = e.target.closest('#tenant-sidebar-close, #tenant-sidebar-overlay');
            if (tenantCloseBtn) {
                e.preventDefault();
                e.stopPropagation();
                const sidebar = document.getElementById('tenant-sidebar');
                const overlay = document.getElementById('tenant-sidebar-overlay');
                if (sidebar && overlay) {
                    sidebar.classList.remove('mobile-open');
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('flex', 'z-50');
                    overlay.classList.remove('active');
                    document.body.classList.remove('mobile-nav-open');
                }
                return;
            }

            const adminMenuBtn = e.target.closest('#admin-sidebar-toggle');
            if (adminMenuBtn) {
                e.preventDefault();
                e.stopPropagation();
                const sidebar = document.getElementById('admin-sidebar');
                const overlay = document.getElementById('admin-sidebar-overlay');
                if (sidebar && overlay) {
                    sidebar.classList.add('mobile-open');
                    sidebar.classList.remove('hidden');
                    sidebar.classList.add('flex', 'z-50');
                    overlay.classList.add('active');
                    document.body.classList.add('mobile-nav-open');
                }
                return;
            }
            const adminCloseBtn = e.target.closest('#admin-sidebar-close, #admin-sidebar-overlay');
            if (adminCloseBtn) {
                e.preventDefault();
                e.stopPropagation();
                const sidebar = document.getElementById('admin-sidebar');
                const overlay = document.getElementById('admin-sidebar-overlay');
                if (sidebar && overlay) {
                    sidebar.classList.remove('mobile-open');
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('flex', 'z-50');
                    overlay.classList.remove('active');
                    document.body.classList.remove('mobile-nav-open');
                }
                return;
            }

            // 5. Outside clicks dismiss open menus
            if (!e.target.closest('#notif-dropdown, #notif-panel, #admin-notif-panel')) {
                document.querySelectorAll('#notif-dropdown, #notif-panel, #admin-notif-panel').forEach(p => p.classList.add('hidden'));
            }
            if (!e.target.closest('#profile-dropdown, #tenant-profile-dropdown, #admin-profile-quickmenu')) {
                document.querySelectorAll('#profile-dropdown, #tenant-profile-dropdown').forEach(p => p.classList.remove('open'));
                document.querySelectorAll('#admin-profile-quickmenu').forEach(p => p.classList.add('hidden'));
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            document.querySelectorAll('#notif-dropdown, #notif-panel, #admin-notif-panel, #admin-profile-quickmenu').forEach(p => p.classList.add('hidden'));
            document.querySelectorAll('#profile-dropdown, #tenant-profile-dropdown').forEach(p => p.classList.remove('open'));
            closeElement('mobile-drawer', 'open');
            closeElement('mobile-drawer-backdrop', 'active');
            const tenantSidebar = document.getElementById('tenant-sidebar');
            const tenantOverlay = document.getElementById('tenant-sidebar-overlay');
            if (tenantSidebar && tenantOverlay) { tenantSidebar.classList.remove('mobile-open'); tenantOverlay.classList.remove('active'); }
            const adminSidebar = document.getElementById('admin-sidebar');
            const adminOverlay = document.getElementById('admin-sidebar-overlay');
            if (adminSidebar && adminOverlay) { adminSidebar.classList.remove('mobile-open'); adminOverlay.classList.remove('active'); }
            document.body.classList.remove('mobile-nav-open');
        });
    }

    function openElement(id, cls) {
        const el = document.getElementById(id);
        if (el) el.classList.add(cls);
    }

    function closeElement(id, cls) {
        const el = document.getElementById(id);
        if (el) el.classList.remove(cls);
    }

    // Resolve the stored theme before first paint. Kept last so the toggle helpers
    // above already exist when setBlaxTheme() dispatches its change event later.
    if (!window.BLAX_DISABLE_DARK_THEME) {
        try {
            const savedTheme = localStorage.getItem('blax_theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {}
    }

    wireChrome();
})();
