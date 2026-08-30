FROM php:8.4-fpm-alpine AS base

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
    $PHPIZE_DEPS

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

RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
    pecl install xdebug-3.4.0 && \
    docker-php-ext-enable xdebug; \
    fi

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN php -m | grep -E 'gd|zip|xdebug|pdo_pgsql|opcache|redis'

FROM base AS runtime

RUN pecl install redis \
    && docker-php-ext-enable redis;

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer


RUN addgroup -g 1000 www && \
    adduser -D -u 1000 -G www www

RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R www:www /var/www

RUN mkdir -p /run/secrets && \
    chown -R www:www /run/secrets


USER www

EXPOSE 9000

CMD ["php-fpm"]