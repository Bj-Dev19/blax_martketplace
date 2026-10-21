<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </span>
                <span class="font-medium text-xs sm:text-sm"><?= session()->getFlashdata('success') ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 p-1">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Summary KPI Cards (Bento Grid) -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- 1. Total Reports -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Reports</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Platform-wide case records</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-[#6E3511]/10 dark:bg-[#6E3511]/20 text-[#6E3511] dark:text-amber-500 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">report_problem</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl sm:text-3xl font-bold font-mono text-slate-900 dark:text-white"><?= number_format($total_reports ?? 0) ?></h3>
                <span class="text-xs text-slate-400 font-medium">Logged</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-[15px] text-[#4A943E]">verified</span>
                <span>Active compliance monitoring</span>
            </div>
        </div>

        <!-- 2. Pending Reviews -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Pending Reviews</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Awaiting moderator audit</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl sm:text-3xl font-bold font-mono text-rose-600 dark:text-rose-400"><?= number_format($pending_reviews ?? 0) ?></h3>
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full">Action Required</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span>Pending platform action</span>
            </div>
        </div>

        <!-- 3. Resolved Cases -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="text-xs font-bold text-[#4A943E] uppercase tracking-wider">Resolved Cases</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Closed & mediated reports</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl sm:text-3xl font-bold font-mono text-[#4A943E]"><?= number_format($resolved_cases ?? 0) ?></h3>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Cleared</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-[15px] text-[#4A943E]">check</span>
                <span>Resolution rate maintained</span>
            </div>
        </div>
    </section>

    <!-- Main Table Bento Card -->
    <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        
        <!-- Header & Description -->
        <div class="p-6 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">Compliance & Trust Escalations</h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Monitor marketplace integrity, handle customer dispute claims, and issue warnings or suspensions.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <span class="font-mono"><?= count($compliance_items ?? []) ?></span> Visible On Page
                </span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
            <form method="get" action="<?= base_url('admin/compliance') ?>" class="flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-3 items-center flex-1 min-w-[280px]">
                    <div class="relative flex-1 min-w-[200px]">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Search by report ID, shop name, or issue..." class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#4A943E] transition-all">
                    </div>
                    <select name="status" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs sm:text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-[#4A943E]">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= ($filters['status']??'')==='pending'?'selected':'' ?>>Pending</option>
                        <option value="under_review" <?= ($filters['status']??'')==='under_review'?'selected':'' ?>>Under Review</option>
                        <option value="flagged" <?= ($filters['status']??'')==='flagged'?'selected':'' ?>>Flagged</option>
                        <option value="resolved" <?= ($filters['status']??'')==='resolved'?'selected':'' ?>>Resolved</option>
                    </select>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <label for="per_page_select" class="font-medium">Show:</label>
                        <select name="per_page" id="per_page_select" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-[#4A943E]">
                            <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php if (!empty($filters['q']) || !empty($filters['status'])): ?>
                        <a href="<?= base_url('admin/compliance') ?>" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Reset
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="bg-[#4A943E] hover:bg-[#3D7C33] text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                        <span>Apply Filter</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200/80 dark:border-slate-800">
                        <th class="py-3.5 px-6">Report ID</th>
                        <th class="py-3.5 px-6">Reporter</th>
                        <th class="py-3.5 px-6">Merchant / Entity</th>
                        <th class="py-3.5 px-6">Issue Details</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <?php if (!empty($compliance_items)): foreach ($compliance_items as $ci): ?>
                        <?php 
                            $shopId = (int)($ci['shop_id'] ?? $ci['reported_shop_id'] ?? 0);
                            $reportId = (int)$ci['id'];
                            $isCustomerReport = !empty($ci['reported_user_id']);
                            $customerName = trim(($ci['reported_customer_first'] ?? '') . ' ' . ($ci['reported_customer_last'] ?? ''));
                            $entityName = $isCustomerReport 
                                ? ('Customer: ' . ($customerName ?: 'User #' . $ci['reported_user_id']))
                                : ($ci['shop_name'] ?? 'Merchant Shop');
                            $issueDesc = $ci['description'] ?? $ci['issue_type'] ?? 'Reported for review';
                            $reporterName = trim(($ci['reporter_first']??'') . ' ' . ($ci['reporter_last']??'')) ?: 'Reporter';
                            $reporterInitial = mb_strtoupper(mb_substr($reporterName, 0, 1));
                        ?>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                            <!-- Report ID -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold text-[#4A943E] bg-[#4A943E]/10 dark:bg-[#4A943E]/20 px-2.5 py-1 rounded-lg border border-[#4A943E]/20">
                                    #<?= esc($ci['report_number'] ?? 'REP-'.$ci['id']) ?>
                                </span>
                            </td>

                            <!-- Reporter -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        <?= esc($reporterInitial) ?>
                                    </div>
                                    <span class="font-medium text-slate-800 dark:text-slate-200"><?= esc($reporterName) ?></span>
                                </div>
                            </td>

                            <!-- Merchant / Entity -->
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-slate-400">
                                            <?= $isCustomerReport ? 'person' : 'storefront' ?>
                                        </span>
                                        <span><?= esc($entityName) ?></span>
                                    </p>
                                    <?php if (!$isCustomerReport): ?>
                                        <div class="flex items-center gap-1 mt-1 text-[11px]">
                                            <span class="text-rose-600 dark:text-rose-400 font-semibold bg-rose-500/10 px-2 py-0.5 rounded-full">
                                                Flags: <?= esc($ci['flag_count'] ?? 0) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 mt-1">
                                            Reported Customer
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Issue Details -->
                            <td class="py-4 px-6 max-w-[280px]">
                                <span class="font-semibold text-slate-900 dark:text-white block text-xs">
                                    <?= esc(ucwords(str_replace('_', ' ', $ci['issue'] ?? $ci['issue_type'] ?? 'Policy Violation'))) ?>
                                </span>
                                <span class="text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 text-xs leading-relaxed">
                                    <?= esc($issueDesc) ?>
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <?= status_badge($ci['compliance_status'] ?? $ci['status'] ?? 'pending') ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if (!$isCustomerReport && $shopId > 0): ?>
                                        <!-- Issue Warning Modal Button -->
                                        <button type="button" 
                                                onclick="openWarnModal(<?= $shopId ?>, <?= $reportId ?>, '<?= esc($entityName, 'js') ?>')" 
                                                class="text-xs font-bold px-3 py-1.5 bg-[#F4AE52]/10 text-amber-700 dark:text-amber-400 border border-[#F4AE52]/30 rounded-xl hover:bg-[#F4AE52] hover:text-slate-900 transition-all flex items-center gap-1"
                                                title="Issue Formal Warning">
                                            <span class="material-symbols-outlined text-[15px]">warning</span>
                                            <span>Warn</span>
                                        </button>

                                        <!-- Suspend Shop Modal Button -->
                                        <button type="button" 
                                                onclick="openSuspendModal(<?= $shopId ?>, '<?= esc($entityName, 'js') ?>')" 
                                                class="text-xs font-bold px-3 py-1.5 bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/30 rounded-xl hover:bg-rose-600 hover:text-white transition-all flex items-center gap-1"
                                                title="Deactivate / Suspend Shop">
                                            <span class="material-symbols-outlined text-[15px]">block</span>
                                            <span>Suspend</span>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Resolve Button -->
                                    <form action="<?= base_url('admin/compliance/resolve') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="shop_id" value="<?= $shopId ?>">
                                        <input type="hidden" name="report_id" value="<?= $reportId ?>">
                                        <button type="submit" class="text-xs font-bold px-3 py-1.5 bg-[#4A943E] text-white rounded-xl hover:bg-[#3D7C33] transition-all shadow-xs flex items-center gap-1" title="Mark Resolved">
                                            <span class="material-symbols-outlined text-[15px]">task_alt</span>
                                            <span>Resolve</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">verified_user</span>
                                    <p class="font-medium text-sm">No compliance issues or violations found.</p>
                                    <p class="text-xs text-slate-400">All merchant and customer operations are operating smoothly.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager)): ?>
            <?php
            $total   = (int) $pager->getTotal('compliance');
            $perPage = (int) ($per_page ?? ($pager ? $pager->getPerPage('compliance') : 10));
            $cur     = (int) $pager->getCurrentPage('compliance');
            $pages   = (int) $pager->getPageCount('compliance');
            $start   = $total === 0 ? 0 : ($cur - 1) * $perPage + 1;
            $end     = min($cur * $perPage, $total);
            ?>
            <div class="px-6 py-4 bg-slate-50/60 dark:bg-slate-800/40 flex justify-between items-center border-t border-slate-200/80 dark:border-slate-800 flex-wrap gap-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Showing <span class="font-semibold text-slate-700 dark:text-slate-300 font-mono"><?= number_format($start) ?></span> to <span class="font-semibold text-slate-700 dark:text-slate-300 font-mono"><?= number_format($end) ?></span> of <span class="font-semibold text-slate-700 dark:text-slate-300 font-mono"><?= number_format($total) ?></span> reports
                </p>
                <?php if ($pages > 1): ?>
                    <div class="flex items-center gap-1.5">
                        <a class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors <?= $cur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('compliance') ?>" title="Previous">
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
                                <span class="px-1 text-slate-400 text-xs">...</span>
                            <?php endif; ?>
                            <a class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-mono font-semibold transition-all <?= $cur === $num ? 'bg-[#4A943E] text-white shadow-xs' : 'border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>" href="<?= $pager->getPageURI($num, 'compliance') ?>"><?= $num ?></a>
                        <?php $prev = $num; endforeach; ?>
                        <a class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors <?= $cur >= $pages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('compliance') ?>" title="Next">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="px-6 py-4 bg-slate-50/60 dark:bg-slate-800/40 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                Showing <?= count($compliance_items ?? []) ?> reports
            </div>
        <?php endif; ?>
    </section>

</div>

<!-- Modal: Issue Formal Warning -->
<div id="warnModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-3.5">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-[#F4AE52]/10 text-amber-600 dark:text-amber-400 flex items-center justify-center material-symbols-outlined text-[22px]">warning</span>
                <div>
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Issue Formal Warning</h3>
                    <p id="warnShopName" class="text-xs text-slate-500 dark:text-slate-400">Merchant Shop</p>
                </div>
            </div>
            <button type="button" onclick="closeWarnModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/compliance/warn') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="shop_id" id="warnShopId">
            <input type="hidden" name="report_id" id="warnReportId">

            <div>
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Warning Message / Notice to Merchant</label>
                <textarea name="warning_message" rows="3" required class="w-full mt-1.5 p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-[#F4AE52] focus:outline-none transition-all leading-relaxed" placeholder="Specify why the shop is receiving a formal warning and required actions..."></textarea>
            </div>

            <div class="p-3.5 bg-[#F4AE52]/10 border border-[#F4AE52]/30 rounded-2xl text-xs text-[#6E3511] dark:text-[#F4AE52] space-y-1">
                <p class="font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-amber-600">info</span>
                    <span>System Warning Notice:</span>
                </p>
                <p class="text-[11px] leading-relaxed">This will dispatch an official warning alert to the merchant portal and log a strike against their compliance record.</p>
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                <button type="button" onclick="closeWarnModal()" class="flex-1 py-2.5 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-[#F4AE52] text-slate-900 hover:bg-[#e09e45] transition-all shadow-xs">
                    Send Warning
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Suspend Shop -->
<div id="suspendModal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800 pb-3.5">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center material-symbols-outlined text-[22px]">block</span>
                <div>
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Deactivate & Suspend Shop</h3>
                    <p id="suspendShopName" class="text-xs text-slate-500 dark:text-slate-400">Merchant Shop</p>
                </div>
            </div>
            <button type="button" onclick="closeSuspendModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/compliance/suspend') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="shop_id" id="suspendShopId">

            <div>
                <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Reason for Suspension / Deactivation</label>
                <textarea name="suspension_reason" rows="3" required class="w-full mt-1.5 p-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs sm:text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all leading-relaxed" placeholder="State policy violations causing suspension..."></textarea>
            </div>

            <div class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-2xl text-xs text-rose-800 dark:text-rose-300 space-y-1">
                <p class="font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-rose-600">report</span>
                    <span>High Impact Action:</span>
                </p>
                <p class="text-[11px] leading-relaxed">This will immediately deactivate the merchant's storefront, hide all listed products, and block ongoing checkout transactions.</p>
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                <button type="button" onclick="closeSuspendModal()" class="flex-1 py-2.5 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition-all shadow-xs">
                    Confirm Suspension
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openWarnModal(shopId, reportId, shopName) {
        document.getElementById('warnShopId').value = shopId;
        document.getElementById('warnReportId').value = reportId;
        document.getElementById('warnShopName').textContent = shopName;
        document.getElementById('warnModal').classList.remove('hidden');
    }
    function closeWarnModal() {
        document.getElementById('warnModal').classList.add('hidden');
    }

    function openSuspendModal(shopId, shopName) {
        document.getElementById('suspendShopId').value = shopId;
        document.getElementById('suspendShopName').textContent = shopName;
        document.getElementById('suspendModal').classList.remove('hidden');
    }
    function closeSuspendModal() {
        document.getElementById('suspendModal').classList.add('hidden');
    }
</script>

<?= $this->endSection() ?>
