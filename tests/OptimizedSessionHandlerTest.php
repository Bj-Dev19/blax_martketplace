<?php

use CodeIgniter\Events\Events;
use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Session as SessionConfig;

/**
 * Runtime contract of App\Session\Handlers\OptimizedDatabaseHandler.
 *
 * Added while upgrading CodeIgniter 4.6 -> 4.7.5. That upgrade made
 * DatabaseHandler::read() return `string|false`, which turns the previously
 * untyped override into a compile-time fatal, so the handler is now checked at
 * runtime as well as by reflection:
 *
 * 1. open/read/write/destroy/gc still round-trip against ci_sessions;
 * 2. the deliberate removal of session locking (see docs/PERF_PLAN.md) is still
 *    in effect — no GET_LOCK/RELEASE_LOCK/FOR UPDATE may reach the database.
 *
 * Statements are observed through the framework's `DBQuery` event, because 4.7
 * removed the connection-level query log ($db->saveQueries / getQueries()).
 */
class OptimizedSessionHandlerTest extends CIUnitTestCase
{
    private string $sessionId;
    private string $idPrefix;

    /** @var list<string> Upper-cased SQL captured while the spy is armed */
    private static array $spySql = [];

    private static bool $spyArmed = false;

    private static bool $spyRegistered = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sessionId = 'blaxtest' . bin2hex(random_bytes(8));
        // The handler namespaces rows by cookie name, exactly like the parent.
        $this->idPrefix = config(SessionConfig::class)->cookieName . ':';

        $this->deleteTestRows();
    }

    protected function tearDown(): void
    {
        self::$spyArmed = false;

        $this->deleteTestRows();

        parent::tearDown();
    }

    private function deleteTestRows(): void
    {
        \Config\Database::connect()->query(
            'DELETE FROM ci_sessions WHERE id LIKE ?',
            [$this->idPrefix . 'blaxtest%']
        );
    }

    /**
     * Arms the spy. Read self::$spySql after the statements you care about have
     * run — PHP copies arrays, so snapshotting before would capture nothing.
     */
    private function armSqlSpy(): void
    {
        self::$spySql = [];
        self::$spyArmed = true;

        if (! self::$spyRegistered) {
            self::$spyRegistered = true;

            Events::on('DBQuery', static function ($query): void {
                if (self::$spyArmed && is_object($query) && method_exists($query, 'getQuery')) {
                    self::$spySql[] = strtoupper((string) $query->getQuery());
                }
            });
        }
    }

    private function handler(): BaseHandler
    {
        $config = config(SessionConfig::class);

        $this->assertSame(
            \App\Session\Handlers\OptimizedDatabaseHandler::class,
            $config->driver,
            'The app must keep running the optimized handler this test guards.'
        );

        return new $config->driver($config, '127.0.0.1');
    }

    public function testRoundTripThroughConfiguredDriver(): void
    {
        $this->armSqlSpy();

        $handler = $this->handler();
        $db      = \Config\Database::connect();

        $this->assertTrue($handler->open($db->DBPrefix . $db->database, 'ci_session'));

        // A cold read on a missing row must return an empty string, not false:
        // PHP treats false from read() as a storage failure.
        $this->assertSame('', $handler->read($this->sessionId));

        $payload = '__ci_vars|a:1:{s:7:"flash_id";i:1;}user_id|s:1:"2":';
        $this->assertTrue($handler->write($this->sessionId, $payload));

        $stored = $db->table('ci_sessions')
            ->where('id', $this->idPrefix . $this->sessionId)
            ->get()->getRowArray();

        $this->assertIsArray($stored, 'write() did not persist a ci_sessions row.');
        $this->assertSame($payload, $stored['data']);
        $this->assertNotEmpty($stored['ip_address']);

        // Re-reading with the id already loaded must still return the payload.
        $this->assertSame($payload, $handler->read($this->sessionId));

        $this->assertTrue($handler->destroy($this->sessionId));
        $this->assertSame(
            0,
            (int) $db->table('ci_sessions')->where('id', $this->idPrefix . $this->sessionId)->countAllResults(),
            'destroy() left the row behind.'
        );

        $this->assertTrue($handler->close());

        // Self-check: if the spy never fired, the lock assertions below are vacuous.
        $this->assertNotEmpty(self::$spySql, 'The DBQuery spy captured nothing.');
    }

    /**
     * The performance plan removed GET_LOCK()/RELEASE_LOCK() from the session
     * path to save two cross-region roundtrips per request. If a framework
     * minor reintroduces blocking locks, or a future edit restores them, this
     * fails instead of shipping a latency regression on a 0.1-CPU instance.
     */
    public function testNoAdvisoryLockIsIssuedOnTheSessionPath(): void
    {
        $this->armSqlSpy();

        $handler = $this->handler();
        $db      = \Config\Database::connect();

        $handler->open($db->DBPrefix . $db->database, 'ci_session');
        $handler->read($this->sessionId);
        $handler->write($this->sessionId, 'user_id|s:1:"2":');
        $handler->read($this->sessionId);
        $handler->close();

        $joined = implode("\n", self::$spySql);

        $this->assertNotEmpty($joined, 'No session query was captured, so this test proves nothing.');

        $this->assertStringNotContainsString('GET_LOCK', $joined);
        $this->assertStringNotContainsString('RELEASE_LOCK', $joined);
        $this->assertStringNotContainsString('FOR UPDATE', $joined);
    }

    public function testGarbageCollectionStillRunsOnTheInheritedMethod(): void
    {
        $this->armSqlSpy();

        $handler = $this->handler();
        $db      = \Config\Database::connect();

        // Our own rows are already deleted in setUp; gc() must still be callable
        // with the parent signature and return an int (4.7 documents bool|int).
        $result = $handler->gc(88200);

        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }
}
