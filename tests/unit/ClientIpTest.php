<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit tests for the client_ip() helper in app/Common.php.
 *
 * The rate-limit filters derive their bucket key from this value, so a forged
 * X-Forwarded-For entry must not be able to move an attacker to a fresh bucket.
 * Render's edge appends the true peer address, which is why the *rightmost*
 * public entry wins.
 */
class ClientIpTest extends CIUnitTestCase
{
    private ?string $originalForwarded = null;
    private ?string $originalRemote     = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalForwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
        $this->originalRemote     = $_SERVER['REMOTE_ADDR'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->originalForwarded === null) {
            unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            $_SERVER['HTTP_X_FORWARDED_FOR'] = $this->originalForwarded;
        }

        if ($this->originalRemote === null) {
            unset($_SERVER['REMOTE_ADDR']);
        } else {
            $_SERVER['REMOTE_ADDR'] = $this->originalRemote;
        }

        parent::tearDown();
    }

    private function setForwarded(string $value): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $value;
    }

    public function testRightmostPublicEntryWins()
    {
        $this->setForwarded('8.8.8.8, 1.1.1.1');

        $this->assertSame('1.1.1.1', client_ip(),
            'Must return the rightmost (load-balancer-appended) entry, never the client-supplied first one.');
    }

    public function testForgedLeadingPublicEntryIsIgnored()
    {
        $this->setForwarded('4.4.4.4, 9.9.9.9');

        $this->assertSame('9.9.9.9', client_ip());
    }

    public function testPrivateLeadingEntryIsSkipped()
    {
        $this->setForwarded('10.0.0.1, 9.9.9.9');

        $this->assertSame('9.9.9.9', client_ip());
    }

    public function testFallsBackToRemoteAddrWhenNoPublicEntryExists()
    {
        $_SERVER['REMOTE_ADDR']             = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR']    = '10.0.0.1, 192.168.1.5, 127.0.0.1';

        // No entry qualifies, so the un-forgable socket peer is used.
        $this->assertSame('127.0.0.1', client_ip());
    }

    public function testFallsBackWhenHeaderAbsent()
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '8.8.4.4';

        $this->assertSame('8.8.4.4', client_ip());
    }

    public function testHandlesIpWithPortSuffix()
    {
        $this->setForwarded('9.9.9.9:5678');

        $this->assertSame('9.9.9.9', client_ip(),
            'An IPv4-with-port entry must still resolve to the bare address.');
    }
}
