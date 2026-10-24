<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<style>
    #checkout-sidebar {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .store-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
</style>

<main class="max-w-7xl mx-auto px-3.5 sm:px-6 md:px-8 lg:px-10 py-5 md:py-10 flex-grow w-full pb-36 lg:pb-12 flex flex-col gap-5 sm:gap-6 overflow-x-hidden">

    <?php
        $shipping = (float) ($shippingFee ?? 50.00);
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();
        $totalItemsCount = count($cartItems ?? []);
    ?>

    <!-- Page Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                <a href="<?= base_url('/') ?>" class="hover:text-terra-green transition-colors">Marketplace</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-slate-900 dark:text-white font-bold">Shopping Cart</span>
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex flex-wrap items-center gap-2.5">
                <span>Shopping Cart</span>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 border border-terra-green/20 font-mono shadow-2xs">
                    <?= $totalItemsCount ?> <?= $totalItemsCount === 1 ? 'item' : 'items' ?>
                </span>
            </h1>
        </div>
        <div>
            <a class="inline-flex items-center gap-1.5 text-xs font-bold text-terra-green dark:text-emerald-400 hover:text-emerald-700 transition-colors" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                <span>Continue Shopping</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs animate-fade-in">
            <span class="material-symbols-outlined text-lg shrink-0">error</span>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs animate-fade-in">
            <span class="material-symbols-outlined text-lg shrink-0">check_circle</span>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <!-- Empty Cart State -->
        <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-14 text-center max-w-lg mx-auto my-8 border border-slate-200/90 dark:border-slate-800 shadow-sm">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-terra-green/10 text-terra-green flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-outlined text-4xl sm:text-5xl">remove_shopping_cart</span>
            </div>
            <h2 class="font-display text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2">Your Shopping Cart is Empty</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mb-6 max-w-sm mx-auto leading-relaxed">Looks like you haven't added anything to your cart yet. Explore verified local shops and printing services today!</p>
            <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-2 bg-terra-green hover:bg-emerald-700 text-white px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span>Start Shopping Now</span>
            </a>
        </div>
    <?php else: ?>

        <form action="<?= base_url('cart/checkout') ?>" method="POST" class="grid grid-cols-12 gap-5 lg:gap-8 items-start" id="cart-checkout-form">
            <?= csrf_field() ?>
            <input type="hidden" name="fulfillment_method" id="form-fulfillment-method" value="delivery">

            <!-- Left: Store-Grouped Cart Items -->
            <div class="col-span-12 lg:col-span-7 xl:col-span-8 space-y-4 min-w-0">

                <!-- Global Select All Toolbar -->
                <div class="bento-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 px-3.5 sm:px-4 py-3 flex items-center justify-between gap-2 shadow-2xs">
                    <label class="flex items-center gap-2.5 sm:gap-3 cursor-pointer select-none group min-h-[36px]">
                        <input class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-700 text-terra-green focus:ring-terra-green/40 transition-all cursor-pointer shrink-0" id="select-all" type="checkbox">
                        <span class="text-[11px] sm:text-xs font-black text-slate-900 dark:text-white group-hover:text-terra-green transition-colors tracking-wide font-mono">
                            SELECT ALL (<?= $totalItemsCount ?>)
                        </span>
                    </label>

                    <button type="button" id="delete-selected" class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 px-2.5 sm:px-3 py-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-all active:scale-95 opacity-50 pointer-events-none shrink-0" disabled>
                        <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                        <span>Delete Selected</span>
                    </button>
                </div>

                <!-- Stores and Products List -->
                <?php
                    $groupedByShop = $itemsByShop ?? [];
                    if (empty($groupedByShop)) {
                        foreach ($cartItems as $it) {
                            $sId = (int) ($it['shop_id'] ?? 0);
                            $groupedByShop[$sId]['shop_id']         = $sId;
                            $groupedByShop[$sId]['shop_name']       = $it['shop_name'] ?? 'Merchant Store';
                            $groupedByShop[$sId]['shop_slug']       = $it['shop_slug'] ?? '';
                            $groupedByShop[$sId]['offers_delivery'] = (int) ($it['offers_delivery'] ?? 1);
                            $groupedByShop[$sId]['offers_pickup']   = (int) ($it['offers_pickup'] ?? 1);
                            $groupedByShop[$sId]['items'][]         = $it;
                        }
                    }
                ?>

                <?php foreach ($groupedByShop as $shopId => $shopGroup): ?>
                    <div class="store-card bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 overflow-hidden shadow-2xs hover:shadow-sm" data-shop-card="<?= (int) $shopId ?>">
                        
                        <!-- Store Header (Responsive on Mobile & Desktop) -->
                        <div class="px-3.5 sm:px-5 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                                <input type="checkbox" class="store-checkbox w-4 h-4 sm:w-5 sm:h-5 rounded border-slate-300 dark:border-slate-700 text-terra-green focus:ring-terra-green/40 transition-all cursor-pointer shrink-0" data-shop-id="<?= (int) $shopId ?>" aria-label="Select all items from <?= esc($shopGroup['shop_name']) ?>">
                                <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                                    <span class="material-symbols-outlined text-[18px] text-terra-green dark:text-emerald-400 shrink-0">storefront</span>
                                    <a href="<?= !empty($shopGroup['shop_slug']) ? base_url('shop/' . esc($shopGroup['shop_slug'])) : base_url('shop/' . (int) $shopId) ?>" class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white hover:text-terra-green dark:hover:text-emerald-400 transition-colors truncate flex items-center gap-0.5">
                                        <span class="truncate"><?= esc($shopGroup['shop_name']) ?></span>
                                        <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">chevron_right</span>
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <?php if (!empty($shopGroup['offers_delivery'])): ?>
                                    <span class="inline-flex items-center gap-0.5 px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-terra-green/10 text-terra-green dark:text-emerald-400 border border-terra-green/20" title="Doorstep Delivery Available">
                                        <span class="material-symbols-outlined text-[12px]">local_shipping</span>
                                        <span>Delivery</span>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($shopGroup['offers_pickup'])): ?>
                                    <span class="inline-flex items-center gap-0.5 px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20" title="Store Pick-up Available">
                                        <span class="material-symbols-outlined text-[12px]">store</span>
                                        <span>Pick-up</span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Product Items Inside This Store -->
                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            <?php foreach ($shopGroup['items'] as $item): ?>
                                <?php
                                    $itemPrice     = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
                                    $itemQty       = (int) ($item['quantity'] ?? 1);
                                    $itemLineTotal = $itemPrice * $itemQty;
                                    $itemImage     = product_image_url($item['image_url'] ?? null, 'thumbnail');
                                    $stockQty      = (int) ($item['stock_quantity'] ?? 99);
                                ?>
                                <div class="p-3.5 sm:p-5 flex gap-3 sm:gap-4 items-start group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors" data-item-row="<?= (int) $item['id'] ?>">
                                    
                                    <!-- Item Checkbox -->
                                    <div class="pt-1.5 sm:pt-3 shrink-0">
                                        <input class="cart-item-checkbox w-4 h-4 sm:w-5 sm:h-5 rounded-md border-slate-300 dark:border-slate-700 text-terra-green focus:ring-terra-green/40 transition-all cursor-pointer"
                                               name="selected_items[]"
                                               type="checkbox"
                                               value="<?= (int) $item['id'] ?>"
                                               data-id="<?= (int) $item['id'] ?>"
                                               data-shop-id="<?= (int) $shopId ?>"
                                               data-price="<?= $itemPrice ?>"
                                               data-qty="<?= $itemQty ?>"
                                               data-shop-name="<?= esc($shopGroup['shop_name']) ?>"
                                               data-offers-delivery="<?= (int) ($shopGroup['offers_delivery'] ?? 1) ?>"
                                               data-offers-pickup="<?= (int) ($shopGroup['offers_pickup'] ?? 1) ?>"
                                               aria-label="Select <?= esc($item['product_name'] ?? $item['name'] ?? 'Product') ?>"
                                               <?= !empty($item['is_selected']) ? 'checked' : '' ?>>
                                    </div>

                                    <!-- Product Image Thumbnail (Fixed valid Tailwind v3 w-20 h-20 on mobile, sm:w-24 sm:h-24 on desktop) -->
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shrink-0 border border-slate-200/90 dark:border-slate-700 relative flex items-center justify-center p-1.5">
                                        <?php if (!empty($itemImage)): ?>
                                            <img alt="<?= esc($item['product_name'] ?? $item['name'] ?? 'Product') ?>" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" src="<?= esc($itemImage) ?>">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <span class="material-symbols-outlined text-3xl">inventory_2</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Product Details & Controls (Mobile-First Stack, Desktop Row) -->
                                    <div class="flex flex-col flex-1 justify-between min-w-0 self-stretch gap-2.5">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1.5 sm:gap-3">
                                            <div class="min-w-0 flex-1">
                                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug break-words">
                                                    <?= esc($item['product_name'] ?? $item['name'] ?? 'Product') ?>
                                                </h3>
                                                <?php if (!empty($item['variant_label'])): ?>
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-terra-green/10 text-terra-green dark:text-emerald-400 text-[10px] sm:text-xs font-semibold rounded-md border border-terra-green/20 max-w-full truncate">
                                                        <span class="material-symbols-outlined text-[12px] shrink-0">tune</span>
                                                        <span class="truncate"><?= esc($item['variant_label']) ?></span>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Unit Price / Total -->
                                            <div class="flex sm:flex-col items-baseline sm:items-end justify-between sm:justify-start gap-2 shrink-0">
                                                <p class="text-xs sm:text-sm font-black text-terra-green dark:text-emerald-400 font-mono">₱<?= number_format($itemPrice, 2) ?></p>
                                                <?php if ($itemQty > 1): ?>
                                                    <p class="text-[10px] text-slate-400 font-mono">Subtotal: ₱<?= number_format($itemLineTotal, 2) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Quantity Controls & Remove Button -->
                                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                            <div class="inline-flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 shadow-2xs overflow-hidden">
                                                <button type="button" class="qty-minus w-8 h-8 sm:w-9 sm:h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white font-bold text-sm transition-colors active:scale-95 select-none" data-id="<?= (int) $item['id'] ?>" data-stock="<?= $stockQty ?>" aria-label="Decrease quantity">−</button>
                                                <span class="qty-value px-2.5 sm:px-3 py-1 text-xs sm:text-sm font-bold text-slate-900 dark:text-white min-w-[32px] text-center border-x border-slate-200 dark:border-slate-700 font-mono select-none"><?= $itemQty ?></span>
                                                <button type="button" class="qty-plus w-8 h-8 sm:w-9 sm:h-8 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white font-bold text-sm transition-colors active:scale-95 select-none" data-id="<?= (int) $item['id'] ?>" data-stock="<?= $stockQty ?>" aria-label="Increase quantity">+</button>
                                            </div>

                                            <a href="<?= base_url('cart/remove/' . $item['id']) ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600/90 hover:text-rose-600 px-2.5 py-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors shrink-0" aria-label="Remove item" title="Remove from cart">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                                <span class="text-[11px] sm:text-xs">Remove</span>
                                            </a>
                                        </div>

                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

            <!-- Right: Guided 2-Step Checkout Sidebar -->
            <div class="col-span-12 lg:col-span-5 xl:col-span-4 min-w-0">
                <div class="space-y-4 lg:sticky lg:top-24" id="checkout-sidebar">

                    <!-- Main Checkout Configuration & Summary Card -->
                    <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h2 class="font-display text-base sm:text-lg font-bold text-slate-900 dark:text-white">Checkout &amp; Summary</h2>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 font-mono" id="selected-count-badge">0 items selected</span>
                        </div>

                        <!-- STEP 1: RECEIVING METHOD (FULFILLMENT) -->
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-terra-green text-white font-mono text-[11px] font-black flex items-center justify-center shrink-0">1</span>
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider">How to Receive Order</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-2.5" role="radiogroup" aria-label="Receiving Method">
                                <!-- Doorstep Delivery Card -->
                                <label id="card-cart-fulfillment-delivery" class="flex items-start gap-2.5 p-3 rounded-2xl border-2 border-terra-green bg-terra-green/5 dark:bg-terra-green/10 hover:border-terra-green transition-all cursor-pointer">
                                    <input type="radio" name="cart_fulfillment_choice" value="delivery" id="cart-fulfillment-delivery" checked class="w-4 h-4 mt-0.5 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[15px] text-terra-green dark:text-emerald-400">local_shipping</span>
                                                <span>Delivery</span>
                                            </span>
                                            <span id="badge-fee-delivery" class="text-[10px] font-black font-mono text-slate-700 dark:text-slate-300">₱<?= number_format($shipping, 2) ?></span>
                                        </div>
                                        <span id="subtext-fulfillment-delivery" class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5 leading-snug">Delivered to your address</span>
                                    </div>
                                </label>

                                <!-- Store Pick-up Card -->
                                <label id="card-cart-fulfillment-pickup" class="flex items-start gap-2.5 p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 hover:border-terra-green/60 transition-all cursor-pointer">
                                    <input type="radio" name="cart_fulfillment_choice" value="pickup" id="cart-fulfillment-pickup" class="w-4 h-4 mt-0.5 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[15px] text-amber-600 dark:text-amber-400">storefront</span>
                                                <span>Pick-up</span>
                                            </span>
                                            <span class="text-[10px] font-black font-mono px-1.5 py-0.2 rounded bg-terra-green/15 text-terra-green dark:text-emerald-400">FREE</span>
                                        </div>
                                        <span id="subtext-fulfillment-pickup" class="text-[10px] text-slate-500 dark:text-slate-400 block mt-0.5 leading-snug">Pick up at shop branch</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Contextual Delivery Address Selector (Shown when Delivery is selected) -->
                            <div id="cart-address-card" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <label for="cart-shipping-address-select" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px] text-terra-green dark:text-emerald-400">location_on</span>
                                        <span>Delivery Address</span>
                                    </label>
                                    <a href="<?= base_url('customer/addresses') ?>" class="text-[11px] font-bold text-terra-green dark:text-emerald-400 hover:underline">Manage</a>
                                </div>

                                <?php if (!empty($addresses)): ?>
                                    <select name="shipping_address_id" id="cart-shipping-address-select" class="w-full max-w-full truncate p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green focus:border-terra-green" required>
                                        <?php foreach ($addresses as $addr): ?>
                                            <option value="<?= $addr['id'] ?>" <?= !empty($addr['is_default']) ? 'selected' : '' ?>>
                                                <?= esc($addr['recipient_name']) ?> — <?= esc($addr['address_line1']) ?>, <?= esc($addr['city']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="cart-distance-pill" class="flex items-center justify-between gap-2 pt-1 text-[10px] text-slate-500 dark:text-slate-400">
                                        <span class="inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px] text-terra-green">route</span>
                                            <span id="cart-distance-summary">Distance-based fee via Google Maps Routes</span>
                                        </span>
                                        <span class="font-mono text-[10px]" id="cart-rate-formula">
                                            Base ₱<?= number_format((float) ($shippingSettings['base_shipping_fee'] ?? 50), 0) ?> + ₱<?= number_format((float) ($shippingSettings['additional_fee_per_km'] ?? 10), 0) ?>/km
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">No saved address found. Please enter delivery address:</p>
                                    <input type="text" name="shipping_address" id="cart-shipping-address-input" placeholder="Enter delivery address (Polomolok only)" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green focus:border-terra-green" required>
                                <?php endif; ?>
                            </div>

                            <!-- Contextual Store Pick-up Banner (Shown when Store Pick-up is selected) -->
                            <div id="cart-pickup-banner" class="hidden p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[20px] shrink-0 mt-0.5">qr_code_2</span>
                                <div class="text-[11px] text-slate-700 dark:text-slate-200 leading-relaxed">
                                    <span class="font-bold text-slate-900 dark:text-white block">Free Store Pick-up (₱0.00 Delivery)</span>
                                    <span>Present your digital QR order pass at the merchant branch counter upon pick-up.</span>
                                </div>
                            </div>

                            <!-- Cart Service Restriction Alert -->
                            <div id="cart-service-notice" class="hidden p-3 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-800 dark:text-amber-300 text-xs flex items-start gap-2">
                                <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0 mt-0.5">info</span>
                                <span id="cart-service-notice-text"></span>
                            </div>
                        </div>

                        <!-- STEP 2: PAYMENT METHOD -->
                        <div class="space-y-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-terra-green text-white font-mono text-[11px] font-black flex items-center justify-center shrink-0">2</span>
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Select Payment Method</span>
                            </div>

                            <div class="grid grid-cols-1 gap-2.5" role="radiogroup" aria-label="Payment Method">
                                <!-- Option 1: GCash Online Checkout -->
                                <label id="label-pay-gcash" class="flex items-start gap-3 p-3 rounded-2xl border-2 border-terra-green bg-terra-green/5 dark:bg-terra-green/10 hover:border-terra-green transition-all cursor-pointer group">
                                    <input class="w-4 h-4 mt-0.5 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green shrink-0" name="payment_method" type="radio" value="gcash" id="cart-pay-gcash" checked>
                                    <div class="w-8 h-8 rounded-xl bg-[#007DFE]/10 text-[#007DFE] flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">GCash (PayMongo)</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#007DFE]/10 text-[#007DFE] font-mono">Online</span>
                                        </div>
                                        <span id="subtext-pay-gcash" class="text-[11px] text-slate-500 dark:text-slate-400 block leading-snug">
                                            Review order first, then pay securely via GCash
                                        </span>
                                    </div>
                                </label>

                                <!-- Option 2: Cash Payment (Dynamically switches between Cash on Delivery and Pay at Store Counter) -->
                                <label id="label-pay-cash" class="flex items-start gap-3 p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 hover:border-terra-green/60 transition-all cursor-pointer group">
                                    <input class="w-4 h-4 mt-0.5 text-terra-green border-slate-300 dark:border-slate-600 focus:ring-terra-green shrink-0" name="payment_method" type="radio" value="cod" id="cart-pay-cash">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <span id="icon-pay-cash" class="material-symbols-outlined text-[20px]">payments</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <span id="title-pay-cash" class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Cash on Delivery (COD)</span>
                                            <span id="badge-pay-cash" class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-mono">Cash</span>
                                        </div>
                                        <span id="subtext-pay-cash" class="text-[11px] text-slate-500 dark:text-slate-400 block leading-snug">
                                            Pay cash upon doorstep arrival
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Calculation Breakdown -->
                        <div class="space-y-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400 border-y border-slate-100 dark:border-slate-800 py-3.5">
                            <div class="flex justify-between items-center">
                                <span>Selected Items Subtotal</span>
                                <span class="font-bold text-slate-900 dark:text-white font-mono" id="subtotal-val">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1">
                                    <span id="shipping-fee-label">Estimated Delivery Fee</span>
                                    <span class="material-symbols-outlined text-[14px] text-slate-400" title="₱50 Standard Delivery per store within Polomolok (Free for Store Pick-up)">help</span>
                                </span>
                                <span class="text-terra-green dark:text-emerald-400 font-bold font-mono" id="shipping-val">₱0.00</span>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="flex justify-between items-baseline">
                            <div>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Estimated Total</span>
                                <span class="text-[11px] text-slate-400" id="summary-mode-caption">Delivery · GCash</span>
                            </div>
                            <span class="text-xl sm:text-2xl font-black text-terra-green dark:text-emerald-400 font-mono" id="total-val">₱0.00</span>
                        </div>

                        <!-- Inline Error Notice -->
                        <div id="checkout-inline-error" class="hidden p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-semibold flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] shrink-0">error</span>
                            <span id="checkout-error-text"></span>
                        </div>

                        <!-- Primary Submit Button -->
                        <button type="submit" class="w-full min-h-[46px] py-3.5 px-4 bg-terra-green hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed" id="place-order-btn" disabled>
                            <span id="place-order-btn-text">Select Items to Checkout</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Mobile Sticky Bottom Checkout Drawer (Aligned above mobile bottom nav on <768px, flush bottom on md 768px-1023px) -->
            <div class="lg:hidden fixed bottom-14 md:bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 px-3.5 py-2.5 shadow-[0_-4px_20px_rgba(0,0,0,0.10)] flex items-center justify-between gap-2.5">
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</span>
                        <button type="button" id="mobile-scroll-options-btn" class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 text-[10px] font-bold font-mono">
                            <span id="mobile-selection-pill">Delivery · GCash</span>
                            <span class="material-symbols-outlined text-[12px]">expand_less</span>
                        </button>
                    </div>
                    <span class="text-base sm:text-lg font-black text-terra-green dark:text-emerald-400 leading-tight font-mono truncate" id="mobile-total-val">₱0.00</span>
                    <span class="text-[10px] text-slate-400 font-mono" id="mobile-item-count">0 items selected</span>
                </div>
                <button type="submit" class="min-h-[42px] bg-terra-green hover:bg-emerald-700 text-white px-4 sm:px-6 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-md active:scale-95 transition-all flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed shrink-0" id="mobile-checkout-btn" disabled>
                    <span id="mobile-checkout-btn-text">Review Order</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </button>
            </div>

        </form>

    <?php endif; ?>

<!-- Non-Confusing Checkout Review & Confirmation Modal -->
<div id="checkout-confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="bento-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 space-y-5 animate-scale-up">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div id="modal-icon-bg" class="w-11 h-11 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]" id="modal-icon">fact_check</span>
                </div>
                <div>
                    <h3 class="font-display text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight" id="modal-title">Review Your Order</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400" id="modal-subtitle">Verify your receiving and payment details before proceeding</p>
                </div>
            </div>
            <button type="button" id="modal-close-x-btn" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" aria-label="Close modal">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="space-y-2.5 bg-slate-50 dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 text-xs">
            <div id="modal-shop-info" class="flex items-start justify-between gap-3">
                <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Merchant(s):</span>
                <span id="modal-shop-name" class="font-bold text-slate-900 dark:text-white text-right"></span>
            </div>

            <div class="flex items-start justify-between gap-3 border-t border-slate-200/70 dark:border-slate-700/70 pt-2.5">
                <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Receiving Method:</span>
                <span id="modal-fulfillment-summary" class="font-bold text-slate-900 dark:text-white text-right">Doorstep Delivery</span>
            </div>

            <div class="flex items-start justify-between gap-3">
                <span id="modal-location-label" class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Delivery Address:</span>
                <span id="modal-location-value" class="font-semibold text-slate-700 dark:text-slate-300 text-right line-clamp-2"></span>
            </div>

            <div class="flex items-start justify-between gap-3 border-t border-slate-200/70 dark:border-slate-700/70 pt-2.5">
                <span class="text-slate-500 dark:text-slate-400 font-semibold shrink-0">Payment Method:</span>
                <span id="modal-payment-summary" class="font-bold text-slate-900 dark:text-white text-right">GCash (PayMongo)</span>
            </div>

            <div class="flex justify-between items-center text-sm font-bold text-slate-900 dark:text-white border-t border-slate-200 dark:border-slate-700 pt-3">
                <span>Total Payable:</span>
                <span class="text-terra-green dark:text-emerald-400 text-base sm:text-lg font-black font-mono" id="modal-order-total">₱0.00</span>
            </div>
        </div>

        <div id="modal-reassurance-box" class="p-3.5 rounded-2xl bg-[#007DFE]/10 border border-[#007DFE]/25 text-xs text-slate-700 dark:text-slate-200 flex items-start gap-2.5">
            <span id="modal-reassurance-icon" class="material-symbols-outlined text-[#007DFE] text-[18px] shrink-0 mt-0.5">account_balance_wallet</span>
            <p class="leading-relaxed" id="modal-description"></p>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-1">
            <button type="button" id="modal-cancel-btn" class="min-h-[42px] px-4 py-2.5 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Go Back &amp; Edit
            </button>
            <button type="button" id="modal-confirm-btn" class="min-h-[42px] px-5 py-2.5 bg-terra-green hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition-all shadow-md active:scale-95 flex items-center gap-1.5">
                <span id="modal-confirm-text">Confirm Order</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </button>
        </div>
    </div>
