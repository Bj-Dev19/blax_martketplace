<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<div class="flex-1 flex flex-col md:flex-row items-start w-full min-h-[calc(100vh-72px)] bg-slate-50/50 dark:bg-slate-950 transition-colors duration-200">

    <?= view('components/profile_sidebar', ['activeNav' => 'printing']) ?>

    <!-- Main Content Area -->
    <main class="flex-1 w-full min-w-0 p-4 sm:p-6 md:p-8">

        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 pb-28">

            <!-- Calm Page Header & Filter Tabs -->
            <header class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-display font-bold text-slate-900 dark:text-white tracking-tight">Printing Requests</h1>
                        <?php if (!empty($requests)): ?>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                <?= count($requests) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <a href="<?= base_url('printing-services') ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-terra-green hover:bg-[#3d7a33] text-white text-xs sm:text-sm font-bold shadow-xs transition-all active:scale-95 shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>New Request</span>
                    </a>
                </div>

                <?php if (!empty($requests)): ?>
                    <?php
                        $prCountAll = count($requests);
                        $prCountActive = 0;
                        $prCountCompleted = 0;
                        $prCountCancelled = 0;
                        foreach ($requests as $rItem) {
                            $rSt = strtolower((string) ($rItem['status'] ?? 'new'));
                            if ($rSt === 'cancelled') {
                                $prCountCancelled++;
                            } elseif ($rSt === 'completed') {
                                $prCountCompleted++;
                            } else {
                                $prCountActive++;
                            }
                        }
                    ?>
                    <nav class="flex border-b border-slate-200/80 dark:border-slate-800 gap-2 sm:gap-4 overflow-x-auto no-scrollbar pb-px">
                        <button type="button" class="pr-tab px-3.5 py-2.5 text-xs sm:text-sm font-bold text-terra-green border-b-2 border-terra-green transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="all" data-label="printing requests">
                            <span>All</span>
                            <span class="px-2 py-0.5 bg-terra-green/10 text-terra-green rounded-full text-[10px] font-mono font-bold"><?= $prCountAll ?></span>
                        </button>
                        <button type="button" class="pr-tab px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="active" data-label="active printing requests">
                            <span>Active</span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-mono font-bold"><?= $prCountActive ?></span>
                        </button>
                        <button type="button" class="pr-tab px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="completed" data-label="completed printing requests">
                            <span>Completed</span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-mono font-bold"><?= $prCountCompleted ?></span>
                        </button>
                        <button type="button" class="pr-tab px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="cancelled" data-label="cancelled printing requests">
                            <span>Cancelled</span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-mono font-bold"><?= $prCountCancelled ?></span>
                        </button>
                    </nav>
                <?php endif; ?>
            </header>

            <?php if (!empty($requests)): ?>

                <div class="flex flex-col gap-4 sm:gap-5">
                <?php foreach ($requests as $req): ?>

                    <?php
                        $reqStatus   = strtolower((string) ($req['status'] ?? 'new'));
                        $reqBinding  = $req['binding_option'] ?? 'none';
                        $reqService  = match ($reqBinding) { 'spiral' => 'Spiral Binding', 'hardcover' => 'Hardcover Binding', 'staple' => 'Staple Binding', default => 'Document Printing' };
                        $reqIcon     = match ($reqBinding) { 'hardcover' => 'menu_book', 'spiral' => 'auto_stories', default => 'description' };
                        $reqGroup    = in_array($reqStatus, ['new', 'in_production', 'ready_for_pickup', 'ready_for_delivery', 'in_transit']) ? 'active' : $reqStatus;
                        $reqLabel    = humanize_status($reqStatus);
                        $reqPill     = match ($reqStatus) {
                            'ready_for_pickup', 'ready_for_delivery' => 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
                            'in_transit'                             => 'bg-sky-500/10 text-sky-700 border-sky-500/20 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60',
                            'in_production', 'new'                   => 'bg-terra-green/10 text-terra-green border-terra-green/20 dark:bg-terra-green/20 dark:text-emerald-300',
                            'completed'                              => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                            'cancelled'                              => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
                            default                                  => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300',
                        };
                        $reqPriceLbl = match ($reqStatus) { 'completed' => 'Paid', 'cancelled' => 'Total', 'new', 'in_production' => 'Estimated Total', default => 'Total' };
                        $reqPrice    = number_format((float) ($req['total_price'] ?? 0), 2);
                        $reqCopies   = (int) ($req['copies'] ?? 1);
                        $reqPages    = (int) ($req['page_count'] ?? 0);
                        $reqSpec     = implode(' · ', array_filter([
                            $req['paper_stock'] ?? null,
                            ($reqPages > 0 ? $reqPages . ' Page' . ($reqPages !== 1 ? 's' : '') : null),
                            ($reqCopies > 1 ? $reqCopies . ' Copies' : null),
                            ($req['paper_size'] ?? null) ? strtoupper(($req['paper_size'] ?? 'A4')) : null,
                            $req['color_mode'] ? strtoupper($req['color_mode']) : null,
                            ($reqBinding !== 'none' ? ucfirst($reqBinding) : null),
                        ]));
                        $reqPrinter  = $req['printer_assigned'] ?? '';
                        $isPickup    = (($req['fulfillment_method'] ?? 'pickup') === 'pickup');
                        $reqMethod   = $isPickup ? 'Pickup' : 'Delivery';
                        $reqCreated  = !empty($req['created_at']) ? date('M d, Y', strtotime($req['created_at'])) : '';
                        $reqDone     = !empty($req['completed_at']) ? date('M d, Y', strtotime($req['completed_at'])) : '';
                        $reqProgress = (int) ($req['progress_percent'] ?? 0);
                        $shopName    = !empty($req['shop_name']) ? $req['shop_name'] : 'Printing Shop';
                        $shopAddress = !empty($req['shop_address']) ? $req['shop_address'] : 'Poblacion, Polomolok';
                        $isPaid      = !empty($req['payment_status']) && strtolower($req['payment_status']) === 'paid';
                    ?>

                    <div class="pr-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-2xs transition-all" data-group="<?= esc($reqGroup) ?>">

                        <!-- 1. Top Header Bar -->
                        <div class="px-4 py-3.5 sm:px-5 bg-slate-50/60 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[18px]"><?= esc($reqIcon) ?></span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate"><?= esc($req['file_name'] ?? 'Document.pdf') ?></h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                        <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">#<?= esc($req['request_number'] ?? ('PR-' . $req['id'])) ?></span>
                                        <span>&bull;</span>
                                        <span class="truncate"><?= esc($shopName) ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap shrink-0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    <span class="material-symbols-outlined text-[13px]"><?= $isPickup ? 'storefront' : 'local_shipping' ?></span>
                                    <span><?= $isPickup ? 'Pick-up' : 'Delivery' ?></span>
                                </span>

                                <span class="px-2.5 py-0.5 border text-[11px] font-bold rounded-full <?= esc($reqPill) ?>"><?= esc($reqLabel) ?></span>
                            </div>
                        </div>

                        <!-- 2. Body: Specs, Stepper & Reference Photos -->
                        <div class="p-4 sm:p-5 space-y-4">

                            <!-- Compact Spec Summary -->
                            <div class="flex items-center justify-between gap-3 text-xs text-slate-600 dark:text-slate-300">
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200"><?= esc($reqService) ?></span>
                                    <?php if ($reqSpec !== ''): ?>
                                        <span class="mx-1.5 text-slate-300 dark:text-slate-600">&bull;</span>
                                        <span class="text-slate-500 dark:text-slate-400"><?= esc($reqSpec) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($reqCreated !== ''): ?>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 shrink-0"><?= esc($reqCreated) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Fulfillment Progress Stepper -->
                            <?php if ($reqStatus === 'cancelled'): ?>
                                <div class="px-3.5 py-2.5 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center gap-2 text-rose-600 dark:text-rose-400 text-xs font-medium">
                                    <span class="material-symbols-outlined text-[16px] shrink-0">cancel</span>
                                    <span>Printing request cancelled</span>
                                </div>
                            <?php else: ?>
                                <?php
                                    if ($isPickup) {
                                        $prSteps = [
                                            ['key' => 'new',              'label' => 'Placed'],
                                            ['key' => 'in_production',    'label' => 'Printing'],
                                            ['key' => 'ready_for_pickup', 'label' => 'Pickup Ready'],
                                            ['key' => 'completed',        'label' => 'Completed'],
                                        ];
                                    } else {
                                        $prSteps = [
                                            ['key' => 'new',                'label' => 'Placed'],
                                            ['key' => 'in_production',      'label' => 'Printing'],
                                            ['key' => 'ready_for_delivery', 'label' => 'On the Way'],
                                            ['key' => 'completed',          'label' => 'Completed'],
                                        ];
                                    }

                                    $prStepOrder = [];
                                    foreach ($prSteps as $si => $st) { $prStepOrder[$st['key']] = $si; }
                                    $prStepOrder['in_transit'] = $prStepOrder['ready_for_delivery'] ?? 2;
                                    $prCurrentIdx = $prStepOrder[$reqStatus] ?? 0;
                                ?>
                                <div class="pt-1 pb-0.5">
                                    <div class="grid grid-cols-4 w-full">
                                        <?php foreach ($prSteps as $si => $st):
                                            $isDone   = ($si < $prCurrentIdx) || ($reqStatus === 'completed');
                                            $isActive = ($si === $prCurrentIdx) && ($reqStatus !== 'completed');
                                        ?>
                                            <div class="relative flex flex-col items-center">
                                                <?php if ($si > 0): ?>
                                                    <!-- Connector line centered at top-3.5 (14px) -->
                                                    <div class="absolute top-3.5 -left-1/2 w-full h-0.5 transition-colors duration-300 <?= ($isDone || $isActive || $reqStatus === 'completed') ? 'bg-terra-green' : 'bg-slate-200 dark:bg-slate-700' ?>"></div>
                                                <?php endif; ?>

                                                <div class="relative z-10">
                                                    <?php if ($isDone): ?>
                                                        <div class="w-7 h-7 rounded-full bg-terra-green text-white flex items-center justify-center shadow-2xs">
                                                            <span class="material-symbols-outlined text-[15px]">check</span>
                                                        </div>
                                                    <?php elseif ($isActive): ?>
                                                        <div class="relative w-7 h-7 rounded-full bg-terra-green text-white flex items-center justify-center shadow-2xs ring-4 ring-terra-green/15">
                                                            <span class="w-2 h-2 rounded-full bg-white"></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                                            <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <span class="mt-1.5 text-[10px] sm:text-[11px] font-semibold <?= ($isDone || $isActive) ? 'text-terra-green dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?> text-center leading-tight px-0.5"><?= esc($st['label']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Single Clean Reference Photos & Change Notes Bar (No duplicate footer button) -->
                            <?php if (!empty($req['attachments']) || !empty($req['special_instructions'])): ?>
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400 shrink-0">attach_file</span>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                <?= !empty($req['attachments']) ? (count($req['attachments']) . ' Reference Photo' . (count($req['attachments']) > 1 ? 's' : '')) : 'Formatting Notes' ?>
                                            </p>
                                            <?php if (!empty($req['special_instructions'])): ?>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate"><?= esc($req['special_instructions']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="view-ref-photos-btn px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-terra-green/50 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer shrink-0"
                                            data-number="<?= esc($req['request_number'] ?? ('PR-' . $req['id'])) ?>"
                                            data-name="<?= esc($req['file_name'] ?? 'Document.pdf') ?>"
                                            data-instructions="<?= esc($req['special_instructions'] ?? '') ?>"
                                            data-attachments="<?= esc(json_encode($req['attachments'] ?? []), 'attr') ?>"
                                            data-changetype="<?= esc(($req['doc_change_type'] ?? '') === 'has_changes' ? 'Requested Changes' : 'Print As-Is') ?>">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        <span>View</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- 3. Footer: Price & Action Buttons (Zero duplicate buttons or passive text notices) -->
                        <div class="px-4 py-3.5 sm:px-5 bg-slate-50/40 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">

                            <div class="flex items-baseline gap-2">
                                <span class="text-xs text-slate-500 dark:text-slate-400"><?= esc($reqPriceLbl) ?>:</span>
                                <span class="text-base sm:text-lg font-extrabold font-mono text-terra-green dark:text-emerald-400">₱<?= esc($reqPrice) ?></span>
                            </div>

                            <?php $reqShipFee = (float) ($req['shipping_fee'] ?? 0); ?>
                            <?php if (!$isPickup && $reqShipFee > 0): ?>
                                <p class="basis-full -mt-1 text-[10px] sm:text-[11px] font-mono text-slate-500 dark:text-slate-400 flex items-center gap-1 flex-wrap">
                                    <span class="material-symbols-outlined text-[13px] text-sky-600 dark:text-sky-400">route</span>
                                    Includes ₱<?= number_format($reqShipFee, 2) ?> distance-based shipping fee<?= !empty($req['distance_km']) ? ' &middot; ' . number_format((float) $req['distance_km'], 1) . ' km route' : '' ?>
                                </p>
                            <?php elseif (!$isPickup): ?>
                                <p class="basis-full -mt-1 text-[10px] sm:text-[11px] font-mono text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px] text-sky-600 dark:text-sky-400">route</span>
                                    Shipping ₱0.00 &middot; within the included distance
                                </p>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="flex flex-wrap items-center gap-2 justify-end">

                                <?php if ($reqStatus === 'completed'): ?>
                                    <button type="button" 
                                            class="rate-pr-btn inline-flex items-center gap-1 px-3.5 py-1.5 bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-300/40 hover:bg-amber-500/25 rounded-xl text-xs font-bold transition-all active:scale-95 cursor-pointer"
                                            data-req='<?= esc(json_encode($req), 'attr') ?>'>
                                        <span class="material-symbols-outlined text-[15px]">star</span>
                                        <span>Rate</span>
                                    </button>
                                <?php endif; ?>

                                <?php if ($isPickup && in_array($reqStatus, ['ready_for_pickup', 'completed'], true)): ?>
                                    <button type="button" 
                                            class="pr-qr-btn bg-terra-green hover:bg-[#3d7a33] text-white py-1.5 px-3.5 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-2xs active:scale-95 cursor-pointer" 
                                            data-number="<?= esc($req['request_number'] ?? ('PR-' . $req['id'])) ?>" 
                                            data-name="<?= esc($req['file_name'] ?? 'Document.pdf') ?>"
                                            data-shop="<?= esc($shopName) ?>"
                                            data-location="<?= esc($shopAddress) ?>"
                                            data-paid="<?= $isPaid ? 'PAID' : 'PAY AT COUNTER' ?>">
                                        <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                                        <span>Pick-up QR</span>
                                    </button>
                                <?php endif; ?>

                                <?php if (!$isPickup && in_array($reqStatus, ['ready_for_delivery', 'in_transit', 'completed', 'delivered'], true)): ?>
                                    <a href="<?= base_url('customer/printing/track/' . esc($req['request_number'] ?? $req['id'])) ?>" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-terra-green hover:bg-[#3d7a33] text-white text-xs font-bold rounded-xl shadow-2xs transition active:scale-95 cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                        <span>Track</span>
                                    </a>
                                <?php endif; ?>

                                <?php if (in_array($reqStatus, ['new', 'pending'], true)): ?>
                                    <button type="button" 
                                            class="edit-pr-btn bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-terra-green/50 text-slate-700 dark:text-slate-200 py-1.5 px-3.5 rounded-xl text-xs font-semibold transition-all inline-flex items-center gap-1 active:scale-95 cursor-pointer"
                                            data-id="<?= (int) $req['id'] ?>"
                                            data-number="<?= esc($req['request_number'] ?? ('PR-' . $req['id'])) ?>"
                                            data-name="<?= esc($req['file_name'] ?? 'Document.pdf') ?>"
                                            data-doctype="<?= esc($req['document_type'] ?? 'pdf') ?>"
                                            data-papersize="<?= esc($req['paper_size'] ?? 'letter') ?>"
                                            data-color="<?= esc($req['color_mode'] ?? 'bw') ?>"
                                            data-stock="<?= esc($req['paper_stock'] ?? 'standard') ?>"
                                            data-binding="<?= esc($req['binding_option'] ?? 'none') ?>"
                                            data-copies="<?= (int) ($req['copies'] ?? 1) ?>"
                                            data-pages="<?= (int) ($req['page_count'] ?? 1) ?>"
                                            data-instructions="<?= esc($req['special_instructions'] ?? '') ?>"
                                            data-changetype="<?= esc($req['doc_change_type'] ?? 'as_is') ?>">
                                        <span>Edit</span>
                                    </button>

                                    <button type="button" 
                                            class="cancel-pr-btn border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 py-1.5 px-3 rounded-xl text-xs font-semibold transition-colors inline-flex items-center gap-1 active:scale-95 cursor-pointer"
                                            data-id="<?= (int) $req['id'] ?>"
                                            data-number="<?= esc($req['request_number'] ?? ('PR-' . $req['id'])) ?>">
                                        <span>Cancel</span>
                                    </button>
                                <?php endif; ?>

                                <?php if (in_array($reqStatus, ['new', 'in_production', 'ready_for_delivery', 'ready_for_pickup', 'in_transit'])): ?>
                                    <button type="button" 
                                            class="pr-track inline-flex items-center gap-1 px-3.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-terra-green/50 rounded-xl text-xs font-semibold transition-all active:scale-95 cursor-pointer" 
                                            data-number="<?= esc($req['request_number'] ?? ('PR-' . $req['id'])) ?>" 
                                            data-name="<?= esc($req['file_name'] ?? 'Document.pdf') ?>" 
                                            data-status="<?= esc($reqStatus) ?>" 
                                            data-status-label="<?= esc($reqLabel) ?>" 
                                            data-progress="<?= esc($reqProgress) ?>" 
                                            data-printer="<?= esc($reqPrinter) ?>" 
                                            data-method="<?= esc($reqMethod) ?>" 
                                            data-created="<?= esc($reqCreated) ?>" 
                                            data-completed="<?= esc($reqDone) ?>">
                                        <span>Details</span>
                                    </button>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>
                </div>

                <div id="pr-empty" class="hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 text-center text-slate-500 dark:text-slate-400 text-xs">
                    <span class="material-symbols-outlined text-3xl text-slate-300 dark:text-slate-600 mb-1 block">print</span>
                    <p>No <span id="pr-empty-label">printing requests</span> found.</p>
                </div>

            <?php else: ?>

                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-8 sm:p-10 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-terra-green/10 text-terra-green dark:text-emerald-400 mx-auto flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">print</span>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">No printing requests yet</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Upload documents for local printing, binding, and pickup.</p>
                    </div>
                    <div class="pt-1">
                        <a href="<?= base_url('printing-services') ?>" class="inline-flex items-center gap-1.5 bg-terra-green hover:bg-[#3d7a33] text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-xs active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">print</span>
                            <span>Find Printing Shops</span>
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </main>

</div><!-- Track Progress Modal -->
<div id="track-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-xs" id="track-overlay"></div>
    <div class="bento-card relative bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-md p-6 flex flex-col gap-4 z-10">
        <div class="flex justify-between items-start gap-4">
            <div class="flex flex-col">
                <span class="text-[11px] font-mono font-bold text-neutral-400 dark:text-neutral-500 tracking-tight" id="track-number">#PR-00000</span>
                <h3 class="text-base sm:text-lg font-bold text-neutral-900 dark:text-white mt-0.5" id="track-name">Document.pdf</h3>
            </div>
            <button type="button" id="track-close" class="p-1.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <span id="track-status" class="self-start px-2.5 py-0.5 bg-terra-green/10 text-terra-green border border-terra-green/20 text-[11px] font-bold rounded-full uppercase tracking-wider">In Production</span>
        <div class="flex flex-col gap-1.5">
            <div class="flex justify-between items-center text-xs">
                <span class="text-neutral-400 dark:text-neutral-500 font-bold uppercase tracking-wider text-[10px]">Progress</span>
                <span class="font-mono font-bold text-terra-green" id="track-progress">0%</span>
            </div>
            <div class="w-full h-2 bg-neutral-100 dark:bg-neutral-800 rounded-full overflow-hidden">
                <div id="track-progress-bar" class="h-2 bg-gradient-to-r from-terra-green to-emerald-500 rounded-full transition-all" style="width:0%"></div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 p-3 bg-neutral-50 dark:bg-neutral-800/40 rounded-xl border border-neutral-100 dark:border-neutral-800/80">
            <div class="flex flex-col gap-0.5">
                <span class="text-[10px] text-neutral-400 dark:text-neutral-500 uppercase font-bold tracking-wider">Printer</span>
                <span class="text-xs font-semibold text-neutral-800 dark:text-neutral-200" id="track-printer">Not assigned yet</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[10px] text-neutral-400 dark:text-neutral-500 uppercase font-bold tracking-wider">Fulfillment</span>
                <span class="text-xs font-semibold text-neutral-800 dark:text-neutral-200" id="track-method">Pickup</span>
            </div>
        </div>
        <div class="flex flex-col gap-0.5 text-xs">
            <span class="text-[10px] text-neutral-400 dark:text-neutral-500 uppercase font-bold tracking-wider">Requested On</span>
            <span class="font-medium text-neutral-700 dark:text-neutral-300" id="track-created">—</span>
        </div>
        <div id="track-completed-row" class="flex flex-col gap-0.5 text-xs hidden">
            <span class="text-[10px] text-neutral-400 dark:text-neutral-500 uppercase font-bold tracking-wider">Completed On</span>
            <span class="font-medium text-neutral-700 dark:text-neutral-300" id="track-completed">—</span>
        </div>
        <button type="button" id="track-done" class="self-end px-5 py-2 bg-terra-green hover:bg-terra-green/90 text-white rounded-xl text-xs font-semibold transition-all active:scale-95 shadow-xs cursor-pointer">Close</button>
    </div>
</div>

<!-- Pick-up QR Modal (Store Pick-up Pass) -->
<div id="qr-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" id="qr-overlay"></div>
    <div class="bento-card relative bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-sm p-6 flex flex-col items-center gap-4 text-center z-10">
        <div class="flex justify-between items-center w-full border-b border-neutral-100 dark:border-neutral-800 pb-3">
            <div class="flex items-center gap-2 text-terra-green font-bold">
                <span class="material-symbols-outlined text-2xl">qr_code_2</span>
                <span class="text-sm font-bold text-neutral-900 dark:text-white">Store Pick-up Pass</span>
            </div>
            <button type="button" id="qr-close" class="p-1 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        
        <div class="flex flex-col items-center justify-center bg-white p-5 rounded-2xl shadow-inner border border-neutral-100 w-full max-w-[220px] aspect-square mx-auto">
            <div id="qr-canvas-container" class="flex justify-center items-center w-full h-full"></div>
        </div>
        
        <div class="flex flex-col items-center w-full space-y-1">
            <span class="text-[10px] uppercase tracking-widest text-neutral-400 dark:text-neutral-500 font-bold">Request Identifier</span>
            <span id="qr-request-number" class="text-lg font-mono font-bold text-terra-green">#PR-00000</span>
            
            <div class="flex items-center justify-center gap-2 mt-1">
                <span id="qr-shop" class="text-xs font-semibold text-neutral-900 dark:text-white">Store</span>
                <span class="text-neutral-300 dark:text-neutral-700">•</span>
                <span id="qr-payment" class="text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
            </div>

            <div id="qr-location-box" class="text-xs text-neutral-500 dark:text-neutral-400 mt-1 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[15px] text-terra-green">location_on</span>
                <span id="qr-location">Poblacion, Polomolok</span>
            </div>

            <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 leading-relaxed italic px-2">
                Present this QR code to the cashier at <span id="qr-inst-shop" class="font-semibold text-neutral-900 dark:text-white not-italic">the store</span> upon claiming your printed document.
            </p>

            <div class="mt-3 p-2.5 bg-terra-amber/10 dark:bg-terra-amber/20 border border-terra-amber/30 rounded-xl flex items-center gap-2 text-amber-800 dark:text-amber-300 text-xs font-medium text-left w-full">
                <span class="material-symbols-outlined text-[16px] text-terra-amber shrink-0">brightness_high</span>
                <span>Turn up screen brightness for fast barcode scanning</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 w-full pt-1">
            <button type="button" id="qr-download" class="w-full py-2.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">download</span>
                <span>Download QR</span>
            </button>
            <button type="button" id="qr-done" class="w-full py-2.5 bg-terra-green text-white rounded-xl text-xs font-semibold hover:bg-terra-green/90 transition-all active:scale-95 shadow-xs cursor-pointer">Close</button>
        </div>
    </div>
</div>

<!-- Cancel Printing Confirmation Modal -->
<div id="cancel-pr-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" id="cancel-pr-overlay"></div>
    <div class="bento-card relative bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-md p-6 flex flex-col gap-4 z-10">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </div>
            <div>
                <h3 class="text-base font-bold text-neutral-900 dark:text-white" id="cancel-pr-title">Cancel Printing Request?</h3>
                <p class="text-xs text-neutral-400 font-medium">Permanent Action</p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
            Are you sure you want to cancel this printing request? This action cannot be undone and will notify the shop attendant.
        </p>

        <div id="cancel-pr-error" class="hidden p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-xl text-xs text-rose-700 dark:text-rose-300 font-semibold flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">error</span>
            <span id="cancel-pr-error-text">Failed to cancel printing request.</span>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-neutral-100 dark:border-neutral-800">
            <button type="button" id="cancel-pr-keep" class="flex-1 py-2.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-xl text-xs font-semibold transition-all cursor-pointer">Keep Request</button>
            <button type="button" id="cancel-pr-confirm" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 shadow-xs active:scale-95 cursor-pointer">
                <span id="cancel-pr-spinner" class="material-symbols-outlined text-[16px] animate-spin hidden">progress_activity</span>
                <span id="cancel-pr-confirm-text">Yes, Cancel</span>
            </button>
        </div>
    </div>
</div>

<!-- Rate Shop & Service Modal -->
<div id="rate-pr-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" id="rate-pr-overlay"></div>
    <div class="bento-card relative bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-lg p-6 flex flex-col gap-4 z-10 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 pb-3">
            <div>
                <span class="text-[10px] uppercase font-bold text-neutral-400 dark:text-neutral-500 tracking-wider">Leave Feedback</span>
                <h3 class="text-base font-bold text-neutral-900 dark:text-white" id="rate-pr-title">Rate Printing Service</h3>
            </div>
            <button type="button" id="rate-pr-close" class="p-1 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="rate-pr-form" class="space-y-4">
            <input type="hidden" id="rate-pr-shop-id" value="">
            <input type="hidden" id="rate-pr-request-id" value="">

            <!-- Shop & Printing Rating -->
            <div class="bg-neutral-50 dark:bg-neutral-800/40 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 space-y-2">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white uppercase tracking-wide">Shop &amp; Service: <span id="rate-pr-shop-name" class="text-terra-green font-semibold">Store</span></label>
                    <span id="rate-pr-val-text" class="text-xs font-bold text-terra-amber font-mono">5 / 5</span>
                </div>
                <div class="flex items-center gap-1" id="pr-star-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="pr-star text-terra-amber hover:scale-110 transition-transform cursor-pointer" data-val="<?= $i ?>">
                            <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="rate-pr-score" value="5">
                <textarea id="rate-pr-comment" rows="3" placeholder="How was the print quality, paper stock, and customer turnaround time?" class="w-full text-xs p-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 focus:border-terra-green focus:ring-1 focus:ring-terra-green text-neutral-900 dark:text-white"></textarea>
            </div>

            <div id="rate-pr-error" class="hidden p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-xl text-xs text-rose-700 dark:text-rose-300 font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">error</span>
                <span id="rate-pr-error-text">Failed to submit review.</span>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-neutral-100 dark:border-neutral-800">
                <button type="button" id="rate-pr-cancel" class="py-2 px-4 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-xl text-xs font-semibold transition-all cursor-pointer">Cancel</button>
                <button type="submit" id="rate-pr-submit-btn" class="py-2 px-5 bg-terra-green hover:bg-terra-green/90 text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 shadow-xs active:scale-95 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Submit Review</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer Reference Photos & Instructions Modal -->
<div id="ref-photos-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" id="ref-photos-overlay"></div>
    <div class="bento-card relative bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-lg p-6 flex flex-col gap-4 z-10 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-terra-earth/10 dark:bg-terra-earth/30 text-terra-earth dark:text-amber-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">photo_library</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 dark:text-white">Reference Photos &amp; Changes</h3>
                    <span id="ref-modal-number" class="text-xs font-mono text-terra-green font-bold"></span>
                </div>
            </div>
            <button type="button" id="ref-modal-close" class="p-1 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="space-y-4">
            <!-- Special Instructions Box -->
            <div id="ref-modal-instructions-box" class="p-3.5 rounded-2xl bg-terra-amber/10 dark:bg-terra-amber/20 border border-terra-amber/20 dark:border-terra-amber/30 space-y-1.5">
                <span class="text-[11px] font-bold text-amber-900 dark:text-amber-300 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-terra-amber">edit_note</span>
                    <span>Your Change Instructions:</span>
                </span>
                <p id="ref-modal-instructions" class="text-xs text-neutral-800 dark:text-neutral-200 bg-white dark:bg-neutral-900 p-3 rounded-xl border border-neutral-200/80 dark:border-neutral-800/80 whitespace-pre-wrap font-medium leading-relaxed"></p>
            </div>

            <!-- Photos Grid -->
            <div id="ref-modal-photos-box" class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-neutral-700 dark:text-neutral-300 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[17px] text-terra-earth">image</span>
                        <span>Attached Photos (<span id="ref-modal-count">0</span>)</span>
                    </span>
                    <span class="text-[10px] text-neutral-400">Click photo to zoom</span>
                </div>
                <div id="ref-modal-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5"></div>
            </div>
        </div>

        <div class="pt-2 border-t border-neutral-100 dark:border-neutral-800 flex justify-end">
            <button type="button" id="ref-modal-done" class="px-5 py-2 bg-terra-green text-white rounded-xl text-xs font-semibold hover:bg-terra-green/90 transition-all cursor-pointer">Close</button>
        </div>
    </div>
</div>

<!-- Edit Printing Request Modal (Restricted if In Production) -->
<div id="edit-pr-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm" id="edit-pr-overlay"></div>
    <div class="bento-card relative bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl w-full max-w-2xl p-6 flex flex-col gap-4 z-10 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-neutral-100 dark:border-neutral-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green dark:bg-terra-green/20 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-2xl">edit_document</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-terra-green font-mono tracking-wider" id="edit-pr-ref-number">#PR-0000</span>
                    <h3 class="text-base font-bold text-neutral-900 dark:text-white">Edit Request &amp; Files</h3>
                </div>
            </div>
            <button type="button" id="edit-pr-close" class="p-1 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div id="edit-pr-locked-alert" class="hidden p-3 bg-terra-amber/10 dark:bg-terra-amber/20 border border-terra-amber/30 rounded-2xl text-xs text-amber-800 dark:text-amber-300 font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-terra-amber shrink-0">lock</span>
            <span>This request is already in processing/production. Options and uploaded files can no longer be modified.</span>
        </div>

        <form id="edit-pr-form" action="<?= base_url('printing/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="request_id" id="edit-pr-id" value="">

            <!-- Document Details & File Replacement -->
            <div class="bg-neutral-50 dark:bg-neutral-800/40 p-4 rounded-2xl border border-neutral-100 dark:border-neutral-800 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-terra-green">description</span>
                        <span>Attached Document</span>
                    </label>
                    <span id="edit-pr-current-pages-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-neutral-200 dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200">1 Page</span>
                </div>

                <div class="flex items-center gap-2.5 p-3 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <span class="material-symbols-outlined text-terra-green text-[22px] shrink-0">file_present</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-neutral-900 dark:text-white truncate" id="edit-pr-current-file-name">Document.pdf</p>
                        <p class="text-[10px] text-neutral-400">Current file used for printing</p>
                    </div>
                </div>

                <!-- Replace Document Upload -->
                <div class="pt-1 space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 flex items-center gap-1">
                        <span>Replace with a new document (Optional):</span>
                    </label>
                    <input type="file" name="document" id="edit-pr-file-input" accept=".pdf,.docx,.doc" class="block w-full text-xs text-neutral-700 dark:text-neutral-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-terra-green file:text-white hover:file:bg-terra-green/90 file:cursor-pointer cursor-pointer border border-neutral-200 dark:border-neutral-700 rounded-xl p-1 bg-white dark:bg-neutral-900">
                    <p class="text-[10px] text-neutral-400">Supported formats: PDF, Word (.docx, .doc). Uploading a new file will automatically recalculate total page count.</p>
                </div>
            </div>

            <!-- Print Options (Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Document Type -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white">Document Format</label>
                    <select name="document_type" id="edit-pr-doctype" class="w-full text-xs font-semibold p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green">
                        <option value="pdf">PDF Document</option>
                        <option value="docx">Word Document (.docx)</option>
                    </select>
                </div>

                <!-- Color Mode -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white">Color Mode</label>
                    <select name="color_mode" id="edit-pr-color" class="w-full text-xs font-semibold p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green">
                        <option value="bw">Black &amp; White (Monochrome)</option>
                        <option value="color">Full Color</option>
                    </select>
                </div>

                <!-- Paper Size -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white">Paper Size</label>
                    <select name="paper_size" id="edit-pr-size" class="w-full text-xs font-semibold p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green">
                        <option value="letter">Short (Letter - 8.5 x 11 in)</option>
                        <option value="legal">Long (Legal - 8.5 x 13/14 in)</option>
                        <option value="a4">A4 (8.27 x 11.69 in)</option>
                        <option value="a3">A3 (11.69 x 16.54 in)</option>
                        <option value="a5">A5</option>
                    </select>
                </div>

                <!-- Paper Stock -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white">Paper Stock</label>
                    <select name="paper_stock" id="edit-pr-stock" class="w-full text-xs font-semibold p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green">
                        <option value="standard">Standard 70gsm (Bond Paper)</option>
                        <option value="standard_80gsm">Standard 80gsm (High Quality)</option>
                        <option value="glossy">Glossy Photo Paper</option>
                        <option value="sticker">Sticker Paper</option>
                    </select>
                </div>

                <!-- Binding Option -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white">Binding</label>
                    <select name="binding" id="edit-pr-binding" class="w-full text-xs font-semibold p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green">
                        <option value="none">No Binding (Loose Sheets)</option>
                        <option value="staple">Corner / Side Staple</option>
                        <option value="spiral">Spiral / Ring Binding</option>
                    </select>
                </div>

                <!-- Number of Copies -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-neutral-900 dark:text-white">Number of Sets / Copies</label>
                    <input type="number" name="copies" id="edit-pr-copies" min="1" max="500" value="1" class="w-full text-xs font-semibold p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green">
                </div>
            </div>

            <!-- Special Instructions & Changes -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-neutral-900 dark:text-white">Special Instructions &amp; Formatting Notes</label>
                <textarea name="notes" id="edit-pr-notes" rows="2" placeholder="e.g. Page 1 to 5 only, double sided, landscape orientation..." class="w-full text-xs p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white focus:ring-2 focus:ring-terra-green resize-none"></textarea>
            </div>

            <!-- Additional Reference Photos Upload -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-neutral-900 dark:text-white flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-terra-earth">photo_library</span>
                    <span>Attach New Reference Photos (Optional)</span>
                </label>
                <input type="file" name="reference_photos[]" multiple accept="image/*" id="edit-pr-photos-input" class="block w-full text-xs text-neutral-700 dark:text-neutral-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-terra-earth file:text-white hover:file:bg-terra-earth/90 file:cursor-pointer cursor-pointer border border-neutral-200 dark:border-neutral-700 rounded-xl p-1 bg-white dark:bg-neutral-900">
            </div>

            <!-- Error Banner -->
            <div id="edit-pr-error" class="hidden p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 rounded-xl text-xs text-rose-700 dark:text-rose-300 font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">error</span>
                <span id="edit-pr-error-text">Failed to update printing request.</span>
            </div>

            <div class="flex items-center gap-2 pt-2 border-t border-neutral-100 dark:border-neutral-800">
                <button type="button" id="edit-pr-cancel" class="flex-1 py-2.5 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-xl text-xs font-semibold transition-all cursor-pointer">Cancel</button>
                <button type="submit" id="edit-pr-submit" class="flex-1 py-2.5 bg-terra-green hover:bg-terra-green/90 text-white rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 shadow-xs active:scale-95 cursor-pointer">
                    <span id="edit-pr-spinner" class="material-symbols-outlined text-[16px] animate-spin hidden">progress_activity</span>
                    <span id="edit-pr-submit-text">Save Changes &amp; Files</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer Lightbox Zoom Modal -->
<div id="ref-lightbox-modal" class="hidden fixed inset-0 bg-neutral-950/85 backdrop-blur-sm z-[80] flex items-center justify-center p-4">
    <div class="bento-card relative max-w-3xl w-full bg-white dark:bg-neutral-900 rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
        <div class="px-4 py-3 bg-neutral-50 dark:bg-neutral-800/60 border-b border-neutral-100 dark:border-neutral-800 flex justify-between items-center">
            <span id="ref-lightbox-title" class="text-xs font-bold text-neutral-900 dark:text-white truncate max-w-md">Reference Photo Preview</span>
            <div class="flex items-center gap-2">
                <a id="ref-lightbox-dl" href="#" target="_blank" download class="py-1 px-3 bg-terra-green/10 hover:bg-terra-green/20 text-terra-green rounded-lg transition-colors flex items-center gap-1 text-xs font-bold" title="Download Image">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Download</span>
                </a>
                <button type="button" id="ref-lightbox-close" class="p-1 rounded-xl hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>
        <div class="p-4 flex-1 flex items-center justify-center overflow-auto bg-neutral-950/40 min-h-[300px]">
            <img id="ref-lightbox-img" src="" alt="Reference Photo Zoom" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-lg">
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<?php if (!empty($requests)): ?>
<script>
(function () {
    // 1. Filter Tabs
    var tabs  = document.querySelectorAll('.pr-tab');
    var cards = document.querySelectorAll('.pr-card');
    var empty = document.getElementById('pr-empty');
    var emptyLabel = document.getElementById('pr-empty-label');

    function applyFilter(filter) {
        var visible = 0;
        cards.forEach(function (card) {
            var show = filter === 'all' || card.getAttribute('data-group') === filter;
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (empty) {
            var showEmpty = visible === 0;
            empty.classList.toggle('hidden', !showEmpty);
            if (showEmpty && emptyLabel) {
                emptyLabel.textContent = filter + ' printing requests';
            }
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) {
                t.classList.remove('text-terra-green', 'border-b-2', 'border-terra-green');
                t.classList.add('text-slate-500', 'dark:text-slate-400');
            });
            tab.classList.remove('text-slate-500', 'dark:text-slate-400');
            tab.classList.add('text-terra-green', 'border-b-2', 'border-terra-green');
            applyFilter(tab.getAttribute('data-filter'));
        });
    });

    // 2. Track Modal Logic
    var trackBtn = document.querySelectorAll('.pr-track');
    var trackModal = document.getElementById('track-modal');
    var trackOverlay = document.getElementById('track-overlay');
    var trackClose = document.getElementById('track-close');
    var trackDone = document.getElementById('track-done');

    var pillMap = {
        ready_for_pickup: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20',
        ready_for_delivery: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20',
        in_transit: 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20',
        in_production: 'bg-terra-green/10 text-terra-green dark:text-emerald-300 border border-terra-green/20',
        new: 'bg-terra-green/10 text-terra-green dark:text-emerald-300 border border-terra-green/20',
        completed: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
        cancelled: 'bg-rose-50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800'
    };

    function openTrack(btn) {
        if (!trackModal) return;
        document.getElementById('track-number').textContent = '#' + (btn.dataset.number || '');
        document.getElementById('track-name').textContent = btn.dataset.name || '';

        var statusEl = document.getElementById('track-status');
        statusEl.textContent = btn.dataset.statusLabel || '';
        statusEl.className = 'self-start px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider ' + (pillMap[btn.dataset.status] || 'bg-slate-100 text-slate-700');

        var progress = parseInt(btn.dataset.progress || '0', 10);
        document.getElementById('track-progress').textContent = progress + '%';
        document.getElementById('track-progress-bar').style.width = progress + '%';
        document.getElementById('track-printer').textContent = btn.dataset.printer || 'Not assigned yet';
        document.getElementById('track-method').textContent = btn.dataset.method || 'Pickup';
        document.getElementById('track-created').textContent = btn.dataset.created || '—';

        var completedRow = document.getElementById('track-completed-row');
        if (btn.dataset.completed) {
            completedRow.classList.remove('hidden');
            document.getElementById('track-completed').textContent = btn.dataset.completed;
        } else {
            completedRow.classList.add('hidden');
        }

        trackModal.classList.remove('hidden');
    }

    function closeTrack() {
        if (trackModal) trackModal.classList.add('hidden');
    }

    trackBtn.forEach(function (btn) {
        btn.addEventListener('click', function () { openTrack(btn); });
    });
    if (trackOverlay) trackOverlay.addEventListener('click', closeTrack);
    if (trackClose) trackClose.addEventListener('click', closeTrack);
    if (trackDone) trackDone.addEventListener('click', closeTrack);

    // 3. Store Pick-up QR Modal Logic (matching orders.php)
    var qrModal    = document.getElementById('qr-modal');
    var qrOverlay  = document.getElementById('qr-overlay');
    var qrClose    = document.getElementById('qr-close');
    var qrDone     = document.getElementById('qr-done');
    var qrCanvas   = document.getElementById('qr-canvas-container');
    var qrNumber   = document.getElementById('qr-request-number');
    var qrShop     = document.getElementById('qr-shop');
    var qrPayment  = document.getElementById('qr-payment');
    var qrLocation = document.getElementById('qr-location');
    var qrInstShop = document.getElementById('qr-inst-shop');

    function openQr(reqNum, fileName, shopName, locationText, isPaid) {
        if (!qrModal) return;
        qrNumber.textContent = '#' + reqNum;
        qrShop.textContent = shopName || 'Storefront';
        if (qrInstShop) qrInstShop.textContent = shopName || 'the store attendant';
        if (qrLocation) qrLocation.textContent = locationText || 'Poblacion, Polomolok';

        if (isPaid === 'PAID') {
            qrPayment.textContent = 'PAID ONLINE';
            qrPayment.className = 'text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
        } else {
            qrPayment.textContent = 'PAY AT COUNTER';
            qrPayment.className = 'text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900';
        }

        qrCanvas.innerHTML = '';
        if (window.QRCode) {
            new QRCode(qrCanvas, {
                text: reqNum,
                width: 200,
                height: 200,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            qrCanvas.innerHTML = '<p class="text-error text-xs font-mono">#' + reqNum + '</p>';
        }

        qrModal.classList.remove('hidden');
    }

    function closeQr() {
        if (qrModal) qrModal.classList.add('hidden');
    }

    document.querySelectorAll('.pr-qr-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            openQr(btn.dataset.number, btn.dataset.name, btn.dataset.shop, btn.dataset.location, btn.dataset.paid);
        });
    });

    if (qrOverlay) qrOverlay.addEventListener('click', closeQr);
    if (qrClose) qrClose.addEventListener('click', closeQr);
    if (qrDone) qrDone.addEventListener('click', closeQr);

    var qrDownloadBtn = document.getElementById('qr-download');
    if (qrDownloadBtn) {
        qrDownloadBtn.addEventListener('click', function () {
            var canvas = qrCanvas.querySelector('canvas');
            var img = qrCanvas.querySelector('img');
            var dataUrl = null;
            if (canvas) {
                dataUrl = canvas.toDataURL('image/png');
            } else if (img && img.src) {
                dataUrl = img.src;
            }
            if (dataUrl) {
                var a = document.createElement('a');
                a.href = dataUrl;
                a.download = 'BLAX-Printing-QR-' + (qrNumber.textContent.replace('#', '') || 'request') + '.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        });
    }

    // 4. Cancel Printing Request Modal (matching cancel-order-modal)
    var cancelModal   = document.getElementById('cancel-pr-modal');
    var cancelOverlay = document.getElementById('cancel-pr-overlay');
    var cancelKeep    = document.getElementById('cancel-pr-keep');
    var cancelConfirm = document.getElementById('cancel-pr-confirm');
    var cancelTitle   = document.getElementById('cancel-pr-title');
    var cancelErr     = document.getElementById('cancel-pr-error');
    var cancelErrText = document.getElementById('cancel-pr-error-text');
    var cancelSpinner = document.getElementById('cancel-pr-spinner');
    var cancelText    = document.getElementById('cancel-pr-confirm-text');

    var currentCancelId = null;

    function openCancelModal(reqId, reqNumber) {
        currentCancelId = reqId;
        if (cancelTitle) cancelTitle.textContent = 'Cancel Printing Request #' + reqNumber + '?';
        if (cancelErr) cancelErr.classList.add('hidden');
        if (cancelModal) cancelModal.classList.remove('hidden');
    }

    function closeCancelModal() {
        if (cancelModal) cancelModal.classList.add('hidden');
        currentCancelId = null;
    }

    if (cancelOverlay) cancelOverlay.addEventListener('click', closeCancelModal);
    if (cancelKeep) cancelKeep.addEventListener('click', closeCancelModal);

    document.querySelectorAll('.cancel-pr-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            openCancelModal(this.getAttribute('data-id'), this.getAttribute('data-number'));
        });
    });

    if (cancelConfirm) {
        cancelConfirm.addEventListener('click', function () {
            if (!currentCancelId) return;

            cancelSpinner.classList.remove('hidden');
            cancelText.textContent = 'Cancelling...';
            cancelConfirm.disabled = true;

            var csrfToken = (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : '';
            var csrfHeader = (typeof window.getCsrfHeader === 'function') ? window.getCsrfHeader() : 'X-CSRF-TOKEN';

            fetch('<?= base_url('customer/printing/cancel') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfToken
                },
                body: new URLSearchParams({
                    'request_id': currentCancelId
                })
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (result) {
                cancelSpinner.classList.add('hidden');
                cancelText.textContent = 'Yes, Cancel';
                cancelConfirm.disabled = false;

                if (result.ok && result.data && result.data.success) {
                    closeCancelModal();
                    if (typeof showToast === 'function') {
                        showToast(result.data.message || 'Printing request cancelled successfully.', 'success');
                    }
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    if (cancelErr && cancelErrText) {
                        cancelErrText.textContent = (result.data && result.data.error) ? result.data.error : 'Failed to cancel printing request.';
                        cancelErr.classList.remove('hidden');
                    }
                }
            })
            .catch(function (err) {
                cancelSpinner.classList.add('hidden');
                cancelText.textContent = 'Yes, Cancel';
                cancelConfirm.disabled = false;
                if (cancelErr && cancelErrText) {
                    cancelErrText.textContent = 'Network or server error. Please try again.';
                    cancelErr.classList.remove('hidden');
                }
            });
        });
    }

    // 5. Rate Printing Request Modal (matching rate-modal in orders.php)
    var rateModal      = document.getElementById('rate-pr-modal');
    var rateOverlay    = document.getElementById('rate-pr-overlay');
    var rateClose      = document.getElementById('rate-pr-close');
    var rateCancel     = document.getElementById('rate-pr-cancel');
    var rateForm       = document.getElementById('rate-pr-form');
    var rateShopName   = document.getElementById('rate-pr-shop-name');
    var rateShopId     = document.getElementById('rate-pr-shop-id');
    var rateReqId      = document.getElementById('rate-pr-request-id');
    var rateScore      = document.getElementById('rate-pr-score');
    var rateComment    = document.getElementById('rate-pr-comment');
    var rateValText    = document.getElementById('rate-pr-val-text');
    var rateErr        = document.getElementById('rate-pr-error');
    var rateErrText    = document.getElementById('rate-pr-error-text');

    function closeRateModal() {
        if (rateModal) rateModal.classList.add('hidden');
    }

    if (rateOverlay) rateOverlay.addEventListener('click', closeRateModal);
    if (rateClose) rateClose.addEventListener('click', closeRateModal);
    if (rateCancel) rateCancel.addEventListener('click', closeRateModal);

    function updatePrStars(score) {
        var stars = document.querySelectorAll('#pr-star-group .pr-star');
        stars.forEach(function (s) {
            var val = parseInt(s.dataset.val, 10);
            var icon = s.querySelector('.material-symbols-outlined');
            if (icon) {
                if (val <= score) {
                    icon.style.fontVariationSettings = "'FILL' 1";
                    s.classList.remove('opacity-40');
                } else {
                    icon.style.fontVariationSettings = "'FILL' 0";
                    s.classList.add('opacity-40');
                }
            }
        });
        if (rateValText) rateValText.textContent = score + ' / 5';
    }

    document.querySelectorAll('#pr-star-group .pr-star').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var val = parseInt(this.dataset.val, 10) || 5;
            rateScore.value = val;
            updatePrStars(val);
        });
    });

    document.querySelectorAll('.rate-pr-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            try {
                var req = JSON.parse(this.getAttribute('data-req'));
                if (!req) return;
                rateReqId.value = req.id || '';
                rateShopId.value = req.shop_id || '';
                rateShopName.textContent = req.shop_name || 'Printing Shop';
                rateScore.value = '5';
                rateComment.value = '';
                updatePrStars(5);
                if (rateErr) rateErr.classList.add('hidden');
                if (rateModal) rateModal.classList.remove('hidden');
            } catch (err) {
                console.error('Failed to open printing rate modal:', err);
            }
        });
    });

    if (rateForm) {
        rateForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var csrfToken = (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : '';
            var csrfHeader = (typeof window.getCsrfHeader === 'function') ? window.getCsrfHeader() : 'X-CSRF-TOKEN';
            var submitBtn = document.getElementById('rate-pr-submit-btn');
            if (submitBtn) submitBtn.disabled = true;

            var postData = new URLSearchParams({
                shop_id: rateShopId.value,
                rating: rateScore.value,
                review: rateComment.value,
                order_id: rateReqId.value
            });

            fetch('<?= base_url('customer/reviews/shop') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfToken
                },
                body: postData
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (submitBtn) submitBtn.disabled = false;
                closeRateModal();
                if (typeof showToast === 'function') {
                    showToast('Thank you! Your review for the printing shop has been submitted.', 'success');
                } else {
                    alert('Thank you! Your review for the printing shop has been submitted.');
                }
            })
            .catch(function (err) {
                if (submitBtn) submitBtn.disabled = false;
                if (rateErr && rateErrText) {
                    rateErrText.textContent = 'Failed to submit review. Please try again.';
                    rateErr.classList.remove('hidden');
                }
            });
        });
    }

    // 6. Reference Photos & Instructions Modal Logic
    var refModal     = document.getElementById('ref-photos-modal');
    var refOverlay   = document.getElementById('ref-photos-overlay');
    var refClose     = document.getElementById('ref-modal-close');
    var refDone      = document.getElementById('ref-modal-done');
    var refNumber    = document.getElementById('ref-modal-number');
    var refInstBox   = document.getElementById('ref-modal-instructions-box');
    var refInst      = document.getElementById('ref-modal-instructions');
    var refPhotosBox = document.getElementById('ref-modal-photos-box');
    var refCount     = document.getElementById('ref-modal-count');
    var refGrid      = document.getElementById('ref-modal-photos-grid');

    var refLightbox      = document.getElementById('ref-lightbox-modal');
    var refLightboxImg   = document.getElementById('ref-lightbox-img');
    var refLightboxTitle = document.getElementById('ref-lightbox-title');
    var refLightboxDl    = document.getElementById('ref-lightbox-dl');
    var refLightboxClose = document.getElementById('ref-lightbox-close');

    function openRefLightbox(url, title) {
        if (!refLightbox) return;
        refLightboxImg.src = url;
        refLightboxTitle.textContent = title || 'Reference Photo Preview';
        refLightboxDl.href = url;
        refLightbox.classList.remove('hidden');
    }

    function closeRefLightbox() {
        if (!refLightbox) return;
        refLightbox.classList.add('hidden');
        refLightboxImg.src = '';
    }

    if (refLightboxClose) refLightboxClose.addEventListener('click', closeRefLightbox);
    if (refLightbox) {
        refLightbox.addEventListener('click', function(e) {
            if (e.target === refLightbox) closeRefLightbox();
        });
    }

    function openRefPhotos(btn) {
        if (!refModal) return;
        var reqNum = btn.dataset.number || '';
        var inst = (btn.dataset.instructions || '').trim();
        var attachments = [];

        try {
            attachments = JSON.parse(btn.dataset.attachments || '[]');
        } catch (e) {
            attachments = [];
        }

        refNumber.textContent = '#' + reqNum;

        // Instructions
        if (inst !== '') {
            refInstBox.classList.remove('hidden');
            refInst.textContent = inst;
        } else {
            refInstBox.classList.add('hidden');
        }

        // Photos Grid
        refGrid.innerHTML = '';
        if (attachments && attachments.length > 0) {
            refPhotosBox.classList.remove('hidden');
            refCount.textContent = attachments.length;
            attachments.forEach(function (att, idx) {
                var imgPath = att.image_url || att.file_path || '';
                if (!imgPath) return;
                var fullUrl = imgPath.startsWith('http') ? imgPath : ('<?= base_url() ?>/' + imgPath.replace(/^\/+/, ''));
                var safeName = att.file_name || imgPath.split('/').pop() || ('Reference Photo ' + (idx + 1));

                var card = document.createElement('div');
                card.className = 'group relative rounded-xl overflow-hidden border border-outline-variant/30 bg-surface-container-low shadow-2xs hover:shadow-md transition-all flex flex-col';
                card.innerHTML = `
                    <div class="aspect-square w-full overflow-hidden bg-surface-container relative cursor-pointer flex items-center justify-center">
                        <img src="${fullUrl}" alt="${safeName}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 pointer-events-none">
                            <span class="p-1.5 rounded-full bg-white/90 text-primary shadow">
                                <span class="material-symbols-outlined text-[16px] block">zoom_in</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-1.5 flex items-center justify-between gap-1 text-[11px] bg-surface-container-lowest border-t border-outline-variant/20">
                        <span class="truncate font-semibold text-on-surface flex-1" title="${safeName}">${safeName}</span>
                        <a href="${fullUrl}" download="${safeName}" target="_blank" class="p-1 hover:text-primary transition-colors shrink-0" title="Download">
                            <span class="material-symbols-outlined text-[14px]">download</span>
                        </a>
                    </div>
                `;

                var imgArea = card.querySelector('.aspect-square');
                if (imgArea) {
                    imgArea.onclick = function() {
                        openRefLightbox(fullUrl, safeName);
                    };
                }

                refGrid.appendChild(card);
            });
        } else {
            refPhotosBox.classList.add('hidden');
            refCount.textContent = '0';
        }

        refModal.classList.remove('hidden');
    }

    function closeRefPhotos() {
        if (refModal) refModal.classList.add('hidden');
    }

    // 7. Edit Printing Request & Replace Files Modal
    var editModal       = document.getElementById('edit-pr-modal');
    var editOverlay     = document.getElementById('edit-pr-overlay');
    var editClose       = document.getElementById('edit-pr-close');
    var editCancel      = document.getElementById('edit-pr-cancel');
    var editForm        = document.getElementById('edit-pr-form');
    var editRefNum      = document.getElementById('edit-pr-ref-number');
    var editId          = document.getElementById('edit-pr-id');
    var editLockedAlert = document.getElementById('edit-pr-locked-alert');
    var editFileName    = document.getElementById('edit-pr-current-file-name');
    var editPagesBadge  = document.getElementById('edit-pr-current-pages-badge');
    var editFileInput   = document.getElementById('edit-pr-file-input');
    var editDocType     = document.getElementById('edit-pr-doctype');
    var editColor       = document.getElementById('edit-pr-color');
    var editSize        = document.getElementById('edit-pr-size');
    var editStock       = document.getElementById('edit-pr-stock');
    var editBinding     = document.getElementById('edit-pr-binding');
    var editCopies      = document.getElementById('edit-pr-copies');
    var editNotes       = document.getElementById('edit-pr-notes');
    var editError       = document.getElementById('edit-pr-error');
    var editErrorText   = document.getElementById('edit-pr-error-text');
    var editSubmit      = document.getElementById('edit-pr-submit');
    var editSpinner     = document.getElementById('edit-pr-spinner');
    var editSubmitText  = document.getElementById('edit-pr-submit-text');

    function openEditModal(btn) {
        if (!editModal) return;
        var id = btn.dataset.id || '';
        var reqNum = btn.dataset.number || '';
        var name = btn.dataset.name || 'Document.pdf';
        var docType = btn.dataset.doctype || 'pdf';
        var size = btn.dataset.papersize || 'letter';
        var color = btn.dataset.color || 'bw';
        var stock = btn.dataset.stock || 'standard';
        var binding = btn.dataset.binding || 'none';
        var copies = btn.dataset.copies || '1';
        var pages = btn.dataset.pages || '1';
        var instructions = btn.dataset.instructions || '';

        editId.value = id;
        editRefNum.textContent = '#' + reqNum;
        editFileName.textContent = name;
        editPagesBadge.textContent = pages + (parseInt(pages, 10) === 1 ? ' Page' : ' Pages');

        if (editDocType) editDocType.value = docType;
        if (editColor) editColor.value = color;
        if (editSize) editSize.value = size;
        if (editStock) editStock.value = stock;
        if (editBinding) editBinding.value = binding;
        if (editCopies) editCopies.value = copies;
        if (editNotes) editNotes.value = instructions;
        if (editFileInput) editFileInput.value = '';

        if (editError) editError.classList.add('hidden');
        if (editLockedAlert) editLockedAlert.classList.add('hidden');
        if (editSubmit) editSubmit.disabled = false;

        // Fetch fresh status check from server
        fetch('<?= base_url('customer/printing/details/') ?>' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data && data.success) {
                if (!data.can_edit) {
                    if (editLockedAlert) editLockedAlert.classList.remove('hidden');
                    if (editSubmit) editSubmit.disabled = true;
                }
            }
        })
        .catch(function() {});

        editModal.classList.remove('hidden');
    }

    function closeEditModal() {
        if (editModal) editModal.classList.add('hidden');
        if (editForm) editForm.reset();
    }

    document.querySelectorAll('.edit-pr-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            openEditModal(btn);
        });
    });

    if (editOverlay) editOverlay.addEventListener('click', closeEditModal);
    if (editClose) editClose.addEventListener('click', closeEditModal);
    if (editCancel) editCancel.addEventListener('click', closeEditModal);

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (editError) editError.classList.add('hidden');
            if (editSubmit) editSubmit.disabled = true;
            if (editSpinner) editSpinner.classList.remove('hidden');
            if (editSubmitText) editSubmitText.textContent = 'Saving Changes...';

            var formData = new FormData(editForm);

            fetch(editForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                return res.json().then(function(json) {
                    return { ok: res.ok, status: res.status, data: json };
                });
            })
            .then(function(result) {
                if (editSpinner) editSpinner.classList.add('hidden');
                if (editSubmitText) editSubmitText.textContent = 'Save Changes & Files';
                if (editSubmit) editSubmit.disabled = false;

                if (result.ok && result.data && result.data.success) {
                    closeEditModal();
                    if (window.showToast) {
                        window.showToast({
                            type: 'success',
                            title: 'Printing Request Updated',
                            message: result.data.message || 'Your printing options and files have been updated.'
                        });
                    }
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                } else {
                    var msg = (result.data && result.data.error) ? result.data.error : 'Failed to update printing request.';
                    if (editError && editErrorText) {
                        editErrorText.textContent = msg;
                        editError.classList.remove('hidden');
                    } else if (window.showToast) {
                        window.showToast({ type: 'error', title: 'Update Error', message: msg });
                    }
                }
            })
            .catch(function(err) {
                if (editSpinner) editSpinner.classList.add('hidden');
                if (editSubmitText) editSubmitText.textContent = 'Save Changes & Files';
                if (editSubmit) editSubmit.disabled = false;
                if (editError && editErrorText) {
                    editErrorText.textContent = 'Network or server error while updating printing request.';
                    editError.classList.remove('hidden');
                }
            });
        });
    }

    document.querySelectorAll('.view-ref-photos-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            openRefPhotos(btn);
        });
    });

    if (refOverlay) refOverlay.addEventListener('click', closeRefPhotos);
    if (refClose) refClose.addEventListener('click', closeRefPhotos);
    if (refDone) refDone.addEventListener('click', closeRefPhotos);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (refLightbox && !refLightbox.classList.contains('hidden')) {
                closeRefLightbox();
                return;
            }
            if (refModal && !refModal.classList.contains('hidden')) {
                closeRefPhotos();
                return;
            }
            if (editModal && !editModal.classList.contains('hidden')) {
                closeEditModal();
                return;
            }
        }
    });

})();
</script>
<?php endif; ?>

<?= $this->endSection() ?>