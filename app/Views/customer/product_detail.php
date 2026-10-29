<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<main class="max-w-container-max mx-auto px-4 md:px-8 lg:px-10 py-6 md:py-10 pb-28 flex-grow w-full">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">

        <!-- Left: Visual Assets Stage (Bento Card 7 Cols) -->
        <div class="lg:col-span-7 bento-card p-4 sm:p-6 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl space-y-4 shadow-2xs">

            <?php
                $primaryImgUrl = null;
                if (!empty($productImages)) {
                    foreach ($productImages as $img) {
                        if (!empty($img['is_primary'])) {
                            $primaryImgUrl = $img['image_url'];
                            break;
                        }
                    }
                    if (!$primaryImgUrl && isset($productImages[0])) {
                        $primaryImgUrl = $productImages[0]['image_url'];
                    }
                }
                $displayMainImg = $primaryImgUrl ?: ($product['image_url'] ?? null);
                $stockQty = (int) ($product['stock_quantity'] ?? 0);
                $outOfStock = $stockQty <= 0;
            ?>

            <div class="relative rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 aspect-square sm:aspect-[4/3] lg:aspect-square max-h-[520px] w-full group flex items-center justify-center p-4">

                <img id="main-product-image" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" src="<?= esc(product_image_url($displayMainImg, 'detail')) ?>" alt="<?= esc($product['name']) ?>">

                <?php if (!empty($product['is_bestseller'])): ?>
                    <div class="absolute top-3 left-3 bg-terra-green text-white px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase shadow-2xs">Bestseller</div>
                <?php endif; ?>

            </div>

            <?php if (!empty($productImages)): ?>
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2.5 sm:gap-3">
                    <?php foreach ($productImages as $idx => $img): ?>
                        <?php 
                            $thumbUrl = product_image_url($img['image_url'] ?? null, 'thumbnail'); 
                            $detailUrl = product_image_url($img['image_url'] ?? null, 'detail');
                            $isMainThumb = (!empty($img['is_primary']) || ($idx === 0 && !$primaryImgUrl));
                        ?>

                        <button type="button" class="gallery-thumb rounded-xl overflow-hidden h-18 sm:h-20 cursor-pointer transition-all p-1 bg-slate-50 dark:bg-slate-800 <?= $isMainThumb ? 'border-2 border-terra-green ring-2 ring-terra-green/20' : 'border border-slate-200 dark:border-slate-700 hover:border-terra-green/50' ?>" data-image="<?= esc($detailUrl) ?>" aria-label="View product image">
                            <img class="w-full h-full object-contain" src="<?= esc($thumbUrl) ?>" alt="<?= esc($img['alt_text'] ?? $product['name']) ?>" loading="lazy" decoding="async">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right: Product Controls & Purchase Box (Bento Card 5 Cols) -->
        <div class="lg:col-span-5 bento-card p-6 sm:p-7 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-3xl flex flex-col justify-between shadow-2xs">

            <div>

                <nav class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-3 flex-wrap">
                    <a class="hover:text-terra-green transition-colors" href="<?= base_url('/') ?>">Home</a>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>

                    <?php if (!empty($product['category_name'])): ?>
                        <a class="hover:text-terra-green transition-colors" href="<?= base_url('categories') ?>"><?= esc($product['category_name']) ?></a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <?php endif; ?>

                    <span class="text-slate-800 dark:text-slate-200 line-clamp-1"><?= esc($product['name']) ?></span>
                </nav>

                <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2.5 leading-tight"><?= esc($product['name']) ?></h1>

                <div class="flex items-center gap-2 mb-4 flex-wrap text-xs">
                    <div class="flex items-center text-terra-amber">
                        <?php
                            $rating    = (float) ($product['rating_average'] ?? 0);
                            $fullStars = (int) floor($rating);
                            $hasHalf   = ($rating - $fullStars) >= 0.5;
                        ?>

                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $fullStars): ?>
                                <span class="material-symbols-outlined fill-icon text-[17px]">star</span>
                            <?php elseif ($i === $fullStars + 1 && $hasHalf): ?>
                                <span class="material-symbols-outlined text-[17px]">star_half</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-[17px] text-slate-300 dark:text-slate-600">star</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <span class="ml-1 font-bold font-mono text-slate-800 dark:text-slate-200"><?= number_format($rating, 1) ?></span>
                    </div>

                    <a href="#reviews" class="text-slate-400 hover:text-terra-green font-mono transition-colors">(<?= number_format($product['rating_count'] ?? 0) ?> reviews)</a>

                    <?php if (!empty($product['sku'])): ?>
                        <div class="h-3.5 w-px bg-slate-200 dark:bg-slate-700 mx-0.5"></div>
                        <span class="text-slate-500 dark:text-slate-400 font-mono text-[11px]">SKU: <?= esc($product['sku']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Clean Price & Stock Status Box (Zero Duplicate Description Clutter) -->
                <div class="bg-slate-50 dark:bg-slate-800/80 rounded-2xl p-4 sm:p-5 mb-5 border border-slate-200/90 dark:border-slate-700/80 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-baseline gap-2.5 flex-wrap font-mono">
                        <span class="text-3xl sm:text-4xl font-black text-terra-green">₱<?= number_format($product['price'], 2) ?></span>

                        <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                            <span class="text-slate-400 line-through text-sm">₱<?= number_format($product['compare_at_price'], 2) ?></span>
                            <span class="font-bold text-xs bg-terra-amber/15 text-terra-amber-dark dark:text-terra-amber px-2 py-0.5 rounded-full border border-terra-amber/30">
                                Save <?= round((1 - $product['price'] / $product['compare_at_price']) * 100) ?>%
                            </span>
                        <?php endif; ?>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold <?= $outOfStock ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $outOfStock ? 'bg-rose-500' : 'bg-emerald-500' ?>"></span>
                        <?= $outOfStock ? 'Out of Stock' : ($stockQty . ' in stock') ?>
                    </span>
                </div>

                <!-- Action Controls -->
                <div class="mt-auto space-y-4">

                    <?php
                        $groupedVariants = [];
                        if (!empty($variants)) {
                            foreach ($variants as $v) {
                                $groupedVariants[$v['name']][] = $v;
                            }
                        }
                        $hasVariants = !empty($groupedVariants);
                        $firstGroupName = $hasVariants ? array_key_first($groupedVariants) : 'option';
                        $cartDisabled = $outOfStock || $hasVariants;
                        $shopOperationalStatus = $shopOperationalStatus ?? ['is_closed_today' => false, 'note' => ''];
                        $isShopClosedToday     = !empty($shopOperationalStatus['is_closed_today']);
                        $shopClosureNote       = trim((string) ($shopOperationalStatus['note'] ?? ''));
                        $buyNowDisabled        = $cartDisabled || $isShopClosedToday;
                        $highlightSelectOption = !empty($_GET['select_option']);
                    ?>

                    <?php if ($isShopClosedToday): ?>
                        <div class="rounded-2xl border border-rose-500/35 bg-rose-500/10 p-3.5 mb-3 flex items-start gap-2.5 text-xs">
                            <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px] shrink-0">event_busy</span>
                            <div class="space-y-0.5">
                                <p class="font-bold text-rose-700 dark:text-rose-300">
                                    Store Closed Today<?= $shopClosureNote !== '' ? ' • ' . esc($shopClosureNote) : '' ?>
                                </p>
                                <p class="text-slate-600 dark:text-slate-300">
                                    You can still select options and <strong>Add to Cart</strong> for later, but instant checkout (Buy Now) is paused until the store reopens.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($hasVariants): ?>
                        <div class="bg-slate-50 dark:bg-slate-800/70 rounded-2xl p-3.5 sm:p-4 mb-3 border <?= $highlightSelectOption ? 'border-2 border-terra-green ring-2 ring-terra-green/20' : 'border-slate-200 dark:border-slate-700' ?> space-y-3 transition-all" id="variants-section">
                            <?php if ($highlightSelectOption): ?>
                                <div class="flex items-center gap-1.5 text-xs font-bold text-terra-green bg-terra-green/10 px-3 py-1.5 rounded-xl border border-terra-green/25">
                                    <span class="material-symbols-outlined text-[16px]">touch_app</span>
                                    <span>Please select your preferred <?= esc($firstGroupName) ?> option below to add this product to your cart.</span>
                                </div>
                            <?php endif; ?>
                            <?php foreach ($groupedVariants as $groupName => $options): ?>
                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Select <?= esc($groupName) ?>:</span>
                                        <span id="selected-val-<?= esc(url_title($groupName, '-', true)) ?>" class="text-xs font-bold text-terra-green"></span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php foreach ($options as $opt): ?>
                                            <?php $isOutOfStock = (int) $opt['stock_quantity'] <= 0; ?>
                                            <button type="button"
                                                    class="variant-pill px-3 py-1.5 rounded-xl border text-xs font-semibold transition-all flex items-center gap-1 <?= $isOutOfStock ? 'opacity-40 border-dashed border-slate-300 cursor-not-allowed line-through bg-slate-100 dark:bg-slate-800 text-slate-400' : 'border-slate-200 dark:border-slate-700 hover:border-terra-green bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 shadow-2xs' ?>"
                                                    <?= $isOutOfStock ? 'disabled title="Out of Stock"' : '' ?>
                                                    data-id="<?= (int) $opt['id'] ?>"
                                                    data-group="<?= esc($groupName) ?>"
                                                    data-group-slug="<?= esc(url_title($groupName, '-', true)) ?>"
                                                    data-value="<?= esc($opt['value']) ?>"
                                                    data-stock="<?= (int) $opt['stock_quantity'] ?>"
                                                    data-price="<?= $opt['price_override'] !== null ? (float) $opt['price_override'] : '' ?>">
                                                <span><?= esc($opt['value']) ?></span>
                                                <?php if ($opt['price_override'] !== null && (float) $opt['price_override'] > 0): ?>
                                                    <span class="text-[10px] opacity-75 font-mono">₱<?= number_format((float) $opt['price_override'], 2) ?></span>
                                                <?php endif; ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div id="variant-stock-status" class="text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-1 pt-1.5 border-t border-slate-200 dark:border-slate-700">
                                <span class="material-symbols-outlined text-[16px] text-terra-green">info</span>
                                <span id="variant-stock-text">Please select a <?= esc($firstGroupName) ?> above</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('cart/add') ?>" method="POST" id="product-purchase-form" class="space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="variant_id" id="selected-variant-id" value="">
                        <input type="hidden" name="quantity" id="quantity-input" value="1" min="1" max="<?= esc($product['stock_quantity'] ?? 99) ?>">

                        <div class="flex items-center gap-2.5">
                            <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl px-2 py-1 border border-slate-200 dark:border-slate-700 shadow-inner">
                                <button type="button" id="qty-minus" class="p-1 px-2 hover:bg-white dark:hover:bg-slate-700 rounded-lg text-slate-600 dark:text-slate-300 transition-colors" <?= $outOfStock ? 'disabled' : '' ?> aria-label="Decrease quantity">
                                    <span class="material-symbols-outlined text-[18px]">remove</span>
                                </button>

                                <span id="quantity-display" class="w-9 text-center font-mono font-bold text-sm text-slate-800 dark:text-slate-100">1</span>

                                <button type="button" id="qty-plus" class="p-1 px-2 hover:bg-white dark:hover:bg-slate-700 rounded-lg text-slate-600 dark:text-slate-300 transition-colors" <?= $outOfStock ? 'disabled' : '' ?> aria-label="Increase quantity">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>

                            <button type="submit" id="add-to-cart-btn" formaction="<?= base_url('cart/add') ?>" formmethod="POST" class="flex-1 bg-terra-green/10 hover:bg-terra-green text-terra-green hover:text-white py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm border border-terra-green/30 transition-all flex items-center justify-center gap-2 shadow-2xs active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed" <?= $cartDisabled ? 'disabled' : '' ?>>
                                <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                                <span id="cart-btn-text"><?= $outOfStock ? 'Out of Stock' : ($hasVariants ? 'Select an option' : 'Add to Cart') ?></span>
                            </button>
                        </div>

                        <?php if ($isShopClosedToday): ?>
                            <button type="button" id="buy-now-btn" disabled data-shop-closed="1" class="w-full bg-rose-500/15 border border-rose-500/30 text-rose-600 dark:text-rose-400 py-3 rounded-xl font-bold text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                <span>Store Closed Today<?= $shopClosureNote !== '' ? ' (' . esc($shopClosureNote) . ')' : '' ?> — Cannot Order Now</span>
                            </button>
                        <?php else: ?>
                            <button type="button" id="buy-now-btn" class="w-full bg-terra-green hover:bg-terra-green-dark text-white py-3 rounded-xl font-bold text-sm shadow-xs hover:shadow-md transition-all active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-1.5" <?= $cartDisabled ? 'disabled' : '' ?>>
                                <span class="material-symbols-outlined text-[18px]">bolt</span>
                                <span>Buy Now</span>
                            </button>
                        <?php endif; ?>
                    </form>

                    <div class="flex justify-center gap-4 py-3 border-t border-slate-100 dark:border-slate-800 mt-2 flex-wrap text-xs text-slate-500 dark:text-slate-400">
                        <?php if (!empty($product['warranty_period'])): ?>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-terra-green">verified_user</span>
                                <span><?= esc($product['warranty_period']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($product['return_policy_days'])): ?>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-terra-green">keyboard_return</span>
                                <span><?= (int) $product['return_policy_days'] ?>-Day Returns</span>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-terra-green">local_shipping</span>
                            <span>Road Distance Delivery & Store Pick-up</span>
                        </div>
                    </div>

                    <!-- Integrated Compact Merchant Storefront Card -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-terra-green shrink-0 shadow-2xs overflow-hidden">
                                <?php 
                                    $sLogoRaw = $product['shop_logo'] ?? $product['logo_url'] ?? '';
                                    $sLogoUrl = !empty($sLogoRaw) ? logo_url($sLogoRaw) : '';
                                ?>
                                <?php if (!empty($sLogoUrl)): ?>
                                    <img src="<?= esc($sLogoUrl) ?>" alt="<?= esc($product['shop_name'] ?? 'Merchant') ?>" class="w-full h-full object-cover" loading="lazy" decoding="async">
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1">
                                    <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate"><?= esc($product['shop_name'] ?? 'Polomolok Merchant') ?></p>
                                    <span class="material-symbols-outlined text-[14px] text-terra-green shrink-0" title="Verified Merchant">verified</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Verified Blax Marketplace Partner</p>
                            </div>
                        </div>
                        <a href="<?= base_url('shop/' . ($product['shop_slug'] ?? $product['shop_id'] ?? '')) ?>" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-terra-green hover:text-white text-terra-green border border-terra-green/30 text-xs font-bold transition-all shadow-2xs">
                            <span>Visit Store</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Balanced 2-Column Product Overview & Customer Reviews Section -->
    <section id="reviews" class="mt-8 sm:mt-10 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">

        <!-- Left (5 Cols): Product Specifications & Description -->
        <div class="lg:col-span-5 bento-card bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xs space-y-5">
            <div class="flex items-center gap-2.5 pb-3.5 border-b border-slate-100 dark:border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">description</span>
                </div>
                <div>
                    <h2 class="font-display text-lg font-extrabold text-slate-900 dark:text-white">Product Overview</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Details & specifications</p>
                </div>
            </div>

            <?php $cleanDesc = trim((string) ($product['description'] ?? '')); ?>
            <?php if ($cleanDesc !== ''): ?>
                <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed space-y-3">
                    <p><?= nl2br(esc($cleanDesc)) ?></p>
                </div>
            <?php endif; ?>

            <!-- Structured Specifications Table (Always informative even if description is brief) -->
            <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 divide-y divide-slate-200/60 dark:divide-slate-700/60 text-xs">
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Category</span>
                    <span class="font-semibold text-slate-900 dark:text-white"><?= esc($product['category_name'] ?? 'General Merchandise') ?></span>
                </div>
                <?php if (!empty($product['sku'])): ?>
                    <div class="flex items-center justify-between px-4 py-2.5">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Product SKU</span>
                        <span class="font-mono font-semibold text-slate-900 dark:text-white"><?= esc($product['sku']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Merchant</span>
                    <span class="font-semibold text-slate-900 dark:text-white"><?= esc($product['shop_name'] ?? 'Polomolok Merchant') ?></span>
                </div>
                <div class="flex items-center justify-between px-4 py-2.5">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Fulfillment</span>
                    <span class="font-semibold text-slate-900 dark:text-white">Doorstep Delivery / Store Pick-up</span>
                </div>
            </div>
        </div>

        <!-- Right (7 Cols): Customer Reviews -->
        <div class="lg:col-span-7 space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-terra-amber flex items-center justify-center shrink-0 border border-amber-200/60">
                        <span class="material-symbols-outlined text-[18px] fill-icon">star</span>
                    </div>
                    <div>
                        <h2 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">Customer Reviews</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Verified buyer feedback</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 font-mono">
                    <?= (int) ($reviewCount ?? 0) ?> review<?= ((int) ($reviewCount ?? 0)) === 1 ? '' : 's' ?>
                </span>
            </div>

            <?php if (session()->get('isLoggedIn')): ?>
                <?php if (!empty($userReview) || !empty($canReview)): ?>
                    <div class="bento-card bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                        <h3 class="font-display text-sm sm:text-base font-bold text-slate-900 dark:text-white mb-3">
                            <?= $userReview ? 'Update Your Review' : 'Write a Review' ?>
                        </h3>

                        <form action="<?= base_url('reviews/product/save') ?>" method="POST" class="space-y-4" id="product-review-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= esc($product['id']) ?>">
                            <input type="hidden" name="shop_id" value="<?= esc($product['shop_id'] ?? '') ?>">

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Your Rating</label>
                                <div class="star-rating flex items-center gap-1" role="radiogroup" aria-label="Select rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" name="rating" value="<?= $i ?>" id="star-<?= $i ?>" class="sr-only" <?= ($userReview && (int) $userReview['rating'] === $i) ? 'checked' : '' ?> required>
                                        <label for="star-<?= $i ?>" class="cursor-pointer text-2xl text-slate-300 dark:text-slate-600 hover:text-terra-amber transition-colors <?= ($userReview && (int) $userReview['rating'] >= $i) ? 'text-terra-amber fill-icon' : '' ?>" data-star="<?= $i ?>">
                                            <span class="material-symbols-outlined">star</span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5" for="review-comment">Your Review (optional)</label>
                                <textarea name="comment" id="review-comment" rows="3" class="w-full p-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-terra-green/30 focus:border-terra-green transition-all" placeholder="Share your experience with this product... (optional)" maxlength="2000"><?= esc($userReview['comment'] ?? '') ?></textarea>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-mono">Max 2000 characters</span>
                                <button type="submit" class="bg-terra-green hover:bg-terra-green-dark text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-xs transition-all cursor-pointer">
                                    <?= $userReview ? 'Update Review' : 'Submit Review' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php elseif (!empty($hasPendingOrder)): ?>
                    <div class="bento-card bg-amber-500/10 dark:bg-amber-500/15 p-5 sm:p-6 rounded-3xl border border-amber-500/30 flex items-start gap-3.5">
                        <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-2xl mt-0.5 shrink-0">local_shipping</span>
                        <div class="space-y-1">
                            <h3 class="font-display text-sm font-bold text-amber-900 dark:text-amber-200">Order in Progress</h3>
                            <p class="text-xs text-amber-800/90 dark:text-amber-300 leading-relaxed font-sans">
                                You have ordered this item. You can write a review once your order has been <strong>delivered</strong>.
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bento-card bg-slate-50 dark:bg-slate-900/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center gap-3 text-left">
                        <span class="material-symbols-outlined text-slate-400 text-xl shrink-0">verified</span>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-sans">
                            Only verified buyers who have received this product can write a review.
                        </p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="bento-card bg-slate-50 dark:bg-slate-900/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 text-center">
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-sans">Please <a href="<?= base_url('login') ?>" class="text-terra-green font-bold hover:underline">sign in</a> to write a review.</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($reviews)): ?>
                <div class="space-y-3.5" id="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="bento-card bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-2xs">
                            <div class="flex items-start gap-3 mb-2">
                                <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold text-xs shrink-0 border border-terra-green/20">
                                    <?= strtoupper(substr(($review['first_name'] ?? '') . ($review['last_name'] ?? ''), 0, 1)) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-xs text-slate-900 dark:text-white truncate"><?= esc(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?></p>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="material-symbols-outlined text-[15px] <?= $i <= (int) $review['rating'] ? 'fill-icon text-terra-amber' : 'text-slate-300 dark:text-slate-600' ?>">star</span>
                                        <?php endfor; ?>
                                        <span class="text-[10px] text-slate-400 ml-1 font-mono"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($review['comment'])): ?>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed"><?= esc($review['comment']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bento-card bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 text-center shadow-2xs">
                    <span class="material-symbols-outlined text-3xl text-slate-300 dark:text-slate-600 mb-1">rate_review</span>
                    <p class="text-xs text-slate-500 dark:text-slate-400">No reviews yet. Be the first to review this product!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Related Products Section -->
    <?php if (!empty($relatedProducts)): ?>
        <section class="mt-12 pt-8 border-t border-slate-200/80 dark:border-slate-800">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">auto_awesome</span>
                    </div>
                    <div>
                        <h2 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">Related Products</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Similar items in this department</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <?php
                        $relImg = product_image_url($rel['image_url'] ?? null);
                        $relPrice = (float) $rel['price'];
                        $relCompare = !empty($rel['compare_at_price']) ? (float) $rel['compare_at_price'] : 0;
                        $hasDiscount = $relCompare > $relPrice;
                        $discountPct = $hasDiscount ? round((($relCompare - $relPrice) / $relCompare) * 100) : 0;
                        $relRating = round((float) ($rel['rating_average'] ?? 0), 1);
                        $relRatingCount = (int) ($rel['rating_count'] ?? 0);
                    ?>
                    <div class="bento-card group bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden hover:shadow-md hover:border-terra-green/40 transition-all duration-300 flex flex-col justify-between shadow-2xs">
                        <div>
                            <div class="relative aspect-square overflow-hidden bg-slate-50 dark:bg-slate-850 p-2 flex items-center justify-center">
                                <a href="<?= base_url('product/' . $rel['id']) ?>" class="block w-full h-full flex items-center justify-center">
                                    <img class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" src="<?= esc($relImg) ?>" alt="<?= esc($rel['name']) ?>" loading="lazy" onerror="this.onerror=null; this.src='<?= esc(placeholder_image_url()) ?>';">
                                </a>

                                <?php if (!empty($rel['is_bestseller'])): ?>
                                    <span class="absolute top-1.5 left-1.5 bg-terra-green text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-2xs">Bestseller</span>
                                <?php elseif ($hasDiscount): ?>
                                    <span class="absolute top-1.5 left-1.5 bg-terra-amber text-slate-950 text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-2xs font-mono">-<?= $discountPct ?>%</span>
                                <?php endif; ?>
                            </div>

                            <div class="p-2.5 sm:p-3">
                                <a href="<?= base_url('product/' . $rel['id']) ?>" class="text-[11px] sm:text-xs font-semibold text-slate-900 dark:text-white line-clamp-2 group-hover:text-terra-green transition-colors leading-snug min-h-[2rem]" title="<?= esc($rel['name']) ?>">
                                    <?= esc($rel['name']) ?>
                                </a>

                                <div class="flex items-center gap-1 mt-1 text-[10px] text-slate-500">
                                    <span class="material-symbols-outlined text-[13px] text-terra-amber fill-icon">star</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono"><?= $relRating > 0 ? number_format($relRating, 1) : '5.0' ?></span>
                                    <span class="font-mono text-slate-400">(<?= $relRatingCount ?>)</span>
                                </div>

                                <div class="mt-1.5">
                                    <div class="flex items-baseline gap-1 flex-wrap font-mono">
                                        <span class="text-xs sm:text-sm font-black text-terra-green">₱<?= number_format($relPrice, 2) ?></span>
                                        <?php if ($hasDiscount): ?>
                                            <span class="text-[9px] sm:text-[10px] text-slate-400 line-through">₱<?= number_format($relCompare, 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($rel['shop_name'])): ?>
                                        <p class="text-[9px] sm:text-[10px] text-slate-400 truncate mt-0.5"><?= esc($rel['shop_name']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="p-2 pt-0">
                            <a href="<?= base_url('product/' . $rel['id']) ?>" class="w-full block text-center py-1 px-2 bg-slate-100 dark:bg-slate-800 hover:bg-terra-green hover:text-white text-slate-700 dark:text-slate-200 text-[10px] font-bold rounded-lg transition-colors">
                                View
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
(function () {
    var mainImage = document.getElementById('main-product-image');
    var thumbs    = document.querySelectorAll('.gallery-thumb');
    var display   = document.getElementById('quantity-display');
    var input     = document.getElementById('quantity-input');
    var minus     = document.getElementById('qty-minus');
    var plus      = document.getElementById('qty-plus');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (mainImage) {
                mainImage.src = this.getAttribute('data-image');
            }
            thumbs.forEach(function (t) {
                t.classList.remove('border-2', 'border-terra-green', 'ring-2', 'ring-terra-green/20');
                t.classList.add('border', 'border-slate-200', 'dark:border-slate-700');
            });
            this.classList.add('border-2', 'border-terra-green', 'ring-2', 'ring-terra-green/20');
            this.classList.remove('border-slate-200', 'dark:border-slate-700');
        });
    });

    if (display && input) {
        var maxQty = parseInt(input.getAttribute('max'), 10) || 99;

        function update(n) {
            n = Math.max(1, Math.min(maxQty, n));
            input.value = n;
            display.textContent = n;
        }

        if (minus) {
            minus.addEventListener('click', function () {
                update(parseInt(display.textContent, 10) - 1);
            });
        }
        if (plus) {
            plus.addEventListener('click', function () {
                update(parseInt(display.textContent, 10) + 1);
            });
        }
    }

    // Variant selection logic
    var hasVariants = <?= !empty($groupedVariants) ? 'true' : 'false' ?>;
    var variantPills = document.querySelectorAll('.variant-pill');
    var variantInput = document.getElementById('selected-variant-id');
    var priceDisplay = document.querySelector('.text-3xl.sm\\:text-4xl.font-black.text-terra-green');
    var defaultPriceText = priceDisplay ? priceDisplay.textContent : '';
    var cartBtn = document.getElementById('add-to-cart-btn');
    var cartBtnText = document.getElementById('cart-btn-text');
    var buyBtn = document.getElementById('buy-now-btn');
    var variantStockText = document.getElementById('variant-stock-text');
    var purchaseForm = document.getElementById('product-purchase-form');

    variantPills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            if (this.hasAttribute('disabled')) return;

            var group = this.getAttribute('data-group');
            var groupSlug = this.getAttribute('data-group-slug');
            var val = this.getAttribute('data-value');
            var stock = parseInt(this.getAttribute('data-stock'), 10);
            var price = this.getAttribute('data-price');
            var id = this.getAttribute('data-id');

            // Toggle active styling among sibling pills in same group
            document.querySelectorAll('.variant-pill[data-group="' + group + '"]').forEach(function (sibling) {
                sibling.classList.remove('ring-2', 'ring-terra-green/30', 'border-terra-green', 'bg-terra-green/10', 'text-terra-green');
                sibling.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-white', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-slate-200');
            });

            this.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-white', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-slate-200');
            this.classList.add('ring-2', 'ring-terra-green/30', 'border-terra-green', 'bg-terra-green/10', 'text-terra-green');

            // Update selected value text
            var labelSpan = document.getElementById('selected-val-' + groupSlug);
            if (labelSpan) {
                labelSpan.textContent = val;
            }

            // Set hidden variant_id
            if (variantInput) {
                variantInput.value = id;
            }

            // Update price if overridden
            if (priceDisplay) {
                if (price && parseFloat(price) > 0) {
                    priceDisplay.textContent = '₱' + parseFloat(price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else {
                    priceDisplay.textContent = defaultPriceText;
                }
            }

            // Update stock availability and quantity bounds
            if (stock > 0) {
                if (variantStockText) {
                    variantStockText.textContent = val + ' — ' + stock + ' in stock';
                    variantStockText.className = 'text-xs font-semibold text-terra-green';
                }
                if (cartBtn) {
                    cartBtn.removeAttribute('disabled');
                    if (cartBtnText) {
                        cartBtnText.textContent = 'Add to Cart';
                    }
                }
                if (buyBtn && buyBtn.getAttribute('data-shop-closed') !== '1') {
                    buyBtn.removeAttribute('disabled');
                }
                if (input) {
                    input.setAttribute('max', stock);
                    maxQty = stock;
                    if (parseInt(input.value, 10) > stock) {
                        update(stock);
                    }
                }
            } else {
                if (variantStockText) {
                    variantStockText.textContent = val + ' is currently out of stock';
                    variantStockText.className = 'text-xs font-semibold text-rose-500';
                }
                if (cartBtn) {
                    cartBtn.setAttribute('disabled', 'disabled');
                    if (cartBtnText) {
                        cartBtnText.textContent = 'Out of Stock';
                    }
                }
                if (buyBtn) {
                    buyBtn.setAttribute('disabled', 'disabled');
                }
            }
        });
    });

    if (hasVariants && (window.location.search.indexOf('select_option=1') !== -1 || window.location.hash === '#variants-section')) {
        var vsEl = document.getElementById('variants-section');
        if (vsEl) {
            setTimeout(function () {
                vsEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 150);
        }
    }

    // Direct Buy Now Navigation Handler
    if (buyBtn) {
        buyBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (this.hasAttribute('disabled')) return;

            if (hasVariants && (!variantInput || !variantInput.value)) {
                if (variantStockText) {
                    variantStockText.textContent = 'Please select an option to continue.';
                    variantStockText.className = 'text-xs font-bold text-rose-500';
                }
                var variantsSection = document.getElementById('variants-section');
                if (variantsSection) {
                    variantsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    variantsSection.classList.add('ring-2', 'ring-rose-500');
                    setTimeout(function () {
                        variantsSection.classList.remove('ring-2', 'ring-rose-500');
                    }, 2500);
                }
                return;
            }

            var productId = '<?= (int) $product['id'] ?>';
            var qtyInput = document.getElementById('quantity-input');
            var qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
            var variantId = variantInput ? variantInput.value : '';

            var targetUrl = '<?= base_url('buy-now') ?>?product_id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(qty);
            if (variantId) {
                targetUrl += '&variant_id=' + encodeURIComponent(variantId);
            }
            window.location.href = targetUrl;
        });
    }

    // Client-side submit guard: require variant selection before submitting Add to Cart
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', function (e) {
            if (hasVariants && (!variantInput || !variantInput.value)) {
                e.preventDefault();
                if (variantStockText) {
                    variantStockText.textContent = 'Please select an option to continue.';
                    variantStockText.className = 'text-xs font-bold text-rose-500';
                }
                var variantsSection = document.getElementById('variants-section');
                if (variantsSection) {
                    variantsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    variantsSection.classList.add('ring-2', 'ring-rose-500');
                    setTimeout(function () {
                        variantsSection.classList.remove('ring-2', 'ring-rose-500');
                    }, 2500);
                }
            }
        });
    }

    // Star rating interaction
    var starLabels = document.querySelectorAll('.star-rating label');
    var starInputs = document.querySelectorAll('.star-rating input[type="radio"]');
    starLabels.forEach(function (label) {
        label.addEventListener('mouseenter', function () {
            var star = parseInt(this.getAttribute('data-star'), 10);
            starLabels.forEach(function (lbl) {
                var s = parseInt(lbl.getAttribute('data-star'), 10);
                if (s <= star) {
                    lbl.classList.add('text-terra-amber', 'fill-icon');
                    lbl.classList.remove('text-slate-300', 'dark:text-slate-600');
                } else {
                    lbl.classList.remove('text-terra-amber', 'fill-icon');
                    lbl.classList.add('text-slate-300', 'dark:text-slate-600');
                }
            });
        });
        label.addEventListener('mouseleave', function () {
            var checked = document.querySelector('.star-rating input[type="radio"]:checked');
            var checkedStar = checked ? parseInt(checked.value, 10) : 0;
            starLabels.forEach(function (lbl) {
                var s = parseInt(lbl.getAttribute('data-star'), 10);
                if (s <= checkedStar) {
                    lbl.classList.add('text-terra-amber', 'fill-icon');
                    lbl.classList.remove('text-slate-300', 'dark:text-slate-600');
                } else {
                    lbl.classList.remove('text-terra-amber', 'fill-icon');
                    lbl.classList.add('text-slate-300', 'dark:text-slate-600');
                }
            });
        });
    });
    starInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            var checkedStar = parseInt(this.value, 10);
            starLabels.forEach(function (lbl) {
                var s = parseInt(lbl.getAttribute('data-star'), 10);
                if (s <= checkedStar) {
                    lbl.classList.add('text-terra-amber', 'fill-icon');
                    lbl.classList.remove('text-slate-300', 'dark:text-slate-600');
                } else {
                    lbl.classList.remove('text-terra-amber', 'fill-icon');
                    lbl.classList.add('text-slate-300', 'dark:text-slate-600');
                }
            });
        });
    });
})();
</script>

<?= $this->endSection() ?>