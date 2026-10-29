<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'action_url',
        'created_at',
    ];

    private static array $unreadCache = [];
    private static array $recentCache = [];

    public static function clearRequestCache(): void
    {
        self::$unreadCache = [];
        self::$recentCache = [];
    }

    public function getUnreadCount(int $userId): int
    {
        if (isset(self::$unreadCache[$userId])) {
            return self::$unreadCache[$userId];
        }

        return self::$unreadCache[$userId] = (int) $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function getRecent(int $userId, int $limit = 5): array
    {
        $cacheKey = $userId . ':' . $limit;
        if (isset(self::$recentCache[$cacheKey])) {
            return self::$recentCache[$cacheKey];
        }

        return self::$recentCache[$cacheKey] = $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Badge count plus the dropdown rows in a single round trip.
     *
     * The layouts previously issued getUnreadCount() and getRecent() back to back on
     * every page. With a remote database each statement costs a full round trip, so
     * folding them into one statement halves that per page.
     *
     * The unread total rides along as a correlated subquery, which is evaluated once
     * against the (user_id, is_read) index. No rows means nothing exists to be read,
     * so the count is legitimately zero.
     *
     * @return array{unread_count: int, notifications: array<int, array<string, mixed>>}
     */
    public function getHeaderSnapshot(int $userId, int $limit = 6): array
    {
        $empty = ['unread_count' => 0, 'notifications' => []];

        if ($userId <= 0) {
            return $empty;
        }

        $limit = max(1, $limit);

        try {
            $rows = $this->db->table('notifications n')
                ->select(
                    'n.id, n.type, n.title, n.message, n.is_read, n.action_url, n.created_at,'
                    . ' (SELECT COUNT(*) FROM notifications u WHERE u.user_id = n.user_id AND u.is_read = 0) AS unread_count',
                    false
                )
                ->where('n.user_id', $userId)
                ->orderBy('n.created_at', 'DESC')
                ->orderBy('n.id', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            return $empty;
        }

        if (! is_array($rows) || $rows === []) {
            return $empty;
        }

        $unread = (int) $rows[0]['unread_count'];
        foreach ($rows as &$row) {
            unset($row['unread_count']);
        }
        unset($row);

        // Seed the per-request caches so a later getUnreadCount()/getRecent() call in
        // the same request reuses this result instead of paying for the query twice.
        self::$unreadCache[$userId]                   = $unread;
        self::$recentCache[$userId . ':' . $limit]    = $rows;

        return ['unread_count' => $unread, 'notifications' => $rows];
    }

    public function markAllRead(int $userId): bool
    {
        self::clearRequestCache();

        return (bool) $this->where('user_id', $userId)
            ->set(['is_read' => 1])
            ->update();
    }

    public function markRead(int $id, int $userId): bool
    {
        self::clearRequestCache();

        return (bool) $this->where('id', $id)
            ->where('user_id', $userId)
            ->set(['is_read' => 1])
            ->update();
    }

    public function create(int $userId, string $type, string $title, string $message, ?string $actionUrl = null): bool
    {
        self::clearRequestCache();
        $safeUrl = null;
        if ($actionUrl !== null && $actionUrl !== '') {
            $parsed = trim($actionUrl);
            if (str_starts_with($parsed, '/') && !str_starts_with($parsed, '//') && !str_contains($parsed, ':')) {
                $safeUrl = $parsed;
            }
        }

        return (bool) $this->insert([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'action_url' => $safeUrl,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
