# Render Production Log Analysis & Fix Plan

**Document Version:** 1.0 (Draft for Approval)  
**Target:** Render Production Environment (`blax-martketplace.onrender.com`, Singapore Free Web Service + Aiven MySQL)  
**Rule:** No application code is changed until explicit user approval per work package.

---

## 1. Executive Summary & Production Log Diagnostic

### 1.1 Production Incident Snapshot (25 Oct 2026, 08:07–08:08 UTC)
From the provided Render production logs during tenant navigation (`/tenant/deliveries` &rarr; `/tenant/deliveries/2128`):
1. **Debug Toolbar running in production**: `GET /?debugbar` (2.5ms) and `GET /?debugbar_time=...` (841ms, 28KB) executed on production. This confirms `CI_ENVIRONMENT` is effectively `development` in the running container.
2. **`ptrace(ATTACH)` Operation not permitted**: FPM slowlog timeout (2s) attempts to attach ptrace to slow workers. Docker/Render security profile denies `CAP_SYS_PTRACE`, generating noisy error logs.
3. **Heavy Initial Polling Storm**: `GET /tenant/realtime/check` without query parameters fired 3 times within 8 seconds (~2,600ms each).
4. **Fast-path Poll Latency**: Parameterized `GET /tenant/realtime/check?last_order_id=...` took ~920ms with low CPU (0.0%–1.1%), indicating ~900ms of remote network/database round-trip latency.
5. **Duplicate `stop-broadcast` requests**: Two identical `POST /tenant/deliveries/stop-broadcast` were sent in the exact same second (1,664ms & 1,688ms).
6. **Delivery Location & Lookup Latency**: `POST /tenant/deliveries/update-location` took 1,755ms and `GET /tenant/deliveries/2128` took 2,467ms at ~1% CPU.

---

## 2. Detailed Work Packages (P0 – P4)

### P0. Production Environment Lockdown & Debug Toolbar Elimination

#### Root Cause
`/?debugbar` and `/?debugbar_time=` only register when `CI_DEBUG === true` (`app/Config/Events.php:45`), which is defined only in `app/Config/Boot/development.php`. Therefore, the Render service has `CI_ENVIRONMENT=development` in its dashboard environment variables. 
Additionally:
- `docker/entrypoint.sh:6` defaults to production only if unset (`${CI_ENVIRONMENT:-production}`).
- `app/Config/Filters.php:67` statically defines `'toolbar'` in `$required['after']`.

#### Exact Files
- `docker/entrypoint.sh`
- `app/Config/Filters.php`
- Render Dashboard (Manual User Action)

#### Changes Proposed
1. **Render Dashboard Setting**:
   - Navigate to **Render Dashboard** &rarr; Select Web Service `blax-martketplace` &rarr; Click **Environment** tab.
   - Locate `CI_ENVIRONMENT` &rarr; Edit value to `production` (or add key `CI_ENVIRONMENT` with value `production` if missing).
   - Save changes (Render will trigger a rebuild/redeploy).
2. **Hardened Docker Entrypoint Guard (`docker/entrypoint.sh`)**:
   - Enforce production safeguard: Unless `ALLOW_DEV_MODE=1` is explicitly set, force `CI_ENVIRONMENT="production"`.
   - Remove stale debugbar files on boot: `rm -rf /var/www/html/writable/debugbar/*`.
   - Print startup audit log:
     ```sh
     echo "Environment: ${CI_ENVIRONMENT} (Debug Toolbar: disabled, Dev Mode Override: ${ALLOW_DEV_MODE:-0})"
     ```
3. **Application Safeguard (`app/Config/Filters.php`)**:
   - In `Filters::__construct()`, remove `'toolbar'` from `$required['after']` unless `defined('CI_DEBUG') && CI_DEBUG === true && !is_cli()`.

#### Risk & Rollback
- **Risk**: Very low. Disables the debugbar and stops collection of queries/profiler history files.
- **Rollback**: Revert `Filters.php` and `entrypoint.sh` commits; reset environment variable in Render dashboard.
- **Test**:
  - `curl -I http://localhost:8080/?debugbar` returns 404 or standard view instead of toolbar payload.
  - No `/?debugbar_time=` requests generated.

