FROM php:7.3-apache
WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install pdo_mysql \
 && a2enmod rewrite \
 && service apache2 restart

COPY . .

RUN composer install --no-dev

EXPOSE 80
