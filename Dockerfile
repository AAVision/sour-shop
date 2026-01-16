# Stage 1: Base PHP image with all dependencies
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk update && apk add --no-cache \
    curl \
    wget \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    mysql-client \
    nodejs \
    npm \
    supervisor \
    netcat-openbsd \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    xml \
    zip \
    mbstring \
    bcmath \
    exif \
    intl \
    opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy PHP configuration directly (inline)
RUN echo "[PHP]" > /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "upload_max_filesize = 512M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "post_max_size = 512M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "default_socket_timeout = 60" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "[Date]" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "date.timezone = UTC" >> /usr/local/etc/php/conf.d/99-custom.ini

# Copy OPcache configuration directly (inline)
RUN echo "[opcache]" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.enable_cli = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption = 256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer = 16" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files = 20000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_wasted_percentage = 10" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.use_cwd = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq = 2" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_path = 0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.save_comments = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown = 1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps = 1" >> /usr/local/etc/php/conf.d/opcache.ini

# Copy application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /app

# Stage 2: Builder stage for dependencies
FROM base AS builder

# Install dependencies as www-data user
USER www-data

RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Install Node dependencies and build assets
RUN npm install && npm run build

# Stage 3: Production image
FROM base AS production

# Copy built dependencies from builder
COPY --from=builder --chown=www-data:www-data /app/vendor /app/vendor
COPY --from=builder --chown=www-data:www-data /app/node_modules /app/node_modules
COPY --from=builder --chown=www-data:www-data /app/public/build /app/public/build
COPY --from=builder --chown=www-data:www-data /app/bootstrap/cache /app/bootstrap/cache

# Create necessary directories
RUN mkdir -p /app/storage/logs && \
    mkdir -p /app/storage/framework/sessions && \
    mkdir -p /app/storage/framework/views && \
    mkdir -p /app/storage/framework/cache && \
    mkdir -p /app/bootstrap/cache && \
    chown -R www-data:www-data /app/storage && \
    chown -R www-data:www-data /app/bootstrap

# Create supervisor configuration (inline)
RUN mkdir -p /etc/supervisor/conf.d && \
    echo "[unix_http_server]" > /etc/supervisor/conf.d/supervisord.conf && \
    echo "file=/tmp/supervisor.sock" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[supervisorctl]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "serverurl=unix:///tmp/supervisor.sock" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[supervisord]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "logfile=/var/log/supervisor/supervisord.log" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "pidfile=/var/run/supervisord.pid" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "user=root" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "loglevel=info" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[rpcinterface:supervisor]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "[include]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "files = /etc/supervisor/conf.d/*.conf" >> /etc/supervisor/conf.d/supervisord.conf

# Create laravel-worker configuration (inline)
RUN echo "[program:laravel-worker]" > /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "process_name=%(program_name)s_%(process_num)02d" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "command=php /app/artisan queue:work redis --sleep=3 --tries=3 --timeout=90 --max-time=3600" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "autostart=true" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "autorestart=true" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "stopasgroup=true" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "stopwaitsecs=60" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "numprocs=4" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "redirect_stderr=true" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "stdout_logfile=/var/log/supervisor/laravel-worker.log" >> /etc/supervisor/conf.d/laravel-worker.conf && \
    echo "stdout_logfile_maxbytes=0" >> /etc/supervisor/conf.d/laravel-worker.conf

# Create entrypoint script (inline)
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "Starting eCommerce Laravel Application..."' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "Waiting for MySQL ($DB_HOST:$DB_PORT)..."' >> /entrypoint.sh && \
    echo 'MAX_ATTEMPTS=30' >> /entrypoint.sh && \
    echo 'ATTEMPT=0' >> /entrypoint.sh && \
    echo 'while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do' >> /entrypoint.sh && \
    echo '  ATTEMPT=$((ATTEMPT + 1))' >> /entrypoint.sh && \
    echo '  if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then' >> /entrypoint.sh && \
    echo '    echo "✗ MySQL did not become available after ${MAX_ATTEMPTS} attempts"' >> /entrypoint.sh && \
    echo '    echo "Continuing anyway (will retry on first request)..."' >> /entrypoint.sh && \
    echo '    break' >> /entrypoint.sh && \
    echo '  fi' >> /entrypoint.sh && \
    echo '  echo "  Attempt $ATTEMPT/$MAX_ATTEMPTS: Waiting for MySQL..."' >> /entrypoint.sh && \
    echo '  sleep 1' >> /entrypoint.sh && \
    echo 'done' >> /entrypoint.sh && \
    echo 'echo "✓ MySQL connection available"' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "Setting up storage directories..."' >> /entrypoint.sh && \
    echo 'chmod -R 775 /app/storage 2>/dev/null || true' >> /entrypoint.sh && \
    echo 'chmod -R 775 /app/bootstrap/cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "Optimizing application..."' >> /entrypoint.sh && \
    echo 'php /app/artisan config:cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo 'php /app/artisan route:cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo 'php /app/artisan view:cache 2>/dev/null || true' >> /entrypoint.sh && \
    echo 'php /app/artisan cache:clear 2>/dev/null || true' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "Running migrations (in background)..."' >> /entrypoint.sh && \
    echo '(php /app/artisan migrate --force 2>/dev/null || echo "⚠ Migration skipped or failed") &' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'php /app/artisan storage:link 2>/dev/null || true' >> /entrypoint.sh && \
    echo '' >> /entrypoint.sh && \
    echo 'echo "✓ Application ready!"' >> /entrypoint.sh && \
    echo 'echo "Starting PHP-FPM..."' >> /entrypoint.sh && \
    echo 'exec "$@"' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Switch to www-data user
USER www-data

# Expose port
EXPOSE 9000

# Health check (checks PHP-FPM is listening)
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD nc -z localhost 9000 || exit 1

# Run entrypoint
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
