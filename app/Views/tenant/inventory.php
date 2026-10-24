<?= $this->extend('layouts/tenant') ?>

<?php
function inv_stock_pill(int $stock, int $threshold): array {
    if ($stock <= 0) {
        return ['bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200 dark:border-rose-900/50', 'block', 'Out of Stock'];
    }
    if ($stock <= $threshold) {
        return ['bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200 dark:border-amber-900/50', 'warning', 'Low Stock'];
    }
    return ['bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/50', 'check_circle', 'Healthy'];
}
?>

<?= $this->section('content') ?>

<div class="flex-1 space-y-6">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-300 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-terra-green animate-pulse"></span>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest font-mono">Catalog Control</span>
            </div>
            <h1 class="font-display text-2xl font-black text-slate-900 dark:text-white tracking-tight">Inventory Management</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xl">
                Oversee stock availability, variations, SKU pricing, and automated restock alerts across your entire storefront catalog.
            </p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap w-full sm:w-auto">
            <button type="button" onclick="openBulkStockModal()" class="px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/60 text-xs font-bold text-slate-700 dark:text-slate-200 transition-all flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <span class="material-symbols-outlined text-[18px] text-terra-green">autorenew</span>
                <span>Quick Restock</span>
            </button>
            <button type="button" onclick="openProductModal(null)" class="flex-1 sm:flex-initial bg-terra-green hover:bg-terra-green-dark text-white px-5 py-2.5 rounded-2xl flex items-center justify-center gap-2 font-display font-bold text-xs shadow-md shadow-terra-green/20 active:scale-95 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[19px]">add_circle</span>
                <span>Add Product</span>
            </button>
        </div>
    </div>

    <!-- Inventory Overview Bento Cards (Clickable Quick Stage Filters) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total SKUs -->
        <a href="<?= base_url('tenant/inventory') ?>" class="group bg-white dark:bg-slate-900 p-5 lg:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between <?= empty($filters['stock']) ? 'ring-2 ring-terra-green/30' : '' ?>">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total SKUs</span>
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green border border-terra-green/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
            </div>
            <div class="mt-4">
                <div id="summary-total-sku" class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight"><?= number_format((int) $summary['total_sku']) ?></div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-terra-green mt-1">
                    <span>Active in catalog</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Card 2: Low Stock Warning -->
        <a href="<?= base_url('tenant/inventory?stock=low') ?>" class="group bg-white dark:bg-slate-900 p-5 lg:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 hover:border-amber-500/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between <?= ($filters['stock'] ?? '') === 'low' ? 'ring-2 ring-amber-500/40' : '' ?>">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Low Stock</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                </div>
            </div>
            <div class="mt-4">
                <div id="summary-low-stock" class="font-display text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight"><?= number_format((int) $summary['low_stock']) ?></div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400 mt-1">
                    <span>Below threshold</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Card 3: Out of Stock -->
        <a href="<?= base_url('tenant/inventory?stock=out') ?>" class="group bg-white dark:bg-slate-900 p-5 lg:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 hover:border-rose-500/50 shadow-sm hover:shadow-md transition-all flex flex-col justify-between <?= ($filters['stock'] ?? '') === 'out' ? 'ring-2 ring-rose-500/40' : '' ?>">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Out of Stock</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 border border-rose-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-[20px]">block</span>
                </div>
            </div>
            <div class="mt-4">
                <div id="summary-out-of-stock" class="font-display text-2xl sm:text-3xl font-extrabold text-rose-600 font-mono tracking-tight"><?= number_format((int) $summary['out_of_stock']) ?></div>
                <div class="flex items-center gap-1 text-[11px] font-semibold text-rose-600 mt-1">
                    <span>Requires restocking</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Card 4: Inventory Valuation -->
        <div class="bg-white dark:bg-slate-900 p-5 lg:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Inventory Value</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </div>
            </div>
            <div class="mt-4">
                <div id="summary-inventory-value" class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight">₱<?= number_format((float) $summary['inventory_value'], 2) ?></div>
                <div class="text-[11px] font-medium text-emerald-600 mt-1">
                    Gross estimated value
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Stock Stage Tabs Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <!-- Stock Filter Pills -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-x-auto custom-scrollbar">
            <a href="<?= base_url('tenant/inventory') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= empty($filters['stock']) ? 'bg-terra-green text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' ?>">
                All Products (<?= (int)$summary['total_sku'] ?>)
            </a>
            <a href="<?= base_url('tenant/inventory?stock=in') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= ($filters['stock'] ?? '') === 'in' ? 'bg-terra-green text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' ?>">
                In Stock Healthy
            </a>
            <a href="<?= base_url('tenant/inventory?stock=low') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= ($filters['stock'] ?? '') === 'low' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' ?>">
                Low Stock Alert (<?= (int)$summary['low_stock'] ?>)
            </a>
            <a href="<?= base_url('tenant/inventory?stock=out') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= ($filters['stock'] ?? '') === 'out' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' ?>">
                Out of Stock (<?= (int)$summary['out_of_stock'] ?>)
            </a>
        </div>

        <!-- Search & Category Form -->
        <form method="get" action="<?= base_url('tenant/inventory') ?>" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="stock" value="<?= esc($filters['stock'] ?? '') ?>">
            
            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input name="q" value="<?= esc($filters['q']) ?>" class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green focus:border-terra-green transition-all placeholder:text-slate-400" placeholder="Search by title, description or SKU..." type="text">
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <select name="category" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-terra-green">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (int) $filters['category'] === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-terra-green hover:bg-terra-green-dark text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-colors shadow-2xs cursor-pointer">
                    Apply Filter
                </button>

                <?php if (!empty($filters['q']) || !empty($filters['category']) || !empty($filters['sku']) || !empty($filters['bestseller']) || !empty($filters['stock'])): ?>
                    <a href="<?= base_url('tenant/inventory') ?>" class="px-2.5 py-2 text-slate-500 hover:text-slate-800 dark:hover:text-white text-xs font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">close</span>
                        <span>Reset</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Product Dataset Container (Modern Table on Desktop, Responsive Cards on Mobile) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
        
        <!-- Desktop / Tablet Table View (hidden on mobile < md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/90 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-10 backdrop-blur-xs">
                    <tr>
                        <th class="py-3.5 px-4 w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-slate-300 dark:border-slate-600 text-terra-green focus:ring-terra-green h-4 w-4">
                        </th>
                        <th class="py-3.5 px-4 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Product Info &amp; Details</th>
                        <th class="py-3.5 px-4 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">SKU &amp; Variations</th>
                        <th class="py-3.5 px-4 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="py-3.5 px-4 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Price</th>
                        <th class="py-3.5 px-4 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-center">Stock Level</th>
                        <th class="py-3.5 px-4 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <?php
                            $stock = (int) $p['stock_quantity'];
                            $thr   = (int) ($p['low_stock_threshold'] ?? 5);
                            [$pill, $icon, $statusLabel] = inv_stock_pill($stock, $thr);
                            $variants = $productVariants[$p['id']] ?? [];
                            $hasVariants = !empty($variants);
                            $images = $productImages[$p['id']] ?? [];
                            $descPreview = trim(strip_tags($p['description'] ?? ''));
                            ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group" id="row-<?= (int) $p['id'] ?>">
                                <td class="py-4 px-4 align-top pt-4">
                                    <input type="checkbox" name="selected_products[]" value="<?= esc($p['id']) ?>" class="product-checkbox rounded border-slate-300 dark:border-slate-700 text-terra-green focus:ring-terra-green h-4 w-4">
                                </td>
                                
                                <!-- Product Info & Preview -->
                                <td class="py-4 px-4 max-w-xs md:max-w-sm">
                                    <div class="flex items-start gap-3">
                                        <div class="relative w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0 mt-0.5">
                                            <?php if (!empty($p['image_url'])): ?>
                                                <?php $imgSrc = product_image_url($p['image_url'], 'thumbnail'); ?>
                                                <img src="<?= esc($imgSrc) ?>" alt="<?= esc($p['name']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-[20px] text-slate-400">inventory_2</span>
                                            <?php endif; ?>
                                            <?php if (count($images) > 1): ?>
                                                <span class="absolute bottom-0 right-0 bg-slate-900/80 text-[9px] text-white font-mono px-1 rounded-tl-md">
                                                    <?= count($images) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors text-xs truncate leading-snug">
                                                    <?= esc($p['name']) ?>
                                                </span>
                                                <?php if (!empty($p['is_bestseller'])): ?>
                                                    <span class="px-1.5 py-0.2 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold border border-amber-500/20">★ Top</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5 leading-snug font-normal">
                                                <?= $descPreview !== '' ? esc($descPreview) : '<span class="italic text-slate-400">No description provided</span>' ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- SKU & Variations Details -->
                                <td class="py-4 px-4 align-top pt-4">
                                    <div class="space-y-1">
                                        <span class="font-mono text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 inline-block">
                                            <?= esc($p['sku'] ?: 'NO-SKU') ?>
                                        </span>
                                        <?php if ($hasVariants): ?>
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-terra-green/10 text-terra-green text-[10px] font-bold border border-terra-green/20" title="<?= count($variants) ?> options configured">
                                                    <span class="material-symbols-outlined text-[13px]">style</span>
                                                    <span><?= count($variants) ?> Variants</span>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="py-4 px-4 align-top pt-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-700 inline-block">
                                        <?= esc($p['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>

                                <!-- Price -->
                                <td class="py-4 px-4 text-right align-top pt-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm font-mono">₱<?= number_format((float) $p['price'], 2) ?></div>
                                    <?php if (!empty($p['compare_at_price']) && (float)$p['compare_at_price'] > (float)$p['price']): ?>
                                        <div class="text-[10px] text-slate-400 line-through font-mono">₱<?= number_format((float) $p['compare_at_price'], 2) ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Stock Health & Threshold -->
                                <td class="py-4 px-4 text-center align-top pt-4">
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <span id="stock-pill-<?= (int) $p['id'] ?>" class="px-2.5 py-1 rounded-xl <?= $pill ?> text-xs font-bold inline-flex items-center gap-1.5 border font-mono shadow-2xs">
                                            <span class="material-symbols-outlined text-[15px]"><?= $icon ?></span>
                                            <span id="stock-val-<?= (int) $p['id'] ?>"><?= number_format($stock) ?></span>
                                            <span class="text-[10px] font-sans font-normal opacity-75">(<?= $statusLabel ?>)</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">Alert below: <?= $thr ?></span>
                                    </div>
                                </td>

                                <!-- Clear Labeled Actions (No Confusion) -->
                                <td class="py-4 px-4 text-right align-top pt-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Primary Edit Button with Text Label -->
                                        <button type="button"
                                                onclick="openProductModal(this)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-terra-green hover:text-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 transition-all cursor-pointer shadow-2xs active:scale-95"
                                                title="Edit Product Details"
                                                data-id="<?= (int) $p['id'] ?>"
                                                data-name="<?= esc($p['name']) ?>"
                                                data-sku="<?= esc($p['sku']) ?>"
                                                data-category="<?= (int) ($p['category_id'] ?? 0) ?>"
                                                data-price="<?= esc($p['price']) ?>"
                                                data-compare="<?= esc($p['compare_at_price'] ?? '') ?>"
                                                data-stock="<?= (int) $p['stock_quantity'] ?>"
                                                data-threshold="<?= (int) ($p['low_stock_threshold'] ?? 5) ?>"
                                                data-shipping-fee="<?= esc($p['shipping_fee'] ?? '0.00') ?>"
                                                data-description="<?= esc($p['description'] ?? '') ?>"
                                                data-images="<?= esc(json_encode($images), 'attr') ?>"
                                                data-variants="<?= esc(json_encode($variants), 'attr') ?>">
                                            <span class="material-symbols-outlined text-[15px]">edit</span>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Direct Restock Action Button -->
                                        <button type="button"
                                                onclick="openStockModal(<?= (int) $p['id'] ?>, <?= (int) $p['stock_quantity'] ?>, '<?= esc($p['name'], 'js') ?>')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500 hover:text-white text-amber-700 dark:text-amber-400 text-xs font-bold border border-amber-500/20 transition-all cursor-pointer shadow-2xs active:scale-95"
                                                title="Quick Adjust Stock Quantity">
                                            <span class="material-symbols-outlined text-[15px]">autorenew</span>
                                            <span>Restock</span>
                                        </button>

                                        <!-- More Actions Dropdown Menu -->
                                        <div class="relative inline-block text-left">
                                            <button type="button"
                                                    onclick="toggleActionMenu(<?= (int) $p['id'] ?>, event)"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:hover:text-slate-100 flex items-center justify-center border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer"
                                                    title="More Actions">
                                                <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                            </button>
                                            <div id="action-menu-<?= (int) $p['id'] ?>" class="hidden action-menu absolute right-0 top-full mt-1.5 w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl p-1.5 z-40 text-left animate-in fade-in zoom-in-95 duration-100">
                                                <button type="button"
                                                        onclick="openProductModalFromMenu(<?= (int) $p['id'] ?>)"
                                                        class="w-full px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl flex items-center gap-2 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px] text-terra-green">edit_note</span>
                                                    <span>Edit Details</span>
                                                </button>
                                                <button type="button"
                                                        onclick="openStockModal(<?= (int) $p['id'] ?>, <?= (int) $p['stock_quantity'] ?>, '<?= esc($p['name'], 'js') ?>')"
                                                        class="w-full px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl flex items-center gap-2 transition-colors">
                                                    <span class="material-symbols-outlined text-[16px] text-amber-500">inventory</span>
                                                    <span>Adjust Stock</span>
                                                </button>
                                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                                                <form action="<?= base_url('tenant/products/archive/' . (int) $p['id']) ?>" method="POST" onsubmit="return confirm('Archive this product? It will be hidden from your storefront and can be restored anytime from the Archive page.')">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="w-full px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl flex items-center gap-2 transition-colors">
                                                        <span class="material-symbols-outlined text-[16px]">archive</span>
                                                        <span>Archive Product</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="w-14 h-14 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <span class="material-symbols-outlined text-3xl">search_off</span>
                                </div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">No products found</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Try modifying your search or clearing active filters.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (shown only on mobile < md) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <?php
                    $stock = (int) $p['stock_quantity'];
                    $thr   = (int) ($p['low_stock_threshold'] ?? 5);
                    [$pill, $icon, $statusLabel] = inv_stock_pill($stock, $thr);
                    $variants = $productVariants[$p['id']] ?? [];
                    $hasVariants = !empty($variants);
                    $images = $productImages[$p['id']] ?? [];
                    $descPreview = trim(strip_tags($p['description'] ?? ''));
                    ?>
                    <div class="p-4 space-y-3" id="mobile-row-<?= (int) $p['id'] ?>">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="selected_products[]" value="<?= esc($p['id']) ?>" class="product-checkbox rounded border-slate-300 dark:border-slate-700 text-terra-green focus:ring-terra-green h-4 w-4 mt-1">
                            <div class="relative w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                                <?php if (!empty($p['image_url'])): ?>
                                    <?php $imgSrc = product_image_url($p['image_url'], 'thumbnail'); ?>
                                    <img src="<?= esc($imgSrc) ?>" alt="<?= esc($p['name']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-[24px] text-slate-400">inventory_2</span>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate"><?= esc($p['name']) ?></h4>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white text-sm">₱<?= number_format((float) $p['price'], 2) ?></span>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    <span class="font-mono text-[10px] text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                        <?= esc($p['sku'] ?: 'NO-SKU') ?>
                                    </span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                        <?= esc($p['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                    <?php if ($hasVariants): ?>
                                        <span class="text-[10px] font-bold text-terra-green bg-terra-green/10 px-1.5 py-0.5 rounded">
                                            <?= count($variants) ?> Variants
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Stock and Actions Row on Mobile -->
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="px-2.5 py-1 rounded-xl <?= $pill ?> text-xs font-bold inline-flex items-center gap-1 border font-mono">
                                    <span class="material-symbols-outlined text-[14px]"><?= $icon ?></span>
                                    <span><?= number_format($stock) ?> in stock</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        onclick="openProductModal(document.querySelector('#row-<?= (int) $p['id'] ?> button[data-id]'))"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-terra-green hover:text-white text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">edit</span>
                                    <span>Edit</span>
                                </button>
                                <button type="button"
                                        onclick="openStockModal(<?= (int) $p['id'] ?>, <?= (int) $p['stock_quantity'] ?>, '<?= esc($p['name'], 'js') ?>')"
                                        class="px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500 hover:text-white text-amber-700 dark:text-amber-400 text-xs font-bold border border-amber-500/20 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">autorenew</span>
                                    <span>Restock</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="py-12 text-center p-4">
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">No products found</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Try modifying your search or clearing active filters.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php
        $total = (int) $pager->getTotal('inventory');
        $perPage = 10;
        $cur  = (int) $pager->getCurrentPage('inventory');
        $pages = (int) $pager->getPageCount('inventory');
        $start = $total === 0 ? 0 : ($cur - 1) * $perPage + 1;
        $end   = min($cur * $perPage, $total);
        ?>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center border-t border-slate-200/80 dark:border-slate-800 flex-wrap gap-2 text-xs">
            <p class="font-medium text-slate-500 dark:text-slate-400">Showing <span class="font-bold text-slate-900 dark:text-white font-mono"><?= number_format($start) ?></span> to <span class="font-bold text-slate-900 dark:text-white font-mono"><?= number_format($end) ?></span> of <span class="font-bold text-slate-900 dark:text-white font-mono"><?= number_format($total) ?></span> items</p>
            <?php if ($pages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <a class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 <?= $cur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('inventory') ?>">
                        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    </a>
                    <?php
                    $window = [];
                    for ($i = 1; $i <= $pages; $i++) {
                        if ($i === 1 || $i === $pages || abs($i - $cur) <= 2) {
                            $window[] = $i;
                        }
                    }
                    $prev = 0;
                    foreach ($window as $num):
                        if ($num - $prev > 1): ?>
                            <span class="px-1 text-slate-400">...</span>
                        <?php endif; ?>
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center font-bold font-mono <?= $cur === $num ? 'bg-terra-green text-white shadow-xs' : 'hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300' ?>" href="<?= $pager->getPageURI($num, 'inventory') ?>"><?= $num ?></a>
                    <?php $prev = $num; endforeach; ?>
                    <a class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 <?= $cur >= $pages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('inventory') ?>">
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- =========================================================================
     100x Better Structured Add / Edit Product Studio Modal
     ========================================================================= -->
<div id="productModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-5">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-3xl w-full border border-slate-200 dark:border-slate-800 shadow-2xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Sticky Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0 border border-terra-green/20">
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                </div>
                <div>
                    <h3 id="productModalTitle" class="font-display font-black text-lg text-slate-900 dark:text-white">Add New Product</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Configure catalog details, pricing rules, photos, and variations</p>
                </div>
            </div>
            <button type="button" onclick="closeProductModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition-colors cursor-pointer" aria-label="Close modal">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Guided Studio Steps Navigation Bar -->
        <div class="px-5 sm:px-6 pt-3 pb-2 bg-slate-50/70 dark:bg-slate-850 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar no-scrollbar py-1">
                <button type="button" onclick="switchProductTab('identity')" id="tab-btn-identity" class="product-step-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap bg-terra-green text-white shadow-2xs">
                    <span class="w-4 h-4 rounded-full bg-white/20 text-white flex items-center justify-center text-[10px] font-mono">1</span>
                    <span>Identity &amp; Photos</span>
                </button>
                <button type="button" onclick="switchProductTab('pricing')" id="tab-btn-pricing" class="product-step-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800">
                    <span class="w-4 h-4 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-mono">2</span>
                    <span>Pricing &amp; Stock</span>
                </button>
                <button type="button" onclick="switchProductTab('variations')" id="tab-btn-variations" class="product-step-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800">
                    <span class="w-4 h-4 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-mono">3</span>
                    <span>Variations (Optional)</span>
                    <span id="tab-variant-badge" class="hidden text-[10px] font-mono px-1.5 py-0.2 rounded-full bg-terra-green/20 text-terra-green font-bold">0</span>
                </button>
            </div>
        </div>

        <!-- Form Scroll Area -->
        <form id="productModalForm" action="<?= base_url('tenant/products/save') ?>" method="POST" enctype="multipart/form-data" class="overflow-y-auto flex-1 p-5 sm:p-6 custom-scrollbar">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" id="product_id">
            <!-- Hidden backward-compatible field for shipping fee (dynamically distance-based) -->
            <input type="hidden" name="shipping_fee" id="p_shipping_fee" value="0.00">

            <!-- SECTION 1: Identity & Photos -->
            <div id="section-identity" class="product-form-section space-y-4">
                <!-- Product Name -->
                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                        Product Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="p_name" required placeholder="e.g. Wireless Ergonomic Mouse or A4 Glossy Photo Paper" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green font-medium transition-all">
                </div>

                <!-- Category & SKU Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="category_id" id="p_category" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green font-semibold">
                            <option value="" disabled selected>Select Category...</option>
                            <?php foreach ($all_categories as $c): ?>
                                <option value="<?= (int) $c['id'] ?>"><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                            SKU Code <span class="text-[10px] font-normal text-slate-400 font-sans">(Auto-generated if empty)</span>
                        </label>
                        <input type="text" name="sku" id="p_sku" placeholder="e.g. SKU-84920" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-900 dark:text-white font-mono focus:outline-none focus:ring-2 focus:ring-terra-green">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                        Product Description
                    </label>
                    <textarea name="description" id="p_description" rows="3" placeholder="Describe the item, features, dimensions, or usage specifications clearly for buyers..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green transition-all"></textarea>
                </div>

                <!-- Photo Manager -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-terra-green">photo_library</span>
                            <span>Product Gallery</span>
                        </label>
                        <span class="text-[11px] text-slate-400 font-sans">JPG, PNG, WEBP (Max 5MB each)</span>
                    </div>

                    <!-- Existing uploaded thumbnails -->
                    <div id="p_existing_gallery" class="hidden p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono">Current Photos</span>
                        <div id="p_existing_thumbnails" class="flex flex-wrap gap-2.5"></div>
                    </div>

                    <!-- Dropzone -->
                    <div id="p_img_dropzone" onclick="document.getElementById('p_images').click()" class="cursor-pointer border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 hover:border-terra-green/60 bg-slate-50/50 dark:bg-slate-800/30 transition-all flex flex-col items-center justify-center gap-1 text-center group">
                        <div class="w-10 h-10 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-[22px]">add_photo_alternate</span>
                        </div>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">Upload Product Photos</p>
                        <p class="text-[11px] text-slate-400">Click to choose or drag images here. Multiple photos supported.</p>
                    </div>
                    <input type="file" name="product_images[]" id="p_images" multiple accept="image/jpeg,image/png,image/webp" class="sr-only">

                    <!-- New Selected Previews -->
                    <div id="p_new_preview_container" class="hidden p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono">Staged For Upload</span>
                        <div id="p_new_preview_list" class="flex flex-wrap gap-2.5"></div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Pricing & Stock Rules -->
            <div id="section-pricing" class="product-form-section hidden space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                            Selling Price (₱) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono font-bold text-slate-400">₱</span>
                            <input type="number" step="0.01" min="0" name="price" id="p_price" required placeholder="0.00" oninput="updateDiscountPreview()" class="w-full pl-8 pr-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                            Original / Compare Price (₱) <span class="text-[10px] font-normal text-slate-400 font-sans">(Optional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono font-bold text-slate-400">₱</span>
                            <input type="number" step="0.01" min="0" name="compare_at_price" id="p_compare" placeholder="0.00" oninput="updateDiscountPreview()" class="w-full pl-8 pr-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green">
                        </div>
                    </div>
                </div>

                <!-- Discount Preview Pill -->
                <div id="p_discount_preview" class="hidden p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/40 text-xs text-emerald-800 dark:text-emerald-300 font-medium flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px] text-emerald-600">sell</span>
                        <span>Promotional Discount:</span>
                    </span>
                    <span id="p_discount_val" class="font-bold font-mono text-emerald-700 dark:text-emerald-400">20% OFF</span>
                </div>

                <!-- Stock Quantity & Low Stock Threshold -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                            Total Stock Quantity <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" min="0" name="stock_quantity" id="p_stock" required placeholder="0" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green">
                        <p id="p_stock_variant_hint" class="hidden text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">sync</span>
                            <span>Auto-calculated from your variations below.</span>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1.5">
                            Low Stock Alert Threshold
                        </label>
                        <input type="number" min="0" name="low_stock_threshold" id="p_threshold" value="5" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-terra-green">
                        <p class="text-[10px] text-slate-400 mt-1">Alert badge triggers when inventory reaches or falls below this number.</p>
                    </div>
                </div>

                <!-- Automatic Delivery Notice (Replaced Confusing Standard Shipping Input) -->
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-[18px] text-terra-green shrink-0 mt-0.5">local_shipping</span>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        <span class="font-bold text-slate-800 dark:text-slate-200">Distance-Based Delivery:</span> Customer delivery fees are dynamically computed via Google Maps Routes API using platform rates. No manual per-item shipping fees required.
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Variations Composer -->
            <div id="section-variations" class="product-form-section hidden space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-display font-bold text-sm text-slate-900 dark:text-white">Product Variations &amp; Options</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Add color, size, pack, or material choices with individual stock quantities.</p>
                    </div>
                    <span id="variantCountBadge" class="text-xs font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">0 options</span>
                </div>

                <!-- Quick Preset Chips -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Quick Presets:</span>
                    <button type="button" onclick="setPresetCategory('Size')" class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">+ Size (S, M, L)</button>
                    <button type="button" onclick="setPresetCategory('Color')" class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">+ Color</button>
                    <button type="button" onclick="setPresetCategory('Pack')" class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">+ Pack / Bundle</button>
                    <button type="button" onclick="setPresetCategory('Type')" class="px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">+ Custom Option</button>
                </div>

                <!-- Compose Panel -->
                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-mono">
                        Add New Option
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                        <div class="sm:col-span-3">
                            <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 block mb-1">Option Category</label>
                            <input type="text" id="compose_variant_name" placeholder="e.g. Size, Color" class="w-full py-2 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 block mb-1">Option Value <span class="text-rose-500">*</span></label>
                            <input type="text" id="compose_variant_value" placeholder="e.g. Medium, Blue, 500g" class="w-full py-2 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green font-medium">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 block mb-1">Stock Qty <span class="text-rose-500">*</span></label>
                            <input type="number" id="compose_variant_stock" min="0" value="0" placeholder="0" class="w-full py-2 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold focus:ring-1 focus:ring-terra-green">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 block mb-1">Price (₱)</label>
                            <input type="number" id="compose_variant_price" step="0.01" min="0" placeholder="Base Price" class="w-full py-2 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono focus:ring-1 focus:ring-terra-green">
                        </div>
                        <div class="sm:col-span-2">
                            <button type="button" id="btnApplyVariant" onclick="applyVariant()" class="w-full py-2 px-3 bg-terra-green text-white rounded-xl text-xs font-bold hover:bg-terra-green-dark flex items-center justify-center gap-1 transition-colors shadow-2xs cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]" id="btnApplyVariantIcon">add</span>
                                <span id="btnApplyVariantText">Add Option</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Applied Variants List -->
                <div class="space-y-2">
                    <div id="variantEmptyHint" class="p-5 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-slate-400 text-xs flex flex-col items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-2xl text-slate-300 dark:text-slate-600">style</span>
                        <span>No variations added. If this product has one single stock and price, leave this empty.</span>
                    </div>

                    <div id="appliedVariantsList" class="space-y-2.5">
                        <!-- Populated dynamically by JS -->
                    </div>
                </div>

                <!-- Hidden inputs serialized on submit -->
                <div id="variantHiddenInputsContainer"></div>
            </div>

            <!-- Sticky Modal Footer -->
            <div class="pt-5 mt-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <button type="button" onclick="closeProductModal()" class="px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    Cancel
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnPrevTab" onclick="navigateProductStep(-1)" class="hidden px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                        Back
                    </button>
                    <button type="button" id="btnNextTab" onclick="navigateProductStep(1)" class="px-5 py-2.5 bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 rounded-2xl text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer shadow-2xs">
                        Next Step →
                    </button>
                    <button type="submit" id="btnSaveProduct" class="px-6 py-2.5 bg-terra-green hover:bg-terra-green-dark text-white rounded-2xl text-xs font-display font-bold shadow-md shadow-terra-green/20 transition-all cursor-pointer">
                        Save Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Adjust / Restock Modal -->
<div id="stockModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-display text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-terra-green">autorenew</span>
                <span>Restock Inventory</span>
            </h3>
            <button onclick="document.getElementById('stockModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-xl cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <div>
            <p id="stockProductName" class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate"></p>
            <p class="text-[11px] text-slate-400 mt-0.5">Current stock on hand: <span id="stockCurrent" class="font-bold text-slate-800 dark:text-white font-mono">0</span> units</p>
        </div>
        <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Adjustment Quantity</label>
            <input type="number" min="1" id="stockQty" value="10" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl font-mono font-bold text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green">
        </div>
        <div class="flex gap-2.5 pt-1">
            <button type="button" onclick="submitStockAdjust('add')" class="flex-1 py-2.5 bg-terra-green text-white rounded-2xl text-xs font-bold hover:bg-terra-green-dark shadow-2xs transition-colors cursor-pointer flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Add to Stock</span>
            </button>
            <button type="button" onclick="submitStockAdjust('set')" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px]">pin</span>
                <span>Set Exact</span>
            </button>
        </div>
        <p id="stockFeedback" class="text-xs text-center font-semibold text-terra-green"></p>
        <form id="stockCsrfForm" class="hidden"><?= csrf_field() ?></form>
    </div>
</div>

<!-- Bulk Actions Floating Bar -->
<div id="bulkActionsBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-800 shadow-2xl rounded-2xl px-5 py-2.5 flex items-center gap-3 transition-all">
    <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white">
        <span class="w-6 h-6 rounded-full bg-terra-green text-white flex items-center justify-center text-xs font-mono" id="selectedCountBadge">0</span>
        <span class="whitespace-nowrap">products selected</span>
    </div>
    <div class="h-4 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
    <button type="button" onclick="openBulkStockModal()" class="px-3.5 py-1.5 bg-terra-green text-white rounded-xl text-xs font-bold hover:bg-terra-green-dark flex items-center gap-1 transition-colors shadow-2xs whitespace-nowrap cursor-pointer">
        <span class="material-symbols-outlined text-[16px]">autorenew</span>
        <span>Bulk Restock</span>
    </button>
    <button type="button" onclick="submitBulkArchive()" class="px-3.5 py-1.5 bg-rose-500/10 text-rose-600 border border-rose-500/20 rounded-xl text-xs font-bold hover:bg-rose-500/20 flex items-center gap-1 transition-colors whitespace-nowrap cursor-pointer">
        <span class="material-symbols-outlined text-[16px]">archive</span>
        <span>Bulk Archive</span>
    </button>
    <button type="button" onclick="deselectAllProducts()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white cursor-pointer" title="Deselect All">
        <span class="material-symbols-outlined text-[18px]">close</span>
    </button>
</div>

<!-- Bulk Adjust Stock Modal -->
<div id="bulkStockModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="font-display text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-terra-green">autorenew</span>
                <span>Bulk Stock Adjustment</span>
            </h3>
            <button onclick="document.getElementById('bulkStockModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-xl cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-400">Update stock quantities for <span id="bulkStockCountText" class="font-bold text-terra-green font-mono">0</span> selected product(s).</p>
        <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Quantity</label>
            <input type="number" id="bulkStockQty" value="10" min="0" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-mono font-bold text-slate-900 dark:text-white">
        </div>
        <div class="flex gap-2.5 pt-1">
            <button type="button" onclick="submitBulkStockAdjust('add')" class="flex-1 py-2.5 bg-terra-green text-white rounded-2xl text-xs font-bold hover:bg-terra-green-dark shadow-2xs transition-colors flex items-center justify-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add_circle</span> Add to Stock
            </button>
            <button type="button" onclick="submitBulkStockAdjust('set')" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">pin</span> Set Exact
            </button>
        </div>
        <p id="bulkStockFeedback" class="text-xs text-center font-medium text-slate-500"></p>
    </div>
</div>

<script>
    /* =========================================================================
       Studio Modal Navigation & State
       ========================================================================= */
    const productTabs = ['identity', 'pricing', 'variations'];
    let currentProductTab = 'identity';

    function switchProductTab(tabId) {
        if (!productTabs.includes(tabId)) return;
        currentProductTab = tabId;

        // Hide all sections, show active
        document.querySelectorAll('.product-form-section').forEach(sec => sec.classList.add('hidden'));
        const activeSec = document.getElementById('section-' + tabId);
        if (activeSec) activeSec.classList.remove('hidden');

        // Update pills
        productTabs.forEach(id => {
            const btn = document.getElementById('tab-btn-' + id);
            if (!btn) return;
            if (id === tabId) {
                btn.className = 'product-step-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap bg-terra-green text-white shadow-2xs';
                const circle = btn.querySelector('span:first-child');
                if (circle) circle.className = 'w-4 h-4 rounded-full bg-white/20 text-white flex items-center justify-center text-[10px] font-mono';
            } else {
                btn.className = 'product-step-pill px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800';
                const circle = btn.querySelector('span:first-child');
                if (circle) circle.className = 'w-4 h-4 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-mono';
            }
        });

        // Navigation buttons state
        const prevBtn = document.getElementById('btnPrevTab');
        const nextBtn = document.getElementById('btnNextTab');
        const saveBtn = document.getElementById('btnSaveProduct');

        if (tabId === 'identity') {
            if (prevBtn) prevBtn.classList.add('hidden');
            if (nextBtn) { nextBtn.classList.remove('hidden'); nextBtn.textContent = 'Pricing & Stock →'; }
        } else if (tabId === 'pricing') {
            if (prevBtn) prevBtn.classList.remove('hidden');
            if (nextBtn) { nextBtn.classList.remove('hidden'); nextBtn.textContent = 'Variations →'; }
        } else if (tabId === 'variations') {
            if (prevBtn) prevBtn.classList.remove('hidden');
            if (nextBtn) nextBtn.classList.add('hidden');
        }
    }

    function navigateProductStep(delta) {
        const idx = productTabs.indexOf(currentProductTab);
        const nextIdx = idx + delta;
        if (nextIdx >= 0 && nextIdx < productTabs.length) {
            switchProductTab(productTabs[nextIdx]);
        }
    }

    function updateDiscountPreview() {
        const price = parseFloat(document.getElementById('p_price')?.value || 0);
        const compare = parseFloat(document.getElementById('p_compare')?.value || 0);
        const preview = document.getElementById('p_discount_preview');
        const val = document.getElementById('p_discount_val');

        if (preview && val && compare > price && price > 0) {
            const pct = Math.round(((compare - price) / compare) * 100);
            val.textContent = `${pct}% OFF (Saves ₱${(compare - price).toFixed(2)})`;
            preview.classList.remove('hidden');
        } else if (preview) {
            preview.classList.add('hidden');
        }
    }

    function setPresetCategory(catName) {
        const catInput = document.getElementById('compose_variant_name');
        if (catInput) {
            catInput.value = catName;
            const valInput = document.getElementById('compose_variant_value');
            if (valInput) valInput.focus();
        }
    }

    function closeProductModal() {
        const modal = document.getElementById('productModal');
        if (modal) modal.classList.add('hidden');
    }

    function openProductModal(btn) {
        const modal = document.getElementById('productModal');
        document.getElementById('productModalTitle').textContent = 'Add New Product';
        document.getElementById('product_id').value = '';
        document.getElementById('p_name').value = '';
        document.getElementById('p_sku').value = '';
        document.getElementById('p_category').value = '';
        document.getElementById('p_price').value = '';
        document.getElementById('p_compare').value = '';
        document.getElementById('p_stock').value = '';
        document.getElementById('p_threshold').value = '5';
        document.getElementById('p_shipping_fee').value = '0.00';
        document.getElementById('p_description').value = '';
        
        resetProductImages();
        variantState = [];
        editingVariantIndex = null;
        resetVariantComposer();
        updateDiscountPreview();

        if (btn) {
            document.getElementById('productModalTitle').textContent = 'Edit Product';
            document.getElementById('product_id').value = btn.dataset.id;
            document.getElementById('p_name').value = btn.dataset.name || '';
            document.getElementById('p_sku').value = btn.dataset.sku || '';
            document.getElementById('p_category').value = btn.dataset.category || '';
            document.getElementById('p_price').value = btn.dataset.price || '';
            document.getElementById('p_compare').value = btn.dataset.compare || '';
            document.getElementById('p_shipping_fee').value = btn.dataset.shippingFee || '0.00';
            document.getElementById('p_stock').value = btn.dataset.stock || '';
            document.getElementById('p_threshold').value = btn.dataset.threshold || '5';
            document.getElementById('p_description').value = btn.dataset.description || '';

            let images = [];
            try { images = JSON.parse(btn.dataset.images || '[]'); } catch (e) { images = []; }
            populateExistingImages(images);

            let variants = [];
            try { variants = JSON.parse(btn.dataset.variants || '[]'); } catch (e) { variants = []; }
            if (Array.isArray(variants) && variants.length > 0) {
                variantState = variants.map(v => ({
                    id: v.id || null,
                    name: (v.name || 'Color').trim(),
                    value: (v.value || '').trim(),
                    sku: (v.sku_suffix || '').trim(),
                    stock: Math.max(0, parseInt(v.stock_quantity, 10) || 0),
                    price: (v.price_override !== null && v.price_override !== '' && v.price_override !== undefined) ? parseFloat(v.price_override) : ''
                })).filter(v => v.value !== '');
            }
            updateDiscountPreview();
        }

        renderAppliedVariants();
        switchProductTab('identity');
        modal.classList.remove('hidden');
    }

    /* =========================================================================
       Photo Gallery Manager
       ========================================================================= */
    let newSelectedFiles = [];

    function syncFileInput() {
        const fileInput = document.getElementById('p_images');
        if (!fileInput) return;
        const dt = new DataTransfer();
        newSelectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
    }

    function renderNewImagePreviews() {
        const previewContainer = document.getElementById('p_new_preview_container');
        const list = document.getElementById('p_new_preview_list');
        if (!previewContainer || !list) return;

        list.innerHTML = '';
        if (newSelectedFiles.length === 0) {
            previewContainer.classList.add('hidden');
            syncFileInput();
            return;
        }

        previewContainer.classList.remove('hidden');
        newSelectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const item = document.createElement('div');
                item.className = 'relative w-16 h-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 group shadow-2xs';
                item.innerHTML = `
                    <img src="${ev.target.result}" class="w-full h-full object-cover">
                    <span class="absolute bottom-0 inset-x-0 bg-slate-900/80 text-[8px] text-white text-center py-0.5 truncate px-1 font-mono">${escapeHtml(file.name)}</span>
                    <button type="button" onclick="removeNewSelectedImage(${index})" class="absolute top-1 right-1 bg-rose-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] shadow hover:scale-110 transition-transform cursor-pointer" title="Cancel upload">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                `;
                list.appendChild(item);
            };
            reader.readAsDataURL(file);
        });

        syncFileInput();
    }

    function removeNewSelectedImage(index) {
        if (index >= 0 && index < newSelectedFiles.length) {
            newSelectedFiles.splice(index, 1);
            renderNewImagePreviews();
        }
    }

    function resetProductImages() {
        newSelectedFiles = [];
        syncFileInput();
        const fileInput = document.getElementById('p_images');
        if (fileInput) fileInput.value = '';
        const previewContainer = document.getElementById('p_new_preview_container');
        if (previewContainer) previewContainer.classList.add('hidden');
        const previewList = document.getElementById('p_new_preview_list');
        if (previewList) previewList.innerHTML = '';
        const existingGallery = document.getElementById('p_existing_gallery');
        if (existingGallery) existingGallery.classList.add('hidden');
        const existingThumbnails = document.getElementById('p_existing_thumbnails');
        if (existingThumbnails) existingThumbnails.innerHTML = '';
    }

    function populateExistingImages(images) {
        const gallery = document.getElementById('p_existing_gallery');
        const container = document.getElementById('p_existing_thumbnails');
        if (!gallery || !container) return;

        container.innerHTML = '';
        if (images && images.length > 0) {
            gallery.classList.remove('hidden');
            images.forEach(img => {
                const wrap = document.createElement('div');
                wrap.className = 'relative w-16 h-16 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 group bg-slate-100 dark:bg-slate-800 shadow-2xs';
                wrap.id = 'img-thumb-' + img.id;
                const src = img.image_url.startsWith('http') ? img.image_url : '<?= base_url() ?>/' + img.image_url;
                wrap.innerHTML = `
                    <img src="${src}" class="w-full h-full object-cover">
                    ${img.is_primary == 1 ? '<span class="absolute bottom-0 inset-x-0 bg-terra-green text-[9px] text-white text-center font-bold py-0.5">Primary</span>' : ''}
                    <button type="button" onclick="deleteExistingImage(${img.id}, this)" class="absolute top-1 right-1 bg-rose-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs shadow hover:scale-110 transition-transform cursor-pointer" title="Remove photo">
                        <span class="material-symbols-outlined text-[12px]">close</span>
                    </button>
                `;
                container.appendChild(wrap);
            });
        } else {
            gallery.classList.add('hidden');
        }
    }

    async function deleteExistingImage(imageId, btn) {
        if (!confirm('Remove this photo from the product?')) return;
        btn.disabled = true;

        const fd = new FormData();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?= csrf_hash() ?>';
        fd.append('<?= csrf_token() ?>', csrfToken);

        try {
            const res = await fetch('<?= base_url('tenant/products/images/delete/') ?>/' + imageId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            const data = await res.json();
            if (data.success) {
                const thumb = document.getElementById('img-thumb-' + imageId);
                if (thumb) thumb.remove();
                const container = document.getElementById('p_existing_thumbnails');
                if (container && container.children.length === 0) {
                    document.getElementById('p_existing_gallery').classList.add('hidden');
                }
            } else {
                alert(data.error || 'Failed to delete photo.');
                btn.disabled = false;
            }
        } catch (err) {
            alert('Network error while deleting photo.');
            btn.disabled = false;
        }
    }

    /* =========================================================================
       Variations State, Auto-Sync & Composer
       ========================================================================= */
    let variantState = [];
    let editingVariantIndex = null;

    function resetVariantComposer() {
        editingVariantIndex = null;
        const nameInput = document.getElementById('compose_variant_name');
        if (nameInput) nameInput.value = '';
        const valInput = document.getElementById('compose_variant_value');
        if (valInput) valInput.value = '';
        const stockInput = document.getElementById('compose_variant_stock');
        if (stockInput) stockInput.value = '0';
        const priceInput = document.getElementById('compose_variant_price');
        if (priceInput) priceInput.value = '';

        const btnText = document.getElementById('btnApplyVariantText');
        if (btnText) btnText.textContent = 'Add Option';
        const btnIcon = document.getElementById('btnApplyVariantIcon');
        if (btnIcon) btnIcon.textContent = 'add';
    }

    function syncVariantStockToMainInput() {
        const stockInput = document.getElementById('p_stock');
        const hint = document.getElementById('p_stock_variant_hint');
        const badge = document.getElementById('tab-variant-badge');

        if (badge) {
            badge.textContent = variantState.length;
            if (variantState.length > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        if (variantState.length > 0 && stockInput) {
            const sum = variantState.reduce((acc, curr) => acc + (parseInt(curr.stock, 10) || 0), 0);
            stockInput.value = sum;
            if (hint) {
                hint.classList.remove('hidden');
                hint.innerHTML = `<span class="material-symbols-outlined text-[14px]">sync</span> Total Stock (${sum} units) auto-calculated from ${variantState.length} variations.`;
            }
        } else if (hint) {
            hint.classList.add('hidden');
        }
    }

    function renderAppliedVariants() {
        const listContainer = document.getElementById('appliedVariantsList');
        const emptyHint = document.getElementById('variantEmptyHint');
        const countBadge = document.getElementById('variantCountBadge');

        if (!listContainer || !emptyHint) return;

        if (countBadge) {
            countBadge.textContent = variantState.length + (variantState.length === 1 ? ' option' : ' options');
        }

        syncVariantStockToMainInput();

        if (variantState.length === 0) {
            emptyHint.classList.remove('hidden');
            listContainer.innerHTML = '';
            return;
        }

        emptyHint.classList.add('hidden');

        // Group variants by Category
        const groups = {};
        variantState.forEach((item, idx) => {
            const cat = item.name && item.name.trim() !== '' ? item.name.trim() : 'Type';
            if (!groups[cat]) groups[cat] = [];
            groups[cat].push({ item, idx });
        });

        let html = '';
        for (const [catName, entries] of Object.entries(groups)) {
            html += `
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-terra-green uppercase tracking-wider flex items-center gap-1 font-mono">
                            <span class="material-symbols-outlined text-[14px]">label</span>
                            ${escapeHtml(catName)}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">${entries.length} option${entries.length === 1 ? '' : 's'}</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
            `;

            entries.forEach(({ item, idx }) => {
                const isOut = item.stock <= 0;
                const isLow = !isOut && item.stock <= 5;
                const stockBadgeClass = isOut ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50' : (isLow ? 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:border-amber-900/50' : 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/50');
                const stockLabel = isOut ? 'Out of Stock' : (isLow ? `Low: ${item.stock}` : `Stock: ${item.stock}`);
                const priceBadge = (item.price !== null && item.price !== '' && item.price !== undefined) ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold font-mono bg-terra-green/10 text-terra-green border border-terra-green/20">₱${Number(item.price).toFixed(2)}</span>` : '';

                html += `
                    <div class="inline-flex items-center gap-2 py-1.5 px-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <span class="font-bold text-xs text-slate-800 dark:text-slate-200">${escapeHtml(item.value)}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold border ${stockBadgeClass} font-mono">${stockLabel}</span>
                        ${priceBadge}
                        <div class="flex items-center gap-1 border-l border-slate-200 dark:border-slate-700 pl-1.5 ml-0.5">
                            <button type="button" onclick="editVariant(${idx})" class="p-1 text-slate-400 hover:text-terra-green transition-colors cursor-pointer" title="Edit variant">
                                <span class="material-symbols-outlined text-[15px]">edit</span>
                            </button>
                            <button type="button" onclick="removeVariant(${idx})" class="p-1 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer" title="Remove variant">
                                <span class="material-symbols-outlined text-[15px]">close</span>
                            </button>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        }

        listContainer.innerHTML = html;
    }

    function applyVariant() {
        const nameInput = document.getElementById('compose_variant_name');
        const valInput = document.getElementById('compose_variant_value');
        const stockInput = document.getElementById('compose_variant_stock');
        const priceInput = document.getElementById('compose_variant_price');

        if (!valInput) return;
        const val = valInput.value.trim();
        if (val === '') {
            valInput.focus();
            valInput.classList.add('ring-2', 'ring-rose-500');
            setTimeout(() => valInput.classList.remove('ring-2', 'ring-rose-500'), 1500);
            return;
        }

        const rawName = nameInput ? nameInput.value.trim() : '';
        const name = rawName !== '' ? rawName : 'Type';
        const stock = stockInput && stockInput.value !== '' ? Math.max(0, parseInt(stockInput.value, 10) || 0) : 0;
        const price = priceInput && priceInput.value !== '' ? parseFloat(priceInput.value) : '';

        if (editingVariantIndex !== null && editingVariantIndex >= 0 && editingVariantIndex < variantState.length) {
            variantState[editingVariantIndex] = {
                ...variantState[editingVariantIndex],
                name: name,
                value: val,
                stock: stock,
                price: price
            };
        } else {
            const existingIdx = variantState.findIndex(v => v.name.toLowerCase() === name.toLowerCase() && v.value.toLowerCase() === val.toLowerCase());
            if (existingIdx >= 0) {
                variantState[existingIdx].stock = stock;
                variantState[existingIdx].price = price;
            } else {
                variantState.push({
                    id: null,
                    name: name,
                    value: val,
                    stock: stock,
                    price: price,
                    sku: ''
                });
            }
        }

        resetVariantComposer();
        renderAppliedVariants();
        if (valInput) valInput.focus();
    }

    function editVariant(idx) {
        if (idx < 0 || idx >= variantState.length) return;
        const item = variantState[idx];
        editingVariantIndex = idx;

        const nameInput = document.getElementById('compose_variant_name');
        if (nameInput) nameInput.value = item.name || '';
        const valInput = document.getElementById('compose_variant_value');
        if (valInput) valInput.value = item.value || '';
        const stockInput = document.getElementById('compose_variant_stock');
        if (stockInput) stockInput.value = item.stock;
        const priceInput = document.getElementById('compose_variant_price');
        if (priceInput) priceInput.value = (item.price !== null && item.price !== '' && item.price !== undefined) ? item.price : '';

        const btnText = document.getElementById('btnApplyVariantText');
        if (btnText) btnText.textContent = 'Update';
        const btnIcon = document.getElementById('btnApplyVariantIcon');
        if (btnIcon) btnIcon.textContent = 'check';

        if (valInput) valInput.focus();
    }

    function removeVariant(idx) {
        if (idx < 0 || idx >= variantState.length) return;
        if (editingVariantIndex === idx) {
            resetVariantComposer();
        } else if (editingVariantIndex !== null && editingVariantIndex > idx) {
            editingVariantIndex--;
        }
        variantState.splice(idx, 1);
        renderAppliedVariants();
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

    /* =========================================================================
       DOM Event Handlers & Submissions
       ========================================================================= */
    document.addEventListener('DOMContentLoaded', function() {
        ['compose_variant_value', 'compose_variant_stock', 'compose_variant_price'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyVariant();
                    }
                });
            }
        });

        // Serialize variantState to hidden inputs on form submit
        const productForm = document.getElementById('productModalForm');
        if (productForm) {
            productForm.addEventListener('submit', function() {
                const hiddenContainer = document.getElementById('variantHiddenInputsContainer');
                if (!hiddenContainer) return;
                hiddenContainer.innerHTML = '';
                variantState.forEach(v => {
                    hiddenContainer.insertAdjacentHTML('beforeend', `
                        <input type="hidden" name="variant_name[]" value="${escapeHtml(v.name || 'Type')}">
                        <input type="hidden" name="variant_value[]" value="${escapeHtml(v.value)}">
                        <input type="hidden" name="variant_stock[]" value="${v.stock}">
                        <input type="hidden" name="variant_price[]" value="${v.price !== null && v.price !== undefined ? v.price : ''}">
                        <input type="hidden" name="variant_sku[]" value="${escapeHtml(v.sku || '')}">
                    `);
                });
            });
        }

        // Live Telemetry Listener: Updates summary counters in real time
        window.addEventListener('blax:inventory-summary-update', function(e) {
            const sum = e.detail;
            if (!sum) return;
            const totalEl = document.getElementById('summary-total-sku');
            if (totalEl && sum.total_sku !== undefined) totalEl.textContent = Number(sum.total_sku).toLocaleString();
            const lowEl = document.getElementById('summary-low-stock');
            if (lowEl && sum.low_stock !== undefined) lowEl.textContent = Number(sum.low_stock).toLocaleString();
            const outEl = document.getElementById('summary-out-of-stock');
            if (outEl && sum.out_of_stock !== undefined) outEl.textContent = Number(sum.out_of_stock).toLocaleString();
            const valEl = document.getElementById('summary-inventory-value');
            if (valEl && sum.inventory_value !== undefined) valEl.textContent = '₱' + Number(sum.inventory_value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    });

    document.getElementById('p_images')?.addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            Array.from(this.files).forEach(file => {
                if (!newSelectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    newSelectedFiles.push(file);
                }
            });
            renderNewImagePreviews();
        }
    });

    /* =========================================================================
       Single Stock Adjust Modal
       ========================================================================= */
    let stockProductId = null;
    function openStockModal(id, current, name) {
        stockProductId = id;
        document.getElementById('stockProductName').textContent = name;
        document.getElementById('stockCurrent').textContent = current;
        document.getElementById('stockQty').value = '10';
        document.getElementById('stockFeedback').textContent = '';
        document.getElementById('stockModal').classList.remove('hidden');
    }

    async function submitStockAdjust(mode) {
        const id = stockProductId;
        const qty = parseInt(document.getElementById('stockQty').value, 10);
        if (!id || !qty || qty < 1) {
            document.getElementById('stockFeedback').textContent = 'Enter a quantity of at least 1.';
            return;
        }
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('#stockCsrfForm [name="csrf_test_name"]')?.value;
        const body = new FormData();
        body.append('csrf_test_name', csrf);
        body.append('product_id', id);
        if (mode === 'add') { body.append('delta', qty); } else { body.append('set_to', qty); }
        document.getElementById('stockFeedback').textContent = 'Updating...';
        try {
            const resp = await fetch('<?= base_url('tenant/products/adjust-stock') ?>', { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await resp.json();
            if (!data.success) {
                document.getElementById('stockFeedback').textContent = data.error || 'Update failed.';
                return;
            }
            document.getElementById('stockCurrent').textContent = data.stock_quantity;
            const valEl = document.getElementById('stock-val-' + data.product_id);
            if (valEl) valEl.textContent = Number(data.stock_quantity).toLocaleString();
            const pillEl = document.getElementById('stock-pill-' + data.product_id);
            if (pillEl) {
                const isOut = data.stock_status === 'out';
                const isLow = data.stock_status === 'low';
                pillEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold inline-flex items-center gap-1 border font-mono ' +
                    (isOut ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200' : (isLow ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200'));
            }
            document.getElementById('stockFeedback').textContent = 'Stock updated successfully!';
            setTimeout(() => document.getElementById('stockModal').classList.add('hidden'), 700);
        } catch (e) {
            document.getElementById('stockFeedback').textContent = 'Could not reach server.';
        }
    }

    /* =========================================================================
       Bulk Operations
       ========================================================================= */
    function getSelectedProductIds() {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function updateBulkBar() {
        const selected = getSelectedProductIds();
        const bar = document.getElementById('bulkActionsBar');
        const badge = document.getElementById('selectedCountBadge');
        if (!bar || !badge) return;
        badge.textContent = selected.length;
        if (selected.length > 0) {
            bar.classList.remove('hidden');
        } else {
            bar.classList.add('hidden');
        }
    }

    function deselectAllProducts() {
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        updateBulkBar();
    }

    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateBulkBar();
        });
    }

    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const all = document.querySelectorAll('.product-checkbox');
            const checked = document.querySelectorAll('.product-checkbox:checked');
            if (selectAllCheckbox) selectAllCheckbox.checked = (all.length > 0 && all.length === checked.length);
            updateBulkBar();
        });
    });

    function openBulkStockModal() {
        const selected = getSelectedProductIds();
        if (selected.length === 0) {
            alert('Please select at least 1 product using the checkboxes.');
            return;
        }
        document.getElementById('bulkStockCountText').textContent = selected.length;
        document.getElementById('bulkStockFeedback').textContent = '';
        document.getElementById('bulkStockModal').classList.remove('hidden');
    }

    async function submitBulkStockAdjust(mode) {
        const selected = getSelectedProductIds();
        if (selected.length === 0) return;
        const qtyVal = document.getElementById('bulkStockQty').value;
        const qty = parseInt(qtyVal, 10);
        if (isNaN(qty) || qty < 0) {
            document.getElementById('bulkStockFeedback').textContent = 'Please enter a valid non-negative number.';
            return;
        }

        const fb = document.getElementById('bulkStockFeedback');
        fb.textContent = 'Updating ' + selected.length + ' products...';

        const body = new FormData();
        selected.forEach(id => body.append('product_ids[]', id));
        if (mode === 'add') {
            body.append('delta', qty);
        } else {
            body.append('set_to', qty);
        }

        try {
            const resp = await fetch('<?= base_url('tenant/products/bulk-adjust-stock') ?>', {
                method: 'POST',
                body: body,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            if (data.success) {
                fb.textContent = data.message || 'Stock updated successfully!';
                setTimeout(() => window.location.reload(), 800);
            } else {
                fb.textContent = data.error || 'Failed to update stock.';
            }
        } catch (err) {
            fb.textContent = 'Network error while updating stock.';
        }
    }

    async function submitBulkArchive() {
        const selected = getSelectedProductIds();
        if (selected.length === 0) return;
        if (!confirm('Archive ' + selected.length + ' selected product(s)? They will be hidden from the storefront and can be restored from the Archive page.')) {
            return;
        }

        const body = new FormData();
        selected.forEach(id => body.append('product_ids[]', id));

        try {
            const resp = await fetch('<?= base_url('tenant/products/bulk-archive') ?>', {
                method: 'POST',
                body: body,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Failed to archive products.');
            }
        } catch (err) {
            alert('Network error while archiving products.');
        }
    }

    /* =========================================================================
       Action Menu Handlers
       ========================================================================= */
    function toggleActionMenu(id, event) {
        event.stopPropagation();
        const targetMenu = document.getElementById('action-menu-' + id);
        document.querySelectorAll('.action-menu').forEach(menu => {
            if (menu !== targetMenu) menu.classList.add('hidden');
        });
        if (targetMenu) {
            targetMenu.classList.toggle('hidden');
        }
    }

    function openProductModalFromMenu(id) {
        const btn = document.querySelector('#row-' + id + ' button[data-id]');
        if (btn) {
            openProductModal(btn);
        }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-menu')) {
            document.querySelectorAll('.action-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });
</script>

<?= $this->endSection() ?>
