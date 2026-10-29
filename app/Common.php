<?php

if (!function_exists('cloudinary_transform_url')) {
    /**
     * Transform a Cloudinary image URL to a named preset (card, thumbnail, avatar, detail, logo, banner).
     * Automatically applies optimal format (WebP/AVIF) and quality compression.
     */
    function cloudinary_transform_url(?string $url, string $variant = 'card'): string
    {
        return \App\Libraries\CloudinaryService::transformUrl($url, $variant);
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Resolve a product image to a renderable URL with Cloudinary optimization.
     * Default variant is 'card' (480x480 max with f_auto,q_auto).
     * Pass 'thumbnail' for 160x160 or 'detail' for 960x960.
     */
    function product_image_url(?string $url, string $variant = 'card', string $fallback = 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=600&q=80'): string
    {
        if (str_starts_with($variant, 'http://') || str_starts_with($variant, 'https://')) {
            $fallback = $variant;
            $variant  = 'card';
        }

        if ($url === null || trim($url) === '') {
            return $fallback;
        }

        $url = trim($url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return $fallback;
            }
        }

        return base_url($url);
    }
}

if (!function_exists('cms_image_url')) {
    function cms_image_url(?string $url, string $variant = 'banner', string $fallback = 'https://images.unsplash.com/photo-1556742049-0a67daf64f42?auto=format&fit=crop&w=1440&q=80'): string
    {
        if (str_starts_with($variant, 'http://') || str_starts_with($variant, 'https://')) {
            $fallback = $variant;
            $variant  = 'banner';
        }

        $url = trim((string) $url);
        if ($url === '') {
            return $fallback;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return $fallback;
            }
        }

        return base_url($url);
    }
}

if (!function_exists('logo_url')) {
    function logo_url(?string $url, string $variant = 'logo'): string
    {
        if ($url === null || trim($url) === '') {
            return '';
        }

        $url = trim($url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return '';
            }
        }

        return base_url($url);
    }
}

if (!function_exists('profile_image_url')) {
    function profile_image_url(?string $url, string $variant = 'avatar'): string
    {
        if ($url === null || trim($url) === '') {
            return '';
        }

        $url = trim($url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return '';
            }
        }

        return base_url($url);
    }
}

if (!function_exists('cover_photo_url')) {
    function cover_photo_url(?string $url, string $variant = 'banner', string $fallback = ''): string
    {
        if (str_starts_with($variant, 'http://') || str_starts_with($variant, 'https://')) {
            $fallback = $variant;
            $variant  = 'banner';
        }

        $url = trim((string) $url);
        if ($url === '') {
            return $fallback;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return $fallback;
            }
        }

        return base_url($url);
    }
}

if (!function_exists('humanize_status')) {
    function humanize_status(?string $status): string
    {
        if ($status === null || trim($status) === '') {
            return 'Pending';
        }
        $normalized = str_replace(['_', '-'], ' ', strtolower(trim($status)));
        return ucwords($normalized);
    }
}

if (!function_exists('client_ip')) {
    /**
     * Resolve the real client IP for rate-limit keys behind a load balancer.
     *
     * On Render, nginx and PHP-FPM share a single container, so REMOTE_ADDR is
     * always 127.0.0.1 and CodeIgniter's own proxy trust list left-to-right scan
     * lets a caller forge the bucket by sending their own X-Forwarded-For entry.
     * The edge *appends* the true peer address, so the rightmost non-private,
     * non-reserved entry is the one the client cannot control.
     *
     * Note: FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE are the valid
     * PHP flags; there is no FILTER_FLAG_NO_PUB_RANGE.
     */
    function client_ip(?\CodeIgniter\HTTP\RequestInterface $request = null): string
    {
        $header = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');

        if ($header !== '') {
            $entries = array_reverse(array_map('trim', explode(',', $header)));

            foreach ($entries as $entry) {
                if ($entry === '') {
                    continue;
                }

                if (filter_var(
                        $entry,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                    ) !== false) {
                    return $entry;
                }

                // Tolerate an IPv4-with-port form such as 203.0.113.7:5678.
                if (str_contains($entry, ':') && ! str_contains($entry, '.')) {
                    continue; // bare IPv6 address already failed the checks above
                }

                $host = substr($entry, 0, strrpos($entry, ':'));

                if (filter_var(
                        $host,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                    ) !== false) {
                    return $host;
                }
            }
        }

        $fromRequest = $request !== null ? $request->getIPAddress() : '';

        return $fromRequest !== '' && $fromRequest !== '0.0.0.0'
            ? $fromRequest
            : (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }
}
