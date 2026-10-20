FROM php:8.4-cli-alpine AS base

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    mysql-client \
    linux-headers \
    imagemagick \
    imagemagick-dev \
    $PHPIZE_DEPS

RUN pecl install imagick \
    && docker-php-ext-enable imagick

FROM base AS builder

ARG INSTALL_XDEBUG=false

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    intl \
    mbstring \
    opcache \
    exif \
    pcntl \
    bcmath \
    soap 

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN php -m | grep -E 'gd|zip|xdebug|pdo_pgsql|opcache|redis'

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

CMD ["php", "bin/queue-worker.php"]