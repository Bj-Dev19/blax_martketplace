<?= $this->extend('layouts/tenant') ?>

<?php
if (!function_exists('pr_binding_label')) {
    function pr_binding_label(?string $b): string {
        return [
            'none'       => 'Standard',
            'spiral'     => 'Spiral Binding',
            'hardcover'  => 'Hardcover',
            'staple'     => 'Stapled',
        ][$b ?? 'none'] ?? 'Standard';
    }
}

if (!function_exists('pr_spec_line')) {
    function pr_spec_line(array $r): string {
        $parts = [
            (int) $r['page_count'] . ' pages',
            (int) $r['copies'] . ' ' . ((int) $r['copies'] === 1 ? 'copy' : 'copies'),
            pr_binding_label($r['binding_option'] ?? 'none'),
        ];
        return implode(' • ', $parts);
    }
}

$nextStates = [
    'new'                => ['in_production' => 'Start Production', 'cancelled' => 'Cancel Request'],
    'in_production'      => ['ready_for_pickup' => 'Ready for Pickup', 'ready_for_delivery' => 'Mark In Transit', 'cancelled' => 'Cancel Request'],
    'ready_for_pickup'   => ['ready_for_delivery' => 'Mark In Transit', 'completed' => 'Mark Completed', 'cancelled' => 'Cancel Request'],
    'ready_for_delivery' => ['ready_for_pickup' => 'Mark Ready for Pickup', 'completed' => 'Mark Completed', 'cancelled' => 'Cancel Request'],
    'completed'          => [],
    'cancelled'          => [],
];

$activeStatus = $filters['status'] ?? '';
$activeDocType = $filters['doc_type'] ?? '';
$searchQuery = $filters['q'] ?? '';
?>

<?= $this->section('content') ?>

