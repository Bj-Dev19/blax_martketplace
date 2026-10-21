<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[#183324]">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TOP/LEFT PANEL: Brand Story & Merchant Network (Top on Mobile)      -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <aside class="w-full lg:w-5/12 xl:w-[46%] bg-[#183324] text-white flex flex-col justify-between pt-7 pb-14 px-5 sm:px-8 sm:py-10 lg:p-14 relative overflow-hidden shrink-0">
        <!-- Ambient organic blur backdrop elements -->
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#4a7c59]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 rounded-full bg-[#c4a66a]/15 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-10 -right-20 w-80 h-80 rounded-full bg-[#3d6749]/30 blur-2xl pointer-events-none"></div>
        <!-- Subtle background grid pattern -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="relative z-10 flex flex-col space-y-4 sm:space-y-6">
            <!-- Brand Header Bar -->
            <header class="flex items-center justify-between gap-3">
                <a class="flex items-center gap-3 group transition-transform hover:scale-[1.02]" href="<?= base_url('/') ?>">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-[#3a6347] via-[#4a7c59] to-[#78a886] text-white flex items-center justify-center font-serif font-bold text-lg sm:text-xl shadow-xl shadow-[#131e17]/50 ring-1 ring-white/20 shrink-0">
                        B
                    </div>
                    <div>
                        <span class="font-serif font-bold text-lg sm:text-xl tracking-wide text-white block leading-tight">Blax Marketplace</span>
                        <span class="text-[11px] sm:text-xs uppercase tracking-wider text-[#d4ccbf]/70 font-medium">South Cotabato Multi-Tenant</span>
                    </div>
                </a>
                <!-- Mobile Storefront Return Pill -->
                <div class="flex items-center gap-2">
                    <a class="lg:hidden inline-flex items-center gap-1 text-[11px] font-semibold text-[#c8e8d0] hover:text-white transition-colors py-1.5 px-3 rounded-full bg-white/10 hover:bg-white/15 border border-white/15 backdrop-blur-sm" href="<?= base_url('/') ?>">
                        <span class="material-symbols-outlined text-[15px]">arrow_back</span>
                        <span>Storefront</span>
                    </a>
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#234431] text-[#8ecf9e] border border-[#3d6749]">
                        <span class="material-symbols-outlined text-[15px]">location_on</span>
                        <span>POLOMOLOK</span>
                    </span>
                </div>
            </header>

            <!-- Headline & Value Pitch -->
            <div class="space-y-2 sm:space-y-3 pt-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#2a4332] text-[#8ecf9e] border border-[#3d6749] text-[11px] sm:text-xs tracking-wide font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-[#8ecf9e] animate-pulse"></span>
                    <span>Verified Merchant Network</span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl xl:text-[40px] leading-[1.18] font-bold text-white tracking-tight">
                    Empower your local business, reach thousands across Polomolok.
                </h1>
                <p class="text-[#d4ccbf]/85 text-xs sm:text-sm lg:text-base leading-relaxed max-w-lg font-normal">
                    Join hundreds of local entrepreneurs and print providers. Expand your storefront reach, receive rush printing jobs, manage stock seamlessly, and offer doorstep delivery.
                </p>
            </div>

            <!-- 4 Feature Cards Mosaic (Desktop only to prevent mobile clutter) -->
            <div class="hidden lg:grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <div class="p-4 rounded-2xl bg-[#203f2d] hover:bg-[#254834] transition-colors duration-200 shadow-sm flex flex-col justify-between border border-white/5">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-[#2c533c] text-[#8ecf9e] inline-flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                        </span>
                        <span class="font-bold text-xs text-white">Direct Payouts</span>
                    </div>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-relaxed">
                        Prompt GCash &amp; direct Philippine bank disbursement cycles.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-[#203f2d] hover:bg-[#254834] transition-colors duration-200 shadow-sm flex flex-col justify-between border border-white/5">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-[#2c533c] text-[#8ecf9e] inline-flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[18px]">print_connect</span>
                        </span>
                        <span class="font-bold text-xs text-white">Rush Print Queue</span>
                    </div>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-relaxed">
                        Accept custom artwork, proofs, &amp; rush printing jobs directly.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-[#203f2d] hover:bg-[#254834] transition-colors duration-200 shadow-sm flex flex-col justify-between border border-white/5">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-[#2c533c] text-[#8ecf9e] inline-flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                        </span>
                        <span class="font-bold text-xs text-white">Couriers &amp; Dispatch</span>
                    </div>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-relaxed">
                        Effortless local order fulfillment with mapped parcel routing.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-[#203f2d] hover:bg-[#254834] transition-colors duration-200 shadow-sm flex flex-col justify-between border border-white/5">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 rounded-xl bg-[#2c533c] text-[#8ecf9e] inline-flex items-center justify-center shrink-0 border border-[#8ecf9e]/30">
                            <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
                        </span>
                        <span class="font-bold text-xs text-white">Storefront POS</span>
                    </div>
                    <p class="text-[11.5px] text-[#d4ccbf]/80 leading-relaxed">
                        Integrated walk-in counter checkout &amp; unified digital inventory.
                    </p>
                </div>
            </div>

            <!-- Trust Stats Highlight Card (Desktop only) -->
            <div class="hidden lg:flex p-4 rounded-2xl bg-[#1d3c2a] border border-[#2b513a] shadow-sm items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-[#4a7c59] text-white flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-xl">verified_user</span>
                    </span>
                    <div>
                        <div class="text-xs font-medium text-[#d4ccbf]/80">Zero Onboarding Fees</div>
                        <div class="text-xs sm:text-sm font-bold text-white">19+ Verified Shops in Polomolok</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-[#2b513a] text-[#8ecf9e] tracking-wider uppercase border border-[#4a7c59]">
                    LIVE HUB
                </span>
            </div>
        </div>

        <!-- Footer Info in Left Panel (Desktop only) -->
        <footer class="hidden lg:flex relative z-10 pt-6 mt-6 border-t border-[#234431] flex-wrap items-center justify-between text-xs text-[#d4ccbf]/70 gap-2">
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-[#8ecf9e]">lock</span>
                <span>TLS 1.3 256-bit Encrypted Session</span>
            </span>
            <span class="tracking-wide">Polomolok, South Cotabato, PH</span>
        </footer>
    </aside>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- BOTTOM/RIGHT PANEL: Merchant Registration Sheet on Mobile           -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <main class="w-full lg:w-7/12 xl:w-[54%] bg-[#faf6f0] -mt-7 sm:-mt-8 lg:mt-0 relative z-20 rounded-t-[32px] sm:rounded-t-[40px] lg:rounded-none shadow-2xl lg:shadow-none flex flex-col justify-between p-6 sm:p-10 lg:p-14 overflow-y-auto flex-1">

        <!-- Mobile Sheet Pull Indicator (Visible only on mobile) -->
        <div class="w-12 h-1.5 rounded-full bg-[#d4ccbf] mx-auto mb-5 lg:hidden shrink-0"></div>

        <!-- Top Utilities (Desktop) -->
        <nav class="hidden lg:flex items-center justify-between gap-4 max-w-xl w-full mx-auto mb-6">
            <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5e5548] hover:text-[#4a7c59] transition-colors py-2 px-3 rounded-lg hover:bg-[#eae6de]/60" href="<?= base_url('shops') ?>">
                <span class="material-symbols-outlined text-[18px]">storefront</span>
                <span>Browse Shops</span>
            </a>
            <a class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4a7c59] hover:text-[#3d6749] transition-colors py-2 px-3.5 rounded-lg bg-[#c8e8d0]/40 hover:bg-[#c8e8d0]/60 border border-[#8ecf9e]/40" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-[17px]">arrow_back</span>
                <span>Return to Storefront</span>
            </a>
        </nav>

        <!-- Main Form Body -->
        <div class="max-w-xl w-full mx-auto my-auto py-4 space-y-6">

            <!-- Header -->
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-1.5 text-xs uppercase tracking-wider text-[#4a7c59] font-bold">
                    <span class="material-symbols-outlined text-[16px]">store</span>
                    <span>Merchant Portal Onboarding</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#2e3230] tracking-tight">
                    Register as Shop Owner
                </h2>
                <p class="text-[#6b6358] text-xs sm:text-sm leading-relaxed">
                    Create your merchant account to start selling products, receiving custom print jobs, and managing local deliveries across the municipality.
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

            <!-- Account-Type Switcher (Segmented Control) -->
            <div class="p-1 bg-[#f0ece4] rounded-2xl grid grid-cols-2 gap-1 border border-[#e4e0d8]" role="tablist">
                <a class="py-2.5 px-3 rounded-xl text-center font-bold text-xs sm:text-sm text-[#5e5548] hover:text-[#2e3230] hover:bg-white/60 transition-all flex items-center justify-center gap-2" href="<?= base_url('signup') ?>">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    <span>Customer</span>
                </a>
                <button class="py-2.5 px-3 rounded-xl text-center font-bold text-xs sm:text-sm bg-[#4a7c59] text-white shadow-sm flex items-center justify-center gap-2 transition-all cursor-pointer" type="button">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>Shop Owner</span>
                </button>
            </div>

            <!-- Merchant Registration Form -->
            <form id="merchant-signup-form" action="<?= base_url('merchant-signup') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Name Grid (2 Cols) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-first-name">
                            First Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">person</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="merchant-first-name" 
                                   name="first_name" 
                                   placeholder="Juan" 
                                   required 
                                   type="text"
                                   value="<?= esc(old('first_name') ?? '') ?>">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-last-name">
                            Last Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">person</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="merchant-last-name" 
                                   name="last_name" 
                                   placeholder="Dela Cruz" 
                                   required 
                                   type="text"
                                   value="<?= esc(old('last_name') ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Email Address (Full Width) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-email">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">mail</span>
                        <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                               id="merchant-email" 
                               name="email" 
                               placeholder="merchant@example.ph" 
                               required 
                               type="email"
                               autocomplete="email"
                               value="<?= esc(old('email') ?? '') ?>">
                    </div>
                </div>

                <!-- Contact Number & Store Brand Name Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-phone">
                            Contact Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">phone</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="merchant-phone" 
                                   name="phone" 
                                   placeholder="0912 345 6789" 
                                   required 
                                   type="tel"
                                   value="<?= esc(old('phone') ?? old('contact_number') ?? '') ?>">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-shop-name">
                            Store / Brand Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">store</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="merchant-shop-name" 
                                   name="shop_name" 
                                   placeholder="InkMaster Supplies & Printing" 
                                   required 
                                   type="text"
                                   value="<?= esc(old('shop_name') ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Physical Shop Address -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-address">
                        Shop Physical Address (Polomolok) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">pin_drop</span>
                        <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-3.5 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                               id="merchant-address" 
                               name="address_line" 
                               placeholder="Purok 3, Poblacion, Near Municipal Hall, Polomolok" 
                               required 
                               type="text"
                               value="<?= esc(old('address_line') ?? '') ?>">
                    </div>
                </div>

                <!-- Document Upload Box (DTI / Mayor's Permit) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider">
                        Mayor's Permit / DTI Business Certificate <span class="text-xs font-normal text-[#74796e]">(Optional)</span>
                    </label>
                    <div id="dropzone-box" class="p-5 rounded-2xl bg-white/80 border-2 border-dashed border-[#c4c8bc] hover:border-[#4a7c59] text-center flex flex-col items-center justify-center space-y-2 cursor-pointer transition-all group">
                        <div class="w-11 h-11 rounded-2xl bg-[#f0ece4] text-[#4a7c59] flex items-center justify-center group-hover:scale-105 group-hover:bg-[#c8e8d0]/50 transition-all">
                            <span class="material-symbols-outlined text-[24px]">upload_file</span>
                        </div>
                        <div class="space-y-0.5">
                            <p id="permit-filename-display" class="text-xs font-bold text-[#2e3230]">Upload Business Permit or License</p>
                            <p class="text-[11px] text-[#74796e]">Accepts PDF, PNG, or JPG (Max 10MB)</p>
                        </div>
                        <input type="file" id="permit-file-input" name="permit_file" accept=".pdf,.png,.jpg,.jpeg" class="hidden">
                        <button class="mt-1 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#f0ece4] text-[#4a7c59] hover:bg-[#4a7c59] hover:text-white transition-colors cursor-pointer" type="button" onclick="document.getElementById('permit-file-input').click()">
                            Browse File
                        </button>
                    </div>
                </div>

                <!-- Password Grid (2 Cols with Show/Hide Toggles) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-password">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">lock</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-10 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="merchant-password" 
                                   minlength="8" 
                                   name="password" 
                                   placeholder="Min. 8 chars" 
                                   required 
                                   type="password">
                            <button class="toggle-pwd-btn absolute right-2.5 top-1/2 -translate-y-1/2 text-[#74796e] hover:text-[#2e3230] p-1 transition-colors" data-target="merchant-password" type="button">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#74796e]">At least 8 characters</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-[#5e5548] uppercase tracking-wider" for="merchant-confirm-password">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-xl border border-[#c4c8bc] bg-white/80 backdrop-blur-sm shadow-xs focus-within:border-[#4a7c59] focus-within:ring-2 focus-within:ring-[#4a7c59]/20 transition-all">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[#74796e] text-[18px] pointer-events-none">lock_reset</span>
                            <input class="w-full bg-transparent border-0 rounded-xl pl-10 pr-10 py-2.5 text-sm text-[#2e3230] placeholder:text-[#a0a59a] focus:ring-0 focus:outline-none" 
                                   id="merchant-confirm-password" 
                                   minlength="8" 
                                   name="confirm_password" 
                                   placeholder="Repeat password" 
                                   required 
                                   type="password">
                            <button class="toggle-pwd-btn absolute right-2.5 top-1/2 -translate-y-1/2 text-[#74796e] hover:text-[#2e3230] p-1 transition-colors" data-target="merchant-confirm-password" type="button">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#74796e]">Must match password</p>
                    </div>
                </div>

                <!-- Merchant Agreement Checkbox -->
                <div class="p-3.5 rounded-xl bg-[#f0ece4] border border-[#e4e0d8] text-[11.5px] text-[#5e5548] leading-relaxed flex items-start gap-2.5 pt-2">
                    <input class="mt-0.5 rounded border-[#c4c8bc] text-[#4a7c59] focus:ring-[#4a7c59]/30 w-4 h-4 cursor-pointer accent-[#4a7c59]" id="merchant-agree" type="checkbox" required checked>
                    <label class="flex-1 cursor-pointer select-none leading-relaxed" for="merchant-agree">
                        By registering as a merchant, you agree to Blax's <a class="text-[#4a7c59] hover:underline font-bold" href="<?= base_url('terms') ?>">Merchant Agreement</a>, <a class="text-[#4a7c59] hover:underline font-bold" href="<?= base_url('terms') ?>">Terms of Service</a>, and Polomolok commerce regulations.
                    </label>
                </div>

                <!-- Submit Action Button -->
                <div class="pt-2">
                    <button class="w-full py-3.5 px-6 rounded-xl bg-[#4a7c59] hover:bg-[#3d6749] active:bg-[#2a6038] text-white font-bold text-sm tracking-wide shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer" type="submit">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                        <span>COMPLETE MERCHANT REGISTRATION</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </form>

            <!-- Secondary Links -->
            <div class="pt-4 border-t border-[#e4e0d8] text-center space-y-2 text-xs">
                <p class="text-[#5e5548]">
                    Already registered as a seller?
                    <a class="font-bold text-[#4a7c59] hover:text-[#2a6038] hover:underline ml-1" href="<?= base_url('login?role=shop_owner') ?>">
                        Sign in to Merchant Portal
                    </a>
                </p>
                <p class="text-[#6b6358]">
                    Looking to buy?
                    <a class="font-bold text-[#2e3230] hover:text-[#4a7c59] hover:underline ml-1" href="<?= base_url('signup') ?>">
                        Register as Customer
                    </a>
                </p>
            </div>
        </div>

        <!-- Right Footer Legal / Trust Note -->
        <footer class="pt-6 border-t border-[#e4e0d8] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#74796e] gap-2 max-w-xl w-full mx-auto">
            <span class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-[#4a7c59]">shield_lock</span>
                <span>Encrypted Multi-Tenant Merchant Authentication</span>
            </span>
            <span>&copy; <?= date('Y') ?> Blax Marketplace &bull; Polomolok</span>
        </footer>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Dropzone file input helper
    const dropzoneBox = document.getElementById('dropzone-box');
    const fileInput = document.getElementById('permit-file-input');
    const filenameDisplay = document.getElementById('permit-filename-display');

    if (dropzoneBox && fileInput) {
        dropzoneBox.addEventListener('click', (e) => {
            if (e.target.tagName !== 'BUTTON') {
                fileInput.click();
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                filenameDisplay.innerHTML = `<span class="text-[#4a7c59] font-bold">✓ Selected:</span> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                dropzoneBox.classList.add('border-[#4a7c59]', 'bg-[#c8e8d0]/20');
            } else {
                filenameDisplay.textContent = 'Upload Business Permit or License';
                dropzoneBox.classList.remove('border-[#4a7c59]', 'bg-[#c8e8d0]/20');
            }
        });

        // Drag and drop events
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneBox.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzoneBox.classList.add('border-[#4a7c59]', 'bg-[#c8e8d0]/20');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneBox.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzoneBox.classList.remove('border-[#4a7c59]', 'bg-[#c8e8d0]/20');
            });
        });

        dropzoneBox.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                const file = fileInput.files[0];
                filenameDisplay.innerHTML = `<span class="text-[#4a7c59] font-bold">✓ Selected:</span> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                dropzoneBox.classList.add('border-[#4a7c59]', 'bg-[#c8e8d0]/20');
            }
        });
    }

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

    // Password matching validation
    const form = document.getElementById('merchant-signup-form');
    const pwd = document.getElementById('merchant-password');
    const confirmPwd = document.getElementById('merchant-confirm-password');

    if (form && pwd && confirmPwd) {
        form.addEventListener('submit', (e) => {
            if (pwd.value !== confirmPwd.value) {
                e.preventDefault();
                alert('Passwords do not match. Please verify your passwords.');
                confirmPwd.focus();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>