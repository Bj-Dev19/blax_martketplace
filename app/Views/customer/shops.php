<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<?php
    $totalPages  = $totalPages ?? 1;
    $currentPage = $currentPage ?? 1;
    $totalShops  = (int) ($totalShops ?? 0);
    $searchQuery = $searchQuery ?? '';
    $category    = $category ?? '';
    $sort        = $sort ?? 'top-rated';

    $compactCount = function ($n) {
        $n = (int) $n;
        if ($n >= 1000) {
            return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'k';
        }
        return number_format($n);
    };

    $filterUrl = function ($newCat = null, $newSort = null, $newQ = null) use ($category, $sort, $searchQuery) {
        $params = [];
        $c = $newCat !== null ? $newCat : $category;
        $s = $newSort !== null ? $newSort : $sort;
        $q = $newQ !== null ? $newQ : $searchQuery;

        if ($q !== '') $params['q'] = $q;
        if ($c !== '') $params['category'] = $c;
        if ($s !== 'top-rated') $params['sort'] = $s;
        return base_url('shops' . (!empty($params) ? '?' . http_build_query($params) : ''));
    };
?>

<main class="max-w-container-max mx-auto px-4 md:px-8 lg:px-10 flex-grow w-full py-6 sm:py-8 md:py-10 flex flex-col gap-6 sm:gap-8">

    <!-- Header & Breadcrumbs Bento Stage -->
    <section class="flex flex-col gap-3">
        <nav aria-label="Breadcrumb" class="flex text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
            <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
                <li class="inline-flex items-center">
                    <a class="hover:text-terra-green transition-colors flex items-center gap-1 text-slate-600 dark:text-slate-400" href="<?= base_url('/') ?>">
                        <span class="material-symbols-outlined text-[16px]">home</span>
                        <span>Home</span>
                    </a>
                </li>
                <li class="flex items-center">
                    <span class="material-symbols-outlined text-[16px] text-slate-400 mx-0.5">chevron_right</span>
                    <span class="text-slate-900 dark:text-white font-bold ml-1">Shops Directory</span>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-terra-green/10 text-terra-green dark:text-terra-green-light text-xs font-bold uppercase tracking-wider mb-2 border border-terra-green/20">
                    <span class="material-symbols-outlined text-[14px]">storefront</span>
                    Polomolok Merchant Directory
                </span>
                <h1 class="font-display text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">Verified Local Shops</h1>
                <p class="text-xs sm:text-base text-slate-500 dark:text-slate-400 mt-1">Discover trusted local merchants, specialty stationery stores, and professional print providers in Polomolok.</p>
            </div>

            <!-- Bento Stats Indicator -->
            <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700 px-3.5 py-1.5 rounded-full shadow-2xs font-mono">
                    <span class="w-2 h-2 rounded-full bg-terra-green animate-pulse"></span>
                    <?= $totalShops ?> shops listed
                </span>
            </div>
        </div>
    </section>

    <!-- Search & Filter Bento Bar -->
    <section class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-3 sm:p-5 flex flex-col md:flex-row gap-3 sm:gap-4 items-stretch md:items-center justify-between shadow-xs">
        
        <!-- Search Input Form -->
        <form method="GET" action="<?= base_url('shops') ?>" class="flex-1 relative" role="search">
            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input name="q" value="<?= esc($searchQuery) ?>" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2 sm:py-2.5 text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green focus:bg-white dark:focus:bg-slate-800 transition-all shadow-2xs" placeholder="Search shops by name or specialty..." type="text">
            
            <?php if ($category !== ''): ?>
                <input type="hidden" name="category" value="<?= esc($category) ?>">
            <?php endif; ?>
            <?php if ($sort !== 'top-rated'): ?>
                <input type="hidden" name="sort" value="<?= esc($sort) ?>">
            <?php endif; ?>
        </form>

        <!-- Segmented Category Filters -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar pb-0.5">
            
            <a href="<?= $filterUrl('') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= empty($category) ? 'bg-terra-green text-white shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' ?>">
                <span>All Shops</span>
            </a>

            <a href="<?= $filterUrl('printing') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $category === 'printing' ? 'bg-terra-green text-white shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' ?>">
                <span class="material-symbols-outlined text-[14px] sm:text-[15px]">print</span>
                <span>Printing</span>
            </a>

            <a href="<?= $filterUrl('enterprise') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $category === 'enterprise' ? 'bg-terra-green text-white shadow-xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' ?>">
                <span class="material-symbols-outlined text-[14px] sm:text-[15px]">corporate_fare</span>
                <span>Enterprise</span>
            </a>

            <!-- Sort By Select -->
            <form method="GET" action="<?= base_url('shops') ?>" class="inline-flex shrink-0">
                <?php if ($searchQuery !== ''): ?>
                    <input type="hidden" name="q" value="<?= esc($searchQuery) ?>">
                <?php endif; ?>
                <?php if ($category !== ''): ?>
                    <input type="hidden" name="category" value="<?= esc($category) ?>">
                <?php endif; ?>

                <select name="sort" onchange="this.form.submit()" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 sm:px-3 sm:py-2 text-xs font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-terra-green focus:bg-white dark:focus:bg-slate-800 shadow-2xs cursor-pointer">
                    <option value="top-rated" <?= $sort === 'top-rated' ? 'selected' : '' ?>>Top Rated</option>
                    <option value="most-recent" <?= $sort === 'most-recent' ? 'selected' : '' ?>>Most Recent</option>
                </select>
            </form>

        </div>

    </section>

    <!-- Bento Shop Cards Grid -->
    <section>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
            <?php if (!empty($shops)): ?>
                <?php foreach ($shops as $s): ?>
                    <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:-translate-y-1 hover:border-terra-green/50 rounded-2xl overflow-hidden flex flex-col justify-between group transition-all duration-300">
                        
                        <div>
                            <!-- Header Cover Banner -->
                            <div class="h-16 sm:h-22 w-full bg-gradient-to-r from-emerald-600/10 via-amber-500/10 to-teal-600/10 dark:from-emerald-950/40 dark:via-amber-950/30 dark:to-slate-900 relative p-2 sm:p-3 flex justify-between items-start">
                                
                                <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-slate-900/90 backdrop-blur-md text-slate-800 dark:text-slate-200 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs border border-white/20 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-terra-green animate-pulse"></span>
                                    <span>Verified</span>
                                </span>

                                <?php if (!empty($s['offers_printing'])): ?>
                                    <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md text-terra-green dark:text-emerald-400 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs border border-emerald-500/20">
                                        <span class="material-symbols-outlined text-[10px] sm:text-[12px]">print</span>
                                        <span>Print</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md text-slate-700 dark:text-slate-300 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs border border-slate-200/80 dark:border-slate-700">
                                        <span class="material-symbols-outlined text-[10px] sm:text-[12px]">storefront</span>
                                        <span>Retail</span>
                                    </span>
                                <?php endif; ?>

                            </div>

                            <!-- Shop Info Body with Squircle Avatar -->
                            <div class="p-3 sm:p-4 pt-0 flex flex-col items-center text-center -mt-7 sm:-mt-10">
                                
                                <div class="w-14 h-14 sm:w-18 sm:h-18 rounded-2xl p-0.5 sm:p-1 bg-white dark:bg-slate-850 shadow-md border border-slate-100 dark:border-slate-750 overflow-hidden mb-2 sm:mb-2.5 group-hover:scale-105 transition-transform shrink-0">
                                    <div class="w-full h-full rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 flex items-center justify-center relative">
                                        <?php $sLogo = logo_url($s['logo_url'] ?? null); ?>
                                        <?php if (!empty($sLogo)): ?>
                                            <img class="w-full h-full object-cover" src="<?= esc($sLogo) ?>" alt="<?= esc($s['shop_name']) ?> logo" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                            <div class="hidden w-full h-full bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex flex-col items-center justify-center text-terra-green font-bold">
                                                <span class="material-symbols-outlined text-[18px] sm:text-[24px]">storefront</span>
                                                <span class="text-[8px] sm:text-[9px] uppercase tracking-wider font-mono"><?= esc(substr($s['shop_name'], 0, 3)) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-full h-full bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex flex-col items-center justify-center text-terra-green font-bold">
                                                <span class="material-symbols-outlined text-[18px] sm:text-[24px]">storefront</span>
                                                <span class="text-[8px] sm:text-[9px] uppercase tracking-wider font-mono"><?= esc(substr($s['shop_name'], 0, 3)) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Shop Title -->
                                <h3 class="font-display text-xs sm:text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors line-clamp-1 w-full" title="<?= esc($s['shop_name']) ?>">
                                    <a href="<?= base_url('shop/' . ($s['slug'] ?? $s['id'])) ?>">
                                        <?= esc($s['shop_name']) ?>
                                    </a>
                                </h3>

                                <!-- Rating & Reviews (Monospace) -->
                                <div class="inline-flex items-center gap-1 bg-amber-50/80 dark:bg-amber-950/30 px-2 py-0.5 rounded-full border border-amber-200/50 dark:border-amber-900/40 text-[10px] sm:text-xs mt-1.5 font-mono">
                                    <span class="material-symbols-outlined text-[12px] sm:text-[14px] text-amber-500 fill-icon">star</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200"><?= number_format($s['rating_average'] ?? 5.0, 1) ?></span>
                                    <span class="text-slate-400 dark:text-slate-500 text-[9px] sm:text-[10px]">(<?= $compactCount($s['rating_count'] ?? 0) ?>)</span>
                                </div>

                                <!-- Description -->
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed font-normal hidden sm:block">
                                    <?= esc($s['description'] ?? 'Verified Polomolok merchant offering quality merchandise and services.') ?>
                                </p>

                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="p-2.5 sm:p-4 pt-0">
                            <a href="<?= base_url('shop/' . ($s['slug'] ?? $s['id'])) ?>" class="w-full py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] sm:text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 shadow-2xs hover:shadow-sm active:scale-95 cursor-pointer">
                                <span>Visit Store</span>
                                <span class="material-symbols-outlined text-[13px] sm:text-[15px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12 bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-8 shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-outlined text-4xl">store_mall_directory</span>
                    </div>
                    <?php if ($searchQuery !== ''): ?>
                        <p class="text-slate-900 dark:text-white font-bold text-sm">No shops matched &quot;<?= esc($searchQuery) ?>&quot;.</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Try adjusting your search query or removing the category filter to explore more local partners.</p>
                        <a href="<?= base_url('shops') ?>" class="inline-flex items-center gap-1 mt-4 text-xs font-bold text-terra-green hover:underline">
                            <span class="material-symbols-outlined text-[14px]">refresh</span>
                            <span>Reset Filters</span>
                        </a>
                    <?php else: ?>
                        <p class="text-slate-600 dark:text-slate-300 font-medium text-sm">No shops found in this category.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php
            $pageUrl = function (int $p) use ($searchQuery, $category, $sort) {
                $params = ['page' => $p];
                if ($searchQuery !== '') $params['q'] = $searchQuery;
                if ($category !== '') $params['category'] = $category;
                if ($sort !== 'top-rated') $params['sort'] = $sort;
                return base_url('shops?' . http_build_query($params));
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
            <div class="mt-8 flex justify-center">
                <nav class="flex items-center gap-1.5 sm:gap-2" aria-label="Pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= $pageUrl($currentPage - 1) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Previous page">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </a>
                    <?php else: ?>
                        <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-800/30 text-slate-300 dark:text-slate-600 cursor-not-allowed" aria-disabled="true">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </span>
                    <?php endif; ?>

                    <?php foreach ($windowPages as $pg): ?>
                        <?php if ($pg === '...'): ?>
                            <span class="px-2 text-slate-400 text-xs font-semibold">...</span>
                        <?php elseif ((int) $pg === $currentPage): ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" aria-current="page" class="w-9 h-9 flex items-center justify-center rounded-xl bg-terra-green text-white font-bold text-xs shadow-xs font-mono"><?= (int) $pg ?></a>
                        <?php else: ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors font-semibold text-xs font-mono"><?= (int) $pg ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= $pageUrl($currentPage + 1) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Next page">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    <?php else: ?>
                        <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-800/30 text-slate-300 dark:text-slate-600 cursor-not-allowed" aria-disabled="true">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </span>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>

    </section>

</main>

<?= $this->endSection() ?>
