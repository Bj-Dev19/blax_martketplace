<?= $this->extend('layouts/tenant') ?>
<?= $this->section('content') ?>

<?php
$ranges = ['7' => 'Last 7 days', '30' => 'Last 30 days', 'year' => 'This year'];
$statusColors = [
    'completed'        => '#4A943E', // Terra Green
    'ready_for_pickup' => '#8b5cf6', // Purple
    'delivered'        => '#06b6d4', // Cyan
    'in_transit'       => '#0284c7', // Sky
    'shipped'          => '#38bdf8', // Light Sky
    'processing'       => '#3b82f6', // Blue
    'pending'          => '#F4AE52', // Terra Amber
    'cancelled'        => '#ef4444', // Rose
    'returned'         => '#64748b', // Slate
];

$statusBgLight = [
    'completed'        => 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-300',
    'ready_for_pickup' => 'bg-purple-500/10 text-purple-700 border-purple-500/20 dark:bg-purple-500/20 dark:text-purple-300',
    'delivered'        => 'bg-cyan-500/10 text-cyan-700 border-cyan-500/20 dark:bg-cyan-500/20 dark:text-cyan-300',
    'in_transit'       => 'bg-sky-500/10 text-sky-700 border-sky-500/20 dark:bg-sky-500/20 dark:text-sky-300',
    'shipped'          => 'bg-sky-500/10 text-sky-700 border-sky-500/20 dark:bg-sky-500/20 dark:text-sky-300',
    'processing'       => 'bg-blue-500/10 text-blue-700 border-blue-500/20 dark:bg-blue-500/20 dark:text-blue-300',
    'pending'          => 'bg-amber-500/10 text-amber-700 border-amber-500/20 dark:bg-amber-500/20 dark:text-amber-300',
    'cancelled'        => 'bg-rose-500/10 text-rose-700 border-rose-500/20 dark:bg-rose-500/20 dark:text-rose-300',
    'returned'         => 'bg-slate-500/10 text-slate-700 border-slate-500/20 dark:bg-slate-500/20 dark:text-slate-300',
];

// Sort status distribution by count descending
if (!empty($status_dist)) {
    usort($status_dist, fn($a, $b) => ((int) ($b['count'] ?? 0)) <=> ((int) ($a['count'] ?? 0)));
}

$completedCount = 0;
$segs = [];
$acc  = 0;
foreach ($status_dist as $s) {
    $pct = (float) $s['pct'];
    if (in_array($s['status'], ['completed', 'delivered'], true)) {
        $completedCount += (int) $s['count'];
    }
    if ($pct <= 0) {
        continue;
    }
    $col = $statusColors[$s['status']] ?? '#64748b';
    $segs[] = $col . ' ' . $acc . '% ' . ($acc + $pct) . '%';
    $acc += $pct;
}
$conic = !empty($segs) ? 'conic-gradient(' . implode(', ', $segs) . ')' : '#e2e8f0';
$fulfilledRate = $status_total > 0 ? round(($completedCount / $status_total) * 100, 1) : 0;

$catColors = ['bg-terra-green', 'bg-terra-earth', 'bg-terra-amber', 'bg-sky-600', 'bg-purple-600'];
$chartMax  = max((float) $chart_max, 0.01);

$seasonDef        = $seasonal_data['season'] ?? [];
$isSeasonActive   = !empty($seasonal_data['is_currently_active']);
$activeKey        = $seasonal_data['selected_season_key'] ?? 'holiday';
$availableSeasons = $seasonal_data['available_seasons'] ?? [];
$recommendations  = $seasonal_data['recommendations'] ?? [];
$expansionIdeas   = $seasonal_data['expansion_ideas'] ?? [];
$detectedSeason   = $seasonal_data['detected_active_season'] ?? 'holiday';
?>

