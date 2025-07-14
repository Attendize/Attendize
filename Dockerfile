# === Étape 1 : Base Composer avec PHP + extensions ===
FROM eu.gcr.io/www-genopsys/php-7.4 AS composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apk update && apk add --no-cache \
    curl \
    unzip \
    git
WORKDIR /app

# === Étape 2 : Build de l'application avec les dépendances ===
FROM composer AS build

WORKDIR /app
# Copier les fichiers nécessaires pour installer les dépendances
COPY . .
# run composer, chmod files, setup laravel key
RUN sh -x ./scripts/setup

# === Étape 1 : Base Composer avec PHP + extensions ===
FROM eu.gcr.io/www-genopsys/php-7.4 AS php

RUN apk add --no-cache \
    libstdc++ \
    libx11 \
    libxrender \
    libxext \
    libssl3 \
    ca-certificates \
    fontconfig \
    freetype \
    ttf-dejavu \
    ttf-droid \
    ttf-freefont \
    ttf-liberation \
    # more fonts
    && apk add --no-cache --virtual .build-deps \
    msttcorefonts-installer \
    # Install microsoft fonts
    && update-ms-fonts \
    && fc-cache -f \
    # Create fontconfig cache directory and set permissions
    && mkdir -p /var/cache/fontconfig \
    && chmod -R 777 /var/cache/fontconfig \
    && mkdir -p /usr/share/fonts \
    && chmod -R 755 /usr/share/fonts \
    # Clean up when done
    && rm -rf /tmp/* \
    && apk del .build-deps

WORKDIR /app
COPY --from=surnet/alpine-wkhtmltopdf:3.20.2-0.12.6-full /bin/wkhtmltopdf /usr/bin/wkhtmltopdf

# Set environment variables for fontconfig
ENV FONTCONFIG_CACHE=/var/cache/fontconfig
ENV XDG_CACHE_HOME=/var/cache

# === Étape 3 : Image finale pour exécution (runtime) ===
FROM eu.gcr.io/www-genopsys/php-7.4 AS app
WORKDIR /app
COPY --from=build /app /app
CMD ["php", "artisan", "queue:work", "--daemon"]
