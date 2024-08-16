# Gunakan Ubuntu 22.04 LTS sebagai gambar dasar
FROM ubuntu:22.04

# Set ENV untuk non-interaktif frontend
ENV DEBIAN_FRONTEND=noninteractive

# Update dan instal dependensi
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update && apt-get install -y \
    php8.0 \
    php8.0-cli \
    php8.0-fpm \
    php8.0-mysql \
    php8.0-pgsql \
    php8.0-zip \
    php8.0-mbstring \
    php8.0-xml \
    php8.0-curl \
    php8.0-bcmath \
    && apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose port 8000
EXPOSE 6000

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=6000
