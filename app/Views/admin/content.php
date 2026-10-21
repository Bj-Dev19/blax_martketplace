<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$sectionMeta = [
    'home_banners' => [
        'title' => 'Homepage Banners',
        'icon' => 'view_carousel',
        'desc' => 'Hero banner badge, main headline, description, background image, CTA button, and catalog headings.'
    ],
    'announcement_bar' => [
        'title' => 'Announcement Bar',
        'icon' => 'campaign',
        'desc' => 'Global notification bar displayed across the top of all public customer storefront pages.'
    ],
    'footer_info' => [
        'title' => 'Footer & Contact Info',
        'icon' => 'contact_support',
        'desc' => 'Customer support email, hotline number, Polomolok hub physical location, and copyright notice.'
    ],
];

// Pre-calculate preview values from grouped items
$previewData = [];
foreach (($grouped ?? []) as $grpPage => $grpItems) {
    foreach ($grpItems as $it) {
        $previewData[$it['content_key']] = [
            'type'  => $it['content_type'],
            'text'  => $it['text_value'] ?? '',
            'image' => $it['image_url'] ?? '',
        ];
    }
}

// Recommended image dimensions & aspect ratio presets
$imageSpecs = [
    'hero_image' => [
        'aspect' => 'aspect-[3/1]',
        'guide'  => 'Recommended: 1440×480px (3:1 panoramic ratio, max 3MB)',
    ],
];

