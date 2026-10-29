<?php

namespace App\Filters;

use CodeIgniter\Events\Events;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Opt-in Performance Profiler Filter
 *
 * Activated exclusively when APP_PROFILE=1 is present in the environment.
 * Logs execution time, peak memory usage, and database query count to stderr.
 * When APP_PROFILE is unset or 0, this filter returns immediately with zero overhead.
 *
 * Query stats come from the framework's `DBQuery` event. CodeIgniter 4.7 removed
 * the connection-level query log ($db->saveQueries / $db->getQueries()), so the
 * event is the only supported way to observe statements; the listener is
 * registered once and only while profiling is on.
 */
class PerformanceFilter implements FilterInterface
{
    private static bool $enabled = false;
    private static float $startTime = 0.0;
    private static int $startMemory = 0;

    /** @var list<object> Query objects collected for the current request */
    private static array $queries = [];

    private static bool $listening = false;

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
        self::$queries   = [];

        if (! self::$listening) {
            self::$listening = true;

            Events::on('DBQuery', static function ($query): void {
                // Keep the object, not a string, so getDuration()/getOriginalQuery()
                // stay available in after().
                self::$queries[] = $query;
            });
        }
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
        $totalSqlMs = 0.0;
        $slowQueries = [];

        foreach (self::$queries as $q) {
            if (is_object($q) && method_exists($q, 'getDuration')) {
                $queryCount++;
                $durMs = round(((float) $q->getDuration()) * 1000, 2);
                $totalSqlMs += $durMs;
                if ($durMs >= 15.0) {
                    $sql = method_exists($q, 'getOriginalQuery') ? (string) $q->getOriginalQuery() : (string) $q;
                    $slowQueries[] = sprintf("%.2fms: %s", $durMs, substr(preg_replace('/\s+/', ' ', trim($sql)), 0, 120));
                }
            }
        }

        self::$queries = [];

        $logMsg = sprintf(
            "[APP_PROFILE] %s /%s %d | Time: %s ms | Peak Mem: %s MB | SQL Queries: %d | SQL Time: %s ms\n",
            $method,
            ltrim($uri, '/'),
            $status,
            number_format($elapsedMs, 2),
            number_format($peakMemMb, 2),
            $queryCount,
            number_format($totalSqlMs, 2)
        );

        if (!empty($slowQueries)) {
            $logMsg .= "  [SLOW SQL] " . implode(" | ", array_slice($slowQueries, 0, 3)) . "\n";
        }

        if (defined('STDERR') && is_resource(STDERR)) {
            fwrite(STDERR, $logMsg);
        } else {
            error_log(trim($logMsg));
        }
    }
}
