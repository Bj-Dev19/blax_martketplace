<?php
    $csrfToken = csrf_hash();
    $csrfName  = csrf_token();
    $defaultGreeting = 'Hello! I am your Blax AI shopping and delivery assistant. How can I help you find merchandise, calculate custom printing quotes, or track orders in Polomolok today?';
    $greeting = $greeting ?? $defaultGreeting;
?>

<!-- AI Assistant Floating Action Button -->
<button id="ai-toggle-btn" 
        type="button" 
        aria-label="Open Blax AI Assistant" 
        class="fixed bottom-20 right-4 sm:bottom-6 sm:right-6 group z-40 flex items-center gap-2 p-1.5 bg-terra-green hover:bg-terra-green-dark text-white rounded-full shadow-[0_8px_30px_rgba(74,148,62,0.35)] hover:shadow-[0_12px_36px_rgba(74,148,62,0.45)] hover:scale-105 active:scale-95 transition-all duration-300">
    <div class="relative w-12 h-12 sm:w-13 sm:h-13 rounded-full flex items-center justify-center">
        <span class="material-symbols-outlined text-[26px] sm:text-[28px] group-hover:rotate-12 transition-transform duration-300">auto_awesome</span>
        <!-- Online Pulse Dot -->
        <span class="absolute top-1 right-1 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-terra-amber opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-terra-amber border-2 border-white dark:border-slate-900"></span>
        </span>
    </div>
    <span class="hidden sm:inline-block pr-4 pl-1 font-bold text-xs tracking-wide">Ask Blax AI</span>
</button>

