<?= $this->extend('layouts/tenant') ?>
<?= $this->section('content') ?>

<?php
    $pendingTotal = 0;
    $pendingCount = 0;
    $completedTotal = 0;
    $completedCount = 0;
    $activeWithdrawal = null;
    if (!empty($withdrawals)) {
        foreach ($withdrawals as $w) {
            $st = strtolower($w['status'] ?? 'pending');
            $amt = (float) ($w['amount'] ?? 0);
            if (in_array($st, ['pending', 'processing', 'under_review'], true)) {
                $pendingTotal += $amt;
                $pendingCount++;
                if (!$activeWithdrawal) {
                    $activeWithdrawal = $w;
                }
            } elseif (in_array($st, ['completed', 'approved', 'disbursed', 'paid', 'success'], true)) {
                $completedTotal += $amt;
                $completedCount++;
            }
        }
    }
?>

<div class="flex-1 space-y-6 pb-12">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-terra-green">check_circle</span>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm font-medium flex items-center gap-2.5 shadow-2xs">
            <span class="material-symbols-outlined text-[20px] text-rose-600">error</span>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- 4-Card Payout Metrics Bento Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Card 1: Available Balance -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between hover:border-terra-green/40 dark:hover:border-terra-green/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Available Balance</span>
                <span class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono tracking-tight">₱<?= number_format($available_balance ?? 0.00, 2) ?></h3>
                <p class="text-[11px] text-terra-green font-semibold mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                    <span>Ready for withdrawal</span>
                </p>
            </div>
        </div>

        <!-- Card 2: Funds in Escrow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between hover:border-terra-amber/40 dark:hover:border-terra-amber/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-terra-amber dark:text-amber-400 uppercase tracking-wider font-sans">Funds in Escrow</span>
                <span class="w-9 h-9 rounded-xl bg-terra-amber/10 text-terra-amber dark:text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">lock</span>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-terra-amber dark:text-amber-400 font-mono tracking-tight">₱<?= number_format($escrow_holding ?? 0.00, 2) ?></h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Held until delivery confirmed</p>
            </div>
        </div>

        <!-- Card 3: Pending Payouts -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Pending Payouts</span>
                <span class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">pending</span>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white font-mono tracking-tight">₱<?= number_format($pendingTotal, 2) ?></h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1"><?= $pendingCount ?> request(s) being processed</p>
            </div>
        </div>

        <!-- Card 4: Total Disbursed -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between hover:border-emerald-300 dark:hover:border-emerald-700 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-sans">Total Disbursed</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-400 font-mono tracking-tight">₱<?= number_format($completedTotal, 2) ?></h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1"><?= $completedCount ?> completed payout(s)</p>
            </div>
        </div>
    </div>

    <!-- GCash Incomplete Warning Banner -->
    <?php if (empty($is_gcash_complete)): ?>
        <div class="bg-amber-50 dark:bg-amber-950/30 rounded-2xl p-5 border border-amber-300 dark:border-amber-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-800 dark:text-amber-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">warning</span>
                </span>
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm font-display">GCash Information Incomplete</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 font-sans">Your GCash account name and 11-digit mobile number must be verified and saved in settings before requesting withdrawals.</p>
                </div>
            </div>
            <a href="<?= base_url('tenant/settings?tab=payment') ?>" class="px-4 py-2.5 bg-terra-amber text-slate-950 rounded-xl font-bold text-xs hover:bg-amber-400 transition-colors shadow-xs flex items-center gap-1.5 whitespace-nowrap active:scale-95">
                <span class="material-symbols-outlined text-[17px]">account_balance_wallet</span>
                <span>Set Up GCash Information</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Request Withdrawal CTA Banner -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white font-display">Merchant Payouts</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">Withdraw earnings to your registered GCash account (minimum ₱20.00). Standard <?= number_format($deduction_percent ?? 3.00, 2) ?>% administrative fee applies to cover gateway disbursement.</p>
        </div>
        <?php if (!empty($is_gcash_complete)): ?>
            <button onclick="window.openModal('withdrawModal')" class="px-5 py-2.5 bg-terra-green text-white rounded-xl font-bold text-xs hover:bg-terra-green/90 transition-all shadow-xs flex items-center gap-1.5 whitespace-nowrap active:scale-95 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                <span>Request Withdrawal</span>
            </button>
        <?php else: ?>
            <a href="<?= base_url('tenant/settings?tab=payment') ?>" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-2xs flex items-center gap-1.5 whitespace-nowrap" title="Complete your GCash details in Settings first">
                <span class="material-symbols-outlined text-[18px]">lock</span>
                <span>Set Up GCash to Withdraw</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($activeWithdrawal): ?>
        <?php
            $activeStatus = strtolower($activeWithdrawal['status'] ?? 'pending');
        ?>
        <!-- Prominent Active Payout Status Tracker -->
        <div class="rounded-3xl p-6 bg-gradient-to-br from-amber-500/10 via-white dark:via-slate-900 to-amber-500/5 dark:from-amber-950/20 dark:to-slate-900 border border-terra-amber/30 space-y-4 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-terra-amber/20 pb-4">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-terra-amber/20 text-terra-amber dark:text-amber-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px] animate-pulse">sync</span>
                    </span>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm font-display">Active Payout Request #<?= esc($activeWithdrawal['reference_number'] ?? ('WD-' . $activeWithdrawal['id'])) ?></h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-sans">Submitted on <?= date('M d, Y h:i A', strtotime($activeWithdrawal['created_at'])) ?> &bull; Recipient: <?= esc($activeWithdrawal['recipient_account_name'] ?? $shop['gcash_name'] ?? 'GCash') ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="font-mono text-xl font-black text-terra-green">₱<?= number_format((float)($activeWithdrawal['net_amount'] ?? $activeWithdrawal['amount']), 2) ?></span>
                    <span class="text-[11px] text-slate-400 block font-mono">Gross: ₱<?= number_format((float)$activeWithdrawal['amount'], 2) ?></span>
                    <span class="block mt-1"><?= status_badge($activeWithdrawal['status']) ?></span>
                </div>
            </div>

            <!-- Payout Lifecycle Stepper -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs">
                <div class="p-3 rounded-xl <?= in_array($activeStatus, ['pending', 'processing', 'transfer_pending', 'completed'], true) ? 'bg-terra-amber/20 text-amber-900 dark:text-amber-200 font-bold border border-terra-amber/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-medium' ?>">
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <span class="material-symbols-outlined text-[15px] text-terra-amber">check_circle</span>
                        <span>1. Requested</span>
                    </div>
                    <span class="text-[10px] opacity-75 font-normal">Reserved</span>
                </div>
                <div class="p-3 rounded-xl <?= in_array($activeStatus, ['processing', 'transfer_pending', 'completed'], true) ? 'bg-terra-amber/20 text-amber-900 dark:text-amber-200 font-bold border border-terra-amber/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-medium' ?>">
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <span class="material-symbols-outlined text-[15px]">hourglass_top</span>
                        <span>2. Review</span>
                    </div>
                    <span class="text-[10px] opacity-75 font-normal">Audited</span>
                </div>
                <div class="p-3 rounded-xl <?= in_array($activeStatus, ['transfer_pending', 'completed'], true) ? 'bg-terra-amber/20 text-amber-900 dark:text-amber-200 font-bold border border-terra-amber/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-medium' ?>">
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <span class="material-symbols-outlined text-[15px]">swap_horiz</span>
                        <span>3. Transferring</span>
                    </div>
                    <span class="text-[10px] opacity-75 font-normal">PayMongo Rail</span>
                </div>
                <div class="p-3 rounded-xl <?= $activeStatus === 'completed' ? 'bg-emerald-500/20 text-emerald-900 dark:text-emerald-200 font-bold border border-emerald-500/40' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-medium' ?>">
                    <div class="flex items-center justify-center gap-1 mb-0.5">
                        <span class="material-symbols-outlined text-[15px]">payments</span>
                        <span>4. Disbursed</span>
                    </div>
                    <span class="text-[10px] opacity-75 font-normal">GCash Credited</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tab Navigation: Settled GCash Earnings | Held in Escrow | Withdrawal Requests -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 dark:border-slate-800 pb-2 overflow-x-auto no-scrollbar">
        <button type="button" 
                id="tabBtnSettled" 
                onclick="switchWithdrawalTab('settled')"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all bg-terra-green text-white shadow-xs cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
            <span>Settled GCash Earnings</span>
            <span id="tabCountSettled" class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-white/20 text-white">
                <?= count($settled_records ?? []) ?>
            </span>
        </button>

        <button type="button" 
                id="tabBtnEscrow" 
                onclick="switchWithdrawalTab('escrow')"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[18px]">lock</span>
            <span>Held in Escrow</span>
            <span id="tabCountEscrow" class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300">
                <?= count($escrow_records ?? []) ?>
            </span>
        </button>

        <button type="button" 
                id="tabBtnHistory" 
                onclick="switchWithdrawalTab('history')"
                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 cursor-pointer shrink-0">
            <span class="material-symbols-outlined text-[18px]">history</span>
            <span>Withdrawal Requests</span>
            <span id="tabCountHistory" class="px-2 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                <?= isset($pager) ? number_format((int) $pager->getTotal('withdrawals')) : count($withdrawals ?? []) ?>
            </span>
        </button>
    </div>

    <!-- Tab Pane 1: Settled GCash Earnings -->
    <div id="tabPaneSettled" class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 font-display">
                    <span class="material-symbols-outlined text-terra-green text-[20px]">verified</span>
                    <span>Completed GCash Transactions</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">
                    Orders and printing requests paid through GCash that have been fulfilled and released to your Available Balance.
                </p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="px-3 py-1 rounded-xl text-xs font-bold font-mono bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                    Gross Settled: ₱<?= number_format($released_earnings ?? 0, 2) ?>
                </span>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <?= count($settled_records ?? []) ?> records
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Payment Channel</th>
                        <th class="py-3 px-4">Settled GCash</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Completed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <?php if (!empty($settled_records)): ?>
                        <?php foreach ($settled_records as $rec): ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-terra-green text-xs">
                                    <?php if ($rec['type'] === 'order'): ?>
                                        <a href="<?= base_url('tenant/orders') ?>" class="hover:underline flex items-center gap-1">
                                            <span>#<?= esc($rec['reference']) ?></span>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('tenant/printing') ?>" class="hover:underline flex items-center gap-1">
                                            <span>#<?= esc($rec['reference']) ?></span>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if ($rec['type'] === 'order'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-sky-500/10 text-sky-700 dark:text-sky-300 border border-sky-500/20">
                                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                            <span>Product Order</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-terra-green/10 text-terra-green border border-terra-green/20">
                                            <span class="material-symbols-outlined text-[14px]">print</span>
                                            <span>Printing Request</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-xs font-semibold text-slate-800 dark:text-slate-100">
                                    <?= esc($rec['customer_name']) ?>
                                    <?php if (!empty($rec['file_name'])): ?>
                                        <span class="block text-[11px] text-slate-400 font-normal truncate max-w-xs"><?= esc($rec['file_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-terra-green">account_balance_wallet</span>
                                        <span><?= esc($rec['channel']) ?></span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold font-mono text-emerald-700 dark:text-emerald-400 text-sm">
                                    ₱<?= number_format((float) $rec['amount'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                        <span>Released</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 text-right font-medium">
                                    <?= date('M d, Y • h:i A', strtotime($rec['completed_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-700">account_balance_wallet</span>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">No completed GCash payments yet</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 max-w-md">
                                        Only customer orders and printing requests paid through GCash that have been fulfilled and delivered will be recorded here and credited to your Available Balance.
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab Pane 2: Held in Escrow -->
    <div id="tabPaneEscrow" class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 font-display">
                    <span class="material-symbols-outlined text-terra-amber text-[20px]">lock</span>
                    <span>GCash Funds in Escrow</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">
                    Customer GCash payments captured and safely held in escrow until order delivery or store pickup completion.
                </p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="px-3 py-1 rounded-xl text-xs font-bold font-mono bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">
                    Total in Escrow: ₱<?= number_format($escrow_holding ?? 0, 2) ?>
                </span>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <?= count($escrow_records ?? []) ?> records
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Payment Channel</th>
                        <th class="py-3 px-4">Amount in Escrow</th>
                        <th class="py-3 px-4 text-center">Fulfillment Status</th>
                        <th class="py-3 px-4 text-right">Order Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <?php if (!empty($escrow_records)): ?>
                        <?php foreach ($escrow_records as $rec): ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-terra-green text-xs">
                                    #<?= esc($rec['reference']) ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if ($rec['type'] === 'order'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-sky-500/10 text-sky-700 dark:text-sky-300 border border-sky-500/20">
                                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                            <span>Product Order</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-terra-green/10 text-terra-green border border-terra-green/20">
                                            <span class="material-symbols-outlined text-[14px]">print</span>
                                            <span>Printing Request</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-xs font-semibold text-slate-800 dark:text-slate-100">
                                    <?= esc($rec['customer_name']) ?>
                                    <?php if (!empty($rec['file_name'])): ?>
                                        <span class="block text-[11px] text-slate-400 font-normal truncate max-w-xs"><?= esc($rec['file_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-terra-amber">lock</span>
                                        <span><?= esc($rec['channel']) ?></span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold font-mono text-amber-700 dark:text-amber-400 text-sm">
                                    ₱<?= number_format((float) $rec['amount'], 2) ?>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?= status_badge($rec['status']) ?>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 text-right font-medium">
                                    <?= date('M d, Y • h:i A', strtotime($rec['placed_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-700">lock_open</span>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">No active funds in escrow</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 max-w-md">
                                        When customers place orders with GCash, payments are held here until you complete preparation and delivery.
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab Pane 3: Withdrawal Requests -->
    <div id="tabPaneHistory" class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/80 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 font-display">
                    <span class="material-symbols-outlined text-terra-green text-[20px]">history</span>
                    <span>Withdrawal History</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-sans">Complete record of payout requests and settlement statuses.</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="get" action="<?= base_url('tenant/withdrawals') ?>" class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <input type="hidden" name="tab" value="history">
                    <label for="withdrawals_per_page" class="font-medium">Show:</label>
                    <select name="per_page" id="withdrawals_per_page" onchange="this.form.submit()" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs font-semibold focus:ring-2 focus:ring-terra-green/30">
                        <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                        <option value="15" <?= ($per_page ?? 10) == 15 ? 'selected' : '' ?>>15</option>
                        <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </form>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400"><?= isset($pager) ? number_format((int) $pager->getTotal('withdrawals')) : count($withdrawals ?? []) ?> records</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60">
                        <th class="py-3 px-4">Request ID</th>
                        <th class="py-3 px-4">Gross Amount</th>
                        <th class="py-3 px-4">Admin Fee (<?= number_format($deduction_percent ?? 3.00, 2) ?>%)</th>
                        <th class="py-3 px-4">Net Payout</th>
                        <th class="py-3 px-4">Recipient Snapshot</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <?php if (!empty($withdrawals)): ?>
                        <?php foreach ($withdrawals as $w): ?>
                            <?php 
                                $gross = (float)$w['amount'];
                                $feeRate = (float)($w['deduction_percent'] ?? $deduction_percent ?? 3.00) / 100;
                                $fee = (float)($w['fee'] ?? ($gross * $feeRate));
                                $net = (float)($w['net_amount'] ?? max(0, $gross - $fee));
                            ?>
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-semibold text-terra-green">#<?= esc($w['reference_number'] ?? ('WD-' . $w['id'])) ?></td>
                                <td class="py-3.5 px-4 font-bold font-mono text-slate-900 dark:text-white">₱<?= number_format($gross, 2) ?></td>
                                <td class="py-3.5 px-4 text-xs font-mono text-amber-700 dark:text-amber-400 font-semibold">-₱<?= number_format($fee, 2) ?></td>
                                <td class="py-3.5 px-4 font-bold font-mono text-emerald-700 dark:text-emerald-400">₱<?= number_format($net, 2) ?></td>
                                <td class="py-3.5 px-4 text-xs">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200"><?= esc($w['recipient_account_name'] ?? $shop['gcash_name'] ?? '—') ?></div>
                                    <div class="font-mono text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[13px] text-terra-green">phone_iphone</span>
                                        <?php
                                            $dest = $w['destination_detail'] ?? $w['account_details'] ?? '';
                                            echo esc(strlen($dest) === 11 ? substr($dest, 0, 2) . '••••••' . substr($dest, -4) : $dest);
                                        ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5"><?= esc($w['recipient_institution'] ?? 'G-Xchange, Inc.') ?></div>
                                </td>
                                <td class="py-3.5 px-4 text-center"><?= status_badge($w['status'] ?? 'pending') ?></td>
                                <td class="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400 text-right"><?= date('M d, Y h:i A', strtotime($w['created_at'] ?? $w['requested_at'] ?? 'now')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-700">payments</span>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">No withdrawal requests yet</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 max-w-xs">When you have delivered customer orders, your escrow funds will release to available balance for withdrawal.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <?php
            $total   = (int) $pager->getTotal('withdrawals');
            $perPage = (int) ($per_page ?? ($pager ? $pager->getPerPage('withdrawals') : 10));
            $cur     = (int) $pager->getCurrentPage('withdrawals');
            $pages   = (int) $pager->getPageCount('withdrawals');
            $start   = $total === 0 ? 0 : ($cur - 1) * $perPage + 1;
            $end     = min($cur * $perPage, $total);
            ?>
            <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl flex justify-between items-center border border-slate-200/80 dark:border-slate-800 flex-wrap gap-2 mt-4">
                <p class="text-xs text-slate-500 dark:text-slate-400">Showing <?= number_format($start) ?> to <?= number_format($end) ?> of <?= number_format($total) ?> records</p>
                <?php if ($pages > 1): ?>
                    <div class="flex items-center gap-1">
                        <a class="p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 <?= $cur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('withdrawals') ?>" title="Previous">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
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
                                <span class="px-1 text-slate-400 text-xs">...</span>
                            <?php endif; ?>
                            <a class="w-8 h-8 rounded-xl flex items-center justify-center text-xs <?= $cur === $num ? 'bg-terra-green text-white font-bold' : 'hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' ?>" href="<?= $pager->getPageURI($num, 'withdrawals') ?>"><?= $num ?></a>
                        <?php $prev = $num; endforeach; ?>
                        <a class="p-1.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 <?= $cur >= $pages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('withdrawals') ?>" title="Next">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" onclick="if(event.target===this)window.closeModal('withdrawModal')">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full space-y-4 border border-slate-200/80 dark:border-slate-800 shadow-2xl">

        <div class="flex justify-between items-center border-b border-slate-200/80 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold text-slate-900 dark:text-white font-display">Request Withdrawal</h3>
            <button type="button" onclick="window.closeModal('withdrawModal')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer"><span class="material-symbols-outlined">close</span></button>
        </div>

        <form action="<?= base_url('tenant/withdrawals/request') ?>" method="POST" class="space-y-4">

            <?= csrf_field() ?>

            <div>
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider font-sans">Withdrawal Amount (₱)</label>
                    <span class="text-xs text-terra-green font-bold font-mono">Max: ₱<?= number_format($available_balance ?? 0, 2) ?></span>
                </div>
                <input type="number" step="0.01" min="20" <?= ($available_balance ?? 0) >= 20 ? 'max="' . ($available_balance ?? 0) . '"' : '' ?> name="amount" id="withdrawAmountInput" oninput="updateWithdrawalBreakdown(this.value)" required placeholder="e.g. 50.00" class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl mt-1 font-mono font-bold text-base focus:ring-2 focus:ring-terra-green/30 text-slate-900 dark:text-white">
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span>Minimum withdrawal: <strong class="text-slate-800 dark:text-slate-200 font-semibold font-mono">₱20.00</strong></span>
                    <?php if (($available_balance ?? 0) < 20): ?>
                        <span class="text-rose-600 font-medium">Insufficient balance</span>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (($available_balance ?? 0) < 20): ?>
                <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl flex items-center gap-2 text-xs text-amber-800 dark:text-amber-300">
                    <span class="material-symbols-outlined text-[18px] text-terra-amber shrink-0">info</span>
                    <span>Your available balance of ₱<?= number_format($available_balance ?? 0, 2) ?> is below the minimum withdrawal amount of <strong>₱20.00</strong>.</span>
                </div>
            <?php endif; ?>

            <!-- Live Calculation Breakdown -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl space-y-2 text-xs font-sans">
                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                    <span>Gross Withdrawal:</span>
                    <span id="breakdownGross" class="font-mono font-semibold text-slate-800 dark:text-slate-200">₱0.00</span>
                </div>
                <div class="flex justify-between text-amber-700 dark:text-amber-400">
                    <span>Platform Admin Fee (<?= number_format($deduction_percent ?? 3.00, 2) ?>%):</span>
                    <span id="breakdownFee" class="font-mono font-semibold">-₱0.00</span>
                </div>
                <div class="flex justify-between text-slate-900 dark:text-white border-t border-slate-200/80 dark:border-slate-700 pt-2 font-bold">
                    <span>Net Disbursed to GCash:</span>
                    <span id="breakdownNet" class="font-mono text-sm text-emerald-700 dark:text-emerald-400">₱0.00</span>
                </div>
            </div>

            <!-- Verified GCash Recipient Destination Snapshot -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px]">Verified Recipient Details</span>
                    <span class="inline-flex items-center gap-1 text-[11px] text-terra-green font-semibold">
                        <span class="material-symbols-outlined text-[14px]">verified</span> Verified GCash
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Account Name</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate block" title="<?= esc($gcash_account_name ?: ($shop['gcash_name'] ?? '—')) ?>"><?= esc($gcash_account_name ?: ($shop['gcash_name'] ?? '—')) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Mobile Number</span>
                        <span class="font-mono font-bold text-terra-green"><?= esc($masked_gcash_number ?: ($shop['gcash_number'] ?? '—')) ?></span>
                    </div>
                    <div class="col-span-2 pt-1 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                        <span>Institution: <strong class="text-slate-800 dark:text-slate-200">G-Xchange, Inc. (GCash)</strong></span>
                        <a href="<?= base_url('tenant/settings?tab=payment') ?>" class="text-terra-green hover:underline font-semibold flex items-center gap-0.5" title="Change GCash recipient in Payment Settings">
                            <span class="material-symbols-outlined text-[13px]">edit</span> Edit in Settings
                        </a>
                    </div>
                </div>
            </div>

            <input type="hidden" name="method" value="GCash">
            <input type="hidden" name="account_details" value="<?= esc($shop['gcash_number'] ?? '') ?>">

            <button type="submit" <?= ($available_balance ?? 0) < 20 ? 'disabled class="w-full py-3 bg-slate-200 dark:bg-slate-800 text-slate-400 rounded-xl font-bold cursor-not-allowed shadow-none"' : 'class="w-full py-3 bg-terra-green text-white rounded-xl font-bold hover:bg-terra-green/90 transition-all shadow-xs active:scale-98 cursor-pointer"' ?>>Submit Request</button>

        </form>

    </div>

</div>

<script>
    (function () {
        var gcashNumber = <?= json_encode($shop['gcash_number'] ?? '') ?>;
        var detailsInput = document.querySelector('#withdrawModal input[name="account_details"]');
        if (detailsInput && gcashNumber && detailsInput.value === '') {
            detailsInput.value = gcashNumber;
        }
    })();

    function updateWithdrawalBreakdown(val) {
        const gross = parseFloat(val) || 0;
        const feeRate = <?= json_encode((float)($deduction_percent ?? 3.00)) ?> / 100;
        const fee = Math.round(gross * feeRate * 100) / 100;
        const net = Math.max(0, gross - fee);

        document.getElementById('breakdownGross').textContent = '₱' + gross.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('breakdownFee').textContent = '-₱' + fee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('breakdownNet').textContent = '₱' + net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function switchWithdrawalTab(tab) {
        const tabs = ['settled', 'escrow', 'history'];
        tabs.forEach(t => {
            const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
            const pane = document.getElementById('tabPane' + t.charAt(0).toUpperCase() + t.slice(1));
            if (!btn || !pane) return;

            if (t === tab) {
                btn.className = 'px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all bg-terra-green text-white shadow-xs cursor-pointer shrink-0';
                pane.classList.remove('hidden');
            } else {
                btn.className = 'px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 cursor-pointer shrink-0';
                pane.classList.add('hidden');
            }
        });
        try {
            sessionStorage.setItem('blax_withdrawal_active_tab', tab);
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page_withdrawals') || urlParams.get('tab') === 'history' || urlParams.has('per_page')) {
            switchWithdrawalTab('history');
        } else {
            try {
                const saved = sessionStorage.getItem('blax_withdrawal_active_tab');
                if (saved && ['settled', 'escrow', 'history'].includes(saved)) {
                    switchWithdrawalTab(saved);
                }
            } catch (e) {}
        }
    });
</script>

<?= $this->endSection() ?>