FROM php:7-apache

ADD  ./              /var/www/html/
COPY ./uploads.ini   /usr/local/etc/php/conf.d/uploads.ini

RUN chmod -R 777 /var/www/html/DB && \
    chmod -R 777 /var/www/html/ARTEFACTS

RUN a2enmod rewrite