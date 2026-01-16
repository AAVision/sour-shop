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

# Copy PHP configuration
COPY docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

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

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

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
