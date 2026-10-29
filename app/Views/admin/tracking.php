<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-6">

    <!-- Header Bento Banner -->
    <div class="rounded-3xl bg-surface-container-lowest p-6 border border-outline-variant/30 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-terra-green/10 text-terra-green border border-terra-green/20">
                    Logistics Telemetry
                </span>
                <span class="text-xs text-on-surface-variant font-mono">
                    Polomolok &amp; Tupi Grid
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-on-surface font-display tracking-tight">
                Live Fleet Tracking
            </h1>
            <p class="text-xs text-on-surface-variant mt-1 max-w-xl">
                Real-time bird's-eye oversight of all merchant couriers, in-transit orders, and dispatch routes across Polomolok &amp; Tupi.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <div class="flex items-center gap-2 bg-surface-container-low px-4 py-2.5 rounded-2xl border border-outline-variant/30 shadow-2xs">
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
                <span class="text-xs font-bold text-on-surface">Live Fleet Feed</span>
                <span class="text-outline text-xs">&bull;</span>
                <span id="lastSyncLabel" class="text-xs text-on-surface-variant font-mono">Syncing...</span>
            </div>
            <button type="button" onclick="refreshFleetPins()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-surface-container-low hover:bg-surface-container border border-outline-variant/30 text-xs font-bold text-on-surface transition-all active:scale-95" title="Force sync fleet telemetry">
                <span class="material-symbols-outlined text-[16px]">sync</span>
                <span>Sync</span>
            </button>
        </div>
    </div>

    <!-- Operational KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Active Couriers -->
        <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-xs hover:border-terra-green/30 transition-all flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-2xl bg-terra-green/10 text-terra-green flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">two_wheeler</span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-terra-green bg-terra-green/10 px-2.5 py-0.5 rounded-full border border-terra-green/20">Fleet</span>
            </div>
            <div class="mt-2">
                <h3 class="text-[11px] text-on-surface-variant font-bold uppercase tracking-wider">Active Couriers</h3>
                <p id="kpi-total-active" class="text-3xl font-black text-on-surface mt-1 font-display font-mono tracking-tight"><?= number_format((int) ($kpis['total_active'] ?? count($pins ?? []))) ?></p>
            </div>
        </div>

        <!-- KPI 2: In Transit -->
        <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-xs hover:border-purple-500/30 transition-all flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">navigation</span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 dark:text-purple-300 bg-purple-500/10 px-2.5 py-0.5 rounded-full border border-purple-500/20">Moving</span>
            </div>
            <div class="mt-2">
                <h3 class="text-[11px] text-on-surface-variant font-bold uppercase tracking-wider">In Transit</h3>
                <p id="kpi-in-transit" class="text-3xl font-black text-purple-600 dark:text-purple-400 mt-1 font-display font-mono tracking-tight"><?= number_format((int) ($kpis['in_transit'] ?? 0)) ?></p>
            </div>
        </div>

        <!-- KPI 3: Shipped -->
        <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-xs hover:border-blue-500/30 transition-all flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 dark:text-blue-300 bg-blue-500/10 px-2.5 py-0.5 rounded-full border border-blue-500/20">Dispatched</span>
            </div>
            <div class="mt-2">
                <h3 class="text-[11px] text-on-surface-variant font-bold uppercase tracking-wider">Shipped</h3>
                <p id="kpi-shipped" class="text-3xl font-black text-blue-600 dark:text-blue-400 mt-1 font-display font-mono tracking-tight"><?= number_format((int) ($kpis['shipped'] ?? 0)) ?></p>
            </div>
        </div>

        <!-- KPI 4: Active Stores -->
        <div class="bg-surface-container-lowest p-5 rounded-3xl border border-outline-variant/30 shadow-xs hover:border-terra-amber/30 transition-all flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-2xl bg-terra-amber/15 text-terra-amber flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                </div>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-300 bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">Hubs</span>
            </div>
            <div class="mt-2">
                <h3 class="text-[11px] text-on-surface-variant font-bold uppercase tracking-wider">Active Stores</h3>
                <p id="kpi-active-shops" class="text-3xl font-black text-terra-amber mt-1 font-display font-mono tracking-tight"><?= number_format((int) ($kpis['active_shops'] ?? count($groupedShops ?? []))) ?></p>
            </div>
        </div>
    </div>

    <!-- Live Fleet Map Container -->
    <div class="relative rounded-3xl overflow-hidden border border-outline-variant/30 bg-surface-container shadow-xs" style="min-height: 540px;">
        <div id="fleet-map" class="w-full h-full" style="min-height: 540px;"></div>

        <!-- Floating Satellite / Roadmap View Toggle Button (Top Right) -->
        <div class="absolute top-4 right-14 z-10 pointer-events-auto">
            <button type="button" 
                    id="btn-admin-map-type" 
                    onclick="toggleAdminMapType()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface-container-lowest/90 backdrop-blur-md border border-outline-variant/40 hover:bg-surface-container-high text-xs font-bold text-on-surface shadow-md transition-all active:scale-95 cursor-pointer">
                <span id="adminMapTypeIcon" class="material-symbols-outlined text-[18px] text-terra-green">satellite_alt</span>
                <span id="adminMapTypeText">Satellite</span>
            </button>
        </div>

        <!-- Live Fleet Legend Overlay Card (Bottom Left) -->
        <div class="absolute bottom-4 left-4 z-10 bg-surface-container-lowest/90 backdrop-blur-md p-4 rounded-2xl border border-outline-variant/30 shadow-md flex flex-col gap-2.5 max-w-xs text-xs pointer-events-auto">
            <div class="flex items-center gap-2 border-b border-outline-variant/20 pb-2">
                <span class="material-symbols-outlined text-terra-green text-[18px]">map</span>
                <span class="font-bold text-on-surface font-display">Fleet Map Legend</span>
            </div>
            <div class="flex flex-col gap-2 text-on-surface-variant">
                <div class="flex items-center gap-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-purple-600 border border-white shadow-2xs shrink-0"></span>
                    <span class="font-medium text-on-surface text-[11px]">In Transit (Moving Courier)</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-blue-600 border border-white shadow-2xs shrink-0"></span>
                    <span class="font-medium text-on-surface text-[11px]">Shipped (Dispatched Order)</span>
                </div>
                <div class="flex items-center gap-2.5 pt-0.5 border-t border-outline-variant/15">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                    <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-bold">Active Realtime GPS Feed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Deliveries Grouped by Storefront -->
    <div class="space-y-4">
        <div>
            <h2 class="text-xl font-black text-on-surface font-display">Active Deliveries by Storefront</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">Store owners deliver packages directly to customers across Polomolok &amp; Tupi.</p>
        </div>

        <div id="grouped-shops-container">
            <?php if (!empty($groupedShops)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($groupedShops as $shop): ?>
                        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl shadow-xs overflow-hidden flex flex-col transition-all hover:border-terra-green/30">
                            <!-- Shop Card Header -->
                            <div class="p-5 border-b border-outline-variant/20 bg-surface-container-low/40 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-2xl bg-surface-container-high border border-outline-variant/30 overflow-hidden flex items-center justify-center shrink-0 shadow-2xs relative">
                                        <?php 
                                        $rawShopLogo = trim((string)($shop['shop_logo'] ?? ''));
                                        $resolvedShopLogo = $rawShopLogo !== '' ? logo_url($rawShopLogo) : '';
                                        ?>
                                        <?php if (!empty($resolvedShopLogo)): ?>
                                            <img src="<?= esc($resolvedShopLogo) ?>" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-terra-green text-xl\'>storefront</span>';" />
                                        <?php else: ?>
                                            <span class="material-symbols-outlined text-terra-green text-xl">storefront</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-on-surface truncate group-hover:text-terra-green"><?= esc($shop['shop_name']) ?></h3>
                                        <p class="text-[11px] text-on-surface-variant font-medium">Merchant Partner</p>
                                    </div>
                                </div>
                                <span class="shrink-0 px-3 py-1 bg-terra-green/10 text-terra-green border border-terra-green/20 text-xs font-bold rounded-full font-mono">
                                    <?= count($shop['deliveries']) ?> Active
                                </span>
                            </div>

                            <!-- Deliveries List -->
                            <div class="divide-y divide-outline-variant/10 flex-1">
                                <?php foreach ($shop['deliveries'] as $d): ?>
                                    <div class="p-4 space-y-2 hover:bg-surface-container-low/30 transition-colors">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-mono font-bold text-xs text-terra-green">#<?= esc($d['tracking_id'] ?? ('TRK-' . $d['id'])) ?></span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full <?= $d['status'] === 'in_transit' ? 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20' : 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20' ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $d['status'] === 'in_transit' ? 'bg-purple-600' : 'bg-blue-600' ?>"></span>
                                                <span><?= $d['status'] === 'in_transit' ? 'In Transit' : 'Shipped' ?></span>
                                            </span>
                                        </div>

                                        <div class="text-xs text-on-surface">
                                            <p class="font-semibold text-on-surface">
                                                <?= esc(trim(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? 'Customer Recipient'))) ?>
                                            </p>
                                            <p class="text-[11px] text-on-surface-variant truncate max-w-full mt-0.5">
                                                <?= esc($d['destination_address'] ?? 'Polomolok') ?>
                                            </p>
                                        </div>

                                        <div class="flex items-center justify-between pt-1 text-[11px] text-outline font-mono">
                                            <span>
                                                Updated: <?= !empty($d['location_updated_at']) ? date('g:i A', strtotime($d['location_updated_at'])) : 'Recently' ?>
                                            </span>
                                            <button type="button" 
                                                    onclick="focusDeliveryPin(<?= (int)$d['id'] ?>)" 
                                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-surface-container border border-outline-variant/40 hover:bg-terra-green hover:text-white hover:border-terra-green transition-all shadow-2xs active:scale-95">
                                                <span class="material-symbols-outlined text-[14px]">my_location</span>
                                                <span>Locate</span>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Honest Empty State -->
                <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-14 text-center shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-surface-container mx-auto flex items-center justify-center text-outline mb-3">
                        <span class="material-symbols-outlined text-3xl text-terra-green">two_wheeler</span>
                    </div>
                    <h3 class="text-base font-bold text-on-surface font-display">No Active Deliveries on the Road</h3>
                    <p class="text-xs text-on-surface-variant max-w-md mx-auto mt-1">
                        There are currently no active deliveries in transit across Polomolok &amp; Tupi. Deliveries will appear here automatically in real time when shop owners open "View Live Route &amp; Map".
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function(){
    // Handle Google Maps API authentication / activation failure
    window.gm_authFailure = function() {
        const container = document.getElementById('fleet-map');
        if (container) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full p-6 text-center bg-amber-50/80 dark:bg-amber-950/30 border-2 border-dashed border-amber-300 rounded-2xl">
                    <span class="material-symbols-outlined text-4xl text-amber-600 mb-2">warning</span>
                    <h3 class="font-bold text-amber-900 dark:text-amber-200 text-base">Google Maps API Activation Required</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300/80 max-w-md mt-1">
                        The Google Cloud project (<strong>300493013944</strong>) has not yet activated the <strong>Maps JavaScript API</strong> or the API Key needs permissions.
                    </p>
                    <a href="https://console.cloud.google.com/apis/library/maps-backend.googleapis.com" target="_blank" class="mt-3 inline-flex items-center gap-1 px-3.5 py-2 bg-amber-600 text-white rounded-xl text-xs font-semibold hover:bg-amber-700 transition-colors">
                        <span>Enable Maps JavaScript API</span>
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </a>
                </div>
            `;
        }
    };

    let currentPins = <?= json_encode($pins ?? [], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?> || [];

    const POLO_CENTER = { lat: 6.2735, lng: 125.0080 };
    const POLO_BOUNDS = {
        north: 6.45,
        south: 6.10,
        east: 125.20,
        west: 124.85
    };

    let mapInstance = null;
    let activeMarkers = new Map(); // id -> { marker, markerElement, infoWindow, pinData }
    const MAP_TYPE_STORAGE_KEY = 'blax_admin_tracking_map_type';
    let currentAdminMapType = localStorage.getItem(MAP_TYPE_STORAGE_KEY) || 'hybrid';

    function updateAdminMapTypeToggleUI() {
        const icon = document.getElementById('adminMapTypeIcon');
        const text = document.getElementById('adminMapTypeText');
        if (!icon || !text) return;
        if (currentAdminMapType === 'hybrid') {
            icon.textContent = 'map';
            text.textContent = 'Roadmap';
        } else {
            icon.textContent = 'satellite_alt';
            text.textContent = 'Satellite';
        }
    }

    window.toggleAdminMapType = function() {
        if (!mapInstance || typeof google === 'undefined' || !google.maps) {
            currentAdminMapType = (currentAdminMapType === 'hybrid') ? 'roadmap' : 'hybrid';
            localStorage.setItem(MAP_TYPE_STORAGE_KEY, currentAdminMapType);
            updateAdminMapTypeToggleUI();
            return;
        }
        if (currentAdminMapType === 'hybrid') {
            currentAdminMapType = 'roadmap';
            mapInstance.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        } else {
            currentAdminMapType = 'hybrid';
            mapInstance.setMapTypeId(google.maps.MapTypeId.HYBRID);
        }
        localStorage.setItem(MAP_TYPE_STORAGE_KEY, currentAdminMapType);
        updateAdminMapTypeToggleUI();
    };

    function calculateBearing(lat1, lon1, lat2, lon2) {
        const toRad = deg => (deg * Math.PI) / 180;
        const toDeg = rad => (rad * 180) / Math.PI;
        const φ1 = toRad(lat1), φ2 = toRad(lat2);
        const Δλ = toRad(lon2 - lon1);
        const y = Math.sin(Δλ) * Math.cos(φ2);
        const x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);
        return (toDeg(Math.atan2(y, x)) + 360) % 360;
    }

    function createMotorcycleMarkerElement(status, isStale) {
        const isTransit = status === 'in_transit';
        let bgColor = isTransit ? '#7c3aed' : '#2563eb';
        let shadowColor = isTransit ? 'rgba(124, 58, 237, 0.4)' : 'rgba(37, 99, 235, 0.4)';
        if (isStale) {
            bgColor = '#d97706'; // Amber badge for stale / signal lost
            shadowColor = 'rgba(217, 119, 6, 0.4)';
        }

        const div = document.createElement('div');
        div.className = 'motorcycle-marker-wrap';
        div.style.position = 'relative';
        div.style.width = '36px';
        div.style.height = '36px';
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.justifyContent = 'center';
        div.style.cursor = 'pointer';

        div.innerHTML = `
            <div style="position:absolute;width:100%;height:100%;border-radius:50%;background:${bgColor};opacity:0.25;animation:pulse 2s infinite;"></div>
            <div class="motorcycle-icon-inner" style="width:32px;height:32px;border-radius:50%;background:${bgColor};border:2px solid #ffffff;box-shadow:0 3px 10px ${shadowColor};display:flex;align-items:center;justify-content:center;color:#ffffff;z-index:2;transition:transform 0.5s ease-out;">
                <span class="material-symbols-outlined" style="font-size:17px;line-height:1;">${isStale ? 'signal_wifi_bad' : 'two_wheeler'}</span>
            </div>
        `;
        return div;
    }

    function animateMarkerTo(entry, targetLat, targetLng) {
        const startLat = parseFloat(entry.pinData.current_lat);
        const startLng = parseFloat(entry.pinData.current_lng);
        const hasMoved = (Math.abs(startLat - targetLat) > 0.00001 || Math.abs(startLng - targetLng) > 0.00001);

        if (hasMoved && entry.markerElement) {
            const bearing = calculateBearing(startLat, startLng, targetLat, targetLng);
            const iconDiv = entry.markerElement.querySelector('.motorcycle-icon-inner');
            if (iconDiv) {
                iconDiv.style.transform = `rotate(${Math.round(bearing)}deg)`;
            }
        }

        if (!hasMoved) {
            if (entry.marker.position && typeof entry.marker.position.lat === 'function') {
                entry.marker.setPosition(new google.maps.LatLng(targetLat, targetLng));
            } else {
                entry.marker.position = { lat: targetLat, lng: targetLng };
            }
            return;
        }

        const duration = 1200; // ms
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out quad
            const ease = 1 - (1 - progress) * (1 - progress);

            const curLat = startLat + (targetLat - startLat) * ease;
            const curLng = startLng + (targetLng - startLng) * ease;
            const curPos = { lat: curLat, lng: curLng };

            if (entry.marker.position && typeof entry.marker.position.lat === 'function') {
                entry.marker.setPosition(new google.maps.LatLng(curLat, curLng));
            } else {
                entry.marker.position = curPos;
            }

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    function createInfoWindowContent(p) {
        const isTransit = p.status === 'in_transit';
        const statusLabel = isTransit ? 'In Transit' : 'Shipped';
        const statusBg = isTransit ? '#f3e8ff' : '#eff6ff';
        const statusText = isTransit ? '#7c3aed' : '#1d4ed8';
        const demoTag = p.is_demo ? '<span style="font-size:9px;background:#fef3c7;color:#92400e;padding:1px 5px;border-radius:4px;font-weight:bold;margin-left:4px;">DEMO</span>' : '';
        const staleBadge = p.location_is_stale ? '<span style="font-size:9px;background:#fee2e2;color:#b91c1c;padding:2px 6px;border-radius:6px;font-weight:bold;margin-left:4px;border:1px solid #fca5a5;">⚠️ Signal Lost</span>' : '';
        const custName = [p.first_name, p.last_name].filter(Boolean).join(' ') || 'Customer';

        return `
            <div style="font-family:inherit;min-width:180px;padding:4px;">
                <div style="font-weight:800;color:#0f172a;font-size:13px;display:flex;align-items:center;">
                    #${p.tracking_id || ''} ${demoTag}
                </div>
                <div style="font-size:11px;color:#4A943E;font-weight:700;margin-top:2px;">
                    🏪 ${p.shop_name || 'Shop Partner'}
                </div>
                <div style="font-size:11px;color:#334155;margin-top:2px;">
                    👤 ${custName}
                </div>
                <div style="font-size:11px;color:#64748b;margin-top:1px;">
                    📍 ${p.destination_address || 'Polomolok'}
                </div>
                <div style="margin-top:6px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px;">
                    <span style="font-size:10px;font-weight:700;text-transform:uppercase;padding:2px 8px;border-radius:999px;background:${statusBg};color:${statusText};">
                        ${statusLabel}
                    </span>
                    ${staleBadge}
                </div>
            </div>
        `;
    }

    function updatePinsOnMap(pins) {
        if (!mapInstance) return;

        const currentIds = new Set();

        (pins || []).forEach(p => {
            const lat = parseFloat(p.current_lat);
            const lng = parseFloat(p.current_lng);
            if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) return;

            const pinId = String(p.id || p.tracking_id);
            currentIds.add(pinId);
            const pos = { lat, lng };

            if (activeMarkers.has(pinId)) {
                // Update existing marker position smoothly
                const entry = activeMarkers.get(pinId);
                animateMarkerTo(entry, lat, lng);
                entry.pinData = p;
                entry.infoWindow.setContent(createInfoWindowContent(p));
            } else {
                // Create new marker
                const isStale = Boolean(p.location_is_stale);
                const pinElem = createMotorcycleMarkerElement(p.status, isStale);
                let marker;
                if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                    marker = new google.maps.marker.AdvancedMarkerElement({
                        map: mapInstance,
                        position: pos,
                        content: pinElem,
                        title: `#${p.tracking_id || ''} - ${p.shop_name || 'Shop'}`
                    });
                } else {
                    marker = new google.maps.Marker({
                        map: mapInstance,
                        position: pos,
                        title: `#${p.tracking_id || ''} - ${p.shop_name || 'Shop'}`
                    });
                }

                const infoWindow = new google.maps.InfoWindow({
                    content: createInfoWindowContent(p)
                });

                marker.addListener('click', () => {
                    infoWindow.open(mapInstance, marker);
                });

                activeMarkers.set(pinId, { marker, markerElement: pinElem, infoWindow, pinData: p });
            }
        });

        // Remove markers that are no longer active
        for (const [id, entry] of activeMarkers.entries()) {
            if (!currentIds.has(id)) {
                if (entry.marker) {
                    if (typeof entry.marker.setMap === 'function') {
                        entry.marker.setMap(null);
                    }
                    entry.marker.map = null;
                }
                if (entry.infoWindow) {
                    entry.infoWindow.close();
                }
                activeMarkers.delete(id);
            }
        }
    }

    window.initAdminMap = function() {
        const el = document.getElementById('fleet-map');
        if (!el || typeof google === 'undefined' || !google.maps) return;

        const initialMapTypeId = currentAdminMapType === 'roadmap' ? google.maps.MapTypeId.ROADMAP : google.maps.MapTypeId.HYBRID;

        mapInstance = new google.maps.Map(el, {
            center: POLO_CENTER,
            zoom: 12,
            minZoom: 11,
            mapTypeId: initialMapTypeId,
            restriction: {
                latLngBounds: POLO_BOUNDS,
                strictBounds: false
            },
            mapId: 'DEMO_MAP_ID',
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        updateAdminMapTypeToggleUI();
        updatePinsOnMap(currentPins);

        if (activeMarkers.size === 1) {
            const firstEntry = activeMarkers.values().next().value;
            if (firstEntry && firstEntry.pinData) {
                const pLat = parseFloat(firstEntry.pinData.current_lat);
                const pLng = parseFloat(firstEntry.pinData.current_lng);
                mapInstance.panTo({ lat: pLat, lng: pLng });
                mapInstance.setZoom(15);
            }
        } else if (activeMarkers.size > 1) {
            const bounds = new google.maps.LatLngBounds();
            activeMarkers.forEach(entry => {
                const p = entry.pinData;
                bounds.extend({ lat: parseFloat(p.current_lat), lng: parseFloat(p.current_lng) });
            });
            mapInstance.fitBounds(bounds, 50);
            google.maps.event.addListenerOnce(mapInstance, 'idle', () => {
                if (mapInstance.getZoom() > 16) {
                    mapInstance.setZoom(16);
                }
            });
        } else {
            mapInstance.setCenter(POLO_CENTER);
            mapInstance.setZoom(12);
        }

        // Set initial sync time
        const syncLabel = document.getElementById('lastSyncLabel');
        if (syncLabel) {
            syncLabel.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        // Start 15-second auto-refresh polling with self-scheduling timer (pauses when tab is hidden)
        let fleetPollTimer = null;

        function scheduleNextFleetPoll() {
            if (fleetPollTimer) clearTimeout(fleetPollTimer);
            if (document.visibilityState === 'hidden') return;
            fleetPollTimer = setTimeout(() => {
                refreshFleetPins().finally(() => {
                    scheduleNextFleetPoll();
                });
            }, 15000);
        }

        scheduleNextFleetPoll();

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                refreshFleetPins().finally(() => {
                    scheduleNextFleetPoll();
                });
            } else {
                if (fleetPollTimer) clearTimeout(fleetPollTimer);
            }
        });

        window.addEventListener('pagehide', () => {
            if (fleetPollTimer) clearTimeout(fleetPollTimer);
        });
    };

    function refreshFleetPins() {
        if (document.visibilityState === 'hidden') return Promise.resolve();
        return fetch('<?= base_url('admin/tracking/pins') ?>', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                currentPins = Array.isArray(data.pins) ? data.pins : [];
                updatePinsOnMap(currentPins);

                // Dynamically update storefronts list in real time
                if (Array.isArray(data.groupedShops)) {
                    renderGroupedShops(data.groupedShops);
                }

                // Update KPIs
                if (data.kpis) {
                    const totalEl = document.getElementById('kpi-total-active');
                    const transitEl = document.getElementById('kpi-in-transit');
                    const shippedEl = document.getElementById('kpi-shipped');
                    const shopsEl = document.getElementById('kpi-active-shops');
                    if (totalEl) totalEl.textContent = data.kpis.total_active ?? currentPins.length;
                    if (transitEl) transitEl.textContent = data.kpis.in_transit ?? 0;
                    if (shippedEl) shippedEl.textContent = data.kpis.shipped ?? 0;
                    if (shopsEl) shopsEl.textContent = data.kpis.active_shops ?? 0;
                }

                // Update sync time
                const syncLabel = document.getElementById('lastSyncLabel');
                if (syncLabel) {
                    syncLabel.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }
            }
        })
        .catch(err => {
            console.debug('Fleet auto-refresh skipped:', err);
        });
    }
    window.refreshFleetPins = refreshFleetPins;

    // Realtime hook: when the global admin poll bus reports a delivery change,
    // re-sync fleet markers/KPIs immediately instead of waiting for the 15s timer.
    (function() {
        let fleetEventTimer = null;
        window.addEventListener('blax:admin-delivery-update', function() {
            clearTimeout(fleetEventTimer);
            fleetEventTimer = setTimeout(function() {
                try { refreshFleetPins(); } catch (e) {}
            }, 800);
        });
    })();

    function renderGroupedShops(groupedShops) {
        const container = document.getElementById('grouped-shops-container');
        if (!container) return;

        if (!groupedShops || groupedShops.length === 0) {
            container.innerHTML = `
                <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-14 text-center shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-surface-container mx-auto flex items-center justify-center text-outline mb-3">
                        <span class="material-symbols-outlined text-3xl text-terra-green">two_wheeler</span>
                    </div>
                    <h3 class="text-base font-bold text-on-surface font-display">No Active Deliveries on the Road</h3>
                    <p class="text-xs text-on-surface-variant max-w-md mx-auto mt-1">
                        There are currently no active deliveries in transit across Polomolok &amp; Tupi. Deliveries will appear here automatically in real time when shop owners open "View Live Route &amp; Map".
                    </p>
                </div>
            `;
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">';
        groupedShops.forEach(shop => {
            const rawLogo = (shop.shop_logo || '').trim();
            let logoSrc = '';
            if (rawLogo) {
                if (rawLogo.startsWith('http://') || rawLogo.startsWith('https://')) {
                    logoSrc = rawLogo;
                } else {
                    logoSrc = '<?= rtrim(base_url(), '/') ?>/' + rawLogo.replace(/^\/+/, '');
                }
            }

            const shopLogo = logoSrc 
                ? `<img src="${encodeURI(logoSrc)}" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\\\'material-symbols-outlined text-terra-green text-xl\\\'>storefront</span>';" />`
                : `<span class="material-symbols-outlined text-terra-green text-xl">storefront</span>`;

            let deliveriesHtml = '';
            (shop.deliveries || []).forEach(d => {
                const isTransit = d.status === 'in_transit';
                const statusBadgeClass = isTransit ? 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-500/20' : 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-500/20';
                const statusDotClass = isTransit ? 'bg-purple-600' : 'bg-blue-600';
                const statusLabel = isTransit ? 'In Transit' : 'Shipped';
                const custName = escapeHtml(([d.first_name, d.last_name].filter(Boolean).join(' ')) || 'Customer Recipient');
                const destAddr = escapeHtml(d.destination_address || 'Polomolok');
                const updatedTime = d.location_updated_at ? new Date(d.location_updated_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Recently';

                deliveriesHtml += `
                    <div class="p-4 space-y-2 hover:bg-surface-container-low/30 transition-colors">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono font-bold text-xs text-terra-green">#${escapeHtml(d.tracking_id || ('TRK-' + d.id))}</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full ${statusBadgeClass}">
                                <span class="w-1.5 h-1.5 rounded-full ${statusDotClass}"></span>
                                <span>${statusLabel}</span>
                            </span>
                        </div>
                        <div class="text-xs text-on-surface">
                            <p class="font-semibold text-on-surface">${custName}</p>
                            <p class="text-[11px] text-on-surface-variant truncate max-w-full mt-0.5">${destAddr}</p>
                        </div>
                        <div class="flex items-center justify-between pt-1 text-[11px] text-outline font-mono">
                            <span>Updated: ${updatedTime}</span>
                            <button type="button" 
                                    onclick="focusDeliveryPin(${d.id})" 
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-surface-container border border-outline-variant/40 hover:bg-terra-green hover:text-white hover:border-terra-green transition-all shadow-2xs active:scale-95">
                                <span class="material-symbols-outlined text-[14px]">my_location</span>
                                <span>Locate</span>
                            </button>
                        </div>
                    </div>
                `;
            });

            html += `
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl shadow-xs overflow-hidden flex flex-col transition-all hover:border-terra-green/30">
                    <div class="p-5 border-b border-outline-variant/20 bg-surface-container-low/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl bg-surface-container-high border border-outline-variant/30 overflow-hidden flex items-center justify-center shrink-0 shadow-2xs">
                                ${shopLogo}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-on-surface truncate group-hover:text-terra-green">${escapeHtml(shop.shop_name)}</h3>
                                <p class="text-[11px] text-on-surface-variant font-medium">Merchant Partner</p>
                            </div>
                        </div>
                        <span class="shrink-0 px-3 py-1 bg-terra-green/10 text-terra-green border border-terra-green/20 text-xs font-bold rounded-full font-mono">
                            ${shop.deliveries ? shop.deliveries.length : 0} Active
                        </span>
                    </div>
                    <div class="divide-y divide-outline-variant/10 flex-1">
                        ${deliveriesHtml}
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Locate button clicked on shop delivery card
    window.focusDeliveryPin = function(deliveryId) {
        const idStr = String(deliveryId);
        let targetEntry = null;

        for (const [id, entry] of activeMarkers.entries()) {
            if (id === idStr || String(entry.pinData.id) === idStr) {
                targetEntry = entry;
                break;
            }
        }

        if (targetEntry && mapInstance) {
            const lat = parseFloat(targetEntry.pinData.current_lat);
            const lng = parseFloat(targetEntry.pinData.current_lng);
            const pos = { lat, lng };

            mapInstance.panTo(pos);
            mapInstance.setZoom(15);
            targetEntry.infoWindow.open(mapInstance, targetEntry.marker);

            // Scroll up to map if not visible
            const mapEl = document.getElementById('fleet-map');
            if (mapEl) {
                mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        updateAdminMapTypeToggleUI();
        if (typeof google !== 'undefined' && google.maps && !mapInstance) {
            window.initAdminMap();
        }
    });
})();
</script>

<!-- Google Maps Platform JS API -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= esc(env('GOOGLE_MAPS_API_KEY')) ?>&libraries=marker,geometry&loading=async&callback=initAdminMap" async defer></script>

<?= $this->endSection() ?>

