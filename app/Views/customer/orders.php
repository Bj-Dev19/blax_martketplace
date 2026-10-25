<?= $this->extend('layouts/marketplace') ?>
<?= $this->section('content') ?>

<div class="flex-1 flex flex-col md:flex-row items-start w-full min-h-[calc(100vh-72px)] bg-slate-50/50 dark:bg-slate-950">

    <?= view('components/profile_sidebar', ['activeNav' => 'orders']) ?>

    <!-- Main Content Area -->
    <main class="flex-1 w-full min-w-0 p-4 sm:p-6 md:p-8">

        <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 pb-28">

            <!-- Calm Page Header & Filter Tabs -->
            <header class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-display font-bold text-slate-900 dark:text-white tracking-tight">My Orders</h1>
                        <?php if (!empty($orders)): ?>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                <?= count($orders) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 hover:border-terra-green/50 hover:text-terra-green transition-colors shadow-2xs">
                        <span class="material-symbols-outlined text-[16px]">storefront</span>
                        <span>Browse Shop</span>
                    </a>
                </div>

                <?php if (!empty($orders)): ?>
                    <?php
                        $countAll = count($orders);
                        $countActive = 0;
                        $countCompleted = 0;
                        $countCancelled = 0;
                        foreach ($orders as $o) {
                            $st = strtolower((string) ($o['status'] ?? 'pending'));
                            if ($st === 'cancelled') {
                                $countCancelled++;
                            } elseif (in_array($st, ['delivered', 'completed'], true)) {
                                $countCompleted++;
                            } else {
                                $countActive++;
                            }
                        }
                    ?>
                    <!-- Filter Tabs -->
                    <div class="flex border-b border-slate-200/80 dark:border-slate-800 gap-2 sm:gap-4 overflow-x-auto no-scrollbar pb-px">
                        <button type="button" class="px-3.5 py-2.5 font-bold text-xs sm:text-sm text-terra-green border-b-2 border-terra-green whitespace-nowrap flex items-center gap-1.5 transition-all cursor-pointer" data-filter="all">
                            <span>All</span>
                            <span class="px-2 py-0.5 bg-terra-green/10 text-terra-green rounded-full text-[10px] font-mono font-bold"><?= $countAll ?></span>
                        </button>
                        <button type="button" class="px-3.5 py-2.5 font-bold text-xs sm:text-sm text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-colors whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="active">
                            <span>Active</span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-mono font-bold"><?= $countActive ?></span>
                        </button>
                        <button type="button" class="px-3.5 py-2.5 font-bold text-xs sm:text-sm text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-colors whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="completed">
                            <span>Completed</span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-mono font-bold"><?= $countCompleted ?></span>
                        </button>
                        <button type="button" class="px-3.5 py-2.5 font-bold text-xs sm:text-sm text-slate-500 dark:text-slate-400 hover:text-terra-green dark:hover:text-emerald-400 transition-colors whitespace-nowrap flex items-center gap-1.5 cursor-pointer" data-filter="cancelled">
                            <span>Cancelled</span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-mono font-bold"><?= $countCancelled ?></span>
                        </button>
                    </div>
                <?php endif; ?>
            </header>

            <?php if (!empty($orders)): ?>
                <!-- Orders List -->
                <div class="flex flex-col gap-4 sm:gap-5">
                    <?php
                        $statusIcons = [
                            'pending'          => 'schedule',
                            'processing'       => 'pending',
                            'shipped'          => 'local_shipping',
                            'in_transit'       => 'local_shipping',
                            'ready_for_pickup' => 'storefront',
                            'delivered'        => 'task_alt',
                            'completed'        => 'task_alt',
                            'cancelled'        => 'cancel',
                        ];
                        $statusPills = [
                            'pending'          => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-300/40',
                            'processing'       => 'bg-terra-green/10 text-terra-green dark:text-emerald-400 border-terra-green/30',
                            'shipped'          => 'bg-sky-500/10 text-sky-700 dark:text-sky-300 border-sky-500/30',
                            'in_transit'       => 'bg-sky-500/10 text-sky-700 dark:text-sky-300 border-sky-500/30',
                            'ready_for_pickup' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-300/40',
                            'delivered'        => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
                            'completed'        => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
                            'cancelled'        => 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30',
                        ];
                        $orderGroup = [
                            'pending'          => 'active',
                            'processing'       => 'active',
                            'shipped'          => 'active',
                            'in_transit'       => 'active',
                            'ready_for_pickup' => 'active',
                            'delivered'        => 'completed',
                            'completed'        => 'completed',
                            'cancelled'        => 'cancelled',
                        ];
                    ?>

                    <?php foreach ($orders as $order): ?>
                        <?php
                            $orderStatus = strtolower((string) ($order['status'] ?? 'pending'));
                            $statusIcon  = $statusIcons[$orderStatus] ?? 'receipt_long';
                            $statusPill  = $statusPills[$orderStatus] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
                            $group       = $orderGroup[$orderStatus] ?? 'active';
                            $orderedOn   = date('M d, Y', strtotime($order['placed_at'] ?? $order['created_at'] ?? 'now'));
                            $isPickup    = (($order['fulfillment_method'] ?? 'delivery') === 'pickup');
                            $items       = $order['items'] ?? [];
                            $totalItems  = count($items);
                            $shopName    = !empty($order['shop_name']) ? $order['shop_name'] : 'Merchant Store';
                            $shopLogo    = !empty($order['shop_logo']) ? $order['shop_logo'] : null;
                            $shopUrl     = !empty($order['shop_slug'])
                                ? base_url('shop/' . esc($order['shop_slug']))
                                : (!empty($order['shop_id']) ? base_url('shop/' . (int) $order['shop_id']) : base_url('shop/' . url_title($shopName, '-', true)));
                        ?>

                        <!-- Store Order Card -->
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-2xs transition-all" data-group="<?= esc($group) ?>" data-status="<?= esc($orderStatus) ?>">
                            
                            <!-- 1. Store Header Bar -->
                            <div class="px-4 py-3.5 sm:px-5 bg-slate-50/60 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                                
                                <!-- Shop Branding & Order Ref -->
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-terra-green/10 text-terra-green flex items-center justify-center font-bold text-sm overflow-hidden border border-terra-green/20 shrink-0">
                                        <?php if (!empty($shopLogo)): ?>
                                            <img src="<?= esc(product_image_url($shopLogo, 'thumbnail')) ?>" alt="<?= esc($shopName) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="<?= $shopUrl ?>" class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white hover:text-terra-green transition-colors truncate inline-flex items-center gap-0.5">
                                            <span class="truncate"><?= esc($shopName) ?></span>
                                            <span class="material-symbols-outlined text-[15px] text-slate-400 shrink-0">chevron_right</span>
                                        </a>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                            <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">#<?= esc($order['order_number'] ?? ('ORD-' . $order['id'])) ?></span>
                                            <span>&bull;</span>
                                            <span><?= esc($orderedOn) ?></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Status & Fulfillment Pills -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        <span class="material-symbols-outlined text-[13px]"><?= $isPickup ? 'storefront' : 'local_shipping' ?></span>
                                        <span><?= $isPickup ? 'Pick-up' : 'Delivery' ?></span>
                                    </span>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= esc($statusPill) ?>">
                                        <span class="material-symbols-outlined text-[13px]"><?= $statusIcon ?></span>
                                        <span><?= humanize_status($orderStatus) ?></span>
                                    </span>
                                </div>

                            </div>

                            <!-- 2. Order Body: Stepper & Products -->
                            <div class="p-4 sm:p-5 space-y-4">

                                <!-- Order Progress Stepper (Hidden on Cancelled) -->
                                <?php if ($orderStatus === 'cancelled'): ?>
                                    <div class="px-3.5 py-2.5 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center gap-2 text-rose-600 dark:text-rose-400 text-xs font-medium">
                                        <span class="material-symbols-outlined text-[16px] shrink-0">cancel</span>
                                        <span>Order cancelled</span>
                                    </div>
                                <?php else: ?>
                                    <?php
                                        $currentStep = match($orderStatus) {
                                            'pending'                                   => 1,
                                            'processing'                                => 2,
                                            'shipped', 'in_transit', 'ready_for_pickup' => 3,
                                            'delivered', 'completed'                    => 4,
                                            default                                     => 1,
                                        };
                                        $steps = [
                                            1 => ['label' => 'Placed'],
                                            2 => ['label' => 'Processing'],
                                            3 => ['label' => $isPickup ? 'Pickup Ready' : 'On the Way'],
                                            4 => ['label' => 'Completed'],
                                        ];
                                    ?>
                                    <div class="pt-1 pb-0.5">
                                        <div class="grid grid-cols-4 w-full">
                                            <?php foreach ($steps as $stepNum => $stepData):
                                                $isCompleted = ($stepNum < $currentStep) || ($orderStatus === 'delivered' || $orderStatus === 'completed');
                                                $isCurrent   = ($stepNum === $currentStep) && !($orderStatus === 'delivered' || $orderStatus === 'completed');
                                            ?>
                                                <div class="relative flex flex-col items-center">
                                                    <?php if ($stepNum > 1): ?>
                                                        <!-- Connector Line mathematically centered at top-3.5 (14px = half of 28px circle) -->
                                                        <div class="absolute top-3.5 -left-1/2 w-full h-0.5 transition-colors duration-300 <?= ($isCompleted || $isCurrent) ? 'bg-terra-green' : 'bg-slate-200 dark:bg-slate-700' ?>"></div>
                                                    <?php endif; ?>

                                                    <!-- Step Circle -->
                                                    <div class="relative z-10">
                                                        <?php if ($isCompleted): ?>
                                                            <div class="w-7 h-7 rounded-full bg-terra-green text-white flex items-center justify-center shadow-2xs">
                                                                <span class="material-symbols-outlined text-[15px]">check</span>
                                                            </div>
                                                        <?php elseif ($isCurrent): ?>
                                                            <div class="relative w-7 h-7 rounded-full bg-terra-green text-white flex items-center justify-center shadow-2xs ring-4 ring-terra-green/15">
                                                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <span class="mt-1.5 text-[10px] sm:text-[11px] font-semibold <?= ($isCompleted || $isCurrent) ? 'text-terra-green dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?> text-center leading-tight px-0.5">
                                                        <?= $stepData['label'] ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Products List Inside This Store Order -->
                                <div class="divide-y divide-slate-100 dark:divide-slate-800/80 border-t border-slate-100 dark:border-slate-800/80 pt-3">
                                    <?php foreach ($items as $item): ?>
                                        <?php
                                            $itemImg = product_image_url($item['image_url'] ?? '', 'thumbnail');
                                            $pName = !empty($item['product_name']) ? $item['product_name'] : 'Product Item';
                                            $unitPrice = (float) ($item['unit_price'] ?? 0);
                                            $qty = (int) ($item['quantity'] ?? 1);
                                            $lineTotal = (float) ($item['line_total'] ?? ($unitPrice * $qty));
                                        ?>
                                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-center gap-3">
                                            <!-- Thumbnail -->
                                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-200/70 dark:border-slate-700 flex items-center justify-center shrink-0 aspect-square">
                                                <?php if (!empty($itemImg)): ?>
                                                    <img class="w-full h-full object-cover" src="<?= esc($itemImg) ?>" alt="<?= esc($pName) ?>" loading="lazy">
                                                <?php else: ?>
                                                    <span class="material-symbols-outlined text-slate-400 text-xl">inventory_2</span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate">
                                                    <?= esc($pName) ?>
                                                </h4>
                                                <div class="flex items-center gap-2 flex-wrap mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                                    <?php if (!empty($item['variant_label'])): ?>
                                                        <span class="text-[10px] font-semibold text-terra-green bg-terra-green/10 px-1.5 py-0.5 rounded">
                                                            <?= esc($item['variant_label']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span>Qty: <strong class="text-slate-700 dark:text-slate-300 font-semibold"><?= $qty ?></strong></span>
                                                </div>
                                            </div>

                                            <!-- Line Subtotal -->
                                            <div class="text-right shrink-0">
                                                <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white font-mono">₱<?= number_format($lineTotal, 2) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>

                            <!-- 3. Order Footer: Total & Clean Action Buttons (Zero passive notice pills) -->
                            <div class="px-4 py-3.5 sm:px-5 bg-slate-50/40 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                                
                                <!-- Total Amount -->
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        Total (<?= $totalItems ?> <?= $totalItems === 1 ? 'item' : 'items' ?>):
                                    </span>
                                    <span class="text-base sm:text-lg font-extrabold text-terra-green dark:text-emerald-400 font-mono">
                                        ₱<?= number_format((float) $order['total_amount'], 2) ?>
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 flex-wrap justify-end">

                                    <?php if ($orderStatus === 'delivered' || $orderStatus === 'completed'): ?>
                                        <button type="button" 
                                                class="rate-order-btn inline-flex items-center gap-1 px-3.5 py-1.5 bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-300/40 hover:bg-amber-500/25 rounded-xl text-xs font-bold transition-all active:scale-95 cursor-pointer"
                                                data-order='<?= esc(json_encode($order), 'attr') ?>'>
                                            <span class="material-symbols-outlined text-[15px]">star</span>
                                            <span>Rate</span>
                                        </button>

                                        <a href="<?= $shopUrl ?>" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-all active:scale-95">
                                            <span>Buy Again</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($isPickup && $orderStatus === 'ready_for_pickup'): ?>
                                        <?php 
                                            $shopLoc = trim(($order['shop_address'] ?? '') . ' ' . ($order['shop_city'] ?? ''));
                                            if ($shopLoc === '') $shopLoc = 'Poblacion, Polomolok';
                                        ?>
                                        <button type="button" 
                                                class="order-qr-btn inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs font-bold transition-all shadow-2xs active:scale-95 cursor-pointer"
                                                data-number="<?= esc($order['order_number'] ?? ('ORD-' . $order['id'])) ?>"
                                                data-shop="<?= esc($shopName) ?>"
                                                data-location="<?= esc($shopLoc) ?>"
                                                data-status="<?= esc(humanize_status($orderStatus)) ?>"
                                                data-date="<?= esc($orderedOn) ?>"
                                                data-paid="<?= ($order['payment_status'] ?? '') === 'paid' ? 'PAID' : 'UNPAID' ?>">
                                            <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                                            <span>Pick-up QR</span>
                                        </button>
                                    <?php endif; ?>

                                    <?php if (!$isPickup && in_array($orderStatus, ['shipped', 'in_transit'], true)): ?>
                                        <a href="<?= base_url('customer/orders/track/' . esc($order['order_number'] ?? $order['id'])) ?>" 
                                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs font-bold transition-all shadow-2xs active:scale-95">
                                            <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                            <span>Track Order</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (in_array($orderStatus, ['pending', 'processing'], true)): ?>
                                        <button type="button" 
                                                class="cancel-order-btn inline-flex items-center gap-1 px-3 py-1.5 border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl text-xs font-semibold transition-all active:scale-95 cursor-pointer"
                                                data-id="<?= (int) $order['id'] ?>"
                                                data-number="<?= esc($order['order_number'] ?? ('ORD-' . $order['id'])) ?>">
                                            <span>Cancel</span>
                                        </button>
                                    <?php endif; ?>

                                    <button type="button" class="order-details-btn inline-flex items-center gap-1 px-3.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-terra-green/50 rounded-xl text-xs font-semibold transition-all active:scale-95 cursor-pointer" data-order='<?= esc(json_encode($order), 'attr') ?>'>
                                        <span>Details</span>
                                    </button>

                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- Empty Orders State -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 sm:p-10 text-center border border-slate-200/80 dark:border-slate-800 space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-terra-green/10 text-terra-green mx-auto flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">inventory_2</span>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">No orders yet</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xs mx-auto">Your marketplace orders will appear here once placed.</p>
                    </div>
                    <div class="pt-1">
                        <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 bg-terra-green hover:bg-[#3d7a33] text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-xs transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">storefront</span>
                            <span>Browse Marketplace</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </main>

</div>

<!-- Order Details Modal -->
<div id="order-details-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="order-details-overlay"></div>
    <div class="relative bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl w-full max-w-lg p-5 sm:p-7 flex flex-col gap-4 z-10 max-h-[90vh] overflow-y-auto animate-scale-up">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Order Details</span>
                <h3 class="text-base sm:text-lg font-bold text-terra-green font-mono" id="od-number">#ORD-00000</h3>
            </div>
            <button type="button" id="od-close" class="p-1.5 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-2.5 text-xs bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 block">Merchant Store</span>
                <span id="od-shop" class="font-bold text-slate-900 dark:text-white"></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 block">Date Placed</span>
                <span id="od-date" class="text-slate-800 dark:text-slate-200 font-medium"></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 block">Fulfillment</span>
                <span id="od-fulfillment" class="text-slate-800 dark:text-slate-200 font-medium capitalize"></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 block">Payment</span>
                <span id="od-payment" class="font-bold uppercase text-terra-green font-mono"></span>
            </div>
        </div>

        <div>
            <h4 class="text-[11px] uppercase font-extrabold text-slate-400 dark:text-slate-500 tracking-wider mb-2">Purchased Items</h4>
            <div id="od-items-list" class="space-y-2 divide-y divide-slate-100 dark:divide-slate-800"></div>
        </div>

        <div class="border-t border-slate-100 dark:border-slate-800 pt-3 flex justify-between items-center text-sm">
            <span class="font-bold text-slate-900 dark:text-white">Grand Total</span>
            <span id="od-total" class="font-black text-terra-green text-lg sm:text-xl font-mono">₱0.00</span>
        </div>

        <button type="button" id="od-done" class="w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all cursor-pointer">Close</button>
    </div>
</div>

<!-- Order Pick-up QR Modal -->
<div id="order-qr-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="order-qr-overlay"></div>
    <div class="relative bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl w-full max-w-sm p-6 flex flex-col items-center gap-4 text-center z-10 animate-scale-up">
        <div class="flex justify-between items-center w-full border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-1.5 text-terra-green font-bold text-sm">
                <span class="material-symbols-outlined text-xl">qr_code_2</span>
                <span>Store Pick-up Pass</span>
            </div>
            <button type="button" id="order-qr-close" class="p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        
        <div class="flex flex-col items-center justify-center bg-white p-5 rounded-2xl shadow-inner border border-slate-100 w-full max-w-[220px] aspect-square mx-auto">
            <div id="order-qr-canvas" class="flex justify-center items-center w-full h-full"></div>
        </div>
        
        <div class="flex flex-col items-center w-full space-y-1">
            <span class="text-[10px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold">Order Identifier</span>
            <span id="order-qr-number" class="text-base font-mono font-black text-terra-green">#ORD-00000</span>
            
            <div class="flex items-center justify-center gap-1.5 mt-1">
                <span id="order-qr-shop" class="text-xs font-bold text-slate-900 dark:text-white"></span>
                <span class="text-slate-300 dark:text-slate-600">•</span>
                <span id="order-qr-payment" class="text-[10px] font-bold px-2 py-0.5 rounded-full font-mono"></span>
            </div>

            <div id="order-qr-location-box" class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-terra-green">location_on</span>
                <span id="order-qr-location">Poblacion, Polomolok</span>
            </div>

            <p id="order-qr-instructions" class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 leading-relaxed italic px-2">
                Present this QR code to the cashier at <span id="order-qr-inst-shop" class="font-semibold text-slate-900 dark:text-white not-italic">the store</span> to claim your order.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 w-full pt-1">
            <button type="button" id="order-qr-download" class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">download</span>
                <span>Download</span>
            </button>
            <button type="button" id="order-qr-done" class="w-full py-2.5 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">Close</button>
        </div>
    </div>
</div>

<!-- Rate Order / Product & Shop Modal -->
<div id="rate-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="rate-overlay"></div>
    <div class="relative bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl w-full max-w-lg p-5 sm:p-7 flex flex-col gap-4 z-10 max-h-[90vh] overflow-y-auto animate-scale-up">
        <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Customer Feedback</span>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white font-display" id="rate-title">Rate Your Order Experience</h3>
            </div>
            <button type="button" id="rate-close" class="p-1.5 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form id="rate-form" class="space-y-4">
            <input type="hidden" id="rate-shop-id" value="">
            <input type="hidden" id="rate-product-id" value="">
            <input type="hidden" id="rate-order-id" value="">

            <!-- Shop Rating -->
            <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wide">Shop Service: <span id="rate-shop-name" class="text-terra-green font-bold">Store</span></label>
                    <span id="rate-shop-val-text" class="text-xs font-bold text-terra-amber font-mono">5 / 5</span>
                </div>
                <div class="flex items-center gap-1" id="shop-star-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="shop-star text-terra-amber hover:scale-110 transition-transform cursor-pointer" data-val="<?= $i ?>">
                            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="rate-shop-score" value="5">
                <textarea id="rate-shop-comment" rows="2" placeholder="How was the shop's fulfillment speed and service?" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-terra-green focus:ring-1 focus:ring-terra-green"></textarea>
            </div>

            <!-- Product Rating -->
            <div class="bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-2.5" id="product-rating-section">
                <div class="flex justify-between items-center gap-2">
                    <label class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wide truncate">Product Quality: <span id="rate-product-name" class="text-terra-green font-bold">Product</span></label>
                    <span id="rate-prod-val-text" class="text-xs font-bold text-terra-amber font-mono shrink-0">5 / 5</span>
                </div>
                <div id="rate-product-picker-wrap" class="hidden">
                    <select id="rate-product-select" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-terra-green"></select>
                </div>
                <div class="flex items-center gap-1" id="prod-star-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="prod-star text-terra-amber hover:scale-110 transition-transform cursor-pointer" data-val="<?= $i ?>">
                            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        </button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="rate-prod-score" value="5">
                <textarea id="rate-prod-comment" rows="2" placeholder="How was the product quality and packaging?" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-terra-green focus:ring-1 focus:ring-terra-green"></textarea>
            </div>

            <div id="rate-error" class="hidden p-2.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] shrink-0">error</span>
                <span id="rate-error-text">Failed to submit review.</span>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" id="rate-cancel" class="py-2.5 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all cursor-pointer">Cancel</button>
                <button type="submit" id="rate-submit-btn" class="py-2.5 px-6 bg-terra-green hover:bg-[#3d7a33] text-white rounded-xl text-xs font-bold transition-all shadow-md active:scale-95 flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Submit Reviews</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Order Confirmation Modal -->
<div id="cancel-order-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="cancel-order-overlay"></div>
    <div class="relative bento-card bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl w-full max-w-md p-5 sm:p-6 flex flex-col gap-3.5 z-10 animate-scale-up">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white font-display" id="cancel-modal-title">Cancel Order?</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">This action cannot be undone</p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            Are you sure you want to cancel this order? Any reserved stocks will be restored to the merchant immediately.
        </p>

        <div id="cancel-modal-error" class="hidden p-2.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px] shrink-0">error</span>
            <span id="cancel-error-text">Failed to cancel order.</span>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <button type="button" id="cancel-modal-keep" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-all cursor-pointer">Keep Order</button>
            <button type="button" id="cancel-modal-confirm" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-md active:scale-95 flex items-center justify-center gap-1 cursor-pointer">
                <span id="cancel-spinner" class="material-symbols-outlined text-[16px] animate-spin hidden">progress_activity</span>
                <span id="cancel-confirm-text">Yes, Cancel</span>
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<?php if (!empty($orders)): ?>
<script>
(function () {
    // Filter tabs
    var tabs  = document.querySelectorAll('[data-filter]');
    var cards = document.querySelectorAll('[data-group]');

    function applyFilter(filter) {
        cards.forEach(function (card) {
            var show = filter === 'all' || card.getAttribute('data-group') === filter;
            card.classList.toggle('hidden', !show);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) {
                t.classList.remove('text-terra-green', 'border-b-2', 'border-terra-green');
                t.classList.add('text-slate-500', 'dark:text-slate-400');
            });
            tab.classList.remove('text-slate-500', 'dark:text-slate-400');
            tab.classList.add('text-terra-green', 'border-b-2', 'border-terra-green');
            applyFilter(tab.getAttribute('data-filter'));
        });
    });

    // Store Pick-up QR Modal logic
    var qrModal    = document.getElementById('order-qr-modal');
    var qrOverlay  = document.getElementById('order-qr-overlay');
    var qrClose    = document.getElementById('order-qr-close');
    var qrDone     = document.getElementById('order-qr-done');
    var qrCanvas   = document.getElementById('order-qr-canvas');
    var qrNumber   = document.getElementById('order-qr-number');
    var qrShop     = document.getElementById('order-qr-shop');
    var qrPayment  = document.getElementById('order-qr-payment');
    var qrLocation = document.getElementById('order-qr-location');
    var qrInstShop = document.getElementById('order-qr-inst-shop');

    function openQr(orderNum, shopName, isPaid, locationText) {
        if (!qrModal) return;
        qrNumber.textContent = '#' + orderNum;
        qrShop.textContent = shopName || 'Storefront';
        if (qrInstShop) qrInstShop.textContent = shopName || 'the store attendant';
        if (qrLocation) qrLocation.textContent = locationText || 'Poblacion, Polomolok';
        
        if (isPaid === 'PAID') {
            qrPayment.textContent = 'PAID ONLINE';
            qrPayment.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30';
        } else {
            qrPayment.textContent = 'PAY AT COUNTER';
            qrPayment.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30';
        }

        qrCanvas.innerHTML = '';
        if (window.QRCode) {
            new QRCode(qrCanvas, {
                text: orderNum,
                width: 190,
                height: 190,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            qrCanvas.innerHTML = '<p class="text-error text-xs font-mono">#' + orderNum + '</p>';
        }

        qrModal.classList.remove('hidden');
    }

    function closeQr() {
        if (qrModal) qrModal.classList.add('hidden');
    }

    document.querySelectorAll('.order-qr-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            openQr(btn.dataset.number, btn.dataset.shop, btn.dataset.paid, btn.dataset.location);
        });
    });

    if (qrOverlay) qrOverlay.addEventListener('click', closeQr);
    if (qrClose) qrClose.addEventListener('click', closeQr);
    if (qrDone) qrDone.addEventListener('click', closeQr);

    var qrDownloadBtn = document.getElementById('order-qr-download');
    if (qrDownloadBtn) {
        qrDownloadBtn.addEventListener('click', function () {
            var canvas = qrCanvas.querySelector('canvas');
            var img = qrCanvas.querySelector('img');
            var dataUrl = null;
            if (canvas) {
                dataUrl = canvas.toDataURL('image/png');
            } else if (img && img.src) {
                dataUrl = img.src;
            }
            if (dataUrl) {
                var a = document.createElement('a');
                a.href = dataUrl;
                a.download = 'BLAX-Pickup-QR-' + (qrNumber.textContent.replace('#', '') || 'order') + '.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        });
    }

    // Rate Order / Product Modal Logic
    var rateModal      = document.getElementById('rate-modal');
    var rateOverlay    = document.getElementById('rate-overlay');
    var rateClose      = document.getElementById('rate-close');
    var rateCancel     = document.getElementById('rate-cancel');
    var rateForm       = document.getElementById('rate-form');
    var rateTitle      = document.getElementById('rate-title');
    var rateShopName   = document.getElementById('rate-shop-name');
    var rateProdName   = document.getElementById('rate-product-name');
    var rateShopId     = document.getElementById('rate-shop-id');
    var rateProdId     = document.getElementById('rate-product-id');
    var rateOrderId    = document.getElementById('rate-order-id');
    var rateShopScore  = document.getElementById('rate-shop-score');
    var rateProdScore  = document.getElementById('rate-prod-score');
    var rateShopComment= document.getElementById('rate-shop-comment');
    var rateProdComment= document.getElementById('rate-prod-comment');
    var rateShopValText= document.getElementById('rate-shop-val-text');
    var rateProdValText= document.getElementById('rate-prod-val-text');
    var rateErr        = document.getElementById('rate-error');
    var rateErrText    = document.getElementById('rate-error-text');

    function closeRateModal() {
        if (rateModal) rateModal.classList.add('hidden');
    }

    if (rateOverlay) rateOverlay.addEventListener('click', closeRateModal);
    if (rateClose) rateClose.addEventListener('click', closeRateModal);
    if (rateCancel) rateCancel.addEventListener('click', closeRateModal);

    function updateStars(containerSelector, score, textEl) {
        var stars = document.querySelectorAll(containerSelector + ' .shop-star, ' + containerSelector + ' .prod-star');
        stars.forEach(function (s) {
            var val = parseInt(s.dataset.val, 10);
            var icon = s.querySelector('.material-symbols-outlined');
            if (icon) {
                if (val <= score) {
                    icon.style.fontVariationSettings = "'FILL' 1";
                    s.classList.remove('opacity-40');
                } else {
                    icon.style.fontVariationSettings = "'FILL' 0";
                    s.classList.add('opacity-40');
                }
            }
        });
        if (textEl) textEl.textContent = score + ' / 5';
    }

    document.querySelectorAll('#shop-star-group .shop-star').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var val = parseInt(this.dataset.val, 10) || 5;
            rateShopScore.value = val;
            updateStars('#shop-star-group', val, rateShopValText);
        });
    });

    document.querySelectorAll('#prod-star-group .prod-star').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var val = parseInt(this.dataset.val, 10) || 5;
            rateProdScore.value = val;
            updateStars('#prod-star-group', val, rateProdValText);
        });
    });

    var rateProdPickerWrap = document.getElementById('rate-product-picker-wrap');
    var rateProdSelect = document.getElementById('rate-product-select');
    if (rateProdSelect) {
        rateProdSelect.addEventListener('change', function () {
            var selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt) {
                rateProdId.value = selectedOpt.value || '';
                rateProdName.textContent = selectedOpt.getAttribute('data-name') || 'Product';
            }
        });
    }

    document.querySelectorAll('.rate-order-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            try {
                var order = JSON.parse(this.getAttribute('data-order'));
                if (!order) return;
                rateOrderId.value = order.id || '';
                rateShopId.value = order.shop_id || '';
                rateShopName.textContent = order.shop_name || 'Store';
                
                var items = Array.isArray(order.items) ? order.items : [];
                var firstItem = items.length > 0 ? items[0] : null;
                if (firstItem) {
                    rateProdId.value = firstItem.product_id || '';
                    rateProdName.textContent = firstItem.product_name || 'Product';
                    document.getElementById('product-rating-section').classList.remove('hidden');

                    if (rateProdPickerWrap && rateProdSelect) {
                        if (items.length > 1) {
                            rateProdSelect.innerHTML = '';
                            items.forEach(function (it) {
                                var opt = document.createElement('option');
                                opt.value = it.product_id || '';
                                opt.textContent = it.product_name || 'Product';
                                opt.setAttribute('data-name', it.product_name || 'Product');
                                rateProdSelect.appendChild(opt);
                            });
                            rateProdPickerWrap.classList.remove('hidden');
                        } else {
                            rateProdPickerWrap.classList.add('hidden');
                        }
                    }
                } else {
                    rateProdId.value = '';
                    document.getElementById('product-rating-section').classList.add('hidden');
                    if (rateProdPickerWrap) rateProdPickerWrap.classList.add('hidden');
                }

                rateShopScore.value = '5';
                rateProdScore.value = '5';
                rateShopComment.value = '';
                rateProdComment.value = '';
                updateStars('#shop-star-group', 5, rateShopValText);
                updateStars('#prod-star-group', 5, rateProdValText);
                if (rateErr) rateErr.classList.add('hidden');

                if (rateModal) rateModal.classList.remove('hidden');
            } catch (err) {
                console.error('Failed to open rate modal:', err);
            }
        });
    });

    if (rateForm) {
        rateForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var csrfToken = (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : '';
            var csrfHeader = (typeof window.getCsrfHeader === 'function') ? window.getCsrfHeader() : 'X-CSRF-TOKEN';
            var submitBtn = document.getElementById('rate-submit-btn');
            if (submitBtn) submitBtn.disabled = true;

            var promises = [];
            if (rateShopId.value) {
                var shopData = new URLSearchParams({
                    shop_id: rateShopId.value,
                    rating: rateShopScore.value,
                    review: rateShopComment.value,
                    order_id: rateOrderId.value
                });
                promises.push(fetch('<?= base_url('customer/reviews/shop') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeader]: csrfToken },
                    body: shopData
                }).then(function(res) { return res.json(); }));
            }

            if (rateProdId.value) {
                var prodData = new URLSearchParams({
                    product_id: rateProdId.value,
                    rating: rateProdScore.value,
                    review: rateProdComment.value,
                    order_id: rateOrderId.value
                });
                promises.push(fetch('<?= base_url('customer/reviews/product') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', [csrfHeader]: csrfToken },
                    body: prodData
                }).then(function(res) { return res.json(); }));
            }

            Promise.all(promises).then(function () {
                if (submitBtn) submitBtn.disabled = false;
                closeRateModal();
                if (typeof showToast === 'function') {
                    showToast('Thank you! Your review has been submitted.', 'success');
                } else {
                    alert('Thank you! Your review has been submitted.');
                }
            }).catch(function () {
                if (submitBtn) submitBtn.disabled = false;
                if (rateErr && rateErrText) {
                    rateErrText.textContent = 'Failed to submit reviews. Please try again.';
                    rateErr.classList.remove('hidden');
                }
            });
        });
    }

    // Cancel Order modal logic
    var cancelModal   = document.getElementById('cancel-order-modal');
    var cancelOverlay = document.getElementById('cancel-order-overlay');
    var cancelKeep    = document.getElementById('cancel-modal-keep');
    var cancelConfirm = document.getElementById('cancel-modal-confirm');
    var cancelTitle   = document.getElementById('cancel-modal-title');
    var cancelErr     = document.getElementById('cancel-modal-error');
    var cancelErrText = document.getElementById('cancel-error-text');
    var cancelSpinner = document.getElementById('cancel-spinner');
    var cancelText    = document.getElementById('cancel-confirm-text');

    var currentCancelId = null;

    function openCancelModal(orderId, orderNumber) {
        currentCancelId = orderId;
        if (cancelTitle) cancelTitle.textContent = 'Cancel Order #' + orderNumber + '?';
        if (cancelErr) cancelErr.classList.add('hidden');
        if (cancelModal) cancelModal.classList.remove('hidden');
    }

    function closeCancelModal() {
        if (cancelModal) cancelModal.classList.add('hidden');
        currentCancelId = null;
    }

    if (cancelOverlay) cancelOverlay.addEventListener('click', closeCancelModal);
    if (cancelKeep) cancelKeep.addEventListener('click', closeCancelModal);

    document.querySelectorAll('.cancel-order-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            openCancelModal(this.getAttribute('data-id'), this.getAttribute('data-number'));
        });
    });

    if (cancelConfirm) {
        cancelConfirm.addEventListener('click', function() {
            if (!currentCancelId) return;

            cancelSpinner.classList.remove('hidden');
            cancelText.textContent = 'Cancelling...';
            cancelConfirm.disabled = true;

            var csrfToken = (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : '';
            var csrfHeader = (typeof window.getCsrfHeader === 'function') ? window.getCsrfHeader() : 'X-CSRF-TOKEN';

            fetch('<?= base_url('customer/orders') ?>/' + currentCancelId + '/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfToken
                },
                body: new URLSearchParams({
                    'order_id': currentCancelId
                })
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function(result) {
                cancelSpinner.classList.add('hidden');
                cancelText.textContent = 'Yes, Cancel';
                cancelConfirm.disabled = false;

                if (result.ok && result.data && result.data.success) {
                    closeCancelModal();
                    if (typeof showToast === 'function') {
                        showToast(result.data.message || 'Order cancelled successfully.', 'success');
                    }
                    setTimeout(function() {
                        window.location.reload();
                    }, 400);
                } else {
                    var errorMsg = (result.data && result.data.error) ? result.data.error : 'Failed to cancel order.';
                    if (cancelErr && cancelErrText) {
                        cancelErrText.textContent = errorMsg;
                        cancelErr.classList.remove('hidden');
                    }
                }
            })
            .catch(function() {
                cancelSpinner.classList.add('hidden');
                cancelText.textContent = 'Yes, Cancel';
                cancelConfirm.disabled = false;
                if (cancelErr && cancelErrText) {
                    cancelErrText.textContent = 'Network error. Please try again.';
                    cancelErr.classList.remove('hidden');
                }
            });
        });
    }

    // Order Details Modal logic
    var odModal   = document.getElementById('order-details-modal');
    var odOverlay = document.getElementById('order-details-overlay');
    var odClose   = document.getElementById('od-close');
    var odDone    = document.getElementById('od-done');
    var odNumber  = document.getElementById('od-number');
    var odShop    = document.getElementById('od-shop');
    var odDate    = document.getElementById('od-date');
    var odFulfill = document.getElementById('od-fulfillment');
    var odPayment = document.getElementById('od-payment');
    var odItems   = document.getElementById('od-items-list');
    var odTotal   = document.getElementById('od-total');

    function closeOdModal() {
        if (odModal) odModal.classList.add('hidden');
    }

    function escapeHtml(str) {
        if (!str) return '';
        var p = document.createElement('p');
        p.textContent = str;
        return p.innerHTML;
    }

    if (odOverlay) odOverlay.addEventListener('click', closeOdModal);
    if (odClose) odClose.addEventListener('click', closeOdModal);
    if (odDone) odDone.addEventListener('click', closeOdModal);

    document.querySelectorAll('.order-details-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            try {
                var raw = this.getAttribute('data-order');
                if (!raw) return;
                var order = JSON.parse(raw);
                if (odNumber) odNumber.textContent = '#' + (order.order_number || ('ORD-' + order.id));
                if (odShop) odShop.textContent = order.shop_name || 'Merchant Store';
                if (odDate) odDate.textContent = order.placed_at || order.created_at || '—';
                if (odFulfill) odFulfill.textContent = order.fulfillment_method || 'Delivery';
                if (odPayment) odPayment.textContent = (order.payment_method || 'Cash') + ' (' + (order.payment_status || 'Pending') + ')';
                if (odTotal) odTotal.textContent = '₱' + parseFloat(order.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                if (odItems) {
                    odItems.innerHTML = '';
                    (order.items || []).forEach(function (it) {
                        var div = document.createElement('div');
                        div.className = 'pt-2 flex items-center justify-between gap-3 text-xs sm:text-sm';
                        var nameSpan = document.createElement('div');
                        nameSpan.className = 'flex-1 min-w-0';
                        var pName = it.product_name || 'Product Item';
                        var pHtml = '<p class="font-bold text-slate-900 dark:text-white truncate">' + escapeHtml(pName) + '</p>';
                        if (it.variant_label) {
                            pHtml += '<span class="text-[10px] font-semibold text-terra-green bg-terra-green/10 px-1.5 py-0.2 rounded border border-terra-green/20 font-mono">' + escapeHtml(it.variant_label) + '</span>';
                        }
                        nameSpan.innerHTML = pHtml;

                        var qtyPrice = document.createElement('div');
                        qtyPrice.className = 'text-right shrink-0';
                        var qty = parseInt(it.quantity, 10) || 1;
                        var uPrice = parseFloat(it.unit_price || 0);
                        var lineTot = parseFloat(it.line_total || (qty * uPrice));
                        qtyPrice.innerHTML = '<span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">' + qty + ' × ₱' + uPrice.toFixed(2) + '</span><p class="font-bold text-slate-900 dark:text-white font-mono">₱' + lineTot.toFixed(2) + '</p>';

                        div.appendChild(nameSpan);
                        div.appendChild(qtyPrice);
                        odItems.appendChild(div);
                    });
                }

                if (odModal) odModal.classList.remove('hidden');
            } catch (err) {
                console.error('Failed to parse order details:', err);
            }
        });
    });
})();
</script>
<?php endif; ?>

<?= $this->endSection() ?>