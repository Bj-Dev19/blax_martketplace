<?= $this->extend('layouts/tenant') ?>
<?= $this->section('content') ?>

<?php
    $mfOpen    = isset($businessHours[1]) && !empty($businessHours[1]['open_time'])  ? substr($businessHours[1]['open_time'], 0, 5)  : '08:00';
    $mfClose   = isset($businessHours[1]) && !empty($businessHours[1]['close_time']) ? substr($businessHours[1]['close_time'], 0, 5) : '18:00';
    $satOpen   = isset($businessHours[6]) && !empty($businessHours[6]['open_time'])  ? substr($businessHours[6]['open_time'], 0, 5)  : '09:00';
    $satClose  = isset($businessHours[6]) && !empty($businessHours[6]['close_time']) ? substr($businessHours[6]['close_time'], 0, 5) : '15:00';
    $sunClosed = (int) ($businessHours[0]['is_closed'] ?? 1);
    $logoUrl   = $shop['logo_url'] ?? '';
    $initials  = strtoupper(substr(trim((string) ($shop['shop_name'] ?? 'RHK')), 0, 2));
    $shopSlug  = $shop['slug'] ?? $shop['id'] ?? '';
    $hasCoords = !empty($shop['latitude']) && !empty($shop['longitude']);
?>

