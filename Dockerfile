FROM php:8.1-fpm AS inventaire

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip sudo \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/html


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN sudo apt install symfony-cli
COPY . .

RUN composer install
# RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN chown www-data:www-data -R .