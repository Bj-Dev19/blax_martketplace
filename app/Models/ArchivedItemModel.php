<?php

namespace App\Models;

use CodeIgniter\Model;

class ArchivedItemModel extends Model
{
    protected $table            = 'archived_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'shop_id',
        'item_type',
        'item_id',
        'item_label',
        'archived_by',
        'archived_at',
        'restored_at',
    ];

    /**
     * Paginated, tenant-scoped archive log with the archiver's name.
     * NOTE: the primary table must be referenced by its real name
     * (archived_items.*) because Model::paginate() regenerates a count
     * query that strips table aliases.
     */
    public function getArchivedForShopPaginated(
        int $shopId,
        int $perPage = 10,
        int $page = 1,
        string $group = 'archive',
        ?string $type = null,
        ?string $search = null
    ): array {
        $builder = $this->builder()
            ->select('archived_items.*, u.first_name AS archived_by_first, u.last_name AS archived_by_last, u.profile_image_url AS archived_by_image')
            ->join('users u', 'u.id = archived_items.archived_by', 'left')
            ->where('archived_items.shop_id', $shopId);

        if (!empty($type) && in_array($type, ['order', 'inventory', 'printing_request'], true)) {
            $builder->where('archived_items.item_type', $type);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('archived_items.item_label', $search)
                ->orLike('archived_items.item_id', $search)
            ->groupEnd();
        }

        $builder->orderBy('archived_items.archived_at', 'DESC');

        $items = $this->paginate($perPage, $group, $page);

        return [
            'items' => $items ?: [],
            'pager' => $this->pager,
        ];
    }

    /**
     * Automatically archives completed orders and completed printing requests
     * for a shop that completed >= $days ago (default 3 days).
     * Records are NEVER deleted; they are logged to archived_items and
     * remain in the shop's archive history indefinitely.
     *
     * Performance-optimized for Railway: uses a 15-minute cache throttle
     * and single-query LEFT JOIN + insertBatch to eliminate 400+ N+1 queries.
     *
     * @return array{archived_orders: int, archived_printing: int}
     */
    public function autoArchiveCompletedItems(int $shopId, int $days = 3, bool $force = false): array
    {
        $cacheKey = "blax_auto_archive_shop_{$shopId}";
        if (!$force) {
            try {
                if (cache($cacheKey)) {
                    return ['archived_orders' => 0, 'archived_printing' => 0];
                }
            } catch (\Throwable $e) {}
        }

        $db = $this->db;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $now = date('Y-m-d H:i:s');
        $userId = (int) (session()->get('user_id') ?? 0) ?: null;

        // 1. Completed / Delivered orders older than 3 days that are NOT yet archived
        // Single LEFT JOIN query — 0 N+1 queries
        $ordersToArchive = $db->table('orders o')
            ->select('o.id, o.order_number')
            ->join('archived_items ai', "ai.shop_id = o.shop_id AND ai.item_type = 'order' AND ai.item_id = o.id", 'left')
            ->where('o.shop_id', $shopId)
            ->whereIn('o.status', ['completed', 'delivered'])
            ->groupStart()
                ->where('o.completed_at IS NOT NULL AND o.completed_at <=', $cutoff)
                ->orWhere('o.completed_at IS NULL AND o.placed_at <=', $cutoff)
            ->groupEnd()
            ->where('ai.id IS NULL')
            ->get()->getResultArray();

        $archivedOrders = 0;
        if (!empty($ordersToArchive)) {
            $batchOrders = [];
            foreach ($ordersToArchive as $ord) {
                $label = !empty($ord['order_number']) ? ('#' . ltrim($ord['order_number'], '#')) : ('#ORD-' . $ord['id']);
                $batchOrders[] = [
                    'shop_id'     => $shopId,
                    'item_type'   => 'order',
                    'item_id'     => (int) $ord['id'],
                    'item_label'  => $label,
                    'archived_by' => $userId,
                    'archived_at' => $now,
                ];
            }
            if (!empty($batchOrders)) {
                $this->insertBatch($batchOrders);
                $archivedOrders = count($batchOrders);
            }
        }

        // 2. Completed printing requests older than 3 days that are NOT yet archived
        $printToArchive = $db->table('printing_requests pr')
            ->select('pr.id, pr.request_number')
            ->join('archived_items ai', "ai.shop_id = pr.shop_id AND ai.item_type = 'printing_request' AND ai.item_id = pr.id", 'left')
            ->where('pr.shop_id', $shopId)
            ->where('pr.status', 'completed')
            ->groupStart()
                ->where('pr.completed_at IS NOT NULL AND pr.completed_at <=', $cutoff)
                ->orWhere('pr.completed_at IS NULL AND pr.created_at <=', $cutoff)
            ->groupEnd()
            ->where('ai.id IS NULL')
            ->get()->getResultArray();

        $archivedPrint = 0;
        if (!empty($printToArchive)) {
            $batchPrint = [];
            foreach ($printToArchive as $pr) {
                $label = !empty($pr['request_number']) ? ('#' . ltrim($pr['request_number'], '#')) : ('#PR-' . $pr['id']);
                $batchPrint[] = [
                    'shop_id'     => $shopId,
                    'item_type'   => 'printing_request',
                    'item_id'     => (int) $pr['id'],
                    'item_label'  => $label,
                    'archived_by' => $userId,
                    'archived_at' => $now,
                ];
            }
            if (!empty($batchPrint)) {
                $this->insertBatch($batchPrint);
                $archivedPrint = count($batchPrint);
            }
        }

        // Cache throttle for 15 minutes (900 seconds)
        try {
            cache()->save($cacheKey, 1, 900);
        } catch (\Throwable $e) {}

        return [
            'archived_orders'   => $archivedOrders,
            'archived_printing' => $archivedPrint,
        ];
    }
}