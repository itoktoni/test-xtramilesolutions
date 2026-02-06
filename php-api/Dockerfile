FROM php:8.1-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    wget \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN wget https://getcomposer.org/download/latest-stable/composer.phar -O composer \
    && chmod +x composer \
    && mv composer /usr/local/bin/composer

COPY composer.json ./
RUN composer install --optimize-autoloader --no-scripts

COPY . .

EXPOSE 8080

# Run tests
RUN chmod +x vendor/bin/phpunit

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]