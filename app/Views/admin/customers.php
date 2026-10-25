<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Flash Alerts -->
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
                    Platform Directory
                </span>
                <span class="text-xs text-on-surface-variant font-mono">
                    <?= number_format($total_count ?? count($customers ?? [])) ?> registered
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-on-surface font-display tracking-tight">
                Customer Directory
            </h1>
            <p class="text-xs text-on-surface-variant mt-1 max-w-xl">
                Oversee customer accounts, monitor authentication activity, govern access privileges, and maintain platform security.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="window.location.reload()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-xs font-bold text-on-surface transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">sync</span>
                <span>Refresh</span>
            </button>
            <a href="<?= base_url('admin/audit-log') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-terra-green text-white hover:bg-emerald-600 text-xs font-bold shadow-xs transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">history</span>
                <span>Audit Logs</span>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Total Registered -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs flex flex-col justify-between hover:border-terra-green/30 transition-all">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Total Registered Customers</span>
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-on-surface font-display font-mono tracking-tight"><?= number_format($total_count ?? count($customers ?? [])) ?></h3>
                <p class="text-[11px] text-terra-green font-semibold flex items-center gap-1 mt-1.5">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span>
                    <span>All verified customer accounts</span>
                </p>
            </div>
        </div>

        <!-- Card 2: Active Today -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs flex flex-col justify-between hover:border-terra-amber/30 transition-all">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Active Today</span>
                <div class="w-10 h-10 rounded-2xl bg-terra-amber/15 text-terra-amber flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-terra-amber font-display font-mono tracking-tight"><?= number_format($active_today ?? 0) ?></h3>
                <p class="text-[11px] text-on-surface-variant/70 font-medium mt-1.5">Logged in within last 24 hours</p>
            </div>
        </div>

        <!-- Card 3: New This Week -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 border border-outline-variant/30 shadow-xs flex flex-col justify-between hover:border-blue-500/30 transition-all">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">New This Week</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-black text-blue-600 dark:text-blue-400 font-display font-mono tracking-tight"><?= number_format($new_this_week ?? 0) ?></h3>
                <p class="text-[11px] text-on-surface-variant/70 font-medium mt-1.5">Joined marketplace in last 7 days</p>
            </div>
        </div>
    </section>

    <!-- Filter Deck & Instant Search -->
    <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-xs">
        <form method="get" action="<?= base_url('admin/customers') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="relative flex-1 min-w-[260px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input name="q" 
                       value="<?= esc($filters['q'] ?? '') ?>" 
                       placeholder="Search by customer name, email, or phone number..." 
                       class="w-full pl-10 pr-9 py-2.5 bg-surface-container-low border border-outline-variant/40 rounded-xl text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-terra-green focus:border-transparent transition-all placeholder:text-on-surface-variant/50">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select name="status" class="bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-2 text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-terra-green">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($filters['status']??'')==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
                    <option value="suspended" <?= ($filters['status']??'')==='suspended'?'selected':'' ?>>Suspended</option>
                    <option value="pending" <?= ($filters['status']??'')==='pending'?'selected':'' ?>>Pending</option>
                </select>

                <div class="flex items-center gap-2 text-xs text-on-surface-variant font-medium">
                    <label for="per_page_select" class="font-bold text-[11px] uppercase tracking-wider text-on-surface-variant/70">Show:</label>
                    <select name="per_page" id="per_page_select" onchange="this.form.submit()" class="bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-2 text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-terra-green">
                        <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </div>

                <button type="submit" class="bg-terra-green text-white hover:bg-emerald-600 px-4 py-2 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply Filter</span>
                </button>

                <?php if (!empty($filters['q']) || !empty($filters['status'])): ?>
                    <a href="<?= base_url('admin/customers') ?>" class="text-xs font-bold text-on-surface-variant hover:text-on-surface px-3 py-2 rounded-xl bg-surface-container-low border border-outline-variant/30 transition-colors">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Customers Data Table -->
    <section class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[880px]">
                <thead class="bg-surface-container-low/70 border-b border-outline-variant/30 text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-5 whitespace-nowrap">Customer Name</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Email / Contact</th>
                        <th class="py-3.5 px-5 text-center whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-5 whitespace-nowrap">Date Joined</th>
                        <th class="py-3.5 px-5 text-right whitespace-nowrap min-w-[120px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20 text-xs">
                    <?php if (!empty($customers)): foreach ($customers as $c): ?>
                        <?php 
                        $fullName = trim(($c['first_name']??'') . ' ' . ($c['last_name']??'')) ?: 'Anonymous Customer';
                        $initials = esc(mb_strtoupper(mb_substr(($c['first_name']??'') . ($c['last_name']??''), 0, 2) ?: 'CU'));
                        $customerStatus = $c['status'] ?? 'active';
                        $profileImg = trim((string)($c['profile_image_url'] ?? ''));
                        ?>
                        <tr class="hover:bg-surface-container-low/40 transition-colors group">
                            <!-- Customer Name -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green border border-terra-green/20 flex items-center justify-center font-black text-xs shrink-0 shadow-2xs font-display relative overflow-hidden">
                                        <?php if ($profileImg !== ''): ?>
                                            <img class="absolute inset-0 w-full h-full object-cover" src="<?= esc(profile_image_url($profileImg, 'avatar')) ?>" alt="<?= esc($fullName) ?>" loading="lazy" onerror="this.remove();">
                                        <?php endif; ?>
                                        <span><?= $initials ?></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-on-surface group-hover:text-terra-green transition-colors truncate">
                                            <?= esc($fullName) ?>
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[10px] bg-surface-container px-1.5 py-0.5 rounded-md border border-outline-variant/20 text-on-surface font-semibold">
                                                CUS-<?= esc(str_pad($c['id'], 3, '0', STR_PAD_LEFT)) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email / Contact -->
                            <td class="py-4 px-5">
                                <div class="min-w-0">
                                    <p class="font-semibold text-on-surface truncate flex items-center gap-1.5 font-mono text-[11px]">
                                        <span class="material-symbols-outlined text-[14px] text-outline">mail</span>
                                        <span><?= esc($c['email']) ?></span>
                                    </p>
                                    <p class="text-[11px] text-on-surface-variant/70 truncate flex items-center gap-1.5 mt-0.5 font-mono">
                                        <span class="material-symbols-outlined text-[14px] text-outline">call</span>
                                        <span><?= esc($c['phone'] ?? 'N/A') ?></span>
                                    </p>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                <?= status_badge($customerStatus) ?>
                            </td>

                            <!-- Date Joined -->
                            <td class="py-4 px-5 text-on-surface-variant font-medium whitespace-nowrap font-mono text-[11px]">
                                <?= date('M d, Y', strtotime($c['created_at'])) ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <?php if (in_array($customerStatus, ['active', 'inactive'], true)): ?>
                                    <div class="relative inline-block">
                                        <button type="button" 
                                                data-row="<?= (int) $c['id'] ?>" 
                                                onclick="toggleCustomerMenu(this)" 
                                                class="customer-more-toggle w-8 h-8 rounded-xl bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shadow-2xs" 
                                                title="Customer actions" 
                                                aria-haspopup="true" 
                                                aria-expanded="false">
                                            <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                        </button>
                                        <div id="customer-more-menu-<?= (int) $c['id'] ?>" class="hidden customer-more-menu z-50 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-xl p-2 min-w-[210px]" role="menu">
                                            <form action="<?= base_url('admin/customers/toggle-status') ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="customer_id" value="<?= (int) $c['id'] ?>">
                                                <?php if ($customerStatus === 'active'): ?>
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs text-rose-600 font-bold hover:bg-rose-500/10 transition-colors" onclick="return confirm('Deactivate this customer account?');">
                                                        <span class="material-symbols-outlined text-[18px]">block</span>
                                                        <span>Deactivate Account</span>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs text-terra-green font-bold hover:bg-terra-green/10 transition-colors" onclick="return confirm('Activate this customer account?');">
                                                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                                        <span>Activate Account</span>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-on-surface-variant font-mono">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="py-14 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center mx-auto mb-3 text-outline">
                                    <span class="material-symbols-outlined text-3xl">person_off</span>
                                </div>
                                <p class="text-sm font-bold text-on-surface font-display">No customers found</p>
                                <p class="text-xs text-on-surface-variant mt-1">Try modifying your search criteria or clearing filters.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if (isset($pager)): ?>
            <?php
            $total   = (int) $pager->getTotal('customers');
            $perPage = (int) ($per_page ?? ($pager ? $pager->getPerPage('customers') : 10));
            $cur     = (int) $pager->getCurrentPage('customers');
            $pages   = (int) $pager->getPageCount('customers');
            $start   = $total === 0 ? 0 : ($cur - 1) * $perPage + 1;
            $end     = min($cur * $perPage, $total);
            ?>
            <div class="px-6 py-4 bg-surface-container-low/70 flex justify-between items-center border-t border-outline-variant/30 flex-wrap gap-3 text-xs">
                <p class="font-medium text-on-surface-variant">
                    Showing <span class="font-bold text-on-surface font-mono"><?= number_format($start) ?></span> to <span class="font-bold text-on-surface font-mono"><?= number_format($end) ?></span> of <span class="font-bold text-on-surface font-mono"><?= number_format($total) ?></span> customers
                </p>
                <?php if ($pages > 1): ?>
                    <div class="flex items-center gap-1">
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant transition-colors <?= $cur <= 1 ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getPreviousPageURI('customers') ?>" title="Previous">
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
                            <a class="w-8 h-8 rounded-xl flex items-center justify-center font-bold font-mono text-xs transition-colors <?= $cur === $num ? 'bg-terra-green text-white shadow-xs' : 'hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant' ?>" href="<?= $pager->getPageURI($num, 'customers') ?>"><?= $num ?></a>
                        <?php $prev = $num; endforeach; ?>
                        <a class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-surface-container border border-outline-variant/30 text-on-surface-variant transition-colors <?= $cur >= $pages ? 'pointer-events-none opacity-30' : '' ?>" href="<?= $pager->getNextPageURI('customers') ?>" title="Next">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="px-6 py-4 bg-surface-container-low/70 border-t border-outline-variant/30 text-xs text-on-surface-variant">
                Showing 1 to <?= count($customers ?? []) ?> of <?= esc($total_count ?? count($customers ?? [])) ?> customers
            </div>
        <?php endif; ?>
    </section>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let activeCustomerMenu = null;

    function closeCustomerMenus() {
        if (activeCustomerMenu) {
            activeCustomerMenu.classList.add('hidden');
            activeCustomerMenu = null;
        }
        document.querySelectorAll('.customer-more-toggle[aria-expanded="true"]').forEach((button) => button.setAttribute('aria-expanded', 'false'));
    }

    function toggleCustomerMenu(button) {
        const menu = document.getElementById('customer-more-menu-' + button.dataset.row);
        if (!menu) return;
        if (activeCustomerMenu === menu) {
            closeCustomerMenus();
            return;
        }
        closeCustomerMenus();
        document.body.appendChild(menu);
        menu.classList.remove('hidden');
        menu.style.position = 'fixed';
        const rect = button.getBoundingClientRect();
        const gap = 8;
        menu.style.left = Math.max(gap, Math.min(rect.right - menu.offsetWidth, window.innerWidth - menu.offsetWidth - gap)) + 'px';
        if (rect.bottom + gap + menu.offsetHeight <= window.innerHeight - gap) {
            menu.style.top = (rect.bottom + gap) + 'px';
            menu.style.bottom = '';
        } else {
            menu.style.top = '';
            menu.style.bottom = (window.innerHeight - rect.top + gap) + 'px';
        }
        activeCustomerMenu = menu;
        button.setAttribute('aria-expanded', 'true');
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.customer-more-menu') && !event.target.closest('.customer-more-toggle')) closeCustomerMenus();
    });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeCustomerMenus(); });
    window.addEventListener('scroll', closeCustomerMenus, true);
    window.addEventListener('resize', closeCustomerMenus);
</script>
<?= $this->endSection() ?>

