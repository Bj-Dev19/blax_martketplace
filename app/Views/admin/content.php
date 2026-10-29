<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// Index all content items by their content_key for clean section-based layout
$itemsByKey = [];
$previewData = [];
foreach (($grouped ?? []) as $pageName => $pageItems) {
    foreach ($pageItems as $item) {
        $key = $item['content_key'];
        $itemsByKey[$key] = $item;
        $previewData[$key] = [
            'type'  => $item['content_type'],
            'text'  => $item['text_value'] ?? '',
            'image' => $item['image_url'] ?? '',
        ];
    }
}

// Extract standard fields with safe defaults
$annActiveVal   = in_array(trim((string)($itemsByKey['announcement_active']['text_value'] ?? '1')), ['1', 'true', 'on', 'yes'], true);
$annTextVal     = $itemsByKey['announcement_text']['text_value'] ?? '🚀 Free doorstep delivery on orders over ₱500 within Polomolok! Use code BLAXSHIP';

$heroBadgeVal   = $itemsByKey['hero_badge']['text_value'] ?? 'Seasonal Event';
$heroTitleVal   = $itemsByKey['hero_title']['text_value'] ?? 'The Ultimate Merchandise & Printing Hub';
$heroSubVal     = $itemsByKey['hero_subtitle']['text_value'] ?? 'Discover premium goods, exclusive deals, and top-tier printing services all in one place across Polomolok.';
$heroCtaVal     = $itemsByKey['hero_cta_text']['text_value'] ?? 'Explore Marketplace';
$catalogTitleVal = $itemsByKey['catalog_title']['text_value'] ?? 'Global Product Catalog';
$catalogSubVal   = $itemsByKey['catalog_subtitle']['text_value'] ?? 'Explore top-rated products from verified local merchants across Polomolok.';

$heroImgRow     = $itemsByKey['hero_image'] ?? null;
$rawHeroImg     = $heroImgRow['image_url'] ?? null;
$heroImgUrl     = !empty($rawHeroImg) ? (str_starts_with($rawHeroImg, 'http') ? $rawHeroImg : base_url($rawHeroImg)) : '';

$footerEmailVal = $itemsByKey['footer_contact_email']['text_value'] ?? 'support@blaxinventory.com';
$footerPhoneVal = $itemsByKey['footer_phone']['text_value'] ?? '+63 917 123 4567';
$footerAddrVal  = $itemsByKey['footer_address']['text_value'] ?? 'Poblacion, Polomolok, South Cotabato, Philippines 9504';
$footerCopyVal  = $itemsByKey['footer_copyright']['text_value'] ?? ('© ' . date('Y') . ' Blax Multi-Tenant Inventory & Printing Platform. All rights reserved.');
?>

