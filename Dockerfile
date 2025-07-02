FROM php:8.2-apache

WORKDIR /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN a2enmod rewrite

RUN groupadd -g ${USER_GID:-1000} appgroup && \
    useradd -m -u ${USER_UID:-1000} -g appgroup appuser

RUN apt-get update \
    && apt-get install -y unzip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html