</div>

</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var itemCheckboxes    = document.querySelectorAll('.cart-item-checkbox');
    var storeCheckboxes   = document.querySelectorAll('.store-checkbox');
    var selectAll         = document.getElementById('select-all');
    var fulfillmentRadios = document.querySelectorAll('input[name="cart_fulfillment_choice"]');
    var paymentRadios     = document.querySelectorAll('input[name="payment_method"]');
    var placeOrderBtn     = document.getElementById('place-order-btn');
    var placeOrderBtnText = document.getElementById('place-order-btn-text');
    var mobileCheckoutBtn = document.getElementById('mobile-checkout-btn');
    var mobileCheckoutBtnText = document.getElementById('mobile-checkout-btn-text');
    var mobileScrollOptionsBtn = document.getElementById('mobile-scroll-options-btn');
    var deleteSelectedBtn = document.getElementById('delete-selected');

    var shipping          = <?= json_encode($shipping) ?>;
    var shopQuotes        = <?= json_encode($shopQuotes ?? (object) []) ?>;
    var quoteUrl          = <?= json_encode(base_url('customer/shipping/quote')) ?>;
    var csrfName          = <?= json_encode($csrfName) ?>;
    var csrfHash          = <?= json_encode($csrfHash) ?>;
    var updateUrl         = <?= json_encode(base_url('cart/update')) ?>;
    var removeSelectedUrl = <?= json_encode(base_url('cart/remove-selected')) ?>;

    var formFulfillmentMethod = document.getElementById('form-fulfillment-method');
    var radioFulfillDelivery  = document.getElementById('cart-fulfillment-delivery');
    var radioFulfillPickup    = document.getElementById('cart-fulfillment-pickup');
    var cardFulfillDelivery   = document.getElementById('card-cart-fulfillment-delivery');
    var cardFulfillPickup     = document.getElementById('card-cart-fulfillment-pickup');

    var payRadioGcash     = document.getElementById('cart-pay-gcash');
    var payRadioCash      = document.getElementById('cart-pay-cash');
    var labelPayGcash     = document.getElementById('label-pay-gcash');
    var labelPayCash      = document.getElementById('label-pay-cash');
    var iconPayCash       = document.getElementById('icon-pay-cash');
    var titlePayCash      = document.getElementById('title-pay-cash');
    var subtextPayCash    = document.getElementById('subtext-pay-cash');
    var subtextPayGcash   = document.getElementById('subtext-pay-gcash');

    var cartAddressCard     = document.getElementById('cart-address-card');
    var cartPickupBanner    = document.getElementById('cart-pickup-banner');
    var addressSelect       = document.getElementById('cart-shipping-address-select');
    var addressInput        = document.getElementById('cart-shipping-address-input');
    var cartDistanceSummary = document.getElementById('cart-distance-summary');

    function money(n) {
        return '\u20B1' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function computeSelectedDeliveryQuote(selectedShopIds) {
        var totalFee = 0;
        var outOfRangeError = null;
        var distances = [];

        if (!selectedShopIds || selectedShopIds.length === 0) {
            return { totalFee: shipping, deliverable: true, outOfRangeError: null, distanceSummary: 'Select items to view route distance' };
        }

        selectedShopIds.forEach(function (sId) {
            var q = shopQuotes && shopQuotes[sId] ? shopQuotes[sId] : null;
            if (q) {
                totalFee += parseFloat(q.shipping_fee) || 0;
                if (q.distance_text) distances.push(q.distance_text);
                if (!q.deliverable && !outOfRangeError) {
                    outOfRangeError = q.error || 'Selected address exceeds the maximum delivery distance.';
                }
            } else {
                totalFee += shipping;
            }
        });

        var distText = distances.length === 1
            ? ('Road distance: ' + distances[0])
            : (distances.length > 1 ? ('Routes: ' + distances.join(', ')) : 'Distance-based fee via Google Maps');

        return {
            totalFee: Math.round(totalFee * 100) / 100,
            deliverable: outOfRangeError === null,
            outOfRangeError: outOfRangeError,
            distanceSummary: distText
        };
    }

    function postForm(url, data) {
        var fd = new FormData();
        fd.append(csrfName, csrfHash);
        Object.keys(data).forEach(function (key) {
            if (Array.isArray(data[key])) {
                data[key].forEach(function (v) { fd.append(key, v); });
            } else {
                fd.append(key, data[key]);
            }
        });
        return fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        });
    }

    function setCardBorderState(cardEl, isActive, isDisabled) {
        if (!cardEl) return;
        cardEl.classList.remove(
            'border-terra-green', 'bg-terra-green/5', 'dark:bg-terra-green/10',
            'border-slate-200', 'dark:border-slate-700', 'bg-slate-50/70', 'dark:bg-slate-800/60',
            'opacity-40', 'cursor-not-allowed', 'pointer-events-none'
        );
        if (isDisabled) {
            cardEl.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-slate-50/70', 'dark:bg-slate-800/60', 'opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        } else if (isActive) {
            cardEl.classList.add('border-terra-green', 'bg-terra-green/5', 'dark:bg-terra-green/10');
        } else {
            cardEl.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-slate-50/70', 'dark:bg-slate-800/60');
        }
    }

    function updateUI() {
        var selected = Array.prototype.filter.call(itemCheckboxes, function (cb) { return cb.checked; });
        var anySelected = selected.length > 0;
        var totalQty = 0;
        var subtotal = 0;
        var selectedShopIds = [];

        selected.forEach(function (cb) {
            var p = parseFloat(cb.getAttribute('data-price')) || 0;
            var q = parseInt(cb.getAttribute('data-qty'), 10) || 1;
            var sId = cb.getAttribute('data-shop-id');
            subtotal += p * q;
            totalQty += q;
            if (sId && selectedShopIds.indexOf(sId) === -1) {
                selectedShopIds.push(sId);
            }
        });

        var quoteResult = computeSelectedDeliveryQuote(selectedShopIds);
        if (cartDistanceSummary) {
            cartDistanceSummary.textContent = quoteResult.distanceSummary;
        }

        // Sync each store checkbox
        storeCheckboxes.forEach(function (scb) {
            var sId = scb.getAttribute('data-shop-id');
            var storeItems = document.querySelectorAll('.cart-item-checkbox[data-shop-id="' + sId + '"]');
            if (storeItems.length > 0) {
                var allStoreChecked = Array.prototype.every.call(storeItems, function (cb) { return cb.checked; });
                var someStoreChecked = Array.prototype.some.call(storeItems, function (cb) { return cb.checked; });
                scb.checked = allStoreChecked;
                scb.indeterminate = someStoreChecked && !allStoreChecked;
            }
        });

        // Sync global select all
        if (selectAll) {
            selectAll.checked = itemCheckboxes.length > 0 && Array.prototype.every.call(itemCheckboxes, function (cb) { return cb.checked; });
            selectAll.indeterminate = anySelected && !selectAll.checked;
        }

        // Enable/disable delete selected
        if (deleteSelectedBtn) {
            deleteSelectedBtn.disabled = !anySelected;
            if (anySelected) {
                deleteSelectedBtn.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                deleteSelectedBtn.classList.add('opacity-50', 'pointer-events-none');
            }
        }

        // Check merchant fulfillment capabilities across selected items
        var canDeliver = true;
        var canPickup  = true;
        selected.forEach(function (cb) {
            if (cb.getAttribute('data-offers-delivery') === '0') canDeliver = false;
            if (cb.getAttribute('data-offers-pickup') === '0') canPickup = false;
        });

        var subtextDel  = document.getElementById('subtext-fulfillment-delivery');
        var subtextPick = document.getElementById('subtext-fulfillment-pickup');
        var badgeFeeDel = document.getElementById('badge-fee-delivery');
        var noticeEl    = document.getElementById('cart-service-notice');
        var noticeText  = document.getElementById('cart-service-notice-text');

        if (radioFulfillDelivery) {
            radioFulfillDelivery.disabled = (anySelected && !canDeliver);
            if (radioFulfillDelivery.disabled && radioFulfillDelivery.checked && canPickup && radioFulfillPickup) {
                radioFulfillPickup.checked = true;
            }
            if (subtextDel) {
                subtextDel.textContent = (anySelected && !canDeliver) ? 'Unavailable for selected shop' : 'Delivered to your address';
            }
            if (badgeFeeDel) {
                badgeFeeDel.textContent = (anySelected && !canDeliver) ? 'N/A' : money(quoteResult.totalFee);
            }
        }

        if (radioFulfillPickup) {
            radioFulfillPickup.disabled = (anySelected && !canPickup);
            if (radioFulfillPickup.disabled && radioFulfillPickup.checked && canDeliver && radioFulfillDelivery) {
                radioFulfillDelivery.checked = true;
            }
            if (subtextPick) {
                subtextPick.textContent = (anySelected && !canPickup) ? 'Unavailable for selected shop' : 'Pick up at shop branch';
            }
        }

        if (noticeEl && noticeText) {
            if (anySelected && (!canDeliver || !canPickup)) {
                noticeEl.classList.remove('hidden');
                if (!canDeliver && !canPickup) {
                    noticeText.textContent = 'Selected shops have conflicting fulfillment methods (one is Pick-up only, another is Delivery only). Please check out those shops separately.';
                } else if (!canDeliver) {
                    noticeText.textContent = 'Doorstep delivery is not offered by one or more selected shops. Switched to Store Pick-up (Free).';
                } else if (!canPickup) {
                    noticeText.textContent = 'Store pick-up is not offered by one or more selected shops. Switched to Doorstep Delivery.';
                }
            } else {
                noticeEl.classList.add('hidden');
            }
        }

        // Determine active Step 1 Fulfillment Method
        var isPickup = (radioFulfillPickup && radioFulfillPickup.checked && !radioFulfillPickup.disabled);
        var activeFulfillment = isPickup ? 'pickup' : 'delivery';
        if (formFulfillmentMethod) {
            formFulfillmentMethod.value = activeFulfillment;
        }

        setCardBorderState(cardFulfillDelivery, !isPickup, anySelected && !canDeliver);
        setCardBorderState(cardFulfillPickup, isPickup, anySelected && !canPickup);

        // Toggle Delivery Address Card vs Store Pick-up Banner
        if (isPickup) {
            if (cartAddressCard) cartAddressCard.classList.add('hidden');
            if (cartPickupBanner) cartPickupBanner.classList.remove('hidden');
            if (addressSelect) addressSelect.removeAttribute('required');
            if (addressInput) addressInput.removeAttribute('required');
        } else {
            if (cartAddressCard) cartAddressCard.classList.remove('hidden');
            if (cartPickupBanner) cartPickupBanner.classList.add('hidden');
            if (addressSelect) addressSelect.setAttribute('required', 'required');
            if (addressInput) addressInput.setAttribute('required', 'required');
        }

        // Adapt Step 2 Cash Payment option dynamically to Step 1 Fulfillment
        if (payRadioCash) {
            payRadioCash.value = isPickup ? 'pickup' : 'cod';
        }
        if (iconPayCash) {
            iconPayCash.textContent = isPickup ? 'storefront' : 'payments';
        }
        if (titlePayCash) {
            titlePayCash.textContent = isPickup ? 'Pay at Store Counter' : 'Cash on Delivery (COD)';
        }
        if (subtextPayCash) {
            subtextPayCash.textContent = isPickup ? 'Pay cash at branch upon pick-up (₱0 fee)' : 'Pay cash upon doorstep arrival';
        }
        if (subtextPayGcash) {
            subtextPayGcash.textContent = isPickup
                ? 'Pay online via GCash for Store Pick-up (Free shipping)'
                : 'Pay online via GCash for Doorstep Delivery';
        }

        var isGcash = payRadioGcash && payRadioGcash.checked;
        setCardBorderState(labelPayGcash, isGcash, false);
        setCardBorderState(labelPayCash, !isGcash, false);

        // Calculate Accurate Distance-Based Shipping & Grand Total
        var currentShipping = isPickup ? 0 : (anySelected ? quoteResult.totalFee : 0);
        var grandTotal      = subtotal + currentShipping;

        var subtotalEl        = document.getElementById('subtotal-val');
        var shippingLabelEl   = document.getElementById('shipping-fee-label');
        var shippingEl        = document.getElementById('shipping-val');
        var totalEl           = document.getElementById('total-val');
        var badgeEl           = document.getElementById('selected-count-badge');
        var modeCaptionEl     = document.getElementById('summary-mode-caption');
        var mobileTotalEl     = document.getElementById('mobile-total-val');
        var mobileCountEl     = document.getElementById('mobile-item-count');
        var mobilePillEl      = document.getElementById('mobile-selection-pill');
        var inlineError       = document.getElementById('checkout-inline-error');
        var inlineErrorText   = document.getElementById('checkout-error-text');

        var outOfRangeBlocked = anySelected && !isPickup && !quoteResult.deliverable;
        if (inlineError && inlineErrorText) {
            if (outOfRangeBlocked) {
                inlineErrorText.textContent = quoteResult.outOfRangeError;
                inlineError.classList.remove('hidden');
            } else {
                inlineError.classList.add('hidden');
            }
        }

        var modeSummaryText = (isPickup ? 'Store Pick-up' : 'Delivery') + ' · ' + (isGcash ? 'GCash' : (isPickup ? 'Pay at Store' : 'COD'));

        if (subtotalEl) subtotalEl.innerText = money(subtotal);
        if (shippingLabelEl) shippingLabelEl.innerText = isPickup ? 'Store Pick-up Fee' : 'Distance Delivery Fee';
        if (shippingEl) {
            if (isPickup) {
                shippingEl.innerText = 'FREE (₱0.00)';
            } else if (!anySelected) {
                shippingEl.innerText = '₱0.00';
            } else if (selectedShopIds.length > 1) {
                shippingEl.innerText = money(currentShipping) + ' (' + selectedShopIds.length + ' shops)';
            } else {
                shippingEl.innerText = money(currentShipping);
            }
        }
        if (totalEl) totalEl.innerText = money(grandTotal);
        if (badgeEl) badgeEl.innerText = totalQty + (totalQty === 1 ? ' item selected' : ' items selected');
        if (modeCaptionEl) modeCaptionEl.innerText = modeSummaryText + ' · VAT Inclusive';

        if (mobileTotalEl) mobileTotalEl.innerText = money(grandTotal);
        if (mobileCountEl) mobileCountEl.innerText = totalQty + (totalQty === 1 ? ' item selected' : ' items selected');
        if (mobilePillEl) mobilePillEl.innerText = modeSummaryText;

        var conflictFulfillment = (anySelected && !canDeliver && !canPickup) || outOfRangeBlocked;

        if (placeOrderBtn) {
            placeOrderBtn.disabled = !anySelected || conflictFulfillment;
            if (placeOrderBtnText) {
                if (!anySelected) {
                    placeOrderBtnText.innerText = 'Select Items to Checkout';
                } else if (outOfRangeBlocked) {
                    placeOrderBtnText.innerText = 'Address Outside Delivery Limit';
                } else if (isGcash) {
                    placeOrderBtnText.innerText = 'Review & Pay via GCash (' + totalQty + ')';
                } else {
                    placeOrderBtnText.innerText = 'Review & Place Order (' + totalQty + ')';
                }
            }
        }
        if (mobileCheckoutBtn) {
            mobileCheckoutBtn.disabled = !anySelected || conflictFulfillment;
            if (mobileCheckoutBtnText) {
                mobileCheckoutBtnText.innerText = outOfRangeBlocked ? 'Out of Range' : (isGcash ? 'Review & GCash' : 'Review Order');
            }
        }
    }

    function refreshAllShopQuotesForAddress() {
        if (!addressSelect || !addressSelect.value) return;
        var allShopIds = [];
        itemCheckboxes.forEach(function (cb) {
            var sId = cb.getAttribute('data-shop-id');
            if (sId && allShopIds.indexOf(sId) === -1) allShopIds.push(sId);
        });
        if (!allShopIds.length) return;

        if (cartDistanceSummary) {
            cartDistanceSummary.textContent = 'Calculating road route...';
        }

        var fd = new FormData();
        fd.append(csrfName, csrfHash);
        fd.append('address_id', addressSelect.value);
        fd.append('shop_ids', allShopIds.join(','));

        fetch(quoteUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res && res.csrf_hash) {
                csrfHash = res.csrf_hash;
                var csrfInput = document.querySelector('#cart-checkout-form input[name="' + csrfName + '"]');
                if (csrfInput) csrfInput.value = csrfHash;
            }
            if (res && res.success && res.quotes) {
                shopQuotes = res.quotes;
                updateUI();
            }
        })
        .catch(function () {});
    }

    if (addressSelect) {
        addressSelect.addEventListener('change', refreshAllShopQuotesForAddress);
    }

    // Event Listeners for Checkboxes & Radios
    if (selectAll) {
        selectAll.addEventListener('change', function (e) {
            itemCheckboxes.forEach(function (cb) { cb.checked = e.target.checked; });
            updateUI();
        });
    }

    storeCheckboxes.forEach(function (scb) {
        scb.addEventListener('change', function (e) {
            var sId = this.getAttribute('data-shop-id');
            var storeItems = document.querySelectorAll('.cart-item-checkbox[data-shop-id="' + sId + '"]');
            storeItems.forEach(function (cb) { cb.checked = e.target.checked; });
            updateUI();
        });
    });

    itemCheckboxes.forEach(function (cb) {
        cb.addEventListener('change', updateUI);
    });

    fulfillmentRadios.forEach(function (radio) {
        radio.addEventListener('change', updateUI);
    });

    paymentRadios.forEach(function (radio) {
        radio.addEventListener('change', updateUI);
    });

    if (mobileScrollOptionsBtn) {
        mobileScrollOptionsBtn.addEventListener('click', function () {
            var sidebar = document.getElementById('checkout-sidebar');
            if (sidebar) {
                sidebar.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Form Submission & Unified Review Modal
    var checkoutForm          = document.getElementById('cart-checkout-form');
    var confirmModal          = document.getElementById('checkout-confirm-modal');
    var modalTitle            = document.getElementById('modal-title');
    var modalDesc             = document.getElementById('modal-description');
    var modalIcon             = document.getElementById('modal-icon');
    var modalShopName         = document.getElementById('modal-shop-name');
    var modalFulfillmentSum   = document.getElementById('modal-fulfillment-summary');
    var modalLocationLabel    = document.getElementById('modal-location-label');
    var modalLocationValue    = document.getElementById('modal-location-value');
    var modalPaymentSum       = document.getElementById('modal-payment-summary');
    var modalTotal            = document.getElementById('modal-order-total');
    var modalReassuranceBox   = document.getElementById('modal-reassurance-box');
    var modalReassuranceIcon  = document.getElementById('modal-reassurance-icon');
    var modalCancelBtn        = document.getElementById('modal-cancel-btn');
    var modalCloseXBtn        = document.getElementById('modal-close-x-btn');
    var modalConfirmBtn       = document.getElementById('modal-confirm-btn');
    var modalConfirmText      = document.getElementById('modal-confirm-text');
    var isConfirmedSubmit     = false;

    function getSelectedShops() {
        var selectedShops = [];
        itemCheckboxes.forEach(function (cb) {
            if (cb.checked) {
                var sName = cb.getAttribute('data-shop-name');
                if (sName && selectedShops.indexOf(sName) === -1) {
                    selectedShops.push(sName);
                }
            }
        });
        return selectedShops;
    }

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            if (isConfirmedSubmit) return;

            var selected = Array.prototype.filter.call(itemCheckboxes, function (cb) { return cb.checked; });
            var inlineError = document.getElementById('checkout-inline-error');
            var inlineErrorText = document.getElementById('checkout-error-text');

            if (selected.length === 0) {
                e.preventDefault();
                if (inlineError && inlineErrorText) {
                    inlineErrorText.textContent = 'Please select at least one item to proceed with checkout.';
                    inlineError.classList.remove('hidden');
                }
                return;
            }

            if (inlineError) inlineError.classList.add('hidden');
            e.preventDefault();

            var isPickup = (radioFulfillPickup && radioFulfillPickup.checked && !radioFulfillPickup.disabled);
            var isGcash  = (payRadioGcash && payRadioGcash.checked);

            var subtotal = 0;
            var selectedShopIds = [];
            selected.forEach(function (cb) {
                var p = parseFloat(cb.getAttribute('data-price')) || 0;
                var q = parseInt(cb.getAttribute('data-qty'), 10) || 1;
                var sId = cb.getAttribute('data-shop-id');
                subtotal += p * q;
                if (sId && selectedShopIds.indexOf(sId) === -1) {
                    selectedShopIds.push(sId);
                }
            });

            var quoteResult     = computeSelectedDeliveryQuote(selectedShopIds);
            var currentShipping = isPickup ? 0 : quoteResult.totalFee;
            var grandTotal      = subtotal + currentShipping;
            var shops           = getSelectedShops();

            if (formFulfillmentMethod) {
                formFulfillmentMethod.value = isPickup ? 'pickup' : 'delivery';
            }

            if (modalShopName) {
                modalShopName.textContent = shops.join(', ') || 'Selected Store(s)';
            }

            if (modalFulfillmentSum) {
                modalFulfillmentSum.textContent = isPickup
                    ? 'Store Pick-up (FREE)'
                    : 'Doorstep Delivery (' + money(currentShipping) + ')';
            }

            if (modalLocationLabel && modalLocationValue) {
                if (isPickup) {
                    modalLocationLabel.textContent = 'Pick-up At:';
                    modalLocationValue.textContent = (shops.join(', ') || 'Store Branch') + ' (Present Digital QR Pass)';
                } else {
                    modalLocationLabel.textContent = 'Delivery Address:';
                    if (addressSelect && addressSelect.options.length > 0) {
                        modalLocationValue.textContent = addressSelect.options[addressSelect.selectedIndex].text.trim();
                    } else if (addressInput && addressInput.value) {
                        modalLocationValue.textContent = addressInput.value.trim();
                    } else {
                        modalLocationValue.textContent = 'Polomolok, South Cotabato';
                    }
                }
            }

            if (modalPaymentSum) {
                modalPaymentSum.textContent = isGcash
                    ? 'GCash (Online via PayMongo)'
                    : (isPickup ? 'Pay at Store Counter (Cash)' : 'Cash on Delivery (COD)');
            }

            if (modalTotal) {
                modalTotal.textContent = money(grandTotal);
            }

            if (isGcash) {
                if (modalTitle) modalTitle.textContent = 'Review GCash Order';
                if (modalIcon) modalIcon.textContent = 'account_balance_wallet';
                if (modalReassuranceBox) {
                    modalReassuranceBox.className = 'p-3.5 rounded-2xl bg-[#007DFE]/10 border border-[#007DFE]/25 text-xs text-slate-700 dark:text-slate-200 flex items-start gap-2.5';
                }
                if (modalReassuranceIcon) {
                    modalReassuranceIcon.className = 'material-symbols-outlined text-[#007DFE] text-[18px] shrink-0 mt-0.5';
                    modalReassuranceIcon.textContent = 'verified_user';
                }
                if (modalDesc) {
                    modalDesc.innerHTML = 'Next, you will be redirected to <strong>PayMongo\'s secure GCash checkout</strong> for your <strong>' + (isPickup ? 'Store Pick-up' : 'Doorstep Delivery') + '</strong> order. You won\'t be charged until you authorize payment.';
                }
                if (modalConfirmText) modalConfirmText.textContent = 'Proceed to GCash (' + money(grandTotal) + ')';
            } else {
                if (modalTitle) modalTitle.textContent = isPickup ? 'Confirm Store Pick-up Order' : 'Confirm Cash on Delivery Order';
                if (modalIcon) modalIcon.textContent = isPickup ? 'storefront' : 'local_shipping';
                if (modalReassuranceBox) {
                    modalReassuranceBox.className = 'p-3.5 rounded-2xl bg-terra-green/10 border border-terra-green/25 text-xs text-slate-700 dark:text-slate-200 flex items-start gap-2.5';
                }
                if (modalReassuranceIcon) {
                    modalReassuranceIcon.className = 'material-symbols-outlined text-terra-green text-[18px] shrink-0 mt-0.5';
                    modalReassuranceIcon.textContent = isPickup ? 'storefront' : 'local_shipping';
                }
                if (modalDesc) {
                    modalDesc.innerHTML = isPickup
                        ? 'Your order will be prepared for pick-up at <strong>' + (shops.join(', ') || 'the store branch') + '</strong>. Please pay <strong>' + money(grandTotal) + '</strong> at the counter upon pick-up.'
                        : 'Your order will be prepared for doorstep delivery. Please prepare <strong>' + money(grandTotal) + '</strong> in cash upon arrival.';
                }
                if (modalConfirmText) modalConfirmText.textContent = 'Confirm & Place Order';
            }

            if (confirmModal) confirmModal.classList.remove('hidden');
        });
    }

    function closeConfirmModal() {
        if (confirmModal) confirmModal.classList.add('hidden');
    }

    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeConfirmModal);
    if (modalCloseXBtn) modalCloseXBtn.addEventListener('click', closeConfirmModal);

    if (confirmModal) {
        confirmModal.addEventListener('click', function (e) {
            if (e.target === confirmModal) closeConfirmModal();
        });
    }

    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', function () {
            closeConfirmModal();
            isConfirmedSubmit = true;
            if (checkoutForm) checkoutForm.submit();
        });
    }

    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function () {
            var ids = Array.prototype.filter.call(itemCheckboxes, function (cb) { return cb.checked; })
                .map(function (cb) { return cb.getAttribute('data-id'); });
            if (!ids.length) return;
            if (!confirm('Are you sure you want to remove ' + ids.length + ' item(s) from your cart?')) return;
            postForm(removeSelectedUrl, { ids: ids }).then(function () {
                window.location.reload();
            });
        });
    }

    // Quantity Plus/Minus buttons
    document.querySelectorAll('.qty-minus, .qty-plus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id    = this.getAttribute('data-id');
            var stock = parseInt(this.getAttribute('data-stock'), 10) || 99;
            var valueEl = this.parentElement.querySelector('.qty-value');
            var qty  = parseInt(valueEl.textContent, 10) || 1;
            qty += this.classList.contains('qty-plus') ? 1 : -1;
            qty = Math.max(1, Math.min(stock, qty));
            postForm(updateUrl, { item_id: id, quantity: qty }).then(function () {
                window.location.reload();
            });
        });
    });

    // Initial state update
    updateUI();
})();
</script>
<?= $this->endSection() ?>