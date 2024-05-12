FROM php:8.2-apache
WORKDIR /var/www/html

COPY . .

ENV DB_HOST_SW=""
ENV DB_DATABASE_SW=""
ENV DB_USERNAME_SW=""
ENV DB_PASSWORD_SW=""

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

RUN curl -sS https://getcomposer.org/installer | php -- \
--install-dir=/usr/bin --filename=composer

RUN composer install
# CMD ["php", "-S", "localhost:8080"]