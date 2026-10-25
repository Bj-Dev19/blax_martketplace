<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class DbPingCommand extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:ping';
    protected $description = 'Measure database connection and round-trip query latency to diagnose network bottleneck.';
    protected $usage       = 'db:ping [--samples <N>]';
    protected $options     = [
        '--samples' => 'Number of sequential ping queries to execute (default: 20)',
    ];

    public function run(array $params)
    {
        $samples = (int) ($params['samples'] ?? CLI::getOption('samples') ?? 20);
        if ($samples < 1) {
            $samples = 20;
        }

        CLI::write('====================================================', 'cyan');
        CLI::write('  Blax Marketplace - Database Latency Diagnostic    ', 'yellow');
        CLI::write('====================================================', 'cyan');

        $dbConfig = config('Database');
        $group    = $dbConfig->defaultGroup ?? 'default';
        $settings = $dbConfig->$group ?? [];

        $hostname = $settings['hostname'] ?? 'unknown';
        $database = $settings['database'] ?? 'unknown';
        $username = $settings['username'] ?? 'unknown';
        $port     = $settings['port'] ?? 3306;
        $pConnect = !empty($settings['pConnect']);

        CLI::write("Target Host:      {$hostname}:{$port}", 'white');
        CLI::write("Database:         {$database}", 'white');
        CLI::write("User:             {$username}", 'white');
        CLI::write("Persistent (p:):  " . ($pConnect ? 'ENABLED' : 'DISABLED'), $pConnect ? 'green' : 'yellow');

        // Measure Connection Time
        CLI::write("\nMeasuring raw connection establishment...", 'cyan');
        $connStart = hrtime(true);
        try {
            $db = Database::connect($group, false);
            // Force connection initialization
            $db->initialize();
            $connEnd = hrtime(true);
            $connTimeMs = ($connEnd - $connStart) / 1e6;
            CLI::write(sprintf("Connection established in: %0.2f ms", $connTimeMs), 'green');
        } catch (\Throwable $e) {
            CLI::error("Database connection failed: " . $e->getMessage());
            return EXIT_ERROR;
        }

        // Check SSL / Cipher
        $sslCipher = 'DISABLED';
        try {
            $sslRes = $db->query("SHOW STATUS LIKE 'Ssl_cipher'")->getRow();
            if ($sslRes && !empty($sslRes->Value)) {
                $sslCipher = $sslRes->Value;
            }
        } catch (\Throwable $e) {
            $sslCipher = 'UNKNOWN (' . $e->getMessage() . ')';
        }
        CLI::write("TLS / SSL Cipher: {$sslCipher}", $sslCipher !== 'DISABLED' ? 'green' : 'yellow');

        // Execute Ping Queries (SELECT 1)
        CLI::write("\nExecuting {$samples} sequential 'SELECT 1' round-trips...", 'cyan');
        $times = [];

        for ($i = 1; $i <= $samples; $i++) {
            $qStart = hrtime(true);
            $db->query("SELECT 1");
            $qEnd = hrtime(true);
            $durationMs = ($qEnd - $qStart) / 1e6;
            $times[] = $durationMs;
        }

        sort($times);
        $min = $times[0];
        $max = $times[count($times) - 1];
        $sum = array_sum($times);
        $avg = $sum / count($times);
        $median = $times[(int) (count($times) / 2)];

        CLI::write("\n--- Query Round-Trip Summary (SELECT 1) ---", 'yellow');
        CLI::write(sprintf("Min Latency:      %0.2f ms", $min));
        CLI::write(sprintf("Avg Latency:      %0.2f ms", $avg), $avg > 50 ? 'red' : ($avg > 15 ? 'yellow' : 'green'));
        CLI::write(sprintf("Median Latency:   %0.2f ms", $median));
        CLI::write(sprintf("Max Latency:      %0.2f ms", $max));
        CLI::write(sprintf("Total Probe Time: %0.2f ms for %d queries", $sum, $samples));

        // Evaluate and provide diagnostic recommendations
        CLI::write("\n--- Latency Diagnostic Verdict ---", 'yellow');
        if ($avg < 5) {
            CLI::write("Status: OPTIMAL (< 5ms)", 'green');
            CLI::write("Database appears to be locally hosted or colocated in the exact same datacenter/region.");
        } elseif ($avg < 25) {
            CLI::write("Status: ACCEPTABLE (5ms - 25ms)", 'yellow');
            CLI::write("Database is nearby in the same metropolitan area or cloud availability zone.");
        } elseif ($avg < 75) {
            CLI::write("Status: HIGH (25ms - 75ms)", 'yellow');
            CLI::write("Moderate inter-region or cross-cloud latency. Sequential queries will noticeably impact page loads.");
        } else {
            CLI::write("Status: CRITICAL (> 75ms)", 'red');
            CLI::write("Cross-region or cross-continental latency detected!");
            CLI::write("A request executing 10 sequential database queries will spend at least " . sprintf('%0.0f ms', $avg * 10) . " waiting purely on network socket I/O.");
            CLI::write("Action: Ensure your database provider (Aiven) is in the SAME cloud region as Render (Singapore: ap-southeast-1).");
        }

        CLI::write("====================================================\n", 'cyan');

        return EXIT_SUCCESS;
    }
}
