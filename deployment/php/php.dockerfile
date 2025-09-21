## Stage 1: Build the application
FROM php:8.4.12-fpm AS build

# Set working directory
WORKDIR /var/www

# Install system dependencies and clean up in single layer
RUN apt-get update && apt-get install -y \
    apt-transport-https\
    build-essential \
    ca-certificates \
    curl \
    gnupg \
    libpq-dev \
    unzip \
    zip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions and Redis
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql pcntl bcmath \
    && pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Build argument for environment
ARG APP_ENV=production

# Install pcov only for local environment
RUN if [ "${APP_ENV}" = "local" ]; then \
    pecl install pcov && docker-php-ext-enable pcov; \
    fi

# Copy application files
COPY . /var/www

# Install PHP dependencies based on environment
RUN if [ "${APP_ENV}" = "local" ]; then \
    composer install --optimize-autoloader --prefer-dist; \
    else \
    composer install --no-dev --no-progress --optimize-autoloader --prefer-dist; \
    fi

## Stage 2: Create the final image
FROM php:8.4.12-fpm

# Set working directory
WORKDIR /var/www

# Build argument for environment
ARG APP_ENV=production

# Install runtime dependencies only
RUN apt-get update && apt-get install -y \
    libpq-dev \ 
    postgresql-client \
    git \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP extensions from the build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy Composer only in local environment
COPY --from=build /usr/bin/composer /tmp/composer
RUN if [ "${APP_ENV}" = "local" ]; then \
    echo "Installing Composer for local environment"; \
    cp /tmp/composer /usr/bin/composer && chmod +x /usr/bin/composer; \
    else \
    rm /tmp/composer; \
    fi

# Copy application files from the build stage
COPY --from=build /var/www /var/www

# Create user and group www
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Create Laravel directories if they don't exist and set permissions
RUN mkdir -p /var/www/storage/logs /var/www/storage/framework/{cache,sessions,views} /var/www/bootstrap/cache \
    && chown -R www:www /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Generate Laravel app key if .env doesn't exist
RUN if [ ! -f ".env" ] && [ -f "artisan" ]; then \
    cp .env.example .env 2>/dev/null || echo "APP_KEY=" > .env; \
    php artisan key:generate --no-interaction; \
    fi

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
COPY --chmod=0755 ./deployment/php/php-docker-entrypoint.sh /usr/local/bin/
ENTRYPOINT ["/usr/local/bin/php-docker-entrypoint.sh"]
