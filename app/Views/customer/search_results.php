<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<?php
    $totalPages    = $totalPages ?? 1;
    $currentPage   = $currentPage ?? 1;
    $totalProducts = (int) ($totalProducts ?? 0);
    $searchQuery   = $searchQuery ?? '';
    $categoryId    = (int) ($categoryId ?? 0);
    $categories    = $categories ?? [];
?>

<main class="max-w-container-max mx-auto px-4 md:px-8 lg:px-10 py-6 md:py-10 flex-grow w-full flex flex-col gap-6 sm:gap-8">

    <!-- Breadcrumbs -->
    <nav aria-label="Breadcrumb" class="flex text-xs font-semibold text-slate-500 dark:text-slate-400">
        <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
            <li class="inline-flex items-center">
                <a class="hover:text-terra-green transition-colors flex items-center gap-1 text-slate-600 dark:text-slate-400" href="<?= base_url('/') ?>">
                    <span class="material-symbols-outlined text-[16px] text-terra-green">home</span>
                    <span>Home</span>
                </a>
            </li>
            <li class="flex items-center">
                <span class="material-symbols-outlined text-[16px] text-slate-400 mx-0.5">chevron_right</span>
                <span class="text-slate-900 dark:text-white font-bold ml-1">Search Results</span>
            </li>
        </ol>
    </nav>

    <!-- Header Bento Stage -->
    <section class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs relative overflow-hidden">
        
        <!-- Ambient Terra Glow -->
        <div class="absolute -top-24 -right-24 w-60 h-60 bg-terra-green/5 dark:bg-terra-green/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2 border border-terra-green/20">
                <span class="material-symbols-outlined text-[14px]">search</span>
                Catalog Query
            </span>
            <h1 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2 flex-wrap">
                <?php if ($searchQuery !== ''): ?>
                    Results for &ldquo;<span class="text-terra-green dark:text-emerald-400 font-black"><?= esc($searchQuery) ?></span>&rdquo;
                <?php else: ?>
                    All Marketplace Products
                <?php endif; ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Found <span class="font-mono font-bold text-slate-900 dark:text-white"><?= number_format($totalProducts) ?></span> matching product<?= $totalProducts === 1 ? '' : 's' ?> from verified local merchants.
            </p>
        </div>

        <!-- Filter bar -->
        <form method="GET" action="<?= base_url('search') ?>" class="flex items-center gap-2 self-start md:self-auto shrink-0 z-10">
            <input type="hidden" name="q" value="<?= esc($searchQuery) ?>">
            <div class="relative">
                <select name="category_id" onchange="this.form.submit()" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-terra-green focus:border-terra-green shadow-2xs cursor-pointer appearance-none pr-8">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= $categoryId === (int) $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400 pointer-events-none">expand_more</span>
            </div>
        </form>
    </section>

    <!-- Results Bento Grid -->
    <?php if (!empty($products)): ?>
        <section class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4 md:gap-5" id="product-grid">
            <?php foreach ($products as $p): ?>
                <?php
                    $imageUrl = product_image_url($p['image_url'] ?? null);
                    $price = (float) ($p['price'] ?? 0);
                    $comparePrice = (float) ($p['compare_at_price'] ?? 0);
                    $hasDiscount = $comparePrice > $price;
                ?>
                <div class="bento-card group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl overflow-hidden hover:border-terra-green/40 dark:hover:border-terra-green/40 hover:shadow-xl transition-all duration-300 flex flex-col justify-between shadow-2xs">
                    
                    <div class="relative aspect-square overflow-hidden bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center p-3">
                        <a href="<?= base_url('product/' . $p['id']) ?>" class="block w-full h-full flex items-center justify-center">
                            <img class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" src="<?= esc($imageUrl) ?>" alt="<?= esc($p['name']) ?>" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80';">
                        </a>

                        <?php if ($hasDiscount): ?>
                            <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white px-2.5 py-0.5 rounded-full text-[9px] font-black shadow-xs z-10 uppercase tracking-wide">
                                SALE
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($p['shop_name'])): ?>
                            <span class="absolute bottom-2.5 left-2.5 bg-slate-900/85 dark:bg-slate-800/90 text-white px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold truncate max-w-[85%] backdrop-blur-xs z-10 border border-white/10">
                                <?= esc($p['shop_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="p-3.5 sm:p-4 flex flex-col flex-grow justify-between gap-2.5">
                        <div>
                            <?php if (!empty($p['category_name'])): ?>
                                <span class="text-[9px] sm:text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block mb-0.5 truncate">
                                    <?= esc($p['category_name']) ?>
                                </span>
                            <?php endif; ?>

                            <a href="<?= base_url('product/' . $p['id']) ?>" class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors line-clamp-2 block leading-snug">
                                <?= esc($p['name']) ?>
                            </a>
                        </div>

                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="text-xs sm:text-base font-black text-terra-green dark:text-emerald-400 font-mono">₱<?= number_format($price, 2) ?></span>
                                <?php if ($hasDiscount): ?>
                                    <span class="text-[10px] sm:text-xs text-slate-400 line-through ml-1 font-mono hidden sm:inline">₱<?= number_format($comparePrice, 2) ?></span>
                                <?php endif; ?>
                            </div>

                            <a href="<?= base_url('product/' . $p['id']) ?>" class="p-2 rounded-xl bg-terra-green/10 hover:bg-terra-green text-terra-green hover:text-white dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-terra-green dark:hover:text-white transition-all flex items-center justify-center shadow-2xs active:scale-95" title="View product">
                                <span class="material-symbols-outlined text-[16px] sm:text-[18px]">visibility</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center items-center gap-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= base_url('search?q=' . urlencode($searchQuery) . '&category_id=' . $categoryId . '&page=' . ($currentPage - 1)) ?>" class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs flex items-center gap-1 active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        <span>Previous</span>
                    </a>
                <?php endif; ?>

                <span class="text-xs text-slate-500 dark:text-slate-400 font-mono font-bold px-3">Page <?= $currentPage ?> of <?= $totalPages ?></span>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= base_url('search?q=' . urlencode($searchQuery) . '&category_id=' . $categoryId . '&page=' . ($currentPage + 1)) ?>" class="px-4 py-2 bg-terra-green hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors shadow-2xs flex items-center gap-1 active:scale-95">
                        <span>Next</span>
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <section class="text-center py-16 bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-8 max-w-xl mx-auto shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl">search_off</span>
            </div>
            <h3 class="font-display text-lg font-bold text-slate-900 dark:text-white mb-2">No matching products found</h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto leading-relaxed">We couldn't find any products matching your search term. Try checking for spelling errors, using broader terms, or exploring our product categories.</p>
            <div class="flex items-center justify-center gap-3 flex-wrap">
                <a href="<?= base_url('categories') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-terra-green hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">category</span>
                    <span>Browse Categories</span>
                </a>
                <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold hover:bg-slate-100 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    <span>Return to Marketplace</span>
                </a>
            </div>
        </section>
    <?php endif; ?>

</main>

<?= $this->endSection() ?>
