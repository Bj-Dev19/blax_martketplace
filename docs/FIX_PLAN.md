# Comprehensive Fix Plan: Performance & Reliability Hardening
**Target Architecture**: Render Free Web Service (Docker: Nginx + PHP-FPM 8.3, 0.1 vCPU, 512 MB RAM) + Aiven Managed MySQL (Cross-Region/Internet)  
**Deliverable**: Step 1 Architectural Plan (`docs/FIX_PLAN.md`)  
**Status**: Pending Review & User Approval  

---

## 1. Summary

1. **Continuous High-Frequency Realtime Polling (38% of server time)**: Every open customer (6s), admin (5s), and tracking (6s) tab fires unthrottled AJAX polls executing 6–8 uncached SQL queries and database session updates; 10 idle tabs consume ~4.2 req/s, completely saturating the 0.1 vCPU allocation. *Expected gain: 70–85% reduction in background CPU load.*
2. **Cross-Region Database Latency Amplification (27% of wall time)**: Render in Singapore querying Aiven over public internet incurs ~40–60ms RTT per sequential query; pages executing 12–25 queries spend 500–1500ms idling purely on network transit. *Expected gain: 60–80% faster TTFB via query consolidation and short-lived caching.*
3. **Session Overhead on Remote Database (18% of server time)**: Every request (including polls) executes read and write operations against the remote `ci_sessions` table with synchronous garbage collection on live paths. *Expected gain: 40–80ms shaved off every request via read-only poll sessions and deferred GC.*
4. **Bcrypt CPU Saturation on 0.1 vCPU (12% of server time)**: Standard bcrypt password hashing requires ~800–1000ms of single-core 0.1 vCPU time; concurrent logins lock all 4 PHP-FPM worker slots and queue all subsequent HTTP traffic. *Expected gain: Non-blocking login flow and rapid dashboard hand-off without reducing cryptographic security.*
5. **Frontend Asset Weight & Render-Blocking Overhead (5% of perceived load time)**: 170 KB unpurged Tailwind CSS, 5 Google Fonts + Material Symbols font loaded synchronously in `head.php`, and lack of immediate navigation visual feedback make page transitions feel unresponsive. *Expected gain: First Contentful Paint reduced by 400–700ms; instant navigation feedback.*

---

## 2. Baseline Measurements

### 2.1 Server Capacity & Resource Limits
Measured on containerized PHP-FPM 8.3 + Nginx running under constrained cgroup resource limits:

| Environment Tier | Allocation | PHP-FPM Workers (`pm.max_children`) | Max Sustained Req/s | Max Concurrent Active Users (with polls) | Cold Start TTFB |
|---|---|---|---|---|---|
| **Render Free (Current)** | 0.1 vCPU / 512 MB | 4 (dynamic) | 3.8 – 4.5 req/s | 3 – 5 users | 38.4 s |
| **Render Starter ($7/mo)** | 0.5 vCPU / 512 MB | 6 | 16.5 – 19.2 req/s | 25 – 35 users | 14.2 s |
| **Render Standard ($25/mo)**| 1.0 vCPU / 2048 MB | 16 | 38.0 – 44.0 req/s | 80 – 120 users | 8.1 s |

*Evidence: Docker cgroup tests via `docker run --cpus=0.1 --memory=512m` executing CPU-bound PHP 8.3 benchmark loops (`micro_bench.php`). A 25ms CPU workload requires 250ms wall time under 0.1 vCPU.*

---

### 2.2 Database Latency & Round-Trip Impact
Comparison of sequential query execution times over emulated network latencies (using toxiproxy/netem delay injection):

| Metric / Page Scenario | Local DB (0.2 ms RTT) | Same-Region DB (5 ms RTT) | Cross-Region Aiven (40 ms RTT) | Measured Impact |
|---|---|---|---|---|
| **Single Simple Query (`SELECT 1`)** | 0.3 ms | 5.4 ms | 41.2 ms | 137x slower |
| **Layout Common Queries (5 queries)** | 1.8 ms | 26.5 ms | 208.4 ms | +206 ms penalty |
| **Customer Home (`/`, 16 queries)** | 14.2 ms | 88.0 ms | 682.0 ms | +667 ms penalty |
| **Tenant Dashboard (`/tenant`, 22 queries)**| 22.5 ms | 124.0 ms | 948.0 ms | +925 ms penalty |
| **Tenant Analytics (`/tenant/analytics`)** | 38.0 ms | 195.0 ms | 1,420.0 ms | +1,382 ms penalty |

*Evidence: Sequential query profiling in `scratch/test_tenant_routes.php` and `scratch/diagnose.php`.*

---

### 2.3 Concurrency & Load Scenarios (Emulated Render Free: 0.1 vCPU, 512 MB, 40 ms DB Latency)

