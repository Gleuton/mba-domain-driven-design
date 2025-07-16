#!/bin/sh
set -e
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
rm -rf /tmp/opcache
php artisan key:generate
exec php-fpm