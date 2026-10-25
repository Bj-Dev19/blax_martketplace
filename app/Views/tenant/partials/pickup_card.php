<?php
/**
 * Shared Handover Card Partial
 *
 * Parameters passed:
 * @var array $item                Order or Print Request record
 * @var string $itemType           'product' or 'print'
 * @var array|null $linkedItem     Linked cross-order or print request (if any)
 */

$isProduct = ($itemType === 'product');

// Customer Name
$custName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
if ($custName === '') {
    $custName = 'Online Customer';
}
$phone = trim((string) ($item['phone'] ?? ($item['customer_phone'] ?? '')));

// Reference & Short Ref
$rawRef = $isProduct ? ($item['order_number'] ?? (string)$item['id']) : ($item['request_number'] ?? (string)$item['id']);
$shortRef = strlen($rawRef) > 6 ? substr($rawRef, -6) : $rawRef;

// Date & Relative Time
$rawDate = $isProduct ? ($item['placed_at'] ?? 'now') : ($item['created_at'] ?? 'now');
$timestamp = strtotime($rawDate);
$fullDate = date('M d, Y h:i A', $timestamp);
$diffSec = max(0, time() - $timestamp);
if ($diffSec < 60) {
    $relTime = 'Just now';
} elseif ($diffSec < 3600) {
    $relTime = floor($diffSec / 60) . ' min ago';
} elseif ($diffSec < 86400) {
    $relTime = floor($diffSec / 3600) . ' hr ago';
} elseif ($diffSec < 172800) {
    $relTime = 'Yesterday';
} else {
    $relTime = date('M d', $timestamp);
}

// Payment & Amounts
$orderId = $isProduct ? (int)$item['id'] : ($linkedItem ? (int)$linkedItem['id'] : 0);
$printingId = !$isProduct ? (int)$item['id'] : ($linkedItem ? (int)$linkedItem['id'] : 0);

if ($isProduct) {
    $orderAmt = (float)($item['total_amount'] ?? 0);
    $isOrderPaid = (($item['payment_status'] ?? '') === 'paid');
    $orderCollect = $isOrderPaid ? 0.0 : $orderAmt;
    $orderSummary = trim((string)($item['items_summary'] ?? 'Store Pick-up items'));

    $printAmt = $linkedItem ? (float)($linkedItem['total_price'] ?? 0) : 0.0;
    $printDp = $linkedItem ? (float)($linkedItem['down_payment'] ?? 0) : 0.0;
    $isPrintPaid = $linkedItem ? ($printAmt > 0 && $printDp >= $printAmt) : false;
    $printCollect = $linkedItem ? max(0.0, round($printAmt - $printDp, 2)) : 0.0;
    $printSpecs = $linkedItem ? ((int)($linkedItem['page_count'] ?? 1) . ' pg · ' . (int)($linkedItem['copies'] ?? 1) . ' copy · ' . strtoupper((string)($linkedItem['color_mode'] ?? 'BW')) . ' · ' . esc($linkedItem['paper_size'] ?? 'Letter')) : '';
    $printSummary = $linkedItem ? (esc($linkedItem['file_name'] ?? 'Document.pdf') . ' · ' . $printSpecs) : '';
} else {
    $printAmt = (float)($item['total_price'] ?? 0);
    $printDp = (float)($item['down_payment'] ?? 0);
    $isPrintPaid = ($printAmt > 0 && $printDp >= $printAmt);
    $printCollect = max(0.0, round($printAmt - $printDp, 2));
    $printSpecs = (int)($item['page_count'] ?? 1) . ' pg · ' . (int)($item['copies'] ?? 1) . ' copy · ' . strtoupper((string)($item['color_mode'] ?? 'BW')) . ' · ' . esc($item['paper_size'] ?? 'Letter');
    $printSummary = esc($item['file_name'] ?? 'Document.pdf') . ' · ' . $printSpecs;

    $orderAmt = $linkedItem ? (float)($linkedItem['total_amount'] ?? 0) : 0.0;
    $isOrderPaid = $linkedItem ? (($linkedItem['payment_status'] ?? '') === 'paid') : false;
    $orderCollect = $linkedItem ? ($isOrderPaid ? 0.0 : $orderAmt) : 0.0;
    $orderSummary = $linkedItem ? trim((string)($linkedItem['items_summary'] ?? 'Store Pick-up items')) : '';
}

$hasLinked = !empty($linkedItem);
$totalCollect = $hasLinked ? round($orderCollect + $printCollect, 2) : ($isProduct ? $orderCollect : $printCollect);
$isFullyPaid = ($totalCollect <= 0.0);

