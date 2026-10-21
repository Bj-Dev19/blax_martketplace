<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<?php
$isShopOwner = ($initialRole ?? 'customer') === 'shop_owner';
?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[#1a241e]">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TOP/LEFT PANEL: Branded Hero Banner (Top on Mobile, Left on Desktop) -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="w-full lg:w-[48%] xl:w-[46%] bg-[#1a241e] relative flex flex-col justify-between pt-7 pb-14 px-5 sm:px-8 sm:py-10 lg:p-12 xl:p-16 text-white overflow-hidden shrink-0">
        <!-- Background atmospheric decorative glows: Earthy Forest & Warm Amber -->
        <div class="absolute -top-40 -left-40 w-[480px] h-[480px] rounded-full bg-[#4a7c59]/25 glow-blur pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 rounded-full bg-[#c4a66a]/15 glow-blur pointer-events-none"></div>
        <div class="absolute -bottom-32 left-10 w-96 h-96 rounded-full bg-[#2a6038]/20 glow-blur pointer-events-none"></div>
        <!-- Subtle background grid pattern -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <!-- Brand Header & Quick Nav -->
        <header class="relative z-10 flex items-center justify-between gap-3">
            <a class="group flex items-center gap-3 transition-transform hover:scale-[1.02]" href="<?= base_url('/') ?>">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-[#3a6347] via-[#4a7c59] to-[#78a886] flex items-center justify-center text-white shadow-xl shadow-[#131e17]/50 ring-1 ring-white/20 shrink-0">
                    <span class="material-symbols-outlined text-[22px] sm:text-[23px] text-white">storefront</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-white leading-none font-serif">Blax</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[#4a7c59]/20 text-[#c8e8d0] border border-[#4a7c59]/30">Marketplace</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#d4ccbf]/70 font-normal mt-0.5 sm:mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px] sm:text-[14px] text-[#8ecf9e]">pin_drop</span>
                        Polomolok, South Cotabato
                    </p>
                </div>
            </a>
            <!-- Mobile Storefront Return Pill -->
            <div class="flex items-center gap-2">
                <a class="lg:hidden inline-flex items-center gap-1 text-[11px] font-semibold text-[#c8e8d0] hover:text-white transition-colors py-1.5 px-3 rounded-full bg-white/10 hover:bg-white/15 border border-white/15 backdrop-blur-sm" href="<?= base_url('/') ?>">
                    <span class="material-symbols-outlined text-[15px]">arrow_back</span>
                    <span>Storefront</span>
                </a>
                <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-[#d4ccbf]">
                    <span class="w-2 h-2 rounded-full bg-[#8ecf9e] animate-pulse"></span>
                    <span>Node Active</span>
                </div>
            </div>
        </header>

        <!-- Center Hero Promo Section -->
        <div class="relative z-10 my-5 sm:my-8 lg:my-auto max-w-xl space-y-4 sm:space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full bg-[#4a7c59]/20 border border-[#8ecf9e]/30 text-[11px] sm:text-xs font-semibold text-[#c8e8d0] backdrop-blur-sm">
                <span class="material-symbols-outlined text-[15px] sm:text-[16px] text-[#8ecf9e]">shield</span>
                Verified Multi-Tenant Commerce Hub
            </div>
            <div class="space-y-2 sm:space-y-3">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-extrabold text-white tracking-tight leading-[1.18] font-serif">
                    Welcome back to <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#c8e8d0] via-[#8ecf9e] to-[#f8e0a8]">Blax</span>
                </h1>
                <p class="text-xs sm:text-sm lg:text-base text-[#d4ccbf]/90 leading-relaxed max-w-lg font-normal">
                    Shop neighborhood merchants, access rush printing order queues, track courier pickups, and manage your local storefront right here in Polomolok.
                </p>
            </div>

            <!-- Bento Value Proposition Cards (Desktop only to keep mobile neat and focused) -->
            <div class="hidden lg:grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                <!-- Feature 1 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#4a7c59]/20 text-[#8ecf9e] flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Verified Local Merchants</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Curated artisanal shops in Polomolok &amp; nearby municipalities.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#78a886]/20 text-[#c8e8d0] flex items-center justify-center shrink-0 border border-[#78a886]/30">
                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Fast Doorstep Delivery</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Same-day order dispatch across South Cotabato routes.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#c4a66a]/20 text-[#f8e0a8] flex items-center justify-center shrink-0 border border-[#c4a66a]/30">
                            <span class="material-symbols-outlined text-[20px]">print</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Rush Printing Hub</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Upload blueprints &amp; marketing files with instant live status.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#705c30]/25 text-[#dcc48e] flex items-center justify-center shrink-0 border border-[#dcc48e]/30">
                            <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Secure QR Pickups</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">One-tap receipt barcodes for instantaneous counter claiming.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Social Proof & Node Security Badge (Desktop only) -->
        <footer class="hidden lg:flex relative z-10 pt-6 border-t border-white/10 flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex -space-x-2.5">
                    <div class="w-8 h-8 rounded-full bg-[#c8e8d0] border-2 border-[#1a241e] flex items-center justify-center text-[#002110] text-[10px] font-extrabold shadow-sm">BM</div>
                    <div class="w-8 h-8 rounded-full bg-[#f8e0a8] border-2 border-[#1a241e] flex items-center justify-center text-[#221a05] text-[10px] font-extrabold shadow-sm">PK</div>
                    <div class="w-8 h-8 rounded-full bg-[#8ecf9e] border-2 border-[#1a241e] flex items-center justify-center text-[#002110] text-[10px] font-extrabold shadow-sm">ST</div>
                    <div class="w-8 h-8 rounded-full bg-[#dcc48e] border-2 border-[#1a241e] flex items-center justify-center text-[#221a05] text-[10px] font-extrabold shadow-sm">+16</div>
                </div>
                <div class="text-xs">
                    <span class="font-bold text-white block tracking-tight">19+ Verified Local Merchants</span>
                    <span class="text-[11px] text-[#d4ccbf]/70">Polomolok Business Network</span>
                </div>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[#d4ccbf] text-xs font-medium backdrop-blur-sm">
                <span class="material-symbols-outlined text-[14px] text-[#8ecf9e]">lock</span>
                <span>Encrypted Session</span>
            </div>
        </footer>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- BOTTOM/RIGHT PANEL: Floating Form Sheet on Mobile, Column on Desktop -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="w-full lg:w-[52%] xl:w-[54%] bg-[#faf6f0] -mt-7 sm:-mt-8 lg:mt-0 relative z-20 rounded-t-[32px] sm:rounded-t-[40px] lg:rounded-none shadow-2xl lg:shadow-none flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-16 overflow-y-auto flex-1">

        <!-- Mobile Sheet Pull Indicator (Visible only on mobile) -->
        <div class="w-12 h-1.5 rounded-full bg-[#d4ccbf] mx-auto mb-5 lg:hidden shrink-0"></div>

        <!-- Top Action Navigation Bar (Desktop) -->
        <nav class="hidden lg:flex items-center justify-between gap-4 max-w-lg w-full mx-auto mb-6">
            <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5e5548] hover:text-[#4a7c59] transition-colors py-2 px-3 rounded-lg hover:bg-[#eae6de]/60" href="<?= base_url('shops') ?>">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span>Browse Shops</span>
            </a>
            <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4a7c59] hover:text-[#3d6749] transition-colors py-2 px-3.5 rounded-lg bg-[#c8e8d0]/40 hover:bg-[#c8e8d0]/60 border border-[#8ecf9e]/40" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-[17px]">arrow_back</span>
                <span>Return to Storefront</span>
            </a>
        </nav>

        <!-- Center Container: Login Card -->
        <main class="my-auto py-2 sm:py-4 max-w-lg w-full mx-auto">
            <div class="space-y-6">

                <!-- Card Header & Badge -->
                <div class="flex items-start justify-between gap-4 pb-1">
                    <div class="space-y-1.5">
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#2e3230] tracking-tight font-serif">Sign In</h2>
                        <p class="text-sm text-[#6b6358] font-normal leading-relaxed" id="role-subtitle">
                            <?= $isShopOwner ? 'Sign in to access your vendor portal, orders, and print queue.' : 'Access your customer account, cart, and active orders.' ?>
                        </p>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#c8e8d0]/30 border border-[#8ecf9e]/50 text-[#2a6038] text-xs font-semibold shrink-0">
                        <span class="material-symbols-outlined text-[15px] text-[#4a7c59]">verified_user</span>
                        <span>Secure Access</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-rose-600 shrink-0">error</span>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-emerald-600 shrink-0">check_circle</span>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                <?php endif; ?>

                <!-- Segmented Role Switcher Tab -->
                <div aria-label="Account Type" class="p-1 bg-[#f0ece4] rounded-2xl grid grid-cols-2 gap-1 border border-[#e4e0d8]" role="tablist">
                    <button aria-selected="<?= $isShopOwner ? 'false' : 'true' ?>" 
                            class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer <?= $isShopOwner ? 'text-[#5e5548] hover:text-[#2e3230] hover:bg-white/60' : 'text-white bg-[#4a7c59] shadow-sm' ?>" 
                            id="tab-customer" 
                            role="tab" 
                            type="button">
                        <span class="material-symbols-outlined text-[17px]">person</span>
                        <span>Customer</span>
                    </button>
                    <button aria-selected="<?= $isShopOwner ? 'true' : 'false' ?>" 
                            class="py-2.5 px-3 text-xs font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer <?= $isShopOwner ? 'text-white bg-[#4a7c59] shadow-sm' : 'text-[#5e5548] hover:text-[#2e3230] hover:bg-white/60' ?>" 
                            id="tab-merchant" 
                            role="tab" 
                            type="button">
                        <span class="material-symbols-outlined text-[17px]">store</span>
                        <span>Shop Owner</span>
                    </button>
                </div>

                <!-- Authentication Form -->
                <form action="<?= base_url('login') ?>" class="space-y-4 pt-1" id="login-form" method="POST">
                    <?= csrf_field() ?>
                    <input id="account_role" name="role" type="hidden" value="<?= $isShopOwner ? 'merchant' : 'customer' ?>">

                    <!-- Identifier / Email Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#5e5548]" for="email" id="email-label">
                            <?= $isShopOwner ? 'Merchant Email or Store Identifier' : 'Email Address' ?>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#74796e]">
                                <span class="material-symbols-outlined text-[19px]">mail</span>
                            </div>
                            <input class="w-full pl-10 pr-3.5 py-3 text-sm text-[#2e3230] placeholder:text-[#a0a59a] bg-transparent rounded-xl focus:outline-none" 
                                   id="email" 
                                   name="email" 
                                   placeholder="name@example.com" 
                                   required 
                                   type="email"
                                   autocomplete="email"
                                   value="<?= esc(old('email') ?? '') ?>">
                        </div>
                    </div>

                    <!-- Password Input with Toggle Button -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e5548]" for="password">Password</label>
                            <a class="text-xs font-semibold text-[#4a7c59] hover:text-[#2a6038] hover:underline transition-colors" href="<?= base_url('forgot-password') ?>">Forgot password?</a>
                        </div>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#74796e]">
                                <span class="material-symbols-outlined text-[19px]">lock</span>
                            </div>
                            <input class="w-full pl-10 pr-10 py-3 text-sm text-[#2e3230] placeholder:text-[#a0a59a] bg-transparent rounded-xl focus:outline-none" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password" 
                                   required 
                                   type="password"
                                   autocomplete="current-password">
                            <button aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#74796e] hover:text-[#2e3230] transition-colors focus:outline-none cursor-pointer" id="toggle-password-btn" type="button">
                                <span class="material-symbols-outlined text-[19px]" id="password-toggle-icon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Device Checkbox -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                            <input class="w-4 h-4 rounded text-[#4a7c59] border-[#c4c8bc] focus:ring-[#4a7c59] focus:ring-offset-0 cursor-pointer transition accent-[#4a7c59]" id="remember" name="remember" type="checkbox">
                            <span class="text-xs font-medium text-[#5e5548] group-hover:text-[#2e3230] transition-colors">Remember me on this device</span>
                        </label>
                    </div>

                    <!-- Primary Action Submit Button: Terra Organic Green CTA -->
                    <button class="w-full py-3.5 px-5 rounded-xl bg-[#4a7c59] hover:bg-[#3d6749] text-white font-bold text-sm shadow-md shadow-[#4a7c59]/25 hover:shadow-lg hover:shadow-[#4a7c59]/35 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer mt-2" type="submit">
                        <span class="material-symbols-outlined text-[19px]">login</span>
                        <span>Sign In to Account</span>
                    </button>
                </form>

                <!-- Secondary Link Callouts -->
                <div class="pt-4 border-t border-[#e4e0d8] space-y-2.5 text-center text-xs">
                    <p class="text-[#5e5548]">Don't have an account?
                        <a class="font-bold text-[#4a7c59] hover:text-[#2a6038] hover:underline ml-1" href="<?= base_url('signup') ?>">Create customer account</a>
                    </p>
                    <p class="text-[#6b6358]">Are you a merchant?
                        <a class="font-bold text-[#2e3230] hover:text-[#4a7c59] hover:underline ml-1" href="<?= base_url('merchant-signup') ?>">Register your shop</a>
                    </p>
                </div>

                <!-- Administrator Helper Notice -->
                <div class="p-3.5 rounded-xl bg-[#f0ece4] border border-[#e4e0d8] flex items-start gap-3 text-[11.5px] text-[#5e5548] leading-normal">
                    <span class="material-symbols-outlined text-[17px] text-[#74796e] shrink-0 mt-0.5">info</span>
                    <span>Administrator accounts can authenticate via their master portal credentials or shop owner tab.</span>
                </div>
            </div>

            <!-- Encrypted Multi-Tenant Sub-Badge -->
            <div class="mt-5 flex items-center justify-center gap-2 text-center">
                <span class="material-symbols-outlined text-[15px] text-[#74796e]">encrypted</span>
                <span class="text-[11.5px] text-[#74796e] font-medium">Encrypted Polomolok Multi-Tenant Authentication</span>
            </div>
        </main>

        <!-- Bottom Marketplace Footer Area -->
        <footer class="mt-6 pt-4 text-center text-xs text-[#74796e] flex flex-wrap items-center justify-between gap-4 border-t border-[#e4e0d8] max-w-lg w-full mx-auto">
            <p>&copy; <?= date('Y') ?> Blax Marketplace &bull; Polomolok, South Cotabato</p>
            <div class="flex items-center gap-3">
                <a class="hover:text-[#2e3230] transition-colors" href="<?= base_url('terms') ?>">Terms</a>
                <span class="text-[#c4c8bc]">&bull;</span>
                <a class="hover:text-[#2e3230] transition-colors" href="<?= base_url('privacy') ?>">Privacy</a>
            </div>
        </footer>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabCustomer = document.getElementById('tab-customer');
    const tabMerchant = document.getElementById('tab-merchant');
    const accountRoleInput = document.getElementById('account_role');
    const roleSubtitle = document.getElementById('role-subtitle');
    const emailLabel = document.getElementById('email-label');

    function setCustomerTab() {
        tabCustomer.classList.remove('text-[#5e5548]', 'hover:text-[#2e3230]', 'hover:bg-white/60');
        tabCustomer.classList.add('text-white', 'bg-[#4a7c59]', 'shadow-sm');
        tabCustomer.setAttribute('aria-selected', 'true');

        tabMerchant.classList.remove('text-white', 'bg-[#4a7c59]', 'shadow-sm');
        tabMerchant.classList.add('text-[#5e5548]', 'hover:text-[#2e3230]', 'hover:bg-white/60');
        tabMerchant.setAttribute('aria-selected', 'false');

        accountRoleInput.value = 'customer';
        roleSubtitle.textContent = 'Access your customer account, cart, and active orders.';
        emailLabel.textContent = 'Email Address';
    }

    function setMerchantTab() {
        tabMerchant.classList.remove('text-[#5e5548]', 'hover:text-[#2e3230]', 'hover:bg-white/60');
        tabMerchant.classList.add('text-white', 'bg-[#4a7c59]', 'shadow-sm');
        tabMerchant.setAttribute('aria-selected', 'true');

        tabCustomer.classList.remove('text-white', 'bg-[#4a7c59]', 'shadow-sm');
        tabCustomer.classList.add('text-[#5e5548]', 'hover:text-[#2e3230]', 'hover:bg-white/60');
        tabCustomer.setAttribute('aria-selected', 'false');

        accountRoleInput.value = 'merchant';
        roleSubtitle.textContent = 'Sign in to access your vendor portal, orders, and print queue.';
        emailLabel.textContent = 'Merchant Email or Store Identifier';
    }

    if (tabCustomer && tabMerchant) {
        tabCustomer.addEventListener('click', setCustomerTab);
        tabMerchant.addEventListener('click', setMerchantTab);
    }

    // Password visibility toggle
    const togglePasswordBtn = document.getElementById('toggle-password-btn');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-toggle-icon');

    if (togglePasswordBtn && passwordInput && passwordIcon) {
        togglePasswordBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
            togglePasswordBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    }
});
</script>
<?= $this->endSection() ?>