<!-- AI Assistant Modal Window -->
<div id="ai-modal" class="fixed inset-0 z-50 pointer-events-none hidden justify-end sm:justify-end sm:items-end p-0 sm:p-0 sm:inset-auto sm:bottom-6 sm:right-6" style="display: none;">

    <!-- Mobile Backdrop Scrim -->
    <div id="ai-backdrop" class="fixed inset-0 bg-black/50 backdrop-blur-xs sm:hidden pointer-events-auto -z-10 transition-opacity"></div>

    <div class="w-full sm:w-[420px] h-[92dvh] sm:h-[620px] max-h-[92dvh] sm:max-h-[660px] rounded-t-3xl sm:rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.35)] flex flex-col pointer-events-auto transition-all duration-300 border-t sm:border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl overflow-hidden animate-in slide-in-from-bottom-6 sm:zoom-in-95 duration-200">

        <!-- Mobile Drawer Drag Handle -->
        <div class="w-12 h-1 rounded-full bg-slate-300 dark:bg-slate-700 mx-auto mt-2.5 mb-1 sm:hidden shrink-0"></div>

        <!-- Modern Bento Header -->
        <div class="px-4 py-3 bg-white/80 dark:bg-slate-900/80 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between shrink-0 backdrop-blur-md">
            <div class="flex items-center gap-2.5">
                <div class="relative">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/60 dark:border-emerald-800/40 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shadow-2xs">
                        <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-emerald-500 border border-white dark:border-slate-900 rounded-full"></span>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white tracking-tight m-0">Blax Assistant</h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                        Online &bull; Polomolok Shopping &amp; Print Guide
                    </p>
                </div>
            </div>

            <!-- Header Controls -->
            <div class="flex items-center gap-1">
                <button type="button" id="ai-reset-btn" title="Start new conversation" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                </button>
                <button type="button" id="ai-close-btn" title="Close assistant" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all">
                    <span class="material-symbols-outlined text-[19px]">close</span>
                </button>
            </div>
        </div>

        <!-- Chat History Container -->
        <div id="chat-history" class="flex-1 overflow-y-auto p-4 space-y-3.5 custom-scrollbar bg-[#faf7f2]/40 dark:bg-slate-950/40 text-xs sm:text-sm">

            <!-- Initial Hero Greeting -->
            <div class="flex items-start gap-2.5 max-w-[92%]">
                <div class="w-7 h-7 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200/60 dark:border-emerald-800/40">
                    <span class="material-symbols-outlined text-[15px]">smart_toy</span>
                </div>
                <div class="space-y-1">
                    <div class="bg-white dark:bg-slate-850 text-slate-700 dark:text-slate-200 p-3.5 rounded-2xl rounded-tl-xs border border-slate-200/70 dark:border-slate-800 shadow-2xs leading-relaxed text-xs sm:text-sm">
                        <?= esc($greeting) ?>
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium block px-1">Just now</span>
                </div>
            </div>

            <!-- Starter Prompt Suggestions (Compact 2x2 Grid) -->
            <div id="starter-prompts-wrap" class="space-y-2 pt-2">
                <p class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-1">Quick Suggestions</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button type="button" class="faq-prompt-btn p-2.5 rounded-xl bg-white dark:bg-slate-850 hover:bg-emerald-50/50 dark:hover:bg-slate-800 border border-slate-200/70 dark:border-slate-800 hover:border-emerald-400/50 transition-all text-left flex items-center gap-2.5 group shadow-2xs cursor-pointer" data-prompt="What school, office, and merchandise products are available?">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[15px]">inventory_2</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block truncate group-hover:text-emerald-700 dark:group-hover:text-emerald-300">Office &amp; School Supplies</span>
                            <span class="text-[10px] text-slate-400 block truncate">Browse catalog</span>
                        </div>
                    </button>
                    <button type="button" class="faq-prompt-btn p-2.5 rounded-xl bg-white dark:bg-slate-850 hover:bg-amber-50/50 dark:hover:bg-slate-800 border border-slate-200/70 dark:border-slate-800 hover:border-amber-400/50 transition-all text-left flex items-center gap-2.5 group shadow-2xs cursor-pointer" data-prompt="How do custom PDF printing requests and downpayments work?">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[15px]">print</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block truncate group-hover:text-amber-700 dark:group-hover:text-amber-300">Print Quotes &amp; Orders</span>
                            <span class="text-[10px] text-slate-400 block truncate">Rates &amp; upload process</span>
                        </div>
                    </button>
                    <button type="button" class="faq-prompt-btn p-2.5 rounded-xl bg-white dark:bg-slate-850 hover:bg-sky-50/50 dark:hover:bg-slate-800 border border-slate-200/70 dark:border-slate-800 hover:border-sky-400/50 transition-all text-left flex items-center gap-2.5 group shadow-2xs cursor-pointer" data-prompt="How do I track my delivery or use store pickup QR code?">
                        <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[15px]">local_shipping</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block truncate group-hover:text-sky-700 dark:group-hover:text-sky-300">Track Orders &amp; QR</span>
                            <span class="text-[10px] text-slate-400 block truncate">Couriers &amp; pickup</span>
                        </div>
                    </button>
                    <button type="button" class="faq-prompt-btn p-2.5 rounded-xl bg-white dark:bg-slate-850 hover:bg-purple-50/50 dark:hover:bg-slate-800 border border-slate-200/70 dark:border-slate-800 hover:border-purple-400/50 transition-all text-left flex items-center gap-2.5 group shadow-2xs cursor-pointer" data-prompt="Show verified shops and print partners in Polomolok">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[15px]">storefront</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 block truncate group-hover:text-purple-700 dark:group-hover:text-purple-300">Verified Local Shops</span>
                            <span class="text-[10px] text-slate-400 block truncate">Polomolok merchants</span>
                        </div>
                    </button>
                </div>
            </div>

        </div>

        <!-- Quick Prompt Chips Floating Bar (Visible when conversation is active) -->
        <div id="quick-chips-bar" class="hidden px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 overflow-x-auto no-scrollbar whitespace-nowrap gap-1.5 shrink-0">
            <button type="button" class="faq-prompt-btn px-3 py-1 rounded-full bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 text-[11px] font-medium border border-slate-200/70 dark:border-slate-700 shrink-0 inline-flex items-center gap-1 transition-colors cursor-pointer" data-prompt="Show popular products">
                <span class="material-symbols-outlined text-[13px] text-amber-500">local_fire_department</span> Popular items
            </button>
            <button type="button" class="faq-prompt-btn px-3 py-1 rounded-full bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 text-[11px] font-medium border border-slate-200/70 dark:border-slate-700 shrink-0 inline-flex items-center gap-1 transition-colors cursor-pointer" data-prompt="How fast is doorstep delivery in Polomolok?">
                <span class="material-symbols-outlined text-[13px] text-emerald-600">speed</span> Delivery speed
            </button>
            <button type="button" class="faq-prompt-btn px-3 py-1 rounded-full bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 text-[11px] font-medium border border-slate-200/70 dark:border-slate-700 shrink-0 inline-flex items-center gap-1 transition-colors cursor-pointer" data-prompt="What are the accepted payment methods?">
                <span class="material-symbols-outlined text-[13px] text-slate-500">payments</span> GCash &amp; COD
            </button>
        </div>

        <!-- Calm, Zen Input Dock -->
        <div class="p-3 sm:p-3.5 bg-white/95 dark:bg-slate-900/95 border-t border-slate-100 dark:border-slate-800 rounded-b-none sm:rounded-b-3xl shrink-0">
            <form id="ai-chat-form" class="relative flex items-center gap-1.5 bg-slate-50/90 dark:bg-slate-800/60 rounded-full border border-slate-200/80 dark:border-slate-700/80 focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-600/15 transition-all p-1.5 pl-3.5 shadow-2xs">
                <?= csrf_field() ?>
                <input type="hidden" name="csrf_name" value="<?= esc($csrfName) ?>">
                <input type="hidden" name="csrf_token" value="<?= esc($csrfToken) ?>">

                <input id="ai-input" 
                       class="w-full bg-transparent border-none focus:outline-none focus:ring-0 text-slate-800 dark:text-white placeholder:text-slate-400 py-1.5 text-xs sm:text-sm font-medium" 
                       placeholder="Ask about products, orders, printing..." 
                       type="text" 
                       autocomplete="off">

                <!-- Voice Input Button -->
                <button type="button" id="ai-mic-btn" title="Voice search" class="p-1.5 text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 rounded-full transition-all shrink-0 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">mic</span>
                </button>

                <!-- Send Button (Smooth circle with arrow) -->
                <button type="submit" id="ai-send-btn" class="w-8 h-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full active:scale-95 transition-all shadow-xs shrink-0 flex items-center justify-center disabled:opacity-30 disabled:pointer-events-none cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                </button>
            </form>
        </div>

    </div>

