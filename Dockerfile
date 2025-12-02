FROM dunglas/frankenphp:latest

# 1. Install System Dependencies & PHP Extensions
# install PHP extensions (install-php-extensions is already available in the image)
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    redis \
    bcmath \
 && apt-get update \
 && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends supervisor bash \
 && rm -rf /var/lib/apt/lists/*

# 2. Set working directory
WORKDIR /app



COPY . /app
# 7. Copy Supervisor Config
COPY octane-supervisor.conf /etc/supervisor/conf.d/octane.conf

# 8. Start Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
