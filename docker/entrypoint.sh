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
FPM_SLOWLOG_TIMEOUT="${FPM_SLOWLOG_TIMEOUT:-2s}"

if [ -d "/usr/local/etc/php-fpm.d" ]; then
    cat <<EOF > /usr/local/etc/php-fpm.d/zz-php-fpm.conf
[www]
clear_env = no
catch_workers_output = yes
decorate_workers_output = no
access.log = /proc/self/fd/2
access.format = "[%{%Y-%m-%d %H:%M:%S}t] %m %{REQUEST_URI}e %s %{mili}dms %{kilo}Mkb %C%%"
request_slowlog_timeout = ${FPM_SLOWLOG_TIMEOUT}
slowlog = /proc/self/fd/2
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

# Run automatic database migrations if enabled and database environment variables are present
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    if [ -n "$MYSQLHOST" ] || [ -n "$MYSQL_URL" ] || [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ] || [ -n "$database_default_hostname" ] || [ -n "$DATABASE_DEFAULT_HOSTNAME" ]; then
        echo "Database environment detected. Checking database connectivity before migrations..."
        # Brief retry loop (up to 6 seconds) in case MySQL is still booting up on cold start
        RETRY_COUNT=0
        MAX_RETRIES=3
        until php spark migrate --all || [ $RETRY_COUNT -ge $MAX_RETRIES ]; do
            RETRY_COUNT=$((RETRY_COUNT + 1))
            echo "Database migration attempt $RETRY_COUNT failed or waiting for MySQL readiness... retrying in 2s ($RETRY_COUNT/$MAX_RETRIES)"
            sleep 2
        done
        if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
            echo "Warning: Database migrations could not be completed automatically. The application will continue booting."
        else
            echo "Database migrations completed successfully."
        fi
    fi
else
    echo "RUN_MIGRATIONS=false. Skipping database migrations on boot for fast wake-up."
fi

# Start PHP-FPM in the background
echo "Starting PHP-FPM daemon..."
php-fpm -D

# Start Nginx in the foreground as PID 1 to keep container alive
echo "Starting Nginx on port ${PORT}..."
exec nginx -g 'daemon off;'
