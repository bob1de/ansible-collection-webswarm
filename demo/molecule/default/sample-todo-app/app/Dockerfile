FROM php:7-apache

RUN docker-php-ext-install pdo_mysql

COPY --chown=www-data:www-data config.php index.php /var/www/html/
