<?php

use CodeIgniter\Test\CIUnitTestCase;
use Config\Security as SecurityConfig;
use Config\Session as SessionConfig;

/**
 * Verifies CSRF token stability, session destruction cookie cleanup,
 * concurrent guest session write protection, and graceful redirect behavior.
 *
 * @internal
 */
final class CsrfAndSessionLifecycleTest extends CIUnitTestCase
{
    public function testSecurityConfigEnablesGracefulRedirectOnCsrfFailure(): void
    {
        $securityConfig = new SecurityConfig();

        $this->assertTrue(
            $securityConfig->redirect,
            'Config\Security::$redirect must be true so stale/expired CSRF tokens redirect back with a fresh token instead of throwing a 403 SecurityException screen.'
        );
        $this->assertSame('session', $securityConfig->csrfProtection);
        $this->assertFalse($securityConfig->regenerate);
    }

    public function testSessionConfigDisablesPrematureFiveMinuteIdRotationRace(): void
    {
        $sessionConfig = new SessionConfig();

        $this->assertSame(7200, $sessionConfig->expiration);
        $this->assertSame(
            7200,
            $sessionConfig->timeToUpdate,
            'Config\Session::$timeToUpdate should match $expiration (7200) to avoid 5-minute session ID rotation races with non-locking OptimizedDatabaseHandler.'
        );
    }

    public function testOptimizedDatabaseHandlerDestroyCallsDestroyCookieAndProtectsConcurrentGuestWrites(): void
    {
        $handlerFile = ROOTPATH . 'app/Session/Handlers/OptimizedDatabaseHandler.php';
        $this->assertFileExists($handlerFile);

        $source = (string) file_get_contents($handlerFile);

        // 1. Verify destroy() invokes close() and destroyCookie()
        $this->assertStringContainsString('$this->destroyCookie();', $source);
        $this->assertStringContainsString('if ($this->close()) {', $source);

        // 2. Verify concurrent unauthenticated guest writes do not overwrite existing data/CSRF hash
        $this->assertStringContainsString('$preserveExistingGuestData = (! $this->rowExists && ! $isAuthenticated && ! str_contains($data, \'__ci_vars\'));', $source);
        $this->assertStringContainsString('ON DUPLICATE KEY UPDATE `ip_address` = VALUES(`ip_address`), `timestamp` = NOW()"', $source);
    }

    public function testAuthLogoutClearsSessionKeysAndDestroysSession(): void
    {
        $authFile = ROOTPATH . 'app/Controllers/Auth.php';
        $this->assertFileExists($authFile);

        $source = (string) file_get_contents($authFile);

        $this->assertStringContainsString('$session->remove([', $source);
        $this->assertStringContainsString("'isLoggedIn'", $source);
        $this->assertStringContainsString('$session->destroy();', $source);
    }

    public function testLoginViewSyncsCsrfTokenOnTabVisibilityAndHandlesExpiredSessionFlash(): void
    {
        $loginView = ROOTPATH . 'app/Views/auth/login.php';
        $this->assertFileExists($loginView);

        $source = (string) file_get_contents($loginView);

        $this->assertStringContainsString('<?= csrf_field() ?>', $source);
        $this->assertStringContainsString('Your session expired or was refreshed. Please sign in again.', $source);
        $this->assertStringContainsString('function syncLoginCsrfToken()', $source);
    }

    public function testAdminLayoutMarkAllReadSendsFormDataWithCsrfToken(): void
    {
        $adminLayout = ROOTPATH . 'app/Views/layouts/admin.php';
        $this->assertFileExists($adminLayout);

        $source = (string) file_get_contents($adminLayout);

        $this->assertStringContainsString('body: new FormData(markAllForm)', $source);
    }
}

