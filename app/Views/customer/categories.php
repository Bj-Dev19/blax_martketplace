<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<?php
    $totalPages  = $totalPages ?? 1;
    $currentPage = $currentPage ?? 1;
    $trending    = $trending ?? [];
?>

<main class="flex-grow w-full max-w-container-max mx-auto px-4 md:px-8 lg:px-10 py-6 sm:py-8 md:py-10 flex flex-col gap-8 sm:gap-10 md:gap-12">

    <!-- Header & Breadcrumbs -->
    <section class="flex flex-col gap-3">
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
                    <span class="text-slate-800 dark:text-slate-200 font-bold ml-1">Categories</span>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-1">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-terra-green/10 dark:bg-terra-green/20 text-terra-green border border-terra-green/20 text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-[14px]">grid_view</span>
                    Marketplace Taxonomy
                </span>
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">Explore Categories</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Browse merchandise, school supplies, custom printing services, and essentials across Polomolok.</p>
            </div>

            <!-- Instant Client-Side Category Filter Bar -->
            <div class="w-full md:w-72 relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input id="category-search-input" 
                       type="text" 
                       placeholder="Filter categories..." 
                       class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green shadow-2xs transition-all">
            </div>
        </div>
    </section>

    <!-- Trending Categories (High-Impact Showcase Cards) -->
    <?php if (!empty($trending)): ?>
        <section class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-terra-amber/15 text-terra-amber-dark dark:text-terra-amber flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px] fill-icon">trending_up</span>
                    </div>
                    <div>
                        <h2 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">Trending Categories</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Most active departments this week in Polomolok</p>
                    </div>
                </div>

                <a href="#all-categories" class="text-xs sm:text-sm font-bold text-terra-green hover:underline flex items-center gap-1">
                    <span>View All</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                </a>
            </div>

            <!-- Trending Cards Grid (3 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                <?php foreach ($trending as $index => $tCat): ?>
                    <?php 
                        $isRank1 = ($index === 0);
                        $isRank2 = ($index === 1);
                        
                        $badgeBg = $isRank1 
                            ? 'bg-terra-amber text-slate-950 font-bold' 
                            : ($isRank2 ? 'bg-terra-green text-white font-bold' : 'bg-terra-earth text-white font-bold');
                        $badgeText = $isRank1 
                            ? '#1 Trending' 
                            : ($isRank2 ? '#2 Trending' : '#3 Trending');
                        $badgeIcon = $isRank1 ? 'local_fire_department' : 'trending_up';
                        $imgSrc = product_image_url($tCat['image_url'] ?? null, 'card');
                    ?>
                    <a href="<?= base_url('category/' . ($tCat['slug'] ?? $tCat['id'])) ?>" 
                       class="bento-card bg-white dark:bg-slate-900 border <?= $isRank1 ? 'border-terra-amber/60 shadow-sm' : 'border-slate-200/90 dark:border-slate-800 shadow-2xs' ?> hover:border-terra-green/50 hover:shadow-md rounded-2xl overflow-hidden flex flex-col justify-between group transition-all duration-300">
                        
                        <!-- Top Image Showcase Banner -->
                        <div class="h-44 sm:h-48 w-full overflow-hidden relative bg-slate-100 dark:bg-slate-800">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                 src="<?= esc($imgSrc) ?>" 
                                 alt="<?= esc($tCat['name']) ?>" 
                                 loading="lazy"
                                 onerror="this.src='https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80';">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>

                            <!-- Rank Pill Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] sm:text-xs uppercase tracking-wider <?= $badgeBg ?> shadow-md">
                                    <span class="material-symbols-outlined text-[13px] fill-icon"><?= $badgeIcon ?></span>
                                    <span><?= $badgeText ?></span>
                                </span>
                            </div>

                            <!-- Item Count Pill -->
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                                <span class="text-xs font-bold font-mono bg-black/60 backdrop-blur-xs px-2.5 py-0.5 rounded-full border border-white/20">
                                    <?= (int) ($tCat['product_count'] ?? 0) ?> items available
                                </span>
                            </div>
                        </div>

                        <!-- Card Info Footer -->
                        <div class="p-4 flex items-center justify-between gap-3 bg-white dark:bg-slate-900">
                            <div class="min-w-0">
                                <h3 class="font-display text-sm sm:text-base font-extrabold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors truncate">
                                    <?= esc($tCat['name']) ?>
                                </h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Explore catalog &amp; deals</p>
                            </div>

                            <span class="w-9 h-9 rounded-xl <?= $isRank1 ? 'bg-terra-amber/15 text-terra-amber-dark dark:text-terra-amber group-hover:bg-terra-amber group-hover:text-slate-950' : 'bg-terra-green/10 text-terra-green group-hover:bg-terra-green group-hover:text-white' ?> transition-colors flex items-center justify-center shrink-0 shadow-2xs">
                                <span class="material-symbols-outlined text-[18px]">arrow_outward</span>
                            </span>
                        </div>

                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- All Categories Grid -->
    <section id="all-categories" class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-terra-green text-xl">category</span>
                <h2 class="font-display text-xl font-extrabold text-slate-900 dark:text-white">All Departments</h2>
            </div>
            <span class="text-xs font-semibold font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full"><?= count($categories ?? []) ?> categories</span>
        </div>

        <div id="all-categories-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <a class="category-card bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col group transition-all duration-300 hover:border-terra-green/40 shadow-2xs" href="<?= base_url('category/' . ($cat['slug'] ?? $cat['id'])) ?>" data-name="<?= esc(strtolower($cat['name'])) ?>">
                        
                        <div class="aspect-[4/3] w-full overflow-hidden relative bg-slate-100 dark:bg-slate-800">
                            <?php if (!empty($cat['image_url'])): ?>
                                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     src="<?= esc(product_image_url($cat['image_url'], 'card')) ?>" 
                                     alt="<?= esc($cat['name']) ?>" 
                                     loading="lazy"
                                     onerror="this.src='https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80';">
                            <?php else: ?>
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-terra-green/60 text-4xl group-hover:scale-110 transition-transform">category</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 bg-slate-950/10 group-hover:bg-transparent transition-colors"></div>
                        </div>

                        <div class="p-3.5 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="font-display text-xs sm:text-sm font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors truncate">
                                    <?= esc($cat['name']) ?>
                                </h3>
                                <span class="text-[11px] text-slate-400 block mt-0.5">Explore Products</span>
                            </div>

                            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 group-hover:bg-terra-green group-hover:text-white transition-colors flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </div>
                        </div>

                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-slate-500 text-sm col-span-full py-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">No categories available.</p>
            <?php endif; ?>
        </div>

        <!-- No search matches feedback element -->
        <div id="no-cat-matches" class="hidden col-span-full text-center py-10 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 mb-1">search_off</span>
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">No categories match your filter.</p>
        </div>

        <!-- Pagination -->
        <?php
            $pageUrl = function (int $p) {
                return base_url('categories?page=' . $p);
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

    </section>

</main>

<script>
// Instant Category Search/Filter on Client Side
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('category-search-input');
    const cards = document.querySelectorAll('.category-card');
    const emptyNotice = document.getElementById('no-cat-matches');

    if (!input || !cards.length) return;

    input.addEventListener('input', function() {
        const query = input.value.trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            if (name.includes(query)) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (emptyNotice) {
            if (visibleCount === 0) {
                emptyNotice.classList.remove('hidden');
            } else {
                emptyNotice.classList.add('hidden');
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
