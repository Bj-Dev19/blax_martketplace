<?= $this->extend('layouts/tenant') ?>
<?= $this->section('content') ?>

<div class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- Top Navigation & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="<?= base_url('tenant/delivery') ?>" class="p-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors flex items-center justify-center shadow-2xs">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                    <a href="<?= base_url('tenant/delivery') ?>" class="hover:underline">Deliveries</a>
                    <span>/</span>
                    <span class="font-mono text-terra-green font-bold">#<?= esc($delivery['tracking_id']) ?></span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black font-display text-slate-900 dark:text-white tracking-tight mt-0.5 flex items-center gap-2.5 flex-wrap">
                    <span>Delivery Route &amp; GPS</span>
                    <?= status_badge($delivery['status']) ?>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3.5 py-1.5 rounded-2xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-terra-green">local_shipping</span>
                <span>Type: <?= esc(ucfirst(str_replace('_', ' ', $delivery['deliverable_type'] ?? 'Order'))) ?></span>
            </span>
            <span class="px-3.5 py-1.5 rounded-2xl text-xs font-semibold bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                <span><?= date('M d, Y h:i A', strtotime($delivery['created_at'])) ?></span>
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm font-semibold flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Main Grid: Left 7 cols Map, Right 5 cols Info & Status -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Map Column (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">near_me</span>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold font-display text-slate-900 dark:text-white">Live Route &amp; GPS Tracking</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Polomolok, South Cotabato &bull; Direct Waypoint Route</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= in_array($delivery['status'], ['shipped', 'in_transit']) ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 border-slate-200 dark:border-slate-700' ?>">
                            <span class="w-2 h-2 rounded-full <?= in_array($delivery['status'], ['shipped', 'in_transit']) ? 'bg-emerald-500 animate-ping' : 'bg-slate-400' ?>"></span>
                            <span><?= in_array($delivery['status'], ['shipped', 'in_transit']) ? 'GPS Live Active' : 'Passive Tracking' ?></span>
                        </span>
                    </div>
                </div>

                <!-- Live GPS Broadcasting & Telemetry Bar -->
                <div id="liveTrackingBar" class="p-3 px-4 bg-slate-50/80 dark:bg-slate-850 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs flex-wrap">
                    <div class="flex items-center gap-2">
                        <span id="gpsIndicatorDot" class="w-2.5 h-2.5 rounded-full <?= in_array($delivery['status'], ['shipped', 'in_transit']) ? 'bg-emerald-500 animate-ping' : 'bg-slate-400' ?>"></span>
                        <span class="font-bold text-slate-900 dark:text-white">GPS Status:</span>
                        <span id="gpsStatusText" class="text-slate-500 dark:text-slate-400 font-medium">
                            <?php if (in_array($delivery['status'], ['shipped', 'in_transit'])): ?>
                                Connecting to device GPS...
                            <?php elseif ($delivery['status'] === 'delivered'): ?>
                                Delivery completed
                            <?php elseif ($delivery['status'] === 'cancelled'): ?>
                                Delivery cancelled
                            <?php else: ?>
                                Awaiting dispatch
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span id="gpsLastUpdated" class="font-mono text-slate-400 text-[11px]"></span>
                        
                        <!-- Quick Action: Scan Delivery QR -->
                        <button type="button" 
                                onclick="openDeliveryScanner()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-terra-green hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer"
                                title="Open QR Scanner to verify delivery">
                            <span class="material-symbols-outlined text-[15px]">qr_code_scanner</span>
                            <span>Scan QR</span>
                        </button>

                        <!-- Satellite / Roadmap Toggle Button -->
                        <button type="button" 
                                id="btn-tenant-map-type"
                                onclick="toggleTenantMapType()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-all cursor-pointer"
                                title="Toggle Satellite / Roadmap View">
                            <span id="tenantMapTypeIcon" class="material-symbols-outlined text-[15px] text-terra-green">satellite_alt</span>
                            <span id="tenantMapTypeText">Satellite</span>
                        </button>

                        <!-- Auto-Follow Camera Button -->
                        <button type="button" 
                                id="btn-tenant-autofollow"
                                onclick="toggleTenantAutoFollow()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-terra-green hover:text-white text-xs font-bold transition-all">
                            <span id="tenantAutoFollowDot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span id="tenantAutoFollowText">Auto-Follow: ON</span>
                        </button>

                        <!-- Center Map on Rider Button -->
                        <button type="button" 
                                onclick="centerOnTenantRider()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold shadow-xs transition-all">
                            <span class="material-symbols-outlined text-[14px]">my_location</span>
                            <span>Center</span>
                        </button>

                        <!-- Force GPS Sync Button -->
                        <button type="button" 
                                onclick="refreshTenantGps()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-all cursor-pointer"
                                title="Force GPS Location Re-detect">
                            <span class="material-symbols-outlined text-[14px] text-terra-green">sync</span>
                            <span>Sync</span>
                        </button>
                    </div>
                </div>

                <!-- GPS Telemetry HUD Bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3.5 bg-slate-50/60 dark:bg-slate-850/60 border-b border-slate-100 dark:border-slate-800 text-xs">
                    <!-- Live Speedometer -->
                    <div class="flex items-center gap-2.5 p-2.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                        <div class="p-1.5 bg-blue-500/10 text-blue-600 rounded-xl shrink-0">
                            <span class="material-symbols-outlined text-[18px]">speed</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Speed</span>
                            <span id="hudSpeed" class="font-bold text-slate-900 dark:text-white font-mono text-xs truncate">0 km/h</span>
                        </div>
                    </div>

                    <!-- Compass Heading -->
                    <div class="flex items-center gap-2.5 p-2.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                        <div class="p-1.5 bg-purple-500/10 text-purple-600 rounded-xl shrink-0">
                            <span id="hudCompassIcon" class="material-symbols-outlined text-[18px] inline-block transition-transform duration-300">explore</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Heading</span>
                            <span id="hudHeading" class="font-bold text-slate-900 dark:text-white font-mono text-xs truncate">0° N</span>
                        </div>
                    </div>

                    <!-- GPS Accuracy -->
                    <div class="flex items-center gap-2.5 p-2.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                        <div class="p-1.5 bg-emerald-500/10 text-emerald-600 rounded-xl shrink-0">
                            <span class="material-symbols-outlined text-[18px]">satellite_alt</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Accuracy</span>
                            <span id="hudAccuracy" class="font-bold text-emerald-700 dark:text-emerald-400 font-mono text-xs truncate">±5m (High)</span>
                        </div>
                    </div>

                    <!-- Remaining Route & ETA -->
                    <div class="flex items-center gap-2.5 p-2.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                        <div class="p-1.5 bg-amber-500/10 text-amber-600 rounded-xl shrink-0">
                            <span class="material-symbols-outlined text-[18px]">timer</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Route & ETA</span>
                            <span id="hudEta" class="font-bold text-amber-700 dark:text-amber-400 font-mono text-xs truncate">Calculating...</span>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Canvas with Floating QR Code Scanner -->
                <div class="relative w-full overflow-hidden">
                    <div id="singleDeliveryMap" class="w-full h-[480px] sm:h-[520px] bg-slate-100 dark:bg-slate-800 z-0 relative" style="min-height:480px;"></div>

                    <!-- Floating QR Code Scanner Action Pill Button on Map -->
                    <div class="absolute top-4 left-4 z-30 pointer-events-auto">
                        <button type="button" 
                                id="btnFloatingQrScan"
                                onclick="openDeliveryScanner()" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-terra-green hover:bg-emerald-700 text-white font-bold text-xs shadow-xl hover:scale-105 active:scale-95 transition-all border border-white/40 cursor-pointer group">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-200 opacity-80"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                            </span>
                            <span class="material-symbols-outlined text-[18px] group-hover:rotate-12 transition-transform">qr_code_scanner</span>
                            <span class="tracking-wide font-bold">Scan Delivery QR</span>
                        </button>
                    </div>
                </div>

                <!-- Map Legend Banner -->
                <div class="p-3.5 bg-slate-50/70 dark:bg-slate-850 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 flex-wrap gap-2">
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-orange-600 inline-block shadow-2xs"></span>
                            <strong class="text-slate-800 dark:text-slate-200">Live Courier (You)</strong>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block shadow-2xs"></span>
                            <strong class="text-slate-800 dark:text-slate-200">Destination (Customer)</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Destination Address Card -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 flex items-start gap-3.5 shadow-xs">
                <span class="p-2.5 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 rounded-2xl material-symbols-outlined shrink-0 text-xl">home_pin</span>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Delivery Destination Address</h4>
                    <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5"><?= esc($delivery['destination_address'] ?: 'Customer Delivery Address, Polomolok') ?></p>
                    <p class="text-xs font-mono text-slate-400 mt-1">Coordinates: <?= number_format($destLat, 6) ?>, <?= number_format($destLng, 6) ?></p>
                </div>
            </div>
        </div>

        <!-- Details Column (5 cols) -->
        <div class="lg:col-span-5 space-y-4">

            <!-- Status Updater & Timeline Card -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Delivery Progress</h3>
                    <span class="font-mono text-xs text-terra-green font-bold">#<?= esc($delivery['tracking_id']) ?></span>
                </div>

                <!-- Visual Step Indicator -->
                <?php
                    $curStatus = $delivery['status'] ?? 'ready_for_pickup';
                    $steps = [
                        'ready_for_pickup' => ['label' => 'Ready', 'icon' => 'inventory_2'],
                        'shipped'          => ['label' => 'Dispatched', 'icon' => 'local_shipping'],
                        'in_transit'       => ['label' => 'In Transit', 'icon' => 'two_wheeler'],
                        'delivered'        => ['label' => 'Delivered', 'icon' => 'check_circle'],
                    ];
                    $stepKeys = array_keys($steps);
                    $curIdx = array_search($curStatus, $stepKeys, true);
                    if ($curIdx === false) {
                        $curIdx = ($curStatus === 'cancelled' || $curStatus === 'returned') ? -1 : 0;
                    }
                ?>
                <div class="grid grid-cols-4 gap-1 relative pt-2">
                    <?php foreach ($stepKeys as $idx => $k): ?>
                        <?php 
                            $isPassed = ($curIdx >= $idx && $curIdx !== -1);
                            $isCurrent = ($curIdx === $idx);
                        ?>
                        <div class="flex flex-col items-center text-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all <?= $isCurrent ? 'bg-terra-green text-white ring-4 ring-terra-green/20 scale-110 shadow-sm' : ($isPassed ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400') ?>">
                                <span class="material-symbols-outlined text-[16px]"><?= $steps[$k]['icon'] ?></span>
                            </div>
                            <span class="text-[10px] mt-1.5 font-semibold <?= $isCurrent ? 'text-terra-green font-bold' : ($isPassed ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400') ?>">
                                <?= $steps[$k]['label'] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Status Update Form -->
                <form id="deliveryStatusForm" action="<?= base_url('tenant/deliveries/update-status') ?>" method="POST" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="delivery_id" value="<?= (int) $delivery['id'] ?>">

                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Update Delivery Status</label>
                    <div class="flex items-center gap-2">
                        <select name="delivery_status" id="deliveryStatusSelect" class="flex-1 p-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-terra-green">
                            <option value="ready_for_pickup" <?= $curStatus === 'ready_for_pickup' ? 'selected' : '' ?>>Ready for Pickup</option>
                            <option value="shipped" <?= $curStatus === 'shipped' ? 'selected' : '' ?>>Shipped (Dispatched)</option>
                            <option value="in_transit" <?= $curStatus === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                            <option value="delivered" <?= $curStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="returned" <?= $curStatus === 'returned' ? 'selected' : '' ?>>Returned</option>
                            <option value="cancelled" <?= $curStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-terra-green text-white font-bold text-xs rounded-xl hover:bg-emerald-700 transition-all shadow-xs shrink-0">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customer Card -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Customer Information</h4>
                    <?php if ($customer): ?>
                        <span class="text-[11px] text-slate-400 font-mono">ID: #<?= (int)$customer['id'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <?php if (!empty($customer['profile_image_url'])): ?>
                        <img src="<?= esc(profile_image_url($customer['profile_image_url'])) ?>" class="w-12 h-12 rounded-full object-cover border border-slate-200 dark:border-slate-700" alt="Customer">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center font-bold text-base ring-2 ring-slate-100 dark:ring-slate-800">
                            <?= strtoupper(substr($customer['first_name'] ?? 'C', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-900 dark:text-white text-sm truncate"><?= esc(trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? 'Customer'))) ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                            <span class="material-symbols-outlined text-[14px] text-terra-green">phone</span>
                            <span><?= esc($customer['phone'] ?? 'No phone provided') ?></span>
                        </p>
                        <?php if (!empty($customer['email'])): ?>
                            <p class="text-[11px] text-slate-400 truncate"><?= esc($customer['email']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Deliverable Details (Order or Printing Job) -->
            <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        <?= $order ? 'Order Items' : 'Printing Job Specs' ?>
                    </h4>
                    <?php if ($order): ?>
                        <a href="<?= base_url('tenant/orders') ?>" class="text-xs font-bold text-terra-green hover:underline font-mono">#<?= esc($order['order_number']) ?></a>
                    <?php elseif ($printingRequest): ?>
                        <span class="text-xs font-bold text-terra-green font-mono">#<?= esc($printingRequest['request_number'] ?? 'PR-'.$printingRequest['id']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($orderItems)): ?>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-64 overflow-y-auto pr-1">
                        <?php foreach ($orderItems as $it): ?>
                            <?php
                                $img = !empty($it['gallery_image']) ? $it['gallery_image'] : (!empty($it['product_image']) ? $it['product_image'] : '');
                                if ($img !== '' && !str_starts_with($img, 'http://') && !str_starts_with($img, 'https://')) {
                                    $img = base_url($img);
                                }
                            ?>
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <?php if ($img): ?>
                                        <img src="<?= esc($img) ?>" class="w-10 h-10 rounded-xl object-cover bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shrink-0" alt="<?= esc($it['product_name']) ?>">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= esc($it['product_name']) ?></p>
                                        <?php if (!empty($it['variant_label'])): ?>
                                            <span class="text-[10px] text-terra-green font-semibold"><?= esc($it['variant_label']) ?></span>
                                        <?php endif; ?>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Qty: <?= (int)$it['quantity'] ?> &times; ₱<?= number_format((float)$it['unit_price'], 2) ?></p>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-xs text-slate-900 dark:text-white shrink-0">₱<?= number_format((float)$it['line_total'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($order): ?>
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-1 text-xs">
                            <div class="flex justify-between text-slate-500 dark:text-slate-400"><span>Subtotal:</span><span class="font-mono">₱<?= number_format((float)$order['subtotal'], 2) ?></span></div>
                            <div class="flex justify-between text-slate-500 dark:text-slate-400"><span>Shipping Fee:</span><span class="font-mono">₱<?= number_format((float)$order['shipping_fee'], 2) ?></span></div>
                            <div class="flex justify-between text-sm font-bold text-slate-900 dark:text-white pt-1.5 border-t border-slate-100 dark:border-slate-800"><span>Total:</span><span class="font-mono text-terra-green">₱<?= number_format((float)$order['total_amount'], 2) ?></span></div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($printingRequest): ?>
                    <div class="space-y-2 text-xs py-1">
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">File:</span><span class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]"><?= esc($printingRequest['file_name']) ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Pages & Copies:</span><span class="font-bold text-slate-900 dark:text-white"><?= (int)$printingRequest['page_count'] ?> pages &times; <?= (int)$printingRequest['copies'] ?> copies</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Paper & Color:</span><span class="font-bold text-slate-900 dark:text-white"><?= esc($printingRequest['paper_size']) ?> (<?= esc($printingRequest['color_mode']) ?>)</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Binding:</span><span class="font-bold text-slate-900 dark:text-white"><?= esc($printingRequest['binding_option'] ?: 'None') ?></span></div>
                        <div class="flex justify-between pt-2 border-t border-slate-100 dark:border-slate-800 font-bold"><span class="text-slate-900 dark:text-white">Total Price:</span><span class="font-mono text-terra-green text-sm">₱<?= number_format((float)$printingRequest['total_price'], 2) ?></span></div>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-slate-400 py-2">No linked items record found.</p>
                <?php endif; ?>
            </div>

        </div>

    </div>

</div>

<script>
    let mapInstance     = null;
    let shopMarker      = null;
    let destMarker      = null;
    let riderMarker     = null;
    let routePolyline   = null;
    let routePathPoints = [];
    let watchId         = null;
    let lastSentTime    = 0;
    let lastSentCoords  = null;

    // Handle Google Maps API authentication / activation failure
    window.gm_authFailure = function() {
        const container = document.getElementById('singleDeliveryMap');
        if (container) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full p-6 text-center bg-amber-50/80 dark:bg-amber-950/30 border-2 border-dashed border-amber-300 rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-amber-600 mb-2">warning</span>
                    <h3 class="font-bold text-amber-900 dark:text-amber-200 text-base">Google Maps API Activation Required</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300/80 max-w-md mt-1">
                        The Google Cloud project has not yet activated the <strong>Maps JavaScript API</strong> or the API Key needs permissions.
                    </p>
                    <a href="https://console.cloud.google.com/apis/library/maps-backend.googleapis.com" target="_blank" class="mt-3 inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 text-white rounded-lg text-xs font-semibold hover:bg-amber-700 transition-colors">
                        <span>Enable Maps JavaScript API</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>
            `;
        }
    };

    const destLatLng = { lat: parseFloat(<?= json_encode($destLat) ?>), lng: parseFloat(<?= json_encode($destLng) ?>) };
    const initialRiderLat = parseFloat(<?= json_encode($delivery['current_lat'] ?? null) ?>) || destLatLng.lat;
    const initialRiderLng = parseFloat(<?= json_encode($delivery['current_lng'] ?? null) ?>) || destLatLng.lng;
    let currentRiderLatLng = { lat: initialRiderLat, lng: initialRiderLng };
    const DELIVERY_ID = <?= (int) $delivery['id'] ?>;
    const IS_ACTIVE_DELIVERY = <?= json_encode(in_array($delivery['status'], ['shipped', 'in_transit'])) ?>;
    let isTenantAutoFollow = true;
    let lastTenantRouteOrigin = null;

    const POLOMOLOK_BOUNDS = {
        north: 6.32,
        south: 6.10,
        east: 125.18,
        west: 124.95
    };

    function createDetailMarkerElement(type) {
        const div = document.createElement('div');
        if (type === 'dest') {
            div.innerHTML = `
                <div style="background-color:#16a34a;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,0.3);border:2.5px solid white;cursor:pointer;">
                    <span class="material-symbols-outlined" style="font-size:18px;line-height:1;">home</span>
                </div>
            `;
        } else if (type === 'rider') {
            div.innerHTML = `
                <div style="position:relative;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                    <div style="position:absolute;width:100%;height:100%;border-radius:50%;background:rgba(234,88,12,0.25);animation:pulse 1.8s infinite;"></div>
                    <div id="tenantRiderIconRotate" style="background-color:#ea580c;color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(234,88,12,0.5);border:2.5px solid white;z-index:2;transition:transform 0.4s ease-out;">
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

    const TENANT_MAP_TYPE_STORAGE_KEY = 'blax_tenant_delivery_map_type';
    let currentTenantMapType = localStorage.getItem(TENANT_MAP_TYPE_STORAGE_KEY) || 'hybrid';

    function updateTenantMapTypeToggleUI() {
        const icon = document.getElementById('tenantMapTypeIcon');
        const text = document.getElementById('tenantMapTypeText');
        if (!icon || !text) return;
        if (currentTenantMapType === 'hybrid') {
            icon.textContent = 'map';
            text.textContent = 'Roadmap';
        } else {
            icon.textContent = 'satellite_alt';
            text.textContent = 'Satellite';
        }
    }

    function toggleTenantMapType() {
        if (!mapInstance || typeof google === 'undefined' || !google.maps) return;
        if (currentTenantMapType === 'hybrid') {
            currentTenantMapType = 'roadmap';
            mapInstance.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        } else {
            currentTenantMapType = 'hybrid';
            mapInstance.setMapTypeId(google.maps.MapTypeId.HYBRID);
        }
        localStorage.setItem(TENANT_MAP_TYPE_STORAGE_KEY, currentTenantMapType);
        updateTenantMapTypeToggleUI();
    }

    function updateTenantAutoFollowUI() {
        const dot = document.getElementById('tenantAutoFollowDot');
        const text = document.getElementById('tenantAutoFollowText');
        const btn = document.getElementById('btn-tenant-autofollow');
        if (!dot || !text || !btn) return;

        if (isTenantAutoFollow) {
            dot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse';
            text.textContent = 'Auto-Follow: ON';
            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-terra-green text-terra-green text-xs font-bold transition-all';
        } else {
            dot.className = 'w-2 h-2 rounded-full bg-slate-400';
            text.textContent = 'Auto-Follow: OFF';
            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 text-xs font-bold transition-all';
        }
    }

    function toggleTenantAutoFollow() {
        isTenantAutoFollow = !isTenantAutoFollow;
        updateTenantAutoFollowUI();
        if (isTenantAutoFollow && mapInstance) {
            mapInstance.panTo(currentRiderLatLng);
        }
    }

    function centerOnTenantRider() {
        if (!mapInstance) return;
        isTenantAutoFollow = true;
        updateTenantAutoFollowUI();
        mapInstance.panTo(currentRiderLatLng);
        mapInstance.setZoom(16);
        if (riderInfoWindow && riderMarker) {
            riderInfoWindow.open(mapInstance, riderMarker);
        }
    }

    window.initDeliveryDetailMap = function() {
        const container = document.getElementById('singleDeliveryMap');
        if (!container || typeof google === 'undefined' || !google.maps) return;

        const initialMapTypeId = currentTenantMapType === 'roadmap' ? google.maps.MapTypeId.ROADMAP : google.maps.MapTypeId.HYBRID;

        mapInstance = new google.maps.Map(container, {
            center: currentRiderLatLng,
            zoom: 15,
            minZoom: 11,
            mapTypeId: initialMapTypeId,
            restriction: {
                latLngBounds: POLOMOLOK_BOUNDS,
                strictBounds: false
            },
            mapId: 'DEMO_MAP_ID',
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        updateTenantMapTypeToggleUI();

        mapInstance.addListener('dragstart', () => {
            if (isTenantAutoFollow) {
                isTenantAutoFollow = false;
                updateTenantAutoFollowUI();
            }
        });

        // 1. Destination Marker (Customer)
        const destElem = createDetailMarkerElement('dest');
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            destMarker = new google.maps.marker.AdvancedMarkerElement({
                map: mapInstance,
                position: destLatLng,
                content: destElem,
                title: 'Customer Destination'
            });
        } else {
            destMarker = new google.maps.Marker({
                map: mapInstance,
                position: destLatLng,
                title: 'Customer Destination'
            });
        }
        const destInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding:4px;font-family:sans-serif;">
                    <strong style="color:#16a34a;font-size:13px;"><?= esc(trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? 'Customer'))) ?></strong>
                    <p style="font-size:11px;color:#334155;margin:2px 0 0 0;"><?= esc($delivery['destination_address']) ?></p>
                </div>
            `
        });
        destMarker.addListener('click', () => {
            destInfoWindow.open(mapInstance, destMarker);
        });

        // 2. Rider Marker
        const riderElem = createDetailMarkerElement('rider');
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            riderMarker = new google.maps.marker.AdvancedMarkerElement({
                map: mapInstance,
                position: currentRiderLatLng,
                content: riderElem,
                title: 'Live Courier Position (You)'
            });
        } else {
            riderMarker = new google.maps.Marker({
                map: mapInstance,
                position: currentRiderLatLng,
                title: 'Live Courier Position (You)'
            });
        }
        riderInfoWindow = new google.maps.InfoWindow({
            content: `<div style="padding:4px;font-family:sans-serif;min-width:140px;"><div style="font-size:11px;font-weight:700;color:#ea580c;text-transform:uppercase;letter-spacing:0.5px;">Live Courier Position</div><div style="font-size:13px;font-weight:700;color:#0f172a;margin-top:2px;">🏍️ <?= esc($shop['shop_name'] ?? 'Your Store') ?> Courier (You)</div><div style="font-size:11px;color:#64748b;margin-top:2px;">Live GPS coordinates broadcasting</div></div>`
        });
        riderMarker.addListener('click', () => {
            riderInfoWindow.open(mapInstance, riderMarker);
        });

        // 3. Direct Route Polyline
        fetchDeliveryRoute(currentRiderLatLng, destLatLng);

        const bounds = new google.maps.LatLngBounds();
        bounds.extend(destLatLng);
        bounds.extend(currentRiderLatLng);
        mapInstance.fitBounds(bounds, 60);

        if (IS_ACTIVE_DELIVERY) {
            startGpsBroadcasting();
        }
    };

    let lastPositionTimestamp = 0;

    function updateTelemetryHUD(lat, lng, accuracy, speedMps, bearingDeg) {
        const speedEl = document.getElementById('hudSpeed');
        if (speedEl) {
            let kmh = 0;
            if (typeof speedMps === 'number' && !isNaN(speedMps) && speedMps >= 0) {
                kmh = Math.round(speedMps * 3.6);
            }
            speedEl.textContent = `${kmh} km/h`;
        }

        const headingEl = document.getElementById('hudHeading');
        const compassIcon = document.getElementById('hudCompassIcon');
        if (headingEl && typeof bearingDeg === 'number' && !isNaN(bearingDeg)) {
            const cardinalDirections = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
            const cardinal = cardinalDirections[Math.round((bearingDeg % 360) / 45)];
            headingEl.textContent = `${Math.round(bearingDeg)}° ${cardinal}`;
            if (compassIcon) {
                compassIcon.style.transform = `rotate(${Math.round(bearingDeg)}deg)`;
            }
        }

        const accEl = document.getElementById('hudAccuracy');
        if (accEl) {
            const accVal = Math.round(accuracy || 5);
            let rating = accVal <= 10 ? 'High' : (accVal <= 25 ? 'Good' : 'Weak');
            accEl.textContent = `±${accVal}m (${rating})`;
            accEl.className = accVal <= 10 ? 'font-bold text-emerald-700 dark:text-emerald-400 font-mono text-xs truncate' : 'font-bold text-amber-600 font-mono text-xs truncate';
        }
    }

    function updateRouteEtaHUD(durationText, distanceText) {
        const etaEl = document.getElementById('hudEta');
        if (etaEl && durationText && distanceText) {
            etaEl.textContent = `${durationText} • ${distanceText}`;
        }
    }

    function fetchDeliveryRoute(origin, destination) {
        lastTenantRouteOrigin = { lat: origin.lat, lng: origin.lng };

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
            if (data && data.success && data.route) {
                if (data.route.points && data.route.points.length > 0) {
                    path = data.route.points.map(p => new google.maps.LatLng(parseFloat(p.lat), parseFloat(p.lng)));
                    routePathPoints = data.route.points;
                } else if (data.route.encodedPolyline && google.maps.geometry && google.maps.geometry.encoding) {
                    path = google.maps.geometry.encoding.decodePath(data.route.encodedPolyline);
                    routePathPoints = path.map(p => ({ lat: p.lat(), lng: p.lng() }));
                }

                if (data.route.duration_text && data.route.distance_text) {
                    updateRouteEtaHUD(data.route.duration_text, data.route.distance_text);
                }
            }

            if (!path || path.length === 0) {
                path = [origin, destination];
                routePathPoints = [origin, destination];
            }

            if (routePolyline) routePolyline.setMap(null);
            routePolyline = new google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: '#4A943E',
                strokeOpacity: 0.85,
                strokeWeight: 4,
                map: mapInstance
            });
        })
        .catch(err => {
            console.warn('Could not fetch route from Routes API:', err);
            routePathPoints = [origin, destination];
            if (routePolyline) routePolyline.setMap(null);
            routePolyline = new google.maps.Polyline({
                path: [origin, destination],
                geodesic: true,
                strokeColor: '#4A943E',
                strokeOpacity: 0.85,
                strokeWeight: 4,
                map: mapInstance
            });
        });
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

    let hasAcquiredFirstFix = false;

    function processGpsPosition(pos) {
        if (!pos || !pos.coords) return;
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const accuracy = Math.round(pos.coords.accuracy || 10);
        const now = Date.now();

        let bearing = 0;
        let speedMps = (typeof pos.coords.speed === 'number' && !isNaN(pos.coords.speed) && pos.coords.speed >= 0) ? pos.coords.speed : 0;

        if (lastSentCoords) {
            bearing = calculateBearing(lastSentCoords.lat, lastSentCoords.lng, lat, lng);
            const iconRotate = document.getElementById('tenantRiderIconRotate');
            if (iconRotate && Math.hypot(lat - lastSentCoords.lat, lng - lastSentCoords.lng) > 0.00002) {
                iconRotate.style.transform = `rotate(${Math.round(bearing)}deg)`;
            }

            if (!pos.coords.speed && lastPositionTimestamp > 0) {
                const dist = distanceMeters(lastSentCoords.lat, lastSentCoords.lng, lat, lng);
                const timeSec = (now - lastPositionTimestamp) / 1000;
                if (timeSec > 0 && dist > 1) {
                    speedMps = dist / timeSec;
                }
            }
        }

        lastPositionTimestamp = now;
        currentRiderLatLng = { lat, lng };

        updateTelemetryHUD(lat, lng, accuracy, speedMps, bearing);

        if (riderMarker) {
            if (typeof riderMarker.setPosition === 'function') {
                riderMarker.setPosition(new google.maps.LatLng(lat, lng));
            } else {
                riderMarker.position = { lat: lat, lng: lng };
            }
        }

        if (!hasAcquiredFirstFix) {
            hasAcquiredFirstFix = true;
            if (mapInstance) {
                mapInstance.panTo(currentRiderLatLng);
                mapInstance.setZoom(16);
            }
            fetchDeliveryRoute({ lat, lng }, destLatLng);
            sendLocationUpdate(lat, lng, accuracy);
            return;
        }

        if (isTenantAutoFollow && mapInstance) {
            mapInstance.panTo(currentRiderLatLng);
        }

        let shouldSend = false;
        if (now - lastSentTime >= 5000) {
            shouldSend = true;
        } else if (lastSentCoords && distanceMeters(lat, lng, lastSentCoords.lat, lastSentCoords.lng) >= 5) {
            if (now - lastSentTime >= 2500) {
                shouldSend = true;
            }
        }

        if (shouldSend) {
            sendLocationUpdate(lat, lng, accuracy);

            if (!lastTenantRouteOrigin || Math.hypot(lat - lastTenantRouteOrigin.lat, lng - lastTenantRouteOrigin.lng) > 0.00035) {
                fetchDeliveryRoute({ lat, lng }, destLatLng);
            }
        }
    }

    function handleGpsError(err) {
        let msg = 'Unable to retrieve GPS location.';
        let isDenied = false;
        if (err.code === err.PERMISSION_DENIED) {
            msg = 'Location permission denied. Please allow GPS access in your browser settings.';
            isDenied = true;
        } else if (err.code === err.POSITION_UNAVAILABLE) {
            msg = 'GPS signal unavailable. Trying network location...';
        } else if (err.code === err.TIMEOUT) {
            msg = 'GPS signal timed out. Retrying automatically...';
        }
        const statusElem = document.getElementById('gpsStatusText');
        if (statusElem) statusElem.textContent = msg;
        const dot = document.getElementById('gpsIndicatorDot');
        if (dot) dot.className = isDenied ? 'w-2.5 h-2.5 rounded-full bg-rose-500' : 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';
    }

    function startGpsBroadcasting() {
        if (!navigator.geolocation) {
            const statusElem = document.getElementById('gpsStatusText');
            if (statusElem) statusElem.textContent = 'Geolocation is not supported by your browser.';
            return;
        }

        const statusElem = document.getElementById('gpsStatusText');
        if (statusElem) statusElem.textContent = 'Acquiring GPS fix from device...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                processGpsPosition(pos);
            },
            (err) => {
                console.warn('[GPS] Initial high accuracy lock failed, falling back to standard accuracy:', err.message);
                navigator.geolocation.getCurrentPosition(
                    (fallbackPos) => {
                        processGpsPosition(fallbackPos);
                    },
                    (fallbackErr) => {
                        handleGpsError(fallbackErr);
                    },
                    { enableHighAccuracy: false, timeout: 15000, maximumAge: 10000 }
                );
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 2000 }
        );

        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }

        watchId = navigator.geolocation.watchPosition(
            (pos) => {
                processGpsPosition(pos);
            },
            (err) => {
                handleGpsError(err);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 3000,
                timeout: 12000
            }
        );
    }

    window.refreshTenantGps = function() {
        hasAcquiredFirstFix = false;
        startGpsBroadcasting();
    };

    function sendLocationUpdate(lat, lng, accuracy) {
        lastSentTime = Date.now();
        lastSentCoords = { lat, lng };

        const formData = new FormData();
        formData.append('delivery_id', DELIVERY_ID);
        formData.append('lat', lat);
        formData.append('lng', lng);
        const csrfToken = window.getCsrfToken ? window.getCsrfToken() : '';
        if (csrfToken) {
            formData.append('<?= csrf_token() ?>', csrfToken);
        }

        fetch('<?= base_url('tenant/deliveries/update-location') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const statusElem = document.getElementById('gpsStatusText');
                if (statusElem) {
                    if (data.in_zone === false) {
                        statusElem.textContent = `Broadcasting your live location (outside zone) (±${accuracy}m)`;
                    } else {
                        statusElem.textContent = `Broadcasting your live location to customer (±${accuracy}m)`;
                    }
                }
                const timeElem = document.getElementById('gpsLastUpdated');
                if (timeElem) timeElem.textContent = `Last sent: ${timeStr}`;
                const dot = document.getElementById('gpsIndicatorDot');
                if (dot) {
                    if (data.in_zone === false) {
                        dot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';
                    } else {
                        dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping';
                    }
                }
            } else if (data && data.error) {
                const statusElem = document.getElementById('gpsStatusText');
                if (statusElem) statusElem.textContent = data.error;
            }
        })
        .catch(err => {
            console.debug('Failed to broadcast GPS position:', err);
        });
    }

    let isExitBroadcastStopped = false;
    function stopBroadcastingOnExit() {
        if (watchId !== null) {
            try { navigator.geolocation.clearWatch(watchId); } catch(e) {}
            watchId = null;
        }
        if (DELIVERY_ID && !isExitBroadcastStopped) {
            isExitBroadcastStopped = true;
            const stopUrl = '<?= base_url('tenant/deliveries/stop-broadcast') ?>';
            const params = new URLSearchParams();
            params.append('delivery_id', String(DELIVERY_ID));
            const csrfToken = window.getCsrfToken ? window.getCsrfToken() : '';
            if (csrfToken) {
                params.append('<?= csrf_token() ?>', csrfToken);
            }
            const payload = params.toString();

            if (navigator.sendBeacon) {
                const blob = new Blob([payload], { type: 'application/x-www-form-urlencoded; charset=UTF-8' });
                navigator.sendBeacon(stopUrl, blob);
            }

            try {
                fetch(stopUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: payload,
                    keepalive: true
                }).catch(() => {});
            } catch(e) {}
        }
    }

    window.addEventListener('beforeunload', stopBroadcastingOnExit);
    window.addEventListener('pagehide', stopBroadcastingOnExit);
    window.addEventListener('unload', stopBroadcastingOnExit);

    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.target && !link.href.startsWith('javascript:') && !link.href.includes('#')) {
            stopBroadcastingOnExit();
        }
    }, { capture: true });

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof google !== 'undefined' && google.maps && !mapInstance) {
            window.initDeliveryDetailMap();
        }
    });

    // QR Code Scanner Handlers for Live Delivery View
    const CURRENT_TRACKING_ID = <?= json_encode((string) ($delivery['tracking_id'] ?? '')) ?>;
    const CURRENT_DELIVERY_STATUS = <?= json_encode((string) ($delivery['status'] ?? '')) ?>;
    let deliveryDetailQrCode = null;
    let currentDeliveryScanMode = 'camera';

    function openDeliveryScanner() {
        const modal = document.getElementById('deliveryDetailQrScannerModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        resetScanResult();
        switchDeliveryScanMode('camera');
    }

    function closeDeliveryScanner() {
        const modal = document.getElementById('deliveryDetailQrScannerModal');
        if (modal) modal.classList.add('hidden');
        stopDeliveryCamera();
        deliveryDetailQrCode = null;
    }

    function switchDeliveryScanMode(mode) {
        currentDeliveryScanMode = mode;
        const camBtn = document.getElementById('deliveryTabCameraBtn');
        const fileBtn = document.getElementById('deliveryTabFileBtn');
        const camBox = document.getElementById('deliveryCameraContainer');
        const fileBox = document.getElementById('deliveryFileContainer');

        if (mode === 'camera') {
            if (camBtn) {
                camBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
                camBtn.classList.add('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
            }
            if (fileBtn) {
                fileBtn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
                fileBtn.classList.add('text-slate-600', 'dark:text-slate-400');
            }
            if (camBox) camBox.classList.remove('hidden');
            if (fileBox) fileBox.classList.add('hidden');
            startDeliveryCamera();
        } else {
            if (fileBtn) {
                fileBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
                fileBtn.classList.add('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
            }
            if (camBtn) {
                camBtn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
                camBtn.classList.add('text-slate-600', 'dark:text-slate-400');
            }
            if (fileBox) fileBox.classList.remove('hidden');
            if (camBox) camBox.classList.add('hidden');
            stopDeliveryCamera();
        }
    }

    function startDeliveryCamera() {
        if (typeof Html5Qrcode === 'undefined') return;

        if (window.isSecureContext === false && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            const statusEl = document.getElementById('scannerStatus');
            if (statusEl) statusEl.textContent = 'Camera requires HTTPS or localhost. Please upload a photo or enter code manually.';
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const statusEl = document.getElementById('scannerStatus');
            if (statusEl) statusEl.textContent = 'Camera is not supported on this browser context. Please upload a photo or enter code manually.';
            return;
        }

        if (!deliveryDetailQrCode) {
            deliveryDetailQrCode = new Html5Qrcode("delivery-detail-qr-reader");
        }

        const config = { fps: 12 };
        deliveryDetailQrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => handleScannedCode(decodedText),
            () => {}
        ).catch(err => {
            if (Html5Qrcode.getCameras) {
                Html5Qrcode.getCameras().then(cameras => {
                    if (cameras && cameras.length > 0) {
                        deliveryDetailQrCode.start(
                            cameras[0].id,
                            config,
                            (decodedText) => handleScannedCode(decodedText),
                            () => {}
                        ).catch(() => {
                            const statusEl = document.getElementById('scannerStatus');
                            if (statusEl) statusEl.textContent = 'Camera unavailable. Please upload a photo or enter code manually.';
                        });
                    } else {
                        const statusEl = document.getElementById('scannerStatus');
                        if (statusEl) statusEl.textContent = 'No camera found. Please upload a photo or enter code manually.';
                    }
                }).catch(() => {
                    const statusEl = document.getElementById('scannerStatus');
                    if (statusEl) statusEl.textContent = 'Camera permission denied or unavailable.';
                });
            } else {
                const statusEl = document.getElementById('scannerStatus');
                if (statusEl) statusEl.textContent = 'Camera unavailable. Please upload a photo or enter code manually.';
            }
        });
    }

    function stopDeliveryCamera() {
        if (deliveryDetailQrCode) {
            try {
                if (deliveryDetailQrCode.isScanning) {
                    deliveryDetailQrCode.stop().catch(() => {});
                }
            } catch (e) {}
        }
    }

    function resetScanResult() {
        const box = document.getElementById('scanResultBox');
        if (box) box.classList.add('hidden');
        const sub = document.getElementById('scanResultSub');
        if (sub) sub.textContent = '';
        const photoStatus = document.getElementById('photoScanStatus');
        if (photoStatus) photoStatus.classList.add('hidden');
    }

    function handleScannedCode(rawText) {
        if (!rawText) return;
        if (deliveryDetailQrCode) {
            try { deliveryDetailQrCode.pause(); } catch (e) {}
        }
        verifyScannedCode(rawText);
    }

    function handleDeliveryPhotoUpload(input) {
        if (!input || !input.files || input.files.length === 0) return;
        const file = input.files[0];
        const statusEl = document.getElementById('photoScanStatus');
        if (statusEl) {
            statusEl.className = 'text-xs text-center text-terra-green font-bold animate-pulse';
            statusEl.textContent = 'Scanning image for QR code...';
            statusEl.classList.remove('hidden');
        }

        if (!deliveryDetailQrCode) {
            deliveryDetailQrCode = new Html5Qrcode("delivery-detail-qr-reader");
        }

        deliveryDetailQrCode.scanFile(file, true)
            .then(decodedText => {
                if (statusEl) {
                    statusEl.className = 'text-xs text-center text-emerald-600 font-bold';
                    statusEl.textContent = 'QR Code detected! Verifying...';
                }
                handleScannedCode(decodedText);
            })
            .catch(err => {
                if (statusEl) {
                    statusEl.className = 'text-xs text-center text-rose-600 font-bold';
                    statusEl.textContent = 'Could not detect a QR code in this photo. Please ensure good lighting or enter the code manually.';
                    statusEl.classList.remove('hidden');
                }
            });
    }

    function submitManualTracking() {
        const val = (document.getElementById('manualTrackingInput').value || '').trim();
        if (!val) return;
        verifyScannedCode(val);
    }

    function verifyScannedCode(rawCode) {
        const resultBox = document.getElementById('scanResultBox');
        const msgEl = document.getElementById('scanResultMessage');
        const subEl = document.getElementById('scanResultSub');
        const iconEl = document.getElementById('scanResultIcon');
        const btnContainer = document.getElementById('scanActionBtnContainer');
        if (!resultBox || !msgEl || !iconEl || !btnContainer) return;

        resultBox.classList.remove('hidden', 'bg-rose-50', 'text-rose-800', 'border-rose-200', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-amber-50', 'text-amber-800', 'border-amber-200');
        btnContainer.innerHTML = '';

        let cleaned = (rawCode || '').trim();
        let matched = false;

        try {
            if (cleaned.startsWith('{') && cleaned.endsWith('}')) {
                const parsed = JSON.parse(cleaned);
                cleaned = parsed.tracking_id || parsed.tracking || parsed.order_number || cleaned;
            }
        } catch (e) {}

        const cleanExpected = CURRENT_TRACKING_ID.replace(/^#/, '').trim().toLowerCase();
        const cleanActual = cleaned.replace(/^#/, '').trim().toLowerCase();

        if (cleanActual === cleanExpected || cleanActual.includes(cleanExpected) || cleanExpected.includes(cleanActual)) {
            matched = true;
        }

        if (matched) {
            resultBox.classList.add('bg-emerald-500/10', 'text-emerald-800', 'dark:text-emerald-300', 'border-emerald-500/20');
            iconEl.textContent = 'verified';
            iconEl.className = 'material-symbols-outlined text-[24px] text-emerald-600 mt-0.5';
            msgEl.textContent = `QR Code Verified: #${CURRENT_TRACKING_ID}`;
            if (subEl) subEl.textContent = 'Customer pass and package tracking confirmed successfully.';

            if (CURRENT_DELIVERY_STATUS !== 'delivered') {
                btnContainer.innerHTML = `
                    <button type="button" 
                            onclick="confirmDeliveryCompletion()" 
                            class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">task_alt</span>
                        <span>Confirm &amp; Mark as Delivered</span>
                    </button>
                `;
            } else {
                btnContainer.innerHTML = `
                    <div class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        <span>This package has already been marked as Delivered.</span>
                    </div>
                `;
            }
        } else {
            resultBox.classList.add('bg-amber-500/10', 'text-amber-800', 'dark:text-amber-300', 'border-amber-500/20');
            iconEl.textContent = 'warning';
            iconEl.className = 'material-symbols-outlined text-[24px] text-amber-600 mt-0.5';
            msgEl.textContent = `Scanned Code: ${cleaned}`;
            if (subEl) subEl.textContent = `Does not match expected #${CURRENT_TRACKING_ID}. Please ensure you are scanning the customer's pass for this specific package.`;
            btnContainer.innerHTML = `
                <button type="button" 
                        onclick="resetScanResult(); if(currentDeliveryScanMode==='camera') startDeliveryCamera();" 
                        class="w-full py-2 px-3 bg-amber-600 text-white font-bold text-xs rounded-xl hover:bg-amber-700 transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    <span>Scan Again</span>
                </button>
            `;
        }
    }

    function confirmDeliveryCompletion() {
        const sel = document.getElementById('deliveryStatusSelect');
        const form = document.getElementById('deliveryStatusForm');
        if (sel && form) {
            sel.value = 'delivered';
            form.submit();
        }
    }
</script>

<!-- Floating Delivery QR Code Scanner Modal -->
<div id="deliveryDetailQrScannerModal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 relative">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">qr_code_scanner</span>
                </div>
                <div>
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Scan Delivery QR Pass</h3>
                    <p class="text-xs text-slate-400 font-mono">Expected: <strong class="text-terra-green">#<?= esc($delivery['tracking_id']) ?></strong></p>
                </div>
            </div>
            <button type="button" onclick="closeDeliveryScanner()" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Scan Mode Tabs -->
        <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
            <button type="button" id="deliveryTabCameraBtn" onclick="switchDeliveryScanMode('camera')" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all bg-white dark:bg-slate-900 text-terra-green shadow-xs flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                <span>Live Camera</span>
            </button>
            <button type="button" id="deliveryTabFileBtn" onclick="switchDeliveryScanMode('file')" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span>
                <span>Scan from Photo</span>
            </button>
        </div>

        <!-- Mode 1: Live Camera Viewfinder -->
        <div id="deliveryCameraContainer" class="space-y-2">
            <div class="rounded-2xl overflow-hidden bg-black aspect-square relative flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-inner">
                <div id="delivery-detail-qr-reader" class="w-full h-full"></div>
                <div class="qr-viewfinder-overlay pointer-events-none absolute inset-4 border border-white/20 rounded-xl">
                    <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-terra-green rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-terra-green rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-terra-green rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-terra-green rounded-br-lg"></div>
                    <div class="qr-laser-line"></div>
                </div>
            </div>
            <p id="scannerStatus" class="text-xs text-center text-slate-400 font-medium">Point camera directly at customer's pickup pass, doorstep QR, or printed receipt</p>
        </div>

        <!-- Mode 2: Scan from Photo / Image File -->
        <div id="deliveryFileContainer" class="hidden space-y-2">
            <label for="deliveryPhotoUpload" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-terra-green/40 hover:border-terra-green rounded-2xl bg-terra-green/5 hover:bg-terra-green/10 transition-all cursor-pointer text-center group">
                <span class="p-3 bg-terra-green/10 text-terra-green rounded-2xl material-symbols-outlined text-3xl group-hover:scale-110 transition-transform mb-2">image_search</span>
                <span class="text-xs font-bold text-slate-900 dark:text-white">Click to Select QR Photo / Take Photo</span>
                <span class="text-[11px] text-slate-400 mt-1">Upload an image of the recipient's QR pass</span>
                <input id="deliveryPhotoUpload" type="file" accept="image/*" class="hidden" onchange="handleDeliveryPhotoUpload(this)">
            </label>
            <p id="photoScanStatus" class="text-xs text-center text-slate-400 font-medium hidden"></p>
        </div>

        <!-- Result Box -->
        <div id="scanResultBox" class="hidden p-3.5 rounded-2xl border space-y-2.5 transition-all">
            <div class="flex items-start gap-2.5">
                <span id="scanResultIcon" class="material-symbols-outlined text-[22px] mt-0.5">check_circle</span>
                <div class="flex-1 min-w-0">
                    <p id="scanResultMessage" class="text-xs font-bold leading-relaxed"></p>
                    <p id="scanResultSub" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5"></p>
                </div>
            </div>
            <div id="scanActionBtnContainer" class="pt-1"></div>
        </div>

        <!-- Manual Tracking ID Input Fallback -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Manual Code / Tracking Entry</label>
            <div class="flex items-center gap-2">
                <input id="manualTrackingInput" 
                       type="text" 
                       placeholder="e.g. <?= esc($delivery['tracking_id']) ?>" 
                       class="flex-1 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green focus:outline-none">
                <button type="button" onclick="submitManualTracking()" class="px-4 py-2 bg-terra-green text-white font-bold text-xs rounded-xl hover:bg-emerald-700 transition-all shrink-0">
                    Verify
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#delivery-detail-qr-reader #qr-shaded-region {
    display: none !important;
}
#delivery-detail-qr-reader {
    border: none !important;
}
#delivery-detail-qr-reader video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    border-radius: 1rem !important;
}
.qr-laser-line {
    position: absolute;
    left: 10px;
    right: 10px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #4A943E, transparent);
    box-shadow: 0 0 10px #4A943E;
    animation: qrLaserScan 2s ease-in-out infinite alternate;
}
@keyframes qrLaserScan {
    0% { top: 12px; }
    100% { top: calc(100% - 14px); }
}
</style>

<!-- HTML5 QR Code library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<!-- Google Maps Platform JS API -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc(env('GOOGLE_MAPS_API_KEY')) ?>&libraries=marker,geometry&loading=async&callback=initDeliveryDetailMap" async defer></script>

<?= $this->endSection() ?>
