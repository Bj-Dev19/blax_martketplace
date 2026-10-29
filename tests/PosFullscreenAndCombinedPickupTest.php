<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PrintingRequestModel;
use App\Models\PaymentModel;
use App\Models\AuditLogModel;

require_once __DIR__ . '/support/PosPickupFixtures.php';

class PosFullscreenAndCombinedPickupTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use PosPickupFixtures;

    protected function tearDown(): void
    {
        $this->posCleanup();
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    private function asTenant(int $shopId = 1): self
    {
        $hash = csrf_hash();
        $_COOKIE['csrf_cookie_name'] = $hash;

        return $this->withSession([
            'user_id'    => 2,
            'user_role'  => 'shop_owner',
            'shop_id'    => $shopId,
            'isLoggedIn' => true,
        ])->withHeaders([
            'X-CSRF-TOKEN' => $hash,
        ]);
    }

    /**
     * Test 1: Fullscreen mode on POS vs standard layout on other tenant pages.
     */
    public function testPosPageUsesFullscreenLayoutWhileOtherTenantPagesUseStandardLayout()
    {
        // 1. Check tenant/pos page
        $res = $this->asTenant(1)->get('tenant/pos');
        $res->assertOK();
        $posBody = $res->response()->getBody();

        // Fullscreen here means: same hub shell, but the content is not put in the
        // centred max-w-7xl reading column, and the sidebar boots collapsed so the
        // register owns the width (layouts/tenant.php $fullscreenLayout / $isPos).
        $this->assertStringContainsString('id="tenant-sidebar"', $posBody);
        $this->assertStringContainsString('<div class="p-2 sm:p-3 md:p-4 w-full">', $posBody);
        $this->assertStringNotContainsString('max-w-7xl mx-auto space-y-6 w-full', $posBody);
        $this->assertStringContainsString('const isPos = true;', $posBody);

        \Config\Services::resetSingle('renderer');

        // 2. Check standard tenant page (e.g., tenant/orders)
        $ordersRes = $this->asTenant(1)->get('tenant/orders');
        $ordersRes->assertOK();
        $ordersBody = $ordersRes->response()->getBody();

        // Standard pages keep the reading column and an expanded sidebar.
        $this->assertStringContainsString('max-w-7xl mx-auto space-y-6 w-full', $ordersBody);
        $this->assertStringNotContainsString('<div class="p-2 sm:p-3 md:p-4 w-full">', $ordersBody);
        $this->assertStringContainsString('const isPos = false;', $ordersBody);
        $this->assertStringContainsString('md:ml-64', $ordersBody);
        // Sidebar toggle should be hidden on desktop (md:hidden)
        $this->assertStringContainsString('md:hidden', $ordersBody);
    }

    /**
     * Test 2: Top header contains the 3 enlarged touch-friendly primary action buttons.
     */
    public function testTopHeaderHasEnlargedTouchButtonsAndBadges()
    {
        $res = $this->asTenant(1)->get('tenant/pos');
        $res->assertOK();
        $body = $res->response()->getBody();

        // Verify "Select Ready for Pick-up" (Order) button
        $this->assertStringContainsString('Ready Pick-up', $body);
        $this->assertStringContainsString('openPickupSelectorModal()', $body);

        // Verify "Printing Ready for Pick-up" button
        $this->assertStringContainsString('Ready Printing', $body);
        $this->assertStringContainsString('openPrintingSelectorModal()', $body);

        // Verify "Scan Customer QR" button
        $this->assertStringContainsString('Scan QR', $body);
        $this->assertStringContainsString('openQrScannerModal()', $body);

        // Verify touch-friendly height classes
        $this->assertStringContainsString('min-h-[48px]', $body);
    }

    /**
     * Test 3: Modals cross-reference customer when both order and printing request are ready.
     */
    public function testCombinedPickupCrossReferenceInModals()
    {
        $orderModel = new OrderModel();
        $prModel = new PrintingRequestModel();

        $uniq = time() . rand(1000, 9999);
        $customerId = 4; // Distinct customer
        $shopId = 1;

        $ordNum = 'ORD-CROSS-' . $uniq;
        $prNum = 'PR-CROSS-' . $uniq;

        // Insert ready product order (anchored at QUEUE_ANCHOR: the selectors only
        // render the oldest 50 queued rows, so a "now" fixture would be cut off)
        $orderId = $orderModel->insert([
            'order_number'       => $ordNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'fulfillment_method' => 'pickup',
            'payment_method'     => 'cod',
            'subtotal'           => 150.00,
            'total_amount'       => 150.00,
            'status'             => 'ready_for_pickup',
            'payment_status'     => 'unpaid',
            'placed_at'          => self::QUEUE_ANCHOR,
        ]);
        $this->posTrack('orders', $orderId);

        // Insert ready printing request
        $prId = $prModel->insert([
            'request_number'     => $prNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'file_name'          => 'Dual_Thesis_' . $uniq . '.pdf',
            'file_url'           => 'uploads/printing/dual.pdf',
            'document_type'      => 'pdf',
            'fulfillment_method' => 'pickup',
            'status'             => 'ready_for_pickup',
            'total_price'        => 80.00,
            'down_payment'       => 40.00,
            'created_at'         => self::QUEUE_ANCHOR,
        ]);
        $this->posTrack('printing_requests', $prId);

        $res = $this->asTenant(1)->get('tenant/pos');
        $res->assertOK();
        $body = $res->response()->getBody();

        // Cross-reference must appear in BOTH modals: one linked-pair chip on the
        // product card and one on the printing card.
        $this->assertGreaterThanOrEqual(
            2,
            substr_count($body, 'Linked pair'),
            'Both the product order card and the printing card must carry the linked-pair chip'
        );
        $this->assertStringContainsString('Dual_Thesis_' . $uniq . '.pdf', $body, 'Product card must surface the linked printing job');
        $this->assertStringContainsString('Load Both to POS', $body);
        $this->assertStringContainsString('order_id=' . $orderId, $body);
        $this->assertStringContainsString('printing_id=' . $prId, $body);
    }

    /**
     * Test 4: POS simultaneously loads both product order and printing request.
     */
    public function testCombinedPickupLoadsBothInPosSimultaneously()
    {
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $prModel = new PrintingRequestModel();

        $uniq = time() . rand(1000, 9999);
        $customerId = 5;
        $shopId = 1;

        $ordNum = 'ORD-COMBLOAD-' . $uniq;
        $prNum = 'PR-COMBLOAD-' . $uniq;

        $orderId = $orderModel->insert([
            'order_number'       => $ordNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'fulfillment_method' => 'pickup',
            'payment_method'     => 'gcash',
            'subtotal'           => 200.00,
            'total_amount'       => 200.00,
            'status'             => 'ready_for_pickup',
            'payment_status'     => 'paid', // Paid online
            'placed_at'          => date('Y-m-d H:i:s'),
        ]);

        $orderItemModel->insert([
            'order_id'     => $orderId,
            'product_id'   => 1,
            'product_name' => 'Highlighter Set ' . $uniq,
            'quantity'     => 2,
            'unit_price'   => 100.00,
            'line_total'   => 200.00,
        ]);
        $this->posTrack('orders', $orderId);

        $prId = $prModel->insert([
            'request_number'     => $prNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'file_name'          => 'Lab_Manual_' . $uniq . '.pdf',
            'file_url'           => 'uploads/printing/lab.pdf',
            'document_type'      => 'pdf',
            'fulfillment_method' => 'pickup',
            'status'             => 'ready_for_pickup',
            'total_price'        => 90.00,
            'down_payment'       => 45.00, // 45 remaining
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
        $this->posTrack('printing_requests', $prId);

        // Load both into POS
        $res = $this->asTenant(1)->get('tenant/pos?order_id=' . $orderId . '&printing_id=' . $prId);
        $res->assertOK();
        $body = $res->response()->getBody();

        // Check combined master banner
        $this->assertStringContainsString('Combined Pick-up POS', $body);
        $this->assertStringContainsString('Combined Pick-up Summary', $body);
        $this->assertStringContainsString('in a single checkout without double-charging', $body);

        // Check both order and printing reference badges
        $this->assertStringContainsString($ordNum, $body);
        $this->assertStringContainsString($prNum, $body);
        $this->assertStringContainsString('Highlighter Set ' . $uniq, $body);
        $this->assertStringContainsString('Lab_Manual_' . $uniq . '.pdf', $body);

        // Check combined settlement summary
        $this->assertStringContainsString('Product Order Due:', $body);
        $this->assertStringContainsString('Printing Balance Due:', $body);
        $this->assertStringContainsString('Combined Amount to Collect:', $body);

        // Check redesigned pick-up fulfillment card
        $this->assertStringContainsString('Hand Over:', $body);
        $this->assertStringContainsString('PAID ONLINE', $body);
        $this->assertStringContainsString('Exit', $body);
    }

    /**
     * Test 5: posCompletePickup executes combined fulfillment atomically.
     */
    public function testCombinedPickupCheckoutAtomicProcessing()
    {
        $orderModel = new OrderModel();
        $prModel = new PrintingRequestModel();
        $paymentModel = new PaymentModel();
        $auditModel = new AuditLogModel();

        $uniq = time() . rand(1000, 9999);
        $customerId = 6;
        $shopId = 1;

        $ordNum = 'ORD-ATOMIC-' . $uniq;
        $prNum = 'PR-ATOMIC-' . $uniq;

        // Order is unpaid counter cash: 100.00 due
        $orderId = $orderModel->insert([
            'order_number'       => $ordNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'fulfillment_method' => 'pickup',
            'payment_method'     => 'cod',
            'subtotal'           => 100.00,
            'total_amount'       => 100.00,
            'status'             => 'ready_for_pickup',
            'payment_status'     => 'unpaid',
            'placed_at'          => date('Y-m-d H:i:s'),
        ]);
        $this->posTrack('orders', $orderId);

        // Printing request has 30.00 remaining balance
        $prId = $prModel->insert([
            'request_number'     => $prNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'file_name'          => 'Project_Report_' . $uniq . '.pdf',
            'file_url'           => 'uploads/printing/proj.pdf',
            'document_type'      => 'pdf',
            'fulfillment_method' => 'pickup',
            'status'             => 'ready_for_pickup',
            'total_price'        => 60.00,
            'down_payment'       => 30.00,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
        $this->posTrack('printing_requests', $prId);

        // Simulate 30.00 down payment already verified online
        $this->posTrack('payments', $paymentModel->insert([
            'payable_type'     => 'printing_request',
            'payable_id'       => $prId,
            'method'           => 'gcash',
            'amount'           => 30.00,
            'reference_number' => 'ref_online_down_' . $uniq,
            'status'           => 'verified',
            'processed_at'     => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s'),
        ]));

        // Complete combined pickup via POS
        $completeRes = $this->asTenant(1)->post('tenant/pos/complete-pickup', [
            'order_id'               => $orderId,
            'printing_id'            => $prId,
            'items'                  => json_encode([]),
            'counter_payment_method' => 'cash',
        ]);

        $completeRes->assertOK();
        $json = json_decode($completeRes->response()->getBody(), true);

        $this->assertTrue($json['success']);
        $this->assertEquals('combined', $json['type']);
        $this->assertEquals($orderId, $json['order_id']);
        $this->assertEquals($prId, $json['printing_id']);
        // Due collected: 100.00 (order) + 30.00 (printing balance) = 130.00
        $this->assertEquals(130.00, (float) $json['total_collected']);

        // Verify order completed
        $updatedOrder = $orderModel->find($orderId);
        $this->assertEquals('completed', $updatedOrder['status']);
        $this->assertEquals('paid', $updatedOrder['payment_status']);

        // Verify printing request completed
        $updatedPr = $prModel->find($prId);
        $this->assertEquals('completed', $updatedPr['status']);
        $this->assertEquals(100, (int) $updatedPr['progress_percent']);

        // Verify payments created for remaining balances
        $orderPayments = $paymentModel->where('payable_type', 'order')->where('payable_id', $orderId)->findAll();
        $this->assertNotEmpty($orderPayments);
        $this->assertEquals(100.00, (float) $orderPayments[0]['amount']);

        $prPayments = $paymentModel->where('payable_type', 'printing_request')->where('payable_id', $prId)->findAll();
        $this->assertCount(2, $prPayments, 'Must have 1 down payment + 1 counter pickup payment');
        $this->assertEquals(60.00, (float) array_sum(array_column($prPayments, 'amount')));

        // Verify audit logs
        $logs = $auditModel->where('actor_id', 2)->orderBy('id', 'DESC')->limit(5)->findAll();
        $logTargets = array_column($logs, 'target_type');
        $this->assertTrue(in_array('order', $logTargets));
        $this->assertTrue(in_array('printing_request', $logTargets));
    }

    /**
     * Test 6: Unified combined receipt renders both order and printing request.
     */
    public function testCombinedReceiptRendersBothOrderAndPrintingDetails()
    {
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $prModel = new PrintingRequestModel();

        $uniq = time() . rand(1000, 9999);
        $customerId = 7;
        $shopId = 1;

        $ordNum = 'ORD-RCPT-' . $uniq;
        $prNum = 'PR-RCPT-' . $uniq;

        $orderId = $orderModel->insert([
            'order_number'       => $ordNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'fulfillment_method' => 'pickup',
            'payment_method'     => 'cod',
            'subtotal'           => 125.00,
            'total_amount'       => 125.00,
            'status'             => 'completed',
            'payment_status'     => 'paid',
            'placed_at'          => date('Y-m-d H:i:s'),
        ]);
        $this->posTrack('orders', $orderId);

        $orderItemModel->insert([
            'order_id'     => $orderId,
            'product_id'   => 1,
            'product_name' => 'Ballpen Box ' . $uniq,
            'quantity'     => 1,
            'unit_price'   => 125.00,
            'line_total'   => 125.00,
        ]);

        $orderItemModel->insert([
            'order_id'        => $orderId,
            'product_id'      => 2,
            'product_name'    => 'Sticky Notes ' . $uniq,
            'quantity'        => 2,
            'unit_price'      => 25.00,
            'line_total'      => 50.00,
            'is_pos_addition' => 1,
        ]);

        $prId = $prModel->insert([
            'request_number'     => $prNum,
            'customer_id'        => $customerId,
            'shop_id'            => $shopId,
            'file_name'          => 'Reviewer_Set_' . $uniq . '.pdf',
            'file_url'           => 'uploads/printing/rev.pdf',
            'document_type'      => 'pdf',
            'fulfillment_method' => 'pickup',
            'status'             => 'completed',
            'total_price'        => 50.00,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
        $this->posTrack('printing_requests', $prId);

        // 1. Load receipt from order endpoint with ?printing_id
        $rcptRes = $this->asTenant(1)->get('tenant/orders/receipt/' . $orderId . '?printing_id=' . $prId);
        $rcptRes->assertOK();
        $body = $rcptRes->response()->getBody();

        $this->assertStringContainsString('Combined Pick-up & Printing Receipt', $body);
        $this->assertStringContainsString($ordNum, $body);
        $this->assertStringContainsString($prNum, $body);
        $this->assertStringContainsString('Ballpen Box ' . $uniq, $body);
        $this->assertStringContainsString('Sticky Notes ' . $uniq, $body);
        $this->assertStringContainsString('+ In-Store Addition', $body);
        $this->assertStringContainsString('Reviewer_Set_' . $uniq . '.pdf', $body);

        // 2. Load receipt from printing endpoint with ?order_id
        $prRcptRes = $this->asTenant(1)->get('tenant/printing/receipt/' . $prId . '?order_id=' . $orderId);
        $prRcptRes->assertOK();
        $prBody = $prRcptRes->response()->getBody();

        $this->assertStringContainsString('Combined Pick-up & Printing Receipt', $prBody);
        $this->assertStringContainsString($ordNum, $prBody);
        $this->assertStringContainsString($prNum, $prBody);
        $this->assertStringContainsString('Ballpen Box ' . $uniq, $prBody);
        $this->assertStringContainsString('Reviewer_Set_' . $uniq . '.pdf', $prBody);
    }
}
