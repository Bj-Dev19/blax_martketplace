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
