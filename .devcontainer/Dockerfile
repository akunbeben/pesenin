# Use the official PHP image for PHP 8.2
FROM php:8.2-fpm

# Install required dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libexif-dev \
    libbcmath-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd pdo pdo_mysql intl exif bcmath

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set up Laravel workspace
WORKDIR /workspace

# Expose port 8000
EXPOSE 8000
