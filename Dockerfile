FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    openssl-dev \
    build-base \
    pkgconfig \
    curl \
    autoconf

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    xml \
    bcmath \
    zip \
    gd

# Install MongoDB extension
RUN apk add --no-cache autoconf build-base pkgconfig && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    apk del autoconf build-base pkgconfig

# Install composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-dev || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true && \
    chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