// Status Tag for Filter Chips
$statusTag = 'all';
if ($isFullyPaid) {
    $statusTag .= ' paid';
} else {
    $statusTag .= ' unpaid';
}
if ($hasLinked) {
    $statusTag .= ' linked';
}

// Confirmation Payloads
$confirmPayload = [
    'order_id'       => $orderId,
    'printing_id'    => $printingId,
    'customer_name'  => $custName,
    'phone'          => $phone,
    'is_combined'    => $hasLinked,
    'order_ref'      => $isProduct ? $rawRef : ($linkedItem ? ($linkedItem['order_number'] ?? '') : ''),
    'printing_ref'   => !$isProduct ? $rawRef : ($linkedItem ? ($linkedItem['request_number'] ?? '') : ''),
    'order_summary'  => $orderSummary,
    'order_due'      => $orderCollect,
    'is_order_paid'  => $isOrderPaid,
    'print_summary'  => $printSummary,
    'print_due'      => $printCollect,
    'is_print_paid'  => $isPrintPaid,
    'total_collect'  => $totalCollect,
];

// Single Confirm Payload (for secondary link)
$singleConfirmPayload = $confirmPayload;
if ($isProduct) {
    $singleConfirmPayload['printing_id'] = 0;
    $singleConfirmPayload['is_combined'] = false;
    $singleConfirmPayload['print_summary'] = '';
    $singleConfirmPayload['print_due'] = 0;
    $singleConfirmPayload['total_collect'] = $orderCollect;
} else {
    $singleConfirmPayload['order_id'] = 0;
    $singleConfirmPayload['is_combined'] = false;
    $singleConfirmPayload['order_summary'] = '';
    $singleConfirmPayload['order_due'] = 0;
    $singleConfirmPayload['total_collect'] = $printCollect;
}

