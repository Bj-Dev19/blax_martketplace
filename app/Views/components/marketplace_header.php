<?php
    $activeNav = $activeNav ?? '';
    $cartCount = $cartCount ?? 0;
    $isLogged  = session()->get('isLoggedIn');
    $userName  = session()->get('user_name') ?? 'User';
    $userEmail = session()->get('user_email') ?? '';
    $profileImage = session()->get('profile_image_url') ?? '';
    $avatarChar = strtoupper(substr($userName, 0, 1));
?>

<header class="sticky top-0 z-40 w-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 transition-colors shadow-2xs">
    <div class="flex justify-between items-center w-full min-h-[58px] sm:min-h-[66px] py-2 px-3 sm:px-6 md:px-8 lg:px-10 max-w-full">

        <!-- Left: Brand & Search -->
        <div class="flex items-center gap-3 sm:gap-5 lg:gap-6 shrink-0">

            <!-- Brand Logo -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2 group transition-transform">
                <div class="w-9 h-9 rounded-xl bg-terra-green/10 dark:bg-terra-green/20 border border-terra-green/30 text-terra-green flex items-center justify-center font-extrabold shadow-2xs group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">storefront</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-display text-lg sm:text-xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-none">
                        Blax<span class="text-terra-green">.</span>
                    </span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 leading-none mt-0.5">Polomolok</span>
                </div>
            </a>

            <!-- Natural-Language AI Search Bar -->
            <form id="ai-search-form" action="<?= base_url('search') ?>" method="GET" class="hidden md:flex items-center relative">
                <span id="ai-search-icon" class="material-symbols-outlined absolute left-3.5 text-slate-400 dark:text-slate-500 text-[18px]" data-icon="search">search</span>
                <input id="ai-search-input" 
                       name="q" 
                       value="<?= esc($searchQuery ?? '') ?>" 
                       maxlength="200" 
                       autocomplete="off" 
                       class="bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-full pl-10 pr-4 py-2 w-64 lg:w-80 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green focus:bg-white dark:focus:bg-slate-900 transition-all font-sans" 
                       placeholder="Search merchandise, rush prints, shops..." 
                       title="Describe what you need in English, Bisaya or Tagalog" 
                       type="text">
            </form>

            <script>
            (function () {
                var f = document.getElementById('ai-search-form');
                if (!f) return;
                var input = document.getElementById('ai-search-input');
                var icon  = document.getElementById('ai-search-icon');
                f.addEventListener('submit', function () {
                    if (!input || input.value.trim() === '') return;
                    if (icon) {
                        icon.textContent = 'progress_activity';
                        icon.classList.add('animate-spin', 'text-terra-green');
                    }
                    input.classList.add('opacity-60');
                    input.setAttribute('readonly', 'readonly');
                });
            })();
            </script>

        </div>

        <!-- Center / Right: Desktop Navigation Links & Action Controls -->
        <div class="flex items-center gap-1.5 sm:gap-3">

            <!-- Desktop Nav Pills -->
            <nav class="hidden lg:flex items-center gap-1.5 mr-2" role="navigation">
                <a class="<?= $activeNav === 'home' ? 'text-terra-green dark:text-terra-green-light font-bold bg-terra-green/10 dark:bg-terra-green/20 border border-terra-green/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' ?> text-xs font-semibold px-3.5 py-2 rounded-xl transition-all" href="<?= base_url('/') ?>">
                    Home
                </a>
                <a class="<?= $activeNav === 'categories' ? 'text-terra-green dark:text-terra-green-light font-bold bg-terra-green/10 dark:bg-terra-green/20 border border-terra-green/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' ?> text-xs font-semibold px-3.5 py-2 rounded-xl transition-all" href="<?= base_url('categories') ?>">
                    Categories
                </a>
                <a class="<?= $activeNav === 'printing' ? 'text-terra-green dark:text-terra-green-light font-bold bg-terra-green/10 dark:bg-terra-green/20 border border-terra-green/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' ?> text-xs font-semibold px-3.5 py-2 rounded-xl transition-all inline-flex items-center gap-1.5" href="<?= base_url('printing-services') ?>">
                    <span>Printing</span>
                    <span class="text-[10px] font-bold bg-terra-amber/15 text-terra-amber-dark dark:text-terra-amber px-1.5 py-0.5 rounded-md leading-none">Rush</span>
                </a>
                <a class="<?= $activeNav === 'shops' ? 'text-terra-green dark:text-terra-green-light font-bold bg-terra-green/10 dark:bg-terra-green/20 border border-terra-green/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' ?> text-xs font-semibold px-3.5 py-2 rounded-xl transition-all" href="<?= base_url('shops') ?>">
                    Shops
                </a>
            </nav>

            <div class="flex items-center gap-1 sm:gap-1.5">

                <!-- Theme Toggle Button -->
                <button type="button" class="theme-toggle-btn p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all relative flex items-center justify-center" title="Toggle dark / light theme" aria-label="Toggle theme">
                    <span class="material-symbols-outlined theme-toggle-icon text-[20px]">dark_mode</span>
                </button>

                <!-- Notification Bell Dropdown Toggle -->
                <div class="relative block">
                    <button id="notif-dropdown-toggle" type="button" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all relative flex items-center justify-center" aria-label="Notifications" aria-haspopup="true" aria-expanded="false">
                        <span class="material-symbols-outlined text-[20px] sm:text-[22px]">notifications</span>
                        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-terra-amber rounded-full border-2 border-white dark:border-slate-900 animate-pulse"></span>
                        <?php endif; ?>
                    </button>

                    <div id="notif-dropdown" class="hidden fixed left-3 right-3 top-16 sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-2 w-auto sm:w-80 max-w-sm sm:max-w-none mx-auto sm:mx-0 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 origin-top">
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-xs text-slate-900 dark:text-white">Notifications</span>
                                <span class="text-[10px] text-terra-green font-bold bg-terra-green/10 px-1.5 py-0.5 rounded-full font-mono"><?= (int)($unreadCount ?? 0) ?> unread</span>
                            </div>
                            <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                                <form method="post" action="<?= base_url('notifications/mark-all-read') ?>" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-[11px] text-terra-green font-semibold hover:underline">Mark all read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 text-xs custom-scrollbar">
                            <?php if (!empty($recentNotifs)): ?>
                                <?php foreach ($recentNotifs as $n): ?>
                                    <a href="<?= base_url('notifications/click/' . (int)$n['id']) ?>" class="block p-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors <?= empty($n['is_read']) ? 'bg-terra-green/5 dark:bg-terra-green/10' : 'opacity-70 hover:opacity-100' ?>">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200 flex items-center justify-between gap-1">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <?php if (empty($n['is_read'])): ?>
                                                    <span class="w-1.5 h-1.5 rounded-full bg-terra-green shrink-0"></span>
                                                <?php endif; ?>
                                                <span class="truncate"><?= esc($n['title']) ?></span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-normal shrink-0 font-mono"><?= date('M d, H:i', strtotime($n['created_at'])) ?></span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug"><?= esc($n['message']) ?></p>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-6 text-center text-slate-400 text-xs">No notifications yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart Pill Trigger -->
                <a href="<?= base_url('cart') ?>" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all relative flex items-center justify-center" title="Shopping Cart" aria-label="Shopping Cart">
                    <span class="material-symbols-outlined text-[20px] sm:text-[22px]">shopping_cart</span>
                    <span id="cart-count-badge" class="absolute top-1 right-1 bg-terra-green text-white text-[10px] min-w-[16px] h-4 px-1 rounded-full flex items-center justify-center font-bold font-mono shadow-2xs <?= ($cartCount > 0) ? '' : 'hidden' ?>"><?= $cartCount ?></span>
                </a>

                <!-- My Orders Link (Desktop / Tablet only) -->
                <?php if ($isLogged): ?>
                    <a href="<?= base_url('customer/orders') ?>" class="hidden sm:inline-flex p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all relative items-center justify-center" title="My Orders" aria-label="My Orders">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                        <?php if (!empty($activeOrdersCount) && $activeOrdersCount > 0): ?>
                            <span id="active-orders-badge" class="absolute top-1 right-1 bg-terra-earth text-white text-[10px] min-w-[16px] h-4 px-1 rounded-full flex items-center justify-center font-bold font-mono" title="<?= (int)$activeOrdersCount ?> active order(s)"><?= (int)$activeOrdersCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

            </div>

            <!-- Mobile Drawer Toggle -->
            <button id="mobile-menu-toggle" type="button" class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl flex items-center justify-center transition-colors" aria-label="Open menu">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>

            <!-- Profile / Sign In Area -->
            <div class="flex items-center pl-1 sm:pl-2 border-l border-slate-200 dark:border-slate-800 relative">

                <?php if ($isLogged): ?>

                    <div class="relative">
                        <button id="profile-dropdown-toggle" type="button" class="profile-dropdown-toggle w-8 h-8 sm:w-9 sm:h-9 rounded-xl overflow-hidden bg-terra-green/10 border border-terra-green/30 flex items-center justify-center font-bold text-terra-green hover:ring-2 hover:ring-terra-green/30 transition-all text-xs sm:text-sm shrink-0" aria-haspopup="true" aria-expanded="false" aria-label="Profile menu">
                            <?php if (!empty($profileImage)): ?>
                                <img class="w-full h-full object-cover" src="<?= esc(profile_image_url($profileImage)) ?>" alt="<?= esc($userName) ?> avatar">
                            <?php else: ?>
                                <?= $avatarChar ?>
                            <?php endif; ?>
                        </button>

                        <!-- Profile Dropdown -->
                        <div id="profile-dropdown" class="dropdown-menu absolute right-0 top-full mt-2 min-w-[240px] w-max max-w-[calc(100vw-1.5rem)] bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 origin-top-right">

                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-terra-green/10 border border-terra-green/30 flex items-center justify-center font-bold text-terra-green shrink-0">
                                    <?php if (!empty($profileImage)): ?>
                                        <img class="w-full h-full object-cover" src="<?= esc(profile_image_url($profileImage)) ?>" alt="<?= esc($userName) ?> avatar">
                                    <?php else: ?>
                                        <?= $avatarChar ?>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white text-xs truncate"><?= esc($userName) ?></p>
                                    <p class="text-[11px] text-slate-400 truncate"><?= esc($userEmail) ?></p>
                                    <span class="inline-flex items-center gap-1 mt-1 text-[9px] font-bold text-terra-green bg-terra-green/10 dark:bg-terra-green/20 px-1.5 py-0.5 rounded-md">Customer</span>
                                </div>
                            </div>

                            <div class="py-1">
                                <a href="<?= base_url('customer/profile') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-terra-green transition-colors">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">person</span>
                                    <span>Profile Account</span>
                                </a>

                                <a href="<?= base_url('customer/printing') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-terra-green transition-colors">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">print</span>
                                    <span>Printing Requests</span>
                                </a>

                                <a href="<?= base_url('customer/orders') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-terra-green transition-colors">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">shopping_bag</span>
                                    <span>Purchase Orders</span>
                                </a>

                                <a href="<?= base_url('customer/addresses') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-terra-green transition-colors">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">location_on</span>
                                    <span>Shipping Addresses</span>
                                </a>
                            </div>

                            <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>

                            <a href="<?= base_url('logout') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                <span class="material-symbols-outlined text-[18px] text-rose-500">logout</span>
                                <span>Logout</span>
                            </a>

                        </div>
                    </div>

                <?php else: ?>

                    <a href="<?= base_url('login') ?>" class="bg-terra-green hover:bg-terra-green-dark text-white font-bold text-xs sm:text-xs uppercase tracking-wider px-3.5 py-2 rounded-xl transition-all shadow-xs hover:shadow hover:-translate-y-0.5 whitespace-nowrap">
                        Sign In
                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="mobile-drawer-backdrop" class="drawer-backdrop"></div>
