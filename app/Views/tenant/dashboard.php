<?= $this->extend('layouts/tenant') ?>

<?php
$renderDelta = function (array $d): string {
    if (empty($d['has_prior'])) {
        return '<p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1 font-medium"><span class="material-symbols-outlined text-[15px]">trending_flat</span>No prior period data</p>';
    }
    if ($d['direction'] === 'neutral') {
        return '<p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1 font-medium"><span class="material-symbols-outlined text-[15px]">trending_flat</span>No change vs prior 30 days</p>';
    }
    $up   = $d['direction'] === 'up';
    $cls  = $up ? 'text-terra-green font-bold' : 'text-rose-500 font-bold';
    $icon = $up ? 'trending_up' : 'trending_down';
    return '<p class="text-xs flex items-center gap-1 ' . $cls . '"><span class="material-symbols-outlined text-[15px]">' . $icon . '</span>' . esc((string) $d['pct']) . '% vs prior 30 days</p>';
};

$compactMoney = function (float $v): string {
    if ($v >= 1000) {
        return '₱' . number_format($v / 1000, 1) . 'k';
    }
    if ($v > 0) {
        return '₱' . number_format($v, 0);
    }
    return '';
};

$chart_trend_pct    = $chart_trend_pct ?? null;
$chart_merch_total  = (float) ($chart_merch_total ?? 0);
$chart_print_total  = (float) ($chart_print_total ?? 0);
$chart_orders_count = (int) ($chart_orders_count ?? 0);
$chart_merch_values = $chart_merch_values ?? [];
$chart_print_values = $chart_print_values ?? [];
$chart_values       = $chart_values ?? [];
$chart_labels       = $chart_labels ?? [];
$chart_total        = (float) ($chart_total ?? 0);
?>

<?= $this->section('content') ?>

