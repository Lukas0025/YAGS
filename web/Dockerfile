FROM php:7-apache

ADD  ./              /var/www/html/
COPY ./uploads.ini   /usr/local/etc/php/conf.d/uploads.ini

RUN a2enmod rewrite