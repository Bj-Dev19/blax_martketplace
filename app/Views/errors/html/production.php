<?php
// Stream critical error details to Docker/Render stdout/stderr so they appear in live logs
if (isset($exception) && $exception instanceof \Throwable) {
    error_log(sprintf(
        '[Production 500] %s: %s in %s:%d' . PHP_EOL . 'Trace: %s',
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));
}

$showDebugDetails = false;
try {
    $session = \Config\Services::session();
    $role = (string) ($session->get('user_role') ?? $session->get('role') ?? '');
    $isStaff = in_array($role, ['admin', 'shop_owner', 'tenant'], true);
    $req = \Config\Services::request();
    $debugParam = $req ? $req->getGet('debug_err') : null;
    $showDebugDetails = $isStaff || ($debugParam === '1');
} catch (\Throwable $e) {}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= lang('Errors.whoops') ?></title>

    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
</head>
<body>

    <div class="container text-center">

        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>

        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>

        <?php if ($showDebugDetails && isset($exception) && $exception instanceof \Throwable): ?>
            <div style="margin-top: 2rem; text-align: left; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 800px; margin-left: auto; margin-right: auto; font-family: monospace; font-size: 13px;">
                <div style="color: #e11d48; font-weight: bold; margin-bottom: 0.5rem;">
                    [Staff Debug Detail] <?= esc(get_class($exception)) ?>: <?= esc($exception->getMessage()) ?>
                </div>
                <div style="color: #64748b; margin-bottom: 1rem;">
                    Location: <?= esc($exception->getFile()) ?> : line <?= (int) $exception->getLine() ?>
                </div>
                <details>
                    <summary style="cursor: pointer; color: #2563eb; font-weight: bold;">View Stack Trace</summary>
                    <pre style="margin-top: 0.5rem; background: #f8fafc; padding: 1rem; border-radius: 8px; overflow-x: auto; white-space: pre-wrap; word-break: break-all; color: #334155; max-height: 300px; overflow-y: auto;"><?= esc($exception->getTraceAsString()) ?></pre>
                </details>
            </div>
        <?php endif; ?>

        <div style="margin-top: 1.5rem;">
            <a href="javascript:history.back()" style="display: inline-block; padding: 0.5rem 1.25rem; font-size: 13px; font-weight: 600; color: #059669; text-decoration: none; border: 1px solid #059669; border-radius: 8px;">&larr; Go Back</a>
            <a href="<?= base_url('tenant/dashboard') ?>" style="display: inline-block; margin-left: 0.5rem; padding: 0.5rem 1.25rem; font-size: 13px; font-weight: 600; color: #fff; background: #059669; text-decoration: none; border-radius: 8px;">Dashboard</a>
        </div>

    </div>

</body>

</html>
