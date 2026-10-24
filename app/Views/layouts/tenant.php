<?php
    $segMap = [
        'dashboard' => ['dashboard', 'Dashboard'],
        'inventory' => ['inventory', 'Inventory'],
        'printing' => ['printing', 'Printing Requests'],
        'printing-requests' => ['printing', 'Printing Requests'],
        'orders' => ['orders', 'Orders'],
        'analytics' => ['analytics', 'Analytics'],
        'deliveries' => ['delivery', 'Deliveries'],
        'withdrawals' => ['withdrawals', 'Transfer & Withdrawal'],
        'transfer' => ['withdrawals', 'Transfer & Withdrawal'],
        'archive' => ['archive', 'Archive'],
        'settings' => ['settings', 'Settings'],
        'pos' => ['pos', 'Point of Sale (POS)'],
    ];

    $uri = service('request')->getUri();
    $seg = ($uri->getTotalSegments() >= 2) ? $uri->getSegment(2) : 'dashboard';
    $resolved = $segMap[$seg] ?? ['', $seg];
    $activeNav = $activeNav ?? $resolved[0];
    $pageTitle = $title ?? $resolved[1];
    $shopName  = $shop['shop_name'] ?? 'Blax Admin';
    $isPos     = ($activeNav === 'pos');
    $fullscreenLayout = $isPos || (!empty($fullscreenLayout) && $fullscreenLayout === true);

    $tenantUserId = (int) session()->get('user_id');
    if (!isset($unread_count)) {
        $unread_count = 0;
    }
    if (!isset($notifications) || !is_array($notifications)) {
        $notifications = [];
    }
    // Only query notifications as fallback if not already supplied by controller
    if ($tenantUserId > 0 && empty($notifications) && $unread_count === 0) {
        try {
            $notifModel = new \App\Models\NotificationModel();
            $unread_count = $notifModel->getUnreadCount($tenantUserId);
            $notifications = $notifModel->getRecent($tenantUserId, 8);
        } catch (\Throwable $e) {}
    }

    $tenantShopId = (int) (session()->get('shop_id') ?? ($shop['id'] ?? 0));
    $todayClosedStatus = null;
    if ($tenantShopId > 0) {
        $sessClosedKey = 'shop_closed_status_' . $tenantShopId;
        $cachedClosed = session()->get($sessClosedKey);
        $now = time();
        if (is_array($cachedClosed) && isset($cachedClosed['expires']) && $cachedClosed['expires'] > $now) {
            $todayClosedStatus = $cachedClosed['data'];
        } else {
            try {
                $calScheduleModel = new \App\Models\ShopCalendarScheduleModel();
                $statusInfo = $calScheduleModel->getShopOperationalStatus($tenantShopId);
                if (!empty($statusInfo['is_closed_today'])) {
                    $todayClosedStatus = $statusInfo;
                }
                session()->set($sessClosedKey, [
                    'expires' => $now + 60, // memoize for 60 seconds
                    'data'    => $todayClosedStatus
                ]);
            } catch (\Throwable $e) {}
        }
    }
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <?= view('components/head', ['title' => $shopName . ' | Merchant Hub • Blax']) ?>
    <style>
        /* Desktop Collapsible Mini-Sidebar Transitions */
        #tenant-sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), padding 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #tenant-main-content {
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Zero FOUC & Collapsed States on Desktop (md: 768px+) */
        @media (min-width: 768px) {
            html.tenant-sidebar-init-collapsed #tenant-sidebar,
            #tenant-sidebar.sidebar-collapsed {
                width: 5rem !important; /* 80px */
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.tenant-sidebar-init-collapsed #tenant-main-content,
            #tenant-main-content.sidebar-collapsed-main {
                margin-left: 5rem !important; /* 80px */
            }

            html.tenant-sidebar-init-collapsed .sidebar-text,
            #tenant-sidebar.sidebar-collapsed .sidebar-text {
                display: none !important;
            }

            html.tenant-sidebar-init-collapsed .sidebar-section-divider,
            #tenant-sidebar.sidebar-collapsed .sidebar-section-divider {
                display: block !important;
                border-top: 1px solid rgba(148, 163, 184, 0.2);
                margin: 0.65rem 0.25rem;
            }

            html.tenant-sidebar-init-collapsed .nav-item,
            #tenant-sidebar.sidebar-collapsed .nav-item {
                justify-content: center !important;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            html.tenant-sidebar-init-collapsed .nav-item .material-symbols-outlined,
            #tenant-sidebar.sidebar-collapsed .nav-item .material-symbols-outlined {
                font-size: 22px !important;
                margin: 0 !important;
            }

            html.tenant-sidebar-init-collapsed .sidebar-brand-box,
            #tenant-sidebar.sidebar-collapsed .sidebar-brand-box {
                justify-content: center !important;
            }

            html.tenant-sidebar-init-collapsed .sidebar-profile-card,
            #tenant-sidebar.sidebar-collapsed .sidebar-profile-card {
                justify-content: center !important;
                padding: 0.5rem !important;
            }

            html.tenant-sidebar-init-collapsed .sidebar-profile-actions,
            #tenant-sidebar.sidebar-collapsed .sidebar-profile-actions {
                display: none !important;
            }

            html.tenant-sidebar-init-collapsed .sidebar-collapsed-signout,
            #tenant-sidebar.sidebar-collapsed .sidebar-collapsed-signout {
                display: flex !important;
            }
        }
        #blax-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #059669);
            z-index: 99999;
            transform: translateX(-100%);
            transition: transform 0.2s ease;
            pointer-events: none;
        }
        #blax-page-loader.loading {
            transform: translateX(-15%);
            transition: transform 8s cubic-bezier(0.1, 0.5, 0.1, 1);
        }
    </style>
    <script>
        (function() {
            let isCollapsed = false;
            try {
                const isPos = <?= json_encode($isPos) ?>;
                const stored = localStorage.getItem('blax_tenant_sidebar_collapsed');
                isCollapsed = isPos ? (stored !== 'false') : (stored === 'true');
            } catch(e) {
                isCollapsed = false;
            }
            if (isCollapsed) {
                document.documentElement.classList.add('tenant-sidebar-init-collapsed');
            }

            window.toggleTenantSidebar = function() {
                const sidebar = document.getElementById('tenant-sidebar');
                const main = document.getElementById('tenant-main-content');
                const icon = document.getElementById('tenant-sidebar-collapse-icon');
                if (!sidebar || !main) return;

                document.documentElement.classList.remove('tenant-sidebar-init-collapsed');
                const willCollapse = !sidebar.classList.contains('sidebar-collapsed');

                if (willCollapse) {
                    sidebar.classList.add('sidebar-collapsed');
                    main.classList.add('sidebar-collapsed-main');
                    if (icon) icon.textContent = 'chevron_right';
                    try { localStorage.setItem('blax_tenant_sidebar_collapsed', 'true'); } catch(e) {}
                } else {
                    sidebar.classList.remove('sidebar-collapsed');
                    main.classList.remove('sidebar-collapsed-main');
                    if (icon) icon.textContent = 'chevron_left';
                    try { localStorage.setItem('blax_tenant_sidebar_collapsed', 'false'); } catch(e) {}
                }
            };
        })();
    </script>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased selection:bg-terra-green selection:text-white">

    <div id="blax-page-loader"></div>

    <div class="flex h-screen overflow-hidden">

        <div id="tenant-sidebar-overlay" class="sidebar-overlay"></div>

        <!-- SideNavBar -->
        <aside id="tenant-sidebar" class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl flex-col h-full py-4 px-3.5 z-40 border-r border-slate-200/80 dark:border-slate-800/80 shadow-sm w-64 fixed left-0 h-screen hidden md:flex transition-transform duration-300">

            <!-- Brand Identity & Store Card -->
            <div class="px-2 mb-5 flex items-center justify-between sidebar-brand-box gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-terra-green via-terra-green-dark to-terra-earth text-white flex items-center justify-center font-black shadow-lg shadow-terra-green/20 shrink-0 ring-2 ring-white/20" title="<?= esc($shopName) ?>">
                        <span class="material-symbols-outlined text-[22px]">storefront</span>
                    </div>
                    <div class="min-w-0 sidebar-text">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base font-display font-extrabold text-slate-900 dark:text-white tracking-tight">Blax</span>
                            <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-terra-green/10 text-terra-green dark:bg-terra-green/20 dark:text-terra-green-light border border-terra-green/20">Merchant</span>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate" title="<?= esc($shopName) ?>"><?= esc($shopName) ?></p>
                    </div>
                </div>

                <!-- Desktop Collapse / Expand Toggle Button -->
                <button id="tenant-sidebar-collapse-toggle" type="button" onclick="toggleTenantSidebar()" class="hidden md:flex p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer shadow-2xs items-center justify-center shrink-0" title="Collapse / Expand navigation (Icon Rail Mode)">
                    <span id="tenant-sidebar-collapse-icon" class="material-symbols-outlined text-[19px]">chevron_left</span>
                </button>

                <!-- Mobile Close Button -->
                <button id="tenant-sidebar-close" type="button" class="md:hidden text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer relative z-50" aria-label="Close navigation">
                    <span class="material-symbols-outlined text-[20px] pointer-events-none">close</span>
                </button>
            </div>

            <!-- Grouped Navigation -->
            <nav class="flex-1 overflow-y-auto custom-scrollbar space-y-4 pr-1">

                <!-- 1. OPERATIONS -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-3 mb-1.5 sidebar-text">Operations</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'dashboard' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/dashboard') ?>" title="Dashboard">
                            <?php if ($activeNav === 'dashboard'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'dashboard' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">dashboard</span>
                            <span class="truncate sidebar-text">Dashboard</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'pos' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/pos') ?>" title="Point of Sale (POS)">
                            <?php if ($activeNav === 'pos'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'pos' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">point_of_sale</span>
                            <span class="truncate sidebar-text">Point of Sale (POS)</span>
                        </a>

                        <a class="nav-item group relative flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'orders' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/orders') ?>" title="Orders">
                            <?php if ($activeNav === 'orders'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'orders' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">shopping_bag</span>
                                <span class="truncate sidebar-text">Orders</span>
                            </div>
                        </a>

                        <a class="nav-item group relative flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'printing' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/printing') ?>" title="Printing Requests">
                            <?php if ($activeNav === 'printing'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'printing' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">print</span>
                                <span class="truncate sidebar-text">Printing Requests</span>
                            </div>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'delivery' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/deliveries') ?>" title="Delivery Dispatch">
                            <?php if ($activeNav === 'delivery'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'delivery' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">local_shipping</span>
                            <span class="truncate sidebar-text">Delivery Dispatch</span>
                        </a>
                    </div>
                </div>

                <!-- 2. CATALOG & STOCK -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-3 mb-1.5 sidebar-text">Catalog &amp; Stock</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'inventory' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/inventory') ?>" title="Inventory">
                            <?php if ($activeNav === 'inventory'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'inventory' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">inventory_2</span>
                            <span class="truncate sidebar-text">Inventory</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'archive' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/archive') ?>" title="Archive">
                            <?php if ($activeNav === 'archive'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'archive' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">archive</span>
                            <span class="truncate sidebar-text">Archive</span>
                        </a>
                    </div>
                </div>

                <!-- 3. FINANCE & INSIGHTS -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-3 mb-1.5 sidebar-text">Finance &amp; Insights</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'analytics' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/analytics') ?>" title="Analytics">
                            <?php if ($activeNav === 'analytics'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'analytics' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">leaderboard</span>
                            <span class="truncate sidebar-text">Analytics</span>
                        </a>

                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'withdrawals' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/withdrawals') ?>" title="Transfer & Payouts">
                            <?php if ($activeNav === 'withdrawals'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'withdrawals' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">payments</span>
                            <span class="truncate sidebar-text">Transfer &amp; Payouts</span>
                        </a>
                    </div>
                </div>

                <!-- 4. PREFERENCES -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-3 mb-1.5 sidebar-text">Preferences</p>
                    <div class="sidebar-section-divider hidden"></div>
                    <div class="space-y-0.5">
                        <a class="nav-item group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all <?= $activeNav === 'settings' ? 'bg-terra-green/10 text-terra-green font-bold dark:bg-terra-green/20 dark:text-terra-green-light shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium' ?>" href="<?= base_url('tenant/settings') ?>" title="Store Settings">
                            <?php if ($activeNav === 'settings'): ?><span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-terra-green"></span><?php endif; ?>
                            <span class="material-symbols-outlined text-[19px] shrink-0 <?= $activeNav === 'settings' ? 'text-terra-green' : 'text-slate-400 group-hover:text-terra-green transition-colors' ?>">settings</span>
                            <span class="truncate sidebar-text">Store Settings</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Bottom Merchant Profile Card -->
            <div class="mt-auto pt-3.5 border-t border-slate-200/80 dark:border-slate-800/80 space-y-2">
                <a href="<?= base_url('tenant/settings') ?>" class="sidebar-profile-card flex items-center gap-2.5 p-2 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/60 transition-all group shadow-2xs" title="<?= esc($shopName) ?> Settings">
                    <div class="w-8 h-8 rounded-xl bg-terra-green/10 dark:bg-terra-green/20 text-terra-green flex items-center justify-center font-bold text-xs overflow-hidden shrink-0 border border-terra-green/20">
                        <?php if (!empty($shop['logo_url'])): ?>
                            <img src="<?= esc(logo_url($shop['logo_url'])) ?>" alt="<?= esc($shopName) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span><?= esc(strtoupper(substr($shopName, 0, 2))) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1 sidebar-text">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate group-hover:text-terra-green transition-colors"><?= esc($shopName) ?></p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate flex items-center gap-1 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified Merchant
                        </p>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-terra-green transition-transform group-hover:translate-x-0.5 sidebar-text">chevron_right</span>
                </a>

                <div class="flex items-center justify-between px-1 sidebar-profile-actions">
                    <?php $storeSlug = $shop['slug'] ?? ''; ?>
                    <?php if (!empty($storeSlug)): ?>
                        <a href="<?= base_url('shop/' . esc($storeSlug)) ?>" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-terra-green hover:text-terra-green-dark hover:underline flex items-center gap-1 transition-colors">
                            <span>Live Store</span>
                            <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                        </a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    <a class="flex items-center gap-1 text-[11px] font-bold text-rose-500 hover:text-rose-600 hover:underline transition-colors" href="<?= base_url('logout') ?>">
                        <span class="material-symbols-outlined text-[15px]">logout</span>
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

        <!-- Main Content -->
        <main id="tenant-main-content" class="flex-1 ml-0 md:ml-64 overflow-y-auto bg-slate-50 dark:bg-slate-950 relative min-h-screen">

            <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 px-4 md:px-8 py-3 flex justify-between items-center transition-all">

                <!-- Left: Mobile Toggle, Desktop Toggle & Breadcrumbs -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <button id="sidebar-toggle" type="button" class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer" aria-label="Toggle mobile sidebar">
                        <span class="material-symbols-outlined text-[20px]">menu</span>
                    </button>

                    <button type="button" onclick="toggleTenantSidebar()" class="hidden md:flex p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer" title="Toggle Sidebar">
                        <span class="material-symbols-outlined text-[20px]">dock_to_left</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500">
                            <span class="font-medium hidden sm:inline">Merchant Hub</span>
                            <span class="text-[10px] text-slate-300 dark:text-slate-600 hidden sm:inline">/</span>
                            <span class="font-display font-bold text-slate-900 dark:text-white text-sm sm:text-base tracking-tight"><?= esc($pageTitle) ?></span>
                        </div>
                        <span class="hidden lg:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 ml-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Store
                        </span>
                    </div>
                </div>

                <!-- Right: Quick Preview, Notification Bell & Profile -->
                <div class="flex items-center gap-2.5 sm:gap-3.5">
                    <?php if (!empty($storeSlug)): ?>
                        <a href="<?= base_url('shop/' . esc($storeSlug)) ?>" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-xs font-bold text-slate-700 dark:text-slate-200 transition-all shadow-2xs group" title="Preview public storefront in new tab">
                            <span class="material-symbols-outlined text-[15px] text-terra-green group-hover:scale-110 transition-transform">open_in_new</span>
                            <span>View Store</span>
                        </a>
                    <?php endif; ?>

                    <!-- Theme Switcher -->
                    <button type="button" class="theme-toggle-btn text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl relative transition-colors" title="Toggle dark / light theme" aria-label="Toggle theme">
                        <span class="material-symbols-outlined theme-toggle-icon text-[22px]">dark_mode</span>
                    </button>

                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notif-toggle" type="button" class="text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl relative transition-colors cursor-pointer" aria-label="Notifications" aria-haspopup="true" aria-expanded="false">
                            <span class="material-symbols-outlined text-[22px]">notifications</span>
                            <span id="notif-badge" class="<?= ($unread_count > 0) ? '' : 'hidden' ?> absolute top-1.5 right-1.5 min-w-[17px] h-[17px] px-1 bg-terra-amber text-slate-950 text-[9px] font-mono font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900 animate-pulse">
                                <?= (int) $unread_count ?>
                            </span>
                        </button>

                        <div id="notif-panel" class="hidden absolute right-0 top-full mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">Notifications</p>
                                    <span id="notif-unread-pill" class="<?= ($unread_count > 0) ? '' : 'hidden' ?> text-[10px] font-bold text-terra-green px-1.5 py-0.5 rounded-md bg-terra-green/10 dark:bg-terra-green/20">
                                        <?= (int) $unread_count ?> unread
                                    </span>
                                </div>
                                <form id="notif-mark-all-form" method="post" action="<?= base_url('tenant/notifications/mark-read') ?>" class="inline <?= ($unread_count > 0 || !empty($notifications)) ? '' : 'hidden' ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" id="notif-mark-all-btn" class="text-[11px] font-bold text-terra-green hover:underline cursor-pointer">Mark all read</button>
                                </form>
                            </div>

                            <div id="notif-items-container" class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                <?php if (!empty($notifications)): ?>
                                    <?php
                                        $notifIcons = [
                                            'new_order'             => 'shopping_bag',
                                            'order'                 => 'shopping_bag',
                                            'order_status'          => 'local_shipping',
                                            'order_shipped'         => 'local_shipping',
                                            'low_stock'             => 'inventory_2',
                                            'new_printing_request'  => 'print',
                                            'printing'              => 'print',
                                            'printing_ready'        => 'print',
                                            'payout'                => 'payments',
                                            'compliance'            => 'flag',
                                            'merchant_verification' => 'verified',
                                            'customer_registration' => 'person_add',
                                        ];
                                    ?>
                                    <?php foreach (array_slice($notifications, 0, 8) as $n): ?>
                                        <a href="<?= base_url('notifications/click/' . (int)$n['id']) ?>" class="flex items-start gap-2.5 px-3.5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors block <?= empty($n['is_read']) ? 'bg-terra-green/5 dark:bg-terra-green/10 font-medium' : 'opacity-70 hover:opacity-100' ?>">
                                            <div class="w-7 h-7 rounded-lg bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0 mt-0.5">
                                                <span class="material-symbols-outlined text-[16px]"><?= esc($notifIcons[$n['type']] ?? 'notifications') ?></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-1">
                                                    <p class="font-bold text-slate-800 dark:text-slate-200 truncate text-xs"><?= esc($n['title']) ?></p>
                                                    <span class="text-[9px] font-mono text-slate-400 shrink-0"><?= date('M d, H:i', strtotime($n['created_at'])) ?></span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug"><?= esc($n['message']) ?></p>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="px-4 py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                        <span class="material-symbols-outlined text-2xl text-slate-300 dark:text-slate-600 mb-1 block">notifications_off</span>
                                        No new notifications.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative inline-flex items-center">
                        <?php
                            $tenantAvatarUrl = '';
                            if (!empty($shop['logo_url'])) {
                                $tenantAvatarUrl = logo_url($shop['logo_url']);
                            } elseif (!empty(session()->get('profile_image_url'))) {
                                $tenantAvatarUrl = logo_url(session()->get('profile_image_url'));
                            }
                        ?>
                        <button id="tenant-profile-toggle" type="button" class="profile-dropdown-toggle flex items-center gap-2 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all group" aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
                            <div class="w-8 h-8 rounded-full bg-terra-green/10 dark:bg-terra-green/20 border border-terra-green/30 flex items-center justify-center text-terra-green font-bold text-xs overflow-hidden shrink-0 group-hover:ring-2 group-hover:ring-terra-green/30 transition-all">
                                <?php if ($tenantAvatarUrl !== ''): ?>
                                    <img src="<?= esc($tenantAvatarUrl) ?>" class="w-full h-full object-cover" alt="<?= esc($shopName) ?>">
                                <?php else: ?>
                                    <span><?= esc(strtoupper(substr($shopName, 0, 2))) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200 transition-transform group-hover:translate-y-0.5 hidden sm:inline">expand_more</span>
                        </button>

                        <div id="tenant-profile-dropdown" class="dropdown-menu absolute right-0 top-full mt-2 min-w-[240px] w-max max-w-[calc(100vw-1.5rem)] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 p-2 z-50 origin-top-right">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800 mb-1.5">
                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= esc($shopName) ?></p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate"><?= esc(session()->get('user_email') ?? 'Merchant') ?></p>
                            </div>
                            <a href="<?= base_url('tenant/settings') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors <?= $activeNav === 'settings' ? 'bg-terra-green/10 text-terra-green font-bold' : '' ?>">
                                <span class="material-symbols-outlined text-[17px] text-terra-green">settings</span>
                                <span>Store Settings</span>
                            </a>
                            <a href="<?= base_url('tenant/withdrawals') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors <?= $activeNav === 'withdrawals' ? 'bg-terra-green/10 text-terra-green font-bold' : '' ?>">
                                <span class="material-symbols-outlined text-[17px] text-emerald-600">payments</span>
                                <span>Transfer &amp; Payouts</span>
                            </a>
                            <?php if (!empty($storeSlug)): ?>
                                <a href="<?= base_url('shop/' . esc($storeSlug)) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors">
                                    <span class="material-symbols-outlined text-[17px] text-terra-earth">storefront</span>
                                    <span>Public Storefront ↗</span>
                                </a>
                            <?php endif; ?>
                            <div class="my-1.5 border-t border-slate-100 dark:border-slate-800"></div>
                            <a href="<?= base_url('logout') ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-colors font-medium">
                                <span class="material-symbols-outlined text-[17px] text-rose-500">logout</span>
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                </div>

            </header>

            <div class="<?= $fullscreenLayout ? 'p-2 sm:p-3 md:p-4 w-full' : 'p-4 md:p-6 lg:p-8 max-w-7xl mx-auto space-y-6 w-full' ?>">

                <?= $this->renderSection('content') ?>

            </div>

        </main>

    </div>

    <?= $this->renderSection('scripts') ?>

    <!-- Real-time Order & Printing Notification Toast Container -->
    <div id="blax-tenant-toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full"></div>

    <?php if (!empty($todayClosedStatus)): ?>
    <!-- Store Closed Sign-In Alert Modal (English) -->
    <div id="storeClosedAlertModal" class="hidden fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-opacity duration-200">
        <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xl p-6 sm:p-8 space-y-6 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header with Amber/Rose Alert Icon -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/30 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[28px]">storefront_off</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase font-mono bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                            Store Status: Closed Today
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white font-display tracking-tight">Your Shop is Currently Closed Today</h3>
                </div>
            </div>

            <!-- Description in English -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2.5">
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-sans">
                    Notice: Your store counter is currently marked as <strong>Closed for today</strong>. Customers on the marketplace cannot place orders, buy items, or request printing services while your shop is closed.
                </p>
                <?php if (!empty($todayClosedStatus['note'])): ?>
                    <div class="flex items-center gap-2 text-xs font-semibold text-amber-900 dark:text-amber-300 bg-amber-500/10 dark:bg-amber-500/15 p-2.5 rounded-xl border border-amber-500/30">
                        <span class="material-symbols-outlined text-[16px] text-amber-600">info</span>
                        <span>Reason / Note: <?= esc($todayClosedStatus['note']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-2">
                <button type="button" onclick="quickReopenStoreToday()" id="quickReopenStoreBtn" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-terra-green hover:bg-terra-green/90 text-white text-xs font-bold transition shadow-sm active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">lock_open</span>
                    <span id="quickReopenBtnText">Open Store Today</span>
                </button>
                <a href="<?= base_url('tenant/settings?tab=hours') ?>" class="w-full sm:w-auto py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-200/70 dark:hover:bg-slate-700 transition text-center cursor-pointer">
                    Manage in Settings
                </a>
                <button type="button" onclick="dismissClosedAlertModal()" class="w-full sm:w-auto py-3 px-3 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-xs font-semibold transition cursor-pointer">
                    Keep Closed
                </button>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const todayKey = <?= json_encode(date('Y-m-d')) ?>;
        const dismissedKey = 'blax_closed_alert_dismissed_' + todayKey;
        const modal = document.getElementById('storeClosedAlertModal');

        // Show modal on login/load if closed and not dismissed in current browser session
        if (modal && !sessionStorage.getItem(dismissedKey)) {
            modal.classList.remove('hidden');
        }

        window.dismissClosedAlertModal = function() {
            sessionStorage.setItem(dismissedKey, 'true');
            if (modal) modal.classList.add('hidden');
        };

        window.quickReopenStoreToday = async function() {
            const btn = document.getElementById('quickReopenStoreBtn');
            const btnText = document.getElementById('quickReopenBtnText');
            if (btnText) btnText.textContent = 'Opening...';
            if (btn) btn.disabled = true;

            const fd = new FormData();
            const csrfInput = document.querySelector('input[name="csrf_test_name"]') || document.querySelector('#settingsCsrfForm [name="csrf_test_name"]');
            if (csrfInput) {
                fd.append('csrf_test_name', csrfInput.value);
            }
            fd.append('section', 'calendar_schedule');
            fd.append('calendar_action', 'delete');
            fd.append('schedule_date', todayKey);

            try {
                const resp = await fetch("<?= base_url('tenant/settings/save') ?>", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                const data = await resp.json();
                if (modal) modal.classList.add('hidden');
                sessionStorage.setItem(dismissedKey, 'true');
                if (typeof showToast === 'function') {
                    showToast('Store is now OPEN today! Customers can now order.', false);
                }
                setTimeout(() => window.location.reload(), 800);
            } catch (err) {
                if (btnText) btnText.textContent = 'Open Store Today';
                if (btn) btn.disabled = false;
                alert('Could not update store status. Please manage operating hours in Settings.');
            }
        };
    })();
    </script>
    <?php endif; ?>

    <script>
    // Sync sidebar state on DOMContentLoaded & bind instant navigation feedback
    document.addEventListener('DOMContentLoaded', () => {
        let isCollapsed = false;
        try {
            const isPos = <?= json_encode($isPos) ?>;
            const stored = localStorage.getItem('blax_tenant_sidebar_collapsed');
            isCollapsed = isPos ? (stored !== 'false') : (stored === 'true');
        } catch(e) {
            isCollapsed = false;
        }

        const sidebar = document.getElementById('tenant-sidebar');
        const main = document.getElementById('tenant-main-content');
        const icon = document.getElementById('tenant-sidebar-collapse-icon');

        if (isCollapsed) {
            if (sidebar) sidebar.classList.add('sidebar-collapsed');
            if (main) main.classList.add('sidebar-collapsed-main');
            if (icon) icon.textContent = 'chevron_right';
        } else {
            if (sidebar) sidebar.classList.remove('sidebar-collapsed');
            if (main) main.classList.remove('sidebar-collapsed-main');
            if (icon) icon.textContent = 'chevron_left';
        }

        // Instant navigation visual feedback & auto-close mobile drawer on link click
        document.querySelectorAll('#tenant-sidebar a.nav-item, #mobile-tenant-nav a.nav-item').forEach(link => {
            link.addEventListener('click', function(e) {
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || this.target === '_blank') return;

                // Stop pending poll timers so PHP-FPM workers prioritize page navigation
                if (typeof window.__blaxCancelPoll === 'function') {
                    window.__blaxCancelPoll();
                }

                // Instant active state switch for rapid visual confirmation
                document.querySelectorAll('#tenant-sidebar a.nav-item').forEach(el => {
                    el.classList.remove('bg-terra-green/10', 'text-terra-green', 'font-semibold', 'shadow-2xs');
                    el.classList.add('text-slate-600', 'dark:text-slate-400');
                });
                this.classList.remove('text-slate-600', 'dark:text-slate-400');
                this.classList.add('bg-terra-green/10', 'text-terra-green', 'font-semibold', 'opacity-85');

                const loader = document.getElementById('blax-page-loader');
                if (loader) loader.classList.add('loading');

                const sb = document.getElementById('tenant-sidebar');
                const ov = document.getElementById('tenant-sidebar-overlay');
                if (sb && sb.classList.contains('mobile-open')) {
                    sb.classList.remove('mobile-open', 'flex', 'z-50');
                    sb.classList.add('hidden');
                    if (ov) ov.classList.remove('active');
                    document.body.classList.remove('mobile-nav-open');
                }
            });
        });
    });
    </script>

    <script>
    (function() {
        let lastOrderId = null;
        let lastPrintingId = null;
        let pollTimer = null;
        let isInitial = true;

        function getTenantToastContainer() {
            let container = document.getElementById('blax-tenant-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'blax-tenant-toast-container';
                container.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full';
                document.body.appendChild(container);
            }
            return container;
        }

        // Web Audio synthesized two-tone notification chime (zero file dependencies)
        function playChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') ctx.resume();

                const now = ctx.currentTime;
                // Note 1: F5 (698.46Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(698.46, now);
                gain1.gain.setValueAtTime(0.18, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                // Note 2: C6 (1046.50Hz) - higher bright ping
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(1046.50, now + 0.12);
                gain2.gain.setValueAtTime(0.22, now + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.12);
                osc2.stop(now + 0.55);
            } catch(e) {
                console.debug('Chime audio error:', e);
            }
        }

        function showRealtimeToast(type, data) {
            const container = getTenantToastContainer();
            if (!container || !data) return;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xl flex items-start gap-3 transform translate-y-[-10px] opacity-0 transition-all duration-300';
            
            const isOrder = type === 'order';
            const icon = isOrder ? 'shopping_bag' : 'print';
            const iconBg = isOrder ? 'bg-terra-green/10 text-terra-green border border-terra-green/20' : 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20';
            const title = isOrder ? `New Order #${data.order_number || data.id}` : `New Print #${data.request_number || data.id}`;
            const sub = isOrder 
                ? `${data.customer_name || 'Customer'} • ₱${data.total_amount_fmt || '0.00'} (${data.is_pickup ? 'Pick-up' : 'Delivery'})`
                : `${data.customer_name || 'Customer'} • ${data.service_name || 'Printing'} (₱${data.total_amount_fmt || '0.00'})`;
            const linkUrl = isOrder ? '<?= base_url('tenant/orders') ?>' : '<?= base_url('tenant/printing') ?>';

            toast.innerHTML = `
                <div class="w-10 h-10 rounded-xl ${iconBg} flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[20px]">${icon}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-xs font-bold text-slate-900 dark:text-white truncate">${title}</span>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-full border border-emerald-500/20">Just Now</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">${sub}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <a href="${linkUrl}" class="text-[11px] font-bold text-terra-green hover:underline flex items-center gap-0.5">
                            <span>Open in Queue</span>
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 -mr-1 text-xs cursor-pointer" onclick="this.closest('div.pointer-events-auto').remove()">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
            });

            playChime();

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 400);
            }, 6500);
        }

        function updateNotificationsUI(unreadCount, notifs) {
            const count = parseInt(unreadCount, 10) || 0;
            const badge = document.getElementById('notif-badge');
            const pill = document.getElementById('notif-unread-pill');
            const form = document.getElementById('notif-mark-all-form');
            const container = document.getElementById('notif-items-container');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (pill) {
                if (count > 0) {
                    pill.textContent = `${count} unread`;
                    pill.classList.remove('hidden');
                } else {
                    pill.classList.add('hidden');
                }
            }

            if (form) {
                if (count > 0 || (Array.isArray(notifs) && notifs.length > 0)) {
                    form.classList.remove('hidden');
                } else {
                    form.classList.add('hidden');
                }
            }

            if (container && Array.isArray(notifs)) {
                if (notifs.length === 0) {
                    container.innerHTML = `
                        <div class="px-4 py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                            <span class="material-symbols-outlined text-2xl text-slate-300 dark:text-slate-600 mb-1 block">notifications_off</span>
                            No new notifications.
                        </div>
                    `;
                } else {
                    const iconMap = {
                        'new_order': 'shopping_bag',
                        'order': 'shopping_bag',
                        'order_status': 'local_shipping',
                        'order_shipped': 'local_shipping',
                        'low_stock': 'inventory_2',
                        'new_printing_request': 'print',
                        'printing': 'print',
                        'printing_ready': 'print',
                        'payout': 'payments',
                        'compliance': 'flag',
                        'merchant_verification': 'verified',
                        'customer_registration': 'person_add'
                    };

                    const escapeHtml = (str) => {
                        if (!str) return '';
                        return String(str).replace(/[&<>"']/g, (m) => ({
                            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                        }[m]));
                    };

                    const formatDate = (dateStr) => {
                        try {
                            const d = new Date(dateStr);
                            if (isNaN(d.getTime())) return dateStr;
                            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' +
                                   d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
                        } catch (e) {
                            return dateStr;
                        }
                    };

                    container.innerHTML = notifs.slice(0, 8).map(n => {
                        const icon = iconMap[n.type] || 'notifications';
                        const isRead = n.is_read == 1 || n.is_read === true;
                        const bgClass = !isRead ? 'bg-terra-green/5 dark:bg-terra-green/10 font-medium' : 'opacity-70 hover:opacity-100';
                        const clickUrl = `<?= base_url('notifications/click') ?>/${encodeURIComponent(n.id)}`;

                        return `
                            <a href="${clickUrl}" class="flex items-start gap-2.5 px-3.5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors block ${bgClass}">
                                <div class="w-7 h-7 rounded-lg bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0 mt-0.5">
                                    <span class="material-symbols-outlined text-[16px]">${icon}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 truncate text-xs">${escapeHtml(n.title)}</p>
                                        <span class="text-[9px] font-mono text-slate-400 shrink-0">${formatDate(n.created_at)}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">${escapeHtml(n.message)}</p>
                                </div>
                            </a>
                        `;
                    }).join('');
                }
            }
        }

        window.updateTenantNotifications = updateNotificationsUI;
        window.__blaxCancelPoll = function() {
            if (pollTimer) {
                clearTimeout(pollTimer);
                pollTimer = null;
            }
        };

        let pollInterval = 20000;

        function scheduleNextPoll() {
            clearTimeout(pollTimer);
            pollTimer = setTimeout(pollTenantUpdates, pollInterval);
        }

        async function pollTenantUpdates() {
            if (document.visibilityState === 'hidden' || !navigator.onLine) return;
            try {
                let url = '<?= base_url('tenant/realtime/check') ?>';
                if (!isInitial && lastOrderId !== null && lastPrintingId !== null) {
                    url += `?last_order_id=${encodeURIComponent(lastOrderId)}&last_printing_id=${encodeURIComponent(lastPrintingId)}`;
                }

                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) {
                    pollInterval = Math.min(pollInterval * 1.5, 60000);
                    scheduleNextPoll();
                    return;
                }
                pollInterval = 20000;
                const data = await res.json();
                if (!data || !data.success) {
                    scheduleNextPoll();
                    return;
                }

                if (isInitial) {
                    lastOrderId = data.max_order_id || 0;
                    lastPrintingId = data.max_printing_id || 0;
                    isInitial = false;
                    if (typeof data.unread_count !== 'undefined') {
                        updateNotificationsUI(data.unread_count, data.notifications);
                    }
                    scheduleNextPoll();
                    return;
                }

                // Process new incoming orders
                if (Array.isArray(data.new_orders) && data.new_orders.length > 0) {
                    data.new_orders.forEach(order => {
                        showRealtimeToast('order', order);
                        window.dispatchEvent(new CustomEvent('blax:new-order', { detail: order }));
                    });
                }
                if (data.max_order_id) {
                    lastOrderId = Math.max(lastOrderId, data.max_order_id);
                }

                // Process new incoming printing requests
                if (Array.isArray(data.new_printing) && data.new_printing.length > 0) {
                    data.new_printing.forEach(printReq => {
                        showRealtimeToast('printing', printReq);
                        window.dispatchEvent(new CustomEvent('blax:new-printing', { detail: printReq }));
                    });
                }
                if (data.max_printing_id) {
                    lastPrintingId = Math.max(lastPrintingId, data.max_printing_id);
                }

                // Dispatch summary updates if available
                if (data.summary) {
                    window.dispatchEvent(new CustomEvent('blax:summary-update', { detail: data.summary }));
                }
                if (data.printing_summary) {
                    window.dispatchEvent(new CustomEvent('blax:printing-summary-update', { detail: data.printing_summary }));
                }
                if (data.inventory_summary) {
                    window.dispatchEvent(new CustomEvent('blax:inventory-summary-update', { detail: data.inventory_summary }));
                }

                // Live update notifications dropdown & bell badge
                if (typeof data.unread_count !== 'undefined') {
                    updateNotificationsUI(data.unread_count, data.notifications);
                }
            } catch(e) {
                console.debug('Tenant realtime poll failed:', e);
                pollInterval = Math.min(pollInterval * 1.5, 60000);
            }
            scheduleNextPoll();
        }

        // Start polling: initial check after 1.5s, then scheduled
        pollTimer = setTimeout(pollTenantUpdates, 1500);

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                pollTenantUpdates();
            }
        });

        window.addEventListener('pagehide', () => {
            clearTimeout(pollTimer);
        });
        window.addEventListener('beforeunload', () => {
            clearTimeout(pollTimer);
        });

        // Mark All As Read AJAX handling
        document.addEventListener('DOMContentLoaded', () => {
            const markAllForm = document.getElementById('notif-mark-all-form');
            if (markAllForm) {
                markAllForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('notif-mark-all-btn');
                    if (btn) btn.disabled = true;
                    try {
                        const formData = new FormData(markAllForm);
                        const res = await fetch(markAllForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        if (res.ok) {
                            updateNotificationsUI(0);
                            const container = document.getElementById('notif-items-container');
                            if (container) {
                                container.querySelectorAll('a').forEach(el => {
                                    el.classList.remove('bg-terra-green/5', 'dark:bg-terra-green/10', 'font-medium');
                                    el.classList.add('opacity-70');
                                });
                            }
                        } else {
                            markAllForm.submit();
                        }
                    } catch(err) {
                        markAllForm.submit();
                    } finally {
                        if (btn) btn.disabled = false;
                    }
                });
            }
        });

        // Flashdata auto-triggers
        <?php if (session()->getFlashdata('success')): ?>
            if (typeof window.showToast === 'function') {
                window.showToast(<?= json_encode((string)session()->getFlashdata('success')) ?>, 'success');
            }
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            if (typeof window.showToast === 'function') {
                window.showToast(<?= json_encode((string)session()->getFlashdata('error')) ?>, 'error');
            }
        <?php endif; ?>
    })();
    </script>
</body>
</html>