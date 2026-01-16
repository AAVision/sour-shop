FROM alpine:3.19

# Install PHP 8.3 and all extensions from Alpine repositories
RUN apk add --no-cache \
    php83 \
    php83-fpm \
    php83-gd \
    php83-mbstring \
    php83-mysqli \
    php83-pdo_mysql \
    php83-xml \
    php83-zip \
    php83-gmp \
    php83-bcmath \
    php83-curl \
    php83-sodium \
    php83-opcache \
    php83-tokenizer \
    php83-fileinfo \
    php83-dom \
    php83-simplexml \
    php83-xmlwriter \
    php83-openssl \
    php83-phar \
    php83-json \
    php83-session \
    composer \
    nodejs \
    npm \
    bash \
    curl \
    git \
    unzip

# Create symlinks for PHP 8.3
RUN ln -s /usr/bin/php83 /usr/bin/php && \
    ln -s /usr/sbin/php-fpm83 /usr/sbin/php-fpm

# Configure PHP-FPM
COPY docker/php-fpm.conf /etc/php83/php-fpm.d/www.conf

# Set working directory
WORKDIR /app

# Copy application
COPY . .

# Install dependencies and build
RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build \
    && rm -rf node_modules

# Set permissions
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]