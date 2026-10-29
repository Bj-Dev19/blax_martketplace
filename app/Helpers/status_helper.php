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

if (!function_exists('placeholder_image_url')) {
    /**
     * Local stand-in for a missing/broken image.
     *
     * Used to point at images.unsplash.com, so every product without a photo cost a
     * cross-origin DNS + TLS round trip and failed outright offline. These ship with
     * the app, are cacheable like any other asset, and need no third-party availability.
     *
     * @param string $kind 'image' (square product tile) or 'banner' (wide cover)
     */
    function placeholder_image_url(string $kind = 'image'): string
    {
        $file = $kind === 'banner' ? 'banner.svg' : 'image.svg';

        return base_url('assets/placeholder/' . $file);
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Resolve a product image to a renderable URL with Cloudinary optimization.
     * Default variant is 'card' (480x480 max with f_auto,q_auto).
     * Pass 'thumbnail' for 160x160 or 'detail' for 960x960.
     * Preserves backward compatibility if a fallback URL string is passed as 2nd argument.
     */
    function product_image_url(?string $url, string $variant = 'card', string $fallback = ''): string
    {
        if (str_starts_with($variant, 'http://') || str_starts_with($variant, 'https://')) {
            $fallback = $variant;
            $variant  = 'card';
        }

        if ($url === null || trim($url) === '') {
            return $fallback !== '' ? $fallback : placeholder_image_url();
        }

        $url = trim($url);

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        // Validate local file existence on disk (handles ephemeral container redeployment where local files are wiped)
        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return $fallback !== '' ? $fallback : placeholder_image_url();
            }
        }

        return base_url($url);
    }
}

if (!function_exists('cms_image_url')) {
    /**
     * Resolve a CMS banner or content image to a renderable URL with Cloudinary optimization.
     * Default variant is 'banner' (1440 max width with f_auto,q_auto).
     */
    function cms_image_url(?string $url, string $variant = 'banner', string $fallback = ''): string
    {
        if (str_starts_with($variant, 'http://') || str_starts_with($variant, 'https://')) {
            $fallback = $variant;
            $variant  = 'banner';
        }

        $url = trim((string) $url);
        if ($url === '') {
            return $fallback !== '' ? $fallback : placeholder_image_url('banner');
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return cloudinary_transform_url($url, $variant);
        }

        if (defined('FCPATH')) {
            $localDiskPath = FCPATH . ltrim($url, '/\\');
            if (!is_file($localDiskPath)) {
                return $fallback !== '' ? $fallback : placeholder_image_url('banner');
            }
        }

        return base_url($url);
    }
}

if (!function_exists('logo_url')) {
    /**
     * Resolve a shop logo to a renderable URL with Cloudinary optimization.
     * Default variant is 'logo' (200x200 max with f_auto,q_auto).
     */
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
                return ''; // Safely return empty string so views render clean letter badge
            }
        }

        return base_url($url);
    }
}

if (!function_exists('profile_image_url')) {
    /**
     * Resolve a user or customer profile image to a renderable URL with Cloudinary optimization.
     * Default variant is 'avatar' (100x100 face crop with f_auto,q_auto).
     */
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
                return ''; // Safely return empty string so views render user initial badge
            }
        }

        return base_url($url);
    }
}

if (!function_exists('cover_photo_url')) {
    /**
     * Resolve a shop cover photo / banner to a renderable URL with Cloudinary optimization.
     * Default variant is 'banner' (1440 max width with f_auto,q_auto).
     */
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
        return ucwords(str_replace(['_', '-'], ' ', (string) $status));
    }
}

if (!function_exists('status_badge')) {
    /**
     * Render a status pill consistent with the Stitch prototypes.
     */
    function status_badge(?string $status, string $extra = ''): string
    {
        $status = strtolower((string) $status);
        $map = [
            // pending / amber
            'pending'          => ['bg-amber-100 text-amber-800 border border-amber-300', 'dot:amber-600'],
            'new'              => ['bg-amber-100 text-amber-800 border border-amber-300', 'dot:amber-600'],
            'inactive'         => ['bg-surface-variant text-on-surface-variant border border-outline-variant/40', 'dot:on-surface-variant'],
            // processing / blue
            'processing'       => ['bg-blue-100 text-blue-800 border border-blue-300', 'dot:blue-600'],
            'transfer_pending' => ['bg-purple-100 text-purple-800 border border-purple-300', 'dot:purple-600'],
            'in_production'    => ['bg-blue-100 text-blue-800 border border-blue-300', 'dot:blue-600'],
            'under_review'     => ['bg-blue-100 text-blue-800 border border-blue-300', 'dot:blue-600'],
            // transit / ready
            'shipped'          => ['bg-indigo-100 text-indigo-800 border border-indigo-300', 'dot:indigo-600'],
            'in_transit'       => ['bg-indigo-100 text-indigo-800 border border-indigo-300', 'dot:indigo-600'],
            'ready_for_pickup' => ['bg-teal-100 text-teal-800 border border-teal-300', 'dot:teal-600'],
            'ready_for_delivery' => ['bg-teal-100 text-teal-800 border border-teal-300', 'dot:teal-600'],
            'flagged'          => ['bg-orange-100 text-orange-800 border border-orange-300', 'dot:orange-600'],
            // positive / completed / green
            'active'           => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'completed'        => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'delivered'        => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'returned'         => ['bg-purple-100 text-purple-800 border border-purple-300', 'dot:purple-600'],
            'success'          => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'resolved'         => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'verified'         => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'in_stock'         => ['bg-emerald-100 text-emerald-800 border border-emerald-300', 'dot:emerald-600'],
            'low_stock'        => ['bg-amber-100 text-amber-800 border border-amber-300', 'dot:amber-600'],
            'out_of_stock'     => ['bg-rose-100 text-rose-800 border border-rose-300', 'dot:rose-600'],
            // negative / cancelled / red
            'cancelled'        => ['bg-rose-100 text-rose-800 border border-rose-300', 'dot:rose-600'],
            'failed'           => ['bg-rose-100 text-rose-800 border border-rose-300', 'dot:rose-600'],
            'suspended'        => ['bg-rose-100 text-rose-800 border border-rose-300', 'dot:rose-600'],
            'rejected'         => ['bg-rose-100 text-rose-800 border border-rose-300', 'dot:rose-600'],
            'refunded'         => ['bg-rose-100 text-rose-800 border border-rose-300', 'dot:rose-600'],
        ];

        if (isset($map[$status])) {
            [$classes, $dot] = $map[$status];
        } else {
            [$classes, $dot] = ['bg-surface-variant text-on-surface-variant', 'dot:on-surface-variant'];
        }

        $dotColor = str_replace('dot:', '', $dot);
        $label = humanize_status($status);

        return '<span class="inline-flex items-center gap-1.5 px-sm py-xs rounded-full text-[11px] font-semibold uppercase tracking-wide ' . $classes . ' ' . $extra . '"><span class="w-1.5 h-1.5 rounded-full bg-' . $dotColor . '"></span>' . esc($label) . '</span>';
    }
}
