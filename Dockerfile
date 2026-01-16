# Builder stage
FROM php:8.2-fpm-alpine as builder

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    sqlite \
    bash \
    openssl \
    libxml2-dev \
    zlib-dev \
    bzip2-dev \
    gmp-dev \
    libsodium-dev \
    oniguruma-dev \
    nodejs \
    npm \
    bash \
    pkgconfig \
    make \
    gcc \
    musl-dev

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    mbstring \
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

# Copy application files
COPY . .

# Build dependencies
RUN composer install --no-dev --optimize-autoloader && \
    npm install && \
    npm run build && \
    rm -rf node_modules

# Production stage
FROM php:8.2-fpm-alpine

# Install runtime dependencies
RUN apk add --no-cache \
    openssl \
    libxml2 \
    gmp \
    libsodium \
    curl \
    oniguruma

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    mbstring \
    json \
    xml \
    zip \
    gmp \
    bcmath \
    curl \
    sodium \
    opcache

# Copy PHP configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-local.ini

# Set working directory
WORKDIR /app

# Copy built application from builder
COPY --from=builder --chown=www-data:www-data /app /app

# Set permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache && \
    chown -R www-data:www-data /app/storage /app/bootstrap && \
    chmod -R 777 /app/storage /app/bootstrap && \
    chmod -R 775 /app/app/Providers /app/lang /app/routes

# Expose port (Laravel Octane will run on this)
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]
