<?= $this->extend('layouts/tenant') ?>
<?= $this->section('content') ?>

<div class="flex-1 space-y-6 max-w-7xl mx-auto pb-12">

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

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black font-display tracking-tight text-slate-900 dark:text-white">Delivery Management</h1>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">Track, dispatch, and monitor live doorstep shipments across Polomolok.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <?php if (($kpis['shipped'] ?? 0) > 0): ?>
                <form action="<?= base_url('tenant/deliveries/bulk-in-transit') ?>" method="POST" class="inline" onsubmit="return confirm('Dispatch and change all <?= (int) ($kpis['shipped'] ?? 0) ?> shipped package(s) to In Transit?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-xs font-bold transition-all shadow-xs active:scale-95 cursor-pointer" title="Dispatch all shipped packages to In Transit">
                        <span class="material-symbols-outlined text-[18px]">navigation</span>
                        <span>Bulk In Transit (<?= (int) ($kpis['shipped'] ?? 0) ?>)</span>
                    </button>
                </form>
            <?php endif; ?>
            <button type="button" id="btnOpenDeliveryScanner" onclick="openScanner()" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-terra-green hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-xs active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">qr_code_scanner</span>
                <span>Scan Delivery QR</span>
            </button>
        </div>
    </div>

    <!-- 4-Column Bento Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Deliveries -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-all hover:border-terra-green/40 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-terra-green/10 text-terra-green rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-terra-green bg-terra-green/10 border border-terra-green/20 px-2.5 py-0.5 rounded-full">Active</span>
            </div>
            <div>
                <h3 class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Active Deliveries</h3>
                <p class="font-mono text-2xl font-black text-slate-900 dark:text-white mt-0.5"><?= number_format((int) ($kpis['active'] ?? 0)) ?></p>
            </div>
        </div>

        <!-- Shipped Packages -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-all hover:border-blue-500/40 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-blue-500/10 text-blue-600 rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">inventory</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-300 bg-blue-500/10 border border-blue-500/20 px-2.5 py-0.5 rounded-full">Shipped</span>
            </div>
            <div>
                <h3 class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Shipped Packages</h3>
                <p class="font-mono text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5"><?= number_format((int) ($kpis['shipped'] ?? 0)) ?></p>
            </div>
        </div>

        <!-- In Transit -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-all hover:border-amber-500/40 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-amber-500/10 text-amber-600 rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">navigation</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-500/10 border border-amber-500/20 px-2.5 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Transit</span>
                </span>
            </div>
            <div>
                <h3 class="text-xs text-slate-500 dark:text-slate-400 font-semibold">In Transit (Courier)</h3>
                <p class="font-mono text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5"><?= number_format((int) ($kpis['in_transit'] ?? 0)) ?></p>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-all hover:border-emerald-500/40 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-emerald-500/10 text-emerald-600 rounded-xl">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">Today</span>
            </div>
            <div>
                <h3 class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Delivered Today</h3>
                <p class="font-mono text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-0.5"><?= number_format((int) ($kpis['completed_today'] ?? 0)) ?></p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Live Search Input -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input id="liveShipmentSearch" 
                       type="text" 
                       placeholder="Search by product name, order #, tracking ID, customer, or destination..." 
                       value="<?= esc($filters['q'] ?? '') ?>"
                       autocomplete="off"
                       class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green transition-all">
            </div>

            <!-- Status Dropdown Filter -->
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <select id="liveStatusFilter" 
                        onchange="applyStatusFilter(this.value)"
                        class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-terra-green cursor-pointer">
                    <option value="">All Active Deliveries</option>
                    <?php foreach ($statusOptions as $val => $label): ?>
                        <option value="<?= esc($val) ?>" <?= ($filters['status'] ?? '') === $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="button" onclick="resetShipmentFilters()" class="px-4 py-2.5 text-xs font-bold rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-600 dark:text-slate-300">
                    Reset
                </button>
            </div>
        </div>

        <!-- Quick Status Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-semibold custom-scrollbar">
            <?php
                $activeFilter = $filters['status'] ?? '';
                $pills = [
                    ''           => 'All Active Deliveries',
                    'shipped'    => 'Shipped',
                    'in_transit' => 'In Transit',
                ];
            ?>
            <?php foreach ($pills as $pkey => $plabel): ?>
                <button type="button" 
                        onclick="quickFilterStatus('<?= esc($pkey) ?>')" 
                        class="status-pill px-3.5 py-1.5 rounded-xl border transition-all whitespace-nowrap <?= $activeFilter === $pkey ? 'bg-terra-green text-white border-terra-green font-bold shadow-xs' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' ?>"
                        data-status="<?= esc($pkey) ?>">
                    <?= esc($plabel) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Section Heading & Realtime Count -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-1">
        <div>
            <h2 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                <span>Shipment Packages</span>
                <span id="shipmentCountBadge" class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono font-bold border border-slate-200 dark:border-slate-700">
                    <?= count($deliveries ?? []) ?> package(s)
                </span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Click any shipment card to view its dedicated live route &amp; GPS mapping page.</p>
        </div>
        <?php if (($kpis['shipped'] ?? 0) > 0): ?>
            <form action="<?= base_url('tenant/deliveries/bulk-in-transit') ?>" method="POST" class="inline" onsubmit="return confirm('Change all <?= (int) ($kpis['shipped'] ?? 0) ?> shipped package(s) to In Transit?');">
                <?= csrf_field() ?>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer" title="Update all shipped packages to In Transit">
                    <span class="material-symbols-outlined text-[16px]">navigation</span>
                    <span>Set All Shipped to In Transit (<?= (int) ($kpis['shipped'] ?? 0) ?>)</span>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Shipment Cards Grid -->
    <div id="shipmentsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        <?php if (!empty($deliveries)): ?>
            <?php foreach ($deliveries as $d): ?>
                <?php
                    $fullName = trim(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? ''));
                    $initials = $fullName !== '' ? mb_strtoupper(mb_substr($fullName, 0, 2)) : 'CU';
                    $profileImage = trim((string) ($d['profile_image_url'] ?? ''));
                    $trackingClean = esc($d['tracking_id']);
                    $refClean = esc($d['ref_number'] ?? ('ID #' . $d['id']));
                    $isPickup = ($d['fulfillment_method'] ?? '') === 'pickup';
                    $destAddress = esc($d['destination_address'] ?: ($isPickup ? 'Storefront Collection, Polomolok' : 'Customer Address, Polomolok'));
                    $productName = esc($d['product_name'] ?? 'Order Item');
                    $allProductsList = esc($d['all_products_list'] ?? $productName);
                    $extraItems = (int) ($d['extra_items_count'] ?? 0);
                    $totalQty = (int) ($d['total_qty'] ?? 1);
                    $variantLabel = esc($d['variant_label'] ?? '');
                    $searchIndex = strtolower($productName . ' ' . $allProductsList . ' ' . $trackingClean . ' ' . $refClean . ' ' . $fullName . ' ' . $destAddress . ' ' . $d['status']);
                ?>
                <div class="shipment-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 shadow-xs hover:shadow-lg hover:border-terra-green/50 transition-all flex flex-col justify-between group relative space-y-4"
                     data-tracking="<?= $trackingClean ?>"
                     data-status="<?= esc($d['status']) ?>"
                     data-search="<?= esc($searchIndex) ?>">

                    <div class="space-y-3.5">
                        <!-- Top Header: Status & Tracking Number & Menu -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                <?= status_badge($d['status']) ?>
                                <a href="<?= base_url('tenant/deliveries/' . (int) $d['id']) ?>" class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-terra-green bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 truncate" title="Tracking Number">
                                    #<?= $trackingClean ?>
                                </a>
                            </div>

                            <!-- More Action Dropdown -->
                            <div class="relative shrink-0">
                                <button type="button" 
                                        data-row="<?= (int) $d['id'] ?>" 
                                        onclick="toggleDropdown(this, event)" 
                                        class="more-toggle w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer"
                                        title="Quick Status Actions">
                                    <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                </button>
                                <div id="more-menu-<?= (int) $d['id'] ?>" class="hidden more-menu z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-3 min-w-[210px] absolute right-0 top-full mt-1.5 animate-in fade-in zoom-in-95 duration-100" role="menu">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1 pb-1.5">Change Delivery Status</p>
                                    <form action="<?= base_url('tenant/deliveries/update-status') ?>" method="POST" class="space-y-2 px-1 pb-2" onclick="event.stopPropagation();">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="delivery_id" value="<?= (int) $d['id'] ?>">
                                        <select name="delivery_status" class="w-full p-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-terra-green">
                                            <?php foreach ($statusOptions as $val => $label): ?>
                                                <option value="<?= esc($val) ?>" <?= $d['status'] === $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="w-full py-1.5 bg-terra-green text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-colors cursor-pointer">Update Status</button>
                                    </form>
                                    <div class="border-t border-slate-100 dark:border-slate-800 my-1.5"></div>
                                    <a href="<?= base_url('tenant/deliveries/' . (int) $d['id']) ?>" class="w-full text-left px-2 py-1.5 rounded-xl text-terra-green text-xs font-bold hover:bg-terra-green/10 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">near_me</span>
                                        <span>Open Route &amp; Map</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Product & Order Reference Details -->
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <a href="<?= base_url('tenant/deliveries/' . (int) $d['id']) ?>" 
                                       class="text-sm font-bold font-display text-slate-900 dark:text-white hover:text-terra-green transition-colors flex items-center gap-2 leading-snug group/title" 
                                       title="<?= $allProductsList ?>">
                                        <div class="w-8 h-8 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                        </div>
                                        <span class="truncate"><?= $productName ?></span>
                                    </a>
                                </div>
                                <div class="flex flex-col items-end shrink-0">
                                    <span class="text-[11px] font-mono font-extrabold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2 py-0.5 rounded-lg">
                                        <?= $refClean ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Quantity & Variant Chips -->
                            <div class="flex items-center gap-1.5 flex-wrap pl-10 text-xs">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium text-[11px]">
                                    Qty: <strong class="text-slate-900 dark:text-white"><?= $totalQty ?></strong><?= $variantLabel !== '' ? ' &bull; ' . $variantLabel : '' ?>
                                </span>
                                <?php if ($extraItems > 0): ?>
                                    <span class="inline-flex items-center text-[10px] font-bold text-terra-green bg-terra-green/10 px-2 py-0.5 rounded-md">
                                        +<?= $extraItems ?> more items
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Customer Details & Quick Contact -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <?php if ($profileImage !== ''): ?>
                                        <img src="<?= esc(profile_image_url($profileImage)) ?>" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0" alt="Customer" loading="lazy" decoding="async">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center font-bold text-xs shrink-0 ring-1 ring-terra-green/30">
                                            <?= esc($initials) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 dark:text-white text-xs truncate"><?= esc($fullName ?: 'Online Customer') ?></p>
                                        <p class="text-[10px] text-slate-400 font-sans">Recipient</p>
                                    </div>
                                </div>

                                <?php if (!empty($d['customer_phone'])): ?>
                                    <a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $d['customer_phone'])) ?>" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-terra-green/10 hover:bg-terra-green hover:text-white text-terra-green text-xs font-bold transition-colors shrink-0"
                                       title="Call customer directly">
                                        <span class="material-symbols-outlined text-[14px]">phone</span>
                                        <span>Call</span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Address Line -->
                            <div class="flex items-start gap-1.5 text-xs text-slate-600 dark:text-slate-300 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                                <span class="material-symbols-outlined text-[15px] text-emerald-600 shrink-0 mt-0.5">location_on</span>
                                <span class="text-[11px] leading-tight line-clamp-2" title="<?= $destAddress ?>">
                                    <?= $destAddress ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Actions Footer -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <?php if ($d['status'] === 'shipped'): ?>
                            <form action="<?= base_url('tenant/deliveries/update-status') ?>" method="POST" onsubmit="return confirm('Dispatch package #<?= esc($d['tracking_id']) ?> to In Transit?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="delivery_id" value="<?= (int) $d['id'] ?>">
                                <input type="hidden" name="delivery_status" value="in_transit">
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">navigation</span>
                                    <span>Dispatch / Start Transit</span>
                                </button>
                            </form>
                        <?php endif; ?>

                        <a href="<?= base_url('tenant/deliveries/' . (int) $d['id']) ?>" 
                           class="w-full py-2.5 px-4 rounded-xl bg-terra-green text-white font-bold text-xs flex items-center justify-center gap-2 hover:bg-emerald-700 shadow-sm transition-all group-hover:scale-[1.01] active:scale-95">
                            <span class="material-symbols-outlined text-[17px]">near_me</span>
                            <span>View Live Route &amp; Map</span>
                            <span class="material-symbols-outlined text-[15px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-16 text-center bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 p-8 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center mx-auto">
                    <span class="material-symbols-outlined text-3xl">local_shipping</span>
                </div>
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">No Deliveries Found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">There are currently no active doorstep deliveries matching your filter. Orders dispatched for delivery will appear here.</p>
                <button type="button" onclick="resetShipmentFilters()" class="mt-2 px-4 py-2 bg-terra-green text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition-colors">
                    Clear Filters
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- No Match Dynamic Message for Client Filtering -->
    <div id="noMatchMessage" class="hidden py-16 text-center bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl border border-slate-200/80 dark:border-slate-800 p-8 space-y-3">
        <span class="material-symbols-outlined text-4xl text-slate-400">search_off</span>
        <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">No matching shipments</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">Try searching for a different product, order reference, customer, or status.</p>
        <button type="button" onclick="resetShipmentFilters()" class="px-4 py-2 bg-terra-green text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition-colors">
            Reset Filters
        </button>
    </div>

    <!-- Modern Styled Pagination Bar -->
    <?php if (isset($pager) && is_object($pager)): ?>
        <?php
            $dTot   = (int) $pager->getTotal('deliveries');
            $dCur   = (int) $pager->getCurrentPage('deliveries');
            $dPag   = (int) $pager->getPageCount('deliveries');
            $dStart = $dTot === 0 ? 0 : ($dCur - 1) * 12 + 1;
            $dEnd   = min($dCur * 12, $dTot);
        ?>
        <div class="mt-6 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/80 dark:border-slate-800">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                Showing <span class="font-bold text-slate-900 dark:text-white"><?= number_format($dStart) ?></span> to <span class="font-bold text-slate-900 dark:text-white"><?= number_format($dEnd) ?></span> of <span class="font-bold text-slate-900 dark:text-white"><?= number_format($dTot) ?></span> deliveries
            </p>
            <?php if ($dPag > 1): ?>
                <div class="flex items-center gap-1.5">
                    <a class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all <?= $dCur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('deliveries') ?>" title="Previous">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                    <?php
                    $w = [];
                    for ($i = 1; $i <= $dPag; $i++) {
                        if ($i === 1 || $i === $dPag || abs($i - $dCur) <= 2) { $w[] = $i; }
                    }
                    $pv = 0;
                    foreach ($w as $n):
                        if ($n - $pv > 1): ?><span class="px-1 text-slate-400 font-bold">...</span><?php endif; ?>
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold transition-all <?= $dCur === $n ? 'bg-terra-green text-white shadow-xs' : 'hover:bg-white dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700' ?>" href="<?= $pager->getPageURI($n, 'deliveries') ?>"><?= $n ?></a>
                    <?php $pv = $n; endforeach; ?>
                    <a class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all <?= $dCur >= $dPag ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('deliveries') ?>" title="Next">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php elseif (isset($pager) && is_string($pager) && trim($pager) !== ''): ?>
        <div class="mt-6 pt-4">
            <?= $pager ?>
        </div>
    <?php endif; ?>

