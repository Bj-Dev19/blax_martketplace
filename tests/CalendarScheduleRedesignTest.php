<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ShopCalendarScheduleModel;

class CalendarScheduleRedesignTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function tearDown(): void
    {
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    public function testModelUpsertDateRangeAndOperationalStatus(): void
    {
        $model = new ShopCalendarScheduleModel();
        $model->ensureSchema();

        $shopId = 1;
        $startDate = '2026-11-20';
        $endDate = '2026-11-22';

        // 1. Test upsert range
        $res = $model->upsertScheduleDateRange($shopId, $startDate, $endDate, true, 'Renovation Closure', 'amber', null, null);
        $this->assertTrue($res);

        $schedules = $model->getSchedulesForShop($shopId);
        $this->assertArrayHasKey('2026-11-20', $schedules);
        $this->assertArrayHasKey('2026-11-21', $schedules);
        $this->assertArrayHasKey('2026-11-22', $schedules);

        $entry = $schedules['2026-11-20'];
        $this->assertSame(1, (int) $entry['is_closed']);
        $this->assertSame('Renovation Closure', $entry['note']);
        $this->assertSame('amber', $entry['color_theme']);

        // 2. Test operational status on one of the range dates
        $status = $model->getShopOperationalStatus($shopId, '2026-11-21 14:00:00');
        $this->assertTrue($status['is_closed_today']);
        $this->assertFalse($status['is_open']);
        $this->assertSame('Renovation Closure', $status['note']);

        // 3. Test partial time window
        $model->upsertScheduleDate($shopId, '2026-11-25', false, 'Morning Inventory', 'sky', '08:00:00', '12:00:00');
        $statusPartial = $model->getShopOperationalStatus($shopId, '2026-11-25 09:00:00');
        $this->assertTrue($statusPartial['is_open']);
        $this->assertSame('08:00:00', $statusPartial['open_time']);
        $this->assertSame('12:00:00', $statusPartial['close_time']);

        // 4. Test delete range
        $del = $model->deleteScheduleDateRange($shopId, $startDate, $endDate);
        $this->assertTrue($del);
        $model->deleteScheduleDate($shopId, '2026-11-25');

        $after = $model->getSchedulesForShop($shopId);
        $this->assertArrayNotHasKey('2026-11-20', $after);
        $this->assertArrayNotHasKey('2026-11-21', $after);
        $this->assertArrayNotHasKey('2026-11-22', $after);
        $this->assertArrayNotHasKey('2026-11-25', $after);
    }

    public function testSettingsViewContainsMonthlyClosureCalendarAndOperationalPlanner(): void
    {
        $viewFile = APPPATH . 'Views/tenant/settings.php';
        $this->assertFileExists($viewFile);
        $source = (string) file_get_contents($viewFile);

        // 1. Dual badges & heading from user screenshot
        $this->assertStringContainsString('2026 OPERATIONAL PLANNER', $source);
        $this->assertStringContainsString('MONTH VIEW', $source);
        $this->assertStringContainsString('Monthly Closure &amp; Schedule Notes Calendar', $source);
        $this->assertStringContainsString('Past dates are locked', $source);

        // 2. Toolbar & View elements
        $this->assertStringContainsString('calViewBtn_month', $source);
        $this->assertStringContainsString('calViewBtn_week', $source);
        $this->assertStringContainsString('calViewBtn_list', $source);
        $this->assertStringContainsString('Schedule Date', $source);
        $this->assertStringContainsString('storeScheduleCalendarGrid', $source);
        $this->assertStringContainsString('calMonthScheduledCount', $source);
        $this->assertStringContainsString('Past Days (Locked)', $source);

        // 3. Customer Visibility Mode Card
        $this->assertStringContainsString('visCardSafe', $source);
        $this->assertStringContainsString('visCardHide', $source);
        $this->assertStringContainsString('Best for Sales', $source);

        // 4. 7-Day Weekly Recurring Operating Hours
        $this->assertStringContainsString('Weekly Recurring Operating Hours', $source);
        $this->assertStringContainsString('weeklyTotalHoursDisplay', $source);
        $this->assertStringContainsString('dayCardWrapper_', $source);
        $this->assertStringContainsString('calCopyMondayToWeekdays', $source);

        // 5. Hero Modal
        $this->assertStringContainsString('calHeroInputModal', $source);
        $this->assertStringContainsString('calHeroTitleInput', $source);
        $this->assertStringContainsString('calHeroSaveBtn', $source);

        // 6. Right-Side Sticky Navigation Bar
        $this->assertStringContainsString('On this page', $source);
        $this->assertStringContainsString('hoursSectionVisibility', $source);
        $this->assertStringContainsString('hoursSectionCalendar', $source);
        $this->assertStringContainsString('hoursSectionWeekly', $source);
        $this->assertStringContainsString('scrollToHoursSection', $source);
    }
}
