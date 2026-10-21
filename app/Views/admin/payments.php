<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Flash Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2.5 shadow-xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Page Header Bento Banner -->
    <div class="rounded-3xl bg-surface-container-lowest p-6 border border-outline-variant/30 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-terra-green/10 text-terra-green border border-terra-green/20">
                    Payout Gateway
                </span>
                <span class="text-xs text-on-surface-variant font-mono">
                    PayMongo Wallet Rails
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-on-surface font-display tracking-tight">
                Merchant GCash Payouts
            </h1>
            <p class="text-xs text-on-surface-variant mt-1 max-w-xl">
                Two-step payout governance: conduct internal balance audits before dispatching real-time transfers to merchant GCash mobile wallets.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-xs font-bold text-on-surface transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">sync</span>
                <span>Refresh</span>
            </button>
            <a href="<?= base_url('admin/analytics') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-terra-green text-white hover:bg-emerald-600 text-xs font-bold shadow-xs transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">monitoring</span>
                <span>Revenue Analytics</span>
            </a>
        </div>
    </div>

    <!-- 4-Card Payout Metrics Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Card 1: Pending Review -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border <?= !empty($pending_count) ? 'border-amber-500/40 bg-amber-500/5' : 'border-outline-variant/30' ?> shadow-xs flex flex-col justify-between hover:border-amber-500/50 transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-[11px] font-bold uppercase tracking-wider <?= !empty($pending_count) ? 'text-amber-800 dark:text-amber-300' : 'text-on-surface-variant/70' ?>">Pending Review</p>
                <span class="w-10 h-10 rounded-2xl bg-amber-500/15 text-amber-700 dark:text-amber-300 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px] <?= !empty($pending_count) ? 'animate-pulse' : '' ?>">pending_actions</span>
                </span>
            </div>
            <div class="mt-2">
                <h3 class="text-2xl lg:text-3xl font-black text-amber-700 dark:text-amber-400 font-display font-mono tracking-tight">₱<?= number_format($pending_total ?? 0, 2) ?></h3>
                <p class="text-[11px] text-on-surface-variant/70 font-medium mt-1.5"><?= esc($pending_count ?? 0) ?> request(s) awaiting review</p>
            </div>
        </div>

        <!-- Card 2: In Transfer / Review -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs flex flex-col justify-between hover:border-terra-green/30 transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">In Transfer / Review</p>
                <span class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">swap_horiz</span>
                </span>
            </div>
            <div class="mt-2">
                <h3 class="text-2xl lg:text-3xl font-black text-terra-green font-display font-mono tracking-tight">₱<?= number_format($processing_total ?? 0, 2) ?></h3>
                <p class="text-[11px] text-on-surface-variant/70 font-medium mt-1.5"><?= esc($processing_count ?? 0) ?> in progress or PayMongo rail</p>
            </div>
        </div>

        <!-- Card 3: Total Disbursed -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs flex flex-col justify-between hover:border-emerald-500/30 transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Total Disbursed</p>
                <span class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </span>
            </div>
            <div class="mt-2">
                <h3 class="text-2xl lg:text-3xl font-black text-emerald-600 font-display font-mono tracking-tight">₱<?= number_format($completed_total ?? 0, 2) ?></h3>
                <p class="text-[11px] text-on-surface-variant/70 font-medium mt-1.5">Completed GCash transfers</p>
            </div>
        </div>

        <!-- Card 4: Platform Fee Rate -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs flex flex-col justify-between hover:border-terra-earth/30 transition-all">
            <div class="flex justify-between items-start mb-2">
                <p class="text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Platform Fee Rate</p>
                <span class="w-10 h-10 rounded-2xl bg-terra-earth/10 text-terra-earth flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">percent</span>
                </span>
            </div>
            <div class="mt-2">
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl lg:text-3xl font-black text-on-surface font-display font-mono tracking-tight"><?= number_format($deduction_percent ?? 3.00, 2) ?>%</h3>
                    <span class="text-xs text-on-surface-variant font-medium">withholding fee</span>
                </div>
                <form action="<?= base_url('admin/payments/deduction') ?>" method="POST" class="mt-2.5 flex items-center gap-2">
                    <?= csrf_field() ?>
                    <input type="number" step="0.01" min="0" max="50" name="deduction_percent" value="<?= esc($deduction_percent ?? 3.00) ?>" class="w-20 px-2.5 py-1 text-xs bg-surface-container-low border border-outline-variant/40 rounded-xl font-mono font-bold text-on-surface focus:ring-2 focus:ring-terra-green focus:outline-none">
                    <button type="submit" class="px-3 py-1 text-xs font-bold bg-terra-green text-white rounded-xl hover:bg-emerald-600 transition-all shadow-2xs active:scale-95">Save</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Filter Deck & Instant Search -->
    <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-xs">
        <form method="get" action="<?= base_url('admin/payments') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="relative flex-1 min-w-[260px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input name="q" 
                       value="<?= esc($filters['q'] ?? '') ?>" 
                       placeholder="Search reference #, merchant shop name, or phone..." 
                       class="w-full pl-10 pr-9 py-2.5 bg-surface-container-low border border-outline-variant/40 rounded-xl text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-terra-green focus:border-transparent transition-all placeholder:text-on-surface-variant/50">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select name="status" class="bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-2 text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-terra-green">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>In Review</option>
                    <option value="transfer_pending" <?= ($filters['status'] ?? '') === 'transfer_pending' ? 'selected' : '' ?>>Transfer Pending</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>

                <div class="flex items-center gap-2 text-xs text-on-surface-variant font-medium">
                    <label for="per_page_select" class="font-bold text-[11px] uppercase tracking-wider text-on-surface-variant/70">Show:</label>
                    <select name="per_page" id="per_page_select" onchange="this.form.submit()" class="bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-2 text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-terra-green">
                        <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                        <option value="15" <?= ($per_page ?? 10) == 15 ? 'selected' : '' ?>>15</option>
                        <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </div>

                <button type="submit" class="bg-terra-green text-white hover:bg-emerald-600 px-4 py-2 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply Filter</span>
                </button>

                <?php if (!empty($filters['q']) || !empty($filters['status'])): ?>
                    <a href="<?= base_url('admin/payments') ?>" class="text-xs font-bold text-on-surface-variant hover:text-on-surface px-3 py-2 rounded-xl bg-surface-container-low border border-outline-variant/30 transition-colors">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Payouts Table -->
    <section class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[1040px]">
                <thead class="bg-surface-container-low/70 border-b border-outline-variant/30 text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-5 whitespace-nowrap">Request ID</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Merchant &amp; Owner</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Gross</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Fee</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Net Disbursed</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Recipient Snapshot</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Status &amp; Rails</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Date</th>
                        <th class="py-3.5 px-5 text-right whitespace-nowrap min-w-[150px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-xs">
                    <?php if (!empty($payment_requests)): ?>
                        <?php foreach ($payment_requests as $p): ?>
                            <?php
                                $status = strtolower($p['status'] ?? 'pending');
                                $gross  = (float) $p['amount'];
                                $rate   = (float) ($p['deduction_percent'] ?? $deduction_percent ?? 3.00);
                                $fee    = (float) (($p['fee'] ?? 0) > 0 ? $p['fee'] : round($gross * ($rate / 100), 2));
                                $net    = (float) ($p['net_amount'] ?? max(0, $gross - $fee));

                                $destNumber = $p['destination_detail'] ?? $p['account_details'] ?? '';
                                $maskedPhone = strlen($destNumber) === 11 ? substr($destNumber, 0, 2) . '••••••' . substr($destNumber, -4) : $destNumber;
                                $recipientName = $p['recipient_account_name'] ?? $p['shop_name'] ?? 'Merchant';
                                $ownerFullName = trim(($p['owner_first_name'] ?? '') . ' ' . ($p['owner_last_name'] ?? ''));
                            ?>
                            <tr class="hover:bg-surface-container-low/40 transition-colors group">
                                <!-- Request ID -->
                                <td class="py-4 px-5 font-mono text-xs font-bold text-terra-green">
                                    #<?= esc($p['reference_number'] ?? ('WD-' . $p['id'])) ?>
                                </td>

                                <!-- Merchant & Owner -->
                                <td class="py-4 px-5">
                                    <div class="font-bold text-on-surface group-hover:text-terra-green transition-colors"><?= esc($p['shop_name'] ?? 'Merchant') ?></div>
                                    <?php if (!empty($ownerFullName)): ?>
                                        <div class="text-[11px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[13px] text-outline">person</span>
                                            <span><?= esc($ownerFullName) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Gross -->
                                <td class="py-4 px-5 font-bold font-mono text-on-surface">
                                    ₱<?= number_format($gross, 2) ?>
                                </td>

                                <!-- Fee -->
                                <td class="py-4 px-5 font-mono text-amber-700 dark:text-amber-400 font-semibold">
                                    -₱<?= number_format($fee, 2) ?>
                                    <span class="block text-[10px] text-outline font-normal"><?= number_format($rate, 1) ?>%</span>
                                </td>

                                <!-- Net Disbursed -->
                                <td class="py-4 px-5 font-black font-mono text-emerald-600 dark:text-emerald-400">
                                    ₱<?= number_format($net, 2) ?>
                                </td>

                                <!-- Recipient Snapshot -->
                                <td class="py-4 px-5">
                                    <div class="font-semibold text-on-surface"><?= esc($recipientName) ?></div>
                                    <div class="font-mono text-[11px] text-terra-green flex items-center gap-1 mt-0.5 font-bold">
                                        <span class="material-symbols-outlined text-[14px]">phone_iphone</span>
                                        <span><?= esc($maskedPhone) ?></span>
                                    </div>
                                    <div class="text-[10px] text-outline mt-0.5"><?= esc($p['recipient_institution'] ?? 'G-Xchange, Inc.') ?></div>
                                </td>

                                <!-- Status & Rails -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-1">
                                        <?= status_badge($status) ?>
                                    </div>

                                    <?php if (!empty($p['paymongo_transfer_id'])): ?>
                                        <div class="font-mono text-[10px] text-on-surface-variant mt-1" title="PayMongo Transfer ID: <?= esc($p['paymongo_transfer_id']) ?>">
                                            ID: <?= esc(substr($p['paymongo_transfer_id'], 0, 15)) ?>...
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($p['processor_first_name'])): ?>
                                        <div class="text-[10px] text-on-surface-variant mt-0.5 font-medium">
                                            By: <?= esc($p['processor_first_name']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($status === 'failed' && !empty($p['failure_reason'])): ?>
                                        <div class="text-[10px] text-rose-600 font-medium mt-1 max-w-[160px] truncate" title="<?= esc($p['failure_reason']) ?>">
                                            <?= esc($p['failure_reason']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Date -->
                                <td class="py-4 px-5 text-on-surface-variant whitespace-nowrap font-mono text-[11px]">
                                    <?= date('M d, Y', strtotime($p['requested_at'] ?? $p['created_at'] ?? 'now')) ?>
                                    <span class="block text-[10px] opacity-75"><?= date('h:i A', strtotime($p['requested_at'] ?? $p['created_at'] ?? 'now')) ?></span>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <?php if ($status === 'pending'): ?>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button"
                                                    onclick="openProcessModal(<?= (int)$p['id'] ?>, '<?= esc($p['reference_number'] ?? ('WD-'.$p['id'])) ?>', '<?= esc(addslashes($p['shop_name'] ?? 'Merchant')) ?>', '<?= esc(addslashes($recipientName)) ?>', '<?= esc($maskedPhone) ?>', '<?= number_format($gross, 2) ?>', '<?= number_format($fee, 2) ?>', '<?= number_format($net, 2) ?>')"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-terra-green text-white hover:bg-emerald-600 transition-all flex items-center gap-1 shadow-xs active:scale-95">
                                                <span class="material-symbols-outlined text-[15px]">rate_review</span>
                                                <span>Process</span>
                                            </button>
                                            <button type="button"
                                                    onclick="openRejectModal(<?= (int)$p['id'] ?>, '<?= esc($p['reference_number'] ?? ('WD-'.$p['id'])) ?>', '<?= esc(addslashes($p['shop_name'] ?? 'Merchant')) ?>', '<?= number_format($gross, 2) ?>')"
                                                    class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-600 border border-rose-500/20 hover:bg-rose-500/20 transition-all active:scale-95"
                                                    title="Reject Request">
                                                <span class="material-symbols-outlined text-[15px]">close</span>
                                            </button>
                                        </div>

                                    <?php elseif ($status === 'processing'): ?>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button"
                                                    onclick="openSendModal(<?= (int)$p['id'] ?>, '<?= esc($p['reference_number'] ?? ('WD-'.$p['id'])) ?>', '<?= esc(addslashes($p['shop_name'] ?? 'Merchant')) ?>', '<?= esc(addslashes($ownerFullName)) ?>', '<?= esc(addslashes($recipientName)) ?>', '<?= esc($destNumber) ?>', '<?= esc($maskedPhone) ?>', '<?= number_format($net, 2) ?>')"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-all flex items-center gap-1 shadow-xs active:scale-95">
                                                <span class="material-symbols-outlined text-[15px]">send</span>
                                                <span>Send to GCash</span>
                                            </button>
                                            <button type="button"
                                                    onclick="openRejectModal(<?= (int)$p['id'] ?>, '<?= esc($p['reference_number'] ?? ('WD-'.$p['id'])) ?>', '<?= esc(addslashes($p['shop_name'] ?? 'Merchant')) ?>', '<?= number_format($gross, 2) ?>')"
                                                    class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-600 border border-rose-500/20 hover:bg-rose-500/20 transition-all active:scale-95"
                                                    title="Reject Request">
                                                <span class="material-symbols-outlined text-[15px]">close</span>
                                            </button>
                                        </div>

                                    <?php elseif ($status === 'transfer_pending'): ?>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form action="<?= base_url('admin/payments/sync-status') ?>" method="POST" class="inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="payout_id" value="<?= (int)$p['id'] ?>">
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-terra-green/10 text-terra-green border border-terra-green/25 hover:bg-terra-green/20 transition-all flex items-center gap-1 active:scale-95">
                                                    <span class="material-symbols-outlined text-[15px] animate-spin">refresh</span>
                                                    <span>Check Status</span>
                                                </button>
                                            </form>
                                        </div>

                                    <?php elseif ($status === 'failed'): ?>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button"
                                                    onclick="openSendModal(<?= (int)$p['id'] ?>, '<?= esc($p['reference_number'] ?? ('WD-'.$p['id'])) ?>', '<?= esc(addslashes($p['shop_name'] ?? 'Merchant')) ?>', '<?= esc(addslashes($ownerFullName)) ?>', '<?= esc(addslashes($recipientName)) ?>', '<?= esc($destNumber) ?>', '<?= esc($maskedPhone) ?>', '<?= number_format($net, 2) ?>')"
                                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-600 text-white hover:bg-amber-700 transition-all flex items-center gap-1 shadow-xs active:scale-95"
                                                    title="Retry disbursement after resolving wallet balance or network error">
                                                <span class="material-symbols-outlined text-[15px]">replay</span>
                                                <span>Retry</span>
                                            </button>
                                            <button type="button"
                                                    onclick="openRejectModal(<?= (int)$p['id'] ?>, '<?= esc($p['reference_number'] ?? ('WD-'.$p['id'])) ?>', '<?= esc(addslashes($p['shop_name'] ?? 'Merchant')) ?>', '<?= number_format($gross, 2) ?>')"
                                                    class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-600 border border-rose-500/20 hover:bg-rose-500/20 transition-all active:scale-95"
                                                    title="Reject and Restore Balance">
                                                <span class="material-symbols-outlined text-[15px]">close</span>
                                            </button>
                                        </div>

                                    <?php else: ?>
                                        <span class="text-xs text-on-surface-variant font-mono">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="py-14 text-center text-on-surface-variant">
                                <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto mb-3 text-outline">
                                    <span class="material-symbols-outlined text-3xl">payments</span>
                                </div>
                                <p class="text-sm font-bold text-on-surface font-display">No payout requests found</p>
                                <p class="text-xs text-on-surface-variant mt-1">There are currently no requests matching your filter criteria.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if (isset($pager)): ?>
            <?php
            $total   = (int) $pager->getTotal('payments');
            $perPage = (int) ($per_page ?? ($pager ? $pager->getPerPage('payments') : 10));
            $cur     = (int) $pager->getCurrentPage('payments');
            $pages   = (int) $pager->getPageCount('payments');
            $start   = $total === 0 ? 0 : ($cur - 1) * $perPage + 1;
            $end     = min($cur * $perPage, $total);
            ?>
            <div class="px-6 py-4 bg-surface-container-low/70 flex justify-between items-center border-t border-outline-variant/30 flex-wrap gap-3 text-xs">
                <p class="font-medium text-on-surface-variant">
                    Showing <span class="font-bold text-on-surface font-mono"><?= number_format($start) ?></span> to <span class="font-bold text-on-surface font-mono"><?= number_format($end) ?></span> of <span class="font-bold text-on-surface font-mono"><?= number_format($total) ?></span> requests
                </p>
                <?php if ($pages > 1): ?>
                    <div class="flex items-center gap-1">
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant transition-colors <?= $cur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('payments') ?>" title="Previous">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </a>
                        <?php
                        $window = [];
                        for ($i = 1; $i <= $pages; $i++) {
                            if ($i === 1 || $i === $pages || abs($i - $cur) <= 2) {
                                $window[] = $i;
                            }
                        }
                        $prev = 0;
                        foreach ($window as $num):
                            if ($num - $prev > 1): ?>
                                <span class="px-1 text-outline">...</span>
                            <?php endif; ?>
                            <a class="w-8 h-8 rounded-xl flex items-center justify-center font-bold font-mono text-xs transition-colors <?= $cur === $num ? 'bg-terra-green text-white shadow-xs' : 'hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant' ?>" href="<?= $pager->getPageURI($num, 'payments') ?>"><?= $num ?></a>
                        <?php $prev = $num; endforeach; ?>
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant transition-colors <?= $cur >= $pages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('payments') ?>" title="Next">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="px-6 py-4 bg-surface-container-low/70 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                Showing <?= count($payment_requests ?? []) ?> requests
            </div>
        <?php endif; ?>
    </section>

</div>

<!-- Modal 1: Step 1 Process Review Modal (Strictly Internal - NO PayMongo Transfer) -->
<div id="processModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-3xl p-6 max-w-lg w-full space-y-4 border border-outline-variant/30 shadow-2xl">
        <div class="flex justify-between items-center border-b border-outline-variant/20 pb-4">
            <h3 class="text-base font-bold text-on-surface font-display flex items-center gap-2">
                <span class="material-symbols-outlined text-terra-green">rate_review</span>
                <span>Review Payout Request</span>
            </h3>
            <button type="button" onclick="closeModal('processModal')" class="rounded-xl p-1.5 text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <div class="p-3.5 bg-terra-green/10 border border-terra-green/20 rounded-2xl text-xs text-on-surface-variant space-y-1">
            <div class="font-bold text-terra-green flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">info</span> Step 1: Internal Balance Audit
            </div>
            <p>Moving this request to <strong>In Progress (processing)</strong> marks it as verified by an administrator. <strong>No funds will be sent yet.</strong> You can execute the PayMongo transfer in the next step.</p>
        </div>

        <div class="p-4 bg-surface-container-low border border-outline-variant/25 rounded-2xl space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-on-surface-variant">Reference:</span><span id="processRef" class="font-mono font-bold text-terra-green">#WD-...</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Merchant:</span><span id="processShop" class="font-semibold text-on-surface">Shop</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">GCash Recipient:</span><span id="processRecipient" class="font-semibold text-on-surface">Name</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">GCash Mobile:</span><span id="processPhone" class="font-mono text-on-surface font-bold">09••••••</span></div>
            <div class="flex justify-between border-t border-outline-variant/20 pt-2"><span class="text-on-surface-variant">Gross Amount:</span><span id="processGross" class="font-mono font-semibold">₱0.00</span></div>
            <div class="flex justify-between text-amber-700 dark:text-amber-400"><span class="text-on-surface-variant">Platform Admin Fee:</span><span id="processFee" class="font-mono font-semibold">-₱0.00</span></div>
            <div class="flex justify-between text-on-surface font-bold text-sm border-t border-outline-variant/20 pt-2"><span>Net Disbursed:</span><span id="processNet" class="font-mono text-emerald-600 dark:text-emerald-400">₱0.00</span></div>
        </div>

        <form action="<?= base_url('admin/payments/process') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="payout_id" id="processPayoutId">
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/20">
                <button type="button" onclick="closeModal('processModal')" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-terra-green text-white hover:bg-emerald-600 transition-all shadow-xs flex items-center gap-1.5 active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    <span>Confirm &amp; Mark as In Progress</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Step 2 Send to GCash Modal (Calls PayMongo API with Confirmation Warning) -->
<div id="sendModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-3xl p-6 max-w-lg w-full space-y-4 border border-outline-variant/30 shadow-2xl">
        <div class="flex justify-between items-center border-b border-outline-variant/20 pb-4">
            <h3 class="text-base font-bold text-on-surface font-display flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">send</span>
                <span>Send to GCash (PayMongo)</span>
            </h3>
            <button type="button" onclick="closeModal('sendModal')" class="rounded-xl p-1.5 text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <!-- Caution Alert -->
        <div class="p-3.5 bg-amber-500/15 border border-amber-500/30 rounded-2xl text-xs text-amber-900 dark:text-amber-300 space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-amber-800 dark:text-amber-300">
                <span class="material-symbols-outlined text-[18px]">warning</span> Caution: Irreversible Transfer Action
            </div>
            <p>You are about to initiate an <strong>instant fund transfer</strong> from the platform's PayMongo account directly to the merchant's GCash mobile wallet. Ensure the recipient snapshot matches the merchant's verified KYC information.</p>
        </div>

        <!-- Transfer Breakdown Card -->
        <div class="p-4 bg-surface-container-low border border-outline-variant/25 rounded-2xl space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-on-surface-variant">Reference:</span><span id="sendRef" class="font-mono font-bold text-terra-green">#WD-...</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Shop:</span><span id="sendShop" class="font-semibold text-on-surface">Shop</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Owner:</span><span id="sendOwner" class="text-on-surface">Owner</span></div>
            <div class="border-t border-outline-variant/20 pt-2 space-y-1.5">
                <div class="flex justify-between"><span class="text-on-surface-variant">Destination Institution:</span><span class="font-semibold text-on-surface">G-Xchange, Inc. (GCash)</span></div>
                <div class="flex justify-between"><span class="text-on-surface-variant">Recipient Name:</span><span id="sendRecipient" class="font-bold text-on-surface">Recipient Name</span></div>
                <div class="flex justify-between"><span class="text-on-surface-variant">GCash Mobile:</span><span id="sendPhone" class="font-mono font-bold text-terra-green">09••••••</span></div>
            </div>
            <div class="border-t border-outline-variant/20 pt-2.5 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-on-surface block">Net Amount to Disburse:</span>
                    <span class="text-[10px] text-on-surface-variant">Withholding fees already deducted</span>
                </div>
                <span id="sendNet" class="font-mono text-xl font-black text-emerald-600 dark:text-emerald-400">₱0.00</span>
            </div>
        </div>

        <form action="<?= base_url('admin/payments/send') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="payout_id" id="sendPayoutId">

            <div class="flex items-center gap-2.5 p-3 bg-surface-container-high/40 rounded-2xl border border-outline-variant/30">
                <input type="checkbox" id="sendConfirmCheck" required class="rounded-lg border-outline-variant text-emerald-600 focus:ring-emerald-600 h-4 w-4">
                <label for="sendConfirmCheck" class="text-xs text-on-surface font-medium cursor-pointer">
                    I confirm that the recipient snapshot is correct and authorize this transfer.
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/20">
                <button type="button" onclick="closeModal('sendModal')" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-xs flex items-center gap-1.5 active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Confirm &amp; Disburse via GCash</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Reject Payout Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-3xl p-6 max-w-md w-full space-y-4 border border-outline-variant/30 shadow-2xl">
        <div class="flex justify-between items-center border-b border-outline-variant/20 pb-4">
            <h3 class="text-base font-bold text-rose-600 font-display flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-600">cancel</span>
                <span>Reject Withdrawal Request</span>
            </h3>
            <button type="button" onclick="closeModal('rejectModal')" class="rounded-xl p-1.5 text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-xs text-rose-800 dark:text-rose-300 space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-rose-600">
                <span class="material-symbols-outlined text-[16px]">restore</span> Accounting Rollback
            </div>
            <p>Rejecting will cancel the request and automatically restore the reserved gross amount (<strong id="rejectGross" class="font-mono">₱0.00</strong>) back to the merchant's Available Balance.</p>
        </div>

        <form action="<?= base_url('admin/payments/reject') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="payout_id" id="rejectPayoutId">

            <div>
                <label class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant/70 block mb-1.5">Reason for Rejection <span class="text-rose-600">*</span></label>
                <textarea name="rejection_reason" required rows="3" placeholder="Provide a reason for the merchant (e.g., GCash name mismatch, invalid mobile number)..." class="w-full p-3.5 bg-surface-container-low border border-outline-variant/40 rounded-2xl text-xs text-on-surface focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-outline-variant/20">
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-xs flex items-center gap-1.5 active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">block</span>
                    <span>Reject &amp; Restore Balance</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    function openProcessModal(id, ref, shop, recipient, phone, gross, fee, net) {
        document.getElementById('processPayoutId').value = id;
        document.getElementById('processRef').textContent = '#' + ref;
        document.getElementById('processShop').textContent = shop;
        document.getElementById('processRecipient').textContent = recipient;
        document.getElementById('processPhone').textContent = phone;
        document.getElementById('processGross').textContent = '₱' + gross;
        document.getElementById('processFee').textContent = '-₱' + fee;
        document.getElementById('processNet').textContent = '₱' + net;
        document.getElementById('processModal').classList.remove('hidden');
    }

    function openSendModal(id, ref, shop, owner, recipient, fullPhone, maskedPhone, net) {
        document.getElementById('sendPayoutId').value = id;
        document.getElementById('sendRef').textContent = '#' + ref;
        document.getElementById('sendShop').textContent = shop;
        document.getElementById('sendOwner').textContent = owner || '—';
        document.getElementById('sendRecipient').textContent = recipient;
        document.getElementById('sendPhone').textContent = maskedPhone;
        document.getElementById('sendNet').textContent = '₱' + net;
        const check = document.getElementById('sendConfirmCheck');
        if (check) check.checked = false;
        document.getElementById('sendModal').classList.remove('hidden');
    }

    function openRejectModal(id, ref, shop, gross) {
        document.getElementById('rejectPayoutId').value = id;
        document.getElementById('rejectGross').textContent = '₱' + gross;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal('processModal');
            closeModal('sendModal');
            closeModal('rejectModal');
        }
    });

    ['processModal', 'sendModal', 'rejectModal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', (e) => {
                if (e.target === el) closeModal(id);
            });
        }
    });
</script>
<?= $this->endSection() ?>

