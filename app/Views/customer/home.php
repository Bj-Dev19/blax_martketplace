<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<main class="flex-1 w-full max-w-container-max mx-auto px-4 md:px-8 lg:px-10 py-4 sm:py-8 md:py-10 flex flex-col gap-8 sm:gap-10 md:gap-12">

    <!-- Mobile Quick Search Bar -->
    <div class="block md:hidden w-full">
        <form action="<?= base_url('search') ?>" method="GET" class="flex items-center relative w-full shadow-2xs">
            <span class="material-symbols-outlined absolute left-3.5 text-terra-green text-[20px]">search</span>
            <input name="q" value="<?= esc($searchQuery ?? '') ?>" maxlength="200" autocomplete="off" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl pl-10 pr-4 py-3 w-full text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green shadow-2xs" placeholder="Search merchandise, rush prints in Polomolok..." type="text">
        </form>
    </div>

    <!-- Hero Section — CMS Configurable with Dual-Intent Focus -->
    <?php
        $sort = $sort ?? null;
        $rotationInfo = $rotationInfo ?? \App\Models\ProductModel::getRotationInfo(3);
        $sc = $siteContents ?? [];
        $heroBadge = $sc['hero_badge']['text_value'] ?? 'Polomolok Premier Hub';
        $heroTitle = $sc['hero_title']['text_value'] ?? 'The Ultimate Merchandise & Printing Hub';
        $heroSubtitle = $sc['hero_subtitle']['text_value'] ?? 'Discover premium goods, exclusive deals, and top-tier printing services all in one place across Polomolok.';
        $rawHeroImg = !empty($sc['hero_image']['image_url']) 
            ? $sc['hero_image']['image_url'] 
            : (!empty($sc['hero_image']['text_value']) ? $sc['hero_image']['text_value'] : null);
        $heroImage = cms_image_url($rawHeroImg, 'https://images.unsplash.com/photo-1556742049-0a67daf64f42?auto=format&fit=crop&w=1440&q=80');
        $catalogTitle = $sc['catalog_title']['text_value'] ?? 'Global Product Catalog';
        $catalogSubtitle = $sc['catalog_subtitle']['text_value'] ?? 'Explore top-rated products from verified local merchants across Polomolok.';
    ?>

    <!-- Bento Grid Hero Section — Calm & Balanced Responsive Stage -->
    <section class="w-full grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-5 items-stretch">

        <!-- Bento Cell 1: Primary Value Proposition Showcase (8 Columns on Desktop) -->
        <div class="lg:col-span-8 bento-card relative overflow-hidden rounded-3xl p-6 sm:p-8 lg:p-10 flex flex-col justify-between group min-h-[290px] sm:min-h-[360px] bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <!-- Ambient Backdrop with Mobile-Safe Scrim -->
            <div class="absolute inset-0 bg-cover bg-center opacity-90 scale-105 group-hover:scale-110 transition-transform duration-700 pointer-events-none" style="background-image: url('<?= esc($heroImage) ?>');"></div>
            <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-white/95 via-white/90 to-white/75 sm:bg-none dark:from-slate-900/95 dark:via-slate-900/90 dark:to-slate-900/75"></div>
            <div class="absolute inset-0 pointer-events-none hidden sm:block dark:hidden" style="background: linear-gradient(90deg, rgba(255, 255, 255, 0.97) 0%, rgba(255, 255, 255, 0.92) 45%, rgba(255, 255, 255, 0.55) 75%, rgba(255, 255, 255, 0.10) 100%);"></div>
            <div class="absolute inset-0 pointer-events-none hidden sm:dark:block" style="background: linear-gradient(90deg, rgba(15, 23, 42, 0.97) 0%, rgba(15, 23, 42, 0.92) 45%, rgba(15, 23, 42, 0.55) 75%, rgba(15, 23, 42, 0.15) 100%);"></div>

            <div class="relative z-10 space-y-3.5 max-w-xl">
                <div class="flex items-center">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-terra-green/10 dark:bg-terra-green/20 text-terra-green dark:text-emerald-400 border border-terra-green/25 text-[11px] font-bold tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-terra-green animate-pulse"></span>
                        <?= esc($heroBadge) ?>
                    </span>
                </div>

                <h1 class="font-display text-2xl sm:text-4xl lg:text-[2.65rem] font-extrabold text-slate-900 dark:text-white leading-[1.14] tracking-tight">
                    <?= esc($heroTitle) ?>
                </h1>

                <p class="text-xs sm:text-sm lg:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-lg">
                    <?= esc($heroSubtitle) ?>
                </p>
            </div>

            <!-- Action CTAs & Calm Trust Pill -->
            <div class="relative z-10 pt-5 mt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 border-t border-slate-200/60 dark:border-slate-800/80">
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="#catalog" class="inline-flex items-center justify-center gap-2 bg-terra-green hover:bg-terra-green-dark text-white text-xs sm:text-sm font-bold px-5 py-2.5 min-h-[42px] rounded-xl shadow-xs hover:shadow-md active:scale-[0.98] transition-all">
                        <span><?= esc($sc['hero_cta_text']['text_value'] ?? 'Explore Marketplace') ?></span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                    <a href="<?= base_url('printing-services') ?>" class="inline-flex items-center justify-center gap-2 bg-white/95 dark:bg-slate-800/95 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 text-xs sm:text-sm font-bold px-5 py-2.5 min-h-[42px] rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 shadow-2xs active:scale-[0.98] transition-all">
                        <span class="material-symbols-outlined text-[18px] text-terra-green">print</span>
                        <span>Custom Printing</span>
                    </a>
                </div>

                <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-300 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md px-3 py-1.5 rounded-full border border-slate-200/70 dark:border-slate-700/70 w-fit">
                    <span class="material-symbols-outlined text-[15px] text-terra-green fill-icon">verified</span>
                    <span>Polomolok Delivery &amp; Pick-up</span>
                </div>
            </div>
        </div>

        <!-- Right Column Stack: Express Printing + Direct Storefronts (Side-by-side on tablet, stacked on desktop) -->
        <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3.5 sm:gap-4">

            <!-- Bento Cell 2: Express Rush Printing Hub -->
            <a href="<?= base_url('printing-services') ?>" class="bento-card rounded-3xl p-5 sm:p-6 flex flex-col justify-between group hover:border-terra-green/50 cursor-pointer bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-md transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">print</span>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 font-mono">
                            2–4h Rush
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors font-display">
                        Express Printing
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed line-clamp-2">
                        Documents, tarpaulins, stickers, and custom apparel from verified local print shops.
                    </p>
                </div>

                <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Start Print Order</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Bento Cell 3: Verified Merchants Spotlight -->
            <a href="<?= base_url('shops') ?>" class="bento-card rounded-3xl p-5 sm:p-6 flex flex-col justify-between group hover:border-terra-green/50 cursor-pointer bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-md transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">storefront</span>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 font-mono">
                            Verified Shops
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors font-display">
                        Local Storefronts
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed line-clamp-2">
                        Shop directly from neighborhood merchants and commercial suppliers in Polomolok.
                    </p>
                </div>

                <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Browse Local Shops</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

        </div>

    </section>

    <!-- Quick Category Pill Rail -->
    <?php if (!empty($categories)): ?>
        <section class="w-full max-w-full overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-terra-green">category</span>
                    Browse by Department
                </span>
                <a href="<?= base_url('categories') ?>" class="text-xs font-semibold text-terra-green hover:underline flex items-center gap-0.5">
                    <span>All Categories</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

            <div class="w-full max-w-full flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
                <?php
                    $allParams = [];
                    if (!empty($sort) && $sort !== 'discovery') $allParams['sort'] = $sort;
                    $allUrl = base_url(!empty($allParams) ? '?' . http_build_query($allParams) : '/') . '#catalog';
                ?>
                <a href="<?= $allUrl ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 <?= empty($categoryId) ? 'bg-terra-green text-white shadow-xs' : 'bg-white dark:bg-slate-850 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-800 shadow-2xs' ?>">
                    <span class="material-symbols-outlined text-[15px]">auto_awesome</span>
                    <span>All Items</span>
                </a>

                <?php foreach ($categories as $cat): ?>
                    <?php 
                        $isCatActive = ((int) ($categoryId ?? 0) === (int) $cat['id']);
                        $catParams = ['category_id' => $cat['id']];
                        if (!empty($sort) && $sort !== 'discovery') $catParams['sort'] = $sort;
                    ?>
                    <a href="<?= base_url('?' . http_build_query($catParams)) ?>#catalog" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 <?= $isCatActive ? 'bg-terra-green text-white shadow-xs' : 'bg-white dark:bg-slate-850 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-800 shadow-2xs' ?>">
                        <span><?= esc($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>

                <a href="<?= base_url('printing-services') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 bg-terra-green/10 text-terra-green border border-terra-green/30 hover:bg-terra-green hover:text-white shadow-2xs">
                    <span class="material-symbols-outlined text-[15px]">print</span>
                    <span>Printing Services</span>
                </a>
            </div>
        </section>
    <?php endif; ?>

    <!-- Search Results: Matching Shops (if searching) -->
    <?php if (($searchQuery ?? '') !== ''): ?>
        <section class="flex flex-col gap-4 w-full max-w-full overflow-hidden">
            <div class="flex justify-between items-center">
                <h2 class="font-display font-extrabold text-xl sm:text-2xl text-slate-900 dark:text-white">Shops matching &quot;<?= esc($searchQuery) ?>&quot;</h2>
                <a class="text-terra-green font-bold text-xs sm:text-sm hover:underline flex items-center gap-1" href="<?= base_url('shops?q=' . urlencode($searchQuery)) ?>">
                    <span>View All Shops</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <?php if (!empty($shopResults)): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                    <?php foreach ($shopResults as $s): ?>
                        <a href="<?= base_url('shop/' . ($s['slug'] ?? $s['id'])) ?>" class="bento-card rounded-2xl p-4 flex flex-col items-center text-center group cursor-pointer bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:border-terra-green/40 transition-all">
                            <div class="w-16 h-16 rounded-full border-2 border-terra-green/20 overflow-hidden mb-2 bg-slate-100 dark:bg-slate-800 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <?php if (!empty($s['logo_url'])): ?>
                                    <img class="w-full h-full object-cover" src="<?= esc(logo_url($s['logo_url'])) ?>" alt="<?= esc($s['shop_name']) ?>" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-terra-green text-3xl">storefront</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors line-clamp-1 w-full"><?= esc($s['shop_name']) ?></span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Verified Shop</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-slate-500 text-xs">No shops matched &quot;<?= esc($searchQuery) ?>&quot;.</p>
            <?php endif; ?>
        </section>
    <?php else: ?>

    <!-- Featured Shops - Rich Merchant Cards -->
    <section class="flex flex-col gap-4 w-full max-w-full overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-1">
            <div>
                <h2 class="font-display font-extrabold text-xl sm:text-2xl text-slate-900 dark:text-white">Featured Local Shops</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Top-rated local merchants and certified printing shops across Polomolok</p>
            </div>
            <a class="text-terra-green text-xs sm:text-sm font-bold hover:underline inline-flex items-center gap-1 shrink-0" href="<?= base_url('shops') ?>">
                <span>View All Shops</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="w-full max-w-full flex overflow-x-auto no-scrollbar snap-x snap-mandatory gap-4 sm:gap-5 pb-3 -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
            <?php if (!empty($shops)): ?>
                <?php
                    $curatedCovers = [
                        'the-thread'          => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=700&q=80',
                        'inkmaster'           => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=700&q=80',
                        'rhk-general'         => 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=700&q=80',
                        'national-book-store' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=700&q=80',
                        'fastprint'           => 'https://images.unsplash.com/photo-1586075010923-2dd4570fb338?auto=format&fit=crop&w=700&q=80',
                    ];
                    $curatedCategories = [
                        'the-thread'          => 'Custom Embroidery & Apparel',
                        'inkmaster'           => 'Rush Printing & Photocopying',
                        'rhk-general'         => 'School & Office Merchandise',
                        'national-book-store' => 'Books, Art & School Supplies',
                        'fastprint'           => 'Digital Printing & Document Press',
                    ];
                ?>
                <?php foreach ($shops as $s): ?>
                    <?php
                        $slug = $s['slug'] ?? '';
                        $coverUrl = !empty($s['cover_photo_url'])
                            ? cover_photo_url($s['cover_photo_url'], 'card')
                            : ($curatedCovers[$slug] 
                                ?? (!empty($s['offers_printing'])
                                    ? 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=700&q=80'
                                    : 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=700&q=80'));
                        $categoryName = $curatedCategories[$slug]
                            ?? (!empty($s['offers_printing']) ? 'Printing & Digital Services' : 'Retail General Merchandise');
                        $location = !empty($s['barangay']) ? $s['barangay'] . ', ' . ($s['city'] ?? 'Polomolok') : ($s['city'] ?? 'Polomolok');
                        $sLogo = logo_url($s['logo_url'] ?? null);
                    ?>
                    <a href="<?= base_url('shop/' . ($s['slug'] ?? $s['id'])) ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:-translate-y-1 hover:border-terra-green/50 rounded-xl sm:rounded-2xl overflow-hidden flex flex-col justify-between group transition-all duration-300 w-[155px] sm:w-[190px] md:w-auto shrink-0 snap-start">
                        
                        <div>
                            <!-- Real-World Storefront Cover Image -->
                            <div class="h-24 sm:h-28 md:h-32 w-full overflow-hidden relative bg-slate-100 dark:bg-slate-800">
                                <img src="<?= esc($coverUrl) ?>" alt="<?= esc($s['shop_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-black/25 pointer-events-none"></div>

                                <!-- Floating Status Badges -->
                                <div class="absolute top-2 left-2 right-2 flex items-center justify-between pointer-events-none">
                                    <?php if (!empty($s['is_closed_today'])): ?>
                                        <span class="inline-flex items-center gap-1 bg-rose-600/95 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            <span>Closed Today</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md text-slate-800 dark:text-slate-100 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-2xs border border-white/40 dark:border-slate-700/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-terra-green animate-pulse"></span>
                                            <span>Verified</span>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!empty($s['offers_printing'])): ?>
                                        <span class="inline-flex items-center gap-0.5 bg-black/60 backdrop-blur-md text-emerald-300 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-2xs border border-emerald-400/30">
                                            <span class="material-symbols-outlined text-[10px]">print</span>
                                            <span>Print</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-0.5 bg-black/60 backdrop-blur-md text-amber-200 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-2xs border border-amber-300/30">
                                            <span class="material-symbols-outlined text-[10px]">storefront</span>
                                            <span>Retail</span>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Brand Logo / Avatar Inset -->
                                <div class="absolute bottom-2 left-2">
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg p-0.5 bg-white dark:bg-slate-900 shadow-md ring-2 ring-white/90 dark:ring-slate-900 overflow-hidden shrink-0">
                                        <?php if (!empty($sLogo)): ?>
                                            <img src="<?= esc($sLogo) ?>" alt="<?= esc($s['shop_name']) ?>" class="w-full h-full object-cover rounded-md" loading="lazy" decoding="async" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                            <div class="hidden w-full h-full bg-emerald-600 rounded-md flex items-center justify-center text-white font-extrabold text-[10px] font-mono">
                                                <?= esc(substr($s['shop_name'], 0, 2)) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-full h-full bg-emerald-700 dark:bg-emerald-800 rounded-md flex items-center justify-center text-white font-extrabold text-[10px] font-mono tracking-wider shadow-inner">
                                                <?= esc(strtoupper(substr($s['shop_name'], 0, 2))) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body Information -->
                            <div class="p-2.5 sm:p-3.5 flex flex-col gap-0.5">
                                <div class="flex items-center justify-between gap-1">
                                    <h3 class="font-display text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors truncate tracking-tight" title="<?= esc($s['shop_name']) ?>">
                                        <?= esc($s['shop_name']) ?>
                                    </h3>
                                    <span class="material-symbols-outlined text-[14px] text-terra-green shrink-0 fill-icon" title="Certified Polomolok Merchant">verified</span>
                                </div>

                                <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                                    <?= esc($categoryName) ?>
                                </p>
                                <?php if (!empty($s['is_closed_today']) && !empty($s['closure_note'])): ?>
                                    <p class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold truncate" title="<?= esc($s['closure_note']) ?>">
                                        Note: <?= esc($s['closure_note']) ?>
                                    </p>
                                <?php endif; ?>

                                <div class="flex items-center justify-between gap-1 pt-1.5 mt-1 border-t border-slate-100 dark:border-slate-800/80 text-[10px] sm:text-[11px]">
                                    <div class="flex items-center gap-0.5 font-mono font-bold text-slate-800 dark:text-slate-200 shrink-0">
                                        <span class="material-symbols-outlined text-[12px] text-amber-500 fill-icon">star</span>
                                        <span><?= number_format($s['rating_average'] ?? 5.0, 1) ?></span>
                                        <span class="text-slate-400 dark:text-slate-500 text-[9px] font-normal">(<?= number_format($s['rating_count'] ?? 0) ?>)</span>
                                    </div>
                                    <div class="flex items-center gap-0.5 truncate text-slate-500 dark:text-slate-400 text-[10px]" title="<?= esc($location) ?>">
                                        <span class="material-symbols-outlined text-[11px] text-terra-green shrink-0">location_on</span>
                                        <span class="truncate"><?= esc(explode(',', $location)[0]) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-500 text-sm">
                    No shops available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php endif; ?>

    <!-- Global Product Catalog Section -->
    <section id="catalog" class="w-full pt-4">

        <div class="flex flex-col sm:flex-row justify-between sm:items-end items-start mb-6 gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2.5 mb-1.5">
                    <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 dark:text-white"><?= esc($catalogTitle) ?></h2>
                    
                    <!-- Dynamic 3-Hour Discovery Rotation Badge -->
                    <?php if (empty($sort) || $sort === 'discovery'): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-terra-green/10 text-terra-green border border-terra-green/30 shadow-2xs" title="Discovery Rotation • Rotates every 3h across all shops">
                            <span class="w-2 h-2 rounded-full bg-terra-green animate-pulse"></span>
                            <span>Discovery Rotation &bull; Rotates every 3h &bull; <span id="catalog-rotation-timer" class="font-bold font-mono" data-seconds="<?= (int) ($rotationInfo['seconds_remaining'] ?? 0) ?>"><?= esc($rotationInfo['formatted_time_left'] ?? '3h') ?> left</span></span>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[14px] text-terra-green">sort</span>
                            <span>Sorted by <?= esc(ucwords(str_replace('_', ' ', $sort))) ?></span>
                            <a href="<?= base_url(empty($categoryId) ? '/' : '?category_id=' . $categoryId) ?>#catalog" class="ml-1 text-terra-green font-bold hover:underline" title="Switch back to 3-hour discovery rotation">&times; Reset</a>
                        </span>
                    <?php endif; ?>
                </div>

                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400"><?= esc($catalogSubtitle) ?></p>

                <?php if (($searchQuery ?? '') !== ''): ?>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <?php if (!empty($semantic)): ?>
                            <span class="inline-flex items-center gap-1 bg-terra-green/15 text-terra-green text-xs font-semibold px-2.5 py-0.5 rounded-full border border-terra-green/30">
                                <span class="material-symbols-outlined text-xs">auto_awesome</span>
                                AI matched
                            </span>
                        <?php endif; ?>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            <?= (int) ($totalProducts ?? 0) ?> result<?= ((int) ($totalProducts ?? 0)) === 1 ? '' : 's' ?> for &quot;<?= esc($searchQuery) ?>&quot;
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Filter & Sort Bar -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="<?= base_url('categories') ?>" class="flex items-center gap-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[16px] text-slate-500">filter_list</span>
                    <span>Categories</span>
                </a>

                <!-- Sort By Dropdown -->
                <div class="relative" id="sort-dropdown-container">
                    <button type="button" id="sort-menu-btn" onclick="toggleSortMenu(event)" class="flex items-center gap-1.5 bg-white dark:bg-slate-900 border <?= (!empty($sort) && $sort !== 'discovery') ? 'border-terra-green text-terra-green font-bold' : 'border-slate-200/90 dark:border-slate-800 text-slate-700 dark:text-slate-200 font-semibold' ?> rounded-xl px-3.5 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">swap_vert</span>
                        <span>
                            <?php
                                $sortLabels = [
                                    'discovery'  => 'Discovery (3h)',
                                    'rating'     => 'Top Rated',
                                    'price_asc'  => 'Price: Low-High',
                                    'price_desc' => 'Price: High-Low',
                                    'newest'     => 'Newest',
                                ];
                                echo esc($sortLabels[$sort ?? 'discovery'] ?? 'Sort By');
                            ?>
                        </span>
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                    </button>

                    <div id="sort-menu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/90 dark:border-slate-800 py-2 z-40">
                        <?php
                            $buildSortLink = function($s) use ($categoryId, $searchQuery) {
                                $p = [];
                                if ($s !== 'discovery') $p['sort'] = $s;
                                if (!empty($categoryId)) $p['category_id'] = $categoryId;
                                if ($searchQuery !== '') $p['q'] = $searchQuery;
                                return base_url(!empty($p) ? '?' . http_build_query($p) : '/') . '#catalog';
                            };
                        ?>
                        <a href="<?= $buildSortLink('discovery') ?>" class="flex items-center gap-2 px-3.5 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors <?= (empty($sort) || $sort === 'discovery') ? 'text-terra-green font-bold bg-terra-green/10' : 'text-slate-700 dark:text-slate-300' ?>">
                            <span class="material-symbols-outlined text-[16px] text-terra-green">autorenew</span>
                            <div>
                                <div>Discovery Rotation</div>
                                <div class="text-[10px] text-slate-400 font-normal">Rotates every 3 hours</div>
                            </div>
                        </a>
                        <a href="<?= $buildSortLink('rating') ?>" class="flex items-center gap-2 px-3.5 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors <?= $sort === 'rating' ? 'text-terra-green font-bold bg-terra-green/10' : 'text-slate-700 dark:text-slate-300' ?>">
                            <span class="material-symbols-outlined text-[16px] text-terra-amber fill-icon">star</span>
                            <span>Top Rated First</span>
                        </a>
                        <a href="<?= $buildSortLink('price_asc') ?>" class="flex items-center gap-2 px-3.5 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors <?= $sort === 'price_asc' ? 'text-terra-green font-bold bg-terra-green/10' : 'text-slate-700 dark:text-slate-300' ?>">
                            <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                            <span>Price: Low to High</span>
                        </a>
                        <a href="<?= $buildSortLink('price_desc') ?>" class="flex items-center gap-2 px-3.5 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors <?= $sort === 'price_desc' ? 'text-terra-green font-bold bg-terra-green/10' : 'text-slate-700 dark:text-slate-300' ?>">
                            <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                            <span>Price: High to Low</span>
                        </a>
                        <a href="<?= $buildSortLink('newest') ?>" class="flex items-center gap-2 px-3.5 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors <?= $sort === 'newest' ? 'text-terra-green font-bold bg-terra-green/10' : 'text-slate-700 dark:text-slate-300' ?>">
                            <span class="material-symbols-outlined text-[16px]">new_releases</span>
                            <span>Newest Arrivals</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid: 2 cols on mobile, 3 on tablet, 4-5 on desktop -->
        <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-5">

            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <?php
                        $imageUrl = product_image_url($p['image_url'] ?? null);
                        $rating   = $p['rating_average'] ?? 0;
                    ?>
                    <div class="bento-card rounded-2xl overflow-hidden flex flex-col group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-lg hover:-translate-y-1 hover:border-terra-green/40 transition-all duration-300">
                        
                        <!-- Image Container with Hover Zoom & Warm Neutral Stage -->
                        <div class="relative aspect-square bg-[#faf7f2] dark:bg-slate-800/40 overflow-hidden flex items-center justify-center p-3">
                            <a href="<?= base_url('product/' . $p['id']) ?>" class="block w-full h-full flex items-center justify-center">
                                <img src="<?= esc($imageUrl) ?>" alt="<?= esc($p['name']) ?>" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80';">
                            </a>
                            
                            <!-- Merchant Tag Badge -->
                            <span class="absolute top-2.5 left-2.5 bg-white/95 dark:bg-slate-900/90 backdrop-blur-md text-slate-800 dark:text-slate-200 text-[9.5px] font-bold px-2.5 py-0.5 rounded-full shadow-2xs truncate max-w-[125px] z-10 border border-slate-200/60 dark:border-slate-700">
                                <?= esc($p['shop_name'] ?? 'Blax Store') ?>
                            </span>

                            <button type="button" class="absolute top-2.5 right-2.5 bg-white/95 dark:bg-slate-900/90 backdrop-blur-md hover:bg-white text-slate-400 hover:text-rose-500 p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-all shadow-xs z-10 cursor-pointer" aria-label="Add to favorites">
                                <span class="material-symbols-outlined text-[15px]">favorite</span>
                            </button>
                        </div>

                        <!-- Card Details -->
                        <div class="p-3.5 sm:p-4 flex flex-col flex-grow">
                            
                            <h4 class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white group-hover:text-terra-green transition-colors line-clamp-2 leading-snug mb-1.5 min-h-[2.5rem]">
                                <a href="<?= base_url('product/' . $p['id']) ?>"><?= esc($p['name']) ?></a>
                            </h4>

                            <!-- Rating or New Arrival Badge -->
                            <?php if ($rating > 0): ?>
                                <div class="flex items-center gap-1 mb-2.5 text-[11px]">
                                    <span class="material-symbols-outlined text-amber-500 text-[13px] fill-icon">star</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono"><?= number_format((float) $rating, 1) ?></span>
                                </div>
                            <?php else: ?>
                                <div class="inline-flex items-center gap-1 mb-2.5 text-[9.5px] text-emerald-700 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full border border-emerald-200/50 dark:border-emerald-900/40 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Verified Item</span>
                                </div>
                            <?php endif; ?>

                            <!-- Footer Price & Add To Cart -->
                            <div class="mt-auto flex justify-between items-center pt-2.5 border-t border-slate-100 dark:border-slate-800/80">
                                <div class="font-mono">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">₱</span>
                                    <span class="text-sm sm:text-base font-black text-slate-900 dark:text-white"><?= number_format($p['price'], 2) ?></span>
                                </div>

                                <?php if ((int) ($p['variant_count'] ?? 0) > 0): ?>
                                    <a href="<?= base_url('product/' . $p['id'] . '?select_option=1#variants-section') ?>" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-xs hover:shadow-md active:scale-95 transition-all cursor-pointer" aria-label="Select product option" title="Select product option">
                                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                    </a>
                                <?php else: ?>
                                    <form action="<?= base_url('cart/add') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">

                                        <button type="submit" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-xs hover:shadow-md active:scale-95 transition-all cursor-pointer" aria-label="Add to cart" title="Add to cart">
                                            <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-2">inventory_2</span>
                    <?php if (($searchQuery ?? '') !== ''): ?>
                        <p class="text-slate-800 dark:text-slate-200 font-bold text-sm">No products matched &quot;<?= esc($searchQuery) ?>&quot;.</p>
                        <p class="text-xs text-slate-500 mt-1">Try searching with other terms or in Bisaya/Tagalog (e.g. &quot;pang sulat nga blue&quot; or &quot;gamit pang drawing&quot;).</p>
                        <a href="<?= base_url('/') ?>" class="inline-block mt-4 text-xs font-bold text-terra-green hover:underline">Clear Search Filter</a>
                    <?php else: ?>
                        <p class="text-slate-600 dark:text-slate-400 font-medium text-sm">No products currently available in this category.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Pagination Controls -->
        <?php
            $totalPages   = $totalPages ?? 1;
            $currentPage  = $currentPage ?? 1;
            $searchQuery  = $searchQuery ?? '';
            $categoryId   = $categoryId ?? null;
            $sortParam    = (!empty($sort) && $sort !== 'discovery') ? $sort : null;
            $pageUrl = function (int $p) use ($searchQuery, $categoryId, $sortParam) {
                $params = ['page' => $p];
                if ($searchQuery !== '') $params['q'] = $searchQuery;
                if (!empty($categoryId)) $params['category_id'] = $categoryId;
                if (!empty($sortParam)) $params['sort'] = $sortParam;
                return base_url('?' . http_build_query($params)) . '#catalog';
            };

            $windowPages = [];
            if ($totalPages <= 7) {
                $windowPages = range(1, $totalPages);
            } else {
                $windowPages = [1];
                $left = max(2, $currentPage - 2);
                $right = min($totalPages - 1, $currentPage + 2);
                if ($left > 2) $windowPages[] = '...';
                for ($i = $left; $i <= $right; $i++) $windowPages[] = $i;
                if ($right < $totalPages - 1) $windowPages[] = '...';
                $windowPages[] = $totalPages;
            }
        ?>

        <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex flex-wrap justify-center items-center gap-1.5 sm:gap-2 px-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= $pageUrl($currentPage - 1) ?>" class="w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Previous page">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                <?php else: ?>
                    <span class="w-9 h-9 rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-900/40 flex items-center justify-center text-slate-300 dark:text-slate-600 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </span>
                <?php endif; ?>

                <div class="flex flex-wrap items-center gap-1">
                    <?php foreach ($windowPages as $pg): ?>
                        <?php if ($pg === '...'): ?>
                            <span class="px-2 text-slate-400 text-xs font-semibold">...</span>
                        <?php elseif ((int) $pg === $currentPage): ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" aria-current="page" class="w-9 h-9 rounded-xl bg-terra-green text-white flex items-center justify-center font-bold text-xs shadow-xs font-mono"><?= (int) $pg ?></a>
                        <?php else: ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" class="w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors font-semibold text-xs font-mono"><?= (int) $pg ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $pageUrl($currentPage + 1) ?>" class="w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Next page">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                <?php else: ?>
                    <span class="w-9 h-9 rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-900/40 flex items-center justify-center text-slate-300 dark:text-slate-600 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </section>

    <!-- Local Community Trust Grid (4 Pillars) -->
    <section class="mt-4 pt-10 border-t border-slate-200/80 dark:border-slate-800">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bento-card rounded-2xl p-5 flex items-start gap-4 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                <div class="w-12 h-12 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Polomolok Verified</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Direct support for vetted neighborhood entrepreneurs and certified printers.</p>
                </div>
            </div>

            <div class="bento-card rounded-2xl p-5 flex items-start gap-4 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                <div class="w-12 h-12 rounded-xl bg-terra-amber/15 text-terra-amber-dark dark:text-terra-amber flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">print</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Rush Printing Ready</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Upload documents or merch files online with instant quote transparency.</p>
                </div>
            </div>

            <div class="bento-card rounded-2xl p-5 flex items-start gap-4 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                <div class="w-12 h-12 rounded-xl bg-terra-earth/10 text-terra-earth flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Pickup or Fast Delivery</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Convenient in-store local pickup or direct delivery right to your door.</p>
                </div>
            </div>

            <div class="bento-card rounded-2xl p-5 flex items-start gap-4 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">shield</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Buyer Guarantee</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Every purchase backed by verified merchant accountability & customer service.</p>
                </div>
            </div>

        </div>
    </section>

</main>

<script>
function toggleSortMenu(e) {
    e.stopPropagation();
    const menu = document.getElementById('sort-menu');
    if (menu) menu.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const container = document.getElementById('sort-dropdown-container');
    const menu = document.getElementById('sort-menu');
    if (container && menu && !container.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// Live 3-Hour Dynamic Rotation Countdown
document.addEventListener('DOMContentLoaded', function() {
    const timerEl = document.getElementById('catalog-rotation-timer');
    if (!timerEl) return;

    let seconds = parseInt(timerEl.getAttribute('data-seconds'), 10) || 0;

    function formatTime(s) {
        if (s <= 0) return '0s';
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;
        if (h > 0) return `${h}h ${m}m`;
        if (m > 0) return `${m}m ${sec}s`;
        return `${sec}s`;
    }

    if (seconds > 0) {
        setInterval(function() {
            if (seconds > 0) {
                seconds--;
                timerEl.textContent = formatTime(seconds) + ' left';
            } else {
                timerEl.textContent = 'Refreshed!';
            }
        }, 1000);
    }
});
</script>

<?= $this->endSection() ?>