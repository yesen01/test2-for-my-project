# Allow selecting PHP version (default 8.3)
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm-bullseye

# Install system dependencies and PHP extensions required for Laravel, MySQL, BCMath and GD
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       ca-certificates \
       curl \
       gnupg \
       unzip \
       zip \
       git \
       nginx \
       supervisor \
       libpng-dev \
       libjpeg62-turbo-dev \
       libfreetype6-dev \
       libwebp-dev \
       libonig-dev \
       libxml2-dev \
       libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql bcmath gd zip opcache \
    && pecl install redis || true \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Install composer binary from official composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer config first to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist || true

# Copy application code
COPY . .

# Copy Aiven CA cert if provided in repo storage (optional)
# If you keep your CA outside the repo, you can set MYSQL_ATTR_SSL_CA to a mounted path or a secret.
COPY storage/certs/cacert.pem /etc/ssl/certs/aiven-ca.pem

# Nginx config
COPY docker/nginx.conf /etc/nginx/sites-enabled/default
# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set correct permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy entrypoint and make it executable
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
