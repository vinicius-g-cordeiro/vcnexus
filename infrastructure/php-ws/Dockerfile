FROM php:8.4-cli-alpine AS base

RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    postgresql-dev \
    $PHPIZE_DEPS

FROM base AS builder

ARG INSTALL_XDEBUG=false

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    zip \
    gd \
    intl \
    mbstring \
    opcache \
    exif \
    pcntl \
    bcmath \
    soap \
    sockets \
    pgsql \
    pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN php -m | grep -E 'gd|zip|pgsql|pdo_pgsql|opcache'

FROM base AS runtime

RUN pecl install redis \
    && docker-php-ext-enable redis;
   
WORKDIR /var/www

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer
COPY ./backend/composer.json ./backend/composer.lock ./

RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

COPY ./backend . 


CMD ["php", "bin/websocket-server.php"]