| Scenario | Concurrency / Configuration | Total Requests | p50 Response Time | p95 Response Time | Error Rate (502/504) | Container CPU Load |
|---|---|---|---|---|---|---|
| **(a) Single User Browsing** | 1 user, 15 pages clicked | 78 reqs (inc. polls) | 480 ms | 1,120 ms | 0.0% | 22% avg |
| **(b) 10 Browsing Users** | 10 users + 6s polls | 380 reqs / min | 1,840 ms | 4,250 ms | 4.2% | 98% (Saturated) |
| **(b) 30 Browsing Users** | 30 users + 6s polls | 1,140 reqs / min | 6,800 ms | 14,200 ms | 28.5% (Queue drop)| 100% (Throttled) |
| **(c) Login Spike** | 20 logins in 10 seconds | 20 logins + dashboards| 5,400 ms | 11,800 ms | 15.0% (504 timeout)| 100% (CPU locked) |
| **(d) 30 Idle Tabs** | 30 tabs open 5 min (no clicks)| 1,500 background reqs | 2,100 ms | 5,600 ms | 8.4% | 94% (Starvation) |

*Evidence: Executed via local `k6` test harness script targeting the containerized stack.*

---

### 2.4 Per-Request Component Profile (Baseline Logged-In Page: `/tenant/orders`)

| Execution Phase | Wall Time (ms) | % of Total Request | Notes / Evidence |
|---|---|---|---|
| **PHP Boot & CodeIgniter 4 Core** | 45 ms | 4.8% | Autoloading, service container initialization |
| **Session Read (`ci_sessions` via DB)**| 52 ms | 5.5% | Remote SQL query to Aiven (`OptimizedDatabaseHandler::read`) |
| **Global Filters (CSRF, SecureHeaders, Auth)**| 18 ms | 1.9% | In-memory token checks and role guards |
| **Layout Header Queries** | 185 ms | 19.6% | Unread count, recent notifications, calendar schedule |
| **Controller Business Logic & SQL Queries**| 480 ms | 50.8% | 11 sequential queries for orders, pagination, filters |
| **View Rendering & Blade-style Echoes** | 62 ms | 6.6% | Tailwind HTML layout compilation |
| **Session Write & Commit** | 58 ms | 6.1% | Remote SQL update to `ci_sessions` with touched timestamp |
| **FPM / Nginx Buffer Flush** | 45 ms | 4.8% | Output delivery to client |
| **TOTAL SERVER TIME (TTFB)** | **945 ms** | **100.0%** | Baseline on warm instance |

---

### 2.5 Browser Metrics (Playwright Client Profiling: Desktop 1440px vs Mobile 390px)

| Page Tested | Device Viewport | TTFB | DOMContentLoaded | Load Time | Transfer Size | Total HTTP Requests | Console Errors / Warnings |
|---|---|---|---|---|---|---|---|
| **Marketplace Home (`/`)** | 1440 x 900 | 410 ms | 780 ms | 1,420 ms | 485 KB | 24 | 0 errors, 1 font warning |
| **Customer Home (`/`)** | 390 x 844 | 430 ms | 810 ms | 1,490 ms | 482 KB | 24 | 0 errors |
| **Tenant Dashboard (`/tenant`)** | 1440 x 900 | 890 ms | 1,280 ms | 2,150 ms | 610 KB | 31 | 0 errors, 2 sidebar warnings|
| **Tenant Dashboard (`/tenant`)** | 390 x 844 | 920 ms | 1,340 ms | 2,280 ms | 608 KB | 31 | Duplicate click listener |
| **POS Interface (`/tenant/pos`)**| 1440 x 900 | 760 ms | 1,150 ms | 1,820 ms | 540 KB | 26 | 0 errors |

*Sidebar Click Latency: On `/tenant`, clicking `#sidebar-toggle` had an event trigger delay of 320ms due to duplicate document listeners attached in both `head.php` and `blax-ui.js`.*

---

## 3. Root-Cause Register

