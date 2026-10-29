<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Static contract tests for the toast-notification JavaScript.
 *
 * PHP cannot execute the browser code, so these tests scan the view files the
 * same way NotificationBellUiTest does and assert the two invariants that the
 * audit found broken:
 *
 *   1. Contract  - the shared window.showToast(message, type) implementations
 *      take a *string* type. Passing a boolean silently falls through every
 *      branch and renders an error as a green success toast.
 *   2. Encoding  - no implementation interpolates caller-supplied text into an
 *      innerHTML string. Those strings originate on the server (JSON responses,
 *      flash data, notification rows built from customer names and uploaded
 *      file names), so interpolation is a stored-XSS sink.
 *
 * A page-local `showToast(message, isError)` with a *boolean* second parameter
 * is a different, legitimate API (app/Views/tenant/settings.php). Because the
 * hoisted function declaration shadows window.showToast for its whole script
 * block, those call sites must NOT be "fixed" to strings, so the owning file is
 * exempt from the contract scan.
 */
class ToastContractTest extends CIUnitTestCase
{
    /**
     * Files defining the shared window.showToast implementations.
     *
     * @var array<string, string>
     */
    private array $implementations = [
        'components/head'    => 'Views/components/head.php',
        'layouts/admin'      => 'Views/layouts/admin.php',
        'layouts/marketplace' => 'Views/layouts/marketplace.php',
    ];

    /**
     * Files whose local showToast takes a boolean flag instead of a type string.
     *
     * @var array<int, string>
     */
    private array $booleanApiFiles = [
        'Views/tenant/settings.php',
    ];

    private function view(string $relative): string
    {
        $path = APPPATH . $relative;
        $this->assertFileExists($path, "View file {$relative} is missing");

        return (string) file_get_contents($path);
    }

    /**
     * Every view that reaches a shared string-typed showToast must pass a quoted
     * type, never a bare boolean.
     */
    public function testNoBooleanPassedToSharedShowToast(): void
    {
        $exempt = [];
        foreach ($this->booleanApiFiles as $relative) {
            $exempt[realpath(APPPATH . $relative)] = true;
        }

        $violations = [];
        $iterator   = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(APPPATH . 'Views', \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            if (isset($exempt[realpath($file->getPathname())])) {
                continue;
            }

            $content = (string) file_get_contents($file->getPathname());

            if (preg_match('/showToast\([^;\n]{0,200},\s*!?(true|false)\s*[,)]/', $content)) {
                $violations[] = str_replace(APPPATH, '', $file->getPathname());
            }
        }

        $this->assertSame(
            [],
            $violations,
            'showToast(message, type) takes a string type; boolean call sites render errors as success.'
        );
    }

    /**
     * The exemption list must stay honest: a whitelisted file has to actually
     * declare the boolean-signature function, otherwise it should be scanned.
     */
    public function testBooleanApiFilesReallyDeclareABooleanSignature(): void
    {
        foreach ($this->booleanApiFiles as $relative) {
            $content = $this->view($relative);

            $this->assertMatchesRegularExpression(
                '/function\s+showToast\s*\(\s*\w+\s*,\s*(isError|is_error|error)\s*\)/',
                $content,
                "{$relative} is exempt from the boolean scan only while it defines a boolean API."
            );
        }
    }

    /**
     * Each shared implementation declares its second parameter as a string type
     * defaulting to a success flavour.
     *
     * @dataProvider implementationProvider
     */
    public function testSharedImplementationTakesStringType(string $relative): void
    {
        $content = $this->view($relative);

        if (str_contains($relative, 'admin') || str_contains($relative, 'marketplace')) {
            // Object-argument API: the type is read from opts, not a positional bool.
            $this->assertMatchesRegularExpression(
                '/window\.showToast\s*=\s*function\s*\(\s*opts\s*\)/',
                $content,
                "{$relative} must keep the single object-argument showToast signature."
            );
            $this->assertMatchesRegularExpression(
                "/opts\.type\s*\|\|\s*'(info|success)'/",
                $content,
                "{$relative} must resolve the toast type to a string default."
            );

            return;
        }

        $this->assertMatchesRegularExpression(
            "/window\.showToast\s*=\s*function\s*\(\s*message\s*,\s*type\s*=\s*'success'/",
            $content,
            "{$relative} must declare showToast(message, type = 'success', ...)."
        );
    }