<div class="flex-1 space-y-6 max-w-7xl mx-auto pb-12">

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-terra-green">check_circle</span>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm font-medium flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header & Storefront Status Banner -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">storefront</span>
                </span>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-display">Store Settings</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-sans">Manage your shop profile, payout destination, operating hours, and store preferences.</p>
                </div>
            </div>
        </div>

        <!-- Quick Storefront Link & Status Badge -->
        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                <span class="w-2 h-2 rounded-full <?= ($shop['status'] ?? '') === 'active' ? 'bg-terra-green animate-pulse' : 'bg-amber-500' ?>"></span>
                <span class="font-bold text-slate-800 dark:text-slate-200 capitalize font-sans"><?= esc($shop['status'] ?? 'Active') ?> Merchant</span>
            </div>

            <?php if (!empty($shopSlug)): ?>
                <a href="<?= base_url('shop/' . $shopSlug) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-terra-green text-white hover:bg-terra-green/90 transition-all shadow-2xs active:scale-95">
                    <span>View Public Storefront</span>
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Responsive Sub-Navigation Tabs -->
    <nav class="flex items-center gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-px overflow-x-auto no-scrollbar" aria-label="Settings Tabs">
        <button type="button" onclick="switchSettingsTab('profile')" id="tab-btn-profile" class="settings-tab-btn inline-flex items-center gap-2 px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap border-terra-green text-terra-green cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">store</span>
            <span>Shop Profile</span>
        </button>
        <button type="button" onclick="switchSettingsTab('payment')" id="tab-btn-payment" class="settings-tab-btn inline-flex items-center gap-2 px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
            <span>GCash Payouts</span>
        </button>
        <button type="button" onclick="switchSettingsTab('hours')" id="tab-btn-hours" class="settings-tab-btn inline-flex items-center gap-2 px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">schedule</span>
            <span>Operating Hours</span>
        </button>
        <button type="button" onclick="switchSettingsTab('preferences')" id="tab-btn-preferences" class="settings-tab-btn inline-flex items-center gap-2 px-4 py-3 border-b-2 text-xs font-bold transition-all whitespace-nowrap border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">tune</span>
            <span>Services &amp; Alerts</span>
        </button>
    </nav>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: SHOP PROFILE                                                    -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tab-panel-profile" class="settings-tab-panel space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 md:p-8 space-y-8">
            
            <!-- Section Header -->
            <div class="flex items-start justify-between border-b border-slate-200/80 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Store Identity &amp; Branding</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">Customize your brand logo, merchant name, and physical location in Polomolok.</p>
                </div>
            </div>

            <!-- Logo Upload Card -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60">
                <!-- Avatar with Preview -->
                <div class="relative w-24 h-24 rounded-2xl overflow-hidden flex items-center justify-center bg-terra-green/10 border-2 border-slate-200 dark:border-slate-700 shrink-0 shadow-2xs group">
                    <img id="logoPreviewImg" src="<?= !empty($logoUrl) ? esc(logo_url($logoUrl)) : '' ?>" alt="Logo" class="w-full h-full object-cover <?= empty($logoUrl) ? 'hidden' : '' ?>">
                    <span id="logoInitialsSpan" class="text-2xl font-black text-terra-green font-display <?= !empty($logoUrl) ? 'hidden' : '' ?>"><?= esc($initials) ?></span>
                    
                    <button type="button" onclick="document.getElementById('logoInput').click()" class="absolute inset-0 bg-slate-950/60 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-[11px] font-bold gap-1 cursor-pointer" title="Change logo">
                        <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                        <span>Change</span>
                    </button>
                </div>

                <div class="space-y-2 flex-1">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white font-display">Shop Brand Logo</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">Displays across the storefront, product catalog, search results, and printed receipts.</p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 px-2.5 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700">
                            Square 1:1 • Min 300×300px • Max 2MB
                        </span>
                        <span class="text-[11px] text-slate-400">JPG, PNG, WEBP, GIF</span>
                    </div>

                    <!-- Hidden Logo Form for Direct Upload -->
                    <form action="<?= base_url('tenant/settings/logo') ?>" method="POST" enctype="multipart/form-data" id="logoForm" class="hidden">
                        <?= csrf_field() ?>
                        <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png,image/webp,image/gif">
                    </form>

                    <div class="pt-1 flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('logoInput').click()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold transition-colors text-xs border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer">
                            <span class="material-symbols-outlined text-[16px] text-terra-green">upload</span>
                            <span>Upload New Logo</span>
                        </button>
                        <span id="logoUploadHint" class="text-xs text-slate-500 hidden"></span>
                    </div>
                </div>
            </div>

            <!-- Profile Details Form -->
            <form action="<?= base_url('tenant/settings/save') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="section" value="profile">
                <input type="hidden" name="latitude" id="shop_latitude" value="<?= esc($shop['latitude'] ?? '') ?>">
                <input type="hidden" name="longitude" id="shop_longitude" value="<?= esc($shop['longitude'] ?? '') ?>">

                <!-- Shop Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block font-sans" for="shop_name">
                        Shop Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                        </span>
                        <input type="text" name="shop_name" id="shop_name" value="<?= esc($shop['shop_name'] ?? '') ?>" placeholder="e.g. RHK General Merchandise" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white" required>
                    </div>
                </div>

                <!-- Shop Description -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block font-sans" for="description">
                            Shop Bio / Description
                        </label>
                        <span id="charCount" class="text-[11px] text-slate-400 font-mono">0 / 500</span>
                    </div>
                    <textarea name="description" id="description" rows="3" maxlength="500" placeholder="Briefly describe your products, specialties, and customer policy..." class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all leading-relaxed text-slate-900 dark:text-white"><?= esc($shop['description'] ?? '') ?></textarea>
                </div>

                <!-- Physical Address Card -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white font-display">Store Location in Polomolok</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-sans">Accurate address coordinates enable doorstep deliveries and store pick-up routing.</p>
                        </div>
                        <?php if ($hasCoords): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20" title="Latitude: <?= esc($shop['latitude']) ?>, Longitude: <?= esc($shop['longitude']) ?>">
                                <span class="material-symbols-outlined text-[14px]">pin_drop</span>
                                <span>GPS Synced</span>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                <span class="material-symbols-outlined text-[14px]">location_searching</span>
                                <span>Pending Geocode</span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Street Address -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-sans" for="street">
                                Street Address / Bldg / Purok
                            </label>
                            <div id="shop-autocomplete-wrapper" class="hidden mb-2"></div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[18px]">location_city</span>
                                </span>
                                <input type="text" name="street" id="street" value="<?= esc($shop['street'] ?? '') ?>" placeholder="House No., Street Name, Purok / Bldg" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Barangay -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-sans" for="barangay">
                                Barangay (Polomolok)
                            </label>
                            <div class="relative">
                                <select name="barangay" id="barangay" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white">
                                    <option value="">-- Select Barangay --</option>
                                    <?php
                                    $validBarangays = [
                                        'Bentung', 'Cannery Site', 'Crossing Palkan', 'Glamang', 'Kinilis',
                                        'Klinan 6', 'Koronadal Proper', 'Lam-Caliaf', 'Landan', 'Lapu', 'Lumakil',
                                        'Magsaysay', 'Maligo', 'Pagalungan', 'Palkan', 'Poblacion', 'Polo',
                                        'Pula Bato', 'Rubber', 'Silway 7', 'Silway 8', 'Sulit', 'Sumbakil',
                                        'Upper Klinan'
                                    ];
                                    $curBrgy = $shop['barangay'] ?? '';
                                    foreach ($validBarangays as $b): ?>
                                        <option value="<?= esc($b) ?>" <?= ($curBrgy === $b) ? 'selected' : '' ?>><?= esc($b) ?></option>
                                    <?php endforeach; ?>
                                    <?php if ($curBrgy !== '' && !in_array($curBrgy, $validBarangays, true)): ?>
                                        <option value="<?= esc($curBrgy) ?>" selected><?= esc($curBrgy) ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Full Address Line -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-sans" for="address_line">
                            Address Line (Full Formatted Address)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">map</span>
                            </span>
                            <input type="text" name="address_line" id="address_line" value="<?= esc($shop['address_line'] ?? '') ?>" placeholder="e.g. Poblacion, Polomolok, South Cotabato" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-terra-green text-white rounded-xl font-bold hover:bg-terra-green/90 transition-all shadow-xs flex items-center gap-2 text-xs cursor-pointer active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Save Store Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: GCASH PAYOUTS                                                   -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tab-panel-payment" class="settings-tab-panel hidden space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 md:p-8 space-y-6 max-w-2xl">
            
            <div class="flex items-start justify-between border-b border-slate-200/80 dark:border-slate-800 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                        </span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">GCash Disbursement Destination</h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-sans">Configure the registered GCash account where your marketplace earnings will be deposited.</p>
                </div>
            </div>

            <!-- Payout Advisory Card -->
            <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-xs text-slate-700 dark:text-slate-300 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-blue-800 dark:text-blue-300">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span>GCash KYC Verification Notice</span>
                </div>
                <p class="text-blue-900/80 dark:text-blue-300/80 leading-relaxed text-[11px] font-sans">
                    Ensure your GCash account name exactly matches the name registered with GCash to prevent transfer delays or bank reversals. Payouts are reviewed and disbursed by the admin team.
                </p>
            </div>

            <form action="<?= base_url('tenant/settings/save') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>
                <input type="hidden" name="section" value="payment">

                <!-- GCash Number -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block font-sans" for="gcash_number">
                        GCash Mobile Number <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">phone_iphone</span>
                        </span>
                        <input type="text" name="gcash_number" id="gcash_number" value="<?= esc($shop['gcash_number'] ?? '') ?>" placeholder="09171234567" maxlength="13" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-semibold focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white">
                    </div>
                    <p class="text-[11px] text-slate-400 font-sans">Format: 11-digit Philippine mobile number starting with 09.</p>
                </div>

                <!-- Confirm GCash Number -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block font-sans" for="confirm_gcash_number">
                        Confirm GCash Mobile Number <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">verified</span>
                        </span>
                        <input type="text" name="confirm_gcash_number" id="confirm_gcash_number" value="<?= esc($shop['gcash_number'] ?? '') ?>" placeholder="Re-enter 09XXXXXXXXX" maxlength="13" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-mono font-semibold focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white">
                    </div>
                    <p class="text-[11px] text-slate-400 font-sans">Must exactly match the GCash mobile number entered above.</p>
                </div>

                <!-- Account Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block font-sans" for="gcash_account_name">
                        GCash Registered Account Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                        </span>
                        <input type="text" name="gcash_account_name" id="gcash_account_name" value="<?= esc($shop['gcash_account_name'] ?? '') ?>" placeholder="e.g. Juan Dela Cruz / RHK Merchandise" maxlength="100" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-terra-green/30 focus:outline-none transition-all text-slate-900 dark:text-white">
                    </div>
                    <p class="text-[11px] text-slate-400 font-sans">The full name registered with your verified GCash mobile wallet.</p>
                </div>

                <!-- Ownership Declaration Checkbox -->
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-start gap-3">
                    <input type="checkbox" name="gcash_confirm_ownership" id="gcash_confirm_ownership" value="1" required class="mt-0.5 w-4 h-4 rounded text-terra-green focus:ring-terra-green border-slate-300 dark:border-slate-600 cursor-pointer">
                    <label for="gcash_confirm_ownership" class="text-xs text-slate-700 dark:text-slate-300 leading-snug cursor-pointer select-none font-medium">
                        I confirm that the GCash account information I provided is correct and belongs to me/my business.
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-terra-green text-white rounded-xl font-bold hover:bg-terra-green/90 transition-all shadow-xs flex items-center gap-2 text-xs cursor-pointer active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                        <span>Save Payment Details</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

            <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: OPERATING HOURS & CALENDAR SCHEDULER                             -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <?php
        $calendarSchedules = $calendarSchedules ?? [];
        $operationalStatus = $operationalStatus ?? [
            'is_closed_today'  => false,
            'status_label'     => 'Open Today',
            'note'             => '',
            'hide_when_closed' => !empty($shop['hide_when_closed']),
        ];
        $hideWhenClosed = !empty($shop['hide_when_closed']);
        $todayDateKey   = date('Y-m-d');
        $todayDayNum    = (int) date('j');

        // 7-day business hours in merchant display order (Monday=1 through Saturday=6, Sunday=0)
        $dayConfig = [
            1 => ['name' => 'Monday', 'short' => 'MON'],
            2 => ['name' => 'Tuesday', 'short' => 'TUE'],
            3 => ['name' => 'Wednesday', 'short' => 'WED'],
            4 => ['name' => 'Thursday', 'short' => 'THU'],
            5 => ['name' => 'Friday', 'short' => 'FRI'],
            6 => ['name' => 'Saturday', 'short' => 'SAT'],
            0 => ['name' => 'Sunday', 'short' => 'SUN'],
        ];

        $totalWeeklyOpenHours = 0;
        $dayHoursData = [];
        foreach ($dayConfig as $d => $cfg) {
            $row       = $businessHours[$d] ?? [];
            $isClosed  = (int) ($row['is_closed'] ?? ($d === 0 ? 1 : 0));
            $openTime  = !empty($row['open_time']) ? substr($row['open_time'], 0, 5) : ($d === 6 ? '09:00' : '08:00');
            $closeTime = !empty($row['close_time']) ? substr($row['close_time'], 0, 5) : ($d === 6 ? '15:00' : '18:00');

            $durationHours = 0;
            if (!$isClosed && $openTime && $closeTime) {
                $tOpen  = strtotime("1970-01-01 $openTime:00");
                $tClose = strtotime("1970-01-01 $closeTime:00");
                if ($tClose > $tOpen) {
                    $durationHours = round(($tClose - $tOpen) / 3600, 1);
                    $totalWeeklyOpenHours += $durationHours;
                }
            }

            $dayHoursData[$d] = [
                'name'           => $cfg['name'],
                'short'          => $cfg['short'],
                'is_closed'      => $isClosed,
                'open_time'      => $openTime,
                'close_time'     => $closeTime,
                'duration_hours' => $durationHours,
            ];
        }
    ?>
    <div id="tab-panel-hours" class="settings-tab-panel hidden space-y-6">

        <!-- Scoped Unbreakable Calendar Grid Layout & Right Navigation Styles -->
        <style>
            .blax-calendar-month-grid {
                display: grid !important;
                grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                gap: 0.5rem !important;
            }
            .blax-calendar-week-header-grid {
                display: grid !important;
                grid-template-columns: 60px repeat(7, minmax(0, 1fr)) !important;
            }
            .hours-main-layout-container {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 1.5rem !important;
            }
            .hours-side-nav-wrapper {
                display: none !important;
            }
            @media (min-width: 1024px) {
                .hours-main-layout-container {
                    flex-direction: row !important;
                    gap: 2rem !important;
                }
                .hours-side-nav-wrapper {
                    display: block !important;
                    width: 16rem !important;
                    flex-shrink: 0 !important;
                    position: sticky !important;
                    top: 6rem !important;
                }
            }
        </style>

        <!-- 2-Column Responsive Container: Main Content + Right Sticky Navigation Bar -->
        <div class="hours-main-layout-container w-full">

            <!-- Main Content Column -->
            <div class="flex-1 min-w-0 max-w-5xl space-y-6 w-full">

                <!-- ═══════════════════════════════════════════════════════════════════════ -->
                <!-- CARD 1: STORE CLOSURE & CUSTOMER VISIBILITY STRATEGY (TOP)              -->
                <!-- ═══════════════════════════════════════════════════════════════════════ -->
                <div id="hoursSectionVisibility" class="scroll-mt-24 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 md:p-8 space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Store Closure &amp; Customer Visibility Strategy</h3>
                        <span id="liveTodayStatusBadge" class="px-3 py-0.5 rounded-full text-xs font-bold <?= !empty($operationalStatus['is_closed_today']) ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30' ?>">
                            <?= esc($operationalStatus['status_label'] ?? 'Open Today') ?>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-sans">
                        Choose how your shop behaves on the customer marketplace when today is marked as closed on your schedule calendar or weekly hours.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Option 1: Recommended Sales-Safe Mode -->
                <label id="visCardSafe" class="cursor-pointer rounded-2xl border-2 <?= !$hideWhenClosed ? 'border-terra-green bg-terra-green/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40' ?> p-4 transition flex items-start gap-3">
                    <input type="radio" name="closure_visibility_mode" value="0" <?= !$hideWhenClosed ? 'checked' : '' ?> onchange="saveClosureVisibilityMode(0)" class="mt-1 accent-emerald-600">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-slate-900 dark:text-white font-display">Keep Visible with "Closed Today" Badge &amp; Lock Checkout</span>
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">Best for Sales</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-sans">
                            Customers can still find your shop, read your calendar note (e.g. 'Annual Planning — Reopens Tomorrow'), browse products, and <strong>Add to Cart for later</strong>. <strong>Checkout, Buy Now, and Printing Requests are strictly disabled</strong> while closed.
                        </p>
                    </div>
                </label>

                <!-- Option 2: Strict Directory Hide Mode -->
                <label id="visCardHide" class="cursor-pointer rounded-2xl border-2 <?= $hideWhenClosed ? 'border-amber-500 bg-amber-500/5' : 'border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40' ?> p-4 transition flex items-start gap-3">
                    <input type="radio" name="closure_visibility_mode" value="1" <?= $hideWhenClosed ? 'checked' : '' ?> onchange="saveClosureVisibilityMode(1)" class="mt-1 accent-amber-600">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-slate-900 dark:text-white font-display">Hide Store from Customer Directory When Closed Today</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-sans">
                            Temporarily hides your shop card from the Customer Shops Directory (<code>/shops</code>) and Featured Shops on the day you are closed, and blocks all ordering on direct links.
                        </p>
                    </div>
                </label>
            </div>
        </div>

                <!-- ═══════════════════════════════════════════════════════════════════════ -->
                <!-- CARD 2: MONTHLY CLOSURE & SCHEDULE NOTES CALENDAR (RESTORED DESIGN)      -->
                <!-- ═══════════════════════════════════════════════════════════════════════ -->
                <div id="hoursSectionCalendar" class="scroll-mt-24 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 md:p-8 space-y-6">

            <!-- Card Master Header with Badges & Toolbar -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-5">
                <div>
                    <!-- Dual Top Badges -->
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">2026 OPERATIONAL PLANNER</span>
                        <span id="calViewBadgeLabel" class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">MONTH VIEW</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white font-display tracking-tight">Monthly Closure &amp; Schedule Notes Calendar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-sans">
                        Click any upcoming date to schedule a store closure or special hours with customer notes. Checking a scheduled date opens its full details. Past dates are locked.
                    </p>
                </div>

                <!-- Calendar Navigation Toolbar -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <!-- + Schedule Date Action Button -->
                    <button type="button" onclick="calOpenNewScheduleModal()" class="px-4 py-2 rounded-xl bg-terra-green text-white text-xs font-bold hover:bg-terra-green/90 transition shadow-2xs active:scale-95 flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        <span>Schedule Date</span>
                    </button>

                    <!-- View Switcher (Month | Week | Agenda) - Month is Active Default -->
                    <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200/70 dark:border-slate-700/70 text-xs font-bold">
                        <button type="button" id="calViewBtn_month" onclick="calSetViewMode('month')" class="px-3 py-1.5 rounded-lg transition-all bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs cursor-pointer">
                            Month
                        </button>
                        <button type="button" id="calViewBtn_week" onclick="calSetViewMode('week')" class="px-3 py-1.5 rounded-lg transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                            Week
                        </button>
                        <button type="button" id="calViewBtn_list" onclick="calSetViewMode('list')" class="px-3 py-1.5 rounded-lg transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                            Agenda
                        </button>
                    </div>

                    <!-- Quick Today Jump -->
                    <button type="button" onclick="calJumpToToday()" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition active:scale-95 cursor-pointer shadow-2xs">
                        Today
                    </button>

                    <!-- Prev / Month Label in Uppercase / Next Navigator -->
                    <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden shadow-2xs">
                        <button type="button" onclick="calNavigate(-1)" class="px-2.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer" title="Previous">
                            <span class="material-symbols-outlined text-[16px] leading-none">chevron_left</span>
                        </button>
                        <span id="calCurrentHeaderTitle" class="px-3.5 py-2 text-xs font-black text-slate-900 dark:text-white font-sans min-w-[130px] text-center uppercase tracking-wider">
                            <?= strtoupper(date('F Y')) ?>
                        </span>
                        <button type="button" onclick="calNavigate(1)" class="px-2.5 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition cursor-pointer" title="Next">
                            <span class="material-symbols-outlined text-[16px] leading-none">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Legend & Scheduled Month Count Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/70 dark:bg-slate-800/40 p-3 rounded-2xl border border-slate-200/60 dark:border-slate-700/60 font-sans">
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <span class="inline-flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Closed / Holiday
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Audit / Maintenance
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Open w/ Note
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> Special Hours
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span> Past Days (Locked)
                    </span>
                </div>
                <span id="calMonthScheduledCount" class="text-xs text-slate-500 dark:text-slate-400 font-sans">
                    0 scheduled days this month
                </span>
            </div>

            <!-- Calendar Display Containers -->
            <div class="w-full">

                <!-- VIEW 1: MONTH VIEW GRID (Default Visible View Matching Image) -->
                <div id="calMonthViewWrapper" class="space-y-2.5">
                    <!-- 7 Weekday Header Pills (Guaranteed 7-Column Layout) -->
                    <div class="blax-calendar-month-grid text-center" style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0.5rem;">
                        <?php foreach (['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'] as $wd): ?>
                            <div class="py-2 rounded-xl bg-slate-100/90 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold font-sans tracking-wider">
                                <?= $wd ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- 7x5 Days Grid with lock icon on past dates (Guaranteed 7-Column Layout) -->
                    <div id="storeScheduleCalendarGrid" class="blax-calendar-month-grid" style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0.5rem;"></div>
                </div>

                <!-- VIEW 2: WEEK VIEW GRID -->
                <div id="calWeekViewWrapper" class="hidden overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <div class="min-w-[680px]">
                        <!-- Week Header Row (GMT + 7 Day Columns) -->
                        <div class="blax-calendar-week-header-grid border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 sticky top-0 z-20" style="display: grid; grid-template-columns: 60px repeat(7, minmax(0, 1fr));">
                            <div class="p-2 text-[10px] font-mono font-bold text-slate-400 text-center border-r border-slate-200/80 dark:border-slate-800 flex items-center justify-center">
                                GMT+08
                            </div>
                            <?php for ($i = 0; $i < 7; $i++): ?>
                                <div id="calWeekColHeader_<?= $i ?>" class="p-2 text-center border-r last:border-r-0 border-slate-200/80 dark:border-slate-800 flex flex-col items-center justify-center gap-1">
                                    <span id="calWeekDayName_<?= $i ?>" class="text-[11px] font-bold text-slate-500 font-mono">SUN</span>
                                    <span id="calWeekDayBadge_<?= $i ?>" class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black text-slate-800 dark:text-slate-200">18</span>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- All-Day Row for Full-day closures & announcements -->
                        <div class="blax-calendar-week-header-grid border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20 text-xs" style="display: grid; grid-template-columns: 60px repeat(7, minmax(0, 1fr));">
                            <div class="p-2 text-[10px] font-mono font-semibold text-slate-400 text-center border-r border-slate-200/80 dark:border-slate-800 flex items-center justify-center">
                                All-day
                            </div>
                            <?php for ($i = 0; $i < 7; $i++): ?>
                                <div id="calWeekAllDayCol_<?= $i ?>" class="p-1 border-r last:border-r-0 border-slate-200/80 dark:border-slate-800 min-h-[36px] space-y-1"></div>
                            <?php endfor; ?>
                        </div>

                        <!-- Hourly Grid: 6 AM to 9 PM (16 business hours) -->
                        <div id="calWeekHoursScrollContainer" class="relative max-h-[500px] overflow-y-auto">
                            <!-- Real-time Horizontal Red Indicator Line -->
                            <div id="calRealTimeIndicator" class="hidden absolute left-0 right-0 z-30 pointer-events-none flex items-center">
                                <div id="calRealTimeDot" class="w-2.5 h-2.5 rounded-full bg-rose-500 -ml-1.5 shadow-xs"></div>
                                <div class="flex-1 h-[2px] bg-rose-500"></div>
                            </div>

                            <?php
                                $hoursSlots = [
                                    '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM',
                                    '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM',
                                    '6 PM', '7 PM', '8 PM', '9 PM'
                                ];
                            ?>
                            <?php foreach ($hoursSlots as $slotIdx => $slotLabel): ?>
                                <div class="blax-calendar-week-header-grid border-b border-slate-100 dark:border-slate-800/60 min-h-[40px]" style="display: grid; grid-template-columns: 60px repeat(7, minmax(0, 1fr));">
                                    <div class="px-2 py-1 text-[10px] font-mono text-slate-400 text-right border-r border-slate-200/80 dark:border-slate-800 select-none -mt-2">
                                        <?= $slotLabel ?>
                                    </div>
                                    <?php for ($d = 0; $d < 7; $d++): ?>
                                        <div id="calWeekSlot_<?= $d ?>_<?= $slotIdx ?>" onclick="calOnWeekSlotClick(<?= $d ?>, <?= $slotIdx ?>)" class="relative border-r last:border-r-0 border-slate-100 dark:border-slate-800/60 hover:bg-emerald-500/5 transition-colors cursor-pointer group">
                                            <span class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 text-[10px] text-terra-green font-mono pointer-events-none">
                                                +
                                            </span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- VIEW 3: AGENDA / LIST VIEW -->
                <div id="calAgendaViewWrapper" class="hidden space-y-3">
                    <div class="flex items-center justify-between pb-1 border-b border-slate-200/80 dark:border-slate-800">
                        <h4 class="text-xs font-bold uppercase font-mono text-slate-600 dark:text-slate-300">Upcoming Scheduled Overrides</h4>
                        <span id="calAgendaEntriesCount" class="text-xs text-slate-400"></span>
                    </div>
                    <div id="calListScheduleItems" class="space-y-2.5"></div>
                </div>

            </div>

        </div>

                <!-- ═══════════════════════════════════════════════════════════════════════ -->
                <!-- CARD 3: 7-DAY WEEKLY RECURRING OPERATING HOURS                          -->
                <!-- ═══════════════════════════════════════════════════════════════════════ -->
                <div id="hoursSectionWeekly" class="scroll-mt-24 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 md:p-8 space-y-6">

            <!-- Card Header & Summary Stat -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-5">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-terra-green animate-pulse"></span>
                        <span class="text-xs font-mono font-bold text-terra-green uppercase tracking-wider">Weekly Operating Schedule</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white font-display tracking-tight">Weekly Recurring Operating Hours</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-sans">
                        Configure your standard store counter schedule. Individual dates follow these hours unless overridden on the calendar above.
                    </p>
                </div>

                <!-- Total Hours Stat & Live Feedback Indicator -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="px-3.5 py-1.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs font-mono text-center shadow-2xs">
                        <span class="text-slate-400 block text-[10px]">TOTAL ACTIVE</span>
                        <span id="weeklyTotalHoursDisplay" class="font-bold text-slate-900 dark:text-white"><?= $totalWeeklyOpenHours ?> hrs/week</span>
                    </div>
                    <div id="hoursFeedback" class="flex items-center gap-1.5 text-xs text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-3 py-1.5 rounded-2xl font-semibold transition-all" aria-live="polite">
                        <span class="material-symbols-outlined text-[16px] text-terra-green">check_circle</span>
                        <span>Auto-saved</span>
                    </div>
                </div>
            </div>

            <!-- Quick Batch Presets Toolbar -->
            <div class="flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-700/60 flex-wrap">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 font-sans">Quick Presets:</span>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="calCopyMondayToWeekdays()" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:border-terra-green transition cursor-pointer shadow-2xs flex items-center gap-1.5 active:scale-95">
                        <span class="material-symbols-outlined text-[15px] text-terra-green">content_copy</span>
                        <span>Copy Monday to Weekdays</span>
                    </button>
                    <button type="button" onclick="calApplyStandardHoursPreset('09:00', '17:00')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:border-terra-green transition cursor-pointer shadow-2xs active:scale-95">
                        9:00 AM – 5:00 PM
                    </button>
                    <button type="button" onclick="calApplyStandardHoursPreset('08:00', '18:00')" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:border-terra-green transition cursor-pointer shadow-2xs active:scale-95">
                        8:00 AM – 6:00 PM
                    </button>
                    <button type="button" onclick="calSetWeekendClosedPreset()" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition cursor-pointer shadow-2xs active:scale-95">
                        Close Weekends
                    </button>
                </div>
            </div>

            <!-- 7-Day Visual Timetable Cards (Monday to Sunday) -->
            <div class="space-y-3">
                <?php foreach ($dayHoursData as $dayNum => $day): ?>
                    <div id="dayCardWrapper_<?= $dayNum ?>" class="p-4 rounded-2xl border transition-all <?= $day['is_closed'] ? 'bg-slate-50/60 dark:bg-slate-800/30 border-slate-200/70 dark:border-slate-800' : 'bg-white dark:bg-slate-800/70 border-slate-200/90 dark:border-slate-700/80 shadow-xs' ?>">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                            <!-- Left: Day Badge, Status Pill, and Summary Text -->
                            <div class="flex items-center gap-3.5 min-w-[230px]">
                                <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs font-mono <?= $day['is_closed'] ? 'bg-slate-200/80 dark:bg-slate-700 text-slate-600 dark:text-slate-400' : 'bg-terra-green/10 text-terra-green border border-terra-green/20' ?>">
                                    <?= esc($day['short']) ?>
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white font-display"><?= esc($day['name']) ?></h4>
                                        <span id="dayStatusBadge_<?= $dayNum ?>" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase font-mono <?= $day['is_closed'] ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20' : 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20' ?>">
                                            <?= $day['is_closed'] ? 'Closed' : 'Open' ?>
                                        </span>
                                    </div>
                                    <p id="dayHoursTextSummary_<?= $dayNum ?>" class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                        <?= $day['is_closed'] ? 'Closed all day' : (date('g:i A', strtotime("1970-01-01 {$day['open_time']}:00")) . ' – ' . date('g:i A', strtotime("1970-01-01 {$day['close_time']}:00")) . " ({$day['duration_hours']} hrs)") ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Center: Proportional 24-Hour Daytime Timeline Bar -->
                            <div class="flex-1 max-w-md hidden sm:block">
                                <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mb-1">
                                    <span>12 AM</span><span>6 AM</span><span>12 PM</span><span>6 PM</span><span>12 AM</span>
                                </div>
                                <div class="relative w-full h-2.5 rounded-full bg-slate-200 dark:bg-slate-700/60 overflow-hidden">
                                    <?php
                                        $openPercent  = 0;
                                        $widthPercent = 0;
                                        if (!$day['is_closed'] && $day['open_time'] && $day['close_time']) {
                                            $partsO = explode(':', $day['open_time']);
                                            $partsC = explode(':', $day['close_time']);
                                            $minO   = ((int) $partsO[0]) * 60 + ((int) ($partsO[1] ?? 0));
                                            $minC   = ((int) $partsC[0]) * 60 + ((int) ($partsC[1] ?? 0));
                                            if ($minC > $minO) {
                                                $openPercent  = round(($minO / 1440) * 100, 1);
                                                $widthPercent = round((($minC - $minO) / 1440) * 100, 1);
                                            }
                                        }
                                    ?>
                                    <div id="dayVisualBar_<?= $dayNum ?>" class="absolute top-0 bottom-0 rounded-full bg-terra-green transition-all" style="left: <?= $openPercent ?>%; width: <?= $widthPercent ?>%;"></div>
                                </div>
                            </div>

                            <!-- Right: Inline Time Inputs & Enable/Disable Toggle -->
                            <div class="flex items-center gap-3 shrink-0">
                                <!-- Time Selectors -->
                                <div id="dayInputsRow_<?= $dayNum ?>" class="flex items-center gap-1.5 <?= $day['is_closed'] ? 'opacity-40 pointer-events-none' : '' ?>">
                                    <label class="sr-only" for="day_open_<?= $dayNum ?>">Opening time for <?= esc($day['name']) ?></label>
                                    <input type="time" id="day_open_<?= $dayNum ?>" value="<?= esc($day['open_time']) ?>" onchange="calOnDayTimeChange(<?= $dayNum ?>)" class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-medium text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green/30">
                                    <span class="text-xs text-slate-400 font-mono">–</span>
                                    <label class="sr-only" for="day_close_<?= $dayNum ?>">Closing time for <?= esc($day['name']) ?></label>
                                    <input type="time" id="day_close_<?= $dayNum ?>" value="<?= esc($day['close_time']) ?>" onchange="calOnDayTimeChange(<?= $dayNum ?>)" class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-medium text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green/30">
                                </div>

                                <!-- Enable / Disable Switch -->
                                <label class="relative inline-flex items-center cursor-pointer shrink-0" title="Toggle open/closed for <?= esc($day['name']) ?>">
                                    <input type="checkbox" id="day_closed_<?= $dayNum ?>" <?= $day['is_closed'] ? 'checked' : '' ?> onchange="calOnDayClosedToggle(<?= $dayNum ?>, this.checked)" class="sr-only peer">
                                    <div class="w-11 h-6 bg-emerald-500 rounded-full peer peer-checked:bg-slate-300 dark:peer-checked:bg-slate-700 transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

                </div>

            </div>

            <!-- Right-Side Sticky Quick-Jump Navigation Bar (Desktop) -->
            <aside class="hours-side-nav-wrapper space-y-4">
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-5 space-y-4">
                    
                    <!-- Header -->
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                        <span class="material-symbols-outlined text-[18px] text-terra-green">format_list_bulleted</span>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">On This Page</span>
                    </div>

                    <!-- Navigation Items List -->
                    <nav class="space-y-1.5" aria-label="Operating Hours Page Sections">
                        <!-- Option 1: Visibility Strategy -->
                        <button type="button" onclick="scrollToHoursSection('hoursSectionVisibility')" id="navLinkVisibility" class="hours-nav-item w-full text-left px-3 py-2.5 rounded-xl border-l-2 border-terra-green bg-emerald-50/50 dark:bg-emerald-950/20 text-terra-green hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all flex items-start gap-2.5 group cursor-pointer active:scale-98">
                            <span class="material-symbols-outlined text-[18px] text-terra-green transition-colors mt-0.5">visibility</span>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors leading-tight">Customer Visibility</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5 truncate">Closure mode &amp; lock</span>
                            </div>
                        </button>

                        <!-- Option 2: Monthly Calendar -->
                        <button type="button" onclick="scrollToHoursSection('hoursSectionCalendar')" id="navLinkCalendar" class="hours-nav-item w-full text-left px-3 py-2.5 rounded-xl border-l-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all flex items-start gap-2.5 group cursor-pointer active:scale-98">
                            <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-terra-green transition-colors mt-0.5">calendar_month</span>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-terra-green transition-colors leading-tight">Closure Calendar</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5 truncate">Schedule notes &amp; holidays</span>
                            </div>
                        </button>

                        <!-- Option 3: Weekly Recurring Hours -->
                        <button type="button" onclick="scrollToHoursSection('hoursSectionWeekly')" id="navLinkWeekly" class="hours-nav-item w-full text-left px-3 py-2.5 rounded-xl border-l-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all flex items-start gap-2.5 group cursor-pointer active:scale-98">
                            <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-terra-green transition-colors mt-0.5">schedule</span>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-terra-green transition-colors leading-tight">Weekly Hours</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5 truncate">7-day regular schedule</span>
                            </div>
                        </button>
                    </nav>

                    <!-- Quick Action / Status Snippet -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-2.5">
                        <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-slate-400 font-sans font-medium">Store Status</span>
                                <span class="font-bold font-mono text-[10px] uppercase <?= !empty($operationalStatus['is_closed_today']) ? 'text-rose-600' : 'text-emerald-600' ?>">
                                    <?= esc($operationalStatus['status_label'] ?? 'Open') ?>
                                </span>
                            </div>
                            <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                <?= $totalWeeklyOpenHours ?> hrs active this week
                            </div>
                        </div>

                        <button type="button" onclick="calOpenNewScheduleModal()" class="w-full py-2.5 px-3 rounded-xl bg-terra-green hover:bg-terra-green/90 text-white text-xs font-bold transition-all shadow-2xs flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            <span>Schedule Date</span>
                        </button>
                    </div>

                </div>
            </aside>

        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL: SCHEDULE HERO DIALOG (GOOGLE CALENDAR INSPIRED POPOVER)          -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div id="calHeroInputModal" class="hidden fixed inset-0 z-[9995] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs transition-opacity animate-in fade-in duration-150">
            <div class="relative w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-7 space-y-5 animate-in zoom-in-95 duration-150">

                <!-- Header with Drag Handle & Close -->
                <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <span class="material-symbols-outlined text-[18px]">event_available</span>
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Schedule Planner</span>
                    </div>
                    <button type="button" onclick="calCloseHeroModal()" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <div class="space-y-4">

                    <!-- Hero Title Input -->
                    <div class="space-y-1">
                        <label class="sr-only" for="calHeroTitleInput">Schedule Title / Reason</label>
                        <input type="text" id="calHeroTitleInput" placeholder="Add title or reason (e.g. Annual Inventory Count)..." class="w-full text-base font-bold bg-transparent border-b-2 border-slate-200 dark:border-slate-700 focus:border-terra-green focus:outline-none py-1.5 transition text-slate-900 dark:text-white placeholder:text-slate-400 font-sans">
                    </div>

                    <!-- Segmented Pill Selector (Store Closure | Special Hours | Announcement) -->
                    <div class="flex items-center gap-2 pt-1 flex-wrap">
                        <button type="button" id="calPill_closed" onclick="calSelectHeroTab('closed')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-terra-green text-white shadow-2xs cursor-pointer">
                            Store Closure
                        </button>
                        <button type="button" id="calPill_hours" onclick="calSelectHeroTab('hours')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                            Special Hours
                        </button>
                        <button type="button" id="calPill_notice" onclick="calSelectHeroTab('notice')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white cursor-pointer">
                            Notice Only
                        </button>
                    </div>

                    <!-- Google Row Items -->
                    <div class="space-y-3 pt-2 text-xs text-slate-700 dark:text-slate-300">

                        <!-- Row 1: Time & Date -->
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 mt-1 shrink-0">schedule</span>
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span id="calHeroDateSummaryLabel" class="font-bold text-slate-900 dark:text-white font-sans">Selected Date</span>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" id="calHeroIsAllDayInput" checked onchange="calToggleHeroAllDay(this.checked)" class="rounded text-terra-green focus:ring-terra-green border-slate-300 dark:border-slate-600">
                                        <span class="text-xs text-slate-500 font-medium">All day</span>
                                    </label>
                                </div>
                                <div id="calHeroTimeRangeRow" class="hidden flex items-center gap-2 pt-1">
                                    <input type="time" id="calHeroOpenTime" value="09:00" class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-medium text-slate-900 dark:text-white">
                                    <span class="text-slate-400">–</span>
                                    <input type="time" id="calHeroCloseTime" value="17:00" class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-medium text-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Customer Storefront Visibility Behavior -->
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 mt-1 shrink-0">storefront</span>
                            <div class="flex-1 space-y-1">
                                <span class="text-xs font-bold text-slate-900 dark:text-white block font-sans">Customer Storefront Behavior</span>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-sans leading-relaxed">
                                    While closed, your shop remains visible to browse with a "Closed Today" banner. Checkout and custom printing orders are locked until reopening.
                                </p>
                            </div>
                        </div>

                        <!-- Row 3: Theme Color Picker -->
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 shrink-0">palette</span>
                            <div class="flex items-center gap-2.5">
                                <button type="button" onclick="calSelectHeroColor('amber')" id="calHeroColor_amber" class="w-6 h-6 rounded-full bg-amber-500 ring-2 ring-offset-2 ring-amber-500 cursor-pointer shadow-xs" title="Closure / Holiday (Amber)"></button>
                                <button type="button" onclick="calSelectHeroColor('rose')" id="calHeroColor_rose" class="w-6 h-6 rounded-full bg-rose-500 hover:ring-2 hover:ring-offset-2 hover:ring-rose-500 cursor-pointer shadow-xs" title="Audit / Maintenance (Rose)"></button>
                                <button type="button" onclick="calSelectHeroColor('emerald')" id="calHeroColor_emerald" class="w-6 h-6 rounded-full bg-emerald-500 hover:ring-2 hover:ring-offset-2 hover:ring-emerald-500 cursor-pointer shadow-xs" title="Open Notice (Emerald)"></button>
                                <button type="button" onclick="calSelectHeroColor('sky')" id="calHeroColor_sky" class="w-6 h-6 rounded-full bg-sky-500 hover:ring-2 hover:ring-offset-2 hover:ring-sky-500 cursor-pointer shadow-xs" title="Special Hours (Sky)"></button>
                            </div>
                        </div>

                        <!-- Expandable More Options Panel (Multi-Day Range & Presets) -->
                        <div id="calHeroMoreOptionsPanel" class="hidden pt-2 border-t border-slate-200 dark:border-slate-700 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] text-slate-400 block mb-0.5" for="calHeroStartDateInput">Start Date</label>
                                    <input type="date" id="calHeroStartDateInput" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-[10px] text-slate-400 block mb-0.5" for="calHeroEndDateInput">End Date (Inclusive)</label>
                                    <input type="date" id="calHeroEndDateInput" class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase font-mono text-slate-400 block">1-Tap Presets</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" onclick="calApplyHeroPreset('Annual Planning', 'closed', 'amber')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-medium hover:border-terra-green transition cursor-pointer">Annual Planning</button>
                                    <button type="button" onclick="calApplyHeroPreset('Inventory Audit', 'closed', 'rose')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-medium hover:border-rose-500 transition cursor-pointer">Stock Count</button>
                                    <button type="button" onclick="calApplyHeroPreset('Holiday Closure', 'closed', 'amber')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-medium hover:border-amber-500 transition cursor-pointer">Holiday</button>
                                    <button type="button" onclick="calApplyHeroPreset('Facility Renovation', 'closed', 'sky')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px] font-medium hover:border-sky-500 transition cursor-pointer">Renovation</button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-200/80 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <button type="button" id="calHeroMoreOptionsBtn" onclick="calToggleHeroMoreOptions()" class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                            More options
                        </button>
                        <button type="button" id="calHeroDeleteBtn" onclick="calDeleteFromHeroModal()" class="hidden text-xs font-bold text-rose-600 hover:text-rose-700 transition cursor-pointer flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                            <span>Clear</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="calCloseHeroModal()" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="button" id="calHeroSaveBtn" onclick="calSaveHeroModal()" class="px-5 py-2 rounded-xl bg-terra-green hover:bg-terra-green/90 text-white text-xs font-bold transition shadow-2xs active:scale-95 cursor-pointer">
                            Save Schedule
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: SERVICES & ALERTS                                               -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tab-panel-preferences" class="settings-tab-panel hidden space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-6 md:p-8 space-y-8 max-w-3xl">
            
            <div class="flex items-start justify-between border-b border-slate-200/80 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Store Services &amp; Notification Preferences</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">Toggle optional services like document printing and configure automatic merchant alerts.</p>
                </div>
            </div>

            <!-- Service Offerings -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Service Modules</h4>
                    <span id="servicesFeedback" class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold"></span>
                </div>

                <div class="space-y-3">
                    <!-- Printing Service Toggle -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-4 transition-all hover:bg-slate-100/70 dark:hover:bg-slate-800">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-terra-green">print</span>
                                <span class="font-bold text-sm text-slate-900 dark:text-white font-display">Document &amp; Photo Printing Services</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-sans">
                                Allows customers in Polomolok to submit custom printing requests (PDF, DOCX, photos) directly to your store counter.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" id="service_offers_printing" data-module="offers_printing" <?= !empty($shop['offers_printing']) ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-full peer peer-checked:bg-terra-green transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-5 peer-checked:after:border-white"></div>
                        </label>
                    </div>

                    <!-- Doorstep Delivery Service Toggle -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-4 transition-all hover:bg-slate-100/70 dark:hover:bg-slate-800">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-sky-600">local_shipping</span>
                                <span class="font-bold text-sm text-slate-900 dark:text-white font-display">Doorstep Delivery Services</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-sans">
                                Allows customers in Polomolok to request direct doorstep delivery (COD &amp; Online) straight to their residence or business address.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" id="service_offers_delivery" data-module="offers_delivery" <?= (isset($shop['offers_delivery']) && (int)$shop['offers_delivery'] === 0) ? '' : 'checked' ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-full peer peer-checked:bg-terra-green transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-5 peer-checked:after:border-white"></div>
                        </label>
                    </div>

                    <!-- Store Pick-up Service Toggle -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-4 transition-all hover:bg-slate-100/70 dark:hover:bg-slate-800">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] text-terra-earth dark:text-amber-500">storefront</span>
                                <span class="font-bold text-sm text-slate-900 dark:text-white font-display">Store Pick-up Services</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-sans">
                                Allows customers to place orders online and personally claim packages at your physical counter with verified pick-up passes.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" id="service_offers_pickup" data-module="offers_pickup" <?= (isset($shop['offers_pickup']) && (int)$shop['offers_pickup'] === 0) ? '' : 'checked' ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-full peer peer-checked:bg-terra-green transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-5 peer-checked:after:border-white"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Merchant Notification Alerts -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Merchant Notifications</h4>
                    <span id="notifFeedback" class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold"></span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 overflow-hidden">
                    
                    <!-- New Orders -->
                    <div class="p-4 flex items-center justify-between gap-4">
                        <div class="space-y-0.5">
                            <p class="font-bold text-xs text-slate-900 dark:text-white">New Order Notifications</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Receive in-app alerts and notifications whenever a customer places an order.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" id="pref_new_orders" <?= !empty($notifPrefs['new_orders']) ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-full peer peer-checked:bg-terra-green transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-5 peer-checked:after:border-white"></div>
                        </label>
                    </div>

                    <!-- Low Stock Alerts -->
                    <div class="p-4 flex items-center justify-between gap-4">
                        <div class="space-y-0.5">
                            <p class="font-bold text-xs text-slate-900 dark:text-white">Low Stock Inventory Warnings</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Alert me when any product stock count drops to 5 units or below.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" id="pref_low_stock" <?= !empty($notifPrefs['low_stock']) ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-full peer peer-checked:bg-terra-green transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-5 peer-checked:after:border-white"></div>
                        </label>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- CSRF Hidden Form for AJAX -->
