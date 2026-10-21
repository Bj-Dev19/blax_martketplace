<?php
    $sc = $cms ?? $siteContents ?? [];
    $contactEmail = trim((string) ($sc['footer_contact_email']['text_value'] ?? 'support@blaxinventory.com'));
    $contactPhone = trim((string) ($sc['footer_phone']['text_value'] ?? '+63 917 123 4567'));
    $contactAddress = trim((string) ($sc['footer_address']['text_value'] ?? 'Poblacion, Polomolok, South Cotabato, Philippines 9504'));
    $copyrightText = trim((string) ($sc['footer_copyright']['text_value'] ?? ''));
    if ($copyrightText === '') {
        $copyrightText = '© ' . date('Y') . ' Blax Marketplace. All rights reserved.';
    }
?>
<footer class="bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 w-full mt-auto">

    <div class="w-full py-10 md:py-14 px-4 sm:px-6 md:px-8 lg:px-10 max-w-7xl mx-auto">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 pb-10 border-b border-slate-100 dark:border-slate-800">

            <!-- Brand & Platform Trust Badges (4 Cols on Desktop) -->
            <div class="lg:col-span-4 flex flex-col gap-4">
                <a class="inline-flex items-center gap-2.5 group w-fit" href="<?= base_url('/') ?>">
                    <div>
                        <span class="text-xl font-display font-extrabold text-slate-900 dark:text-white tracking-tight block leading-none">Blax</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-terra-green block mt-0.5">Local Marketplace</span>
                    </div>
                </a>

                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                    Connecting Polomolok &amp; South Cotabato shoppers with verified local merchants, custom printing shops, and real-time road delivery.
                </p>

                <!-- Meaningful Trust Badges (Replaces dead # icon links) -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                        <span class="material-symbols-outlined text-[14px] text-terra-green">verified_user</span>
                        <span>Verified Merchants</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                        <span class="material-symbols-outlined text-[14px] text-blue-600">account_balance_wallet</span>
                        <span>GCash &amp; COD</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                        <span class="material-symbols-outlined text-[14px] text-emerald-600">route</span>
                        <span>Live GPS Tracking</span>
                    </span>
                </div>
            </div>

            <!-- Navigation Columns (8 Cols on Desktop, 2-Col Grid on Mobile) -->
            <div class="lg:col-span-8 grid grid-cols-2 sm:grid-cols-3 gap-6 sm:gap-8">

                <!-- Column 1: Marketplace -->
                <div class="flex flex-col gap-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Marketplace</h4>
                    <nav class="flex flex-col gap-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('categories') ?>">Browse Categories</a>
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('shops') ?>">Verified Shops</a>
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('printing-services') ?>">Printing Services</a>
                        <a class="hover:text-terra-green transition-colors w-fit inline-flex items-center gap-1 font-semibold text-terra-green" href="<?= base_url('signup/merchant') ?>">
                            <span>Become a Merchant</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
                        </a>
                    </nav>
                </div>

                <!-- Column 2: Customer Care -->
                <div class="flex flex-col gap-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Customer Area</h4>
                    <nav class="flex flex-col gap-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('customer/orders') ?>">Track My Orders</a>
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('customer/printing') ?>">Printing Requests</a>
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('customer/addresses') ?>">Shipping Addresses</a>
                        <a class="hover:text-terra-green transition-colors w-fit" href="<?= base_url('customer/profile') ?>">Account Settings</a>
                    </nav>
                </div>

                <!-- Column 3: Contact & Office Info (CMS Managed) -->
                <div class="col-span-2 sm:col-span-1 flex flex-col gap-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Contact &amp; Support</h4>
                    <div class="flex flex-col gap-2.5 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        <?php if (!empty($contactEmail)): ?>
                            <a href="mailto:<?= esc($contactEmail) ?>" class="inline-flex items-center gap-2 hover:text-terra-green transition-colors min-w-0">
                                <span class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 flex items-center justify-center shrink-0 text-terra-green">
                                    <span class="material-symbols-outlined text-[15px]">mail</span>
                                </span>
                                <span class="truncate"><?= esc($contactEmail) ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($contactPhone)): ?>
                            <a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $contactPhone)) ?>" class="inline-flex items-center gap-2 hover:text-terra-green transition-colors">
                                <span class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 flex items-center justify-center shrink-0 text-terra-green">
                                    <span class="material-symbols-outlined text-[15px]">call</span>
                                </span>
                                <span><?= esc($contactPhone) ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($contactAddress)): ?>
                            <div class="inline-flex items-start gap-2">
                                <span class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700 flex items-center justify-center shrink-0 text-terra-green mt-0.5">
                                    <span class="material-symbols-outlined text-[15px]">location_on</span>
                                </span>
                                <span class="leading-relaxed text-xs"><?= esc($contactAddress) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>

        <!-- Bottom Copyright Bar with Safe Bottom Padding for Floating Chat Button -->
        <div class="pt-6 pb-16 sm:pb-2 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400 dark:text-slate-500">
            <p><?= esc($copyrightText) ?></p>
            <p class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Polomolok &amp; South Cotabato Delivery Network</span>
            </p>
        </div>

    </div>

</footer>