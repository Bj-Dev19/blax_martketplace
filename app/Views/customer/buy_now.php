<?= $this->extend('layouts/marketplace') ?>

<?= $this->section('content') ?>

<?php
    $shopOffersPickup   = !isset($shop['offers_pickup']) || (int) $shop['offers_pickup'] === 1;
    $shopOffersDelivery = !isset($shop['offers_delivery']) || (int) $shop['offers_delivery'] === 1;
    $defaultFulfillment = $shopOffersDelivery ? 'delivery' : ($shopOffersPickup ? 'pickup' : 'delivery');
    $initialShipping    = ($defaultFulfillment === 'pickup') ? 0.00 : (float) $shipping;
?>

<main class="max-w-6xl mx-auto px-4 sm:px-6 md:px-8 py-6 md:py-10 flex-grow w-full flex flex-col gap-6 overflow-x-hidden">

    <!-- Breadcrumb & Back Link -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?= base_url('product/' . $product['id']) ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-colors min-h-[36px]">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Back to Product</span>
        </a>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 text-xs font-bold border border-terra-green/20 font-mono shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-terra-green animate-pulse"></span>
            Direct Instant Checkout
        </span>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-lg shrink-0">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-lg shrink-0 text-amber-600">warning</span>
            <span><?= esc(session()->getFlashdata('warning')) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

        <!-- Left Column: Single Product Bento Card -->
        <div class="lg:col-span-5 xl:col-span-5 space-y-4 min-w-0">

            <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 shadow-xs border border-slate-200/90 dark:border-slate-800 space-y-5">
                
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-900 dark:text-white min-w-0">
                        <span class="material-symbols-outlined text-terra-green dark:text-emerald-400 text-[20px] shrink-0">storefront</span>
                        <span class="truncate"><?= esc($shop['shop_name'] ?? 'Partner Merchant') ?></span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <?php if ($shopOffersDelivery): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-terra-green/10 text-terra-green dark:text-emerald-400 border border-terra-green/20">
                                <span class="material-symbols-outlined text-[12px]">local_shipping</span> Delivery
                            </span>
                        <?php endif; ?>
                        <?php if ($shopOffersPickup): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                <span class="material-symbols-outlined text-[12px]">store</span> Pick-up
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Row -->
                <div class="flex gap-3.5 sm:gap-5 items-start">
                    <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shrink-0 flex items-center justify-center p-2">
                        <?php if (!empty($imageUrl)): ?>
                            <img src="<?= esc($imageUrl) ?>" alt="<?= esc($product['name']) ?>" class="w-full h-full object-contain">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-slate-400 text-[36px]">image</span>
                        <?php endif; ?>
                    </div>

                    <div class="flex-1 flex flex-col justify-between min-w-0 space-y-2.5">
                        <div>
                            <h1 class="font-display text-sm sm:text-base font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug"><?= esc($product['name']) ?></h1>
                            
                            <?php if (!empty($variantLabel)): ?>
                                <div class="mt-1.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-terra-green/10 text-terra-green dark:text-emerald-400 text-[11px] font-bold border border-terra-green/20">
                                        <span class="material-symbols-outlined text-[13px]">tune</span>
                                        <span><?= esc($variantLabel) ?></span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-baseline justify-between gap-2 pt-1 font-mono">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Unit Price</span>
                            <span class="text-sm font-black text-slate-900 dark:text-white">₱<?= number_format($unitPrice, 2) ?></span>
                        </div>

                        <div class="flex items-center justify-between gap-2 border-t border-slate-100 dark:border-slate-800 pt-2 font-mono">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Quantity</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200">
                                <?= (int) $quantity ?> <?= $quantity === 1 ? 'unit' : 'units' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Item Line Total -->
                <div class="bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 rounded-2xl p-4 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Item Subtotal</span>
                    <span class="text-lg font-black text-terra-green dark:text-emerald-400 font-mono">₱<?= number_format($subtotal, 2) ?></span>
                </div>

                <p class="text-xs text-slate-400 text-center">
                    Want to adjust quantity or variant? 
                    <a href="<?= base_url('product/' . $product['id']) ?>" class="text-terra-green dark:text-emerald-400 font-bold hover:underline">Change on product page</a>
                </p>

            </div>

            <!-- Guarantee Badges -->
            <div class="grid grid-cols-2 gap-3 text-xs text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2 p-3.5 rounded-2xl bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                    <span class="material-symbols-outlined text-terra-green dark:text-emerald-400 text-[20px] shrink-0">local_shipping</span>
                    <span class="font-semibold text-[11px] leading-tight">Townwide Polomolok delivery</span>
                </div>
                <div class="flex items-center gap-2 p-3.5 rounded-2xl bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                    <span class="material-symbols-outlined text-terra-green dark:text-emerald-400 text-[20px] shrink-0">verified_user</span>
                    <span class="font-semibold text-[11px] leading-tight">Direct verified merchant</span>
                </div>
            </div>

        </div>

        <!-- Right Column: Guided 2-Step Checkout Form -->
        <div class="lg:col-span-7 xl:col-span-7 min-w-0">

            <form action="<?= base_url('buy-now/place') ?>" method="POST" id="buy-now-form" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= esc($product['id']) ?>">
                <input type="hidden" name="variant_id" value="<?= esc($selectedVariant['id'] ?? '') ?>">
                <input type="hidden" name="quantity" value="<?= esc($quantity) ?>">
                <input type="hidden" name="fulfillment_method" id="form-fulfillment-method" value="<?= esc($defaultFulfillment) ?>">

                <!-- STEP 1: RECEIVING METHOD (FULFILLMENT) -->
                <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 shadow-xs border border-slate-200/90 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-terra-green text-white font-mono text-xs font-black flex items-center justify-center shrink-0">1</span>
                            <div>
                                <h2 class="font-display text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight">How would you like to receive your order?</h2>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Choose delivery to your address or free pick-up at the store</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup" aria-label="Receiving Method">
                        <!-- Option A: Doorstep Delivery -->
                        <label id="card-fulfillment-delivery" class="relative flex flex-col justify-between p-4 rounded-2xl border-2 transition-all <?= $shopOffersDelivery ? 'cursor-pointer hover:border-terra-green/60' : 'opacity-40 cursor-not-allowed pointer-events-none border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40' ?> <?= $defaultFulfillment === 'delivery' ? 'border-terra-green bg-terra-green/5 dark:bg-terra-green/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60' ?>">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="fulfillment_choice" value="delivery" class="w-4 h-4 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green fulfillment-radio" <?= $defaultFulfillment === 'delivery' ? 'checked' : '' ?> <?= !$shopOffersDelivery ? 'disabled' : '' ?>>
                                    <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white block">Doorstep Delivery</span>
                                        <span id="buynow-delivery-subtext" class="text-[11px] text-slate-500 dark:text-slate-400 block">
                                            <?= !empty($shippingQuote['distance_text']) ? esc($shippingQuote['distance_text']) . (empty($shippingQuote['distance_approximate']) ? ' road route' : '') : 'Polomolok area' ?>
                                        </span>
                                    </div>
                                </div>
                                <span id="buynow-delivery-fee-badge" class="px-2 py-0.5 rounded-lg text-[11px] font-black font-mono <?= $shopOffersDelivery ? 'bg-slate-200/70 dark:bg-slate-700 text-slate-800 dark:text-slate-200' : 'bg-rose-500/10 text-rose-600' ?> shrink-0">
                                    <?= $shopOffersDelivery ? '₱' . number_format($shipping, 2) : 'N/A' ?>
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2.5 pl-6 leading-relaxed">
                                <?= $shopOffersDelivery ? 'Distance-based fee calculated via Google Maps Routes.' : 'Doorstep delivery is currently not offered by this shop.' ?>
                            </p>
                            <p id="buynow-distance-note" class="text-[11px] text-amber-700 dark:text-amber-400 bg-amber-500/10 border border-amber-500/25 rounded-lg px-2.5 py-1.5 mt-2 leading-relaxed<?= empty($shippingQuote['distance_approximate']) ? ' hidden' : '' ?>">
                                <?= esc($shippingQuote['accuracy_notice'] ?? '') ?>
                            </p>
                        </label>

                        <!-- Option B: Store Pick-up -->
                        <label id="card-fulfillment-pickup" class="relative flex flex-col justify-between p-4 rounded-2xl border-2 transition-all <?= $shopOffersPickup ? 'cursor-pointer hover:border-terra-green/60' : 'opacity-40 cursor-not-allowed pointer-events-none border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40' ?> <?= $defaultFulfillment === 'pickup' ? 'border-terra-green bg-terra-green/5 dark:bg-terra-green/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60' ?>">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="fulfillment_choice" value="pickup" class="w-4 h-4 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green fulfillment-radio" <?= $defaultFulfillment === 'pickup' ? 'checked' : '' ?> <?= !$shopOffersPickup ? 'disabled' : '' ?>>
                                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white block">Store Pick-up</span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block">No shipping fee</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black font-mono <?= $shopOffersPickup ? 'bg-terra-green/15 text-terra-green dark:text-emerald-400 border border-terra-green/25' : 'bg-rose-500/10 text-rose-600' ?> shrink-0">
                                    <?= $shopOffersPickup ? 'FREE' : 'N/A' ?>
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2.5 pl-6 leading-relaxed">
                                <?= $shopOffersPickup ? 'Pick up directly at ' . esc($shop['shop_name'] ?? 'shop branch') . ' counter.' : 'Store pick-up is currently not offered by this shop.' ?>
                            </p>
                        </label>
                    </div>

                    <?php if (!$shopOffersDelivery && $shopOffersPickup): ?>
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0">info</span>
                            <span><strong>Note:</strong> This shop currently offers <strong>Store Pick-up only</strong> (Free shipping).</span>
                        </div>
                    <?php elseif (!$shopOffersPickup && $shopOffersDelivery): ?>
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0">info</span>
                            <span><strong>Note:</strong> This shop currently offers <strong>Doorstep Delivery only</strong>.</span>
                        </div>
                    <?php endif; ?>

                    <!-- Contextual Delivery Address Box (Shown when Doorstep Delivery is active) -->
                    <div id="address-section" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2.5 <?= $defaultFulfillment === 'pickup' ? 'hidden' : '' ?>">
                        <div class="flex items-center justify-between gap-2">
                            <label for="shipping-address-select" class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-terra-green dark:text-emerald-400">location_on</span>
                                <span>Delivery Address</span>
                            </label>
                            <a href="<?= base_url('customer/addresses') ?>" class="text-xs text-terra-green dark:text-emerald-400 font-bold hover:underline">Manage Addresses</a>
                        </div>

                        <?php if (!empty($addresses)): ?>
                            <select name="shipping_address_id" id="shipping-address-select" class="w-full max-w-full truncate p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green" <?= $defaultFulfillment === 'delivery' ? 'required' : '' ?>>
                                <?php foreach ($addresses as $addr): ?>
                                    <option value="<?= $addr['id'] ?>" <?= !empty($addr['is_default']) ? 'selected' : '' ?>>
                                        <?= esc($addr['recipient_name']) ?> — <?= esc($addr['address_line1']) ?>, <?= esc($addr['city']) ?> <?= !empty($addr['phone_number']) ? '(' . esc($addr['phone_number']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Live Google Maps Route Distance Breakdown Pill -->
                            <div id="buynow-distance-info" class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/70 dark:border-slate-700/70 text-[11px] text-slate-600 dark:text-slate-300">
                                <span class="inline-flex items-center gap-1.5 font-semibold">
                                    <span class="material-symbols-outlined text-[15px] text-terra-green">route</span>
                                    <span id="buynow-route-distance-text">
                                        Road Distance: <strong><?= esc($shippingQuote['distance_text'] ?? '—') ?></strong>
                                    </span>
                                </span>
                                <span id="buynow-route-formula-text" class="font-mono text-[10px] text-slate-500 dark:text-slate-400">
                                    Base ₱<?= number_format((float) ($shippingQuote['base_shipping_fee'] ?? 50), 2) ?> (up to <?= number_format((float) ($shippingQuote['included_distance_km'] ?? 2), 1) ?> km limit)
                                    <?php if (!empty($shippingQuote['extra_distance_fee']) && (float) $shippingQuote['extra_distance_fee'] > 0): ?>
                                        + ₱<?= number_format((float) $shippingQuote['extra_distance_fee'], 2) ?> (<?= (int) ($shippingQuote['extra_steps'] ?? 1) ?> <?= ((int) ($shippingQuote['extra_steps'] ?? 1) === 1) ? 'step' : 'steps' ?> beyond limit)
                                    <?php endif; ?>
                                </span>
                            </div>

                            <!-- Out-of-range delivery warning -->
                            <div id="buynow-distance-error" class="<?= empty($shippingQuote['deliverable']) && isset($shippingQuote['deliverable']) ? '' : 'hidden' ?> p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">location_off</span>
                                <span id="buynow-distance-error-text"><?= esc($shippingQuote['error'] ?? 'Selected address exceeds the maximum delivery distance.') ?></span>
                            </div>
                        <?php else: ?>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">No saved addresses. Please enter your delivery address:</p>
                            <input type="text" name="shipping_address" placeholder="Enter delivery address (e.g. Poblacion, Polomolok)" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white" <?= $defaultFulfillment === 'delivery' ? 'required' : '' ?>>
                        <?php endif; ?>
                    </div>

                    <!-- Contextual Store Pick-up Info Box (Shown when Store Pick-up is active) -->
                    <div id="pickup-info-section" class="pt-3 border-t border-slate-100 dark:border-slate-800 <?= $defaultFulfillment === 'pickup' ? '' : 'hidden' ?>">
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[22px] shrink-0 mt-0.5">qr_code_2</span>
                            <div class="text-xs text-slate-700 dark:text-slate-200 space-y-0.5">
                                <p class="font-bold text-slate-900 dark:text-white">Pick-up Branch: <?= esc($shop['shop_name'] ?? 'Merchant Store') ?></p>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                                    Shipping fee is <strong>FREE (₱0.00)</strong>. After placing your order, you'll receive a digital QR pass to show at the store counter upon pick-up.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: PAYMENT METHOD -->
                <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 shadow-xs border border-slate-200/90 dark:border-slate-800 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-terra-green text-white font-mono text-xs font-black flex items-center justify-center shrink-0">2</span>
                        <div>
                            <h2 class="font-display text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight">How would you like to pay?</h2>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Select online payment via GCash or pay in cash</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3" role="radiogroup" aria-label="Payment Method">

                        <!-- Option 1: GCash Online Payment (Works with both Delivery & Store Pick-up) -->
                        <label id="card-pay-gcash" class="flex items-start gap-3.5 p-4 rounded-2xl border-2 border-terra-green bg-terra-green/5 dark:bg-terra-green/10 hover:border-terra-green transition-all cursor-pointer group">
                            <input class="w-4 h-4 mt-1 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green payment-radio shrink-0" name="payment_method" id="pay-radio-gcash" type="radio" value="gcash" checked>
                            <div class="w-10 h-10 rounded-xl bg-[#007DFE]/10 text-[#007DFE] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_balance_wallet</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">GCash (via PayMongo)</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#007DFE]/10 text-[#007DFE] border border-[#007DFE]/20 font-mono">Online Payment</span>
                                </div>
                                <p id="gcash-helper-text" class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                                    Pay securely online via GCash. Selecting this option does not charge you yet — you will review your order before redirecting to PayMongo.
                                </p>
                            </div>
                        </label>

                        <!-- Option 2: Cash Payment (Dynamically adapts between Cash on Delivery and Pay at Store Counter) -->
                        <label id="card-pay-cash" class="flex items-start gap-3.5 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 hover:border-terra-green/60 transition-all cursor-pointer group">
                            <input class="w-4 h-4 mt-1 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green payment-radio shrink-0" name="payment_method" id="pay-radio-cash" type="radio" value="cod">
                            <div id="cash-icon-box" class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <span id="cash-icon-symbol" class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform"><?= $defaultFulfillment === 'pickup' ? 'storefront' : 'payments' ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span id="cash-title-text" class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                                        <?= $defaultFulfillment === 'pickup' ? 'Pay at Store Counter (Cash)' : 'Cash on Delivery (COD)' ?>
                                    </span>
                                    <span id="cash-badge-text" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-mono">
                                        <?= $defaultFulfillment === 'pickup' ? 'Pay on Pick-up' : 'Pay on Delivery' ?>
                                    </span>
                                </div>
                                <p id="cash-helper-text" class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                    <?= $defaultFulfillment === 'pickup'
                                        ? 'Pay cash directly at ' . esc($shop['shop_name'] ?? 'the shop counter') . ' when you pick up your order.'
                                        : 'Pay cash directly to the delivery rider when your order arrives at your doorstep.' ?>
                                </p>
                            </div>
                        </label>

                    </div>
                </div>

                <!-- Order Cost Summary & Review CTA -->
                <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 shadow-xs border border-slate-200/90 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="font-display text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Order Summary</h2>
                        <span id="summary-selection-pill" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-terra-green/10 text-terra-green dark:text-emerald-400 font-mono">
                            <?= $defaultFulfillment === 'pickup' ? 'Store Pick-up · GCash' : 'Delivery · GCash' ?>
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Item Subtotal (<?= (int) $quantity ?> <?= $quantity === 1 ? 'unit' : 'units' ?>)</span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono" id="subtotal-display">₱<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span id="shipping-label-text"><?= $defaultFulfillment === 'pickup' ? 'Store Pick-up Fee' : 'Doorstep Delivery Fee' ?></span>
                            <span class="font-bold text-terra-green dark:text-emerald-400 font-mono" id="shipping-display">
                                <?= $defaultFulfillment === 'pickup' ? 'FREE (₱0.00)' : '₱' . number_format($initialShipping, 2) ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-baseline text-base font-bold text-slate-900 dark:text-white border-t border-slate-100 dark:border-slate-800 pt-3">
                            <span>Total Payable</span>
                            <span class="text-terra-green dark:text-emerald-400 text-xl sm:text-2xl font-black font-mono" id="total-display">₱<?= number_format($subtotal + $initialShipping, 2) ?></span>
                        </div>
                    </div>

                    <button type="submit" id="place-order-btn" class="w-full min-h-[48px] py-3.5 px-5 bg-terra-green hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2 mt-2">
                        <span id="place-order-btn-text">Review &amp; Pay via GCash (₱<?= number_format($subtotal + $initialShipping, 2) ?>)</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>

                    <p class="text-[11px] text-center text-slate-400">
                        You will review a final summary of your order before any payment or submission is processed.
                    </p>
                </div>

            </form>

        </div>

    </div>

</main>

<!-- Non-Confusing Order Review & Confirmation Modal -->
<div id="order-review-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4" role="dialog" aria-modal="true" aria-labelledby="review-modal-title">
    <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-5 sm:p-7 w-full max-w-md shadow-2xl relative space-y-5 animate-scale-up">
        
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div id="review-modal-icon-box" class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                    <span id="review-modal-icon" class="material-symbols-outlined text-[22px]">fact_check</span>
                </div>
                <div>
                    <h3 id="review-modal-title" class="font-display text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">Review Your Order</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Please verify your receiving and payment details</p>
                </div>
            </div>
            <button type="button" id="btn-close-review-modal" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" aria-label="Close modal">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Clear Summary Breakdown -->
        <div class="space-y-3 bg-slate-50 dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 text-xs">
            <div class="flex items-start justify-between gap-3">
                <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Merchant:</span>
                <span class="font-bold text-slate-900 dark:text-white text-right"><?= esc($shop['shop_name'] ?? 'Partner Merchant') ?></span>
            </div>

            <div class="flex items-start justify-between gap-3 border-t border-slate-200/70 dark:border-slate-700/70 pt-2.5">
                <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Receiving Method:</span>
                <span id="review-fulfillment-badge" class="font-bold text-slate-900 dark:text-white text-right">Doorstep Delivery</span>
            </div>

            <div class="flex items-start justify-between gap-3">
                <span id="review-location-label" class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Delivery Address:</span>
                <span id="review-location-value" class="font-semibold text-slate-700 dark:text-slate-300 text-right line-clamp-2"></span>
            </div>

            <div class="flex items-start justify-between gap-3 border-t border-slate-200/70 dark:border-slate-700/70 pt-2.5">
                <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Payment Method:</span>
                <span id="review-payment-badge" class="font-bold text-slate-900 dark:text-white text-right">GCash (via PayMongo)</span>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-slate-200 dark:border-slate-700 pt-3 text-sm">
                <span class="font-bold text-slate-900 dark:text-white">Total Payable:</span>
                <span id="review-total-amount" class="text-base sm:text-lg font-black text-terra-green dark:text-emerald-400 font-mono">₱0.00</span>
            </div>
        </div>

        <!-- Reassurance Notice for GCash / Cash -->
        <div id="review-payment-note" class="p-3.5 rounded-2xl bg-[#007DFE]/10 border border-[#007DFE]/25 text-xs text-slate-700 dark:text-slate-200 flex items-start gap-2.5">
            <span id="review-note-icon" class="material-symbols-outlined text-[#007DFE] text-[18px] shrink-0 mt-0.5">verified_user</span>
            <span id="review-note-text" class="leading-relaxed">
                Next, you will be redirected to <strong>PayMongo's secure GCash checkout</strong>. Your GCash account will only be charged after you authorize the payment.
            </span>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-1">
            <button type="button" id="btn-cancel-review" class="min-h-[42px] px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Go Back &amp; Edit
            </button>
            <button type="button" id="btn-confirm-submit" class="min-h-[42px] px-5 py-2.5 bg-terra-green hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md active:scale-95 transition-all flex items-center gap-1.5">
                <span id="btn-confirm-submit-text">Proceed to GCash</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </button>
        </div>
    </div>
</div>

<!-- Floating Go Back Button -->
<a href="<?= base_url('product/' . $product['id']) ?>" aria-label="Go Back" class="hidden md:flex fixed bottom-8 left-8 w-12 h-12 bg-white dark:bg-slate-900 text-terra-green dark:text-emerald-400 rounded-full shadow-lg items-center justify-center z-40 transition-transform border border-slate-200 dark:border-slate-800 duration-300 hover:scale-105 hover:shadow-xl">
    <span class="material-symbols-outlined text-[24px]">arrow_back</span>
</a>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var subtotal       = <?= json_encode((float) $subtotal) ?>;
    var baseShipping   = <?= json_encode((float) $shipping) ?>;
    var isDeliverable  = <?= json_encode((bool) ($shippingQuote['deliverable'] ?? true)) ?>;
    var shopId         = <?= json_encode((int) ($product['shop_id'] ?? $shop['id'] ?? 0)) ?>;
    var shopName       = <?= json_encode($shop['shop_name'] ?? 'the shop counter') ?>;
    var quoteUrl       = <?= json_encode(base_url('customer/shipping/quote')) ?>;
    var csrfName       = <?= json_encode($csrfName ?? csrf_token()) ?>;
    var csrfHash       = <?= json_encode($csrfHash ?? csrf_hash()) ?>;

    var form              = document.getElementById('buy-now-form');
    var fulfillmentRadios = document.querySelectorAll('.fulfillment-radio');
    var paymentRadios     = document.querySelectorAll('.payment-radio');
    var fulfillmentInput  = document.getElementById('form-fulfillment-method');
    var placeOrderBtn     = document.getElementById('place-order-btn');

    var cardDelivery      = document.getElementById('card-fulfillment-delivery');
    var cardPickup        = document.getElementById('card-fulfillment-pickup');
    var cardPayGcash      = document.getElementById('card-pay-gcash');
    var cardPayCash       = document.getElementById('card-pay-cash');

    var payRadioGcash     = document.getElementById('pay-radio-gcash');
    var payRadioCash      = document.getElementById('pay-radio-cash');
    var cashIconSymbol    = document.getElementById('cash-icon-symbol');
    var cashTitleText     = document.getElementById('cash-title-text');
    var cashBadgeText     = document.getElementById('cash-badge-text');
    var cashHelperText    = document.getElementById('cash-helper-text');
    var gcashHelperText   = document.getElementById('gcash-helper-text');

    var addressSection    = document.getElementById('address-section');
    var pickupInfoSection = document.getElementById('pickup-info-section');
    var addressSelect     = document.getElementById('shipping-address-select');

    var summaryPill       = document.getElementById('summary-selection-pill');
    var shippingLabelText = document.getElementById('shipping-label-text');
    var shippingDisplay   = document.getElementById('shipping-display');
    var totalDisplay      = document.getElementById('total-display');
    var placeOrderBtnText = document.getElementById('place-order-btn-text');

    var deliverySubtext   = document.getElementById('buynow-delivery-subtext');
    var deliveryFeeBadge  = document.getElementById('buynow-delivery-fee-badge');
    var routeDistText     = document.getElementById('buynow-route-distance-text');
    var routeFormulaText  = document.getElementById('buynow-route-formula-text');
    var distErrorBox      = document.getElementById('buynow-distance-error');
    var distErrorText     = document.getElementById('buynow-distance-error-text');

    // Review Modal Elements
    var reviewModal           = document.getElementById('order-review-modal');
    var btnCloseReview        = document.getElementById('btn-close-review-modal');
    var btnCancelReview       = document.getElementById('btn-cancel-review');
    var btnConfirmSubmit      = document.getElementById('btn-confirm-submit');
    var btnConfirmSubmitText  = document.getElementById('btn-confirm-submit-text');
    var reviewFulfillmentBadge = document.getElementById('review-fulfillment-badge');
    var reviewLocationLabel   = document.getElementById('review-location-label');
    var reviewLocationValue   = document.getElementById('review-location-value');
    var reviewPaymentBadge    = document.getElementById('review-payment-badge');
    var reviewTotalAmount     = document.getElementById('review-total-amount');
    var reviewPaymentNote     = document.getElementById('review-payment-note');
    var reviewNoteIcon        = document.getElementById('review-note-icon');
    var reviewNoteText        = document.getElementById('review-note-text');

    var isConfirmedSubmit = false;

    function formatMoney(amount) {
        return '₱' + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function getSelectedFulfillment() {
        var selected = '';
        fulfillmentRadios.forEach(function (r) {
            if (r.checked && !r.disabled) selected = r.value;
        });
        if (!selected) {
            for (var i = 0; i < fulfillmentRadios.length; i++) {
                if (!fulfillmentRadios[i].disabled) {
                    fulfillmentRadios[i].checked = true;
                    selected = fulfillmentRadios[i].value;
                    break;
                }
            }
        }
        return selected || 'delivery';
    }

    function setCardActiveState(cardEl, isActive) {
        if (!cardEl) return;
        if (isActive) {
            cardEl.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-slate-50/70', 'dark:bg-slate-800/60');
            cardEl.classList.add('border-terra-green', 'bg-terra-green/5', 'dark:bg-terra-green/10');
        } else {
            cardEl.classList.remove('border-terra-green', 'bg-terra-green/5', 'dark:bg-terra-green/10');
            cardEl.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-slate-50/70', 'dark:bg-slate-800/60');
        }
    }

    function updateCheckoutUI() {
        var fulfillment = getSelectedFulfillment();
        var isPickup    = (fulfillment === 'pickup');
        var shippingFee = isPickup ? 0.00 : baseShipping;
        var total       = subtotal + shippingFee;

        // 1. Sync hidden fulfillment_method input
        if (fulfillmentInput) {
            fulfillmentInput.value = fulfillment;
        }

        // 2. Highlight active Step 1 card
        setCardActiveState(cardDelivery, !isPickup);
        setCardActiveState(cardPickup, isPickup);

        // 3. Toggle Address Section vs Pick-up Info Section
        var textAddressInput = addressSection ? addressSection.querySelector('input[name="shipping_address"]') : null;
        if (isPickup) {
            if (addressSection) addressSection.classList.add('hidden');
            if (pickupInfoSection) pickupInfoSection.classList.remove('hidden');
            if (addressSelect) addressSelect.removeAttribute('required');
            if (textAddressInput) textAddressInput.removeAttribute('required');
        } else {
            if (addressSection) addressSection.classList.remove('hidden');
            if (pickupInfoSection) pickupInfoSection.classList.add('hidden');
            if (addressSelect) addressSelect.setAttribute('required', 'required');
            if (textAddressInput) textAddressInput.setAttribute('required', 'required');
        }

        // 4. Adapt Step 2 Cash Payment option dynamically to match Step 1 Fulfillment
        if (payRadioCash) {
            payRadioCash.value = 'cod';
        }
        if (cashIconSymbol) {
            cashIconSymbol.textContent = isPickup ? 'storefront' : 'payments';
        }
        if (cashTitleText) {
            cashTitleText.textContent = isPickup ? 'Pay at Store Counter (Cash)' : 'Cash on Delivery (COD)';
        }
        if (cashBadgeText) {
            cashBadgeText.textContent = isPickup ? 'Pay on Pick-up' : 'Pay on Delivery';
        }
        if (cashHelperText) {
            cashHelperText.textContent = isPickup
                ? 'Pay cash directly at ' + shopName + ' when you pick up your order.'
                : 'Pay cash directly to the delivery rider when your order arrives at your doorstep.';
        }
        if (gcashHelperText) {
            gcashHelperText.textContent = isPickup
                ? 'Pay online via GCash for Store Pick-up (Free shipping). You will review your order before redirecting to PayMongo.'
                : 'Pay online via GCash for Doorstep Delivery. You will review your order before redirecting to PayMongo.';
        }

        // 5. Highlight active Step 2 card
        var isGcash = payRadioGcash && payRadioGcash.checked;
        setCardActiveState(cardPayGcash, isGcash);
        setCardActiveState(cardPayCash, !isGcash);

        // 6. Update Order Summary & CTA Button
        var fulfillmentShort = isPickup ? 'Store Pick-up' : 'Delivery';
        var paymentShort     = isGcash ? 'GCash' : (isPickup ? 'Pay at Store' : 'COD');
        if (summaryPill) {
            summaryPill.textContent = fulfillmentShort + ' · ' + paymentShort;
        }
        if (shippingLabelText) {
            shippingLabelText.textContent = isPickup ? 'Store Pick-up Fee' : 'Doorstep Delivery Fee';
        }
        if (shippingDisplay) {
            shippingDisplay.textContent = isPickup ? 'FREE (₱0.00)' : formatMoney(shippingFee);
        }
        if (totalDisplay) {
            totalDisplay.textContent = formatMoney(total);
        }

        var blockedByDistance = (!isPickup && !isDeliverable);
        if (placeOrderBtn) {
            placeOrderBtn.disabled = blockedByDistance;
            if (blockedByDistance) {
                placeOrderBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                placeOrderBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        if (placeOrderBtnText) {
            if (blockedByDistance) {
                placeOrderBtnText.textContent = 'Address Outside Delivery Limit — Choose Pick-up';
            } else if (isGcash) {
                placeOrderBtnText.textContent = 'Review & Pay via GCash (' + formatMoney(total) + ')';
            } else if (isPickup) {
                placeOrderBtnText.textContent = 'Review & Place Pick-up Order (' + formatMoney(total) + ')';
            } else {
                placeOrderBtnText.textContent = 'Review & Place COD Order (' + formatMoney(total) + ')';
            }
        }
    }

    function fetchAddressShippingQuote() {
        if (!addressSelect || !addressSelect.value || !shopId) return;
        var fd = new FormData();
        fd.append(csrfName, csrfHash);
        fd.append('address_id', addressSelect.value);
        fd.append('shop_ids', String(shopId));

        if (routeDistText) {
            routeDistText.innerHTML = 'Calculating road route...';
        }

        fetch(quoteUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res && res.csrf_hash) {
                csrfHash = res.csrf_hash;
                var csrfInput = form ? form.querySelector('input[name="' + csrfName + '"]') : null;
                if (csrfInput) csrfInput.value = csrfHash;
            }
            if (!res || !res.success) return;

            var q = (res.quotes && res.quotes[shopId]) ? res.quotes[shopId] : null;
            if (q) {
                baseShipping  = parseFloat(q.shipping_fee) || 0;
                isDeliverable = !!q.deliverable;

                if (deliverySubtext) {
                    deliverySubtext.textContent = (q.distance_text || '—') + (q.distance_approximate ? '' : ' road route');
                }
                var distNote = document.getElementById('buynow-distance-note');
                if (distNote) {
                    if (q.distance_approximate && q.accuracy_notice) {
                        distNote.textContent = q.accuracy_notice;
                        distNote.classList.remove('hidden');
                    } else {
                        distNote.classList.add('hidden');
                    }
                }
                if (deliveryFeeBadge) {
                    deliveryFeeBadge.textContent = formatMoney(baseShipping);
                }
                if (routeDistText) {
                    routeDistText.innerHTML = 'Road Distance: <strong>' + (q.distance_text || '—') + '</strong>';
                }
                if (routeFormulaText) {
                    var formula = 'Base ' + formatMoney(q.base_shipping_fee) + ' (up to ' + Number(q.included_distance_km).toFixed(1) + ' km limit)';
                    if (Number(q.extra_distance_fee) > 0) {
                        var steps = parseInt(q.extra_steps, 10) || 1;
                        formula += ' + ' + formatMoney(q.extra_distance_fee) + ' (' + steps + (steps === 1 ? ' step' : ' steps') + ' beyond limit)';
                    }
                    routeFormulaText.textContent = formula;
                }
                if (distErrorBox && distErrorText) {
                    if (!isDeliverable) {
                        distErrorText.textContent = q.error || 'Selected address exceeds the maximum delivery distance.';
                        distErrorBox.classList.remove('hidden');
                    } else {
                        distErrorBox.classList.add('hidden');
                    }
                }
                updateCheckoutUI();
            }
        })
        .catch(function () {});
    }

    if (addressSelect) {
        addressSelect.addEventListener('change', fetchAddressShippingQuote);
    }

    fulfillmentRadios.forEach(function (r) {
        r.addEventListener('change', updateCheckoutUI);
    });

    paymentRadios.forEach(function (r) {
        r.addEventListener('change', updateCheckoutUI);
    });

    updateCheckoutUI();

    // Form Submit -> Open Clear Order Review Modal
    if (form) {
        form.addEventListener('submit', function (e) {
            if (isConfirmedSubmit) return;
            e.preventDefault();

            var fulfillment = getSelectedFulfillment();
            var isPickup    = (fulfillment === 'pickup');
            var isGcash     = payRadioGcash && payRadioGcash.checked;
            var shippingFee = isPickup ? 0.00 : baseShipping;
            var total       = subtotal + shippingFee;

            if (reviewFulfillmentBadge) {
                reviewFulfillmentBadge.textContent = isPickup ? 'Store Pick-up (FREE)' : 'Doorstep Delivery (' + formatMoney(shippingFee) + ')';
            }

            if (reviewLocationLabel && reviewLocationValue) {
                if (isPickup) {
                    reviewLocationLabel.textContent = 'Pick-up Branch:';
                    reviewLocationValue.textContent = shopName + ' (Present Digital QR Pass)';
                } else {
                    reviewLocationLabel.textContent = 'Delivery Address:';
                    if (addressSelect && addressSelect.options.length > 0) {
                        reviewLocationValue.textContent = addressSelect.options[addressSelect.selectedIndex].text.trim();
                    } else {
                        var textInput = addressSection ? addressSection.querySelector('input[name="shipping_address"]') : null;
                        reviewLocationValue.textContent = textInput ? textInput.value : 'Polomolok, South Cotabato';
                    }
                }
            }

            if (reviewPaymentBadge) {
                reviewPaymentBadge.textContent = isGcash
                    ? 'GCash (Online via PayMongo)'
                    : (isPickup ? 'Pay at Store Counter (Cash)' : 'Cash on Delivery (COD)');
            }

            if (reviewTotalAmount) {
                reviewTotalAmount.textContent = formatMoney(total);
            }

            if (reviewPaymentNote && reviewNoteIcon && reviewNoteText) {
                if (isGcash) {
                    reviewPaymentNote.className = 'p-3.5 rounded-2xl bg-[#007DFE]/10 border border-[#007DFE]/25 text-xs text-slate-700 dark:text-slate-200 flex items-start gap-2.5';
                    reviewNoteIcon.className = 'material-symbols-outlined text-[#007DFE] text-[18px] shrink-0 mt-0.5';
                    reviewNoteIcon.textContent = 'account_balance_wallet';
                    reviewNoteText.innerHTML = 'Clicking <strong>Proceed to GCash</strong> will take you to PayMongo\'s secure checkout page for your <strong>' + (isPickup ? 'Store Pick-up' : 'Doorstep Delivery') + '</strong> order. You won\'t be charged until you authorize payment.';
                    if (btnConfirmSubmitText) btnConfirmSubmitText.textContent = 'Proceed to GCash (' + formatMoney(total) + ')';
                } else {
                    reviewPaymentNote.className = 'p-3.5 rounded-2xl bg-terra-green/10 border border-terra-green/25 text-xs text-slate-700 dark:text-slate-200 flex items-start gap-2.5';
                    reviewNoteIcon.className = 'material-symbols-outlined text-terra-green text-[18px] shrink-0 mt-0.5';
                    reviewNoteIcon.textContent = isPickup ? 'storefront' : 'local_shipping';
                    reviewNoteText.innerHTML = isPickup
                        ? 'Your order will be sent to <strong>' + shopName + '</strong> for preparation. Please pay <strong>' + formatMoney(total) + '</strong> at the store counter upon pick-up.'
                        : 'Your order will be prepared for doorstep delivery. Please prepare <strong>' + formatMoney(total) + '</strong> in cash upon arrival.';
                    if (btnConfirmSubmitText) btnConfirmSubmitText.textContent = 'Confirm & Place Order';
                }
            }

            if (reviewModal) {
                reviewModal.classList.remove('hidden');
            }
        });
    }

    function closeReviewModal() {
        if (reviewModal) reviewModal.classList.add('hidden');
    }

    if (btnCloseReview) btnCloseReview.addEventListener('click', closeReviewModal);
    if (btnCancelReview) btnCancelReview.addEventListener('click', closeReviewModal);
    if (reviewModal) {
        reviewModal.addEventListener('click', function (e) {
            if (e.target === reviewModal) closeReviewModal();
        });
    }

    if (btnConfirmSubmit) {
        btnConfirmSubmit.addEventListener('click', function () {
            isConfirmedSubmit = true;
            closeReviewModal();
            if (form) form.submit();
        });
    }
})();
</script>
<?= $this->endSection() ?>
