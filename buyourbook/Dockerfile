# =============================================================================
# BuyOurBook — Production Dockerfile (PHP 8.3 + Nginx)
# =============================================================================

# ---------- Stage 1 : Build des assets frontend ----------
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/

# Les vues de pagination Laravel sont référencées dans tailwind.config.js
# On crée un dossier vide pour éviter une erreur (les classes sont dans safelist)
RUN mkdir -p vendor/laravel/framework/src/Illuminate/Pagination/resources/views

RUN npm run build

# ---------- Stage 2 : Dépendances PHP ----------
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------- Stage 3 : Image finale ----------
FROM serversideup/php:8.4-fpm-nginx AS production

LABEL maintainer="BuyOurBook"

# Variables d'environnement par défaut
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PHP_OPCACHE_ENABLE=1 \
    PHP_MEMORY_LIMIT=256M \
    PHP_POST_MAX_SIZE=25M \
    PHP_UPLOAD_MAX_FILE_SIZE=10M \
    SSL_MODE=off

WORKDIR /var/www/html

# Copier le code source
COPY --chown=www-data:www-data . .

# Copier les dépendances Composer depuis le stage 2
COPY --from=composer --chown=www-data:www-data /app/vendor ./vendor

# Copier les assets buildés depuis le stage 1
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

# Créer les dossiers nécessaires et le fichier SQLite
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

# Configuration Nginx personnalisée
COPY docker/nginx.conf /etc/nginx/site-opts.d/buyourbook.conf

# Copier le script de déploiement
COPY --chmod=755 docker/deploy.sh /var/www/html/docker/deploy.sh



EXPOSE 8080