---

### P1. Eliminate `ptrace(ATTACH)` Noise & Configure Non-blocking Profiling

#### Root Cause
`docker/entrypoint.sh:23,33` sets `FPM_SLOWLOG_TIMEOUT="${FPM_SLOWLOG_TIMEOUT:-2s}"` and `slowlog = /proc/self/fd/2`. When a remote database query causes a web request to cross 2.0s, PHP-FPM attempts to ptrace the child worker to generate a PHP stack trace. Render container environments do not grant `CAP_SYS_PTRACE`, resulting in:
`ERROR: failed to ptrace(ATTACH) child 23: Operation not permitted (1)`
`WARNING: [pool www] child 23 ... executing too slow (2.4 sec), logging`
The trace never succeeds, producing only noisy errors.

#### Exact Files
- `docker/entrypoint.sh`

#### Changes Proposed
1. Make FPM slowlog opt-in and default to disabled:
   - Set `FPM_SLOWLOG_TIMEOUT="${FPM_SLOWLOG_TIMEOUT:-0}"`.
   - Only configure `request_slowlog_timeout` and `slowlog` in `zz-php-fpm.conf` if `FPM_SLOWLOG_TIMEOUT` is non-zero.
2. Rely on:
   - PHP-FPM access log format (`%m %{REQUEST_URI}e %s %{mili}dms %{kilo}Mkb %C%%`), which already logs duration, status, memory, and CPU.
   - Nginx `$request_time` and `$upstream_response_time` in access log.
   - Application-level `PerformanceFilter` under `APP_PROFILE=1` to log slow queries (>500ms) with execution context.

#### Risk & Rollback
- **Risk**: None. Disables a failing ptrace call that cannot work in Render sandbox containers.
- **Rollback**: Set `FPM_SLOWLOG_TIMEOUT=2s` in environment or revert `entrypoint.sh`.
- **Test**:
  - Requests exceeding 2.0s log standard access log timings without `ptrace(ATTACH)` error lines.

---

### P2. Realtime Polling Optimization & Elimination of Heavy Initial Polls

#### Root Cause
1. **Client State Reset on Page Navigation**: In `app/Views/layouts/tenant.php:751-756`, `lastOrderId = null`, `lastPrintingId = null`, `lastNotifId = null`, and `isInitial = true` are reinitialized on every page load. After 1.5s, the browser sends an un-parameterized `GET /tenant/realtime/check`.
2. **Server Heavy Path**: When query parameters are missing, `Tenant::realtimeCheck()` executes the full path (inventory summaries, pending counts, notification list), requiring 8 sequential round-trips (~2,600ms over Aiven remote connection).
3. **No In-flight Guard**: When tab visibility changes or multiple events fire, multiple `pollTenantUpdates()` run concurrently.
4. **Customer & Admin Alignment**: Similar checks apply to customer and admin layouts.

#### Exact Files
- `app/Controllers/Tenant.php`
- `app/Views/layouts/tenant.php`
- `app/Controllers/Customer.php`
- `app/Views/layouts/marketplace.php`
- `app/Controllers/Admin.php`
- `app/Views/layouts/admin.php`

#### Changes Proposed
1. **Embed Server-Side Watermark Bootstrap in Layout**:
   - In `Tenant::getShopOrRedirect()`, resolve current max IDs once during page render (or in a lightweight shared helper).
   - Embed in `app/Views/layouts/tenant.php`:
     ```html
     <script id="blax-tenant-watermark" type="application/json">
     <?= json_encode($realtimeBootstrap ?? ['max_order_id' => 0, 'max_printing_id' => 0, 'max_notif_id' => 0, 'unread_count' => 0]) ?>
     </script>
     ```
   - On page load, `tenant.php` parses this bootstrap payload, initializes `lastOrderId`, `lastPrintingId`, and `lastNotifId`, and sets `isInitial = false`.
   - **Result**: The very first background poll (scheduled at 20s or 30s instead of 1.5s) already uses the **fast path**. The ~2.6s initial poll is completely eliminated.
2. **Concurrency & In-Flight Protection**:
   - Add an `isPollInFlight` flag and `AbortController`.
   - If a poll is currently in-flight, skip new triggers.
   - On `visibilitychange`, do not schedule if already in-flight or if interval has not elapsed.
