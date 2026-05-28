# ==========================================
# STAGE 1: Build Frontend Assets
# ==========================================
FROM node:20-alpine AS node-builder
WORKDIR /app

# Copy dependency files
COPY package.json package-lock.json tailwind.config.js vite.config.js ./
RUN npm ci

# Copy raw assets and views
COPY resources ./resources

# Build production assets (Vite/Tailwind v4)
RUN npm run build

# ==========================================
# STAGE 2: Production PHP & Nginx Environment
# ==========================================
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    shadow

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        zip \
        gd \
        opcache \
        bcmath \
        intl

# Set custom OPCache configuration for maximum performance
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files (ignoring files in .dockerignore)
COPY . .

# Copy built frontend assets from node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies with Composer (optimized for production)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Setup custom Nginx server block configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Setup write permissions for Laravel dynamic directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy the orchestrator script
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose Nginx port
EXPOSE 80

# Run entrypoint process orchestrator
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
