<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$baseFee     = (float) old('base_shipping_fee', $settings['base_shipping_fee'] ?? 50.00);
$includedKm  = (float) old('included_distance_km', $settings['included_distance_km'] ?? 3.00);
$perKmFee    = (float) old('additional_fee_per_km', $settings['additional_fee_per_km'] ?? 10.00);
$maxDistKm   = (float) old('max_delivery_distance_km', $settings['max_delivery_distance_km'] ?? 25.00);
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
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Covers first <span class="font-semibold text-slate-700 dark:text-slate-200"><?= number_format($includedKm, 1) ?> km</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200/60 dark:border-emerald-800/50">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Additional Distance Rate</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1">+₱<?= number_format($perKmFee, 2) ?><span class="text-xs font-semibold text-slate-400"> / km</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Beyond <?= number_format($includedKm, 1) ?> km road distance</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-200/60 dark:border-sky-800/50">
                <span class="material-symbols-outlined text-[20px]">add_road</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-2xs flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Max Delivery Radius</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono mt-1"><?= number_format($maxDistKm, 1) ?> <span class="text-xs font-semibold text-slate-400">km</span></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Auto-switches beyond limit to Pick-up</p>
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
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Distance-Based Shipping Rates</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Calculated automatically via Google Maps Routes API from each merchant shop to the customer's delivery pin.</p>
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
                            Flat starting fee charged for doorstep delivery within the included kilometer threshold.
                        </p>
                    </div>

                    <!-- 2. Included Distance (km) -->
                    <div class="space-y-1.5">
                        <label for="inputIncludedKm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Included Distance (km)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
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
                            Road distance covered by the base fee before per-kilometer surcharges begin.
                        </p>
                    </div>

                    <!-- 3. Additional Fee per Kilometer -->
                    <div class="space-y-1.5">
                        <label for="inputPerKmFee" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Additional Fee per Kilometer
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
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 font-mono">/ km</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Added for each kilometer of actual road travel beyond the included distance.
                        </p>
                    </div>

                    <!-- 4. Maximum Delivery Distance (km) -->
                    <div class="space-y-1.5">
                        <label for="inputMaxDistKm" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Maximum Delivery Distance
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
                            Destinations further than this road distance cannot use Doorstep Delivery (Store Pick-up remains available).
                        </p>
                    </div>
                </div>

                <!-- Formula Explanation Callout -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex items-start gap-3">
                    <span class="material-symbols-outlined text-terra-green text-[20px] shrink-0 mt-0.5">functions</span>
                    <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                        <p class="font-bold text-slate-900 dark:text-white">How Checkout Calculates Shipping Fee</p>
                        <p class="font-mono text-[11px] bg-white dark:bg-slate-900 px-2.5 py-1.5 rounded-lg border border-slate-200/70 dark:border-slate-700 inline-block">
                            Fee = Base Fee + max(0, Road Distance − Included Distance) × Per-KM Rate
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            For multi-shop Cart checkouts, the route distance is calculated per merchant shop so each courier dispatch receives an accurate delivery fee. Store Pick-up orders are always ₱0.00.
                        </p>
                    </div>
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
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Preview how rates behave across Polomolok &amp; Tupi</p>
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
                        <label for="simDistanceRange" class="text-xs font-bold text-slate-700 dark:text-slate-300">Simulated Road Distance</label>
                        <span id="simDistanceLabel" class="text-base font-extrabold text-terra-green font-mono">4.5 km</span>
                    </div>
                    <input
                        type="range"
                        id="simDistanceRange"
                        min="0.5"
                        max="35"
                        step="0.5"
                        value="4.5"
                        class="w-full accent-terra-green cursor-pointer"
                    >
                    <div class="flex justify-between text-[10px] font-mono text-slate-400">
                        <span>0.5 km (Town Center)</span>
                        <span>15 km (Tupi)</span>
                        <span>35 km</span>
                    </div>
                </div>

                <!-- Sample Route Presets -->
                <div class="space-y-1.5">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick Route Presets (From Poblacion Hub)</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" data-km="1.4" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Poblacion (1.4 km)
                        </button>
                        <button type="button" data-km="3.2" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Magsaysay (3.2 km)
                        </button>
                        <button type="button" data-km="5.8" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Cannery Site (5.8 km)
                        </button>
                        <button type="button" data-km="9.5" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Glamang (9.5 km)
                        </button>
                        <button type="button" data-km="14.8" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Tupi Poblacion (14.8 km)
                        </button>
                        <button type="button" data-km="28.0" class="sim-preset-btn px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                            Beyond Limit (28.0 km)
                        </button>
                    </div>
                </div>

                <!-- Breakdown Receipt Card -->
                <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/70 p-4 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Base Fee (<span id="simBreakIncluded">first 3.0 km</span>)</span>
                        <span id="simBreakBase" class="font-bold font-mono text-slate-900 dark:text-white">₱50.00</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Extra Distance (<span id="simBreakExtraKm">1.5 km × ₱10.00</span>)</span>
                        <span id="simBreakExtraFee" class="font-bold font-mono text-slate-900 dark:text-white">₱15.00</span>
                    </div>
                    <div class="pt-2.5 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">Calculated Shipping Fee</span>
                        <span id="simTotalFee" class="text-xl font-extrabold font-mono text-terra-green">₱65.00</span>
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
        const includedKm = Math.max(0, parseFloat(inputIncluded.value) || 0);
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
        simBreakInc.textContent  = 'first ' + includedKm.toFixed(1) + ' km';
        simBreakBase.textContent = '₱' + baseFee.toFixed(2);

        const extraKm  = Math.max(0, distKm - includedKm);
        const extraFee = extraKm * perKmFee;
        const totalFee = baseFee + extraFee;

        simBreakExKm.textContent  = extraKm.toFixed(1) + ' km × ₱' + perKmFee.toFixed(2);
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

    document.querySelectorAll('.sim-preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const km = parseFloat(this.getAttribute('data-km')) || 3.0;
            simRange.value = km;
            updateSimulator();
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            inputBase.value     = '50.00';
            inputIncluded.value = '3.00';
            inputPerKm.value    = '10.00';
            inputMaxDist.value  = '25.00';
            updateSimulator();
        });
    }

    updateSimulator();
})();
</script>

<?= $this->endSection() ?>

