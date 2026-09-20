
#!/bin/sh
set -e

echo "Démarrage de Laravel..."

# Préparer le cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécuter les migrations
php artisan migrate --force

# Démarrer le serveur Laravel sur le port Render
php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"