# End-to-End (E2E) Browser Tests

Automated Playwright tests verifying critical UI flows and navigation reliability.

## 1. Tenant Sidebar Test (`tenant_sidebar_test.js`)

Verifies that the tenant navigation sidebar:
- Opens, collapses, and expands cleanly on desktop (1440px).
- Opens and closes via drawer overlay on mobile (390px).
- Navigates reliably to all 10 merchant hub pages without throwing errors or breaking layout:
  1. Dashboard (`/tenant/dashboard`)
  2. Point of Sale (`/tenant/pos`)
  3. Orders (`/tenant/orders`)
  4. Printing Requests (`/tenant/printing`)
  5. Delivery Dispatch (`/tenant/deliveries`)
  6. Inventory (`/tenant/inventory`)
  7. Archive (`/tenant/archive`)
  8. Analytics (`/tenant/analytics`)
  9. Transfer & Payouts (`/tenant/withdrawals`)
  10. Store Settings (`/tenant/settings`)

### Running the Test

```bash
# 1. Ensure test server is running
php spark serve

# 2. Run the test
node tests/e2e/tenant_sidebar_test.js

# Custom URL override (e.g. live Render site)
TEST_BASE_URL=https://blax-martketplace.onrender.com node tests/e2e/tenant_sidebar_test.js
```
