FROM php:8.4-fpm-alpine

RUN apk update && apk add --no-cache \
    bash \
    nginx \
    openssl \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    linux-headers \
    zlib-dev \
    libpng-dev \
    libxml2-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    bcmath \
    opcache \
    pcntl \
    sockets \
    intl \
    zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apk del $PHPIZE_DEPS

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . .

COPY ./start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 9000

ENTRYPOINT [ "/start.sh" ]
