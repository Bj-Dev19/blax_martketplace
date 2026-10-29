<?php
    $segMap = [
        'dashboard' => ['dashboard', 'Platform Overview', 'Monitor overall marketplace health'],
        'tenants' => ['tenants', 'Tenants & Merchants', 'Manage all registered merchant shops'],
        'customers' => ['customers', 'Customers', 'Manage registered platform users'],
        'analytics' => ['analytics', 'Platform Analytics', 'Admin revenue & performance insights'],
        'payments' => ['payments', 'Payment Requests', 'GCash merchant payouts — 3% admin fee'],
        'compliance' => ['compliance', 'Compliance & Reports', 'Review and resolve compliance reports'],
        'tracking' => ['tracking', 'Live Dispatch & Tracking', 'Real-time oversight of shop deliveries across Polomolok'],
        'shipping-settings' => ['shipping_settings', 'Shipping Fee Settings', 'Configure distance-based delivery pricing & limits'],
        'audit-log' => ['audit', 'Audit Log', 'Security and activity log'],
        'content' => ['content', 'Content Management', 'Manage customer-facing text and images'],
    ];

    try {
        $seg = service('request')->getUri()->getSegment(2) ?: 'dashboard';
    } catch (\Throwable $e) {
        $seg = 'dashboard';
    }
    $resolved = $segMap[$seg] ?? ['', $seg, ''];
    $activeNav = $activeNav ?? $resolved[0];
    $pageTitle = $title ?? $resolved[1];
    $pageSubtitle = $subtitle ?? $resolved[2];
    $adminName = session()->get('user_name') ?? 'Super Admin';
    $adminEmail = session()->get('user_email') ?? 'admin@blax.com';
    $adminInitials = strtoupper(substr($adminName, 0, 1) . (substr($adminName, strpos($adminName, ' ') + 1, 1) ?? ''));

    $adminUserId = (int) session()->get('user_id');
    $adminUnreadCount = 0;
    $adminNotifications = [];
    if ($adminUserId > 0) {
        // Badge and dropdown come from one statement. Querying them separately made
        // every admin page pay two full round trips before rendering anything.
        $adminNotifSnapshot = (new \App\Models\NotificationModel())->getHeaderSnapshot($adminUserId, 6);
        $adminUnreadCount   = $adminNotifSnapshot['unread_count'];
        $adminNotifications = $adminNotifSnapshot['notifications'];
    }
?>