$searchString = strtolower($rawRef . ' ' . $custName . ' ' . $phone . ' ' . ($isProduct ? $orderSummary : $printSummary) . ($hasLinked ? (' ' . ($isProduct ? $printSummary : $orderSummary)) : ''));
?>
<div class="pos-pickup-card bento-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between gap-3.5"
     data-type="<?= $itemType ?>"
     data-id="<?= (int)$item['id'] ?>"
     data-status="<?= $statusTag ?>"
     data-search="<?= esc($searchString) ?>"
     data-collectible="<?= $totalCollect ?>">

    <!-- 1. Top Row: Customer Name + Relative Time + Monospace Short Ref -->
    <div class="flex items-center justify-between gap-2.5 flex-wrap">
        <div class="flex items-baseline gap-2 min-w-0">
            <h4 class="font-display font-extrabold text-base sm:text-lg text-slate-900 dark:text-white truncate" title="<?= esc($custName) ?>">
                <?= esc($custName) ?>
            </h4>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium shrink-0" title="<?= esc($fullDate) ?>">
                <?= esc($relTime) ?>
            </span>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" 
                    onclick="copyRefText('<?= esc($rawRef) ?>', this)" 
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-600 active:scale-95 transition-all cursor-pointer" 
                    title="Click to copy #<?= esc($rawRef) ?>">
                <span>#<?= esc($shortRef) ?></span>
                <span class="material-symbols-outlined text-[14px] text-slate-400">content_copy</span>
            </button>
        </div>
    </div>

    <!-- 2 & 4. Item Summary Line OR Linked Item Sub-Rows -->
    <?php if ($hasLinked): ?>
        <!-- Linked Item Sub-Rows (Inside the card) -->
        <div class="p-3 rounded-xl bg-slate-50/90 dark:bg-slate-900/50 border border-slate-200/70 dark:border-slate-700/70 space-y-2">
            <!-- Product Order Sub-row -->
            <div class="flex items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 min-w-0 truncate text-slate-800 dark:text-slate-200">
                    <span class="material-symbols-outlined text-[16px] text-terra-green shrink-0">shopping_bag</span>
                    <span class="truncate font-medium" title="<?= esc($orderSummary) ?>"><?= esc($orderSummary) ?></span>
                </div>
                <div class="shrink-0 font-mono text-[11px] font-bold">
                    <?php if ($isOrderPaid): ?>
                        <span class="text-emerald-700 dark:text-emerald-400">Paid ✓</span>
                    <?php else: ?>
                        <span class="text-amber-700 dark:text-amber-400">₱<?= number_format($orderCollect, 2) ?> due</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Printing Request Sub-row -->
            <div class="flex items-center justify-between gap-2 text-xs pt-1.5 border-t border-slate-200/50 dark:border-slate-800">
                <div class="flex items-center gap-2 min-w-0 truncate text-slate-800 dark:text-slate-200">
                    <span class="material-symbols-outlined text-[16px] text-[#007DFE] shrink-0">print</span>
                    <span class="truncate font-medium" title="<?= esc($printSummary) ?>"><?= esc($printSummary) ?></span>
                </div>
                <div class="shrink-0 font-mono text-[11px] font-bold">
                    <?php if ($isPrintPaid): ?>
                        <span class="text-emerald-700 dark:text-emerald-400">Paid ✓</span>
                    <?php elseif ($printCollect < $printAmt): ?>
                        <span class="text-amber-700 dark:text-amber-400">₱<?= number_format($printCollect, 2) ?> bal</span>
                    <?php else: ?>
                        <span class="text-amber-700 dark:text-amber-400">₱<?= number_format($printCollect, 2) ?> due</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Single Item Summary Line (Never generic "Pick-up Items") -->
        <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 font-medium truncate" title="<?= esc($isProduct ? $orderSummary : $printSummary) ?>">
            <?php if ($isProduct): ?>
                <span class="material-symbols-outlined text-[15px] text-terra-green align-text-bottom mr-1">shopping_bag</span>
                <?= esc($orderSummary) ?>
            <?php else: ?>
                <span class="material-symbols-outlined text-[15px] text-[#007DFE] align-text-bottom mr-1">print</span>
                <?= esc($printSummary) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 3. Status Chips -->
    <div class="flex items-center gap-1.5 flex-wrap text-xs">
        <?php if ($isFullyPaid): ?>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Paid online
            </span>
        <?php elseif (!$isProduct && $printDp > 0): ?>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                Down payment paid
            </span>
        <?php else: ?>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Unpaid
            </span>
        <?php endif; ?>

        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200/80">
            Store pick-up
        </span>

        <?php if ($hasLinked): ?>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/80">
                <span class="material-symbols-outlined text-[13px]">join_inner</span>
                Linked pair
            </span>
        <?php endif; ?>
    </div>

    <!-- 5 & 6. Bottom Row: Amount to Collect + Actions -->
    <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-3 flex-wrap">
        <!-- Amount to Collect -->
        <div>
            <?php if ($isFullyPaid): ?>
                <div class="text-emerald-600 dark:text-emerald-400 font-extrabold text-sm sm:text-base flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Paid ✓</span>
                </div>
                <span class="text-[10px] text-slate-400 font-medium block">No balance due</span>
            <?php elseif ($hasLinked): ?>
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Amount to collect</span>
                <span class="font-mono text-base sm:text-xl font-black text-amber-600 dark:text-amber-400">
                    ₱<?= number_format($totalCollect, 2) ?>
                </span>
            <?php elseif (!$isProduct && $printDp > 0): ?>
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Collect balance</span>
                <span class="font-mono text-base sm:text-xl font-black text-amber-600 dark:text-amber-400">
                    ₱<?= number_format($totalCollect, 2) ?>
                </span>
                <span class="text-[10px] text-slate-400 font-mono block">₱<?= number_format($printAmt, 2) ?> total</span>
            <?php else: ?>
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Amount to collect</span>
                <span class="font-mono text-base sm:text-xl font-black text-amber-600 dark:text-amber-400">
                    ₱<?= number_format($totalCollect, 2) ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col items-end gap-1 w-full sm:w-auto">
            <?php if ($hasLinked): ?>
                <button type="button" 
                        onclick="openHandoverConfirm(<?= htmlspecialchars(json_encode($confirmPayload), ENT_QUOTES, 'UTF-8') ?>)" 
                        class="w-full sm:w-auto min-h-[44px] px-4 py-2.5 bg-terra-green hover:bg-terra-green-dark active:scale-[0.98] text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[17px]">inventory</span>
                    <span>Hand over both · Collect ₱<?= number_format($totalCollect, 2) ?></span>
                </button>
                <button type="button" 
                        onclick="openHandoverConfirm(<?= htmlspecialchars(json_encode($singleConfirmPayload), ENT_QUOTES, 'UTF-8') ?>)" 
                        class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 underline font-medium transition-colors cursor-pointer pt-0.5">
                    Only this <?= $isProduct ? 'order' : 'print job' ?>
                </button>
            <?php else: ?>
                <button type="button" 
                        onclick="openHandoverConfirm(<?= htmlspecialchars(json_encode($confirmPayload), ENT_QUOTES, 'UTF-8') ?>)" 
                        class="w-full sm:w-auto min-h-[44px] px-4 py-2.5 bg-terra-green hover:bg-terra-green-dark active:scale-[0.98] text-white rounded-xl text-xs sm:text-sm font-bold shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span><?= $totalCollect > 0 ? 'Hand over · Collect ₱' . number_format($totalCollect, 2) : 'Hand over' ?></span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

</div>
