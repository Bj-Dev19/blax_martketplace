<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header Section with Live Controls -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center font-bold shadow-xs">
                    <span class="material-symbols-outlined text-[24px]">security</span>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold font-display text-slate-900 dark:text-white tracking-tight">Security &amp; Activity Audit Log</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Real-time forensic telemetry, administrative interventions, and network events.</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all shadow-2xs" title="Reload live events">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                <span>Refresh Feed</span>
            </button>
            <?php
            $exportParams = http_build_query($filters ?? []);
            ?>
            <a href="<?= base_url('admin/audit-log/export?' . $exportParams) ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#4A943E] hover:bg-[#3D7C33] text-white rounded-xl text-xs font-bold transition-all shadow-xs shadow-[#4A943E]/20">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- 4 Bento KPI Summary Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total 24h -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#4A943E]/10 dark:bg-[#4A943E]/20 text-[#4A943E] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">browse_activity</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total (24h)</p>
                <h3 class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white mt-0.5"><?= number_format($total_24h ?? 0) ?></h3>
                <p class="text-xs text-[#4A943E] flex items-center gap-1 mt-0.5 font-medium">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> Platform actions
                </p>
            </div>
        </div>

        <!-- 2. Security Alerts / Failed Actions -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl <?= ($critical ?? 0) > 0 ? 'bg-rose-500/10 text-rose-600 animate-pulse' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' ?> flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]"><?= ($critical ?? 0) > 0 ? 'warning' : 'verified_user' ?></span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Failed / Alerts</p>
                <h3 class="text-xl sm:text-2xl font-bold font-mono <?= ($critical ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600 dark:text-emerald-400' ?> mt-0.5"><?= esc($critical ?? 0) ?></h3>
                <p class="text-xs <?= ($critical ?? 0) > 0 ? 'text-rose-600 font-semibold' : 'text-emerald-600 dark:text-emerald-400 font-medium' ?> mt-0.5">
                    <?= ($critical ?? 0) > 0 ? 'Action required' : 'Zero security alerts' ?>
                </p>
            </div>
        </div>

        <!-- 3. Financial Operations Audited -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F4AE52]/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Financial Ops</p>
                <h3 class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white mt-0.5"><?= number_format($financial_ops ?? 0) ?></h3>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5 font-medium">Payouts &amp; settlements</p>
            </div>
        </div>

        <!-- 4. Active Entities / Users -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#6E3511]/10 text-[#6E3511] dark:text-amber-500 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[26px]">group</span>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Entities</p>
                <h3 class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white mt-0.5"><?= number_format($active_actors ?? 0) ?></h3>
                <p class="text-xs text-[#6E3511] dark:text-amber-500 mt-0.5 font-medium">Unique actors in 24h</p>
            </div>
        </div>
    </section>

    <!-- Filter Presets & Search Toolbar Bento Card -->
    <section class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <!-- Quick Preset Filter Chips -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mr-1">Quick Presets:</span>
            <a href="<?= base_url('admin/audit-log') ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold <?= empty($filters['role']) && empty($filters['status']) && empty($filters['range']) ? 'bg-[#4A943E] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' ?> transition-colors">
                All Logs
            </a>
            <a href="<?= base_url('admin/audit-log?status=failed') ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold <?= ($filters['status'] ?? '') === 'failed' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' ?> transition-colors inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">error</span>
                <span>Critical / Failed</span>
            </a>
            <a href="<?= base_url('admin/audit-log?role=admin') ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold <?= ($filters['role'] ?? '') === 'admin' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-500/10 text-purple-700 dark:text-purple-300 hover:bg-purple-500/20' ?> transition-colors">
                Admin Actions
            </a>
            <a href="<?= base_url('admin/audit-log?role=tenant') ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold <?= ($filters['role'] ?? '') === 'tenant' ? 'bg-[#6E3511] text-white shadow-xs' : 'bg-[#6E3511]/10 text-[#6E3511] dark:text-amber-400 hover:bg-[#6E3511]/20' ?> transition-colors">
                Tenant Ops
            </a>
            <a href="<?= base_url('admin/audit-log?role=customer') ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold <?= ($filters['role'] ?? '') === 'customer' ? 'bg-teal-600 text-white shadow-xs' : 'bg-teal-500/10 text-teal-700 dark:text-teal-300 hover:bg-teal-500/20' ?> transition-colors">
                Customer Activity
            </a>
            <a href="<?= base_url('admin/audit-log?range=today') ?>" class="px-3.5 py-1.5 rounded-full text-xs font-bold <?= ($filters['range'] ?? '') === 'today' ? 'bg-[#F4AE52] text-slate-900 shadow-xs' : 'bg-[#F4AE52]/10 text-amber-700 dark:text-amber-400 hover:bg-[#F4AE52]/20' ?> transition-colors">
                Today Only
            </a>
        </div>

        <!-- Filter Form -->
        <form method="get" action="<?= base_url('admin/audit-log') ?>" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="relative md:col-span-2">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Search action, target, actor, email, IP..." class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#4A943E] transition-all">
            </div>

            <!-- Role Selector -->
            <div>
                <select name="role" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-700 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#4A943E]">
                    <option value="">All Roles</option>
                    <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="tenant" <?= ($filters['role'] ?? '') === 'tenant' ? 'selected' : '' ?>>Tenant</option>
                    <option value="customer" <?= ($filters['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                    <option value="system" <?= ($filters['role'] ?? '') === 'system' ? 'selected' : '' ?>>System</option>
                </select>
            </div>

            <!-- Status Selector -->
            <div>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-700 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#4A943E]">
                    <option value="">All Statuses</option>
                    <option value="success" <?= ($filters['status'] ?? '') === 'success' ? 'selected' : '' ?>>Success</option>
                    <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>

            <!-- Timeframe Range -->
            <div>
                <select name="range" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-700 dark:text-slate-300 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#4A943E]">
                    <option value="">All Time</option>
                    <option value="today" <?= ($filters['range'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="7d" <?= ($filters['range'] ?? '') === '7d' ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="30d" <?= ($filters['range'] ?? '') === '30d' ? 'selected' : '' ?>>Last 30 Days</option>
                </select>
            </div>

            <!-- If IP filter is active -->
            <?php if (!empty($filters['ip'])): ?>
                <input type="hidden" name="ip" value="<?= esc($filters['ip']) ?>">
                <div class="md:col-span-5 flex items-center gap-2 p-2.5 bg-[#4A943E]/10 rounded-xl text-xs font-semibold text-[#4A943E]">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Filtering by IP: <code class="font-mono"><?= esc($filters['ip']) ?></code></span>
                    <a href="<?= base_url('admin/audit-log') ?>" class="ml-auto underline hover:text-[#3D7C33]">Clear IP Filter</a>
                </div>
            <?php endif; ?>

            <!-- Submit Actions & Page Size -->
            <div class="md:col-span-5 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <label for="audit_per_page" class="font-medium">Show per page:</label>
                    <select name="per_page" id="audit_per_page" onchange="this.form.submit()" class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-[#4A943E]">
                        <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-[#4A943E] hover:bg-[#3D7C33] text-white rounded-xl text-xs font-bold transition-all shadow-xs inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply Filters</span>
                </button>
            </div>
        </form>
    </section>

    <!-- Audit Events Table Bento Card -->
    <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800">
                    <tr class="text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-wider">
                        <th class="py-3.5 px-5">Timestamp</th>
                        <th class="py-3.5 px-5">Actor / User</th>
                        <th class="py-3.5 px-5">Role</th>
                        <th class="py-3.5 px-5">Action</th>
                        <th class="py-3.5 px-5">Target Resource</th>
                        <th class="py-3.5 px-5">Network IP</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <?php if (!empty($audit_logs)): foreach ($audit_logs as $log): ?>
                        <?php
                        $actorName = trim(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?: ($log['actor_role'] === 'system' ? 'System' : 'Unknown');
                        $actorInitials = $actorName !== '' ? mb_strtoupper(mb_substr($actorName, 0, 2)) : '??';
                        $role = strtolower($log['actor_role'] ?? 'system');
                        $roleBadgeClasses = [
                            'admin'    => 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20',
                            'tenant'   => 'bg-[#6E3511]/10 text-[#6E3511] dark:text-amber-400 border border-[#6E3511]/20',
                            'customer' => 'bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-500/20',
                            'system'   => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
                        ];
                        $status = strtolower($log['status'] ?? 'success');
                        ?>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                            <!-- Timestamp -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <p class="text-xs font-semibold text-slate-900 dark:text-white"><?= date('M d, Y', strtotime($log['created_at'])) ?></p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5"><?= date('h:i:s A', strtotime($log['created_at'])) ?></p>
                            </td>

                            <!-- Actor -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs font-bold shrink-0">
                                        <?= esc($actorInitials) ?>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 dark:text-white"><?= esc($actorName) ?></p>
                                        <p class="text-[11px] text-slate-400"><?= esc($log['email'] ?? '—') ?></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $roleBadgeClasses[$role] ?? 'bg-slate-100 text-slate-600' ?>">
                                    <?= esc($role) ?>
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 uppercase tracking-wide">
                                    <?= esc(str_replace('_', ' ', $log['action'])) ?>
                                </span>
                            </td>

                            <!-- Target -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 capitalize"><?= esc($log['target_type'] ?? '—') ?></span>
                                    <?php if (!empty($log['target_id'])): ?>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400">#<?= (int) $log['target_id'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Network IP -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <?php if (!empty($log['ip_address'])): ?>
                                    <a href="<?= base_url('admin/audit-log?ip=' . urlencode($log['ip_address'])) ?>" class="px-2.5 py-1 rounded-lg font-mono text-[11px] bg-slate-100 dark:bg-slate-800 hover:bg-[#4A943E]/10 hover:text-[#4A943E] text-slate-600 dark:text-slate-400 font-semibold transition-colors inline-block" title="Filter events by this IP">
                                        <?= esc($log['ip_address']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 font-mono">127.0.0.1</span>
                                <?php endif; ?>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <?php if ($status === 'success'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-[13px]">check</span>
                                        Success
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-[13px]">close</span>
                                        Failed
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <button type="button" onclick="inspectAuditEvent(<?= (int) $log['id'] ?>)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all" title="Inspect full audit telemetry">
                                    <span class="material-symbols-outlined text-[15px] text-[#4A943E]">visibility</span>
                                    <span>Inspect</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <span class="material-symbols-outlined text-[28px]">search_off</span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">No audit log entries found</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Try resetting your search query or selecting a broader date range.</p>
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
            $total   = (int) $pager->getTotal('audit_log');
            $perPage = (int) ($per_page ?? ($pager ? $pager->getPerPage('audit_log') : 10));
            $cur     = (int) $pager->getCurrentPage('audit_log');
            $pages   = (int) $pager->getPageCount('audit_log');
            $start   = $total === 0 ? 0 : ($cur - 1) * $perPage + 1;
            $end     = min($cur * $perPage, $total);
            ?>
            <div class="px-6 py-4 bg-slate-50/60 dark:bg-slate-800/40 flex justify-between items-center border-t border-slate-200/80 dark:border-slate-800 flex-wrap gap-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Showing <span class="font-semibold text-slate-700 dark:text-slate-300 font-mono"><?= number_format($start) ?></span> to <span class="font-semibold text-slate-700 dark:text-slate-300 font-mono"><?= number_format($end) ?></span> of <span class="font-semibold text-slate-700 dark:text-slate-300 font-mono"><?= number_format($total) ?></span> entries
                </p>
                <?php if ($pages > 1): ?>
                    <div class="flex items-center gap-1.5">
                        <a class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors <?= $cur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('audit_log') ?>" title="Previous">
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
                            <a class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-mono font-semibold transition-all <?= $cur === $num ? 'bg-[#4A943E] text-white shadow-xs' : 'border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800' ?>" href="<?= $pager->getPageURI($num, 'audit_log') ?>"><?= $num ?></a>
                        <?php $prev = $num; endforeach; ?>
                        <a class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors <?= $cur >= $pages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('audit_log') ?>" title="Next">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

</div>

<!-- Modal: Audit Event Inspector -->
<div id="auditInspectModal" class="fixed inset-0 bg-slate-950/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-6 py-4.5 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between bg-slate-50/60 dark:bg-slate-800/40">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-[#4A943E] animate-pulse"></span>
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Audit Event Inspector</h3>
            </div>
            <button type="button" onclick="closeAuditInspectModal()" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Body (Dynamic AJAX Content) -->
        <div id="auditInspectBody" class="p-6 overflow-y-auto space-y-4 text-xs sm:text-sm">
            <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[32px] text-[#4A943E]">progress_activity</span>
                <p class="text-xs mt-2">Loading event telemetry...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex items-center justify-between">
            <span id="auditInspectTimeAgo" class="text-xs text-slate-500 dark:text-slate-400 font-mono">Timestamp</span>
            <button type="button" onclick="closeAuditInspectModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors">
                Close Inspector
            </button>
        </div>
    </div>
</div>

<script>
    async function inspectAuditEvent(logId) {
        const modal = document.getElementById('auditInspectModal');
        const body  = document.getElementById('auditInspectBody');
        const timeAgo = document.getElementById('auditInspectTimeAgo');

        modal.classList.remove('hidden');
        body.innerHTML = `
            <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[32px] text-[#4A943E]">progress_activity</span>
                <p class="text-xs mt-2">Loading telemetry...</p>
            </div>
        `;

        try {
            const res = await fetch('<?= base_url('admin/audit-log/detail') ?>/' + logId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (!json.success || !json.data) {
                body.innerHTML = `<div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 text-rose-700 dark:text-rose-300 rounded-2xl text-xs font-semibold">${json.error || 'Unable to load details.'}</div>`;
                return;
            }

            const d = json.data;
            timeAgo.textContent = d.time_ago;

            const isSuccess = (d.status || '').toLowerCase() === 'success';

            let targetCardHtml = '';
            if (d.target_type) {
                targetCardHtml = `
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Target Resource</p>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-900 dark:text-white capitalize">${d.target_type} ${d.target_id ? '#' + d.target_id : ''}</span>
                            ${d.target_info ? `<span class="text-xs px-2.5 py-1 rounded-lg bg-slate-200/80 dark:bg-slate-700 font-mono text-slate-700 dark:text-slate-300">${JSON.stringify(d.target_info)}</span>` : ''}
                        </div>
                    </div>
                `;
            }

            body.innerHTML = `
                <!-- Action & Status Banner -->
                <div class="p-4 rounded-2xl ${isSuccess ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300' : 'bg-rose-500/10 border border-rose-500/20 text-rose-800 dark:text-rose-300'} flex items-center justify-between">
                    <div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Recorded Action</p>
                        <h4 class="text-sm font-bold font-mono uppercase tracking-wide mt-0.5">${d.action}</h4>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ${isSuccess ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/20 text-rose-600 dark:text-rose-400'}">
                        ${d.status}
                    </span>
                </div>

                <!-- Actor Profile Details -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 space-y-2.5">
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actor Information</p>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 block">Full Name</span>
                            <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">${d.actor_name}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Actor Role</span>
                            <span class="font-bold text-[#4A943E] uppercase font-mono">${d.actor_role}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-400 block">Email Address</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">${d.actor_email || 'None / System'}</span>
                        </div>
                    </div>
                </div>

                ${targetCardHtml}

                <!-- Network & Security Telemetry -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 space-y-2.5">
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Network Footprint</p>
                    <div class="flex items-center justify-between text-xs">
                        <div>
                            <span class="text-slate-400 block">IP Address</span>
                            <span class="font-mono font-bold text-[#4A943E]">${d.ip_address}</span>
                        </div>
                        <a href="<?= base_url('admin/audit-log') ?>?ip=${encodeURIComponent(d.ip_address)}" class="px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-[#4A943E] text-xs font-bold border border-slate-200 dark:border-slate-700 transition-colors">
                            Filter by this IP
                        </a>
                    </div>
                </div>
            `;
        } catch (e) {
            body.innerHTML = `<div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 text-rose-700 dark:text-rose-300 rounded-2xl text-xs font-semibold">Error loading telemetry.</div>`;
        }
    }

    function closeAuditInspectModal() {
        document.getElementById('auditInspectModal').classList.add('hidden');
    }
</script>

<?= $this->endSection() ?>