    /**
     * @return array<string, array{0, string}>
     */
    public static function implementationProvider(): array
    {
        return [
            'head.php'        => ['Views/components/head.php'],
            'admin.php'       => ['Views/layouts/admin.php'],
            'marketplace.php' => ['Views/layouts/marketplace.php'],
        ];
    }

    /**
     * No caller-supplied value may be interpolated into the toast markup.
     *
     * @dataProvider implementationProvider
     */
    public function testMessageIsNeverInterpolatedIntoInnerHtml(string $relative): void
    {
        $content = $this->view($relative);

        foreach (['message', 'title', 'sub'] as $variable) {
            $this->assertDoesNotMatchRegularExpression(
                '/\$\{' . $variable . '\}/',
                $content,
                "{$relative} must not interpolate \${$variable} into an innerHTML template."
            );
        }

        $this->assertStringContainsString(
            '.textContent =',
            $content,
            "{$relative} must render toast text through textContent."
        );
    }

    /**
     * The realtime poll renderers inject customer/shop/order text into the
     * merchant and admin sessions and must go through textContent as well.
     */
    public function testRealtimeToastRenderersUseTextContent(): void
    {
        $targets = [
            'Views/layouts/admin.php'   => 'showAdminToast',
            'Views/layouts/tenant.php'  => 'showRealtimeToast',
        ];

        foreach ($targets as $relative => $function) {
            $content = $this->view($relative);

            $this->assertStringContainsString('function ' . $function . '(', $content, "{$relative} lost {$function}()");

            foreach (['title', 'sub'] as $variable) {
                $this->assertDoesNotMatchRegularExpression(
                    '/\$\{' . $variable . '\}/',
                    $content,
                    "{$relative}::{$function} must not interpolate \${$variable} (poll payload text) into innerHTML."
                );
                $this->assertMatchesRegularExpression(
                    '/\.textContent\s*=\s*' . $variable . ';/',
                    $content,
                    "{$relative}::{$function} must assign \${$variable} via textContent."
                );
            }
        }
    }

    /**
     * The object-argument layouts render an optional action link. Its href must
     * be assigned after a scheme check, never spliced into the template.
     *
     * @dataProvider objectApiProvider
     */
    public function testActionLinkHrefIsSchemeChecked(string $relative): void
    {
        $content = $this->view($relative);

        $this->assertDoesNotMatchRegularExpression(
            '/href="\$\{url\}"/',
            $content,
            "{$relative} must not interpolate the url into an href attribute."
        );
        $this->assertStringContainsString('blax-toast-link', $content);
        $this->assertStringContainsString("setAttribute('href', candidate)", $content);
        $this->assertMatchesRegularExpression(
            '/!\s*candidate\.startsWith\(\'\/\/\'\)/',
            $content,
            "{$relative} must reject protocol-relative and scheme-based hrefs."
        );
    }

    /**
     * @return array<string, array{0, string}>
     */
    public static function objectApiProvider(): array
    {
        return [
            'admin.php'       => ['Views/layouts/admin.php'],
            'marketplace.php' => ['Views/layouts/marketplace.php'],
        ];
    }

    /**
     * Toasts are transient status messages: the container is a polite live
     * region and error toasts escalate to role="alert".
     *
     * @dataProvider implementationProvider
     */
    public function testToastContainersAreLiveRegions(string $relative): void
    {
        $content = $this->view($relative);

        $this->assertStringContainsString("aria-live', 'polite'", $content);
        $this->assertStringContainsString("aria-atomic', 'false'", $content);
        $this->assertStringContainsString("setAttribute('role', 'alert')", $content);
    }
}
