# ── Stage 1: Node Build (compile Vite/Tailwind assets) ──────────────────────
FROM node:20-alpine AS assets-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci --no-audit --prefer-offline

COPY resources/ ./resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./

RUN npm run build

# ── Stage 2: PHP Composer Dependencies ──────────────────────────────────────
FROM php:8.2-fpm-alpine AS composer-builder
WORKDIR /app

RUN apk add --no-cache \
        git \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libzip-dev \
        zip \
        unzip \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
        --no-interaction \
        --prefer-dist \
        --no-dev \
        --optimize-autoloader

# ── Stage 3: Final Production Image ─────────────────────────────────────────
FROM php:8.2-fpm-alpine AS production
WORKDIR /var/www/html

LABEL maintainer="NANDSKILLS Team <dev@nandskills.com>"
LABEL org.opencontainers.image.title="NANDSKILLS EduOS"
LABEL org.opencontainers.image.version="1.0.0"

# Install system dependencies
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng \
        libjpeg-turbo \
        libwebp \
        libzip \
        oniguruma \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache

# OPcache configuration for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=32'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.fast_shutdown=0'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# PHP-FPM configuration
RUN { \
    echo '[www]'; \
    echo 'pm = dynamic'; \
    echo 'pm.max_children = 20'; \
    echo 'pm.start_servers = 5'; \
    echo 'pm.min_spare_servers = 2'; \
    echo 'pm.max_spare_servers = 8'; \
} > /usr/local/etc/php-fpm.d/www.conf

# Nginx configuration
COPY docker/nginx/nandskills.conf /etc/nginx/http.d/default.conf

# Supervisord configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application files
COPY --from=composer-builder /app/vendor ./vendor
COPY --from=assets-builder  /app/public/build ./public/build
COPY . .

# Set proper permissions
RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www \
    && chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/nginx /var/run/nginx

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/api/v1/docs || exit 1