// Determine primary section order
$orderedSections = ['home_banners', 'announcement_bar', 'footer_info'];
foreach (array_keys($grouped ?? []) as $pageKey) {
    if (!in_array($pageKey, $orderedSections, true)) {
        $orderedSections[] = $pageKey;
    }
}
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

    <!-- CMS Overview Header Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center font-bold shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">edit_note</span>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold font-display text-slate-900 dark:text-white tracking-tight">Storefront Content Management</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Manage headlines, hero visual banners, announcements, and footer meta with real-time responsive preview.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            <a href="<?= base_url('/') ?>" target="_blank" class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-300 transition-all inline-flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-slate-500">open_in_new</span>
                <span>View Live Site</span>
            </a>
            <button type="button" onclick="document.getElementById('addEntryModal').classList.remove('hidden')" class="px-4 py-2.5 bg-[#4A943E] hover:bg-[#3D7C33] text-white rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-xs shadow-[#4A943E]/20">
                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                <span>Add Custom Field</span>
            </button>
        </div>
    </div>

    <!-- Main Split Grid: Left = Structured Form Cards, Right = Sticky Live Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- LEFT COLUMN: Structured Form Cards (7 cols on lg screens)          -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="lg:col-span-7 space-y-6">

            <?php foreach ($orderedSections as $page): ?>
                <?php 
                    $items = $grouped[$page] ?? [];
                    if (empty($items)) continue;
                    $meta = $sectionMeta[$page] ?? [
                        'title' => ucwords(str_replace('_', ' ', $page)),
                        'icon' => 'tune',
                        'desc' => 'Custom page settings and storefront parameters.'
                    ];
                ?>

                <section id="section-<?= esc($page) ?>" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                    
                    <!-- Section Header -->
                    <div class="px-6 py-4.5 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 flex flex-wrap justify-between items-center gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-[20px]"><?= esc($meta['icon']) ?></span>
                            </span>
                            <div>
                                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white"><?= esc($meta['title']) ?></h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?= esc($meta['desc']) ?></p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-mono">
                            <?= count($items) ?> <?= count($items) === 1 ? 'Field' : 'Fields' ?>
                        </span>
                    </div>

                    <!-- Entries List sorted top-to-bottom -->
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <?php foreach ($items as $it): ?>
                            <?php 
                                $key = $it['content_key'];
                                $isLive = isset($connectedFields[$key]);
                                $liveLocation = $connectedFields[$key] ?? null;
                                $displayLabel = !empty($it['label']) ? $it['label'] : ucwords(str_replace('_', ' ', $key));
                            ?>
                            <div class="p-6 space-y-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                
                                <!-- Field Header: Plain-language Label + Live Status Badge -->
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div class="space-y-1">
                                        <label for="field-<?= esc($it['id']) ?>" class="text-sm font-bold text-slate-900 dark:text-white block">
                                            <?= esc($displayLabel) ?>
                                        </label>
                                        <?php if ($key === 'announcement_active'): ?>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Toggle whether the top announcement banner is visible across the storefront.</p>
                                        <?php elseif ($key === 'hero_cta_text'): ?>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Label text displayed on the primary call-to-action button in the hero banner.</p>
                                        <?php elseif ($key === 'footer_copyright'): ?>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Legal trademark and copyright statement displayed at the very bottom.</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Live Status Badge -->
                                    <div class="shrink-0">
                                        <?php if ($isLive): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 shadow-2xs" title="This field is actively rendered on the live customer storefront">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#4A943E] animate-pulse"></span>
                                                <span>Live on <?= esc($liveLocation) ?></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700" title="Custom field created by admin; not connected to any storefront template yet">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Not connected to any page</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Field Form: Image Upload or Text Controls -->
                                <?php if ($it['content_type'] === 'image'): ?>
                                    <?php 
                                        $img = $it['image_url'] ?? ''; 
                                        $src = !empty($img) ? (str_starts_with($img, 'http') ? $img : base_url($img)) : '';
                                        $spec = $imageSpecs[$key] ?? ['aspect' => 'aspect-[2/1]', 'guide' => 'Recommended: 1200×600px (2:1 ratio, max 3MB)'];
                                    ?>
                                    <div class="space-y-3">
                                        <!-- Proportional Preview Frame -->
                                        <div class="relative w-full max-w-xl <?= esc($spec['aspect']) ?> rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 shadow-inner group">
                                            <?php if (!empty($src)): ?>
                                                <img src="<?= esc($src) ?>" alt="<?= esc($displayLabel) ?>" id="frame-img-<?= esc($it['id']) ?>" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300">
                                                <a href="<?= esc($src) ?>" target="_blank" class="absolute inset-0 bg-slate-950/50 backdrop-blur-xs text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold gap-1.5">
                                                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                                    <span>View Full Resolution</span>
                                                </a>
                                            <?php else: ?>
                                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-xs gap-1">
                                                    <span class="material-symbols-outlined text-3xl">image</span>
                                                    <span>No banner image uploaded</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Upload Form -->
                                        <form action="<?= base_url('admin/content/upload') ?>" method="POST" enctype="multipart/form-data" class="bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-3 max-w-xl">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $it['id'] ?>">

                                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                                <span class="uppercase font-bold tracking-wider text-slate-700 dark:text-slate-300">Upload Image File (JPG, PNG, WEBP)</span>
                                                <span class="font-mono text-[#4A943E] font-semibold"><?= esc($spec['guide']) ?></span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <input type="file" name="image" accept="image/*" class="flex-1 text-xs border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4A943E]/10 file:text-[#4A943E] hover:file:bg-[#4A943E]/20" required>
                                                <button type="submit" class="px-4 py-2 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs shrink-0 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[16px]">upload</span>
                                                    <span>Upload</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                <?php else: ?>
                                    <!-- Text / Textarea Form -->
                                    <form action="<?= base_url('admin/content/save') ?>" method="POST" class="space-y-3">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $it['id'] ?>">

                                        <?php if ($key === 'announcement_active'): ?>
                                            <!-- Friendly Toggle Selector for Announcement Active -->
                                            <?php $isActive = in_array(trim((string)($it['text_value'] ?? '0')), ['1', 'true', 'on'], true); ?>
                                            <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 flex-wrap">
                                                <label class="flex items-center gap-2 cursor-pointer p-1.5 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors">
                                                    <input type="radio" name="text_value" value="1" <?= $isActive ? 'checked' : '' ?> data-preview-key="<?= esc($key) ?>" class="text-[#4A943E] focus:ring-[#4A943E] h-4 w-4">
                                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                                        <span class="w-2 h-2 rounded-full bg-[#4A943E]"></span> Active (Show Bar)
                                                    </span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer p-1.5 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors">
                                                    <input type="radio" name="text_value" value="0" <?= !$isActive ? 'checked' : '' ?> data-preview-key="<?= esc($key) ?>" class="text-[#4A943E] focus:ring-[#4A943E] h-4 w-4">
                                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Inactive (Hide Bar)
                                                    </span>
                                                </label>
                                                <button type="submit" class="ml-auto px-4 py-2 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs">
                                                    Save Status
                                                </button>
                                            </div>

                                        <?php elseif ($it['content_type'] === 'textarea'): ?>
                                            <div class="space-y-2">
                                                <textarea id="field-<?= esc($it['id']) ?>" name="text_value" rows="3" data-preview-key="<?= esc($key) ?>" class="w-full border border-slate-200 dark:border-slate-700 rounded-2xl p-3.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:border-transparent focus:outline-none transition-all leading-relaxed" placeholder="Enter <?= esc($displayLabel) ?>..."><?= esc($it['text_value'] ?? '') ?></textarea>
                                                <div class="flex flex-wrap justify-between items-center gap-2">
                                                    <span class="text-[11px] text-slate-400 dark:text-slate-500">Previews instantly on the right. Press Save to persist changes.</span>
                                                    <button type="submit" class="px-4 py-2 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs flex items-center gap-1.5">
                                                        <span class="material-symbols-outlined text-[16px]">save</span>
                                                        <span>Save Text</span>
                                                    </button>
                                                </div>
                                            </div>

                                        <?php else: ?>
                                            <div class="flex items-center gap-2">
                                                <input id="field-<?= esc($it['id']) ?>" name="text_value" value="<?= esc($it['text_value'] ?? '') ?>" data-preview-key="<?= esc($key) ?>" class="flex-1 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800/40 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#4A943E] focus:border-transparent focus:outline-none transition-all" placeholder="Enter <?= esc($displayLabel) ?>...">
                                                <button type="submit" class="px-4 py-2.5 bg-[#4A943E] text-white rounded-xl text-xs font-bold hover:bg-[#3D7C33] transition-all shadow-xs shrink-0 flex items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                                    <span>Save</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                                <!-- Collapsed Technical Details / Rename for Developers -->
                                <details class="text-xs text-slate-500 dark:text-slate-400 group pt-1">
                                    <summary class="cursor-pointer font-medium hover:text-[#4A943E] inline-flex items-center gap-1.5 text-[11px] select-none text-slate-400 dark:text-slate-500 transition-colors">
                                        <span class="material-symbols-outlined text-[15px] transition-transform group-open:rotate-90">chevron_right</span>
                                        <span>Technical Developer Properties</span>
                                    </summary>
                                    <div class="mt-2.5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 space-y-2.5">
                                        <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                            <span class="font-mono bg-white dark:bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-semibold">Key: <?= esc($key) ?></span>
                                            <span class="font-mono bg-white dark:bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">Type: <?= esc($it['content_type']) ?></span>
                                            <span class="font-mono bg-white dark:bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">Page: <?= esc($page) ?></span>
                                        </div>
                                        <!-- Inline rename form -->
                                        <form action="<?= base_url('admin/content/save') ?>" method="POST" class="flex items-center gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $it['id'] ?>">
                                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase shrink-0">Display Label:</label>
                                            <input name="label" value="<?= esc($it['label'] ?? '') ?>" placeholder="Label name" class="flex-1 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200">
                                            <button type="submit" class="px-3 py-1 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                                                Update Label
                                            </button>
                                        </form>
                                    </div>
                                </details>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

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
                    <?php 
                        $annText = $previewData['announcement_text']['text'] ?? '🚀 Free doorstep delivery on orders over ₱500 within Polomolok! Use code BLAXSHIP';
                        $annActive = in_array(trim((string)($previewData['announcement_active']['text'] ?? '1')), ['1', 'true', 'on'], true);
                    ?>
                    <div id="pv-announcement-bar" class="<?= $annActive ? '' : 'hidden' ?> bg-gradient-to-r from-[#4A943E] via-emerald-600 to-[#4A943E] text-white text-[10px] py-2 px-3 rounded-xl shadow-xs flex items-center justify-between gap-2 transition-all">
                        <div class="flex-1 flex items-center justify-center gap-1.5 text-center font-medium truncate">
                            <span class="material-symbols-outlined text-[14px] text-amber-300 shrink-0">campaign</span>
                            <span id="pv-announcement-text" class="truncate font-sans"><?= esc($annText) ?></span>
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
                    <?php
                        $heroBadge = $previewData['hero_badge']['text'] ?? 'Seasonal Event';
                        $heroTitle = $previewData['hero_title']['text'] ?? 'The Ultimate Merchandise & Printing Hub';
                        $heroSubtitle = $previewData['hero_subtitle']['text'] ?? 'Discover premium goods, exclusive deals, and top-tier printing services all in one place across Polomolok.';
                        $heroCta = $previewData['hero_cta_text']['text'] ?? 'Explore Marketplace';
                        $heroImg = $previewData['hero_image']['image'] ?? 'https://images.unsplash.com/photo-1556742049-0a67daf64f42?auto=format&fit=crop&w=1440&q=80';
                        if ($heroImg && !str_starts_with($heroImg, 'http')) $heroImg = base_url($heroImg);
                    ?>
                    <div id="pv-hero-banner" class="relative w-full rounded-2xl overflow-hidden shadow-xs bg-white dark:bg-slate-900 min-h-[150px] flex items-center group border border-slate-200/60 dark:border-slate-800">
                        <div id="pv-hero-bg" class="absolute inset-0 bg-cover bg-center w-full h-full scale-105 opacity-95 transition-transform duration-500 pointer-events-none" style="background-image: url('<?= esc($heroImg) ?>');"></div>
                        <div class="absolute inset-0 pointer-events-none bg-gradient-to-r from-white/95 via-white/70 to-transparent dark:from-slate-900/95 dark:via-slate-900/75 dark:to-transparent"></div>
                        
                        <div class="relative z-10 p-4 max-w-[220px] space-y-1.5">
                            <span id="pv-hero-badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#4A943E]/10 dark:bg-[#4A943E]/20 border border-[#4A943E]/30 text-[9px] font-bold text-[#4A943E] uppercase tracking-wider truncate backdrop-blur-xs">
                                <?= esc($heroBadge) ?>
                            </span>
                            <h4 id="pv-hero-title" class="text-xs font-bold font-display text-slate-900 dark:text-white line-clamp-2 leading-snug">
                                <?= esc($heroTitle) ?>
                            </h4>
                            <p id="pv-hero-subtitle" class="text-[10px] text-slate-600 dark:text-slate-400 font-medium line-clamp-2 leading-tight">
                                <?= esc($heroSubtitle) ?>
                            </p>
                            <div class="pt-1">
                                <span id="pv-hero-cta" class="inline-block bg-[#4A943E] text-white text-[9px] font-bold px-3 py-1 rounded-lg shadow-xs truncate">
                                    <?= esc($heroCta) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Preview Catalog Section Header -->
                    <?php
                        $catTitle = $previewData['catalog_title']['text'] ?? 'Global Product Catalog';
                        $catSub = $previewData['catalog_subtitle']['text'] ?? 'Aggregation of all products currently available across the entire RHK network.';
                    ?>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-800 space-y-1.5 shadow-2xs">
                        <div class="flex justify-between items-center">
                            <h5 id="pv-catalog-title" class="text-[11px] font-bold text-slate-900 dark:text-white truncate font-display">
                                <?= esc($catTitle) ?>
                            </h5>
                            <span class="text-[9px] text-[#4A943E] font-semibold">Filter & Sort</span>
                        </div>
                        <p id="pv-catalog-subtitle" class="text-[9px] text-slate-500 dark:text-slate-400 line-clamp-1 leading-tight">
                            <?= esc($catSub) ?>
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
                    <?php
                        $ftEmail = $previewData['footer_contact_email']['text'] ?? 'support@blaxinventory.com';
                        $ftPhone = $previewData['footer_phone']['text'] ?? '+63 917 123 4567';
                        $ftAddress = $previewData['footer_address']['text'] ?? 'Poblacion, Polomolok, South Cotabato, Philippines 9504';
                        $ftCopy = $previewData['footer_copyright']['text'] ?? ('© ' . date('Y') . ' Blax. All rights reserved.');
                    ?>
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
                                <span id="pv-footer-email" class="text-slate-500 dark:text-slate-400 block truncate mt-0.5"><?= esc($ftEmail) ?></span>
                                <span id="pv-footer-phone" class="text-slate-500 dark:text-slate-400 block truncate"><?= esc($ftPhone) ?></span>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 text-[8px] text-slate-500 dark:text-slate-400 text-center leading-tight">
                            <span id="pv-footer-copyright"><?= esc($ftCopy) ?></span>
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
<!-- MODAL: Add New Custom Field (Gated with Clear Developer Warning)       -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div id="addEntryModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-3.5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[20px]">developer_mode</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold font-display text-slate-900 dark:text-white">Add New Custom CMS Field</h3>
            </div>
            <button type="button" onclick="document.getElementById('addEntryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Prominent Developer Warning -->
        <div class="p-4 rounded-2xl bg-[#F4AE52]/10 border border-[#F4AE52]/30 text-[#6E3511] dark:text-[#F4AE52] text-xs space-y-1.5">
            <div class="flex items-center gap-2 font-bold">
                <span class="material-symbols-outlined text-[18px] text-amber-600 dark:text-amber-400">warning</span>
                <span>Important Developer Notice</span>
            </div>
            <p class="leading-relaxed text-[11px] text-amber-900/90 dark:text-amber-200/80">
                Custom fields added here will <strong>not automatically appear</strong> anywhere on the customer website. A software developer must write code in the corresponding template file (e.g. reading <code>$siteContents['your_key']</code>) before customers will be able to see it.
            </p>
        </div>

        <form action="<?= base_url('admin/content/save') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            
            <div>
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Section Group</label>
                <select name="page" class="w-full mt-1.5 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#4A943E] focus:outline-none" required>
                    <option value="home_banners">Homepage Banners (home_banners)</option>
                    <option value="announcement_bar">Announcement Bar (announcement_bar)</option>
                    <option value="footer_info">Footer Info (footer_info)</option>
                    <option value="custom_page">Custom / Other Section (custom_page)</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Unique Key Name (snake_case)</label>
                <input name="content_key" placeholder="e.g. flash_sale_discount_badge" required pattern="[a-z0-9_]+" class="w-full mt-1.5 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#4A943E] focus:outline-none">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Lowercase letters, numbers, and underscores only.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Field Type</label>
                    <select name="content_type" class="w-full mt-1.5 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#4A943E] focus:outline-none">
                        <option value="text">Single Line Text</option>
                        <option value="textarea">Multi-line Textarea</option>
                        <option value="image">Image Upload</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Display Label</label>
                    <input name="label" placeholder="e.g. Flash Sale Badge" class="w-full mt-1.5 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#4A943E] focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Initial Text Value (Optional)</label>
                <textarea name="text_value" rows="2" placeholder="Initial value or placeholder..." class="w-full mt-1.5 p-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#4A943E] focus:outline-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addEntryModal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-[#4A943E] hover:bg-[#3D7C33] text-white transition-all shadow-xs">
                    Create Field
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- Real-time Live Preview JS Synchronization                              -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Map content_key to preview element IDs
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
rue' || val === 'on');
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
