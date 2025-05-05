FROM php:8.3-fpm

RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install -y curl

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini