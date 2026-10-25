<?php if (!empty($active_warning)): ?>
    <!-- Formal Compliance Warning Banner -->
    <div class="rounded-3xl p-5 lg:p-6 bg-rose-500/10 border-2 border-rose-500/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm backdrop-blur-xs">
        <div class="flex items-center gap-3.5">
            <span class="w-11 h-11 rounded-2xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">error</span>
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="font-display font-bold text-rose-800 dark:text-rose-300 text-sm sm:text-base"><?= esc($active_warning['title'] ?? 'Formal Compliance Warning') ?></h4>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-rose-200 dark:bg-rose-900/60 text-rose-900 dark:text-rose-200 uppercase tracking-wider">Attention Required</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1"><?= esc($active_warning['message'] ?? 'Your shop has been flagged for a policy review. Please ensure all store activities comply with marketplace standards.') ?></p>
            </div>
        </div>
        <a href="<?= base_url('tenant/settings') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-colors shadow-sm whitespace-nowrap">
            <span>View Settings & Policies</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
<?php endif; ?>

<?php if (!empty($low_stock_count) && $low_stock_count > 0): ?>
    <!-- Low Stock / Out of Stock Alert Banner -->
    <div class="rounded-3xl p-5 lg:p-6 bg-terra-amber/10 border border-terra-amber/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm backdrop-blur-xs">
        <div class="flex items-center gap-3.5">
            <span class="w-11 h-11 rounded-2xl bg-terra-amber/20 text-terra-earth dark:text-terra-amber flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </span>
            <div>
                <h4 class="font-display font-bold text-slate-900 dark:text-white text-sm sm:text-base">Low Stock Attention Needed</h4>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">You have <span class="font-mono font-bold text-terra-earth dark:text-terra-amber"><?= (int)$low_stock_count ?></span> item<?= (int)$low_stock_count === 1 ? '' : 's' ?> at or below the safety threshold. Restock now to prevent stockouts.</p>
            </div>
        </div>
        <a href="<?= base_url('tenant/inventory?stock=low') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-terra-amber text-slate-950 text-xs font-bold rounded-xl hover:bg-terra-amber-dark transition-colors shadow-sm whitespace-nowrap">
            <span>Review Low Stock Items</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
    </div>
<?php endif; ?>

<?php if (!empty($seasonal_spotlight) && !empty($seasonal_spotlight['top_items'])): ?>
    <!-- AI Seasonal Intelligence & Restock Forecast Banner -->
    <div class="rounded-3xl p-6 lg:p-7 bg-gradient-to-br from-slate-950 via-[#1B2A1E] to-slate-900 text-white shadow-xl border border-white/10 relative overflow-hidden group">
        <!-- Ambient Glow -->
        <div class="absolute -top-16 -right-16 w-72 h-72 bg-terra-green/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-72 h-72 bg-terra-amber/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="max-w-xl">
                <div class="flex items-center gap-2 mb-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-white/10 text-emerald-300 border border-white/15 backdrop-blur-md">
                        <span class="material-symbols-outlined text-[16px]"><?= esc($seasonal_spotlight['icon']) ?></span>
                        <span>Active Season: <?= esc($seasonal_spotlight['short_name']) ?></span>
                    </span>
                    <?php if (!empty($seasonal_spotlight['cohere_powered'])): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-terra-amber/20 text-terra-amber border border-terra-amber/30">
                            <span class="material-symbols-outlined text-[13px]">smart_toy</span>
                            <span>Cohere AI</span>
                        </span>
                    <?php endif; ?>
                    <?php if ($seasonal_spotlight['critical_count'] > 0): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500 text-white shadow-xs animate-pulse">
                            <span><?= (int) $seasonal_spotlight['critical_count'] ?> Critical Stockouts</span>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="font-display text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                    AI Seasonal Restock Intelligence
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                    <?php if (!empty($seasonal_spotlight['cohere_summary'])): ?>
                        <?= esc($seasonal_spotlight['cohere_summary']) ?>
                    <?php else: ?>
                        Demand is surging in Polomolok for <?= esc($seasonal_spotlight['short_name']) ?>. Replenish high-velocity items to capture an estimated <strong class="text-terra-amber font-mono font-extrabold">₱<?= number_format($seasonal_spotlight['est_opp_revenue'], 2) ?></strong> in seasonal turnover.
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="<?= base_url('tenant/analytics') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-xs font-extrabold hover:bg-slate-100 transition-all shadow-md active:scale-95">
                    <span>Explore AI Forecast</span>
                    <span class="material-symbols-outlined text-[18px]">insights</span>
                </a>
            </div>
        </div>

        <!-- Spotlight Top Restock Items -->
        <div class="relative z-10 mt-6 pt-5 border-t border-white/10 grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <?php foreach ($seasonal_spotlight['top_items'] as $item): ?>
                <div class="bg-slate-900/80 hover:bg-slate-800/90 rounded-2xl p-4 border border-white/10 backdrop-blur-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 truncate block"><?= esc($item['category']) ?></span>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full uppercase <?= $item['urgency'] === 'critical' ? 'bg-rose-500 text-white' : 'bg-terra-amber text-slate-950 font-mono' ?>">
                                <?= $item['urgency'] === 'critical' ? 'Urgent' : 'Low Buffer' ?>
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-white truncate mt-1.5" title="<?= esc($item['name']) ?>">
                            <?= esc($item['name']) ?>
                        </h4>
                        <div class="mt-2.5 flex items-center justify-between text-[11px] text-slate-300">
                            <span>Stock: <strong class="text-white font-mono"><?= $item['current_stock'] ?></strong> / <?= $item['target_buffer'] ?> pcs</span>
                            <span class="font-mono text-emerald-400 font-bold">+<?= $item['restock_units'] ?> needed</span>
                        </div>
                        <!-- Mini Progress Bar -->
                        <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="h-full rounded-full <?= $item['urgency'] === 'critical' ? 'bg-rose-400' : 'bg-terra-amber' ?>" style="width: <?= $item['stock_pct'] ?>%"></div>
                        </div>
                    </div>

                    <div class="mt-3.5 pt-3 border-t border-white/10 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 font-mono">Est. ₱<?= number_format($item['est_revenue_uplift'], 2) ?></span>
                        <a href="<?= base_url('tenant/inventory?search=' . urlencode($item['name'])) ?>" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-300 hover:text-white transition-colors">
                            <span>Restock</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
