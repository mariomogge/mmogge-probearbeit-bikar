FROM php:8.4-fpm-alpine

RUN apk add --no-cache git icu-dev libpq-dev zlib-dev oniguruma-dev bash openssl \
&& docker-php-ext-install intl pdo_pgsql opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

CMD ["php-fpm"]