<div class="flex-1 space-y-6">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center gap-2.5 shadow-xs animate-fadeIn">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm font-semibold flex items-center gap-2.5 shadow-xs animate-fadeIn">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold shadow-2xs">
                    <span class="material-symbols-outlined text-[24px]">print</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black font-display tracking-tight text-slate-900 dark:text-white">Printing Production Hub</h1>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">Manage customer document queues, print parameters, and fulfillment status in real-time.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" onclick="openPrintingSettingsModal()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-xs active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px] text-terra-green">tune</span>
                <span>Printing Settings</span>
            </button>
        </div>
    </div>

    <!-- 5-Column Bento KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
        <!-- New Requests -->
        <a href="<?= base_url('tenant/printing?status=new') ?>" class="group bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border <?= $activeStatus === 'new' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80 dark:border-slate-800' ?> hover:border-amber-500/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-32 cursor-pointer">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-amber-600 transition-colors">New Requests</span>
                <span class="material-symbols-outlined text-amber-600 bg-amber-500/10 p-1.5 rounded-xl text-[18px]">new_releases</span>
            </div>
            <div>
                <div id="kpi-printing-new" class="font-mono text-2xl font-black text-amber-600 dark:text-amber-400"><?= number_format((int) ($summary['new'] ?? 0)) ?></div>
                <span class="text-[10px] font-semibold text-slate-400">Needs acceptance</span>
            </div>
        </a>

        <!-- In Production -->
        <a href="<?= base_url('tenant/printing?status=in_production') ?>" class="group bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border <?= $activeStatus === 'in_production' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80 dark:border-slate-800' ?> hover:border-terra-amber/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-32 cursor-pointer">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-terra-earth dark:group-hover:text-amber-400 transition-colors flex items-center gap-1.5">
                    <span>In Production</span>
                    <span class="w-2 h-2 rounded-full bg-terra-amber <?= !empty($queue) ? 'animate-pulse' : '' ?>"></span>
                </span>
                <span class="material-symbols-outlined text-terra-earth dark:text-amber-400 bg-terra-amber/15 p-1.5 rounded-xl text-[18px]">manufacturing</span>
            </div>
            <div>
                <div id="kpi-printing-in-production" class="font-mono text-2xl font-black text-terra-earth dark:text-amber-300"><?= number_format((int) ($summary['in_production'] ?? 0)) ?></div>
                <span class="text-[10px] font-semibold text-slate-400">On printer queue</span>
            </div>
        </a>

        <!-- Ready for Pickup -->
        <a href="<?= base_url('tenant/printing?status=ready_for_pickup') ?>" class="group bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border <?= $activeStatus === 'ready_for_pickup' ? 'border-terra-green ring-2 ring-terra-green/20' : 'border-slate-200/80 dark:border-slate-800' ?> hover:border-emerald-500/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-32 cursor-pointer">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-terra-green transition-colors">Ready for Pickup</span>
                <span class="material-symbols-outlined text-terra-green bg-terra-green/10 p-1.5 rounded-xl text-[18px]">storefront</span>
            </div>
            <div>
                <div id="kpi-printing-ready-pickup" class="font-mono text-2xl font-black text-terra-green"><?= number_format((int) ($summary['ready_for_pickup'] ?? 0)) ?></div>
                <span class="text-[10px] font-semibold text-slate-400">Counter collection</span>
            </div>
        </a>

        <!-- In Transit -->
        <a href="<?= base_url('tenant/printing?status=ready_for_delivery') ?>" class="group bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border <?= $activeStatus === 'ready_for_delivery' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200/80 dark:border-slate-800' ?> hover:border-blue-500/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-32 cursor-pointer">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">In Transit</span>
                <span class="material-symbols-outlined text-blue-600 bg-blue-500/10 p-1.5 rounded-xl text-[18px]">local_shipping</span>
            </div>
            <div>
                <div id="kpi-printing-ready-delivery" class="font-mono text-2xl font-black text-blue-600 dark:text-blue-400"><?= number_format((int) ($summary['ready_for_delivery'] ?? 0)) ?></div>
                <span class="text-[10px] font-semibold text-slate-400">Rider dispatched</span>
            </div>
        </a>

        <!-- Completed -->
        <a href="<?= base_url('tenant/printing?status=completed') ?>" class="group col-span-2 sm:col-span-1 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-5 rounded-3xl border <?= $activeStatus === 'completed' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 dark:border-slate-800' ?> hover:border-emerald-500/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-32 cursor-pointer">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 transition-colors">Completed</span>
                <span class="material-symbols-outlined text-emerald-600 bg-emerald-500/10 p-1.5 rounded-xl text-[18px]">task_alt</span>
            </div>
            <div>
                <div id="kpi-printing-completed" class="font-mono text-2xl font-black text-slate-800 dark:text-slate-100"><?= number_format((int) ($summary['completed'] ?? 0)) ?></div>
                <span class="text-[10px] font-semibold text-slate-400">Archived jobs</span>
            </div>
        </a>
    </div>

    <!-- Collapsible Active Production Workbench (Horizontal Shelf) -->
    <!-- Always rendered in the DOM (hidden while empty) so realtime events can inject job
         cards without a page refresh, mirroring tenant/orders.php processingQueueGrid. -->
    <div id="productionWorkbenchContainer" class="bg-gradient-to-r from-amber-500/5 via-amber-500/10 to-orange-500/5 dark:from-amber-950/20 dark:via-amber-900/20 dark:to-orange-950/10 rounded-3xl p-5 border border-amber-500/30 shadow-xs space-y-4<?= empty($queue) ? ' hidden' : '' ?>">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[20px]">manufacturing</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold font-display text-slate-900 dark:text-white">Active Production Workbench</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-white flex items-center gap-1 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                <span id="workbenchActiveCount"><?= count($queue) ?></span> Active
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Requests awaiting acceptance and documents currently on the printing desk</p>
                    </div>
                </div>
                <button type="button" onclick="toggleWorkbenchCards()" class="p-1.5 rounded-xl bg-white/80 dark:bg-slate-800 hover:bg-white text-slate-500 dark:text-slate-400 transition-colors shadow-2xs flex items-center gap-1 text-xs font-semibold cursor-pointer">
                    <span id="workbenchToggleText" class="hidden sm:inline">Collapse</span>
                    <span id="workbenchToggleIcon" class="material-symbols-outlined text-[18px]">expand_less</span>
                </button>
            </div>

            <!-- Workbench Job Cards Grid -->
            <div id="workbenchCardsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($queue as $q): ?>
                    <?php
                    $qFullName = trim(($q['first_name'] ?? '') . ' ' . ($q['last_name'] ?? ''));
                    $qInitials = $qFullName !== '' ? mb_strtoupper(mb_substr($qFullName, 0, 2)) : 'CU';
                    $qIsDelivery = ($q['fulfillment_method'] ?? 'pickup') === 'delivery';
                    $qIsNew      = ($q['status'] ?? '') === 'new';
                    $qFileName = !empty($q['file_name']) ? $q['file_name'] : 'Document.pdf';
                    $qDocType = strtoupper(!empty($q['document_type']) ? $q['document_type'] : (pathinfo($qFileName, PATHINFO_EXTENSION) ?: 'PDF'));
                    $qTotal = (float) ($q['total_price'] ?? 0);
                    $qDown = (float) ($q['down_payment'] ?? 0);
                    $qBalance = max(0, $qTotal - $qDown);
                    ?>
                    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm rounded-2xl p-4 border border-amber-500/25 shadow-xs hover:border-amber-500/50 transition-all flex flex-col justify-between gap-3 group">
                        <!-- Top Meta -->
                        <div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-1.5">
                                    <button type="button"
                                            onclick="openRequestDetails(this)"
                                            class="font-mono text-xs font-bold text-terra-green hover:underline cursor-pointer"
                                            data-id="<?= (int) $q['id'] ?>"
                                            data-request="<?= esc($q['request_number']) ?>"
                                            data-customer="<?= esc($qFullName !== '' ? $qFullName : 'Customer') ?>"
                                            data-file="<?= esc($qFileName) ?>"
                                            data-doctype="<?= esc($qDocType) ?>"
                                            data-changetype="<?= esc(($q['doc_change_type'] ?? '') === 'has_changes' ? 'Requested Changes' : 'Print As-Is') ?>"
                                            data-instructions="<?= esc($q['special_instructions'] ?? '') ?>"
                                            data-attachments="<?= esc(json_encode($q['attachments'] ?? []), 'attr') ?>"
                                            data-specs="<?= esc(pr_spec_line($q)) ?>"
                                            data-color="<?= esc(strtoupper(($q['color_mode'] ?? '') === 'color' ? 'Color' : 'B&W')) ?> • <?= esc(ucfirst($q['paper_size'] ?? 'Letter')) ?>"
                                            data-fulfillment="<?= esc(ucfirst(str_replace('_', ' ', $q['fulfillment_method'] ?? 'delivery'))) ?>"
                                            data-printer="<?= esc($q['printer_assigned'] ?? 'Not assigned') ?>"
                                            data-progress="<?= (int) ($q['progress_percent'] ?? 0) ?>"
                                            data-amount="<?= esc(number_format($qTotal, 2)) ?>"
                                            data-down="<?= esc(number_format($qDown, 2)) ?>"
                                            data-date="<?= esc(date('M d, Y h:i A', strtotime($q['created_at']))) ?>">
                                        #<?= esc($q['request_number']) ?>
                                    </button>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?= $qIsNew ? 'bg-amber-500 text-white border border-amber-500 animate-pulse' : 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20' ?>">
                                        <?= $qIsNew ? 'New' : 'Printing' ?>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium"><?= esc(date('g:i A', strtotime($q['created_at']))) ?></span>
                            </div>

                            <!-- Customer & File -->
                            <div class="mt-2.5 flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center text-xs font-bold shrink-0">
                                    <?= esc($qInitials) ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= esc($qFullName !== '' ? $qFullName : 'Customer') ?></div>
                                    <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                                        <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">description</span>
                                        <span class="truncate font-medium text-[11px]" title="<?= esc($qFileName) ?>"><?= esc($qFileName) ?></span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold <?= ($qDocType === 'DOCX') ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' ?>">
                                            <?= esc($qDocType) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Specs Chips -->
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-300">
                                    <?= strtoupper(esc(($q['color_mode'] ?? '') === 'color' ? 'Color' : 'B&W')) ?> • <?= esc(ucfirst($q['paper_size'] ?? 'Letter')) ?>
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                                    <?= (int) ($q['page_count'] ?? 1) ?> pgs × <?= (int) ($q['copies'] ?? 1) ?> <?= (int) ($q['copies'] ?? 1) === 1 ? 'copy' : 'copies' ?>
                                </span>
                                <?php if (!empty($q['binding_option']) && $q['binding_option'] !== 'none'): ?>
                                    <span class="px-2 py-0.5 rounded-md bg-terra-green/10 text-terra-green border border-terra-green/20 text-[10px] font-bold">
                                        <?= esc(pr_binding_label($q['binding_option'])) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Actions Area -->
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                            <?php $qShipFee = (float) ($q['shipping_fee'] ?? 0); ?>
                            <?php if ($qIsDelivery && $qShipFee > 0): ?>
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500 dark:text-slate-400">Shipping (<?= number_format((float) ($q['distance_km'] ?? 0), 1) ?> km):</span>
                                    <span class="font-mono font-bold text-sky-700 dark:text-sky-400">₱<?= number_format($qShipFee, 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 dark:text-slate-400">Balance:</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">₱<?= number_format($qBalance, 2) ?></span>
                            </div>

                            <!-- Primary 1-Click Action Button -->
                            <?php if ($qIsNew): ?>
                                <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" onsubmit="return confirm('Accept Request #<?= esc($q['request_number'] ?? ('PR-' . $q['id'])) ?> and move it onto the printing desk?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $q['id'] ?>">
                                    <input type="hidden" name="status" value="in_production">
                                    <button type="submit" class="w-full py-2 px-3 bg-terra-amber hover:bg-amber-500 active:scale-[0.98] text-slate-950 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                                        <span>Accept &amp; Print</span>
                                    </button>
                                </form>
                            <?php elseif ($qIsDelivery): ?>
                                <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" onsubmit="return confirm('Dispatch Request #<?= esc($q['request_number'] ?? ('PR-' . $q['id'])) ?> as In Transit for delivery?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $q['id'] ?>">
                                    <input type="hidden" name="status" value="ready_for_delivery">
                                    <button type="submit" class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                        <span>Mark In Transit</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $q['id'] ?>">
                                    <input type="hidden" name="status" value="ready_for_pickup">
                                    <button type="submit" class="w-full py-2 px-3 bg-terra-green hover:bg-emerald-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">storefront</span>
                                        <span>Ready for Pick-up</span>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- Secondary Quick Tools -->
                            <div class="grid grid-cols-3 gap-1 pt-0.5">
                                <button type="button"
                                        onclick="openRequestDetails(this.closest('.group').querySelector('button[data-request]'))"
                                        class="py-1 px-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors cursor-pointer">
                                    <span class="material-symbols-outlined text-[13px] text-terra-green">visibility</span>
                                    <span>Ticket</span>
                                </button>
                                <a href="<?= base_url('tenant/printing/download/' . (int) $q['id']) ?>" class="py-1 px-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-[13px] text-terra-green">download</span>
                                    <span>File</span>
                                </a>
                                <a href="<?= base_url('tenant/printing/receipt/' . (int) $q['id']) ?>" target="_blank" class="py-1 px-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors">
                                    <span class="material-symbols-outlined text-[13px] text-terra-green">receipt_long</span>
                                    <span>Receipt</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <!-- 100% Full-Width Unified Print Queue Table Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-3xl overflow-hidden shadow-xs border border-slate-200/80 dark:border-slate-800">

        <!-- Stage Filter Tabs & Search Header -->
        <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-4">
            
            <!-- Stage Pipeline Tabs -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full scrollbar-none">
                    <a href="<?= base_url('tenant/printing') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $activeStatus === '' ? 'bg-terra-green text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        All Requests
                    </a>
                    <a href="<?= base_url('tenant/printing?status=new') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 <?= $activeStatus === 'new' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        <span>New</span>
                        <?php if (!empty($summary['new'])): ?>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black <?= $activeStatus === 'new' ? 'bg-white text-amber-600' : 'bg-amber-500/20 text-amber-700 dark:text-amber-400' ?>"><?= (int) $summary['new'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('tenant/printing?status=in_production') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 <?= $activeStatus === 'in_production' ? 'bg-terra-amber text-slate-950 font-black shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        <span>In Production</span>
                        <?php if (!empty($summary['in_production'])): ?>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black <?= $activeStatus === 'in_production' ? 'bg-slate-950 text-white' : 'bg-terra-amber/20 text-terra-earth dark:text-amber-300' ?>"><?= (int) $summary['in_production'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('tenant/printing?status=ready_for_pickup') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 <?= $activeStatus === 'ready_for_pickup' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        <span>Ready for Pickup</span>
                        <?php if (!empty($summary['ready_for_pickup'])): ?>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black <?= $activeStatus === 'ready_for_pickup' ? 'bg-white text-emerald-600' : 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' ?>"><?= (int) $summary['ready_for_pickup'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('tenant/printing?status=ready_for_delivery') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 <?= $activeStatus === 'ready_for_delivery' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        <span>In Transit</span>
                        <?php if (!empty($summary['ready_for_delivery'])): ?>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black <?= $activeStatus === 'ready_for_delivery' ? 'bg-white text-blue-600' : 'bg-blue-500/20 text-blue-700 dark:text-blue-300' ?>"><?= (int) $summary['ready_for_delivery'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('tenant/printing?status=completed') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 <?= $activeStatus === 'completed' ? 'bg-slate-800 text-white shadow-xs dark:bg-slate-700' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        <span>Completed</span>
                        <?php if (!empty($summary['completed'])): ?>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black <?= $activeStatus === 'completed' ? 'bg-white text-slate-800 dark:text-slate-900' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' ?>"><?= (int) $summary['completed'] ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?= base_url('tenant/printing?status=cancelled') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $activeStatus === 'cancelled' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>">
                        Cancelled
                    </a>
                </div>

                <?php if ($activeStatus === 'completed' && !empty($requests)): ?>
                    <form action="<?= base_url('tenant/printing/archive-all') ?>" method="POST" onsubmit="return confirm('Archive all completed requests? They can still be restored from the Archive page.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5 text-xs font-bold transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">archive</span>
                            <span>Archive All Completed</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Filters & Real-time Search Toolbar -->
            <form method="get" action="<?= base_url('tenant/printing') ?>" id="printingFiltersForm" class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-200/60 dark:border-slate-800/60">
                <input type="hidden" name="status" value="<?= esc($activeStatus) ?>">
                <input type="hidden" name="doc_type" id="filterDocTypeInput" value="<?= esc($activeDocType) ?>">

                <!-- Real-time Search Box -->
                <div class="relative flex-1 min-w-[220px] max-w-md">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input name="q" id="printingSearchInput" value="<?= esc($searchQuery) ?>" oninput="handleSearchInput(this)" placeholder="Search request #, customer name, or file..." autocomplete="off" class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green text-xs" type="text">
                    <button type="button" id="searchClearBtn" onclick="clearSearchInput()" class="<?= empty($searchQuery) ? 'hidden ' : '' ?>absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-0.5 rounded-full transition-colors cursor-pointer" title="Clear search">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>

                <!-- Document Type Filter Pills -->
                <div class="flex items-center gap-2">
                    <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <button type="button" onclick="setDocTypeFilter('')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer <?= $activeDocType === '' ? 'bg-terra-green text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' ?>" title="Show all file types">
                            All Formats
                        </button>
                        <button type="button" onclick="setDocTypeFilter('pdf')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer <?= $activeDocType === 'pdf' ? 'bg-red-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' ?>" title="Filter only PDF requests">
                            <span class="w-1.5 h-1.5 rounded-full <?= $activeDocType === 'pdf' ? 'bg-white' : 'bg-red-500' ?>"></span>
                            <span>PDF</span>
                        </button>
                        <button type="button" onclick="setDocTypeFilter('docx')" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer <?= $activeDocType === 'docx' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' ?>" title="Filter only Word / DOCS requests">
                            <span class="w-1.5 h-1.5 rounded-full <?= $activeDocType === 'docx' ? 'bg-white' : 'bg-blue-500' ?>"></span>
                            <span>Word</span>
                        </button>
                    </div>

                    <!-- Per Page Filter -->
                    <select name="per_page" onchange="document.getElementById('printingFiltersForm').submit()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-terra-green cursor-pointer">
                        <option value="10" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '10') ? 'selected' : '' ?>>10 / page</option>
                        <option value="25" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '25') ? 'selected' : '' ?>>25 / page</option>
                        <option value="50" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '50') ? 'selected' : '' ?>>50 / page</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- 100% Full-Width Unified Table -->
        <div class="overflow-x-auto w-full scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Request #</th>
                        <th class="px-4 py-3.5">Customer</th>
                        <th class="px-4 py-3.5">Document File</th>
                        <th class="px-4 py-3.5">Print Specifications</th>
                        <th class="px-4 py-3.5">Total &amp; Payment</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Fulfillment Actions</th>
                    </tr>
                </thead>
                <tbody id="printingTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $r): ?>
                            <?php
                            $rawReqNum = trim((string) ($r['request_number'] ?? ''));
                            $reqNum = $rawReqNum !== '' ? $rawReqNum : ('PR-' . $r['id']);
                            $cleanReq = '#' . ltrim($reqNum, '#');
                            $fullName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                            $initials = $fullName !== '' ? mb_strtoupper(mb_substr($fullName, 0, 2)) : 'GU';
                            $profileImage = trim((string) ($r['profile_image_url'] ?? ''));
                            $isDeliv = ($r['fulfillment_method'] ?? 'pickup') === 'delivery';
                            $fileName = !empty($r['file_name']) ? $r['file_name'] : 'Document.pdf';
                            $docType = strtoupper(!empty($r['document_type']) ? $r['document_type'] : (pathinfo($fileName, PATHINFO_EXTENSION) ?: 'PDF'));
                            $st = $r['status'] ?? 'new';
                            $totalAmt = (float) ($r['total_price'] ?? 0);
                            $downAmt = (float) ($r['down_payment'] ?? 0);
                            $balance = max(0, $totalAmt - $downAmt);
                            $hasAttachments = !empty($r['attachments']) && is_array($r['attachments']) && count($r['attachments']) > 0;
                            ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors group">
                                <!-- Request # & Date -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button"
                                                onclick="openRequestDetails(this)"
                                                class="font-mono text-xs font-bold text-terra-green hover:text-emerald-700 bg-terra-green/10 hover:bg-terra-green/20 border border-terra-green/25 px-2.5 py-1 rounded-xl inline-flex items-center gap-1 transition-all active:scale-95 shadow-2xs cursor-pointer"
                                                title="View Full Job Ticket"
                                                data-id="<?= (int) $r['id'] ?>"
                                                data-request="<?= esc($reqNum) ?>"
                                                data-customer="<?= esc($fullName !== '' ? $fullName : 'Customer') ?>"
                                                data-file="<?= esc($fileName) ?>"
                                                data-doctype="<?= esc($docType) ?>"
                                                data-changetype="<?= esc(($r['doc_change_type'] ?? '') === 'has_changes' ? 'Requested Changes' : 'Print As-Is') ?>"
                                                data-instructions="<?= esc($r['special_instructions'] ?? '') ?>"
                                                data-attachments="<?= esc(json_encode($r['attachments'] ?? []), 'attr') ?>"
                                                data-specs="<?= esc(pr_spec_line($r)) ?>"
                                                data-color="<?= esc(strtoupper(($r['color_mode'] ?? '') === 'color' ? 'Color' : 'B&W')) ?> • <?= esc(ucfirst($r['paper_size'] ?? 'Letter')) ?>"
                                                data-fulfillment="<?= esc(ucfirst(str_replace('_', ' ', $r['fulfillment_method'] ?? 'pickup'))) ?>"
                                                data-printer="<?= esc($r['printer_assigned'] ?? 'Not assigned') ?>"
                                                data-progress="<?= (int) ($r['progress_percent'] ?? 0) ?>"
                                                data-amount="<?= esc(number_format($totalAmt, 2)) ?>"
                                                data-down="<?= esc(number_format($downAmt, 2)) ?>"
                                                data-date="<?= esc(date('M d, Y h:i A', strtotime($r['created_at']))) ?>">
                                            <span class="material-symbols-outlined text-[13px]">tag</span>
                                            <span><?= esc($cleanReq) ?></span>
                                        </button>

                                        <?php if ($hasAttachments): ?>
                                            <span class="w-5 h-5 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-[12px] cursor-pointer" title="<?= count($r['attachments']) ?> reference photos attached" onclick="openRequestDetails(this.previousElementSibling)">
                                                <span class="material-symbols-outlined text-[13px]">photo_library</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-1 flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        <span><?= esc(date('M d, g:i A', strtotime($r['created_at']))) ?></span>
                                    </div>
                                </td>

                                <!-- Customer -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5 min-w-[140px]">
                                        <div class="w-8 h-8 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center text-xs font-bold shrink-0 <?= $profileImage !== '' ? 'relative overflow-hidden' : '' ?> ring-2 ring-slate-100 dark:ring-slate-800">
                                            <span><?= esc($initials) ?></span>
                                            <?php if ($profileImage !== ''): ?>
                                                <img class="absolute inset-0 w-full h-full object-cover" src="<?= esc(profile_image_url($profileImage)) ?>" alt="<?= esc($fullName) ?> avatar" loading="lazy" onerror="this.remove();">
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white truncate block leading-tight max-w-[140px]" title="<?= esc($fullName !== '' ? $fullName : 'Customer') ?>"><?= esc($fullName !== '' ? $fullName : 'Guest Customer') ?></span>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <?php if ($isDeliv): ?>
                                                    <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-blue-600 dark:text-blue-400">
                                                        <span class="material-symbols-outlined text-[12px]">local_shipping</span> Delivery
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-600 dark:text-emerald-400">
                                                        <span class="material-symbols-outlined text-[12px]">storefront</span> Pickup
                                                    </span>
                                                <?php endif; ?>
                                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 truncate max-w-[90px]"><?= esc(!empty($r['phone']) ? $r['phone'] : 'Direct') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- File & Format -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-terra-green shrink-0 border border-slate-200 dark:border-slate-700">
                                            <span class="material-symbols-outlined text-[18px]">description</span>
                                        </div>
                                        <div class="min-w-0 max-w-[170px]">
                                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate block" title="<?= esc($fileName) ?>"><?= esc($fileName) ?></span>
                                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="px-1.5 py-0.2 rounded-md text-[9px] font-bold tracking-wider uppercase <?= ($docType === 'DOCX') ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' ?>">
                                                    <?= esc($docType) ?>
                                                </span>
                                                <?php if ($docType === 'DOCX' && ($r['doc_change_type'] ?? '') === 'has_changes'): ?>
                                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 flex items-center gap-0.5">
                                                        <span class="material-symbols-outlined text-[10px]">edit</span> Changes
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Specifications -->
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1 min-w-[160px]">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                <?= strtoupper(esc(($r['color_mode'] ?? '') === 'color' ? 'Color' : 'B&W')) ?>
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                <?= esc(ucfirst($r['paper_size'] ?? 'Letter')) ?>
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                            <?= (int) ($r['page_count'] ?? 1) ?> pgs × <?= (int) ($r['copies'] ?? 1) ?> <?= (int) ($r['copies'] ?? 1) === 1 ? 'copy' : 'copies' ?>
                                            <?php if (!empty($r['binding_option']) && $r['binding_option'] !== 'none'): ?>
                                                • <span class="text-terra-green font-bold"><?= esc(pr_binding_label($r['binding_option'])) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total & Payment -->
                                <td class="px-4 py-3.5">
                                    <div class="min-w-[110px]">
                                        <div class="font-mono text-xs font-black text-slate-900 dark:text-white">₱<?= number_format($totalAmt, 2) ?></div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            <?php $rShipFee = (float) ($r['shipping_fee'] ?? 0); ?>
                                            <?php if ($isDeliv && $rShipFee > 0): ?>
                                                <span class="block text-sky-700 dark:text-sky-400 font-mono">Ship ₱<?= number_format($rShipFee, 2) ?><?= !empty($r['distance_km']) ? ' · ' . number_format((float) $r['distance_km'], 1) . ' km' : '' ?></span>
                                            <?php endif; ?>
                                            <?php if ($balance <= 0): ?>
                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Fully Paid</span>
                                            <?php else: ?>
                                                <span>Bal: <b class="font-mono text-amber-600 dark:text-amber-400">₱<?= number_format($balance, 2) ?></b></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if ($st === 'new'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/25">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> New
                                        </span>
                                    <?php elseif ($st === 'in_production'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-terra-amber/15 text-terra-earth dark:text-amber-300 border border-terra-amber/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-terra-amber animate-pulse"></span> Printing
                                        </span>
                                    <?php elseif ($st === 'ready_for_pickup'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/25">
                                            <span class="material-symbols-outlined text-[14px]">storefront</span> Ready
                                        </span>
                                    <?php elseif ($st === 'ready_for_delivery'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/25">
                                            <span class="material-symbols-outlined text-[14px]">local_shipping</span> In Transit
                                        </span>
                                    <?php elseif ($st === 'completed'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            <span class="material-symbols-outlined text-[14px] text-emerald-600">check</span> Completed
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/25">
                                            Cancelled
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Primary Action & Dropdown -->
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Primary Stage Button -->
                                        <?php if ($st === 'new'): ?>
                                            <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" class="inline-block">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                                <input type="hidden" name="status" value="in_production">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-terra-green hover:bg-emerald-700 text-white text-xs font-bold transition-all flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95">
                                                    <span class="material-symbols-outlined text-[15px]">play_arrow</span>
                                                    <span>Accept &amp; Print</span>
                                                </button>
                                            </form>
                                        <?php elseif ($st === 'in_production'): ?>
                                            <?php if ($isDeliv): ?>
                                                <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" class="inline-block" onsubmit="return confirm('Dispatch Request #<?= esc($reqNum) ?> as In Transit for delivery?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                                    <input type="hidden" name="status" value="ready_for_delivery">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95">
                                                        <span class="material-symbols-outlined text-[15px]">local_shipping</span>
                                                        <span>Dispatch</span>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" class="inline-block">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                                    <input type="hidden" name="status" value="ready_for_pickup">
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-terra-green hover:bg-emerald-700 text-white text-xs font-bold transition-all flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95">
                                                        <span class="material-symbols-outlined text-[15px]">storefront</span>
                                                        <span>Ready</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php elseif ($st === 'ready_for_pickup' || $st === 'ready_for_delivery'): ?>
                                            <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" class="inline-block" onsubmit="return confirm('Mark Request #<?= esc($reqNum) ?> as completed?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95">
                                                    <span class="material-symbols-outlined text-[15px]">task_alt</span>
                                                    <span>Complete</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Contextual Actions Dropdown Toggle -->
                                        <div class="relative inline-block text-left">
                                            <button type="button"
                                                    onclick="toggleDropdown(this)"
                                                    data-row="<?= (int) $r['id'] ?>"
                                                    class="more-toggle p-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                                    title="More actions"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                            </button>

                                            <!-- Dropdown Menu Item -->
                                            <div id="more-menu-<?= (int) $r['id'] ?>" class="more-menu hidden fixed z-[90] w-48 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-1.5 text-xs text-slate-700 dark:text-slate-200">
                                                <button type="button"
                                                        onclick="openRequestDetails(document.querySelector('button[data-id=\"<?= (int) $r['id'] ?>\"]')); closeMenus();"
                                                        class="w-full px-3 py-2 text-left hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2 font-semibold cursor-pointer">
                                                    <span class="material-symbols-outlined text-[16px] text-terra-green">receipt_long</span>
                                                    <span>View Job Ticket</span>
                                                </button>
                                                <a href="<?= base_url('tenant/printing/download/' . (int) $r['id']) ?>" class="w-full px-3 py-2 text-left hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2 font-semibold">
                                                    <span class="material-symbols-outlined text-[16px] text-terra-green">download</span>
                                                    <span>Download File</span>
                                                </a>
                                                <a href="<?= base_url('tenant/printing/receipt/' . (int) $r['id']) ?>" target="_blank" class="w-full px-3 py-2 text-left hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2 font-semibold">
                                                    <span class="material-symbols-outlined text-[16px] text-terra-green">receipt</span>
                                                    <span>Print Official Receipt</span>
                                                </a>

                                                <?php if ($st !== 'completed' && $st !== 'cancelled'): ?>
                                                    <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                                    <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" onsubmit="return confirm('Cancel request #<?= esc($reqNum) ?>?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="w-full px-3 py-2 text-left hover:bg-rose-50 dark:hover:bg-rose-950/30 text-rose-600 dark:text-rose-400 flex items-center gap-2 font-semibold cursor-pointer">
                                                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                                                            <span>Cancel Request</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($st === 'completed'): ?>
                                                    <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                                                    <form action="<?= base_url('tenant/printing/archive/' . (int) $r['id']) ?>" method="POST" onsubmit="return confirm('Archive completed request #<?= esc($reqNum) ?>?');">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="w-full px-3 py-2 text-left hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 flex items-center gap-2 font-semibold cursor-pointer">
                                                            <span class="material-symbols-outlined text-[16px]">archive</span>
                                                            <span>Archive Job</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 mx-auto flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl">print_disabled</span>
                                </div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No print requests found</p>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">There are no printing requests currently matching the selected status or search filter.</p>
                                <?php if ($activeStatus !== '' || $activeDocType !== '' || $searchQuery !== ''): ?>
                                    <a href="<?= base_url('tenant/printing') ?>" class="mt-3.5 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">filter_alt_off</span>
                                        <span>Reset Filters</span>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginator Footer -->
        <?php if (isset($pager)): ?>
            <?php
            $pTotal   = (int) $pager->getTotal('recent');
            $pPerPage = (int) ($pager->getPerPage('recent') ?: 10);
            $pCur     = (int) $pager->getCurrentPage('recent');
            $pPages   = (int) $pager->getPageCount('recent');
            $pStart   = $pTotal === 0 ? 0 : ($pCur - 1) * $pPerPage + 1;
            $pEnd     = min($pCur * $pPerPage, $pTotal);
            ?>
            <div class="px-6 py-4 bg-slate-50/60 dark:bg-slate-800/40 flex justify-between items-center border-t border-slate-200/80 dark:border-slate-800 flex-wrap gap-3 text-xs">
                <p class="font-medium text-slate-500 dark:text-slate-400">
                    Showing <span class="font-bold text-slate-900 dark:text-white font-mono"><?= number_format($pStart) ?></span> to <span class="font-bold text-slate-900 dark:text-white font-mono"><?= number_format($pEnd) ?></span> of <span class="font-bold text-slate-900 dark:text-white font-mono"><?= number_format($pTotal) ?></span> requests
                </p>

                <?php if ($pPages > 1): ?>
                    <div class="flex items-center gap-1.5">
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs <?= $pCur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('recent') ?>" title="Previous Page">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>

                        <?php
                        $window = [];
                        for ($i = 1; $i <= $pPages; $i++) {
                            if ($i === 1 || $i === $pPages || abs($i - $pCur) <= 2) {
                                $window[] = $i;
                            }
                        }
                        $prev = 0;
                        foreach ($window as $num):
                            if ($num - $prev > 1): ?>
                                <span class="px-1 text-slate-400 font-mono">...</span>
                            <?php endif; ?>
                            <a class="w-8 h-8 rounded-xl flex items-center justify-center font-bold font-mono transition-all <?= $pCur === $num ? 'bg-terra-green text-white shadow-xs' : 'border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 shadow-2xs' ?>" href="<?= $pager->getPageURI($num, 'recent') ?>"><?= $num ?></a>
                        <?php $prev = $num; endforeach; ?>

                        <a class="w-8 h-8 rounded-xl flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs <?= $pCur >= $pPages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('recent') ?>" title="Next Page">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Job Ticket Details Modal -->
<div id="requestModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-xl w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl animate-scaleUp">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Job Ticket <span id="rmRequest" class="text-terra-green font-mono"></span></h3>
                    <p class="text-[11px] text-slate-400">Complete customer submission and printing parameters</p>
                </div>
            </div>
            <button onclick="document.getElementById('requestModal').classList.add('hidden')" class="p-1.5 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="space-y-3.5 max-h-[75vh] overflow-y-auto pr-1">

            <!-- Word / DOCS Special Callout Notice -->
            <div id="rmDocsCalloutBox" class="hidden p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-amber-900 dark:text-amber-300 font-bold text-xs">
                        <span class="material-symbols-outlined text-[18px] text-amber-700 dark:text-amber-400">description</span>
                        <span>Word Document Request</span>
                    </div>
                    <span id="rmDocsChangeBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-200 text-amber-900 border border-amber-300">
                        Requested Changes
                    </span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-amber-950 dark:text-amber-200 block mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-amber-800 dark:text-amber-400">edit_note</span>
                        <span>Customer Instructions:</span>
                    </span>
                    <p id="rmInstructions" class="text-xs text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-900/90 p-3 rounded-xl border border-amber-200/80 dark:border-amber-900/40 whitespace-pre-wrap font-medium leading-relaxed shadow-2xs"></p>
                </div>
            </div>

            <!-- Generic Instructions Box -->
            <div id="rmGenericInstructionsBox" class="hidden p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-1.5">
                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1 uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px] text-terra-green">edit_note</span>
                    <span>Special Instructions</span>
                </span>
                <p id="rmGenericInstructions" class="text-xs text-slate-800 dark:text-slate-200 bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200/80 dark:border-slate-700/80 whitespace-pre-wrap"></p>
            </div>

            <!-- Customer Reference Images Gallery -->
            <div id="rmAttachmentsBox" class="hidden p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px] text-terra-green">photo_library</span>
                        <span>Customer Reference Photos</span>
                        <span id="rmAttachmentsCount" class="text-[10px] px-2 py-0.2 rounded-full bg-terra-green text-white font-bold"></span>
                    </span>
                    <span class="text-[10px] text-slate-400">Click photo to preview</span>
                </div>
                <div id="rmAttachmentsList" class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1"></div>
            </div>

            <!-- Core Specs Breakdown Card -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5 text-xs">
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Customer</span><span id="rmCustomer" class="font-bold text-slate-900 dark:text-white"></span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Main File</span><span id="rmFile" class="font-semibold text-terra-green truncate max-w-[220px]" title=""></span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Document Format</span><span id="rmDocType" class="font-bold uppercase text-slate-900 dark:text-white"></span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Print Configuration</span><span id="rmSpecs" class="font-semibold text-slate-800 dark:text-slate-200"></span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Color &amp; Paper Size</span><span id="rmColor" class="font-semibold text-slate-800 dark:text-slate-200"></span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Fulfillment Option</span><span id="rmFulfillment" class="font-semibold text-slate-800 dark:text-slate-200"></span></div>
                <div class="flex justify-between items-center border-t border-slate-200/60 dark:border-slate-700/60 pt-2"><span class="text-slate-500 dark:text-slate-400 font-medium">Total Cost</span><span id="rmAmount" class="font-mono font-bold text-terra-green text-sm">₱0.00</span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Down Payment</span><span id="rmDown" class="font-mono font-semibold text-emerald-600 dark:text-emerald-400">₱0.00</span></div>
                <div class="flex justify-between items-center"><span class="text-slate-500 dark:text-slate-400 font-medium">Submitted On</span><span id="rmDate" class="text-slate-500 dark:text-slate-400"></span></div>
            </div>

            <!-- Action Toolbar in Modal -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <a id="rmDownloadFileBtn" href="#" class="py-2.5 px-3 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center justify-center gap-1.5 text-xs shadow-xs">
                        <span class="material-symbols-outlined text-[16px] text-terra-green">download</span>
                        <span>Download File</span>
                    </a>
                    <button type="button" id="rmPrintReceiptBtn" class="py-2.5 px-3 bg-terra-green text-white rounded-xl font-bold hover:bg-emerald-700 transition-all flex items-center justify-center gap-1.5 text-xs shadow-xs cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                        <span>Print Receipt</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attachment Lightbox Modal -->
<div id="attachmentLightboxModal" class="hidden fixed inset-0 bg-black/85 backdrop-blur-sm z-[70] flex items-center justify-center p-4" onclick="if (event.target === this) closeAttachmentLightbox()">
    <div class="relative max-w-3xl w-full bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh] animate-scaleUp">
        <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <span id="lightboxFileName" class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-md">Reference Image Preview</span>
            <div class="flex items-center gap-2">
                <a id="lightboxDownloadBtn" href="#" target="_blank" download class="py-1 px-3 bg-terra-green/10 hover:bg-terra-green/20 text-terra-green rounded-lg transition-colors flex items-center gap-1 text-xs font-bold" title="Download Image">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Download</span>
                </a>
                <button type="button" onclick="closeAttachmentLightbox()" class="p-1 rounded-full hover:bg-slate-200 dark:hover:bg-slate-750 text-slate-400 hover:text-slate-700 dark:hover:text-white transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>
        <div class="p-4 flex-1 flex items-center justify-center overflow-auto bg-black/40 min-h-[300px]">
            <img id="lightboxImage" src="" alt="Reference Preview" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-lg">
        </div>
    </div>
</div>

<!-- Printing Settings Modal -->
<div id="printingSettingsModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-2xl w-full space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl my-auto animate-scaleUp">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5 text-terra-green font-bold">
                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">tune</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">Printing Service Settings</h3>
                    <p class="text-xs text-slate-400">Configure page rates, paper sizes, and binding fees</p>
                </div>
            </div>
            <button type="button" onclick="closePrintingSettingsModal()" class="p-1.5 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="printingSettingsForm" onsubmit="handleSavePrintingSettings(event)" class="space-y-4">
            <?= csrf_field() ?>

            <!-- Card 1: Per-Page Rates & Down Payment -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-terra-green">price_change</span>
                        <span>Page Rates &amp; Down Payment</span>
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Applied per printed page across all supported paper sizes</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">Color Page (₱)</label>
                        <div class="relative">
                            <input type="number" step="0.25" min="0" name="price_color_per_page" id="set_price_color_per_page" value="<?= esc($printingSettings['price_color_per_page'] ?? 5.00) ?>" required class="w-full py-2 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">₱</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">B&amp;W Page (₱)</label>
                        <div class="relative">
                            <input type="number" step="0.25" min="0" name="price_bw_per_page" id="set_price_bw_per_page" value="<?= esc($printingSettings['price_bw_per_page'] ?? 2.00) ?>" required class="w-full py-2 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">₱</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">Down Payment (%)</label>
                        <div class="relative">
                            <input type="number" step="5" min="0" max="100" name="down_payment_percent" id="set_down_payment_percent" value="<?= esc($printingSettings['down_payment_percent'] ?? 50) ?>" required class="w-full py-2 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Binding & Finishing Add-ons -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-terra-green">auto_stories</span>
                        <span>Binding Add-ons (Per Copy)</span>
                    </h4>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">Staple Binding (₱)</label>
                        <div class="relative">
                            <input type="number" step="0.50" min="0" name="price_staple" id="set_price_staple" value="<?= esc($printingSettings['price_staple'] ?? 10) ?>" required class="w-full py-2 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">₱</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-300 block mb-1">Spiral Binding (₱)</label>
                        <div class="relative">
                            <input type="number" step="0.50" min="0" name="price_spiral" id="set_price_spiral" value="<?= esc($printingSettings['price_spiral'] ?? 35) ?>" required class="w-full py-2 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:ring-1 focus:ring-terra-green">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">₱</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Supported Paper Sizes -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-terra-green">format_size</span>
                        <span>Supported Paper Sizes</span>
                    </h4>
                    <span class="text-[11px] text-slate-400">Disabled sizes will be unavailable in customer order form</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-52 overflow-y-auto p-1 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900">
                    <?php if (!empty($paperSizes)): ?>
                        <?php foreach ($paperSizes as $sKey => $sVal): ?>
                            <?php $isEnabled = !empty($sVal['is_enabled']); ?>
                            <label id="card_size_<?= esc($sKey) ?>" class="size-card flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800 <?= $isEnabled ? 'bg-slate-50/70 dark:bg-slate-800/60 border-terra-green/40 dark:border-terra-green/40' : 'opacity-60 bg-white dark:bg-slate-900' ?>">
                                <div class="flex items-center gap-2.5">
                                    <input type="hidden" name="paper_sizes[<?= esc($sKey) ?>][is_enabled]" value="0">
                                    <input type="checkbox" name="paper_sizes[<?= esc($sKey) ?>][is_enabled]" value="1" <?= $isEnabled ? 'checked' : '' ?> onchange="togglePaperSizeCard('<?= esc($sKey) ?>', this.checked)" class="rounded border-slate-300 dark:border-slate-600 text-terra-green focus:ring-terra-green h-4 w-4 cursor-pointer">
                                    <div>
                                        <div class="text-xs font-bold text-slate-900 dark:text-white"><?= esc($sVal['label'] ?? strtoupper($sKey)) ?></div>
                                        <span class="text-[10px] text-slate-400 font-mono uppercase"><?= esc($sKey) ?></span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?= $isEnabled ? 'bg-terra-green/10 text-terra-green' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' ?>" id="badge_size_<?= esc($sKey) ?>">
                                    <?= $isEnabled ? 'Available' : 'Disabled' ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="settingsFeedback" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closePrintingSettingsModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="btnSavePrintingSettings" class="px-5 py-2.5 bg-terra-green text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPrintingSettingsModal() {
        document.getElementById('printingSettingsModal').classList.remove('hidden');
    }

    function closePrintingSettingsModal() {
        document.getElementById('printingSettingsModal').classList.add('hidden');
    }

    function toggleWorkbenchCards() {
        const grid = document.getElementById('workbenchCardsGrid');
        const txt = document.getElementById('workbenchToggleText');
        const icon = document.getElementById('workbenchToggleIcon');
        if (!grid) return;

        if (grid.classList.contains('hidden')) {
            grid.classList.remove('hidden');
            if (txt) txt.textContent = 'Collapse';
            if (icon) icon.textContent = 'expand_less';
        } else {
            grid.classList.add('hidden');
            if (txt) txt.textContent = 'Expand';
            if (icon) icon.textContent = 'expand_more';
        }
    }

    function togglePaperSizeCard(key, isChecked) {
        const card = document.getElementById('card_size_' + key);
        const badge = document.getElementById('badge_size_' + key);
        if (!card) return;
        if (isChecked) {
            card.classList.remove('opacity-60', 'bg-white', 'dark:bg-slate-900');
            card.classList.add('bg-slate-50/70', 'dark:bg-slate-800/60', 'border-terra-green/40');
            if (badge) {
                badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-terra-green/10 text-terra-green';
                badge.textContent = 'Available';
            }
        } else {
            card.classList.remove('bg-slate-50/70', 'dark:bg-slate-800/60', 'border-terra-green/40');
            card.classList.add('opacity-60', 'bg-white', 'dark:bg-slate-900');
            if (badge) {
                badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400';
                badge.textContent = 'Disabled';
            }
        }
    }

    async function handleSavePrintingSettings(e) {
        e.preventDefault();
        const form = document.getElementById('printingSettingsForm');
        const btn = document.getElementById('btnSavePrintingSettings');
        const fb = document.getElementById('settingsFeedback');

        btn.disabled = true;
        btn.classList.add('opacity-70');
        fb.className = 'hidden';

        try {
            const formData = new FormData(form);
            const res = await fetch('<?= base_url('tenant/printing/settings/save') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                fb.className = 'p-3 rounded-xl text-xs font-semibold bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 border border-emerald-500/20';
                fb.innerHTML = `<span class="material-symbols-outlined text-[16px]">check_circle</span> <span>${data.message || 'Settings saved successfully!'}</span>`;
                setTimeout(() => {
                    closePrintingSettingsModal();
                    window.location.reload();
                }, 800);
            } else {
                fb.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-800 dark:text-rose-300 border border-rose-500/20';
                fb.textContent = data.error || 'Failed to save settings.';
            }
        } catch (err) {
            fb.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-800 dark:text-rose-300 border border-rose-500/20';
            fb.textContent = 'Network error while saving settings.';
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-70');
        }
    }

    let activeMenu = null;

    function closeMenus() {
        if (activeMenu) {
            activeMenu.classList.add('hidden');
            activeMenu = null;
        }
        document.querySelectorAll('.more-toggle[aria-expanded="true"]').forEach((b) => b.setAttribute('aria-expanded', 'false'));
    }

    function toggleDropdown(btn) {
        const id   = btn.dataset.row;
        const menu = document.getElementById('more-menu-' + id);
        if (!menu) return;

        if (activeMenu === menu) {
            closeMenus();
            return;
        }
        closeMenus();

        document.body.appendChild(menu);
        menu.classList.remove('hidden');
        menu.style.position = 'fixed';
        menu.style.left = '';
        menu.style.right = '';
        menu.style.top = '';
        menu.style.bottom = '';

        const rect       = btn.getBoundingClientRect();
        const gap        = 8;
        const menuWidth  = menu.offsetWidth;
        const menuHeight = menu.offsetHeight;

        const left = rect.left + menuWidth <= window.innerWidth - gap
            ? rect.left
            : Math.max(gap, window.innerWidth - menuWidth - gap);
        menu.style.left = left + 'px';

        if (rect.bottom + gap + menuHeight <= window.innerHeight - gap) {
            menu.style.top = (rect.bottom + gap) + 'px';
        } else {
            menu.style.bottom = (window.innerHeight - rect.top + gap) + 'px';
        }

        activeMenu = menu;
        btn.setAttribute('aria-expanded', 'true');
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.more-menu') && !e.target.closest('.more-toggle')) {
            closeMenus();
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMenus();
    });
    window.addEventListener('scroll', closeMenus, true);
    window.addEventListener('resize', closeMenus);

    function setDocTypeFilter(type) {
        const input = document.getElementById('filterDocTypeInput');
        if (input) {
            input.value = type;
            const form = document.getElementById('printingFiltersForm');
            if (form) form.submit();
        }
    }

    let searchDebounceTimer = null;
    function handleSearchInput(input) {
        clearTimeout(searchDebounceTimer);
        const clearBtn = document.getElementById('searchClearBtn');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !input.value.trim());
        }
        searchDebounceTimer = setTimeout(() => {
            sessionStorage.setItem('printingSearchActive', 'true');
            document.getElementById('printingFiltersForm').submit();
        }, 400);
    }

    function clearSearchInput() {
        const input = document.getElementById('printingSearchInput');
        if (input) {
            input.value = '';
            sessionStorage.removeItem('printingSearchActive');
            document.getElementById('printingFiltersForm').submit();
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('printingSearchInput');
        if (searchInput && sessionStorage.getItem('printingSearchActive') === 'true') {
            sessionStorage.removeItem('printingSearchActive');
            searchInput.focus();
            const len = searchInput.value.length;
            searchInput.setSelectionRange(len, len);
        }
    });

    function openAttachmentLightbox(url, name) {
        document.getElementById('lightboxImage').src = url;
        document.getElementById('lightboxFileName').textContent = name || 'Reference Photo Preview';
        document.getElementById('lightboxDownloadBtn').href = url;
        document.getElementById('attachmentLightboxModal').classList.remove('hidden');
    }

    function closeAttachmentLightbox() {
        document.getElementById('attachmentLightboxModal').classList.add('hidden');
        document.getElementById('lightboxImage').src = '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const lb = document.getElementById('attachmentLightboxModal');
            if (lb && !lb.classList.contains('hidden')) {
                closeAttachmentLightbox();
                return;
            }
            const rm = document.getElementById('requestModal');
            if (rm && !rm.classList.contains('hidden')) {
                rm.classList.add('hidden');
            }
        }
    });

    function openRequestDetails(btn) {
        if (!btn) return;
        const reqNum = btn.dataset.request || '';
        document.getElementById('rmRequest').textContent = '#' + reqNum.replace(/^#+/, '');
        document.getElementById('rmCustomer').textContent = btn.dataset.customer || '';
        
        const fileElem = document.getElementById('rmFile');
        const fileName = btn.dataset.file || 'Document.pdf';
        fileElem.textContent = fileName;
        fileElem.title = fileName;

        const docType = (btn.dataset.doctype || 'PDF').toUpperCase();
        document.getElementById('rmDocType').textContent = docType;

        const changeType = btn.dataset.changetype || 'Print As-Is';
        const inst = (btn.dataset.instructions || '').trim();

        // Word / DOCS Callout Box
        const docsBox = document.getElementById('rmDocsCalloutBox');
        const docsBadge = document.getElementById('rmDocsChangeBadge');
        const rmInst = document.getElementById('rmInstructions');
        const genericBox = document.getElementById('rmGenericInstructionsBox');
        const genericInst = document.getElementById('rmGenericInstructions');

        if (docType === 'DOCX') {
            docsBox.classList.remove('hidden');
            docsBadge.textContent = changeType;
            if (changeType.toLowerCase().includes('change')) {
                docsBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-200 text-amber-900 border border-amber-300';
            } else {
                docsBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300';
            }
            rmInst.textContent = inst !== '' ? inst : 'No special instructions provided (Customer selected "Print As-Is").';
            genericBox.classList.add('hidden');
        } else {
            docsBox.classList.add('hidden');
            if (inst !== '') {
                genericBox.classList.remove('hidden');
                genericInst.textContent = inst;
            } else {
                genericBox.classList.add('hidden');
            }
        }

        document.getElementById('rmSpecs').textContent = btn.dataset.specs || '';
        document.getElementById('rmColor').textContent = btn.dataset.color || '';
        document.getElementById('rmFulfillment').textContent = btn.dataset.fulfillment || '';
        document.getElementById('rmAmount').textContent = '₱' + (btn.dataset.amount || '0.00');
        document.getElementById('rmDown').textContent = '₱' + (btn.dataset.down || '0.00');
        document.getElementById('rmDate').textContent = btn.dataset.date || '';

        // Reference Attachments / Photos Gallery
        const attBox = document.getElementById('rmAttachmentsBox');
        const attList = document.getElementById('rmAttachmentsList');
        const attCount = document.getElementById('rmAttachmentsCount');
        attList.innerHTML = '';

        let attachments = [];
        try {
            attachments = JSON.parse(btn.dataset.attachments || '[]');
        } catch (e) {
            attachments = [];
        }

        if (attachments && attachments.length > 0) {
            attBox.classList.remove('hidden');
            attCount.textContent = attachments.length;
            attachments.forEach((att, idx) => {
                const imgPath = att.image_url || att.file_path || '';
                if (!imgPath) return;
                const fullUrl = imgPath.startsWith('http') ? imgPath : ('<?= base_url() ?>/' + imgPath.replace(/^\/+/, ''));
                const safeName = att.file_name || imgPath.split('/').pop() || ('Reference Photo ' + (idx + 1));

                const card = document.createElement('div');
                card.className = 'group relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-2xs hover:shadow-md transition-all flex flex-col';
                card.innerHTML = `
                    <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative cursor-pointer flex items-center justify-center">
                        <img src="${fullUrl}" alt="${safeName}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML='<div class=\\'flex flex-col items-center justify-center text-slate-400 p-2\\'><span class=\\'material-symbols-outlined text-2xl\\'>broken_image</span><span class=\\'text-[10px]\\'>Preview unavailable</span></div>';">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 pointer-events-none">
                            <span class="p-1.5 rounded-full bg-white text-terra-green shadow">
                                <span class="material-symbols-outlined text-[16px] block">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-1.5 flex items-center justify-between gap-1 text-[11px] bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
                        <span class="truncate font-semibold text-slate-800 dark:text-slate-200 flex-1" title="${safeName}">${safeName}</span>
                        <a href="${fullUrl}" download="${safeName}" target="_blank" class="p-1 text-slate-400 hover:text-terra-green transition-colors shrink-0" title="Download Reference Photo">
                            <span class="material-symbols-outlined text-[15px]">download</span>
                        </a>
                    </div>
                `;

                const imgArea = card.querySelector('.aspect-video');
                if (imgArea) {
                    imgArea.onclick = () => openAttachmentLightbox(fullUrl, safeName);
                }

                attList.appendChild(card);
            });
        } else {
            attBox.classList.add('hidden');
            attCount.textContent = '';
        }

        const reqId = btn.dataset.id || '';
        const rmDlBtn = document.getElementById('rmDownloadFileBtn');
        if (rmDlBtn) {
            rmDlBtn.href = reqId ? ('<?= base_url('tenant/printing/download') ?>/' + reqId) : '#';
        }

        const rmPrintBtn = document.getElementById('rmPrintReceiptBtn');
        if (rmPrintBtn) {
            rmPrintBtn.onclick = function() {
                window.open('<?= base_url('tenant/printing/receipt') ?>/' + encodeURIComponent(btn.dataset.request || ''), '_blank');
            };
        }

        document.getElementById('requestModal').classList.remove('hidden');
    }

    // Real-time Printing KPI & Metrics Synchronization
    window.addEventListener('blax:printing-summary-update', function(e) {
        const summary = e.detail;
        if (!summary) return;

        const kpiNew = document.getElementById('kpi-printing-new');
        if (kpiNew && summary.new !== undefined) {
            kpiNew.textContent = Number(summary.new).toLocaleString();
        }

        const kpiProd = document.getElementById('kpi-printing-in-production');
        if (kpiProd && summary.in_production !== undefined) {
            kpiProd.textContent = Number(summary.in_production).toLocaleString();
        }

        const kpiPickup = document.getElementById('kpi-printing-ready-pickup');
        if (kpiPickup && summary.ready_for_pickup !== undefined) {
            kpiPickup.textContent = Number(summary.ready_for_pickup).toLocaleString();
        }

        const kpiDelivery = document.getElementById('kpi-printing-ready-delivery');
        if (kpiDelivery && summary.ready_for_delivery !== undefined) {
            kpiDelivery.textContent = Number(summary.ready_for_delivery).toLocaleString();
        }

        const kpiComp = document.getElementById('kpi-printing-completed');
        if (kpiComp && summary.completed !== undefined) {
            kpiComp.textContent = Number(summary.completed).toLocaleString();
        }
    });

    // --- Active Production Workbench: realtime job card injection ------------
    const WB_BINDING = { none: 'Standard', spiral: 'Spiral Binding', hardcover: 'Hardcover', staple: 'Stapled' };
    const wbEsc = (v) => String(v === null || v === undefined ? '' : v)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const wbBindingLabel = (b) => WB_BINDING[String(b || 'none').toLowerCase()] || 'Standard';

    function wbSpecLine(pr) {
        const pages = parseInt(pr.page_count || 1, 10) || 1;
        const copies = parseInt(pr.copies || 1, 10) || 1;
        return pages + ' pages \u2022 ' + copies + ' ' + (copies === 1 ? 'copy' : 'copies') + ' \u2022 ' + wbBindingLabel(pr.binding_option);
    }

    // Mirrors the server-rendered card markup, including the fulfillment-aware primary
    // action: doorstep delivery => blue "Mark In Transit" (ready_for_delivery),
    // store pick-up => green "Ready for Pick-up" (ready_for_pickup).
    function buildWorkbenchCard(pr) {
        const id = parseInt(pr.id, 10);
        const reqNum = pr.request_number || ('PR-' + id);
        const custName = pr.customer_name || String((pr.first_name || '') + ' ' + (pr.last_name || '')).trim() || 'Customer';
        const initials = String(pr.customer_initials || custName.slice(0, 2)).toUpperCase() || 'CU';
        const isDelivery = (pr.fulfillment_method || 'pickup') === 'delivery';
        const isNew = String(pr.status || 'new') === 'new';
        const fileName = pr.file_name || 'Document.pdf';
        const docType = String(pr.document_type || 'PDF').toUpperCase();
        const pages = parseInt(pr.page_count || 1, 10) || 1;
        const copies = parseInt(pr.copies || 1, 10) || 1;
        const colorMode = (String(pr.color_mode || '').toLowerCase() === 'color' ? 'Color' : 'B&W');
        const paperSize = pr.paper_size || 'Letter';
        const binding = wbBindingLabel(pr.binding_option);
        const total = parseFloat((pr.total_amount !== undefined ? pr.total_amount : pr.total_price) || 0) || 0;
        const down = parseFloat(pr.down_payment || 0) || 0;
        const balance = Math.max(0, total - down);
        const shipFee = parseFloat(pr.shipping_fee || 0) || 0;
        const shipKm = parseFloat(pr.distance_km || 0) || 0;
        const specs = wbSpecLine(pr);
        const attachments = wbEsc(JSON.stringify(pr.attachments || []));

        const primaryAction = isNew
            ? `<form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" onsubmit="return confirm('Accept Request #${wbEsc(reqNum)} and move it onto the printing desk?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="request_id" value="${id}">
                    <input type="hidden" name="status" value="in_production">
                    <button type="submit" class="w-full py-2 px-3 bg-terra-amber hover:bg-amber-500 active:scale-[0.98] text-slate-950 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                        <span>Accept &amp; Print</span>
                    </button>
                </form>`
            : isDelivery
            ? `<form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" onsubmit="return confirm('Dispatch Request #${wbEsc(reqNum)} as In Transit for delivery?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="request_id" value="${id}">
                    <input type="hidden" name="status" value="ready_for_delivery">
                    <button type="submit" class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                        <span>Mark In Transit</span>
                    </button>
                </form>`
            : `<form action="<?= base_url('tenant/printing/update-status') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="request_id" value="${id}">
                    <input type="hidden" name="status" value="ready_for_pickup">
                    <button type="submit" class="w-full py-2 px-3 bg-terra-green hover:bg-emerald-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">storefront</span>
                        <span>Ready for Pick-up</span>
                    </button>
                </form>`;

        return `
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm rounded-2xl p-4 border border-amber-500/25 shadow-xs hover:border-amber-500/50 transition-all flex flex-col justify-between gap-3 group animate-fadeIn">
                <div>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    onclick="openRequestDetails(this)"
                                    class="font-mono text-xs font-bold text-terra-green hover:underline cursor-pointer"
                                    data-id="${id}"
                                    data-request="${wbEsc(reqNum)}"
                                    data-customer="${wbEsc(custName)}"
                                    data-file="${wbEsc(fileName)}"
                                    data-doctype="${wbEsc(docType)}"
                                    data-changetype="${wbEsc(pr.doc_change_type || 'Print As-Is')}"
                                    data-instructions="${wbEsc(pr.special_instructions || '')}"
                                    data-attachments="${attachments}"
                                    data-specs="${wbEsc(specs)}"
                                    data-color="${wbEsc(colorMode)} \u2022 ${wbEsc(paperSize)}"
                                    data-fulfillment="${isDelivery ? 'Delivery' : 'Pickup'}"
                                    data-printer="Not assigned"
                                    data-progress="0"
                                    data-amount="${total.toFixed(2)}"
                                    data-down="${down.toFixed(2)}"
                                    data-date="${wbEsc(pr.placed_at_fmt || 'Just now')}">
                                #${wbEsc(reqNum)}
                            </button>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold ${isNew ? 'bg-amber-500 text-white border border-amber-500 animate-pulse' : 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20'}">
                                ${isNew ? 'New' : 'Printing'}
                            </span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium">Just now</span>
                    </div>

                    <div class="mt-2.5 flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center text-xs font-bold shrink-0">
                            ${wbEsc(initials)}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate">${wbEsc(custName)}</div>
                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 text-xs mt-0.5">
                                <span class="material-symbols-outlined text-[14px] text-slate-400 shrink-0">description</span>
                                <span class="truncate font-medium text-[11px]" title="${wbEsc(fileName)}">${wbEsc(fileName)}</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold ${docType === 'DOCX' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300'}">
                                    ${wbEsc(docType)}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-700 dark:text-slate-300">
                            ${wbEsc(colorMode.toUpperCase())} \u2022 ${wbEsc(paperSize)}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                            ${pages} pgs \u00d7 ${copies} ${copies === 1 ? 'copy' : 'copies'}
                        </span>
                        ${binding !== 'Standard' ? `<span class="px-2 py-0.5 rounded-md bg-terra-green/10 text-terra-green border border-terra-green/20 text-[10px] font-bold">${wbEsc(binding)}</span>` : ''}
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    ${isDelivery && shipFee > 0 ? `
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500 dark:text-slate-400">Shipping (${shipKm.toFixed(1)} km):</span>
                            <span class="font-mono font-bold text-sky-700 dark:text-sky-400">\u20b1${shipFee.toFixed(2)}</span>
                        </div>` : ''}
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-500 dark:text-slate-400">Balance:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">\u20b1${balance.toFixed(2)}</span>
                    </div>
                    ${primaryAction}
                    <div class="grid grid-cols-3 gap-1 pt-0.5">
                        <button type="button"
                                onclick="openRequestDetails(this.closest('.group').querySelector('button[data-request]'))"
                                class="py-1 px-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[13px] text-terra-green">visibility</span>
                            <span>Ticket</span>
                        </button>
                        <a href="<?= base_url('tenant/printing/download') ?>/${id}" class="py-1 px-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-[13px] text-terra-green">download</span>
                            <span>File</span>
                        </a>
                        <a href="<?= base_url('tenant/printing/receipt') ?>/${id}" target="_blank" class="py-1 px-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-[13px] text-terra-green">receipt_long</span>
                            <span>Receipt</span>
                        </a>
                    </div>
                </div>
            </div>`;
    }

    // Reveals the (possibly empty) workbench shelf and prepends a live job card.
    function injectWorkbenchCard(pr) {
        const grid = document.getElementById('workbenchCardsGrid');
        const shell = document.getElementById('productionWorkbenchContainer');
        if (!grid || !shell) return false;
        if (grid.querySelector('button[data-id="' + parseInt(pr.id, 10) + '"]')) return false;

        shell.classList.remove('hidden');
        grid.insertAdjacentHTML('afterbegin', buildWorkbenchCard(pr));

        const badge = document.getElementById('workbenchActiveCount');
        if (badge) badge.textContent = String((parseInt(badge.textContent || '0', 10) || 0) + 1);
        return true;
    }

    // Real-time Event Listener for Incoming New Printing Requests
    window.addEventListener('blax:new-printing', function(e) {
        const pr = e.detail;
        if (!pr || !pr.id) return;

        // Check if row already exists
        if (document.querySelector(`button[data-id="${pr.id}"]`)) return;

        // Freshly submitted jobs and jobs already on the desk both belong on the shelf.
        if (pr.status === 'in_production' || pr.status === 'new') {
            injectWorkbenchCard(pr);
        }

        // Auto reload or prepend to table if on 'new' or 'all' status view
        const currentStatus = '<?= esc($activeStatus) ?>';
        const tbody = document.getElementById('printingTableBody');
        if (tbody && (currentStatus === '' || currentStatus === 'new')) {
            const reqNum = pr.request_number || ('PR-' + pr.id);
            const cleanReq = '#' + reqNum.replace(/^#+/, '');
            const custName = pr.customer_name || (String((pr.first_name || '') + ' ' + (pr.last_name || '')).trim() || 'Customer');
            const custPhone = pr.customer_phone || pr.phone || pr.customer_email || pr.email || 'Direct';
            const custInitials = pr.customer_initials || ((pr.first_name ? pr.first_name.charAt(0) : 'C') + (pr.last_name ? pr.last_name.charAt(0) : 'U')).toUpperCase();
            const isDeliv = (pr.fulfillment_method || 'pickup') === 'delivery';
            const fileName = pr.file_name || 'Document.pdf';
            const docType = (pr.document_type || 'PDF').toUpperCase();
            const totalPages = parseInt(pr.page_count || 1);
            const copies = parseInt(pr.copies || 1);
            const colorStr = (pr.color_mode === 'color' ? 'Color' : 'B&W') + ' • ' + (pr.paper_size || 'Letter');
            const bindingStr = pr.binding_option ? (pr.binding_option.charAt(0).toUpperCase() + pr.binding_option.slice(1)) : 'Standard';
            const specsLine = totalPages + ' pages • ' + copies + ' ' + (copies === 1 ? 'copy' : 'copies') + ' • ' + bindingStr;
            const totalAmt = parseFloat(pr.total_price || pr.total_amount || 0);
            const downPayment = parseFloat(pr.down_payment || 0);
            const balance = Math.max(0, totalAmt - downPayment);
            const shipFee = parseFloat(pr.shipping_fee || 0);
            const shipKm = parseFloat(pr.distance_km || 0);

            const safeCustName = custName.replace(/"/g, '&quot;');
            const safeFile = fileName.replace(/"/g, '&quot;');
            const safeInst = (pr.special_instructions || '').replace(/"/g, '&quot;');
            const safeAtts = JSON.stringify(pr.attachments || []).replace(/"/g, '&quot;');

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors bg-amber-50/40 dark:bg-amber-950/20 animate-fadeIn';
            tr.innerHTML = `
                <td class="px-5 py-3.5 whitespace-nowrap">
                    <button type="button"
                            onclick="openRequestDetails(this)"
                            class="font-mono text-xs font-bold text-terra-green hover:text-emerald-700 bg-terra-green/10 hover:bg-terra-green/20 border border-terra-green/25 px-2.5 py-1 rounded-xl inline-flex items-center gap-1.5 transition-all active:scale-95 shadow-2xs"
                            title="View Full Job Ticket"
                            data-id="${pr.id}"
                            data-request="${reqNum}"
                            data-customer="${safeCustName}"
                            data-file="${safeFile}"
                            data-doctype="${docType}"
                            data-changetype="${pr.doc_change_type === 'has_changes' ? 'Requested Changes' : 'Print As-Is'}"
                            data-instructions="${safeInst}"
                            data-attachments="${safeAtts}"
                            data-specs="${specsLine}"
                            data-color="${colorStr}"
                            data-fulfillment="${isDeliv ? 'Delivery' : 'Pickup'}"
                            data-printer="Not assigned"
                            data-progress="0"
                            data-amount="${totalAmt.toFixed(2)}"
                            data-down="${downPayment.toFixed(2)}"
                            data-date="${pr.placed_at_fmt || 'Just now'}">
                        <span class="material-symbols-outlined text-[13px]">tag</span>
                        <span>${cleanReq}</span>
                    </button>
                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-ping ml-1" title="New Request"></span>
                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-1 flex items-center gap-1.5 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                        <span>Just now</span>
                    </div>
                </td>
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-2.5 min-w-[140px]">
                        <div class="w-8 h-8 rounded-full bg-terra-green/10 text-terra-green flex items-center justify-center text-xs font-bold shrink-0 ring-2 ring-slate-100 dark:ring-slate-800">
                            <span>${custInitials}</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 dark:text-white truncate block leading-tight max-w-[140px]">${custName}</span>
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="inline-flex items-center gap-0.5 text-[9px] font-bold ${isDeliv ? 'text-blue-600' : 'text-emerald-600'}">
                                    <span class="material-symbols-outlined text-[12px]">${isDeliv ? 'local_shipping' : 'storefront'}</span> ${isDeliv ? 'Delivery' : 'Pickup'}
                                </span>
                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                <span class="text-[10px] text-slate-400 truncate max-w-[90px]">${custPhone}</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-terra-green shrink-0 border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[18px]">description</span>
                        </div>
                        <div class="min-w-0 max-w-[170px]">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate block" title="${safeFile}">${fileName}</span>
                            <span class="px-1.5 py-0.2 rounded-md text-[9px] font-bold tracking-wider uppercase ${docType === 'DOCX' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'}">
                                ${docType}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5">
                    <div class="flex flex-col gap-1 min-w-[160px]">
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 w-fit">
                            ${colorStr}
                        </span>
                        <div class="text-[10px] text-slate-500 font-medium">
                            ${specsLine}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5">
                    <div class="min-w-[110px]">
                        <div class="font-mono text-xs font-black text-slate-900 dark:text-white">₱${totalAmt.toFixed(2)}</div>
                        ${isDeliv && shipFee > 0 ? `<div class="text-[10px] text-sky-700 dark:text-sky-400 font-mono mt-0.5">Ship ₱${shipFee.toFixed(2)}${shipKm > 0 ? ' · ' + shipKm.toFixed(1) + ' km' : ''}</div>` : ''}
                        <div class="text-[10px] text-slate-500 mt-0.5">Bal: <b class="font-mono text-amber-600">₱${balance.toFixed(2)}</b></div>
                    </div>
                </td>
                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-700 border border-amber-500/25">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> New
                    </span>
                </td>
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <form action="<?= base_url('tenant/printing/update-status') ?>" method="POST" class="inline-block">
                        <?= csrf_field() ?>
                        <input type="hidden" name="request_id" value="${pr.id}">
                        <input type="hidden" name="status" value="in_production">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-terra-green hover:bg-emerald-700 text-white text-xs font-bold transition-all flex items-center gap-1 shadow-2xs cursor-pointer active:scale-95">
                            <span class="material-symbols-outlined text-[15px]">play_arrow</span>
                            <span>Accept &amp; Print</span>
                        </button>
                    </form>
                </td>
            `;

            // If table has empty placeholder, remove it
            const emptyTr = tbody.querySelector('td[colspan="7"]');
            if (emptyTr) emptyTr.parentElement.remove();

            tbody.insertBefore(tr, tbody.firstChild);
        }
    });
</script>

<?= $this->endSection() ?>
