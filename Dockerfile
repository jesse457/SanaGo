# Stage 1: Builder
FROM dunglas/frankenphp:1.10.1-php8.3 AS builder

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    git curl unzip gnupg ca-certificates && \
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

RUN install-php-extensions pcntl pdo_pgsql redis bcmath zip gd intl

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer config platform.php 8.3 && \
    composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist --optimize-autoloader

COPY package.json package-lock.json ./

RUN npm config set fetch-retry-maxtimeout 600000 && \
    npm config set fetch-retry-mintimeout 10000 && \
    npm ci

COPY . .

RUN npm run build
RUN composer dump-autoload --optimize --no-dev

# Stage 2: Runtime
FROM dunglas/frankenphp:1.10.1-php8.3

# Install runtime dependencies (Supervisor)
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    supervisor curl \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions pcntl pdo_pgsql redis bcmath zip gd intl opcache

# Production PHP Configuration
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "variables_order=EGPCS" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/custom.ini

WORKDIR /app

# 1. Setup Supervisor Logs/Pid folders and assign ownership to www-data BEFORE switching users
RUN mkdir -p /var/log/supervisor /var/run/supervisor \
    && chown -R www-data:www-data /var/log/supervisor /var/run/supervisor

# 2. Copy Configs
COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY octane-supervisor.conf /etc/supervisor/conf.d/octane.conf

# 3. Copy Application Code with Ownership
# This avoids the need for 'chown -R' later which bloats image layers
COPY --chown=www-data:www-data --from=builder /app /app

# 4. Create Laravel specific directories if they don't exist and set correct permissions
# 775 is safer than 777 (User/Group can write, World can only read)
RUN mkdir -p /app/storage/logs \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/framework/cache \
    /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

# 5. Switch to non-root user
USER www-data

# Ensure Healthcheck uses localhost
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/up || exit 1

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
