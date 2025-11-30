FROM dunglas/frankenphp:latest

# 1. Install System Dependencies & PHP Extensions
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    redis \
    bcmath \
    zip \
    intl \
 && apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    supervisor \
    bash \
    git \
    unzip \
 && rm -rf /var/lib/apt/lists/*

# 2. Set working directory
WORKDIR /app

# 3. Copy Composer files first (for caching)
COPY composer.json composer.lock ./

# 4. Install Composer Dependencies
# Note: We need secret mounts if using Flux UI, otherwise just run composer install
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 5. Copy the rest of the application
COPY . .

# 6. Finish Composer installation
RUN composer dump-autoload --optimize

# 7. Copy Supervisor Config
COPY octane-supervisor.conf /etc/supervisor/conf.d/octane.conf

# 8. Start Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
