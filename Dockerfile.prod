# -------------------------------
# Builder stage
# -------------------------------
FROM php:8.3-fpm-alpine AS builder  # Changed from 8.2 to 8.3

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
FROM php:8.3-fpm-alpine  # Changed from 8.2 to 8.3

# Runtime dependencies and dev packages for building PHP extensions
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
    npm \
    # GD runtime dependencies
    freetype \
    libjpeg-turbo \
    libpng \
    libwebp \
    # Add dev packages for building PHP extensions
    libxml2-dev \
    zlib-dev \
    bzip2-dev \
    gmp-dev \
    libsodium-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    autoconf \
    gcc \
    g++ \
    make \
    pkgconfig

# Install PHP extensions (only in production stage)
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

# Clean up dev packages to reduce image size
RUN apk del --no-cache \
    libxml2-dev \
    zlib-dev \
    bzip2-dev \
    gmp-dev \
    libsodium-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    autoconf \
    gcc \
    g++ \
    make \
    pkgconfig

# Copy PHP config
COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini

# Set working directory
WORKDIR /app

# Copy built app from builder
COPY --from=builder --chown=www-data:www-data /app /app

# Set permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap \
    && chmod -R 777 /app/storage /app/bootstrap \
    && chmod -R 775 /app/app/Providers /app/lang /app/routes

# Expose port (optional for Octane)
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]