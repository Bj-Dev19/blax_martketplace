<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<style>
    /* Pulsing radar effects for live tracking */
    @keyframes pulseRing {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.35); opacity: 0.2; }
        100% { transform: scale(1.6); opacity: 0; }
    }
    .pulse-ring {
        animation: pulseRing 2s cubic-bezier(0.24, 0, 0.38, 1) infinite;
    }
    @keyframes activeDotPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.25); opacity: 0.7; }
    }
    .active-dot-pulse {
        animation: activeDotPulse 1.8s ease-in-out infinite;
    }
    /* Map Container Style */
    #trackMap {
        width: 100%;
        height: 100%;
        min-height: 480px;
        background-color: #f8fafc;
    }
    .gm-style-iw {
        font-family: inherit !important;
        border-radius: 12px !important;
        padding: 4px 8px !important;
    }
</style>

<div class="bg-slate-50/70 dark:bg-slate-950 min-h-screen py-5 md:py-8 pb-28 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <!-- Main 2-Column Responsive Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- ========================================================= -->
            <!-- LEFT COLUMN (5 Cols): Order & Progress Details            -->
            <!-- ========================================================= -->
            <div class="lg:col-span-5 flex flex-col gap-5">
                
                <!-- 1. Top Navigation & Status Badges -->
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <a href="<?= !empty($backUrl) ? esc($backUrl) : base_url('customer/orders') ?>" 
                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors group">
                        <span class="material-symbols-outlined text-[18px] transition-transform group-hover:-translate-x-1">arrow_back</span>
                        <span><?= !empty($backLabel) ? esc($backLabel) : 'Back to My Orders' ?></span>
                    </a>

                    <!-- Status & Fulfillment Badges -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <?php 
                            $fulfillmentMethod = strtolower(trim((string) ($order['fulfillment_method'] ?? 'delivery')));
                            $isPickup = ($fulfillmentMethod === 'pickup' || $fulfillmentMethod === 'store pick-up');
                            $fulfillmentLabel = $isPickup ? 'Store Pick-up' : 'Doorstep Delivery';
                        ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border <?= $isPickup ? 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60' : 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60' ?>">
                            <span class="material-symbols-outlined text-[14px]"><?= $isPickup ? 'storefront' : 'local_shipping' ?></span>
                            <span><?= esc($fulfillmentLabel) ?></span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide <?= esc($badgeClass) ?>">
                            <span class="w-2 h-2 rounded-full <?= ($statusBadge === 'Delivered') ? 'bg-emerald-500' : (($statusBadge === 'Cancelled') ? 'bg-rose-500' : 'bg-blue-600 active-dot-pulse') ?>"></span>
                            <?= esc($statusBadge) ?>
                        </span>
                    </div>
                </div>

                <!-- 2. Unified Order Reference & Live Arrival Estimate Card -->
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800/90 rounded-2xl p-5 shadow-xs relative overflow-hidden">
                    <!-- Reference & Total Row -->
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80 gap-3">
                        <div>
                            <span class="text-[11px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">Order Reference</span>
                            <h1 class="text-lg md:text-xl font-bold text-slate-900 dark:text-white tracking-tight mt-0.5 font-mono">
                                #<?= esc($order['order_number'] ?? ('ORD-' . $order['id'])) ?>
                            </h1>
                            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1.5">
                                <span class="material-symbols-outlined text-[15px] text-slate-400">storefront</span>
                                <span>Sold by <strong class="text-slate-800 dark:text-slate-200"><?= esc($shop['shop_name'] ?? 'Store Partner') ?></strong></span>
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-[11px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">Total</span>
                            <div class="text-base md:text-lg font-bold text-slate-900 dark:text-white font-mono mt-0.5">
                                ₱<?= number_format((float) ($order['total_amount'] ?? 0), 2) ?>
                            </div>
                            <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-md <?= ($order['payment_status'] ?? '') === 'paid' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50' ?>">
                                <?= strtoupper($order['payment_status'] ?? 'UNPAID') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Arrival Estimate & Fulfillment Progress -->
                    <div class="pt-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    <span>Arrival Estimate</span>
                                </div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mt-1">
                                    <?= esc($estimatedArrival) ?>
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    <?= strtolower($order['status'] ?? '') === 'delivered' ? 'Package successfully delivered' : ($isPickup ? 'Ready for Store Pick-up' : 'Standard Doorstep Delivery via Local Fleet') ?>
                                </p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="flex justify-between items-center text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">
                                <span>Fulfillment Progress</span>
                                <span class="font-mono font-bold text-primary"><?= (int) $progressPercent ?>%</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-2 rounded-full transition-all duration-700 ease-out" 
                                     style="width: <?= (int) $progressPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Milestone Stepper -->
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800/90 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px] text-primary">timeline</span>
                        <span>Delivery Timeline</span>
                    </h2>

                    <div class="relative pl-6 space-y-5 before:absolute before:left-[11px] before:top-2.5 before:bottom-2.5 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                        <?php foreach ($milestones ?? [] as $idx => $m): ?>
                            <?php 
                                $isDone = ($m['state'] === 'completed');
                                $isActive = ($m['state'] === 'active');
                            ?>
                            <div class="relative flex items-start group">
                                <!-- Stepper Node Dot / Icon -->
                                <div class="absolute -left-6 mt-0.5 flex items-center justify-center">
                                    <?php if ($isDone): ?>
                                        <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center ring-4 ring-emerald-50 dark:ring-emerald-950/40 shadow-xs">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                        </div>
                                    <?php elseif ($isActive): ?>
                                        <div class="relative w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-xs">
                                            <span class="absolute w-full h-full rounded-full bg-blue-500 pulse-ring"></span>
                                            <span class="w-2 h-2 rounded-full bg-white"></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-700 text-slate-400 flex items-center justify-center">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Milestone Content -->
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <h3 class="text-xs sm:text-sm font-bold <?= $isDone ? 'text-slate-900 dark:text-white' : ($isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500') ?>">
                                            <?= esc($m['title']) ?>
                                        </h3>
                                        <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                            <?= esc($m['timestamp']) ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <?= esc($m['description']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 4. Destination Address & Ordered Items Summary (Single Clean Card) -->
                <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800/90 rounded-2xl p-5 shadow-xs space-y-4">
                    <div class="pb-3.5 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="material-symbols-outlined text-[17px] text-primary">location_on</span>
                            <span class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">Destination Address</span>
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-slate-200 leading-relaxed">
                            <?= esc($destAddressText) ?>
                        </p>
                        <?php if (!empty($destApproximate)): ?>
                            <p class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase tracking-wide">
                                <span class="material-symbols-outlined text-[13px]">location_off</span>
                                Approximate location &mdash; pending exact geocode
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($shippingAddress['recipient_name'])): ?>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Recipient: <strong class="text-slate-900 dark:text-slate-200"><?= esc($shippingAddress['recipient_name']) ?></strong> 
                                <?= !empty($shippingAddress['phone']) ? '• ' . esc($shippingAddress['phone']) : '' ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Ordered Items (Shown once cleanly) -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-primary">inventory_2</span>
                                <span>Ordered <?= count($items) > 1 ? 'Items (' . count($items) . ')' : 'Product' ?></span>
                            </span>
                        </div>

                        <?php foreach ($items as $item): ?>
                            <?php 
                                $itemImg = product_image_url($item['image_url'] ?? '', 'thumbnail');
                                $pName = !empty($item['product_name']) ? $item['product_name'] : ('Product #' . ($item['product_id'] ?? ''));
                            ?>
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <div class="relative w-14 h-14 rounded-xl overflow-hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0">
                                    <?php if (!empty($itemImg)): ?>
                                        <img src="<?= esc($itemImg) ?>" 
                                             alt="<?= esc($pName) ?>" 
                                             class="w-full h-full object-cover object-center"
                                             loading="lazy" decoding="async"
                                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
                                        <div class="hidden w-full h-full flex items-center justify-center text-slate-400">
                                            <span class="material-symbols-outlined text-xl">image_not_supported</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100 dark:bg-slate-800">
                                            <span class="material-symbols-outlined text-xl">inventory_2</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white line-clamp-1">
                                        <?= esc($pName) ?>
                                    </h3>
                                    <?php if (!empty($item['variant_label'])): ?>
                                        <span class="inline-block text-[10px] font-semibold text-primary bg-primary/10 px-2 py-0.5 rounded-md mt-0.5">
                                            <?= esc($item['variant_label']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        <span>Qty: <strong class="text-slate-900 dark:text-white"><?= (int) ($item['quantity'] ?? 1) ?></strong></span>
                                        <span class="font-mono font-bold text-slate-900 dark:text-white">₱<?= number_format((float) ($item['unit_price'] ?? 0), 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Compact Cost Summary -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl space-y-1.5 text-xs text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-mono">₱<?= number_format((float) ($order['subtotal'] ?? 0), 2) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Delivery Fee<?= !empty($order['distance_km']) ? ' <span class="text-[10px] font-mono">(' . number_format((float) $order['distance_km'], 1) . ' km route)</span>' : '' ?></span>
                                <span class="font-mono">₱<?= number_format((float) ($order['shipping_fee'] ?? 0), 2) ?></span>
                            </div>
                            <div class="flex justify-between font-bold text-slate-900 dark:text-white pt-1.5 border-t border-slate-200 dark:border-slate-700 text-xs sm:text-sm">
                                <span>Total</span>
                                <span class="font-mono text-primary">₱<?= number_format((float) ($order['total_amount'] ?? 0), 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================= -->
            <!-- RIGHT COLUMN (7 Cols): Live Tracking Map                   -->
            <!-- ========================================================= -->
            <div class="lg:col-span-7">
                <div class="sticky top-20">
                    
                    <!-- Map Card Container with Docked Header & Footer Bars (Zero Floating Map Clutter) -->
                    <div class="bento-card w-full rounded-2xl overflow-hidden shadow-xs border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col">
                        
                        <!-- DOCKED MAP HEADER BAR -->
                        <div class="px-4 py-3 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2.5">
                            
                            <!-- Left: Live Status + Real-Time Route ETA & Distance -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 tracking-wide uppercase">Live Route</span>
                                </div>

                                <!-- Real-Time ETA & Road Distance Pill -->
                                <div id="liveRouteStats" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700 text-xs font-semibold">
                                    <span class="material-symbols-outlined text-[15px] text-blue-600 dark:text-blue-400">directions_bike</span>
                                    <span id="liveEtaText">Calculating route...</span>
                                </div>
                            </div>

                            <!-- Right: Clean Map Action Controls -->
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <!-- Satellite / Roadmap View Toggle Button -->
                                <button type="button" 
                                        id="btn-customer-map-type"
                                        onclick="toggleCustomerMapType()"
                                        title="Toggle Satellite / Roadmap View"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-semibold transition-all active:scale-95 cursor-pointer">
                                    <span id="custMapTypeIcon" class="material-symbols-outlined text-[15px] text-primary">satellite_alt</span>
                                    <span id="custMapTypeText">Satellite</span>
                                </button>

                                <!-- Auto-Follow Camera Toggle -->
                                <button type="button" 
                                        id="btn-auto-follow"
                                        onclick="toggleAutoFollow()"
                                        title="Toggle Camera Auto-Follow"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-all active:scale-95 cursor-pointer">
                                    <span id="autoFollowDot" class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span id="autoFollowText">Auto-Follow: ON</span>
                                </button>

                                <!-- "Center on Courier" Control Button -->
                                <button type="button" 
                                        id="btn-center-courier"
                                        onclick="centerOnCourier()"
                                        title="Center Map on Courier"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary hover:bg-primary-dark text-white text-xs font-semibold shadow-xs transition-all active:scale-95 cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px]">my_location</span>
                                    <span class="hidden sm:inline">Center on Courier</span>
                                    <span class="sm:hidden">Center</span>
                                </button>
                            </div>
                        </div>

                        <!-- THE UNOBSTRUCTED GOOGLE MAPS CANVAS -->
                        <div id="trackMap" class="w-full flex-grow z-[1] min-h-[400px] sm:min-h-[480px] lg:h-[520px]"></div>

                        <!-- DOCKED MAP FOOTER BAR (Courier, Destination & Telemetry) -->
                        <div class="px-4 py-3 bg-slate-900 text-white border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1 min-w-0">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-400/30 flex items-center justify-center shrink-0 text-blue-400">
                                        <span class="material-symbols-outlined text-[16px]">two_wheeler</span>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-blue-300">Courier / Rider</p>
                                        <p class="text-xs font-semibold text-white truncate"><?= esc($courierName) ?> (Live En Route)</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center shrink-0 text-emerald-400">
                                        <span class="material-symbols-outlined text-[16px]">home_pin</span>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">Delivery Destination</p>
                                        <p class="text-xs font-semibold text-white truncate"><?= esc($destAddressText) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Compact GPS Telemetry Pill -->
                            <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700 text-xs font-mono shrink-0 self-start sm:self-center">
                                <div class="flex items-center gap-1 text-blue-300">
                                    <span class="material-symbols-outlined text-[14px]">speed</span>
                                    <span id="custHudSpeed" class="font-bold">Active</span>
                                </div>
                                <span class="text-slate-600">&bull;</span>
                                <div class="flex items-center gap-1 text-purple-300">
                                    <span id="custHudCompassIcon" class="material-symbols-outlined text-[14px] inline-block transition-transform duration-300">explore</span>
                                    <span id="custHudHeading" class="font-bold">Tracking</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    // Map Coordinates Data passed from Controller
    const STORE_COORDS   = <?= json_encode($storeCoords ?? null) ?>;
    const DEST_COORDS    = <?= json_encode($destCoords) ?>;
    const COURIER_COORDS = <?= json_encode($courierCoords) ?>;
    const DEST_ADDRESS   = <?= json_encode($destAddressText) ?>;
    const COURIER_NAME   = <?= json_encode($courierName) ?>;
    const ORDER_STATUS   = <?= json_encode($order['status'] ?? 'pending') ?>;
    const ORDER_ID       = <?= (int) ($order['id'] ?? 0) ?>;

    // Realtime: when the marketplace poll bus detects this order's status transitioned
    // server-side (e.g. shipped -> in_transit -> delivered), refresh the tracking view once.
    let __trackAutoReloaded = false;
    window.addEventListener('blax:customer-realtime', function(e) {
        const res = e && e.detail;
        if (!res || __trackAutoReloaded) return;
        const match = (arr) => Array.isArray(arr)
            ? arr.find(function(x) { return String(x.id) === String(ORDER_ID); })
            : null;
        const o = match(res.active_orders) || match(res.active_printing);
        if (o && o.status && o.status !== ORDER_STATUS) {
            __trackAutoReloaded = true;
            setTimeout(function() { location.reload(); }, 1200);
        }
    });

    let trackMap          = null;
    let courierMarker     = null;
    let destMarker        = null;
    let courierInfoWindow = null;
    let routePolyline     = null;
    let isAutoFollow      = true;
    let lastRouteOrigin   = null;

    // Handle Google Maps API authentication / activation failure
    window.gm_authFailure = function() {
        const container = document.getElementById('trackMap');
        if (container) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full p-6 text-center bg-amber-50/80 dark:bg-amber-950/30 border-2 border-dashed border-amber-300 rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-amber-600 mb-2">warning</span>
                    <h3 class="font-bold text-amber-900 dark:text-amber-200 text-base">Google Maps API Activation Required</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300/80 max-w-md mt-1">
                        The Google Cloud project (<strong>300493013944</strong>) has not yet activated the <strong>Maps JavaScript API</strong> or the API Key needs permissions.
                    </p>
                    <a href="https://console.cloud.google.com/apis/library/maps-backend.googleapis.com" target="_blank" class="mt-3 inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 text-white rounded-lg text-xs font-semibold hover:bg-amber-700 transition-colors">
                        <span>Enable Maps JavaScript API</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>
            `;
        }
    };

    function createMarkerContent(type) {
        const div = document.createElement('div');
        if (type === 'dest') {
            div.innerHTML = `
                <div style="width:36px;height:36px;border-radius:50%;background:#10b981;border:2.5px solid #ffffff;box-shadow:0 3px 8px rgba(16,185,129,0.35);display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;">
                    <span class="material-symbols-outlined" style="font-size:18px;line-height:1;">home</span>
                </div>
            `;
        } else if (type === 'courier') {
            div.innerHTML = `
                <div style="position:relative;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <div style="position:absolute;width:100%;height:100%;border-radius:50%;background:rgba(37,99,235,0.25);animation:pulseRing 1.8s infinite;"></div>
                    <div id="courierIconRotate" style="width:36px;height:36px;border-radius:50%;background:#2563eb;border:2.5px solid #ffffff;box-shadow:0 3px 10px rgba(37,99,235,0.5);display:flex;align-items:center;justify-content:center;color:#fff;z-index:2;transition:transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);">
                        <span class="material-symbols-outlined" style="font-size:20px;line-height:1;">two_wheeler</span>
                    </div>
                </div>
            `;
        }
        return div;
    }

    function calculateBearing(lat1, lng1, lat2, lng2) {
        const toRad = Math.PI / 180;
        const toDeg = 180 / Math.PI;
        const phi1 = lat1 * toRad;
        const phi2 = lat2 * toRad;
        const deltaLambda = (lng2 - lng1) * toRad;
        const y = Math.sin(deltaLambda) * Math.cos(phi2);
        const x = Math.cos(phi1) * Math.sin(phi2) - Math.cos(phi1) * Math.cos(deltaLambda);
        const theta = Math.atan2(y, x);
        return (theta * toDeg + 360) % 360;
    }

    function distanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const p1 = lat1 * Math.PI / 180;
        const p2 = lat2 * Math.PI / 180;
        const dp = (lat2 - lat1) * Math.PI / 180;
        const dl = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dp / 2) * Math.sin(dp / 2) +
                  Math.cos(p1) * Math.cos(p2) *
                  Math.sin(dl / 2) * Math.sin(dl / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function updateCustTelemetryHUD(speedKmh, bearingDeg) {
        const speedEl = document.getElementById('custHudSpeed');
        const headingEl = document.getElementById('custHudHeading');
        const compassIcon = document.getElementById('custHudCompassIcon');

        if (speedEl) {
            speedEl.textContent = `${Math.round(speedKmh)} km/h`;
        }
        if (headingEl && typeof bearingDeg === 'number' && !isNaN(bearingDeg)) {
            const cardinalDirections = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
            const cardinal = cardinalDirections[Math.round((bearingDeg % 360) / 45)] || 'N';
            headingEl.textContent = `${Math.round(bearingDeg)}° ${cardinal}`;
            if (compassIcon) {
                compassIcon.style.transform = `rotate(${Math.round(bearingDeg)}deg)`;
            }
        }
    }

    function updateFallbackRouteEta(origin, destination) {
        const statsWrap = document.getElementById('liveRouteStats');
        const etaText = document.getElementById('liveEtaText');
        if (!etaText) return;

        const straightMeters = distanceMeters(origin.lat, origin.lng, destination.lat, destination.lng);
        const roadKm = Math.max(0.2, (straightMeters * 1.25) / 1000);
        const estMins = Math.max(2, Math.round((roadKm / 28) * 60));
        etaText.textContent = `~${estMins} min${estMins > 1 ? 's' : ''} (${roadKm.toFixed(1)} km)`;
        if (statsWrap) statsWrap.classList.remove('hidden');
    }

    function fetchRoutePolyline(origin, destination) {
        lastRouteOrigin = { lat: origin.lat, lng: origin.lng };

        fetch('<?= base_url('api/route') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                origin: origin,
                destination: destination
            })
        })
        .then(res => res.json())
        .then(data => {
            let path = [];
            let updatedStats = false;
            if (data && data.success && data.route) {
                if (data.route.points && data.route.points.length > 0) {
                    path = data.route.points.map(p => new google.maps.LatLng(parseFloat(p.lat), parseFloat(p.lng)));
                } else if (data.route.encodedPolyline && google.maps.geometry && google.maps.geometry.encoding) {
                    path = google.maps.geometry.encoding.decodePath(data.route.encodedPolyline);
                }

                if (data.route.duration_text && data.route.distance_text) {
                    const statsWrap = document.getElementById('liveRouteStats');
                    const etaText = document.getElementById('liveEtaText');
                    if (statsWrap && etaText) {
                        etaText.textContent = `${data.route.duration_text} (${data.route.distance_text})`;
                        statsWrap.classList.remove('hidden');
                        updatedStats = true;
                    }
                }
            }

            if (!updatedStats) {
                updateFallbackRouteEta(origin, destination);
            }

            if (!path || path.length === 0) {
                path = [origin, destination];
            }

            if (routePolyline) routePolyline.setMap(null);
            routePolyline = new google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: '#2563eb',
                strokeOpacity: 0.85,
                strokeWeight: 4,
                map: trackMap
            });
        })
        .catch(err => {
            console.warn('Could not fetch route polyline:', err);
            updateFallbackRouteEta(origin, destination);
            if (routePolyline) routePolyline.setMap(null);
            routePolyline = new google.maps.Polyline({
                path: [origin, destination],
                geodesic: true,
                strokeColor: '#2563eb',
                strokeOpacity: 0.85,
                strokeWeight: 4,
                map: trackMap
            });
        });
    }

    const CUST_MAP_TYPE_STORAGE_KEY = 'blax_customer_track_map_type';
    let currentCustMapType = localStorage.getItem(CUST_MAP_TYPE_STORAGE_KEY) || 'hybrid';

    function updateCustomerMapTypeToggleUI() {
        const icon = document.getElementById('custMapTypeIcon');
        const text = document.getElementById('custMapTypeText');
        if (!icon || !text) return;
        if (currentCustMapType === 'hybrid') {
            icon.textContent = 'map';
            text.textContent = 'Roadmap';
        } else {
            icon.textContent = 'satellite_alt';
            text.textContent = 'Satellite';
        }
    }

    function toggleCustomerMapType() {
        if (!trackMap || typeof google === 'undefined' || !google.maps) return;
        if (currentCustMapType === 'hybrid') {
            currentCustMapType = 'roadmap';
            trackMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        } else {
            currentCustMapType = 'hybrid';
            trackMap.setMapTypeId(google.maps.MapTypeId.HYBRID);
        }
        localStorage.setItem(CUST_MAP_TYPE_STORAGE_KEY, currentCustMapType);
        updateCustomerMapTypeToggleUI();
    }

    function createCourierInfoWindowContent(isStale) {
        const staleNotice = isStale ? '<div style="font-size:10px;font-weight:700;color:#dc2626;background:#fef2f2;padding:2px 6px;border-radius:4px;border:1px solid #fecaca;margin-top:3px;">⚠️ Signal Lost • Last Known Position</div>' : '<div style="font-size:11px;color:#475569;margin-top:2px;">Status: En Route to Delivery</div>';
        return `
            <div style="font-family:inherit;padding:4px;min-width:140px;">
                <div style="font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.5px;">Live Courier Position</div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:2px;">🏍️ ${COURIER_NAME}</div>
                ${staleNotice}
            </div>
        `;
    }

    window.initGoogleTrackMap = function() {
        const container = document.getElementById('trackMap');
        if (!container || typeof google === 'undefined' || !google.maps) return;

        const destLatLng    = { lat: parseFloat(DEST_COORDS[0]), lng: parseFloat(DEST_COORDS[1]) };
        const courierLatLng = { lat: parseFloat(COURIER_COORDS[0]), lng: parseFloat(COURIER_COORDS[1]) };

        const initialMapTypeId = currentCustMapType === 'roadmap' ? google.maps.MapTypeId.ROADMAP : google.maps.MapTypeId.HYBRID;

        trackMap = new google.maps.Map(container, {
            center: courierLatLng,
            zoom: 15,
            mapTypeId: initialMapTypeId,
            mapId: 'DEMO_MAP_ID',
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        updateCustomerMapTypeToggleUI();

        // Detect user manual pan/drag to suspend auto-follow until re-enabled
        trackMap.addListener('dragstart', () => {
            if (isAutoFollow) {
                isAutoFollow = false;
                updateAutoFollowUI();
            }
        });

        // 1. Customer Destination Marker
        const destContent = createMarkerContent('dest');
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            destMarker = new google.maps.marker.AdvancedMarkerElement({
                map: trackMap,
                position: destLatLng,
                content: destContent,
                title: 'Delivery Destination'
            });
        } else {
            destMarker = new google.maps.Marker({
                map: trackMap,
                position: destLatLng,
                title: 'Delivery Destination'
            });
        }
        const destInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="font-family:inherit;padding:4px;">
                    <div style="font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;">Delivery Destination</div>
                    <div style="font-size:12px;font-weight:600;color:#0f172a;margin-top:2px;">${DEST_ADDRESS}</div>
                </div>
            `
        });
        destMarker.addListener('click', () => {
            destInfoWindow.open(trackMap, destMarker);
        });

        // 2. Courier Marker (Live Rider)
        const courierContent = createMarkerContent('courier');
        const courierTitle = `${COURIER_NAME} (Live Courier)`;
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            courierMarker = new google.maps.marker.AdvancedMarkerElement({
                map: trackMap,
                position: courierLatLng,
                content: courierContent,
                title: courierTitle
            });
        } else {
            courierMarker = new google.maps.Marker({
                map: trackMap,
                position: courierLatLng,
                title: courierTitle
            });
        }
        courierInfoWindow = new google.maps.InfoWindow({
            content: createCourierInfoWindowContent(false)
        });
        courierMarker.addListener('click', () => {
            courierInfoWindow.open(trackMap, courierMarker);
        });

        // Open courier info window briefly on load
        setTimeout(() => {
            courierInfoWindow.open(trackMap, courierMarker);
        }, 500);

        // 3. Direct Route Polyline from Live Courier to Customer Destination
        fetchRoutePolyline(courierLatLng, destLatLng);

        // Initial map bounds fitting
        const bounds = new google.maps.LatLngBounds();
        bounds.extend(courierLatLng);
        bounds.extend(destLatLng);
        trackMap.fitBounds(bounds, 70);

        // Start live position polling (every 6 seconds)
        startPositionPolling();
    };

    let currentRiderPos = { lat: parseFloat(COURIER_COORDS[0]), lng: parseFloat(COURIER_COORDS[1]) };
    let animationFrameId = null;
    let pollInterval = null;

    function updateAutoFollowUI() {
        const dot = document.getElementById('autoFollowDot');
        const text = document.getElementById('autoFollowText');
        const btn = document.getElementById('btn-auto-follow');
        if (!dot || !text || !btn) return;

        if (isAutoFollow) {
            dot.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-pulse';
            text.textContent = 'Auto-Follow: ON';
            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 hover:bg-slate-800 text-white text-xs font-bold shadow-md backdrop-blur-md transition-all active:scale-95';
        } else {
            dot.className = 'w-2 h-2 rounded-full bg-gray-400';
            text.textContent = 'Auto-Follow: OFF';
            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-600/90 hover:bg-gray-700 text-gray-200 text-xs font-bold shadow-md backdrop-blur-md transition-all active:scale-95';
        }
    }

    function toggleAutoFollow() {
        isAutoFollow = !isAutoFollow;
        updateAutoFollowUI();
        if (isAutoFollow && trackMap) {
            trackMap.panTo(currentRiderPos);
        }
    }

    function animateMarkerTo(targetLat, targetLng) {
        if (!courierMarker) return;
        const startLat = currentRiderPos.lat;
        const startLng = currentRiderPos.lng;

        // Calculate heading bearing and speed if moved
        const dist = Math.hypot(targetLat - startLat, targetLng - startLng);
        let bearing = 0;
        let speedKmh = 0;
        if (dist > 0.00002) {
            bearing = calculateBearing(startLat, startLng, targetLat, targetLng);
            const meters = distanceMeters(startLat, startLng, targetLat, targetLng);
            // Polling interval is 6s
            speedKmh = Math.min(90, Math.round((meters / 6) * 3.6));
            const iconRotate = document.getElementById('courierIconRotate');
            if (iconRotate) {
                iconRotate.style.transform = `rotate(${Math.round(bearing)}deg)`;
            }
            updateCustTelemetryHUD(speedKmh, bearing);
        } else {
            updateCustTelemetryHUD(0, bearing);
        }

        const startTime = performance.now();
        const duration = 2000; // 2-second smooth lerp

        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out quad
            const ease = 1 - (1 - progress) * (1 - progress);

            const curLat = startLat + (targetLat - startLat) * ease;
            const curLng = startLng + (targetLng - startLng) * ease;
            currentRiderPos = { lat: curLat, lng: curLng };

            if (courierMarker.position && typeof courierMarker.position.lat === 'function') {
                courierMarker.setPosition(new google.maps.LatLng(curLat, curLng));
            } else {
                courierMarker.position = { lat: curLat, lng: curLng };
            }

            // Camera auto-follow tracking
            if (isAutoFollow && trackMap) {
                trackMap.panTo(currentRiderPos);
            }

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(step);
            }
        }
        animationFrameId = requestAnimationFrame(step);
    }

    function startPositionPolling() {
        if (['delivered', 'completed', 'cancelled'].includes(ORDER_STATUS)) return;

        let isTrackingTerminated = false;

        function fetchRiderPosition() {
            if (isTrackingTerminated || document.visibilityState === 'hidden') return Promise.resolve();
            return fetch('<?= !empty($pollPositionUrl) ? esc($pollPositionUrl) : base_url('customer/orders/track/' . ($order['order_number'] ?? $order['id']) . '/position') ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success && data.lat && data.lng) {
                    const newLat = parseFloat(data.lat);
                    const newLng = parseFloat(data.lng);
                    const isStale = Boolean(data.location_is_stale);

                    if (courierInfoWindow) {
                        courierInfoWindow.setContent(createCourierInfoWindowContent(isStale));
                    }

                    // If coordinates moved significantly (> 1 meter), smoothly interpolate
                    if (Math.abs(newLat - currentRiderPos.lat) > 0.00001 || Math.abs(newLng - currentRiderPos.lng) > 0.00001) {
                        animateMarkerTo(newLat, newLng);

                        // If rider moved > 40 meters since last route calculation, refresh road polyline to customer destination
                        if (!lastRouteOrigin || Math.hypot(newLat - lastRouteOrigin.lat, newLng - lastRouteOrigin.lng) > 0.0004) {
                            const destLatLng = { lat: parseFloat(DEST_COORDS[0]), lng: parseFloat(DEST_COORDS[1]) };
                            fetchRoutePolyline({ lat: newLat, lng: newLng }, destLatLng);
                        }
                    }

                    if (data.status === 'delivered' || data.status === 'completed' || data.status === 'cancelled') {
                        isTrackingTerminated = true;
                        if (pollInterval) clearTimeout(pollInterval);
                        setTimeout(() => location.reload(), 1500);
                    }
                }
            })
            .catch(err => {
                console.debug('Position poll skipped:', err);
            });
        }

        function scheduleNextRiderPoll() {
            if (pollInterval) clearTimeout(pollInterval);
            if (isTrackingTerminated || document.visibilityState === 'hidden') return;
            pollInterval = setTimeout(() => {
                fetchRiderPosition().finally(() => {
                    scheduleNextRiderPoll();
                });
            }, 12000);
        }

        scheduleNextRiderPoll();

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible' && !isTrackingTerminated) {
                fetchRiderPosition().finally(() => {
                    scheduleNextRiderPoll();
                });
            } else {
                if (pollInterval) clearTimeout(pollInterval);
            }
        });

        window.addEventListener('pagehide', () => {
            if (pollInterval) clearTimeout(pollInterval);
        });
    }

    function centerOnCourier() {
        if (!trackMap) return;
        isAutoFollow = true;
        updateAutoFollowUI();
        trackMap.panTo(currentRiderPos);
        trackMap.setZoom(16);
        if (courierInfoWindow && courierMarker) {
            courierInfoWindow.open(trackMap, courierMarker);
        }
    }

    function toggleItemsCollapsible() {
        const content = document.getElementById('items-collapsible-content');
        const chevron = document.getElementById('items-toggle-chevron');
        if (!content) return;

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }

    // Fallback if callback didn't fire immediately
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof google !== 'undefined' && google.maps && !trackMap) {
            window.initGoogleTrackMap();
        }
    });
</script>

<!-- Google Maps JavaScript API (loaded after callback is defined) -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc(env('GOOGLE_MAPS_API_KEY')) ?>&libraries=marker,geometry&loading=async&callback=initGoogleTrackMap" async defer></script>

<?= $this->endSection() ?>
