FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    oniguruma-dev

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]