#!/bin/bash
# =============================================================================
# BuyOurBook — Script de redéploiement (mise à jour de l'application)
# À exécuter sur le VPS à chaque nouvelle version
#
# Usage depuis le VPS :
#   cd /opt/buyourbook && bash docker/redeploy.sh
#
# Usage depuis votre machine locale (one-liner) :
#   ssh root@187.127.232.219 "cd /opt/buyourbook && git pull && docker compose up -d --build --remove-orphans"
# =============================================================================

set -euo pipefail

APP_DIR="/opt/buyourbook"
BRANCH="master"

cd "$APP_DIR"

echo "============================================"
echo "  BuyOurBook — Redéploiement"
echo "  $(date '+%Y-%m-%d %H:%M:%S')"
echo "============================================"

# ─── 1. Pull du code ─────────────────────────────────────────────────────────
echo ""
echo "[ 1/4 ] Récupération du code depuis Git..."
git fetch origin
git reset --hard "origin/$BRANCH"
echo "   → Commit : $(git log -1 --oneline)"

# ─── 2. Build et redémarrage ─────────────────────────────────────────────────
echo ""
echo "[ 2/4 ] Build Docker et redémarrage..."
docker compose pull 2>/dev/null || true
docker compose up -d --build --remove-orphans

# ─── 3. Nettoyage des images obsolètes ───────────────────────────────────────
echo ""
echo "[ 3/4 ] Nettoyage des images Docker obsolètes..."
docker image prune -f

# ─── 4. Vérification santé ───────────────────────────────────────────────────
echo ""
echo "[ 4/4 ] Vérification de l'état du conteneur..."
sleep 10

STATUS=$(docker inspect --format='{{.State.Health.Status}}' buyourbook 2>/dev/null || echo "unknown")
echo "   → Santé du conteneur : $STATUS"

if [ "$STATUS" = "healthy" ] || [ "$STATUS" = "starting" ]; then
    echo ""
    echo "  ✅  Redéploiement réussi !"
else
    echo ""
    echo "  ⚠  Le conteneur répond '$STATUS' — vérifier les logs :"
    echo "      docker logs buyourbook --tail=50"
fi

echo "============================================"
