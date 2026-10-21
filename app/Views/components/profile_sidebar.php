<?php
    $activeNav   = $activeNav ?? 'overview';
    $userName    = session()->get('user_name') ?? 'Customer';
    $userEmail   = session()->get('user_email') ?? '';
    $userImage   = session()->get('profile_image_url') ?? '';
    $userInitial = strtoupper(substr($userName, 0, 1));

    $navItems = [
        ['key' => 'overview',  'label' => 'Profile',           'mobileLabel' => 'Overview',  'icon' => 'account_circle', 'url' => base_url('customer/profile')],
        ['key' => 'orders',    'label' => 'My Orders',         'mobileLabel' => 'Orders',    'icon' => 'shopping_bag',   'url' => base_url('customer/orders')],
        ['key' => 'printing',  'label' => 'Printing Requests', 'mobileLabel' => 'Printing',  'icon' => 'print',          'url' => base_url('customer/printing')],
        ['key' => 'addresses', 'label' => 'Addresses',         'mobileLabel' => 'Addresses', 'icon' => 'location_on',    'url' => base_url('customer/addresses')],
        ['key' => 'favorites', 'label' => 'Saved Shops',       'mobileLabel' => 'Saved',     'icon' => 'favorite',       'url' => base_url('customer/favorites')],
    ];
?>

<!-- Mobile Segmented Tabs (Sticky Glassmorphism Header Rail) -->
<div class="md:hidden w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/70 dark:border-slate-800 sticky top-[58px] sm:top-[66px] z-30">
    <nav id="mobile-account-tabs" class="px-3 py-2 flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth" aria-label="Account Navigation">
        <?php foreach ($navItems as $item): ?>
            <?php $isActive = ($activeNav === $item['key']); ?>
            <a href="<?= $item['url'] ?>"
               <?= $isActive ? 'data-active-account-tab="1" aria-current="page"' : '' ?>
               class="px-3.5 py-2 min-h-[38px] whitespace-nowrap text-xs font-bold rounded-xl flex items-center gap-1.5 transition-all shrink-0 <?= $isActive ? 'bg-terra-green text-white shadow-2xs' : 'bg-slate-100/80 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:text-terra-green' ?>">
                <span class="material-symbols-outlined text-[16px] <?= $isActive ? 'fill-icon text-white' : '' ?>"><?= $item['icon'] ?></span>
                <span><?= esc($item['mobileLabel']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var activeTab = document.querySelector('#mobile-account-tabs [data-active-account-tab="1"]');
    if (activeTab && typeof activeTab.scrollIntoView === 'function') {
        activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
});
</script>

<!-- Desktop Calm Sidebar -->
<aside class="w-60 lg:w-64 shrink-0 hidden md:flex flex-col justify-between self-start sticky top-[66px] h-[calc(100dvh-4.125rem)] bg-white dark:bg-slate-900 border-r border-slate-200/70 dark:border-slate-800 p-5 z-30 overflow-y-auto no-scrollbar">

    <div class="flex flex-col gap-6">

        <!-- User Mini Profile Card -->
        <a href="<?= base_url('customer/profile') ?>" class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 hover:border-terra-green/30 transition-colors">
            <div class="w-10 h-10 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center font-bold text-sm overflow-hidden border border-terra-green/20 shrink-0">
                <?php if (!empty($userImage)): ?>
                    <img src="<?= esc(profile_image_url($userImage, 'thumbnail')) ?>" alt="<?= esc($userName) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="font-mono"><?= esc($userInitial) ?></span>
                <?php endif; ?>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= esc($userName) ?></p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate"><?= esc($userEmail) ?></p>
            </div>
        </a>

        <!-- Calm Unified Navigation List (No Noisy Section Overlines) -->
        <nav class="flex flex-col gap-1.5" aria-label="Customer Account">
            <?php foreach ($navItems as $item): ?>
                <?php $isActive = ($activeNav === $item['key']); ?>
                <a href="<?= $item['url'] ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 <?= $isActive ? 'bg-terra-green text-white shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:text-terra-green dark:hover:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-800/70' ?>">
                    <span class="material-symbols-outlined text-[18px] <?= $isActive ? 'fill-icon text-white' : 'text-slate-400 dark:text-slate-500' ?>"><?= $item['icon'] ?></span>
                    <span><?= esc($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

    </div>

    <!-- Bottom Shortcut: Return to Marketplace -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
        <a href="<?= base_url('/') ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-800/70 transition-colors group">
            <span class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span>Marketplace</span>
            </span>
            <span class="material-symbols-outlined text-[15px] transform group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
        </a>
    </div>

</aside>