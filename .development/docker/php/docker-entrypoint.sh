#!/usr/bin/env sh

pecl install xdebug-3.3.0alpha3
docker-php-ext-enable xdebug

php-fpm
