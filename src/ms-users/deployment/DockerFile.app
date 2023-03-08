#FROM php:8.1-fpm-alpine
FROM php:8.1-fpm-alpine3.16

# TODO BORRAR LOS ENV QUE NO VAN
# Essentials
RUN echo "UTC" > /etc/timezone

RUN apk add --no-cache \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    autoconf \
    g++ \
    imagemagick-dev \
    libtool \
    make \
    curl \
    sqlite \
    postgresql-dev \
    libzip-dev && \
                 docker-php-ext-configure gd \
                   --with-freetype \
                   --with-jpeg

# Installing bash
RUN apk add bash
RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

# Installing PECL
RUN pecl install redis

# Installing PHP
RUN apk add --no-cache php8 \
    php8-common \
    php8-fpm \
    php8-pdo \
    php8-opcache \
    php8-zip \
    php8-phar \
    php8-iconv \
    php8-cli \
    php8-curl \
    php8-openssl \
    php8-mbstring \
    php8-tokenizer \
    php8-fileinfo \
    php8-json \
    php8-xml \
    php8-xmlwriter \
    php8-simplexml \
    php8-dom \
    php8-pdo_sqlite \
    php8-pdo_pgsql \
    php8-tokenizer \
    php8-pgsql \
    php8-redis

# Configure PHP
RUN mkdir -p /run/php/
RUN touch /run/php/php8.1-fpm.pid

# Configure PHP extensions
RUN docker-php-ext-install sockets pdo pdo_pgsql pgsql zip exif gd
RUN docker-php-ext-enable pdo pdo_pgsql redis exif

# Clear APK cache
RUN rm -rf /var/cache/apk/*

# Composer install
COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# COPY
COPY . .

# Download libriaries
RUN composer install --optimize-autoloader

# Publish assests
RUN php artisan vendor:publish --tag=config
RUN php artisan vendor:publish --tag=assets

RUN php artisan storage:link

# Setup www-data user
RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data ./
RUN ["chmod", "+x", "/var/www/html/deployment/scripts/post_deploy.sh"]

USER www-data

# Set entrypoit to execute post deploy script, this script then pass handle to CMD
ENTRYPOINT ["/var/www/html/deployment/scripts/post_deploy.sh"]
CMD ["php-fpm"]

EXPOSE 9000
