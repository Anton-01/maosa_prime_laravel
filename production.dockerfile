# ==========================================
# Etapa 1: Construcción de dependencias PHP
# ==========================================
FROM composer:latest AS vendor
WORKDIR /app

# 1. Copiamos solo los archivos de dependencias para aprovechar el caché de Docker
COPY src/composer.json src/composer.lock ./

# 2. Instalamos paquetes SIN generar el autoloader ni ejecutar scripts de Laravel
RUN composer install --no-dev --ignore-platform-reqs --no-interaction --prefer-dist --no-scripts --no-autoloader

# 3. Ahora sí, copiamos todo el código de la aplicación (incluyendo tu GlobalHelper)
COPY src/ ./

# BORRAR LA CACHÉ LOCAL COPIADA
RUN rm -f bootstrap/cache/*.php

# 4. Generamos el autoloader optimizado y ejecutamos los scripts post-install
RUN composer dump-autoload --optimize --no-dev && \
    php artisan package:discover --ansi

# ==========================================
# Etapa 2: Construcción de assets (Vite)
# ==========================================
FROM node:18-alpine AS frontend
WORKDIR /app
COPY src/package.json src/package-lock.json ./
RUN npm ci
COPY src/ ./
# Generamos los assets estáticos
RUN npm run build

# ==========================================
# Etapa 3: Imagen Final de Producción
# ==========================================
FROM php:8.4-fpm-alpine

# Instalamos Nginx, Supervisor y extensiones de PHP en una sola capa
RUN apk add --no-cache nginx supervisor postgresql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu \
    && apk add --no-cache --virtual build-essentials icu-dev icu-libs zlib-dev g++ make automake autoconf libzip-dev libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev postgresql-dev pcre-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo_pgsql pgsql intl bcmath opcache exif zip \
    && pecl install redis \
    && docker-php-ext-enable redis.so \
    && apk del build-essentials \
    && rm -rf /usr/src/php* /var/cache/apk/*

WORKDIR /var/www/html

# Copiamos configuraciones de PHP
COPY dockerfiles/uploads.ini /usr/local/etc/php/conf.d/zz-uploads.ini
COPY dockerfiles/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

# Copiamos el código fuente
COPY src/ .

# Copiamos dependencias y assets compilados de las etapas anteriores
COPY --from=vendor /app/vendor/ ./vendor/
COPY --from=frontend /app/public/build/ ./public/build/

# Configuramos permisos
RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel \
    && chown -R laravel:laravel /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Asignamos permisos al usuario nativo de PHP-FPM (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Configuración de Nginx para Cloud Run
RUN echo "server { \
    listen 8080; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files \$uri \$uri/ /index.php?\$query_string; \
    } \
    location ~ \.php$ { \
        include fastcgi_params; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name; \
    } \
}" > /etc/nginx/http.d/default.conf

# Script de Arranque Anti-Race-Condition
# Levantamos PHP, le damos 2 segundos de ventaja, y luego abrimos la puerta con Nginx
RUN echo '#!/bin/sh' > /start.sh \
    && echo 'php-fpm -D' >> /start.sh \
    && echo 'sleep 2' >> /start.sh \
    && echo 'nginx -g "daemon off;"' >> /start.sh \
    && chmod +x /start.sh

EXPOSE 8080

# Usamos nuestro script en lugar de supervisor
CMD ["/start.sh"]