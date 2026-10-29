<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Tenant;

/**
 * Guards the SQL escrow aggregate (Tenant::computeShopEscrowTotals) against the
 * row-by-row PHP algorithm it replaced.
 *
 * The refactor moved money out of the GET_LOCK critical section, and money is the
 * one thing the audit said must not change. The oracle below is a literal
 * re-statement of the pre-refactor loop (fetch every order and printing request of
 * the shop, resolve verified GCash payments, bucket by status, subtract pending
 * payouts). If the SQL and this PHP ever disagree, the withdrawal gate and the
 * withdrawals page would start showing different balances, and this test fails.
 */
class EscrowAggregateEquivalenceTest extends CIUnitTestCase
{
    /** @var array<int, int> */
    private array $shopIds = [];

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();

        foreach ($this->shopIds as $shopId) {
            $orderIds = array_column(
                $db->table('orders')->where('shop_id', $shopId)->get()->getResultArray(),
                'id'
            );
            $prIds = array_column(
                $db->table('printing_requests')->where('shop_id', $shopId)->get()->getResultArray(),
                'id'
            );

            if ($orderIds !== [] || $prIds !== []) {
                $db->table('payments')->where('payable_type', 'order')->whereIn('payable_id', $orderIds ?: [0])->delete();
                $db->table('payments')->where('payable_type', 'printing_request')->whereIn('payable_id', $prIds ?: [0])->delete();
            }
            if ($orderIds !== []) {
                $db->table('order_items')->whereIn('order_id', $orderIds)->delete();
            }
            $db->table('orders')->where('shop_id', $shopId)->delete();
            $db->table('printing_requests')->where('shop_id', $shopId)->delete();
            $db->table('payout_requests')->where('shop_id', $shopId)->delete();
            $db->table('archived_items')->where('shop_id', $shopId)->delete();
            $db->table('shops')->where('id', $shopId)->delete();
        }

