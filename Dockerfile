# Use official PHP image with Apache
FROM php:8.2-apache

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl \
    && docker-php-ext-configure intl

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create a PHP info file for debugging
RUN echo '<?php phpinfo(); ?>' > /var/www/html/phpinfo.php

# Configure PHP for production
RUN { \
    echo 'display_errors=On'; \
    echo 'error_reporting=E_ALL'; \
    echo 'log_errors=On'; \
    echo 'error_log=/var/log/apache2/php_errors.log'; \
    echo 'max_execution_time=60'; \
    echo 'memory_limit=256M'; \
    echo 'post_max_size=20M'; \
    echo 'upload_max_filesize=20M'; \
} > /usr/local/etc/php/conf.d/custom.ini

# Create log directory and set permissions
RUN mkdir -p /var/log/apache2 \
    && touch /var/log/apache2/php_errors.log \
    && chown -R www-data:www-data /var/log/apache2

# Expose port 80 (Apache runs here)
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
