# Self-hosted bootstrap image for Laravel project scaffolding.
# Equivalent to laravelsail/php85-composer:latest (which is not yet published).
#
# Build:  docker build -t laravelsail/php85-composer:latest docker/bootstrap/
#
# When the official laravelsail/php85-composer image is published on Docker Hub,
# this Dockerfile serves as a fallback in case the upstream image is ever removed.
#
# Base: php:8.4-cli until php:8.5-cli is available on Docker Hub.

FROM php:8.4-cli

# System dependencies for Composer and general usage
RUN apt-get update \
    && apt-get install -y git zip unzip zlib1g-dev libzip-dev \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# PHP extensions required by Laravel
RUN docker-php-ext-install zip pcntl bcmath

# Composer (multi-stage copy from official image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js v24 (matching Laravel Sail convention)
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && rm -rf /var/lib/apt/lists/*

# Laravel installer
RUN cd / \
    && git clone https://github.com/laravel/installer laravel-installer \
    && cd laravel-installer \
    && composer install
RUN ln -s /laravel-installer/bin/laravel /usr/bin/laravel \
    && chmod +x /usr/bin/laravel

CMD ["php", "-a"]
