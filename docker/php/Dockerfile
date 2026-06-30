FROM php:8.4-fpm

WORKDIR /api

RUN apt-get update && apt-get install -y \
    vim \
    git

RUN apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev libpng-dev libgmp-dev \
    && docker-php-ext-install pdo_mysql mysqli curl pcntl gd bcmath opcache

RUN apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    p7zip-full \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

COPY ./src-backend .

RUN chmod 777 -R storage
RUN chmod 777 -R storage/logs

RUN composer install

EXPOSE 9000