</div>

<!-- QR Code Scanner Modal -->
<div id="deliveryScannerModal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 relative">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                </div>
                <div>
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Scan Delivery &amp; Order QR</h3>
                    <p class="text-[11px] text-slate-400">Live camera or scan from saved photo</p>
                </div>
            </div>
            <button type="button" onclick="closeScanner()" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
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
                <div id="qr-reader" class="w-full h-full"></div>
                <!-- Clean, non-blocking transparent corner reticle -->
                <div class="qr-viewfinder-overlay pointer-events-none absolute inset-4 border border-white/20 rounded-xl">
                    <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-terra-green rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-terra-green rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-terra-green rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-terra-green rounded-br-lg"></div>
                    <div class="qr-laser-line"></div>
                </div>
            </div>
            <p id="scannerStatus" class="text-xs text-center text-slate-400 font-medium">Point camera directly at QR code or phone screen</p>
        </div>

        <!-- Mode 2: Scan from Photo / Image File -->
        <div id="deliveryFileContainer" class="hidden space-y-2">
            <label for="deliveryPhotoUpload" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-terra-green/40 hover:border-terra-green rounded-2xl bg-terra-green/5 hover:bg-terra-green/10 transition-all cursor-pointer text-center group">
                <span class="p-3 bg-terra-green/10 text-terra-green rounded-2xl material-symbols-outlined text-3xl group-hover:scale-110 transition-transform mb-2">image_search</span>
                <span class="text-xs font-bold text-slate-900 dark:text-white">Click to Select QR Photo / Take Photo</span>
                <span class="text-[11px] text-slate-400 mt-1">Upload the photo of the completed order/slip</span>
                <input id="deliveryPhotoUpload" type="file" accept="image/*" class="hidden" onchange="handleDeliveryPhotoUpload(this)">
            </label>
            <p id="photoScanStatus" class="text-xs text-center text-slate-400 font-medium hidden"></p>
        </div>

        <!-- Result Box with Direct Route Map Link -->
        <div id="scanResultBox" class="hidden p-3.5 rounded-2xl border space-y-2 transition-all">
            <div class="flex items-start gap-2.5">
                <span id="scanResultIcon" class="material-symbols-outlined text-[22px] mt-0.5">check_circle</span>
                <div class="flex-1 min-w-0">
                    <p id="scanResultMessage" class="text-xs font-bold leading-relaxed"></p>
                    <p id="scanResultSub" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5"></p>
                </div>
            </div>
            <div id="scanActionBtnContainer" class="pt-1"></div>
        </div>

        <!-- Manual Tracking ID Input -->
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Manual Code / Order Entry</label>
            <div class="flex items-center gap-2">
                <input id="manualTrackingInput" 
                       type="text" 
                       placeholder="e.g. TRK-F7E82B04, ORD-88460, PR-190" 
                       class="flex-1 px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green focus:outline-none">
                <button type="button" onclick="submitManualTracking()" class="px-4 py-2 bg-terra-green text-white font-bold text-xs rounded-xl hover:bg-emerald-700 transition-all shadow-xs">
                    Lookup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#qr-reader #qr-shaded-region,