<div class="flex-1 space-y-6">

    <!-- Deferred Top Banners Container (compliance warning, low stock alert, seasonal intelligence) -->
    <div id="tenant-deferred-banners" class="space-y-6"></div>

    <!-- KPI Bento Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5">

        <!-- Revenue Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 lg:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Revenue</p>
                    <span class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green border border-terra-green/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </span>
                </div>
                <h3 id="kpi-total-revenue" data-raw="<?= (float)$total_revenue ?>" class="font-display font-extrabold text-2xl lg:text-3xl text-terra-green transition-all duration-300 font-mono tracking-tight">₱<?= number_format($total_revenue, 2) ?></h3>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <?= $renderDelta($revenue_delta) ?>
            </div>
        </div>

        <!-- Total Sales Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 lg:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Orders</p>
                    <span class="w-10 h-10 rounded-2xl bg-terra-earth/10 text-terra-earth dark:text-terra-amber border border-terra-earth/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    </span>
                </div>
                <h3 id="kpi-total-sales" data-raw="<?= (int)$total_sales ?>" class="font-display font-extrabold text-2xl lg:text-3xl text-slate-900 dark:text-white transition-all duration-300 font-mono tracking-tight"><?= number_format((int) $total_sales) ?></h3>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <?= $renderDelta($sales_delta) ?>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 lg:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pending Orders</p>
                    <span class="w-10 h-10 rounded-2xl bg-terra-amber/15 text-terra-earth dark:text-terra-amber border border-terra-amber/25 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                    </span>
                </div>
                <h3 id="kpi-pending-orders" data-raw="<?= (int)$pending_orders ?>" class="font-display font-extrabold text-2xl lg:text-3xl text-slate-900 dark:text-white transition-all duration-300 font-mono tracking-tight"><?= number_format((int) $pending_orders) ?></h3>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <?= $renderDelta($pending_delta) ?>
            </div>
        </div>

        <!-- Printing Requests Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 lg:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Printing Queue</p>
                    <span class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                    </span>
                </div>
                <h3 id="kpi-printing-count" data-raw="<?= (int)$printing_count ?>" class="font-display font-extrabold text-2xl lg:text-3xl text-slate-900 dark:text-white transition-all duration-300 font-mono tracking-tight"><?= number_format((int) $printing_count) ?></h3>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <?= $renderDelta($printing_delta) ?>
            </div>
        </div>

    </div>

    <!-- Sales & Revenue Velocity Bento Card (High-DPI Interactive Visual Engine) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 lg:p-7 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">

        <!-- Header: Title, Telemetry, and Period Switcher Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="w-2.5 h-2.5 rounded-full bg-terra-green animate-pulse"></span>
                    <h3 class="font-display font-bold text-lg sm:text-xl text-slate-900 dark:text-white">Sales &amp; Revenue Velocity</h3>
                    <span id="sales-trend-badge" class="px-2.5 py-0.5 text-xs font-bold rounded-full <?= ($chart_trend_pct !== null && $chart_trend_pct >= 0) ? 'bg-emerald-500/10 text-terra-green border border-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700' ?>">
                        <?= $chart_trend_pct !== null ? (($chart_trend_pct >= 0 ? '+' : '') . $chart_trend_pct . '% vs prev') : 'Baseline Active' ?>
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Aggregated verified sales from retail merchandise orders &amp; custom print fulfillment</p>
            </div>

            <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800/80 p-1 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 self-start sm:self-center shrink-0">
                <button type="button" data-sales-range="7" class="sales-range-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-terra-green text-white shadow-xs transition-all cursor-pointer">Last 7 Days</button>
                <button type="button" data-sales-range="30" class="sales-range-btn px-3.5 py-1.5 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all cursor-pointer">Last 30 Days</button>
                <button type="button" data-sales-range="year" class="sales-range-btn px-3.5 py-1.5 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all cursor-pointer">This Year</button>
            </div>
        </div>

        <!-- Executive Metrics Strip (Merchandise vs Custom Print Breakdown) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Total Revenue</span>
                <span id="sales-metric-total" class="text-lg sm:text-xl font-black text-terra-green font-mono mt-0.5 block">₱<?= number_format($chart_total, 2) ?></span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Retail Merchandise</span>
                <span id="sales-metric-merch" class="text-base sm:text-lg font-bold text-slate-900 dark:text-white font-mono mt-0.5 block">₱<?= number_format($chart_merch_total ?? 0, 2) ?></span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Custom Printing</span>
                <span id="sales-metric-print" class="text-base sm:text-lg font-bold text-terra-earth dark:text-amber-500 font-mono mt-0.5 block">₱<?= number_format($chart_print_total ?? 0, 2) ?></span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/60">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Orders Velocity</span>
                <span id="sales-metric-orders" class="text-base sm:text-lg font-bold text-slate-900 dark:text-white font-mono mt-0.5 block"><?= number_format($chart_orders_count ?? 0) ?> Verified</span>
            </div>
        </div>

        <!-- Chart Visualization Container with Zero-State & Precision Tooltip -->
        <div id="tenantSalesChartWrap" class="relative rounded-2xl bg-slate-50/70 dark:bg-slate-800/30 border border-slate-200/80 dark:border-slate-700/60 p-4 sm:p-5 overflow-hidden">
            <!-- Error Banner -->
            <div id="sales-error" class="hidden mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span id="sales-error-msg">Failed to refresh sales data.</span>
                </div>
                <button type="button" id="sales-retry-btn" class="font-bold underline ml-2 hover:text-rose-950 dark:hover:text-white cursor-pointer">Retry</button>
            </div>

            <!-- Zero-State Overlay (Shown when revenue is ₱0.00) -->
            <div id="sales-zero-state" class="<?= $chart_total <= 0 ? '' : 'hidden' ?> absolute inset-0 z-10 flex flex-col items-center justify-center p-6 bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-xs text-center transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-terra-green/10 border border-terra-green/20 text-terra-green flex items-center justify-center mb-2.5 shadow-2xs">
                    <span class="material-symbols-outlined text-[24px]">query_stats</span>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white font-display">No Completed Sales Recorded in This Period</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm">When customers purchase store products or pick up custom printing jobs, daily velocity trends will illuminate here automatically.</p>
                <span class="text-[11px] font-mono text-terra-green mt-2 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">● Real-time Sync Active</span>
            </div>

            <!-- High-DPI Interactive Canvas Chart -->
            <div class="relative h-56 sm:h-64 w-full">
                <canvas id="tenantSalesCanvas" class="w-full h-full block cursor-crosshair"></canvas>
                <div id="tenantSalesTooltip" class="hidden absolute z-30 pointer-events-none whitespace-nowrap text-xs rounded-2xl shadow-2xl p-3 bg-slate-900/95 text-slate-100 border border-slate-700/60 backdrop-blur-md transition-all duration-75 min-w-[150px]">
                    <div class="text-[11px] font-bold text-slate-400 pb-1.5 border-b border-slate-700/60" id="tt-sales-date">Oct 21</div>
                    <div class="flex items-center justify-between gap-3 pt-1.5">
                        <span class="text-xs text-slate-300">Total:</span>
                        <span class="text-xs font-mono font-black text-emerald-400" id="tt-sales-total">₱0.00</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[11px] text-slate-400 mt-0.5">
                        <span>Merchandise:</span>
                        <span class="font-mono text-slate-200" id="tt-sales-merch">₱0.00</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-[11px] text-slate-400 mt-0.5">
                        <span>Printing:</span>
                        <span class="font-mono text-amber-400" id="tt-sales-print">₱0.00</span>
                    </div>
                </div>
            </div>

            <!-- Chart Footer Legend & Telemetry Bar -->
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700/60 font-mono">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-terra-green"></span> Total Gross</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-terra-earth dark:bg-amber-500"></span> Custom Printing</span>
                </div>
                <div class="flex items-center gap-1 font-sans">
                    <span class="w-1.5 h-1.5 rounded-full bg-terra-green animate-ping"></span>
                    <span class="text-slate-600 dark:text-slate-300 text-[11px] font-medium">Instant Telemetry</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Low Stock Alert Bento Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 lg:p-7 border border-slate-200/80 dark:border-slate-800 shadow-sm">

        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">Low Stock Warning</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Inventory items requiring prompt restock replenishment</p>
            </div>
            <a href="<?= base_url('tenant/inventory') ?>" class="text-xs font-bold text-terra-green hover:underline flex items-center gap-1">
                <span>Manage Inventory</span>
                <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
            </a>
        </div>

        <?php if (!empty($low_stock_items)): ?>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[560px]">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Product</th>
                            <th class="py-3 px-4">SKU</th>
                            <th class="py-3 px-4">Current Stock</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <?php foreach (array_slice($low_stock_items, 0, 8) as $p): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <?php if (!empty($p['image_url'])): ?>
                                            <img src="<?= esc(product_image_url($p['image_url'], 'thumbnail')) ?>" alt="<?= esc($p['name']) ?>" class="w-10 h-10 rounded-xl object-cover shrink-0 border border-slate-200 dark:border-slate-700" loading="lazy" decoding="async">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-400">
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            </div>
                                        <?php endif; ?>
                                        <span class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate"><?= esc($p['name']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"><?= esc($p['sku']) ?></span>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-xs text-slate-900 dark:text-white"><?= (int) $p['stock_quantity'] ?></td>
                                <td class="py-3.5 px-4"><?= ((int) $p['stock_quantity'] === 0) ? status_badge('out_of_stock') : status_badge('low_stock') ?></td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="<?= base_url('tenant/inventory') ?>" class="text-xs font-bold text-terra-green hover:text-terra-green-dark hover:underline whitespace-nowrap">Restock</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-10 text-slate-400 dark:text-slate-500 flex flex-col items-center justify-center gap-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Healthy Inventory Levels</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">All catalog products are comfortably stocked above buffer thresholds.</p>
            </div>
        <?php endif; ?>

    </div>

    <!-- Recent Orders + Recent Printing Requests -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <!-- Recent Orders -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 lg:p-7 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">Recent Orders</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Latest customer merchandise purchases</p>
                    </div>
                    <a href="<?= base_url('tenant/orders') ?>" class="text-xs font-bold text-terra-green hover:underline">View All</a>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-3">Order ID</th>
                                <th class="py-3 px-3">Customer</th>
                                <th class="py-3 px-3">Total</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Date</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-recent-orders-tbody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <?php if (!empty($recent_orders)): ?>
                                <?php foreach ($recent_orders as $o): ?>
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="py-3.5 px-3 font-mono font-bold text-xs text-slate-900 dark:text-white whitespace-nowrap">#<?= esc($o['order_number'] ?? ('ORD-' . $o['id'])) ?></td>
                                        <td class="py-3.5 px-3 text-xs font-medium text-slate-700 dark:text-slate-300"><?= esc(trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? ''))) ?: 'Customer' ?></td>
                                        <td class="py-3.5 px-3 font-mono font-bold text-xs text-terra-green whitespace-nowrap">₱<?= number_format((float) $o['total_amount'], 2) ?></td>
                                        <td class="py-3.5 px-3"><?= status_badge($o['status']) ?></td>
                                        <td class="py-3.5 px-3 text-xs font-mono text-slate-400 whitespace-nowrap"><?= date('M d, Y', strtotime($o['placed_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="dashboard-no-orders-row"><td colspan="5" class="py-8 text-center text-xs text-slate-400">No orders recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Printing Requests -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 lg:p-7 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">Recent Printing Requests</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Custom apparel & print file submissions</p>
                    </div>
                    <a href="<?= base_url('tenant/printing') ?>" class="text-xs font-bold text-terra-green hover:underline">View All</a>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-3">Request No.</th>
                                <th class="py-3 px-3">File</th>
                                <th class="py-3 px-3">Customer</th>
                                <th class="py-3 px-3">Total</th>
                                <th class="py-3 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-recent-printing-tbody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <?php if (!empty($recent_requests)): ?>
                                <?php foreach ($recent_requests as $r): ?>
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="py-3.5 px-3 font-mono font-bold text-xs text-slate-900 dark:text-white whitespace-nowrap"><?= esc($r['request_number'] ?? ('PR-' . $r['id'])) ?></td>
                                        <td class="py-3.5 px-3 text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]"><?= esc($r['file_name']) ?></td>
                                        <td class="py-3.5 px-3 text-xs font-medium text-slate-700 dark:text-slate-300"><?= esc(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?: 'Customer' ?></td>
                                        <td class="py-3.5 px-3 font-mono font-bold text-xs text-terra-earth dark:text-terra-amber whitespace-nowrap">₱<?= number_format((float) $r['total_price'], 2) ?></td>
                                        <td class="py-3.5 px-3"><?= status_badge($r['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="dashboard-no-printing-row"><td colspan="5" class="py-8 text-center text-xs text-slate-400">No printing requests yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
(function () {
    const salesEndpoint = '<?= site_url('tenant/dashboard/sales') ?>';
    const canvas = document.getElementById('tenantSalesCanvas');
    const tooltip = document.getElementById('tenantSalesTooltip');
    const zeroStateEl = document.getElementById('sales-zero-state');
    const metricTotal = document.getElementById('sales-metric-total');
    const metricMerch = document.getElementById('sales-metric-merch');
    const metricPrint = document.getElementById('sales-metric-print');
    const metricOrders = document.getElementById('sales-metric-orders');
    const trendBadge = document.getElementById('sales-trend-badge');
    const salesError = document.getElementById('sales-error');
    const salesErrorMsg = document.getElementById('sales-error-msg');
    const salesRetryBtn = document.getElementById('sales-retry-btn');
    let lastActiveBtn = document.querySelector('.sales-range-btn.bg-terra-green') || document.querySelector('.sales-range-btn');

    // In-memory instant cache map initialized with server payload for '7'
    const salesRangeCache = {
        '7': {
            labels: <?= json_encode($chart_labels ?? []) ?>,
            values: <?= json_encode(array_map('floatval', $chart_values ?? [])) ?>,
            merchandise_values: <?= json_encode(array_map('floatval', $chart_merch_values ?? [])) ?>,
            printing_values: <?= json_encode(array_map('floatval', $chart_print_values ?? [])) ?>,
            total: <?= (float) $chart_total ?>,
            merchandise_total: <?= (float) ($chart_merch_total ?? 0) ?>,
            printing_total: <?= (float) ($chart_print_total ?? 0) ?>,
            total_orders_count: <?= (int) ($chart_orders_count ?? 0) ?>,
            trend_percentage: <?= json_encode($chart_trend_pct ?? null) ?>,
            range: '7'
        }
    };

    let activeData = salesRangeCache['7'];
    let hoveredIdx = -1;

    function formatPeso(num) {
        return '₱' + (Number(num) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderMetrics(data) {
        if (!data) return;
        const total = Number(data.total) || 0;
        const merch = Number(data.merchandise_total) || 0;
        const print = Number(data.printing_total) || 0;
        const orders = Number(data.total_orders_count) || 0;

        if (metricTotal) metricTotal.textContent = formatPeso(total);
        if (metricMerch) metricMerch.textContent = formatPeso(merch);
        if (metricPrint) metricPrint.textContent = formatPeso(print);
        if (metricOrders) metricOrders.textContent = (orders > 0 ? (orders + ' Verified') : '0 Orders');

        if (trendBadge) {
            if (data.trend_percentage !== null && data.trend_percentage !== undefined) {
                const t = Number(data.trend_percentage);
                trendBadge.textContent = (t >= 0 ? '+' : '') + t + '% vs prev';
                trendBadge.className = 'px-2.5 py-0.5 text-xs font-bold rounded-full ' + (t >= 0 ? 'bg-emerald-500/10 text-terra-green border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/20');
            } else {
                trendBadge.textContent = 'Baseline Active';
                trendBadge.className = 'px-2.5 py-0.5 text-xs font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700';
            }
        }

        if (zeroStateEl) {
            if (total <= 0) {
                zeroStateEl.classList.remove('hidden');
            } else {
                zeroStateEl.classList.add('hidden');
            }
        }
    }

    function drawChart(activeIdx = -1) {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) return;

        canvas.width = Math.round(rect.width * dpr);
        canvas.height = Math.round(rect.height * dpr);
        ctx.resetTransform();
        ctx.scale(dpr, dpr);

        const w = rect.width;
        const h = rect.height;
        const pad = { t: 20, r: 18, b: 28, l: 48 };

        ctx.clearRect(0, 0, w, h);

        const data = activeData;
        const labels = data.labels || [];
        const totals = data.values || [];
        const prints = data.printing_values || [];
        const isZero = (Number(data.total) || 0) <= 0;
        const count = labels.length;
        const maxVal = Math.max(...totals, 100);
        const stepX = (w - pad.l - pad.r) / Math.max(count - 1, 1);

        // Grid lines
        const isDark = document.documentElement.classList.contains('dark');
        ctx.strokeStyle = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.20)';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 3; i++) {
            const y = pad.t + (h - pad.t - pad.b) * (i / 3);
            ctx.beginPath();
            ctx.moveTo(pad.l, y);
            ctx.lineTo(w - pad.r, y);
            ctx.stroke();

            const yVal = Math.round(maxVal * (1 - i / 3));
            ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
            ctx.font = '10px "JetBrains Mono", monospace';
            ctx.textAlign = 'right';
            const textVal = isZero ? '₱0' : ('₱' + (yVal >= 1000 ? (yVal / 1000).toFixed(0) + 'k' : yVal));
            ctx.fillText(textVal, pad.l - 8, y + 3.5);
        }

        // Helper to cleanly render spaced-out X-axis dates without overlap
        function renderXAxisDates() {
            ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
            ctx.font = '11px "Plus Jakarta Sans", sans-serif';
            const availableWidth = w - pad.l - pad.r;
            // Minimum 75px per date label to prevent messy cramped text
            const maxLabels = Math.max(3, Math.min(count, Math.floor(availableWidth / 75)));
            const stepInterval = Math.max(1, Math.ceil(count / maxLabels));

            labels.forEach((lbl, i) => {
                const isFirst = (i === 0);
                const isLast = (i === count - 1);
                const isSampled = (i % stepInterval === 0);

                // Prevent collision between the second-to-last sampled label and the final date
                if (isLast || (isSampled && (count - 1 - i) >= Math.floor(stepInterval * 0.6)) || isFirst) {
                    const x = pad.l + i * stepX;
                    if (isFirst) {
                        ctx.textAlign = 'left';
                        ctx.fillText(lbl, pad.l, h - 8);
                    } else if (isLast) {
                        ctx.textAlign = 'right';
                        ctx.fillText(lbl, w - pad.r, h - 8);
                    } else {
                        ctx.textAlign = 'center';
                        ctx.fillText(lbl, x, h - 8);
                    }
                }
            });
        }

        if (isZero) {
            // Clean baseline dotted curve for zero state
            ctx.strokeStyle = 'rgba(74, 148, 62, 0.45)';
            ctx.lineWidth = 2;
            ctx.setLineDash([4, 4]);
            ctx.beginPath();
            ctx.moveTo(pad.l, h - pad.b);
            ctx.lineTo(w - pad.r, h - pad.b);
            ctx.stroke();
            ctx.setLineDash([]);

            // Draw clean spaced X-axis labels
            renderXAxisDates();
            return;
        }

        // 1. Area Fill Gradient (Terra Green)
        ctx.beginPath();
        totals.forEach((v, i) => {
            const x = pad.l + i * stepX;
            const y = pad.t + (h - pad.t - pad.b) * (1 - v / maxVal);
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        const lastX = pad.l + (count - 1) * stepX;
        ctx.lineTo(lastX, h - pad.b);
        ctx.lineTo(pad.l, h - pad.b);
        ctx.closePath();

        const grad = ctx.createLinearGradient(0, pad.t, 0, h - pad.b);
        grad.addColorStop(0, 'rgba(74, 148, 62, 0.28)');
        grad.addColorStop(1, 'rgba(74, 148, 62, 0.00)');
        ctx.fillStyle = grad;
        ctx.fill();

        // 2. Line (Terra Green)
        ctx.beginPath();
        ctx.strokeStyle = '#4A943E';
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        totals.forEach((v, i) => {
            const x = pad.l + i * stepX;
            const y = pad.t + (h - pad.t - pad.b) * (1 - v / maxVal);
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.stroke();

        // 3. Custom Printing Line (Terra Amber Accent)
        if (prints.length === count) {
            ctx.beginPath();
            ctx.strokeStyle = '#C87A1E';
            ctx.lineWidth = 1.5;
            ctx.lineJoin = 'round';
            prints.forEach((v, i) => {
                const x = pad.l + i * stepX;
                const y = pad.t + (h - pad.t - pad.b) * (1 - v / maxVal);
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            });
            ctx.stroke();
        }

        // 4. Vertical dashed cursor on hover
        if (activeIdx >= 0 && activeIdx < count) {
            const hx = pad.l + activeIdx * stepX;
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(74, 148, 62, 0.45)';
            ctx.lineWidth = 1.5;
            ctx.setLineDash([3, 3]);
            ctx.moveTo(hx, pad.t);
            ctx.lineTo(hx, h - pad.b);
            ctx.stroke();
            ctx.setLineDash([]);
        }

        // 5. Data Points (Smooth curve: show dots on hover, for small ranges <= 12, or on peak)
        totals.forEach((v, i) => {
            const x = pad.l + i * stepX;
            const y = pad.t + (h - pad.t - pad.b) * (1 - v / maxVal);
            const isHov = (i === activeIdx);

            const showDot = isHov || (count <= 12) || (v === maxVal && maxVal > 0);
            if (!showDot) return;

            if (isHov) {
                ctx.beginPath();
                ctx.arc(x, y, 7, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(74, 148, 62, 0.25)';
                ctx.fill();
            }

            ctx.beginPath();
            ctx.arc(x, y, isHov ? 4.5 : 3, 0, Math.PI * 2);
            ctx.fillStyle = '#4A943E';
            ctx.fill();
            ctx.beginPath();
            ctx.arc(x, y, isHov ? 2.2 : 1.5, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
        });

        // 6. X-Axis Spaced Labels
        renderXAxisDates();
    }

    async function setSalesRange(rangeVal, btn) {
        if (btn) {
            document.querySelectorAll('.sales-range-btn').forEach(b => {
                b.classList.remove('bg-terra-green', 'text-white', 'shadow-xs', 'font-bold');
                b.classList.add('text-slate-600', 'dark:text-slate-400', 'font-medium');
            });
            btn.classList.add('bg-terra-green', 'text-white', 'shadow-xs', 'font-bold');
            btn.classList.remove('text-slate-600', 'dark:text-slate-400', 'font-medium');
            lastActiveBtn = btn;
        }

        // Check in-memory instant cache (0ms instant transition!)
        if (salesRangeCache[rangeVal]) {
            activeData = salesRangeCache[rangeVal];
            renderMetrics(activeData);
            drawChart();
            return;
        }

        // Otherwise fetch asynchronously
        if (canvas) canvas.classList.add('opacity-50');
        try {
            const sep = salesEndpoint.indexOf('?') >= 0 ? '&' : '?';
            const res = await fetch(salesEndpoint + sep + 'range=' + encodeURIComponent(rangeVal) + '&_t=' + Date.now(), {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (data && data.success) {
                salesRangeCache[rangeVal] = data;
                activeData = data;
                renderMetrics(data);
                drawChart();
            }
        } catch (err) {
            console.warn('Sales range fetch failed:', err);
            if (salesError) {
                if (salesErrorMsg) salesErrorMsg.textContent = 'Unable to refresh sales chart (' + (err.message || 'network error') + ').';
                salesError.classList.remove('hidden');
            }
        } finally {
            if (canvas) canvas.classList.remove('opacity-50');
        }
    }

    // Attach range button events
    document.querySelectorAll('.sales-range-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const range = btn.dataset.salesRange || '7';
            setSalesRange(range, btn);
        });
    });

    if (salesRetryBtn) {
        salesRetryBtn.addEventListener('click', function () {
            if (lastActiveBtn) {
                const range = lastActiveBtn.dataset.salesRange || '7';
                delete salesRangeCache[range];
                setSalesRange(range, lastActiveBtn);
            }
        });
    }

    // Interactive Crosshair & Tooltip
    if (canvas && tooltip) {
        canvas.addEventListener('mousemove', function (e) {
            if ((Number(activeData.total) || 0) <= 0) {
                tooltip.classList.add('hidden');
                return;
            }

            const rect = canvas.getBoundingClientRect();
            const mx = e.clientX - rect.left;
            const pad = { t: 20, r: 18, b: 28, l: 48 };
            const count = (activeData.labels || []).length;
            if (count <= 0) return;

            const stepX = (rect.width - pad.l - pad.r) / Math.max(count - 1, 1);
            let closest = -1;
            let minDist = 99999;

            for (let i = 0; i < count; i++) {
                const x = pad.l + i * stepX;
                const d = Math.abs(mx - x);
                if (d < minDist) {
                    minDist = d;
                    closest = i;
                }
            }

            if (closest >= 0 && minDist < 36) {
                hoveredIdx = closest;
                drawChart(closest);

                const dateStr = activeData.labels[closest] || '';
                const totalVal = activeData.values[closest] || 0;
                const merchVal = (activeData.merchandise_values && activeData.merchandise_values[closest]) ? activeData.merchandise_values[closest] : 0;
                const printVal = (activeData.printing_values && activeData.printing_values[closest]) ? activeData.printing_values[closest] : 0;

                const ttDate = document.getElementById('tt-sales-date');
                const ttTotal = document.getElementById('tt-sales-total');
                const ttMerch = document.getElementById('tt-sales-merch');
                const ttPrint = document.getElementById('tt-sales-print');

                if (ttDate) ttDate.textContent = dateStr;
                if (ttTotal) ttTotal.textContent = formatPeso(totalVal);
                if (ttMerch) ttMerch.textContent = formatPeso(merchVal);
                if (ttPrint) ttPrint.textContent = formatPeso(printVal);

                const px = pad.l + closest * stepX;
                const tipLeft = Math.min(Math.max(px - 75, 10), rect.width - 165);
                tooltip.style.left = tipLeft + 'px';
                tooltip.style.top = '12px';
                tooltip.classList.remove('hidden');
            } else {
                hoveredIdx = -1;
                drawChart(-1);
                tooltip.classList.add('hidden');
            }
        });

        canvas.addEventListener('mouseleave', function () {
            hoveredIdx = -1;
            drawChart(-1);
            tooltip.classList.add('hidden');
        });
    }

    // Reactive to theme switch and window resize
    window.addEventListener('resize', () => drawChart(hoveredIdx));
    window.addEventListener('blax:theme-changed', () => drawChart(hoveredIdx));

    // Initial render
    renderMetrics(activeData);
    requestAnimationFrame(() => drawChart());

    // Pre-cache 30d and year in background for 0ms instant switches
    setTimeout(() => {
        ['30', 'year'].forEach(async r => {
            try {
                const sep = salesEndpoint.indexOf('?') >= 0 ? '&' : '?';
                const res = await fetch(salesEndpoint + sep + 'range=' + r, {
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success) salesRangeCache[r] = data;
                }
            } catch (e) {}
        });
    }, 400);

    // Immediate Cache Invalidation & Refetch when sale is completed in POS or another window
    window.addEventListener('storage', function (e) {
        if (e.key === 'blax_last_sale' && e.newValue) {
            if (lastActiveBtn) {
                const range = lastActiveBtn.dataset.salesRange || '7';
                delete salesRangeCache[range];
                setSalesRange(range, lastActiveBtn);
            }
        }
    });

    window.addEventListener('blax:sale_completed', function () {
        if (lastActiveBtn) {
            const range = lastActiveBtn.dataset.salesRange || '7';
            delete salesRangeCache[range];
            setSalesRange(range, lastActiveBtn);
        }
    });

    // Real-time Event Listeners for Instant Dashboard Metrics & Table Updating
    window.addEventListener('blax:new-order', function (e) {
        const order = e.detail;
        if (!order) return;

        // 1. Live KPI Increment: Total Sales
        const salesEl = document.getElementById('kpi-total-sales');
        if (salesEl) {
            let currentSales = parseInt(salesEl.getAttribute('data-raw') || '0', 10) + 1;
            salesEl.setAttribute('data-raw', currentSales);
            salesEl.textContent = new Intl.NumberFormat().format(currentSales);
            salesEl.classList.add('scale-110', 'text-terra-green');
            setTimeout(() => salesEl.classList.remove('scale-110', 'text-terra-green'), 600);
        }

        // 2. Live KPI Increment: Total Revenue
        const revEl = document.getElementById('kpi-total-revenue');
        if (revEl && order.total_amount) {
            let currentRev = parseFloat(revEl.getAttribute('data-raw') || '0') + parseFloat(order.total_amount);
            revEl.setAttribute('data-raw', currentRev);
            revEl.textContent = '₱' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(currentRev);
            revEl.classList.add('scale-110', 'text-terra-green');
            setTimeout(() => revEl.classList.remove('scale-110', 'text-terra-green'), 600);
        }

        // 3. Live KPI Increment: Pending Orders
        const pendingEl = document.getElementById('kpi-pending-orders');
        if (pendingEl) {
            let currentPending = parseInt(pendingEl.getAttribute('data-raw') || '0', 10) + 1;
            pendingEl.setAttribute('data-raw', currentPending);
            pendingEl.textContent = new Intl.NumberFormat().format(currentPending);
            pendingEl.classList.add('scale-110', 'text-terra-amber');
            setTimeout(() => pendingEl.classList.remove('scale-110', 'text-terra-amber'), 600);
        }

        // 4. Prepend to Recent Orders Table
        const tbody = document.getElementById('dashboard-recent-orders-tbody');
        if (tbody) {
            const noOrdersRow = document.getElementById('dashboard-no-orders-row');
            if (noOrdersRow) noOrdersRow.remove();

            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100 dark:border-slate-800 bg-terra-green/5 dark:bg-terra-green/10 transition-all duration-500';
            tr.innerHTML = `
                <td class="py-3.5 px-3 font-mono font-bold text-xs text-terra-green whitespace-nowrap">#${order.order_number} <span class="text-[9px] font-bold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded ml-1 animate-pulse">⚡ Just Now</span></td>
                <td class="py-3.5 px-3 text-xs font-medium text-slate-800 dark:text-slate-200">${order.customer_name}</td>
                <td class="py-3.5 px-3 font-mono font-bold text-xs text-terra-green whitespace-nowrap">₱${order.total_amount_fmt}</td>
                <td class="py-3.5 px-3"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-terra-amber/15 text-terra-earth border border-terra-amber/30">Pending</span></td>
                <td class="py-3.5 px-3 text-xs font-mono text-slate-400 whitespace-nowrap">Just now</td>
            `;
            tbody.insertBefore(tr, tbody.firstChild);
        }

        // 5. Trigger Sales Chart Refresh
        if (lastActiveBtn) {
            loadSalesRange(lastActiveBtn);
        }
    });

    window.addEventListener('blax:new-printing', function (e) {
        const pr = e.detail;
        if (!pr) return;

        // 1. Live KPI Increment: Printing Count
        const prCountEl = document.getElementById('kpi-printing-count');
        if (prCountEl) {
            let currentPr = parseInt(prCountEl.getAttribute('data-raw') || '0', 10) + 1;
            prCountEl.setAttribute('data-raw', currentPr);
            prCountEl.textContent = new Intl.NumberFormat().format(currentPr);
            prCountEl.classList.add('scale-110', 'text-purple-600');
            setTimeout(() => prCountEl.classList.remove('scale-110', 'text-purple-600'), 600);
        }

        // 2. Prepend to Recent Printing Table
        const tbody = document.getElementById('dashboard-recent-printing-tbody');
        if (tbody) {
            const noPrRow = document.getElementById('dashboard-no-printing-row');
            if (noPrRow) noPrRow.remove();

            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100 dark:border-slate-800 bg-purple-500/5 dark:bg-purple-500/10 transition-all duration-500';
            tr.innerHTML = `
                <td class="py-3.5 px-3 font-mono font-bold text-xs text-purple-600 dark:text-purple-400 whitespace-nowrap">${pr.request_number} <span class="text-[9px] font-bold bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded ml-1 animate-pulse">⚡ New</span></td>
                <td class="py-3.5 px-3 text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]">${pr.service_name}</td>
                <td class="py-3.5 px-3 text-xs font-medium text-slate-800 dark:text-slate-200">${pr.customer_name}</td>
                <td class="py-3.5 px-3 font-mono font-bold text-xs text-terra-earth dark:text-terra-amber whitespace-nowrap">₱${pr.total_amount_fmt}</td>
                <td class="py-3.5 px-3"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-300">New</span></td>
            `;
            tbody.insertBefore(tr, tbody.firstChild);
        }
    });

    // Defer loading of top dashboard banners & AI seasonal spotlight
    fetch('<?= base_url('tenant/dashboard/widgets') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const container = document.getElementById('tenant-deferred-banners');
        if (container && html.trim().length > 0) {
            container.innerHTML = html;
        }
    })
    .catch(err => console.debug('Deferred banners fetch skipped:', err));
})();
</script>

<?= $this->endSection() ?>
