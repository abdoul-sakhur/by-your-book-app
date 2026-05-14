#!/bin/bash
# =============================================================================
# BuyOurBook — Script d'initialisation du conteneur
# Exécuté automatiquement au démarrage du conteneur (via cont-init.d)
# =============================================================================

set -e

echo "🚀 Début du post-déploiement BuyOurBook..."

# 1. Migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 2. Créer les dossiers de storage nécessaires (important si volume vide au premier démarrage)
echo "📁 Création des dossiers de stockage..."
mkdir -p storage/app/public/banners \
         storage/app/public/books \
         storage/app/public/sliders \
         storage/app/public/popups \
         storage/app/private \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs

# 3. Lien symbolique storage -> public
echo "🔗 Création du lien storage..."
php artisan storage:link 2>/dev/null || true

# 3. Cache de production
echo "⚡ Mise en cache de la configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Seed initial (uniquement si la table users est vide)
echo "🌱 Vérification du seed initial..."
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "   → Base vide, exécution du seeder..."
    php artisan db:seed --force
else
    echo "   → Base déjà peuplée ($USER_COUNT utilisateurs), seed ignoré."
fi

echo "✅ Post-déploiement terminé !"
