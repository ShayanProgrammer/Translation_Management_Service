FROM php:8.2-fpm

### Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    vim \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

### Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

### Set working directory
WORKDIR /var/www

COPY . .

RUN composer install

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache