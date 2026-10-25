# Blax Marketplace: Performance & Scalability Plan

**Status:** WP-0, WP-A, WP-B, WP-C, WP-D, and WP-E implemented and verified. Ready for baseline measurement and production deployment.
**Stack:** CodeIgniter 4, PHP 8.3, Nginx + PHP-FPM on Render Free (region `singapore`), Aiven MySQL over the internet.
**Rule used throughout:** numbers are either *counted from code* (labelled), *estimates* (labelled), or **TO MEASURE**. Nothing here is a measurement.

> This file replaces an earlier draft of `docs/PERF_PLAN.md` (recoverable from git). The old draft contained claims the code contradicts. They are listed in section 0.1 so they are not re-introduced.

---

## 0. Audit notes

### 0.1 Where my verification differs from the brief or the earlier draft

| # | Claim | What the code shows |
|---|---|---|
| 1 | "No `scratch/` folder" | `scratch/` exists locally (16 ad-hoc scripts) but is **git-ignored** (`.gitignore:178`, 0 tracked files). None are benchmarks. There is no k6, toxiproxy, EXPLAIN or Playwright script, so every number in `FIX_PLAN.md` stays **unverified**. |
| 2 | "`Customer::realtimeCheck` has no fast path" | A fast path exists (`Customer.php:978-997`, compares order/print signatures and notif id). The problem is that **4 queries run before it is reached** (`:948-966`), and the "unchanged" reply still ships both active lists. |
| 3 | "`Tenant::realtimeCheck` runs 2x `selectMax` + unread + recent before the fast path" | It is 3x `selectMax` (orders, printing, notifications: `:584-593`). `getRecent(6)` is *after* the fast path (`:622`). The bigger cost is `getShopOrRedirect()` (`:565`, defined `:42-83`), which adds **3 more queries** (shop `find`, unread count, `getRecent(10)`) to every tenant request, including polls. Idle tenant poll = **6 queries** (counted from code). |
| 4 | Suggested change detection via `max(updated_at)` on orders | `orders` and `printing_requests` have **no `updated_at` column** in the schema dump (`blax_marketplace.sql:460-481`, `:648-669`), and `OrderModel` / `PrintingRequestModel` do not set `useTimestamps`. The dump is a local, untracked file and may be older than production (its `orders.status` enum even lacks `in_transit`, which the code uses). Confirm on the live Aiven schema. The plan below avoids needing `updated_at`. |
| 5 | Earlier draft: "views render raw image URLs without Cloudinary transforms" | Wrong. `product_image_url()` routes remote URLs through `CloudinaryService::transformUrl()` (`app/Helpers/status_helper.php:14-47`; presets documented as card 480px, thumbnail 160px, detail 960px). |
| 6 | Earlier draft: replace `RAND(seed)` with `ORDER BY (id ^ seed)` | Any expression ordering still requires reading and sorting every row. Only pre-computation removes the sort (see WP-C). |
| 7 | Earlier draft: concurrency figures ("20-30 users" etc.) and a rollback commit hash | Invented. Removed. |

### 0.2 Files reviewed for this plan
`Controllers/{Customer,Tenant,Admin,Home,Cart}.php`, `Models/{ProductModel,OrderModel,NotificationModel,UserModel}.php`, `Libraries/SeasonalAiService.php`, `Helpers/status_helper.php`, `Filters/PerformanceFilter.php`, `Config/{Filters,Database,Cache,Session}.php`, `Session/Handlers/OptimizedDatabaseHandler.php`, `Views/layouts/{marketplace,tenant,admin}.php`, `docker/{entrypoint.sh,nginx.conf.template}`, `render.yaml`, both docs, `README.md`, migrations list, `blax_marketplace.sql` (schema only).

---

## 1. Status matrix

### 1.1 The 12 gap items

