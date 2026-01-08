FROM php:8.4-fpm

# Copy standard PHP configuration file to php.ini
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install Xdebug & copy xdebug settings
RUN pecl install xdebug \
   && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update && apt-get install -y \
    git curl zip unzip nano \
    supervisor \
    libzip-dev libpng-dev \
    libonig-dev libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer

ARG UID=1000
ARG GID=1000

RUN groupadd -g ${GID} appgroup \
    && useradd -m -u ${UID} -g appgroup appuser

WORKDIR /var/www/html

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Change ownership of workdir
RUN chown -R appuser:appgroup /var/www/html

USER appuser

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]