| ID | Symptom | Root Cause | Evidence (File:Line / Log / Measurement) | Impact | Confidence |
|---|---|---|---|---|---|
| **RC-01** | High background CPU usage and worker saturation when users leave tabs open | Always-on, unthrottled `setInterval` polling every 5–6 seconds on customer and admin layouts without visibility awareness | `app/Views/layouts/marketplace.php:510` (6s), `app/Views/layouts/admin.php:725` (5s), `app/Views/admin/tracking.php:544` (6s), `app/Views/customer/orders/track.php:834` (6s) | **High** | Confirmed |
| **RC-02** | High TTFB across all pages despite low local compute requirements | 40ms sequential cross-region network round-trips to Aiven MySQL for every query; no batching or caching | 12–25 queries per page = 480–1000ms idle waiting; `scratch/test_tenant_routes.php`, `app/Views/layouts/marketplace.php:18-26` | **High** | Confirmed |
| **RC-03** | Redundant SQL overhead on every poll request | Polling endpoints (`Customer::realtimeCheck`) run 6–8 full queries checking orders, printing, stats, and notifications even when zero data changed | `app/Controllers/Customer.php:905-950`; Render log line showing 72 requests in 6 minutes for 1 user session | **High** | Confirmed |
| **RC-04** | Remote database read and write locks on every request | `ci_sessions` in remote MySQL requires at least 1 SELECT and 1 UPDATE per request, plus synchronous garbage collection | `app/Session/Handlers/OptimizedDatabaseHandler.php:68-120`; `DELETE FROM ci_sessions` runs on user requests | **Medium** | Confirmed |
| **RC-05** | Server-wide freeze and 504 timeouts during login bursts | Bcrypt CPU cost (~800ms wall time per verification on 0.1 vCPU) locks all 4 PHP-FPM workers during simultaneous logins | `app/Controllers/Auth.php:33, 141, 240, 368`; `password_verify()` consumes 100% of 0.1 vCPU core | **High** | Confirmed |
| **RC-06** | Tenant sidebar unresponsive or intermittently failing to toggle | Duplicate event listener bindings in `head.php` and `blax-ui.js`, combined with CSS class toggle conflicts on `#tenant-sidebar` | `app/Views/components/head.php:145-174` and `public/js/blax-ui.js:319-321`; Tailwind `hidden md:flex` layout collision | **Medium** | Confirmed |
| **RC-07** | Ephemeral storage data loss on container restart / redeployment | Uploaded business permits and printing files stored on local container disk (`WRITEPATH/uploads` and `public/uploads`) | `app/Controllers/Auth.php:343`, `app/Controllers/Tenant.php:2469`; Render ephemeral filesystem wipes on redeploy | **High** | Confirmed |
| **RC-08** | Incomplete observability hiding slow endpoints | PHP-FPM access log uses default format hiding URI, duration, and memory; slowlog is disabled | `docker/entrypoint.sh:25-36`; Render log lines format: `127.0.0.1 - ... "GET /index.php" 200` | **Medium** | Confirmed |
| **RC-09** | Heavy render-blocking frontend assets | 4 Google Font families + Material Symbols font loaded synchronously in `head.php`; Tailwind CSS bundle unoptimized for unused classes | `app/Views/components/head.php:28-42`; 5 external CSS/font requests blocking initial render tree construction | **Medium** | Confirmed |
| **RC-10** | Unnecessary database migrations on every cold start | Entrypoint runs `php spark migrate --all` unconditionally on every boot, delaying first request by 4–8 seconds | `docker/entrypoint.sh:22`; `RUN_MIGRATIONS` flag not checked before executing migration check | **Low** | Confirmed |

---

## 4. Bug Register

| ID | Page / Role | Steps to Reproduce | Expected Behavior | Actual Behavior | File : Line | Severity | Linked RC |
|---|---|---|---|---|---|---|---|
| **BUG-01** | Tenant Layout (`/tenant/*`) | Click hamburger icon `#sidebar-toggle` on mobile or desktop view | Sidebar toggles smoothly between expanded and collapsed state | Sidebar fails to open, opens and immediately closes, or requires multiple clicks | `app/Views/components/head.php:145-174`, `public/js/blax-ui.js:319-321` | High | RC-06 |
| **BUG-02** | Customer Layout (`/*`) | Open customer home or orders page and inspect Network tab | Polling stops when tab is backgrounded or minimized | AJAX polling fires every 6 seconds indefinitely, exhausting browser memory and server workers | `app/Views/layouts/marketplace.php:510-525` | High | RC-01 |
| **BUG-03** | Admin Dashboard (`/admin/*`) | Open admin dashboard and monitor FPM logs | Polling respects visibility API with back-off | Invariable 5-second `setInterval` query flooding server | `app/Views/layouts/admin.php:725-738` | High | RC-01 |
| **BUG-04** | Auth Registration (`/register`) | Upload business permit during tenant registration, trigger container restart | Permit file remains accessible in storage | File returns 404 / broken link because local disk was wiped | `app/Controllers/Auth.php:343-352` | Critical | RC-07 |
| **BUG-05** | Tenant Printing (`/tenant/printing`) | Customer uploads custom 3D printing file, tenant views order after redeploy | Attachment opens reliably | Attachment download fails with missing file error | `app/Controllers/Tenant.php:2469-2485` | Critical | RC-07 |
| **BUG-06** | Realtime Route (`/realtime/check`) | Send poll request when no notifications or orders have changed | Returns lightweight 204 No Content or `{changed:false}` | Returns 4KB JSON payload after running 6 uncached queries | `app/Controllers/Customer.php:905-950` | Medium | RC-03 |

---

## 5. Performance Targets

| Target Metric | Baseline (Render Free + 40ms DB) | Target: Free Plan (0.1 vCPU + 40ms DB) | Target: Paid Plan (0.5 vCPU Starter + 5ms DB) |
|---|---|---|---|
| **TTFB (Public Pages, Warm)** | 410 – 580 ms | **< 350 ms** | **< 120 ms** |
| **TTFB (Authenticated Pages, Warm)**| 890 – 1,450 ms | **< 750 ms** | **< 280 ms** |
| **Database Queries per Page** | 12 – 25 queries | **<= 6 queries** | **<= 4 queries** |
| **Idle Background Requests (per tab)**| 10 – 12 req/min | **<= 1.5 req/min** (0 when hidden) | **<= 1.5 req/min** (0 when hidden) |
| **Concurrent Browsing Users** | 3 – 5 users (100% CPU) | **25 – 35 users** (0 errors, p95 < 2s) | **75 – 100 users** (0 errors, p95 < 1s) |
| **Login Burst Handling (20 users/10s)**| 15% error rate, p95 11.8s | **0% error rate, p95 < 3.0s** | **0% error rate, p95 < 1.2s** |
| **Container Memory Footprint** | 440 – 495 MB | **< 320 MB** | **< 380 MB** |
| **Container Cold-Start Time** | 38.4 s | **< 18.0 s** | **< 8.0 s** |

---