#posQrReader #qr-shaded-region {
    display: none !important;
}
#qr-reader, #posQrReader {
    border: none !important;
}
#qr-reader video, #posQrReader video {
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

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let activeMenu = null;
    let deliveryHtml5QrCode = null;

    function closeMenus() {
        if (activeMenu) {
            activeMenu.classList.add('hidden');
            activeMenu = null;
        }
    }

    function toggleDropdown(btn, e) {
        if (e) e.stopPropagation();
        const id   = btn.dataset.row;
        const menu = document.getElementById('more-menu-' + id);
        if (!menu) return;

        if (activeMenu === menu) {
            closeMenus();
            return;
        }
        closeMenus();
        menu.classList.remove('hidden');
        activeMenu = menu;
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.more-menu') && !e.target.closest('.more-toggle')) {
            closeMenus();
        }
    });

    // Realtime Client-Side Filtering
    const searchInput = document.getElementById('liveShipmentSearch');
    const statusSelect = document.getElementById('liveStatusFilter');
    const cards = document.querySelectorAll('.shipment-card');
    const noMatchMessage = document.getElementById('noMatchMessage');
    const countBadge = document.getElementById('shipmentCountBadge');

    function filterShipments() {
        const query = (searchInput.value || '').trim().toLowerCase();
        const status = (statusSelect.value || '').trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const cardSearch = (card.dataset.search || '').toLowerCase();
            const cardStatus = (card.dataset.status || '').toLowerCase();

            const matchQuery = query === '' || cardSearch.includes(query);
            const matchStatus = status === '' || cardStatus === status;

            if (matchQuery && matchStatus) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (countBadge) {
            countBadge.textContent = visibleCount + ' package(s)';
        }

        if (noMatchMessage) {
            if (visibleCount === 0 && cards.length > 0) {
                noMatchMessage.classList.remove('hidden');
            } else {
                noMatchMessage.classList.add('hidden');
            }
        }

        // Update pills
        document.querySelectorAll('.status-pill').forEach(pill => {
            if (pill.dataset.status === status) {
                pill.classList.remove('bg-slate-50', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
                pill.classList.add('bg-terra-green', 'text-white', 'font-bold', 'border-terra-green', 'shadow-xs');
            } else {
                pill.classList.remove('bg-terra-green', 'text-white', 'font-bold', 'border-terra-green', 'shadow-xs');
                pill.classList.add('bg-slate-50', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterShipments);
    }

    function applyStatusFilter(val) {
        statusSelect.value = val;
        filterShipments();
    }

    function quickFilterStatus(val) {
        statusSelect.value = val;
        filterShipments();
    }

    function resetShipmentFilters() {
        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = '';
        filterShipments();
    }

    // QR Code Scanner Handlers
    let currentDeliveryScanMode = 'camera';

    function switchDeliveryScanMode(mode) {
        currentDeliveryScanMode = mode;
        const camBtn = document.getElementById('deliveryTabCameraBtn');
        const fileBtn = document.getElementById('deliveryTabFileBtn');
        const camBox = document.getElementById('deliveryCameraContainer');
        const fileBox = document.getElementById('deliveryFileContainer');

        if (mode === 'camera') {
            camBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
            camBtn.classList.add('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
            fileBtn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
            fileBtn.classList.add('text-slate-600', 'dark:text-slate-400');
            camBox.classList.remove('hidden');
            fileBox.classList.add('hidden');
            startDeliveryCamera();
        } else {
            fileBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
            fileBtn.classList.add('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
            camBtn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-terra-green', 'shadow-xs');
            camBtn.classList.add('text-slate-600', 'dark:text-slate-400');
            fileBox.classList.remove('hidden');
            camBox.classList.add('hidden');
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

        if (!deliveryHtml5QrCode) {
            deliveryHtml5QrCode = new Html5Qrcode("qr-reader");
        }
        
        const config = { fps: 12 };
        deliveryHtml5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                handleScannedCode(decodedText);
            },
            () => {}
        ).catch(err => {
            if (Html5Qrcode.getCameras) {
                Html5Qrcode.getCameras().then(cameras => {
                    if (cameras && cameras.length > 0) {
                        deliveryHtml5QrCode.start(
                            cameras[0].id,
                            config,
                            (decodedText) => handleScannedCode(decodedText),
                            () => {}
                        ).catch(() => {
                            document.getElementById('scannerStatus').textContent = 'Camera unavailable. Please upload a photo or enter code manually.';
                        });
                    } else {
                        document.getElementById('scannerStatus').textContent = 'No camera found. Please upload a photo or enter code manually.';
                    }
                }).catch(() => {
                    document.getElementById('scannerStatus').textContent = 'Camera permission denied or unavailable.';
                });
            } else {
                document.getElementById('scannerStatus').textContent = 'Camera unavailable. Please upload a photo or enter code manually.';
            }
        });
    }

    function stopDeliveryCamera() {
        if (deliveryHtml5QrCode) {
            try {
                if (deliveryHtml5QrCode.isScanning) {
                    deliveryHtml5QrCode.stop().catch(() => {});
                }
            } catch (e) {}
        }
    }

    function openScanner() {
        const modal = document.getElementById('deliveryScannerModal');
        modal.classList.remove('hidden');
        resetScanResult();
        switchDeliveryScanMode('camera');
    }

    function closeScanner() {
        const modal = document.getElementById('deliveryScannerModal');
        modal.classList.add('hidden');
        stopDeliveryCamera();
        deliveryHtml5QrCode = null;
    }

    function resetScanResult() {
        const box = document.getElementById('scanResultBox');
        box.classList.add('hidden');
        const sub = document.getElementById('scanResultSub');
        if (sub) sub.textContent = '';
        const photoStatus = document.getElementById('photoScanStatus');
        if (photoStatus) photoStatus.classList.add('hidden');
    }

    function handleScannedCode(code) {
        if (!code) return;
        if (deliveryHtml5QrCode) {
            try { deliveryHtml5QrCode.pause(); } catch (e) {}
        }
        lookupTracking(code);
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

        if (!deliveryHtml5QrCode) {
            deliveryHtml5QrCode = new Html5Qrcode("qr-reader");
        }

        deliveryHtml5QrCode.scanFile(file, true)
            .then(decodedText => {
                if (statusEl) {
                    statusEl.className = 'text-xs text-center text-emerald-600 font-bold';
                    statusEl.textContent = 'QR Code detected! Looking up details...';
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
        lookupTracking(val);
    }

    function lookupTracking(trackingCode) {
        const resultBox = document.getElementById('scanResultBox');
        const msgEl = document.getElementById('scanResultMessage');
        const subEl = document.getElementById('scanResultSub');
        const iconEl = document.getElementById('scanResultIcon');
        const btnContainer = document.getElementById('scanActionBtnContainer');

        resultBox.classList.remove('hidden', 'bg-rose-50', 'text-rose-800', 'border-rose-200', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-blue-50', 'text-blue-800', 'border-blue-200');
        resultBox.classList.add('bg-slate-50', 'text-slate-800', 'border-slate-200');
        iconEl.textContent = 'progress_activity';
        iconEl.classList.add('animate-spin');
        msgEl.textContent = 'Looking up code: ' + trackingCode + '...';
        if (subEl) subEl.textContent = 'Checking orders, printing requests, and delivery records...';
        btnContainer.innerHTML = '';

        const fd = new FormData();
        fd.append('tracking_id', trackingCode);
        const csrfToken = '<?= csrf_token() ?>';
        const csrfHash  = (window.getCsrfToken && window.getCsrfToken()) ? window.getCsrfToken() : '<?= csrf_hash() ?>';
        fd.append(csrfToken, csrfHash);

        fetch('<?= base_url('tenant/deliveries/lookup') ?>', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            iconEl.classList.remove('animate-spin');
            if (data && data.csrf_hash) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', data.csrf_hash);
            }
            if (data.success && data.delivery) {
                const isDelivered = data.action_type === 'delivered' || data.action_type === 'already_delivered' || data.delivery.status === 'delivered' || data.delivery.status === 'completed';
                resultBox.classList.remove('bg-slate-50', 'text-slate-800', 'border-slate-200');
                
                if (isDelivered) {
                    resultBox.classList.add('bg-emerald-500/10', 'text-emerald-800', 'dark:text-emerald-300', 'border-emerald-500/20');
                    iconEl.textContent = 'check_circle';
                } else {
                    resultBox.classList.add('bg-blue-500/10', 'text-blue-800', 'dark:text-blue-300', 'border-blue-500/20');
                    iconEl.textContent = 'local_shipping';
                }

                const pName = data.delivery.product_name ? ('"' + data.delivery.product_name + '" • ') : '';
                msgEl.textContent = data.message || ('Found: ' + pName + '#' + (data.delivery.tracking_id || trackingCode));
                
                if (subEl) {
                    const recipient = data.delivery.recipient_name ? ('Recipient: ' + data.delivery.recipient_name + ' • ') : '';
                    const fulfillment = data.delivery.fulfillment_method ? ('Fulfillment: ' + data.delivery.fulfillment_method.toUpperCase() + ' • ') : '';
                    subEl.textContent = `${recipient}${fulfillment}Status: ${(data.delivery.status || 'Active').toUpperCase()}`;
                }

                const targetUrl = '<?= base_url('tenant/deliveries') ?>/' + data.delivery.id;
                btnContainer.innerHTML = `
                    <div class="flex items-center gap-2 mt-1">
                        <a href="${targetUrl}" class="flex-1 py-2 bg-terra-green text-white font-bold text-xs rounded-xl flex items-center justify-center gap-1.5 shadow-sm hover:bg-emerald-700 transition-all">
                            <span class="material-symbols-outlined text-[16px]">map</span>
                            <span>Open Route &amp; Shipment →</span>
                        </a>
                        <button type="button" onclick="closeScanner(); location.reload();" class="px-3.5 py-2 border border-slate-200 dark:border-slate-700 font-bold text-xs rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-700 dark:text-slate-300">
                            Done
                        </button>
                    </div>
                `;
            } else {
                resultBox.classList.remove('bg-slate-50', 'text-slate-800', 'border-slate-200');
                resultBox.classList.add('bg-rose-500/10', 'text-rose-800', 'dark:text-rose-300', 'border-rose-500/20');
                iconEl.textContent = 'error';
                msgEl.textContent = data.error || data.message || 'No matching order or request found.';
                if (subEl) subEl.textContent = 'Please check the order number or take a clearer picture.';
            }
        })
       .catch(() => {
            iconEl.classList.remove('animate-spin');
            resultBox.classList.remove('bg-slate-50', 'text-slate-800', 'border-slate-200');
            resultBox.classList.add('bg-rose-500/10', 'text-rose-800', 'dark:text-rose-300', 'border-rose-500/20');
            iconEl.textContent = 'error';
            msgEl.textContent = 'Server connection error during lookup.';
            if (subEl) subEl.textContent = 'Please try again or enter the code manually.';
        });
    }

    // ── Realtime: soft-refresh the shipment grid when the poll bus reports changes ──
    (function() {
        let reloadTimer = null;
        let isReloading = false;
        const knownStatus = {};
        document.querySelectorAll('#shipmentsGrid .shipment-card').forEach(card => {
            knownStatus[card.dataset.tracking] = card.dataset.status;
        });

        function refreshGrid() {
            if (isReloading) return;
            isReloading = true;
            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.text() : null)
                .then(html => {
                    if (!html) return;
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const freshGrid = doc.getElementById('shipmentsGrid');
                    const liveGrid = document.getElementById('shipmentsGrid');
                    if (freshGrid && liveGrid) {
                        liveGrid.innerHTML = freshGrid.innerHTML;
                        // Re-apply active client-side filters after swapping markup.
                        if (typeof filterShipments === 'function') filterShipments();
                    }
                    const freshCount = doc.getElementById('shipmentCountBadge');
                    const liveCount = document.getElementById('shipmentCountBadge');
                    if (freshCount && liveCount) liveCount.textContent = freshCount.textContent;
                })
                .catch(() => {})
                .finally(() => { isReloading = false; });
        }

        function scheduleReload() {
            clearTimeout(reloadTimer);
            reloadTimer = setTimeout(refreshGrid, 1500);
        }

        window.addEventListener('blax:delivery-update', function(e) {
            const d = e.detail;
            if (!d || !d.tracking_id) return;
            const key = d.tracking_id;
            // New shipment, or a status change on an existing one -> soft refresh.
            if (!(key in knownStatus) || knownStatus[key] !== d.status) {
                knownStatus[key] = d.status;
                scheduleReload();
            }
        });
    })();
</script>

<?= $this->endSection() ?>