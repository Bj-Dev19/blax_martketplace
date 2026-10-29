<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<?php
    $totalPages  = $totalPages ?? 1;
    $currentPage = $currentPage ?? 1;
    $totalProducts = (int) ($totalProducts ?? 0);
    $categorySlug = $category['slug'] ?? ($category['id'] ?? '');
    $categoryName = $category['name'] ?? 'Category Products';
?>

<main class="max-w-container-max mx-auto px-4 md:px-8 lg:px-10 flex-grow w-full py-6 sm:py-8 md:py-10 flex flex-col gap-6 sm:gap-8">

    <!-- Breadcrumbs -->
    <nav aria-label="Breadcrumb" class="flex text-xs font-semibold text-slate-500 dark:text-slate-400">
        <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
            <li class="inline-flex items-center">
                <a class="hover:text-terra-green transition-colors flex items-center gap-1" href="<?= base_url('/') ?>">
                    <span class="material-symbols-outlined text-[16px]">home</span>
                    <span>Home</span>
                </a>
            </li>
            <li class="flex items-center">
                <span class="material-symbols-outlined text-[16px] text-slate-400 mx-0.5">chevron_right</span>
                <a class="hover:text-terra-green transition-colors" href="<?= base_url('categories') ?>">Categories</a>
            </li>
            <li class="flex items-center">
                <span class="material-symbols-outlined text-[16px] text-slate-400 mx-0.5">chevron_right</span>
                <span class="text-slate-800 dark:text-slate-200 font-bold ml-1"><?= esc($categoryName) ?></span>
            </li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-terra-green/10 dark:bg-terra-green/20 text-terra-green border border-terra-green/20 text-xs font-bold uppercase tracking-wider mb-2">
                <span class="material-symbols-outlined text-[14px]">category</span>
                Department Catalog
            </span>
            <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= esc($categoryName) ?></h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Curated collection of quality merchandise and essentials available in Polomolok.</p>
        </div>

        <div class="flex items-center gap-3 text-xs font-semibold text-slate-600 dark:text-slate-300">
            <span class="bg-slate-100 dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 font-mono px-3.5 py-1.5 rounded-full"><?= $totalProducts ?> item<?= $totalProducts === 1 ? '' : 's' ?> found</span>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="cv-skip grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-5">

        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <?php
                    $imageUrl = product_image_url($p['image_url'] ?? null);
                    $rating   = $p['rating_average'] ?? 0;
                ?>
                <div class="bento-card rounded-2xl overflow-hidden flex flex-col group bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:border-terra-green/40 transition-all">
                    
                    <div class="relative aspect-square bg-slate-50 dark:bg-slate-800/50 overflow-hidden flex items-center justify-center p-2.5">
                        <a href="<?= base_url('product/' . $p['id']) ?>" class="block w-full h-full flex items-center justify-center">
                            <img src="<?= esc($imageUrl) ?>" alt="<?= esc($p['name']) ?>" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.onerror=null; this.src='<?= esc(placeholder_image_url()) ?>';">
                        </a>

                        <?php if (!empty($p['shop_name'])): ?>
                            <span class="absolute top-2 left-2 bg-slate-900/80 dark:bg-slate-800/90 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-2xs truncate max-w-[120px] backdrop-blur-xs z-10 border border-white/10">
                                <?= esc($p['shop_name']) ?>
                            </span>
                        <?php endif; ?>

                        <button type="button" class="absolute top-2 right-2 bg-white/90 dark:bg-slate-800/90 backdrop-blur-xs hover:bg-white text-slate-500 hover:text-rose-500 p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-all shadow-xs z-10" aria-label="Add to favorites">
                            <span class="material-symbols-outlined text-[15px]">favorite</span>
                        </button>
                    </div>

                    <div class="p-3 sm:p-4 flex flex-col flex-grow">
                        <h4 class="font-display text-xs sm:text-sm font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors line-clamp-2 leading-snug mb-1.5 min-h-[2.5rem]">
                            <a href="<?= base_url('product/' . $p['id']) ?>"><?= esc($p['name']) ?></a>
                        </h4>

                        <div class="flex items-center gap-1 mb-3">
                            <span class="material-symbols-outlined text-terra-amber text-[13px] fill-icon">star</span>
                            <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 font-mono"><?= number_format((float) $rating, 1) ?></span>
                        </div>

                        <div class="mt-auto flex justify-between items-center pt-2.5 border-t border-slate-100 dark:border-slate-800">
                            <div class="font-mono">
                                <span class="text-[11px] font-bold text-terra-green">₱</span>
                                <span class="text-sm sm:text-base font-black text-slate-900 dark:text-white"><?= number_format($p['price'], 2) ?></span>
                            </div>

                            <?php if ((int) ($p['variant_count'] ?? 0) > 0): ?>
                                <a href="<?= base_url('product/' . $p['id'] . '?select_option=1#variants-section') ?>" class="bg-terra-green/10 hover:bg-terra-green text-terra-green hover:text-white p-2 rounded-xl transition-all flex items-center justify-center shrink-0 w-8 h-8 sm:w-9 sm:h-9 active:scale-95 shadow-2xs" aria-label="Select product option" title="Select product option">
                                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                </a>
                            <?php else: ?>
                                <form action="<?= base_url('cart/add') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">

                                    <button type="submit" class="bg-terra-green/10 hover:bg-terra-green text-terra-green hover:text-white p-2 rounded-xl transition-all flex items-center justify-center shrink-0 w-8 h-8 sm:w-9 sm:h-9 active:scale-95 shadow-2xs" aria-label="Add to cart" title="Add to cart">
                                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full flex flex-col items-center text-center p-12 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-2">inventory_2</span>
                <h3 class="font-display text-base font-bold text-slate-800 dark:text-slate-200 mb-1">No Products in This Category</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mb-4">Check back soon as merchants regularly update their catalog inventory.</p>
                <a href="<?= base_url('categories') ?>" class="inline-block bg-terra-green text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs hover:bg-terra-green-dark transition-all">
                    Explore Other Categories
                </a>
            </div>
        <?php endif; ?>

    </div>

    <!-- Pagination -->
    <?php
        $pageUrl = function (int $pg) use ($categorySlug) {
            return base_url('category/' . $categorySlug . '?page=' . $pg);
        };

        $windowPages = [];
        if ($totalPages <= 7) {
            $windowPages = range(1, $totalPages);
        } else {
            $windowPages = [1];
            $left  = max(2, $currentPage - 2);
            $right = min($totalPages - 1, $currentPage + 2);
            if ($left > 2) $windowPages[] = '...';
            for ($pg = $left; $pg <= $right; $pg++) $windowPages[] = $pg;
            if ($right < $totalPages - 1) $windowPages[] = '...';
            $windowPages[] = $totalPages;
        }
    ?>

    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center items-center gap-1.5 sm:gap-2 mt-6">
            <?php if ($currentPage > 1): ?>
                <a href="<?= $pageUrl($currentPage - 1) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Previous page">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </a>
            <?php else: ?>
                <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-900/40 text-slate-300 dark:text-slate-600 cursor-not-allowed" aria-disabled="true">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </span>
            <?php endif; ?>

            <div class="flex items-center gap-1">
                <?php foreach ($windowPages as $pg): ?>
                    <?php if ($pg === '...'): ?>
                        <span class="px-2 text-slate-400 text-xs font-semibold" aria-hidden="true">...</span>
                    <?php elseif ((int) $pg === $currentPage): ?>
                        <a href="<?= $pageUrl((int) $pg) ?>" aria-current="page" class="w-9 h-9 flex items-center justify-center rounded-xl bg-terra-green text-white font-bold text-xs shadow-xs font-mono"><?= (int) $pg ?></a>
                    <?php else: ?>
                        <a href="<?= $pageUrl((int) $pg) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors font-semibold text-xs font-mono"><?= (int) $pg ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $pageUrl($currentPage + 1) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Next page">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </a>
            <?php else: ?>
                <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-900/40 text-slate-300 dark:text-slate-600 cursor-not-allowed" aria-disabled="true">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</main>

<?= $this->endSection() ?>