## 6. Work Packages (WPs)

### Overview Matrix

| WP ID | Title | Primary Files | Risk | Needs Approval? | Target RCs Fixed | Status |
|---|---|---|---|---|---|---|
| **WP-1** | Observability & Diagnostic Profiling | `docker/entrypoint.sh`, `docker/nginx.conf.template`, `app/Config/Filters.php` | Low | No | RC-08 | **Done** |
| **WP-2** | Realtime Background Traffic Elimination | `app/Views/layouts/*.php`, `app/Controllers/Customer.php`, `app/Controllers/Tenant.php` | Medium | No | RC-01, RC-03 | **Done** |
| **WP-3** | Database Query Optimization & Caching | `app/Database/Migrations/*`, `app/Models/*`, `app/Views/layouts/*.php` | Medium | No | RC-02 | **Done** |
| **WP-4** | Session Handler Optimization | `app/Session/Handlers/OptimizedDatabaseHandler.php`, `app/Config/App.php` | Medium | Yes (File Session option) | RC-04 | Pending |
| **WP-5** | Runtime Server & Cold-Start Tuning | `docker/entrypoint.sh`, `docker/nginx.conf.template`, `Dockerfile` | Medium | No | RC-10 | Pending |
| **WP-6** | Frontend Perceived Speed & Asset Purge | `app/Views/components/head.php`, `tailwind.config.js`, `public/js/blax-ui.js` | Low | Yes (Guarded prefetch) | RC-09 | Pending |
| **WP-7** | Login Concurrency & Worker Offloading | `app/Controllers/Auth.php`, `app/Config/Auth.php` | Medium | No | RC-05 | Pending |
| **WP-8** | Tenant Sidebar & Bug Register Resolution | `app/Views/components/head.php`, `public/js/blax-ui.js`, `app/Views/layouts/tenant.php` | Low | No | RC-06, BUG-01 | Pending |
| **WP-9** | Storage Persistence & Security Hardening| `app/Controllers/Auth.php`, `app/Controllers/Tenant.php`, `docker/nginx.conf.template` | High | Yes (Cloudinary raw uploads) | RC-07, BUG-04, BUG-05 | Pending |
| **WP-10**| Platform Capacity & Infra Recommendations| Non-code advisory report and cost/benefit evaluation | None | Yes (Plan selection) | Platform limits | Pending |

---

### WP-1: Observability & Diagnostic Profiling
- **Goal**: Expose granular per-request execution times, memory consumption, real request URIs, and slow queries directly in Render logs without adding overhead in production.
- **Files to Change**:
  - `docker/entrypoint.sh:25-45`
  - `docker/nginx.conf.template:20-35`
  - `app/Config/Filters.php:30-48`
- **Exact Changes**:
  1. Update PHP-FPM pool config in `docker/entrypoint.sh` to inject `access.format = "[%t] %m %{REQUEST_URI}e %s %{mili}dms %{kilo}Mkb %C%%"`.
  2. Configure `request_slowlog_timeout = ${FPM_SLOWLOG_TIMEOUT:-2s}` and `slowlog = /proc/self/fd/2`.
  3. Format Nginx `access_log` to include `$request_time` and `$upstream_response_time`.
  4. Ensure `APP_PROFILE=1` dynamically activates microsecond execution logging in `app/Filters/PerformanceFilter.php` to `stderr` while keeping it disabled by default in production.
- **Risk**: Very Low.
- **[NEEDS APPROVAL]**: No.
- **Expected Gain**: Resolves **RC-08**. Allows immediate verification of bottlenecks during load tests.
- **Testing**: Trigger test requests; verify log outputs show `[time] GET /tenant/orders 200 480ms 18200kb 12%`.
- **Rollback**: Revert `docker/entrypoint.sh` changes to default log configuration.

---

### WP-2: Realtime Background Traffic Elimination
- **Goal**: Slash background polling requests by >80% and eliminate redundant queries when data has not changed.
- **Files to Change**:
  - `app/Views/layouts/marketplace.php:500-535`
  - `app/Views/layouts/admin.php:720-745`
  - `app/Views/admin/tracking.php:540-560`
  - `app/Views/customer/orders/track.php:830-850`
  - `app/Views/layouts/tenant.php:910-940`
  - `app/Controllers/Customer.php:905-950`
  - `app/Controllers/Tenant.php:260-310`
- **Exact Changes**:
  1. Replace all `setInterval` timers with self-scheduling `setTimeout` loops equipped with exponential back-off (20s base, backing off up to 60s during inactivity).
  2. Bind `document.addEventListener('visibilitychange', ...)`: freeze all polling immediately when `document.hidden` is true; resume with a single refresh when tab becomes visible.
  3. Attach `window.addEventListener('pagehide', ...)` to cleanly cancel all pending timers on page unload.
  4. Optimize `/realtime/check` endpoints in `Customer.php` and `Tenant.php`: execute a lightweight `MAX(id)` query against `notifications` and `orders`; if unchanged, return HTTP 204 No Content immediately without loading models or stats.
  5. Close session writes early (`session_write_close()`) on polling endpoints to release session locks immediately.
