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



COPY . /app
# 7. Copy Supervisor Config
COPY octane-supervisor.conf /etc/supervisor/conf.d/octane.conf

# 8. Start Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