<div id="mobile-drawer" class="mobile-drawer flex flex-col justify-between bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800">
    <div>
        <div class="flex justify-between items-center p-4 border-b border-slate-200 dark:border-slate-800">
            <span class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-terra-green/10 text-terra-green flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                </div>
                <span>Blax<span class="text-terra-green">.</span> Marketplace</span>
            </span>
            <button id="mobile-menu-close" type="button" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Close menu">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <?php if ($isLogged): ?>
            <!-- User Profile Summary in Drawer -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-terra-green/10 border border-terra-green/30 flex items-center justify-center font-bold text-terra-green shrink-0">
                        <?php if (!empty($profileImage)): ?>
                            <img class="w-full h-full object-cover" src="<?= esc(profile_image_url($profileImage)) ?>" alt="<?= esc($userName) ?> avatar">
                        <?php else: ?>
                            <?= $avatarChar ?>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-xs text-slate-900 dark:text-white truncate"><?= esc($userName) ?></p>
                        <p class="text-[10px] text-slate-400 truncate"><?= esc($userEmail) ?></p>
                        <span class="inline-flex items-center gap-1 mt-0.5 text-[9px] font-bold text-terra-green bg-terra-green/10 px-1.5 py-0.5 rounded-md">Customer</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-slate-200/80 dark:border-slate-700/60">
                    <a href="<?= base_url('customer/orders') ?>" class="flex items-center justify-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-[11px] font-bold py-1.5 px-2 rounded-xl hover:border-terra-green transition-colors">
                        <span class="material-symbols-outlined text-[15px] text-terra-green">shopping_bag</span>
                        <span>Orders</span>
                        <?php if (!empty($activeOrdersCount) && $activeOrdersCount > 0): ?>
                            <span class="text-[9px] bg-terra-earth text-white font-bold px-1 rounded-full ml-0.5 font-mono"><?= (int)$activeOrdersCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('customer/printing') ?>" class="flex items-center justify-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-[11px] font-bold py-1.5 px-2 rounded-xl hover:border-terra-green transition-colors">
                        <span class="material-symbols-outlined text-[15px] text-terra-green">print</span>
                        <span>Printing</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Guest Welcome Card -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold shrink-0">
                        <span class="material-symbols-outlined text-[22px]">person</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-xs text-slate-900 dark:text-white">Welcome, Guest!</p>
                        <p class="text-[11px] text-slate-400 truncate">Sign in for orders, printing & cart</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="<?= base_url('login') ?>" class="bg-terra-green text-white text-center font-bold text-xs py-2 px-3 rounded-xl hover:bg-terra-green-dark transition-all shadow-xs flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">login</span>
                        Sign In
                    </a>
                    <a href="<?= base_url('signup') ?>" class="bg-white dark:bg-slate-800 text-terra-green border border-terra-green/30 text-center font-bold text-xs py-2 px-3 rounded-xl hover:bg-terra-green/5 transition-all flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">person_add</span>
                        Sign Up
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Mobile Search Input -->
        <div class="p-3 border-b border-slate-200 dark:border-slate-800 md:hidden">
            <form action="<?= base_url('search') ?>" method="GET" class="flex items-center relative w-full">
                <span class="material-symbols-outlined absolute left-3 text-slate-400 text-[18px]">search</span>
                <input name="q" value="<?= esc($searchQuery ?? '') ?>" maxlength="200" autocomplete="off" class="bg-slate-100 dark:bg-slate-800 border-none rounded-xl pl-9 pr-4 py-2 w-full text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-terra-green" placeholder="Search products & printing..." type="text">
            </form>
        </div>

        <!-- Navigation Links -->
        <nav class="flex flex-col py-2 divide-y divide-slate-100 dark:divide-slate-800/60">
            <a class="<?= $activeNav === 'home' ? 'text-terra-green bg-terra-green/10 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' ?> text-xs px-4 py-3 flex items-center gap-3 transition-colors" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-[18px] <?= $activeNav === 'home' ? 'text-terra-green' : 'text-slate-400' ?>">home</span>
                <span>Home</span>
            </a>
            <a class="<?= $activeNav === 'categories' ? 'text-terra-green bg-terra-green/10 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' ?> text-xs px-4 py-3 flex items-center gap-3 transition-colors" href="<?= base_url('categories') ?>">
                <span class="material-symbols-outlined text-[18px] <?= $activeNav === 'categories' ? 'text-terra-green' : 'text-slate-400' ?>">category</span>
                <span>Categories</span>
            </a>
            <a class="<?= $activeNav === 'printing' ? 'text-terra-green bg-terra-green/10 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' ?> text-xs px-4 py-3 flex items-center gap-3 transition-colors" href="<?= base_url('printing-services') ?>">
                <span class="material-symbols-outlined text-[18px] <?= $activeNav === 'printing' ? 'text-terra-green' : 'text-slate-400' ?>">print</span>
                <span>Printing Services</span>
                <span class="ml-auto text-[9px] font-bold bg-terra-amber/15 text-terra-amber-dark dark:text-terra-amber px-1.5 py-0.5 rounded-full">Rush</span>
            </a>
            <a class="<?= $activeNav === 'shops' ? 'text-terra-green bg-terra-green/10 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' ?> text-xs px-4 py-3 flex items-center gap-3 transition-colors" href="<?= base_url('shops') ?>">
                <span class="material-symbols-outlined text-[18px] <?= $activeNav === 'shops' ? 'text-terra-green' : 'text-slate-400' ?>">storefront</span>
                <span>Featured Shops</span>
            </a>
            <?php if ($isLogged): ?>
                <a class="text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs px-4 py-3 flex items-center gap-3 transition-colors" href="<?= base_url('customer/profile') ?>">
                    <span class="material-symbols-outlined text-[18px] text-slate-400">person</span>
                    <span>Account Profile</span>
                </a>
                <a class="text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs px-4 py-3 flex items-center gap-3 transition-colors" href="<?= base_url('customer/addresses') ?>">
                    <span class="material-symbols-outlined text-[18px] text-slate-400">location_on</span>
                    <span>Shipping Addresses</span>
                </a>
                <a class="text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 text-xs px-4 py-3 flex items-center gap-3 transition-colors font-medium" href="<?= base_url('logout') ?>">
                    <span class="material-symbols-outlined text-[18px] text-rose-500">logout</span>
                    <span>Logout</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Merchant Portal Callout at Bottom of Drawer -->
    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
        <div class="flex items-center gap-2 mb-1.5">
            <span class="material-symbols-outlined text-terra-green text-[18px]">store</span>
            <span class="text-xs font-bold text-slate-900 dark:text-white">Are you a merchant?</span>
        </div>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-2 leading-relaxed">Sell merchandise or offer custom printing in Polomolok.</p>
        <a href="<?= base_url('merchant-signup') ?>" class="block w-full text-center text-xs font-bold text-terra-green bg-white dark:bg-slate-900 border border-terra-green/30 py-2 px-3 rounded-xl hover:bg-terra-green hover:text-white transition-all shadow-2xs">
            Register Shop / Merchant
        </a>
    </div>
</div>
