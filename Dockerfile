FROM php:8.0-apache

RUN \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli

RUN apt-get update
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

COPY ./docker/php.ini /usr/local/etc/php/conf.d/php.ini

RUN a2enmod rewrite

RUN apt update
RUN apt install mariadb-server -y

# el orden de este puede cambiar
RUN chown -R www-data:www-data /var/www

# Revisar
RUN pwd /var/www/public
RUN chmod 777 .
RUN pwd /var/www/storage
RUN chmod 777 .
RUN pwd /tmp
RUN chmod 777 .

EXPOSE 80

WORKDIR /var/www/html