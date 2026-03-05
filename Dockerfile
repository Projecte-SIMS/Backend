FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y git unzip libpq-dev libzip-dev libpng-dev libonig-dev libxml2-dev libssl-dev build-essential pkg-config --no-install-recommends \
    && docker-php-ext-install pdo_pgsql mbstring xml bcmath zip gd \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install composer binary from official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Install composer dependencies if composer.json exists
RUN if [ -f /var/www/html/composer.json ]; then composer install --prefer-dist --no-interaction --optimize-autoloader; fi || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Exponemos el puerto 8000 por defecto
EXPOSE 8000

# Comando final: LIMPIEZA TOTAL, Migración y Seedado automático (Usuarios/Roles)
CMD php artisan config:cache && \
    php artisan migrate:fresh --seed --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
