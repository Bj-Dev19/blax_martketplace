<?= $this->extend('layouts/tenant') ?>
<?= $this->section('content') ?>

<div class="space-y-6 pb-12">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-terra-green">check_circle</span>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm font-medium flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Header Section with Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">archive</span>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-display">Archive &amp; Recovery Hub</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-sans">Review, inspect, and safely restore historical catalog and order records.</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 font-medium" title="Orders and prints completed more than 3 days ago are preserved here safely.">
                <span class="material-symbols-outlined text-[16px] text-terra-green">auto_delete</span>
                <span>Auto-Archived (&gt;3 days old)</span>
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Stored -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4 hover:border-terra-green/40 transition-all">
            <div class="w-12 h-12 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">inventory</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Total Stored</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5 font-mono"><?= number_format($kpis['total'] ?? 0) ?></h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Archived items</p>
            </div>
        </div>

        <!-- Card 2: Archived Orders -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4 hover:border-sky-300 dark:hover:border-sky-700 transition-all">
            <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">shopping_bag</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Archived Orders</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5 font-mono"><?= number_format($kpis['orders'] ?? 0) ?></h3>
                <p class="text-[11px] text-sky-600 dark:text-sky-400 mt-0.5">Fulfilled &amp; preserved</p>
            </div>
        </div>

        <!-- Card 3: Archived Products -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4 hover:border-terra-amber/40 transition-all">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">inventory_2</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Archived Products</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5 font-mono"><?= number_format($kpis['inventory'] ?? 0) ?></h3>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-0.5">Products &amp; catalog</p>
            </div>
        </div>

        <!-- Card 4: Archived Prints -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4 hover:border-purple-300 dark:hover:border-purple-700 transition-all">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">print</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Archived Prints</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5 font-mono"><?= number_format($kpis['printing'] ?? 0) ?></h3>
                <p class="text-[11px] text-purple-600 dark:text-purple-400 mt-0.5">Completed printing jobs</p>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar & Category Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <!-- Category Tabs with Live Pill Counters -->
            <div class="inline-flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl p-1 border border-slate-200/60 dark:border-slate-700" role="tablist">
                <button type="button" data-tab="all" class="archive-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer" role="tab">
                    <span>All Items</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-full font-mono bg-white/30 text-white font-bold"><?= (int) ($kpis['total'] ?? 0) ?></span>
                </button>
                <button type="button" data-tab="order" class="archive-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer" role="tab">
                    <span>Orders</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-full font-mono bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold"><?= (int) ($kpis['orders'] ?? 0) ?></span>
                </button>
                <button type="button" data-tab="inventory" class="archive-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer" role="tab">
                    <span>Inventory</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-full font-mono bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold"><?= (int) ($kpis['inventory'] ?? 0) ?></span>
                </button>
                <button type="button" data-tab="printing_request" class="archive-tab px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5 cursor-pointer" role="tab">
                    <span>Printing</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-full font-mono bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold"><?= (int) ($kpis['printing'] ?? 0) ?></span>
                </button>
            </div>

            <!-- Per-Page Selector & Instant Search Input -->
            <div class="flex items-center gap-2 flex-wrap flex-1 justify-end">
                <form method="get" action="<?= base_url('tenant/archive') ?>" class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <?php if (!empty($activeType)): ?>
                        <input type="hidden" name="type" value="<?= esc($activeType) ?>">
                    <?php endif; ?>
                    <?php if (!empty($searchQuery)): ?>
                        <input type="hidden" name="q" value="<?= esc($searchQuery) ?>">
                    <?php endif; ?>
                    <label for="archive_per_page" class="font-medium">Show:</label>
                    <select name="per_page" id="archive_per_page" onchange="this.form.submit()" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs font-semibold focus:ring-2 focus:ring-terra-green/30 text-slate-800 dark:text-slate-200">
                        <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                        <option value="15" <?= ($per_page ?? 10) == 15 ? 'selected' : '' ?>>15</option>
                        <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </form>
                <div class="relative flex-1 sm:max-w-xs">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input id="archiveSearch" type="text" placeholder="Search by name, #ID, or order..." class="w-full pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-terra-green/30 text-slate-900 dark:text-white">
                    <button type="button" id="clearSearchBtn" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Multi-select Batch Action Bar -->
        <div id="floatingArchiveBar" class="hidden p-3.5 bg-terra-green text-white rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-lg transition-all animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">check_box</span>
                <span id="selectedCountLabel" class="text-xs font-bold font-mono">0 item(s) selected</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="submitBulkRestore()" class="px-3.5 py-1.5 bg-white text-terra-green rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5 shadow-xs cursor-pointer active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">restore</span>
                    <span>Restore Selected</span>
                </button>
                <button type="button" onclick="deselectAllItems()" class="px-2.5 py-1.5 text-white/80 hover:text-white text-xs underline transition-colors cursor-pointer">
                    Deselect All
                </button>
            </div>
        </div>
    </div>

    <!-- Archive Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead class="bg-slate-50 dark:bg-slate-800/70 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-10 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-terra-green focus:ring-terra-green cursor-pointer">
                        </th>
                        <th class="py-3 px-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Item Type</th>
                        <th class="py-3 px-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Item Details &amp; Reference</th>
                        <th class="py-3 px-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Archived Date</th>
                        <th class="py-3 px-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Archived By</th>
                        <th class="py-3 px-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs archive-table-body">
                    <?php if (!empty($archivedItems)): ?>
                        <?php foreach ($archivedItems as $p): ?>
                            <?php
                            $type = $p['item_type'] ?? 'order';
                            $details = $p['details'] ?? [];
                            $archiverName = trim(($p['archived_by_first'] ?? '') . ' ' . ($p['archived_by_last'] ?? ''));
                            $archiverInitials = $archiverName !== '' ? mb_strtoupper(mb_substr($archiverName, 0, 2)) : 'SY';
                            $archiverImage = trim((string) ($p['archived_by_image'] ?? ''));
                            $searchText = strtolower(($p['item_label'] ?? '') . ' ' . ($p['item_id'] ?? '') . ' ' . ($details['name'] ?? '') . ' ' . ($details['customer_name'] ?? ''));
                            ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors archive-row" data-id="<?= (int) $p['id'] ?>" data-type="<?= esc($type) ?>" data-search="<?= esc($searchText) ?>">
                                <!-- Checkbox -->
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" value="<?= (int) $p['id'] ?>" class="archive-item-cb w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-terra-green focus:ring-terra-green cursor-pointer" onchange="updateBatchToolbar()">
                                </td>

                                <!-- Item Type Badge -->
                                <td class="py-3 px-4">
                                    <?php if ($type === 'inventory'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 uppercase tracking-wide">
                                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                            <span>Inventory</span>
                                        </span>
                                    <?php elseif ($type === 'order'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-500/20 uppercase tracking-wide">
                                            <span class="material-symbols-outlined text-[14px]">shopping_bag</span>
                                            <span>Order</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20 uppercase tracking-wide">
                                            <span class="material-symbols-outlined text-[14px]">print</span>
                                            <span>Printing</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Rich Item Details -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <?php if ($type === 'inventory'): ?>
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0 overflow-hidden">
                                                <?php if (!empty($details['image_url'])): ?>
                                                    <img src="<?= esc(product_image_url($details['image_url'])) ?>" alt="product" class="w-full h-full object-cover" loading="lazy" decoding="async" onerror="this.remove()">
                                                <?php else: ?>
                                                    <span class="material-symbols-outlined text-slate-400 text-[20px]">package_2</span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-100 text-xs"><?= esc($p['item_label'] ?? ('Product #' . $p['item_id'])) ?></p>
                                                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                    <span class="font-mono">#<?= (int) $p['item_id'] ?></span>
                                                    <span>&bull;</span>
                                                    <span class="font-bold text-terra-green font-mono">₱<?= number_format($details['price'] ?? 0, 2) ?></span>
                                                    <?php if (!empty($details['category'])): ?>
                                                        <span>&bull;</span>
                                                        <span><?= esc($details['category']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php elseif ($type === 'order'): ?>
                                            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-100 text-xs"><?= esc($p['item_label'] ?? ('Order #' . $p['item_id'])) ?></p>
                                                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                    <span class="font-semibold text-slate-800 dark:text-slate-200"><?= esc($details['customer_name'] ?? 'Customer') ?></span>
                                                    <span>&bull;</span>
                                                    <span class="font-bold text-emerald-700 dark:text-emerald-400 font-mono">₱<?= number_format($details['total_amount'] ?? 0, 2) ?></span>
                                                    <span>&bull;</span>
                                                    <span class="capitalize"><?= esc($details['fulfillment_method'] ?? 'pickup') ?></span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-slate-100 text-xs"><?= esc($p['item_label'] ?? ('Print #' . $p['item_id'])) ?></p>
                                                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                    <span class="font-semibold text-slate-800 dark:text-slate-200"><?= esc($details['file_name'] ?? 'Document.pdf') ?></span>
                                                    <span>&bull;</span>
                                                    <span class="font-bold text-purple-600 font-mono">₱<?= number_format($details['total_price'] ?? 0, 2) ?></span>
                                                    <span>&bull;</span>
                                                    <span><?= esc($details['customer_name'] ?? 'Customer') ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Archive Date -->
                                <td class="py-3 px-4">
                                    <p class="text-xs text-slate-800 dark:text-slate-200 font-medium font-mono"><?= date('M d, Y', strtotime($p['archived_at'] ?? 'now')) ?></p>
                                    <p class="text-[11px] text-slate-400 font-mono"><?= date('h:i A', strtotime($p['archived_at'] ?? 'now')) ?></p>
                                </td>

                                <!-- Archived By -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center text-[11px] font-bold overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700">
                                            <?php if ($archiverImage !== ''): ?>
                                                <img class="w-full h-full object-cover" src="<?= esc(profile_image_url($archiverImage)) ?>" alt="avatar" loading="lazy" decoding="async" onerror="this.remove()">
                                            <?php else: ?>
                                                <span><?= esc($archiverInitials) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium"><?= esc($archiverName !== '' ? $archiverName : 'System') ?></span>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Quick Preview Details Modal Button -->
                                        <button type="button" onclick="openArchiveDetailModal(<?= (int) $p['id'] ?>)" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer" title="View details before restoring">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>

                                        <!-- Single Restore Form -->
                                        <form action="<?= base_url('tenant/archive/restore/' . (int) $p['id']) ?>" method="POST" onsubmit="return confirm('Restore this item back to active records?');" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-terra-green text-white rounded-xl text-xs font-bold hover:bg-terra-green/90 transition-all shadow-2xs active:scale-95 cursor-pointer">
                                                <span class="material-symbols-outlined text-[15px]">restore</span>
                                                <span>Restore</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <span class="material-symbols-outlined text-[28px]">inbox</span>
                                    </div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">Archive is completely clear</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Completed orders and prints older than 3 days will automatically appear here.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        $aTot     = (int) $pager->getTotal('archive');
        $aCur     = (int) $pager->getCurrentPage('archive');
        $aPag     = (int) $pager->getPageCount('archive');
        $aPerPage = (int) ($per_page ?? ($pager ? $pager->getPerPage('archive') : 10));
        $aStart   = $aTot === 0 ? 0 : ($aCur - 1) * $aPerPage + 1;
        $aEnd     = min($aCur * $aPerPage, $aTot);
        ?>
        <?php if ($aPag > 1): ?>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center border-t border-slate-200/80 dark:border-slate-800 flex-wrap gap-2">
                <p class="text-xs text-slate-500 dark:text-slate-400">Showing <?= number_format($aStart) ?> to <?= number_format($aEnd) ?> of <?= number_format($aTot) ?> items</p>
                <div class="flex items-center gap-1">
                    <a class="p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 <?= $aCur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('archive') ?>">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                    <?php
                    $w = [];
                    for ($i = 1; $i <= $aPag; $i++) {
                        if ($i === 1 || $i === $aPag || abs($i - $aCur) <= 2) { $w[] = $i; }
                    }
                    $pv = 0;
                    foreach ($w as $n):
                        if ($n - $pv > 1): ?><span class="px-1 text-slate-400 text-xs">...</span><?php endif; ?>
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center text-xs <?= $aCur === $n ? 'bg-terra-green text-white font-bold shadow-2xs' : 'hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' ?>" href="<?= $pager->getPageURI($n, 'archive') ?>"><?= $n ?></a>
                    <?php $pv = $n; endforeach; ?>
                    <a class="p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 <?= $aCur >= $aPag ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('archive') ?>">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal: Archived Item Preview / Details Inspector -->
<div id="archiveDetailModal" class="fixed inset-0 bg-slate-950/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-xs transition-opacity">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-800 flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
            <div class="flex items-center gap-2">
                <span id="modalItemTypeBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider font-mono">Item</span>
                <h3 id="modalItemTitle" class="text-base font-bold text-slate-900 dark:text-white font-display">Archived Item Details</h3>
            </div>
            <button type="button" onclick="closeArchiveDetailModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Body (Dynamic AJAX Content) -->
        <div id="modalItemBody" class="p-6 overflow-y-auto space-y-4 text-xs font-sans">
            <div class="py-8 flex flex-col items-center justify-center text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[32px] text-terra-green">progress_activity</span>
                <p class="text-xs mt-2">Loading item details...</p>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="px-6 py-3.5 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end gap-2">
            <button type="button" onclick="closeArchiveDetailModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">
                Close
            </button>
            <button type="button" id="modalRestoreBtn" class="px-4 py-2 bg-terra-green text-white rounded-xl text-xs font-bold hover:bg-terra-green/90 transition-all inline-flex items-center gap-1.5 shadow-xs cursor-pointer active:scale-95">
                <span class="material-symbols-outlined text-[16px]">restore</span>
                <span>Restore Item</span>
            </button>
        </div>
    </div>
</div>

<script>
    let archiveTab = 'all';
    const CSRF_NAME = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';

    function applyArchiveFilters() {
        const query = document.getElementById('archiveSearch').value.trim().toLowerCase();
        document.getElementById('clearSearchBtn').classList.toggle('hidden', query === '');
        let visible = 0;
        document.querySelectorAll('.archive-row').forEach((row) => {
            const typeMatch = archiveTab === 'all' || row.dataset.type === archiveTab;
            const searchMatch = query === '' || (row.dataset.search || '').indexOf(query) !== -1;
            const show = typeMatch && searchMatch;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        let emptyRow = document.getElementById('archiveEmptyRow');
        if (!emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.id = 'archiveEmptyRow';
            emptyRow.innerHTML = '<td colspan="6" class="py-12 text-center text-slate-500 dark:text-slate-400 font-medium text-xs">No archived items match your selected filter or search query.</td>';
            document.querySelector('.archive-table-body')?.appendChild(emptyRow);
        }
        emptyRow.classList.toggle('hidden', visible > 0 || document.querySelectorAll('.archive-row').length === 0);
    }

    document.querySelectorAll('.archive-tab').forEach((btn) => {
        btn.addEventListener('click', () => {
            archiveTab = btn.dataset.tab;
            document.querySelectorAll('.archive-tab').forEach((b) => {
                b.classList.remove('bg-terra-green', 'text-white', 'shadow-xs');
                b.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-200/60');
                const pill = b.querySelector('span:last-child');
                if (pill) {
                    pill.className = 'text-[11px] px-1.5 py-0.2 rounded-full font-mono bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold';
                }
            });
            btn.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-200/60');
            btn.classList.add('bg-terra-green', 'text-white', 'shadow-xs');
            const activePill = btn.querySelector('span:last-child');
            if (activePill) {
                activePill.className = 'text-[11px] px-1.5 py-0.2 rounded-full font-mono bg-white/30 text-white font-bold';
            }
            applyArchiveFilters();
        });
    });

    document.getElementById('archiveSearch').addEventListener('input', applyArchiveFilters);
    document.getElementById('clearSearchBtn').addEventListener('click', () => {
        document.getElementById('archiveSearch').value = '';
        applyArchiveFilters();
    });

    // Default tab active state
    document.querySelectorAll('.archive-tab').forEach((btn) => btn.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-200/60'));
    const defaultTab = document.querySelector('.archive-tab[data-tab="all"]');
    if (defaultTab) {
        defaultTab.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-200/60');
        defaultTab.classList.add('bg-terra-green', 'text-white', 'shadow-xs');
    }

    // Multi-Select Batch Actions
    function toggleSelectAll(masterCb) {
        const cbs = document.querySelectorAll('.archive-item-cb');
        cbs.forEach((cb) => {
            if (!cb.closest('tr').classList.contains('hidden')) {
                cb.checked = masterCb.checked;
            }
        });
        updateBatchToolbar();
    }

    function updateBatchToolbar() {
        const checked = document.querySelectorAll('.archive-item-cb:checked');
        const bar = document.getElementById('floatingArchiveBar');
        const countLabel = document.getElementById('selectedCountLabel');
        if (checked.length > 0) {
            bar.classList.remove('hidden');
            countLabel.textContent = checked.length + ' item(s) selected';
        } else {
            bar.classList.add('hidden');
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
        }
    }

    function deselectAllItems() {
        document.querySelectorAll('.archive-item-cb').forEach((cb) => cb.checked = false);
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) selectAll.checked = false;
        updateBatchToolbar();
    }

    async function submitBulkRestore() {
        const checked = Array.from(document.querySelectorAll('.archive-item-cb:checked')).map(c => c.value);
        if (checked.length === 0) return;
        if (!confirm('Restore ' + checked.length + ' selected item(s) back to active records?')) return;

        const formData = new FormData();
        formData.append(CSRF_NAME, CSRF_HASH);
        checked.forEach(id => formData.append('archive_ids[]', id));

        try {
            const res = await fetch('<?= base_url('tenant/archive/bulk-restore') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to restore items.');
            }
        } catch (e) {
            location.reload();
        }
    }

    async function submitBulkPermanentDelete() {
        const checked = Array.from(document.querySelectorAll('.archive-item-cb:checked')).map(c => c.value);
        if (checked.length === 0) return;
        if (!confirm('WARNING: Permanently purge ' + checked.length + ' item(s) from the archive? This cannot be undone.')) return;

        const formData = new FormData();
        formData.append(CSRF_NAME, CSRF_HASH);
        checked.forEach(id => formData.append('archive_ids[]', id));

        try {
            const res = await fetch('<?= base_url('tenant/archive/bulk-delete') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to purge items.');
            }
        } catch (e) {
            location.reload();
        }
    }

    // Archived Item Preview Modal
    async function openArchiveDetailModal(archiveId) {
        const modal = document.getElementById('archiveDetailModal');
        const body = document.getElementById('modalItemBody');
        const badge = document.getElementById('modalItemTypeBadge');
        const title = document.getElementById('modalItemTitle');
        const restoreBtn = document.getElementById('modalRestoreBtn');

        modal.classList.remove('hidden');
        body.innerHTML = `
            <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[32px] text-terra-green">progress_activity</span>
                <p class="text-xs mt-2">Loading item details...</p>
            </div>
        `;

        try {
            const res = await fetch('<?= base_url('tenant/archive/detail') ?>/' + archiveId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (!json.success || !json.data) {
                body.innerHTML = `<div class="p-4 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-semibold">${json.error || 'Unable to load details.'}</div>`;
                return;
            }

            const d = json.data;
            const ent = d.entity || {};

            title.textContent = d.item_label;
            badge.textContent = d.item_type.toUpperCase();
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider ' + 
                (d.item_type === 'inventory' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : (d.item_type === 'order' ? 'bg-sky-500/10 text-sky-600 border border-sky-500/20' : 'bg-purple-500/10 text-purple-600 border border-purple-500/20'));

            // Build dynamic detail cards
            let html = `
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Archive Date:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono">${d.archived_at}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Archived By:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">${d.archived_by_name} (${d.archived_by_email || 'System'})</span>
                    </div>
                </div>
            `;

            if (d.item_type === 'inventory') {
                html += `
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5 font-display">
                            <span class="material-symbols-outlined text-[18px] text-amber-500">inventory_2</span>
                            <span>Product Specifications</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-400 block">Price</span>
                                <span class="text-sm font-bold text-terra-green font-mono">₱${parseFloat(ent.price || 0).toFixed(2)}</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-400 block">Stock Before Archive</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-white font-mono">${ent.stock_quantity || 0}</span>
                            </div>
                        </div>
                        ${ent.sku ? `<div class="text-xs text-slate-500">SKU: <span class="font-mono font-semibold text-slate-800 dark:text-slate-200">${ent.sku}</span></div>` : ''}
                        ${ent.description ? `<div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-xs text-slate-600 dark:text-slate-300">${ent.description}</div>` : ''}
                    </div>
                `;
            } else if (d.item_type === 'order') {
                html += `
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5 font-display">
                            <span class="material-symbols-outlined text-[18px] text-sky-500">shopping_bag</span>
                            <span>Order Information</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-400 block">Total Amount</span>
                                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400 font-mono">₱${parseFloat(ent.total_amount || 0).toFixed(2)}</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-400 block">Fulfillment</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-white capitalize">${ent.fulfillment_method || 'pickup'}</span>
                            </div>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-xs text-slate-600 dark:text-slate-300 space-y-1">
                            <p><span class="font-semibold text-slate-800 dark:text-slate-200">Customer:</span> ${ent.first_name || ''} ${ent.last_name || ''}</p>
                            <p><span class="font-semibold text-slate-800 dark:text-slate-200">Email:</span> ${ent.email || 'N/A'}</p>
                            ${ent.phone_number ? `<p><span class="font-semibold text-slate-800 dark:text-slate-200">Phone:</span> ${ent.phone_number}</p>` : ''}
                        </div>
                    </div>
                `;
            } else if (d.item_type === 'printing_request') {
                html += `
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5 font-display">
                            <span class="material-symbols-outlined text-[18px] text-purple-500">print</span>
                            <span>Printing Job Specifications</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-400 block">Print Total</span>
                                <span class="text-sm font-bold text-purple-600 font-mono">₱${parseFloat(ent.total_price || 0).toFixed(2)}</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                                <span class="text-slate-400 block">Pages / Copies</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-white font-mono">${ent.page_count || 1} pages &bull; ${ent.copies || 1} copies</span>
                            </div>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl text-xs text-slate-600 dark:text-slate-300 space-y-1">
                            <p><span class="font-semibold text-slate-800 dark:text-slate-200">File:</span> ${ent.file_name || 'Document.pdf'}</p>
                            <p><span class="font-semibold text-slate-800 dark:text-slate-200">Customer:</span> ${ent.first_name || ''} ${ent.last_name || ''}</p>
                        </div>
                    </div>
                `;
            }

            body.innerHTML = html;

            restoreBtn.onclick = () => {
                if (confirm('Restore this item back to active records?')) {
                    const f = document.createElement('form');
                    f.method = 'POST';
                    f.action = '<?= base_url('tenant/archive/restore') ?>/' + archiveId;
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = CSRF_NAME;
                    inp.value = CSRF_HASH;
                    f.appendChild(inp);
                    document.body.appendChild(f);
                    f.submit();
                }
            };
        } catch (e) {
            body.innerHTML = `<div class="p-4 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 rounded-xl text-xs font-semibold">Error loading details.</div>`;
        }
    }

    function closeArchiveDetailModal() {
        document.getElementById('archiveDetailModal').classList.add('hidden');
    }
</script>

<?= $this->endSection() ?>