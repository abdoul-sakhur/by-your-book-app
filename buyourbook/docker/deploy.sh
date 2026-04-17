#!/bin/bash
# =============================================================================
# BuyOurBook — Script de post-déploiement pour Coolify
# Exécuté automatiquement après chaque déploiement
# =============================================================================

set -e

echo "🚀 Début du post-déploiement BuyOurBook..."

# 1. Migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 2. Lien symbolique storage -> public
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
