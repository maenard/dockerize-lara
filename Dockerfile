# Docker image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html/

# install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the Laravel project into the container
COPY . .

# Install PHP dependencies
RUN composer install

USER root

# Give permission to storage and bootstrap cache directories
RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]
