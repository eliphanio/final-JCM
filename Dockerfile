
# ==========================================
# Laravel + React + Wayfinder + PostgreSQL
# ==========================================

FROM php:8.3-cli

WORKDIR /app

# ==========================================
# 1. Installer les dépendances système
# ==========================================

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    ca-certificates \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        zip \
        bcmath \
        intl \
    && rm -rf /var/lib/apt/lists/*

# ==========================================
# 2. Installer Node.js 22
# ==========================================

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# ==========================================
# 3. Installer Composer
# ==========================================

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==========================================
# 4. Copier les dépendances du projet
# ==========================================

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ==========================================
# 5. Copier les fichiers Node.js
# ==========================================

COPY package*.json ./

RUN npm ci

# ==========================================
# 6. Copier le code source
# ==========================================

COPY . .

# ==========================================
# 7. Générer les fichiers Wayfinder et React
# ==========================================

RUN php artisan wayfinder:generate --with-form

RUN npm run build

# ==========================================
# 8. Préparer Laravel
# ==========================================

RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

# ==========================================
# 9. Configurer le démarrage
# ==========================================

COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 10000

CMD ["/entrypoint.sh"]