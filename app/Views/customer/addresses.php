<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<div class="flex-1 flex flex-col md:flex-row items-start w-full min-h-[calc(100vh-72px)] bg-slate-50/50 dark:bg-slate-950 transition-colors">

    <?= view('components/profile_sidebar', ['activeNav' => 'addresses']) ?>

    <!-- Content Area -->
    <main class="flex-1 w-full min-w-0 p-4 sm:p-6 md:p-8">

        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 pb-28">

            <!-- Calm Page Header -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-display font-bold text-slate-900 dark:text-white tracking-tight">Shipping Addresses</h1>
                    <?php if (!empty($addresses)): ?>
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            <?= count($addresses) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <button type="button" id="addr-add-btn" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all active:scale-95 shrink-0 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Add Address</span>
                </button>
            </div>

            <!-- Addresses Grid -->
            <div>

                <?php if (!empty($addresses)): ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <?php foreach ($addresses as $addr): ?>

                            <?php
                                $addrDefault = (int) ($addr['is_default'] ?? 0) === 1;
                                $addrLabel   = strtolower((string) ($addr['label'] ?? ''));
                                $addrIcon    = 'location_on';
                                $badgeColor  = 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300';
                                if ($addrDefault || str_contains($addrLabel, 'home')) {
                                    $addrIcon   = 'home';
                                    $badgeColor = 'bg-terra-green/10 text-terra-green dark:text-emerald-400';
                                } elseif (str_contains($addrLabel, 'office') || str_contains($addrLabel, 'work')) {
                                    $addrIcon   = 'work';
                                    $badgeColor = 'bg-amber-500/10 text-amber-700 dark:text-amber-400';
                                } elseif (str_contains($addrLabel, 'business') || str_contains($addrLabel, 'store') || str_contains($addrLabel, 'shop')) {
                                    $addrIcon   = 'storefront';
                                    $badgeColor = 'bg-amber-500/10 text-amber-700 dark:text-amber-400';
                                } elseif (str_contains($addrLabel, 'apartment') || str_contains($addrLabel, 'dorm') || str_contains($addrLabel, 'boarding')) {
                                    $addrIcon   = 'apartment';
                                    $badgeColor = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400';
                                }

                                $addrPhone   = $addr['phone'] ?? $addr['phone_number'] ?? '';
                                $addrLine1   = $addr['address_line1'] ?? '';
                                $addrLine2   = $addr['address_line2'] ?? '';
                                $addrCity    = $addr['city'] ?: 'Polomolok';
                                $addrProv    = $addr['province'] ?: 'South Cotabato';
                                $addrZip     = $addr['postal_code'] ?: ($addrCity === 'Tupi' ? '9505' : '9504');
                                $addrCountry = $addr['country'] ?: 'Philippines';
                                $hasGps      = !empty($addr['latitude']) && !empty($addr['longitude']) && (float)$addr['latitude'] != 0;
                            ?>

                            <div class="bg-white dark:bg-slate-900 border <?= $addrDefault ? 'border-terra-green/60 dark:border-terra-green/60 ring-1 ring-terra-green/15' : 'border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700' ?> rounded-2xl p-5 flex flex-col justify-between gap-4 shadow-2xs transition-all">

                                <div class="space-y-3">
                                    <!-- Top Row: Icon + Label + Top-Right Edit/Delete Icons -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl <?= $badgeColor ?> flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[20px]"><?= esc($addrIcon) ?></span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate"><?= esc($addr['label'] ?: 'Address') ?></h3>
                                                    <?php if ($addrDefault): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-terra-green/10 text-terra-green dark:text-emerald-400">
                                                            Default
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                    <span class="font-medium text-slate-700 dark:text-slate-300"><?= esc($addr['recipient_name'] ?: 'Customer') ?></span>
                                                    <?php if ($addrPhone !== ''): ?>
                                                        <span class="mx-1 text-slate-300 dark:text-slate-600">&bull;</span>
                                                        <span><?= esc($addrPhone) ?></span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Compact Edit & Delete Icon Actions -->
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button"
                                                    class="addr-edit w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                    title="Edit address"
                                                    data-id="<?= esc($addr['id']) ?>"
                                                    data-label="<?= esc($addr['label'] ?? '') ?>"
                                                    data-recipient="<?= esc($addr['recipient_name'] ?? '') ?>"
                                                    data-phone="<?= esc($addrPhone) ?>"
                                                    data-line1="<?= esc($addrLine1) ?>"
                                                    data-line2="<?= esc($addrLine2) ?>"
                                                    data-city="<?= esc($addrCity) ?>"
                                                    data-province="<?= esc($addrProv) ?>"
                                                    data-zip="<?= esc($addrZip) ?>"
                                                    data-country="<?= esc($addrCountry) ?>"
                                                    data-lat="<?= esc($addr['latitude'] ?? '') ?>"
                                                    data-lng="<?= esc($addr['longitude'] ?? '') ?>"
                                                    data-place_id="<?= esc($addr['place_id'] ?? '') ?>"
                                                    data-default="<?= $addrDefault ? '1' : '0' ?>">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>

                                            <form method="post" action="<?= base_url('customer/addresses/delete') ?>" onsubmit="return confirm('Delete this shipping address?');" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="address_id" value="<?= esc($addr['id']) ?>">
                                                <button type="submit" title="Delete address" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors cursor-pointer">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Clean 2-Line Address Summary (No nested box) -->
                                    <div class="pl-[52px] space-y-0.5">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 leading-snug">
                                            <?= esc($addrLine1 ?: 'Street / House Address') ?>
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            <?= $addrLine2 !== '' ? 'Brgy. ' . esc($addrLine2) . ', ' : '' ?><?= esc($addrCity) ?>, <?= esc($addrProv) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Card Footer: GPS Status Pill & Set as Default -->
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium <?= $hasGps ? 'text-terra-green dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $hasGps ? 'bg-terra-green' : 'bg-slate-300 dark:bg-slate-600' ?>"></span>
                                        <?= $hasGps ? 'GPS Pin Saved' : 'Barangay Area' ?>
                                    </span>

                                    <?php if (!$addrDefault): ?>
                                        <form method="post" action="<?= base_url('customer/addresses/set-default') ?>" class="inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="address_id" value="<?= esc($addr['id']) ?>">
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-terra-green dark:hover:text-emerald-400 hover:bg-terra-green/10 transition-colors cursor-pointer">
                                                <span>Set as Default</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <!-- Calm Empty State -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 sm:p-10 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-terra-green/10 text-terra-green dark:text-emerald-400 mx-auto flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">location_on</span>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">No saved addresses yet</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Add your delivery address for faster checkout.</p>
                        </div>
                        <div class="pt-1">
                            <button type="button" id="addr-add-btn-empty" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                <span>Add Address</span>
                            </button>
                        </div>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>

