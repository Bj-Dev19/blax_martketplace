<?php
/**
 * Shared Pickup Queue Modal Shell Partial
 *
 * Parameters passed:
 * @var string $modalId          DOM element ID (e.g. 'posPickupSelectorModal')
 * @var string $modalType        'product' or 'print'
 * @var string $title            Modal heading
 * @var string $subtitle         Single line subline
 * @var string $icon             Material icon name
 * @var array $items             List of orders or print requests (FIFO sorted)
 * @var array $linkedMap         Cross-reference map for matching customer items
 * @var string $searchInputId    Input ID for live filtering
 * @var string $footerCountId    Footer counter span ID
 */

$totalCount = count($items);
$unpaidCount = 0;
$linkedCount = 0;

foreach ($items as $it) {
    $cId = (int)($it['customer_id'] ?? 0);
    $hasLinked = !empty($linkedMap[$cId]);
    if ($hasLinked) {
        $linkedCount++;
    }

    if ($modalType === 'product') {
        $isPaid = (($it['payment_status'] ?? '') === 'paid');
        $match = $hasLinked ? $linkedMap[$cId][0] : null;
        $matchBal = $match ? max(0.0, (float)($match['total_price'] ?? 0) - (float)($match['down_payment'] ?? 0)) : 0.0;
        if (!$isPaid || $matchBal > 0) {
            $unpaidCount++;
        }
    } else {
        $tot = (float)($it['total_price'] ?? 0);
        $dp = (float)($it['down_payment'] ?? 0);
        $isPrPaid = ($tot > 0 && $dp >= $tot);
        $match = $hasLinked ? $linkedMap[$cId][0] : null;
        $matchPaid = $match ? (($match['payment_status'] ?? '') === 'paid') : true;
        if (!$isPrPaid || !$matchPaid) {
            $unpaidCount++;
        }
    }
}
?>
<div id="<?= esc($modalId) ?>" 
     class="pos-queue-modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-5"
     data-type="<?= esc($modalType) ?>">
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-3xl w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col font-sans">
        
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-100 dark:border-slate-800 pb-3 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0 border border-terra-green/20">
                    <span class="material-symbols-outlined text-[22px]"><?= esc($icon) ?></span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-display text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                            <?= esc($title) ?>
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-terra-green/15 text-terra-green">
                            <span class="pos-modal-total-badge"><?= $totalCount ?></span> in Queue
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                        <?= esc($subtitle) ?>
                    </p>
                </div>
            </div>

            <button type="button" 
                    onclick="closePickupQueueModal('<?= esc($modalId) ?>')" 
                    class="min-h-[44px] min-w-[44px] text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center justify-center gap-1 cursor-pointer"
                    title="Close (Esc)">
                <span class="material-symbols-outlined text-[20px]">close</span>
                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded font-mono text-slate-400 hidden sm:inline">Esc</span>
            </button>
        </div>

        <!-- Search & Filter Chips Controls -->
        <div class="space-y-2.5 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" 
                       id="<?= esc($searchInputId) ?>" 
                       placeholder="Filter by order no., customer name, phone, or product/file..." 
                       oninput="filterQueueModal('<?= esc($modalId) ?>', this.value)"
                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green shadow-xs font-sans">
                <button type="button" 
                        onclick="clearQueueSearch('<?= esc($modalId) ?>', '<?= esc($searchInputId) ?>')" 
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-full cursor-pointer hidden pos-search-clear-btn"
                        title="Clear search">
                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                </button>
            </div>

            <!-- Segmented Filter Chips with Live Counts -->
            <div class="flex items-center gap-2 overflow-x-auto pb-0.5 custom-scrollbar text-xs">
                <button type="button" 
                        class="pos-chip-btn pos-chip-all min-h-[36px] px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-terra-green text-white shadow-xs cursor-pointer" 
                        data-filter="all"
                        onclick="setQueueChipFilter('<?= esc($modalId) ?>', 'all', this)">
                    All (<span class="count-all"><?= $totalCount ?></span>)
                </button>
                <button type="button" 
                        class="pos-chip-btn pos-chip-unpaid min-h-[36px] px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 hover:bg-amber-50 dark:hover:bg-amber-950/40 cursor-pointer" 
                        data-filter="unpaid"
                        onclick="setQueueChipFilter('<?= esc($modalId) ?>', 'unpaid', this)">
                    Unpaid / Balance due (<span class="count-unpaid"><?= $unpaidCount ?></span>)
                </button>
                <button type="button" 
                        class="pos-chip-btn pos-chip-linked min-h-[36px] px-3.5 py-1.5 rounded-full font-bold transition-all shrink-0 bg-white dark:bg-slate-800 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800/60 hover:bg-purple-50 dark:hover:bg-purple-950/40 cursor-pointer" 
                        data-filter="linked"
                        onclick="setQueueChipFilter('<?= esc($modalId) ?>', 'linked', this)">
                    Linked (<span class="count-linked"><?= $linkedCount ?></span>)
                </button>
            </div>
        </div>

        <!-- Scrollable Card Queue List (FIFO Sorted) -->
        <div class="overflow-y-auto space-y-3 flex-1 pr-1 custom-scrollbar pos-queue-cards-list" id="<?= esc($modalId) ?>-list">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $it): ?>
                    <?php 
                    $cId = (int)($it['customer_id'] ?? 0);
                    $matchingLinked = !empty($linkedMap[$cId]) ? $linkedMap[$cId][0] : null;
                    ?>
                    <?= view('tenant/partials/pickup_card', [
                        'item'       => $it,
                        'itemType'   => $modalType,
                        'linkedItem' => $matchingLinked,
                    ]) ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Empty Queue State -->
            <div class="<?= !empty($items) ? 'hidden ' : '' ?>py-14 text-center text-slate-400 text-xs flex flex-col items-center gap-2.5 pos-empty-queue-msg">
                <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600">inventory_2</span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">No orders ready for pick-up</span>
                <span class="text-xs text-slate-400 max-w-[280px]">All customer orders have been fulfilled or are currently being prepared.</span>
            </div>

            <!-- No Search Results State -->
            <div class="hidden py-12 text-center text-slate-400 text-xs flex flex-col items-center gap-2 pos-no-results-msg">
                <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">search_off</span>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">No matching orders found</span>
                <button type="button" 
                        onclick="clearQueueSearch('<?= esc($modalId) ?>', '<?= esc($searchInputId) ?>')" 
                        class="text-xs text-terra-green hover:underline font-bold mt-1 cursor-pointer">
                    Clear search filter
                </button>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-3 shrink-0 text-xs text-slate-500 dark:text-slate-400">
            <span id="<?= esc($footerCountId) ?>" class="pos-footer-counter font-medium">
                Showing <?= $totalCount ?> ready orders (Sorted oldest first)
            </span>
            <button type="button" 
                    onclick="closePickupQueueModal('<?= esc($modalId) ?>')" 
                    class="min-h-[44px] px-5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">
                Close
            </button>
        </div>

    </div>
</div>