<div class="flex-1 space-y-5 pb-14">

    <!-- Top Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">insights</span>
                </span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest font-sans">Store Telemetry &amp; Demand Intelligence</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight font-display">Business Analytics &amp; Forecasting</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl font-sans">
                Real-time sales telemetry, seasonal demand predictions, inventory replenishment buffers, and customer sentiment analytics.
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <!-- Active General Season Indicator Pill -->
            <div class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 shadow-2xs">
                <span class="material-symbols-outlined text-[18px] text-terra-green"><?= esc($seasonDef['icon'] ?? 'celebration') ?></span>
                <div class="text-left">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Market Cycle</span>
                        <span class="w-1.5 h-1.5 rounded-full <?= $isSeasonActive ? 'bg-terra-green animate-pulse' : 'bg-slate-400' ?>"></span>
                    </div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block"><?= esc($seasonDef['short_name'] ?? 'Seasonal Rush') ?></span>
                </div>
            </div>

            <!-- Open Inventory Button -->
            <a href="<?= base_url('tenant/inventory') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 transition-all shadow-2xs">
                <span class="material-symbols-outlined text-[17px] text-terra-green">inventory_2</span>
                <span>Open Inventory</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- HORIZONTAL SUB-NAVIGATION BAR (Mirroring Reference Layout)                -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-2 sm:p-2.5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 sticky top-[60px] z-10 backdrop-blur-md">
        
        <!-- Tab Navigation Buttons -->
        <div class="flex items-center gap-1 sm:gap-1.5 overflow-x-auto no-scrollbar pb-1 md:pb-0" id="analytics-tabs-nav" role="tablist">
            
            <button type="button" 
                    data-tab-target="scorecard" 
                    class="analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-bold transition-all bg-terra-green text-white shadow-xs" 
                    role="tab" 
                    aria-selected="true">
                <span class="material-symbols-outlined text-[16px]">speed</span>
                <span>KPI Scorecard</span>
            </button>

            <button type="button" 
                    data-tab-target="revenue" 
                    class="analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all" 
                    role="tab" 
                    aria-selected="false">
                <span class="material-symbols-outlined text-[16px]">monitoring</span>
                <span>Sales &amp; Revenue</span>
            </button>

            <button type="button" 
                    data-tab-target="forecasting" 
                    class="analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all" 
                    role="tab" 
                    aria-selected="false">
                <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                <span>Seasonal Demand AI</span>
            </button>

            <button type="button" 
                    data-tab-target="replenishment" 
                    class="analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all" 
                    role="tab" 
                    aria-selected="false">
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>Replenishment</span>
            </button>

            <button type="button" 
                    data-tab-target="reviews" 
                    class="analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all" 
                    role="tab" 
                    aria-selected="false">
                <span class="material-symbols-outlined text-[16px] text-amber-500 fill-icon">star</span>
                <span>Ratings &amp; Reviews</span>
                <?php if ($total_reviews_count > 0): ?>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-mono font-bold"><?= $total_reviews_count ?></span>
                <?php endif; ?>
            </button>
        </div>

        <!-- Date Filter Telemetry Component on Right (matching reference screenshot) -->
        <div class="flex items-center gap-2 shrink-0 self-end md:self-auto text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Preset Range Pills -->
                <div class="inline-flex items-center bg-slate-100 dark:bg-slate-800/80 p-0.5 rounded-xl border border-slate-200/80 dark:border-slate-700">
                    <a href="<?= base_url('tenant/analytics?range=7') ?>" class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all <?= $range === '7' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' ?>">7D</a>
                    <a href="<?= base_url('tenant/analytics?range=30') ?>" class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all <?= $range === '30' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' ?>">30D</a>
                    <a href="<?= base_url('tenant/analytics?range=year') ?>" class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all <?= $range === 'year' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' ?>">Year</a>
                </div>

                <!-- Date Range Display Box -->
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 font-mono text-[11px]">
                    <span class="text-slate-400">From</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200"><?= date('m/d/Y', strtotime($range === '7' ? '-7 days' : ($range === 'year' ? '-1 year' : '-30 days'))) ?></span>
                    <span class="text-slate-400">To</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200"><?= date('m/d/Y') ?></span>
                    <span class="material-symbols-outlined text-[14px] text-terra-green">calendar_today</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: 📊 EXECUTIVE SCORECARD                                             -->
    <!-- ========================================================================= -->
    <div id="tab-panel-scorecard" class="analytics-tab-panel space-y-5">
        
        <!-- Top 4 Primary KPI Bento Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- KPI 1: Gross Revenue -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-terra-green/40 dark:hover:border-terra-green/40 transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Gross Revenue</span>
                    <span class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </span>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-mono">₱<?= number_format((float) $total_revenue, 2) ?></p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-terra-green">verified</span>
                        <span>Delivered orders + print jobs</span>
                    </p>
                </div>
            </div>

            <!-- KPI 2: Avg. Order Value -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-terra-earth/40 dark:hover:border-terra-earth/40 transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Avg. Order Value</span>
                    <span class="w-9 h-9 rounded-xl bg-terra-earth/10 text-terra-earth dark:text-amber-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                    </span>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-mono">₱<?= number_format((float) $avg_order_value, 2) ?></p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Across <?= number_format((int) $avg_order_base) ?> verified orders</p>
                </div>
            </div>

            <!-- KPI 3: Customer Satisfaction (CSAT) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-amber-400/40 transition-all flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Customer Sentiment</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">sentiment_satisfied</span>
                    </span>
                </div>
                <div>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-mono"><?= number_format($csat_score, 1) ?>%</p>
                        <span class="text-xs font-bold text-amber-500 flex items-center">
                            <span class="material-symbols-outlined text-[14px] fill-icon">star</span>
                            <?= number_format($shop_avg_rating, 1) ?>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">CSAT from <?= number_format((int) $total_reviews_count) ?> verified buyer reviews</p>
                </div>
            </div>

            <!-- KPI 4: Seasonal AI Restock Opportunity -->
            <div class="bg-gradient-to-br from-amber-500/5 via-white dark:via-slate-900 to-emerald-500/5 dark:from-amber-950/20 dark:to-emerald-950/20 p-5 rounded-2xl border border-terra-amber/30 dark:border-terra-amber/30 shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-center justify-between gap-2 mb-3 relative z-10">
                    <span class="text-xs font-bold text-terra-amber dark:text-amber-400 uppercase tracking-wider">Seasonal Opportunity</span>
                    <span class="w-9 h-9 rounded-xl bg-terra-amber/10 text-terra-amber dark:text-amber-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">auto_awesome</span>
                    </span>
                </div>
                <div class="relative z-10">
                    <p class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-mono">₱<?= number_format((float) ($seasonal_data['est_seasonal_opp_revenue'] ?? 0), 2) ?></p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                        <?php if (($seasonal_data['critical_stockout_count'] ?? 0) > 0): ?>
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <strong class="text-rose-600 font-bold"><?= (int) $seasonal_data['critical_stockout_count'] ?></strong> items need immediate restock
                        <?php else: ?>
                            <span class="material-symbols-outlined text-[14px] text-terra-green">verified</span>
                            <span class="text-terra-green font-semibold">Safety stock well-maintained</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Secondary Scorecard Pillars (4 Pillars) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Fulfillment Velocity -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-terra-green flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Fulfillment Velocity</span>
                    <span class="text-base font-extrabold text-slate-900 dark:text-white font-mono"><?= $fulfilledRate ?>% Completed</span>
                    <span class="text-[10px] text-slate-500 block"><?= number_format($completedCount) ?> of <?= number_format($status_total) ?> orders processed</span>
                </div>
            </div>

            <!-- Active Catalog Health -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Active Catalog</span>
                    <span class="text-base font-extrabold text-slate-900 dark:text-white font-mono"><?= (int) ($inventory_summary['total_items'] ?? 0) ?> SKUs Listed</span>
                    <span class="text-[10px] text-slate-500 block"><?= (int) ($inventory_summary['low_stock_count'] ?? 0) ?> low stock &bull; <?= (int) ($inventory_summary['out_of_stock_count'] ?? 0) ?> out</span>
                </div>
            </div>

            <!-- Top Category -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">category</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Leading Category</span>
                    <span class="text-base font-extrabold text-slate-900 dark:text-white truncate block font-display" title="<?= esc($top_category) ?>"><?= esc($top_category) ?></span>
                    <span class="text-[10px] text-slate-500 block"><?= count($category_sales) ?> classifications active</span>
                </div>
            </div>

            <!-- Top Revenue Product -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">military_tech</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Bestseller of Period</span>
                    <span class="text-base font-extrabold text-slate-900 dark:text-white truncate block" title="<?= esc($top_products[0]['name'] ?? 'None recorded') ?>"><?= esc($top_products[0]['name'] ?? 'No sales yet') ?></span>
                    <span class="text-[10px] text-slate-500 font-mono block">₱<?= number_format((float) ($top_products[0]['revenue'] ?? 0), 2) ?></span>
                </div>
            </div>

        </div>

        <!-- Quick Launch Modules Banner -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">psychology</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Seasonal Demand Engine &amp; Replenishment Active</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Explore predictive restock recommendations for upcoming holidays or review customer sentiment ratings.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="activateTab('forecasting')" class="px-3.5 py-2 rounded-xl bg-terra-green text-white text-xs font-bold hover:bg-emerald-700 transition-colors shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                    <span>View AI Forecast</span>
                </button>
                <button type="button" onclick="activateTab('reviews')" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-amber-500 fill-icon">star</span>
                    <span>Ratings Feed</span>
                </button>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: 📈 SALES & REVENUE TELEMETRY                                       -->
    <!-- ========================================================================= -->
    <div id="tab-panel-revenue" class="analytics-tab-panel space-y-5 hidden">
        
        <!-- Multi-Period Interactive Line Chart Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800 space-y-4">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white font-display">Revenue Velocity Over Time</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-sans">Gross revenue from completed online orders, POS retail transactions, and released printing jobs.</p>
                </div>
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                    <?php foreach ($ranges as $key => $label): ?>
                        <button type="button" data-range="<?= esc($key) ?>"
                                class="analytics-range-btn px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all <?= $range === $key ? 'bg-terra-green text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700' ?>"><?= esc($label) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Metric Comparison Badges -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-terra-green ring-4 ring-terra-green/20"></span>
                        <div>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium block uppercase tracking-wider">Current Period</span>
                            <span id="analytics-current-total" class="text-lg font-black text-slate-900 dark:text-white font-mono">₱<?= number_format((float) $chart_total, 2) ?></span>
                        </div>
                    </div>
                    <div id="analytics-prev-container" class="flex items-center gap-2 <?= (float) $chart_previous_total > 0 ? '' : 'hidden' ?>">
                        <span class="w-3.5 h-3.5 rounded-full bg-slate-400 ring-4 ring-slate-400/20"></span>
                        <div>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium block uppercase tracking-wider">Previous Period</span>
                            <span id="analytics-prev-total" class="text-lg font-black text-slate-500 dark:text-slate-400 font-mono">₱<?= number_format((float) $chart_previous_total, 2) ?></span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <?php
                    $growthPct = 0;
                    if ((float) $chart_previous_total > 0) {
                        $growthPct = round((($chart_total - $chart_previous_total) / $chart_previous_total) * 100, 1);
                    }
                    ?>
                    <span id="analytics-growth-badge" class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full font-mono <?= (float) $chart_previous_total > 0 ? ($growthPct >= 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300') : 'hidden' ?>">
                        <span id="analytics-growth-icon" class="material-symbols-outlined text-[16px]"><?= $growthPct >= 0 ? 'trending_up' : 'trending_down' ?></span>
                        <span id="analytics-growth-text"><?= $growthPct >= 0 ? '+' : '' ?><?= $growthPct ?>% vs prev</span>
                    </span>
                </div>
            </div>

            <!-- Chart Container -->
            <div id="chart-wrapper" class="relative w-full min-h-[300px] h-80 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30 p-2 overflow-hidden" style="min-height: 300px; height: 320px;">
                <div id="analytics-loading" class="hidden absolute inset-0 rounded-2xl bg-white/70 dark:bg-slate-900/70 backdrop-blur-xs flex items-center justify-center z-20 transition-opacity">
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 shadow-md border border-slate-200 dark:border-slate-700 text-terra-green text-xs font-semibold">
                        <span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                        <span>Updating sales telemetry...</span>
                    </div>
                </div>
                <canvas id="revenueLineChart" class="w-full h-full block transition-opacity duration-200"></canvas>
                <div id="revenueTooltip" class="hidden absolute z-30 pointer-events-none whitespace-nowrap text-xs font-medium px-3 py-2 rounded-xl shadow-xl bg-slate-900/95 text-slate-100 border border-slate-700/60 backdrop-blur-xs transition-[opacity,transform] duration-75"></div>
            </div>
        </div>

        <!-- Sales Channel Attribution (3 Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($channels as $chKey => $ch): ?>
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider"><?= esc($ch['name']) ?></span>
                            <span class="w-8 h-8 rounded-lg <?= $ch['color'] ?>/10 text-slate-800 dark:text-slate-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]"><?= esc($ch['icon']) ?></span>
                            </span>
                        </div>
                        <p class="text-xl font-black text-slate-900 dark:text-white font-mono">₱<?= number_format((float) $ch['revenue'], 2) ?></p>
                        <p class="text-xs text-slate-500 mt-0.5"><?= number_format((int) $ch['orders']) ?> orders completed</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Revenue Contribution</span>
                        <span class="font-bold text-terra-green font-mono"><?= number_format((float) $ch['pct'], 1) ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Category Sales & Order Status Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <!-- Sales by Category -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Sales by Category</h3>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400"><?= count($category_sales) ?> Categories</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 font-sans">Revenue composition across product classifications.</p>

                    <?php if (!empty($category_sales)): ?>
                        <div class="space-y-3">
                            <?php foreach ($category_sales as $i => $c): ?>
                                <?php $catPct = (float) $c['pct']; ?>
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100/70 dark:hover:bg-slate-800 transition-all border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-800 dark:text-slate-200 font-bold truncate max-w-xs"><?= esc($c['category']) ?></span>
                                        <div class="flex items-center gap-2 shrink-0 font-mono">
                                            <span class="font-extrabold text-slate-900 dark:text-white">₱<?= number_format((float) $c['revenue'], 2) ?></span>
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-terra-green/10 text-terra-green dark:bg-terra-green/20">
                                                <?= number_format($catPct, 1) ?>%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full <?= $catColors[$i % count($catColors)] ?> transition-all duration-500" style="width: <?= min(100, max(0, $catPct)) ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-12 text-center text-slate-500 dark:text-slate-400 flex flex-col items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-slate-400">category</span>
                            <p class="text-xs text-slate-500">Not enough category data recorded yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Status Distribution -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Order Status Breakdown</h3>
                        <?php if ($status_total > 0): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-terra-green animate-pulse"></span>
                                <span><?= $fulfilledRate ?>% Fulfilled</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 font-sans">Pipeline fulfillment status across recorded orders.</p>

                    <?php if ($status_total > 0): ?>
                        <div class="flex flex-col sm:flex-row items-center gap-6 lg:gap-7">
                            <div class="relative w-40 h-40 shrink-0 mx-auto sm:mx-0">
                                <div class="w-40 h-40 rounded-full shadow-sm ring-4 ring-slate-100 dark:ring-slate-800 transition-transform duration-300" style="background: <?= $conic ?>"></div>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-28 h-28 rounded-full bg-white dark:bg-slate-900 flex flex-col items-center justify-center shadow-md border border-slate-200 dark:border-slate-800">
                                        <span class="text-xl font-black text-slate-900 dark:text-white tracking-tight font-mono"><?= number_format((int) $status_total) ?></span>
                                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-widest mt-0.5">Orders</span>
                                        <span class="text-[9px] font-bold text-terra-green mt-0.5"><?= $fulfilledRate ?>% Done</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 w-full space-y-1.5">
                                <?php foreach ($status_dist as $s): ?>
                                    <?php
                                    $col = $statusColors[$s['status']] ?? '#64748b';
                                    $badgeStyle = $statusBgLight[$s['status']] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                    $pct = (float) $s['pct'];
                                    ?>
                                    <div class="group px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/40 hover:bg-slate-100/70 transition-all border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-3 h-3 rounded-full shrink-0 shadow-2xs" style="background: <?= $col ?>"></span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 truncate"><?= esc(humanize_status($s['status'])) ?></span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 font-mono">
                                            <span class="font-bold text-slate-900 dark:text-white text-xs"><?= number_format((int) $s['count']) ?></span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold border <?= $badgeStyle ?>">
                                                <?= number_format($pct, 1) ?>%
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="py-12 text-center text-slate-500 dark:text-slate-400 flex flex-col items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-slate-400">shopping_bag</span>
                            <p class="text-xs text-slate-500">No customer orders recorded yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: 🔮 SEASONAL DEMAND AI & FORECASTING HUB                            -->
    <!-- ========================================================================= -->
    <div id="tab-panel-forecasting" class="analytics-tab-panel space-y-5 hidden">
        
        <div id="seasonal-ai-hub" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-md overflow-hidden relative">

            <!-- Hub Hero Banner -->
            <div class="bg-slate-900 bg-gradient-to-r from-slate-950 via-slate-900 to-[#142b12] text-white p-6 sm:p-8 relative overflow-hidden" style="background-color: #0f172a;">
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-terra-green/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-terra-amber/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="max-w-2xl space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-white/10 text-emerald-300 border border-white/20 backdrop-blur-xs font-sans">
                                <span class="material-symbols-outlined text-[15px]">auto_awesome</span>
                                <span>Cohere Predictive Demand Engine</span>
                            </span>
                            <span id="active-season-status-pill" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider <?= $isSeasonActive ? 'bg-terra-green/20 text-emerald-300 border border-terra-green/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $isSeasonActive ? 'bg-terra-green animate-pulse' : 'bg-amber-400' ?>"></span>
                                <span id="active-season-status-text"><?= $isSeasonActive ? 'Active Calendar Cycle' : 'Simulation Mode' ?></span>
                            </span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight flex items-center gap-2.5 font-display">
                            <span id="hub-season-icon" class="material-symbols-outlined text-[28px] text-emerald-300"><?= esc($seasonDef['icon'] ?? 'celebration') ?></span>
                            <span id="hub-season-title"><?= esc($seasonDef['name'] ?? 'Seasonal Rush') ?></span>
                        </h2>

                        <p id="hub-season-description" class="text-xs sm:text-sm text-slate-300 leading-relaxed font-sans">
                            <?= esc($seasonDef['description'] ?? 'Local consumer demand surges during this operational cycle. Replenish high-velocity items to maximize sales.') ?>
                        </p>

                        <!-- Cohere Generative AI Strategic Advisory Banner -->
                        <div id="cohere-advisory-box" class="<?= !empty($seasonal_data['cohere_summary']) ? '' : 'hidden' ?> mt-3 p-3.5 rounded-2xl bg-white/10 border border-emerald-400/30 text-xs text-slate-100 backdrop-blur-xs flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-terra-amber text-[20px] shrink-0 mt-0.5">smart_toy</span>
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-white uppercase tracking-wider text-[10px]">Cohere AI Strategic Retail Advisory</span>
                                    <span class="px-1.5 py-0.5 rounded-full bg-terra-green text-white text-[9px] font-mono">command-a</span>
                                </div>
                                <p id="cohere-executive-summary" class="text-xs text-slate-200 leading-relaxed font-sans">
                                    <?= esc($seasonal_data['cohere_summary'] ?? '') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Velocity & Period Badge Group -->
                    <div class="flex lg:flex-col items-center lg:items-end gap-3 shrink-0">
                        <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-2.5 backdrop-blur-xs text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 block">Anticipated Velocity Surge</span>
                            <span id="hub-season-multiplier" class="text-xl font-black text-amber-300 font-mono"><?= number_format((float) ($seasonDef['velocity_multiplier'] ?? 2.0), 1) ?>x Velocity</span>
                        </div>
                        <span id="hub-season-period" class="text-xs text-slate-300 font-medium bg-black/40 px-3 py-1.5 rounded-xl border border-white/10">
                            <?= esc($seasonDef['period_label'] ?? '') ?>
                        </span>
                    </div>
                </div>

                <!-- General Seasonal Event Switcher Tabs -->
                <div class="relative z-10 mt-6 pt-5 border-t border-white/15">
                    <div class="flex items-center justify-between gap-3 mb-2.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5 font-sans">
                            <span class="material-symbols-outlined text-[16px]">tune</span>
                            <span>Simulate Demand Across Recurring Holidays &amp; Events</span>
                        </span>
                        <span class="text-[11px] text-slate-400">Click an event to preview inventory targets</span>
                    </div>

                    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar" id="seasonal-tabs-container">
                        <?php foreach ($availableSeasons as $key => $s): ?>
                            <?php
                            $isCurrentTab = ($key === $activeKey);
                            $isCalendarCurrent = ($key === $detectedSeason);
                            ?>
                            <button type="button"
                                    data-season-key="<?= esc($key) ?>"
                                    class="season-tab-btn shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all <?= $isCurrentTab ? 'bg-white text-slate-900 shadow-md ring-2 ring-terra-green/80 scale-102' : 'bg-white/10 hover:bg-white/20 text-white border border-white/10' ?>">
                                <span class="material-symbols-outlined text-[16px] <?= $isCurrentTab ? 'text-terra-green' : 'text-slate-300' ?>"><?= esc($s['icon']) ?></span>
                                <span><?= esc($s['short_name']) ?></span>
                                <?php if ($isCalendarCurrent): ?>
                                    <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-full <?= $isCurrentTab ? 'bg-terra-green text-white' : 'bg-terra-green/90 text-white' ?>">Active</span>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Hub Interactive Content -->
            <div class="relative p-6 sm:p-8 space-y-6">

                <!-- Loading Spinner for AJAX Tab Switching -->
                <div id="seasonal-loading-overlay" class="hidden absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xs flex items-center justify-center z-20 transition-opacity">
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 text-terra-green text-xs font-bold">
                        <span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                        <span>AI projecting seasonal demand &amp; safety buffers...</span>
                    </div>
                </div>

                <!-- Live Telemetry Bar & Refresh Action -->
                <div class="flex items-center justify-between flex-wrap gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-mono">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Real-Time Telemetry</span>
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-sans" id="forecast-computed-time">
                            Calculated from live stock &amp; orders &bull; <?= date('M d, Y h:i A') ?>
                        </span>
                    </div>

                    <button type="button" 
                            id="btn-refresh-forecast" 
                            onclick="refreshLiveForecast()" 
                            class="px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 shadow-2xs transition-all cursor-pointer active:scale-95">
                        <span class="material-symbols-outlined text-[16px] text-terra-green transition-transform" id="refresh-spinner-icon">sync</span>
                        <span>Refresh Live Forecast</span>
                    </button>
                </div>

                <!-- Seasonal Metrics Bar -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Demand Multiplier</span>
                        <span id="metric-surge" class="text-xl font-black text-slate-900 dark:text-white mt-1 block font-mono">
                            +<?= round(((float) ($seasonDef['velocity_multiplier'] ?? 2.0) - 1.0) * 100) ?>%
                        </span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400">Above normal daily run rate</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20">
                        <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider block">Critical Stockouts</span>
                        <span id="metric-critical" class="text-xl font-black text-rose-700 dark:text-rose-400 mt-1 block font-mono">
                            <?= (int) ($seasonal_data['critical_stockout_count'] ?? 0) ?> items
                        </span>
                        <span class="text-[10px] text-rose-600 dark:text-rose-400/80">Stock ≤ 0 or depleted</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20">
                        <span class="text-[11px] font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider block">Restock Priority</span>
                        <span id="metric-restock-count" class="text-xl font-black text-amber-800 dark:text-amber-400 mt-1 block font-mono">
                            <?= (int) ($seasonal_data['items_needing_restock'] ?? 0) ?> items
                        </span>
                        <span class="text-[10px] text-amber-700 dark:text-amber-400/80">Below seasonal safety buffer</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-[11px] font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider block">Est. Revenue Uplift</span>
                        <span id="metric-opp-revenue" class="text-xl font-black text-emerald-800 dark:text-emerald-400 mt-1 block font-mono">
                            ₱<?= number_format((float) ($seasonal_data['est_seasonal_opp_revenue'] ?? 0), 2) ?>
                        </span>
                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400/80">Protected by replenishment</span>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- SECTION A: RECOMMENDED PRODUCT RESTOCKS (WITH PAGINATION & SEARCH)         -->
                <!-- ========================================================================= -->
                <div class="space-y-4 pt-2" id="seasonal-restock-section">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-terra-green text-[20px]">inventory</span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Recommended Product Restocks (Based on Your Current Inventory)</h4>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-sans">
                                Real-time inventory audit: current stock compared against projected <?= esc($seasonDef['short_name']) ?> safety buffer.
                            </p>
                        </div>
                        <a href="<?= base_url('tenant/inventory') ?>" class="text-xs font-bold text-terra-green hover:underline flex items-center gap-1 shrink-0">
                            <span>Open Inventory</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    </div>

                    <!-- Search Input & Urgency Filter Toolbar -->
                    <div class="p-3 bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                        <!-- Live Search Input -->
                        <div class="relative flex-1">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                            <input type="text"
                                   id="seasonalSearchInput"
                                   oninput="filterSeasonalRestocks()"
                                   placeholder="Search restock items by product name, SKU, or category..."
                                   class="w-full pl-9 pr-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green transition-all">
                        </div>

                        <!-- Urgency Filter Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
                            <button type="button" onclick="setSeasonalUrgency('all', this)" class="seasonal-urgency-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-terra-green text-white shadow-2xs transition-all cursor-pointer">
                                All (<span id="count-all"><?= count($recommendations ?? []) ?></span>)
                            </button>
                            <button type="button" onclick="setSeasonalUrgency('critical', this)" class="seasonal-urgency-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-slate-900 text-rose-600 border border-slate-200 dark:border-slate-700 hover:border-rose-300 transition-all cursor-pointer">
                                Critical
                            </button>
                            <button type="button" onclick="setSeasonalUrgency('high', this)" class="seasonal-urgency-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-slate-900 text-amber-600 border border-slate-200 dark:border-slate-700 hover:border-amber-300 transition-all cursor-pointer">
                                High
                            </button>
                            <button type="button" onclick="setSeasonalUrgency('moderate', this)" class="seasonal-urgency-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-slate-900 text-blue-600 border border-slate-200 dark:border-slate-700 hover:border-blue-300 transition-all cursor-pointer">
                                Moderate
                            </button>
                        </div>
                    </div>

                    <!-- Filter Results Summary Banner -->
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 px-1">
                        <span id="seasonalPaginationSummary">Showing recommendations...</span>
                        <span class="text-[11px] font-mono text-slate-400">9 items per page</span>
                    </div>

                    <!-- Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5" id="seasonal-restock-cards-container">
                        <?php if (!empty($recommendations)): ?>
                            <?php foreach ($recommendations as $r): ?>
                                <?php
                                $urgencyClass = match ($r['urgency']) {
                                    'critical' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300',
                                    'high'     => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300',
                                    'moderate' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/40 dark:text-blue-300',
                                    default    => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300',
                                };
                                $barColor = match ($r['urgency']) {
                                    'critical' => 'bg-rose-500',
                                    'high'     => 'bg-amber-500',
                                    'moderate' => 'bg-blue-500',
                                    default    => 'bg-terra-green',
                                };
                                ?>
                                <div class="seasonal-restock-card bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between hover:border-terra-green/50 transition-all shadow-2xs group"
                                     id="restock-card-<?= $r['id'] ?>"
                                     data-name="<?= esc(strtolower($r['name'])) ?>"
                                     data-sku="<?= esc(strtolower($r['sku'] ?? '')) ?>"
                                     data-category="<?= esc(strtolower($r['category'] ?? '')) ?>"
                                     data-urgency="<?= esc($r['urgency']) ?>">
                                    <div class="space-y-3">
                                        <!-- Top Row: Category & Urgency Badge -->
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono truncate max-w-[130px]"><?= esc($r['category']) ?></span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wider shrink-0 <?= $urgencyClass ?>">
                                                <?= esc(humanize_status($r['urgency'])) ?>
                                            </span>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-700 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200 dark:border-slate-600 shadow-2xs">
                                                <?php if (!empty($r['image_url'])): ?>
                                                    <img src="<?= esc($r['image_url']) ?>" alt="<?= esc($r['name']) ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <span class="material-symbols-outlined text-[24px] text-terra-green">inventory_2</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0">
                                                <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate" title="<?= esc($r['name']) ?>"><?= esc($r['name']) ?></h5>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 font-mono">₱<?= number_format((float) $r['price'], 2) ?></span>
                                                    <span class="text-[10px] text-slate-400 font-mono">&bull; <?= esc($r['sku']) ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock vs Buffer Progress Bar -->
                                        <div class="bg-white dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                                            <div class="flex items-center justify-between text-[11px] font-mono">
                                                <span class="font-bold text-slate-900 dark:text-slate-100" id="card-stock-<?= $r['id'] ?>"><?= $r['current_stock'] ?> in stock</span>
                                                <span class="text-slate-500 dark:text-slate-400 text-[10px]">Buffer Target: <?= $r['target_buffer'] ?></span>
                                            </div>
                                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full <?= $barColor ?> transition-all duration-300" style="width: <?= $r['stock_pct'] ?>%"></div>
                                            </div>
                                            <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-0.5">
                                                <span>Runway: ~<?= $r['days_remaining'] ?> days</span>
                                                <span>Threshold: <?= $r['threshold'] ?></span>
                                            </div>
                                        </div>

                                        <!-- AI Operational Insight -->
                                        <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed font-sans line-clamp-2">
                                            <?= esc($r['rationale'] ?? ($r['ai_rationale'] ?? 'Replenish stock to maintain uninterrupted fulfillment.')) ?>
                                        </p>
                                    </div>

                                    <!-- Action Button Row -->
                                    <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700/80 flex items-center justify-between gap-2">
                                        <?php if ($r['restock_units'] > 0): ?>
                                            <button type="button" 
                                                    onclick="quickRestockProduct(<?= $r['id'] ?>, <?= $r['restock_units'] ?>, this)"
                                                    class="w-full py-2 px-3 rounded-xl bg-terra-green hover:bg-terra-green-dark text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm active:scale-98 transition-all cursor-pointer">
                                                <span class="material-symbols-outlined text-[16px]">bolt</span>
                                                <span>Quick Restock +<?= $r['restock_units'] ?> pcs</span>
                                            </button>
                                        <?php else: ?>
                                            <div class="w-full flex items-center justify-between px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold">
                                                <span class="flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                                    <span>Stock Level Optimal</span>
                                                </span>
                                                <button type="button" 
                                                        onclick="quickRestockProduct(<?= $r['id'] ?>, 10, this)"
                                                        class="text-[11px] text-terra-green hover:underline font-mono">
                                                    +10 pcs
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 text-center text-slate-500 dark:text-slate-400 text-xs">
                                No active products found in this store's inventory.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- No Match Feedback for Search/Filter -->
                    <div id="seasonalNoMatch" class="hidden p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 text-center space-y-2">
                        <span class="material-symbols-outlined text-3xl text-slate-400">search_off</span>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No restock recommendations found</p>
                        <p class="text-[11px] text-slate-400">Try adjusting your search terms or urgency filter.</p>
                    </div>

                    <!-- Client-Side Pagination Controls -->
                    <div id="seasonalPaginationControls" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-200/80 dark:border-slate-800">
                        <div class="flex items-center gap-1.5" id="seasonalPageNumbers">
                            <!-- Injected dynamically by JavaScript -->
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    id="btnSeasonalPrev"
                                    onclick="changeSeasonalPage(-1)"
                                    class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                                <span>Previous</span>
                            </button>
                            <button type="button"
                                    id="btnSeasonalNext"
                                    onclick="changeSeasonalPage(1)"
                                    class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors flex items-center gap-1 cursor-pointer">
                                <span>Next</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                // Client-side instant search & pagination for seasonal restocks
                (function() {
                    let currentUrgency = 'all';
                    let currentPage = 1;
                    const pageSize = 9;

                    window.setSeasonalUrgency = function(urgency, btn) {
                        currentUrgency = urgency;
                        currentPage = 1;
                        document.querySelectorAll('.seasonal-urgency-btn').forEach(b => {
                            b.className = 'seasonal-urgency-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer';
                        });
                        if (btn) {
                            btn.className = 'seasonal-urgency-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-terra-green text-white shadow-2xs transition-all cursor-pointer';
                        }
                        updateSeasonalView();
                    };

                    window.filterSeasonalRestocks = function() {
                        currentPage = 1;
                        updateSeasonalView();
                    };

                    window.changeSeasonalPage = function(delta) {
                        currentPage += delta;
                        updateSeasonalView();
                        const sec = document.getElementById('seasonal-restock-section');
                        if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    };

                    window.goToSeasonalPage = function(p) {
                        currentPage = p;
                        updateSeasonalView();
                        const sec = document.getElementById('seasonal-restock-section');
                        if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    };

                    function updateSeasonalView() {
                        const searchVal = (document.getElementById('seasonalSearchInput')?.value || '').trim().toLowerCase();
                        const cards = Array.from(document.querySelectorAll('.seasonal-restock-card'));
                        if (cards.length === 0) return;

                        // Filter matching cards
                        const matched = cards.filter(card => {
                            const name = card.dataset.name || '';
                            const sku = card.dataset.sku || '';
                            const cat = card.dataset.category || '';
                            const urg = card.dataset.urgency || '';

                            const matchesSearch = !searchVal || name.includes(searchVal) || sku.includes(searchVal) || cat.includes(searchVal);
                            const matchesUrgency = (currentUrgency === 'all') || (urg === currentUrgency);

                            return matchesSearch && matchesUrgency;
                        });

                        const totalMatched = matched.length;
                        const totalPages = Math.max(1, Math.ceil(totalMatched / pageSize));
                        if (currentPage > totalPages) currentPage = totalPages;
                        if (currentPage < 1) currentPage = 1;

                        const startIdx = (currentPage - 1) * pageSize;
                        const endIdx = startIdx + pageSize;

                        // Hide all, then show slice of matched
                        cards.forEach(c => c.style.display = 'none');
                        matched.slice(startIdx, endIdx).forEach(c => c.style.display = 'flex');

                        // Summary
                        const summaryEl = document.getElementById('seasonalPaginationSummary');
                        const noMatchEl = document.getElementById('seasonalNoMatch');
                        const controlsEl = document.getElementById('seasonalPaginationControls');

                        if (totalMatched === 0) {
                            if (summaryEl) summaryEl.textContent = 'Showing 0 recommendations';
                            if (noMatchEl) noMatchEl.classList.remove('hidden');
                            if (controlsEl) controlsEl.classList.add('hidden');
                        } else {
                            const startNum = startIdx + 1;
                            const endNum = Math.min(endIdx, totalMatched);
                            if (summaryEl) summaryEl.textContent = `Showing ${startNum}-${endNum} of ${totalMatched} recommendations`;
                            if (noMatchEl) noMatchEl.classList.add('hidden');
                            if (controlsEl) controlsEl.classList.remove('hidden');
                        }

                        // Update buttons
                        const prevBtn = document.getElementById('btnSeasonalPrev');
                        const nextBtn = document.getElementById('btnSeasonalNext');
                        if (prevBtn) prevBtn.disabled = (currentPage <= 1);
                        if (nextBtn) nextBtn.disabled = (currentPage >= totalPages);

                        // Build page number pills
                        const pageNumsContainer = document.getElementById('seasonalPageNumbers');
                        if (pageNumsContainer) {
                            let html = '';
                            for (let p = 1; p <= totalPages; p++) {
                                if (p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1) {
                                    const activeClass = (p === currentPage) 
                                        ? 'bg-terra-green text-white font-bold' 
                                        : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700';
                                    html += `<button type="button" onclick="goToSeasonalPage(${p})" class="w-8 h-8 rounded-xl text-xs font-mono transition-colors cursor-pointer ${activeClass}">${p}</button>`;
                                } else if (p === currentPage - 2 || p === currentPage + 2) {
                                    html += `<span class="px-1 text-slate-400 text-xs">...</span>`;
                                }
                            }
                            pageNumsContainer.innerHTML = html;
                        }
                    }

                    // Initial mount
                    document.addEventListener('DOMContentLoaded', updateSeasonalView);
                    setTimeout(updateSeasonalView, 50);
                })();
                </script>

                <!-- ========================================================================= -->
                <!-- SECTION B: CURATED MARKET EXPANSION IDEAS (NEW CATALOG OPPORTUNITIES)     -->
                <!-- ========================================================================= -->
                <div class="space-y-3 pt-4 border-t border-slate-200/80 dark:border-slate-800">
                    <div class="flex items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-terra-amber text-[20px]">lightbulb</span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Curated Market Expansion Ideas (New Seasonal Products)</h4>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-sans">
                                Trending seasonal items in Polomolok not currently in your store catalog. Stock these to capture additional seasonal demand.
                            </p>
                        </div>
                        <a href="<?= base_url('tenant/inventory') ?>" class="text-xs font-bold text-terra-green hover:underline flex items-center gap-1 shrink-0">
                            <span>+ Add to Catalog</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="seasonal-expansion-container">
                        <?php if (!empty($expansionIdeas)): ?>
                            <?php foreach ($expansionIdeas as $idea): ?>
                                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between hover:border-terra-green/40 transition-all shadow-2xs">
                                    <div>
                                        <div class="flex items-start justify-between gap-2 mb-1.5">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono"><?= esc($idea['category']) ?></span>
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-700">
                                                <?= esc($idea['demand_surge']) ?>
                                            </span>
                                        </div>
                                        <h5 class="text-xs font-bold text-slate-900 dark:text-white"><?= esc($idea['name']) ?></h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed"><?= esc($idea['reason']) ?></p>
                                    </div>
                                    <div class="mt-3 pt-2.5 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white font-mono">₱<?= number_format((float) $idea['est_price'], 2) ?> <span class="text-[10px] font-normal text-slate-400">sugg.</span></span>
                                        <a href="<?= base_url('tenant/inventory') ?>" class="text-xs font-bold text-terra-green hover:underline flex items-center gap-0.5">
                                            <span>Stock Item</span>
                                            <span class="material-symbols-outlined text-[13px]">add</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: 📦 INVENTORY REPLENISHMENT INTELLIGENCE                            -->
    <!-- ========================================================================= -->
    <div id="tab-panel-replenishment" class="analytics-tab-panel space-y-5 hidden">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200/80 dark:border-slate-800 space-y-5">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 font-display">
                        <span class="material-symbols-outlined text-terra-green text-[22px]">inventory</span>
                        <span>Automated Stockout Risk &amp; Replenishment Recommendations</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-sans mt-0.5">
                        Dynamic run-rate safety stock calculations based on lead times, sales velocity, and seasonal risk.
                    </p>
                </div>

                <div class="flex items-center gap-3 text-xs shrink-0">
                    <span class="inline-flex items-center gap-1 font-bold text-rose-600 dark:text-rose-400">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>Critical</span>
                    </span>
                    <span class="inline-flex items-center gap-1 font-bold text-amber-600 dark:text-amber-400">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>High Urgency</span>
                    </span>
                    <span class="inline-flex items-center gap-1 font-bold text-terra-green">
                        <span class="w-2 h-2 rounded-full bg-terra-green"></span>
                        <span>Healthy</span>
                    </span>
                </div>
            </div>

            <!-- Product Recommendations Table -->
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden bg-white dark:bg-slate-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[750px]" id="seasonal-recommendations-table">
                        <thead class="bg-slate-50 dark:bg-slate-800/70 text-[11px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-3">Product Name &amp; Category</th>
                                <th class="px-4 py-3 text-center">Stock vs Target Buffer</th>
                                <th class="px-4 py-3 text-center">Calculated Reorder</th>
                                <th class="px-4 py-3 text-center">Urgency Status</th>
                                <th class="px-4 py-3 text-right">Protected Revenue</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs" id="seasonal-table-body">
                            <?php if (!empty($recommendations)): ?>
                                <?php foreach ($recommendations as $idx => $r): ?>
                                    <?php
                                    $urgencyClass = match ($r['urgency']) {
                                        'critical' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300',
                                        'high'     => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300',
                                        'moderate' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/40 dark:text-blue-300',
                                        default    => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300',
                                    };
                                    $barColor = match ($r['urgency']) {
                                        'critical' => 'bg-rose-500',
                                        'high'     => 'bg-amber-500',
                                        'moderate' => 'bg-blue-500',
                                        default    => 'bg-terra-green',
                                    };
                                    ?>
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group">
                                        <!-- Product Info -->
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                                                    <?php if (!empty($r['image_url'])): ?>
                                                        <img src="<?= esc($r['image_url']) ?>" alt="<?= esc($r['name']) ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <span class="material-symbols-outlined text-[20px] text-terra-green">inventory_2</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-800 dark:text-slate-100 text-xs block truncate max-w-xs" title="<?= esc($r['name']) ?>">
                                                        <?= esc($r['name']) ?>
                                                    </span>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium"><?= esc($r['category']) ?></span>
                                                        <span class="text-[10px] text-slate-300 dark:text-slate-600">&bull;</span>
                                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"><?= esc($r['sku']) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Stock vs Buffer -->
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="inline-block text-left w-36">
                                                <div class="flex items-center justify-between text-[11px] mb-1 font-mono">
                                                    <span class="font-extrabold text-slate-800 dark:text-slate-200"><?= $r['current_stock'] ?> in stock</span>
                                                    <span class="text-slate-500 dark:text-slate-400 text-[10px]"><?= $r['target_buffer'] ?> buf</span>
                                                </div>
                                                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full <?= $barColor ?> transition-all duration-300" style="width: <?= $r['stock_pct'] ?>%"></div>
                                                </div>
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 block">
                                                    <?php if ($r['current_stock'] <= 0): ?>
                                                        <strong class="text-rose-600 font-bold">Depleted</strong>
                                                    <?php else: ?>
                                                        ~<?= $r['days_remaining'] ?> days remaining
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Restock Order -->
                                        <td class="px-4 py-3.5 text-center">
                                            <?php if ($r['restock_units'] > 0): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-terra-green/10 text-terra-green border border-terra-green/20 font-mono">
                                                    <span class="material-symbols-outlined text-[14px]">add</span>
                                                    <span><?= $r['restock_units'] ?> pcs</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30">
                                                    <span class="material-symbols-outlined text-[13px]">check</span>
                                                    <span>Optimal</span>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Urgency Status -->
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wider <?= $urgencyClass ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $r['urgency'] === 'critical' ? 'bg-rose-500 animate-pulse' : ($r['urgency'] === 'high' ? 'bg-amber-500' : 'bg-terra-green') ?>"></span>
                                                <span><?= esc(humanize_status($r['urgency'])) ?></span>
                                            </span>
                                        </td>

                                        <!-- Est Revenue Uplift -->
                                        <td class="px-4 py-3.5 text-right font-bold text-slate-900 dark:text-white font-mono">
                                            ₱<?= number_format((float) $r['est_revenue_uplift'], 2) ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-5 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <?php if ($r['restock_units'] > 0): ?>
                                                    <button type="button"
                                                            onclick="quickRestockProduct(<?= $r['id'] ?>, <?= $r['restock_units'] ?>, this)"
                                                            class="px-2.5 py-1 rounded-xl bg-terra-green hover:bg-terra-green-dark text-white text-[11px] font-bold inline-flex items-center gap-1 shadow-2xs transition-all active:scale-95 cursor-pointer"
                                                            title="Instantly Restock <?= $r['restock_units'] ?> units">
                                                        <span class="material-symbols-outlined text-[14px]">bolt</span>
                                                        <span>+<?= $r['restock_units'] ?></span>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button"
                                                        onclick="toggleAiReason(<?= $idx ?>)"
                                                        class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-terra-green transition-colors"
                                                        title="View AI Strategic Rationale">
                                                    <span class="material-symbols-outlined text-[18px]">psychology</span>
                                                </button>
                                                <a href="<?= base_url('tenant/inventory') ?>"
                                                   class="p-1.5 rounded-xl hover:bg-terra-green/10 text-terra-green transition-colors"
                                                   title="Manage Stock in Inventory">
                                                    <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Hidden AI Strategic Rationale Drawer -->
                                    <tr id="ai-reason-row-<?= $idx ?>" class="hidden bg-emerald-50/40 dark:bg-emerald-950/20 border-b border-emerald-100 dark:border-emerald-900/30">
                                        <td colspan="6" class="px-5 py-3.5 text-xs text-slate-700 dark:text-slate-300">
                                            <div class="flex items-start gap-2.5 max-w-3xl">
                                                <span class="material-symbols-outlined text-terra-green text-[18px] shrink-0 mt-0.5">smart_toy</span>
                                                <div class="space-y-1">
                                                    <span class="font-bold text-terra-green text-[11px] uppercase tracking-wider block">AI Operational Rationale:</span>
                                                    <p class="leading-relaxed font-sans text-slate-600 dark:text-slate-300"><?= esc($r['rationale'] ?? ($r['ai_rationale'] ?? 'Replenish inventory to maintain safe run-rate buffer.')) ?></p>
                                                    <div class="flex items-center gap-4 text-[10px] text-slate-400 mt-1 font-mono">
                                                        <span>ADS Run Rate: <?= esc($r['sales_velocity'] ?? '1.0') ?> units/day</span>
                                                        <span>&bull;</span>
                                                        <span>Lead Time Buffer: 30 Days</span>
                                                        <span>&bull;</span>
                                                        <span>Seasonal Surge: <?= esc($r['urgency'] ?? 'moderate') ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <span class="material-symbols-outlined text-4xl text-terra-green">task_alt</span>
                                            <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">Inventory Safety Stock Optimal</p>
                                            <p class="text-xs text-slate-500 max-w-sm">No items in your catalog require immediate emergency restocking.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: ⭐ RATINGS, REVIEWS & SENTIMENT                                    -->
    <!-- ========================================================================= -->
    <div id="tab-panel-reviews" class="analytics-tab-panel space-y-5 hidden">
        
        <!-- Ratings Overview Bento Header -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Store Average Star Rating Card -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">Overall Store Rating</span>
                    <div class="flex items-baseline gap-3">
                        <span class="text-4xl font-black text-slate-900 dark:text-white font-mono"><?= number_format($shop_avg_rating, 1) ?></span>
                        <div class="flex items-center text-amber-500 text-lg">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="material-symbols-outlined text-[20px] <?= $i <= round($shop_avg_rating) ? 'fill-icon' : '' ?>">star</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Based on <?= number_format((int) $total_reviews_count) ?> verified customer reviews</p>
                </div>
                
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Merchant Status</span>
                    <span class="inline-flex items-center gap-1 font-bold text-terra-green">
                        <span class="material-symbols-outlined text-[15px]">verified</span>
                        <span>Polomolok Certified</span>
                    </span>
                </div>
            </div>

            <!-- Customer Satisfaction (CSAT) -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">Satisfaction Index (CSAT)</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900 dark:text-white font-mono"><?= number_format($csat_score, 1) ?>%</span>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">Positive</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Proportion of 4-star and 5-star customer ratings</p>
                </div>

                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden mt-4">
                    <div class="bg-terra-green h-full rounded-full transition-all duration-500" style="width: <?= min(100, max(0, $csat_score)) ?>%"></div>
                </div>
            </div>

            <!-- 5-Star Breakdown Mini-Chart -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">Star Breakdown</span>
                <div class="space-y-1.5">
                    <?php for ($star = 5; $star >= 1; $star--): ?>
                        <?php
                        $cnt = $star_counts[$star] ?? 0;
                        $pct = $total_reviews_count > 0 ? round(($cnt / $total_reviews_count) * 100) : 0;
                        ?>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-300 w-6 flex items-center gap-0.5">
                                <span><?= $star ?></span>
                                <span class="material-symbols-outlined text-[13px] text-amber-500 fill-icon">star</span>
                            </span>
                            <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-400 h-full rounded-full" style="width: <?= $pct ?>%"></div>
                            </div>
                            <span class="text-[11px] text-slate-400 font-mono w-8 text-right"><?= $cnt ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

        </div>

        <!-- Most Rated Products Leaderboard Grid -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 font-display">
                        <span class="material-symbols-outlined text-amber-500 text-[20px] fill-icon">military_tech</span>
                        <span>Most Rated Products</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-sans">Ranked by total customer review volume and positive average ratings.</p>
                </div>
                <a href="<?= base_url('tenant/inventory') ?>" class="text-xs font-bold text-terra-green hover:underline flex items-center gap-1">
                    <span>Manage Products</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <?php if (!empty($most_rated_products)): ?>
                    <?php foreach ($most_rated_products as $mp): ?>
                        <?php
                        $pImg = product_image_url($mp['image_url'] ?? null);
                        $pAvg = !empty($mp['rating_average']) ? (float) $mp['rating_average'] : 5.0;
                        $pCnt = (int) ($mp['rating_count'] ?? 0);
                        ?>
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/60 flex flex-col justify-between hover:border-terra-green/40 transition-all">
                            <div>
                                <div class="w-full aspect-video rounded-xl bg-white dark:bg-slate-800 overflow-hidden mb-2.5 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700/50">
                                    <img src="<?= esc($pImg) ?>" alt="<?= esc($mp['name']) ?>" class="w-full h-full object-contain">
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1" title="<?= esc($mp['name']) ?>">
                                    <?= esc($mp['name']) ?>
                                </h4>
                                <span class="text-[10px] text-slate-500 block truncate"><?= esc($mp['category_name'] ?? 'General Merchandise') ?></span>
                            </div>

                            <div class="mt-3 pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs font-mono">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-amber-500 fill-icon">star</span>
                                    <span class="font-black text-slate-900 dark:text-white"><?= number_format($pAvg, 1) ?></span>
                                    <span class="text-[10px] text-slate-400 font-normal">(<?= number_format($pCnt) ?>)</span>
                                </div>
                                <span class="font-extrabold text-terra-green">₱<?= number_format((float) $mp['price'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full py-8 text-center text-slate-400 text-xs">No rated products recorded yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Live Verified Customer Comments Feed -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-xs border border-slate-200/80 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between gap-3 pb-2 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-terra-green text-[22px]">reviews</span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Customer Comments &amp; Review Telemetry</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-sans">Direct feedback submitted by verified buyers across Polomolok.</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-500"><?= count($reviews) ?> Recent Reviews</span>
            </div>

            <?php if (!empty($reviews)): ?>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($reviews as $rev): ?>
                        <?php
                        $userName = trim(($rev['first_name'] ?? '') . ' ' . ($rev['last_name'] ?? ''));
                        if (empty($userName)) {
                            $userName = 'Verified Customer';
                        }
                        $rRating = (int) ($rev['rating'] ?? 5);
                        ?>
                        <div class="py-4 flex flex-col sm:flex-row items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-terra-green/10 text-terra-green font-extrabold text-sm flex items-center justify-center shrink-0 border border-terra-green/20">
                                    <?= esc(strtoupper(substr($userName, 0, 1))) ?>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white"><?= esc($userName) ?></span>
                                        <span class="inline-flex items-center gap-0.5 text-[9.5px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.2 rounded-full border border-emerald-200/50">
                                            <span class="material-symbols-outlined text-[11px]">verified</span>
                                            <span>Verified Purchase</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-amber-500">
                                        <div class="flex">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <span class="material-symbols-outlined text-[15px] <?= $s <= $rRating ? 'fill-icon' : '' ?>">star</span>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-slate-400 text-[10px]">&bull;</span>
                                        <span class="text-slate-400 font-mono text-[11px]"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-sans pt-1">
                                        &ldquo;<?= esc(!empty($rev['comment']) ? $rev['comment'] : 'No written comment provided with this rating.') ?>&rdquo;
                                    </p>
                                </div>
                            </div>

                            <?php if (!empty($rev['product_name'])): ?>
                                <div class="self-end sm:self-start shrink-0 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center gap-2 max-w-xs">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">shopping_bag</span>
                                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 truncate" title="<?= esc($rev['product_name']) ?>">
                                        <?= esc($rev['product_name']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="py-12 text-center text-slate-400 text-xs">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">rate_review</span>
                    <p class="font-bold text-slate-700 dark:text-slate-300">No customer reviews yet</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Reviews submitted by verified buyers on completed orders will appear here automatically.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js CDN with fail-safe fallback -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// -------------------------------------------------------------
// Interactive Navigation Tab Controller with Hash Persistence
// -------------------------------------------------------------
function activateTab(tabKey) {
    if (!tabKey) return;

    // Hide all tab panels
    document.querySelectorAll('.analytics-tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });

    // Reveal targeted panel
    const targetPanel = document.getElementById('tab-panel-' + tabKey);
    if (targetPanel) {
        targetPanel.classList.remove('hidden');
    }

    // Update navigation tab button styles
    document.querySelectorAll('.analytics-nav-tab').forEach(btn => {
        const isTarget = btn.getAttribute('data-tab-target') === tabKey;
        if (isTarget) {
            btn.className = 'analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-bold transition-all bg-terra-green text-white shadow-xs';
            btn.setAttribute('aria-selected', 'true');
        } else {
            btn.className = 'analytics-nav-tab inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all';
            btn.setAttribute('aria-selected', 'false');
        }
    });

    // Sync URL hash without triggering scroll jump
    history.replaceState(null, null, '#' + tabKey);

    // If switching to revenue tab, trigger chart resize so canvas calculates proper aspect ratio
    if (tabKey === 'revenue' && window._revenueChart) {
        setTimeout(() => {
            window._revenueChart.resize();
        }, 50);
    }
}

// Bind tab clicks
document.querySelectorAll('.analytics-nav-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        const target = this.getAttribute('data-tab-target');
        activateTab(target);
    });
});

// Auto-activate tab from URL hash on page load
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash.replace('#', '');
    const validTabs = ['scorecard', 'revenue', 'forecasting', 'replenishment', 'reviews'];
    if (hash && validTabs.includes(hash)) {
        activateTab(hash);
    }
});

// Toggle AI Strategic Rationale drawer
function toggleAiReason(idx) {
    const row = document.getElementById('ai-reason-row-' + idx);
    if (row) {
        row.classList.toggle('hidden');
    }
}

// -------------------------------------------------------------
// Interactive AI Seasonal Switcher (AJAX)
// -------------------------------------------------------------
(function () {
    const seasonalEndpoint = '<?= site_url('tenant/analytics/seasonal') ?>';
    const inventoryBaseUrl = '<?= site_url('tenant/inventory') ?>';
    const tabButtons = document.querySelectorAll('.season-tab-btn');
    const loadingOverlay = document.getElementById('seasonal-loading-overlay');
    const tableBody = document.getElementById('seasonal-table-body');
    const expansionContainer = document.getElementById('seasonal-expansion-container');

    const hubSeasonIcon = document.getElementById('hub-season-icon');
    const hubSeasonTitle = document.getElementById('hub-season-title');
    const hubSeasonDesc = document.getElementById('hub-season-description');
    const hubSeasonMultiplier = document.getElementById('hub-season-multiplier');
    const hubSeasonPeriod = document.getElementById('hub-season-period');
    const activeStatusPill = document.getElementById('active-season-status-pill');
    const activeStatusText = document.getElementById('active-season-status-text');

    const metricSurge = document.getElementById('metric-surge');
    const metricCritical = document.getElementById('metric-critical');
    const metricRestockCount = document.getElementById('metric-restock-count');
    const metricOppRevenue = document.getElementById('metric-opp-revenue');

    function formatMoney(v) {
        return '₱' + Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    let currentActiveSeason = '<?= esc($activeKey) ?>';
    const restockContainer = document.getElementById('seasonal-restock-cards-container');

    window.refreshLiveForecast = async function () {
        const spinner = document.getElementById('refresh-spinner-icon');
        if (spinner) spinner.classList.add('animate-spin');
        try {
            await switchSeason(currentActiveSeason, null, true);
        } finally {
            if (spinner) {
                setTimeout(() => spinner.classList.remove('animate-spin'), 400);
            }
        }
    };

    window.quickRestockProduct = async function (productId, qty, btnEl) {
        if (!productId || qty <= 0) return;
        const originalContent = btnEl.innerHTML;
        btnEl.disabled = true;
        btnEl.innerHTML = '<span class="material-symbols-outlined text-[15px] animate-spin">progress_activity</span><span>Updating...</span>';

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('delta', qty);

        try {
            const resp = await fetch('<?= base_url('tenant/products/adjust-stock') ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            if (data.success) {
                btnEl.innerHTML = '<span class="material-symbols-outlined text-[15px]">check</span><span>Restocked!</span>';
                btnEl.classList.remove('bg-terra-green', 'hover:bg-terra-green-dark');
                btnEl.classList.add('bg-emerald-600', 'text-white');

                // Trigger real-time recalculation
                setTimeout(() => {
                    refreshLiveForecast();
                }, 400);
            } else {
                alert(data.error || 'Failed to update stock.');
                btnEl.disabled = false;
                btnEl.innerHTML = originalContent;
            }
        } catch (err) {
            alert('Network error while updating stock.');
            btnEl.disabled = false;
            btnEl.innerHTML = originalContent;
        }
    };

    async function switchSeason(seasonKey, clickedBtn, forceRefresh = false) {
        if (!seasonKey) return;
        currentActiveSeason = seasonKey;

        tabButtons.forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-900', 'shadow-md', 'ring-2', 'ring-terra-green/80', 'scale-102');
            btn.classList.add('bg-white/10', 'text-white', 'border', 'border-white/10');
            const icon = btn.querySelector('.material-symbols-outlined');
            if (icon) {
                icon.classList.remove('text-terra-green');
                icon.classList.add('text-slate-300');
            }
        });

        if (clickedBtn) {
            clickedBtn.classList.remove('bg-white/10', 'text-white', 'border', 'border-white/10');
            clickedBtn.classList.add('bg-white', 'text-slate-900', 'shadow-md', 'ring-2', 'ring-terra-green/80', 'scale-102');
            const icon = clickedBtn.querySelector('.material-symbols-outlined');
            if (icon) {
                icon.classList.remove('text-slate-300');
                icon.classList.add('text-terra-green');
            }
        }

        if (loadingOverlay) loadingOverlay.classList.remove('hidden');

        try {
            const refreshParam = forceRefresh ? '&refresh=1' : '';
            const res = await fetch(`${seasonalEndpoint}?season=${encodeURIComponent(seasonKey)}${refreshParam}&_t=${Date.now()}`, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();

            if (!data || !data.season) {
                throw new Error('Invalid seasonal payload');
            }

            const s = data.season;
            if (hubSeasonIcon) hubSeasonIcon.textContent = s.icon || 'celebration';
            if (hubSeasonTitle) hubSeasonTitle.textContent = s.name || s.short_name;
            if (hubSeasonDesc) hubSeasonDesc.textContent = s.description || '';
            if (hubSeasonMultiplier) hubSeasonMultiplier.textContent = (Number(s.velocity_multiplier) || 2.0).toFixed(1) + 'x Velocity';
            if (hubSeasonPeriod) hubSeasonPeriod.textContent = s.period_label || '';

            const computedTimeEl = document.getElementById('forecast-computed-time');
            if (computedTimeEl && data.computed_at) {
                computedTimeEl.innerHTML = `Calculated from live stock &bull; ${escapeHtml(data.computed_at)}`;
            }

            const isActive = !!data.is_currently_active;
            if (activeStatusPill) {
                if (isActive) {
                    activeStatusPill.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-terra-green/20 text-emerald-300 border border-terra-green/40';
                    activeStatusPill.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-terra-green animate-pulse"></span><span>Active Calendar Cycle</span>';
                } else {
                    activeStatusPill.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40';
                    activeStatusPill.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span><span>Simulation Mode</span>';
                }
            }

            const cohereBox = document.getElementById('cohere-advisory-box');
            const cohereSummaryEl = document.getElementById('cohere-executive-summary');
            if (data.cohere_summary) {
                if (cohereSummaryEl) cohereSummaryEl.textContent = data.cohere_summary;
                if (cohereBox) cohereBox.classList.remove('hidden');
            } else {
                if (cohereBox) cohereBox.classList.add('hidden');
            }

            const mult = Number(s.velocity_multiplier) || 2.0;
            if (metricSurge) metricSurge.textContent = '+' + Math.round((mult - 1) * 100) + '%';
            if (metricCritical) metricCritical.textContent = (data.critical_stockout_count || 0) + ' items';
            if (metricRestockCount) metricRestockCount.textContent = (data.items_needing_restock || 0) + ' items';
            if (metricOppRevenue) metricOppRevenue.textContent = formatMoney(data.est_seasonal_opp_revenue);

            // Render Current Inventory Restock Cards (Tab 3)
            if (restockContainer) {
                const recs = data.recommendations || [];
                if (recs.length === 0) {
                    restockContainer.innerHTML = `
                        <div class="col-span-full p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 text-center text-slate-500 dark:text-slate-400 text-xs">
                            No active products found in this store's inventory.
                        </div>`;
                } else {
                    let cardsHtml = '';
                    recs.forEach(r => {
                        let urgencyClass = 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300';
                        let barColor = 'bg-terra-green';
                        if (r.urgency === 'critical') {
                            urgencyClass = 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300';
                            barColor = 'bg-rose-500';
                        } else if (r.urgency === 'high') {
                            urgencyClass = 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300';
                            barColor = 'bg-amber-500';
                        } else if (r.urgency === 'moderate') {
                            urgencyClass = 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/40 dark:text-blue-300';
                            barColor = 'bg-blue-500';
                        }

                        const imageEl = r.image_url
                            ? `<img src="${escapeHtml(r.image_url)}" alt="${escapeHtml(r.name)}" class="w-full h-full object-cover">`
                            : `<span class="material-symbols-outlined text-[24px] text-terra-green">inventory_2</span>`;

                        let actionBtnHtml = '';
                        if (r.restock_units > 0) {
                            actionBtnHtml = `
                                <button type="button" 
                                        onclick="quickRestockProduct(${r.id}, ${r.restock_units}, this)"
                                        class="w-full py-2 px-3 rounded-xl bg-terra-green hover:bg-terra-green-dark text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm active:scale-98 transition-all cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">bolt</span>
                                    <span>Quick Restock +${r.restock_units} pcs</span>
                                </button>`;
                        } else {
                            actionBtnHtml = `
                                <div class="w-full flex items-center justify-between px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        <span>Stock Level Optimal</span>
                                    </span>
                                    <button type="button" 
                                            onclick="quickRestockProduct(${r.id}, 10, this)"
                                            class="text-[11px] text-terra-green hover:underline font-mono cursor-pointer">
                                        +10 pcs
                                    </button>
                                </div>`;
                        }

                        cardsHtml += `
                            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between hover:border-terra-green/50 transition-all shadow-2xs group" id="restock-card-${r.id}">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono truncate max-w-[130px]">${escapeHtml(r.category)}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border uppercase tracking-wider shrink-0 ${urgencyClass}">
                                            ${escapeHtml(r.urgency)}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-700 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200 dark:border-slate-600 shadow-2xs">
                                            ${imageEl}
                                        </div>
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate" title="${escapeHtml(r.name)}">${escapeHtml(r.name)}</h5>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 font-mono">${formatMoney(r.price)}</span>
                                                <span class="text-[10px] text-slate-400 font-mono">&bull; ${escapeHtml(r.sku)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60 space-y-1.5">
                                        <div class="flex items-center justify-between text-[11px] font-mono">
                                            <span class="font-bold text-slate-900 dark:text-slate-100" id="card-stock-${r.id}">${r.current_stock} in stock</span>
                                            <span class="text-slate-500 dark:text-slate-400 text-[10px]">Buffer Target: ${r.target_buffer}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full ${barColor} transition-all duration-300" style="width: ${r.stock_pct}%"></div>
                                        </div>
                                        <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-0.5">
                                            <span>Runway: ~${r.days_remaining} days</span>
                                            <span>Threshold: ${r.threshold}</span>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed font-sans line-clamp-2">
                                        ${escapeHtml(r.rationale || r.ai_rationale || 'Replenish stock to maintain uninterrupted fulfillment.')}
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700/80 flex items-center justify-between gap-2">
                                    ${actionBtnHtml}
                                </div>
                            </div>
                        `;
                    });
                    restockContainer.innerHTML = cardsHtml;
                }
            }

            // Render Replenishment Table (Tab 4)
            if (tableBody) {
                const recs = data.recommendations || [];
                if (recs.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-terra-green">task_alt</span>
                                    <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">Inventory Fully Prepared</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm">No items in your catalog require urgent restock for the ${escapeHtml(s.short_name)} cycle.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';
                    recs.forEach((r, idx) => {
                        let urgencyClass = 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300';
                        let barColor = 'bg-terra-green';
                        let dotColor = 'bg-terra-green';
                        if (r.urgency === 'critical') {
                            urgencyClass = 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300';
                            barColor = 'bg-rose-500';
                            dotColor = 'bg-rose-500 animate-pulse';
                        } else if (r.urgency === 'high') {
                            urgencyClass = 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300';
                            barColor = 'bg-amber-500';
                            dotColor = 'bg-amber-500';
                        } else if (r.urgency === 'moderate') {
                            urgencyClass = 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/40 dark:text-blue-300';
                            barColor = 'bg-blue-500';
                            dotColor = 'bg-blue-500';
                        }

                        const stockNote = (r.current_stock <= 0)
                            ? '<strong class="text-rose-600 font-bold">Depleted</strong>'
                            : `~${r.days_remaining} days remaining`;

                        const restockPill = (r.restock_units > 0)
                            ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-terra-green/10 text-terra-green border border-terra-green/20 font-mono"><span class="material-symbols-outlined text-[14px]">add</span><span>${r.restock_units} pcs</span></span>`
                            : `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30"><span class="material-symbols-outlined text-[13px]">check</span><span>Optimal</span></span>`;

                        const imageEl = r.image_url
                            ? `<img src="${escapeHtml(r.image_url)}" alt="${escapeHtml(r.name)}" class="w-full h-full object-cover">`
                            : `<span class="material-symbols-outlined text-[20px] text-terra-green">inventory_2</span>`;

                        const restockActionBtn = (r.restock_units > 0)
                            ? `<button type="button" onclick="quickRestockProduct(${r.id}, ${r.restock_units}, this)" class="px-2.5 py-1 rounded-xl bg-terra-green hover:bg-terra-green-dark text-white text-[11px] font-bold inline-flex items-center gap-1 shadow-2xs transition-all active:scale-95 cursor-pointer" title="Instantly Restock ${r.restock_units} units"><span class="material-symbols-outlined text-[14px]">bolt</span><span>+${r.restock_units}</span></button>`
                            : '';

                        html += `
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                                            ${imageEl}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-800 dark:text-slate-100 text-xs block truncate max-w-xs" title="${escapeHtml(r.name)}">${escapeHtml(r.name)}</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">${escapeHtml(r.category)}</span>
                                                <span class="text-[10px] text-slate-300 dark:text-slate-600">&bull;</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">${escapeHtml(r.sku)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-block text-left w-36">
                                        <div class="flex items-center justify-between text-[11px] mb-1 font-mono">
                                            <span class="font-extrabold text-slate-800 dark:text-slate-200">${r.current_stock} in stock</span>
                                            <span class="text-slate-500 dark:text-slate-400 text-[10px]">${r.target_buffer} buf</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full ${barColor} transition-all duration-300" style="width: ${r.stock_pct}%"></div>
                                        </div>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 block">${stockNote}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">${restockPill}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wider ${urgencyClass}">
                                        <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>
                                        <span>${escapeHtml(r.urgency)}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-slate-900 dark:text-white font-mono">${formatMoney(r.est_revenue_uplift)}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        ${restockActionBtn}
                                        <button type="button" onclick="toggleAiReason(${idx})" class="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-terra-green transition-colors cursor-pointer" title="View AI Strategic Rationale">
                                            <span class="material-symbols-outlined text-[18px]">psychology</span>
                                        </button>
                                        <a href="${inventoryBaseUrl}" class="p-1.5 rounded-xl hover:bg-terra-green/10 text-terra-green transition-colors" title="Manage Stock in Inventory">
                                            <span class="material-symbols-outlined text-[18px]">edit_note</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr id="ai-reason-row-${idx}" class="hidden bg-emerald-50/40 dark:bg-emerald-950/20 border-b border-emerald-100 dark:border-emerald-900/30">
                                <td colspan="6" class="px-5 py-3.5 text-xs text-slate-700 dark:text-slate-300">
                                    <div class="flex items-start gap-2.5 max-w-3xl">
                                        <span class="material-symbols-outlined text-terra-green text-[18px] shrink-0 mt-0.5">smart_toy</span>
                                        <div class="space-y-1">
                                            <span class="font-bold text-terra-green text-[11px] uppercase tracking-wider block">AI Operational Rationale:</span>
                                            <p class="leading-relaxed font-sans text-slate-600 dark:text-slate-300">${escapeHtml(r.rationale)}</p>
                                            <div class="flex items-center gap-4 text-[10px] text-slate-400 mt-1 font-mono">
                                                <span>ADS Velocity: ${r.sales_velocity} units/day</span>
                                                <span>&bull;</span>
                                                <span>Buffer Target: ${r.target_buffer} pcs</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tableBody.innerHTML = html;
                }
            }

            // Render Expansion Ideas (Tab 3)
            if (expansionContainer) {
                const ideas = data.expansion_ideas || [];
                if (ideas.length === 0) {
                    expansionContainer.innerHTML = '<div class="col-span-full py-4 text-center text-slate-400 text-xs">No expansion catalog suggestions available.</div>';
                } else {
                    let html = '';
                    ideas.forEach(idea => {
                        html += `
                            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/60 flex flex-col justify-between hover:border-terra-green/40 transition-all shadow-2xs">
                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-1.5">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 font-mono">${escapeHtml(idea.category)}</span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-700">${escapeHtml(idea.demand_surge)}</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-slate-900 dark:text-white">${escapeHtml(idea.name)}</h5>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">${escapeHtml(idea.reason)}</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white font-mono">${formatMoney(idea.est_price)} <span class="text-[10px] font-normal text-slate-400">sugg.</span></span>
                                    <a href="${inventoryBaseUrl}" class="text-xs font-bold text-terra-green hover:underline flex items-center gap-0.5">
                                        <span>Stock Item</span>
                                        <span class="material-symbols-outlined text-[13px]">add</span>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    expansionContainer.innerHTML = html;
                }
            }

        } catch (err) {
            console.error('Failed to switch season:', err);
        } finally {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
        }
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const key = this.getAttribute('data-season-key');
            switchSeason(key, this);
        });
    });
})();