<!-- Add / Edit Shipping Address Modal (Pin-First, Eye-Relaxing & Mobile-Responsive) -->
<div id="addr-modal" class="hidden fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4 md:p-6 overflow-y-auto">

    <!-- Modal Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" id="addr-overlay"></div>

    <!-- Modal Dialog -->
    <div class="relative bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl w-full max-w-3xl my-auto max-h-[92vh] sm:max-h-[88vh] flex flex-col overflow-hidden z-10">

        <!-- Sticky Modal Header -->
        <div class="px-5 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 bg-white dark:bg-slate-900">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]" id="addr-modal-icon">pin_drop</span>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-display font-bold text-slate-900 dark:text-white truncate" id="addr-modal-title">Add Delivery Address</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Pin your exact doorstep for accurate shipping fee &amp; rider navigation</p>
                </div>
            </div>

            <button type="button" id="addr-modal-close" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer shrink-0" aria-label="Close modal">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Scrollable Modal Body -->
        <form method="post" action="<?= base_url('customer/addresses/save') ?>" id="addressForm" class="overflow-y-auto flex-1 p-4 sm:p-6">

            <?= csrf_field() ?>
            <input type="hidden" name="address_id" id="addr-id" value="">
            <input type="hidden" name="latitude" id="addr-latitude" value="6.2185">
            <input type="hidden" name="longitude" id="addr-longitude" value="125.0650">
            <input type="hidden" name="place_id" id="addr-place-id" value="">
            <input type="hidden" name="address_line2" id="addr-line2" value="Poblacion">

            <!-- Auto-synced Province & Postal Code (kept in DOM for backend submission) -->
            <div class="hidden">
                <select id="addr-province" name="province">
                    <option value="South Cotabato" selected>South Cotabato</option>
                </select>
                <select id="addr-zip" name="postal_code">
                    <option value="9504" selected>9504 (Polomolok)</option>
                    <option value="9505">9505 (Tupi)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                <!-- STEP 1 (Left / Top): Interactive Map Pin Picker -->
                <div class="lg:col-span-6 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                            <span class="w-5 h-5 rounded-full bg-terra-green text-white text-[11px] font-black flex items-center justify-center font-mono">1</span>
                            <span>Pin Your Doorstep</span>
                        </span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" id="btn-address-map-type" onclick="toggleAddressMapType()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                <span id="addressMapTypeIcon" class="material-symbols-outlined text-[14px] text-terra-green dark:text-emerald-400">satellite_alt</span>
                                <span id="addressMapTypeText">Satellite</span>
                            </button>
                            <button type="button" id="btn-locate-me" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-terra-green text-white hover:bg-[#3d7a33] shadow-2xs transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]">my_location</span>
                                <span>Use My GPS</span>
                            </button>
                        </div>
                    </div>

                    <!-- Map Canvas -->
                    <div class="relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-2xs">
                        <div id="modalAddressMap" class="w-full h-[200px] sm:h-[230px] lg:h-[255px] bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <!-- Live Detected Pinned Location Confirmation Strip -->
                    <div class="p-3 rounded-xl bg-terra-green/5 dark:bg-terra-green/10 border border-terra-green/20 flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-terra-green dark:text-emerald-400 text-[18px] shrink-0 mt-0.5">where_to_vote</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate" id="pinned-area-text">Brgy. Poblacion, Polomolok</p>
                                <span id="map-coord-pill" class="text-[10px] font-mono text-slate-400 shrink-0">6.2185, 125.0650</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">
                                Tap anywhere on the map or drag the green pin to your exact house or gate.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- STEP 2 (Right / Bottom): Address & Recipient Details -->
                <div class="lg:col-span-6 space-y-3.5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-terra-green text-white text-[11px] font-black flex items-center justify-center font-mono">2</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Address &amp; Contact Details</span>
                    </div>

                    <!-- Municipality & Barangay -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label for="addr-city" class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 block mb-1">
                                Municipality <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="addr-city" name="city" class="w-full py-2 pl-3 pr-7 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green appearance-none cursor-pointer outline-none" required>
                                    <option value="Polomolok" selected>Polomolok</option>
                                    <option value="Tupi">Tupi</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[16px] text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        <div>
                            <label for="addr-barangay" class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 block mb-1">
                                Barangay <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="addr-barangay" name="barangay" class="w-full py-2 pl-3 pr-7 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green appearance-none cursor-pointer outline-none" required>
                                </select>
                                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-[16px] text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Street / House / Landmark -->
                    <div>
                        <label for="addr-line1" class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 block mb-1">
                            Street, House No. &amp; Landmark <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="addr-line1" name="address_line1" placeholder="e.g., Blk 4 Lot 2, National Hwy (Near Municipal Gym)" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green focus:bg-white dark:focus:bg-slate-800 outline-none" required>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label for="addr-recipient" class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 block mb-1">
                                Recipient Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="addr-recipient" name="recipient_name" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green focus:bg-white dark:focus:bg-slate-800 outline-none" placeholder="Full Name" required>
                        </div>

                        <div>
                            <label for="addr-phone" class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 block mb-1">
                                Mobile Number <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" id="addr-phone" name="phone" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green focus:bg-white dark:focus:bg-slate-800 outline-none" placeholder="09XX-XXX-XXXX" required>
                        </div>
                    </div>

                    <!-- Single-Click Address Label Pills (Custom Input only appears when 'Other' is selected) -->
                    <div>
                        <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 block mb-1.5">Save Address As</label>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" class="addr-preset-btn flex items-center justify-center gap-1 py-2 px-2 rounded-xl border border-terra-green bg-terra-green/10 text-terra-green text-xs font-bold transition-all cursor-pointer" data-preset="Home">
                                <span class="material-symbols-outlined text-[15px]">home</span>
                                <span>Home</span>
                            </button>
                            <button type="button" class="addr-preset-btn flex items-center justify-center gap-1 py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:border-terra-green transition-all cursor-pointer" data-preset="Office">
                                <span class="material-symbols-outlined text-[15px]">work</span>
                                <span>Office</span>
                            </button>
                            <button type="button" class="addr-preset-btn flex items-center justify-center gap-1 py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:border-terra-green transition-all cursor-pointer" data-preset="Apartment">
                                <span class="material-symbols-outlined text-[15px]">apartment</span>
                                <span>Apt</span>
                            </button>
                            <button type="button" class="addr-preset-btn flex items-center justify-center gap-1 py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:border-terra-green transition-all cursor-pointer" data-preset="Other">
                                <span class="material-symbols-outlined text-[15px]">bookmark</span>
                                <span>Other</span>
                            </button>
                        </div>
                        <div id="addr-custom-label-wrap" class="hidden mt-2">
                            <input type="text" id="addr-label" name="label" class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green focus:bg-white dark:focus:bg-slate-800 outline-none" placeholder="Enter custom label (e.g. Parents' House, Dorm)" value="Home" required>
                        </div>
                    </div>

                    <!-- Default Address Toggle -->
                    <div id="addr-default-wrap" class="pt-0.5">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="addr-default" name="is_default" value="1" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-terra-green focus:ring-terra-green cursor-pointer">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Set as default delivery address</span>
                        </label>
                    </div>
                </div>

            </div>

        </form>

        <!-- Sticky Modal Footer (Always Visible on Mobile & Desktop) -->
        <div class="px-5 py-3.5 sm:px-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90 flex items-center justify-end gap-2.5 shrink-0">
            <button type="button" id="addr-modal-cancel" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" form="addressForm" id="addr-submit-btn" class="px-5 py-2.5 rounded-xl bg-terra-green hover:bg-[#3d7a33] text-white text-xs font-bold shadow-xs transition-all active:scale-95 inline-flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">check</span>
                <span>Save Address</span>
            </button>
        </div>

    </div>

</div>

<script>
(function () {
    // Official PSA & PhilAtlas Verified Coordinates per Municipality & Barangay
    const BARANGAY_COORDS_BY_CITY = {
        'Polomolok': {
            'Bentung':          [6.2360, 125.0090],
            'Cannery Site':     [6.2409, 125.0902],
            'Crossing Palkan':  [6.2686, 125.0107],
            'Glamang':          [6.1663, 125.0839],
            'Kinilis':          [6.3130, 125.0795],
            'Klinan 6':         [6.1861, 125.1371],
            'Koronadal Proper': [6.2442, 124.9870],
            'Lam-Caliaf':       [6.2731, 125.0530],
            'Landan':           [6.3277, 125.1471],
            'Lapu':             [6.2220, 125.0150],
            'Lumakil':          [6.2145, 125.0368],
            'Magsaysay':        [6.2003, 125.0615],
            'Maligo':           [6.3078, 125.1286],
            'Pagalungan':       [6.2392, 125.0484],
            'Palkan':           [6.2935, 125.0375],
            'Poblacion':        [6.2196, 125.0693],
            'Polo':             [6.2811, 125.1111],
            'Pula Bato':        [6.2220, 125.0150],
            'Rubber':           [6.1972, 125.0435],
            'Silway 7':         [6.1769, 125.1210],
            'Silway 8':         [6.1940, 125.0931],
            'Sulit':            [6.2362, 125.0246],
            'Sumbakil':         [6.2544, 124.9799],
            'Upper Klinan':     [6.2249, 125.1227]
        },
        'Tupi': {
            'Acmonan':            [6.3436, 124.9914],
            'Bololmala':          [6.3912, 124.9200],
            'Bunao':              [6.3249, 124.9423],
            'Cebuano':            [6.3819, 124.9617],
            'Crossing Rubber':    [6.3648, 124.9206],
            'Juan-Loreto Tamayo': [6.3530, 124.9480],
            'Kablon':             [6.3165, 125.0147],
            'Kalkam':             [6.3342, 124.9214],
            'Linan':              [6.3713, 124.9867],
            'Lunen':              [6.3397, 124.9003],
            'Miasong':            [6.4131, 125.0932],
            'Palian':             [6.3745, 124.9115],
            'Poblacion':          [6.3389, 124.9520],
            'Polonoling':         [6.2862, 124.9711],
            'Simbo':              [6.2480, 124.9588],
            'Tubeng':             [6.3593, 124.8810]
        }
    };

    const BARANGAYS_BY_CITY = {
        'Polomolok': Object.keys(BARANGAY_COORDS_BY_CITY['Polomolok']),
        'Tupi':      Object.keys(BARANGAY_COORDS_BY_CITY['Tupi'])
    };

    const modal           = document.getElementById('addr-modal');
    const modalTitle      = document.getElementById('addr-modal-title');
    const addrId          = document.getElementById('addr-id');
    const defaultWrap     = document.getElementById('addr-default-wrap');
    const citySelect      = document.getElementById('addr-city');
    const brgySelect      = document.getElementById('addr-barangay');
    const zipSelect       = document.getElementById('addr-zip');
    const line2Input      = document.getElementById('addr-line2');
    const coordPill       = document.getElementById('map-coord-pill');
    const pinnedAreaText  = document.getElementById('pinned-area-text');
    const customLabelWrap = document.getElementById('addr-custom-label-wrap');
    const labelInput      = document.getElementById('addr-label');

    let modalMap = null;
    let modalMarker = null;

    function haversineMeters(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const toRad = d => d * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLng = toRad(lng2 - lng1);
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                  Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function getBarangayCoords(city, brgy) {
        const cityKey = (city && city.toLowerCase() === 'tupi') ? 'Tupi' : 'Polomolok';
        const cityMap = BARANGAY_COORDS_BY_CITY[cityKey] || BARANGAY_COORDS_BY_CITY['Polomolok'];
        let normalizedBrgy = (brgy || '').trim();
        if (normalizedBrgy.toLowerCase() === 'miaso') normalizedBrgy = 'Miasong';
        if (normalizedBrgy.toLowerCase() === 'polonuling') normalizedBrgy = 'Polonoling';

        for (const [bName, coords] of Object.entries(cityMap)) {
            if (bName.toLowerCase() === normalizedBrgy.toLowerCase()) {
                return coords;
            }
        }
        return cityKey === 'Tupi' ? [6.3389, 124.9520] : [6.2196, 125.0693];
    }

    function updatePinnedAreaBanner() {
        const city = citySelect ? citySelect.value : 'Polomolok';
        const brgy = brgySelect ? brgySelect.value : 'Poblacion';
        if (pinnedAreaText) {
            pinnedAreaText.textContent = `Brgy. ${brgy || 'Poblacion'}, ${city || 'Polomolok'}`;
        }
    }

    function populateBarangays(selectedCity, selectedBrgy) {
        const city = selectedCity === 'Tupi' ? 'Tupi' : 'Polomolok';
        const list = BARANGAYS_BY_CITY[city] || BARANGAYS_BY_CITY['Polomolok'];
        let targetBrgy = (selectedBrgy || 'Poblacion').trim();
        if (targetBrgy.toLowerCase() === 'miaso') targetBrgy = 'Miasong';
        if (targetBrgy.toLowerCase() === 'polonuling') targetBrgy = 'Polonoling';

        brgySelect.innerHTML = '';
        let matched = false;
        list.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b;
            opt.textContent = b;
            if (targetBrgy && targetBrgy.toLowerCase() === b.toLowerCase()) {
                opt.selected = true;
                matched = true;
            }
            brgySelect.appendChild(opt);
        });

        if (!matched) {
            for (let i = 0; i < brgySelect.options.length; i++) {
                if (brgySelect.options[i].value === 'Poblacion') {
                    brgySelect.selectedIndex = i;
                    break;
                }
            }
        }

        if (zipSelect) {
            zipSelect.value = city === 'Tupi' ? '9505' : '9504';
        }
        if (line2Input) {
            line2Input.value = brgySelect.value || 'Poblacion';
        }
        updatePinnedAreaBanner();
    }

    // Automatically match nearest verified barangay when customer taps map or drags pin
    function syncNearestBarangayFromCoords(lat, lng) {
        let bestCity = citySelect ? citySelect.value : 'Polomolok';
        let bestBrgy = null;
        let minDistanceMeters = Infinity;

        Object.keys(BARANGAY_COORDS_BY_CITY).forEach(city => {
            const cityMap = BARANGAY_COORDS_BY_CITY[city];
            Object.keys(cityMap).forEach(brgyName => {
                // Avoid duplicate alias matching (Lapu takes precedence over Pula Bato when dragging pin)
                if (brgyName === 'Pula Bato') return;
                const coords = cityMap[brgyName];
                if (coords) {
                    const distM = haversineMeters(lat, lng, coords[0], coords[1]);
                    if (distM < minDistanceMeters) {
                        minDistanceMeters = distM;
                        bestCity = city;
                        bestBrgy = brgyName;
                    }
                }
            });
        });

        if (bestBrgy && citySelect && brgySelect) {
            if (citySelect.value !== bestCity) {
                citySelect.value = bestCity;
                populateBarangays(bestCity, bestBrgy);
            } else {
                for (let i = 0; i < brgySelect.options.length; i++) {
                    if (brgySelect.options[i].value.toLowerCase() === bestBrgy.toLowerCase()) {
                        brgySelect.selectedIndex = i;
                        break;
                    }
                }
                if (line2Input) line2Input.value = brgySelect.value;
                updatePinnedAreaBanner();
            }
        }
    }

    function updateCoordinateFields(lat, lng, autoDetectBrgy = false) {
        const latInput = document.getElementById('addr-latitude');
        const lngInput = document.getElementById('addr-longitude');
        if (latInput) latInput.value = Number(lat).toFixed(7);
        if (lngInput) lngInput.value = Number(lng).toFixed(7);
        if (coordPill) {
            coordPill.textContent = `${Number(lat).toFixed(4)}, ${Number(lng).toFixed(4)}`;
        }
        if (autoDetectBrgy) {
            syncNearestBarangayFromCoords(Number(lat), Number(lng));
        }
    }

    const ADDR_MAP_TYPE_STORAGE_KEY = 'blax_address_modal_map_type';
    let currentAddrMapType = localStorage.getItem(ADDR_MAP_TYPE_STORAGE_KEY) || 'hybrid';

    function updateAddressMapTypeToggleUI() {
        const icon = document.getElementById('addressMapTypeIcon');
        const text = document.getElementById('addressMapTypeText');
        if (!icon || !text) return;
        if (currentAddrMapType === 'hybrid') {
            icon.textContent = 'map';
            text.textContent = 'Roadmap';
        } else {
            icon.textContent = 'satellite_alt';
            text.textContent = 'Satellite';
        }
    }

    function toggleAddressMapType() {
        if (!modalMap || typeof google === 'undefined' || !google.maps) return;
        if (currentAddrMapType === 'hybrid') {
            currentAddrMapType = 'roadmap';
            modalMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        } else {
            currentAddrMapType = 'hybrid';
            modalMap.setMapTypeId(google.maps.MapTypeId.HYBRID);
        }
        localStorage.setItem(ADDR_MAP_TYPE_STORAGE_KEY, currentAddrMapType);
        updateAddressMapTypeToggleUI();
    }

    function initOrUpdateModalMap(initialLat, initialLng, autoDetect = false) {
        const lat = parseFloat(initialLat) || 6.2196;
        const lng = parseFloat(initialLng) || 125.0693;
        const center = { lat, lng };

        updateCoordinateFields(lat, lng, autoDetect);

        const mapEl = document.getElementById('modalAddressMap');
        if (!mapEl || typeof google === 'undefined' || !google.maps) return;

        if (!modalMap) {
            const initialMapTypeId = currentAddrMapType === 'roadmap' ? google.maps.MapTypeId.ROADMAP : google.maps.MapTypeId.HYBRID;
            modalMap = new google.maps.Map(mapEl, {
                center: center,
                zoom: 16,
                mapTypeId: initialMapTypeId,
                mapId: 'ADDRESS_PICKER_MAP',
                disableDefaultUI: false,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });
            updateAddressMapTypeToggleUI();

            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                const pinImg = document.createElement('div');
                pinImg.style.width = '34px';
                pinImg.style.height = '34px';
                pinImg.style.borderRadius = '50%';
                pinImg.style.backgroundColor = '#1f451c';
                pinImg.style.border = '3px solid #ffffff';
                pinImg.style.boxShadow = '0 4px 12px rgba(0,0,0,0.35)';
                pinImg.style.display = 'flex';
                pinImg.style.alignItems = 'center';
                pinImg.style.justifyContent = 'center';
                pinImg.style.color = '#ffffff';
                pinImg.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">home_pin</span>';

                modalMarker = new google.maps.marker.AdvancedMarkerElement({
                    map: modalMap,
                    position: center,
                    gmpDraggable: true,
                    content: pinImg,
                    title: 'Drag to your exact doorstep'
                });

                modalMarker.addListener('dragend', () => {
                    const pos = modalMarker.position;
                    const pLat = typeof pos.lat === 'function' ? pos.lat() : pos.lat;
                    const pLng = typeof pos.lng === 'function' ? pos.lng() : pos.lng;
                    updateCoordinateFields(pLat, pLng, true);
                });
            } else {
                modalMarker = new google.maps.Marker({
                    map: modalMap,
                    position: center,
                    draggable: true,
                    title: 'Drag to your exact doorstep'
                });

                modalMarker.addListener('dragend', (evt) => {
                    updateCoordinateFields(evt.latLng.lat(), evt.latLng.lng(), true);
                });
            }

            modalMap.addListener('click', (evt) => {
                const clickedLat = evt.latLng.lat();
                const clickedLng = evt.latLng.lng();
                if (modalMarker.position && typeof modalMarker.position.lat === 'function') {
                    modalMarker.setPosition(new google.maps.LatLng(clickedLat, clickedLng));
                } else {
                    modalMarker.position = { lat: clickedLat, lng: clickedLng };
                }
                updateCoordinateFields(clickedLat, clickedLng, true);
            });
        } else {
            modalMap.setCenter(center);
            modalMap.setZoom(16);
            if (modalMarker) {
                if (modalMarker.position && typeof modalMarker.position.lat === 'function') {
                    modalMarker.setPosition(new google.maps.LatLng(lat, lng));
                } else {
                    modalMarker.position = center;
                }
            }
            google.maps.event.trigger(modalMap, 'resize');
        }
    }

    function panToBarangayCentroid() {
        const city = citySelect ? citySelect.value : 'Polomolok';
        const brgy = brgySelect ? brgySelect.value : 'Poblacion';
        const target = getBarangayCoords(city, brgy);

        const newLat = target[0];
        const newLng = target[1];

        if (modalMap && modalMarker) {
            const newPos = { lat: newLat, lng: newLng };
            modalMap.panTo(newPos);
            modalMap.setZoom(15);
            if (modalMarker.position && typeof modalMarker.position.lat === 'function') {
                modalMarker.setPosition(new google.maps.LatLng(newLat, newLng));
            } else {
                modalMarker.position = newPos;
            }
        }
        updateCoordinateFields(newLat, newLng, false);
        updatePinnedAreaBanner();
    }

    if (citySelect) {
        citySelect.addEventListener('change', function () {
            populateBarangays(this.value, 'Poblacion');
            panToBarangayCentroid();
        });
    }

    if (brgySelect) {
        brgySelect.addEventListener('change', function () {
            if (line2Input) line2Input.value = this.value;
            panToBarangayCentroid();
        });
    }

    // Single-Click Label Pills (Only show custom input when 'Other' is active)
    function selectLabelPreset(presetValue) {
        const stdPresets = ['Home', 'Office', 'Apartment'];
        const isStandard = stdPresets.some(p => p.toLowerCase() === (presetValue || '').toLowerCase());
        const matchedStd = stdPresets.find(p => p.toLowerCase() === (presetValue || '').toLowerCase());

        document.querySelectorAll('.addr-preset-btn').forEach(b => {
            const btnPreset = b.dataset.preset;
            const active = isStandard ? (btnPreset === matchedStd) : (btnPreset === 'Other');
            if (active) {
                b.className = 'addr-preset-btn flex items-center justify-center gap-1 py-2 px-2 rounded-xl border border-terra-green bg-terra-green/10 text-terra-green text-xs font-bold transition-all cursor-pointer';
            } else {
                b.className = 'addr-preset-btn flex items-center justify-center gap-1 py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:border-terra-green transition-all cursor-pointer';
            }
        });

        if (isStandard && matchedStd) {
            if (labelInput) labelInput.value = matchedStd;
            if (customLabelWrap) customLabelWrap.classList.add('hidden');
        } else {
            if (customLabelWrap) customLabelWrap.classList.remove('hidden');
            if (labelInput) {
                labelInput.value = (presetValue && presetValue !== 'Other') ? presetValue : 'Other';
                setTimeout(() => labelInput.focus(), 50);
            }
        }
    }

    document.querySelectorAll('.addr-preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            selectLabelPreset(this.dataset.preset || 'Home');
        });
    });

    // Locate Me GPS Button
    const locateBtn = document.getElementById('btn-locate-me');
    if (locateBtn) {
        locateBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }
            locateBtn.disabled = true;
            locateBtn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">refresh</span><span>Locating...</span>';

            navigator.geolocation.getCurrentPosition(
                pos => {
                    locateBtn.disabled = false;
                    locateBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">my_location</span><span>Use My GPS</span>';
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    initOrUpdateModalMap(lat, lng, true);
                },
                () => {
                    locateBtn.disabled = false;
                    locateBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">my_location</span><span>Use My GPS</span>';
                    alert('Unable to retrieve location. Please grant location permissions in your browser.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        });
    }

    function openAdd() {
        addrId.value = '';
        modalTitle.textContent = 'Add Delivery Address';
        document.getElementById('addr-modal-icon').textContent = 'add_location_alt';
        selectLabelPreset('Home');
        document.getElementById('addr-recipient').value = '';
        document.getElementById('addr-phone').value = '';
        document.getElementById('addr-line1').value = '';
        citySelect.value = 'Polomolok';
        populateBarangays('Polomolok', 'Poblacion');
        document.getElementById('addr-default').checked = false;
        defaultWrap.classList.remove('hidden');
        modal.classList.remove('hidden');

        setTimeout(() => {
            initOrUpdateModalMap(6.2196, 125.0693, false);
        }, 150);
    }

    function openEdit(btn) {
        addrId.value = btn.dataset.id;
        modalTitle.textContent = 'Edit Delivery Address';
        document.getElementById('addr-modal-icon').textContent = 'edit_location';
        selectLabelPreset(btn.dataset.label || 'Home');
        document.getElementById('addr-recipient').value = btn.dataset.recipient || '';
        document.getElementById('addr-phone').value = btn.dataset.phone || '';
        document.getElementById('addr-line1').value = btn.dataset.line1 || '';

        const city = btn.dataset.city || 'Polomolok';
        const brgy = btn.dataset.line2 || 'Poblacion';
        citySelect.value = city;
        populateBarangays(city, brgy);

        const isDef = btn.dataset.default === '1';
        document.getElementById('addr-default').checked = isDef;
        if (isDef) {
            defaultWrap.classList.add('hidden');
        } else {
            defaultWrap.classList.remove('hidden');
        }

        modal.classList.remove('hidden');

        const verifiedCentroid = getBarangayCoords(city, brgy);
        let lat = parseFloat(btn.dataset.lat) || verifiedCentroid[0];
        let lng = parseFloat(btn.dataset.lng) || verifiedCentroid[1];

        // If previously saved coordinate is > 3.5 km away from the selected barangay's real centroid
        // (e.g. saved under the old buggy barangay coordinates), snap to the verified barangay centroid
        if (haversineMeters(lat, lng, verifiedCentroid[0], verifiedCentroid[1]) > 3500) {
            lat = verifiedCentroid[0];
            lng = verifiedCentroid[1];
        }

        setTimeout(() => {
            initOrUpdateModalMap(lat, lng, false);
        }, 150);
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    const addBtn = document.getElementById('addr-add-btn');
    if (addBtn) addBtn.addEventListener('click', openAdd);
    const addBtnEmpty = document.getElementById('addr-add-btn-empty');
    if (addBtnEmpty) addBtnEmpty.addEventListener('click', openAdd);

    document.querySelectorAll('.addr-edit').forEach(btn => {
        btn.addEventListener('click', () => openEdit(btn));
    });

    document.getElementById('addr-overlay').addEventListener('click', closeModal);
    document.getElementById('addr-modal-close').addEventListener('click', closeModal);
    document.getElementById('addr-modal-cancel').addEventListener('click', closeModal);
    window.toggleAddressMapType = toggleAddressMapType;

    populateBarangays('Polomolok', 'Poblacion');

    const params = new URLSearchParams(window.location.search);
    if (params.get('open_add') === '1' || window.location.hash === '#add') {
        openAdd();
    }
})();
</script>

<!-- Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc(env('GOOGLE_MAPS_API_KEY')) ?>&libraries=marker,geometry&loading=async" async defer></script>

<?= $this->endSection() ?>