<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$baseFee     = (float) old('base_shipping_fee', $settings['base_shipping_fee'] ?? 50.00);
$includedKm  = (float) old('included_distance_km', $settings['included_distance_km'] ?? 2.00);
$perKmFee    = (float) old('additional_fee_per_km', $settings['additional_fee_per_km'] ?? 10.00);
$maxDistKm   = (float) old('max_delivery_distance_km', $settings['max_delivery_distance_km'] ?? 25.00);
$simulateKm  = !empty($settings['simulate_distance_km']) && filter_var($settings['simulate_distance_km'], FILTER_VALIDATE_BOOLEAN);
$overrideKm  = (float) old('override_distance_km', $settings['override_distance_km'] ?? 0);
?>

<div class="space-y-6 pb-10">

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </span>
                <span class="font-medium text-xs sm:text-sm"><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 p-1">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 text-sm flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                </span>
                <span class="font-medium text-xs sm:text-sm"><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200 p-1">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Top Summary KPI Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Base Shipping Fee</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1">₱<?= number_format($baseFee, 2) ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Fixed up to <span class="font-semibold text-slate-700 dark:text-slate-200"><?= number_format($includedKm, 1) ?> km</span> limit</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200/60 dark:border-emerald-800/50">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Additional Shipping Fee</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1">+₱<?= number_format($perKmFee, 2) ?><span class="text-xs font-semibold text-slate-400"> / <?= number_format($includedKm, 1) ?> km</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Auto-added when exceeding <?= number_format($includedKm, 1) ?> km</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-200/60 dark:border-sky-800/50">
                <span class="material-symbols-outlined text-[20px]">add_road</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Polomolok Max Radius</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1"><?= number_format($maxDistKm, 1) ?> <span class="text-xs font-semibold text-slate-400">km</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Around Polomolok only (Pick-up beyond)</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200/60 dark:border-amber-800/50">
                <span class="material-symbols-outlined text-[20px]">radar</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Routing Engine Status</p>
                <div class="flex items-center gap-1.5 mt-1.5">
                    <span class="w-2 h-2 rounded-full <?= $hasMapsKey ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' ?>"></span>
                    <p class="text-sm font-bold text-slate-900 dark:text-white"><?= $hasMapsKey ? 'Google Routes API v2' : 'Road Engine Fallback' ?></p>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"><?= (int) $pinnedShopsCount ?> / <?= (int) $totalActiveShops ?> active shops pinned</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-200/60 dark:border-indigo-800/50">
                <span class="material-symbols-outlined text-[20px]">route</span>
            </div>
        </div>
    </div>

    <!-- Main Configuration + Live Distance Simulator Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Shipping Rate Configuration Form -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Polomolok KM-Limit Shipping Rates</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Set the KM limit where the base shipping fee stays fixed, and automatically add the additional shipping fee when distance exceeds that limit around Polomolok only.</p>
                    </div>
                </div>
            </div>

            <form action="<?= base_url('admin/shipping-settings/save') ?>" method="post" id="shippingSettingsForm" class="p-6 space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- 1. Base Shipping Fee -->
                    <div class="space-y-1.5">
                        <label for="inputBaseFee" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Base Shipping Fee (PHP)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 font-mono">₱</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="5000"
                                required
                                id="inputBaseFee"
                                name="base_shipping_fee"
                                value="<?= esc(number_format($baseFee, 2, '.', '')) ?>"
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-sm font-bold font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green transition-all"
                            >
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Flat starting fee charged for doorstep delivery. Does not increase while within the KM limit.
                        </p>
                    </div>

                    <!-- 2. Shipping Fee KM Limit (km) -->
                    <div class="space-y-1.5">
                        <label for="inputIncludedKm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Shipping Fee KM Limit (km)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.1"
                                min="0.5"
                                max="100"
                                required
                                id="inputIncludedKm"
                                name="included_distance_km"
                                value="<?= esc(number_format($includedKm, 2, '.', '')) ?>"
                                class="w-full pl-4 pr-12 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-sm font-bold font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green transition-all"
                            >
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 font-mono">km</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Distance limit (e.g. 2 km) where the shipping fee will not increase. Exceeding this limit adds the additional fee per step.
                        </p>
                    </div>

                    <!-- 3. Additional Shipping Fee -->
                    <div class="space-y-1.5">
                        <label for="inputPerKmFee" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Additional Shipping Fee (PHP)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 font-mono">₱</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="1000"
                                required
                                id="inputPerKmFee"
                                name="additional_fee_per_km"
                                value="<?= esc(number_format($perKmFee, 2, '.', '')) ?>"
                                class="w-full pl-8 pr-14 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-sm font-bold font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green transition-all"
                            >
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 font-mono">/ step</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Automatically added per KM-limit bracket whenever the delivery distance exceeds the KM limit around Polomolok.
                        </p>
                    </div>

                    <!-- 4. Maximum Delivery Distance (km) -->
                    <div class="space-y-1.5">
                        <label for="inputMaxDistKm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Maximum Delivery Distance (Polomolok)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.5"
                                min="0.5"
                                max="200"
                                required
                                id="inputMaxDistKm"
                                name="max_delivery_distance_km"
                                value="<?= esc(number_format($maxDistKm, 2, '.', '')) ?>"
                                class="w-full pl-4 pr-12 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/70 text-sm font-bold font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green transition-all"
                            >
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 font-mono">km</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Destinations outside Polomolok or further than this road distance cannot use Doorstep Delivery (Store Pick-up remains available).
                        </p>
                    </div>
                </div>

                <!-- Formula Explanation Callout -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex items-start gap-3">
                    <span class="material-symbols-outlined text-terra-green text-[20px] shrink-0 mt-0.5">functions</span>
                    <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                        <p class="font-bold text-slate-900 dark:text-white">How Checkout Calculates Shipping Fee (Around Polomolok Only)</p>
                        <p class="font-mono text-[11px] bg-white dark:bg-slate-900 px-2.5 py-1.5 rounded-lg border border-slate-200/70 dark:border-slate-700 inline-block">
                            If Distance ≤ KM Limit: Fee = Base Fee &nbsp;|&nbsp; If Distance &gt; KM Limit: Fee = Base Fee + ceil((Distance − KM Limit) / KM Limit) × Additional Fee
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Within the set KM limit (e.g. 2.0 km), the shipping fee does not increase. When distance exceeds the limit around Polomolok, the system automatically adds the additional shipping fee per KM-limit step without fractional decimal creep. Store Pick-up orders are always ₱0.00.
                        </p>
                    </div>
                </div>

                <!-- TEST-ONLY Simulated Distance Override -->
                <div class="p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[20px] shrink-0 mt-0.5">science</span>
                        <div class="flex-1 min-w-0">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    id="inputSimulateKm"
                                    name="simulate_distance_km"
                                    value="1"
                                    <?= $simulateKm ? 'checked' : '' ?>
                                    class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-amber-600 focus:ring-amber-500"
                                >
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Test Only &mdash; Simulate Distance Override</span>
                            </label>
                            <p class="text-[11px] text-amber-700/90 dark:text-amber-300/80 mt-1.5 leading-relaxed">
                                <strong>Disabled by default.</strong> Live checkout quotes always use real Google/OSRM route kilometers. Enable this only while testing to force every delivery quote to a fixed distance below &mdash; never leave it on in production.
                            </p>
                            <div id="simulatedKmField" class="mt-3 space-y-1.5 <?= $simulateKm ? '' : 'hidden' ?>">
                                <label for="inputOverrideKm" class="block text-[11px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Simulated Distance (km)</label>
                                <div class="relative w-40">
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0.1"
                                        max="200"
                                        id="inputOverrideKm"
                                        name="override_distance_km"
                                        value="<?= esc(number_format($overrideKm, 2, '.', '')) ?>"
                                        class="w-full pl-3 pr-10 py-2 rounded-lg border border-amber-300 dark:border-amber-800 bg-white dark:bg-slate-900 text-sm font-bold font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                                    >
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-amber-500 font-mono">km</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($simulateKm): ?>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-rose-100/70 dark:bg-rose-950/50 border border-rose-300 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-[11px] font-bold">
                            <span class="material-symbols-outlined text-[16px]">warning</span>
                            SIMULATED DISTANCE IS CURRENTLY ACTIVE &mdash; all shipping quotes are using fake km, not real routes.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Client-side validation message -->
                <div id="formValidationAlert" class="hidden p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] shrink-0">warning</span>
                    <span id="formValidationAlertText">Maximum delivery distance must be greater than or equal to the included distance.</span>
                </div>

                <div class="pt-2 border-t border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <button
                        type="button"
                        id="resetDefaultsBtn"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                        Reset Recommended Defaults
                    </button>

                    <button
                        type="submit"
                        id="saveShippingSettingsBtn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-terra-green hover:bg-[#193816] text-white text-xs font-bold shadow-sm transition-all cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Save Shipping Fee Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Interactive Live Rate Simulator -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[19px]">calculate</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Live Checkout Fee Simulator</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Preview how rates behave across Polomolok, South Cotabato</p>
                    </div>
                </div>
                <span id="simStatusBadge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Deliverable
                </span>
            </div>

            <div class="p-6 space-y-5">
                <!-- Distance Slider -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="simDistanceRange" class="text-xs font-bold text-slate-700 dark:text-slate-300">Simulated Road Distance (Polomolok)</label>
                        <span id="simDistanceLabel" class="text-base font-extrabold text-terra-green font-mono">2.0 km</span>
                    </div>
                    <input
                        type="range"
                        id="simDistanceRange"
                        min="0.5"
                        max="35"
                        step="0.5"
                        value="2.0"
                        class="w-full accent-terra-green cursor-pointer"
                    >
                    <div class="flex justify-between text-[10px] font-mono text-slate-400">
                        <span>0.5 km (Poblacion)</span>
                        <span>10 km (Outskirts)</span>
                        <span>35 km</span>
                    </div>
                </div>

                <!-- Sample Route Presets -->
                <div class="space-y-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick Route Presets (Around Polomolok Only)</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" data-km="1.4" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Poblacion (1.4 km)
                        </button>
                        <button type="button" data-km="2.0" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Magsaysay (2.0 km)
                        </button>
                        <button type="button" data-km="3.2" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Lumakil (3.2 km)
                        </button>
                        <button type="button" data-km="5.8" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Cannery Site (5.8 km)
                        </button>
                        <button type="button" data-km="9.5" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Glamang (9.5 km)
                        </button>
                        <button type="button" data-km="13.5" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Landan (13.5 km)
                        </button>
                        <button type="button" data-km="28.0" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Beyond Limit (28.0 km)
                        </button>
                    </div>
                </div>

                <!-- Breakdown Receipt Card -->
                <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/70 p-4 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Base Fee (<span id="simBreakIncluded">up to 2.0 km limit</span>)</span>
                        <span id="simBreakBase" class="font-bold font-mono text-slate-900 dark:text-white">₱50.00</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Exceeded Limit (<span id="simBreakExtraKm">Within limit — no increase</span>)</span>
                        <span id="simBreakExtraFee" class="font-bold font-mono text-slate-900 dark:text-white">₱0.00</span>
                    </div>
                    <div class="pt-2.5 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">Calculated Shipping Fee</span>
                        <span id="simTotalFee" class="text-xl font-extrabold font-mono text-terra-green">₱50.00</span>
                    </div>
                </div>

                <!-- Out of Range Warning Preview -->
                <div id="simOutOfRangeBox" class="hidden p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/70 text-amber-800 dark:text-amber-300 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">location_off</span>
                        Outside Maximum Delivery Limit
                    </p>
                    <p id="simOutOfRangeText" class="text-[11px] leading-relaxed">
                        This destination exceeds the configured maximum radius. Customers at this distance will be prompted to select Store Pick-up or choose a closer address.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    const inputBase     = document.getElementById('inputBaseFee');
    const inputIncluded = document.getElementById('inputIncludedKm');
    const inputPerKm    = document.getElementById('inputPerKmFee');
    const inputMaxDist  = document.getElementById('inputMaxDistKm');
    const simRange      = document.getElementById('simDistanceRange');
    const simDistLabel  = document.getElementById('simDistanceLabel');
    const simBreakInc   = document.getElementById('simBreakIncluded');
    const simBreakBase  = document.getElementById('simBreakBase');
    const simBreakExKm  = document.getElementById('simBreakExtraKm');
    const simBreakExFee = document.getElementById('simBreakExtraFee');
    const simTotalFee   = document.getElementById('simTotalFee');
    const simBadge      = document.getElementById('simStatusBadge');
    const simOutBox     = document.getElementById('simOutOfRangeBox');
    const formAlert     = document.getElementById('formValidationAlert');
    const saveBtn       = document.getElementById('saveShippingSettingsBtn');
    const resetBtn      = document.getElementById('resetDefaultsBtn');

    function updateSimulator() {
        const baseFee    = Math.max(0, parseFloat(inputBase.value) || 0);
        const includedKm = Math.max(0.5, parseFloat(inputIncluded.value) || 0.5);
        const perKmFee   = Math.max(0, parseFloat(inputPerKm.value) || 0);
        const maxDistKm  = Math.max(0.5, parseFloat(inputMaxDist.value) || 0.5);
        const distKm     = Math.max(0.5, parseFloat(simRange.value) || 0.5);

        // Validate maxDistKm >= includedKm
        if (maxDistKm < includedKm) {
            formAlert.classList.remove('hidden');
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-50', 'pointer-events-none');
        } else {
            formAlert.classList.add('hidden');
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'pointer-events-none');
        }

        simDistLabel.textContent = distKm.toFixed(1) + ' km';
        simBreakInc.textContent  = 'up to ' + includedKm.toFixed(1) + ' km limit';
        simBreakBase.textContent = '₱' + baseFee.toFixed(2);

        const rawExtraKm = Math.max(0, distKm - includedKm);
        const extraSteps = (distKm <= includedKm || rawExtraKm <= 0)
            ? 0
            : Math.ceil((rawExtraKm - 1e-6) / (includedKm > 0 ? includedKm : 1));
        const extraFee   = extraSteps * perKmFee;
        const totalFee   = baseFee + extraFee;

        if (extraSteps === 0) {
            simBreakExKm.textContent = 'Within ' + includedKm.toFixed(1) + ' km limit (no increase)';
        } else {
            simBreakExKm.textContent = '+' + rawExtraKm.toFixed(1) + ' km (' + extraSteps + (extraSteps === 1 ? ' step' : ' steps') + ' × ₱' + perKmFee.toFixed(2) + ')';
        }
        simBreakExFee.textContent = '₱' + extraFee.toFixed(2);
        simTotalFee.textContent   = '₱' + totalFee.toFixed(2);

        if (distKm <= maxDistKm) {
            simBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200';
            simBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Deliverable';
            simOutBox.classList.add('hidden');
            simTotalFee.className = 'text-xl font-extrabold font-mono text-terra-green';
        } else {
            simBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200';
            simBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Out of Range';
            simOutBox.classList.remove('hidden');
            simTotalFee.className = 'text-xl font-extrabold font-mono text-rose-600 line-through';
        }
    }

    [inputBase, inputIncluded, inputPerKm, inputMaxDist, simRange].forEach(el => {
        if (el) el.addEventListener('input', updateSimulator);
    });

    // Test-only simulated distance toggle visibility
    const simToggle = document.getElementById('inputSimulateKm');
    const simKmField = document.getElementById('simulatedKmField');
    if (simToggle && simKmField) {
        simToggle.addEventListener('change', function() {
            simKmField.classList.toggle('hidden', !this.checked);
        });
    }

    document.querySelectorAll('.sim-preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const km = parseFloat(this.getAttribute('data-km')) || 2.0;
            simRange.value = km;
            updateSimulator();
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            inputBase.value     = '50.00';
            inputIncluded.value = '2.00';
            inputPerKm.value    = '10.00';
            inputMaxDist.value  = '25.00';
            updateSimulator();
        });
    }

    updateSimulator();
})();
</script>

<?= $this->endSection() ?>