// -------------------------------------------------------------
// Chart.js Line Chart Controller
// -------------------------------------------------------------
(function () {
    const endpoint = '<?= site_url('tenant/analytics/data') ?>';
    const canvas = document.getElementById('revenueLineChart');
    if (!canvas) return;

    let chartInstance = null;
    let initialLabels = <?= json_encode($chart_labels) ?>;
    let initialValues = <?= json_encode($chart_values) ?>;
    let initialPrevious = <?= json_encode($chart_previous) ?>;

    function renderChart(labels, values, previousValues) {
        if (chartInstance) chartInstance.destroy();
        const ctx = canvas.getContext('2d');

        const datasets = [];
        if (previousValues && previousValues.length > 0) {
            datasets.push({
                label: 'Previous Period',
                data: previousValues,
                borderColor: '#94a3b8',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 0,
                pointHoverRadius: 4,
                tension: 0.35,
            });
        }

        datasets.push({
            label: 'Current Period',
            data: values,
            borderColor: '#4A943E',
            backgroundColor: 'rgba(74, 148, 62, 0.08)',
            fill: true,
            borderWidth: 2.5,
            pointBackgroundColor: '#4A943E',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 3.5,
            pointHoverRadius: 6,
            tension: 0.35,
        });

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.dataset.label + ': ₱' + Number(ctx.parsed.y || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        grid: { color: 'rgba(148, 163, 184, 0.15)' },
                        ticks: {
                            font: { size: 10 },
                            color: '#94a3b8',
                            callback: function (val) {
                                return '₱' + (val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val);
                            }
                        }
                    }
                }
            }
        });
        window._revenueChart = chartInstance;
    }

    renderChart(initialLabels, initialValues, initialPrevious);

    // Range Switcher Event
    document.querySelectorAll('.analytics-range-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const range = this.getAttribute('data-range');
            document.querySelectorAll('.analytics-range-btn').forEach(b => {
                b.className = 'analytics-range-btn px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700';
            });
            this.className = 'analytics-range-btn px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all bg-terra-green text-white shadow-xs';

            const loader = document.getElementById('analytics-loading');
            if (loader) loader.classList.remove('hidden');

            try {
                const res = await fetch(`${endpoint}?range=${encodeURIComponent(range)}&_t=${Date.now()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                renderChart(data.labels || [], data.values || [], data.previous_values || []);

                const currTotalEl = document.getElementById('analytics-current-total');
                if (currTotalEl) currTotalEl.textContent = '₱' + Number(data.current_total || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const prevTotalEl = document.getElementById('analytics-prev-total');
                if (prevTotalEl) prevTotalEl.textContent = '₱' + Number(data.previous_total || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            } catch (err) {
                console.error('Failed to refresh sales chart:', err);
            } finally {
                if (loader) loader.classList.add('hidden');
            }
        });
    });
})();
</script>
<?= $this->endSection() ?>