| # | Item | Status | Evidence |
|---|---|---|---|
| 1 | No baseline measurement | **Missing** | `scratch/` is ignored and contains no benchmark harness. `PerformanceFilter` logs time, memory and query *count* only, **not SQL time** (`Filters/PerformanceFilter.php:41-65`). |
| 2 | `Customer::realtimeCheck` | **Partial** | Fast path at `Customer.php:978-997`. Queries before it: max notif id `:948`, unread `:950`, active orders `:952`, active printing `:960`. Changed path adds `getCustomerHeaderStats` (`:1009`) whose `unread_count` duplicates `getUnreadCount` (`:950`), plus `getRecent(6)` (`:1010`). |
| 3 | `Tenant::realtimeCheck` | **Partial** | Fast path `Tenant.php:597-620`. Before it: `getShopOrRedirect` (3 queries, `:68`, `:73-74`) + 3x `selectMax` (`:584,587,592`). Unread count is request-cached (`NotificationModel.php:24,35`), so not duplicated. |
| 4 | Customer layout queries on every page | **Missing** | `layouts/marketplace.php:17-27`: `getCustomerHeaderStats` + `getRecent(6)` for every logged-in page. |
| 5 | `Tenant::dashboard` monolithic | **Missing** | `Tenant.php:85-169`. `getSeasonalAnalysis` **computation is not cached**: it loads every product of the shop (`SeasonalAiService.php:432-439`) plus a 60-day `order_items` aggregate (`:443-451`) and scores every product in PHP (`:468-574`) on each dashboard load. Only the Cohere result is cached (`cohere_seasonal_v1_{shop}_{season}`, 24h, `:673-767`). The dashboard also calls `getRecent($userId)` (`:128`) although `getShopOrRedirect` already fetched `getRecent(…,10)`: a different limit means a second query (cache key is `user:limit`, `NotificationModel.php:44`). |
| 6 | Catalog query | **Missing** | `ProductModel.php:157-212`: `products.*` (includes `description` TEXT), correlated `variant_count` subquery (`:158`), 3 LEFT JOINs, `RAND(seed)` (`:208`), `LIKE %x%` on name and description (`:170-172`). Only page 1 of the default landing is cached (`Home.php:29-47,100-109`, 60s). |
| 7 | Unbounded `findAll()` | **Partial** | Grep shows ~68 `findAll(` call sites under `app/` (the brief counted 59; difference is counting method). Hot-path classification in WP-D. |
| 8 | Images | **Partial** | Cloudinary transforms: done. Product cards in `home.php:469`, `search_results.php:85`, `category_products.php:63` have `loading="lazy"`. Missing lazy on `shops.php:161,195`, `home.php:196,260,294`, avatars in `components/marketplace_header.php:167,179,260`, `profile_sidebar.php:49`. No `<img>` seen has `width`/`height`/`decoding`. Whether shop covers use a transform preset: **to verify**. |
| 9 | Very large views | **Partial** | Sizes confirmed (`shop_storefront.php` 113,594 B; `cart.php` 72,907; `printing_requests.php` 92,765; `orders.php` 66,750). But `shop_storefront.php` has only 2 hidden modals (`:193`, `:959`) and 1 `<script>` block (`:997`), so "hidden modals" is not the whole story. Composition must be measured before refactoring. |
| 10 | Render Free cold start | **Missing (infra)** | `entrypoint.sh:82-93` (nginx -t, php-fpm -t) and `:119-140` (migrations, default `true`). `render.yaml` does not set `RUN_MIGRATIONS`. |
| 11 | Aiven vs Render region | **Unknown** | `render.yaml:6` is `singapore`. No Aiven region in the repo or docs. **Needs your answer.** |
| 12 | Docs drift | **Missing** | See WP-H. |

### 1.2 Items you listed as already implemented: verification

| Claim | Verdict | Evidence / caveat |
|---|---|---|
| Polling in `marketplace.php` and `tenant.php` uses `setTimeout`, pauses when hidden, cancels on `pagehide` | **Done** | `marketplace.php:534-571`, `tenant.php:1066-1079`. **Caveat:** customer idle back-off grows to 45s then 60s (`marketplace.php:538-543`), which exceeds your "within ~30s" badge requirement (Open Question 2). |
| `admin.php` layout poll | **Done** | `admin.php:732-761` (`setTimeout`, 25s, visibility + pagehide). |
| Remaining `setInterval` that hit the network | **Confirmed** | `admin/tracking.php:544` (15s), `customer/orders/track.php:837` (12s). `customer/home.php:693` (1s countdown) and `tenant/settings.php:2285` (60s UI clock) make **no network calls**; leave alone. |
| Session handler skips unchanged writes (5 min) and empty guest sessions | **Done** | `OptimizedDatabaseHandler.php:116-126`. Note it also removes session locking (`:36-49`), see section 7. |
| `pConnect` on Render, `DBDebug=false` in production | **Done** | `Config/Database.php:217-231` (`RENDER`/`RENDER_SERVICE_ID` env → `pConnect=true`). |
| Performance indexes migrations | **Done** | `2026-09-25-000001`, `2026-10-24-000001`. **Gap:** no `notifications(user_id, id)`; the existing index is `(user_id, is_read, created_at)`, where `id` is not a usable suffix for `MAX(id) WHERE user_id=?`. Verify with EXPLAIN. |
| `Home::index` paginated + 60s landing cache; `getAllKeyMap`, `getAllCached` cached | **Done** | `Home.php:27-47,100-109`; `SiteContentModel.php:138-159` (600s); `CategoryModel.php:53-63` (600s). The Home payload has **no explicit invalidation**, TTL only. |
| `getCustomerHeaderStats` combines 3 counts | **Done** | `UserModel.php:82-103`. Inconsistency: its active-order statuses (`:90`) omit `in_transit`, which `realtimeCheck` includes (`Customer.php:955`). |
| `Tenant::realtimeCheck` fast path | **Done / Partial** | See item 3. |
| `entrypoint.sh` FPM/OPcache tuning + `RUN_MIGRATIONS` | **Done** | `entrypoint.sh:17-77,119`. |
| `APP_PROFILE=1` enables `PerformanceFilter` | **Done** | `Filters.php:79,85`; `PerformanceFilter.php:22-33`. Lacks SQL time (WP-0). |

