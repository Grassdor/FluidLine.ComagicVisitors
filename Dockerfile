FROM php:8.2-apache
WORKDIR /var/www/html

COPY . .
# CMD ["php", "-S", "localhost:8080"]