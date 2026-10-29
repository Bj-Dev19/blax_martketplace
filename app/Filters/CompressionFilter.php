<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Response Compression Filter
 *
 * Gzips textual responses at the framework layer so compression depends on the
 * application rather than on the web server. Local Laragon/Apache ships with
 * mod_deflate commented out in httpd.conf, and Render's nginx already has gzip
 * on; doing it here means both environments (and any future one) behave the same.
 *
 * Runs as the last global "after" filter, which in CodeIgniter is after the
 * required filters (PageCache, DebugToolbar) have finished mutating the body,
 * so whatever they append is compressed too and the page cache still stores
 * the uncompressed document.
 *
 * Deliberately a no-op when:
 *  - the body was already encoded upstream (mod_deflate / nginx / zlib.output_compression)
 *  - the client did not declare gzip support
 *  - the payload is binary (PDF receipts, uploads) or too small to amortise the CPU
 */
class CompressionFilter implements FilterInterface
{
    /** Below this many bytes gzip's headers and framing can make the response larger. */
    private const MIN_BYTES = 1024;

    /** Level 5 halves a ~290 KB HTML document for a few milliseconds of CPU; level 9 costs far more for ~2 percent. */
    private const LEVEL = 5;

    /** Only these content types are compressed. Everything binary (PDF, images, zip) is skipped. */
    private const TEXT_TYPES = [
        'text/html',
        'text/plain',
        'text/css',
        'text/xml',
        'text/javascript',
        'application/javascript',
        'application/json',
        'application/xml',
        'application/manifest+json',
        'application/x-web-app-manifest+json',
        'image/svg+xml',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Compression is a response-side concern only.
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (is_cli() || ! extension_loaded('zlib')) {
            return;
        }

        // Never touch a body something else already encoded, or PHP would double-compress it.
        if (trim($response->getHeaderLine('Content-Encoding')) !== '') {
            return;
        }
        if (ini_get('zlib.output_compression')) {
            return;
        }

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            return;
        }

        // Browsers always send this; curl without --compressed does not, and would get garbage.
        if (stripos($request->getHeaderLine('Accept-Encoding'), 'gzip') === false) {
            return;
        }

        $contentType = strtolower(trim(explode(';', $response->getHeaderLine('Content-Type'))[0]));
        if (! in_array($contentType, self::TEXT_TYPES, true)) {
            return;
        }

        $body = $response->getBody();
        if (! is_string($body) || strlen($body) < self::MIN_BYTES) {
            return;
        }

        $gzipped = @gzencode($body, self::LEVEL);
        if (! is_string($gzipped) || strlen($gzipped) >= strlen($body)) {
            return; // incompressible payload: keep it as-is rather than add overhead
        }

        $response->setBody($gzipped);
        $response->setHeader('Content-Encoding', 'gzip');
        $response->setHeader('Content-Length', (string) strlen($gzipped));

        // Caches must key on this or a gzip client could be served a plain body and vice versa.
        $vary = trim($response->getHeaderLine('Vary'));
        if ($vary === '') {
            $response->setHeader('Vary', 'Accept-Encoding');
        } elseif (stripos($vary, 'accept-encoding') === false) {
            $response->setHeader('Vary', $vary . ', Accept-Encoding');
        }
    }
}
