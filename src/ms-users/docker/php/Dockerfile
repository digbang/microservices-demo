FROM php:8.1-fpm-alpine

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
RUN apk add --no-cache php81 \
    php81-common \
    php81-fpm \
    php81-pdo \
    php81-opcache \
    php81-zip \
    php81-phar \
    php81-iconv \
    php81-cli \
    php81-curl \
    php81-openssl \
    php81-mbstring \
    php81-tokenizer \
    php81-fileinfo \
    php81-json \
    php81-xml \
    php81-xmlwriter \
    php81-simplexml \
    php81-dom \
    php81-pdo_sqlite \
    php81-pdo_pgsql \
    php81-tokenizer \
    php81-pgsql \
    php81-redis

# Configure PHP
RUN mkdir -p /run/php/
RUN touch /run/php/php8.1-fpm.pid

# Configure PHP extensions
RUN docker-php-ext-install sockets pdo pdo_pgsql pgsql zip exif gd
RUN docker-php-ext-enable pdo pdo_pgsql redis exif

# Clear APK cache
RUN rm -rf /var/cache/apk/*

# Composer install
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Setup www-data user
RUN apk add shadow && usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data ./

USER www-data
