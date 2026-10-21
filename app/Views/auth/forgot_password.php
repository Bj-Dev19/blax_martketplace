<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[#1a241e]">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TOP/LEFT PANEL: Recovery Hero Banner (Top on Mobile, Left on Desktop) -->
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
                <a class="lg:hidden inline-flex items-center gap-1 text-[11px] font-semibold text-[#c8e8d0] hover:text-white transition-colors py-1.5 px-3 rounded-full bg-white/10 hover:bg-white/15 border border-white/15 backdrop-blur-sm" href="<?= base_url('login') ?>">
                    <span class="material-symbols-outlined text-[15px]">login</span>
                    <span>Sign In</span>
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
                    Secure Account <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#c8e8d0] via-[#8ecf9e] to-[#f8e0a8]">Recovery</span>
                </h1>
                <p class="text-xs sm:text-sm lg:text-base text-[#d4ccbf]/90 leading-relaxed max-w-lg font-normal">
                    Protecting your local storefront, orders, and wallet balance with cryptographically signed verification links across South Cotabato.
                </p>
            </div>

            <!-- Bento Value Proposition Cards (Desktop only to prevent mobile clutter) -->
            <div class="hidden lg:grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                <!-- Feature 1 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#4a7c59]/20 text-[#8ecf9e] flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Verified Identity</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">One-time token validation protects your credentials.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#78a886]/20 text-[#c8e8d0] flex items-center justify-center shrink-0 border border-[#78a886]/30">
                            <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Instant Reset Link</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Receive secure authorization straight to your inbox.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#c4a66a]/20 text-[#f8e0a8] flex items-center justify-center shrink-0 border border-[#c4a66a]/30">
                            <span class="material-symbols-outlined text-[20px]">storefront</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Merchant &amp; Customer</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Universal recovery across all marketplace roles.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#705c30]/25 text-[#dcc48e] flex items-center justify-center shrink-0 border border-[#dcc48e]/30">
                            <span class="material-symbols-outlined text-[20px]">support_agent</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Local Support</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Direct assistance from the Polomolok operations team.</p>
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

        <!-- Center Container: Forgot Password Card -->
        <main class="my-auto py-2 sm:py-4 max-w-lg w-full mx-auto">
            <div class="space-y-6">

                <!-- Header Block -->
                <div class="flex flex-col items-center text-center">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#f0ece4] border border-[#e4e0d8] text-[#705c30] mb-3 shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                        <span class="text-xs uppercase tracking-wider font-bold">Self-Service Recovery</span>
                    </div>
                    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-[#2e3230] tracking-tight">Forgot Password?</h1>
                    <p class="text-xs sm:text-sm text-[#6b6358] mt-2 leading-relaxed max-w-sm">
                        No worries! Enter your registered email address and we'll dispatch a verified link to restore your marketplace access.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e4e0d8] space-y-5">
                    
                    <!-- Role Informational Box -->
                    <div class="flex items-center gap-3.5 p-3.5 rounded-xl bg-[#f5f1ea] border border-[#e4e0d8] text-[#5e5548]">
                        <div class="w-9 h-9 rounded-full bg-[#c8e8d0] text-[#002110] flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">info</span>
                        </div>
                        <p class="text-xs leading-relaxed">
                            Works for both <span class="font-bold text-[#2e3230]">customer accounts</span> and <span class="font-bold text-[#2e3230]">registered merchant portals</span> in Polomolok.
                        </p>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-rose-600 shrink-0">error</span>
                            <span><?= session()->getFlashdata('error') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="p-4 rounded-xl bg-[#c8e8d0]/40 border border-[#8ecf9e]/60 text-[#1d2e22] space-y-1.5" id="success-feedback">
                            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide text-[#2a6038]">
                                <span class="material-symbols-outlined text-[18px]">mark_email_read</span>
                                <span>Check Your Inbox</span>
                            </div>
                            <p class="text-xs leading-relaxed">
                                <?= session()->getFlashdata('success') ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form class="space-y-4" id="recovery-form" action="<?= base_url('forgot-password') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e5548]" for="recovery-email">
                                Registered Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl border border-[#c4c8bc] bg-white backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#74796e]">
                                    <span class="material-symbols-outlined text-[19px]">mail</span>
                                </div>
                                <input autocomplete="email" 
                                       class="block w-full rounded-xl bg-transparent pl-10 pr-4 py-3 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:outline-none" 
                                       id="recovery-email" 
                                       name="email" 
                                       placeholder="you@example.com" 
                                       required 
                                       type="email"
                                       value="<?= esc(old('email') ?? '') ?>">
                            </div>
                            <p class="text-[11px] text-[#74796e] flex items-center gap-1.5 pt-1">
                                <span class="material-symbols-outlined text-[14px]">shield_lock</span>
                                <span>We'll transmit a cryptographically signed one-time link.</span>
                            </p>
                        </div>

                        <button class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#4a7c59] hover:bg-[#3d6749] px-5 py-3.5 text-sm font-bold text-white transition-all duration-200 active:scale-[0.99] shadow-md hover:shadow-lg cursor-pointer" 
                                id="submit-button" 
                                type="submit">
                            <span class="tracking-wide uppercase text-xs sm:text-sm" id="button-text">Send Reset Link</span>
                            <span class="material-symbols-outlined text-[19px] transition-transform duration-200 group-hover:translate-x-1" id="button-icon">send</span>
                        </button>
                    </form>

                    <!-- Navigation Links -->
                    <div class="pt-4 border-t border-[#e4e0d8] flex flex-col items-center gap-3">
                        <a class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#4a7c59] hover:text-[#3d6749] transition-colors" href="<?= base_url('login') ?>">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Back to Sign In</span>
                        </a>
                        <div class="flex items-center gap-2 text-xs text-[#74796e]">
                            <span>Experiencing issues?</span>
                            <a class="font-semibold text-[#5e5548] hover:text-[#4a7c59] transition-colors underline" href="mailto:support@blax.ph">
                                Contact Local Support
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer Encryption Note -->
                <div class="flex flex-col items-center gap-1.5 text-center">
                    <div class="inline-flex items-center gap-1.5 text-[11px] text-[#74796e] uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#4a7c59] animate-pulse"></span>
                        <span>TLS 1.3 256-Bit Encrypted Session</span>
                        <span class="text-[#c4c8bc]">&bull;</span>
                        <span>Polomolok Hub</span>
                    </div>
                    <p class="text-[11px] text-[#74796e]/80">
                        &copy; <?= date('Y') ?> Blax Marketplace &bull; Local Commerce Node
                    </p>
                </div>
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
    const form = document.getElementById('recovery-form');
    const submitBtn = document.getElementById('submit-button');
    const btnText = document.getElementById('button-text');
    const btnIcon = document.getElementById('button-icon');

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'Transmitting Token...';
            if (btnIcon) {
                btnIcon.textContent = 'autorenew';
                btnIcon.classList.add('animate-spin');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
