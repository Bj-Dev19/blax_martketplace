<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<?php
    $totalPages  = $totalPages ?? 1;
    $currentPage = $currentPage ?? 1;
    $totalShops  = (int) ($totalShops ?? 0);
    $searchQuery = $searchQuery ?? '';
    $sort        = $sort ?? 'top-rated';

    $compactCount = function ($n) {
        $n = (int) $n;
        if ($n >= 1000) {
            return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'k';
        }
        return number_format($n);
    };

    $sc = $siteContents ?? [];
    $psTitle = $sc['hero_title']['text_value'] ?? 'Professional Printing, Everywhere You Are.';
    $psSubtitle = $sc['hero_subtitle']['text_value'] ?? 'Connect directly with verified local commercial and custom printing shops in Polomolok for large format tarpaulins, documents, promotional apparel, and marketing merchandise.';
    $rawHeroImg = !empty($sc['hero_image']['image_url']) 
        ? $sc['hero_image']['image_url'] 
        : (!empty($sc['hero_image']['text_value']) ? $sc['hero_image']['text_value'] : null);
    $psImage = cms_image_url($rawHeroImg, 'banner', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=1200&q=80');
?>

<main class="max-w-container-max mx-auto px-4 md:px-8 lg:px-10 flex-grow w-full py-6 sm:py-8 md:py-10 flex flex-col gap-10 sm:gap-14 md:gap-16">

    <!-- Hero Section Bento Stage -->
    <section class="bento-card relative rounded-3xl overflow-hidden bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-lg p-6 sm:p-10 lg:p-12">
        <!-- Ambient Background Gradients -->
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-terra-green/10 dark:bg-terra-green/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-terra-amber/10 dark:bg-terra-amber/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            <!-- Hero Left Column -->
            <div class="lg:col-span-7 flex flex-col gap-4">
                
                <div class="flex items-center">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-terra-green/10 border border-terra-green/20 text-terra-green dark:text-emerald-400 text-xs font-bold tracking-wide uppercase shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-terra-green animate-pulse"></span>
                        Polomolok On-Demand Print Network
                    </span>
                </div>

                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white leading-[1.15] tracking-tight">
                    <?= esc($psTitle) ?>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                    <?= esc($psSubtitle) ?>
                </p>

                <!-- Dual Action CTAs -->
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <a href="<?= base_url('shops?category=printing') ?>" class="inline-flex items-center justify-center gap-2 bg-terra-green hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold px-6 py-3.5 rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">upload_file</span>
                        <span>Start a Printing Request</span>
                    </a>

                    <a href="#master-printers" class="inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 text-xs sm:text-sm font-bold px-6 py-3.5 rounded-xl shadow-2xs hover:shadow-xs hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                        <span class="material-symbols-outlined text-[18px] text-terra-green">storefront</span>
                        <span>Browse Printing Shops</span>
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="flex flex-wrap items-center gap-4 pt-4 text-xs text-slate-600 dark:text-slate-400 font-semibold border-t border-slate-100 dark:border-slate-800 mt-2">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-terra-green text-[18px] fill-icon">verified</span>
                        <span>Verified Commercial Printers</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-amber-500 text-[18px] fill-icon">bolt</span>
                        <span>Rush &amp; Same-Day Services</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-terra-green text-[18px]">local_shipping</span>
                        <span>Townwide Delivery &amp; Pickup</span>
                    </div>
                </div>

            </div>

            <!-- Hero Right Column: Visual Mockup Showcase -->
            <div class="lg:col-span-5 relative">
                <div class="rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-md bg-slate-100 dark:bg-slate-800 aspect-[16/10] sm:aspect-[4/3] lg:aspect-square relative group">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" alt="Professional printing equipment and supplies" src="<?= esc($psImage) ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>

                    <!-- Floating Certified Count Badge (Compact on Mobile, Spacious on Desktop) -->
                    <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 p-3 sm:p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-white/70 dark:border-slate-700 shadow-lg flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-terra-green text-white flex items-center justify-center shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-[20px] sm:text-[24px]">print</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 font-mono">
                                <span class="text-sm sm:text-lg font-extrabold text-slate-900 dark:text-white truncate"><?= max(1, $totalShops) ?>+ Certified Print Shops</span>
                            </div>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium truncate">Ready for custom orders &amp; rush requests</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Print Categories & Formats (Bento Grid) -->
    <section class="flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-end gap-1">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-terra-green dark:text-emerald-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">format_paint</span>
                    Service Directory
                </span>
                <h2 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">Popular Printing Capabilities</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Choose a service category to connect with specialized local equipment</p>
            </div>
            <a href="<?= base_url('shops?category=printing') ?>" class="text-xs sm:text-sm font-bold text-terra-green dark:text-emerald-400 hover:underline flex items-center gap-1 shrink-0">
                <span>View All Printing Shops</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            
            <!-- Category 1: Tarpaulins & Large Format -->
            <a href="<?= base_url('shops?category=printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl p-6 flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-700 dark:text-amber-400 flex items-center justify-center group-hover:bg-terra-green group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">view_carousel</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 px-2.5 py-0.5 rounded-full border border-amber-200/60 dark:border-amber-800">Same-Day Ready</span>
                    </div>
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors">Tarpaulins &amp; Large Format</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Outdoor banners, birthday backdrops, vinyl signage, event standees, and photo canvas prints.</p>
                </div>
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Find Tarpaulin Printers</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Category 2: Documents & Thesis Binding -->
            <a href="<?= base_url('shops?category=printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl p-6 flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center group-hover:bg-terra-green group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">menu_book</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 px-2.5 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800">High Speed Laser</span>
                    </div>
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors">Documents, Booklets &amp; Binding</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Thesis hardbound &amp; softbound, book duplication, laminating, manuals, and school/office paperwork.</p>
                </div>
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Find Document Printers</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Category 3: Stickers & Decals -->
            <a href="<?= base_url('shops?category=printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl p-6 flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-terra-green group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">loyalty</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 px-2.5 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800">Die-Cut &amp; Waterproof</span>
                    </div>
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors">Stickers &amp; Product Labels</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Custom bottle labels, business branding stickers, vinyl laptop decals, and kiss-cut sheet printing.</p>
                </div>
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Find Sticker Cutters</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Category 4: Business Stationery -->
            <a href="<?= base_url('shops?category=printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl p-6 flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-terra-earth/10 text-terra-earth dark:text-amber-400 flex items-center justify-center group-hover:bg-terra-green group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">badge</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 px-2.5 py-0.5 rounded-full border border-amber-200/60 dark:border-amber-800">Commercial Forms</span>
                    </div>
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors">Business Cards &amp; Receipts</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Premium calling cards, official receipts, sales invoices, carbonless forms, and company letterheads.</p>
                </div>
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Find Commercial Printers</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Category 5: Apparel & Merchandise -->
            <a href="<?= base_url('shops?category=printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl p-6 flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:bg-terra-green group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">apparel</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 px-2.5 py-0.5 rounded-full border border-rose-200/60 dark:border-rose-800">DTF &amp; Sublimation</span>
                    </div>
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors">Custom Apparel &amp; Giveaways</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Custom printed t-shirts, tote bags, caps, personalized mugs, keychains, and corporate giveaways.</p>
                </div>
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Find Apparel Printers</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Category 6: Engineering & Blueprint CAD -->
            <a href="<?= base_url('shops?category=printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl p-6 flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:bg-terra-green group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[24px]">architecture</span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-400 px-2.5 py-0.5 rounded-full border border-teal-200/60 dark:border-teal-800">Architectural Plans</span>
                    </div>
                    <h3 class="font-display text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors">Blueprints &amp; Engineering CAD</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">Plotting for architects and engineers: A0, A1, A2 CAD plots, building plans, and schematic prints.</p>
                </div>
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-terra-green dark:text-emerald-400">
                    <span>Find CAD Plotters</span>
                    <span class="material-symbols-outlined text-[16px] transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>

        </div>
    </section>

    <!-- How It Works: Interactive 4-Step Bento Timeline -->
    <section class="rounded-3xl bg-slate-900 dark:bg-slate-950 text-white p-6 sm:p-10 lg:p-12 relative overflow-hidden border border-slate-800 shadow-xl">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-terra-green/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold uppercase tracking-widest text-terra-green bg-terra-green/10 px-3 py-1 rounded-full inline-block mb-2 font-mono border border-terra-green/20">Step-by-Step Workflow</span>
            <h2 class="font-display text-2xl sm:text-3xl font-black text-white">Seamless From File to Finish</h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">Our integrated online printing workflow takes the friction out of custom print orders.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 sm:gap-6 relative z-10">
            
            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 flex flex-col hover:border-terra-green/40 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-terra-green text-white flex items-center justify-center font-bold text-sm mb-3 shadow-xs font-mono">
                    01
                </div>
                <h4 class="font-display text-sm font-bold text-white mb-1">Choose a Verified Shop</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Browse local Polomolok shops based on equipment, location, and verified customer ratings.</p>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 flex flex-col hover:border-terra-green/40 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-terra-green text-white flex items-center justify-center font-bold text-sm mb-3 shadow-xs font-mono">
                    02
                </div>
                <h4 class="font-display text-sm font-bold text-white mb-1">Upload Your Files</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Easily upload PDF, PNG, JPG, or DOCX documents with your paper size, color, and finish preferences.</p>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 flex flex-col hover:border-terra-green/40 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-terra-green text-white flex items-center justify-center font-bold text-sm mb-3 shadow-xs font-mono">
                    03
                </div>
                <h4 class="font-display text-sm font-bold text-white mb-1">Quote &amp; Direct Approval</h4>
                <p class="text-xs text-slate-400 leading-relaxed">The merchant reviews specifications, provides transparent pricing, and prepares your proofs.</p>
            </div>

            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 flex flex-col hover:border-terra-green/40 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm mb-3 shadow-xs font-mono">
                    04
                </div>
                <h4 class="font-display text-sm font-bold text-white mb-1">Pickup or Fast Delivery</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Collect in-store for free, or get your printed goods delivered directly to your doorstep.</p>
            </div>

        </div>
    </section>

    <!-- Master Printers Directory Bento Section -->
    <section id="master-printers" class="flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-end gap-1">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-terra-green dark:text-emerald-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">verified</span>
                    Certified Partners
                </span>
                <h2 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">Master Printers in Polomolok</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Top-rated shops equipped for professional volume and custom finishes</p>
            </div>

            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3.5 py-1.5 rounded-full self-start sm:self-auto font-mono shadow-2xs">
                <?= $totalShops ?> shops active
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4 md:gap-5">
            <?php if (!empty($shops)): ?>
                <?php foreach ($shops as $s): ?>
                    <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-3xl overflow-hidden flex flex-col justify-between group shadow-2xs hover:shadow-xl transition-all duration-300">
                        
                        <!-- Header Banner & Avatar -->
                        <div>
                            <div class="h-16 bg-gradient-to-r from-terra-green/20 via-terra-amber/15 to-emerald-700/20 dark:from-emerald-950/40 dark:via-slate-800 dark:to-slate-900 relative">
                                <span class="absolute top-2.5 right-2.5 inline-flex items-center gap-1 bg-white/95 dark:bg-slate-900/90 text-terra-green dark:text-emerald-400 text-[9px] font-bold px-2 py-0.5 rounded-full shadow-2xs border border-white/20 dark:border-slate-700">
                                    <span class="material-symbols-outlined text-[11px]">print</span>
                                    <span>Print Partner</span>
                                </span>
                            </div>

                            <div class="p-3.5 sm:p-4 pt-0 flex flex-col items-center text-center -mt-8">
                                <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-full bg-white dark:bg-slate-900 shadow-md border-2 border-slate-200/90 dark:border-slate-700 p-0.5 overflow-hidden mb-2.5 group-hover:scale-105 transition-transform relative ring-2 ring-terra-green/10">
                                    <?php $pLogo = logo_url($s['logo_url'] ?? null); ?>
                                    <?php if (!empty($pLogo)): ?>
                                        <img class="w-full h-full object-cover rounded-full" src="<?= esc($pLogo) ?>" alt="<?= esc($s['shop_name']) ?>" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                        <div class="hidden w-full h-full bg-terra-green/10 rounded-full flex items-center justify-center text-terra-green font-bold">
                                            <span class="material-symbols-outlined text-[24px]">print</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-full h-full bg-terra-green/10 rounded-full flex items-center justify-center text-terra-green font-bold">
                                            <span class="material-symbols-outlined text-[24px]">print</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <h3 class="font-display text-xs sm:text-sm font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors line-clamp-1 w-full" title="<?= esc($s['shop_name']) ?>">
                                    <?= esc($s['shop_name']) ?>
                                </h3>

                                <div class="flex items-center justify-center gap-1 text-xs mt-1 font-mono">
                                    <span class="material-symbols-outlined text-[13px] text-amber-500 fill-icon">star</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200"><?= number_format($s['rating_average'] ?? 5.0, 1) ?></span>
                                    <span class="text-slate-400 dark:text-slate-500 text-[10px]">(<?= $compactCount($s['rating_count'] ?? 0) ?>)</span>
                                </div>

                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                                    <?= esc($s['description'] ?? 'Equipped for rush documents, tarpaulins, and custom printing services.') ?>
                                </p>
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="p-3.5 sm:p-4 pt-0">
                            <a href="<?= base_url('shop/' . ($s['slug'] ?? $s['id'])) ?>" class="w-full py-2 bg-terra-green hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1 shadow-2xs hover:shadow-xs active:scale-95">
                                <span>Visit Printer</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-10 bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-sm shadow-xs">
                    No printing service providers available currently.
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php
            $pageUrl = function (int $p) {
                return base_url('printing-services?page=' . $p);
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
                    <a href="<?= $pageUrl($currentPage - 1) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs active:scale-95" aria-label="Previous page">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                <?php else: ?>
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-800/30 text-slate-300 dark:text-slate-600 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </span>
                <?php endif; ?>

                <div class="flex items-center gap-1">
                    <?php foreach ($windowPages as $pg): ?>
                        <?php if ($pg === '...'): ?>
                            <span class="px-2 text-slate-400 text-xs font-semibold">...</span>
                        <?php elseif ((int) $pg === $currentPage): ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" aria-current="page" class="w-9 h-9 flex items-center justify-center rounded-xl bg-terra-green text-white font-bold text-xs shadow-xs font-mono"><?= (int) $pg ?></a>
                        <?php else: ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors font-semibold text-xs font-mono active:scale-95"><?= (int) $pg ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $pageUrl($currentPage + 1) ?>" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs active:scale-95" aria-label="Next page">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                <?php else: ?>
                    <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-100 dark:border-slate-800/60 bg-slate-50 dark:bg-slate-800/30 text-slate-300 dark:text-slate-600 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </section>

</main>

<?= $this->endSection() ?>