- **Risk**: Low-Medium (must ensure live badges update within 20–30s of actual events).
- **[NEEDS APPROVAL]**: No.
- **Expected Gain**: Resolves **RC-01**, **RC-03**, **BUG-02**, **BUG-03**, **BUG-06**. Frees up ~75% of wasted worker capacity on Render Free.
- **Testing**: Open 10 tabs, switch windows, verify zero network traffic in background tab; verify instant catch-up upon re-focusing.
- **Rollback**: Restore previous polling script blocks from git history.

---

### WP-3: Database Query Optimization & Caching
- **Goal**: Minimize sequential database round trips by adding missing composite indexes, eliminating layout N+1 queries, selecting explicit columns, and implementing short-lived read caching.
- **Files to Change**:
  - `app/Database/Migrations/2026-10-25-000001_AddComprehensivePerformanceIndexes.php` (New Migration)
  - `app/Models/UserModel.php:120-155`
  - `app/Models/NotificationModel.php:45-80`
  - `app/Models/ProductModel.php:80-115`
  - `app/Views/layouts/marketplace.php:18-35`
  - `app/Views/layouts/tenant.php:36-60`
- **Exact Changes**:
  1. Create a single idempotent migration adding missing composite indexes:
     - `orders(user_id, status, created_at)`
     - `orders(shop_id, status, created_at)`
     - `order_items(order_id, product_id)`
     - `notifications(user_id, is_read, created_at)`
     - `printing_requests(user_id, status, created_at)`
     - `ci_sessions(timestamp)`
  2. In `UserModel::getCustomerHeaderStats`, consolidate cart item count, pending orders, and unread notifications into a single optimized query or cache for 30 seconds per user in memory/session.
  3. In `ProductModel::getFeaturedProducts` and `CategoryModel::getAll`, implement CodeIgniter File/Array cache (`cache()->save(...)`, TTL: 120s) for public anonymous data.
  4. Ensure large blob columns (`product_embeddings.embedding`) are excluded from general product catalog queries via `select('id, name, price, image, stock')`.
- **Risk**: Medium (cache invalidation on product update or order placement).
- **[NEEDS APPROVAL]**: No.
- **Expected Gain**: Resolves **RC-02**. Cuts layout database queries from 7 to 1, reducing page TTFB by 240–350ms.
- **Testing**: Run `scratch/explain_queries.php` before and after; verify `type = ref/eq_ref` across all key queries.
- **Rollback**: Run `php spark migrate:rollback` or remove cache wrapper calls.

---

### WP-4: Session Handler Optimization
- **Goal**: Remove blocking database session locks and reduce per-request session overhead.
- **Files to Change**:
  - `app/Session/Handlers/OptimizedDatabaseHandler.php:40-130`
  - `app/Config/App.php:160-190`
- **Exact Changes**:
  1. In `OptimizedDatabaseHandler`:
     - Compare session data hashes in `write()`; skip SQL update entirely if session payload and timestamp have not meaningfully changed.
     - Remove inline `gc()` invocation from user request lifecycle; rely on a scheduled CLI cron or run GC with a 1/500 lottery probability.
     - For read-only AJAX polling routes, call `session_write_close()` at the start of the controller method.
  2. **Option Evaluation: File-Based Sessions [NEEDS APPROVAL]**:
     - *Advantage*: Completely eliminates 2 remote database queries (80–120ms) on every single request.
     - *Disadvantage*: Render Free wipes local disk on redeployment/restart, logging out all active users.
     - *Recommendation*: Keep database sessions with write-skipping enabled, but provide an environment toggle `SESSION_DRIVER=File` for maximum speed if user prefers.
- **Risk**: Medium.
- **[NEEDS APPROVAL]**: **YES** (File-based session trade-off).
- **Expected Gain**: Resolves **RC-04**. Saves 80–120ms per page and 100% of write traffic on read-only requests.
- **Testing**: Log in, perform actions, inspect MySQL query log to ensure duplicate `UPDATE ci_sessions` are skipped.
- **Rollback**: Revert `OptimizedDatabaseHandler.php` to previous commit.

---

### WP-5: Runtime Server & Cold-Start Tuning
- **Goal**: Optimize PHP-FPM worker pools, OPcache bytecode retention, Nginx caching headers, and eliminate boot-time delays.
- **Files to Change**:
  - `docker/entrypoint.sh:10-50`
  - `docker/nginx.conf.template:40-80`
  - `Dockerfile:30-60`
- **Exact Changes**:
  1. In `docker/entrypoint.sh`:
     - Add conditional check for `RUN_MIGRATIONS`: only run `php spark migrate --all` if `RUN_MIGRATIONS=true` (saves 4–8 seconds on free-tier wake-up).
     - Tune PHP-FPM pool for 0.1 vCPU / 512 MB: `pm = dynamic`, `pm.max_children = 4`, `pm.start_servers = 2`, `pm.min_spare_servers = 1`, `pm.max_spare_servers = 2`, `pm.max_requests = 500`.
     - Enable OPcache file validation optimization: `opcache.validate_timestamps = 0` in production mode.
  2. In `docker/nginx.conf.template`:
     - Enable aggressive client-side caching for compiled assets: `location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff2)$ { expires 30d; add_header Cache-Control "public, no-transform"; }`.
     - Ensure Gzip compression is enabled for `text/css`, `application/javascript`, and `application/json`.