3. **Ultra-Light Fast-Path Response**:
   - When `changed === false`, return minimal response payload (or 204 No Content with headers) containing only `unread_count` if updated. Fetch full notifications only when `last_notif_id` increases or when user opens the notification bell.

#### Risk & Rollback
- **Risk**: Low. Order/print toasts and notification badges remain real-time via the fast-path snapshot.
- **Rollback**: Revert `tenant.php` layout and `Tenant.php` controller changes.
- **Test**:
  - Open `/tenant/deliveries`. Verify network tab: no poll at 1.5s; first poll at 20s contains `?last_order_id=...&last_printing_id=...&last_notif_id=...`. Response is ~900ms (fast path) instead of 2,600ms.

---

### P3. Fix Duplicate `stop-broadcast` & Optimize Delivery Location Endpoints

#### Root Cause
1. **Dual Dispatch in `delivery_detail.php:1183-1198`**:
   The script invokes `navigator.sendBeacon(stopUrl, blob)` AND immediately calls `fetch(stopUrl, ...)` in the same function without an `else` condition. This caused duplicate requests at the exact same second (`1,664ms` and `1,688ms`).
2. **Redundant Heavy Queries in `stopDeliveryBroadcast`**:
   `Tenant::stopDeliveryBroadcast()` called `$this->getShopOrRedirect()` which executes shop lookup, unread notification count, and view renderer setup for a headless background beacon.
3. **Unbounded Frequency & Redundant DB Writes in `updateDeliveryLocation`**:
   `updateDeliveryLocation` was called every 5s regardless of whether the coordinates changed. Inside the controller, it ran `getShopOrRedirect()`, `resolveShopId()`, `find()`, and `update()`.

#### Exact Files
- `app/Views/tenant/delivery_detail.php`
- `app/Controllers/Tenant.php`

#### Changes Proposed
1. **Fix Dual Dispatch in `delivery_detail.php`**:
   - Update `stopBroadcastingOnExit()`:
     ```javascript
     if (navigator.sendBeacon) {
         const blob = new Blob([payload], { type: 'application/x-www-form-urlencoded; charset=UTF-8' });
         navigator.sendBeacon(stopUrl, blob);
     } else {
         fetch(stopUrl, { method: 'POST', body: payload, keepalive: true, headers: { ... } }).catch(() => {});
     }
     ```
   - Add an execution guard `if (isExitBroadcastStopped) return; isExitBroadcastStopped = true;`.
2. **Streamline `stopDeliveryBroadcast()` Controller**:
   - Replace `$this->getShopOrRedirect()` with `$this->resolveShopIdForPoll()`.
   - Release session immediately (`session_write_close()`).
   - Combine shop verification and update into a single targeted update query:
     `UPDATE deliveries SET location_updated_at = NULL, current_lat = NULL, current_lng = NULL WHERE id = ? AND shop_id = ?`
3. **Coordinate Jitter Filter & Throttling in `updateDeliveryLocation`**:
   - In `delivery_detail.php`, only dispatch location updates if moved > 10 meters OR if 15 seconds have elapsed since last send.
   - In `Tenant::updateDeliveryLocation`, release session lock early (`session_write_close()`). Combine lookup into 1 query. If distance moved is negligible (< 5m), return early without updating DB.

#### Risk & Rollback
- **Risk**: Low. Live tracking HUD and customer map view continue to receive live coordinates.
- **Rollback**: Revert `delivery_detail.php` and `Tenant.php` changes.
- **Test**:
  - Navigate away from `/tenant/deliveries/2128` &rarr; observe exactly 1 `stop-broadcast` request in access log.
  - Verify GPS broadcast updates are throttled and take < 500ms on the server.

---

### P4. Diagnostic: Prove Database Round-Trip Bottleneck

#### Root Cause
Slow requests show CPU `%C` between 0.4% and 1.6%. The PHP process spends >98% of its execution time idle, waiting on network socket I/O to Aiven MySQL.

#### Exact Files
- `app/Commands/DbPingCommand.php` [NEW]
- `app/Config/Routes.php` (optional protected internal health route)