<div class="space-y-6">

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </span>
                <span class="font-medium text-xs sm:text-sm"><?= session()->getFlashdata('success') ?></span>
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
                <span class="font-medium text-xs sm:text-sm"><?= session()->getFlashdata('error') ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200 p-1">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- CMS Header Banner -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center font-bold shadow-xs">
                    <span class="material-symbols-outlined text-[24px]">dashboard_customize</span>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold font-display text-slate-900 dark:text-white tracking-tight">Storefront Content Management</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Update announcements, hero banners, and customer support info with real-time responsive preview.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="<?= base_url('/') ?>" target="_blank" class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 transition-all inline-flex items-center gap-2 shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-slate-500">open_in_new</span>
                <span>View Live Storefront</span>
            </a>
        </div>
    </div>

    <!-- Main Split Grid: Left = 3 Focused Section Cards, Right = Sticky Live Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- LEFT COLUMN: Structured Feature Cards (7 cols on lg screens)        -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="lg:col-span-7 space-y-6">

            <!-- ───────────────────────────────────────────────────────────── -->
            <!-- SECTION 1: GLOBAL ANNOUNCEMENT BAR                           -->
            <!-- ───────────────────────────────────────────────────────────── -->
            <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-6 py-4.5 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">campaign</span>
                        </span>
                        <div>
                            <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Announcement Bar</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Top notification bar shown across all public storefront pages.</p>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('admin/content/save') ?>" method="POST" class="p-6 space-y-5">
                    <?= csrf_field() ?>

                    <!-- Active Toggle Selector -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Announcement Status
                        </label>
                        <div class="grid grid-cols-2 gap-3 max-w-md">
                            <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors has-[:checked]:border-[#4A943E] has-[:checked]:bg-[#4A943E]/5">
                                <input type="radio" name="fields[announcement_active]" value="1" <?= $annActiveVal ? 'checked' : '' ?> data-preview-key="announcement_active" class="text-[#4A943E] focus:ring-[#4A943E] h-4 w-4">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-900 dark:text-white block flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#4A943E]"></span> Visible
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">Show banner at top</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors has-[:checked]:border-slate-400 has-[:checked]:bg-slate-100 dark:has-[:checked]:bg-slate-800">
                                <input type="radio" name="fields[announcement_active]" value="0" <?= !$annActiveVal ? 'checked' : '' ?> data-preview-key="announcement_active" class="text-slate-600 focus:ring-slate-500 h-4 w-4">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-900 dark:text-white block flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Hidden
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400 text-[11px]">Hide banner from site</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Announcement Text Field -->
                    <div>
                        <label for="input-announcement-text" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Announcement Message
                        </label>
                        <input id="input-announcement-text" name="fields[announcement_text]" value="<?= esc($annTextVal) ?>" data-preview-key="announcement_text" class="w-full border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="e.g. 🚀 Free doorstep delivery on orders over ₱500!">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Recommended under 90 characters for best display on mobile devices.</p>
                    </div>

                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" class="px-5 py-2.5 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[17px]">save</span>
                            <span>Save Announcement</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- ───────────────────────────────────────────────────────────── -->
            <!-- SECTION 2: HOMEPAGE HERO BANNER & CATALOG HEADINGS            -->
            <!-- ───────────────────────────────────────────────────────────── -->
            <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-6 py-4.5 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">view_carousel</span>
                        </span>
                        <div>
                            <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Homepage Hero &amp; Catalog</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Primary value proposition, hero visual banner, and product catalog headers.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    <!-- Background Image Frame & Upload -->
                    <?php if ($heroImgRow): ?>
                        <div class="p-4.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 space-y-3.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    Hero Banner Background Image
                                </label>
                                <span class="text-[11px] font-mono text-[#4A943E] font-medium">Recommended: 1440×480px (3:1, max 3MB)</span>
                            </div>

                            <div class="relative w-full aspect-[3/1] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-200 dark:bg-slate-950 group shadow-inner">
                                <?php if (!empty($heroImgUrl)): ?>
                                    <img src="<?= esc($heroImgUrl) ?>" alt="Hero Banner" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300" loading="lazy" decoding="async">
                                    <a href="<?= esc($heroImgUrl) ?>" target="_blank" class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold gap-1.5">
                                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                        <span>View Full Image</span>
                                    </a>
                                <?php else: ?>
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 text-xs gap-1">
                                        <span class="material-symbols-outlined text-3xl">image</span>
                                        <span>No image uploaded</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Upload Action Form -->
                            <form action="<?= base_url('admin/content/upload') ?>" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= $heroImgRow['id'] ?>">
                                <input type="file" name="image" accept="image/*" class="flex-1 text-xs border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4A943E]/10 file:text-[#4A943E] hover:file:bg-[#4A943E]/20" required>
                                <button type="submit" class="px-4 py-2.5 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-bold hover:bg-slate-800 dark:hover:bg-white transition-all shadow-xs shrink-0 flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px]">upload</span>
                                    <span>Upload New Image</span>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Text Fields Form (Section Bulk Save) -->
                    <form action="<?= base_url('admin/content/save') ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Hero Badge -->
                            <div>
                                <label for="input-hero-badge" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Hero Badge / Tagline
                                </label>
                                <input id="input-hero-badge" name="fields[hero_badge]" value="<?= esc($heroBadgeVal) ?>" data-preview-key="hero_badge" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="e.g. Polomolok Premier Hub">
                            </div>

                            <!-- Hero CTA Button Text -->
                            <div>
                                <label for="input-hero-cta" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    Primary Button (CTA) Label
                                </label>
                                <input id="input-hero-cta" name="fields[hero_cta_text]" value="<?= esc($heroCtaVal) ?>" data-preview-key="hero_cta_text" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="e.g. Explore Marketplace">
                            </div>
                        </div>

                        <!-- Hero Main Headline -->
                        <div>
                            <label for="input-hero-title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Hero Main Headline
                            </label>
                            <input id="input-hero-title" name="fields[hero_title]" value="<?= esc($heroTitleVal) ?>" data-preview-key="hero_title" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="e.g. The Ultimate Merchandise & Printing Hub">
                        </div>

                        <!-- Hero Subtitle Description -->
                        <div>
                            <label for="input-hero-sub" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Hero Subtitle Description
                            </label>
                            <textarea id="input-hero-sub" name="fields[hero_subtitle]" rows="3" data-preview-key="hero_subtitle" class="w-full border border-slate-200 dark:border-slate-700 rounded-2xl p-3.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all leading-relaxed" placeholder="Write a short summary for your visitors..."><?= esc($heroSubVal) ?></textarea>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800/60 space-y-4">
                            <h4 class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Product Catalog Section Headings</h4>

                            <!-- Catalog Title -->
                            <div>
                                <label for="input-cat-title" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    Catalog Heading
                                </label>
                                <input id="input-cat-title" name="fields[catalog_title]" value="<?= esc($catalogTitleVal) ?>" data-preview-key="catalog_title" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="e.g. Global Product Catalog">
                            </div>

                            <!-- Catalog Subtitle -->
                            <div>
                                <label for="input-cat-sub" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    Catalog Subtitle Description
                                </label>
                                <textarea id="input-cat-sub" name="fields[catalog_subtitle]" rows="2" data-preview-key="catalog_subtitle" class="w-full border border-slate-200 dark:border-slate-700 rounded-2xl p-3.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all leading-relaxed"><?= esc($catalogSubVal) ?></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" class="px-5 py-2.5 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs flex items-center gap-2">
                                <span class="material-symbols-outlined text-[17px]">save</span>
                                <span>Save Hero &amp; Catalog Content</span>
                            </button>
                        </div>
                    </form>

                </div>
            </section>

            <!-- ───────────────────────────────────────────────────────────── -->
            <!-- SECTION 3: FOOTER & SUPPORT CONTACT                          -->
            <!-- ───────────────────────────────────────────────────────────── -->
            <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-6 py-4.5 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">contact_support</span>
                        </span>
                        <div>
                            <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Footer &amp; Support Contacts</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Contact details, physical hub location, and legal copyright text.</p>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('admin/content/save') ?>" method="POST" class="p-6 space-y-4">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Support Email -->
                        <div>
                            <label for="input-ft-email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Customer Support Email
                            </label>
                            <input id="input-ft-email" type="email" name="fields[footer_contact_email]" value="<?= esc($footerEmailVal) ?>" data-preview-key="footer_contact_email" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="support@blaxinventory.com">
                        </div>

                        <!-- Hotline Phone -->
                        <div>
                            <label for="input-ft-phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Hotline / Phone Number
                            </label>
                            <input id="input-ft-phone" name="fields[footer_phone]" value="<?= esc($footerPhoneVal) ?>" data-preview-key="footer_phone" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="+63 917 123 4567">
                        </div>
                    </div>

                    <!-- Hub Physical Address -->
                    <div>
                        <label for="input-ft-addr" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Physical Hub / Office Location
                        </label>
                        <input id="input-ft-addr" name="fields[footer_address]" value="<?= esc($footerAddrVal) ?>" data-preview-key="footer_address" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="Poblacion, Polomolok, South Cotabato, Philippines 9504">
                    </div>

                    <!-- Copyright Notice -->
                    <div>
                        <label for="input-ft-copy" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Copyright Notice
                        </label>
                        <input id="input-ft-copy" name="fields[footer_copyright]" value="<?= esc($footerCopyVal) ?>" data-preview-key="footer_copyright" class="w-full border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:outline-none transition-all" placeholder="© 2026 Blax. All rights reserved.">
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" class="px-5 py-2.5 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[17px]">save</span>
                            <span>Save Footer Information</span>
                        </button>
                    </div>
                </form>
            </section>

        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- RIGHT COLUMN: Sticky Real-time Storefront Live Preview             -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="lg:col-span-5 sticky top-6 space-y-3">
            
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-md overflow-hidden">
                
                <!-- Preview Chrome Bar (Browser Mockup Header) -->
                <div class="px-4 py-3 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-400 shadow-2xs"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400 shadow-2xs"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400 shadow-2xs"></span>
                        <span class="ml-2 text-[11px] font-mono text-slate-600 dark:text-slate-300 font-medium bg-white dark:bg-slate-900 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 shadow-2xs">
                            <span class="material-symbols-outlined text-[13px] text-[#4A943E]">lock</span>
                            <span>blax.ph/home</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-[#4A943E] bg-[#4A943E]/10 dark:bg-[#4A943E]/20 px-2.5 py-1 rounded-lg border border-[#4A943E]/20">
                        <span class="w-2 h-2 rounded-full bg-[#4A943E] animate-pulse"></span>
                        <span>LIVE PREVIEW</span>
                    </div>
                </div>

                <!-- Preview Viewport Container -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-950 overflow-y-auto max-h-[calc(100vh-140px)] space-y-3.5 text-slate-800 dark:text-slate-200 text-xs select-none">
                    
                    <!-- 1. Preview Announcement Bar -->
                    <div id="pv-announcement-bar" class="<?= $annActiveVal ? '' : 'hidden' ?> bg-gradient-to-r from-[#4A943E] via-emerald-600 to-[#4A943E] text-white text-[10px] py-2 px-3 rounded-xl shadow-xs flex items-center justify-between gap-2 transition-all">
                        <div class="flex-1 flex items-center justify-center gap-1.5 text-center font-medium truncate">
                            <span class="material-symbols-outlined text-[14px] text-amber-300 shrink-0">campaign</span>
                            <span id="pv-announcement-text" class="truncate font-sans"><?= esc($annTextVal) ?></span>
                        </div>
                        <span class="text-[10px] text-white/70">✕</span>
                    </div>

                    <!-- 2. Preview Mini Header -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <img src="<?= base_url('icon.png') ?>" alt="Blax" class="w-5 h-5 rounded-md object-contain">
                            <span class="font-bold text-xs font-display text-slate-900 dark:text-white">Blax<span class="text-[#4A943E]">.</span></span>
                        </div>
                        <div class="flex-1 max-w-[140px] bg-slate-100 dark:bg-slate-800 rounded-lg px-2.5 py-1 text-[10px] text-slate-400 dark:text-slate-500 truncate flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">search</span>
                            <span>Search items...</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-[16px]">shopping_cart</span>
                            <span class="material-symbols-outlined text-[16px]">person</span>
                        </div>
                    </div>

                    <!-- 3. Preview Hero Banner -->
                    <div id="pv-hero-banner" class="relative w-full rounded-2xl overflow-hidden shadow-xs bg-white dark:bg-slate-900 min-h-[155px] flex items-center group border border-slate-200/60 dark:border-slate-800">
                        <div id="pv-hero-bg" class="absolute inset-0 bg-cover bg-center w-full h-full scale-105 opacity-95 transition-transform duration-500 pointer-events-none" style="background-image: url('<?= esc($heroImgUrl) ?>');"></div>
                        <div class="absolute inset-0 pointer-events-none bg-gradient-to-r from-white/95 via-white/75 to-transparent dark:from-slate-900/95 dark:via-slate-900/80 dark:to-transparent"></div>
                        
                        <div class="relative z-10 p-4 max-w-[220px] space-y-1.5">
                            <span id="pv-hero-badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#4A943E]/10 dark:bg-[#4A943E]/20 border border-[#4A943E]/30 text-[9px] font-bold text-[#4A943E] uppercase tracking-wider truncate backdrop-blur-xs">
                                <?= esc($heroBadgeVal) ?>
                            </span>
                            <h4 id="pv-hero-title" class="text-xs font-bold font-display text-slate-900 dark:text-white line-clamp-2 leading-snug">
                                <?= esc($heroTitleVal) ?>
                            </h4>
                            <p id="pv-hero-subtitle" class="text-[10px] text-slate-600 dark:text-slate-400 font-medium line-clamp-2 leading-tight">
                                <?= esc($heroSubVal) ?>
                            </p>
                            <div class="pt-1">
                                <span id="pv-hero-cta" class="inline-block bg-[#4A943E] text-white text-[9px] font-bold px-3 py-1 rounded-lg shadow-xs truncate">
                                    <?= esc($heroCtaVal) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Preview Catalog Section Header -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-800 space-y-1.5 shadow-2xs">
                        <div class="flex justify-between items-center">
                            <h5 id="pv-catalog-title" class="text-[11px] font-bold text-slate-900 dark:text-white truncate font-display">
                                <?= esc($catalogTitleVal) ?>
                            </h5>
                            <span class="text-[9px] text-[#4A943E] font-semibold">Filter &amp; Sort</span>
                        </div>
                        <p id="pv-catalog-subtitle" class="text-[9px] text-slate-500 dark:text-slate-400 line-clamp-1 leading-tight">
                            <?= esc($catalogSubVal) ?>
                        </p>
                        <!-- Mock Product Mini Row -->
                        <div class="grid grid-cols-3 gap-2 pt-1.5">
                            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-1.5 text-center space-y-1 border border-slate-100 dark:border-slate-700/50">
                                <div class="w-full h-10 bg-slate-200/60 dark:bg-slate-700/40 rounded-lg flex items-center justify-center text-[12px]">👕</div>
                                <div class="text-[8px] font-semibold truncate text-slate-700 dark:text-slate-300">T-Shirt</div>
                                <div class="text-[8px] font-bold text-[#4A943E] font-mono">₱250</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-1.5 text-center space-y-1 border border-slate-100 dark:border-slate-700/50">
                                <div class="w-full h-10 bg-slate-200/60 dark:bg-slate-700/40 rounded-lg flex items-center justify-center text-[12px]">📄</div>
                                <div class="text-[8px] font-semibold truncate text-slate-700 dark:text-slate-300">Flyer</div>
                                <div class="text-[8px] font-bold text-[#4A943E] font-mono">₱5</div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-1.5 text-center space-y-1 border border-slate-100 dark:border-slate-700/50">
                                <div class="w-full h-10 bg-slate-200/60 dark:bg-slate-700/40 rounded-lg flex items-center justify-center text-[12px]">☕</div>
                                <div class="text-[8px] font-semibold truncate text-slate-700 dark:text-slate-300">Mug</div>
                                <div class="text-[8px] font-bold text-[#4A943E] font-mono">₱180</div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Preview Mini Footer -->
                    <div id="pv-footer" class="bg-slate-100 dark:bg-slate-800 rounded-2xl p-3.5 border border-slate-200 dark:border-slate-700/80 space-y-2.5 text-[9px]">
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <span class="font-bold block text-slate-900 dark:text-white">Quick Links</span>
                                <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Shops • Print</span>
                            </div>
                            <div>
                                <span class="font-bold block text-slate-900 dark:text-white">Support</span>
                                <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Help • Terms</span>
                            </div>
                            <div>
                                <span class="font-bold block text-slate-900 dark:text-white">Contact</span>
                                <span id="pv-footer-email" class="text-slate-500 dark:text-slate-400 block truncate mt-0.5"><?= esc($footerEmailVal) ?></span>
                                <span id="pv-footer-phone" class="text-slate-500 dark:text-slate-400 block truncate"><?= esc($footerPhoneVal) ?></span>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 text-[8px] text-slate-500 dark:text-slate-400 text-center leading-tight">
                            <span id="pv-footer-copyright"><?= esc($footerCopyVal) ?></span>
                        </div>
                    </div>

                </div>

                <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 text-center border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-center gap-1.5 font-medium">
                    <span class="material-symbols-outlined text-[15px] text-[#4A943E]">sync</span>
                    <span>Live preview synchronizes instantly as you type on the left</span>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- Real-time Live Preview JS Synchronization                              -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const textTargetMap = {
        'hero_badge': 'pv-hero-badge',
        'hero_title': 'pv-hero-title',
        'hero_subtitle': 'pv-hero-subtitle',
        'hero_cta_text': 'pv-hero-cta',
        'catalog_title': 'pv-catalog-title',
        'catalog_subtitle': 'pv-catalog-subtitle',
        'announcement_text': 'pv-announcement-text',
        'footer_contact_email': 'pv-footer-email',
        'footer_phone': 'pv-footer-phone',
        'footer_address': 'pv-footer-address',
        'footer_copyright': 'pv-footer-copyright',
    };

    function updatePreview(key, val) {
        if (!key) return;

        // Announcement Active Toggle
        if (key === 'announcement_active') {
            const bar = document.getElementById('pv-announcement-bar');
            if (bar) {
                const isActive = (val === '1' || val === 'true' || val === 'on');
                if (isActive) {
                    bar.classList.remove('hidden');
                } else {
                    bar.classList.add('hidden');
                }
            }
            return;
        }

        const targetId = textTargetMap[key];
        if (targetId) {
            const el = document.getElementById(targetId);
            if (el) {
                el.textContent = val || '—';
            }
        }
    }

    // Listen on input and change across all previewable fields
    document.addEventListener('input', (e) => {
        const key = e.target.getAttribute('data-preview-key');
        if (key) {
            updatePreview(key, e.target.value);
        }
    });

    document.addEventListener('change', (e) => {
        const key = e.target.getAttribute('data-preview-key');
        if (key) {
            if (e.target.type === 'radio') {
                if (e.target.checked) updatePreview(key, e.target.value);
            } else {
                updatePreview(key, e.target.value);
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
