<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[#1d2e22]">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TOP/LEFT PANEL: Customer Perks & Marketplace Showcase (Top on Mobile)-->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="w-full lg:w-[48%] xl:w-[46%] bg-[#1d2e22] text-[#f4f8f5] relative flex flex-col justify-between pt-7 pb-14 px-5 sm:px-8 sm:py-10 lg:p-12 xl:p-14 overflow-hidden shrink-0">
        <!-- Background decorative ambient circles -->
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-[#4a7c59]/25 glow-blur pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 rounded-full bg-[#c4a66a]/15 glow-blur pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-[#3d6749]/30 glow-blur pointer-events-none"></div>
        <!-- Subtle background grid pattern -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <!-- Top Hero Header & Customer Perks Content -->
        <div class="relative z-10 space-y-4 sm:space-y-6">
            <header class="flex items-center justify-between gap-3">
                <a class="flex items-center gap-3 group transition-transform hover:scale-[1.02]" href="<?= base_url('/') ?>">
                    <img src="<?= base_url('assets/brand/logo-mark.svg') ?>" alt="Blax" width="44" height="44" class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl shrink-0 shadow-xl shadow-[#131e17]/50 ring-1 ring-white/20">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-white leading-none font-serif">Blax</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[#33553d] text-[#8ecf9e] border border-[#4a7c59]/50">
                                Polomolok
                            </span>
                        </div>
                        <span class="text-[11px] sm:text-xs text-[#d4ccbf]/70 font-normal mt-0.5 block">South Cotabato Multi-Tenant Commerce Hub</span>
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

            <!-- Main Hero Headline & Customer Copy -->
            <div class="space-y-2 sm:space-y-3 pt-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#2a4332] border border-[#3d6749] text-[11px] sm:text-xs font-semibold text-[#8ecf9e]">
                    <span class="w-2 h-2 rounded-full bg-[#8ecf9e] animate-pulse"></span>
                    <span>Verified Shopper Network</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-serif text-white tracking-tight leading-snug">
                    Your neighborhood market, right at your fingertips.
                </h1>
                <p class="text-xs sm:text-sm text-[#c8e8d0]/80 leading-relaxed max-w-md font-normal">
                    Join thousands of local shoppers. Browse neighborhood stores, send documents for same-day rush printing, and enjoy trackable doorstep delivery across Polomolok.
                </p>
            </div>

            <!-- 4 Tailored Customer Value Cards (Desktop only to prevent mobile clutter) -->
            <div class="hidden lg:grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <div class="glass-card-dark p-3.5 rounded-2xl border border-white/10 backdrop-blur-sm space-y-1.5">
                    <div class="w-8 h-8 rounded-xl bg-[#4a7c59]/20 text-[#8ecf9e] flex items-center justify-center border border-[#8ecf9e]/30">
                        <span class="material-symbols-outlined text-[19px]">local_offer</span>
                    </div>
                    <h2 class="text-xs font-bold text-white">Exclusive Local Deals</h2>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-tight">Save on top products and support local shops</p>
                </div>
                <div class="glass-card-dark p-3.5 rounded-2xl border border-white/10 backdrop-blur-sm space-y-1.5">
                    <div class="w-8 h-8 rounded-xl bg-[#c4a66a]/20 text-[#f8e0a8] flex items-center justify-center border border-[#c4a66a]/30">
                        <span class="material-symbols-outlined text-[19px]">print</span>
                    </div>
                    <h2 class="text-xs font-bold text-white">Instant Rush Printing</h2>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-tight">Upload files for quick queue printing &amp; proofing</p>
                </div>
                <div class="glass-card-dark p-3.5 rounded-2xl border border-white/10 backdrop-blur-sm space-y-1.5">
                    <div class="w-8 h-8 rounded-xl bg-[#78a886]/20 text-[#c8e8d0] flex items-center justify-center border border-[#78a886]/30">
                        <span class="material-symbols-outlined text-[19px]">local_shipping</span>
                    </div>
                    <h2 class="text-xs font-bold text-white">Doorstep Delivery &amp; GPS</h2>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-tight">Track couriers straight to your door in town</p>
                </div>
                <div class="glass-card-dark p-3.5 rounded-2xl border border-white/10 backdrop-blur-sm space-y-1.5">
                    <div class="w-8 h-8 rounded-xl bg-[#705c30]/25 text-[#dcc48e] flex items-center justify-center border border-[#dcc48e]/30">
                        <span class="material-symbols-outlined text-[19px]">qr_code_scanner</span>
                    </div>
                    <h2 class="text-xs font-bold text-white">One-Tap QR Pickups</h2>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-tight">Effortless claim verification with account QR</p>
                </div>
            </div>

            <!-- Member Perk Metric Micro-Card (Desktop only) -->
            <div class="hidden lg:flex p-3.5 rounded-2xl bg-[#2a4332]/50 border border-[#3d6749]/80 items-center justify-between text-xs backdrop-blur-sm">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-[#8ecf9e] text-[20px]">electric_moped</span>
                    <span class="text-[#c8e8d0]/90">Average town delivery <strong class="text-white font-semibold">under 45 mins</strong></span>
                </div>
                <span class="text-[10px] font-bold text-[#8ecf9e] uppercase tracking-wider bg-[#33553d] px-2.5 py-0.5 rounded-full border border-[#4a7c59]">500+ Items</span>
            </div>
        </div>

        <!-- Bottom Hero Indicator (Desktop only) -->
        <footer class="hidden lg:flex relative z-10 pt-6 mt-6 border-t border-[#2a4332] items-center justify-between text-xs text-[#9aa39c]">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-[#8ecf9e]">shield_lock</span>
                <span>TLS 1.3 256-bit Encrypted Session</span>
            </div>
            <span class="font-medium text-[#c8e8d0]/60">Polomolok, PH</span>
        </footer>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- BOTTOM/RIGHT PANEL: Customer Registration Sheet on Mobile           -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="w-full lg:w-[52%] xl:w-[54%] bg-[#faf7f2] -mt-7 sm:-mt-8 lg:mt-0 relative z-20 rounded-t-[32px] sm:rounded-t-[40px] lg:rounded-none shadow-2xl lg:shadow-none flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-16 overflow-y-auto flex-1">

        <!-- Mobile Sheet Pull Indicator (Visible only on mobile) -->
        <div class="w-12 h-1.5 rounded-full bg-[#d4ccbf] mx-auto mb-5 lg:hidden shrink-0"></div>

        <!-- Top Utility Navigation (Desktop) -->
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

        <!-- Centered Form Wrapper -->
        <main class="w-full max-w-lg mx-auto my-auto py-2 sm:py-4 space-y-6">

            <!-- Heading block -->
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wider text-[#4a7c59] font-bold">
                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                    <span>Shopper Registration</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold font-serif text-[#2e3230] tracking-tight">Create Your Account</h2>
                <p class="text-xs sm:text-sm text-[#6b6358] leading-relaxed">
                    Register your customer account to start shopping and ordering printing from verified Polomolok merchants.
                </p>
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

            <!-- Account Type Switcher (Segmented Tab) -->
            <div class="bg-[#f0ece4] p-1 rounded-2xl grid grid-cols-2 gap-1 border border-[#e4e0d8]" role="tablist">
                <a class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2 bg-[#4a7c59] text-white shadow-sm" href="<?= base_url('signup') ?>">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    <span>Customer</span>
                </a>
                <a class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2 text-[#5e5548] hover:text-[#2e3230] hover:bg-white/60" href="<?= base_url('merchant-signup') ?>">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>Shop Owner</span>
                </a>
            </div>

            <!-- Registration Form -->
            <form action="<?= base_url('signup') ?>" class="space-y-4" id="signup-form" method="POST" novalidate>
                <?= csrf_field() ?>

                <!-- First & Last Name Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-[#5e5548] uppercase tracking-wider flex items-center justify-between" for="first-name">
                            <span>First Name <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">person</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="first-name" 
                                   name="first_name" 
                                   placeholder="e.g. Maria" 
                                   required 
                                   type="text"
                                   value="<?= esc(old('first_name') ?? '') ?>">
                        </div>
                        <span class="field-error text-[11px] text-rose-600 font-medium hidden" id="err-first-name"></span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-[#5e5548] uppercase tracking-wider flex items-center justify-between" for="last-name">
                            <span>Last Name <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">person</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="last-name" 
                                   name="last_name" 
                                   placeholder="e.g. Santos" 
                                   required 
                                   type="text"
                                   value="<?= esc(old('last_name') ?? '') ?>">
                        </div>
                        <span class="field-error text-[11px] text-rose-600 font-medium hidden" id="err-last-name"></span>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-[#5e5548] uppercase tracking-wider flex items-center justify-between" for="email">
                        <span>Email Address <span class="text-rose-500">*</span></span>
                    </label>
                    <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">mail</span>
                        <input autocomplete="email" 
                               class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                               id="email" 
                               name="email" 
                               placeholder="you@example.com" 
                               required 
                               type="email"
                               value="<?= esc(old('email') ?? '') ?>">
                    </div>
                    <span class="field-error text-[11px] text-rose-600 font-medium hidden" id="err-email"></span>
                </div>

                <!-- Cellphone Number -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#5e5548] uppercase tracking-wider block" for="phone">Cellphone Number</label>
                        <span class="text-[11px] font-medium text-[#74796e]">(Optional)</span>
                    </div>
                    <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">call</span>
                        <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                               id="phone" 
                               name="phone" 
                               placeholder="0912 345 6789" 
                               type="tel"
                               value="<?= esc(old('phone') ?? '') ?>">
                    </div>
                    <span class="field-error text-[11px] text-rose-600 font-medium hidden" id="err-phone"></span>
                </div>

                <!-- Password & Confirm Password Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-[#5e5548] uppercase tracking-wider flex items-center justify-between" for="password">
                            <span>Password <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">lock</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-10 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="password" 
                                   minlength="8" 
                                   name="password" 
                                   placeholder="Min. 8 chars" 
                                   required 
                                   type="password">
                            <button aria-label="Toggle password visibility" class="toggle-pwd-btn absolute right-2.5 top-1/2 -translate-y-1/2 text-[#74796e] hover:text-[#2e3230] p-1 transition-colors" data-target="password" type="button">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#74796e]">At least 8 characters</p>
                        <span class="field-error text-[11px] text-rose-600 font-medium hidden" id="err-password"></span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-[#5e5548] uppercase tracking-wider flex items-center justify-between" for="confirm-password">
                            <span>Confirm Password <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">lock</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-10 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="confirm-password" 
                                   minlength="8" 
                                   name="confirm_password" 
                                   placeholder="Repeat password" 
                                   required 
                                   type="password">
                            <button aria-label="Toggle confirm password visibility" class="toggle-pwd-btn absolute right-2.5 top-1/2 -translate-y-1/2 text-[#74796e] hover:text-[#2e3230] p-1 transition-colors" data-target="confirm-password" type="button">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#74796e]">Must match password</p>
                        <span class="field-error text-[11px] text-rose-600 font-medium hidden" id="err-confirm-password"></span>
                    </div>
                </div>

                <!-- Consent & Terms -->
                <div class="p-3.5 rounded-xl bg-[#f0ece4] border border-[#e4e0d8] text-[11.5px] text-[#5e5548] leading-relaxed flex items-start gap-2.5">
                    <input checked class="mt-0.5 rounded border-[#c4c8bc] text-[#4a7c59] focus:ring-[#4a7c59]/30 w-4 h-4 cursor-pointer accent-[#4a7c59]" id="terms-agree" type="checkbox" required>
                    <label class="flex-1 cursor-pointer select-none" for="terms-agree">
                        By creating an account, you agree to Blax's <a class="text-[#4a7c59] hover:underline font-bold" href="<?= base_url('terms') ?>">Terms of Service</a> and acknowledge local delivery policies in Polomolok.
                    </label>
                </div>

                <!-- Submit CTA -->
                <button class="w-full bg-[#4a7c59] hover:bg-[#3d6749] active:bg-[#2a6038] text-white font-bold py-3.5 px-5 rounded-xl shadow-md hover:shadow-lg transition-all text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center gap-2 mt-3 cursor-pointer" id="signup-submit-btn" type="submit">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    <span>Create Customer Account</span>
                </button>
            </form>

            <!-- Bottom Links & Navigation -->
            <div class="pt-4 border-t border-[#e4e0d8] space-y-2.5 text-center text-xs">
                <p class="text-[#5e5548]">
                    Already have an account? 
                    <a class="text-[#4a7c59] hover:text-[#2a6038] font-bold hover:underline transition-colors ml-1" href="<?= base_url('login') ?>">
                        Sign in
                    </a>
                </p>
                <p class="text-[#6b6358]">
                    Are you a business in Polomolok? 
                    <a class="text-[#2e3230] hover:text-[#4a7c59] font-semibold hover:underline ml-1" href="<?= base_url('merchant-signup') ?>">
                        Register as Shop Owner
                    </a>
                </p>
            </div>
        </main>

        <!-- Bottom Footer Meta Info -->
        <footer class="pt-6 border-t border-[#e4e0d8] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#74796e] gap-2 max-w-lg w-full mx-auto">
            <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-[#4a7c59]">shield_lock</span>
                <span>Encrypted Multi-Tenant Authentication</span>
            </div>
            <span>&copy; <?= date('Y') ?> Blax Marketplace &bull; Polomolok</span>
        </footer>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Password visibility toggle behavior
    document.querySelectorAll('.toggle-pwd-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = btn.querySelector('.material-symbols-outlined');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });

    // Client-side quick password matching check
    const form = document.getElementById('signup-form');
    const pwd = document.getElementById('password');
    const confirmPwd = document.getElementById('confirm-password');
    const errConfirm = document.getElementById('err-confirm-password');

    if (form && pwd && confirmPwd) {
        form.addEventListener('submit', (e) => {
            if (pwd.value !== confirmPwd.value) {
                e.preventDefault();
                if (errConfirm) {
                    errConfirm.textContent = 'Passwords do not match.';
                    errConfirm.classList.remove('hidden');
                }
                confirmPwd.focus();
            } else {
                if (errConfirm) errConfirm.classList.add('hidden');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>