<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Opt-in Performance Profiler Filter
 *
 * Activated exclusively when APP_PROFILE=1 is present in the environment.
 * Logs execution time, peak memory usage, and database query count to stderr.
 * When APP_PROFILE is unset or 0, this filter returns immediately with zero overhead.
 */
class PerformanceFilter implements FilterInterface
{
    private static bool $enabled = false;
    private static float $startTime = 0.0;
    private static int $startMemory = 0;

    public function before(RequestInterface $request, $arguments = null)
    {
        $appProfile = getenv('APP_PROFILE') ?: (isset($_SERVER['APP_PROFILE']) ? $_SERVER['APP_PROFILE'] : null);
        if ($appProfile !== '1' && $appProfile !== 'true') {
            self::$enabled = false;
            return;
        }

        self::$enabled = true;
        self::$startTime = microtime(true);
        self::$startMemory = memory_get_usage(false);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (!self::$enabled) {
            return;
        }

        $elapsedMs = round((microtime(true) - self::$startTime) * 1000, 2);
        $peakMemMb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        $method    = $request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request->getMethod() : 'CLI';
        $uri       = $request instanceof \CodeIgniter\HTTP\IncomingRequest ? (string) $request->getUri()->getPath() : 'cli';
        $status    = $response->getStatusCode();

        $queryCount = 0;
        try {
            $db = \Config\Database::connect(null, false);
            if ($db) {
                $queryCount = count($db->getQueries());
            }
        } catch (\Throwable $e) {
            // Do not fail if database is not connected
        }

        $logMsg = sprintf(
            "[APP_PROFILE] %s /%s %d | Time: %s ms | Peak Mem: %s MB | SQL Queries: %d\n",
            $method,
            ltrim($uri, '/'),
            $status,
            number_format($elapsedMs, 2),
            number_format($peakMemMb, 2),
            $queryCount
        );

        if (defined('STDERR') && is_resource(STDERR)) {
            fwrite(STDERR, $logMsg);
        } else {
            error_log(trim($logMsg));
        }
    }
}
