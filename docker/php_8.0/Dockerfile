FROM php:8.0-fpm-alpine
RUN apk add --update --no-cache --virtual .build-deps $PHPIZE_DEPS \
        nodejs npm \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        gmp-dev \
        zip \
        unzip \
        git \
    && docker-php-ext-configure gd  --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && docker-php-ext-install sockets \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-configure gmp \
    && docker-php-ext-install gmp \
    && docker-php-ext-install exif
	
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer