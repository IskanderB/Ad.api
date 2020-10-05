FROM php:7.3-apache

RUN docker-php-ext-install \
    pdo_mysql \
    && a2enmod \
    rewrite

RUN apt-get update -y && apt-get install -y nano

RUN rm /etc/apache2/sites-available/000-default.conf

COPY 000-default.conf /etc/apache2/sites-available
