#!/bin/sh
set -e

# Default PORT to 8080 if not injected by the hosting platform / cloud environment
export PORT="${PORT:-8080}"

# Production environment safeguard: Always enforce production unless explicitly opting into development mode
if [ "${ALLOW_DEV_MODE:-0}" != "1" ]; then
    export CI_ENVIRONMENT="production"
fi

echo "Starting container on PORT ${PORT} (CI_ENVIRONMENT: ${CI_ENVIRONMENT}, ALLOW_DEV_MODE: ${ALLOW_DEV_MODE:-0})..."
echo "Security Audit: Debug Toolbar disabled in production mode."

# Clean legacy debugbar history and profiler cache files on container boot
rm -rf /var/www/html/writable/debugbar/*

# Substitute only ${PORT} into the Nginx config template to protect Nginx variables ($uri, $document_root, etc.)
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# Remove default Nginx site so only our port-configured site is active
rm -f /etc/nginx/sites-enabled/default

# Configure PHP-FPM with tunable defaults suitable for Render 512MB Free instance
PHP_FPM_PM="${PHP_FPM_PM:-dynamic}"
PHP_FPM_MAX_CHILDREN="${PHP_FPM_MAX_CHILDREN:-4}"
PHP_FPM_START_SERVERS="${PHP_FPM_START_SERVERS:-1}"
PHP_FPM_MIN_SPARE_SERVERS="${PHP_FPM_MIN_SPARE_SERVERS:-1}"
PHP_FPM_MAX_SPARE_SERVERS="${PHP_FPM_MAX_SPARE_SERVERS:-2}"
PHP_FPM_MAX_REQUESTS="${PHP_FPM_MAX_REQUESTS:-500}"

# FPM slowlog requires CAP_SYS_PTRACE which Render containers block.
# Default to 0 (disabled) to avoid ptrace(ATTACH) errors.
# Slow requests are tracked non-blockingly via access.format and PerformanceFilter.
FPM_SLOWLOG_TIMEOUT="${FPM_SLOWLOG_TIMEOUT:-0}"

SLOWLOG_DIRECTIVES=""
if [ "${FPM_SLOWLOG_TIMEOUT}" != "0" ] && [ -n "${FPM_SLOWLOG_TIMEOUT}" ]; then
    SLOWLOG_DIRECTIVES="request_slowlog_timeout = ${FPM_SLOWLOG_TIMEOUT}
slowlog = /proc/self/fd/2"
fi

if [ -d "/usr/local/etc/php-fpm.d" ]; then
    cat <<EOF > /usr/local/etc/php-fpm.d/zz-php-fpm.conf
[www]
clear_env = no
catch_workers_output = yes
decorate_workers_output = no
access.log = /proc/self/fd/2
access.format = "[%{%Y-%m-%d %H:%M:%S}t] %m %{REQUEST_URI}e %s %{mili}dms %{kilo}Mkb %C%%"
${SLOWLOG_DIRECTIVES}
pm = ${PHP_FPM_PM}
pm.max_children = ${PHP_FPM_MAX_CHILDREN}
pm.start_servers = ${PHP_FPM_START_SERVERS}
pm.min_spare_servers = ${PHP_FPM_MIN_SPARE_SERVERS}
pm.max_spare_servers = ${PHP_FPM_MAX_SPARE_SERVERS}
pm.max_requests = ${PHP_FPM_MAX_REQUESTS}
EOF
fi

# Configure PHP OPcache and production performance settings with env overrides
PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT:-192M}"
PHP_MAX_EXECUTION_TIME="${PHP_MAX_EXECUTION_TIME:-120}"
PHP_OPCACHE_MEMORY="${PHP_OPCACHE_MEMORY:-64}"
PHP_OPCACHE_JIT="${PHP_OPCACHE_JIT:-0}"

if [ -d "/usr/local/etc/php/conf.d" ]; then
    cat <<EOF > /usr/local/etc/php/conf.d/zz-opcache.ini
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=${PHP_OPCACHE_MEMORY}
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
opcache.jit=${PHP_OPCACHE_JIT}
opcache.jit_buffer_size=0
realpath_cache_size=4096k
realpath_cache_ttl=600
EOF

    cat <<EOF > /usr/local/etc/php/conf.d/zz-uploads.ini
upload_max_filesize=50M
post_max_size=50M
memory_limit=${PHP_MEMORY_LIMIT}
max_execution_time=${PHP_MAX_EXECUTION_TIME}
max_input_time=120
default_socket_timeout=120
expose_php=Off
log_errors=On
error_log=/proc/self/fd/2
EOF
fi

# Fail fast at startup: validate Nginx and PHP-FPM configurations before running migrations or daemons
echo "Validating Nginx and PHP-FPM configurations..."
if ! nginx_test_output=$(nginx -t 2>&1); then
    echo "ERROR: nginx configuration test failed:" >&2
    echo "$nginx_test_output" >&2
    exit 1
fi

if ! fpm_test_output=$(php-fpm -t 2>&1); then
    echo "ERROR: php-fpm configuration test failed:" >&2
    echo "$fpm_test_output" >&2
    exit 1
fi
echo "Nginx and PHP-FPM configuration tests passed."

# Ensure writable directories exist with correct permissions for www-data
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/cache/views \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/uploads/printing \
         /var/www/html/writable/uploads/business_permits \
         /var/www/html/writable/debugbar
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

mkdir -p /var/www/html/public/uploads/profiles \
         /var/www/html/public/uploads/products \
         /var/www/html/public/uploads/product_images \
         /var/www/html/public/uploads/business_permits \
         /var/www/html/public/uploads/shop_logos \
         /var/www/html/public/uploads/cms \
         /var/www/html/public/uploads/printing \
         /var/www/html/public/uploads/printing_attachments
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 775 /var/www/html/public/uploads

# Run automatic database migrations if enabled and database environment variables are present.
# Off by default: schema changes are applied deliberately (docs/DEPLOY_RENDER.md), not
# implicitly on every cold start, so a restart can never race a partially migrated schema.
RUN_MIGRATIONS="${RUN_MIGRATIONS:-false}"

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    if [ -n "$MYSQLHOST" ] || [ -n "$MYSQL_URL" ] || [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ] || [ -n "$database_default_hostname" ] || [ -n "$DATABASE_DEFAULT_HOSTNAME" ]; then
        echo "Database environment detected. Checking database connectivity before migrations..."
        # Brief retry loop (up to 6 seconds) in case the database is still booting up on cold start
        RETRY_COUNT=0
        MAX_RETRIES=3
        until php spark migrate --all || [ $RETRY_COUNT -ge $MAX_RETRIES ]; do
            RETRY_COUNT=$((RETRY_COUNT + 1))
            echo "Database migration attempt $RETRY_COUNT failed or waiting for database readiness... retrying in 2s ($RETRY_COUNT/$MAX_RETRIES)"
            sleep 2
        done
        if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
            # A half-migrated schema surfaces as per-request failures long after the
            # deploy reports success. Fail the deploy where the operator can see it.
            echo "ERROR: Database migrations failed after ${MAX_RETRIES} attempts. Refusing to boot." >&2
            exit 1
        fi
        echo "Database migrations completed successfully."
    fi
else
    echo "RUN_MIGRATIONS=false. Skipping database migrations on boot for fast wake-up."
fi

# Cache persistence guard. CACHE_HANDLER=database is what keeps pending carts
# readable across restarts (Cart::paymongoWebhook reads them by session id). If
# the handler is set but ci_cache was never migrated, every cache write fails
# silently, so make the misconfiguration visible. Warn only: this must not stop
# a boot that would otherwise succeed.
if [ "${CACHE_HANDLER:-}" = "database" ]; then
    if php spark db:table ci_cache > /dev/null 2>&1; then
        echo "CACHE_HANDLER=database and ci_cache is present."
    else
        echo "WARNING: CACHE_HANDLER=database but the ci_cache table is missing. Run 'php spark migrate --all' or unset CACHE_HANDLER." >&2
    fi
fi

# Fail fast in production if the payment webhook secret is unset or still the
# documented placeholder: with either value every PayMongo webhook fails HMAC
# verification and paid orders silently never confirm. Reference the variable
# only for emptiness/comparison so its value is never echoed into logs.
if [ "${CI_ENVIRONMENT}" = "production" ]; then
    case "${PAYMONGO_WEBHOOK_SECRET:-}" in
        ""|"whsec_placeholder_secret")
            echo "ERROR: PAYMONGO_WEBHOOK_SECRET is unset or still the placeholder value. Refusing to boot." >&2
            echo "Set the real whsec_... signing secret from the PayMongo dashboard in your host environment." >&2
            exit 1
            ;;
    esac
    echo "PAYMONGO_WEBHOOK_SECRET present."
fi

# Start PHP-FPM in the background
echo "Starting PHP-FPM daemon..."
php-fpm -D

# Start Nginx in the foreground as PID 1 to keep container alive
echo "Starting Nginx on port ${PORT}..."
exec nginx -g 'daemon off;'
