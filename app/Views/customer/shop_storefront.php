<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<style>
    .drag-active { 
        border-color: #4A943E !important; 
        background-color: rgba(74, 148, 62, 0.06) !important; 
    }
</style>

<main class="max-w-container-max mx-auto px-4 md:px-8 lg:px-10 py-8 md:py-12 flex-grow w-full">

    <!-- Shop Header Bento Stage -->
    <section class="bento-card bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xs mb-8 flex flex-col md:flex-row gap-6 sm:gap-8 items-center md:items-start text-center md:text-left relative">
        
        <!-- Ambient Terra Glow (Isolated clipping layer so child popovers do not get sliced) -->
        <div class="absolute inset-0 rounded-3xl overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-terra-green/5 dark:bg-terra-green/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-60 h-60 bg-terra-amber/5 dark:bg-terra-amber/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Logo Container -->
        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl sm:rounded-3xl border-2 border-slate-200/90 dark:border-slate-700/80 shadow-md overflow-hidden flex-shrink-0 bg-slate-50 dark:bg-slate-800 flex items-center justify-center relative p-1.5 ring-4 ring-terra-green/10">
            <?php $stLogo = logo_url($shop['logo_url'] ?? null); ?>
            <?php if (!empty($stLogo)): ?>
                <img class="w-full h-full object-cover rounded-xl sm:rounded-2xl" src="<?= esc($stLogo) ?>" alt="<?= esc($shop['shop_name']) ?> logo" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                <span class="hidden material-symbols-outlined text-terra-green text-4xl sm:text-6xl items-center justify-center w-full h-full">store</span>
            <?php else: ?>
                <span class="material-symbols-outlined text-terra-green text-4xl sm:text-6xl">store</span>
            <?php endif; ?>
        </div>

        <div class="flex-grow flex flex-col justify-between h-full w-full">

            <div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3.5 justify-center md:justify-start mb-2.5">
                    <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight"><?= esc($shop['shop_name']) ?></h1>

                    <div class="inline-flex items-center gap-1 bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 px-3 py-1 rounded-full text-xs font-bold font-mono self-center sm:self-auto shadow-2xs">
                        <span class="material-symbols-outlined text-[15px] fill-icon text-amber-500">star</span>
                        <span><?= number_format($shop['rating_average'] ?? 5.0, 1) ?></span>
                        <span class="text-slate-400 dark:text-slate-500 font-normal text-[11px]">(<?= number_format($shop['rating_count'] ?? 0) ?>)</span>
                    </div>

                    <span class="inline-flex items-center gap-1.5 bg-terra-green/10 text-terra-green dark:text-emerald-400 border border-terra-green/20 px-3 py-1 rounded-full text-[11px] font-bold self-center sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-terra-green animate-pulse"></span>
                        Verified Merchant
                    </span>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
                    <?= esc($shop['description'] ?? 'Premium printing, documentation, and office supplies. High-grade equipment with rapid turnaround guaranteed.') ?>
                </p>
            </div>

            <!-- Operating Hours & Action CTAs -->
            <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-center md:justify-between gap-3">
                
                <div class="flex items-center gap-2.5 flex-wrap justify-center">
                    <?php if (!empty($isFavorite)): ?>
                        <form action="<?= base_url('customer/favorites/remove') ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="shop_id" value="<?= (int) $shop['id'] ?>">
                            <button type="submit" class="px-4 py-2 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-300 rounded-xl text-xs font-bold hover:bg-rose-100 transition-all flex items-center gap-1.5 shadow-2xs active:scale-95">
                                <span class="material-symbols-outlined text-[17px] fill-icon text-rose-500">favorite</span>
                                <span>Following Store</span>
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="<?= base_url('customer/favorites/add') ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="shop_id" value="<?= (int) $shop['id'] ?>">
                            <button type="submit" class="px-4 py-2 bg-terra-green hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm hover:shadow-md active:scale-95">
                                <span class="material-symbols-outlined text-[17px]">favorite_border</span>
                                <span>Follow Store</span>
                            </button>
                        </form>
                    <?php endif; ?>

                    <button type="button" id="btn-open-report-modal" class="px-3.5 py-2 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-rose-600 hover:border-rose-300 dark:hover:border-rose-800 rounded-xl transition-all flex items-center gap-1 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 active:scale-95">
                        <span class="material-symbols-outlined text-[15px]">flag</span>
                        <span>Report</span>
                    </button>
                </div>

                <?php
                    $operationalStatus = $operationalStatus ?? ['is_closed_today' => false, 'note' => '', 'status_label' => 'Open Today'];
                    $isShopClosedToday = !empty($operationalStatus['is_closed_today']);
                    $closureNoteText   = trim((string) ($operationalStatus['note'] ?? ''));
                ?>
                <?php if (!empty($businessHours) || !empty($operationalStatus)): ?>
                    <?php
                        $todayName = strtolower(date('l'));
                        $currentStatusText = 'Open Today';
                        $isOpenNow = true;
                        if ($isShopClosedToday) {
                            $currentStatusText = !empty($operationalStatus['status_label']) ? $operationalStatus['status_label'] : 'Closed Today';
                            $isOpenNow = false;
                        } elseif (!empty($businessHours)) {
                            foreach ($businessHours as $h) {
                                $label = strtolower($h['label'] ?? '');
                                if (str_contains($label, $todayName) || $label === 'daily') {
                                    if (!empty($h['is_closed'])) {
                                        $currentStatusText = 'Closed Today';
                                        $isOpenNow = false;
                                    } else {
                                        $currentStatusText = 'Open Today • ' . ($h['open'] ?? '8:00 AM') . ' – ' . ($h['close'] ?? '5:00 PM');
                                        $isOpenNow = true;
                                    }
                                    break;
                                }
                            }
                            if ($closureNoteText !== '') {
                                $currentStatusText .= ' • ' . $closureNoteText;
                            }
                        }
                    ?>
                    <!-- Compact Bento Schedule Pill -->
                    <div class="relative" id="hours-dropdown-container">
                        <button type="button" id="hours-dropdown-trigger" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 text-xs font-medium cursor-pointer select-none hover:border-terra-green/40 hover:bg-slate-100/80 dark:hover:bg-slate-700/60 transition-colors shadow-2xs">
                            <span class="w-2 h-2 rounded-full <?= $isOpenNow ? 'bg-terra-green animate-pulse' : 'bg-rose-500' ?>"></span>
                            <span class="font-bold text-slate-800 dark:text-slate-200"><?= esc($currentStatusText) ?></span>
                            <span id="hours-chevron" class="material-symbols-outlined text-[16px] text-slate-400 transition-transform duration-200">expand_more</span>
                        </button>

                        <div id="full-business-hours" class="hidden absolute md:right-0 md:left-auto left-1/2 -translate-x-1/2 md:translate-x-0 top-full mt-2 w-72 max-w-[calc(100vw-2rem)] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 shadow-2xl z-50 text-xs space-y-2.5 backdrop-blur-md">
                            <div class="font-bold text-slate-900 dark:text-white pb-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-terra-green/10 text-terra-green flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[15px]">schedule</span>
                                    </span>
                                    <span class="font-display font-semibold text-sm">Weekly Schedule</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full <?= $isOpenNow ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' ?>">
                                    <?= $isOpenNow ? 'Open Now' : 'Closed' ?>
                                </span>
                            </div>
                            <?php if ($closureNoteText !== ''): ?>
                                <div class="p-2.5 rounded-xl <?= $isShopClosedToday ? 'bg-rose-500/10 border border-rose-500/25 text-rose-700 dark:text-rose-300' : 'bg-amber-500/10 border border-amber-500/25 text-amber-800 dark:text-amber-200' ?> text-[11px] font-semibold">
                                    <span class="font-extrabold uppercase tracking-wider block text-[10px]">Today's Calendar Note:</span>
                                    <?= esc($closureNoteText) ?>
                                </div>
                            <?php endif; ?>
                            <div class="space-y-1">
                                <?php foreach ($businessHours as $hours): 
                                    $labelLower = strtolower($hours['label'] ?? '');
                                    $isToday = str_contains($labelLower, $todayName) || $labelLower === 'daily';
                                ?>
                                    <div class="flex items-center justify-between px-2.5 py-1.5 rounded-xl <?= $isToday ? 'bg-terra-green/10 text-slate-900 dark:text-white font-bold border border-terra-green/20' : 'text-slate-600 dark:text-slate-300 font-medium' ?>">
                                        <span class="flex items-center gap-1.5">
                                            <?php if ($isToday): ?>
                                                <span class="w-1.5 h-1.5 rounded-full bg-terra-green"></span>
                                            <?php endif; ?>
                                            <span><?= esc($hours['label']) ?></span>
                                            <?php if ($isToday): ?>
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-terra-green px-1.5 py-0.2 rounded bg-terra-green/20">Today</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="<?= $hours['is_closed'] ? 'text-rose-500 font-bold' : ($isToday ? 'font-mono text-terra-green dark:text-emerald-400 font-bold' : 'font-mono text-slate-700 dark:text-slate-200') ?>">
                                            <?= $hours['is_closed'] ? 'Closed' : esc($hours['open'] . ' – ' . $hours['close']) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </section>

    <?php if ($isShopClosedToday): ?>
        <div class="mb-6 rounded-2xl border border-rose-500/35 bg-rose-500/10 p-4 sm:p-5 flex items-start gap-3.5 shadow-2xs">
            <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-2xl shrink-0">event_busy</span>
            <div class="space-y-1 text-xs sm:text-sm">
                <p class="font-extrabold text-rose-700 dark:text-rose-300 font-display">
                    <?= esc($shop['shop_name'] ?? 'This store') ?> is Closed Today (<?= date('M j, Y') ?>)
                    <?php if ($closureNoteText !== ''): ?>
                        — <span class="underline decoration-rose-400/60"><?= esc($closureNoteText) ?></span>
                    <?php endif; ?>
                </p>
                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    You can still browse the store catalog and save items to your cart for later, but placing new orders and submitting printing requests are paused until the store reopens.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Report Shop Modal -->
    <div id="report-shop-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 w-full max-w-md shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                    <span class="material-symbols-outlined text-2xl">report</span>
                    <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">Report Shop</h3>
                </div>
                <button type="button" id="btn-close-report-modal" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form action="<?= base_url('customer/shop/report') ?>" method="POST" class="flex flex-col gap-4">
                <?= csrf_field() ?>
                <input type="hidden" name="shop_id" value="<?= (int) $shop['id'] ?>">

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="report-reason">Reason for Reporting <span class="text-rose-500">*</span></label>
                    <select id="report-reason" name="issue_type" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green focus:border-terra-green" required>
                        <option value="" disabled selected>Select a reason...</option>
                        <option value="Counterfeit/Fake Product">Counterfeit/Fake Product</option>
                        <option value="Item Not as Described">Item Not as Described</option>
                        <option value="Harassment/Abusive Behavior">Harassment/Abusive Behavior</option>
                        <option value="Scam/Fraud">Scam/Fraud</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" for="report-desc">Additional Details (Optional)</label>
                    <textarea id="report-desc" name="description" rows="3" class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-terra-green placeholder:text-slate-400" placeholder="Provide extra details to help our team investigate..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" id="btn-cancel-report" class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs sm:text-sm font-bold bg-rose-600 text-white rounded-xl shadow-xs hover:bg-rose-700 transition-all active:scale-95">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PDF/DOCX Printing Services Bento (If Shop Offers Printing) -->
    <?php if (!empty($shop['offers_printing'])): ?>

        <section class="mb-12" id="printing-services-section">

            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-4">

                <div>
                    <div class="flex items-center gap-1.5 text-terra-green font-bold text-xs uppercase tracking-wider mb-1.5">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span>Document Reproduction &amp; Finishing</span>
                    </div>
                    <h2 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Printing Services</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">High-fidelity laser reproduction for PDF &amp; Word documents</p>
                </div>

                <!-- Subtle 3-Step Indicator -->
                <div class="inline-flex items-center gap-2 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-700 text-xs font-semibold self-start sm:self-auto shadow-2xs">
                    <span class="flex items-center gap-1.5 text-terra-green dark:text-emerald-400 font-bold px-2.5 py-1 bg-terra-green/10 rounded-xl">
                        <span class="w-4 h-4 rounded-full bg-terra-green text-white text-[10px] flex items-center justify-center font-bold">1</span> Upload
                    </span>
                    <span class="text-slate-300 dark:text-slate-600 text-xs">→</span>
                    <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 px-2.5 py-1">
                        <span class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] flex items-center justify-center font-bold">2</span> Configure
                    </span>
                    <span class="text-slate-300 dark:text-slate-600 text-xs">→</span>
                    <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300 px-2.5 py-1">
                        <span class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] flex items-center justify-center font-bold">3</span> Pay 50%
                    </span>
                </div>

            </div>

            <form action="<?= base_url('printing/request') ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" id="printingRequestForm" onsubmit="return validatePrintingSubmit(event)">

                <?= csrf_field() ?>

                <input type="hidden" name="shop_id" value="<?= $shop['id'] ?>">
                <input type="hidden" name="page_count" id="pdf-page-count" value="0">

                <!-- Left Column: Compact Upload & Document Status (5 cols on Desktop) -->
                <div class="lg:col-span-5 space-y-4">

                    <!-- Document Format Selection Toggle -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-4 shadow-2xs">
                        <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5 block">1. Select Document Format</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="cursor-pointer">
                                <input type="radio" name="document_type" value="pdf" id="docTypePdf" class="peer sr-only" checked onchange="toggleDocType('pdf')">
                                <span class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green peer-checked:shadow-xs">
                                    <span class="material-symbols-outlined text-[17px]">picture_as_pdf</span> PDF Document
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="document_type" value="docx" id="docTypeDocx" class="peer sr-only" onchange="toggleDocType('docx')">
                                <span class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green peer-checked:shadow-xs">
                                    <span class="material-symbols-outlined text-[17px]">description</span> Word (.doc, .docx)
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Compact Dropzone Card -->
                    <div class="bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl p-6 min-h-[220px] max-h-[290px] flex flex-col items-center justify-center text-center transition-all hover:border-terra-green group cursor-pointer relative shadow-2xs" id="dropzone">

                        <div class="w-14 h-14 bg-terra-green/10 dark:bg-terra-green/20 rounded-2xl flex items-center justify-center text-terra-green mb-3 group-hover:scale-110 group-hover:bg-terra-green group-hover:text-white transition-all duration-200" id="dropzone-icon-box">
                            <span class="material-symbols-outlined text-3xl" id="dropzoneIcon">picture_as_pdf</span>
                        </div>

                        <h3 class="font-display text-base font-bold text-slate-900 dark:text-white mb-1" id="dropzoneTitle">Upload your PDF</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mb-4 max-w-xs leading-relaxed" id="dropzoneDesc">Drag &amp; drop your document here, or click to browse files from your device.</p>

                        <input accept=".pdf" class="sr-only" id="fileInput" type="file" name="document">

                        <button type="button" id="selectPdfBtn" class="bg-terra-green text-white px-5 py-2 rounded-xl text-xs font-bold shadow-xs hover:shadow-md hover:bg-emerald-700 transition-all flex items-center gap-1.5 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            <span id="selectBtnText">Select PDF Document</span>
                        </button>

                        <p class="mt-2.5 text-[11px] text-slate-400 dark:text-slate-500" id="dropzoneFooter">Max file size: 50MB &bull; Formats: PDF only</p>

                    </div>

                    <!-- Upload Verification Status Chip -->
                    <div class="bg-slate-50 dark:bg-slate-800/80 rounded-2xl p-4 border border-slate-200/90 dark:border-slate-700 flex flex-col gap-1.5" id="upload-status-container">

                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1" id="upload-badge">
                                <span class="material-symbols-outlined text-[15px]">info</span>No file selected
                            </span>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 font-mono" id="page-count">No file selected</span>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 hidden mt-1" id="upload-hint">Page count will be verified automatically once your PDF is uploaded.</p>

                    </div>

                    <!-- DOCX Manual Page Count (Word documents) -->
                    <div id="docxPageCountContainer" class="hidden bg-slate-50 dark:bg-slate-800/80 rounded-2xl p-4 border border-slate-200/90 dark:border-slate-700 space-y-1.5">
                        <label for="docxEstimatedPages" class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center justify-between">
                            <span>Estimated Page Count <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] text-slate-400 font-normal">Enter total pages</span>
                        </label>
                        <input type="number" name="estimated_page_count" id="docxEstimatedPages" min="1" step="1" value="1" placeholder="e.g. 5" class="w-full py-2 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white font-mono focus:ring-1 focus:ring-terra-green focus:border-terra-green" oninput="handleManualPageCount(this.value)" onchange="handleManualPageCount(this.value)">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Word files require estimated page count for pricing calculations.</p>
                    </div>

                    <!-- DOCX Change Request & Reference Attachments -->
                    <div id="docxChangesContainer" class="hidden bg-slate-50 dark:bg-slate-800/80 rounded-2xl p-4 border border-slate-200/90 dark:border-slate-700 space-y-3">
                        <div>
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1.5">Printing Requirement</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="doc_change_type" value="as_is" class="peer sr-only" checked onchange="toggleDocxChanges('as_is')">
                                    <span class="flex items-center justify-center gap-1.5 py-2 px-2 text-center rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green">
                                        <span class="material-symbols-outlined text-[16px]">print</span> Print As-Is
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="doc_change_type" value="has_changes" class="peer sr-only" onchange="toggleDocxChanges('has_changes')">
                                    <span class="flex items-center justify-center gap-1.5 py-2 px-2 text-center rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green">
                                        <span class="material-symbols-outlined text-[16px]">edit_note</span> With Changes
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Reference Photos for Changes -->
                        <div id="docxReferencePhotosContainer" class="hidden space-y-2">
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center justify-between">
                                <span>Reference Photos / Markups</span>
                                <span class="text-[10px] text-slate-400 font-normal">Max 5 images</span>
                            </label>
                            <input type="file" name="reference_photos[]" id="referencePhotosInput" multiple accept="image/*" class="w-full py-1.5 px-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-terra-green/10 file:text-terra-green hover:file:bg-terra-green/20 cursor-pointer">
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Attach photos or screenshots showing edits requested.</p>

                            <!-- Live Photo Preview Gallery -->
                            <div id="refPhotosPreviewGrid" class="hidden grid grid-cols-3 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200 dark:border-slate-700"></div>
                        </div>
                    </div>

                    <!-- Same-Day Pickup Promotion Card -->
                    <div class="bg-gradient-to-br from-terra-green to-emerald-800 text-white rounded-3xl p-5 sm:p-6 relative overflow-hidden shadow-sm">
                        <div class="relative z-10">
                            <div class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider mb-1 opacity-90 text-amber-200">
                                <span class="material-symbols-outlined text-[16px]">bolt</span> Same-Day Ready
                            </div>
                            <h4 class="font-display text-base font-bold mb-1">Quick Kiosk Pick-up</h4>
                            <p class="text-xs opacity-90 leading-relaxed">Submit your request before 2:00 PM and pick up your finished prints today at any available shop kiosk.</p>
                        </div>
                        <span class="material-symbols-outlined absolute -bottom-3 -right-3 text-7xl opacity-10 select-none pointer-events-none text-white">print</span>
                    </div>

                </div>

                <!-- Right Column: Compact Order Configuration (7 cols on Desktop) -->
                <div class="lg:col-span-7">

                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-5">

                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">

                            <h4 class="font-display text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-terra-green text-[22px]">tune</span>
                                Order Configuration
                            </h4>

                            <span class="text-[11px] font-bold text-terra-green uppercase tracking-wider bg-terra-green/10 px-2.5 py-0.5 rounded-full">Step 2 of 3</span>

                        </div>

                        <div class="space-y-4">

                            <!-- Paper Size & Copies -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block">Paper Size</label>
                                    <div class="relative">
                                        <select name="paper_size" id="paperSizeSelect" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-terra-green focus:border-terra-green text-slate-900 dark:text-white transition-all appearance-none pr-8">
                                            <?php if (!empty($paperSizes)): ?>
                                                <?php foreach ($paperSizes as $sKey => $sVal): ?>
                                                    <?php if (!empty($sVal['is_enabled'])): ?>
                                                        <option value="<?= esc($sKey) ?>" <?= $sKey === 'letter' ? 'selected' : '' ?>>
                                                            <?= esc($sVal['label'] ?? strtoupper($sKey)) ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="letter" selected>Letter (8.5" x 11")</option>
                                                <option value="legal">Legal (8.5" x 14")</option>
                                                <option value="a4">A4 (8.27" x 11.69")</option>
                                            <?php endif; ?>
                                        </select>
                                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400 pointer-events-none">expand_more</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block">Copies</label>
                                    <div class="flex items-center">
                                        <button type="button" id="btnCopiesMinus" class="w-9 h-9 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-l-xl text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors active:scale-95 font-bold text-base select-none">−</button>
                                        <input type="number" name="copies" id="copiesInput" min="1" max="500" value="1" class="w-full py-2 px-2 text-center bg-slate-50 dark:bg-slate-800/70 border-y border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white font-mono focus:ring-1 focus:ring-terra-green focus:border-terra-green [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        <button type="button" id="btnCopiesPlus" class="w-9 h-9 flex items-center justify-center bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-r-xl text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors active:scale-95 font-bold text-base select-none">+</button>
                                    </div>
                                </div>

                            </div>

                            <!-- Color Mode -->
                            <div>
                                <?php
                                    $priceColor = (float) ($printingSettings['price_color_per_page'] ?? 5.00);
                                    $priceBw    = (float) ($printingSettings['price_bw_per_page'] ?? 2.00);
                                ?>

                                <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block">Color Mode</label>

                                <div class="grid grid-cols-2 gap-2.5">

                                    <label class="cursor-pointer">
                                        <input type="radio" name="color_mode" value="colored" class="peer sr-only" checked>
                                        <div class="flex items-center justify-between gap-1.5 py-2.5 px-3 text-center rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 text-slate-800 dark:text-slate-200 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green peer-checked:shadow-xs">
                                            <span class="flex items-center gap-1.5 truncate">
                                                <span class="material-symbols-outlined text-[16px] shrink-0">palette</span>
                                                <span class="truncate">Full Color</span>
                                            </span>
                                            <span class="text-[11px] font-mono font-bold opacity-90 shrink-0">₱<?= number_format($priceColor, 2) ?>/page</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <input type="radio" name="color_mode" value="black_white" class="peer sr-only">
                                        <div class="flex items-center justify-between gap-1.5 py-2.5 px-3 text-center rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 text-slate-800 dark:text-slate-200 transition-all peer-checked:bg-terra-green peer-checked:text-white peer-checked:border-terra-green peer-checked:shadow-xs">
                                            <span class="flex items-center gap-1.5 truncate">
                                                <span class="material-symbols-outlined text-[16px] shrink-0">grayscale</span>
                                                <span class="truncate">Black &amp; White</span>
                                            </span>
                                            <span class="text-[11px] font-mono font-bold opacity-90 shrink-0">₱<?= number_format($priceBw, 2) ?>/page</span>
                                        </div>
                                    </label>

                                </div>

                            </div>

                            <!-- Binding Options -->
                            <div>

                                <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block">Binding Options</label>

                                <div class="grid grid-cols-3 gap-2.5">

                                    <label class="cursor-pointer">
                                        <input checked class="peer sr-only" name="binding" type="radio" value="none" data-cost="0">
                                        <div class="p-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex flex-col items-center justify-center">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block">No Binding</span>
                                            <span class="text-[11px] text-slate-400 font-medium">Free</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <?php $staplePrice = (float) ($printingSettings['price_staple'] ?? 10.00); ?>
                                        <input class="peer sr-only" name="binding" type="radio" value="stapled" data-cost="<?= $staplePrice ?>">
                                        <div class="p-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex flex-col items-center justify-center">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block">Stapled</span>
                                            <span class="text-[11px] text-terra-green font-mono font-bold"><?= $staplePrice > 0 ? ('+₱' . number_format($staplePrice, 2)) : 'Free' ?></span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer">
                                        <?php $spiralPrice = (float) ($printingSettings['price_spiral'] ?? 35.00); ?>
                                        <input class="peer sr-only" name="binding" type="radio" value="spiral" data-cost="<?= $spiralPrice ?>">
                                        <div class="p-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex flex-col items-center justify-center">
                                            <span class="text-xs font-bold text-slate-900 dark:text-white block">Spiral</span>
                                            <span class="text-[11px] text-terra-green font-mono font-bold">+₱<?= number_format($spiralPrice, 2) ?></span>
                                        </div>
                                    </label>

                                </div>

                            </div>

                            <!-- Fulfillment Method -->
                            <div>
                                <?php
                                    $shopOffersPickup   = !isset($shop['offers_pickup']) || (int)$shop['offers_pickup'] === 1;
                                    $shopOffersDelivery = !isset($shop['offers_delivery']) || (int)$shop['offers_delivery'] === 1;
                                    $defaultFulfillment = $shopOffersPickup ? 'pickup' : ($shopOffersDelivery ? 'delivery' : '');
                                ?>
                                <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block">Fulfillment Method</label>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">

                                    <!-- Store Pick-up Option -->
                                    <label class="<?= $shopOffersPickup ? 'cursor-pointer' : 'cursor-not-allowed opacity-50 pointer-events-none' ?>" title="<?= $shopOffersPickup ? 'Store Pick-up' : 'Store Pick-up is not available for this shop' ?>">
                                        <input <?= ($defaultFulfillment === 'pickup') ? 'checked' : '' ?> <?= !$shopOffersPickup ? 'disabled' : '' ?> class="peer sr-only" name="fulfillment_method" type="radio" value="pickup">
                                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex items-center gap-2.5 h-full">
                                            <div class="w-8 h-8 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">storefront</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">Store Pick-up</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate"><?= $shopOffersPickup ? 'Free at shop branch' : 'Service currently unavailable' ?></p>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Doorstep Delivery Option -->
                                    <label class="<?= $shopOffersDelivery ? 'cursor-pointer' : 'cursor-not-allowed opacity-50 pointer-events-none' ?>" title="<?= $shopOffersDelivery ? 'Doorstep Delivery' : 'Doorstep Delivery is not available for this shop' ?>">
                                        <input <?= ($defaultFulfillment === 'delivery') ? 'checked' : '' ?> <?= !$shopOffersDelivery ? 'disabled' : '' ?> class="peer sr-only" name="fulfillment_method" type="radio" value="delivery">
                                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex items-center gap-2.5 h-full">
                                            <div class="w-8 h-8 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">Doorstep Delivery</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate"><?= $shopOffersDelivery ? 'Polomolok area only' : 'Service currently unavailable' ?></p>
                                            </div>
                                        </div>
                                    </label>

                                </div>

                                <?php if (!$shopOffersDelivery && $shopOffersPickup): ?>
                                    <div class="mt-2.5 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0">info</span>
                                        <span><strong>Paalala:</strong> Kasalukuyang walang Doorstep Delivery service ang tindahang ito. Store Pick-up lamang ang maaari.</span>
                                    </div>
                                <?php elseif (!$shopOffersPickup && $shopOffersDelivery): ?>
                                    <div class="mt-2.5 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] text-amber-600 shrink-0">info</span>
                                        <span><strong>Paalala:</strong> Kasalukuyang walang Store Pick-up service ang tindahang ito. Doorstep Delivery lamang ang maaari.</span>
                                    </div>
                                <?php elseif (!$shopOffersPickup && !$shopOffersDelivery): ?>
                                    <div class="mt-2.5 p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] shrink-0">warning</span>
                                        <span><strong>Paalala:</strong> Kasalukuyang sarado ang lahat ng fulfillment services ng tindahang ito.</span>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <!-- Payment Method -->
                            <div>
                                <?php $dpPercent = (float) ($printingSettings['down_payment_percent'] ?? 50.00); ?>
                                <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block">Payment Method</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="prPaymentMethodGroup">
                                    <label class="cursor-pointer">
                                        <input checked class="peer sr-only" name="payment_method" type="radio" value="gcash" id="payMethodGcash">
                                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-[#007DFE]/10 text-[#007DFE] flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">GCash Online</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate"><?= round($dpPercent) ?>% Down Payment</p>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer" id="payMethodPickupLabel">
                                        <input class="peer sr-only" name="payment_method" type="radio" value="pickup" id="payMethodPickup">
                                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">storefront</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">Pay at Counter</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Upon Store Pick-up</p>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer hidden" id="payMethodCodLabel">
                                        <input class="peer sr-only" name="payment_method" type="radio" value="cod" id="payMethodCod">
                                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all peer-checked:border-terra-green peer-checked:bg-terra-green/5 peer-checked:ring-1 peer-checked:ring-terra-green flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">Cash on Delivery</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Pay upon Arrival</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Special Instructions -->
                            <div>
                                <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 block" id="notesLabel">Special Instructions (Optional)</label>
                                <textarea name="notes" id="printingNotes" rows="2" placeholder="e.g. Back-to-back, page range to print, specific paper color..." class="w-full p-3 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-terra-green focus:border-terra-green text-slate-900 dark:text-white placeholder:text-slate-400"></textarea>
                            </div>

                            <!-- Pricing Breakdown & Down Payment -->
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">

                                <div class="bg-slate-50 dark:bg-slate-800/80 p-4 rounded-2xl space-y-2 border border-slate-200/90 dark:border-slate-700">

                                    <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                                        <div>
                                            <span class="font-bold">Total Printing Price:</span>
                                            <span class="text-[11px] text-slate-400 block font-normal font-mono" id="printing-rate-helper"></span>
                                        </div>
                                        <span class="text-sm font-black text-slate-900 dark:text-white font-mono" id="printing-total-price">₱0.00</span>
                                    </div>

                                    <div class="flex justify-between items-center text-sm font-bold border-t border-slate-200/60 dark:border-slate-700 pt-2" id="prDownPaymentRow">
                                        <span class="text-slate-800 dark:text-slate-200 flex items-center gap-1.5 font-bold">
                                            <span class="material-symbols-outlined text-[17px] text-[#007DFE]">account_balance_wallet</span>
                                            <span id="dpPercentLabel"><?= round($dpPercent) ?>%</span> Down Payment:
                                        </span>
                                        <span class="text-base font-black text-[#007DFE] font-mono" id="printing-down-payment">₱0.00</span>
                                    </div>

                                </div>

                                <!-- Trust & Security Subtext -->
                                <div class="flex items-center gap-2 px-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    <span class="material-symbols-outlined text-[16px] text-terra-green">verified_user</span>
                                    <span id="prTrustSubtext">Pay <?= round($dpPercent) ?>% now via GCash / PayMongo. Remaining balance paid on pickup or delivery.</span>
                                </div>

                                <!-- Primary Submit Button -->
                                <?php if (!empty($isShopClosedToday)): ?>
                                    <button type="button" disabled class="w-full py-3 bg-rose-500/15 border border-rose-500/30 text-rose-600 dark:text-rose-400 rounded-2xl font-bold text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">lock</span>
                                        <span>Store Closed Today<?= $closureNoteText !== '' ? ' (' . esc($closureNoteText) . ')' : '' ?> — Cannot Submit Printing Order</span>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" id="btnSubmitPrinting" class="w-full py-3 bg-[#007DFE] hover:bg-[#006bd6] text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                        <span id="submitBtnText">Pay <?= round($dpPercent) ?>% Down Payment via GCash</span>
                                    </button>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </section>

    <?php endif; ?>

    <!-- Product Catalog Bento Section -->
    <section class="mb-12">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">

            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2 border border-terra-green/20">
                    <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                    Store Inventory
                </span>
                <h2 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Product Catalog</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Essential school and office merchandise available for direct purchase</p>
            </div>

            <form method="GET" action="<?= base_url('shop/' . ($shop['slug'] ?? $shop['store_slug'] ?? $shop['id'])) ?>" class="flex gap-2 w-full md:w-auto" role="search">

                <div class="relative flex-1 md:w-72">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input name="q" value="<?= esc($searchQuery ?? '') ?>" class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-terra-green focus:border-terra-green text-xs font-medium text-slate-900 dark:text-white placeholder:text-slate-400 shadow-2xs" placeholder="Search shop merchandise..." type="text">
                </div>

                <button type="submit" aria-label="Filter products" class="px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-600 dark:text-slate-300 hover:text-terra-green hover:border-terra-green transition-colors shadow-2xs shrink-0 flex items-center justify-center active:scale-95">
                    <span class="material-symbols-outlined text-[20px]">filter_list</span>
                </button>

            </form>

        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4 md:gap-5">

            <?php if (!empty($products)): ?>

                <?php foreach ($products as $p): ?>

                    <div class="bento-card group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 hover:border-terra-green/40 dark:hover:border-terra-green/40 hover:shadow-xl transition-all duration-300 flex flex-col justify-between shadow-2xs">

                        <a href="<?= base_url('product/' . $p['id']) ?>" class="block relative aspect-square overflow-hidden bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center p-3">

                            <img class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" src="<?= esc(product_image_url($p['image_url'] ?? null)) ?>" alt="<?= esc($p['name']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80';">

                            <?php if (!empty($p['is_bestseller'])): ?>
                                <span class="absolute top-2.5 right-2.5 bg-amber-500 text-white px-2.5 py-0.5 rounded-full text-[10px] font-black shadow-xs z-10 uppercase tracking-wide">
                                    Hot
                                </span>
                            <?php endif; ?>

                        </a>

                        <div class="p-3.5 sm:p-4 flex flex-col flex-grow justify-between gap-2.5">

                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white group-hover:text-terra-green dark:group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                                    <a href="<?= base_url('product/' . $p['id']) ?>"><?= esc($p['name']) ?></a>
                                </h3>
                                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 line-clamp-1 hidden sm:block"><?= esc($p['description'] ?? '') ?></p>
                            </div>

                            <div class="flex justify-between items-center pt-2.5 border-t border-slate-100 dark:border-slate-800">

                                <span class="text-xs sm:text-base font-black text-terra-green dark:text-emerald-400 font-mono">₱<?= number_format($p['price'], 2) ?></span>

                                <?php if ((int) ($p['variant_count'] ?? 0) > 0): ?>
                                    <a href="<?= base_url('product/' . $p['id'] . '?select_option=1#variants-section') ?>" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-terra-green/10 hover:bg-terra-green text-terra-green hover:text-white dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-terra-green dark:hover:text-white flex items-center justify-center transition-all shadow-2xs active:scale-95" aria-label="Select product option" title="Select Product Option">
                                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                    </a>
                                <?php else: ?>
                                    <form action="<?= base_url('cart/add') ?>" method="POST">

                                        <?= csrf_field() ?>

                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">

                                        <button type="submit" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-terra-green/10 hover:bg-terra-green text-terra-green hover:text-white dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-terra-green dark:hover:text-white flex items-center justify-center transition-all shadow-2xs active:scale-95" aria-label="Add to cart" title="Add to Cart">
                                            <span class="material-symbols-outlined text-[16px] sm:text-[18px]">add_shopping_cart</span>
                                        </button>

                                    </form>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-span-full text-center py-12 bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-8 shadow-xs">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">inventory_2</span>
                    <?php if (($searchQuery ?? '') !== ''): ?>
                        <p class="text-slate-800 dark:text-slate-200 font-bold text-sm">No products matched &quot;<?= esc($searchQuery) ?>&quot;.</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try another search keyword or browse all categories.</p>
                    <?php else: ?>
                        <p class="text-slate-600 dark:text-slate-300 font-medium text-sm">No products currently listed in this shop catalog.</p>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>

        <?php
            $totalPages = $totalPages ?? 1;
            $currentPage = $currentPage ?? 1;
            $searchQuery = $searchQuery ?? '';
            $pageUrl = function (int $p) use ($shop, $searchQuery) {
                $params = ['page' => $p];
                if ($searchQuery !== '') {
                    $params['q'] = $searchQuery;
                }
                return base_url('shop/' . ($shop['slug'] ?? $shop['store_slug'] ?? $shop['id']) . '?' . http_build_query($params));
            };

            $windowPages = [];
            if ($totalPages <= 7) {
                $windowPages = range(1, $totalPages);
            } else {
                $windowPages = [1];
                $left = max(2, $currentPage - 2);
                $right = min($totalPages - 1, $currentPage + 2);
                if ($left > 2) {
                    $windowPages[] = '...';
                }
                for ($i = $left; $i <= $right; $i++) {
                    $windowPages[] = $i;
                }
                if ($right < $totalPages - 1) {
                    $windowPages[] = '...';
                }
                $windowPages[] = $totalPages;
            }
        ?>

        <?php if ($totalPages > 1): ?>

            <div class="mt-8 flex justify-center">

                <nav class="flex items-center gap-1.5" aria-label="Pagination">

                    <?php if ($currentPage > 1): ?>
                        <a href="<?= $pageUrl($currentPage - 1) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Previous page">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </a>
                    <?php else: ?>
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200/50 dark:border-slate-800 text-slate-300 dark:text-slate-700 cursor-not-allowed" aria-disabled="true">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </span>
                    <?php endif; ?>

                    <?php foreach ($windowPages as $pg): ?>
                        <?php if ($pg === '...'): ?>
                            <span class="px-2 text-slate-400 font-mono" aria-hidden="true">...</span>
                        <?php elseif ((int) $pg === $currentPage): ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" aria-current="page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-terra-green text-white font-mono font-bold text-xs shadow-xs"><?= (int) $pg ?></a>
                        <?php else: ?>
                            <a href="<?= $pageUrl((int) $pg) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors font-mono font-bold text-xs"><?= (int) $pg ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= $pageUrl($currentPage + 1) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs" aria-label="Next page">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    <?php else: ?>
                        <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200/50 dark:border-slate-800 text-slate-300 dark:text-slate-700 cursor-not-allowed" aria-disabled="true">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </span>
                    <?php endif; ?>

                </nav>

            </div>

        <?php endif; ?>

    </section>

    <!-- Shop Reviews Bento Section -->
    <section id="shop-reviews" class="mt-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Customer Reviews</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Authentic feedback from verified community patrons</p>
            </div>
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-full font-mono border border-slate-200/80 dark:border-slate-700">
                <?= (int) ($shopReviewCount ?? 0) ?> review<?= ((int) ($shopReviewCount ?? 0)) === 1 ? '' : 's' ?>
            </span>
        </div>

        <?php if ($session = session()->get('isLoggedIn')): ?>
            <div class="bento-card bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/90 dark:border-slate-800 mb-8 shadow-xs">
                <h3 class="font-display text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-terra-green text-[20px]">rate_review</span>
                    <span><?= $userShopReview ? 'Update Your Review' : 'Write a Shop Review' ?></span>
                </h3>

                <form action="<?= base_url('reviews/shop/save') ?>" method="POST" class="space-y-4" id="shop-review-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="shop_id" value="<?= esc($shop['id']) ?>">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Your Rating</label>
                        <div class="star-rating flex items-center gap-2" role="radiogroup" aria-label="Select rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="shop-star-<?= $i ?>" class="sr-only" <?= ($userShopReview && (int) $userShopReview['rating'] === $i) ? 'checked' : '' ?> required>
                                <label for="shop-star-<?= $i ?>" class="cursor-pointer text-2xl text-slate-300 dark:text-slate-600 hover:text-amber-500 transition-colors <?= ($userShopReview && (int) $userShopReview['rating'] >= $i) ? 'text-amber-500 fill-icon' : '' ?>" data-star="<?= $i ?>">
                                    <span class="material-symbols-outlined">star</span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" for="shop-review-comment">Your Review (optional)</label>
                        <textarea name="comment" id="shop-review-comment" rows="3" class="w-full p-3 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green focus:border-transparent transition-all" placeholder="Share your experience with this shop... (optional)" maxlength="2000"><?= esc($userShopReview['comment'] ?? '') ?></textarea>
                        <p class="text-[11px] text-slate-400 mt-1 text-right">Max 2000 characters</p>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-terra-green hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition-colors shadow-xs active:scale-95">
                            <?= $userShopReview ? 'Update Review' : 'Submit Review' ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="bento-card bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 mb-8 text-center shadow-xs">
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Please <a href="<?= base_url('login') ?>" class="text-terra-green font-bold hover:underline">sign in</a> to write a shop review.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($shopReviews)): ?>
            <div class="space-y-4" id="shop-reviews-list">
                <?php foreach ($shopReviews as $review): ?>
                    <div class="bento-card bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                        <div class="flex items-start gap-3 mb-2.5">
                            <div class="w-10 h-10 rounded-full bg-terra-green/10 text-terra-green dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 ring-2 ring-terra-green/20">
                                <?= strtoupper(substr(($review['first_name'] ?? '') . ($review['last_name'] ?? ''), 0, 1)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate"><?= esc(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?></p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <div class="flex items-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="material-symbols-outlined text-[15px] <?= $i <= (int) $review['rating'] ? 'fill-icon text-amber-500' : 'text-slate-200 dark:text-slate-700' ?>">star</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-mono ml-1"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($review['comment'])): ?>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed pl-13"><?= esc($review['comment']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bento-card bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/90 dark:border-slate-800 text-center shadow-xs">
                <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2">rate_review</span>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">No reviews yet. Be the first to review this shop!</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- PDF Upload Status Modal -->
    <div id="pdf-upload-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 text-center animate-in fade-in zoom-in-95 duration-200">
            
            <div id="pdf-modal-icon-bg" class="w-16 h-16 rounded-2xl mx-auto flex items-center justify-center transition-all duration-300">
                <span id="pdf-modal-icon" class="material-symbols-outlined !text-4xl"></span>
            </div>

            <div class="space-y-1">
                <h3 id="pdf-modal-title" class="font-display text-lg font-bold text-slate-900 dark:text-white"></h3>
                <p id="pdf-modal-message" class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-xs mx-auto"></p>
            </div>

            <div id="pdf-modal-details" class="bg-slate-50 dark:bg-slate-800/80 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700 text-left space-y-1.5 hidden">
                <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                    <span>File Name:</span>
                    <span id="pdf-modal-filename" class="font-semibold text-slate-900 dark:text-white truncate max-w-[200px]"></span>
                </div>
                <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-400">
                    <span>Total Pages:</span>
                    <span id="pdf-modal-pagecount" class="font-bold text-terra-green font-mono"></span>
                </div>
            </div>

            <div class="pt-2">
                <button type="button" id="pdf-modal-close-btn" class="w-full py-2.5 px-6 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-xs active:scale-95 flex items-center justify-center gap-1.5">
                    <span>Continue</span>
                </button>
            </div>

        </div>
    </div>

</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
const selectPdfBtn = document.getElementById('selectPdfBtn');
const badge = document.getElementById('upload-badge');
const pageCount = document.getElementById('page-count');
const uploadHint = document.getElementById('upload-hint');
const hiddenPageCount = document.getElementById('pdf-page-count');

function showPdfModal(status, title, message, filename, pages) {
    const modal = document.getElementById('pdf-upload-modal');
    const iconBg = document.getElementById('pdf-modal-icon-bg');
    const icon = document.getElementById('pdf-modal-icon');
    const titleEl = document.getElementById('pdf-modal-title');
    const msgEl = document.getElementById('pdf-modal-message');
    const details = document.getElementById('pdf-modal-details');
    const filenameEl = document.getElementById('pdf-modal-filename');
    const pagecountEl = document.getElementById('pdf-modal-pagecount');
    const closeBtn = document.getElementById('pdf-modal-close-btn');

    if (!modal) return;

    if (status === 'success') {
        iconBg.className = 'w-16 h-16 rounded-2xl mx-auto flex items-center justify-center transition-all duration-300 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400';
        icon.textContent = 'check_circle';
        titleEl.textContent = title || 'PDF Uploaded Successfully';
        titleEl.className = 'font-display text-lg font-bold text-emerald-700 dark:text-emerald-400';
        msgEl.textContent = message || 'Your document has been verified and processed for printing.';

        if (filename && pages !== undefined && pages !== null) {
            filenameEl.textContent = filename;
            pagecountEl.textContent = pages + ' Page' + (parseInt(pages, 10) > 1 ? 's' : '');
            details.classList.remove('hidden');
        } else {
            details.classList.add('hidden');
        }

        closeBtn.className = 'w-full py-2.5 px-6 rounded-xl font-bold text-xs sm:text-sm bg-terra-green hover:bg-emerald-700 text-white transition-all shadow-xs active:scale-95 flex items-center justify-center gap-1.5';
        closeBtn.querySelector('span').textContent = 'Continue';

    } else {
        iconBg.className = 'w-16 h-16 rounded-2xl mx-auto flex items-center justify-center transition-all duration-300 bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400';
        icon.textContent = 'cancel';
        titleEl.textContent = title || 'PDF Upload Failed';
        titleEl.className = 'font-display text-lg font-bold text-rose-700 dark:text-rose-400';
        msgEl.textContent = message || 'An error occurred while uploading your file.';
        details.classList.add('hidden');

        closeBtn.className = 'w-full py-2.5 px-6 rounded-xl font-bold text-xs sm:text-sm bg-rose-600 hover:bg-rose-700 text-white transition-all shadow-xs active:scale-95 flex items-center justify-center gap-1.5';
        closeBtn.querySelector('span').textContent = 'Try Again';
    }

    modal.classList.remove('hidden');
}

const modalCloseBtn = document.getElementById('pdf-modal-close-btn');
const pdfModal = document.getElementById('pdf-upload-modal');
if (modalCloseBtn && pdfModal) {
    modalCloseBtn.addEventListener('click', function() {
        pdfModal.classList.add('hidden');
    });
    pdfModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
}

function setUploadState(state, pageCountValue) {
    if (!badge || !pageCount || !hiddenPageCount) return;
    const badgeIcon = badge.querySelector('.material-symbols-outlined');
    if (state === 'ready') {
        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center gap-1';
        if (badgeIcon) badgeIcon.textContent = 'check_circle';
        badge.lastChild.textContent = ' Ready Successfully';
        pageCount.className = 'text-xs font-bold text-terra-green font-mono';
        pageCount.textContent = 'Total Pages: ' + pageCountValue;
        hiddenPageCount.value = pageCountValue;
        if (uploadHint) uploadHint.classList.add('hidden');
    } else if (state === 'error') {
        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 flex items-center gap-1';
        if (badgeIcon) badgeIcon.textContent = 'error';
        badge.lastChild.textContent = ' ' + pageCountValue;
        pageCount.className = 'text-xs font-bold text-rose-600 font-mono';
        pageCount.textContent = 'Unable to verify PDF';
        hiddenPageCount.value = 0;
        if (uploadHint) uploadHint.classList.remove('hidden');
        uploadHint.textContent = pageCountValue + ' Please select a valid PDF file.';
    } else {
        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1';
        if (badgeIcon) badgeIcon.textContent = 'info';
        badge.lastChild.textContent = ' No PDF selected';
        pageCount.className = 'text-xs font-bold text-slate-500 dark:text-slate-400 font-mono';
        pageCount.textContent = 'No file selected';
        hiddenPageCount.value = 0;
        if (uploadHint) uploadHint.classList.add('hidden');
    }
}

function isValidFile(file) {
    if (!file) return false;
    const isDocx = document.getElementById('docTypeDocx')?.checked;
    if (isDocx) {
        return /\.(docx|doc)$/i.test(file.name);
    }
    return /\.pdf$/i.test(file.name);
}

function toggleDocType(type) {
    const isDocx = type === 'docx';
    const dropzoneTitle = document.getElementById('dropzoneTitle');
    const dropzoneDesc = document.getElementById('dropzoneDesc');
    const dropzoneFooter = document.getElementById('dropzoneFooter');
    const selectBtnText = document.getElementById('selectBtnText');
    const dropzoneIcon = document.getElementById('dropzoneIcon');
    const fileInput = document.getElementById('fileInput');
    const docxPageCountContainer = document.getElementById('docxPageCountContainer');
    const docxChangesContainer = document.getElementById('docxChangesContainer');
    const hiddenPageCount = document.getElementById('pdf-page-count');
    const badge = document.getElementById('upload-badge');
    const pageCount = document.getElementById('page-count');

    // Reset current file input
    if (fileInput) fileInput.value = '';

    if (isDocx) {
        if (fileInput) fileInput.setAttribute('accept', '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        if (dropzoneTitle) dropzoneTitle.textContent = 'Upload your Word Document';
        if (dropzoneDesc) dropzoneDesc.textContent = 'Drag & drop your .doc or .docx file here, or click to browse.';
        if (dropzoneFooter) dropzoneFooter.textContent = 'Max file size: 50MB • Formats: DOC, DOCX';
        if (selectBtnText) selectBtnText.textContent = 'Select Word Document';
        if (dropzoneIcon) dropzoneIcon.textContent = 'description';

        if (docxPageCountContainer) docxPageCountContainer.classList.remove('hidden');
        if (docxChangesContainer) docxChangesContainer.classList.remove('hidden');

        const estInput = document.getElementById('docxEstimatedPages');
        let estPages = parseInt(estInput?.value || '0', 10);
        if (estPages <= 0) {
            estPages = 1;
            if (estInput) estInput.value = '1';
        }
        if (hiddenPageCount) hiddenPageCount.value = estPages;
        if (badge) {
            badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1';
            const icon = badge.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = 'description';
            badge.lastChild.textContent = ' Word Document selected';
        }
        if (pageCount) {
            pageCount.className = 'text-xs font-bold text-terra-green font-mono';
            pageCount.textContent = estPages + ' estimated page' + (estPages === 1 ? '' : 's');
        }
    } else {
        if (fileInput) fileInput.setAttribute('accept', '.pdf');
        if (dropzoneTitle) dropzoneTitle.textContent = 'Upload your PDF';
        if (dropzoneDesc) dropzoneDesc.textContent = 'Drag & drop your document here, or click to browse files from your device.';
        if (dropzoneFooter) dropzoneFooter.textContent = 'Max file size: 50MB • Formats: PDF only';
        if (selectBtnText) selectBtnText.textContent = 'Select PDF Document';
        if (dropzoneIcon) dropzoneIcon.textContent = 'picture_as_pdf';

        if (docxPageCountContainer) docxPageCountContainer.classList.add('hidden');
        if (docxChangesContainer) docxChangesContainer.classList.add('hidden');
        const refContainer = document.getElementById('docxReferencePhotosContainer');
        if (refContainer) refContainer.classList.add('hidden');

        if (hiddenPageCount) hiddenPageCount.value = 0;
        setUploadState('empty');
        toggleDocxChanges('as_is');
    }

    updatePrintingPrice();
}

function toggleDocxChanges(mode) {
    const hasChanges = mode === 'has_changes';
    const photosContainer = document.getElementById('docxReferencePhotosContainer');
    const notesLabel = document.getElementById('notesLabel');
    const notesTextarea = document.getElementById('printingNotes');

    if (hasChanges) {
        if (photosContainer) photosContainer.classList.remove('hidden');
        if (notesLabel) notesLabel.innerHTML = 'Special Instructions / Change Details <span class="text-rose-500 font-bold">*</span>';
        if (notesTextarea) notesTextarea.placeholder = 'Please describe in detail the exact changes, edits, or formatting adjustments to make before printing...';
    } else {
        if (photosContainer) photosContainer.classList.add('hidden');
        if (notesLabel) notesLabel.innerHTML = 'Special Instructions (Optional)';
        if (notesTextarea) notesTextarea.placeholder = 'e.g. Back-to-back, page range to print, specific paper color...';
    }
}

function handleManualPageCount(val) {
    const pages = Math.max(1, parseInt(val, 10) || 1);
    const hiddenPageCount = document.getElementById('pdf-page-count');
    const pageCount = document.getElementById('page-count');
    if (hiddenPageCount) hiddenPageCount.value = pages;
    if (pageCount) {
        pageCount.className = 'text-xs font-bold text-terra-green font-mono';
        pageCount.textContent = pages + ' estimated page' + (pages === 1 ? '' : 's');
    }
    updatePrintingPrice();
}

function validatePrintingSubmit(e) {
    const isDocx = document.getElementById('docTypeDocx')?.checked;
    const fileInput = document.getElementById('fileInput');
    const hiddenPageCount = document.getElementById('pdf-page-count');
    const pages = parseInt(hiddenPageCount?.value || '0', 10);

    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please attach a document file before submitting.');
        e.preventDefault();
        return false;
    }

    if (pages <= 0) {
        if (isDocx) {
            alert('Please enter an estimated page count of at least 1 for your Word document.');
            document.getElementById('docxEstimatedPages')?.focus();
        } else {
            alert('Please wait for the PDF page count to be verified or upload a valid PDF.');
        }
        e.preventDefault();
        return false;
    }

    if (isDocx) {
        const changeRadio = document.querySelector('input[name="doc_change_type"]:checked');
        if (changeRadio && changeRadio.value === 'has_changes') {
            const notes = (document.getElementById('printingNotes')?.value || '').trim();
            if (notes === '') {
                alert('Special Instructions / Change Details are required when requesting document changes.');
                document.getElementById('printingNotes')?.focus();
                e.preventDefault();
                return false;
            }
        }
    }

    const payMethodVal = document.querySelector('input[name="payment_method"]:checked')?.value || 'gcash';
    const btn = document.getElementById('btnSubmitPrinting');
    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-70');
        const btnText = document.getElementById('submitBtnText');
        if (btnText) btnText.textContent = payMethodVal === 'gcash' ? 'Connecting to PayMongo...' : 'Submitting Request...';
    }

    return true;
}

if (dropzone && fileInput) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('drag-active'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('drag-active'), false);
    });

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    dropzone.addEventListener('click', function(e) {
        if (e.target !== selectPdfBtn) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    if (selectPdfBtn) {
        selectPdfBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.click();
        });
    }

    function handleFiles(files) {
        if (!files || files.length === 0) {
            setUploadState('empty');
            return;
        }

        const file = files[0];
        const isDocx = document.getElementById('docTypeDocx')?.checked;

        if (!isValidFile(file)) {
            const errMsg = isDocx
                ? 'Invalid file type. Only Word documents (.doc, .docx) are accepted.'
                : 'Invalid file type. Only PDF documents are accepted.';
            setUploadState('error', errMsg);
            showPdfModal('error', 'Upload Failed', errMsg);
            return;
        }

        try {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        } catch (err) {}

        if (isDocx) {
            if (badge) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center gap-1';
                const icon = badge.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = 'check_circle';
                badge.lastChild.textContent = ' Word Document Attached';
            }

            const formData = new FormData();
            formData.append('document', file, file.name);

            fetch('<?= base_url('printing/count-pages') ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const estInput = document.getElementById('docxEstimatedPages');
                let count = 1;
                if (data && data.success && data.page_count > 0) {
                    count = data.page_count;
                } else if (estInput && parseInt(estInput.value, 10) > 0) {
                    count = parseInt(estInput.value, 10);
                }
                if (estInput) estInput.value = count;
                if (hiddenPageCount) hiddenPageCount.value = count;
                if (pageCount) {
                    pageCount.className = 'text-xs font-bold text-terra-green font-mono';
                    pageCount.textContent = count + ' page' + (count === 1 ? '' : 's') + ' detected';
                }
                updatePrintingPrice();
                showPdfModal('success', 'Word Document Uploaded', 'Your document was verified and page count was calculated.', file.name, count);
            })
            .catch(() => {
                const estInput = document.getElementById('docxEstimatedPages');
                let estVal = parseInt(estInput?.value || '1', 10);
                if (estVal <= 0) estVal = 1;
                if (estInput) estInput.value = estVal;
                if (hiddenPageCount) hiddenPageCount.value = estVal;
                if (pageCount) {
                    pageCount.className = 'text-xs font-bold text-terra-green font-mono';
                    pageCount.textContent = estVal + ' estimated page' + (estVal === 1 ? '' : 's');
                }
                updatePrintingPrice();
                showPdfModal('success', 'Word Document Uploaded', 'Document attached. Please verify your estimated page count below.', file.name, estVal);
            });
            return;
        }

        // PDF Document handling
        if (badge) {
            badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 flex items-center gap-1';
            if (badge.querySelector('.material-symbols-outlined')) badge.querySelector('.material-symbols-outlined').textContent = 'sync';
            badge.lastChild.textContent = ' Processing PDF...';
            pageCount.className = 'text-xs font-bold text-slate-500 dark:text-slate-400 font-mono';
            pageCount.textContent = 'Counting pages...';
        }

        const formData = new FormData();
        formData.append('document', file, file.name);

        fetch('<?= base_url('printing/count-pages') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            const pageManualContainer = document.getElementById('docxPageCountContainer');
            if (ok && data && data.success && data.page_count > 0) {
                if (pageManualContainer && !document.getElementById('docTypeDocx')?.checked) {
                    pageManualContainer.classList.add('hidden');
                }
                setUploadState('ready', data.page_count);
                updatePrintingPrice();
                showPdfModal('success', 'PDF Uploaded Successfully', 'Your document was verified and page count was calculated.', file.name, data.page_count);
            } else if (data && data.can_manual) {
                if (pageManualContainer) {
                    pageManualContainer.classList.remove('hidden');
                    const label = pageManualContainer.querySelector('label span:first-child');
                    if (label) label.innerHTML = 'Confirm Total PDF Pages <span class="text-rose-500">*</span>';
                }
                const fallbackPages = Math.max(1, parseInt(document.getElementById('docxEstimatedPages')?.value || '1', 10));
                setUploadState('ready', fallbackPages);
                updatePrintingPrice();
                showPdfModal('success', 'PDF Attached', 'Document attached. Please confirm your total page count below.', file.name, fallbackPages);
            } else {
                const msg = (data && data.error) ? data.error : 'Unable to determine page count.';
                setUploadState('error', msg);
                updatePrintingPrice();
                showPdfModal('error', 'PDF Upload Failed', msg);
            }
        })
        .catch(() => {
            const pageManualContainer = document.getElementById('docxPageCountContainer');
            if (pageManualContainer) {
                pageManualContainer.classList.remove('hidden');
                const label = pageManualContainer.querySelector('label span:first-child');
                if (label) label.innerHTML = 'Confirm Total PDF Pages <span class="text-rose-500">*</span>';
            }
            const fallbackPages = Math.max(1, parseInt(document.getElementById('docxEstimatedPages')?.value || '1', 10));
            setUploadState('ready', fallbackPages);
            updatePrintingPrice();
            showPdfModal('success', 'PDF Attached', 'Document attached. Please confirm your total page count below.', file.name, fallbackPages);
        });
    }

    function updatePrintingPrice() {
        const globalPriceColor = <?= (float) ($printingSettings['price_color_per_page'] ?? 5.00) ?>;
        const globalPriceBw    = <?= (float) ($printingSettings['price_bw_per_page'] ?? 2.00) ?>;

        const isDocx = document.getElementById('docTypeDocx')?.checked;
        let pages = parseInt(hiddenPageCount ? hiddenPageCount.value : 0, 10) || 0;
        if (isDocx && pages <= 0) {
            const estInput = document.getElementById('docxEstimatedPages');
            pages = parseInt(estInput?.value || '1', 10);
            if (pages <= 0) pages = 1;
            if (hiddenPageCount) hiddenPageCount.value = pages;
        }

        const colorRadio = document.querySelector('input[name="color_mode"]:checked');
        const isColored = colorRadio ? (colorRadio.value === 'colored') : true;

        const copiesInput = document.getElementById('copiesInput') || document.querySelector('input[name="copies"]');
        let rawCopies = parseInt(copiesInput ? copiesInput.value : 1, 10);
        if (isNaN(rawCopies) || rawCopies < 1) rawCopies = 1;
        if (rawCopies > 500) rawCopies = 500;
        const copies = rawCopies;

        const bindingRadio = document.querySelector('input[name="binding"]:checked');
        const bindingCost = parseFloat(bindingRadio ? (bindingRadio.dataset.cost || 0) : 0);

        const downPaymentPercent = <?= (float) ($printingSettings['down_payment_percent'] ?? 50.00) ?>;

        if (pages <= 0) {
            const elTotal = document.getElementById('printing-total-price');
            const elDown = document.getElementById('printing-down-payment');
            const elHelper = document.getElementById('printing-rate-helper');
            if (elTotal) elTotal.textContent = '₱0.00';
            if (elDown) elDown.textContent = '₱0.00';
            if (elHelper) elHelper.textContent = '';
            return;
        }

        // Global rates based on color mode only
        const basePerPage = isColored ? globalPriceColor : globalPriceBw;

        let total = ((pages * basePerPage) + bindingCost) * copies;
        let downPayment = total * (downPaymentPercent / 100.00);

        const elTotal = document.getElementById('printing-total-price');
        const elDown = document.getElementById('printing-down-payment');
        const elHelper = document.getElementById('printing-rate-helper');
        if (elTotal) elTotal.textContent = '₱' + total.toFixed(2);
        if (elDown) elDown.textContent = '₱' + downPayment.toFixed(2);
        if (elHelper) {
            let helper = `${pages} page${pages > 1 ? 's' : ''} × ₱${basePerPage.toFixed(2)} (${isColored ? 'Full Color' : 'B&W'})`;
            if (bindingCost > 0) helper += ` + ₱${bindingCost.toFixed(2)} binding`;
            if (copies > 1) helper += ` × ${copies} copies`;
            elHelper.textContent = helper;
        }

        updatePrintingPaymentUI();
    }

    function updatePrintingPaymentUI() {
        const fulfillmentVal = document.querySelector('input[name="fulfillment_method"]:checked')?.value || 'pickup';
        const pickupPayLabel = document.getElementById('payMethodPickupLabel');
        const codPayLabel = document.getElementById('payMethodCodLabel');
        const payPickupInput = document.getElementById('payMethodPickup');
        const payCodInput = document.getElementById('payMethodCod');
        const payGcashInput = document.getElementById('payMethodGcash');

        if (fulfillmentVal === 'delivery') {
            if (pickupPayLabel) pickupPayLabel.classList.add('hidden');
            if (codPayLabel) codPayLabel.classList.remove('hidden');
            if (payPickupInput && payPickupInput.checked && payCodInput) {
                payCodInput.checked = true;
            }
        } else {
            if (pickupPayLabel) pickupPayLabel.classList.remove('hidden');
            if (codPayLabel) codPayLabel.classList.add('hidden');
            if (payCodInput && payCodInput.checked && payPickupInput) {
                payPickupInput.checked = true;
            }
        }

        const payMethodVal = document.querySelector('input[name="payment_method"]:checked')?.value || 'gcash';
        const btn = document.getElementById('btnSubmitPrinting');
        const btnText = document.getElementById('submitBtnText');
        const subtext = document.getElementById('prTrustSubtext');
        const dpRow = document.getElementById('prDownPaymentRow');
        const dpPercent = <?= (float) ($printingSettings['down_payment_percent'] ?? 50.00) ?>;

        if (payMethodVal === 'gcash') {
            if (dpRow) dpRow.classList.remove('hidden');
            if (btn) btn.className = 'w-full py-3 bg-[#007DFE] hover:bg-[#006bd6] text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2';
            if (btnText) btnText.textContent = 'Pay ' + Math.round(dpPercent) + '% Down Payment via GCash';
            if (subtext) subtext.textContent = 'Pay ' + Math.round(dpPercent) + '% now via GCash / PayMongo. Remaining balance paid on pickup or delivery.';
        } else if (payMethodVal === 'pickup') {
            if (dpRow) dpRow.classList.add('hidden');
            if (btn) btn.className = 'w-full py-3 bg-terra-green hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2';
            if (btnText) btnText.textContent = 'Submit Printing Request (Pay at Counter)';
            if (subtext) subtext.textContent = 'No online payment required now. Pay total upon picking up at the store.';
        } else {
            if (dpRow) dpRow.classList.add('hidden');
            if (btn) btn.className = 'w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2';
            if (btnText) btnText.textContent = 'Submit Printing Request (Cash on Delivery)';
            if (subtext) subtext.textContent = 'No online payment required now. Payment will be collected upon doorstep delivery.';
        }
    }

    document.querySelectorAll('input[name="payment_method"], input[name="fulfillment_method"]').forEach(r => {
        r.addEventListener('change', updatePrintingPaymentUI);
    });

    const copiesInput = document.getElementById('copiesInput');
    const btnMinus = document.getElementById('btnCopiesMinus');
    const btnPlus  = document.getElementById('btnCopiesPlus');

    if (btnMinus && copiesInput) {
        btnMinus.addEventListener('click', () => {
            let val = parseInt(copiesInput.value, 10) || 1;
            if (val > 1) {
                copiesInput.value = val - 1;
                updatePrintingPrice();
            }
        });
    }

    if (btnPlus && copiesInput) {
        btnPlus.addEventListener('click', () => {
            let val = parseInt(copiesInput.value, 10) || 1;
            if (val < 500) {
                copiesInput.value = val + 1;
                updatePrintingPrice();
            }
        });
    }

    if (copiesInput) {
        copiesInput.addEventListener('input', () => {
            let val = parseInt(copiesInput.value, 10);
            if (!isNaN(val)) {
                if (val < 1) copiesInput.value = 1;
                if (val > 500) copiesInput.value = 500;
            }
            updatePrintingPrice();
        });
        copiesInput.addEventListener('blur', () => {
            let val = parseInt(copiesInput.value, 10);
            if (isNaN(val) || val < 1) copiesInput.value = 1;
            if (val > 500) copiesInput.value = 500;
            updatePrintingPrice();
        });
    }

    document.querySelectorAll('input[name="color_mode"], select[name="paper_size"], input[name="copies"], input[name="binding"]').forEach(el => {
        el.addEventListener('change', updatePrintingPrice);
    });

    const docxPagesInput = document.getElementById('docxEstimatedPages');
    if (docxPagesInput) {
        docxPagesInput.addEventListener('input', function() {
            handleManualPageCount(this.value);
        });
        docxPagesInput.addEventListener('change', function() {
            handleManualPageCount(this.value);
        });
    }

    // Reference Photos Thumbnail Preview
    const refPhotosInput = document.getElementById('referencePhotosInput');
    if (refPhotosInput) {
        refPhotosInput.addEventListener('change', function() {
            const previewContainer = document.getElementById('refPhotosPreviewGrid');
            if (!previewContainer) return;
            previewContainer.innerHTML = '';
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach((f, idx) => {
                    if (!f.type.startsWith('image/')) return;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const thumb = document.createElement('div');
                        thumb.className = 'relative group aspect-square rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-2xs';
                        thumb.innerHTML = `
                            <img src="${e.target.result}" alt="${f.name}" class="w-full h-full object-cover">
                            <span class="absolute bottom-0 inset-x-0 bg-black/75 text-white text-[9px] px-1 py-0.5 truncate text-center">${f.name}</span>
                        `;
                        previewContainer.appendChild(thumb);
                    };
                    reader.readAsDataURL(f);
                });
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }

    // Report Shop Modal Handlers
    const reportModal = document.getElementById('report-shop-modal');
    const btnOpenReport = document.getElementById('btn-open-report-modal');
    const btnCloseReport = document.getElementById('btn-close-report-modal');
    const btnCancelReport = document.getElementById('btn-cancel-report');

    if (btnOpenReport && reportModal) {
        btnOpenReport.addEventListener('click', () => reportModal.classList.remove('hidden'));
    }
    if (btnCloseReport && reportModal) {
        btnCloseReport.addEventListener('click', () => reportModal.classList.add('hidden'));
    }
    if (btnCancelReport && reportModal) {
        btnCancelReport.addEventListener('click', () => reportModal.classList.add('hidden'));
    }
    if (reportModal) {
        reportModal.addEventListener('click', (e) => {
            if (e.target === reportModal) reportModal.classList.add('hidden');
        });
    }

    // Weekly Business Hours Dropdown Handler
    const hoursContainer = document.getElementById('hours-dropdown-container');
    const hoursTrigger = document.getElementById('hours-dropdown-trigger');
    const hoursDropdown = document.getElementById('full-business-hours');
    const hoursChevron = document.getElementById('hours-chevron');

    if (hoursTrigger && hoursDropdown) {
        hoursTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = hoursDropdown.classList.contains('hidden');
            if (isHidden) {
                hoursDropdown.classList.remove('hidden');
                if (hoursChevron) hoursChevron.classList.add('rotate-180');
            } else {
                hoursDropdown.classList.add('hidden');
                if (hoursChevron) hoursChevron.classList.remove('rotate-180');
            }
        });

        document.addEventListener('click', function(e) {
            if (hoursContainer && !hoursContainer.contains(e.target)) {
                hoursDropdown.classList.add('hidden');
                if (hoursChevron) hoursChevron.classList.remove('rotate-180');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !hoursDropdown.classList.contains('hidden')) {
                hoursDropdown.classList.add('hidden');
                if (hoursChevron) hoursChevron.classList.remove('rotate-180');
            }
        });
    }
}
</script>

<?= $this->endSection() ?>
