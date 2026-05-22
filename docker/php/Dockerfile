FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    python3 \
    python3-pip \
    && docker-php-ext-install pdo_mysql opcache \
    && pip3 install --break-system-packages edge-tts \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY entrypoint.sh /usr/local/bin/storybook-entrypoint
RUN chmod +x /usr/local/bin/storybook-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/storybook-entrypoint"]
CMD ["php-fpm"]