        $this->shopIds = [];
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    private function makeShop(string $prefix): int
    {
        $db   = \Config\Database::connect();
        $uniq = time() . random_int(1000, 9999);

        $db->table('shops')->insert([
            'owner_id'   => 2,
            'shop_name'  => $prefix . '_' . $uniq,
            'slug'       => strtolower($prefix) . '-' . $uniq,
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->shopIds[] = (int) $db->insertID();

        return (int) $db->insertID();
    }

    private function insertOrder(int $shopId, string $number, array $overrides): int
    {
        $db = \Config\Database::connect();

        $db->table('orders')->insert(array_merge([
            'order_number'       => $number,
            'customer_id'        => 5,
            'shop_id'            => $shopId,
            'fulfillment_method' => 'delivery',
            'payment_method'     => 'gcash',
            'payment_status'     => 'paid',
            'subtotal'           => 100.00,
            'total_amount'       => 100.00,
            'status'             => 'delivered',
            'placed_at'          => date('Y-m-d H:i:s'),
            'completed_at'       => date('Y-m-d H:i:s'),
        ], $overrides));

        return (int) $db->insertID();
    }

    private function insertPrinting(int $shopId, string $number, array $overrides): int
    {
        $db = \Config\Database::connect();

        $db->table('printing_requests')->insert(array_merge([
            'request_number' => $number,
            'customer_id'    => 5,
            'shop_id'        => $shopId,
            'status'         => 'completed',
            'file_name'      => 'EscrowAgg.pdf',
            'total_price'    => 200.00,
            'down_payment'   => 50.00,
            'created_at'     => date('Y-m-d H:i:s'),
            'completed_at'   => date('Y-m-d H:i:s'),
        ], $overrides));

        return (int) $db->insertID();
    }

    private function insertPayment(string $payableType, int $payableId, array $overrides): void
    {
        \Config\Database::connect()->table('payments')->insert(array_merge([
            'payable_type'     => $payableType,
            'payable_id'       => $payableId,
            'method'           => 'gcash',
            'amount'           => 100.00,
            'reference_number' => 'ref_escrow_' . $payableType . '_' . $payableId,
            'status'           => 'verified',
            'processed_at'     => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s'),
        ], $overrides));
    }

    /**
     * Seed a shop whose rows exercise every branch of the amount arithmetic:
     * verified payments vs. inline payment columns, online vs. counter POS, POS
     * top-ups on an online order, cancelled rows, non-GCash rows, archived rows,
     * and pending vs. rejected payouts.
     */
    private function seedMatrixShop(): int
    {
        $shopId = $this->makeShop('EscrowAggTestShop');
        $tag    = substr((string) $shopId, -4) . random_int(100, 999);

        // 1. Delivered online GCash order, no payment row: subtotal is credited.
        $this->insertOrder($shopId, 'ORD-AGG-A-' . $tag, ['subtotal' => 111.11, 'total_amount' => 130.00]);

        // 2. Completed order with a verified GCash payment: the payment row wins
        //    over the inline columns.
        $paidOrder = $this->insertOrder($shopId, 'ORD-AGG-B-' . $tag, [
            'status' => 'completed', 'subtotal' => 500.00, 'total_amount' => 500.00,
        ]);
        $this->insertPayment('order', $paidOrder, ['amount' => 222.22]);

        // 3. In-progress GCash order: escrow, not released.
        $this->insertOrder($shopId, 'ORD-AGG-C-' . $tag, ['status' => 'processing', 'total_amount' => 80.00, 'subtotal' => 80.00]);

        // 4. Cancelled order: excluded entirely.
        $this->insertOrder($shopId, 'ORD-AGG-D-' . $tag, ['status' => 'cancelled', 'subtotal' => 999.00, 'total_amount' => 999.00]);

        // 5. Delivered order carrying cancelled_at: excluded (PHP checks both).
        $this->insertOrder($shopId, 'ORD-AGG-E-' . $tag, ['cancelled_at' => date('Y-m-d H:i:s')]);

        // 6. COD order: no GCash amount, skipped.
        $this->insertOrder($shopId, 'ORD-AGG-F-' . $tag, ['payment_method' => 'cod', 'payment_status' => 'unpaid']);

        // 7. Counter POS GCash on a non-online order: total_amount credited.
        $this->insertOrder($shopId, 'ORD-AGG-G-' . $tag, [
            'status'             => 'completed',
            'payment_method'     => 'walk_in',
            'payment_status'     => 'unpaid',
            'pos_payment_method' => 'gcash',
            'pos_payment_status' => 'paid',
            'subtotal'           => 60.00,
            'total_amount'       => 75.50,
        ]);

        // 8. Online GCash plus a POS top-up: only the additional amount is added.
        $this->insertOrder($shopId, 'ORD-AGG-H-' . $tag, [
            'status'                => 'delivered',
            'payment_method'        => 'paymongo',
            'payment_status'        => 'paid',
            'pos_payment_method'    => 'gcash',
            'pos_payment_status'    => 'completed',
            'pos_additional_amount' => 12.34,
            'subtotal'              => 200.00,
            'total_amount'          => 212.34,
        ]);

        // 9. Archived GCash order: excluded because getOrdersByShop() excludes it.
        $archived = $this->insertOrder($shopId, 'ORD-AGG-I-' . $tag, ['subtotal' => 777.00, 'total_amount' => 777.00]);
        \Config\Database::connect()->table('archived_items')->insert([
            'shop_id'    => $shopId,
            'item_type'  => 'order',
            'item_id'    => $archived,
            'item_label' => 'ORD-AGG-I-' . $tag,
            'archived_by' => 2,
            'archived_at' => date('Y-m-d H:i:s'),
        ]);

        // 10. Completed printing request with a down payment: released.
        $this->insertPrinting($shopId, 'PR-AGG-A-' . $tag, ['down_payment' => 33.33]);

        // 11. Pending printing request with a verified GCash payment: escrow.
        $pendingPr = $this->insertPrinting($shopId, 'PR-AGG-B-' . $tag, ['status' => 'in_progress', 'down_payment' => 0.00]);
        $this->insertPayment('printing_request', $pendingPr, ['amount' => 44.44]);

        // 12. Cancelled printing request: excluded.
        $this->insertPrinting($shopId, 'PR-AGG-C-' . $tag, ['status' => 'cancelled', 'down_payment' => 55.00]);

        // 13. Printing request with neither a payment row nor a down payment: skipped.
        $this->insertPrinting($shopId, 'PR-AGG-D-' . $tag, ['status' => 'for_payment', 'down_payment' => 0.00]);

        $db = \Config\Database::connect();
        // Pending payout reduces the available balance; a rejected one must not.
        $db->table('payout_requests')->insert([
            'shop_id'          => $shopId,
            'reference_number' => 'WD-AGG-P-' . $tag,
            'amount'           => 25.00,
            'status'           => 'pending',
            'requested_at'     => date('Y-m-d H:i:s'),
        ]);
        $db->table('payout_requests')->insert([
            'shop_id'          => $shopId,
            'reference_number' => 'WD-AGG-R-' . $tag,
            'amount'           => 500.00,
            'status'           => 'rejected',
            'requested_at'     => date('Y-m-d H:i:s'),
        ]);

        return $shopId;
    }

    /**
     * The pre-refactor algorithm, restated in PHP over the same rows.
     *
     * @return array{available_balance: float, escrow_holding: float, released_earnings: float, total_deductions: float}
     */
    private function referenceTotals(int $shopId): array
    {
        helper('status');
        $db         = \Config\Database::connect();
        $orderModel = new \App\Models\OrderModel();

        $orders = $orderModel->getOrdersByShop($shopId);
        $printingRequests = $db->table('printing_requests pr')
            ->select('pr.*, u.first_name, u.last_name, u.email')
            ->join('users u', 'u.id = pr.customer_id', 'left')
            ->where('pr.shop_id', $shopId)
            ->get()->getResultArray();

        $orderIds = array_map('intval', array_column($orders, 'id'));
        $prIds    = array_map('intval', array_column($printingRequests, 'id'));
        $orderPaymentsMap = [];
        $prPaymentsMap    = [];

        if ($orderIds !== [] || $prIds !== []) {
            $rows = $db->table('payments')
                ->select('payable_type, payable_id, SUM(amount) as total_amount')
                ->where('status', 'verified')
                ->where('LOWER(method)', 'gcash')
                ->groupStart()
                    ->groupStart()->where('payable_type', 'order')->whereIn('payable_id', $orderIds ?: [0])->groupEnd()
                    ->orGroupStart()->where('payable_type', 'printing_request')->whereIn('payable_id', $prIds ?: [0])->groupEnd()
                ->groupEnd()
                ->groupBy('payable_type, payable_id')
                ->get()->getResultArray();

            foreach ($rows as $row) {
                if ($row['payable_type'] === 'order') {
                    $orderPaymentsMap[(int) $row['payable_id']] = (float) $row['total_amount'];
                } else {
                    $prPaymentsMap[(int) $row['payable_id']] = (float) $row['total_amount'];
                }
            }
        }

        $released = 0.0;
        $escrow   = 0.0;

        foreach ($orders as $o) {
            $status = strtolower(trim((string) ($o['status'] ?? '')));
            if ($status === 'cancelled' || !empty($o['cancelled_at'])) {
                continue;
            }

            $verified = (float) ($orderPaymentsMap[(int) $o['id']] ?? 0.0);
            $isOnline = in_array(strtolower(trim((string) ($o['payment_method'] ?? ''))), ['gcash', 'paymongo', 'online_gcash'], true)
                && strtolower(trim((string) ($o['payment_status'] ?? ''))) === 'paid';
            $isPos    = strtolower(trim((string) ($o['pos_payment_method'] ?? ''))) === 'gcash'
                && in_array(strtolower(trim((string) ($o['pos_payment_status'] ?? ''))), ['paid', 'completed'], true);

            $amount = 0.0;
            if ($verified > 0) {
                $amount = $verified;
            } else {
                if ($isOnline) {
                    $amount += (float) ($o['subtotal'] ?? ($o['total_amount'] ?? 0));
                }
                if ($isPos && ! $isOnline) {
                    $amount += (float) ($o['total_amount'] ?? 0);
                } elseif ($isPos && $isOnline && (float) ($o['pos_additional_amount'] ?? 0) > 0) {
                    $amount += (float) $o['pos_additional_amount'];
                }
            }

            if ($amount <= 0) {
                continue;
            }

            if (in_array($status, ['delivered', 'completed'], true)) {
                $released += $amount;
            } else {
                $escrow += $amount;
            }
        }

        foreach ($printingRequests as $pr) {
            $status = strtolower(trim((string) ($pr['status'] ?? '')));
            if ($status === 'cancelled') {
                continue;
            }

            $verified = (float) ($prPaymentsMap[(int) $pr['id']] ?? 0.0);
            $amount   = $verified > 0 ? $verified : (float) ($pr['down_payment'] ?? 0);

            if ($amount <= 0) {
                continue;
            }

            if ($status === 'completed') {
                $released += $amount;
            } else {
                $escrow += $amount;
            }
        }

        $payoutRow = $db->table('payout_requests')
            ->where('shop_id', $shopId)
            ->whereNotIn('status', ['rejected', 'failed', 'cancelled'])
            ->selectSum('amount')
            ->get()->getRowArray();
        $deductions = (float) ($payoutRow['amount'] ?? 0);

        return [
            'available_balance' => max(0.0, round($released - $deductions, 2)),
            'escrow_holding'    => round($escrow, 2),
            'released_earnings' => round($released, 2),
            'total_deductions'  => $deductions,
        ];
    }

    public function testAggregateMatchesRowByRowReference(): void
    {
        $shopId     = $this->seedMatrixShop();
        $controller = new Tenant();

        $expected = $this->referenceTotals($shopId);
        $actual   = $controller->computeShopEscrowTotals($shopId);

        // The fixture must actually contain money, or the comparison proves nothing.
        $this->assertGreaterThan(0.0, $expected['released_earnings'], 'Seed produced no released earnings.');
        $this->assertGreaterThan(0.0, $expected['escrow_holding'], 'Seed produced no escrow holding.');
        $this->assertGreaterThan(0.0, $expected['total_deductions'], 'Seed produced no payout deduction.');

        foreach (['available_balance', 'escrow_holding', 'released_earnings', 'total_deductions'] as $key) {
            $this->assertSame(
                $expected[$key],
                $actual[$key],
                "computeShopEscrowTotals() disagrees with the row-by-row reference on [{$key}]."
            );
        }
    }

    public function testPresentationPathReportsTheSameTotals(): void
    {
        $shopId     = $this->seedMatrixShop();
        $controller = new Tenant();

        $expected  = $this->referenceTotals($shopId);
        $breakdown = $controller->getShopEscrowAndBalance($shopId);

        foreach (['available_balance', 'escrow_holding', 'released_earnings', 'total_deductions'] as $key) {
            $this->assertSame($expected[$key], $breakdown[$key], "getShopEscrowAndBalance() changed [{$key}].");
        }

        // The ledger rows the withdrawals page renders must still be produced.
        $this->assertNotEmpty($breakdown['settled_records']);
        $this->assertNotEmpty($breakdown['escrow_records']);
        $this->assertArrayHasKey('reference', $breakdown['settled_records'][0]);
    }
}