<!DOCTYPE html><html class="light" lang="en"><head>
    <?= view('components/head', ['title' => 'Blax | Platform Master Console']) ?>
    <style>
        /* Desktop Collapsible Mini-Sidebar Transitions */
        #admin-sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), padding 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #admin-main-content {
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Zero FOUC & Collapsed States on Desktop (md: 768px+) */
        @media (min-width: 768px) {
            html.admin-sidebar-init-collapsed #admin-sidebar,
            #admin-sidebar.sidebar-collapsed {
                width: 5rem !important; /* 80px */
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.admin-sidebar-init-collapsed #admin-main-content,
            #admin-main-content.sidebar-collapsed-main {
                margin-left: 5rem !important; /* 80px */
            }

            html.admin-sidebar-init-collapsed .sidebar-text,
            #admin-sidebar.sidebar-collapsed .sidebar-text {
                display: none !important;
            }

            html.admin-sidebar-init-collapsed .sidebar-section-divider,
            #admin-sidebar.sidebar-collapsed .sidebar-section-divider {
                display: block !important;
                border-top: 1px solid rgba(148, 163, 184, 0.2);
                margin: 0.65rem 0.25rem;
            }

            html.admin-sidebar-init-collapsed .nav-item,
            #admin-sidebar.sidebar-collapsed .nav-item {
                justify-content: center !important;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.admin-sidebar-init-collapsed .nav-item .material-symbols-outlined,
            #admin-sidebar.sidebar-collapsed .nav-item .material-symbols-outlined {
                font-size: 22px !important;
                margin: 0 !important;
            }

            html.admin-sidebar-init-collapsed .sidebar-brand-box,
            #admin-sidebar.sidebar-collapsed .sidebar-brand-box {
                justify-content: center !important;
            }

            html.admin-sidebar-init-collapsed .sidebar-profile-card,
            #admin-sidebar.sidebar-collapsed .sidebar-profile-card {
                justify-content: center !important;
                padding: 0.5rem !important;
            }

            html.admin-sidebar-init-collapsed .sidebar-profile-actions,
            #admin-sidebar.sidebar-collapsed .sidebar-profile-actions {
                display: none !important;
            }

            html.admin-sidebar-init-collapsed .sidebar-collapsed-signout,
            #admin-sidebar.sidebar-collapsed .sidebar-collapsed-signout {
                display: flex !important;
            }
        }

        /* Compact sidebar nav vertical rhythm so the menu fits without a visible scrollbar */
        #admin-sidebar nav > * + * { margin-top: 0.75rem !important; }
        #admin-sidebar nav .nav-item { padding-top: 0.375rem; padding-bottom: 0.375rem; }
        #admin-sidebar nav p.sidebar-text { margin-bottom: 0.25rem !important; }
    </style>
    <script>
        (function() {
            if (localStorage.getItem('blax_admin_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('admin-sidebar-init-collapsed');
            }
        })();
    </script>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased selection:bg-terra-green/20 selection:text-terra-green">

    <div class="flex h-screen overflow-hidden">

        <div id="admin-sidebar-overlay" class="sidebar-overlay"></div>

        <!-- SideNavBar (Bento / Clean Precision Design System) -->
        <aside id="admin-sidebar" class="bg-white dark:bg-slate-900 hidden md:flex flex-col h-full py-4 px-3 z-40 border-r border-slate-200/80 dark:border-slate-800 shadow-xs w-64 fixed left-0 h-screen transition-transform duration-300">

            <!-- Brand Identity & Console Card -->
            <div class="px-2 mb-4 flex items-center justify-between sidebar-brand-box gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="<?= base_url('assets/brand/logo-mark.svg') ?>" alt="Blax" width="40" height="40" class="w-10 h-10 rounded-xl shrink-0 shadow-md shadow-slate-900/20 border border-slate-700/50" title="Blax Console">
                    <div class="min-w-0 sidebar-text">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base font-bold text-slate-900 dark:text-white tracking-tight font-display">Blax</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 border border-slate-700/30 font-mono">Console</span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate font-sans">Master Platform Ops</p>
                    </div>
                </div>

                <!-- Mobile Close Button -->
                <button id="admin-sidebar-close" type="button" class="md:hidden text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer relative z-50" aria-label="Close navigation" onclick="const s=document.getElementById('admin-sidebar');if(s){s.classList.remove('mobile-open','flex','z-50');s.classList.add('hidden');}const o=document.getElementById('admin-sidebar-overlay');if(o){o.classList.remove('active');}document.body.classList.remove('mobile-nav-open');">
                    <span class="material-symbols-outlined text-[20px] pointer-events-none">close</span>
                </button>
            </div>

            <!-- Grouped Semantic Navigation -->
            <nav class="flex-1 overflow-y-auto no-scrollbar space-y-4 pr-1">

                <!-- 1. CORE PLATFORM -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-1.5 font-sans sidebar-text">Core Platform</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'dashboard' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/dashboard') ?>" title="Overview">
                            <?php if ($activeNav === 'dashboard'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'dashboard' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">dashboard</span>
                            <span class="truncate sidebar-text">Overview</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'tenants' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/tenants') ?>" title="Tenants & Merchants">
                            <?php if ($activeNav === 'tenants'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'tenants' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">storefront</span>
                            <span class="truncate sidebar-text">Tenants &amp; Merchants</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'customers' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/customers') ?>" title="Customers">
                            <?php if ($activeNav === 'customers'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'customers' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">group</span>
                            <span class="truncate sidebar-text">Customers</span>
                        </a>
                    </div>
                </div>

                <!-- 2. FINANCE & RISK -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-1.5 font-sans sidebar-text">Finance &amp; Risk</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'payments' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/payments') ?>" title="Payment Requests">
                            <?php if ($activeNav === 'payments'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'payments' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">payments</span>
                                <span class="truncate sidebar-text">Payment Requests</span>
                            </div>
                        </a>

                        <a class="nav-item group relative flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'compliance' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/compliance') ?>" title="Compliance & Reports">
                            <?php if ($activeNav === 'compliance'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'compliance' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">policy</span>
                                <span class="truncate sidebar-text">Compliance &amp; Reports</span>
                            </div>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'audit' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/audit-log') ?>" title="Audit Log">
                            <?php if ($activeNav === 'audit'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'audit' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">history_toggle_off</span>
                            <span class="truncate sidebar-text">Audit Log</span>
                        </a>
                    </div>
                </div>

                <!-- 3. INTELLIGENCE & OPERATIONS -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-3 mb-1.5 font-sans sidebar-text">Intelligence &amp; Ops</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'analytics' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/analytics') ?>" title="Platform Analytics">
                            <?php if ($activeNav === 'analytics'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'analytics' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">insights</span>
                            <span class="truncate sidebar-text">Platform Analytics</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'tracking' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/tracking') ?>" title="Live Dispatch & Tracking">
                            <?php if ($activeNav === 'tracking'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'tracking' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">route</span>
                            <span class="truncate sidebar-text">Live Dispatch &amp; Tracking</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'shipping_settings' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/shipping-settings') ?>" title="Shipping Fee Settings">
                            <?php if ($activeNav === 'shipping_settings'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'shipping_settings' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">local_shipping</span>
                            <span class="truncate sidebar-text">Shipping Fee Settings</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'content' ? 'bg-terra-green/10 text-terra-green font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('admin/content') ?>" title="Content Management">
                            <?php if ($activeNav === 'content'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'content' ? 'fill-icon text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">web</span>
                            <span class="truncate sidebar-text">Content Management</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Bottom Super Admin Profile Card -->
            <div class="mt-auto pt-3 border-t border-slate-200/80 dark:border-slate-800 space-y-2">
                <div class="sidebar-profile-card flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 shadow-2xs" title="<?= esc($adminName) ?> (Super Administrator)">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs font-mono">
                        <?= esc($adminInitials) ?>
                    </div>
                    <div class="min-w-0 flex-1 sidebar-text">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate font-sans"><?= esc($adminName) ?></p>
                        <p class="text-[10px] text-slate-400 truncate flex items-center gap-1 font-sans">
                            <span class="w-1.5 h-1.5 rounded-full bg-terra-green"></span> Super Administrator
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between px-1 sidebar-profile-actions">
                    <a href="<?= base_url('/') ?>" target="_blank" rel="noopener noreferrer" class="text-[11px] font-semibold text-terra-green hover:underline flex items-center gap-1">
                        <span>Public Marketplace</span>
                        <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                    </a>
                    <a class="flex items-center gap-1 text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:underline transition-all" href="<?= base_url('logout') ?>">
                        <span class="material-symbols-outlined text-[14px]">logout</span>
                        <span>Sign Out</span>
                    </a>
                </div>

                <!-- Collapsed Icon-Only Sign Out Shortcut -->
                <div class="hidden sidebar-collapsed-signout items-center justify-center pt-1">
                    <a href="<?= base_url('logout') ?>" class="w-9 h-9 rounded-xl flex items-center justify-center text-rose-500 hover:bg-rose-500/10 dark:hover:bg-rose-950/30 transition-colors" title="Sign Out">
                        <span class="material-symbols-outlined text-[19px]">logout</span>
                    </a>
                </div>
            </div>

        </aside>

        <!-- Main Content Area -->
        <main id="admin-main-content" class="flex-1 ml-0 md:ml-64 overflow-y-auto bg-slate-50 dark:bg-slate-950 relative">

            <!-- Top Header Bar -->
            <header class="sticky top-0 z-40 bg-white/85 dark:bg-slate-900/85 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800 px-4 md:px-8 lg:px-10 py-3 flex justify-between items-center transition-all">

                <!-- Left: Mobile Trigger, Desktop Toggle + Breadcrumb & Title -->
                <div class="flex items-center gap-3 min-w-0">
                    <button id="admin-sidebar-toggle" type="button" class="md:hidden text-slate-600 dark:text-slate-300 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer" aria-label="Open mobile navigation">
                        <span class="material-symbols-outlined text-[20px]">menu</span>
                    </button>

                    <button id="admin-header-sidebar-toggle" type="button" onclick="toggleAdminSidebar()" class="hidden md:flex p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer" title="Toggle Sidebar">
                        <span class="material-symbols-outlined text-[20px]">dock_to_left</span>
                    </button>
                    
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium">
                            <span class="hover:text-slate-600 dark:hover:text-slate-200 cursor-default">Console</span>
                            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                            <span class="text-slate-700 dark:text-slate-300 font-semibold truncate"><?= esc($pageTitle) ?></span>
                        </div>
                        <h2 class="text-lg md:text-xl font-black text-slate-900 dark:text-white tracking-tight truncate leading-tight font-display"><?= esc($pageTitle) ?></h2>
                    </div>
                </div>

                <!-- Right: Platform Status, Notifications & Profile -->
                <div class="flex items-center gap-2 md:gap-3 shrink-0">

                    <!-- Live Telemetry Status Pill -->
                    <div class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-terra-green animate-pulse"></span>
                        <span class="text-[11px] font-sans">System Nominal</span>
                    </div>

                    <!-- Theme Switcher -->
                    <button type="button" class="theme-toggle-btn w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 relative transition-colors cursor-pointer" title="Toggle dark / light theme" aria-label="Toggle theme">
                        <span class="material-symbols-outlined theme-toggle-icon text-[20px]">dark_mode</span>
                    </button>

                    <!-- Notification Bell Dropdown -->
                    <div class="relative">
                        <button id="admin-notif-toggle" type="button" class="w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 relative transition-colors cursor-pointer" aria-label="Notifications" aria-haspopup="true" aria-expanded="false">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                            <?php if ($adminUnreadCount > 0): ?>
                                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-xs animate-bounce font-mono"><?= (int) $adminUnreadCount ?></span>
                            <?php endif; ?>
                        </button>

                        <div id="admin-notif-panel" class="hidden fixed left-3 right-3 top-16 sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-2 w-auto sm:w-80 max-w-sm sm:max-w-none mx-auto sm:mx-0 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/80 dark:border-slate-800 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white font-display">Platform Alerts</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-terra-green/10 text-terra-green font-mono"><?= (int) $adminUnreadCount ?> new</span>
                                </div>
                                <?php if ($adminUnreadCount > 0): ?>
                                    <form method="post" action="<?= base_url('notifications/mark-all-read') ?>" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-[11px] font-semibold text-terra-green hover:underline cursor-pointer">Mark all read</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                <?php if (!empty($adminNotifications)): ?>
                                    <?php foreach ($adminNotifications as $n): ?>
                                        <a href="<?= base_url('notifications/click/' . (int)$n['id']) ?>" class="flex items-start gap-3 p-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors block <?= empty($n['is_read']) ? 'bg-terra-green/5' : 'opacity-70 hover:opacity-100' ?>">
                                            <span class="material-symbols-outlined text-[18px] text-terra-green mt-0.5 shrink-0">
                                                <?= match($n['type'] ?? '') {
                                                    'merchant_verification' => 'storefront',
                                                    'customer_registration' => 'person_add',
                                                    'compliance'            => 'flag',
                                                    default                 => 'notifications',
                                                } ?>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-1">
                                                    <p class="font-semibold text-slate-800 dark:text-slate-200 truncate"><?= esc($n['title']) ?></p>
                                                    <span class="text-[9px] text-slate-400 font-normal shrink-0 font-mono"><?= date('M d, H:i', strtotime($n['created_at'])) ?></span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5"><?= esc($n['message']) ?></p>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-6 text-center">
                                        <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 text-3xl mb-1">notifications_off</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">No alerts at the moment.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Admin Dropdown -->
                    <div class="relative inline-flex items-center">
                        <button id="admin-profile-toggle" type="button" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer" aria-haspopup="true" aria-expanded="false" aria-label="Admin menu">
                            <div class="w-7 h-7 rounded-lg bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 flex items-center justify-center font-bold text-xs shrink-0 font-mono">
                                <?= esc($adminInitials) ?>
                            </div>
                            <span class="hidden md:inline text-xs font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[100px]"><?= esc($adminName) ?></span>
                            <span class="material-symbols-outlined text-[16px] text-slate-400">arrow_drop_down</span>
                        </button>
                        <div id="admin-profile-quickmenu" class="hidden absolute right-0 top-full mt-2 min-w-[220px] w-max max-w-[calc(100vw-1.5rem)] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/80 dark:border-slate-800 py-1.5 z-50 text-xs origin-top-right">
                            <div class="px-3 py-2 border-b border-slate-200/80 dark:border-slate-800 mb-1">
                                <p class="font-bold text-slate-900 dark:text-white truncate font-display"><?= esc($adminName) ?></p>
                                <p class="text-[10px] text-slate-400 truncate font-mono"><?= esc($adminEmail) ?></p>
                            </div>
                            <a href="<?= base_url('admin/audit-log') ?>" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">security</span>
                                <span>Security Logs</span>
                            </a>
                            <a href="<?= base_url('logout') ?>" class="flex items-center gap-2 px-3 py-2 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400">
                                <span class="material-symbols-outlined text-[16px]">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>

                </div>

            </header>

            <div class="p-4 md:p-8 lg:p-10 max-w-7xl mx-auto space-y-6">

                <?= $this->renderSection('content') ?>

            </div>

        </main>

    </div>

    <?= $this->renderSection('scripts') ?>

    <script>
    function toggleAdminSidebar() {
        const sidebar = document.getElementById('admin-sidebar');
        const main = document.getElementById('admin-main-content');
        const icon = document.getElementById('admin-sidebar-collapse-icon');
        if (!sidebar || !main) return;

        // Clean up early FOUC class
        document.documentElement.classList.remove('admin-sidebar-init-collapsed');

        const willCollapse = !sidebar.classList.contains('sidebar-collapsed');

        if (willCollapse) {
            sidebar.classList.add('sidebar-collapsed');
            main.classList.add('sidebar-collapsed-main');
            if (icon) icon.textContent = 'chevron_right';
            localStorage.setItem('blax_admin_sidebar_collapsed', 'true');
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            main.classList.remove('sidebar-collapsed-main');
            if (icon) icon.textContent = 'chevron_left';
            localStorage.setItem('blax_admin_sidebar_collapsed', 'false');
        }
    }

    // Sync state on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
        const stored = localStorage.getItem('blax_admin_sidebar_collapsed');
        const isCollapsed = (stored === 'true');

        const sidebar = document.getElementById('admin-sidebar');
        const main = document.getElementById('admin-main-content');
        const icon = document.getElementById('admin-sidebar-collapse-icon');

        if (isCollapsed) {
            if (sidebar) sidebar.classList.add('sidebar-collapsed');
            if (main) main.classList.add('sidebar-collapsed-main');
            if (icon) icon.textContent = 'chevron_right';
        } else {
            if (sidebar) sidebar.classList.remove('sidebar-collapsed');
            if (main) main.classList.remove('sidebar-collapsed-main');
            if (icon) icon.textContent = 'chevron_left';
        }

        // Mobile drawer bindings
        var toggle = document.getElementById('admin-sidebar-toggle');
        var close = document.getElementById('admin-sidebar-close');
        var overlay = document.getElementById('admin-sidebar-overlay');

        if (!sidebar || !toggle || !overlay) return;

        function openSidebar() {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex', 'z-50', 'mobile-open');
            overlay.classList.add('active');
            document.body.classList.add('mobile-nav-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('mobile-open', 'flex', 'z-50');
            sidebar.classList.add('hidden');
            overlay.classList.remove('active');
            document.body.classList.remove('mobile-nav-open');
        }

        toggle.addEventListener('click', openSidebar);
        if (close) close.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    });
    </script>

    <!-- Admin Real-time Marketplace Alerts Container -->
    <div id="blax-admin-toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full"></div>

    <script>
    (function() {
        let lastOrderId = null;
        let lastPrintingId = null;
        let lastNotifId = null;
        let lastDeliverySig = null;
        let lastMaxDeliveryId = 0;
        let isInitial = true;

        function getAdminToastContainer() {
            let container = document.getElementById('blax-admin-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'blax-admin-toast-container';
                container.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full';
                // Toasts carry server-generated, user-supplied text (customer names,
                // notification bodies). Announce them without hijacking the whole page.
                container.setAttribute('aria-live', 'polite');
                container.setAttribute('aria-atomic', 'false');
                document.body.appendChild(container);
            }
            return container;
        }

        function playAdminChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') ctx.resume();

                const now = ctx.currentTime;
                // Double chime note: D5 (587.33Hz) -> A5 (880Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0.16, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, now + 0.12);
                gain2.gain.setValueAtTime(0.2, now + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.12);
                osc2.stop(now + 0.55);
            } catch(e) {
                console.debug('Admin chime error:', e);
            }
        }

        function showAdminToast(type, data) {
            const container = getAdminToastContainer();
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 shadow-xl flex items-start gap-3 transform translate-y-[-10px] opacity-0 transition-all duration-300';
            
            const isOrder = type === 'order';
            const isDelivery = type === 'delivery';
            const icon = isDelivery ? 'local_shipping' : (isOrder ? 'shopping_cart' : 'print');
            const iconBg = isDelivery ? 'bg-sky-500/10 text-sky-600' : (isOrder ? 'bg-terra-green/10 text-terra-green' : 'bg-purple-500/10 text-purple-600');
            const title = isDelivery ? `Delivery #${data.ref_number || data.tracking_id || data.id}` : (isOrder ? `Platform Order #${data.order_number}` : `Printing #${data.request_number}`);
            const sub = isDelivery
                ? `${data.shop_name || ''} • ${data.customer_name || 'Customer'} (${data.status || 'shipped'})`
                : `${data.shop_name} • ${data.customer_name} (₱${data.total_amount_fmt})`;

            // Static skeleton only. title/sub/fulfillment_method are built from the
            // realtime poll payload (customer_name, shop_name, order_number), i.e.
            // user-controlled text rendered inside the admin session, so none of it
            // may be interpolated into this string. Each value is set via textContent.
            toast.innerHTML = `
                <div class="blax-admin-toast-badge w-10 h-10 rounded-xl ${iconBg} flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="blax-admin-toast-icon material-symbols-outlined text-[20px]"></span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="blax-admin-toast-title text-xs font-bold text-slate-900 dark:text-white truncate font-display"></span>
                        <span class="text-[10px] font-semibold text-terra-green bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full font-mono">Realtime</span>
                    </div>
                    <p class="blax-admin-toast-sub text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate"></p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="blax-admin-toast-fulfillment text-[10px] text-slate-400 font-medium uppercase tracking-wider font-mono"></span>
                    </div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 -mr-1 text-xs cursor-pointer" onclick="this.closest('div.pointer-events-auto').remove()">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            toast.querySelector('.blax-admin-toast-icon').textContent = icon;
            toast.querySelector('.blax-admin-toast-title').textContent = title;
            toast.querySelector('.blax-admin-toast-sub').textContent = sub;
            toast.querySelector('.blax-admin-toast-fulfillment').textContent = data.fulfillment_method || 'Delivery';
            toast.setAttribute('role', 'status');

            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 350);
            }, 7000);
        }

        let isAdminPollInFlight = false;

        function checkAdminRealtime() {
            if (document.visibilityState === 'hidden' || isAdminPollInFlight) return;
            isAdminPollInFlight = true;

            let url = '<?= base_url('admin/realtime/check') ?>';
            const params = [];
            if (lastOrderId !== null) params.push('last_order_id=' + encodeURIComponent(lastOrderId));
            if (lastPrintingId !== null) params.push('last_printing_id=' + encodeURIComponent(lastPrintingId));
            if (lastNotifId !== null) params.push('last_notif_id=' + encodeURIComponent(lastNotifId));
            if (lastDeliverySig !== null) params.push('last_delivery_sig=' + encodeURIComponent(lastDeliverySig));
            params.push('last_max_delivery_id=' + encodeURIComponent(lastMaxDeliveryId || 0));
            params.push('_t=' + Date.now());
            if (params.length > 0) url += '?' + params.join('&');

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => {
                    if (!r.ok) return null;
                    return r.json();
                })
                .then(res => {
                    if (!res || !res.success) return;

                    if (isInitial) {
                        lastOrderId = res.max_order_id || 0;
                        lastPrintingId = res.max_printing_id || 0;
                        lastNotifId = res.max_notif_id || 0;
                        if (typeof res.delivery_sig !== 'undefined') lastDeliverySig = res.delivery_sig;
                        if (typeof res.max_delivery_id !== 'undefined') lastMaxDeliveryId = res.max_delivery_id || 0;
                        isInitial = false;
                        if (typeof res.unread_count === 'number') {
                            updateAdminNotifBadge(res.unread_count);
                        }
                        if (Array.isArray(res.active_deliveries)) {
                            res.active_deliveries.forEach(d => window.dispatchEvent(new CustomEvent('blax:admin-delivery-update', { detail: d })));
                        }
                        return;
                    }

                    if (res.max_order_id) lastOrderId = Math.max(lastOrderId || 0, res.max_order_id);
                    if (res.max_printing_id) lastPrintingId = Math.max(lastPrintingId || 0, res.max_printing_id);
                    if (res.max_notif_id) lastNotifId = Math.max(lastNotifId || 0, res.max_notif_id);
                    if (typeof res.delivery_sig !== 'undefined') lastDeliverySig = res.delivery_sig;
                    if (typeof res.max_delivery_id !== 'undefined') lastMaxDeliveryId = Math.max(lastMaxDeliveryId || 0, res.max_delivery_id);

                    if (typeof res.unread_count === 'number') {
                        updateAdminNotifBadge(res.unread_count);
                    }

                    if (res.changed === false) {
                        return;
                    }

                    if (res.summary) {
                        window.dispatchEvent(new CustomEvent('blax:admin-summary-update', { detail: res.summary }));
                    }

                    if (res.has_new_orders && Array.isArray(res.new_orders) && res.new_orders.length > 0) {
                        playAdminChime();
                        res.new_orders.forEach(ord => {
                            showAdminToast('order', ord);
                            window.dispatchEvent(new CustomEvent('blax:admin-new-order', { detail: ord }));
                        });
                    }

                    if (res.has_new_printing && Array.isArray(res.new_printing) && res.new_printing.length > 0) {
                        playAdminChime();
                        res.new_printing.forEach(pr => {
                            showAdminToast('printing', pr);
                            window.dispatchEvent(new CustomEvent('blax:admin-new-printing', { detail: pr }));
                        });
                    }

                    if (Array.isArray(res.new_deliveries) && res.new_deliveries.length > 0) {
                        playAdminChime();
                        res.new_deliveries.forEach(d => showAdminToast('delivery', d));
                    }

                    if (Array.isArray(res.active_deliveries)) {
                        res.active_deliveries.forEach(d => {
                            window.dispatchEvent(new CustomEvent('blax:admin-delivery-update', { detail: d }));
                        });
                    }
                })
                .catch(err => {
                    console.debug('Admin realtime poll skipped:', err);
                })
                .finally(() => {
                    isAdminPollInFlight = false;
                });
        }

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && !isAdminPollInFlight) {
                checkAdminRealtime();
            }
        });

        function updateAdminNotifBadge(count) {
            const toggleBtn = document.getElementById('admin-notif-toggle');
            if (!toggleBtn) return;

            let badge = toggleBtn.querySelector('span.bg-rose-500');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-xs animate-bounce font-mono';
                    toggleBtn.appendChild(badge);
                }
                badge.textContent = count;
            } else if (badge) {
                badge.remove();
            }

            const headerUnread = document.querySelector('#admin-notif-panel .bg-terra-green\\/10');
            if (headerUnread) {
                if (count > 0) {
                    headerUnread.textContent = count + ' new';
                    headerUnread.classList.remove('hidden');
                } else {
                    headerUnread.classList.add('hidden');
                }
            }
        }

        // AJAX Mark All Read handler for Admin
        document.addEventListener('DOMContentLoaded', function() {
            const markAllForm = document.querySelector('#admin-notif-panel form');
            if (markAllForm) {
                markAllForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetch(markAllForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(markAllForm)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            updateAdminNotifBadge(0);
                            document.querySelectorAll('#admin-notif-panel a.bg-terra-green\\/5').forEach(el => {
                                el.classList.remove('bg-terra-green/5');
                                el.classList.add('opacity-70');
                            });
                            const btn = markAllForm.querySelector('button');
                            if (btn) btn.textContent = 'All read ✓';
                        }
                    })
                    .catch(() => {
                        markAllForm.submit();
                    });
                });
            }

            document.querySelectorAll('#admin-notif-panel a').forEach(item => {
                item.addEventListener('click', function() {
                    this.classList.remove('bg-terra-green/5');
                    this.classList.add('opacity-70');
                    const badge = document.querySelector('#admin-notif-toggle span.bg-rose-500');
                    if (badge) {
                        const current = parseInt(badge.textContent || '0', 10);
                        if (current > 1) {
                            badge.textContent = current - 1;
                        } else {
                            badge.remove();
                        }
                    }
                });
            });
        });

        let adminPollTimer = null;
        let adminPollInterval = 25000;

        function scheduleNextAdminPoll() {
            if (adminPollTimer) clearTimeout(adminPollTimer);
            if (document.visibilityState === 'hidden') return;
            adminPollTimer = setTimeout(() => {
                checkAdminRealtime();
                scheduleNextAdminPoll();
            }, adminPollInterval);
        }

        // Initial check after page renders, then schedule adaptive polling
        setTimeout(() => {
            checkAdminRealtime();
            scheduleNextAdminPoll();
        }, 2000);

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                checkAdminRealtime();
                scheduleNextAdminPoll();
            } else {
                if (adminPollTimer) clearTimeout(adminPollTimer);
            }
        });

        window.addEventListener('pagehide', () => {
            if (adminPollTimer) clearTimeout(adminPollTimer);
        });

        // Universal showToast helper for admin pages
        window.showToast = function(opts) {
            if (typeof opts === 'string') opts = { message: opts, type: 'info' };
            const type = opts.type || 'info';
            const title = opts.title || (type === 'success' ? 'Success' : (type === 'error' ? 'Notice' : 'System Notification'));
            const message = opts.message || '';
            const url = opts.url || '';
            const icon = opts.icon || (
                type === 'success' ? 'check_circle' :
                type === 'error' ? 'error' :
                type === 'warning' ? 'warning' :
                type === 'order' ? 'shopping_bag' :
                type === 'delivery' ? 'two_wheeler' :
                type === 'printing' ? 'print' : 'notifications'
            );

            const container = getAdminToastContainer();
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3.5 shadow-xl flex items-start gap-3 transform translate-y-[-10px] opacity-0 transition-all duration-300';
            
            const colorMap = {
                success: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                error: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                warning: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                order: 'bg-terra-green/10 text-terra-green border-terra-green/20',
                printing: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                info: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20'
            };
            const badgeStyle = colorMap[type] || colorMap.info;

            let actionHtml = '';
            if (url) {
                actionHtml = `
                    <a href="#" class="blax-toast-link inline-flex items-center gap-1 text-[11px] font-bold text-terra-green hover:underline mt-1.5">
                        <span>View Details</span>
                        <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                    </a>
                `;
            }

            // Static skeleton only. title/message/icon/url all originate from the
            // server (notification rows built from customer names, order numbers and
            // uploaded file names), so interpolating them into innerHTML here would
            // be a stored-XSS sink executing in the admin session. Every dynamic
            // value below is assigned with textContent, or via a scheme-checked href.
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl ${badgeStyle} border flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                    <span class="blax-toast-icon material-symbols-outlined text-[18px]"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="blax-toast-title text-xs font-bold text-slate-900 dark:text-white leading-tight font-display"></h4>
                    <p class="blax-toast-message text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-snug break-words"></p>
                    ${actionHtml}
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 -mr-1 rounded-lg transition-colors cursor-pointer" onclick="this.closest('div.pointer-events-auto').remove()">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            toast.querySelector('.blax-toast-icon').textContent = icon;
            toast.querySelector('.blax-toast-title').textContent = title;
            toast.querySelector('.blax-toast-message').textContent = message;

            if (url) {
                const link = toast.querySelector('.blax-toast-link');
                // Block javascript:/data: and other scheme-based href injection.
                const candidate = String(url).trim();
                if (/^https?:\/\//i.test(candidate) || (candidate.startsWith('/') && !candidate.startsWith('//'))) {
                    link.setAttribute('href', candidate);
                } else {
                    link.remove();
                }
            }

            if (type === 'error') {
                toast.setAttribute('role', 'alert');
            }

            container.appendChild(toast);
            playAdminChime();

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            const duration = opts.duration || 6000;
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 350);
            }, duration);
        };

        // Flashdata auto-triggers
        <?php if (session()->getFlashdata('success')): ?>
            window.showToast({
                type: 'success',
                title: 'Admin Action Completed',
                message: <?= json_encode((string)session()->getFlashdata('success')) ?>
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            window.showToast({
                type: 'error',
                title: 'Notice',
                message: <?= json_encode((string)session()->getFlashdata('error')) ?>
            });
        <?php endif; ?>
    })();
    </script>
</body></html>