<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<div class="flex flex-1 flex-col md:flex-row w-full min-h-[calc(100vh-72px)] bg-slate-50/50 dark:bg-slate-950">

    <?= view('components/profile_sidebar', ['activeNav' => 'favorites']) ?>

    <!-- Main Content Area -->
    <main class="flex-1 w-full min-w-0 p-4 sm:p-6 md:p-8">

        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 pb-28">

            <!-- Calm Page Header -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-display font-bold text-slate-900 dark:text-white tracking-tight">Favorite Shops</h1>
                    <?php if (!empty($shops)): ?>
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            <?= count($shops) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <a href="<?= base_url('shops') ?>" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>Explore Shops</span>
                </a>
            </div>

            <?php if (!empty($shops)): ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <?php foreach ($shops as $fav): ?>

                        <?php
                            $shopSlug    = $fav['slug'] ?? $fav['shop_id'];
                            $shopUrl     = base_url('shop/' . $shopSlug);
                            $shopRating  = number_format((float) ($fav['rating_average'] ?? 5.0), 1);
                            $shopDesc    = $fav['description'] ?? 'Verified local merchant in Polomolok.';
                        ?>

                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 rounded-2xl p-5 shadow-2xs transition-all flex flex-col justify-between gap-4 group">

                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <a href="<?= esc($shopUrl) ?>" class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 flex items-center justify-center shrink-0">
                                            <?php if (!empty($fav['logo_url'])): ?>
                                                <img class="w-full h-full object-cover" src="<?= esc(logo_url($fav['logo_url'])) ?>" alt="<?= esc($fav['shop_name']) ?> logo">
                                            <?php else: ?>
                                                <span class="material-symbols-outlined text-2xl text-terra-green dark:text-emerald-400">store</span>
                                            <?php endif; ?>
                                        </a>

                                        <div class="min-w-0">
                                            <h3 class="font-display text-sm sm:text-base font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors truncate">
                                                <a href="<?= esc($shopUrl) ?>"><?= esc($fav['shop_name']) ?></a>
                                            </h3>
                                            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                <span class="inline-flex items-center gap-0.5 font-mono font-bold text-slate-700 dark:text-slate-200">
                                                    <span class="material-symbols-outlined text-[14px] text-amber-500 fill-icon">star</span>
                                                    <?= esc($shopRating) ?>
                                                </span>
                                                <span>&bull;</span>
                                                <span class="truncate">Verified Shop</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" class="fav-share w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" data-url="<?= esc($shopUrl) ?>" title="Share shop" aria-label="Share shop">
                                            <span class="material-symbols-outlined text-[17px]">share</span>
                                        </button>

                                        <form method="post" action="<?= base_url('customer/favorites/remove') ?>" onsubmit="return confirm('Remove this shop from your favorites?');" class="inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="shop_id" value="<?= esc($fav['shop_id'] ?? $fav['id']) ?>">
                                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer" title="Remove from favorites" aria-label="Remove from favorites">
                                                <span class="material-symbols-outlined text-[18px] fill-icon">favorite</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 leading-relaxed">
                                    <?= esc($shopDesc) ?>
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-end">
                                <a href="<?= esc($shopUrl) ?>" class="w-full py-2 px-4 bg-terra-green/10 hover:bg-terra-green text-terra-green dark:text-emerald-400 hover:text-white rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5">
                                    <span>Visit Store</span>
                                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                </a>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <!-- Calm Empty State -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 sm:p-10 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 mx-auto flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">favorite_border</span>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">No saved shops yet</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Tap the heart icon on any store to save it here for quick access.</p>
                    </div>
                    <div class="pt-1">
                        <a href="<?= base_url('shops') ?>" class="inline-flex items-center gap-1.5 bg-terra-green hover:bg-[#3d7a33] text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">storefront</span>
                            <span>Explore Shops</span>
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </main>

</div>

<script>
    (function () {
        document.querySelectorAll('.fav-share').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.dataset.url;
                if (navigator.share) {
                    navigator.share({ title: 'Shop', url: url }).catch(function () {});
                } else if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        var label = btn.querySelector('span');
                        label.textContent = 'check';
                        setTimeout(function () { label.textContent = 'share'; }, 1500);
                    }).catch(function () {});
                } else {
                    window.prompt('Copy shop link:', url);
                }
            });
        });
    })();
</script>

<?= $this->endSection() ?>