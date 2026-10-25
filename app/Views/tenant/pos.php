<?= $this->extend('layouts/tenant') ?>

<?= $this->section('content') ?>

<?php
$isStorePickup = !empty($order);
$customerFullName = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''));
$customerPhone = trim((string) ($order['customer_phone'] ?? ($order['phone'] ?? '')));
$customerEmail = trim((string) ($order['customer_email'] ?? ($order['email'] ?? '')));
$customerProfileImg = trim((string) ($order['profile_image_url'] ?? ''));
$customerInitials = $customerFullName !== '' ? mb_strtoupper(mb_substr($customerFullName, 0, 2)) : 'OC';
$isOnlinePaid = ($order['payment_status'] ?? '') === 'paid';
$originalTotal = (float) ($order['total_amount'] ?? 0);
$existingPosAdd = (float) ($order['pos_additional_amount'] ?? 0);
$baseOnlineAmount = max(0, $originalTotal - $existingPosAdd);
$pickupOrders = $pickupOrders ?? [];
$printingPickupRequests = $printingPickupRequests ?? [];
$printingRequest = $printingRequest ?? null;
$isPrintingPickup = !empty($printingRequest);
$isCombinedPickup = $isStorePickup && $isPrintingPickup;
$printingOnlinePaid = (float) ($printingOnlinePaid ?? 0);
$printingRemainingBalance = (float) ($printingRemainingBalance ?? 0);
$categories = $categories ?? [];
$orderItems = $orderItems ?? [];
$customerHasPrintingMap = $customerHasPrintingMap ?? [];
$customerHasOrderMap = $customerHasOrderMap ?? [];
?>

