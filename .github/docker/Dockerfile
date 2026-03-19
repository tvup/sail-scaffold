FROM php:8.4-cli AS base

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    curl \
    supervisor \
    && docker-php-ext-install pdo_sqlite pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- Build stage: install deps and compile assets ---
FROM base AS build

RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

COPY package.json package-lock.json* ./
RUN npm install

COPY . .

RUN composer dump-autoload --optimize \
    && npm run build

# --- Production stage: lean runtime ---
FROM base AS production

COPY --from=build /app /app

ARG GIT_SHA=unknown
RUN echo "${GIT_SHA}" > /app/.version

# Remove node_modules (not needed at runtime)
RUN rm -rf node_modules

# Ensure writable directories exist
RUN mkdir -p storage/logs \
    storage/framework/{cache,sessions,views} \
    bootstrap/cache \
    database \
    && chmod -R 775 storage bootstrap/cache database

# Generate .env with app key and cache routes/views
RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan route:cache \
    && php artisan view:cache

COPY .github/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .github/docker/entrypoint.sh /app/docker-entrypoint.sh
RUN chmod +x /app/docker-entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/app/docker-entrypoint.sh"]
