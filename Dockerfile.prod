# -------------------------------
# Builder stage
# -------------------------------
FROM php:8.3-fpm-alpine AS builder

# Install system dependencies and dev libraries for PHP extensions
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    zip \
    libxml2-dev \
    zlib-dev \
    bzip2-dev \
    gmp-dev \
    libsodium-dev \
    oniguruma-dev \
    libzip-dev \
    autoconf \
    gcc \
    g++ \
    make \
    pkgconfig \
    openssl \
    nodejs \
    npm \
    curl \
    # GD extension dependencies
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev

# Install PHP extensions including GD
# Note: We're installing extensions in the builder stage to satisfy composer requirements
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mbstring \
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

# Copy app source
COPY . .

# Build PHP + Node dependencies
RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build \
    && rm -rf node_modules

# -------------------------------
# Production stage
# -------------------------------
FROM php:8.3-fpm-alpine

# Install system dependencies (only runtime)
RUN apk add --no-cache \
    bash \
    curl \
    gmp \
    libsodium \
    openssl \
    libxml2 \
    oniguruma \
    zip \
    nodejs \
    npm \
    # GD runtime dependencies
    freetype \
    libjpeg-turbo \
    libpng \
    libwebp

# Install PHP extensions using docker-php-ext-install directly
# This bypasses the problematic build system
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxml2-dev \
    zlib-dev \
    gmp-dev \
    libsodium-dev \
    oniguruma-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mbstring \
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
    opcache \
    && apk del --no-cache .build-deps

# Alternative: Use pecl for extensions if docker-php-ext-install fails
# RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
#     && pecl install redis \
#     && docker-php-ext-enable redis \
#     && apk del .build-deps

# Copy PHP config if exists
COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini 2>/dev/null || echo "No local.ini found, using default PHP config"

# Set working directory
WORKDIR /app

# Copy built app from builder
COPY --from=builder --chown=www-data:www-data /app /app

# Set permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap \
    && chmod -R 777 /app/storage /app/bootstrap \
    && chmod -R 775 /app/app/Providers /app/lang /app/routes

# Create php-fpm user if it doesn't exist
RUN addgroup -g 1000 -S www-data && adduser -u 1000 -S www-data -G www-data || true

# Expose port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

# Start PHP-FPM
CMD ["php-fpm", "-R"]