<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Guards the deploy-time boot policy for payment configuration.
 *
 * A missing PayMongo webhook secret only disables ONE endpoint: the webhook
 * handler fails closed (app/Controllers/Cart.php::paymongoWebhook logs the
 * variable name and returns HTTP 400). Every customer, tenant and admin page is
 * unaffected. docker/entrypoint.sh used to `exit 1` on that condition, which
 * turned a degraded payment feature into a total outage - Render restart-looped
 * the container, so the whole marketplace answered 502.
 *
 * These tests scan the shipped sources rather than booting a container, which
 * is the only way to assert shell-script behaviour from the PHPUnit suite.
 */
class DeployBootPolicyTest extends CIUnitTestCase
{
    private function entrypoint(): string
    {
        $path = ROOTPATH . 'docker/entrypoint.sh';
        $this->assertFileExists($path);

        return (string) file_get_contents($path);
    }

    /**
     * The section that decides what to do about a missing webhook secret.
     */
    private function paymongoSection(): string
    {
        $script = $this->entrypoint();

        $start = strpos($script, 'Payment webhook secret: warn loudly');
        $end   = strpos($script, '# Start PHP-FPM');

        $this->assertNotFalse($start, 'entrypoint.sh must document the webhook-secret boot policy');
        $this->assertNotFalse($end, 'entrypoint.sh must still start PHP-FPM');

        return substr($script, (int) $start, (int) $end - (int) $start);
    }

    public function testMissingWebhookSecretWarnsAndServesInsteadOfRefusingToBoot(): void
    {
        $section = $this->paymongoSection();

        // A warning path must exist, and it must say the site still serves.
        $this->assertStringContainsString(
            'WARNING: PAYMONGO_WEBHOOK_SECRET is unset or still a placeholder value.',
            $section
        );
        $this->assertStringContainsString('The site will serve', $section);

        // The old unconditional refusal must not come back.
        $this->assertStringNotContainsString(
            'is unset or still the placeholder value. Refusing to boot.',
            $section,
            'A missing webhook secret must not stop the container: it only disables webhook verification'
        );
    }

    public function testRefusingToBootRequiresAnExplicitOptIn(): void
    {
        $section = $this->paymongoSection();

        $gate = strpos($section, 'REQUIRE_PAYMONGO_WEBHOOK:-0}" = "1"');
        $exit = strpos($section, 'exit 1');

        $this->assertNotFalse($gate, 'The hard failure must be gated behind REQUIRE_PAYMONGO_WEBHOOK');
        $this->assertNotFalse($exit, 'The opt-in branch must be able to fail the boot');
        $this->assertLessThan(
            $exit,
            $gate,
            'exit 1 must only be reachable after the REQUIRE_PAYMONGO_WEBHOOK check'
        );
    }

    public function testShippedTemplateSecretIsRecognisedAsAPlaceholder(): void
    {
        // Whatever .env.render.example ships must be caught by the shell case
        // list, otherwise an operator who copies the template gets no warning.
        $example = (string) file_get_contents(ROOTPATH . '.env.render.example');
        $this->assertSame(1, preg_match('/^PAYMONGO_WEBHOOK_SECRET\s*=\s*(\S+)/m', $example, $m));

        $templateValue = $m[1];
        $this->assertStringStartsWith('whsec_your', $templateValue);
        $this->assertStringContainsString('whsec_your*', $this->paymongoSection());
    }

    public function testServiceConstructionIsNotFatalWhenPaymentsAreUnconfigured(): void
    {
        // Constructing the service must stay side-effect free: a page that merely
        // builds it (wallet balance, payment-state listing) must not 500 just
        // because production has no PayMongo key.
        $source = (string) file_get_contents(APPPATH . 'Services/PaymongoService.php');

        $this->assertSame(
            1,
            preg_match('/public function __construct\([^)]*\)[^{]*\{([^}]*)\}/s', $source, $constructor),
            'PaymongoService constructor not found'
        );
        $this->assertStringNotContainsString(
            'assertConfiguredSecret()',
            $constructor[1],
            'The secret check must not run in the constructor'
        );

        // ... but the first outbound call must still fail loudly.
        $this->assertSame(
            1,
            preg_match('/private function request\([^)]*\)[^{]*\{[^}]*assertConfiguredSecret\(\)/s', $source),
            'request() must assert the secret before calling PayMongo'
        );
    }
}
