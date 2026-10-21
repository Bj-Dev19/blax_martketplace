<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<?php
    $fullName  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
    if ($fullName === '') {
        $fullName = session()->get('user_name') ?? 'Customer';
    }
    $avatarInitial = strtoupper(substr($fullName, 0, 1));
    $profileImage  = $user['profile_image_url'] ?? '';

    $totalOrders         = $totalOrders ?? 0;
    $activePrinting      = $activePrinting ?? 0;
    $savedAddressesCount = $savedAddressesCount ?? 0;
    $favoriteCount       = $favoriteCount ?? 0;
    $recentActivity      = $recentActivity ?? [];
    $defaultAddress      = $defaultAddress ?? null;

    $memberSince = date('M Y', strtotime($user['created_at'] ?? 'now'));
?>

<div class="flex-1 flex flex-col md:flex-row items-start w-full min-h-[calc(100dvh-66px)] bg-slate-50/60 dark:bg-slate-950">

    <?= view('components/profile_sidebar', ['activeNav' => 'overview']) ?>

    <!-- Main Content Canvas -->
    <main class="flex-1 w-full min-w-0 p-4 sm:p-6 md:p-8 lg:p-10">

        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 pb-28">

            <!-- 1. Calm Profile Header Card (Responsive Horizontal Layout on Mobile & Desktop) -->
            <section class="bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 sm:p-7 shadow-2xs relative overflow-hidden">
                <div class="relative z-10 flex flex-row items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-3.5 sm:gap-5 min-w-0">
                        <div class="relative shrink-0">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-700 overflow-hidden bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center text-2xl sm:text-3xl font-black">
                                <?php if (!empty($profileImage)): ?>
                                    <img class="w-full h-full object-cover" src="<?= esc(profile_image_url($profileImage)) ?>" alt="<?= esc($fullName) ?> avatar">
                                <?php else: ?>
                                    <span class="font-mono"><?= esc($avatarInitial) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-terra-green border-2 border-white dark:border-slate-900" title="Active"></span>
                        </div>

                        <div class="min-w-0 space-y-1">
                            <h1 class="font-display text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight truncate"><?= esc($fullName) ?></h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-terra-green/10 text-terra-green dark:text-emerald-400">
                                    <span class="material-symbols-outlined text-[13px] fill-icon">verified</span>
                                    <span>Verified</span>
                                </span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">Since <?= esc($memberSince) ?></span>
                            </div>
                        </div>
                    </div>

                    <button type="button" data-open-edit-modal class="shrink-0 inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-terra-green dark:bg-slate-800 dark:hover:bg-terra-green text-slate-700 hover:text-white dark:text-slate-200 dark:hover:text-white px-3.5 sm:px-4 py-2.5 min-h-[40px] rounded-xl font-bold text-xs transition-all active:scale-95 cursor-pointer">
                        <span class="material-symbols-outlined text-[17px]">edit</span>
                        <span class="hidden sm:inline">Edit Profile</span>
                        <span class="sm:hidden">Edit</span>
                    </button>

                </div>
            </section>

            <!-- 2. Eye-Relaxing Stat Tiles (2x2 on Mobile, 4x1 on Desktop — Clean, Zero Clutter) -->
            <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                
                <a href="<?= base_url('customer/orders') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl shadow-2xs hover:border-terra-green/40 hover:-translate-y-0.5 transition-all group flex items-center justify-between gap-3">
                    <div class="space-y-0.5 min-w-0">
                        <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-none font-mono"><?= (int) $totalOrders ?></p>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-terra-green transition-colors truncate">My Orders</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    </div>
                </a>

                <a href="<?= base_url('customer/printing') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl shadow-2xs hover:border-terra-green/40 hover:-translate-y-0.5 transition-all group flex items-center justify-between gap-3">
                    <div class="space-y-0.5 min-w-0">
                        <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-none font-mono"><?= (int) $activePrinting ?></p>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-terra-green transition-colors truncate">Printing</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                    </div>
                </a>

                <a href="<?= base_url('customer/addresses') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl shadow-2xs hover:border-terra-green/40 hover:-translate-y-0.5 transition-all group flex items-center justify-between gap-3">
                    <div class="space-y-0.5 min-w-0">
                        <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-none font-mono"><?= (int) $savedAddressesCount ?></p>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-terra-green transition-colors truncate">Addresses</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                    </div>
                </a>

                <a href="<?= base_url('customer/favorites') ?>" class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 rounded-2xl shadow-2xs hover:border-terra-green/40 hover:-translate-y-0.5 transition-all group flex items-center justify-between gap-3">
                    <div class="space-y-0.5 min-w-0">
                        <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-none font-mono"><?= (int) $favoriteCount ?></p>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 group-hover:text-terra-green transition-colors truncate">Saved Shops</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">favorite</span>
                    </div>
                </a>

            </section>

            <!-- 3. Balanced 2-Column Bento Row: Personal Details + Default Address -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">

                <!-- Personal Details (7 Columns) -->
                <section class="lg:col-span-7 bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="font-display text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-terra-green dark:text-emerald-400">badge</span>
                            <span>Account Details</span>
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 text-xs sm:text-sm">
                        <div class="space-y-1">
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 block">Full Name</span>
                            <p class="font-bold text-slate-900 dark:text-white truncate"><?= esc($fullName) ?></p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 block">Contact Phone</span>
                            <p class="font-bold text-slate-900 dark:text-white font-mono">
                                <?= !empty($user['phone']) ? esc($user['phone']) : '<span class="text-slate-400 font-sans font-normal">Not set</span>' ?>
                            </p>
                        </div>

                        <div class="space-y-1 sm:col-span-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 block">Email Address</span>
                            <p class="font-bold text-slate-900 dark:text-white break-all flex items-center gap-1.5">
                                <span><?= esc($user['email'] ?? '—') ?></span>
                                <span class="material-symbols-outlined text-terra-green dark:text-emerald-400 text-[15px] fill-icon">check_circle</span>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Default Shipping Address (5 Columns) -->
                <section class="lg:col-span-5 bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="font-display text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-terra-green dark:text-emerald-400">location_on</span>
                            <span>Default Address</span>
                        </h2>
                        <a href="<?= base_url('customer/addresses') ?>" class="text-xs font-bold text-terra-green dark:text-emerald-400 hover:underline">
                            Manage
                        </a>
                    </div>

                    <div class="pt-4 flex-1 flex flex-col justify-center">
                        <?php if ($defaultAddress): ?>
                            <?php
                                $addrPhone = $defaultAddress['phone'] ?? $defaultAddress['phone_number'] ?? ($user['phone'] ?? '');
                                $addrLabel = $defaultAddress['label'] ?? 'Home';
                            ?>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs sm:text-sm text-slate-900 dark:text-white">
                                        <span><?= esc($addrLabel) ?></span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-terra-green/10 text-terra-green dark:text-emerald-400">Default</span>
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                    <?= esc($defaultAddress['address_line1']) ?><?= !empty($defaultAddress['address_line2']) ? (', ' . esc($defaultAddress['address_line2'])) : '' ?>,
                                    <?= esc($defaultAddress['city']) ?>
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-mono pt-1">
                                    <?= esc($defaultAddress['recipient_name'] ?? $fullName) ?><?= !empty($addrPhone) ? ' · ' . esc($addrPhone) : '' ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 space-y-2">
                                <p class="text-xs text-slate-400">No default address saved yet.</p>
                                <a href="<?= base_url('customer/addresses?open_add=1') ?>" class="inline-flex items-center gap-1 text-xs font-bold text-terra-green dark:text-emerald-400 hover:underline">
                                    <span class="material-symbols-outlined text-[15px]">add</span>
                                    <span>Add Address</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

            </div>

            <!-- 4. Recent Activity Feed -->
            <section class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-5 sm:p-6 shadow-2xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="font-display text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-terra-green dark:text-emerald-400">schedule</span>
                        <span>Recent Activity</span>
                    </h2>
                    <a href="<?= base_url('customer/orders') ?>" class="text-xs font-bold text-terra-green dark:text-emerald-400 hover:underline">View All</a>
                </div>

                <div class="pt-2">
                    <?php if (!empty($recentActivity)): ?>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <?php foreach ($recentActivity as $activity): ?>
                                <?php
                                    $isPr         = str_starts_with($activity['reference'] ?? '', 'PR-');
                                    $actIcon      = $isPr ? 'print' : 'shopping_bag';
                                    $actIconColor = $isPr ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-terra-green/10 text-terra-green dark:text-emerald-400';
                                    $actDate      = !empty($activity['date']) ? date('M d, Y · h:i A', strtotime($activity['date'])) : '—';
                                    $actUrl       = $isPr ? base_url('customer/printing') : base_url('customer/orders');
                                ?>
                                <a href="<?= $actUrl ?>" class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/60 dark:hover:bg-slate-800/30 -mx-2 px-2 rounded-xl transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl <?= $actIconColor ?> flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[18px]"><?= $actIcon ?></span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-xs text-terra-green dark:text-emerald-400">#<?= esc($activity['reference']) ?></span>
                                                <span class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= esc($activity['service']) ?></span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-mono"><?= esc($actDate) ?></p>
                                        </div>
                                    </div>

                                    <div class="shrink-0">
                                        <?= status_badge($activity['status']) ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-slate-400 space-y-1">
                            <span class="material-symbols-outlined text-2xl opacity-50">history</span>
                            <p class="text-xs">No recent orders or print requests yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>

    </main>

