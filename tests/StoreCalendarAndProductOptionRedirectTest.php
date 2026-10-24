<?php

use App\Models\ShopCalendarScheduleModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

/**
 * @internal
 */
final class StoreCalendarAndProductOptionRedirectTest extends CIUnitTestCase
{
    private int $testShopId = 999881;
    private ?BaseConnection $sqliteDb = null;

    protected function setUp(): void
    {
        parent::setUp();
        ShopCalendarScheduleModel::resetSchemaCheck();

        $this->sqliteDb = Database::connect([
            'DSN'      => '',
            'hostname' => 'localhost',
            'username' => '',
            'password' => '',
            'database' => ':memory:',
            'DBDriver' => 'SQLite3',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8',
            'DBCollat' => '',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3306,
        ], false);

        $this->sqliteDb->query("
            CREATE TABLE IF NOT EXISTS `shops` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `shop_name` TEXT NOT NULL,
                `offers_pickup` INTEGER NOT NULL DEFAULT 1
            )
        ");
        $this->sqliteDb->query("
            CREATE TABLE IF NOT EXISTS `shop_business_hours` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `shop_id` INTEGER NOT NULL,
                `day_of_week` INTEGER NOT NULL,
                `open_time` TEXT NULL,
                `close_time` TEXT NULL,
                `is_closed` INTEGER NOT NULL DEFAULT 0
            )
        ");
        $this->sqliteDb->table('shops')->insert([
            'id'            => $this->testShopId,
            'shop_name'     => 'Polomolok Print & Supply Hub',
            'offers_pickup' => 1,
        ]);
    }

    public function testShopCalendarScheduleModelCrudAndOperationalStatusPriority(): void
    {
        $calModel = new ShopCalendarScheduleModel($this->sqliteDb);

        $this->assertTrue($this->sqliteDb->tableExists('shop_calendar_schedules'));
        $this->assertTrue($this->sqliteDb->fieldExists('hide_when_closed', 'shops'));

        $targetDate = '2026-10-23';

        // 1. Default status when no calendar override exists
        $defaultStatus = $calModel->getShopOperationalStatus($this->testShopId, $targetDate . ' 10:00:00');
        $this->assertFalse($defaultStatus['is_closed_today']);
        $this->assertTrue($defaultStatus['is_open']);

        // 2. Schedule a closed day with a custom note and color
        $saved = $calModel->upsertScheduleDate(
            $this->testShopId,
            $targetDate,
            true,
            'Annual Planning & Inventory Audit',
            'amber'
        );
        $this->assertTrue($saved);

        $schedules = $calModel->getSchedulesForShop($this->testShopId, '2026-10');
        $this->assertArrayHasKey($targetDate, $schedules);
        $this->assertSame(1, $schedules[$targetDate]['is_closed']);
        $this->assertSame('Annual Planning & Inventory Audit', $schedules[$targetDate]['note']);
        $this->assertSame('amber', $schedules[$targetDate]['color_theme']);

        // 3. Operational status on that closed date
        $closedStatus = $calModel->getShopOperationalStatus($this->testShopId, $targetDate . ' 14:00:00');
        $this->assertTrue($closedStatus['is_closed_today']);
        $this->assertFalse($closedStatus['is_open']);
        $this->assertSame('calendar', $closedStatus['source']);
        $this->assertSame('Annual Planning & Inventory Audit', $closedStatus['note']);
        $this->assertStringContainsString('Closed Today', $closedStatus['status_label']);
        $this->assertStringContainsString('Annual Planning & Inventory Audit', $closedStatus['status_label']);

        // 4. Update the same date to Open with a special note ("Budget Setup")
        $calModel->upsertScheduleDate(
            $this->testShopId,
            $targetDate,
            false,
            'Budget Setup',
            'emerald'
        );
        $openNoteStatus = $calModel->getShopOperationalStatus($this->testShopId, $targetDate . ' 14:00:00');
        $this->assertFalse($openNoteStatus['is_closed_today']);
        $this->assertTrue($openNoteStatus['is_open']);
        $this->assertSame('Budget Setup', $openNoteStatus['note']);
        $this->assertSame('emerald', $openNoteStatus['color_theme']);

        // 5. Delete schedule entry
        $this->assertTrue($calModel->deleteScheduleDate($this->testShopId, $targetDate));
        $this->assertArrayNotHasKey($targetDate, $calModel->getSchedulesForShop($this->testShopId));
    }

