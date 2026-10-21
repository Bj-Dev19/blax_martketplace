<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Top Command & Status Bento Banner -->
    <div class="rounded-3xl bg-surface-container-lowest p-6 border border-outline-variant/30 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-terra-green/10 text-terra-green border border-terra-green/20">
                    Platform Telemetry
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Data
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-on-surface font-display tracking-tight">
                Analytics &amp; Revenue Intelligence
            </h1>
            <p class="text-xs text-on-surface-variant mt-1 max-w-xl">
                Executive revenue metrics, merchant volume throughput, payment rails distribution, and regional delivery telemetry.
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface-container-low text-on-surface-variant text-xs font-mono border border-outline-variant/30">
                <span class="material-symbols-outlined text-[16px] text-terra-green">location_on</span>
                <span>Polomolok: 6.10°–6.32° N</span>
            </span>
            <a href="<?= base_url('admin/tracking') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-terra-green text-white hover:bg-emerald-600 transition-all shadow-xs active:scale-95">
                <span class="material-symbols-outlined text-[16px]">two_wheeler</span>
                <span>Live Fleet Map</span>
            </a>
            <button type="button" id="btnRefreshAnalytics" onclick="refreshAnalyticsData()" class="p-2 rounded-xl text-on-surface-variant hover:bg-surface-container border border-outline-variant/30 transition-all active:scale-95" title="Refresh data">
                <span class="material-symbols-outlined text-[18px]" id="refreshIcon">refresh</span>
            </button>
        </div>
    </div>

    <!-- 4 High-Impact KPI Scorecards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Admin Net Revenue -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs hover:border-terra-green/30 transition-all relative overflow-hidden group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70">Admin Revenue</span>
                    <span class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </span>
                </div>
                <h3 class="text-3xl font-black text-terra-green font-display font-mono tracking-tight mt-1">₱<?= number_format((float) ($admin_revenue ?? 0), 2) ?></h3>
                <div class="mt-2 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>3.0% fee on payouts</span>
                    <span class="font-bold font-mono text-terra-green bg-terra-green/10 px-2 py-0.5 rounded-lg border border-terra-green/20"><?= number_format((int) ($payout_count ?? 0)) ?> payouts</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-outline-variant/20 flex items-center justify-between text-[11px] text-on-surface-variant">
                <span>Avg. Fee / Payout</span>
                <span class="font-bold text-on-surface font-mono">₱<?= number_format((float) ($avg_fee ?? 0), 2) ?></span>
            </div>
        </div>

        <!-- Marketplace GMV -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs hover:border-emerald-500/30 transition-all relative overflow-hidden group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70">Marketplace GMV</span>
                    <span class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">currency_exchange</span>
                    </span>
                </div>
                <h3 class="text-3xl font-black text-on-surface font-display font-mono tracking-tight mt-1">₱<?= number_format((float) ($platform_gmv ?? 0), 2) ?></h3>
                <div class="mt-2 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>Transacted volume</span>
                    <span class="font-bold text-emerald-600 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">All Channels</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-outline-variant/20 flex items-center justify-between text-[11px] text-on-surface-variant">
                <span>Avg. Order Value</span>
                <span class="font-bold text-on-surface font-mono">₱<?= ($total_orders ?? 0) > 0 ? number_format(($platform_gmv ?? 0) / $total_orders, 2) : '0.00' ?></span>
            </div>
        </div>

        <!-- Order Throughput & Volume -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs hover:border-sky-500/30 transition-all relative overflow-hidden group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70">Total Orders</span>
                    <span class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                    </span>
                </div>
                <h3 class="text-3xl font-black text-on-surface font-display font-mono tracking-tight mt-1"><?= number_format((int) ($total_orders ?? 0)) ?></h3>
                <div class="mt-2 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>All merchant orders</span>
                    <span class="font-bold text-sky-600 bg-sky-500/10 px-2 py-0.5 rounded-lg border border-sky-500/20 font-mono"><?= number_format((int) ($active_deliveries_count ?? 0)) ?> Active Now</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-outline-variant/20 flex items-center justify-between text-[11px] text-on-surface-variant">
                <span>Fulfillment Split</span>
                <span class="font-bold text-on-surface font-mono">🛵 <?= esc($fulfillment_dist['delivery_pct'] ?? 0) ?>% · 🏪 <?= esc($fulfillment_dist['pickup_pct'] ?? 0) ?>%</span>
            </div>
        </div>

        <!-- Merchant Network & Coverage -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs hover:border-terra-earth/30 transition-all relative overflow-hidden group flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70">Merchant Network</span>
                    <span class="w-10 h-10 rounded-2xl bg-terra-earth/10 text-terra-earth flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                    </span>
                </div>
                <h3 class="text-3xl font-black text-on-surface font-display font-mono tracking-tight mt-1"><?= number_format((int) ($active_shops ?? 0)) ?></h3>
                <div class="mt-2 flex items-center justify-between text-xs text-on-surface-variant">
                    <span>Active stores</span>
                    <span class="font-bold text-terra-earth bg-terra-earth/10 px-2 py-0.5 rounded-lg border border-terra-earth/20 font-mono"><?= number_format((int) ($total_shops ?? 0)) ?> registered</span>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-outline-variant/20 flex items-center justify-between text-[11px] text-on-surface-variant">
                <span>Ecosystem Composition</span>
                <span class="font-bold text-on-surface font-mono">🖨️ <?= (int) ($printing_shops ?? 0) ?> Hubs · 👥 <?= number_format((int) ($total_customers ?? 0)) ?> Users</span>
            </div>
        </div>
    </section>

    <!-- Primary Charts & Distribution Section -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Revenue Velocity Chart (2 Columns) -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-on-surface font-display flex items-center gap-2">
                            <span class="material-symbols-outlined text-terra-green text-[20px]">trending_up</span>
                            <span>Admin Revenue Growth &amp; Fee Velocity</span>
                        </h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Commission and platform fees earned from verified merchant disbursements</p>
                    </div>
                    <!-- Range Switcher -->
                    <div class="inline-flex items-center gap-1 bg-surface-container-low p-1.5 rounded-2xl border border-outline-variant/20 shrink-0 self-start sm:self-auto">
                        <button type="button" data-range="7" class="rangeBtn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($range ?? '30') === '7' ? 'bg-terra-green text-white shadow-xs' : 'text-on-surface-variant hover:text-on-surface' ?>">7 Days</button>
                        <button type="button" data-range="30" class="rangeBtn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($range ?? '30') === '30' ? 'bg-terra-green text-white shadow-xs' : 'text-on-surface-variant hover:text-on-surface' ?>">30 Days</button>
                        <button type="button" data-range="year" class="rangeBtn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($range ?? '30') === 'year' ? 'bg-terra-green text-white shadow-xs' : 'text-on-surface-variant hover:text-on-surface' ?>">This Year</button>
                    </div>
                </div>

                <!-- Micro KPI summary strip for the selected period -->
                <div class="grid grid-cols-3 gap-3 p-3.5 bg-surface-container-low/60 rounded-2xl border border-outline-variant/20 mb-4 text-center">
                    <div>
                        <p class="text-[10px] sm:text-[11px] text-on-surface-variant/70 uppercase font-bold tracking-wider">Period Total</p>
                        <p id="metric-period-total" class="text-sm sm:text-base font-black text-terra-green font-display font-mono mt-0.5">₱<?= number_format((float) ($chart_sum ?? 0), 2) ?></p>
                    </div>
                    <div class="border-x border-outline-variant/20">
                        <p class="text-[10px] sm:text-[11px] text-on-surface-variant/70 uppercase font-bold tracking-wider">Daily Average</p>
                        <p id="metric-period-avg" class="text-sm sm:text-base font-black text-on-surface font-display font-mono mt-0.5">₱<?= number_format((float) ($chart_avg ?? 0), 2) ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] sm:text-[11px] text-on-surface-variant/70 uppercase font-bold tracking-wider">Peak Day</p>
                        <p id="metric-period-peak" class="text-sm sm:text-base font-black text-emerald-600 font-display font-mono mt-0.5">₱<?= number_format((float) ($chart_peak ?? 0), 2) ?></p>
                    </div>
                </div>

                <!-- High-DPI Interactive Canvas Chart -->
                <div id="analyticsChartWrap" class="relative h-72 sm:h-80 w-full bg-surface-container-lowest rounded-2xl border border-outline-variant/20 p-2 sm:p-4">
                    <canvas id="analyticsChart" class="w-full h-full block cursor-crosshair"></canvas>
                    <div id="analyticsTooltip" class="hidden absolute z-30 pointer-events-none whitespace-nowrap text-xs font-medium px-3.5 py-2.5 rounded-2xl shadow-xl bg-slate-900/95 text-slate-100 border border-slate-700/60 backdrop-blur-md transition-[opacity,transform] duration-75"></div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 mt-2 border-t border-outline-variant/15 text-[11px] text-on-surface-variant">
                <span>Hover data points for exact timestamps and settlement figures.</span>
                <span class="inline-flex items-center gap-1 font-bold text-terra-green">
                    <span class="material-symbols-outlined text-[15px]">verified</span>
                    <span>Audited Payouts</span>
                </span>
            </div>
        </div>

        <!-- Channel & Logistics Share (1 Column) -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-xs p-6 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-base font-bold text-on-surface font-display flex items-center gap-2">
                        <span class="material-symbols-outlined text-terra-earth text-[20px]">pie_chart</span>
                        <span>Channel &amp; Logistics Share</span>
                    </h3>
                    <span class="text-[10px] font-black uppercase tracking-wider text-terra-green bg-terra-green/10 px-2 py-0.5 rounded-full border border-terra-green/20">Live</span>
                </div>
                <p class="text-xs text-on-surface-variant mb-4">Verified distribution across fulfillment and transaction methods</p>

                <!-- Section 1: Fulfillment Breakdown -->
                <div class="space-y-2 mb-5">
                    <div class="flex items-center justify-between text-xs font-bold text-on-surface">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-terra-green">two_wheeler</span>
                            <span>Doorstep Delivery</span>
                        </span>
                        <span class="font-mono"><?= esc($fulfillment_dist['delivery_pct'] ?? 0) ?>% <span class="font-normal text-on-surface-variant">(<?= number_format((int) ($fulfillment_dist['delivery_count'] ?? 0)) ?>)</span></span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2.5 overflow-hidden">
                        <div class="bg-terra-green h-full rounded-full transition-all duration-500" style="width: <?= max(4, min(100, (int) ($fulfillment_dist['delivery_pct'] ?? 0))) ?>%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs font-bold text-on-surface pt-1">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-terra-earth">store</span>
                            <span>Store Pick-up</span>
                        </span>
                        <span class="font-mono"><?= esc($fulfillment_dist['pickup_pct'] ?? 0) ?>% <span class="font-normal text-on-surface-variant">(<?= number_format((int) ($fulfillment_dist['pickup_count'] ?? 0)) ?>)</span></span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2.5 overflow-hidden">
                        <div class="bg-terra-earth h-full rounded-full transition-all duration-500" style="width: <?= max(4, min(100, (int) ($fulfillment_dist['pickup_pct'] ?? 0))) ?>%"></div>
                    </div>
                </div>

                <!-- Section 2: Payment Methods Share -->
                <div class="pt-3 border-t border-outline-variant/20 space-y-2 mb-4">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70 mb-2">Payment Channels</h4>
                    
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-on-surface flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> GCash Digital
                        </span>
                        <span class="font-bold text-on-surface font-mono"><?= esc($payment_dist['gcash']['pct'] ?? 0) ?>% <span class="font-normal text-on-surface-variant">(<?= number_format((int) ($payment_dist['gcash']['count'] ?? 0)) ?>)</span></span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full" style="width: <?= max(4, min(100, (int) ($payment_dist['gcash']['pct'] ?? 0))) ?>%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="font-semibold text-on-surface flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Online (PayMongo)
                        </span>
                        <span class="font-bold text-on-surface font-mono"><?= esc($payment_dist['online']['pct'] ?? 0) ?>% <span class="font-normal text-on-surface-variant">(<?= number_format((int) ($payment_dist['online']['count'] ?? 0)) ?>)</span></span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full" style="width: <?= max(4, min(100, (int) ($payment_dist['online']['pct'] ?? 0))) ?>%"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="font-semibold text-on-surface flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span> Cash on Delivery
                        </span>
                        <span class="font-bold text-on-surface font-mono"><?= esc($payment_dist['cash']['pct'] ?? 0) ?>% <span class="font-normal text-on-surface-variant">(<?= number_format((int) ($payment_dist['cash']['count'] ?? 0)) ?>)</span></span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2 overflow-hidden">
                        <div class="bg-slate-500 h-full rounded-full" style="width: <?= max(4, min(100, (int) ($payment_dist['cash']['pct'] ?? 0))) ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Ecosystem Summary Badge -->
            <div class="p-3.5 rounded-2xl bg-surface-container-low border border-outline-variant/20 flex items-center justify-between text-xs">
                <span class="text-on-surface-variant font-medium">Specialization</span>
                <span class="font-bold text-terra-green font-mono"><?= (int) ($printing_shops ?? 0) ?> Print Hubs · <?= max(0, (int) ($active_shops ?? 0) - (int) ($printing_shops ?? 0)) ?> Retail</span>
            </div>
        </div>
    </section>

    <!-- Secondary Section: Merchant Leaderboard & Polomolok Logistics -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Top Performing Merchants Leaderboard (2 Columns) -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-outline-variant/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-on-surface font-display flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500 text-[22px]">trophy</span>
                        <span>Merchant Performance Leaderboard</span>
                    </h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">Top-ranked partner shops by order throughput and gross transaction value</p>
                </div>
                <a href="<?= base_url('admin/tenants') ?>" class="text-xs font-bold text-terra-green hover:underline inline-flex items-center gap-0.5">
                    <span>View All Merchants</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[620px]">
                    <thead>
                        <tr class="bg-surface-container-low/60 text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider border-b border-outline-variant/20">
                            <th class="py-3.5 px-5">Rank &amp; Shop</th>
                            <th class="py-3.5 px-5 text-center">Plan</th>
                            <th class="py-3.5 px-5 text-center">Orders</th>
                            <th class="py-3.5 px-5 text-right">Volume (GMV)</th>
                            <th class="py-3.5 px-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-outline-variant/10">
                        <?php if (!empty($top_shops)): ?>
                            <?php 
                            $rank = 1;
                            $medals = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                            foreach ($top_shops as $s): 
                                $medal = $medals[$rank] ?? "#{$rank}";
                            ?>
                                <tr class="hover:bg-surface-container-low/40 transition-colors group">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 text-center font-black text-sm <?= $rank <= 3 ? 'text-amber-500' : 'text-on-surface-variant font-mono' ?>"><?= $medal ?></span>
                                            <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green border border-terra-green/20 flex items-center justify-center font-black text-xs overflow-hidden shrink-0 shadow-2xs">
                                                <?php if (!empty($s['logo_url'])): ?>
                                                    <img src="<?= esc(logo_url($s['logo_url'])) ?>" alt="<?= esc($s['shop_name']) ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <span class="font-display font-black text-xs"><?= esc(strtoupper(substr($s['shop_name'] ?? 'S', 0, 2))) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-on-surface text-sm group-hover:text-terra-green transition-colors truncate"><?= esc($s['shop_name']) ?></p>
                                                <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-on-surface-variant">
                                                    <?php if (!empty($s['offers_printing'])): ?>
                                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-terra-earth/10 text-terra-earth font-bold text-[10px]">
                                                            <span class="material-symbols-outlined text-[12px]">print</span> Print Hub
                                                        </span>
                                                    <?php endif; ?>
                                                    <span>Verified Partner</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-surface-container text-on-surface-variant border border-outline-variant/30">
                                            <?= esc($s['plan'] ?? 'standard') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="font-black text-terra-green font-mono text-sm"><?= number_format((int) ($s['total_orders'] ?? 0)) ?></span>
                                        <span class="block text-[10px] text-on-surface-variant">orders</span>
                                    </td>
                                    <td class="py-4 px-5 text-right font-black text-on-surface font-mono text-sm">
                                        ₱<?= number_format((float) ($s['total_gmv'] ?? 0), 2) ?>
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap">
                                        <a href="<?= base_url('admin/tenants?q=' . urlencode($s['shop_name'])) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-terra-green hover:bg-terra-green/10 border border-terra-green/20 transition-all active:scale-95">
                                            <span>Manage</span>
                                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php $rank++; endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-14 text-center text-on-surface-variant">
                                    <div class="w-14 h-14 rounded-2xl bg-surface-container mx-auto flex items-center justify-center text-outline mb-2">
                                        <span class="material-symbols-outlined text-3xl">store</span>
                                    </div>
                                    <p class="text-sm font-bold text-on-surface font-display">No merchant order activity recorded yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Polomolok Delivery & Logistics Operations (1 Column) -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-base font-bold text-on-surface font-display flex items-center gap-2">
                        <span class="material-symbols-outlined text-terra-green text-[20px]">near_me</span>
                        <span>Polomolok Logistics Hub</span>
                    </h3>
                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                        Bounded
                    </span>
                </div>
                <p class="text-xs text-on-surface-variant mb-4">All delivery coordinates constrained to Polomolok municipality</p>

                <!-- Core Zone Coordinates Box -->
                <div class="p-3.5 rounded-2xl bg-surface-container-low border border-outline-variant/20 mb-4">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-on-surface flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-terra-green">pin_drop</span>
                            <span>Operational Zone</span>
                        </span>
                        <span class="text-[11px] text-terra-green font-bold">Active &amp; Guarded</span>
                    </div>
                    <p class="text-[11px] text-on-surface-variant font-mono">Latitude: 6.1000° to 6.3200° N</p>
                    <p class="text-[11px] text-on-surface-variant font-mono">Longitude: 124.9500° to 125.1800° E</p>
                </div>

                <!-- Top Areas Breakdown -->
                <div class="space-y-2 mb-4">
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70 mb-1">Key Delivery Destinations</h4>
                    <?php if (!empty($area_counts)): ?>
                        <?php foreach ($area_counts as $area): ?>
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-surface-container-low/50 hover:bg-surface-container-low transition-colors">
                                <span class="font-semibold text-on-surface flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-outline">location_city</span>
                                    <span><?= esc($area['area']) ?></span>
                                </span>
                                <span class="font-bold text-terra-green bg-terra-green/10 px-2 py-0.5 rounded-lg border border-terra-green/20 font-mono">
                                    <?= number_format((int) $area['count']) ?> deliveries
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-surface-container-low/50">
                            <span class="font-semibold text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-outline">location_city</span>
                                <span>Poblacion / Cannery Site</span>
                            </span>
                            <span class="font-bold text-terra-green bg-terra-green/10 px-2 py-0.5 rounded-lg font-mono">Polomolok Core</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CTA to Live Fleet Tracking -->
            <div class="pt-3 border-t border-outline-variant/20">
                <a href="<?= base_url('admin/tracking') ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-surface-container-low text-on-surface hover:bg-terra-green hover:text-white border border-outline-variant/30 hover:border-terra-green transition-all group shadow-xs active:scale-95">
                    <span class="material-symbols-outlined text-[18px] text-terra-green group-hover:text-white">map</span>
                    <span>Launch Live GPS Fleet Map</span>
                </a>
            </div>
        </div>
    </section>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    let labels = <?= json_encode($chart_labels ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
    let values = <?= json_encode($chart_values ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
    const canvas  = document.getElementById('analyticsChart'),
          wrap    = document.getElementById('analyticsChartWrap'),
          tooltip = document.getElementById('analyticsTooltip');
    
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let hoveredIdx = -1;

    function draw(activeIdx = -1) {
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const w = rect.width, h = rect.height;
        const pad = { t: 20, r: 24, b: 32, l: 60 };
        const max = Math.max(...values, 1);
        const stepX = (w - pad.l - pad.r) / Math.max(labels.length - 1, 1);
        ctx.clearRect(0, 0, w, h);

        // Grid lines & labels
        ctx.strokeStyle = 'rgba(115, 118, 134, 0.12)';
        ctx.lineWidth = 1;
        ctx.fillStyle = '#64748b';
        ctx.font = '10px Inter, sans-serif';
        ctx.textAlign = 'right';

        for (let i = 0; i <= 4; i++) {
            const y = pad.t + (h - pad.t - pad.b) * (i / 4);
            ctx.beginPath();
            ctx.moveTo(pad.l, y);
            ctx.lineTo(w - pad.r, y);
            ctx.stroke();

            const val = max * (1 - i / 4);
            ctx.fillText('₱' + Math.round(val).toLocaleString(), pad.l - 8, y + 3);
        }

        // Plot points calculation
        const pts = values.map((v, i) => ({
            x: pad.l + i * stepX,
            y: pad.t + (h - pad.t - pad.b) * (1 - v / max),
            v: v,
            label: labels[i],
            index: i
        }));
        canvas._pts = pts;

        if (pts.length === 0) return;

        // Smooth curve path (Catmull-Rom or Bézier interpolation)
        function drawCurve(targetCtx) {
            targetCtx.beginPath();
            targetCtx.moveTo(pts[0].x, pts[0].y);
            for (let i = 0; i < pts.length - 1; i++) {
                const p0 = pts[Math.max(i - 1, 0)];
                const p1 = pts[i];
                const p2 = pts[i + 1];
                const p3 = pts[Math.min(i + 2, pts.length - 1)];

                const cp1x = p1.x + (p2.x - p0.x) / 6;
                const cp1y = p1.y + (p2.y - p0.y) / 6;
                const cp2x = p2.x - (p3.x - p1.x) / 6;
                const cp2y = p2.y - (p3.y - p1.y) / 6;

                targetCtx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, p2.x, p2.y);
            }
        }

        // Area Gradient in Terra Green
        ctx.save();
        drawCurve(ctx);
        ctx.lineTo(pts[pts.length - 1].x, h - pad.b);
        ctx.lineTo(pts[0].x, h - pad.b);
        ctx.closePath();

        const grad = ctx.createLinearGradient(0, pad.t, 0, h - pad.b);
        grad.addColorStop(0, 'rgba(74, 148, 62, 0.25)');
        grad.addColorStop(0.7, 'rgba(74, 148, 62, 0.05)');
        grad.addColorStop(1, 'rgba(74, 148, 62, 0.00)');
        ctx.fillStyle = grad;
        ctx.fill();
        ctx.restore();

        // Curve stroke in Terra Green
        ctx.save();
        drawCurve(ctx);
        ctx.strokeStyle = '#4A943E';
        ctx.lineWidth = 2.5;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';
        ctx.stroke();
        ctx.restore();

        // Vertical guide on hover
        if (activeIdx >= 0 && activeIdx < pts.length) {
            const ap = pts[activeIdx];
            ctx.save();
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(74, 148, 62, 0.40)';
            ctx.lineWidth = 1.5;
            ctx.setLineDash([4, 4]);
            ctx.moveTo(ap.x, pad.t);
            ctx.lineTo(ap.x, h - pad.b);
            ctx.stroke();
            ctx.restore();
        }

        // Data points
        pts.forEach((p, i) => {
            const isHovered = (i === activeIdx);
            if (isHovered) {
                ctx.beginPath();
                ctx.arc(p.x, p.y, 8, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(74, 148, 62, 0.25)';
                ctx.fill();
            }

            ctx.beginPath();
            ctx.arc(p.x, p.y, isHovered ? 5 : 3.5, 0, Math.PI * 2);
            ctx.fillStyle = '#4A943E';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(p.x, p.y, isHovered ? 2.5 : 1.5, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
        });

        // X-axis labels
        ctx.textAlign = 'center';
        ctx.fillStyle = '#64748b';
        ctx.font = '10px Inter, sans-serif';

        const labelStep = Math.max(1, Math.floor(labels.length / 6));
        for (let i = 0; i < labels.length; i += labelStep) {
            ctx.fillText(labels[i] || '', pts[i].x, h - 10);
        }
        if ((labels.length - 1) % labelStep !== 0) {
            const lastIdx = labels.length - 1;
            ctx.fillText(labels[lastIdx] || '', pts[lastIdx].x, h - 10);
        }
    }

    draw();
    window.addEventListener('resize', () => draw(hoveredIdx));

    // Canvas hover handler
    canvas.addEventListener('mousemove', e => {
        if (!canvas._pts || !canvas._pts.length) return;
        const canvasRect = canvas.getBoundingClientRect();
        const mx = e.clientX - canvasRect.left;
        let best = null, minDiff = 9999;

        canvas._pts.forEach(p => {
            const diff = Math.abs(p.x - mx);
            if (diff < minDiff) {
                minDiff = diff;
                best = p;
            }
        });

        if (best && minDiff < 32) {
            if (hoveredIdx !== best.index) {
                hoveredIdx = best.index;
                draw(hoveredIdx);
            }

            tooltip.innerHTML = `
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">${best.label}</div>
                <div class="text-sm font-black text-emerald-400 font-mono">₱${Number(best.v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                <div class="text-[9px] text-slate-400 mt-0.5">Admin Fee Yield</div>
            `;
            tooltip.classList.remove('hidden');

            const wrapRect = wrap.getBoundingClientRect();
            const ptX = (canvasRect.left - wrapRect.left) + best.x;
            const ptY = (canvasRect.top - wrapRect.top) + best.y;

            const tipW = tooltip.offsetWidth || 130;
            const tipH = tooltip.offsetHeight || 50;

            let left = ptX - (tipW / 2);
            left = Math.max(8, Math.min(left, wrapRect.width - tipW - 8));

            let top = ptY - tipH - 12;
            if (top < 8) top = ptY + 14;

            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
        } else {
            if (hoveredIdx !== -1) {
                hoveredIdx = -1;
                draw(-1);
            }
            tooltip.classList.add('hidden');
        }
    });

    canvas.addEventListener('mouseleave', () => {
        if (hoveredIdx !== -1) {
            hoveredIdx = -1;
            draw(-1);
        }
        tooltip.classList.add('hidden');
    });

    // AJAX range switcher
    document.querySelectorAll('.rangeBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const range = btn.dataset.range;
            loadAnalyticsRange(range);
        });
    });

    function loadAnalyticsRange(range) {
        document.querySelectorAll('.rangeBtn').forEach(b => {
            if (b.dataset.range === range) {
                b.className = 'rangeBtn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-terra-green text-white shadow-xs';
            } else {
                b.className = 'rangeBtn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface';
            }
        });

        fetch('<?= base_url('admin/analytics/data') ?>?range=' + range, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                labels = data.labels || [];
                values = data.values || [];
                hoveredIdx = -1;
                draw();

                // Update summary KPI pills
                const sumEl = document.getElementById('metric-period-total');
                const avgEl = document.getElementById('metric-period-avg');
                const peakEl = document.getElementById('metric-period-peak');

                if (sumEl) sumEl.textContent = '₱' + Number(data.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (avgEl) avgEl.textContent = '₱' + Number(data.avg || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                if (peakEl) peakEl.textContent = '₱' + Number(data.peak || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        })
        .catch(err => console.error('Failed to load analytics range:', err));
    }

    // Global refresh helper
    window.refreshAnalyticsData = function() {
        const icon = document.getElementById('refreshIcon');
        if (icon) icon.classList.add('animate-spin');
        const activeBtn = document.querySelector('.rangeBtn.bg-terra-green') || document.querySelector('.rangeBtn');
        const range = activeBtn ? activeBtn.dataset.range : '30';
        
        loadAnalyticsRange(range);
        setTimeout(() => {
            if (icon) icon.classList.remove('animate-spin');
            if (window.showToast) window.showToast('Analytics refreshed successfully', 'success');
        }, 600);
    };
})();
</script>
<?= $this->endSection() ?>

