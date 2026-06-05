FROM php:8.2-apache

# Instal ekstensi mysqli untuk koneksi ke MariaDB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy semua source code aplikasi web dinamis ke folder apache
COPY . /var/www/html/

EXPOSE 80