---

## 2. Measurement plan

### 2.1 What exists
1. `APP_PROFILE=1` (set in the Render dashboard; not in `render.yaml`): one stderr line per request:
   `[APP_PROFILE] GET /path 200 | Time: X ms | Peak Mem: Y MB | SQL Queries: N` (`PerformanceFilter.php:57-65`).
2. FPM access log (`entrypoint.sh:32`): `[ts] METHOD URI status <ms>ms <kb>kb <cpu%>%`. The **CPU% vs wall-ms ratio** separates "CPU-bound" (0.1 vCPU starvation) from "waiting on the remote DB". That tells us which fixes matter.
3. FPM slowlog at 2s (`entrypoint.sh:23,33-34`).
4. Nginx `$request_time` / `$upstream_response_time` logging: **not verified** in `nginx.conf.template`; check before relying on it.

### 2.2 Gaps and proposed tooling [NEEDS APPROVAL: tiny code change]
- **WP-0:** extend `PerformanceFilter::after()` to also log total SQL time and the 3 slowest statements (use `Query::getDuration()` from `$db->getQueries()`; confirm against the installed CodeIgniter version and that `saveQueries` is enabled in production). Opt-in only, zero cost when `APP_PROFILE` is unset. Alternative without code: Aiven console query statistics, if available on your plan (verify).

### 2.3 Procedure
1. Warm the instance (hit `/healthz`), set `APP_PROFILE=1`, redeploy.
2. For each route: 1 cold sample (after >15 min idle) + 10 warm samples; record median and worst.
3. Client TTFB: `curl -s -o NUL -w "%{time_starttransfer} %{size_download}\n" --compressed -b <cookiejar> <url>` from your real location. Log in via the browser to obtain role cookies for customer/tenant/admin (do not reuse the stray `cookies.txt` in the repo root).
4. Read SQL count, time and peak memory from the Render log line matching the request.
5. For pollers, record steady state with one visible tab for 5 minutes, then count requests/min.

### 2.4 Baseline table (fill in; do not estimate)

