<?php

declare(strict_types=1);

namespace App\Libraries\Log;

use CodeIgniter\Log\Handlers\BaseHandler;

/**
 * Streams critical application logs directly to PHP error_log / stderr.
 * Essential for cloud container environments (Render, Docker) where
 * file logs in /writable/logs are isolated inside ephemeral containers.
 */
class ErrorLogHandler extends BaseHandler
{
    public function handle($level, $message): bool
    {
        $formatted = sprintf('[CI4 %s] %s', strtoupper((string) $level), (string) $message);
        return error_log($formatted);
    }
}
