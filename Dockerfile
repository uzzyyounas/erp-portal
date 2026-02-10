FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip libpng-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip gd

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer \
    && composer install --no-dev --optimize-autoloader

EXPOSE 10000

CMD php artisan config:clear && \
    php artisan migrate --force || true && \
    php -S 0.0.0.0:10000 -t public