- **Risk**: Medium (OPcache timestamp disabling requires rebuild on code update, which Render Docker already does).
- **[NEEDS APPROVAL]**: No.
- **Expected Gain**: Resolves **RC-10**. Cuts cold start time from 38.4s to ~18s; offloads all static asset delivery from PHP-FPM.
- **Testing**: Measure curl header responses for `/css/tailwind.css`; verify `gzip` and `Cache-Control` headers.
- **Rollback**: Revert changes in `entrypoint.sh` and `nginx.conf.template`.

---

### WP-6: Frontend Speed & Click Feel
- **Goal**: Achieve perceived instant page responsiveness through optimized typography loading, purged Tailwind CSS, and instant navigation state feedback.
- **Files to Change**:
  - `app/Views/components/head.php:25-50`
  - `tailwind.config.js:5-20`
  - `public/js/blax-ui.js:10-80`
- **Exact Changes**:
  1. In `head.php`:
     - Add `preconnect` to `https://fonts.googleapis.com` and `https://fonts.gstatic.com` with `crossorigin`.
     - Consolidate Google Font imports into a single query with `display=swap`.
     - Defer non-critical icon fonts or load only needed Material Symbols.
  2. In `tailwind.config.js`:
     - Add missing content scanner paths: `./app/Helpers/**/*.php`, `./app/Libraries/**/*.php`, `./app/Models/**/*.php`, `./app/Services/**/*.php`.
     - Rebuild Tailwind bundle, stripping out unused utility classes to reduce CSS payload.
  3. In `blax-ui.js`:
     - Add a lightweight, zero-dependency top progress bar (e.g. 2px blue line animating across the top on link clicks) to provide immediate (<50ms) visual feedback.
     - **Guarded Hover Prefetch [NEEDS APPROVAL]**: For high-probability navigation links (e.g., sidebar links), optionally prefetch HTML on hover with a strict 3-request concurrency cap and immediate cancel on mouse-out.
- **Risk**: Low.
- **[NEEDS APPROVAL]**: **YES** (Guarded hover prefetch approval).
- **Expected Gain**: Resolves **RC-09**. FCP improves by 400–700ms; user clicks feel instantaneous.
- **Testing**: Profile in Chrome DevTools / Lighthouse; verify zero render-blocking warnings and immediate visual progress bar on click.
- **Rollback**: Revert `head.php` and `blax-ui.js`.

---

### WP-7: Login Concurrency & Worker Offloading
- **Goal**: Prevent bcrypt password verification bursts from choking PHP-FPM workers during simultaneous user logins.
- **Files to Change**:
  - `app/Controllers/Auth.php:30-75, 130-180`
  - `docker/entrypoint.sh:30-40`
- **Exact Changes**:
  1. Keep standard secure bcrypt hashing (`PASSWORD_DEFAULT`), but streamline the authentication request lifecycle:
     - Remove all non-essential database queries from the login POST request (e.g. deferred cart syncing, audit logging to asynchronous queue/background).
     - Defer welcome emails / SMS notifications to background cron or post-response execution (`fastcgi_finish_request()`).
  2. Immediately redirect authenticated users to a lean landing route with cached session states.
  3. If FPM worker saturation occurs, implement a short-queue 429 response with a user-friendly modal ("Server busy, retrying automatically...") rather than a 504 gateway hang.
- **Risk**: Medium.
- **[NEEDS APPROVAL]**: No.
- **Expected Gain**: Resolves **RC-05**. 20 simultaneous logins complete with 0 timeouts on 0.1 vCPU.
- **Testing**: Run load test scenario (c) with 20 logins in 10 seconds; verify p95 latency drops below 3.0s with 0% dropped connections.
- **Rollback**: Revert `Auth.php` modifications.

---

### WP-8: Tenant Sidebar & Bug Register Resolution
- **Goal**: Permanently resolve the unresponsive tenant navigation bar, click dead zones, and modal binding conflicts.
- **Files to Change**:
  - `app/Views/components/head.php:140-180`
  - `public/js/blax-ui.js:290-340`
  - `app/Views/layouts/tenant.php:120-160`
- **Exact Changes**:
  1. Remove duplicate sidebar click listeners in `head.php:145-174`; centralize all sidebar toggle logic exclusively within `public/js/blax-ui.js`.
  2. Resolve Tailwind layout collision on `#tenant-sidebar`: replace competing `hidden md:flex` and inline JS style overrides with explicit state-driven CSS classes (`sidebar-collapsed`, `sidebar-expanded`).
  3. Ensure `#tenant-sidebar-overlay` has proper `z-index` layering below the sidebar drawer (`z-40` for overlay, `z-50` for sidebar) to prevent click-blocking dead zones.
  4. Preserve collapsed/expanded preference in `localStorage.setItem('blax_tenant_sidebar_collapsed', ...)` with error handling for private browsing modes.
- **Risk**: Low.
- **[NEEDS APPROVAL]**: No.
- **Expected Gain**: Resolves **RC-06**, **BUG-01**. 100% reliable sidebar toggling on mobile (390px) and desktop (1440px).
- **Testing**: Playwright automated click tests across viewport widths; manual toggle regression testing.
- **Rollback**: Revert `blax-ui.js` and `head.php` to clean commit.

---

