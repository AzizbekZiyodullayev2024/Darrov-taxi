FROM php:8.0-fpm

RUN apt-get update \
    && apt-get install \
    libzip-dev \
    libcurl4-openssl-dev \
    git \
    zip \
    curl \
    wget \
    -y

RUN docker-php-ext-install \
    zip \
    curl \
    pdo \
    pdo_mysql


RUN  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