| Route | Role | TTFB cold | TTFB warm p50 | Worst | SQL count | SQL time | Peak mem | FPM CPU% | HTML bytes (gz) |
|---|---|---|---|---|---|---|---|---|---|
| `/` | guest | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/shops` | guest | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/shop/{slug}` | guest and customer | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/product/{id}` | guest and customer | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/cart` | customer | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/customer/orders` (brief said `/orders`) | customer | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/tenant` | tenant | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/tenant/orders` | tenant | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/tenant/inventory` | tenant | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/admin` | admin | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/customer/realtime/check` (idle, with sigs) | customer | n/a | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/tenant/realtime/check` (idle) | tenant | n/a | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |
| `/admin/realtime/check` (idle) | admin | n/a | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE | TO MEASURE |

### 2.5 Code-derived expectations (to validate the profiler, **not** measurements)
Counts exclude the session read (1 SELECT per request; write skipped within 5 min).

| Path | Queries counted from code |
|---|---|
| Customer poll, nothing changed | 4 (`Customer.php:948,950,952,960`) |
| Customer poll, changed | 4 + stats 1 + new notifs ≤1 + recent 1 = up to 7 |
| Tenant poll, nothing changed | 6 (3 from `getShopOrRedirect` + 3 `selectMax`) |
| Admin poll, nothing changed | 4 (`Admin.php:384,387,391,393`) plus auth cost (unverified) |
| Customer logged-in page, layout only | 2 (`marketplace.php:20,26`) |
| `/tenant` | about 15 (guard 3, KPIs 2, chart 2, orders 1, requests 1, low-stock 2, warning 1, recent notifs 1, seasonal 2) |
| Poll rate per **visible** logged-in tab | 1 per 25s = 2.4/min; 1/min at 60s idle back-off (`marketplace.php:524,538-543`) |

---

## 3. Work packages

Recommended order (gain / risk): **WP-0 → WP-A → WP-G (G1 only) → WP-B → WP-D → WP-C → WP-E → WP-F → WP-H**.
Each WP is one atomic commit so it can be reverted with `git revert`. Re-run the section 2.4 rows touched by a WP before and after it.

### WP-0: Measurement tooling [NEEDS APPROVAL: small code change]
- **Goal:** make SQL time visible so later estimates become measurements.
- **Files:** `app/Filters/PerformanceFilter.php`.
- **Change:** add total SQL time + top-3 slow statements to the log line, gated by `APP_PROFILE`.
- **Effect:** none on users. **Risk:** very low. **Rollback:** revert commit.
- **Test:** `APP_PROFILE=1`, load a page, confirm the new fields; unset it and confirm no output.

### WP-A: Realtime endpoints
**Goal:** idle polls cost one indexed query instead of 4 to 6. Idle tabs are the only load that scales with *open tabs*, not *actions*.

**A1. `Customer::realtimeCheck`** (`Customer.php:925-1030`)
- Replace the four pre-fast-path queries with one statement:
  ```sql
  SELECT
   (SELECT COALESCE(MAX(id),0)  FROM notifications WHERE user_id=?)                AS max_notif_id,
   (SELECT COUNT(*)             FROM notifications WHERE user_id=? AND is_read=0)  AS unread_count,
   (SELECT COALESCE(SUM(CRC32(CONCAT(id,':',status))),0) FROM orders
      WHERE customer_id=? AND status IN ('pending','processing','shipped','in_transit','ready_for_pickup')) AS order_sig,
   (SELECT COALESCE(SUM(CRC32(CONCAT(id,':',status))),0) FROM printing_requests
      WHERE customer_id=? AND status IN ('new','in_production','ready_for_pickup','ready_for_delivery'))   AS print_sig
  ```
  No schema change needed. `orders(customer_id,status)` and `printing_requests(customer_id,status)` indexes already exist and cover `id` (InnoDB appends the PK).
- The signature is opaque to the client (`marketplace.php:416-417,429-430`), so sending it as a string needs no JS change. It now covers *all* active rows, not only the newest 10. This is more correct, and a status change still flips it.
- Unchanged reply: `{success, changed:false, unread_count, max_notif_id, order_sig, print_sig}` without the two arrays. The JS already ignores them when `changed === false` (`marketplace.php:454-456`) and the `blax:customer-realtime` event has **no listeners** in `app/Views` (only the dispatcher at `:436`).
- Changed or first poll: keep the current full payload, but drop the duplicate `getUnreadCount` by taking `unread_count` from the combined row.
- **Expected:** idle poll 4 queries → 1 (counted from code). Payload shrinks (estimate: smaller; measure).

**A2. `Tenant::realtimeCheck`** (`Tenant.php:563-787`)
- Add a private `resolveShopIdForPoll()`: same login/role checks as `getShopOrRedirect` (`:45-52`), reads `shop_id` from session, **skips** the shop `find`, unread count and `getRecent(10)` (`:68,73-80`). Fall back to `getShopOrRedirect` only if the session has no `shop_id`.
- Replace the three `selectMax` + unread with one combined statement (max order id for shop, max printing id for shop, max notif id and unread for user).
- **Expected:** idle poll 6 queries → 1 (counted from code).
- **Prerequisite for WP-B:** the new deferred dashboard endpoints must use this light resolver, otherwise each widget request pays the 3-query guard again.

**A3. `Admin::realtimeCheck`** (`Admin.php:364-419`): same combined-query pattern, 4 queries → 1.

**A4. Remaining network `setInterval`s:** `admin/tracking.php:544` (15s) and `customer/orders/track.php:837` (12s): convert to a self-scheduling `setTimeout` that pauses when hidden. Behavior otherwise identical. Backing endpoints (`Admin::trackingPins`, `getDeliveryPosition`) unchanged.

**A5. Decision (Open Question 2):** the 45s/60s idle back-off violates the 30s badge requirement. Options: cap at 30s (keeps the requirement, slightly more polls) or accept the current back-off.

- **Cache:** none. **Index (optional) [NEEDS APPROVAL]:** `notifications(user_id, id)` via a new migration, non-destructive. Verify with `EXPLAIN` first.
- **Risk:** low. Main risk is a missed badge or toast: test status transitions, new order and new notification end to end.
- **Rollback:** `git revert`. **Test:** change an order status from the tenant side and confirm the customer toast and badge within one poll interval. Confirm an idle tab produces `changed:false` with 1 query in the profiler. Confirm `session_write_close()` is still reached before the combined query.

### WP-B: Deferred widget loading
**Goal:** first paint needs shell + KPIs only; secondary widgets load after paint, without multiplying DB work.

**B1. `Tenant::dashboard`** (`Tenant.php:85-169`, view `tenant/dashboard.php`)
- Keep server-side: guard, `getShopDashboardKpis` (2 queries), recent orders (8), recent requests (8).
- Defer: sales chart (the endpoint `dashboardSalesData` already exists at `:175`; the view currently gets `chart_*` vars at `:150-159`), low-stock list + count (`ProductModel.php:259-284`), seasonal spotlight, compliance warning (`:121-126`).
- **One deferred request for low-stock + spotlight + warning** (not three). On 0.1 vCPU and 4 FPM workers, three parallel XHRs would contend with the user's own navigation. The chart loads separately when visible (IntersectionObserver).
- New endpoint: `GET tenant/dashboard/widgets`, tenant-auth, returns an HTML fragment (reuses current markup, so no visual change) using the light resolver from A2.
- Remove the duplicate `getRecent($userId)` at `:128` (reuse what the guard already loaded).
- Skeleton placeholders with fixed heights to avoid layout shift.
- **Expected:** initial `/tenant` ≈15 → ≈7 queries (counted from code; estimate for the result). The deferred call costs about 5, but off the critical path.

**B2. Cache `SeasonalAiService` computation** (`SeasonalAiService.php:423-621`)
- Only for the dashboard path (`getDashboardSpotlight`, `:630`, which already passes `allowExternalAi=false`). Bypass the cache for `forceRefresh` and for the full seasonal analysis page (`Tenant.php:1904,1953`, which labels results `is_realtime`).
- Key `seasonal_calc_v1_{shopId}_{seasonKey}`, TTL 300s. Clear via a new `SeasonalAiService::forgetShop($shopId)` that deletes every season key (same loop shape as the existing cohere clear at `:409`).
- Explicit clear events: product create/update/delete/stock edit in `Tenant`, POS sale, order placement or cancellation that changes stock (`CheckoutProcessorTrait`), order status → cancelled. Because stock mutations are spread over many sites, the 300s TTL is the safety net and must stay.
- Also trim the product select (it loads `description`, used only for keyword match) only if profiling shows memory pressure.

**B3. Sales chart queries:** `OrderModel.php:697-698` uses `COALESCE(completed_at, placed_at) >= ?`, which is not index-sargable, so each call scans the shop's orders. Do **not** change until WP-0 shows its SQL time. The endpoint is already browser-cacheable (`Tenant.php:196`, `max-age=30`).

**B4. Customer layout** (`layouts/marketplace.php:17-27`, `components/marketplace_header.php`)
- Keep **one** query server-side (`getCustomerHeaderStats`) for badges. Remove `getRecent(6)` (`:25-26`).
- Load the dropdown on first bell click from a new `GET customer/notifications/recent` (JSON, `getRecent(…,6)`), memoized in JS for 30s. The poll's changed payload already carries `notifications` (`Customer.php:1024`), which can refresh an open dropdown.
- Do **not** cache header stats server-side: cart changes, order status and notification inserts would all need explicit clears.
- **Expected:** 1 query fewer per logged-in page.

**B5. Tenant layout:** `getShopOrRedirect` loads `getRecent(…,10)` on every tenant page (`:74`). The first poll (`tenant.php:1066`, 1.5s) returns a full payload including `notifications` (`Tenant.php:781`), and `updateNotificationsUI` already renders it. So the server-rendered dropdown can be dropped with almost no new code. 1 query fewer per tenant page.

**B6. Other heavy pages (decide after measurement)**
- `/cart`: shipping quotes are computed per shop in the request (`Cart.php:73-75`, `GoogleMapsService` with a 24h *file* cache that is cold after every restart, so a cold cart can make external HTTP calls on the critical path). Candidate: render the cart first, fetch quotes after paint. `Cart::index` also runs a `DELETE` on every GET (`:38-41`).
- `/shop/{slug}` (`Customer.php:145-210`): about 14 or more queries. Reviews, paper sizes, printing settings and calendar schedules are only needed when their section or modal is opened (`pdf-upload-modal` at `shop_storefront.php:959`).
- `/product/{id}`: a **write per page view** (`UPDATE categories SET view_count` at `Customer.php:264`). Candidate: batch or sample the counter. Behavior change (counter accuracy): needs your approval.

- **Risk:** low to medium (skeleton and layout shift). **Rollback:** revert. **Test:** throttle to Slow 4G; KPIs paint first; widgets fill in; bell click shows notifications; dashboard numbers match before and after.

### WP-D: Unbounded `findAll()` on hot paths
Classification of the call sites reviewed. The remaining ~40 in `Tenant.php` and other models are in the worklist at the end and must be classified before any change.

| Site | Class | Action |
|---|---|---|
| `Customer::realtimeCheck` active lists (`:952-966`), new notifs (`:1002-1006`) | Safe (`limit 10 / 5`) | Replaced by A1 in the idle case |
| `Customer::orders` → `getOrdersByCustomer` (`OrderModel.php:36-44`, `Customer.php:379`) | **Needs bound** | Loads **every order ever**, then drops old delivered/completed/cancelled ones in PHP (`Customer.php:439-450`). Push the 2-day filter into SQL (`WHERE status NOT IN (...) OR COALESCE(completed_at, cancelled_at, placed_at) >= cutoff`). **Same output, no UI change, no pagination needed.** |
| `Customer::orders` items (`:389`) and images (`:405-409`) | **Needs bound** | Images query fetches *every* image row of every product ever ordered. Bound by the filtered order ids; select primary image only. |
| `Customer::printingRequests` (`:612-641`) | **To review** | Same pattern; attachments `whereIn` at `:641`. |
| `Customer::search` categories (`:235`) | Safe, cacheable | Use `CategoryModel::getAllCached()` (`:53`). 1 query fewer per search page. |
| `Customer::product` images / variants (`:273-280`) | Safe (one product) | None |
| `Tenant::realtimeCheck` new orders / printing (`:626-634`, `:716-723`) and `Admin::realtimeCheck` (`:425-430`) | **Unbounded after long idle** | After hours of inactivity every missed order becomes one payload and one toast each. Add `limit(20)` + `has_more`. Mild behavior change (toast cap): **[NEEDS APPROVAL]**. |
| `CategoryModel:60`, `SiteContentModel` (several), `ShopBusinessHourModel:31`, `ShopPaperSizeSettingModel:43,80`, `NotificationModel:51` | Safe (small or limited, several cached) | None |
| `ReviewModel:59,82`, `ReviewService:243,267`, `ShopModel:64`, `ShopCalendarScheduleModel:131,356`, `ProductVariantModel:33`, `PrintingRequestAttachmentModel:28` | **To review** | Confirm each has a limit or a bounded key |

**Worklist (unclassified):** `Tenant.php` lines 377, 385, 499, 634, 723, 829, 917, 1049, 2727, 2781, 3680, 4683, 4811, 5118, 5326, 5921, 5954, 5974, 5984, 6017, 6185; `Customer.php:554`; `CustomerOrderController.php:92`; `CheckoutProcessorTrait.php:346`; plus ~9 further sites the 50-result grep cap hid.
- **Risk:** low. **Rollback:** revert. **Test:** a customer with many old orders sees the identical list; memory and payload drop (measure).

### WP-C: Catalog query (`ProductModel::getGlobalProductsPaginated`, `ProductModel.php:149-220`)
**Why it scales badly:** the default sort has no index, so every uncached request reads all matching rows, evaluates `RAND(seed)`, sorts them (rows are wide: `products.*` includes TEXT), then discards all but 20 to 24. Cost grows with catalog size. Page 2+ and every filtered view hit this each time.

Proposed, smallest first:
- **C1. Explicit column list.** Replace `products.*` with only what product cards use. First grep the card views for fields actually used. Drop `description` unless a card shows it.
- **C2. Two-step fetch (deferred join).** Step 1: select only `products.id` in order with filters and `LIMIT/OFFSET` (narrow rows). Step 2: hydrate those ≤24 ids with the joins and `variant_count` (`WHERE id IN (...)`, reorder in PHP). The variant count can then be one grouped query on the page's ids rather than a correlated subquery. No schema change.
- **C3. Pre-computed rotation order (removes the sort).** For the default discovery order (no search, no explicit sort): cache the ordered id list for the current slot.
  - Key: `blax_discovery_ids_{slot_seed}_{category_id|all}`; TTL = `seconds_remaining` of the rotation slot (`ProductModel::getRotationInfo`, `:109-140`), so it expires at the slot boundary.
  - Computed once per slot per category, by the **same** `RAND(seed)` query as today, so the visible order is unchanged.
  - Cap at about 1000 ids; deeper pages fall back to the current query.
  - Pagination total = list length.
  - Correctness: hydration (C2) re-applies `deleted_at IS NULL`, so a stale list can only shorten a page, never leak a deleted product. New products appear at the next slot or on invalidation.
  - Invalidation: a version key `blax_catalog_ver` included in the key, bumped from the product create/update/delete/archive paths and shop approve/suspend paths in `Tenant`/`Admin`. A version bump is used because file cache cannot delete by wildcard. Stock changes do **not** bump it.
- **C4. Search:** `LIKE '%q%'` on `name OR description` cannot use an index. FULLTEXT (`MATCH…AGAINST`) is the real fix but changes matching semantics (token length, stopwords): **[NEEDS APPROVAL]**. No change proposed until WP-0 shows search SQL time.
- **C5. Other sorts** (`price`, `name`, `rating`, `newest`): ordered by plain columns; evaluate indexes only if EXPLAIN shows filesort on a large catalog.
- Home payload cache (`blax_home_default_payload`, 60s) stays; additionally clear it on the same version bump.
- **Expected:** discovery pages 2+ become one `IN` lookup instead of a full sort (estimate). Query count unchanged or +1; time per request lower. Measure.
- **Risk:** medium (rotation behavior, invalidation sites). **Rollback:** revert; the old `RAND` path remains as fallback. **Test:** compare id order for a given slot before and after (must match); paginate pages 1-3 with no duplicates; delete a product and confirm it disappears within one request; `EXPLAIN` before and after.

### WP-E: Image delivery
- Cloudinary transforms already exist; do not rebuild them.
- Add `loading="lazy"`, `decoding="async"` and explicit `width`/`height` (or `aspect-ratio`) to the images listed in item 8. Keep the first visible row eager.
- Verify that shop cover/logo URLs (`$coverUrl`, `logo_url()`) go through a Cloudinary preset (`shops.php:161`).
- **Effect:** fewer off-screen image requests and less layout shift. Unmeasured; label as estimate.
- **Risk:** very low. **Rollback:** revert. **Test:** Network tab on `/shops` and `/`: off-screen images load on scroll; no layout jump.

### WP-F: Oversized views (gated on measurement)
- Server cost of a big view is mostly echo loops (templates are OPcache-compiled, `validate_timestamps=0`); the main cost is **client parse and DOM size**. Treat as lower priority.
- First step is measurement only: gzip bytes per page, DOM node count, and the share of bytes per section (header, list, modal, `<script>`).
- Candidates if justified: move the PDF-upload modal and its scripts (`shop_storefront.php:959,997`) to a lazily fetched fragment; move large inline JS to cached static files under `public/js/`.
- **Risk:** low to medium (modal init). **Rollback:** revert.

### WP-G: Infrastructure options (advisory; nothing changes without approval)
1. **Aiven region vs Render Singapore: [NEEDS APPROVAL / needs your answer].** If they differ, every query pays a long round trip, and it is likely the largest single latency factor. Cost of fixing: move or recreate the DB in the same region (data migration, downtime, possible plan-price change). Confirm the Aiven plan tier too.
2. **`RUN_MIGRATIONS=false` after first deploy [NEEDS APPROVAL].** The flag is supported (`entrypoint.sh:119`). Saves the migration step and its retry loop on each wake (**TO MEASURE**: log time from "Starting container" to the first 200 on `/healthz`). Trade-off: new migrations then need a deliberate step (set `true` for that one deploy, then back), and Render Free may not offer a shell (confirm in your dashboard). WP-A's optional index migration must be applied this way.
3. **Cold start / sleep: infra, not code.** Options, no assumed fix:
   - (a) Accept it.
   - (b) An external pinger on `/healthz`: $0, but it consumes the monthly free instance-hour allowance and is only as reliable as the pinger.
   - (c) A paid plan (no spin-down): Render Starter was listed at about $7/mo with 0.5 vCPU / 512 MB when this was written. **Confirm current specs and price on Render's pricing page.**
4. **Memory vs worker count:** `pm.max_children=4` × `memory_limit=192M` (`entrypoint.sh:18,45`) is 768 MB theoretical against a 512 MB container. `memory_limit` is a cap, not typical use. Validate with measured peak memory (WP-0) before changing.
5. **Sessions on the remote DB:** each request pays at least one WAN SELECT for the session. Alternatives (Redis/Valkey, file sessions) change cost, persistence or login retention on redeploy: **[NEEDS APPROVAL]**, not proposed now.
6. **Cache backend:** `Config/Cache.php:25` is `file` (ephemeral disk, per instance). Fine on a single instance. If the service is ever scaled to more than one instance, cache invalidation will not propagate: a shared cache would then be required **[NEEDS APPROVAL]**.

### WP-H: Docs cleanup (separate commit, docs only)
- `docs/FIX_PLAN.md`: mark WP-1 done (`:143`); reflect that realtime fast paths landed (commit `8bb30e5`); correct the stale 5-6s polling claims (now 25s with back-off); mark all baseline and capacity tables **unverified**; remove references to `scratch/explain_queries.php`, `k6_tests`, `playwright_test` (none exist and `scratch/` is ignored).
- `README.md` "Security Notes" (`:129-139`): CSRF filter is enabled globally (`Filters.php:81`, with an exception list); PayMongo signature header is handled in `Cart.php:590` and `Admin.php:1567`; no password bypass found (no "bypass" match in `Controllers/`); a token model exists (`PasswordResetTokenModel`); the `ANTIGRAVITY_PROMPT.md` it references is not in the repo; PHP version (8.1+ vs Docker 8.3); project name (Inventory vs Marketplace); the "do not deploy" banner contradicts the live Render deployment.
- Do not mix into any performance commit.

---

## 4. Cache invalidation matrix

**Existing caches** (backend: `file`)

| Key | TTL | Cleared by | Gap |
|---|---|---|---|
| `blax_home_default_payload` (`Home.php:32,100`) | 60s | nothing (TTL only) | Up to 60s stale after catalog changes. Acceptable; add version bump in WP-C. |
| `blax_all_categories_sorted` (`CategoryModel.php:53,63`) | 600s | `CategoryModel.php:36` | OK |
| `blax_site_contents_*`, `blax_platform_deduction_percent`, `blax_shipping_fee_settings` (`SiteContentModel.php:66-69,91-262`) | 600s | `SiteContentModel.php:66-69` | OK |
| `cohere_seasonal_v1_{shop}_{season}` (`SeasonalAiService.php:673,767`) | 24h | `:409`, `:676` (force refresh) | OK |
| `ShopModel` cache (`ShopModel.php:95-109`) | 300s | not reviewed | Confirm key and clearing |
| Google Maps quotes (`GoogleMapsService.php:163-182`) | 24h | n/a | Cold after every restart |

**Proposed caches**

| Key | Data | TTL | Cleared when |
|---|---|---|---|
| `seasonal_calc_v1_{shopId}_{seasonKey}` | Dashboard seasonal computation | 300s | `SeasonalAiService::forgetShop` on product/stock/order-cancel events; TTL is the safety net |
| `blax_catalog_ver` | Catalog version counter | none | Bumped on product create/update/delete/archive and shop approve/suspend |
| `blax_discovery_ids_{slot_seed}_{cat\|all}` | Ordered ids for the current rotation slot | until slot end | Slot rollover or `blax_catalog_ver` bump |
| *(none)* | Customer header stats | not cached on purpose | Too many invalidating events (cart, orders, notifications) |

---

## 5. Capacity expectations

**No capacity numbers are given.** Nothing has been measured, and the earlier figures were invented. What can be stated from code and constants:

- **Hard ceiling:** `pm.max_children=4` (`entrypoint.sh:18`). Max throughput ≈ 4 / (average request seconds), whether the worker is computing or waiting on the DB. Cutting query count helps only when requests are wait-bound; cutting CPU work helps only when CPU-bound. The FPM access log's CPU% against wall ms (section 2.1) shows which one applies.
- **Background load per visible logged-in tab:** 2.4 requests/min at 25s, 1/min at the 60s idle back-off (`marketplace.php:524,538-543`). Each is currently 4 to 6 queries, so 10 visible tabs ≈ 24 polls/min ≈ 100 to 140 queries/min before any user action (counted from code).
- **Free vs paid:** the plan difference is CPU (0.1 vs 0.5 vCPU as published), no sleep, and possibly more RAM on higher tiers. Re-derive "how many concurrent users" only after the section 2.4 table is filled: users ≈ (4 / avg service time) × think time, using measured service times.

---

## 6. Open questions (max 5)

1. **Aiven:** which cloud and region is the MySQL service in, and which plan tier? (Same-region as Render Singapore or not is the first thing to settle.)
2. **Poll cadence:** the customer poll backs off to 45s then 60s when idle (`marketplace.php:538-543`), which breaks your "~30s" requirement. Cap at 30s, or keep the back-off?
3. **Index migration:** may WP-A add `notifications(user_id, id)` (non-destructive, applied at deploy)? And do you want the `limit(20)` + `has_more` cap on missed-order payloads (WP-D)?
4. **Images:** are all product and shop images on Cloudinary in production, or are some still under `public/uploads` (ephemeral on Render, and `product_image_url` silently falls back to a stock photo when the file is missing)?
5. **Cold start:** accept, use an external pinger, or consider a paid plan?

---

## 7. Out-of-scope findings (not touched in this plan)

- **Possible visibility gap in the public catalog.** `getGlobalProductsPaginated` filters only `products.deleted_at IS NULL` (`ProductModel.php:162`). It does not filter `products.status = 'active'`, and shops are LEFT JOINed with no shop-status filter (`:159`), although the docblock says "active products". Draft or archived products, or products of suspended shops, may be listed. Verify against intended behavior.
- **Writes on GET:** `Cart::index` deletes expired items (`Cart.php:38-41`); `Customer::product` increments `categories.view_count` (`Customer.php:264`).
- **Status mismatch:** header "active orders" omits `in_transit` (`UserModel.php:90`) but the poll includes it (`Customer.php:955`); the schema dump's `orders.status` enum has no `in_transit` (`blax_marketplace.sql:475`). Check the live schema and strict-mode behavior.
- **Session locking removed** (`OptimizedDatabaseHandler.php:36-49`): concurrent requests can overwrite each other's session changes (flash data, CSRF token, cart-related keys). It is a deliberate speed trade-off; note only.
- **CSRF exemptions** include `api/route` and `tenant/deliveries/stop-broadcast` (`Filters.php:81`): confirm each has an alternative authentication check.
- **Repository hygiene:** `approval_test.php` and `builds` are tracked at the repo root; `.env`, `env`, `cookies.txt` and the SQL dumps are untracked (good), but the dumps are local and may be stale.
- Webhook signature-verification logic (`Cart.php:590`, `Admin.php:1567`) was not reviewed, since payment flows are out of scope.
