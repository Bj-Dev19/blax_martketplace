<?php

use CodeIgniter\Test\CIUnitTestCase;
use Config\App;

/**
 * Guards two boot-time failures that take down every portal at once.
 *
 * Both were introduced by the CodeIgniter 4.6 -> 4.7 upgrade. 4.6 tolerated them;
 * 4.7 turned each into a site-wide outage that the rest of the suite could not see,
 * because phpunit.xml.dist pins CI_ENVIRONMENT=testing and a clean baseURL:
 *
 *  1. Config\App::$baseURL is now validated on EVERY request by
 *     SiteURI::normalizeBaseURL(), which throws ConfigException when PHP's
 *     filter_var(..., FILTER_VALIDATE_URL) rejects it. App\Config\App builds the
 *     development baseURL from HTTP_HOST, and the filter rejects hostnames
 *     containing an underscore - so Laragon's own vhost (blax_inventory.test) made
 *     every customer, tenant and admin page return a fatal error inside an HTTP 200.
 *
 *  2. Debug mode appends a CSP nonce to script-src/style-src, and a source list
 *     containing a nonce makes browsers ignore 'unsafe-inline' - which this app's
 *     policy depends on. Enforcing CSP locally blocks every inline script in
 *     app/Views/**, so the UI renders but is inert.
 */
class AppBootConfigTest extends CIUnitTestCase
{
    /** @var array<string, mixed> */
    private array $originalServer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;

        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidHostProvider(): array
    {
        return [
            'laragon underscore vhost' => ['blax_inventory.test', 'http://blax-inventory.test/'],
            'underscore with port'     => ['my_app.test:8080', 'http://my-app.test:8080/'],
            'nested underscores'       => ['a_b_c.localhost', 'http://a-b-c.localhost/'],
        ];
    }

    /**
     * @dataProvider invalidHostProvider
     */
    public function testBaseUrlIsAlwaysFrameworkValidOnUnderscoredHosts(string $httpHost, string $expected): void
    {
        $_SERVER['HTTP_HOST']     = $httpHost;
        $_SERVER['SCRIPT_NAME']   = '/index.php';
        $_SERVER['SERVER_PORT']   = '80';
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO']);

        $config = new App();

        $this->assertNotFalse(
            filter_var($config->baseURL, FILTER_VALIDATE_URL),
            "Config\App::\$baseURL '{$config->baseURL}' would fatal every page under CodeIgniter 4.7."
        );
        $this->assertSame($expected, $config->baseURL);

        // The real host must still be accepted per-request, because
        // SiteURIFactory::getValidHost() drops any host missing from this list and
        // SiteURI then restores it over the sanitised placeholder.
        $hostOnly = explode(':', $httpHost)[0];
        $this->assertContains($hostOnly, $config->allowedHostnames);
    }

    public function testCleanHostsAreLeftUntouched(): void
    {
        $_SERVER['HTTP_HOST']   = 'blaxmarketplace.test';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO']);

        $config = new App();

        $this->assertSame('http://blaxmarketplace.test/', $config->baseURL);
    }

    public function testSubfolderDeploymentsKeepTheirBasePath(): void
    {
        $_SERVER['HTTP_HOST']   = 'localhost';
        $_SERVER['SCRIPT_NAME'] = '/blax_inventory/public/index.php';
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO']);

        $config = new App();

        $this->assertSame('http://localhost/blax_inventory/public/', $config->baseURL);
        $this->assertNotFalse(filter_var($config->baseURL, FILTER_VALIDATE_URL));
    }

    public function testCspIsDisabledOnlyInDevelopment(): void
    {
        // Development: a debug-bar nonce would neutralise 'unsafe-inline' and
        // silently kill every inline script in app/Views/**.
        $this->assertFalse(
            App::cspEnabledFor('development'),
            'CSP must stay off in development or the whole UI is inert.'
        );

        // Testing: phpunit.xml.dist runs here, and tests/CspHeaderTest.php asserts
        // the real policy, so enforcement must remain on.
        $this->assertTrue(App::cspEnabledFor('testing'));

        // Production: Render runs here, so this is where the policy is enforced.
        $this->assertTrue(App::cspEnabledFor('production'));
    }

    public function testConstructedConfigMatchesTheRuleForTheRunningEnvironment(): void
    {
        // ENVIRONMENT is a constant, so this asserts the wiring rather than the
        // branch. The suite runs under 'testing', which must emit the header.
        $config = new App();

        $this->assertSame(App::cspEnabledFor(ENVIRONMENT), $config->CSPEnabled);
        $this->assertTrue($config->CSPEnabled, 'The suite must keep CSP on, or CspHeaderTest stops guarding anything.');
    }
}