</div>

<!-- Edit Profile Modal -->
<div id="edit-profile-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="edit-profile-title">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" data-close-edit-modal></div>

    <div class="bento-card bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl shadow-2xl border border-slate-200/90 dark:border-slate-800 flex flex-col relative overflow-hidden z-10 animate-scale-up">
        
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h2 id="edit-profile-title" class="font-display text-base sm:text-lg font-bold text-slate-900 dark:text-white">Edit Profile</h2>
            <button type="button" data-close-edit-modal class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer" aria-label="Close modal">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('customer/profile/update') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto max-h-[calc(100dvh-130px)]">
            <?= csrf_field() ?>

            <!-- Avatar Uploader -->
            <div class="flex items-center gap-4 pb-2">
                <div id="edit-avatar-preview" class="w-16 h-16 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-terra-green/10 text-terra-green flex items-center justify-center text-2xl font-black shrink-0">
                    <?php if (!empty($profileImage)): ?>
                        <img class="w-full h-full object-cover" src="<?= esc(profile_image_url($profileImage)) ?>" alt="<?= esc($fullName) ?> avatar">
                    <?php else: ?>
                        <span class="font-mono"><?= esc($avatarInitial) ?></span>
                    <?php endif; ?>
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <label class="text-terra-green dark:text-emerald-400 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-terra-green/10 hover:bg-terra-green/20 transition-colors">
                            <span class="material-symbols-outlined text-[15px]">photo_camera</span>
                            <span>Change Photo</span>
                            <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">
                        </label>

                        <?php if (!empty($profileImage)): ?>
                            <label class="text-rose-600 dark:text-rose-400 font-semibold text-xs cursor-pointer inline-flex items-center gap-1 hover:underline">
                                <input type="checkbox" name="remove_profile_image" value="1" class="accent-rose-600 rounded">
                                <span>Remove</span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <p id="edit-avatar-error" class="text-rose-600 dark:text-rose-400 text-xs font-semibold hidden"></p>
                </div>
            </div>

            <!-- Profile Input Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label for="first_name" class="text-xs font-bold text-slate-700 dark:text-slate-300 block">First Name</label>
                    <input id="first_name" name="first_name" type="text" value="<?= esc($user['first_name'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green outline-none" required>
                </div>

                <div class="space-y-1">
                    <label for="last_name" class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Last Name</label>
                    <input id="last_name" name="last_name" type="text" value="<?= esc($user['last_name'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green outline-none" required>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label for="email" class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Email Address</label>
                    <input id="email" name="email" type="email" value="<?= esc($user['email'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green outline-none" required>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label for="phone" class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Phone Number</label>
                    <input id="phone" name="phone" type="tel" value="<?= esc($user['phone'] ?? '') ?>" placeholder="09XXXXXXXXX" class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" data-close-edit-modal class="min-h-[40px] px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs font-bold cursor-pointer">Cancel</button>
                <button type="submit" class="min-h-[40px] px-5 py-2 rounded-xl bg-terra-green hover:bg-[#3d7a33] text-white font-bold text-xs transition-all shadow-xs active:scale-95 cursor-pointer">Save Changes</button>
            </div>

        </form>

    </div>