<form id="settingsCsrfForm" class="hidden"><?= csrf_field() ?></form>

<!-- Toast Notification -->
<div id="settingsToast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl shadow-xl text-xs font-bold text-white transition-all duration-300 transform"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
(function () {
    // ── 1. Tab Switching with Hash Support ─────────────────────────
    const tabs = ['profile', 'payment', 'hours', 'preferences'];

    window.switchSettingsTab = function (tabName) {
        if (!tabs.includes(tabName)) tabName = 'profile';

        tabs.forEach(t => {
            const panel = document.getElementById('tab-panel-' + t);
            const btn = document.getElementById('tab-btn-' + t);

            if (t === tabName) {
                if (panel) panel.classList.remove('hidden');
                if (btn) {
                    btn.classList.remove('border-transparent', 'text-slate-500');
                    btn.classList.add('border-terra-green', 'text-terra-green');
                }
            } else {
                if (panel) panel.classList.add('hidden');
                if (btn) {
                    btn.classList.remove('border-terra-green', 'text-terra-green');
                    btn.classList.add('border-transparent', 'text-slate-500');
                }
            }
        });

        if (tabName === 'hours') {
            if (typeof renderStoreScheduleCalendar === 'function') {
                renderStoreScheduleCalendar();
            }
        }

        // Sync URL hash without triggering scroll jump
        history.replaceState(null, null, '#' + tabName);
    };

    // Auto-select tab based on URL query param (?tab=...) or hash (#...) on page load
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        const hash = window.location.hash.replace('#', '');

        if (tabParam && tabs.includes(tabParam)) {
            switchSettingsTab(tabParam);
        } else if (tabs.includes(hash)) {
            switchSettingsTab(hash);
        }

        // Always initialize calendar on page load
        if (typeof renderStoreScheduleCalendar === 'function') {
            renderStoreScheduleCalendar();
        }
    });

    // ── 2. Toast Notifications ─────────────────────────────────────
    function csrfToken() {
        return document.querySelector('#settingsCsrfForm [name="csrf_test_name"]').value;
    }

    var toast = document.getElementById('settingsToast');
    var toastTimer = null;

    function showToast(message, isError) {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden', 'bg-rose-600', 'bg-terra-green');
        toast.classList.add(isError ? 'bg-rose-600' : 'bg-terra-green');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toast.classList.add('hidden'); }, 3500);
    }

    async function postSettings(body) {
        try {
            var resp = await fetch("<?= base_url('tenant/settings/save') ?>", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: body
            });
            var data = await resp.json();
            showToast(data.message || (data.success ? 'Saved.' : 'Save failed.'), !data.success);
            return data.success;
        } catch (err) {
            showToast('Could not reach the server.', true);
            return false;
        }
    }

    // ── 3. Character Counter for Description ───────────────────────
    var descEl = document.getElementById('description');
    var charCountEl = document.getElementById('charCount');
    if (descEl && charCountEl) {
        function updateCharCount() {
            charCountEl.textContent = descEl.value.length + ' / 500';
        }
        descEl.addEventListener('input', updateCharCount);
        updateCharCount();
    }

    // ── 4. GCash Number Formatter ──────────────────────────────────
    var gcashEl = document.getElementById('gcash_number');
    if (gcashEl) {
        gcashEl.addEventListener('input', function (e) {
            var val = e.target.value.replace(/\D/g, '');
            if (val.length > 11) val = val.substring(0, 11);
            if (val.length > 4 && val.length <= 7) {
                val = val.substring(0, 4) + ' ' + val.substring(4);
            } else if (val.length > 7) {
                val = val.substring(0, 4) + ' ' + val.substring(4, 7) + ' ' + val.substring(7);
            }
            e.target.value = val;
        });
    }

    // ── 5. Instant Logo Preview & Immediate Submit ─────────────────
    var logoInput = document.getElementById('logoInput');
    var logoPreviewImg = document.getElementById('logoPreviewImg');
    var logoInitialsSpan = document.getElementById('logoInitialsSpan');
    var logoUploadHint = document.getElementById('logoUploadHint');

    if (logoInput) {
        logoInput.addEventListener('change', function () {
            if (logoInput.files && logoInput.files[0]) {
                var file = logoInput.files[0];
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (logoPreviewImg) {
                        logoPreviewImg.src = e.target.result;
                        logoPreviewImg.classList.remove('hidden');
                    }
                    if (logoInitialsSpan) logoInitialsSpan.classList.add('hidden');
                    if (logoUploadHint) {
                        logoUploadHint.textContent = 'Uploading logo...';
                        logoUploadHint.classList.remove('hidden');
                    }
                    document.getElementById('logoForm').submit();
                };
                reader.readAsDataURL(file);
            }
        });
    }

            // ── 6. Business Hours Auto-Save & Visual Timetable ─────────────
    var isSavingHours = false;
    var pendingHoursPayload = null;
    var hoursSaveTimer = null;

    function collectHours() {
        var fd = new FormData();
        fd.append('csrf_test_name', csrfToken());
        fd.append('section', 'hours');

        for (var d = 0; d <= 6; d++) {
            var closedToggle = document.getElementById('day_closed_' + d);
            var isClosed = (closedToggle && closedToggle.checked) ? 1 : 0;
            var openInput = document.getElementById('day_open_' + d);
            var closeInput = document.getElementById('day_close_' + d);

            var openVal = openInput ? openInput.value : '08:00';
            var closeVal = closeInput ? closeInput.value : '18:00';

            fd.append('closed_' + d, isClosed);
            fd.append('open_' + d, isClosed ? '' : openVal);
            fd.append('close_' + d, isClosed ? '' : closeVal);
        }
        return fd;
    }

    function wireHours() {
        for (var d = 0; d <= 6; d++) {
            (function(dayNum) {
                var openEl = document.getElementById('day_open_' + dayNum);
                var closeEl = document.getElementById('day_close_' + dayNum);
                var closedEl = document.getElementById('day_closed_' + dayNum);

                if (openEl) {
                    openEl.addEventListener('change', function() { calOnDayTimeChange(dayNum); });
                }
                if (closeEl) {
                    closeEl.addEventListener('change', function() { calOnDayTimeChange(dayNum); });
                }
                if (closedEl) {
                    closedEl.addEventListener('change', function(e) { calOnDayClosedToggle(dayNum, e.target.checked); });
                }
            })(d);
        }
    }

    function calFormatTimeString(timeStr) {
        if (!timeStr) return '';
        var parts = timeStr.split(':');
        if (parts.length < 2) return timeStr;
        var h = parseInt(parts[0], 10);
        var m = parts[1];
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        if (h === 0) h = 12;
        return h + ':' + m + ' ' + ampm;
    }

    function calParseDateKey(dateKey) {
        var p = dateKey.split('-');
        return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
    }

    window.calOnDayClosedToggle = function(dayNum, isClosed) {
        var card = document.getElementById('dayCardWrapper_' + dayNum);
        var badge = document.getElementById('dayStatusBadge_' + dayNum);
        var summary = document.getElementById('dayHoursTextSummary_' + dayNum);
        var inputsRow = document.getElementById('dayInputsRow_' + dayNum);
        var bar = document.getElementById('dayVisualBar_' + dayNum);

        if (isClosed) {
            if (badge) {
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase font-mono bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                badge.textContent = 'Closed';
            }
            if (summary) summary.textContent = 'Closed all day';
            if (inputsRow) inputsRow.classList.add('opacity-40', 'pointer-events-none');
            if (bar) { bar.style.left = '0%'; bar.style.width = '0%'; }
            if (card) {
                card.className = 'p-4 rounded-2xl border transition-all bg-slate-50/60 dark:bg-slate-800/30 border-slate-200/70 dark:border-slate-800';
            }
        } else {
            if (badge) {
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase font-mono bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20';
                badge.textContent = 'Open';
            }
            if (inputsRow) inputsRow.classList.remove('opacity-40', 'pointer-events-none');
            if (card) {
                card.className = 'p-4 rounded-2xl border transition-all bg-white dark:bg-slate-800/70 border-slate-200/90 dark:border-slate-700/80 shadow-xs';
            }
            calUpdateDayVisual(dayNum);
        }

        calAutoSaveHours();
    };

    window.calOnDayTimeChange = function(dayNum) {
        calUpdateDayVisual(dayNum);
        calAutoSaveHours();
    };

    function calUpdateDayVisual(dayNum) {
        var openEl = document.getElementById('day_open_' + dayNum);
        var closeEl = document.getElementById('day_close_' + dayNum);
        var summary = document.getElementById('dayHoursTextSummary_' + dayNum);
        var bar = document.getElementById('dayVisualBar_' + dayNum);

        if (!openEl || !closeEl) return;
        var openVal = openEl.value || '08:00';
        var closeVal = closeEl.value || '18:00';

        var pO = openVal.split(':');
        var pC = closeVal.split(':');
        var minO = parseInt(pO[0], 10) * 60 + parseInt(pO[1] || '0', 10);
        var minC = parseInt(pC[0], 10) * 60 + parseInt(pC[1] || '0', 10);

        if (minC > minO) {
            var dur = ((minC - minO) / 60).toFixed(1);
            if (summary) {
                summary.textContent = calFormatTimeString(openVal) + ' – ' + calFormatTimeString(closeVal) + ' (' + dur + ' hrs)';
            }
            if (bar) {
                var lPct = ((minO / 1440) * 100).toFixed(1);
                var wPct = (((minC - minO) / 1440) * 100).toFixed(1);
                bar.style.left = lPct + '%';
                bar.style.width = wPct + '%';
            }
        }
        calUpdateWeeklyTotalDisplay();
    }

    function calUpdateWeeklyTotalDisplay() {
        var total = 0;
        for (var d = 0; d <= 6; d++) {
            var closedToggle = document.getElementById('day_closed_' + d);
            if (closedToggle && !closedToggle.checked) {
                var openEl = document.getElementById('day_open_' + d);
                var closeEl = document.getElementById('day_close_' + d);
                if (openEl && closeEl && openEl.value && closeEl.value) {
                    var pO = openEl.value.split(':');
                    var pC = closeEl.value.split(':');
                    var minO = parseInt(pO[0], 10) * 60 + parseInt(pO[1] || '0', 10);
                    var minC = parseInt(pC[0], 10) * 60 + parseInt(pC[1] || '0', 10);
                    if (minC > minO) {
                        total += (minC - minO) / 60;
                    }
                }
            }
        }
        var totalEl = document.getElementById('weeklyTotalHoursDisplay');
        if (totalEl) totalEl.textContent = total.toFixed(1) + ' hrs/week';
    }

    function calAutoSaveHours() {
        var feedback = document.getElementById('hoursFeedback');
        if (feedback) {
            feedback.innerHTML = '<span class="material-symbols-outlined text-[16px] text-amber-500 animate-spin">sync</span><span>Saving...</span>';
            feedback.className = 'flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 px-3 py-1.5 rounded-2xl font-semibold transition-all';
        }

        clearTimeout(hoursSaveTimer);
        hoursSaveTimer = setTimeout(function() {
            var payload = collectHours();
            if (isSavingHours) {
                pendingHoursPayload = payload;
                return;
            }
            executeHoursSave(payload);
        }, 350);
    }

    async function executeHoursSave(payload) {
        isSavingHours = true;
        var feedback = document.getElementById('hoursFeedback');

        try {
            var resp = await fetch("<?= base_url('tenant/settings/save') ?>", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: payload
            });
            var data = await resp.json();

            if (data.csrf_hash) {
                var csrfInput = document.querySelector('#settingsCsrfForm [name="csrf_test_name"]');
                if (csrfInput) csrfInput.value = data.csrf_hash;
            }

            if (data.success) {
                if (feedback) {
                    feedback.innerHTML = '<span class="material-symbols-outlined text-[16px] text-terra-green">check_circle</span><span>Auto-saved</span>';
                    feedback.className = 'flex items-center gap-1.5 text-xs text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-3 py-1.5 rounded-2xl font-semibold transition-all';
                }
                if (data.operational_status) {
                    updateLiveTodayBadge(data.operational_status);
                }
            } else {
                if (feedback) {
                    feedback.innerHTML = '<span class="material-symbols-outlined text-[16px] text-rose-500">error</span><span>Save failed</span>';
                    feedback.className = 'flex items-center gap-1.5 text-xs text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 px-3 py-1.5 rounded-2xl font-semibold transition-all';
                }
                showToast(data.message || 'Could not save operating hours.', true);
            }
        } catch (err) {
            if (feedback) {
                feedback.innerHTML = '<span class="material-symbols-outlined text-[16px] text-rose-500">error</span><span>Network error</span>';
            }
        } finally {
            isSavingHours = false;
            if (pendingHoursPayload) {
                var nextPayload = pendingHoursPayload;
                pendingHoursPayload = null;
                executeHoursSave(nextPayload);
            }
        }
    }

    window.calCopyMondayToWeekdays = function() {
        var monClosed = document.getElementById('day_closed_1') ? document.getElementById('day_closed_1').checked : false;
        var monOpen = document.getElementById('day_open_1') ? document.getElementById('day_open_1').value : '08:00';
        var monClose = document.getElementById('day_close_1') ? document.getElementById('day_close_1').value : '18:00';

        [2, 3, 4, 5].forEach(function(d) {
            var closedEl = document.getElementById('day_closed_' + d);
            var openEl = document.getElementById('day_open_' + d);
            var closeEl = document.getElementById('day_close_' + d);

            if (closedEl) closedEl.checked = monClosed;
            if (openEl) openEl.value = monOpen;
            if (closeEl) closeEl.value = monClose;

            calOnDayClosedToggle(d, monClosed);
        });

        showToast('Monday schedule copied to Tuesday – Friday.');
    };

    window.calApplyStandardHoursPreset = function(openTime, closeTime) {
        [1, 2, 3, 4, 5].forEach(function(d) {
            var closedEl = document.getElementById('day_closed_' + d);
            var openEl = document.getElementById('day_open_' + d);
            var closeEl = document.getElementById('day_close_' + d);

            if (closedEl) closedEl.checked = false;
            if (openEl) openEl.value = openTime;
            if (closeEl) closeEl.value = closeTime;

            calOnDayClosedToggle(d, false);
        });

        showToast('Applied standard ' + calFormatTimeString(openTime) + ' – ' + calFormatTimeString(closeTime) + ' schedule.');
    };

    window.calSetWeekendClosedPreset = function() {
        [6, 0].forEach(function(d) {
            var closedEl = document.getElementById('day_closed_' + d);
            if (closedEl) {
                closedEl.checked = true;
                calOnDayClosedToggle(d, true);
            }
        });
        showToast('Weekends marked as closed.');
    };

    // ── Right-Side Sticky Navigation for Operating Hours ──────────
    window.scrollToHoursSection = function(sectionId) {
        var el = document.getElementById(sectionId);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            updateHoursNavActiveState(sectionId);
        }
    };

    function updateHoursNavActiveState(activeId) {
        var mapping = {
            'hoursSectionVisibility': 'navLinkVisibility',
            'hoursSectionCalendar': 'navLinkCalendar',
            'hoursSectionWeekly': 'navLinkWeekly'
        };
        for (var sec in mapping) {
            var btn = document.getElementById(mapping[sec]);
            if (btn) {
                var icon = btn.querySelector('.material-symbols-outlined');
                var title = btn.querySelector('.font-bold');
                if (sec === activeId) {
                    btn.classList.add('border-terra-green', 'bg-emerald-50/50', 'dark:bg-emerald-950/20', 'text-terra-green');
                    btn.classList.remove('border-transparent');
                    if (icon) icon.className = 'material-symbols-outlined text-[18px] text-terra-green transition-colors mt-0.5';
                    if (title) title.className = 'block text-xs font-bold text-terra-green transition-colors leading-tight';
                } else {
                    btn.classList.remove('border-terra-green', 'bg-emerald-50/50', 'dark:bg-emerald-950/20', 'text-terra-green');
                    btn.classList.add('border-transparent');
                    if (icon) icon.className = 'material-symbols-outlined text-[18px] text-slate-400 group-hover:text-terra-green transition-colors mt-0.5';
                    if (title) title.className = 'block text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-terra-green transition-colors leading-tight';
                }
            }
        }
    }

    if (typeof window !== 'undefined') {
        window.addEventListener('scroll', function() {
            var sections = ['hoursSectionVisibility', 'hoursSectionCalendar', 'hoursSectionWeekly'];
            var scrollPos = window.scrollY + 180;
            var currentSec = 'hoursSectionVisibility';
            sections.forEach(function(sId) {
                var el = document.getElementById(sId);
                if (el && el.offsetTop <= scrollPos) {
                    currentSec = sId;
                }
            });
            updateHoursNavActiveState(currentSec);
        }, { passive: true });
    }

    // ── 6.1 Store Closure & Schedule Planner Calendar Engine ──────
    var storeCalendarMap = <?= json_encode($calendarSchedules ?? new stdClass(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    if (Array.isArray(storeCalendarMap)) {
        var obj = {};
        storeCalendarMap.forEach(function(it) {
            if (it && it.schedule_date) obj[it.schedule_date] = it;
        });
        storeCalendarMap = obj;
    }

    var calCurrentDate = new Date();
    // Default to Month View to match screenshot
    var calViewMode = 'month';
    var calSelectedDateKey = null;
    var calHeroSelectedType = 'closed';
    var calHeroColor = 'amber';

    var calMonthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    var calDayNamesShort = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];

    function calFormatDateKey(d) {
        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
    var calTodayKey = calFormatDateKey(new Date());

    function calGetThemeStyles(color) {
        var styles = {
            amber: {
                cardBg: 'bg-amber-500/15 border-amber-400 text-amber-900 dark:text-amber-200',
                badgeBg: 'bg-amber-500 text-white',
                dotBg: 'bg-amber-500'
            },
            rose: {
                cardBg: 'bg-rose-500/15 border-rose-400 text-rose-900 dark:text-rose-200',
                badgeBg: 'bg-rose-600 text-white',
                dotBg: 'bg-rose-500'
            },
            emerald: {
                cardBg: 'bg-emerald-500/15 border-emerald-400 text-emerald-900 dark:text-emerald-200',
                badgeBg: 'bg-emerald-600 text-white',
                dotBg: 'bg-emerald-500'
            },
            sky: {
                cardBg: 'bg-sky-500/15 border-sky-400 text-sky-900 dark:text-sky-200',
                badgeBg: 'bg-sky-600 text-white',
                dotBg: 'bg-sky-500'
            }
        };
        return styles[color] || styles.amber;
    }

    window.calSetViewMode = function(mode) {
        calViewMode = mode;
        ['month', 'week', 'list'].forEach(function(m) {
            var btn = document.getElementById('calViewBtn_' + m);
            if (btn) {
                if (m === mode) {
                    btn.className = 'px-3 py-1.5 rounded-lg transition-all bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs cursor-pointer';
                } else {
                    btn.className = 'px-3 py-1.5 rounded-lg transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white cursor-pointer';
                }
            }
        });

        var badgeLabel = document.getElementById('calViewBadgeLabel');
        if (badgeLabel) {
            if (mode === 'month') badgeLabel.textContent = 'MONTH VIEW';
            else if (mode === 'week') badgeLabel.textContent = 'WEEK VIEW';
            else if (mode === 'list') badgeLabel.textContent = 'AGENDA VIEW';
        }

        var mWrapper = document.getElementById('calMonthViewWrapper');
        var wWrapper = document.getElementById('calWeekViewWrapper');
        var aWrapper = document.getElementById('calAgendaViewWrapper');

        if (mWrapper) mWrapper.classList.toggle('hidden', mode !== 'month');
        if (wWrapper) wWrapper.classList.toggle('hidden', mode !== 'week');
        if (aWrapper) aWrapper.classList.toggle('hidden', mode !== 'list');

        renderStoreScheduleCalendar();
    };

    window.calNavigate = function(direction) {
        if (calViewMode === 'week') {
            calCurrentDate.setDate(calCurrentDate.getDate() + (direction * 7));
        } else {
            calCurrentDate.setMonth(calCurrentDate.getMonth() + direction);
        }
        renderStoreScheduleCalendar();
    };

    window.calJumpToToday = function() {
        calCurrentDate = new Date();
        renderStoreScheduleCalendar();
    };

    // Render Month View Grid (Restored Default Matching Image)
    function renderMonthView() {
        var grid = document.getElementById('storeScheduleCalendarGrid');
        if (!grid) return;
        grid.innerHTML = '';

        var y = calCurrentDate.getFullYear();
        var m = calCurrentDate.getMonth();
        var firstDayOfWeek = new Date(y, m, 1).getDay(); // 0 = Sun
        var daysInMonth = new Date(y, m + 1, 0).getDate();
        var daysInPrevMonth = new Date(y, m, 0).getDate();

        // Update Header Title to Uppercase (e.g. OCTOBER 2026)
        var titleEl = document.getElementById('calCurrentHeaderTitle');
        if (titleEl) {
            titleEl.textContent = (calMonthNames[m] + ' ' + y).toUpperCase();
        }

        // Count scheduled days in this month
        var scheduledDaysCount = 0;
        var monthPrefix = y + '-' + String(m + 1).padStart(2, '0') + '-';

        for (var k in storeCalendarMap) {
            if (k.indexOf(monthPrefix) === 0) {
                scheduledDaysCount++;
            }
        }

        var countEl = document.getElementById('calMonthScheduledCount');
        if (countEl) {
            countEl.textContent = scheduledDaysCount === 1 ?
                '1 scheduled day this month' :
                scheduledDaysCount + ' scheduled days this month';
        }

        // 1. Trailing previous month days (e.g. Sep 27, 28, 29, 30 for Oct 2026)
        for (var p = firstDayOfWeek - 1; p >= 0; p--) {
            var prevDayNum = daysInPrevMonth - p;
            var prevSlot = document.createElement('div');
            prevSlot.className = 'min-h-[96px] p-2.5 rounded-2xl bg-slate-50/40 dark:bg-slate-900/30 border border-slate-200/50 dark:border-slate-800/40 select-none text-left flex flex-col justify-between opacity-40';
            prevSlot.innerHTML = '<span class="text-xs font-semibold text-slate-400 font-sans">' + prevDayNum + '</span>';
            grid.appendChild(prevSlot);
        }

        // 2. Active days of current month
        for (var d = 1; d <= daysInMonth; d++) {
            (function(dayNum) {
                var dt = new Date(y, m, dayNum);
                var key = calFormatDateKey(dt);
                var isToday = (key === calTodayKey);
                var isPast = (key < calTodayKey);
                var entry = storeCalendarMap[key];

                var cell = document.createElement('div');

                // Dynamic Box Container Class based on State
                if (isPast) {
                    cell.className = 'min-h-[96px] p-2.5 rounded-2xl border border-slate-200/60 dark:border-slate-800/60 bg-slate-50/60 dark:bg-slate-850 text-left flex flex-col justify-between transition-all opacity-60 cursor-not-allowed select-none';
                } else if (isToday) {
                    cell.className = 'min-h-[96px] p-2.5 rounded-2xl border-2 border-terra-green bg-emerald-50/30 dark:bg-emerald-950/20 text-left flex flex-col justify-between transition-all shadow-xs cursor-pointer group hover:shadow-md';
                } else {
                    cell.className = 'min-h-[96px] p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-800 text-left flex flex-col justify-between transition-all hover:border-terra-green hover:bg-emerald-50/20 hover:shadow-xs cursor-pointer group';
                }

                // Top Header Row inside Date Box
                var topRow = document.createElement('div');
                topRow.className = 'flex items-center justify-between w-full leading-none';

                if (isToday) {
                    var todayBadge = document.createElement('span');
                    todayBadge.className = 'w-6 h-6 rounded-full bg-terra-green text-white flex items-center justify-center font-bold text-xs font-sans shadow-2xs';
                    todayBadge.textContent = String(dayNum);
                    topRow.appendChild(todayBadge);

                    var todayTag = document.createElement('span');
                    todayTag.className = 'px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-600/15 text-emerald-700 dark:text-emerald-400 font-mono';
                    todayTag.textContent = 'TODAY';
                    topRow.appendChild(todayTag);
                } else if (isPast) {
                    var pastDaySpan = document.createElement('span');
                    pastDaySpan.className = 'text-xs font-semibold text-slate-400 font-sans';
                    pastDaySpan.textContent = String(dayNum);
                    topRow.appendChild(pastDaySpan);
                } else {
                    var activeDaySpan = document.createElement('span');
                    activeDaySpan.className = 'text-xs font-bold text-slate-800 dark:text-slate-200 font-sans group-hover:text-terra-green transition-colors';
                    activeDaySpan.textContent = String(dayNum);
                    topRow.appendChild(activeDaySpan);
                }

                cell.appendChild(topRow);

                // Body content: Event chip OR Hover Add Affordance
                if (entry) {
                    var th = calGetThemeStyles(entry.color_theme);
                    var isClosed = parseInt(entry.is_closed, 10) === 1;
                    var chipWrapper = document.createElement('div');
                    chipWrapper.className = 'mt-auto pt-1 w-full' + (isPast ? ' opacity-75' : '');

                    var chip = document.createElement('div');
                    chip.className = 'px-2 py-1 rounded-lg text-[11px] font-bold border truncate shadow-2xs flex items-center gap-1.5 transition-transform ' + (isPast ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 border-slate-200 dark:border-slate-700' : (th.cardBg + ' hover:scale-[1.02]'));

                    var tooltipText = (entry.note || (isClosed ? 'Closed Today' : 'Special Hours'));
                    if (entry.open_time && entry.close_time) {
                        tooltipText += ' (' + calFormatTimeString(entry.open_time) + ' – ' + calFormatTimeString(entry.close_time) + ')';
                    }
                    chip.title = tooltipText;

                    var dot = document.createElement('span');
                    dot.className = 'w-1.5 h-1.5 rounded-full shrink-0 ' + (isPast ? 'bg-slate-400' : th.dotBg);
                    chip.appendChild(dot);

                    var label = document.createElement('span');
                    label.className = 'truncate leading-tight';
                    label.textContent = entry.note || (isClosed ? 'Closed Today' : 'Special Hours');
                    chip.appendChild(label);

                    chipWrapper.appendChild(chip);
                    cell.appendChild(chipWrapper);
                } else if (!isPast) {
                    // Empty upcoming box: Add clean Google Calendar hover affordance
                    var hoverAffordance = document.createElement('div');
                    hoverAffordance.className = 'opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-1 text-[11px] font-bold text-terra-green py-1 mt-auto';
                    hoverAffordance.innerHTML = '<span class="material-symbols-outlined text-[15px] leading-none">add_circle</span><span>Schedule</span>';
                    cell.appendChild(hoverAffordance);
                }

                // Click opens modal only for today and future dates (past dates are locked)
                if (!isPast) {
                    cell.onclick = function() {
                        calOpenHeroModal(key);
                    };
                } else if (entry) {
                    cell.onclick = function() {
                        showToast('Past date schedule cannot be modified.', true);
                    };
                }

                grid.appendChild(cell);
            })(d);
        }
    }

    // Render Week View Grid
    function renderWeekView() {
        var cur = new Date(calCurrentDate);
        var dayOfWeek = cur.getDay(); // 0 = Sun
        var startOfWeek = new Date(cur);
        startOfWeek.setDate(cur.getDate() - dayOfWeek);

        var titleEl = document.getElementById('calCurrentHeaderTitle');
        if (titleEl) {
            titleEl.textContent = (calMonthNames[cur.getMonth()] + ' ' + cur.getFullYear()).toUpperCase();
        }

        var weekDates = [];
        for (var i = 0; i < 7; i++) {
            var dt = new Date(startOfWeek);
            dt.setDate(startOfWeek.getDate() + i);
            weekDates.push(dt);

            var key = calFormatDateKey(dt);
            var isToday = (key === calTodayKey);

            var nameEl = document.getElementById('calWeekDayName_' + i);
            var badgeEl = document.getElementById('calWeekDayBadge_' + i);
            if (nameEl) nameEl.textContent = calDayNamesShort[i];
            if (badgeEl) {
                badgeEl.textContent = String(dt.getDate());
                if (isToday) {
                    badgeEl.className = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-terra-green text-white shadow-xs';
                } else {
                    badgeEl.className = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-black text-slate-800 dark:text-slate-200';
                }
            }

            // All-day banner column
            var allDayCol = document.getElementById('calWeekAllDayCol_' + i);
            if (allDayCol) {
                allDayCol.innerHTML = '';
                var entry = storeCalendarMap[key];
                if (entry) {
                    var th = calGetThemeStyles(entry.color_theme);
                    var isClosed = parseInt(entry.is_closed, 10) === 1;
                    var chip = document.createElement('div');
                    chip.className = 'px-2 py-0.5 rounded-md text-[11px] font-bold truncate border shadow-2xs cursor-pointer flex items-center gap-1 ' + th.cardBg;
                    chip.innerHTML = '<span class="w-1.5 h-1.5 rounded-full ' + th.dotBg + ' shrink-0"></span>' +
                        '<span>' + (entry.note ? entry.note : (isClosed ? 'Closed' : 'Special')) + '</span>';
                    chip.onclick = (function(dKey) {
                        return function() { calOpenHeroModal(dKey); };
                    })(key);
                    allDayCol.appendChild(chip);
                }
            }

            // Hourly slots for this day (6 AM to 9 PM, 16 slots)
            for (var slotIdx = 0; slotIdx < 16; slotIdx++) {
                var slotCell = document.getElementById('calWeekSlot_' + i + '_' + slotIdx);
                if (slotCell) {
                    slotCell.innerHTML = '';
                }
            }

            var entry = storeCalendarMap[key];
            if (entry && !entry.is_closed && entry.open_time && entry.close_time) {
                var startH = parseInt(entry.open_time.split(':')[0], 10);
                var slotIdx = startH - 6; // 6 AM is slot 0
                if (slotIdx >= 0 && slotIdx < 16) {
                    var targetSlot = document.getElementById('calWeekSlot_' + i + '_' + slotIdx);
                    if (targetSlot) {
                        var th = calGetThemeStyles(entry.color_theme);
                        var eventCard = document.createElement('div');
                        eventCard.className = 'absolute inset-x-1 top-0.5 bottom-0.5 rounded-lg p-1 text-[10px] font-bold border shadow-xs overflow-hidden z-10 cursor-pointer flex flex-col justify-between ' + th.cardBg;
                        eventCard.innerHTML = '<span class="truncate leading-tight">' + (entry.note || 'Special Hours') + '</span>' +
                            '<span class="font-mono text-[9px] opacity-80">' + calFormatTimeString(entry.open_time) + ' – ' + calFormatTimeString(entry.close_time) + '</span>';
                        eventCard.onclick = (function(dKey) {
                            return function(e) {
                                e.stopPropagation();
                                calOpenHeroModal(dKey);
                            };
                        })(key);
                        targetSlot.appendChild(eventCard);
                    }
                }
            }
        }

        calUpdateRealTimeIndicator(weekDates);
    }

    function calUpdateRealTimeIndicator(weekDates) {
        var indicator = document.getElementById('calRealTimeIndicator');
        if (!indicator) return;

        var now = new Date();
        var todayKey = calFormatDateKey(now);
        var dayIdx = -1;

        if (weekDates) {
            for (var i = 0; i < weekDates.length; i++) {
                if (calFormatDateKey(weekDates[i]) === todayKey) {
                    dayIdx = i;
                    break;
                }
            }
        }

        if (dayIdx === -1) {
            indicator.classList.add('hidden');
            return;
        }

        var curH = now.getHours();
        var curM = now.getMinutes();
        if (curH < 6 || curH > 21) {
            indicator.classList.add('hidden');
            return;
        }

        var totalMinutesFrom6AM = (curH - 6) * 60 + curM;
        var topPx = (totalMinutesFrom6AM / 60) * 40;

        indicator.classList.remove('hidden');
        indicator.style.top = topPx + 'px';
        indicator.style.left = '60px';
        indicator.style.right = '0px';
    }

    window.calOnWeekSlotClick = function(dayColIdx, slotIdx) {
        var cur = new Date(calCurrentDate);
        var dayOfWeek = cur.getDay();
        var targetDt = new Date(cur);
        targetDt.setDate(cur.getDate() - dayOfWeek + dayColIdx);

        var dateKey = calFormatDateKey(targetDt);
        var hour = 6 + slotIdx;
        calOpenHeroModal(dateKey, hour);
    };

    // Render Agenda View
    function renderAgendaView() {
        var container = document.getElementById('calListScheduleItems');
        var countEl = document.getElementById('calAgendaEntriesCount');
        if (!container) return;
        container.innerHTML = '';

        var keys = Object.keys(storeCalendarMap).sort();
        if (countEl) countEl.textContent = keys.length + ' scheduled overrides';

        if (keys.length === 0) {
            container.innerHTML = '<div class="p-8 text-center text-xs text-slate-400">No scheduled calendar overrides found.</div>';
            return;
        }

        keys.forEach(function(key) {
            var entry = storeCalendarMap[key];
            var th = calGetThemeStyles(entry.color_theme);
            var isClosed = parseInt(entry.is_closed, 10) === 1;

            var row = document.createElement('div');
            row.className = 'p-3.5 rounded-2xl border transition-all flex items-center justify-between gap-3 ' + th.cardBg;
            row.innerHTML = '<div>' +
                '<span class="text-xs font-bold text-slate-900 dark:text-white font-mono">' + key + '</span>' +
                '<span class="ml-2 px-2 py-0.5 rounded text-[10px] font-black uppercase ' + (isClosed ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white') + '">' + (isClosed ? 'Closed' : 'Special') + '</span>' +
                '<p class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1">' + (entry.note || 'No description.') + '</p>' +
                '</div>' +
                '<button type="button" class="px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold bg-white dark:bg-slate-800 hover:border-terra-green transition cursor-pointer">Edit</button>';
            row.onclick = function() { calOpenHeroModal(key); };
            container.appendChild(row);
        });
    }

    function renderStoreScheduleCalendar() {
        if (calViewMode === 'month') {
            renderMonthView();
        } else if (calViewMode === 'week') {
            renderWeekView();
        } else if (calViewMode === 'list') {
            renderAgendaView();
        }
    }

    // ── Schedule Hero Modal Interactions ──────────────────────────
    window.calOpenNewScheduleModal = function() {
        calOpenHeroModal(calTodayKey);
    };

    window.calOpenHeroModal = function(dateKey, defaultHour) {
        calSelectedDateKey = dateKey || calTodayKey;
        var dt = calParseDateKey(calSelectedDateKey);
        var fullDateLabel = dt.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' });

        var summaryLabel = document.getElementById('calHeroDateSummaryLabel');
        if (summaryLabel) summaryLabel.textContent = fullDateLabel;

        var startInput = document.getElementById('calHeroStartDateInput');
        var endInput = document.getElementById('calHeroEndDateInput');
        if (startInput) startInput.value = calSelectedDateKey;
        if (endInput) endInput.value = calSelectedDateKey;

        var titleInput = document.getElementById('calHeroTitleInput');
        var allDayToggle = document.getElementById('calHeroIsAllDayInput');
        var openTimeInput = document.getElementById('calHeroOpenTime');
        var closeTimeInput = document.getElementById('calHeroCloseTime');
        var deleteBtn = document.getElementById('calHeroDeleteBtn');

        var entry = storeCalendarMap[calSelectedDateKey];
        if (entry) {
            if (titleInput) titleInput.value = entry.note || '';
            var isClosed = parseInt(entry.is_closed, 10) === 1;
            calSelectHeroTab(isClosed ? 'closed' : (entry.open_time ? 'hours' : 'notice'));
            calSelectHeroColor(entry.color_theme || 'amber');

            if (allDayToggle) {
                var isAllDay = !entry.open_time && !entry.close_time;
                allDayToggle.checked = isAllDay;
                calToggleHeroAllDay(isAllDay);
            }
            if (openTimeInput && entry.open_time) openTimeInput.value = entry.open_time.substring(0, 5);
            if (closeTimeInput && entry.close_time) closeTimeInput.value = entry.close_time.substring(0, 5);
            if (deleteBtn) deleteBtn.classList.remove('hidden');
        } else {
            if (titleInput) titleInput.value = '';
            calSelectHeroTab('closed');
            calSelectHeroColor('amber');
            if (allDayToggle) {
                allDayToggle.checked = true;
                calToggleHeroAllDay(true);
            }
            if (defaultHour && openTimeInput && closeTimeInput) {
                allDayToggle.checked = false;
                calToggleHeroAllDay(false);
                var padH = String(defaultHour).padStart(2, '0');
                var padEndH = String(Math.min(defaultHour + 1, 23)).padStart(2, '0');
                openTimeInput.value = padH + ':00';
                closeTimeInput.value = padEndH + ':00';
            }
            if (deleteBtn) deleteBtn.classList.add('hidden');
        }

        var modal = document.getElementById('calHeroInputModal');
        if (modal) modal.classList.remove('hidden');
    };

    window.calCloseHeroModal = function() {
        var modal = document.getElementById('calHeroInputModal');
        if (modal) modal.classList.add('hidden');
    };

    window.calSelectHeroTab = function(type) {
        calHeroSelectedType = type;
        var pillClosed = document.getElementById('calPill_closed');
        var pillHours = document.getElementById('calPill_hours');
        var pillNotice = document.getElementById('calPill_notice');
        var allDayToggle = document.getElementById('calHeroIsAllDayInput');

        if (pillClosed) {
            pillClosed.className = type === 'closed' ?
                'px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-terra-green text-white shadow-2xs cursor-pointer' :
                'px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white cursor-pointer';
        }
        if (pillHours) {
            pillHours.className = type === 'hours' ?
                'px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-terra-green text-white shadow-2xs cursor-pointer' :
                'px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white cursor-pointer';
        }
        if (pillNotice) {
            pillNotice.className = type === 'notice' ?
                'px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-terra-green text-white shadow-2xs cursor-pointer' :
                'px-4 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white cursor-pointer';
        }

        if (type === 'hours') {
            if (allDayToggle) allDayToggle.checked = false;
            calToggleHeroAllDay(false);
            calSelectHeroColor('sky');
        } else if (type === 'closed') {
            if (allDayToggle) allDayToggle.checked = true;
            calToggleHeroAllDay(true);
            calSelectHeroColor('amber');
        } else if (type === 'notice') {
            calSelectHeroColor('emerald');
        }
    };

    window.calToggleHeroAllDay = function(isAllDay) {
        var rangeRow = document.getElementById('calHeroTimeRangeRow');
        if (rangeRow) {
            rangeRow.classList.toggle('hidden', !!isAllDay);
        }
    };

    window.calSelectHeroColor = function(color) {
        calHeroColor = color;
        ['amber', 'rose', 'emerald', 'sky'].forEach(function(c) {
            var btn = document.getElementById('calHeroColor_' + c);
            if (btn) {
                if (c === color) {
                    btn.classList.add('ring-2', 'ring-offset-2');
                } else {
                    btn.classList.remove('ring-2', 'ring-offset-2');
                }
            }
        });
    };

    window.calToggleHeroMoreOptions = function() {
        var p = document.getElementById('calHeroMoreOptionsPanel');
        var btn = document.getElementById('calHeroMoreOptionsBtn');
        if (!p) return;
        var isHidden = p.classList.contains('hidden');
        p.classList.toggle('hidden', !isHidden);
        if (btn) btn.textContent = isHidden ? 'Fewer options' : 'More options';
    };

    window.calApplyHeroPreset = function(title, type, color) {
        var titleInput = document.getElementById('calHeroTitleInput');
        if (titleInput) titleInput.value = title;
        calSelectHeroTab(type);
        calSelectHeroColor(color);
    };

    window.calSaveHeroModal = async function() {
        var titleInput = document.getElementById('calHeroTitleInput');
        var title = (titleInput ? titleInput.value : '').trim();

        var isClosed = (calHeroSelectedType === 'closed') ? 1 : 0;
        var allDayToggle = document.getElementById('calHeroIsAllDayInput');
        var isAllDay = allDayToggle && allDayToggle.checked ? 1 : 0;

        var openTime = (!isAllDay && !isClosed) ? (document.getElementById('calHeroOpenTime') ? document.getElementById('calHeroOpenTime').value : '') : '';
        var closeTime = (!isAllDay && !isClosed) ? (document.getElementById('calHeroCloseTime') ? document.getElementById('calHeroCloseTime').value : '') : '';

        var startDate = (document.getElementById('calHeroStartDateInput') ? document.getElementById('calHeroStartDateInput').value : '') || calSelectedDateKey;
        var endDate = (document.getElementById('calHeroEndDateInput') ? document.getElementById('calHeroEndDateInput').value : '') || startDate;

        var fd = new FormData();
        fd.append('csrf_test_name', csrfToken());
        fd.append('section', 'calendar_schedule');
        fd.append('calendar_action', 'save');
        fd.append('schedule_date', startDate);
        fd.append('end_date', endDate);
        fd.append('is_closed', isClosed);
        fd.append('is_all_day', isAllDay);
        fd.append('open_time', openTime);
        fd.append('close_time', closeTime);
        fd.append('note', title);
        fd.append('color_theme', calHeroColor);

        var saveBtn = document.getElementById('calHeroSaveBtn');
        if (saveBtn) { saveBtn.textContent = 'Saving...'; saveBtn.disabled = true; }

        try {
            var resp = await fetch("<?= base_url('tenant/settings/save') ?>", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            var data = await resp.json();

            if (data.csrf_hash) {
                var csrfInput = document.querySelector('#settingsCsrfForm [name="csrf_test_name"]');
                if (csrfInput) csrfInput.value = data.csrf_hash;
            }

            showToast(data.message || 'Schedule updated.', !data.success);
            if (data.success) {
                storeCalendarMap = (data.schedules && !Array.isArray(data.schedules)) ? data.schedules : {};
                updateLiveTodayBadge(data.operational_status);
                calCloseHeroModal();
                renderStoreScheduleCalendar();
            }
        } catch (err) {
            showToast('Could not save schedule.', true);
        } finally {
            if (saveBtn) { saveBtn.textContent = 'Save Schedule'; saveBtn.disabled = false; }
        }
    };

    window.calDeleteFromHeroModal = async function() {
        var startDate = (document.getElementById('calHeroStartDateInput') ? document.getElementById('calHeroStartDateInput').value : '') || calSelectedDateKey;
        var endDate = (document.getElementById('calHeroEndDateInput') ? document.getElementById('calHeroEndDateInput').value : '') || startDate;

        var fd = new FormData();
        fd.append('csrf_test_name', csrfToken());
        fd.append('section', 'calendar_schedule');
        fd.append('calendar_action', 'delete');
        fd.append('schedule_date', startDate);
        fd.append('end_date', endDate);

        try {
            var resp = await fetch("<?= base_url('tenant/settings/save') ?>", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            var data = await resp.json();

            if (data.csrf_hash) {
                var csrfInput = document.querySelector('#settingsCsrfForm [name="csrf_test_name"]');
                if (csrfInput) csrfInput.value = data.csrf_hash;
            }

            showToast(data.message || 'Schedule cleared.', !data.success);
            if (data.success) {
                storeCalendarMap = (data.schedules && !Array.isArray(data.schedules)) ? data.schedules : {};
                updateLiveTodayBadge(data.operational_status);
                calCloseHeroModal();
                renderStoreScheduleCalendar();
            }
        } catch (err) {
            showToast('Could not clear schedule.', true);
        }
    };

    function updateLiveTodayBadge(opStatus) {
        var badge = document.getElementById('liveTodayStatusBadge');
        if (!badge || !opStatus) return;
        badge.textContent = opStatus.status_label || (opStatus.is_closed_today ? 'Closed Today' : 'Open Today');
        if (opStatus.is_closed_today) {
            badge.className = 'px-3 py-0.5 rounded-full text-xs font-bold bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30';
        } else {
            badge.className = 'px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30';
        }
    }

    window.saveClosureVisibilityMode = async function (hideVal) {
        var safeCard = document.getElementById('visCardSafe');
        var hideCard = document.getElementById('visCardHide');

        if (safeCard && hideCard) {
            if (hideVal === 1) {
                hideCard.className = 'cursor-pointer rounded-2xl border-2 border-amber-500 bg-amber-500/5 p-4 transition flex items-start gap-3';
                safeCard.className = 'cursor-pointer rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 transition flex items-start gap-3';
            } else {
                safeCard.className = 'cursor-pointer rounded-2xl border-2 border-terra-green bg-terra-green/5 p-4 transition flex items-start gap-3';
                hideCard.className = 'cursor-pointer rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 p-4 transition flex items-start gap-3';
            }
        }

        var fd = new FormData();
        fd.append('csrf_test_name', csrfToken());
        fd.append('section', 'closure_visibility');
        fd.append('hide_when_closed', hideVal ? 1 : 0);

        try {
            var resp = await fetch("<?= base_url('tenant/settings/save') ?>", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            var data = await resp.json();

            if (data.csrf_hash) {
                var csrfInput = document.querySelector('#settingsCsrfForm [name="csrf_test_name"]');
                if (csrfInput) csrfInput.value = data.csrf_hash;
            }

            showToast(data.message || 'Visibility mode updated.', !data.success);
            if (data.success && data.operational_status) {
                updateLiveTodayBadge(data.operational_status);
            }
        } catch (err) {
            showToast('Could not update visibility mode.', true);
        }
    };

    // Live real-time indicator interval (every 60 seconds)
    setInterval(function() {
        if (calViewMode === 'week') {
            calUpdateRealTimeIndicator();
        }
    }, 60000);
// ── 7. Notification Preferences Auto-Save ──────────────────────
    function collectNotifs() {
        var fd = new FormData();
        fd.append('csrf_test_name', csrfToken());
        fd.append('section', 'notifications');
        fd.append('new_orders', document.getElementById('pref_new_orders').checked ? 1 : 0);
        fd.append('low_stock', document.getElementById('pref_low_stock').checked ? 1 : 0);
        return fd;
    }

    function wireNotifs() {
        ['pref_new_orders', 'pref_low_stock'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function () {
                    var feedback = document.getElementById('notifFeedback');
                    if (feedback) { feedback.textContent = 'Saving...'; }
                    postSettings(collectNotifs()).then(function (ok) {
                        if (feedback) { feedback.textContent = ok ? 'Preferences saved.' : ''; }
                    });
                });
            }
        });
    }

    // ── 7.1 Service Modules Auto-Save ──────────────────────────────
    function wireServices() {
        ['service_offers_printing', 'service_offers_delivery', 'service_offers_pickup'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function () {
                    var moduleName = el.getAttribute('data-module');
                    var feedback = document.getElementById('servicesFeedback');
                    if (feedback) { feedback.textContent = 'Saving...'; }
                    var fd = new FormData();
                    fd.append('csrf_test_name', csrfToken());
                    fd.append('section', 'services');
                    fd.append('module', moduleName);
                    fd.append('enabled', el.checked ? 1 : 0);
                    postSettings(fd).then(function (ok) {
                        if (feedback) { feedback.textContent = ok ? 'Service updated.' : 'Save failed.'; }
                        if (!ok) { el.checked = !el.checked; }
                    });
                });
            }
        });
    }

    // ── 8. Street & Barangay Auto-Sync to Address Line ─────────────
    var streetEl = document.getElementById('street');
    var brgyEl = document.getElementById('barangay');
    var addrEl = document.getElementById('address_line');
    if (streetEl && brgyEl && addrEl) {
        var userModifiedAddr = false;
        addrEl.addEventListener('input', function() {
            userModifiedAddr = true;
        });

        function syncAddressLine() {
            if (userModifiedAddr) return;
            var st = streetEl.value.trim();
            var bg = brgyEl.value.trim();
            var parts = [];
            if (st) parts.push(st);
            if (bg) parts.push(bg);
            parts.push('Polomolok', 'South Cotabato');
            if (st || bg) {
                addrEl.value = parts.join(', ');
            }
        }

        streetEl.addEventListener('input', syncAddressLine);
        brgyEl.addEventListener('change', syncAddressLine);
    }

    wireHours();
    wireNotifs();
    wireServices();

    // ── 9. Google Maps Autocomplete & Geocoding ────────────────────
    let shopAutocomplete = null;

    function handleShopPlaceSelection(place) {
        if (!place) return;
        let lat = null;
        let lng = null;

        if (place.geometry && place.geometry.location) {
            lat = typeof place.geometry.location.lat === 'function' ? place.geometry.location.lat() : place.geometry.location.lat;
            lng = typeof place.geometry.location.lng === 'function' ? place.geometry.location.lng() : place.geometry.location.lng;
        } else if (place.location) {
            lat = typeof place.location.lat === 'function' ? place.location.lat() : place.location.lat;
            lng = typeof place.location.lng === 'function' ? place.location.lng() : place.location.lng;
        }

        const latInput = document.getElementById('shop_latitude');
        const lngInput = document.getElementById('shop_longitude');
        if (latInput && lat !== null) latInput.value = lat;
        if (lngInput && lng !== null) lngInput.value = lng;

        const components = place.address_components || place.addressComponents;
        if (components) {
            for (const component of components) {
                const types = component.types || [];
                const longName = component.long_name || component.longText || '';
                if (types.includes('sublocality') || types.includes('sublocality_level_1') || types.includes('neighborhood') || types.includes('political')) {
                    const brgyName = longName.replace(/^(Barangay|Brgy\.?)\s+/i, '').trim();
                    const brgySelect = document.getElementById('barangay');
                    if (brgySelect) {
                        for (let i = 0; i < brgySelect.options.length; i++) {
                            if (brgySelect.options[i].value.toLowerCase() === brgyName.toLowerCase()) {
                                brgySelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (addrEl && !userModifiedAddr) {
            addrEl.value = place.formatted_address || place.formattedAddress || place.displayName || place.name || '';
        }
    }

    window.initShopAddressAutocomplete = async function() {
        const input = document.getElementById('street');
        if (!input || typeof google === 'undefined' || !google.maps || !google.maps.places) return;

        const poloBounds = new google.maps.LatLngBounds(
            { lat: 6.10, lng: 124.95 },
            { lat: 6.32, lng: 125.18 }
        );

        if (google.maps.places.PlaceAutocompleteElement) {
            try {
                const elem = new google.maps.places.PlaceAutocompleteElement();
                elem.componentRestrictions = { country: 'ph' };

                const onSelect = async (event) => {
                    let place = event.place;
                    if (event.placePrediction && typeof event.placePrediction.toPlace === 'function') {
                        place = event.placePrediction.toPlace();
                    }
                    if (place && typeof place.fetchFields === 'function') {
                        await place.fetchFields({
                            fields: ['displayName', 'formattedAddress', 'location', 'addressComponents']
                        });
                    }
                    if (place && place.formattedAddress) {
                        input.value = place.formattedAddress;
                    } else if (place && place.displayName) {
                        input.value = place.displayName;
                    }
                    handleShopPlaceSelection(place);
                };

                elem.addEventListener('gmp-placeselect', onSelect);
                elem.addEventListener('gmp-select', onSelect);

                const wrapper = document.getElementById('shop-autocomplete-wrapper');
                if (wrapper && !wrapper.hasChildNodes()) {
                    elem.classList.add('w-full');
                    wrapper.appendChild(elem);
                    wrapper.classList.remove('hidden');
                    shopAutocomplete = elem;
                    return;
                }
            } catch (err) {
                console.debug('PlaceAutocompleteElement fallback:', err);
            }
        }

        shopAutocomplete = new google.maps.places.Autocomplete(input, {
            bounds: poloBounds,
            componentRestrictions: { country: 'ph' },
            fields: ['address_components', 'geometry', 'name', 'place_id', 'formatted_address'],
            strictBounds: false
        });

        shopAutocomplete.addListener('place_changed', function() {
            const place = shopAutocomplete.getPlace();
            handleShopPlaceSelection(place);
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.places && !shopAutocomplete) {
            window.initShopAddressAutocomplete();
        }
    });
})();
</script>

<!-- Google Maps Places API -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc(env('GOOGLE_MAPS_API_KEY')) ?>&libraries=places&loading=async&callback=initShopAddressAutocomplete" async defer></script>

<?= $this->endSection() ?>