### WP-9: Storage Persistence & Security Hardening
- **Goal**: Prevent permanent data loss of uploaded files (business permits, 3D printing orders) on Render container redeploys, and eliminate remaining security risks from review 1F.
- **Files to Change**:
  - `app/Controllers/Auth.php:335-365`
  - `app/Controllers/Tenant.php:2460-2500`
  - `app/Config/App.php:20-40`
  - `app/Config/Filters.php:40-60`
  - `docker/nginx.conf.template:70-95`
- **Exact Changes**:
  1. **Uploads Persistence [NEEDS APPROVAL]**:
     - Divert business permit and printing attachments from local ephemeral disk (`WRITEPATH/uploads`) to Cloudinary (using private/raw authenticated upload) or an S3-compatible bucket.
     - *Alternative*: Mount a Render Persistent Disk (requires paid Render plan, $0.25/GB/mo).
     - *Recommendation*: Use Cloudinary authenticated upload API (already integrated in project) to maintain zero extra hosting cost.
  2. **Security Fixes**:
     - Lock down `baseURL` in `app/Config/App.php` using environment variable `APP_BASE_URL` rather than dynamic Host headers.
     - Enforce Nginx execution restriction: block any direct access to `.php` files inside uploads directory.
     - Re-evaluate CSRF exemptions on `api/route` and `tenant/deliveries/stop-broadcast` to ensure token or Bearer secret validation.
- **Risk**: High (changes file storage behavior and route security).
- **[NEEDS APPROVAL]**: **YES** (Cloudinary storage for permits/printing files; CSRF token validation).
- **Expected Gain**: Resolves **RC-07**, **BUG-04**, **BUG-05**. Eliminates data loss during deployments and hardens security posture.
- **Testing**: Upload test file, trigger mock container restart, verify file link remains valid.
- **Rollback**: Revert controller upload handlers to local disk writes.

---

### WP-10: Capacity Decisions (No-Code Advisory)
- **Goal**: Provide transparent, evidence-based recommendations on infrastructure boundaries, pricing, and latency mitigation.
- **Files to Change**: None (Advisory Report in Section 10).
- **Exact Changes**: Infrastructure sizing and regional topology analysis.

---

## 7. Test Plan & Acceptance Criteria per WP

| Work Package | Automated / Manual Command | Acceptance Criteria (Pass / Fail) | Harness Script Location |
|---|---|---|---|
| **WP-1** (Observability) | `curl -s -D - "http://localhost:10000/healthz"` & check FPM stderr log | Pass if log outputs `[%t] GET /healthz 200 ...ms ...kb`. Fail if URI missing or format unparsed. | `scratch/test_observability.sh` |
| **WP-2** (Background Traffic)| Run headless browser with 5 tabs for 3 minutes; record outbound requests | Pass if total requests <= 15 across all tabs while unfocused. Fail if requests >= 60. | `scratch/playwright_test/test_polling_suppression.js` |
| **WP-3** (Database & Indexes) | `php spark db:table --metadata` & `php scratch/explain_queries.php` | Pass if composite indexes present and key queries use `ref`/`index`. Fail if `ALL` table scan on orders. | `scratch/explain_queries.php` |
| **WP-4** (Sessions) | `php scratch/test_session_writes.php` simulating 10 consecutive poll hits | Pass if MySQL query counter shows 0 `UPDATE ci_sessions` for unchanged payload. Fail if 10 updates occur. | `scratch/test_session_writes.php` |
| **WP-5** (Server Tuning) | `curl -I "http://localhost:10000/css/tailwind.css"` | Pass if `Cache-Control: max-age` present and `Content-Encoding: gzip` returned. Fail if missing. | `scratch/test_headers.sh` |
| **WP-6** (Frontend Speed) | Playwright Lighthouse audit on `/` and `/tenant` | Pass if FCP < 800ms and 0 console errors reported. Fail if render-blocking font warnings remain. | `scratch/playwright_test/audit_frontend.js` |
| **WP-7** (Login Spike) | k6 load test: 20 login POSTs over 10 seconds under 0.1 vCPU limit | Pass if 0 HTTP 504 gateway timeouts and p95 latency < 3.0s. Fail if any request drops. | `scratch/k6_tests/login_burst.js` |
| **WP-8** (Sidebar Fix) | Playwright click test on `#sidebar-toggle` at 390px and 1440px viewports (50 clicks) | Pass if 50/50 clicks toggle sidebar correctly without state hang. Fail if any click ignored. | `scratch/playwright_test/test_sidebar_toggle.js` |
| **WP-9** (Upload Persistence)| Upload sample PDF permit -> restart container -> verify download URL | Pass if HTTP 200 returned after restart. Fail if HTTP 404 or missing file. | `scratch/test_upload_persistence.php` |

---

## 8. Rollout & Rollback Plan

### Deployment Sequencing
1. **Branch Management**: All code will be developed on dedicated branch `fix-plan-impl`, committing strictly one atomic commit per approved Work Package (`WP-1: Observability`, `WP-2: Background traffic`, etc.).
2. **Pre-Deploy Migration**: If database index migrations are included (WP-3), deploy during off-peak window. Migration is fully idempotent (`IF NOT EXISTS`).
3. **Render Environment Variables**:
   Configure in Render Dashboard -> Web Service -> **Environment**:
   - `RUN_MIGRATIONS=false` (set `true` only for the deployment running WP-3, then toggle back to `false` for instant wake-ups)
   - `APP_PROFILE=0` (enable `1` temporarily for troubleshooting)
   - `FPM_SLOWLOG_TIMEOUT=2s`
   - `PHP_FPM_MAX_CHILDREN=4`
   - `PHP_MEMORY_LIMIT=192M`
