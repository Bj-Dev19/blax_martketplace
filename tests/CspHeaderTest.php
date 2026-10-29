<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class CspHeaderTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function tearDown(): void
    {
        \Config\Services::resetSingle('renderer');
        parent::tearDown();
    }

    public function testCspIsEnabledAndConfigured()
    {
        $csp = service('csp');
        $this->assertTrue($csp->enabled());

        $response = service('response');
        $csp->finalize($response);

        $this->assertTrue($response->hasHeader('Content-Security-Policy'));
        $header = $response->getHeaderLine('Content-Security-Policy');
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $header);
        $this->assertStringContainsString('fonts.googleapis.com', $header);
        $this->assertStringContainsString('fonts.gstatic.com', $header);
        $this->assertStringContainsString('maps.googleapis.com', $header);
        $this->assertStringContainsString('checkout.paymongo.com', $header);
        $this->assertStringContainsString("'unsafe-inline'", $header);

        // The policy the app *declares* must stay nonce-free: once script-src or
        // style-src carries a `nonce-` source, browsers ignore 'unsafe-inline',
        // and app/Views/** depends on it. Checked on a fresh config object so it
        // cannot be masked by runtime mutations of the shared CSP service.
        $policy = new \Config\ContentSecurityPolicy();
        $this->assertFalse($policy->autoNonce);
        $this->assertStringNotContainsString('nonce', implode(' ', (array) $policy->scriptSrc));
        $this->assertStringNotContainsString('nonce', implode(' ', (array) $policy->styleSrc));

        // CodeIgniter 4.7 registers CSP nonces for Kint's rich renderer whenever
        // CI_DEBUG is on (Autoloader::configureKint()), so the emitted header does
        // carry one under development/testing. Production is unaffected because
        // docker/entrypoint.sh forces CI_ENVIRONMENT=production, which defines
        // CI_DEBUG as false and never loads Kint.
        if (! CI_DEBUG) {
            $this->assertStringNotContainsString('nonce-', $header);
        }

        // Clickjacking: frame-ancestors must be emitted. It was previously absent
        // because Config\ContentSecurityPolicy::$frameAncestors was null.
        $this->assertStringContainsString('frame-ancestors', $header);
        $this->assertStringContainsString("frame-ancestors 'self'", $header);
    }

    /**
     * script-src still requires unsafe-inline/unsafe-eval because app/Views/**
     * ships inline scripts. Pinned deliberately: removing either is a separate,
     * larger refactor and must be an explicit, reviewed change.
     */
    public function testScriptSrcStillCarriesInlineExceptions()
    {
        $header = $this->buildHeader();

        $this->assertStringContainsString("'unsafe-inline'", $header);
        $this->assertStringContainsString("'unsafe-eval'", $header);
    }

    private function buildHeader(): string
    {
        $response = service('response');
        service('csp')->finalize($response);

        return $response->getHeaderLine('Content-Security-Policy');
    }
}