<div class="space-y-md">
    <!-- TOP: Sleek POS Command Bar -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-3.5 sm:px-5 sm:py-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl <?= $isCombinedPickup ? 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300' : ($isPrintingPickup ? 'bg-terra-earth/10 dark:bg-terra-earth/20 text-terra-earth dark:text-terra-amber' : ($isStorePickup ? 'bg-terra-green/10 dark:bg-terra-green/20 text-terra-green' : 'bg-terra-green/10 dark:bg-terra-green/20 text-terra-green')) ?> flex items-center justify-center shrink-0 shadow-sm border border-current/20">
                <span class="material-symbols-outlined text-2xl"><?= $isCombinedPickup ? 'shopping_cart_checkout' : ($isPrintingPickup ? 'print' : ($isStorePickup ? 'storefront' : 'point_of_sale')) ?></span>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">
                        Point of Sale (POS)
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= $isCombinedPickup ? 'bg-purple-100 text-purple-800 border border-purple-300' : ($isPrintingPickup ? 'bg-terra-earth/15 text-terra-earth dark:text-terra-amber border border-terra-earth/30' : ($isStorePickup ? 'bg-terra-green/15 text-terra-green border border-terra-green/30' : 'bg-terra-green/15 text-terra-green border border-terra-green/30')) ?>">
                        <?= $isCombinedPickup ? 'Combined Pick-up POS' : ($isPrintingPickup ? 'Printing Pick-up POS' : ($isStorePickup ? 'Store Pick-up POS' : 'Walk-in Counter POS')) ?>
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium hidden sm:block">
                    <?= $isCombinedPickup
                        ? 'Combined fulfillment: process product order and printing request in a single checkout without double-charging.'
                        : ($isPrintingPickup
                            ? 'Fulfill customer printing request, verify pick-up, collect counter balance, and record additions.'
                            : ($isStorePickup 
                                ? 'Fulfill online pick-up order, add in-store purchases, and record counter payment without double-charging.' 
                                : 'Quick counter retail billing and instant real-time inventory deduction for walk-in customers.')) ?>
                </p>
            </div>
        </div>

        <!-- Mode Buttons: Primary 3-Action Touch Group + Secondary Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 w-full xl:w-auto">
            <!-- Segmented Primary Action Group: min-h-[48px], 22-24px icons -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl shadow-inner">
                <!-- 1. Select Ready for Pick-up -->
                <button type="button" 
                        onclick="openPickupSelectorModal()" 
                        class="min-h-[48px] px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all flex-1 sm:flex-initial <?= $isStorePickup ? 'bg-terra-earth text-white shadow-md ring-2 ring-terra-earth/30' : 'bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-xs' ?>"
                        title="Select ready-for-pickup product order">
                    <span class="material-symbols-outlined text-[22px] sm:text-[24px] <?= $isStorePickup ? 'text-white' : 'text-terra-earth dark:text-terra-amber' ?>">shopping_bag</span>
                    <span>Ready Pick-up</span>
                    <?php if (!empty($pickupOrders)): ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-mono font-extrabold <?= $isStorePickup ? 'bg-white/20 text-white' : 'bg-terra-earth/15 text-terra-earth dark:text-terra-amber' ?>">
                            <?= count($pickupOrders) ?>
                        </span>
                    <?php endif; ?>
                </button>

                <!-- 2. Printing Ready for Pick-up -->
                <button type="button" 
                        onclick="openPrintingSelectorModal()" 
                        class="min-h-[48px] px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all flex-1 sm:flex-initial <?= $isPrintingPickup ? 'bg-terra-green text-white shadow-md ring-2 ring-terra-green/30' : 'bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-xs' ?>"
                        title="Select ready-for-pickup printing request">
                    <span class="material-symbols-outlined text-[22px] sm:text-[24px] <?= $isPrintingPickup ? 'text-white' : 'text-terra-green' ?>">print</span>
                    <span>Ready Printing</span>
                    <?php if (!empty($printingPickupRequests)): ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-mono font-extrabold <?= $isPrintingPickup ? 'bg-white/20 text-white' : 'bg-terra-green/15 text-terra-green' ?>">
                            <?= count($printingPickupRequests) ?>
                        </span>
                    <?php endif; ?>
                </button>

                <!-- 3. Scan Customer QR -->
                <button type="button" 
                        onclick="openQrScannerModal()" 
                        class="min-h-[48px] px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-all flex-1 sm:flex-initial bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-xs"
                        title="Scan customer pickup QR code">
                    <span class="material-symbols-outlined text-[22px] sm:text-[24px] text-terra-amber">qr_code_scanner</span>
                    <span>Scan QR</span>
                </button>
            </div>

            <!-- Secondary Links: New Walk-in & Back to Orders -->
            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <a href="<?= base_url('tenant/pos') ?>" 
                   class="h-10 px-3.5 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all <?= (!$isStorePickup && !$isPrintingPickup) ? 'bg-terra-green/15 text-terra-green border border-terra-green/30 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' ?>">
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    <span>New Walk-in</span>
                </a>

                <a href="<?= base_url('tenant/orders') ?>" 
                   class="h-10 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300 flex items-center gap-1 transition-all">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Orders</span>
                </a>
            </div>
        </div>
    </div>

    <?php if ($isStorePickup || $isPrintingPickup): ?>
        <?php
        // Resolve customer details if printing-only
        if (empty($customerFullName) && !empty($printingRequest)) {
            $customerFullName = trim(($printingRequest['first_name'] ?? '') . ' ' . ($printingRequest['last_name'] ?? ''));
            $customerPhone = trim((string) ($printingRequest['phone'] ?? ''));
            $customerEmail = trim((string) ($printingRequest['email'] ?? ''));
            $customerInitials = $customerFullName !== '' ? mb_strtoupper(mb_substr($customerFullName, 0, 2)) : 'OC';
        }
        
        $orderCounterDue = $isStorePickup ? ($isOnlinePaid ? 0.0 : $baseOnlineAmount) : 0.0;
        $prTotal = (float) ($printingRequest['total_price'] ?? 0);
        $prPaid = (float) $printingOnlinePaid;
        $prDue = (float) $printingRemainingBalance;
        $printCounterDue = $isPrintingPickup ? $prDue : 0.0;
        $totalCounterDue = $orderCounterDue + $printCounterDue;

        // Cross-reference checks for single mode
        $poCustId = (int) ($order['customer_id'] ?? 0);
        $matchingPr = (!$isCombinedPickup && $isStorePickup && !empty($customerHasPrintingMap[$poCustId])) ? $customerHasPrintingMap[$poCustId][0] : null;

        $prCustId = (int) ($printingRequest['customer_id'] ?? 0);
        $matchingOrder = (!$isCombinedPickup && $isPrintingPickup && !empty($customerHasOrderMap[$prCustId])) ? $customerHasOrderMap[$prCustId][0] : null;

        $prSpecs = [];
        if ($isPrintingPickup) {
            $prSpecs = [
                ((int) ($printingRequest['page_count'] ?? 1)) . ' pgs',
                ((int) ($printingRequest['copies'] ?? 1)) . ' ' . (((int) ($printingRequest['copies'] ?? 1)) === 1 ? 'copy' : 'copies'),
                strtoupper((string) ($printingRequest['color_mode'] ?? 'BW')),
                esc($printingRequest['paper_size'] ?? 'Letter'),
            ];
            if (!empty($printingRequest['binding_option']) && $printingRequest['binding_option'] !== 'none') {
                $prSpecs[] = ucfirst($printingRequest['binding_option']);
            }
        }
        ?>

        <!-- Unified Handover Manifest Dock (<120px) -->
        <div class="bg-white dark:bg-slate-900 border <?= $isCombinedPickup ? 'border-purple-300 dark:border-purple-800/60' : ($isPrintingPickup ? 'border-terra-earth/30 dark:border-terra-earth/40' : 'border-terra-green/30 dark:border-terra-green/40') ?> rounded-2xl p-3 sm:p-3.5 shadow-sm transition-all mb-3">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                
                <!-- 1. Left: Customer Profile & Order References -->
                <div class="flex items-center gap-3 min-w-[260px] max-w-[340px] shrink-0 border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-800 pb-2.5 lg:pb-0 lg:pr-3.5">
                    <div class="w-10 h-10 rounded-xl <?= $isCombinedPickup ? 'bg-purple-100 text-purple-800 dark:bg-purple-950/70 dark:text-purple-300' : ($isPrintingPickup ? 'bg-terra-earth/15 text-terra-earth dark:text-terra-amber' : 'bg-terra-green/15 text-terra-green') ?> flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs relative overflow-hidden">
                        <?php if ($customerProfileImg !== ''): ?>
                            <img class="absolute inset-0 w-full h-full object-cover" src="<?= esc(profile_image_url($customerProfileImg)) ?>" alt="<?= esc($customerFullName) ?>" loading="lazy" onerror="this.remove();">
                        <?php endif; ?>
                        <span><?= esc($customerInitials) ?></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white truncate max-w-[170px]" title="<?= esc($customerFullName) ?>">
                                <?= esc($customerFullName ?: 'Online Customer') ?>
                            </h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $isCombinedPickup ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : ($isPrintingPickup ? 'bg-terra-earth/15 text-terra-earth dark:text-terra-amber' : 'bg-terra-green/15 text-terra-green') ?>">
                                <?= $isCombinedPickup ? 'Combined' : ($isPrintingPickup ? 'Print Only' : 'Order Only') ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex-wrap font-mono">
                            <?php if ($customerPhone !== ''): ?>
                                <a href="tel:<?= esc($customerPhone) ?>" class="hover:text-terra-green transition-colors inline-flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[13px]">call</span>
                                    <span><?= esc($customerPhone) ?></span>
                                </a>
                                <span class="text-slate-300 dark:text-slate-600">•</span>
                            <?php endif; ?>
                            <?php if ($isStorePickup): ?>
                                <span class="text-slate-700 dark:text-slate-300 font-semibold" title="Order Number">#<?= esc($order['order_number'] ?? ($order['id'] ?? '')) ?></span>
                            <?php endif; ?>
                            <?php if ($isPrintingPickup): ?>
                                <?php if ($isStorePickup): ?><span class="text-slate-300 dark:text-slate-600">•</span><?php endif; ?>
                                <span class="text-terra-earth dark:text-terra-amber font-semibold" title="Printing Request Number">#<?= esc($printingRequest['request_number'] ?? '') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 2. Center: Physical Handover Manifest Items (Horizontal Scrolling Chips) -->
                <div class="flex-1 flex items-center gap-2 overflow-x-auto py-0.5 custom-scrollbar min-w-0">
                    <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider shrink-0 hidden sm:inline-block">
                        Hand Over:
                    </span>

                    <!-- Store Pick-up Product Items -->
                    <?php if ($isStorePickup && !empty($orderItems)): ?>
                        <?php foreach ($orderItems as $oit): ?>
                            <?php if (empty($oit['is_pos_addition'])): ?>
                                <?php $itemImg = trim((string) ($oit['gallery_image'] ?? '')); ?>
                                <div class="flex items-center gap-2 p-1.5 pr-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700/80 shrink-0 text-xs shadow-2xs hover:border-terra-green/30 transition-colors">
                                    <?php if ($itemImg !== ''): ?>
                                        <img src="<?= esc(product_image_url($itemImg, 'thumbnail')) ?>" alt="<?= esc($oit['product_name'] ?? 'Product') ?>" class="w-8 h-8 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0 bg-white dark:bg-slate-800" loading="lazy" onerror="this.remove();">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-400 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 leading-tight truncate max-w-[130px]" title="<?= esc($oit['product_name'] ?? 'Product') ?>">
                                            <?= esc($oit['product_name'] ?? 'Product') ?>
                                        </div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                            <span class="font-bold text-slate-700 dark:text-slate-300"><?= (int) ($oit['quantity'] ?? 1) ?>×</span>
                                            <span>• ₱<?= number_format((float) ($oit['unit_price'] ?? $oit['price'] ?? 0), 2) ?></span>
                                            <?php if (!empty($oit['variant_label'])): ?>
                                                <span class="text-[9px] px-1 rounded bg-slate-200 dark:bg-slate-700"><?= esc($oit['variant_label']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php elseif ($isStorePickup): ?>
                        <div class="text-xs text-slate-400 italic px-2">No physical items recorded for order.</div>
                    <?php endif; ?>

                    <!-- Printing Request Document Badge -->
                    <?php if ($isPrintingPickup): ?>
                        <div class="flex items-center gap-2 p-1.5 pr-2.5 rounded-xl bg-purple-50/70 dark:bg-purple-950/40 border border-purple-200/80 dark:border-purple-800/60 shrink-0 text-xs shadow-2xs">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">print</span>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-purple-900 dark:text-purple-200 leading-tight truncate max-w-[140px]" title="<?= esc($printingRequest['file_name'] ?? 'Document.pdf') ?>">
                                    <?= esc($printingRequest['file_name'] ?? 'Document.pdf') ?>
                                </div>
                                <div class="text-[10px] text-purple-700 dark:text-purple-400 font-mono mt-0.5">
                                    <?= esc(implode(' • ', $prSpecs)) ?>
                                </div>
                            </div>
                            <?php if (!empty($printingRequest['file_path'])): ?>
                                <a href="<?= base_url('uploads/' . $printingRequest['file_path']) ?>" target="_blank" class="p-1 text-purple-600 hover:text-purple-800 dark:hover:text-purple-300 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg transition-colors" title="Preview or Download document">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Cross-reference Quick Combine Buttons (if single pickup mode has another order ready) -->
                    <?php if ($matchingPr): ?>
                        <a href="<?= base_url('tenant/pos?order_id=' . $order['id'] . '&printing_id=' . $matchingPr['id']) ?>" 
                           class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shrink-0 shadow-xs transition-all active:scale-95"
                           title="Customer also has Print Request #<?= esc($matchingPr['request_number']) ?>">
                            <span class="material-symbols-outlined text-[15px]">join_inner</span>
                            <span>+ Combine Print</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($matchingOrder): ?>
                        <a href="<?= base_url('tenant/pos?order_id=' . $matchingOrder['id'] . '&printing_id=' . $printingRequest['id']) ?>" 
                           class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shrink-0 shadow-xs transition-all active:scale-95"
                           title="Customer also has Product Order #<?= esc($matchingOrder['order_number']) ?>">
                            <span class="material-symbols-outlined text-[15px]">join_inner</span>
                            <span>+ Combine Order</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- 3. Right: Counter Balance Due & Action Buttons -->
                <div class="flex items-center justify-between lg:justify-end gap-3 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 dark:border-slate-800 lg:pl-3.5">
                    <div class="text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">Collect at Counter</span>
                            <?php if ($totalCounterDue <= 0): ?>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-green-100 dark:bg-green-950 text-green-800 dark:text-green-300">PAID ONLINE</span>
                            <?php endif; ?>
                        </div>
                        <div class="font-mono text-base sm:text-lg font-black leading-tight <?= $totalCounterDue > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' ?>">
                            ₱<?= number_format($totalCounterDue, 2) ?>
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono">
                            <?php if ($isCombinedPickup): ?>
                                Order: <?= $isOnlinePaid ? 'Paid' : '₱' . number_format($orderCounterDue, 2) ?> • Print: <?= $prDue <= 0 ? 'Paid' : '₱' . number_format($prDue, 2) ?>
                            <?php elseif ($isStorePickup): ?>
                                <?= $isOnlinePaid ? 'Paid via ' . esc(strtoupper($order['payment_method'] ?: 'ONLINE')) : 'Unpaid order' ?>
                            <?php else: ?>
                                <?= $prPaid > 0 ? 'Paid ₱' . number_format($prPaid, 2) . ' online' : 'Unpaid print request' ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions Button Group -->
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                onclick="<?= $isPrintingPickup && !$isStorePickup ? 'openPrintingSelectorModal()' : 'openPickupSelectorModal()' ?>" 
                                class="h-9 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1 transition-all"
                                title="Change pick-up order/request">
                            <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                            <span class="hidden sm:inline">Change</span>
                        </button>
                        <a href="<?= base_url('tenant/pos') ?>" 
                           class="h-9 px-2.5 rounded-xl border border-red-200 dark:border-red-900/60 hover:bg-red-50 dark:hover:bg-red-950/40 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1 transition-all"
                           title="Exit pick-up mode to walk-in register">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            <span class="hidden sm:inline">Exit</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <!-- POS Main Interactive Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 items-start">
        
        <!-- Left Side: Product Search, Category Chips & Product Cards Grid (7 cols on lg, 8 cols on xl) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-3">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-4 sm:p-5 shadow-sm space-y-4">
                
                <!-- Search Header & Shortcuts Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-terra-green text-[20px]">manage_search</span>
                        <h2 class="font-display text-sm sm:text-base font-bold text-slate-900 dark:text-white">Find Products</h2>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 hidden sm:inline-block">Search by name, brand, or SKU</span>
                </div>

                <div class="relative">
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[20px] pointer-events-none">search</span>
                        <input type="text" 
                               id="posProductSearch" 
                               placeholder="Search products by name, brand, or SKU... (Press '/' to search)" 
                               autocomplete="off"
                               class="w-full pl-11 pr-20 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green focus:border-terra-green transition-all">
                        <div class="absolute right-3 flex items-center gap-1">
                            <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono font-bold text-slate-400 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-md shadow-2xs">/</kbd>
                            <button type="button" id="posClearSearch" class="hidden text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                    </div>

                    <!-- Live Results Dropdown -->
                    <div id="posSearchResults" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl z-40 max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 custom-scrollbar">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Category Filter Chips -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-0.5 custom-scrollbar text-xs">
                    <button type="button" 
                            class="pos-cat-chip px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-terra-green text-white shadow-xs" 
                            data-category="">
                        All Categories
                    </button>
                    <?php foreach ($categories as $cat): ?>
                        <button type="button" 
                                class="pos-cat-chip px-3.5 py-1.5 rounded-full font-medium transition-all shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700" 
                                data-category="<?= (int) $cat['id'] ?>">
                            <?= esc($cat['name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Product Catalog Header & Quick Grid -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-terra-green">grid_view</span>
                            <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Product Catalog</h3>
                            <span id="posActiveCategoryText" class="text-xs text-slate-400 dark:text-slate-500 font-medium hidden sm:inline-block">• All Products</span>
                        </div>
                        <span id="posCatalogHint" class="text-xs text-slate-400 font-medium">Click card to add to cart</span>
                    </div>

                    <!-- Modern Product Cards Grid -->
                    <div id="posQuickCatalog" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 max-h-[calc(100vh-270px)] min-h-[420px] overflow-y-auto pr-1 custom-scrollbar">
                        <!-- Populated dynamically via JS product cards -->
                        <div class="col-span-full py-16 text-center text-slate-400 flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-3xl animate-spin text-terra-green">progress_activity</span>
                            <span class="text-xs font-medium">Loading store products...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Sticky Cashier Register Panel (5 cols on lg, 4 cols on xl) -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-3 lg:sticky lg:top-4">
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-3.5 sm:p-4 shadow-sm space-y-3">
                
                <!-- Register Header -->
                <div class="flex items-center justify-between border-b border-outline-variant/20 pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                        <h2 class="text-title-md font-bold text-on-surface">
                            <?= $isCombinedPickup ? 'Combined Pick-up Summary' : ($isPrintingPickup ? 'Printing Pick-up Summary' : ($isStorePickup ? 'Sale & Pick-up Summary' : 'Counter Cart')) ?>
                        </h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="posCartCountBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant font-mono">0 items</span>
                        <button type="button" onclick="posClearAllCart()" class="text-xs font-semibold text-outline hover:text-error transition-colors px-1.5 py-0.5 rounded hover:bg-error-container/20" title="Clear all items in cart">
                            Clear
                        </button>
                    </div>
                </div>

                <?php if ($isCombinedPickup): ?>
                    <!-- Section: COMBINED PICK-UP (ORDER + PRINTING) -->
                    <!-- 1. Original Order Box -->
                    <div class="border border-secondary/30 rounded-xl p-3 bg-secondary-container/10 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-secondary flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                                Order #<?= esc($order['order_number']) ?>
                            </h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold <?= $isOnlinePaid ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= $isOnlinePaid ? 'PAID ONLINE' : 'UNPAID' ?>
                            </span>
                        </div>
                        <div class="border border-outline-variant/20 rounded-lg overflow-hidden bg-surface-container-lowest divide-y divide-outline-variant/10 text-xs">
                            <?php if (!empty($orderItems)): ?>
                                <?php foreach ($orderItems as $oit): ?>
                                    <?php if (empty($oit['is_pos_addition'])): ?>
                                        <div class="p-1.5 px-2.5 flex items-center justify-between">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <p class="font-semibold text-on-surface truncate"><?= esc($oit['product_name'] ?? 'Item') ?></p>
                                                <p class="text-[10px] text-outline font-mono">Qty: <?= (int) ($oit['quantity'] ?? 1) ?> × ₱<?= number_format((float) ($oit['unit_price'] ?? $oit['price'] ?? 0), 2) ?></p>
                                            </div>
                                            <span class="font-mono font-bold text-on-surface">₱<?= number_format((float) ($oit['line_total'] ?? (((int) ($oit['quantity'] ?? 1)) * ((float) ($oit['unit_price'] ?? $oit['price'] ?? 0)))), 2) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold pt-0.5">
                            <span class="text-on-surface-variant">Order Total:</span>
                            <span class="font-mono <?= $isOnlinePaid ? 'text-green-700' : 'text-on-surface' ?>">
                                ₱<?= number_format($baseOnlineAmount, 2) ?> <?= $isOnlinePaid ? '(PAID)' : '' ?>
                            </span>
                        </div>
                    </div>

                    <!-- 2. Original Printing Box -->
                    <div class="border border-primary/30 rounded-xl p-3 bg-primary-container/10 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">print</span>
                                Printing #<?= esc($printingRequest['request_number']) ?>
                            </h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold <?= $printingRemainingBalance <= 0 ? 'bg-green-100 text-green-800' : ($printingOnlinePaid > 0 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') ?>">
                                <?= $printingRemainingBalance <= 0 ? 'PAID ONLINE' : ($printingOnlinePaid > 0 ? 'DOWN PAYMENT PAID' : 'UNPAID') ?>
                            </span>
                        </div>
                        <div class="border border-outline-variant/20 rounded-lg p-2 bg-surface-container-lowest text-xs space-y-1">
                            <div class="flex justify-between items-start">
                                <p class="font-semibold text-on-surface truncate max-w-[200px]"><?= esc($printingRequest['file_name'] ?? 'Document') ?></p>
                                <span class="font-mono font-bold text-on-surface">₱<?= number_format((float) ($printingRequest['total_price'] ?? 0), 2) ?></span>
                            </div>
                            <p class="text-[10px] text-outline font-mono">
                                <?= (int) ($printingRequest['page_count'] ?? 1) ?> pgs • <?= (int) ($printingRequest['copies'] ?? 1) ?> copies • <?= strtoupper((string) ($printingRequest['color_mode'] ?? 'BW')) ?>
                            </p>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold pt-0.5">
                            <span class="text-on-surface-variant">Print Balance Due:</span>
                            <span class="font-mono <?= $printingRemainingBalance > 0 ? 'text-amber-800 font-extrabold' : 'text-green-700' ?>">
                                ₱<?= number_format($printingRemainingBalance, 2) ?> <?= $printingRemainingBalance <= 0 ? '(PAID)' : '' ?>
                            </span>
                        </div>
                    </div>

                <?php elseif ($isPrintingPickup): ?>
                    <!-- Section: ORIGINAL PRINTING REQUEST -->
                    <div class="border border-outline-variant/30 rounded-xl p-3 bg-surface-container-low/40 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-on-surface flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-primary">print</span>
                                Original Printing Request
                            </h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold <?= $printingRemainingBalance <= 0 ? 'bg-green-100 text-green-800' : ($printingOnlinePaid > 0 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') ?>">
                                <?= $printingRemainingBalance <= 0 ? 'PAID ONLINE' : ($printingOnlinePaid > 0 ? 'DOWN PAYMENT PAID' : 'UNPAID') ?>
                            </span>
                        </div>

                        <!-- Printing Details Box -->
                        <div class="border border-outline-variant/20 rounded-lg p-2 bg-surface-container-lowest text-xs space-y-1">
                            <div class="flex justify-between items-start">
                                <p class="font-semibold text-on-surface truncate max-w-[200px]"><?= esc($printingRequest['file_name'] ?? 'Document') ?></p>
                                <span class="font-mono font-bold text-on-surface">₱<?= number_format((float) ($printingRequest['total_price'] ?? 0), 2) ?></span>
                            </div>
                            <p class="text-[10px] text-outline font-mono">
                                #<?= esc($printingRequest['request_number']) ?> • <?= (int) ($printingRequest['page_count'] ?? 1) ?> pgs • <?= (int) ($printingRequest['copies'] ?? 1) ?> copies • <?= strtoupper((string) ($printingRequest['color_mode'] ?? 'BW')) ?>
                            </p>
                        </div>

                        <div class="flex justify-between items-center text-xs pt-0.5">
                            <span class="text-on-surface-variant font-medium">Online Paid Credit:</span>
                            <span class="font-mono text-green-700 font-bold">
                                -₱<?= number_format($printingOnlinePaid, 2) ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-on-surface-variant">Remaining Print Balance:</span>
                            <span class="font-mono <?= $printingRemainingBalance > 0 ? 'text-amber-800 font-extrabold' : 'text-green-700' ?>">
                                ₱<?= number_format($printingRemainingBalance, 2) ?>
                            </span>
                        </div>
                    </div>
                <?php elseif ($isStorePickup): ?>
                    <!-- Section: ORIGINAL ONLINE ORDER -->
                    <div class="border border-outline-variant/30 rounded-xl p-3 bg-surface-container-low/40 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-on-surface flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-primary">shopping_bag</span>
                                Original Online Order
                            </h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold <?= $isOnlinePaid ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= $isOnlinePaid ? 'PAID ONLINE' : 'UNPAID' ?>
                            </span>
                        </div>

                        <!-- Original Online Items List -->
                        <div class="border border-outline-variant/20 rounded-lg overflow-hidden bg-surface-container-lowest divide-y divide-outline-variant/10 text-xs">
                            <?php if (!empty($orderItems)): ?>
                                <?php foreach ($orderItems as $oit): ?>
                                    <?php if (empty($oit['is_pos_addition'])): ?>
                                        <div class="p-1.5 px-2.5 flex items-center justify-between">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <p class="font-semibold text-on-surface truncate"><?= esc($oit['product_name'] ?? 'Item') ?></p>
                                                <p class="text-[10px] text-outline font-mono">Qty: <?= (int) ($oit['quantity'] ?? 1) ?> × ₱<?= number_format((float) ($oit['unit_price'] ?? $oit['price'] ?? 0), 2) ?></p>
                                            </div>
                                            <span class="font-mono font-bold text-on-surface">₱<?= number_format((float) ($oit['line_total'] ?? (((int) ($oit['quantity'] ?? 1)) * ((float) ($oit['unit_price'] ?? $oit['price'] ?? 0)))), 2) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-2 text-center text-outline">Items details loaded from online order.</div>
                            <?php endif; ?>
                        </div>

                        <div class="flex justify-between items-center text-xs font-bold pt-0.5">
                            <span class="text-on-surface-variant">Original Online Total:</span>
                            <span class="font-mono <?= $isOnlinePaid ? 'text-green-700' : 'text-on-surface' ?>">
                                ₱<?= number_format($baseOnlineAmount, 2) ?> <?= $isOnlinePaid ? '(PAID)' : '' ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Section: ADDITIONAL IN-STORE ITEMS (or Walk-in Items) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-on-surface flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-secondary">add_shopping_cart</span>
                            <?= ($isStorePickup || $isPrintingPickup) ? 'Additional POS Items' : 'Sale Items' ?>
                        </h4>
                        <span class="text-[10px] text-outline" id="posItemsHintText"><?= ($isStorePickup || $isPrintingPickup) ? 'Add items at counter' : 'Selected items' ?></span>
                    </div>

                    <!-- Cart Items List with Quantity Controls and Remove -->
                    <div class="space-y-1.5 min-h-[110px] max-h-[220px] overflow-y-auto custom-scrollbar pr-0.5" id="posCartList">
                        <div id="posEmptyCartNotice" class="py-6 text-center text-on-surface-variant/70 flex flex-col items-center gap-1 border border-dashed border-outline-variant/40 rounded-xl">
                            <span class="material-symbols-outlined text-2xl text-outline-variant">remove_shopping_cart</span>
                            <span class="text-xs font-medium px-3 text-outline">
                                <?= ($isStorePickup || $isPrintingPickup)
                                    ? 'No additional in-store items. (You can complete pick-up without adding any extra items)' 
                                    : 'No items in sale yet. Click products from the catalog to add.' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Walk-in Customer Name / Note Field -->
                <?php if (!$isStorePickup && !$isPrintingPickup): ?>
                    <div class="pt-0.5">
                        <label for="posCustomerName" class="text-[11px] font-bold text-on-surface uppercase tracking-wider block mb-1">
                            Customer Name / Note (Optional)
                        </label>
                        <input type="text" id="posCustomerName" placeholder="e.g. Walk-in Student, Cash Customer" class="w-full px-3 py-1.5 bg-surface-container-low border border-outline-variant/40 rounded-xl text-xs font-medium focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    </div>
                <?php endif; ?>

                <!-- Settlement Breakdown & Amount Due Card -->
                <div class="pt-2 border-t border-outline-variant/20 space-y-2">
                    <?php if ($isCombinedPickup): ?>
                        <?php 
                        $orderCounterDue = $isOnlinePaid ? 0.0 : $baseOnlineAmount;
                        $combinedBaseDue = $orderCounterDue + $printingRemainingBalance;
                        ?>
                        <div class="space-y-1 text-xs text-on-surface-variant">
                            <div class="flex justify-between items-center">
                                <span>Product Order Due:</span>
                                <span class="font-mono font-bold <?= $orderCounterDue > 0 ? 'text-amber-800' : 'text-green-700' ?>">
                                    ₱<?= number_format($orderCounterDue, 2) ?> <?= $isOnlinePaid ? '(Paid Online)' : '' ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Printing Balance Due:</span>
                                <span class="font-mono font-bold <?= $printingRemainingBalance > 0 ? 'text-amber-800' : 'text-green-700' ?>">
                                    ₱<?= number_format($printingRemainingBalance, 2) ?> <?= $printingRemainingBalance <= 0 ? '(Paid Online)' : '' ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Additional POS Purchase:</span>
                                <span class="font-mono font-bold text-secondary" id="posAdditionalSubtotal">₱0.00</span>
                            </div>
                        </div>
                        <!-- Combined Amount to Collect Now -->
                        <div class="p-3 rounded-xl bg-purple-50 border border-purple-200">
                            <span class="text-xs font-bold text-purple-900 block mb-1">Combined Amount to Collect:</span>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-purple-700 font-medium">Total Collectible</span>
                                <span class="font-mono text-2xl font-black text-purple-900" id="posAmountDueNow">
                                    ₱<?= number_format($combinedBaseDue, 2) ?>
                                </span>
                            </div>
                        </div>
                    <?php elseif ($isPrintingPickup): ?>
                        <div class="space-y-1 text-xs text-on-surface-variant">
                            <div class="flex justify-between items-center">
                                <span>Print Request Total:</span>
                                <span class="font-mono font-medium">₱<?= number_format((float) ($printingRequest['total_price'] ?? 0), 2) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Online Paid Credit:</span>
                                <span class="font-mono font-bold text-green-700">-₱<?= number_format($printingOnlinePaid, 2) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Remaining Print Balance:</span>
                                <span class="font-mono font-bold text-on-surface" id="posPrintingBalance">₱<?= number_format($printingRemainingBalance, 2) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Additional POS Purchase:</span>
                                <span class="font-mono font-bold text-secondary" id="posAdditionalSubtotal">₱0.00</span>
                            </div>
                        </div>
                        <!-- Amount to collect now: remaining balance + in-store additions -->
                        <div class="p-3 rounded-xl bg-primary-container/30 border border-primary/30">
                            <span class="text-xs font-bold text-on-surface block mb-1">Amount to collect now:</span>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-on-surface-variant font-medium">Total Collectible</span>
                                <span class="font-mono text-2xl font-black text-primary" id="posAmountDueNow">
                                    ₱<?= number_format($printingRemainingBalance, 2) ?>
                                </span>
                            </div>
                        </div>
                    <?php elseif ($isStorePickup): ?>
                        <div class="space-y-1 text-xs text-on-surface-variant">
                            <div class="flex justify-between items-center">
                                <span>Original Online Order:</span>
                                <span class="font-mono font-medium">₱<?= number_format($baseOnlineAmount, 2) ?> <?= $isOnlinePaid ? '<span class="text-green-700 font-bold">(PAID)</span>' : '' ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Additional POS Purchase:</span>
                                <span class="font-mono font-bold text-secondary" id="posAdditionalSubtotal">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-1 border-t border-outline-variant/10">
                                <span>Combined Order Total:</span>
                                <span class="font-mono font-bold text-on-surface" id="posUpdatedGrandTotal">₱<?= number_format($originalTotal, 2) ?></span>
                            </div>
                        </div>
                        <!-- Amount to collect now: MUST NEVER CHARGE ONLINE PAID AMOUNT AGAIN -->
                        <div class="p-3 rounded-xl bg-secondary-container/30 border border-secondary/30">
                            <span class="text-xs font-bold text-on-surface block mb-1">Amount to collect now:</span>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-on-surface-variant font-medium">Total Collectible</span>
                                <span class="font-mono text-2xl font-black text-secondary" id="posAmountDueNow">
                                    ₱<?= number_format($isOnlinePaid ? 0.00 : $originalTotal, 2) ?>
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400">
                            <span>Subtotal:</span>
                            <span class="font-mono font-medium" id="posWalkinSubtotal">₱0.00</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-terra-green/10 border border-terra-green/20">
                            <span class="text-xs font-bold text-terra-green block mb-1">Total Amount Due:</span>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Due Now</span>
                                <span class="font-display text-2xl sm:text-3xl font-black text-terra-green font-mono" id="posAmountDueNow">₱0.00</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Payment Method Selector -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                        Payment Method
                    </label>
                    <div class="grid grid-cols-3 gap-1.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="pos_payment_method" value="cash" class="peer sr-only" checked>
                            <span class="flex items-center justify-center gap-1 py-2.5 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">payments</span>
                                Cash
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="pos_payment_method" value="gcash" class="peer sr-only">
                            <span class="flex items-center justify-center gap-1 py-2.5 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span>
                                GCash
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="pos_payment_method" value="card" class="peer sr-only">
                            <span class="flex items-center justify-center gap-1 py-2.5 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">credit_card</span>
                                Card
                            </span>
                        </label>
                    </div>

                    <!-- Cash Calculation Container -->
                    <div id="posCashCalculationContainer" class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl space-y-2.5 transition-all">
                        <div class="flex items-center justify-between gap-2">
                            <label for="posCashTendered" class="text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">Cash Tendered (₱):</label>
                            <input type="number" id="posCashTendered" step="any" min="0" placeholder="0.00" class="w-28 sm:w-32 py-1.5 px-2.5 text-right font-mono font-bold text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-terra-green">
                        </div>

                        <!-- Quick Cash Denominations -->
                        <div class="flex flex-wrap items-center gap-1 justify-end">
                            <button type="button" class="pos-denom-btn px-2.5 py-1 text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:border-terra-green hover:text-terra-green active:scale-95 transition-all" data-val="exact">Exact</button>
                            <button type="button" class="pos-denom-btn px-2.5 py-1 text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:border-terra-green hover:text-terra-green active:scale-95 transition-all" data-val="20">+20</button>
                            <button type="button" class="pos-denom-btn px-2.5 py-1 text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:border-terra-green hover:text-terra-green active:scale-95 transition-all" data-val="50">+50</button>
                            <button type="button" class="pos-denom-btn px-2.5 py-1 text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:border-terra-green hover:text-terra-green active:scale-95 transition-all" data-val="100">+100</button>
                            <button type="button" class="pos-denom-btn px-2.5 py-1 text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:border-terra-green hover:text-terra-green active:scale-95 transition-all" data-val="500">+500</button>
                            <button type="button" class="pos-denom-btn px-2.5 py-1 text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg hover:border-terra-green hover:text-terra-green active:scale-95 transition-all" data-val="1000">+1000</button>
                        </div>

                        <div class="flex justify-between items-center text-xs font-bold border-t border-slate-200 dark:border-slate-700 pt-2">
                            <span class="text-slate-500 dark:text-slate-400">Change Due:</span>
                            <span id="posChangeDueText" class="font-mono text-base font-bold text-terra-green">₱0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Error Notice Container -->
                <div id="posErrorMessage" class="hidden p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-xl text-xs font-medium"></div>

                <!-- Complete Button -->
                <button type="button" id="posSubmitBtn" class="w-full min-h-[50px] py-3.5 bg-terra-green hover:bg-terra-green-dark text-white rounded-2xl font-display text-sm font-extrabold shadow-lg shadow-terra-green/20 hover:shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span id="posSubmitBtnLabel"><?= $isPrintingPickup ? 'Complete Printing Pick-up' : ($isStorePickup ? 'Complete Store Pick-up' : 'Complete Walk-in Sale') ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Persistent Mobile / Tablet POS Running Total Floating Bar -->
<div id="posMobileRunningBar" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-surface-container-lowest/95 backdrop-blur border-t border-outline-variant/40 px-md py-sm shadow-2xl flex items-center justify-between">
    <div class="flex items-center gap-sm">
        <span class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-sm">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
        </span>
        <div>
            <span class="text-[10px] text-on-surface-variant font-medium uppercase tracking-wider block">Due Now</span>
            <span id="posMobileTotalText" class="font-mono text-title-md font-extrabold text-primary">₱0.00</span>
        </div>
    </div>
    <button type="button" onclick="document.getElementById('posSubmitBtn').scrollIntoView({behavior: 'smooth'})" class="px-md py-2 bg-primary text-on-primary rounded-xl text-xs font-bold hover:bg-primary/90 flex items-center gap-xs shadow-md active:scale-95 transition-all">
        <span>Proceed to Pay</span>
        <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
    </button>
</div>

<!-- Modal: Store Pick-up Order Selector -->
<?php
$poTotalCount = count($pickupOrders);
$poUnpaidCount = 0;
$poPaidCount = 0;
$poCombinedCount = 0;
foreach ($pickupOrders as $p_item) {
    if (($p_item['payment_status'] ?? '') === 'paid') {
        $poPaidCount++;
    } else {
        $poUnpaidCount++;
    }
    $cId = (int)($p_item['customer_id'] ?? 0);
    if (!empty($customerHasPrintingMap[$cId])) {
        $poCombinedCount++;
    }
}
?>
<div id="posPickupSelectorModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-5">
    <div class="bg-[#FAF6F0] dark:bg-slate-900 border border-[#E8E1D5] dark:border-slate-800 rounded-[28px] max-w-4xl w-full p-5 sm:p-7 shadow-2xl space-y-4 max-h-[92vh] flex flex-col font-sans">
        
        <!-- Top Telemetry & Header -->
        <div class="flex items-start justify-between border-b border-[#E8E1D5] dark:border-slate-800 pb-3.5 shrink-0">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-[#E8F0EA] text-[#2A6038] dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span><?= $poTotalCount ?> Orders Awaiting Handover</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Ready for Pick-up Orders</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Customer Pick-up Queue & Combined Printing Dispatch</p>
            </div>
            <button type="button" onclick="closePickupSelectorModal()" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-xl hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                <span>✕</span>
                <span class="text-[10px] bg-slate-200/80 dark:bg-slate-700 px-1.5 py-0.5 rounded font-mono">Esc</span>
            </button>
        </div>

        <!-- Search & Segmented Filter Controls -->
        <div class="space-y-2.5 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" 
                       id="posPickupFilterInput" 
                       placeholder="Search customer, phone, order #, or item..." 
                       oninput="filterPickupOrders(this.value)"
                       class="w-full pl-11 pr-20 py-2.5 bg-white dark:bg-slate-800 border border-[#E8E1D5] dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A7C59] shadow-2xs">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-600">Ctrl + K</span>
            </div>

            <!-- Segmented Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar text-xs">
                <button type="button" 
                        class="pos-po-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-[#4A7C59] text-white shadow-xs" 
                        onclick="setPickupTabFilter('', this)">
                    All Orders (<?= $poTotalCount ?>)
                </button>
                <button type="button" 
                        class="pos-po-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 hover:bg-amber-50" 
                        onclick="setPickupTabFilter('unpaid', this)">
                    ⚠️ Unpaid (<?= $poUnpaidCount ?>)
                </button>
                <button type="button" 
                        class="pos-po-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 hover:bg-emerald-50" 
                        onclick="setPickupTabFilter('paid', this)">
                    ✓ Paid Online (<?= $poPaidCount ?>)
                </button>
                <button type="button" 
                        class="pos-po-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800/60 hover:bg-purple-50" 
                        onclick="setPickupTabFilter('combined', this)">
                    🔗 Has Print Job (<?= $poCombinedCount ?>)
                </button>
            </div>
        </div>

        <!-- Boarding-Pass Ticket Queue List -->
        <div class="overflow-y-auto space-y-3 flex-1 pr-1 custom-scrollbar" id="posPickupOrdersList">
            <?php if (!empty($pickupOrders)): ?>
                <?php foreach ($pickupOrders as $po): ?>
                    <?php 
                    $poName = trim(($po['first_name'] ?? '') . ' ' . ($po['last_name'] ?? ''));
                    $poInitials = $poName !== '' ? mb_strtoupper(mb_substr($poName, 0, 2)) : 'OC';
                    $poPhone = trim((string) ($po['phone'] ?? ''));
                    $isPoPaid = ($po['payment_status'] ?? '') === 'paid';
                    $itemsSummary = trim((string) ($po['items_summary'] ?? ''));
                    $poCustId = (int) ($po['customer_id'] ?? 0);
                    $matchingPr = !empty($customerHasPrintingMap[$poCustId]) ? $customerHasPrintingMap[$poCustId][0] : null;
                    $statusTag = $isPoPaid ? 'paid' : 'unpaid';
                    if ($matchingPr) {
                        $statusTag .= ' combined';
                    }
                    $orderTotal = (float)($po['total_amount'] ?? 0);

                    // Combined financial calculations
                    $matchPrTotal = $matchingPr ? (float)($matchingPr['total_price'] ?? 0) : 0;
                    $matchPrDp = $matchingPr ? (float)($matchingPr['down_payment'] ?? 0) : 0;
                    $matchPrDue = max(0.0, round($matchPrTotal - $matchPrDp, 2));
                    $combinedCollect = ($isPoPaid ? 0.0 : $orderTotal) + $matchPrDue;

                    // Parse item chips cleanly
                    $rawItems = $itemsSummary !== '' ? $itemsSummary : 'Store Pick-up items';
                    $itemChips = array_filter(array_map('trim', explode(',', $rawItems)));
                    ?>
                    <div class="pos-pickup-row bg-white dark:bg-slate-800/90 border border-[#E8E1D5] dark:border-slate-700/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md transition-all space-y-3"
                         data-search="<?= strtolower(esc(($po['order_number'] ?? '') . ' ' . $poName . ' ' . $poPhone . ' ' . $itemsSummary)) ?>"
                         data-status="<?= $statusTag ?>">
                        
                        <!-- Ticket Top: Customer & Tag -->
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-[#E8F0EA] text-[#2A6038] dark:bg-emerald-950/70 dark:text-emerald-300 font-black text-sm flex items-center justify-center shrink-0 border border-emerald-200/80 dark:border-emerald-800/50 shadow-2xs">
                                    <?= esc($poInitials) ?>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-base text-slate-900 dark:text-white"><?= esc($poName ?: 'Online Customer') ?></span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            #<?= esc($po['order_number'] ?? $po['id']) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <?php if ($poPhone !== ''): ?>
                                            <a href="tel:<?= esc($poPhone) ?>" class="hover:text-[#4A7C59] flex items-center gap-0.5 font-mono">
                                                <span class="material-symbols-outlined text-[13px]">phone</span>
                                                <span><?= esc($poPhone) ?></span>
                                            </a>
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                        <?php endif; ?>
                                        <span><?= esc(date('M d, h:i A', strtotime($po['placed_at'] ?? 'now'))) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center gap-1 border border-slate-200 dark:border-slate-600">
                                    <span class="material-symbols-outlined text-[15px]">inventory_2</span>
                                    <span>Ready at Counter</span>
                                </span>
                            </div>
                        </div>

                        <!-- Ticket Middle: Items Manifest Chips -->
                        <div class="pt-2 border-t border-dashed border-[#E8E1D5] dark:border-slate-700/60 flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1">ITEMS:</span>
                            <?php foreach ($itemChips as $chip): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#FAF6F0] dark:bg-slate-700/50 text-slate-800 dark:text-slate-200 border border-[#E8E1D5] dark:border-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4A7C59]"></span>
                                    <span><?= esc($chip) ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Ticket Cross-Order Bridge (If Linked Print Job exists) -->
                        <?php if ($matchingPr): ?>
                            <div class="p-2.5 rounded-xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 flex items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 text-amber-900 dark:text-amber-200 truncate">
                                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[18px] shrink-0">link</span>
                                    <span class="font-bold">Linked Printing Ready:</span>
                                    <span class="truncate"><?= esc($matchingPr['file_name'] ?? ('#' . $matchingPr['request_number'])) ?></span>
                                    <span class="text-amber-400">•</span>
                                    <span class="font-mono font-bold text-amber-700 dark:text-amber-300">₱<?= number_format($matchPrDue, 2) ?> Bal Due</span>
                                </div>
                                <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider shrink-0">Combine Available</span>
                            </div>
                        <?php endif; ?>

                        <!-- Ticket Bottom: Financial Due & Action Buttons -->
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <?php if ($matchingPr): ?>
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-500 text-[18px]">payments</span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                            <?= $combinedCollect > 0 ? 'Collect at Counter:' : 'All Verified Paid Online' ?>
                                        </span>
                                        <span class="font-mono text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">
                                            ₱<?= number_format($combinedCollect, 2) ?> Total
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        (<?= $isPoPaid ? 'Order Paid Online' : '₱' . number_format($orderTotal, 2) . ' order' ?> + ₱<?= number_format($matchPrDue, 2) ?> print bal)
                                    </div>
                                <?php elseif ($isPoPaid): ?>
                                    <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                                        <span class="text-xs font-bold">Fully Paid Online (₱<?= number_format($orderTotal, 2) ?>)</span>
                                        <span class="text-xs font-medium text-slate-400">• Collect ₱0.00</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Verified payment • Release upon signature or ID check</div>
                                <?php else: ?>
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-500 text-[18px]">payments</span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Collect at Counter:</span>
                                        <span class="font-mono text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">
                                            ₱<?= number_format($orderTotal, 2) ?>
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Unpaid order • Collect cash or GCash at counter</div>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-2">
                                <?php if ($matchingPr): ?>
                                    <a href="<?= base_url('tenant/pos?order_id=' . $po['id']) ?>" 
                                       class="h-10 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center transition-all">
                                        Order Only
                                    </a>
                                    <a href="<?= base_url('tenant/pos?order_id=' . $po['id'] . '&printing_id=' . $matchingPr['id']) ?>" 
                                       class="h-10 px-4 bg-[#4A7C59] hover:bg-[#3D664A] text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                                        <span class="material-symbols-outlined text-[17px]">bolt</span>
                                        <span>Fulfill Both Together (₱<?= number_format($combinedCollect, 2) ?>)</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('tenant/pos?order_id=' . $po['id']) ?>" 
                                       class="h-10 px-4 bg-[#4A7C59] hover:bg-[#3D664A] text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                                        <span><?= $isPoPaid ? 'Handover Items' : 'Fulfill Order' ?></span>
                                        <span class="material-symbols-outlined text-[17px]">arrow_forward</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="py-14 text-center text-slate-400 text-xs flex flex-col items-center gap-2.5">
                    <span class="material-symbols-outlined text-5xl text-slate-300">inventory_2</span>
                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">No Orders in Pick-up Queue</span>
                    <span class="text-xs text-slate-400">All customer store pick-up orders have been fulfilled or are being packed.</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ticket Queue Footer -->
        <div class="flex items-center justify-between border-t border-[#E8E1D5] dark:border-slate-800 pt-3 shrink-0 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span id="posPickupFooterCount">Showing <?= $poTotalCount ?> active pick-up tickets • Register Terminal 01</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openPosQrScanner()" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 transition-all">
                    <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                    <span>Scan Pick-up QR</span>
                </button>
                <button type="button" onclick="closePickupSelectorModal()" class="px-4 py-1.5 rounded-xl border border-[#E8E1D5] dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 transition-colors">
                    Close [ESC]
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Printing Ready for Pick-up Selector -->
<?php
$prTotalCount = count($printingPickupRequests);
$prUnpaidCount = 0;
$prPaidCount = 0;
$prDpCount = 0;
$prCombinedCount = 0;
foreach ($printingPickupRequests as $pr_item) {
    $prTot = (float)($pr_item['total_price'] ?? 0);
    $prDp = (float)($pr_item['down_payment'] ?? 0);
    if ($prTot > 0 && $prDp >= $prTot) {
        $prPaidCount++;
    } elseif ($prDp > 0) {
        $prDpCount++;
    } else {
        $prUnpaidCount++;
    }
    $prCId = (int)($pr_item['customer_id'] ?? 0);
    if (!empty($customerHasOrderMap[$prCId])) {
        $prCombinedCount++;
    }
}
?>
<div id="posPrintingSelectorModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-5">
    <div class="bg-[#FAF6F0] dark:bg-slate-900 border border-[#E8E1D5] dark:border-slate-800 rounded-[28px] max-w-4xl w-full p-5 sm:p-7 shadow-2xl space-y-4 max-h-[92vh] flex flex-col font-sans">
        
        <!-- Top Telemetry & Header -->
        <div class="flex items-start justify-between border-b border-[#E8E1D5] dark:border-slate-800 pb-3.5 shrink-0">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-[#E8F0EA] text-[#2A6038] dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span><?= $prTotalCount ?> Print Jobs Awaiting Handover</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Printing Ready for Pick-up</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Customer Print Dispatch & Combined Order Queue</p>
            </div>
            <button type="button" onclick="closePrintingSelectorModal()" class="px-2.5 py-1 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-xl hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                <span>✕</span>
                <span class="text-[10px] bg-slate-200/80 dark:bg-slate-700 px-1.5 py-0.5 rounded font-mono">Esc</span>
            </button>
        </div>

        <!-- Search & Segmented Filter Controls -->
        <div class="space-y-2.5 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" 
                       id="posPrintingFilterInput" 
                       placeholder="Search customer, phone, request #, or file name..." 
                       oninput="filterPrintingRequests(this.value)"
                       class="w-full pl-11 pr-20 py-2.5 bg-white dark:bg-slate-800 border border-[#E8E1D5] dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#4A7C59] shadow-2xs">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-600">Ctrl + K</span>
            </div>

            <!-- Segmented Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar text-xs">
                <button type="button" 
                        class="pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-[#4A7C59] text-white shadow-xs" 
                        onclick="setPrintingTabFilter('', this)">
                    All Requests (<?= $prTotalCount ?>)
                </button>
                <button type="button" 
                        class="pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 hover:bg-amber-50" 
                        onclick="setPrintingTabFilter('unpaid', this)">
                    ⚠️ Unpaid (<?= $prUnpaidCount ?>)
                </button>
                <button type="button" 
                        class="pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 hover:bg-emerald-50" 
                        onclick="setPrintingTabFilter('paid', this)">
                    ✓ Paid Online (<?= $prPaidCount ?>)
                </button>
                <button type="button" 
                        class="pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/60 hover:bg-blue-50" 
                        onclick="setPrintingTabFilter('downpayment', this)">
                    💰 DP Paid (<?= $prDpCount ?>)
                </button>
                <button type="button" 
                        class="pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60 hover:bg-indigo-50" 
                        onclick="setPrintingTabFilter('combined', this)">
                    🛍️ Has Product Order (<?= $prCombinedCount ?>)
                </button>
            </div>
        </div>

        <!-- Printing Tickets Queue List -->
        <div class="overflow-y-auto space-y-3 flex-1 pr-1 custom-scrollbar" id="posPrintingRequestsList">
            <?php if (!empty($printingPickupRequests)): ?>
                <?php foreach ($printingPickupRequests as $pr): ?>
                    <?php 
                    $prCustName = trim(($pr['first_name'] ?? '') . ' ' . ($pr['last_name'] ?? ''));
                    $prInitials = $prCustName !== '' ? mb_strtoupper(mb_substr($prCustName, 0, 2)) : 'OC';
                    $prPhone = trim((string) ($pr['phone'] ?? ''));
                    $prDownPayment = (float) ($pr['down_payment'] ?? 0);
                    $prTotalPrice = (float) ($pr['total_price'] ?? 0);
                    $prIsFullyPaid = ($prTotalPrice > 0 && $prDownPayment >= $prTotalPrice);
                    $prRemainingDue = max(0.0, round($prTotalPrice - $prDownPayment, 2));
                    $prSpecs = (int)($pr['page_count'] ?? 1) . ' pgs • ' . (int)($pr['copies'] ?? 1) . ' cop • ' . strtoupper((string)($pr['color_mode'] ?? 'BW')) . ' • ' . esc($pr['paper_size'] ?? 'Letter');
                    $prCustId = (int) ($pr['customer_id'] ?? 0);
                    $matchingPo = !empty($customerHasOrderMap[$prCustId]) ? $customerHasOrderMap[$prCustId][0] : null;
                    
                    $prStatusTag = 'unpaid';
                    if ($prIsFullyPaid) {
                        $prStatusTag = 'paid';
                    } elseif ($prDownPayment > 0) {
                        $prStatusTag = 'downpayment';
                    }
                    if ($matchingPo) {
                        $prStatusTag .= ' combined';
                    }

                    // Combined financial calculations
                    $poAmount = $matchingPo ? (float)($matchingPo['total_amount'] ?? 0) : 0;
                    $isPoPaid = $matchingPo ? (($matchingPo['payment_status'] ?? '') === 'paid') : false;
                    $combinedCollect = $prRemainingDue + ($isPoPaid ? 0.0 : $poAmount);
                    ?>
                    <div class="pos-printing-row bg-white dark:bg-slate-800/90 border border-[#E8E1D5] dark:border-slate-700/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:shadow-md transition-all space-y-3"
                         data-search="<?= strtolower(esc(($pr['request_number'] ?? '') . ' ' . $prCustName . ' ' . $prPhone . ' ' . ($pr['file_name'] ?? ''))) ?>"
                         data-status="<?= $prStatusTag ?>">
                        
                        <!-- Ticket Top: Customer & Tag -->
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-[#E8F0EA] text-[#2A6038] dark:bg-emerald-950/70 dark:text-emerald-300 font-black text-sm flex items-center justify-center shrink-0 border border-emerald-200/80 dark:border-emerald-800/50 shadow-2xs">
                                    <?= esc($prInitials) ?>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-base text-slate-900 dark:text-white"><?= esc($prCustName ?: 'Online Customer') ?></span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            #<?= esc($pr['request_number']) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <?php if ($prPhone !== ''): ?>
                                            <a href="tel:<?= esc($prPhone) ?>" class="hover:text-[#4A7C59] flex items-center gap-0.5 font-mono">
                                                <span class="material-symbols-outlined text-[13px]">phone</span>
                                                <span><?= esc($prPhone) ?></span>
                                            </a>
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                        <?php endif; ?>
                                        <span><?= esc(date('M d, h:i A', strtotime($pr['created_at'] ?? 'now'))) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center gap-1 border border-slate-200 dark:border-slate-600">
                                    <span class="material-symbols-outlined text-[15px]">print</span>
                                    <span>Printed & Bound</span>
                                </span>
                            </div>
                        </div>

                        <!-- Ticket Middle: Document Details & Specs Chips -->
                        <div class="pt-2 border-t border-dashed border-[#E8E1D5] dark:border-slate-700/60 flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1">DOCUMENT:</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-[#FAF6F0] dark:bg-slate-700/50 text-slate-900 dark:text-white border border-[#E8E1D5] dark:border-slate-600">
                                <span class="material-symbols-outlined text-[15px] text-[#4A7C59]">description</span>
                                <span class="truncate max-w-[280px]" title="<?= esc($pr['file_name'] ?? 'Document.pdf') ?>">
                                    <?= esc($pr['file_name'] ?? 'Document.pdf') ?>
                                </span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                <?= esc($prSpecs) ?>
                            </span>
                        </div>

                        <!-- Ticket Cross-Order Bridge (If Linked Product Order exists) -->
                        <?php if ($matchingPo): ?>
                            <div class="p-2.5 rounded-xl bg-indigo-50/90 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 flex items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 text-indigo-900 dark:text-indigo-200 truncate">
                                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-[18px] shrink-0">shopping_bag</span>
                                    <span class="font-bold">Linked Product Order Ready:</span>
                                    <span class="truncate">#<?= esc($matchingPo['order_number']) ?> (<?= esc($matchingPo['items_summary'] ?? 'Items') ?>)</span>
                                    <span class="text-indigo-400">•</span>
                                    <span class="font-mono font-bold text-indigo-700 dark:text-indigo-300">
                                        <?= $isPoPaid ? 'Paid Online' : '₱' . number_format($poAmount, 2) . ' due' ?>
                                    </span>
                                </div>
                                <span class="text-[11px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider shrink-0">Combine Available</span>
                            </div>
                        <?php endif; ?>

                        <!-- Ticket Bottom: Financial Due & Action Buttons -->
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <?php if ($matchingPo): ?>
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-500 text-[18px]">payments</span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                            <?= $combinedCollect > 0 ? 'Collect at Counter:' : 'All Verified Paid Online' ?>
                                        </span>
                                        <span class="font-mono text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">
                                            ₱<?= number_format($combinedCollect, 2) ?> Total
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        (₱<?= number_format($prRemainingDue, 2) ?> print bal + <?= $isPoPaid ? 'Order Paid Online' : '₱' . number_format($poAmount, 2) . ' order' ?>)
                                    </div>
                                <?php elseif ($prIsFullyPaid): ?>
                                    <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                                        <span class="text-xs font-bold">Fully Paid Online (₱<?= number_format($prTotalPrice, 2) ?>)</span>
                                        <span class="text-xs font-medium text-slate-400">• Collect ₱0.00</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Paid via verified online transfer • Ready for handover</div>
                                <?php elseif ($prDownPayment > 0): ?>
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-500 text-[18px]">payments</span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Balance Due:</span>
                                        <span class="font-mono text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">
                                            ₱<?= number_format($prRemainingDue, 2) ?>
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">₱<?= number_format($prDownPayment, 2) ?> DP Paid Online of ₱<?= number_format($prTotalPrice, 2) ?> total</div>
                                <?php else: ?>
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-500 text-[18px]">payments</span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Collect at Counter:</span>
                                        <span class="font-mono text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">
                                            ₱<?= number_format($prTotalPrice, 2) ?>
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Unpaid printing request • Cash or GCash on pickup</div>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-2">
                                <?php if ($matchingPo): ?>
                                    <a href="<?= base_url('tenant/pos?printing_id=' . $pr['id']) ?>" 
                                       class="h-10 px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center transition-all">
                                        Print Only
                                    </a>
                                    <a href="<?= base_url('tenant/pos?order_id=' . $matchingPo['id'] . '&printing_id=' . $pr['id']) ?>" 
                                       class="h-10 px-4 bg-[#4A7C59] hover:bg-[#3D664A] text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                                        <span class="material-symbols-outlined text-[17px]">bolt</span>
                                        <span>Fulfill Both Together (₱<?= number_format($combinedCollect, 2) ?>)</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('tenant/pos?printing_id=' . $pr['id']) ?>" 
                                       class="h-10 px-4 bg-[#4A7C59] hover:bg-[#3D664A] text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                                        <span>Handover Print Job</span>
                                        <span class="material-symbols-outlined text-[17px]">arrow_forward</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="py-14 text-center text-slate-400 text-xs flex flex-col items-center gap-2.5">
                    <span class="material-symbols-outlined text-5xl text-slate-300">print_disabled</span>
                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">No Printing Requests in Queue</span>
                    <span class="text-xs text-slate-400">All customer printing requests have been fulfilled or are in production.</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ticket Queue Footer -->
        <div class="flex items-center justify-between border-t border-[#E8E1D5] dark:border-slate-800 pt-3 shrink-0 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span id="posPrintingFooterCount">Showing <?= $prTotalCount ?> active printing tickets • Production Hub 01</span>
            </div>
            <button type="button" onclick="closePrintingSelectorModal()" class="px-4 py-1.5 rounded-xl border border-[#E8E1D5] dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 transition-colors">
                Close [ESC]
            </button>
        </div>
    </div>
</div>

<!-- Receipt / Success Modal -->
<div id="posSuccessModal" class="hidden fixed inset-0 bg-black/65 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="w-full max-w-[420px] my-auto space-y-3">
        <!-- Floating Success Notification Banner -->
        <div class="bg-emerald-600 text-white px-4 py-2.5 rounded-2xl shadow-lg flex items-center justify-between text-xs font-bold animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span id="posSuccessModalMessage">Sale Completed Successfully!</span>
            </div>
            <span class="bg-white/20 px-2 py-0.5 rounded-full text-[10px] tracking-wider uppercase font-mono">Paid & Verified</span>
        </div>

        <!-- The Exact Receipt Ticket matching Scan Image -->
        <div id="posReceiptSummary" class="bg-white text-slate-900 border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xl space-y-3 max-h-[75vh] overflow-y-auto custom-scrollbar font-sans text-xs">
            <!-- Receipt lines populated dynamically -->
        </div>

        <!-- Action Control Buttons -->
        <div class="flex items-center gap-2.5 pt-1">
            <button type="button" id="posPrintReceiptBtn" class="flex-1 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition-all active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Print Receipt
            </button>
            <button type="button" id="posNewSaleBtn" class="flex-1 py-3 bg-primary hover:bg-primary/90 text-on-primary rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition-all active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">autorenew</span>
                Next Sale
            </button>
        </div>
    </div>
</div>

<!-- Customer QR Scanner Modal -->
<div id="posQrScannerModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-md">
    <div class="glass-card bg-surface-container-lowest rounded-2xl p-lg max-w-md w-full border border-outline-variant/30 shadow-2xl space-y-md">
        <div class="flex justify-between items-center border-b border-outline-variant/20 pb-sm">
            <div class="flex items-center gap-xs text-secondary font-bold">
                <span class="material-symbols-outlined text-2xl">qr_code_scanner</span>
                <div>
                    <h3 class="text-title-md text-on-surface">Scan Pick-up &amp; Order QR</h3>
                    <p class="text-[11px] text-on-surface-variant font-normal">Live camera, scan from photo, or enter code</p>
                </div>
            </div>
            <button type="button" onclick="closeQrScannerModal()" class="text-on-surface-variant hover:text-on-surface p-1 rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="posScanFeedback" class="hidden text-xs rounded-xl p-sm font-medium"></div>

        <!-- Scan Mode Tabs -->
        <div class="flex items-center gap-xs p-1 bg-surface-container-low rounded-xl border border-outline-variant/20">
            <button type="button" id="posTabCameraBtn" onclick="switchPosScanMode('camera')" class="flex-1 py-1.5 px-sm rounded-lg text-xs font-bold transition-all bg-surface-container-lowest text-primary shadow-sm flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                <span>Live Camera</span>
            </button>
            <button type="button" id="posTabFileBtn" onclick="switchPosScanMode('file')" class="flex-1 py-1.5 px-sm rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span>
                <span>Scan from Photo</span>
            </button>
        </div>

        <!-- Mode 1: Live Camera (Unobstructed, No Dark Box) -->
        <div id="posCameraContainer" class="space-y-sm">
            <div class="rounded-2xl overflow-hidden bg-black aspect-square relative flex items-center justify-center border border-outline-variant/30 shadow-inner">
                <div id="posQrReader" class="w-full h-full"></div>
                <!-- Clean, non-blocking transparent corner reticle -->
                <div class="qr-viewfinder-overlay pointer-events-none absolute inset-4 border border-white/20 rounded-xl">
                    <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-secondary rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-secondary rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-secondary rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-secondary rounded-br-lg"></div>
                    <div class="pos-qr-laser-line"></div>
                </div>
                <div id="posQrCameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-white/70 p-md text-center bg-black/80">
                    <span class="material-symbols-outlined text-4xl mb-1 text-secondary">photo_camera</span>
                    <span class="text-xs font-semibold">Starting camera...</span>
                    <span class="text-[10px] opacity-70 mt-1">Please allow camera permissions if prompted</span>
                </div>
            </div>
            <p class="text-xs text-center text-on-surface-variant font-medium">Point camera directly at customer phone or ticket QR</p>
        </div>

        <!-- Mode 2: Scan from Photo / Image File -->
        <div id="posFileContainer" class="hidden space-y-sm">
            <label for="posPhotoUpload" class="flex flex-col items-center justify-center p-xl border-2 border-dashed border-secondary/40 hover:border-secondary rounded-2xl bg-secondary/5 hover:bg-secondary/10 transition-all cursor-pointer text-center group">
                <span class="p-3 bg-secondary/10 text-secondary rounded-2xl material-symbols-outlined text-3xl group-hover:scale-110 transition-transform mb-2">image_search</span>
                <span class="text-xs font-bold text-on-surface">Click to Select QR Photo / Image</span>
                <span class="text-[11px] text-on-surface-variant mt-1">Upload picture of completed order or ticket</span>
                <input id="posPhotoUpload" type="file" accept="image/*" class="hidden" onchange="handlePosPhotoUpload(this)">
            </label>
            <p id="posPhotoScanStatus" class="text-xs text-center text-on-surface-variant font-medium hidden"></p>
        </div>

        <!-- Manual Lookup Fallback -->
        <div class="space-y-xs pt-xs border-t border-outline-variant/20">
            <label for="posManualOrderCode" class="text-xs font-bold text-on-surface uppercase tracking-wider block">
                Manual Order / Request Code or ID
            </label>
            <div class="flex gap-xs">
                <input type="text" id="posManualOrderCode" placeholder="e.g. ORD-88102, PR-190, or 190" class="flex-1 px-md py-2 bg-surface-container-low border border-outline-variant rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-primary focus:border-primary">
                <button type="button" id="posManualLookupBtn" onclick="verifyManualOrderCode()" class="bg-primary hover:bg-primary/90 text-on-primary px-md py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[16px]">search</span>
                    <span>Verify</span>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-end pt-xs">
            <button type="button" onclick="closeQrScannerModal()" class="px-md py-2 rounded-xl border border-outline-variant text-xs font-semibold text-on-surface hover:bg-surface-container">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Completed Order / Item Action Modal (Returned vs Add) -->
<div id="posCompletedOrderModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="glass-card bg-surface-container-lowest rounded-3xl p-5 sm:p-7 max-w-lg w-full border border-outline-variant/30 shadow-2xl space-y-5 my-auto">
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-outline-variant/20 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-600 flex items-center justify-center shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl">history</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base sm:text-lg font-extrabold text-on-surface">Completed Order Scanned</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            COMPLETED
                        </span>
                    </div>
                    <p class="text-xs text-on-surface-variant font-medium mt-0.5" id="posCompletedModalSubtitle">
                        Na-record na ang transaksyong ito. Pumili ng aksyon sa ibaba:
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeCompletedOrderModal()" class="text-on-surface-variant hover:text-on-surface p-1.5 rounded-full hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Recorded Receipt Overview Card -->
        <div class="bg-surface-container-low rounded-2xl p-4 border border-outline-variant/25 space-y-3">
            <!-- Meta details row -->
            <div class="grid grid-cols-2 gap-2 text-xs border-b border-outline-variant/20 pb-3">
                <div>
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider block">Reference / Receipt</span>
                    <span class="font-mono font-extrabold text-sm text-on-surface" id="posCompletedRefText">#ORD-00000</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider block">Customer</span>
                    <span class="font-bold text-xs text-on-surface truncate max-w-[160px] inline-block" id="posCompletedCustomerText">Counter Customer</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider block">Completed Date</span>
                    <span class="font-medium text-xs text-on-surface-variant" id="posCompletedDateText">Sep 20, 2026</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider block">Total Recorded</span>
                    <span class="font-mono font-black text-sm text-primary" id="posCompletedTotalText">₱0.00</span>
                </div>
            </div>

            <!-- Itemized List Header -->
            <div class="flex items-center justify-between text-[11px] font-bold text-outline uppercase tracking-wider">
                <span>Recorded Items (<span id="posCompletedItemCount">0</span>)</span>
                <span>Subtotal</span>
            </div>

            <!-- Itemized List Scrollbox -->
            <div id="posCompletedItemsList" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                <!-- Dynamically populated rows -->
            </div>
        </div>

        <!-- Feedback Alert Container -->
        <div id="posCompletedModalFeedback" class="hidden text-xs rounded-xl p-3 font-semibold"></div>

        <!-- 2 Primary Touch Action Buttons (Returned vs Add) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <!-- Action 1: RETURNED -->
            <button type="button" 
                    id="posBtnActionReturn"
                    onclick="confirmReturnScannedOrder()"
                    class="p-4 rounded-2xl border-2 border-purple-500/40 bg-purple-50 hover:bg-purple-100 text-purple-950 flex flex-col items-center text-center gap-1 transition-all active:scale-95 group cursor-pointer shadow-xs hover:shadow-md">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">assignment_return</span>
                </div>
                <span class="font-black text-sm text-purple-900 tracking-wide mt-1">Returned</span>
                <span class="text-[11px] text-purple-700 font-medium leading-tight">
                    I-mark as returned &amp; ibalik sa inventory stock
                </span>
            </button>

            <!-- Action 2: ADD TO POS -->
            <button type="button" 
                    id="posBtnActionAdd"
                    onclick="addScannedOrderToPos()"
                    class="p-4 rounded-2xl border-2 border-primary/40 bg-primary/10 hover:bg-primary/20 text-primary flex flex-col items-center text-center gap-1 transition-all active:scale-95 group cursor-pointer shadow-xs hover:shadow-md">
                <div class="w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">add_shopping_cart</span>
                </div>
                <span class="font-black text-sm tracking-wide mt-1">Add to POS</span>
                <span class="text-[11px] text-on-surface-variant font-medium leading-tight">
                    I-load ang lahat ng items sa aktibong POS cart
                </span>
            </button>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/20 text-xs">
            <a id="posCompletedViewReceiptLink" href="#" target="_blank" class="text-primary hover:underline font-bold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                <span>View Full Receipt</span>
            </a>
            <button type="button" onclick="closeCompletedOrderModal()" class="px-4 py-2 rounded-xl border border-outline-variant text-xs font-semibold text-on-surface hover:bg-surface-container">
                Cancel
            </button>
        </div>
    </div>
</div>

<style>
/* Remove Html5Qrcode blocking dark region box in POS */
#posQrReader #qr-shaded-region {
    display: none !important;
}
#posQrReader {
    border: none !important;
}
#posQrReader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 0.875rem !important;
}
.pos-qr-laser-line {
    position: absolute;
    left: 10px;
    right: 10px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #eab308, transparent);
    box-shadow: 0 0 10px #eab308;
    animation: posQrLaserScan 2s ease-in-out infinite alternate;
}
@keyframes posQrLaserScan {
    0% { top: 12px; }
    100% { top: calc(100% - 14px); }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    const isStorePickup = <?= $isStorePickup ? 'true' : 'false' ?>;
    const storePickupOrderId = <?= (int) ($order['id'] ?? 0) ?>;
    const onlineOrderBaseAmount = <?= (float) $baseOnlineAmount ?>;
    const isOnlinePaid = <?= $isOnlinePaid ? 'true' : 'false' ?>;

    const isPrintingPickup = <?= !empty($printingRequest) ? 'true' : 'false' ?>;
    const printingRequestId = <?= (int) ($printingRequest['id'] ?? 0) ?>;
    const printingRemainingBalance = <?= (float) ($printingRemainingBalance ?? 0) ?>;
    const printingOnlinePaid = <?= (float) ($printingOnlinePaid ?? 0) ?>;

    const isCombinedPickup = isStorePickup && isPrintingPickup;

    const orderItemsSummary = <?= !empty($orderItems) ? json_encode(array_values(array_map(static fn($oit) => [
        'name' => (string) ($oit['product_name'] ?? 'Product'),
        'qty' => (int) ($oit['quantity'] ?? 1),
        'variant' => (string) ($oit['variant_label'] ?? ''),
        'price' => (float) ($oit['unit_price'] ?? 0),
        'total' => (float) ($oit['line_total'] ?? 0),
    ], array_filter($orderItems, static fn($oit) => empty($oit['is_pos_addition']))))) : '[]' ?>;

    const printingItemSummary = <?= !empty($printingRequest) ? json_encode([
        'file_name' => (string) ($printingRequest['file_name'] ?? 'Document.pdf'),
        'request_number' => (string) ($printingRequest['request_number'] ?? ''),
        'specs' => trim(($printingRequest['paper_size'] ?? '') . ' • ' . ($printingRequest['color_mode'] ?? '') . (!empty($printingRequest['binding_option']) ? ' • ' . $printingRequest['binding_option'] : '')),
        'total' => (float) ($printingRequest['total_price'] ?? 0),
    ]) : 'null' ?>;

    const BASE_URL = '<?= rtrim(site_url(), '/') ?>';
    const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    let CSRF_HASH_VAL = '<?= csrf_hash() ?>';

    <?php
    $shopAddressStr = implode(', ', array_filter([
        $shop['street'] ?? '',
        $shop['barangay'] ?? '',
        $shop['address_line'] ?? '',
        $shop['city'] ?? 'Polomolok',
        $shop['province'] ?? 'South Cotabato',
    ])) ?: '8 Pioneer Avenue, Poblacion, Polomolok, South Cotabato';
    $cashierDisplayName = session()->get('user_name') ?? 'Sarah Jenkins';
    $printingCustomerName = !empty($printingRequest) ? trim(($printingRequest['first_name'] ?? '') . ' ' . ($printingRequest['last_name'] ?? '')) : '';
    ?>
    const SHOP_INFO = {
        name: <?= json_encode($shop['shop_name'] ?? 'InkMaster') ?>,
        address: <?= json_encode($shopAddressStr) ?>,
        phone: <?= json_encode($shop['phone_number'] ?? '') ?>,
        cashier: <?= json_encode($cashierDisplayName) ?>,
    };
    const ORDER_CUSTOMER_NAME = <?= json_encode(!empty($customerFullName) ? $customerFullName : (!empty($printingCustomerName) ? $printingCustomerName : '')) ?>;

    let cart = []; // Array of { product_id, name, price, stock_quantity, quantity, sku, image_url }
    let currentCategoryId = '';
    let searchDebounceTimer = null;
    let catalogProducts = [];

    // DOM Elements
    const searchInput = document.getElementById('posProductSearch');
    const clearSearchBtn = document.getElementById('posClearSearch');
    const searchResultsBox = document.getElementById('posSearchResults');
    const quickCatalogBox = document.getElementById('posQuickCatalog');
    const cartListBox = document.getElementById('posCartList');
    const emptyCartNotice = document.getElementById('posEmptyCartNotice');
    const cartCountBadge = document.getElementById('posCartCountBadge');
    const amountDueNowEl = document.getElementById('posAmountDueNow');
    const additionalSubtotalEl = document.getElementById('posAdditionalSubtotal');
    const updatedGrandTotalEl = document.getElementById('posUpdatedGrandTotal');
    const walkinSubtotalEl = document.getElementById('posWalkinSubtotal');
    const cashInput = document.getElementById('posCashTendered');
    const changeDueText = document.getElementById('posChangeDueText');
    const submitBtn = document.getElementById('posSubmitBtn');
    const submitBtnLabel = document.getElementById('posSubmitBtnLabel');
    const errBox = document.getElementById('posErrorMessage');

    const successModal = document.getElementById('posSuccessModal');
    const successMessage = document.getElementById('posSuccessModalMessage');
    const receiptSummary = document.getElementById('posReceiptSummary');
    const printReceiptBtn = document.getElementById('posPrintReceiptBtn');
    const newSaleBtn = document.getElementById('posNewSaleBtn');

    // Currency Formatter
    function formatMoney(amount) {
        return '₱' + (parseFloat(amount) || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function esc(str) {
        return String(str || '').replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[m]);
    }

    // Load Catalog with optional category & query
    function loadCatalog(query = '', categoryId = '') {
        quickCatalogBox.innerHTML = `
            <div class="col-span-full py-xl text-center text-on-surface-variant/70 flex flex-col items-center gap-xs">
                <span class="material-symbols-outlined text-3xl animate-spin text-primary">progress_activity</span>
                <span class="text-xs font-medium">Loading catalog products...</span>
            </div>
        `;

        let base = (window.BASE_URL || '<?= rtrim(base_url(), "/") ?>/').replace(/^http:\/\//i, window.location.protocol + '//');
        let url = `${base}tenant/pos/search-products?q=${encodeURIComponent(query)}`;
        if (categoryId) {
            url += `&category_id=${encodeURIComponent(categoryId)}`;
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.products && data.products.length > 0) {
                catalogProducts = data.products;
                renderProductCards(data.products);
            } else {
                quickCatalogBox.innerHTML = '<div class="col-span-full py-lg text-center text-outline text-xs">No products found matching your selection.</div>';
            }
        })
        .catch(err => {
            console.error('POS Catalog load error:', err);
            quickCatalogBox.innerHTML = '<div class="col-span-full py-lg text-center text-error text-xs">Failed to load shop catalog.</div>';
        });
    }

    // Render modern product cards grid
    function renderProductCards(products) {
        try {
            quickCatalogBox.innerHTML = products.map(p => {
                const stockQty = parseInt(p.stock_quantity, 10) || 0;
                const outOfStock = stockQty <= 0;
                const isLowStock = stockQty > 0 && stockQty <= 5;
                const cartItem = cart.find(x => x.product_id === p.id);
                const inCartQty = cartItem ? cartItem.quantity : 0;
                const priceFormatted = formatMoney(p.price);

                return `
                    <div class="pos-product-card bg-surface-container-lowest hover:bg-surface-container-low border ${inCartQty > 0 ? 'border-primary ring-1 ring-primary/40' : 'border-outline-variant/30'} rounded-2xl p-2.5 sm:p-3 flex flex-col justify-between gap-2 transition-all hover:shadow-md group cursor-pointer relative" data-id="${p.id}" ${outOfStock ? 'style="opacity: 0.6; cursor: not-allowed;"' : ''}>
                        <!-- Product Image -->
                        <div class="w-full aspect-square rounded-xl bg-slate-50 dark:bg-slate-900/60 overflow-hidden flex items-center justify-center relative p-1.5">
                            ${p.image_url 
                                ? `<img src="${p.image_url}" class="w-full h-full object-contain group-hover:scale-105 transition-transform" alt="${esc(p.name)}" onerror="this.parentElement.innerHTML='<div class=\\'text-primary/40 flex flex-col items-center gap-1\\'><span class=\\'material-symbols-outlined text-3xl sm:text-4xl\\'>inventory_2</span><span class=\\'text-[9px] uppercase font-bold text-outline\\'>No Image</span></div>'">` 
                                : `<div class="text-primary/40 flex flex-col items-center gap-1"><span class="material-symbols-outlined text-3xl sm:text-4xl">inventory_2</span><span class="text-[9px] uppercase font-bold text-outline">No Image</span></div>`}
                            
                            <!-- In-cart Badge -->
                            ${inCartQty > 0 ? `
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-primary text-on-primary shadow-xs flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[12px]">shopping_bag</span>
                                    ${inCartQty} in cart
                                </span>
                            ` : ''}

                            <!-- Stock Badge Overlay -->
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-bold shadow-xs ${
                                outOfStock ? 'bg-error text-on-error' : (isLowStock ? 'bg-amber-500 text-white' : 'bg-surface-container-lowest/90 text-on-surface')
                            }">
                                ${outOfStock ? 'Out of Stock' : (isLowStock ? `Low: ${stockQty}` : `Stock: ${stockQty}`)}
                            </span>
                        </div>

                        <!-- Product Details -->
                        <div class="space-y-0.5 min-w-0">
                            <p class="text-xs font-bold text-on-surface truncate" title="${esc(p.name)}">${esc(p.name)}</p>
                            <p class="text-[10px] text-outline truncate font-mono">SKU: ${esc(p.sku || 'N/A')}</p>
                            <p class="text-sm sm:text-body-md font-mono font-bold text-primary">${priceFormatted}</p>
                        </div>

                        <!-- Add Button -->
                        <button type="button" 
                                class="pos-card-add-btn w-full py-1.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1 transition-all shadow-2xs ${
                                    outOfStock ? 'bg-outline/20 text-outline cursor-not-allowed' : (inCartQty > 0 ? 'bg-primary/10 text-primary border border-primary/30 hover:bg-primary hover:text-on-primary' : 'bg-primary text-on-primary hover:bg-primary/90 active:scale-95')
                                }" 
                                data-id="${p.id}" 
                                ${outOfStock ? 'disabled' : ''}>
                            <span class="material-symbols-outlined text-[16px]">${inCartQty > 0 ? 'add' : 'add_shopping_cart'}</span>
                            <span>${inCartQty > 0 ? 'Add More' : 'Add'}</span>
                        </button>
                    </div>
                `;
            }).join('');
        } catch (e) {
            console.error('Error rendering product cards:', e);
            quickCatalogBox.innerHTML = '<div class="col-span-full py-lg text-center text-error text-xs">Failed to display products.</div>';
        }

        // Attach click handlers to cards and add buttons
        quickCatalogBox.querySelectorAll('.pos-product-card').forEach(card => {
            card.addEventListener('click', function(e) {
                const id = parseInt(this.dataset.id);
                const prod = catalogProducts.find(x => x.id === id);
                if (prod && prod.stock_quantity > 0) {
                    addToCart(prod);
                }
            });
        });

        quickCatalogBox.querySelectorAll('.pos-card-add-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const id = parseInt(this.dataset.id);
                const prod = catalogProducts.find(x => x.id === id);
                if (prod && prod.stock_quantity > 0) {
                    addToCart(prod);
                }
            });
        });
    }

    function updateCatalogBadges() {
        if (!catalogProducts || catalogProducts.length === 0) return;
        const currentScroll = quickCatalogBox ? quickCatalogBox.scrollTop : 0;
        renderProductCards(catalogProducts);
        if (quickCatalogBox) quickCatalogBox.scrollTop = currentScroll;
    }

    // Category chips click handler
    document.querySelectorAll('.pos-cat-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.querySelectorAll('.pos-cat-chip').forEach(c => {
                c.className = 'pos-cat-chip px-3 py-1.5 rounded-full font-medium transition-all shrink-0 bg-surface-container-low text-on-surface-variant hover:bg-surface-container border border-outline-variant/30';
            });
            this.className = 'pos-cat-chip px-3 py-1.5 rounded-full font-bold transition-all shrink-0 bg-primary text-on-primary shadow-xs';

            currentCategoryId = this.dataset.category || '';
            const activeCategoryText = document.getElementById('posActiveCategoryText');
            if (activeCategoryText) {
                activeCategoryText.textContent = this.dataset.category ? `• ${this.textContent.trim()}` : '• All Products';
            }

            const query = searchInput ? searchInput.value.trim() : '';
            loadCatalog(query, currentCategoryId);
        });
    });

    // Keyboard shortcut '/' to search
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Payment method toggle (show/hide cash calculations)
    document.querySelectorAll('input[name="pos_payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cashCalc = document.getElementById('posCashCalculationContainer');
            if (cashCalc) {
                if (this.value === 'cash') {
                    cashCalc.classList.remove('hidden');
                } else {
                    cashCalc.classList.add('hidden');
                }
            }
        });
    });

    // Search input handler
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounceTimer);
            const query = e.target.value.trim();

            if (query.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
                searchResultsBox.classList.add('hidden');
                searchResultsBox.innerHTML = '';
            }

            searchDebounceTimer = setTimeout(() => {
                loadCatalog(query, currentCategoryId);
            }, 250);
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            searchResultsBox.classList.add('hidden');
            searchResultsBox.innerHTML = '';
            loadCatalog('', currentCategoryId);
        });
    }

    // Add product to cart
    function addToCart(prod) {
        hideError();
        const existing = cart.find(x => x.product_id === prod.id);
        if (existing) {
            if (existing.quantity < prod.stock_quantity) {
                existing.quantity += 1;
            } else {
                showError(`Cannot add more than ${prod.stock_quantity} units available in inventory.`);
                return;
            }
        } else {
            cart.push({
                product_id: prod.id,
                name: prod.name,
                price: parseFloat(prod.price),
                stock_quantity: parseInt(prod.stock_quantity),
                sku: prod.sku || '',
                quantity: 1
            });
        }
        renderCart();
    }

    window.posUpdateQty = function(productId, delta) {
        hideError();
        const item = cart.find(x => x.product_id === productId);
        if (!item) return;

        const newQty = item.quantity + delta;
        if (newQty <= 0) {
            cart = cart.filter(x => x.product_id !== productId);
        } else if (newQty <= item.stock_quantity) {
            item.quantity = newQty;
        } else {
            showError(`Cannot exceed available inventory of ${item.stock_quantity} units.`);
        }
        renderCart();
    };

    window.posRemoveItem = function(productId) {
        hideError();
        cart = cart.filter(x => x.product_id !== productId);
        renderCart();
    };

    window.posClearAllCart = function() {
        if (cart.length === 0) return;
        cart = [];
        hideError();
        renderCart();
    };

    // Render cart items & totals
    function renderCart() {
        const totalItemsCount = cart.reduce((acc, it) => acc + it.quantity, 0);
        cartCountBadge.textContent = `${totalItemsCount} ${totalItemsCount === 1 ? 'item' : 'items'}`;

        if (cart.length === 0) {
            cartListBox.innerHTML = `
                <div id="posEmptyCartNotice" class="py-6 text-center text-on-surface-variant/70 flex flex-col items-center gap-1 border border-dashed border-outline-variant/40 rounded-xl">
                    <span class="material-symbols-outlined text-2xl text-outline-variant">remove_shopping_cart</span>
                    <span class="text-xs font-medium px-3 text-outline">
                        ${isStorePickup ? 'No additional in-store items. (You can complete store pickup without adding any extra items)' : 'No items in sale yet. Click products from the catalog to add.'}
                    </span>
                </div>
            `;
        } else {
            cartListBox.innerHTML = cart.map(it => {
                const lineTotal = it.price * it.quantity;
                return `
                    <div class="p-2 bg-surface-container-low rounded-xl flex items-center justify-between gap-2 border border-outline-variant/20">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-on-surface truncate">${esc(it.name)}</p>
                            <p class="text-[10px] text-outline font-mono">${formatMoney(it.price)} ea • Line: <span class="font-bold text-on-surface">${formatMoney(lineTotal)}</span></p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="inline-flex items-center border border-outline-variant/40 rounded-lg overflow-hidden bg-surface-container-lowest">
                                <button type="button" onclick="posUpdateQty(${it.product_id}, -1)" class="w-5 h-5 flex items-center justify-center font-bold text-xs hover:bg-surface-container">-</button>
                                <span class="px-1.5 font-mono font-bold text-xs">${it.quantity}</span>
                                <button type="button" onclick="posUpdateQty(${it.product_id}, 1)" class="w-5 h-5 flex items-center justify-center font-bold text-xs hover:bg-surface-container">+</button>
                            </div>
                            <button type="button" onclick="posRemoveItem(${it.product_id})" class="p-1 text-error hover:bg-error/10 rounded-lg transition-colors" title="Remove">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        const additionalSubtotal = cart.reduce((acc, it) => acc + (it.price * it.quantity), 0);

        if (isCombinedPickup) {
            if (additionalSubtotalEl) additionalSubtotalEl.textContent = formatMoney(additionalSubtotal);
            const orderDue = isOnlinePaid ? 0 : onlineOrderBaseAmount;
            const combinedDue = orderDue + printingRemainingBalance + additionalSubtotal;
            if (amountDueNowEl) amountDueNowEl.textContent = formatMoney(combinedDue);
            calculateChange(combinedDue);
        } else if (isStorePickup) {
            if (additionalSubtotalEl) additionalSubtotalEl.textContent = formatMoney(additionalSubtotal);
            const updatedGrandTotal = onlineOrderBaseAmount + additionalSubtotal;
            if (updatedGrandTotalEl) updatedGrandTotalEl.textContent = formatMoney(updatedGrandTotal);
            const dueNow = isOnlinePaid ? additionalSubtotal : updatedGrandTotal;
            if (amountDueNowEl) amountDueNowEl.textContent = formatMoney(dueNow);
            calculateChange(dueNow);
        } else if (isPrintingPickup) {
            if (additionalSubtotalEl) additionalSubtotalEl.textContent = formatMoney(additionalSubtotal);
            const dueNow = printingRemainingBalance + additionalSubtotal;
            if (amountDueNowEl) amountDueNowEl.textContent = formatMoney(dueNow);
            calculateChange(dueNow);
        } else {
            if (walkinSubtotalEl) walkinSubtotalEl.textContent = formatMoney(additionalSubtotal);
            if (amountDueNowEl) amountDueNowEl.textContent = formatMoney(additionalSubtotal);
            calculateChange(additionalSubtotal);
        }
        const mobileTotal = document.getElementById('posMobileTotalText');
        if (mobileTotal && amountDueNowEl) mobileTotal.textContent = amountDueNowEl.textContent;

        updateCatalogBadges();
    }

    // Cash calculations
    function calculateChange(dueAmount) {
        const tendered = parseFloat(cashInput.value) || 0;
        const change = Math.max(0, tendered - dueAmount);
        changeDueText.textContent = formatMoney(change);
    }

    if (cashInput) {
        cashInput.addEventListener('input', () => {
            const due = getDueAmount();
            calculateChange(due);
        });
    }

    document.querySelectorAll('.pos-denom-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const val = this.dataset.val;
            const due = getDueAmount();
            if (val === 'exact') {
                cashInput.value = due.toFixed(2);
            } else {
                const addVal = parseFloat(val) || 0;
                const current = parseFloat(cashInput.value) || 0;
                cashInput.value = (current + addVal).toFixed(2);
            }
            calculateChange(due);
        });
    });

    function getDueAmount() {
        const additionalSubtotal = cart.reduce((acc, it) => acc + (it.price * it.quantity), 0);
        if (isCombinedPickup) {
            const orderDue = isOnlinePaid ? 0 : onlineOrderBaseAmount;
            return orderDue + printingRemainingBalance + additionalSubtotal;
        }
        if (isStorePickup) {
            return isOnlinePaid ? additionalSubtotal : (onlineOrderBaseAmount + additionalSubtotal);
        }
        if (isPrintingPickup) {
            return printingRemainingBalance + additionalSubtotal;
        }
        return additionalSubtotal;
    }

    function showError(msg) {
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
    }

    function hideError() {
        errBox.classList.add('hidden');
        errBox.textContent = '';
    }

    // Submit handler
    if (submitBtn) {
        submitBtn.addEventListener('click', async function() {
            hideError();

            const due = getDueAmount();
            const selectedPaymentMethod = document.querySelector('input[name="pos_payment_method"]:checked')?.value || 'cash';

            if (!isStorePickup && !isPrintingPickup && cart.length === 0) {
                showError('Please add at least one product to the sale before completing.');
                return;
            }

            // If cash and customer owes an amount, verify tendered cash
            if (selectedPaymentMethod === 'cash' && due > 0) {
                const tendered = parseFloat(cashInput.value) || 0;
                if (tendered < due) {
                    showError(`Tendered cash (${formatMoney(tendered)}) is less than total due (${formatMoney(due)}).`);
                    return;
                }
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span><span>Processing...</span>';

            const payload = new FormData();
            payload.append(CSRF_TOKEN_NAME, CSRF_HASH_VAL);
            payload.append('counter_payment_method', selectedPaymentMethod);
            payload.append('items', JSON.stringify(cart.map(i => ({
                product_id: i.product_id,
                quantity: i.quantity
            }))));

            let endpoint = '';
            if (isCombinedPickup) {
                endpoint = `${BASE_URL}/tenant/pos/complete-pickup`;
                payload.append('order_id', storePickupOrderId);
                payload.append('printing_id', printingRequestId);
            } else if (isStorePickup) {
                endpoint = `${BASE_URL}/tenant/pos/complete-pickup`;
                payload.append('order_id', storePickupOrderId);
            } else if (isPrintingPickup) {
                endpoint = `${BASE_URL}/tenant/pos/complete-pickup`;
                payload.append('printing_id', printingRequestId);
            } else {
                endpoint = `${BASE_URL}/tenant/pos/complete-walkin`;
                const custName = document.getElementById('posCustomerName')?.value.trim() || '';
                payload.append('customer_name', custName);
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: payload
                });

                const data = await response.json();

                if (!data.success) {
                    showError(data.error || 'Failed to complete transaction.');
                    resetSubmitButton();
                    return;
                }

                // Show Receipt Modal
                showSuccessReceipt(data, selectedPaymentMethod, due);

            } catch (err) {
                showError('Network or server error while completing transaction. Please try again.');
                resetSubmitButton();
            }
        });
    }

    function resetSubmitButton() {
        submitBtn.disabled = false;
        let label = 'Complete Walk-in Sale';
        if (isCombinedPickup) {
            label = 'Complete Combined Pick-up';
        } else if (isPrintingPickup) {
            label = 'Complete Printing Pick-up';
        } else if (isStorePickup) {
            label = 'Complete Store Pick-up';
        }
        submitBtn.innerHTML = `<span class="material-symbols-outlined text-[20px]">check_circle</span><span>${label}</span>`;
    }

    let lastCompletedOrderId = null;
    let lastCompletedPrintingId = null;
    let lastIsCombined = false;

    function showSuccessReceipt(data, paymentMethod, dueAmount) {
        lastCompletedOrderId = data.order_id || data.order_number;
        lastCompletedPrintingId = data.printing_id || null;
        lastIsCombined = isCombinedPickup || data.type === 'combined';

        try {
            localStorage.setItem('blax_last_sale', JSON.stringify({ time: Date.now(), shop_id: <?= (int) ($shop['id'] ?? 0) ?>, order_id: data.order_id }));
            window.dispatchEvent(new CustomEvent('blax:sale_completed', { detail: data }));
        } catch (e) {}

        const successMsgEl = document.getElementById('posSuccessModalMessage');
        if (successMsgEl) {
            successMsgEl.textContent = data.message || 'Sale Completed Successfully!';
        }

        const tendered = parseFloat(cashInput.value) || dueAmount;
        const change = Math.max(0, tendered - dueAmount);

        // Reference number format: e.g. #ORD-90113
        const rawRef = String(data.order_number || (data.order_id ? ('ORD-' + data.order_id) : 'ORD-COMPLETED'));
        const cleanRef = '#' + rawRef.replace(/^#+/, '');

        // Customer Name resolution
        const customerName = (data.customer_name && data.customer_name !== 'Counter Customer')
            ? data.customer_name
            : (document.getElementById('posCustomerName')?.value.trim()
                || ORDER_CUSTOMER_NAME
                || data.customer_name
                || 'Counter Customer');

        // Fulfillment Label
        let fulfillmentLabel = 'Store Pick-up';
        if (isPrintingPickup && !isStorePickup) {
            fulfillmentLabel = 'Store Pick-up';
        } else if (!isStorePickup && !isPrintingPickup) {
            fulfillmentLabel = 'Store Pick-up';
        }

        // Formatted Date & Time (e.g. Sep 20, 2026 • 09:26 PM)
        const now = new Date();
        const datePart = now.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        const timePart = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        const formattedDateTime = `${datePart} • ${timePart}`;

        // Normalized Payment Method (e.g. CASH, GCASH)
        let pmLabel = String(paymentMethod || 'cash').toUpperCase();
        if (pmLabel === 'COD') pmLabel = 'CASH';

        // Render item rows matching image exactly
        let itemsHtml = '';
        let calculatedSubtotal = 0;

        // Fulfilled / Online items (Store Pick-up)
        if ((lastIsCombined || isStorePickup) && orderItemsSummary && orderItemsSummary.length > 0) {
            orderItemsSummary.forEach(it => {
                const lineTotal = it.total || (it.price * it.qty);
                calculatedSubtotal += lineTotal;
                itemsHtml += `
                    <div class="flex justify-between items-start gap-2 text-left">
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-xs text-slate-900 leading-snug">${esc(it.name)}</div>
                            ${it.variant ? `<div class="text-[10px] font-semibold text-blue-600">${esc(it.variant)}</div>` : ''}
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">${it.qty} &times; ${formatMoney(it.price || (lineTotal / it.qty))}</div>
                        </div>
                        <div class="font-bold font-mono text-xs text-slate-900 text-right whitespace-nowrap pt-0.5">
                            ${formatMoney(lineTotal)}
                        </div>
                    </div>
                `;
            });
        }

        // Printing Item (Printing Pick-up)
        if ((lastIsCombined || isPrintingPickup) && printingItemSummary) {
            const printTotal = printingItemSummary.total || 0;
            calculatedSubtotal += printTotal;
            itemsHtml += `
                <div class="flex justify-between items-start gap-2 text-left">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs text-slate-900 leading-snug">${esc(printingItemSummary.file_name)}</div>
                        <div class="text-[10px] font-semibold text-blue-600">${esc(printingItemSummary.specs || 'Custom Printing Job')}</div>
                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">Printing Fulfillment</div>
                    </div>
                    <div class="font-bold font-mono text-xs text-slate-900 text-right whitespace-nowrap pt-0.5">
                        ${formatMoney(printTotal)}
                    </div>
                </div>
            `;
        }

        // POS Counter Cart Items
        if (cart && cart.length > 0) {
            cart.forEach(it => {
                const lineTotal = (it.price || 0) * (it.quantity || 1);
                calculatedSubtotal += lineTotal;
                itemsHtml += `
                    <div class="flex justify-between items-start gap-2 text-left">
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-xs text-slate-900 leading-snug">${esc(it.name)}</div>
                            ${it.variant ? `<div class="text-[10px] font-semibold text-blue-600">${esc(it.variant)}</div>` : ''}
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">${it.quantity} &times; ${formatMoney(it.price)}</div>
                        </div>
                        <div class="font-bold font-mono text-xs text-slate-900 text-right whitespace-nowrap pt-0.5">
                            ${formatMoney(lineTotal)}
                        </div>
                    </div>
                `;
            });
        }

        // Fallback row if cart & summaries are empty
        if (!itemsHtml) {
            itemsHtml = `
                <div class="flex justify-between items-start gap-2 text-left">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-xs text-slate-900 leading-snug">Retail Purchase</div>
                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">1 &times; ${formatMoney(dueAmount)}</div>
                    </div>
                    <div class="font-bold font-mono text-xs text-slate-900 text-right whitespace-nowrap pt-0.5">
                        ${formatMoney(dueAmount)}
                    </div>
                </div>
            `;
            calculatedSubtotal = dueAmount;
        }

        const finalTotalAmount = dueAmount > 0 ? dueAmount : calculatedSubtotal;
        const subtotalToDisplay = calculatedSubtotal > 0 ? calculatedSubtotal : finalTotalAmount;

        let cashBreakdownHtml = '';
        if (pmLabel === 'CASH' && dueAmount > 0 && tendered > dueAmount) {
            cashBreakdownHtml = `
                <div class="flex justify-between text-slate-500 font-mono text-xs pt-1 border-t border-dashed border-slate-200">
                    <span>Cash Tendered:</span>
                    <span>${formatMoney(tendered)}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold font-mono text-xs">
                    <span>Change Returned:</span>
                    <span>${formatMoney(change)}</span>
                </div>
            `;
        }

        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(cleanRef)}`;

        // Build exact HTML matching image layout & typography
        const receiptHtml = `
            <!-- 1. Shop Header -->
            <div class="text-center space-y-1">
                <h2 class="text-xl font-black text-slate-950 tracking-tight">${esc(SHOP_INFO.name)}</h2>
                <p class="text-[11px] text-slate-500 max-w-[280px] mx-auto leading-relaxed">${esc(SHOP_INFO.address)}</p>
                <div class="pt-1.5">
                    <span class="inline-block px-3.5 py-0.5 rounded-full border border-slate-300 text-[10px] font-extrabold uppercase tracking-widest text-slate-700 bg-slate-50/80">
                        OFFICIAL SALES RECEIPT
                    </span>
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-slate-300 my-3"></div>

            <!-- 2. Meta Grid (2 columns) -->
            <div class="grid grid-cols-2 gap-y-2.5 text-xs text-left">
                <div>
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">REFERENCE</span>
                    <span class="font-bold font-mono text-slate-900 text-xs">${esc(cleanRef)}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">DATE & TIME</span>
                    <span class="font-bold text-slate-900 text-xs">${formattedDateTime}</span>
                </div>
                <div>
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">CUSTOMER</span>
                    <span class="font-bold text-slate-900 text-xs">${esc(customerName)}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">FULFILLMENT</span>
                    <span class="font-bold text-slate-900 text-xs">${esc(fulfillmentLabel)}</span>
                </div>
                <div>
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">CASHIER / STAFF</span>
                    <span class="font-bold text-slate-900 text-xs">${esc(SHOP_INFO.cashier)}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">STATUS</span>
                    <span class="font-bold text-emerald-600 text-xs tracking-wider">PAID</span>
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-slate-300 my-3"></div>

            <!-- 3. Items & Details -->
            <div>
                <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">
                    <span>ITEM & DETAILS</span>
                    <span>SUBTOTAL</span>
                </div>
                <div class="space-y-3">
                    ${itemsHtml}
                </div>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-slate-300 my-3"></div>

            <!-- 4. Financial Summary -->
            <div class="space-y-1.5 text-xs text-left">
                <div class="flex justify-between font-mono text-slate-500">
                    <span>Subtotal:</span>
                    <span>${formatMoney(subtotalToDisplay)}</span>
                </div>
                <div class="border-t border-slate-950 my-1.5"></div>
                <div class="flex justify-between font-black font-mono text-sm text-slate-950 tracking-wider">
                    <span>TOTAL AMOUNT:</span>
                    <span>${formatMoney(finalTotalAmount)}</span>
                </div>
                <div class="border-t border-slate-950 my-1.5"></div>
                <div class="flex justify-between font-bold text-slate-900">
                    <span>Payment Method:</span>
                    <span class="uppercase font-mono">${esc(pmLabel)}</span>
                </div>
                ${cashBreakdownHtml}
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-slate-300 my-3"></div>

            <!-- 5. QR Code Section -->
            <div class="text-center pt-1 space-y-1">
                <div class="inline-block p-1.5 bg-white border-2 border-slate-950 rounded-2xl shadow-xs">
                    <img src="${qrUrl}" alt="Order QR Code" class="w-32 h-32 block mx-auto rounded-lg" />
                </div>
                <div class="font-mono font-bold text-xs text-slate-900 mt-1">${esc(cleanRef)}</div>
                <p class="text-[10px] text-slate-500 max-w-[240px] mx-auto">Scan with Blax POS or Camera to verify order authenticity</p>
            </div>

            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-slate-300 my-3"></div>

            <!-- 6. Receipt Footer -->
            <div class="text-center text-xs space-y-0.5 pt-0.5">
                <p class="font-bold text-slate-900">Thank you for choosing ${esc(SHOP_INFO.name)}!</p>
                <p class="text-[10px] text-slate-500">Please keep this receipt for order tracking & pick-up verification.</p>
                <p class="text-[9px] text-slate-400 mt-1">Powered by Blax Inventory • Polomolok, South Cotabato</p>
            </div>
        `;

        receiptSummary.innerHTML = receiptHtml;
        successModal.classList.remove('hidden');
    }

    if (printReceiptBtn) {
        printReceiptBtn.addEventListener('click', () => {
            if (lastIsCombined && lastCompletedOrderId && lastCompletedPrintingId) {
                const printUrl = `${BASE_URL}/tenant/orders/receipt/${encodeURIComponent(lastCompletedOrderId)}?printing_id=${encodeURIComponent(lastCompletedPrintingId)}`;
                window.open(printUrl, '_blank');
            } else if (lastCompletedOrderId) {
                const printUrl = isPrintingPickup
                    ? `${BASE_URL}/tenant/printing/receipt/${encodeURIComponent(lastCompletedOrderId)}`
                    : `${BASE_URL}/tenant/orders/receipt/${encodeURIComponent(lastCompletedOrderId)}`;
                window.open(printUrl, '_blank');
            } else {
                window.print();
            }
        });
    }

    if (newSaleBtn) {
        newSaleBtn.addEventListener('click', () => {
            if (isCombinedPickup) {
                window.location.href = `${BASE_URL}/tenant/pos`;
            } else if (isStorePickup) {
                window.location.href = `${BASE_URL}/tenant/orders`;
            } else if (isPrintingPickup) {
                window.location.href = `${BASE_URL}/tenant/printing`;
            } else {
                window.location.reload();
            }
        });
    }

    // Modal functions for Store Pick-up selector
    // Modal functions for Store Pick-up selector
    let currentPickupTab = '';
    window.setPickupTabFilter = function(status, btn) {
        currentPickupTab = status;
        document.querySelectorAll('.pos-po-tab').forEach(b => {
            b.className = 'pos-po-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700';
        });
        if (btn) {
            btn.className = 'pos-po-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-[#4A7C59] text-white shadow-xs';
        }
        const searchInput = document.getElementById('posPickupFilterInput');
        filterPickupOrders(searchInput ? searchInput.value : '');
    };

    window.openPickupSelectorModal = function() {
        const modal = document.getElementById('posPickupSelectorModal');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const input = document.getElementById('posPickupFilterInput');
                if (input) input.focus();
            }, 60);
        }
    };

    window.closePickupSelectorModal = function() {
        const modal = document.getElementById('posPickupSelectorModal');
        if (modal) modal.classList.add('hidden');
    };

    window.filterPickupOrders = function(query) {
        const q = (query || '').toLowerCase().trim();
        let visibleCount = 0;
        document.querySelectorAll('.pos-pickup-row').forEach(row => {
            const searchData = row.dataset.search || '';
            const statusData = row.dataset.status || '';
            const matchesQuery = (q === '' || searchData.includes(q));
            const matchesTab = (currentPickupTab === '' || statusData.includes(currentPickupTab));

            if (matchesQuery && matchesTab) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        const countSpan = document.getElementById('posPickupFooterCount');
        if (countSpan) {
            countSpan.textContent = `Showing ${visibleCount} active pick-up tickets • Register Terminal 01`;
        }
    };

    // Modal functions for Printing Pick-up selector
    let currentPrintingTab = '';
    window.setPrintingTabFilter = function(status, btn) {
        currentPrintingTab = status;
        document.querySelectorAll('.pos-pr-tab').forEach(b => {
            b.className = 'pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700';
        });
        if (btn) {
            btn.className = 'pos-pr-tab px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-[#4A7C59] text-white shadow-xs';
        }
        const searchInput = document.getElementById('posPrintingFilterInput');
        filterPrintingRequests(searchInput ? searchInput.value : '');
    };

    window.openPrintingSelectorModal = function() {
        const modal = document.getElementById('posPrintingSelectorModal');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const input = document.getElementById('posPrintingFilterInput');
                if (input) input.focus();
            }, 60);
        }
    };

    window.closePrintingSelectorModal = function() {
        const modal = document.getElementById('posPrintingSelectorModal');
        if (modal) modal.classList.add('hidden');
    };

    window.filterPrintingRequests = function(query) {
        const q = (query || '').toLowerCase().trim();
        let visibleCount = 0;
        document.querySelectorAll('.pos-printing-row').forEach(row => {
            const searchData = row.dataset.search || '';
            const statusData = row.dataset.status || '';
            const matchesQuery = (q === '' || searchData.includes(q));
            const matchesTab = (currentPrintingTab === '' || statusData.includes(currentPrintingTab));

            if (matchesQuery && matchesTab) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        const countSpan = document.getElementById('posPrintingFooterCount');
        if (countSpan) {
            countSpan.textContent = `Showing ${visibleCount} active printing tickets • Production Hub 01`;
        }
    };

    // Global Keyboard Shortcuts (Escape to dismiss, Ctrl+K to search active modal)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePickupSelectorModal();
            closePrintingSelectorModal();
        } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            const poModal = document.getElementById('posPickupSelectorModal');
            const prModal = document.getElementById('posPrintingSelectorModal');
            if (poModal && !poModal.classList.contains('hidden')) {
                e.preventDefault();
                const input = document.getElementById('posPickupFilterInput');
                if (input) input.focus();
            } else if (prModal && !prModal.classList.contains('hidden')) {
                e.preventDefault();
                const input = document.getElementById('posPrintingFilterInput');
                if (input) input.focus();
            }
        }
    });

    // QR Scanner & Verification Handlers
    let html5QrCode = null;
    let currentPosScanMode = 'camera';

    window.switchPosScanMode = function(mode) {
        currentPosScanMode = mode;
        const camBtn = document.getElementById('posTabCameraBtn');
        const fileBtn = document.getElementById('posTabFileBtn');
        const camBox = document.getElementById('posCameraContainer');
        const fileBox = document.getElementById('posFileContainer');

        if (mode === 'camera') {
            camBtn.classList.remove('text-on-surface-variant');
            camBtn.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
            fileBtn.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
            fileBtn.classList.add('text-on-surface-variant');
            camBox.classList.remove('hidden');
            fileBox.classList.add('hidden');
            startPosCamera();
        } else {
            fileBtn.classList.remove('text-on-surface-variant');
            fileBtn.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
            camBtn.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
            camBtn.classList.add('text-on-surface-variant');
            fileBox.classList.remove('hidden');
            camBox.classList.add('hidden');
            stopQrScanner();
        }
    };

    function startPosCamera() {
        if (!window.Html5Qrcode) return;
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("posQrReader");
        }

        const placeholder = document.getElementById('posQrCameraPlaceholder');
        if (placeholder) {
            placeholder.classList.remove('hidden');
            placeholder.innerHTML = `
                <span class="material-symbols-outlined text-4xl mb-1 text-secondary animate-pulse">photo_camera</span>
                <span class="text-xs font-semibold">Starting camera...</span>
                <span class="text-[10px] opacity-70 mt-1">Please allow camera permissions if prompted</span>
            `;
        }

        const showCameraError = () => {
            if (placeholder) {
                placeholder.classList.remove('hidden');
                placeholder.innerHTML = `
                    <span class="material-symbols-outlined text-3xl mb-1 text-amber-400">videocam_off</span>
                    <span class="text-xs font-semibold">Camera unavailable or blocked</span>
                    <span class="text-[10px] opacity-70 mt-1">Upload a photo or enter code manually below</span>
                `;
            }
        };

        if (window.isSecureContext === false && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            showCameraError();
            if (placeholder) {
                const sub = placeholder.querySelector('span:last-child');
                if (sub) sub.textContent = 'Camera requires HTTPS or localhost. Upload photo or enter code manually.';
            }
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError();
            return;
        }

        const config = { fps: 12 };
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                stopQrScanner();
                sendVerifyQrRequest(decodedText);
            },
            () => {}
        ).then(() => {
            if (placeholder) placeholder.classList.add('hidden');
        }).catch(() => {
            if (Html5Qrcode.getCameras) {
                Html5Qrcode.getCameras().then(cameras => {
                    if (cameras && cameras.length) {
                        html5QrCode.start(
                            { deviceId: { exact: cameras[0].id } },
                            config,
                            (decodedText) => {
                                stopQrScanner();
                                sendVerifyQrRequest(decodedText);
                            },
                            () => {}
                        ).then(() => {
                            if (placeholder) placeholder.classList.add('hidden');
                        }).catch(() => showCameraError());
                    } else {
                        showCameraError();
                    }
                }).catch(() => showCameraError());
            } else {
                showCameraError();
            }
        });
    }

    window.openQrScannerModal = function() {
        const modal = document.getElementById('posQrScannerModal');
        const feedback = document.getElementById('posScanFeedback');
        const manualInput = document.getElementById('posManualOrderCode');
        const photoStatus = document.getElementById('posPhotoScanStatus');

        if (feedback) {
            feedback.classList.add('hidden');
            feedback.textContent = '';
        }
        if (manualInput) {
            manualInput.value = '';
            manualInput.disabled = false;
        }
        if (photoStatus) {
            photoStatus.classList.add('hidden');
        }

        if (modal) modal.classList.remove('hidden');
        switchPosScanMode('camera');
    };

    window.stopQrScanner = function() {
        if (html5QrCode) {
            try {
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().catch(e => console.error(e));
                }
            } catch (e) {
                console.error(e);
            }
        }
    };

    window.closeQrScannerModal = function() {
        stopQrScanner();
        const modal = document.getElementById('posQrScannerModal');
        if (modal) modal.classList.add('hidden');
    };

    window.handlePosPhotoUpload = function(input) {
        if (!input || !input.files || input.files.length === 0) return;
        const file = input.files[0];
        const statusEl = document.getElementById('posPhotoScanStatus');
        if (statusEl) {
            statusEl.className = 'text-xs text-center text-primary font-bold animate-pulse';
            statusEl.textContent = 'Scanning image for QR code...';
            statusEl.classList.remove('hidden');
        }

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("posQrReader");
        }

        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                if (statusEl) {
                    statusEl.className = 'text-xs text-center text-green-700 font-bold';
                    statusEl.textContent = 'QR Code detected! Verifying...';
                }
                sendVerifyQrRequest(decodedText);
            })
            .catch(err => {
                if (statusEl) {
                    statusEl.className = 'text-xs text-center text-red-600 font-bold';
                    statusEl.textContent = 'Could not detect a clear QR code in this photo. Please ensure good lighting or enter the code manually.';
                    statusEl.classList.remove('hidden');
                }
            });
    };

    window.verifyManualOrderCode = function() {
        const input = document.getElementById('posManualOrderCode');
        const val = input ? input.value.trim() : '';
        if (!val) {
            const feedback = document.getElementById('posScanFeedback');
            if (feedback) {
                feedback.className = 'text-xs rounded-xl p-sm font-semibold bg-amber-100 text-amber-900 border border-amber-300';
                feedback.textContent = 'Please enter an order/request number or scan a QR code.';
                feedback.classList.remove('hidden');
            }
            return;
        }
        stopQrScanner();
        sendVerifyQrRequest(val);
    };

    // Allow Enter key in manual order code input
    const manualOrderInput = document.getElementById('posManualOrderCode');
    if (manualOrderInput) {
        manualOrderInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                verifyManualOrderCode();
            }
        });
    }

    function sendVerifyQrRequest(code) {
        const feedback = document.getElementById('posScanFeedback');
        const manualBtn = document.getElementById('posManualLookupBtn');
        const manualInput = document.getElementById('posManualOrderCode');

        if (feedback) {
            feedback.className = 'text-xs rounded-xl p-sm font-semibold bg-primary/10 text-primary border border-primary/20 flex items-center gap-xs';
            feedback.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span> Verifying Order / Request #' + code + '...';
            feedback.classList.remove('hidden');
        }
        if (manualBtn) manualBtn.disabled = true;
        if (manualInput) manualInput.disabled = true;

        const fd = new FormData();
        fd.append('qr_code', code);
        const currentCsrf = (window.getCsrfToken && window.getCsrfToken()) ? window.getCsrfToken() : CSRF_HASH_VAL;
        fd.append(CSRF_TOKEN_NAME, currentCsrf);

        fetch(`${BASE_URL}/tenant/pos/verify-qr`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(r => r.json().then(data => ({ status: r.status, data: data })))
        .then(({ status, data }) => {
            if (manualBtn) manualBtn.disabled = false;
            if (manualInput) manualInput.disabled = false;
            if (data.csrf_hash) {
                CSRF_HASH_VAL = data.csrf_hash;
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }

            if (data.success) {
                if (data.already_done) {
                    closeQrScannerModal();
                    openCompletedOrderModal(data);
                    return;
                }

                if (feedback) {
                    feedback.className = 'text-xs rounded-xl p-sm font-semibold bg-green-100 text-green-900 border border-green-300';
                    feedback.textContent = `${data.message} Redirecting...`;
                }
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 700);
            } else {
                if (feedback) {
                    feedback.className = 'text-xs rounded-xl p-sm font-semibold bg-red-100 text-red-900 border border-red-300';
                    feedback.textContent = data.error || 'Verification failed.';
                }
            }
        })
        .catch(() => {
            if (manualBtn) manualBtn.disabled = false;
            if (manualInput) manualInput.disabled = false;
            if (feedback) {
                feedback.className = 'text-xs rounded-xl p-sm font-semibold bg-red-100 text-red-900 border border-red-300';
                feedback.textContent = 'Server or network error while verifying code.';
            }
        });
    }

    // Completed Order / Receipt Detected Modal Handlers
    let currentScannedCompletedData = null;

    window.openCompletedOrderModal = function(data) {
        currentScannedCompletedData = data;
        const modal = document.getElementById('posCompletedOrderModal');
        const feedback = document.getElementById('posCompletedModalFeedback');
        if (feedback) {
            feedback.classList.add('hidden');
            feedback.textContent = '';
        }

        const order = data.order || {};
        const items = data.items || [];

        const refEl = document.getElementById('posCompletedRefText');
        const custEl = document.getElementById('posCompletedCustomerText');
        const dateEl = document.getElementById('posCompletedDateText');
        const totalEl = document.getElementById('posCompletedTotalText');
        const countEl = document.getElementById('posCompletedItemCount');
        const listEl = document.getElementById('posCompletedItemsList');
        const receiptLink = document.getElementById('posCompletedViewReceiptLink');
        const returnBtn = document.getElementById('posBtnActionReturn');

        const cleanRef = '#' + String(order.order_number || data.order_number || 'COMPLETED').replace(/^#+/, '');
        if (refEl) refEl.textContent = cleanRef;
        if (custEl) custEl.textContent = order.customer_name || 'Counter Customer';
        if (dateEl) dateEl.textContent = order.completed_at || order.placed_at || 'Recently';
        if (totalEl) totalEl.textContent = formatMoney(order.total_amount || 0);
        if (countEl) countEl.textContent = items.length;

        if (receiptLink) {
            receiptLink.href = order.receipt_url || `${BASE_URL}/tenant/orders/receipt/${encodeURIComponent(order.order_number || '')}`;
        }

        if (returnBtn) {
            returnBtn.disabled = false;
            returnBtn.innerHTML = `
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">assignment_return</span>
                </div>
                <span class="font-black text-sm text-purple-900 tracking-wide mt-1">Returned</span>
                <span class="text-[11px] text-purple-700 font-medium leading-tight">I-mark as returned &amp; ibalik sa inventory stock</span>
            `;
        }

        if (listEl) {
            if (items.length === 0) {
                listEl.innerHTML = '<p class="text-xs text-outline text-center py-3">Walang indibidwal na items na na-record.</p>';
            } else {
                listEl.innerHTML = items.map(it => `
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-2xs">
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-xs text-on-surface truncate">${esc(it.name)}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-outline font-mono">
                                ${it.variant_label ? `<span class="px-1.5 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold">${esc(it.variant_label)}</span>` : ''}
                                <span>${it.quantity} &times; ${formatMoney(it.price)}</span>
                            </div>
                        </div>
                        <div class="font-mono font-bold text-xs text-on-surface text-right shrink-0">
                            ${formatMoney(it.line_total || (it.price * it.quantity))}
                        </div>
                    </div>
                `).join('');
            }
        }

        if (modal) modal.classList.remove('hidden');
    };

    window.closeCompletedOrderModal = function() {
        const modal = document.getElementById('posCompletedOrderModal');
        if (modal) modal.classList.add('hidden');
        currentScannedCompletedData = null;
    };

    window.confirmReturnScannedOrder = function() {
        if (!currentScannedCompletedData) return;
        const order = currentScannedCompletedData.order || {};
        const orderId = order.id || currentScannedCompletedData.order_id;
        const isPrinting = currentScannedCompletedData.is_printing;
        const refNumber = order.order_number || currentScannedCompletedData.order_number || currentScannedCompletedData.request_number || '';

        if (!confirm(`Kumpirmahin ang pag-return ng Order #${refNumber}?\nAwtomatikong ibabalik ang lahat ng items sa inventory stock ng tindahan.`)) {
            return;
        }

        const returnBtn = document.getElementById('posBtnActionReturn');
        const feedback = document.getElementById('posCompletedModalFeedback');

        if (returnBtn) {
            returnBtn.disabled = true;
            returnBtn.innerHTML = '<span class="material-symbols-outlined text-2xl animate-spin text-purple-700">progress_activity</span><span class="font-black text-sm text-purple-900 tracking-wide mt-1">Processing Return...</span>';
        }

        const fd = new FormData();
        if (isPrinting) {
            fd.append('printing_id', currentScannedCompletedData.request_id || order.id);
        } else {
            fd.append('order_id', orderId);
        }
        fd.append(CSRF_TOKEN_NAME, CSRF_HASH_VAL);

        fetch(`${BASE_URL}/tenant/pos/return-order`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(r => r.json())
        .then(res => {
            if (res.csrf_hash) {
                CSRF_HASH_VAL = res.csrf_hash;
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', res.csrf_hash);
            }

            if (res.success) {
                if (feedback) {
                    feedback.className = 'text-xs rounded-xl p-3 font-semibold bg-green-100 text-green-900 border border-green-300 flex items-center gap-2';
                    feedback.innerHTML = '<span class="material-symbols-outlined text-[18px] text-green-700">check_circle</span> ' + (res.message || 'Matagumpay na na-mark bilang RETURNED.');
                    feedback.classList.remove('hidden');
                }

                // Refresh catalog so restored inventory quantities are immediately visible in POS
                loadCatalog(searchInput ? searchInput.value.trim() : '', currentCategoryId);

                setTimeout(() => {
                    closeCompletedOrderModal();
                }, 1600);
            } else {
                if (returnBtn) {
                    returnBtn.disabled = false;
                    returnBtn.innerHTML = `
                        <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-2xl">assignment_return</span>
                        </div>
                        <span class="font-black text-sm text-purple-900 tracking-wide mt-1">Returned</span>
                        <span class="text-[11px] text-purple-700 font-medium leading-tight">I-mark as returned &amp; ibalik sa inventory stock</span>
                    `;
                }
                if (feedback) {
                    feedback.className = 'text-xs rounded-xl p-3 font-semibold bg-red-100 text-red-900 border border-red-300';
                    feedback.textContent = res.error || 'Hindi na-proseso ang return.';
                    feedback.classList.remove('hidden');
                }
            }
        })
        .catch(() => {
            if (returnBtn) {
                returnBtn.disabled = false;
                returnBtn.innerHTML = `
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl">assignment_return</span>
                    </div>
                    <span class="font-black text-sm text-purple-900 tracking-wide mt-1">Returned</span>
                    <span class="text-[11px] text-purple-700 font-medium leading-tight">I-mark as returned &amp; ibalik sa inventory stock</span>
                `;
            }
            if (feedback) {
                feedback.className = 'text-xs rounded-xl p-3 font-semibold bg-red-100 text-red-900 border border-red-300';
                feedback.textContent = 'Server o network error habang pino-proseso ang return.';
                feedback.classList.remove('hidden');
            }
        });
    };

    window.addScannedOrderToPos = function() {
        if (!currentScannedCompletedData) return;
        const order = currentScannedCompletedData.order || {};
        const items = currentScannedCompletedData.items || [];
        const isPrinting = currentScannedCompletedData.is_printing;

        if (isPrinting) {
            window.location.href = `${BASE_URL}/tenant/pos?printing_id=${encodeURIComponent(currentScannedCompletedData.request_id || order.id)}`;
            return;
        }

        if (items.length === 0) {
            alert('Walang nakitang items sa resibong ito.');
            return;
        }

        // Add items to active POS cart with their recorded receipt details
        items.forEach(it => {
            const pId = parseInt(it.product_id);
            if (pId <= 0) return;

            const qtyToAdd = parseInt(it.quantity || 1);
            const existing = cart.find(x => x.product_id === pId);
            if (existing) {
                existing.quantity += qtyToAdd;
            } else {
                cart.push({
                    product_id: pId,
                    name: it.name,
                    price: parseFloat(it.price || 0),
                    stock_quantity: parseInt(it.stock_quantity || 99),
                    sku: '',
                    variant_id: it.variant_id || 0,
                    variant_label: it.variant_label || '',
                    quantity: qtyToAdd
                });
            }
        });

        // Pre-fill customer name if walk-in input exists and customer name is available
        const custNameInput = document.getElementById('posCustomerName');
        if (custNameInput && order.customer_name && order.customer_name !== 'Counter Customer') {
            custNameInput.value = order.customer_name;
        }

        renderCart();
        closeCompletedOrderModal();

        // Inform user via errBox styled as positive banner
        if (errBox) {
            errBox.className = 'p-3 rounded-xl bg-green-500/10 text-green-800 border border-green-500/25 text-xs font-semibold flex items-center gap-2 mb-3';
            errBox.innerHTML = `<span class="material-symbols-outlined text-[16px] text-green-700">check_circle</span> Naidagdag na ang ${items.length} item(s) mula sa resibo #${order.order_number || 'completed'} sa POS cart!`;
            errBox.classList.remove('hidden');
            setTimeout(() => {
                errBox.classList.add('hidden');
                errBox.className = 'p-3 rounded-xl bg-error/10 text-error border border-error/20 text-xs font-medium hidden mb-3';
            }, 3500);
        }
    };

    // Initialize catalog
    loadCatalog('', '');
    renderCart();
})();
</script>
<?= $this->endSection() ?>
