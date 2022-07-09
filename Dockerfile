FROM php:7.4-fpm-alpine

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

# Install pdo pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# Redis
# https://developpaper.com/question/error-when-php-installs-redis-phpize-failed/
RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install -o -f redis  \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini\
    && rm -rf /usr/share/php \
    && rm -rf /tmp/* \
    && apk del .phpize-deps \
    && docker-php-ext-enable redis

# Zip
RUN apk add --no-cache zip

RUN docker-php-ext-install pcntl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# Laravel Horizon
RUN composer require laravel/horizon --ignore-platform-reqs
RUN php artisan horizon:publish

EXPOSE 80

CMD ["php-fpm"]
