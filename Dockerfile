FROM php:8.2-apache
WORKDIR /var/www/html

COPY . .

RUN docker-php-ext-install pdo pdo_mysql

#RUN apt-get update && apt-get install --yes libzip-dev \ libsqlite3-dev
#RUN docker-php-ext-install mbstring

#ENV COMPOSER_ALLOW_SUPERUSER=1

ENV DB_HOST_SW="swagelok.su"
ENV DB_DATABASE_SW="fluidacy_swagelo"
ENV DB_USERNAME_SW="fluidacy_swagelo"
ENV DB_PASSWORD_SW="KTzx%3FB"

ENV DB_HOST_HL=""
ENV DB_DATABASE_HL=""
ENV DB_USERNAME_HL=""
ENV DB_PASSWORD_HL=""

ENV DB_HOST_HY=""
ENV DB_DATABASE_HY=""
ENV DB_USERNAME_HY=""
ENV DB_PASSWORD_HY=""

ENV DB_HOST_WM=""
ENV DB_DATABASE_WM=""
ENV DB_USERNAME_WM=""
ENV DB_PASSWORD_WM=""

ENV DB_HOST_CZ=""
ENV DB_DATABASE_CZ=""
ENV DB_USERNAME_CZ=""
ENV DB_PASSWORD_CZ=""

#RUN curl -sS https://getcomposer.org/installer | php -- \
#--install-dir=/usr/bin --filename=composer

#RUN composer update

#RUN composer install
# CMD ["php", "-S", "localhost:8080"]