</div>

<script>
(function () {
    const toggleBtn = document.getElementById('ai-toggle-btn');
    const closeBtn  = document.getElementById('ai-close-btn');
    const backdrop  = document.getElementById('ai-backdrop');
    const resetBtn  = document.getElementById('ai-reset-btn');
    const modal     = document.getElementById('ai-modal');
    const form      = document.getElementById('ai-chat-form');
    const input     = document.getElementById('ai-input');
    const history   = document.getElementById('chat-history');
    const micBtn    = document.getElementById('ai-mic-btn');
    const sendBtn   = document.getElementById('ai-send-btn');
    const starterPrompts = document.getElementById('starter-prompts-wrap');
    const quickChips = document.getElementById('quick-chips-bar');

    if (!toggleBtn || !modal) return;

    // --- Voice Recognition ---
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition && micBtn) {
        const recognition = new SpeechRecognition();
        recognition.lang = 'en-US';
        recognition.continuous = false;
        recognition.interimResults = true;
        let listening = false;
        let finalTranscript = '';

        function setMicState(active) {
            listening = active;
            micBtn.querySelector('span').textContent = active ? 'mic_off' : 'mic';
            micBtn.classList.toggle('text-rose-500', active);
            micBtn.classList.toggle('animate-pulse', active);
            micBtn.classList.toggle('bg-rose-500/10', active);
            micBtn.classList.toggle('text-slate-400', !active);
        }

        micBtn.addEventListener('click', () => {
            if (listening) {
                recognition.stop();
            } else {
                finalTranscript = '';
                input.value = '';
                input.placeholder = 'Listening to your voice...';
                try { recognition.start(); } catch(err) {}
            }
        });

        recognition.onstart = () => setMicState(true);
        recognition.onresult = (e) => {
            let interim = '';
            for (let i = 0; i < e.results.length; i++) {
                if (e.results[i].isFinal) {
                    finalTranscript += e.results[i][0].transcript;
                } else {
                    interim += e.results[i][0].transcript;
                }
            }
            input.value = finalTranscript + interim;
            updateSendButtonState();
        };

        recognition.onend = () => {
            setMicState(false);
            input.placeholder = 'Ask about products, orders, printing...';
            if (input.value.trim()) {
                setTimeout(() => form.requestSubmit(), 150);
            }
        };

        recognition.onerror = () => {
            setMicState(false);
            input.placeholder = 'Ask about products, orders, printing...';
        };
    } else if (micBtn) {
        micBtn.style.display = 'none';
    }

    // Modal open/close handling
    function openModal() {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'flex-col');
        setTimeout(() => input && input.focus(), 100);
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'flex-col');
    }

    toggleBtn.addEventListener('click', () => {
        if (modal.style.display === 'none' || modal.classList.contains('hidden')) {
            openModal();
        } else {
            closeModal();
        }
    });

    closeBtn.addEventListener('click', closeModal);

    if (backdrop) {
        backdrop.addEventListener('click', closeModal);
    }

    // Reset conversation
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (confirm('Start a new conversation?')) {
                window.location.reload();
            }
        });
    }

    // Send button active state toggle
    function updateSendButtonState() {
        if (!sendBtn || !input) return;
        const hasText = input.value.trim().length > 0;
        sendBtn.disabled = !hasText;
    }
    input.addEventListener('input', updateSendButtonState);
    updateSendButtonState();

    function escapeHtml(text) {
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function getFormattedTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    const PRODUCT_URL = '<?= base_url('product/') ?>';
    const FALLBACK_IMG = 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=300&q=80';

    function peso(value) {
        const n = parseFloat(value);
        if (isNaN(n)) return '';
        return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function resolveImgUrl(url) {
        if (!url) return FALLBACK_IMG;
        if (url.startsWith('http://') || url.startsWith('https://')) return url;
        return '<?= base_url() ?>/' + url.replace(/^\/+/, '');
    }

    // Product card inside chat
    function productCard(p) {
        const inStock = Number(p.stock_quantity) > 0;
        const url = PRODUCT_URL + encodeURIComponent(p.id);
        const img = escapeHtml(resolveImgUrl(p.image_url));
        const stockBadge = inStock
            ? '<span class="text-[9px] font-bold text-terra-green bg-terra-green/10 dark:bg-terra-green/20 px-1.5 py-0.5 rounded-md">In Stock</span>'
            : '<span class="text-[9px] font-bold text-rose-600 bg-rose-100 dark:bg-rose-950/40 px-1.5 py-0.5 rounded-md">Out of Stock</span>';

        return `
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 overflow-hidden shadow-2xs hover:shadow-sm transition-all flex flex-col group">
            <a href="${url}" class="block h-24 bg-slate-100 dark:bg-slate-700/50 overflow-hidden relative">
                <img src="${img}" alt="${escapeHtml(p.name)}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.onerror=null;this.src='${FALLBACK_IMG}';">
                <div class="absolute top-1.5 right-1.5">${stockBadge}</div>
            </a>
            <div class="p-2 flex flex-col flex-grow justify-between gap-1">
                <div>
                    <a href="${url}" class="font-bold text-[11px] leading-snug line-clamp-2 text-slate-900 dark:text-white hover:text-terra-green transition-colors">${escapeHtml(p.name)}</a>
                    ${p.shop_name ? `<div class="text-[9px] text-terra-earth dark:text-amber-400 font-semibold uppercase tracking-tight flex items-center gap-0.5 mt-0.5 truncate"><span class="material-symbols-outlined text-[10px]">store</span> ${escapeHtml(p.shop_name)}</div>` : ''}
                </div>
                <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between mt-auto">
                    <span class="text-terra-green font-mono font-extrabold text-xs">${peso(p.price)}</span>
                    <a href="${url}" class="px-2 py-0.5 rounded-lg bg-terra-green/10 hover:bg-terra-green hover:text-white text-terra-green font-bold text-[10px] transition-all">View</a>
                </div>
            </div>
        </div>`;
    }

    function formatMessageText(text) {
        if (!text) return '';
        let safe = escapeHtml(text);
        safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        safe = safe.replace(/\n\n/g, '<br><br>').replace(/\n/g, '<br>');
        return safe;
    }

    function appendAssistant(text, products, actions) {
        if (starterPrompts) starterPrompts.classList.add('hidden');
        if (quickChips) quickChips.classList.remove('hidden');

        const wrap = document.createElement('div');
        wrap.className = 'flex items-start gap-2 max-w-[95%] animate-in fade-in slide-in-from-bottom-2 duration-200';

        let productsHtml = '';
        if (Array.isArray(products) && products.length > 0) {
            productsHtml = '<div class="grid grid-cols-2 gap-2 w-full pt-2">' + products.map(productCard).join('') + '</div>';
        }

        let actionsHtml = '';
        if (Array.isArray(actions) && actions.length > 0) {
            actionsHtml = '<div class="pt-2 flex flex-wrap gap-1.5">' + actions.map(act => `
                <a href="${escapeHtml(act.url)}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-terra-green text-white text-xs font-bold hover:bg-terra-green-dark transition-all shadow-2xs">
                    ${act.icon ? `<span class="material-symbols-outlined text-[15px]">${escapeHtml(act.icon)}</span>` : ''}
                    <span>${escapeHtml(act.label)}</span>
                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                </a>
            `).join('') + '</div>';
        }

        wrap.innerHTML = `
            <div class="w-7 h-7 rounded-xl bg-terra-green/10 dark:bg-terra-green/20 text-terra-green flex items-center justify-center shrink-0 mt-1 border border-terra-green/20">
                <span class="material-symbols-outlined text-[16px]">smart_toy</span>
            </div>
            <div class="space-y-1 flex-1">
                <div class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 p-3.5 rounded-2xl rounded-tl-xs border border-slate-200/80 dark:border-slate-700/80 shadow-xs leading-relaxed">
                    ${formatMessageText(text)}
                    ${actionsHtml}
                    ${productsHtml}
                </div>
                <span class="text-[10px] text-slate-400 font-mono block px-1">${getFormattedTime()}</span>
            </div>
        `;

        history.appendChild(wrap);
        history.scrollTop = history.scrollHeight;
    }

    function appendUserMessage(text) {
        if (starterPrompts) starterPrompts.classList.add('hidden');
        if (quickChips) quickChips.classList.remove('hidden');

        const wrap = document.createElement('div');
        wrap.className = 'flex flex-col items-end gap-1 ml-auto max-w-[85%] animate-in fade-in slide-in-from-bottom-2 duration-200';

        wrap.innerHTML = `
            <div class="bg-terra-green text-white p-3 rounded-2xl rounded-tr-xs shadow-xs font-medium leading-relaxed">
                ${escapeHtml(text)}
            </div>
            <span class="text-[10px] text-slate-400 font-mono px-1">${getFormattedTime()}</span>
        `;

        history.appendChild(wrap);
        history.scrollTop = history.scrollHeight;
    }

    function appendTyping() {
        const el = document.createElement('div');
        el.className = 'flex items-start gap-2 max-w-[85%] animate-in fade-in duration-150';
        el.innerHTML = `
            <div class="w-7 h-7 rounded-xl bg-terra-green/10 dark:bg-terra-green/20 text-terra-green flex items-center justify-center shrink-0 mt-1 border border-terra-green/20">
                <span class="material-symbols-outlined text-[16px]">smart_toy</span>
            </div>
            <div class="bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5 rounded-2xl rounded-tl-xs border border-slate-200/80 dark:border-slate-700/80 shadow-xs flex items-center gap-2">
                <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-terra-green rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-terra-green rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-terra-green rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Blax AI is searching...</span>
            </div>
        `;
        history.appendChild(el);
        history.scrollTop = history.scrollHeight;
        return el;
    }

    let busy = false;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (busy) return;

        const query = input.value.trim();
        if (!query) return;

        busy = true;
        appendUserMessage(query);
        input.value = '';
        updateSendButtonState();

        const typing = appendTyping();

        try {
            const fd = new FormData();
            fd.append('message', query);
            fd.append('<?= esc($csrfName) ?>', '<?= esc($csrfToken) ?>');
            const response = await fetch('<?= base_url('ai-assistant/chat') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });

            let data = null;
            try { data = await response.json(); } catch (parseErr) { data = null; }

            if (typing.parentNode) history.removeChild(typing);

            if (!response.ok || !data || data.status === 'error') {
                const errMsg = (data && data.message) ? data.message : 'I had trouble checking that just now. Could you please rephrase your request?';
                appendAssistant(errMsg, [], []);
            } else {
                appendAssistant(data.reply || 'Here is what I found for you in Polomolok:', data.products, data.actions);
            }
        } catch (err) {
            if (typing.parentNode) history.removeChild(typing);
            appendAssistant('I could not connect to the assistant service. Please check your internet connection and try again.', [], []);
        } finally {
            busy = false;
            input.focus();
        }
    });

    // Prompt chip listeners
    document.body.addEventListener('click', (e) => {
        const btn = e.target.closest('.faq-prompt-btn');
        if (btn) {
            const prompt = btn.dataset.prompt;
            if (prompt && input) {
                input.value = prompt;
                updateSendButtonState();
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    });
})();
</script>