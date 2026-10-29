<?php

/**
 * Fixture hygiene for the POS ready-for-pick-up queue tests.
 *
 * Tenant::pos() renders both pick-up selectors as a FIFO window that is capped
 * at the oldest 50 rows (->orderBy('placed_at'|'created_at', 'ASC')->findAll(50)).
 * Two consequences shape every fixture in here:
 *
 *   1. A row stamped with "now" is only rendered while fewer than 50 older rows
 *      are already queued, so the assertion quietly depends on how dirty the
 *      database happens to be. Queued fixtures are anchored at QUEUE_ANCHOR
 *      instead, which puts them at the head of the window for the same reason a
 *      real attendant sees the oldest job first.
 *   2. Inserts have to be undone. Every previous run of these tests left its
 *      rows behind; that backlog is what made the window assertions fail.
 *
 * @see \App\Controllers\Tenant::pos()
 */
trait PosPickupFixtures
{
    protected const QUEUE_ANCHOR = '2018-01-01 06:00:00';

    /** @var array<string, array<int>> */
    protected array $posSeeded = [
        'orders'            => [],
        'printing_requests' => [],
        'payments'          => [],
    ];

    protected function posDb(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    /**
     * Remember a row so tearDown() can remove it again. Silently ignores a
     * failed insert so a fixture problem surfaces as an assertion, not as a
     * delete error during cleanup.
     */
    protected function posTrack(string $table, int|string|false $id): void
    {
        if (!empty($id)) {
            $this->posSeeded[$table][] = (int) $id;
        }
    }

    /**
     * Delete everything this test created. Rows that point at an order or a
     * printing request only polymorphically (payments, deliveries, archived
     * items) are removed by hand because no foreign key covers them; the
     * genuine children (order_items, printing_request_attachments) cascade.
     */
    protected function posCleanup(): void
    {
        $db     = $this->posDb();
        $orders = array_values(array_unique($this->posSeeded['orders']));
        $prints = array_values(array_unique($this->posSeeded['printing_requests']));

        foreach ([['orders', 'order', $orders], ['printing_requests', 'printing_request', $prints]] as [$table, $type, $ids]) {
            if ($ids === []) {
                continue;
            }

            foreach (['payments' => 'payable', 'deliveries' => 'deliverable'] as $child => $prefix) {
                try {
                    $db->table($child)
                        ->where($prefix . '_type', $type)
                        ->whereIn($prefix . '_id', $ids)
                        ->delete();
                } catch (\Throwable $e) {
                    log_message('error', 'POS fixture cleanup skipped ' . $child . ': ' . $e->getMessage());
                }
            }

            try {
                $db->table('archived_items')->where('item_type', $type)->whereIn('item_id', $ids)->delete();
            } catch (\Throwable $e) {
                // Archived-item rows are optional; the parent delete still stands.
            }

            $db->table($table)->whereIn('id', $ids)->delete();
        }

        foreach (array_values(array_unique($this->posSeeded['payments'])) as $paymentId) {
            $db->table('payments')->where('id', $paymentId)->delete();
        }

        foreach (array_keys($this->posSeeded) as $table) {
            $this->posSeeded[$table] = [];
        }
    }
}
