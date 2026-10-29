<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Base Site URL
     * --------------------------------------------------------------------------
     *
     * URL to your CodeIgniter root. Typically, this will be your base URL,
     * WITH a trailing slash:
     *
     * E.g., http://example.com/
     */
    public string $baseURL = 'https://blaxmarketplace.vercel.app/';

    /**
     * Allowed Hostnames in the Site URL other than the hostname in the baseURL.
     * If you want to accept multiple Hostnames, set this.
     *
     * E.g.,
     * When your site URL ($baseURL) is 'http://example.com/', and your site
     * also accepts 'http://media.example.com/' and 'http://accounts.example.com/':
     *     ['media.example.com', 'accounts.example.com']
     *
     * @var list<string>
     */
    public array $allowedHostnames = ['blaxmarketplace.vercel.app', 'localhost', '127.0.0.1'];

    /**
     * --------------------------------------------------------------------------
     * Index File
     * --------------------------------------------------------------------------
     *
     * Typically, this will be your `index.php` file, unless you've renamed it to
     * something else. If you have configured your web server to remove this file
     * from your site URIs, set this variable to an empty string.
     */
    public string $indexPage = 'index.php';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     *
     * This item determines which server global should be used to retrieve the
     * URI string. The default setting of 'REQUEST_URI' works for most servers.
     * If your links do not seem to work, try one of the other delicious flavors:
     *
     *  'REQUEST_URI': Uses $_SERVER['REQUEST_URI']
     * 'QUERY_STRING': Uses $_SERVER['QUERY_STRING']
     *    'PATH_INFO': Uses $_SERVER['PATH_INFO']
     *
     * WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
     */
    public string $uriProtocol = 'REQUEST_URI';

    /*
    |--------------------------------------------------------------------------
    | Allowed URL Characters
    |--------------------------------------------------------------------------
    |
    | This lets you specify which characters are permitted within your URLs.
    | When someone tries to submit a URL with disallowed characters they will
    | get a warning message.
    |
    | As a security measure you are STRONGLY encouraged to restrict URLs to
    | as few characters as possible.
    |
    | By default, only these are allowed: `a-z 0-9~%.:_-`
    |
    | Set an empty string to allow all characters -- but only if you are insane.
    |
    | The configured value is actually a regular expression character group
    | and it will be used as: '/\A[<permittedURIChars>]+\z/iu'
    |
    | DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
    |
    */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    /**
     * --------------------------------------------------------------------------
     * Default Locale
     * --------------------------------------------------------------------------
     *
     * The Locale roughly represents the language and location that your visitor
     * is viewing the site from. It affects the language strings and other
     * strings (like currency markers, numbers, etc), that your program
     * should run under for this request.
     */
    public string $defaultLocale = 'en';

    /**
     * --------------------------------------------------------------------------
     * Negotiate Locale
     * --------------------------------------------------------------------------
     *
     * If true, the current Request object will automatically determine the
     * language to use based on the value of the Accept-Language header.
     *
     * If false, no automatic detection will be performed.
     */
    public bool $negotiateLocale = false;

    /**
     * --------------------------------------------------------------------------
     * Supported Locales
     * --------------------------------------------------------------------------
     *
     * If $negotiateLocale is true, this array lists the locales supported
     * by the application in descending order of priority. If no match is
     * found, the first locale will be used.
     *
     * IncomingRequest::setLocale() also uses this list.
     *
     * @var list<string>
     */
    public array $supportedLocales = ['en'];

    /**
     * --------------------------------------------------------------------------
     * Application Timezone
     * --------------------------------------------------------------------------
     *
     * The default timezone that will be used in your application to display
     * dates with the date helper, and can be retrieved through app_timezone()
     *
     * @see https://www.php.net/manual/en/timezones.php for list of timezones
     *      supported by PHP.
     */
    public string $appTimezone = 'Asia/Manila';

    /**
     * --------------------------------------------------------------------------
     * Default Character Set
     * --------------------------------------------------------------------------
     *
     * This determines which character set is used by default in various methods
     * that require a character set to be provided.
     *
     * @see http://php.net/htmlspecialchars for a list of supported charsets.
     */
    public string $charset = 'UTF-8';

    /**
     * --------------------------------------------------------------------------
     * Force Global Secure Requests
     * --------------------------------------------------------------------------
     *
     * If true, this will force every request made to this application to be
     * made via a secure connection (HTTPS). If the incoming request is not
     * secure, the user will be redirected to a secure version of the page
     * and the HTTP Strict Transport Security (HSTS) header will be set.
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * --------------------------------------------------------------------------
     * Reverse Proxy IPs
     * --------------------------------------------------------------------------
     *
     * If your server is behind a reverse proxy, you must whitelist the proxy
     * IP addresses from which CodeIgniter should trust headers such as
     * X-Forwarded-For or Client-IP in order to properly identify
     * the visitor's IP address.
     *
     * You need to set a proxy IP address or IP address with subnets and
     * the HTTP header for the client IP address.
     *
     * IMPORTANT - do not "simplify" this to 0.0.0.0/0 and do not empty it.
     *
     * On Render, nginx and PHP-FPM run inside the SAME container (see
     * docker/nginx.conf.template: `fastcgi_pass 127.0.0.1:9000`), so REMOTE_ADDR
     * is 127.0.0.1 for every request. Emptying this list would collapse all
     * visitors into one rate-limit bucket; widening it to 0.0.0.0/0 lets any
     * caller forge their bucket with a self-supplied X-Forwarded-For entry.
     * These entries are the only peers that can legitimately open the FPM
     * socket. Rate-limit keys must not rely on this list alone - use
     * client_ip(), which takes the rightmost public X-Forwarded-For entry that
     * the load balancer appended.
     *
     * @var array<string, string>
     */
    public array $proxyIPs = [
        '127.0.0.1'     => 'X-Forwarded-For',
        '::1'           => 'X-Forwarded-For',
        '172.16.0.0/12' => 'X-Forwarded-For',
    ];

    /**
     * --------------------------------------------------------------------------
     * Content Security Policy
     * --------------------------------------------------------------------------
     *
     * Enables the Response's Content Secure Policy to restrict the sources that
     * can be used for images, scripts, CSS files, audio, video, etc. If enabled,
     * the Response object will populate default values for the policy from the
     * `ContentSecurityPolicy.php` file. Controllers can always add to those
     * restrictions at run time.
     *
     * For a better understanding of CSP, see these documents:
     *
     * @see http://www.html5rocks.com/en/tutorials/security/content-security-policy/
     * @see http://www.w3.org/TR/CSP/
     */
    public bool $CSPEnabled = true;

    public function __construct()
    {
        parent::__construct();

        $ciEnv = (defined('ENVIRONMENT') ? ENVIRONMENT : null)
            ?: (function_exists('env') ? env('CI_ENVIRONMENT') : null)
            ?: getenv('CI_ENVIRONMENT')
            ?: 'development';

        $isProduction = ($ciEnv === 'production');

        /**
         * CSP is enforced everywhere except local development.
         *
         * In debug mode the framework boots Kint/Debugbar, and
         * Autoloader::configureKint() calls
         * ContentSecurityPolicy::getScriptNonce()/getStyleNonce(). Those calls
         * lazily generate a real nonce and APPEND it to script-src/style-src, and
         * under CSP Level 3 a source list that contains a nonce makes the browser
         * IGNORE 'unsafe-inline'. This policy deliberately relies on
         * 'unsafe-inline' because app/Views/** is built from inline scripts and
         * handlers (see tests/CspHeaderTest.php), so enforcing CSP on a debug box
         * silently blocks every un-nonce'd inline script: the sign-in role
         * switcher, modals, the POS grid, dashboards - while the page itself still
         * renders, which reads as "the buttons do nothing" rather than an error.
         *
         * The value here is 'development' only: phpunit.xml.dist runs with
         * CI_ENVIRONMENT=testing and Render runs with production, so both keep the
         * header and tests/CspHeaderTest.php still guards the real policy. A policy
         * that permits 'unsafe-inline' and 'unsafe-eval' provides no protection to
         * lose locally.
         */
        $this->CSPEnabled = self::cspEnabledFor($ciEnv);

        if ($isProduction) {
            // 1. Production (Render / Cloud Container): Strict base URL and allowedHostnames to prevent Host-header injection
            $envBaseURL = (function_exists('env') ? env('APP_BASE_URL') : null)
                ?: getenv('APP_BASE_URL')
                ?: (function_exists('env') ? env('app.baseURL') : null)
                ?: getenv('app.baseURL')
                ?: getenv('RENDER_EXTERNAL_URL')
                ?: 'http://localhost:8080/';

            $this->baseURL = rtrim((string) $envBaseURL, '/') . '/';
            $this->indexPage = '';

            $configuredHost = parse_url($this->baseURL, PHP_URL_HOST);
            $this->allowedHostnames = array_values(array_unique(array_filter([
                'localhost',
                '127.0.0.1',
                $configuredHost,
                'blaxmarketplace.vercel.app',
            ])));

            // Enable forceGlobalSecureRequests safely behind Render HTTPS proxy
            if (
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            ) {
                $this->forceGlobalSecureRequests = true;
            }
        } else {
            // 2. Development / Local Laragon: Keep dynamic host detection
            if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') {
                $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
                    || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
                    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
                    || (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && (int)$_SERVER['HTTP_X_FORWARDED_PORT'] === 443);

                $scheme = $isHttps ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];

                // Determine if the app is served from a subfolder (e.g. Laragon /blax_inventory/public) or web root (Docker, Cloud Container, Spark)
                $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
                $basePath = (trim($scriptDir, '/') !== '') ? '/' . trim($scriptDir, '/') . '/' : '/';

                $this->baseURL = self::safeBaseURL(
                    $scheme . '://' . $host . $basePath,
                    $scheme . '://localhost' . $basePath
                );
                $this->indexPage = '';

                // Dynamically allow the current HTTP_HOST so SiteURIFactory always accepts it
                $hostOnly = explode(':', (string) $host)[0];
                if ($hostOnly !== '' && !in_array($hostOnly, $this->allowedHostnames, true)) {
                    $this->allowedHostnames[] = $hostOnly;
                }
            } else {
                // Non-HTTP context in development (CLI, Spark, automated testing)
                $envBaseURL = (function_exists('env') ? env('app.baseURL') : null)
                    ?: getenv('app.baseURL')
                    ?: getenv('APP_BASEURL')
                    ?: getenv('APP_BASE_URL')
                    ?: getenv('RENDER_EXTERNAL_URL');

                if (!empty($envBaseURL)) {
                    $this->baseURL = self::safeBaseURL((string) $envBaseURL);
                    $this->indexPage = '';
                } else {
                    $this->baseURL = 'http://localhost/blax_inventory/public/';
                }
            }
        }
    }

    /**
     * Whether the Content Security Policy header should be emitted for a given
     * CodeIgniter environment. Extracted from the constructor because ENVIRONMENT
     * is a constant fixed at bootstrap, so the branch cannot be exercised directly
     * per test case.
     */
    public static function cspEnabledFor(string $environment): bool
    {
        return $environment !== 'development';
    }

    /**
     * Guarantees a $baseURL the framework will accept.
     *
     * CodeIgniter 4.7 validates Config::$baseURL on every request and throws
     * `ConfigException` when PHP's filter_var(..., FILTER_VALIDATE_URL) rejects it.
     * 4.6 fell back silently, so a rejected host simply produced wrong URLs.
     * The filter rejects hostnames containing an underscore, which is exactly what
     * Laragon's generated vhost is (http://blax_inventory.test/), so an app built
     * from HTTP_HOST can hand 4.7 a value that fatals *every* page — customer,
     * tenant and admin alike — while Apache still answers HTTP 200.
     *
     * Repairing the *config value* is safe: the real host is applied per request by
     * SiteURIFactory, which passes HTTP_HOST (checked against $allowedHostnames) into
     * SiteURI::determineBaseURL(), where it overrides the host from this string. Only
     * the host syntax is normalised here; scheme, port and path are preserved.
     *
     * The production branch deliberately does NOT use this helper: a malformed
     * APP_BASE_URL there is a deploy-time mistake that should fail loudly rather
     * than be quietly rewritten.
     */
    private static function safeBaseURL(string $candidate, string $fallback = 'http://localhost/'): string
    {
        $candidate = rtrim($candidate, '/ ') . '/';

        if (filter_var($candidate, FILTER_VALIDATE_URL) !== false) {
            return $candidate;
        }

        $parts = parse_url($candidate);

        if (is_array($parts) && !empty($parts['host'])) {
            $repaired = ($parts['scheme'] ?? 'http') . '://'
                . str_replace('_', '-', (string) $parts['host'])
                . (isset($parts['port']) ? ':' . $parts['port'] : '')
                . ($parts['path'] ?? '/');
            $repaired = rtrim($repaired, '/ ') . '/';

            if (filter_var($repaired, FILTER_VALIDATE_URL) !== false) {
                return $repaired;
            }
        }

        return $fallback;
    }
}
