# Dockerfile (Optimized for Development)
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies (Không thay đổi)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (Không thay đổi)
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache

# Install Redis extension (Không thay đổi)
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer (Không thay đổi)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# # Install PHP dependencies (Sẽ chạy thủ công bên trong container)
# RUN composer install --no-dev --optimize-autoloader --no-interaction

# # Set permissions (Volume mount sẽ sử dụng quyền từ máy host)
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 755 /var/www/storage \
#     && chmod -R 755 /var/www/bootstrap/cache

# Copy configuration files
# Note: In development, these are overridden by volume mounts in docker-compose.yml
# Uncomment for production builds without volume mounts
# COPY docker/nginx.conf /etc/nginx/sites-available/default
# COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Create log directories
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /var/log/nginx \
    && mkdir -p /var/log/php

# Expose port
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]