<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class CsrfProtectionTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /**
     * Config\Security::$redirect is true in this app, so the CSRF filter catches the
     * SecurityException and bounces the request (system/Filters/CSRF.php:54) instead
     * of letting it bubble. Asserting on the exception therefore tested a strategy
     * this application does not use, and could never pass.
     *
     * What has to be proven is that the controller never runs. Cart::add() sends an
     * unauthenticated visitor to /login, so a redirect whose target is NOT /login can
     * only have come from the filter's redirect()->back().
     */
    public function testCsrfBlocksPostWithoutToken()
    {
        $response = $this->post('cart/add', ['product_id' => 1, 'quantity' => 1]);

        self::assertTrue(
            $response->isRedirect(),
            'A token-less POST was neither blocked nor redirected; CSRF is not enforcing.'
        );

        $location = (string) $response->getRedirectUrl();

        self::assertNotSame('', $location, 'Blocked request carried no redirect target.');
        self::assertStringEndsNotWith('/login', $location,
            'The request reached Cart::add() instead of being stopped by the CSRF filter.'
        );

        // The filter bounces with redirect()->back()->with('error', $e->getMessage());
        // only the filter puts an error in the session for a request this anonymous.
        $response->assertSessionHas('error');
    }

    /**
     * The AJAX branch is the one the real UI exercises: cart, POS and checkout all post
     * through fetch(), and for those the filter re-throws so the caller gets a 403
     * instead of an HTML redirect it cannot follow.
     */
    public function testCsrfThrowsOnAjaxPostWithoutToken()
    {
        $this->expectException(\CodeIgniter\Security\Exceptions\SecurityException::class);

        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post('cart/add', ['product_id' => 1, 'quantity' => 1]);
    }

    public function testCsrfExemptsPaymongoWebhook()
    {
        // payment/webhook should not throw SecurityException even without CSRF token
        $result = $this->withBody('{"test":1}')->post('payment/webhook');
        // Webhook signature validator should run and return 400, not CSRF exception
        $result->assertStatus(400);
        $this->assertStringContainsString('Missing signature header', $result->getBody());
    }
}