</div>

<script>
(function () {
    var modal       = document.getElementById('edit-profile-modal');
    var openers     = document.querySelectorAll('[data-open-edit-modal]');
    var closers     = document.querySelectorAll('[data-close-edit-modal]');
    var fileInput   = document.getElementById('profile_image');
    var avatarBox   = document.getElementById('edit-avatar-preview');
    var avatarError = document.getElementById('edit-avatar-error');

    function openModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (avatarError) avatarError.classList.add('hidden');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    openers.forEach(function (btn) { btn.addEventListener('click', openModal); });
    closers.forEach(function (btn) { btn.addEventListener('click', closeModal); });

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var file = fileInput.files && fileInput.files[0];
            if (!file) return;

            var allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (allowed.indexOf(file.type) === -1) {
                if (avatarError) {
                    avatarError.textContent = 'Only JPG, PNG, WEBP or GIF images are allowed.';
                    avatarError.classList.remove('hidden');
                }
                fileInput.value = '';
                return;
            }

            if (file.size > 2097152) {
                if (avatarError) {
                    avatarError.textContent = 'Profile picture must be 2 MB or smaller.';
                    avatarError.classList.remove('hidden');
                }
                fileInput.value = '';
                return;
            }

            if (avatarError) avatarError.classList.add('hidden');
            if (avatarBox) {
                avatarBox.innerHTML = '<img class="w-full h-full object-cover" src="' + URL.createObjectURL(file) + '" alt="Profile preview">';
            }
        });
    }
})();
</script>

<?= $this->endSection() ?>
