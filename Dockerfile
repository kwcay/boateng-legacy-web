# Use a PHP 7.2 image
FROM php:7.2.4-fpm

# Install some software
RUN apt-get update \
        && apt-get install -y libmcrypt-dev --no-install-recommends \
        && docker-php-ext-install mcrypt pdo_mysql

#
WORKDIR /var/www
ADD . /var/www

# Install dependencies
RUN composer install
RUN yarn
