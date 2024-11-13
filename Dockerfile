# Step 1: Use PHP 8.2 base image
FROM php:8.2-fpm

# Step 2: Install necessary dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    curl \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*

# Step 3: Install Redis extension for PHP
RUN pecl install redis-6.1.0 \
    && docker-php-ext-enable redis

# Step 4: Set working directory
WORKDIR /var/www

# Step 5: Copy application files to container
COPY . /var/www

# Step 6: Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Step 7: Install PHP dependencies using Composer
RUN composer install --no-scripts --no-progress

# Step 8: Expose the port that the Symfony app will run on
EXPOSE 9000

# Step 9: Start the PHP-FPM server
CMD ["php-fpm"]