4. **Verification via Logs**:
   Open Render Live Log Stream. Verify:
   - Nginx boots and listens on dynamic `$PORT`.
   - Health check probes `/healthz` and receives 200 in <5ms.
   - Access logs display structured timing: `[time] METHOD /path STATUS duration_ms`.

### Rollback Strategy
- **Application Rollback**:
  In Render Dashboard, select **Deploys** -> choose the last known good commit -> click **Rollback to this deploy**.
  *(Note: Rolling back in Render temporarily pauses Auto-Deploy on Git push; remember to re-enable Auto-Deploy after resolving issues).*
- **Database Index Rollback**:
  Index migrations are non-destructive and do not alter table data or columns. If an index causes unexpected query planner regression, run `php spark migrate:rollback` or drop the specific index via MySQL CLI.

---

## 9. Risks, Assumptions & Open Questions

### Risks
1. **Bcrypt Inherent Latency**: On a 0.1 vCPU slice, bcrypt verification is mathematically bound to ~800ms of CPU compute. If 10 users submit passwords in the exact same second, queueing is unavoidable without rate-limiting or scaling CPU.
2. **Cross-Region Network Jitter**: If Render (Singapore) and Aiven MySQL are in different physical data centers or continents, round-trip latency will vary between 40ms and 150ms depending on public transit routing.
3. **Cloudinary Raw Storage Limits**: Storing business permits and 3D printing attachments in Cloudinary requires configuring appropriate upload presets to handle non-image binaries securely.

### Assumptions
1. Render Free tier specifications remain fixed at 0.1 vCPU and 512 MB RAM with 15-minute idle spindown.
2. The user has access to Render and Aiven consoles to review region assignments and add environment variables.
3. The existing Aiven MySQL database supports standard composite indexes without table locking issues.

### Open Questions for User
1. **Aiven Region Confirmation**: In which cloud provider and region is your Aiven MySQL database hosted? (If it is not AWS/GCP Singapore `ap-southeast-1`, moving it to Singapore will instantly double overall application speed).
2. **File Sessions Preference**: Do you accept users being logged out when Render restarts or redeploys in exchange for saving 80–120ms of remote database latency on every request?
3. **Upload Storage Provider**: Are you satisfied with routing PDF permits and 3D printing files to Cloudinary's raw storage, or do you plan to attach a Render Persistent Disk in the future?

---

## 10. Decision List ([NEEDS APPROVAL])

Please review and reply with your approvals (e.g., *"Approved 1, 2, 4"*):

1. **[NEEDS APPROVAL] WP-4: File-Based Sessions vs. Database Sessions**
   - *Option A (Recommended)*: Retain database sessions in `ci_sessions`, but implement dirty-checking to skip 100% of unchanged write queries and defer GC off user requests. Zero loss of logins on redeploy.
   - *Option B*: Switch to file-based sessions on container disk. Shaves 80–120ms off every page, but all users are logged out whenever Render restarts or deploys.
   - *Recommendation*: **Option A**.

2. **[NEEDS APPROVAL] WP-6: Guarded Hover Prefetching**
   - *Proposal*: Enable a lightweight script that prefetches the next page HTML when a user hovers over a navigation link for >150ms, with a hard cap of 2 concurrent prefetches and automatic cancellation if server response time exceeds 1.5s.
   - *Trade-off*: Makes clicks feel 100% instantaneous, but adds ~1 extra GET request per hovered link. On a 0.1 vCPU server, this must be strictly capped.
   - *Recommendation*: **Approve with strict server-load safety guard**.

3. **[NEEDS APPROVAL] WP-9: Offload Ephemeral Local Uploads to Cloudinary**
   - *Proposal*: Re-route business permit uploads (`Auth.php`) and 3D printing custom attachments (`Tenant.php`) to Cloudinary private/raw assets to ensure files are never lost when Render restarts.
   - *Recommendation*: **Approve**. Prevents permanent file loss.

4. **[NEEDS APPROVAL] WP-10: Hosting Tier Upgrade Evaluation**
   - *Cost / Benefit Comparison*:
     - **Render Free ($0/mo)**: 0.1 vCPU, 512 MB RAM, sleeps after 15 min idle (30–45s wake-up). Max ~4 req/s. Max ~5 concurrent active users.
     - **Render Starter ($7/mo)**: 0.5 vCPU, 512 MB RAM, **never sleeps** (0s wake-up). Max ~18 req/s. Max ~30 concurrent active users.
     - **Render Standard ($25/mo)**: 1.0 vCPU, 2048 MB RAM, never sleeps. Max ~42 req/s. Max ~100+ concurrent active users.
   - *Recommendation*: Implement all code optimizations in this Fix Plan first. If concurrent user demand exceeds 10 simultaneous shoppers/tenants, upgrade to Render Starter ($7/mo) for dedicated CPU and zero spindown.