#### Changes Proposed
1. Create Spark CLI command `php spark db:ping`:
   - Measures raw TCP/TLS connection open time.
   - Inspects whether SSL is active (`SHOW STATUS LIKE 'Ssl_cipher'`) and whether persistent connection `p:` is in use.
   - Executes `SELECT 1` sequentially 20 times, recording min, avg, max, and standard deviation.
   - Displays a clean diagnostic table.
2. Run command via Render Shell / SSH and report findings.
3. **[NEEDS APPROVAL] Infrastructure Options**:
   - **Question to User**: What region and plan is your Aiven MySQL cluster deployed in? (Render web service is in `singapore`).
   - If Aiven is in a different region (e.g. Frankfurt, US-East, Mumbai), inter-region latency is ~100–250ms per round trip. Moving Aiven to Singapore (`ap-southeast-1`) would reduce round-trip latency to ~2–5ms, speeding up all pages by 5x–10x with zero code changes.

#### Risk & Rollback
- **Risk**: None (read-only diagnostic command).
- **Rollback**: Delete `app/Commands/DbPingCommand.php`.
- **Test**:
  - Run `php spark db:ping` locally and in Render container.

---

## 3. Out-of-Scope Report: CSRF Exemptions Analysis

In `app/Config/Filters.php:81`, the following two routes are exempted from CSRF:
1. `api/route`:
   - **Protection**: Protected by authentication check in production (`CustomerOrderController.php:365-371`: `if (!session()->get('isLoggedIn')) return 401;`) plus `actionThrottle` rate limiter filter.
   - **Risk Assessment**: Low. Only computes road routing coordinates via Google Routes proxy for logged-in users; does not alter state or mutate user data.
2. `tenant/deliveries/stop-broadcast`:
   - **Protection**: Protected by `TenantAuth` filter and session verification in `Tenant.php:6318-6324`. Verifies that the delivery record belongs to the authenticated shop.
   - **Risk Assessment**: Low. Required for `navigator.sendBeacon` during page unload, which cannot send dynamic CSRF tokens. Mutates only the ephemeral GPS broadcast state of the tenant's own delivery.

---

## 4. Expected vs Measured Effect Table

| Metric / Endpoint | Prior (Production Log) | Target Expected | Measured Effect |
|---|---|---|---|
| `GET /?debugbar` & `debugbar_time` | 2 requests, 843ms, 28KB | 0 requests (eliminated) | **TO MEASURE** |
| `ptrace(ATTACH)` error lines | 3 errors per 8s | 0 errors (disabled) | **TO MEASURE** |
| Initial `GET /tenant/realtime/check` | ~2,600ms (x3 in 8s) | 0 un-parameterized calls | **TO MEASURE** |
| Fast-path `GET /tenant/realtime/check` | ~920ms | < 350ms (or 204 No Content) | **TO MEASURE** |
| `POST /tenant/deliveries/stop-broadcast` | 2 calls in same second, ~1,680ms | 1 call via beacon, < 400ms | **TO MEASURE** |
| `POST /tenant/deliveries/update-location` | 1,755ms every 5s | Throttled, < 500ms | **TO MEASURE** |
| `GET /tenant/deliveries/{id}` | 2,467ms | < 800ms | **TO MEASURE** |

---

## 5. Verification Checklist (Post-Deployment Log Assertions)

When deployed to Render, the logs should reflect the following criteria:
- [ ] Startup line reads: `Starting container on PORT 8080 (CI_ENVIRONMENT: production)...`
- [ ] Startup line reads: `Environment: production (Debug Toolbar: disabled)`
- [ ] No `GET /?debugbar` or `GET /?debugbar_time=` entries appear in Nginx/FPM access logs.
- [ ] Zero occurrences of `ERROR: failed to ptrace(ATTACH) child ... Operation not permitted`.
- [ ] No un-parameterized `GET /tenant/realtime/check` calls appear upon loading tenant pages.
- [ ] Realtime checks arrive with `?last_order_id=...&last_printing_id=...&last_notif_id=...` and complete without slow log warnings.
- [ ] Navigating away from delivery detail page emits exactly one `POST /tenant/deliveries/stop-broadcast`.
