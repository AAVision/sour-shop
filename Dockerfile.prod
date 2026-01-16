# ================================
# Builder stage
# ================================
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies + dev libs for PHP extensions
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    zip \
    libxml2-dev \
    zlib-dev \
    bzip2-dev \
    gmp-dev \
    libsodium-dev \
    oniguruma-dev \
    autoconf \
    gcc \
    g++ \
    make \
    pkgconfig \
    nodejs \
    npm \
    sqlite \
    libzip-dev \
    openssl

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    json \
    xml \
    zip \
    gmp \
    bcmath \
    curl \
    sodium \
    opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy application source
COPY . .

# Build PHP & Node dependencies
RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build \
    && rm -rf node_modules

# ================================
# Production stage
# ================================
FROM php:8.2-fpm-alpine

# Install runtime dependencies only
RUN apk add --no-cache \
    curl \
    gmp \
    libsodium \
    openssl \
    libxml2 \
    oniguruma \
    zip \
    bash \
    nodejs \
    npm

# Install PHP extensions again for runtime
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    json \
    xml \
    zip \
    gmp \
    bcmath \
    curl \
    sodium \
    opcache

# Copy php config
COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini

# Set working directory
WORKDIR /app

# Copy built app from builder stage
COPY --from=builder --chown=www-data:www-data /app /app

# Create necessary directories & set permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap \
    && chmod -R 777 /app/storage /app/bootstrap \
    && chmod -R 775 /app/app/Providers /app/lang /app/routes

# Expose HTTP port (Laravel Octane optional)
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]
