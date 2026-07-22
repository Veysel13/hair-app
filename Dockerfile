# --- Stage 1: Build Assets with Node.js ---
FROM node:20-alpine AS node_builder
WORKDIR /app
COPY package*.json vite.config.js ./
COPY resources/ ./resources/
COPY .env* ./
RUN npm install && npm run build

# --- Stage 2: Production Environment ---
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_sqlite zip opcache bcmath mbstring xml

# Configure PHP-FPM to listen on a Unix socket
RUN sed -i 's/listen = 9000/listen = \/var\/run\/php-fpm.sock/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/;listen.mode = 0660/listen.mode = 0666/' /usr/local/etc/php-fpm.d/www.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy compiled assets from node_builder
COPY --from=node_builder /app/public/build ./public/build

# Install Laravel composer dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Set folder permissions for Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Make entrypoint script executable
RUN chmod +x /var/www/html/docker/entrypoint.sh

# Expose Nginx default port
EXPOSE 80

# Run entrypoint script
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