    public function testSalesSafeVsHideDirectoryVisibilityEnrichment(): void
    {
        $calModel   = new ShopCalendarScheduleModel($this->sqliteDb);
        $targetDate = '2026-10-27';

        $calModel->upsertScheduleDate($this->testShopId, $targetDate, true, 'Town Fiesta Holiday', 'rose');

        $sampleShops = [
            ['id' => $this->testShopId, 'shop_name' => 'Test Holiday Shop', 'hide_when_closed' => 0],
        ];

        // 1. Sales-Safe Mode (default: hide_when_closed = 0 in DB) keeps shop in list with is_closed_today = true & closure_note
        $enriched = $calModel->enrichShopsWithStatus($sampleShops, true, $targetDate . ' 09:00:00');
        $this->assertCount(1, $enriched);
        $this->assertTrue($enriched[0]['is_closed_today']);
        $this->assertSame('Town Fiesta Holiday', $enriched[0]['closure_note']);

        // 2. Hide from Directory Mode (hide_when_closed = 1 in DB) hides closed shop when respectHideWhenClosed is true
        $this->sqliteDb->table('shops')->where('id', $this->testShopId)->update(['hide_when_closed' => 1]);
        $filtered = $calModel->enrichShopsWithStatus($sampleShops, true, $targetDate . ' 09:00:00');
        $this->assertCount(0, $filtered);
    }

    public function testCheckoutAndPrintingControllersEnforceClosedTodayLock(): void
    {
        $traitSource = (string) file_get_contents(APPPATH . 'Traits/CheckoutProcessorTrait.php');
        $this->assertStringContainsString('ShopCalendarScheduleModel', $traitSource);
        $this->assertStringContainsString('is_closed_today', $traitSource);
        $this->assertStringContainsString('cannot accept new orders right now', $traitSource);

        $checkoutSource = (string) file_get_contents(APPPATH . 'Controllers/Checkout.php');
        $this->assertStringContainsString('getShopOperationalStatus', $checkoutSource);
        $this->assertStringContainsString('is_closed_today', $checkoutSource);

        $customerSource = (string) file_get_contents(APPPATH . 'Controllers/Customer.php');
        $this->assertStringContainsString('getShopOperationalStatus', $customerSource);
        $this->assertStringContainsString('cannot accept printing requests right now', $customerSource);
    }

    public function testProductModelSelectsVariantCountAndCardsRedirectToProductOptions(): void
    {
        $productModelSource = (string) file_get_contents(APPPATH . 'Models/ProductModel.php');
        $this->assertGreaterThanOrEqual(
            5,
            substr_count($productModelSource, 'variant_count'),
            'ProductModel catalog/storefront/related queries must select variant_count.'
        );

        // Verify Cart::add redirects to product detail #variants-section when variants exist and no variant_id is given
        $cartSource = (string) file_get_contents(APPPATH . 'Controllers/Cart.php');
        $this->assertStringContainsString("product/' . \$productId . '?select_option=1#variants-section'", $cartSource);
        $this->assertStringContainsString("'redirect_url' => \$detailUrl", $cartSource);

        // Verify customer product card views link directly to ?select_option=1#variants-section when variant_count > 0
        foreach ([
            APPPATH . 'Views/customer/home.php',
            APPPATH . 'Views/customer/shop_storefront.php',
            APPPATH . 'Views/customer/category_products.php',
        ] as $viewFile) {
            $viewContent = (string) file_get_contents($viewFile);
            $this->assertStringContainsString('variant_count', $viewContent);
            $this->assertStringContainsString('?select_option=1#variants-section', $viewContent);
        }
    }
}

