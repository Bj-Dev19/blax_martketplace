<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="min-h-screen w-full flex flex-col-reverse lg:flex-row">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- LEFT PANEL: Promotional / Value Proposition Showcase               -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="lg:w-[48%] xl:w-[46%] bg-[#1a241e] relative flex flex-col justify-between p-8 sm:p-12 xl:p-16 text-white overflow-hidden shrink-0">
        <!-- Background atmospheric decorative glows: Earthy Forest & Warm Amber -->
        <div class="absolute -top-40 -left-40 w-[480px] h-[480px] rounded-full bg-[#4a7c59]/25 glow-blur pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 rounded-full bg-[#c4a66a]/15 glow-blur pointer-events-none"></div>
        <div class="absolute -bottom-32 left-10 w-96 h-96 rounded-full bg-[#2a6038]/20 glow-blur pointer-events-none"></div>
        <!-- Subtle background grid pattern -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <!-- Brand Header -->
        <header class="relative z-10 flex items-center justify-between">
            <a class="group flex items-center gap-3.5 transition-transform hover:scale-[1.02]" href="<?= base_url('/') ?>">
                <img src="<?= base_url('assets/brand/logo-mark.svg') ?>" alt="Blax" width="44" height="44" class="w-11 h-11 rounded-2xl shrink-0 shadow-xl shadow-[#131e17]/50 ring-1 ring-white/20">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-2xl tracking-tight text-white leading-none font-serif">Blax</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-[#4a7c59]/20 text-[#c8e8d0] border border-[#4a7c59]/30">Marketplace</span>
                    </div>
                    <p class="text-xs text-[#d4ccbf]/70 font-normal mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] text-[#8ecf9e]">pin_drop</span>
                        Polomolok, South Cotabato
                    </p>
                </div>
            </a>
            <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-[#d4ccbf]">
                <span class="w-2 h-2 rounded-full bg-[#8ecf9e] animate-pulse"></span>
                <span>Node Active</span>
            </div>
        </header>

        <!-- Center Hero Promo Section -->
        <div class="relative z-10 my-10 lg:my-auto max-w-xl space-y-7">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#4a7c59]/20 border border-[#8ecf9e]/30 text-xs font-semibold text-[#c8e8d0] backdrop-blur-sm">
                <span class="material-symbols-outlined text-[16px] text-[#8ecf9e]">lock_reset</span>
                Account Security Hardening
            </div>
            <div class="space-y-3">
                <h1 class="text-3xl sm:text-4xl xl:text-5xl font-extrabold text-white tracking-tight leading-[1.15] font-serif">
                    Set a New <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#c8e8d0] via-[#8ecf9e] to-[#f8e0a8]">Password</span>
                </h1>
                <p class="text-sm sm:text-base text-[#d4ccbf]/90 leading-relaxed max-w-lg font-normal">
                    Strengthen your local storefront access, purchase records, and merchant earnings with modern credential security.
                </p>
            </div>

            <!-- Bento Security Recommendation Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                <!-- Feature 1 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#4a7c59]/20 text-[#8ecf9e] flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[20px]">pin</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">8+ Characters</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Combine uppercase, numbers, and symbols for resilience.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#78a886]/20 text-[#c8e8d0] flex items-center justify-center shrink-0 border border-[#78a886]/30">
                            <span class="material-symbols-outlined text-[20px]">sync_saved_locally</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Instant Sync</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Immediately updates across both mobile and desktop sessions.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#c4a66a]/20 text-[#f8e0a8] flex items-center justify-center shrink-0 border border-[#c4a66a]/30">
                            <span class="material-symbols-outlined text-[20px]">encrypted</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Argon2/Bcrypt Hash</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Protected by salted cryptographic one-way hashing.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card-dark rounded-2xl p-4 transition-all duration-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-[#705c30]/25 text-[#dcc48e] flex items-center justify-center shrink-0 border border-[#dcc48e]/30">
                            <span class="material-symbols-outlined text-[20px]">shield</span>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-white tracking-wide">Session Invalidation</h2>
                            <p class="text-[11.5px] text-[#d4ccbf]/80 leading-snug mt-1">Single-use reset tokens automatically self-destruct.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Social Proof & Node Security Badge -->
        <footer class="relative z-10 pt-6 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
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
    <!-- RIGHT PANEL: Reset Password Form                                   -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="lg:w-[52%] xl:w-[54%] bg-[#faf6f0] flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-16 overflow-y-auto">

        <!-- Top Action Navigation Bar -->
        <nav class="flex items-center justify-between gap-4 max-w-lg w-full mx-auto mb-6 lg:mb-0">
            <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5e5548] hover:text-[#4a7c59] transition-colors py-2 px-3 rounded-lg hover:bg-[#eae6de]/60" href="<?= base_url('shops') ?>">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span>Browse Shops</span>
            </a>
            <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4a7c59] hover:text-[#3d6749] transition-colors py-2 px-3.5 rounded-lg bg-[#c8e8d0]/40 hover:bg-[#c8e8d0]/60 border border-[#8ecf9e]/40" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-[17px]">arrow_back</span>
                <span>Return to Storefront</span>
            </a>
        </nav>

        <!-- Center Container: Reset Password Card -->
        <main class="my-auto py-4 max-w-lg w-full mx-auto">
            <div class="space-y-6">

                <!-- Header Block -->
                <div class="flex flex-col items-center text-center">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#f0ece4] border border-[#e4e0d8] text-[#705c30] mb-3 shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">key</span>
                        <span class="text-xs uppercase tracking-wider font-bold">Credential Update</span>
                    </div>
                    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-[#2e3230] tracking-tight">Set New Password</h1>
                    <p class="text-xs sm:text-sm text-[#6b6358] mt-2 leading-relaxed max-w-sm">
                        Please choose a secure new password of at least 8 characters to finalize recovery.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#e4e0d8] space-y-5">

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] text-rose-600 shrink-0">error</span>
                            <span><?= session()->getFlashdata('error') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="p-4 rounded-xl bg-[#c8e8d0]/40 border border-[#8ecf9e]/60 text-[#1d2e22] space-y-1.5">
                            <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wide text-[#2a6038]">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                <span>Success</span>
                            </div>
                            <p class="text-xs leading-relaxed">
                                <?= session()->getFlashdata('success') ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form class="space-y-4" id="reset-password-form" action="<?= base_url('reset-password/' . esc($token)) ?>" method="POST">
                        <?= csrf_field() ?>

                        <!-- New Password Field -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e5548]" for="password">
                                New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl border border-[#c4c8bc] bg-white backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#74796e]">
                                    <span class="material-symbols-outlined text-[19px]">lock</span>
                                </div>
                                <input autocomplete="new-password" 
                                       autofocus
                                       class="block w-full rounded-xl bg-transparent pl-10 pr-11 py-3 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:outline-none" 
                                       id="password" 
                                       minlength="8" 
                                       name="password" 
                                       placeholder="At least 8 characters" 
                                       required 
                                       type="password">
                                <button class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#74796e] hover:text-[#2e3230] cursor-pointer" 
                                        id="toggle-password" 
                                        tabindex="-1" 
                                        type="button">
                                    <span class="material-symbols-outlined text-[19px]" id="password-toggle-icon">visibility</span>
                                </button>
                            </div>
                            <!-- Live Strength Indicator -->
                            <div class="pt-1.5 space-y-1" id="strength-meter-container">
                                <div class="h-1.5 w-full bg-[#f0ece4] rounded-full overflow-hidden flex">
                                    <div class="h-full w-0 transition-all duration-300 rounded-full" id="strength-bar"></div>
                                </div>
                                <div class="flex justify-between items-center text-[11px] text-[#74796e]">
                                    <span id="strength-label">Password strength</span>
                                    <span class="text-[10px]" id="strength-hint">Min. 8 characters</span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#5e5548]" for="confirm_password">
                                Confirm New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl border border-[#c4c8bc] bg-white backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#74796e]">
                                    <span class="material-symbols-outlined text-[19px]">lock_clock</span>
                                </div>
                                <input autocomplete="new-password" 
                                       class="block w-full rounded-xl bg-transparent pl-10 pr-11 py-3 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:outline-none" 
                                       id="confirm_password" 
                                       minlength="8" 
                                       name="confirm_password" 
                                       placeholder="Repeat new password" 
                                       required 
                                       type="password">
                                <button class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#74796e] hover:text-[#2e3230] cursor-pointer" 
                                        id="toggle-confirm-password" 
                                        tabindex="-1" 
                                        type="button">
                                    <span class="material-symbols-outlined text-[19px]" id="confirm-toggle-icon">visibility</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-[#74796e] flex items-center gap-1.5 pt-1" id="match-message">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span>Passwords must match exactly.</span>
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#4a7c59] hover:bg-[#3d6749] px-5 py-3.5 text-sm font-bold text-white transition-all duration-200 active:scale-[0.99] shadow-md hover:shadow-lg cursor-pointer pt-3" 
                                id="submit-button" 
                                type="submit">
                            <span class="tracking-wide uppercase text-xs sm:text-sm" id="button-text">Update Password</span>
                            <span class="material-symbols-outlined text-[19px] transition-transform duration-200 group-hover:translate-x-1" id="button-icon">key</span>
                        </button>
                    </form>

                    <!-- Navigation Links -->
                    <div class="pt-4 border-t border-[#e4e0d8] flex flex-col items-center gap-3">
                        <a class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#4a7c59] hover:text-[#3d6749] transition-colors" href="<?= base_url('login') ?>">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            <span>Back to Sign In</span>
                        </a>
                        <div class="flex items-center gap-2 text-xs text-[#74796e]">
                            <span>Need a new token?</span>
                            <a class="font-semibold text-[#5e5548] hover:text-[#4a7c59] transition-colors underline" href="<?= base_url('forgot-password') ?>">
                                Request Reset Link Again
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
    // Password visibility toggles
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('toggle-password');
    const passwordIcon = document.getElementById('password-toggle-icon');

    const confirmInput = document.getElementById('confirm_password');
    const toggleConfirm = document.getElementById('toggle-confirm-password');
    const confirmIcon = document.getElementById('confirm-toggle-icon');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    }

    if (toggleConfirm && confirmInput) {
        toggleConfirm.addEventListener('click', () => {
            const isPassword = confirmInput.type === 'password';
            confirmInput.type = isPassword ? 'text' : 'password';
            confirmIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    }

    // Password Strength & Match Feedback
    const strengthBar = document.getElementById('strength-bar');
    const strengthLabel = document.getElementById('strength-label');
    const matchMessage = document.getElementById('match-message');

    function calculateStrength(pwd) {
        if (!pwd) return { score: 0, label: 'Password strength', color: 'bg-[#f0ece4]', width: '0%' };
        let score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;

        switch (score) {
            case 1:
                return { score: 1, label: 'Weak', color: 'bg-rose-500', width: '25%' };
            case 2:
                return { score: 2, label: 'Fair', color: 'bg-amber-500', width: '50%' };
            case 3:
                return { score: 3, label: 'Good', color: 'bg-[#4a7c59]', width: '75%' };
            case 4:
                return { score: 4, label: 'Strong', color: 'bg-[#2a6038]', width: '100%' };
            default:
                return { score: 0, label: 'Too short', color: 'bg-rose-400', width: '15%' };
        }
    }

    function checkMatch() {
        if (!confirmInput.value) {
            matchMessage.innerHTML = '<span class="material-symbols-outlined text-[14px]">info</span><span>Passwords must match exactly.</span>';
            matchMessage.className = 'text-[11px] text-[#74796e] flex items-center gap-1.5 pt-1';
            return;
        }

        if (passwordInput.value === confirmInput.value) {
            matchMessage.innerHTML = '<span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span><span class="text-emerald-700 font-semibold">Passwords match</span>';
            matchMessage.className = 'text-[11px] text-emerald-700 flex items-center gap-1.5 pt-1';
        } else {
            matchMessage.innerHTML = '<span class="material-symbols-outlined text-[14px] text-rose-500">cancel</span><span class="text-rose-600 font-semibold">Passwords do not match</span>';
            matchMessage.className = 'text-[11px] text-rose-600 flex items-center gap-1.5 pt-1';
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            const strength = calculateStrength(passwordInput.value);
            strengthBar.style.width = strength.width;
            strengthBar.className = `h-full transition-all duration-300 rounded-full ${strength.color}`;
            strengthLabel.textContent = strength.label;
            checkMatch();
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkMatch);
    }

    // Submission spinner
    const form = document.getElementById('reset-password-form');
    const submitBtn = document.getElementById('submit-button');
    const btnText = document.getElementById('button-text');
    const btnIcon = document.getElementById('button-icon');

    if (form && submitBtn) {
        form.addEventListener('submit', (e) => {
            if (passwordInput.value !== confirmInput.value) {
                e.preventDefault();
                alert('Please ensure both passwords match before submitting.');
                return;
            }
            submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'Updating Password...';
            if (btnIcon) {
                btnIcon.textContent = 'autorenew';
                btnIcon.classList.add('animate-spin');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
