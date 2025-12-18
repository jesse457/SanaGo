# ==============================================================================
# Multi-stage Dockerfile for Laravel Octane + FrankenPHP
# Optimized for GitHub Container Registry deployment
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Builder - Install dependencies and compile assets
# ------------------------------------------------------------------------------
FROM dunglas/frankenphp:1.0-php8.3 AS builder

# Install system dependencies needed for build
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

ARG MAKEFLAGS="-j2"
# Install PHP extensions required by Laravel
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    redis \
    bcmath \
    zip \
    gd \
    intl

# Set working directory
WORKDIR /app

# Copy dependency files first (for better Docker layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies (production mode)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Copy package files
COPY package.json package-lock.json ./

# Install Node dependencies
RUN npm ci --only=production

# Copy application source code
COPY . .

# Build frontend assets with Vite
RUN npm run build

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Set proper permissions for Laravel
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

# Copy supervisor configuration
COPY --chown=www-data:www-data octane-supervisor.conf /etc/supervisor/conf.d/octane.conf

# Create necessary directories and set permissions
RUN mkdir -p /app/storage/logs /app/bootstrap/cache && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

# Expose port 8000 (Octane default)
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/api/health || exit 1

# Switch to www-data user for security
USER www-data

# Start supervisor (manages Octane + queue workers)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
