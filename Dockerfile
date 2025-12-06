# ==============================================================================
# Multi-stage Dockerfile for Laravel Octane + FrankenPHP
# Optimized for GitHub Container Registry deployment
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Builder - Install dependencies and compile assets
# ------------------------------------------------------------------------------
FROM dunglas/frankenphp:1-php8.3 AS builder

# 1. FIX: Copy Composer binary from the official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. FIX: Install system dependencies AND Node.js 22
# We use nodesource to get Node 22 (instead of the default Node 18)
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    gnupg \
    ca-certificates && \
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    redis \
    bcmath \
    zip \
    gd \
    intl

WORKDIR /app

COPY composer.json composer.lock ./

# Install PHP dependencies
# Using 'update' + platform config to handle the PHP 8.4 vs 8.3 lock file mismatch
RUN composer config platform.php 8.3 && \
    composer update \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

COPY package.json package-lock.json ./

# 3. FIX: Install ALL Node dependencies (including dev) and increase timeout
# We need 'devDependencies' (like vite) to run the build script.
# We also increase the timeout to prevent the ERR_SOCKET_TIMEOUT.
RUN npm config set fetch-retry-maxtimeout 600000 && \
    npm config set fetch-retry-mintimeout 10000 && \
    npm ci

COPY . .

# Build frontend assets with Vite
RUN npm run build

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
# ------------------------------------------------------------------------------
# Stage 2: Runtime - Minimal production image
# ------------------------------------------------------------------------------
FROM dunglas/frankenphp:1.0-php8.3

# Install only runtime dependencies
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    supervisor \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (runtime only)
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    redis \
    bcmath \
    zip \
    gd \
    intl \
    opcache

# Configure PHP for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /app

# Copy built application from builder stage
COPY --from=builder --chown=www-data:www-data /app /app

COPY --chown=www-data:www-data supervisord.conf /etc/supervisor/supervisord.conf

# Copy supervisor configuration
COPY --chown=www-data:www-data octane-supervisor.conf /etc/supervisor/conf.d/octane.conf

# Create necessary directories and set permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache /var/log/supervisor && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache /var/log/supervisor && \
    chmod -R 775 /app/storage /app/bootstrap/cache /var/log/supervisor && \
    chmod -R 775 /app/database/

# ==============================================================================
# FIX: Manually update FrankenPHP binary to latest version
# This prevents Octane from asking "Do you want to update?" at runtime
# ==============================================================================
RUN curl -fL https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-linux-$(uname -m) -o /usr/local/bin/frankenphp && \
    chmod +x /usr/local/bin/frankenphp


# Expose port 8000 (Octane default)
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/api/health || exit 1

# Switch to www-data user for security
USER www-data

# Start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
