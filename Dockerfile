# Gunakan Ubuntu 22.04 LTS sebagai gambar dasar
FROM ubuntu:22.04

# Set ENV untuk non-interaktif frontend
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

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
    php8.1 \
    php8.1-cli \
    php8.1-fpm \
    php8.1-mysql \
    php8.1-pgsql \
    php8.1-zip \
    php8.1-mbstring \
    php8.1-xml \
    php8.1-curl \
    php8.1-bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . /app

# Update Composer dependencies
RUN composer update --no-interaction --prefer-dist

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader || { \
    echo "Composer install failed. Attempting to diagnose..."; \
    composer diagnose; \
    exit 1; }

# Generate application key
RUN php artisan key:generate

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose port 6000
EXPOSE 6000

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=6000
