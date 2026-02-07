FROM php:8.2-cli

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    wget \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN wget https://getcomposer.org/download/latest-stable/composer.phar -O composer \
    && chmod +x composer \
    && mv composer /usr/local/bin/composer

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --optimize-autoloader --prefer-dist || true

# Copy application files
COPY . .

# Create storage directories
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Set permissions for artisan
RUN chmod +x artisan

# Generate autoloader
RUN composer dump-autoload --optimize || true

# Run Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]