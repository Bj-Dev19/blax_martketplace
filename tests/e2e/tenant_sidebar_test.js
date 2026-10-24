let chromium;
try {
    chromium = require('playwright').chromium;
} catch (e) {
    try {
        const fallback = require.resolve('playwright', {
            paths: [
                process.env.PLAYWRIGHT_PATH || '',
                'C:/Users/Gwapo/.gemini/antigravity-ide/brain/28c9ac83-7876-4124-aeed-7d6b6d11e7bf/scratch/playwright_test/node_modules'
            ]
        });
        chromium = require(fallback).chromium;
    } catch (err) {
        console.error('Playwright not found. Install via `npm install -D playwright` or set PLAYWRIGHT_PATH.');
        process.exit(1);
    }
}

const BASE_URL = process.env.TEST_BASE_URL || 'http://localhost/blax_inventory/public';

const LINKS_TO_TEST = [
    { name: 'Dashboard', path: '/tenant/dashboard', expectedHeading: /Dashboard|Overview|Store/i },
    { name: 'Point of Sale (POS)', path: '/tenant/pos', expectedHeading: /POS|Point of Sale|Cashier/i },
    { name: 'Orders', path: '/tenant/orders', expectedHeading: /Orders|Product Orders/i },
    { name: 'Printing Requests', path: '/tenant/printing', expectedHeading: /Printing|Print/i },
    { name: 'Delivery Dispatch', path: '/tenant/deliveries', expectedHeading: /Deliveries|Dispatch/i },
    { name: 'Inventory', path: '/tenant/inventory', expectedHeading: /Inventory|Catalog|Products/i },
    { name: 'Archive', path: '/tenant/archive', expectedHeading: /Archive|Archived/i },
    { name: 'Analytics', path: '/tenant/analytics', expectedHeading: /Analytics|Forecasting/i },
    { name: 'Transfer & Payouts', path: '/tenant/withdrawals', expectedHeading: /Payouts|Withdrawals|Transfer/i },
    { name: 'Store Settings', path: '/tenant/settings', expectedHeading: /Settings|Profile/i }
];

async function runTenantSidebarSuite() {
    console.log(`Starting Tenant Sidebar E2E Suite against ${BASE_URL}...`);
    const browser = await chromium.launch({ headless: true });

    // Scenarios: Desktop Expanded, Desktop Collapsed, Mobile Drawer
    const scenarios = [
        { name: 'Desktop (1440px) - Expanded', viewport: { width: 1440, height: 900 }, collapsed: false, isMobile: false },
        { name: 'Desktop (1440px) - Collapsed', viewport: { width: 1440, height: 900 }, collapsed: true, isMobile: false },
        { name: 'Mobile (390px) - Drawer Mode', viewport: { width: 390, height: 844 }, collapsed: false, isMobile: true }
    ];

    let totalPassed = 0;
    let totalFailed = 0;

    for (const sc of scenarios) {
        console.log(`\n=== Running Scenario: ${sc.name} ===`);
        const context = await browser.newContext({ viewport: sc.viewport });
        const page = await context.newPage();

        // 1. Authenticate as merchant
        await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
        await page.click('#tab-merchant');
        await page.fill('input[name="email"]', 'paolo@printpixel.ph');
        await page.fill('input[name="password"]', '12345678');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
            page.click('button[type="submit"]')
        ]);

        // Set initial sidebar collapse state if desktop
        if (!sc.isMobile) {
            await page.evaluate((val) => {
                localStorage.setItem('blax_tenant_sidebar_collapsed', val ? 'true' : 'false');
            }, sc.collapsed);
            await page.reload({ waitUntil: 'domcontentloaded' });
        }

        // Test each navigation link
        for (const item of LINKS_TO_TEST) {
            process.stdout.write(`  Testing ${item.name}... `);
            const t0 = Date.now();

            if (sc.isMobile) {
                // Ensure mobile drawer is opened
                const isSidebarVisible = await page.evaluate(() => {
                    const sb = document.getElementById('tenant-sidebar');
                    return sb && !sb.classList.contains('hidden');
                });
                if (!isSidebarVisible) {
                    await page.click('#sidebar-toggle');
                    await page.waitForTimeout(200);
                }
            }

            const linkSelector = `#tenant-sidebar a.nav-item[href*="${item.path}"]`;
            const link = page.locator(linkSelector).first();
            await link.waitFor({ state: 'attached', timeout: 5000 });

            const [response] = await Promise.all([
                page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 }),
                link.click()
            ]);

            const elapsed = Date.now() - t0;
            const status = response ? response.status() : 0;
            const currentUrl = page.url();

            if (status === 200 && currentUrl.includes(item.path)) {
                console.log(`PASS (${status}, ${elapsed}ms)`);
                totalPassed++;
            } else {
                console.log(`FAIL (${status}, url: ${currentUrl})`);
                totalFailed++;
            }
        }

        await context.close();
    }

    await browser.close();
    console.log(`\n========================================`);
    console.log(`Suite Summary: ${totalPassed} passed, ${totalFailed} failed.`);
    console.log(`========================================`);

    if (totalFailed > 0) {
        process.exit(1);
    }
}

runTenantSidebarSuite().catch((err) => {
    console.error('Fatal test error:', err);
    process.exit(1);
});
