#!/bin/bash
# =============================================================================
# BuyOurBook — Préparation du VPS Ubuntu (Hostinger)
# À exécuter UNE SEULE FOIS en root sur le serveur vierge
#
# Usage :
#   scp docker/vps-setup.sh root@187.127.232.219:/root/
#   ssh root@187.127.232.219 "bash /root/vps-setup.sh"
# =============================================================================

set -euo pipefail

SERVER_IP="187.127.232.219"
APP_DIR="/opt/buyourbook"
REPO_URL="https://github.com/abdoul-sakhur/by-your-book-app.git"
BRANCH="master"

echo "============================================"
echo "  BuyOurBook — Setup VPS Hostinger"
echo "  Serveur : $SERVER_IP"
echo "============================================"

# ─── 1. Mise à jour système ───────────────────────────────────────────────────
echo ""
echo "[ 1/6 ] Mise à jour du système..."
apt-get update -qq
apt-get upgrade -y -qq

# ─── 2. Paquets essentiels ───────────────────────────────────────────────────
echo ""
echo "[ 2/6 ] Installation des paquets essentiels..."
apt-get install -y -qq \
    curl \
    git \
    ufw \
    ca-certificates \
    gnupg \
    lsb-release

# ─── 3. Installation Docker ───────────────────────────────────────────────────
echo ""
echo "[ 3/6 ] Installation de Docker..."

if command -v docker &> /dev/null; then
    echo "   → Docker déjà installé : $(docker --version)"
else
    # Dépôt officiel Docker
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
        -o /etc/apt/keyrings/docker.asc
    chmod a+r /etc/apt/keyrings/docker.asc

    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] \
      https://download.docker.com/linux/ubuntu \
      $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
      > /etc/apt/sources.list.d/docker.list

    apt-get update -qq
    apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    systemctl enable docker
    systemctl start docker

    echo "   → Docker installé : $(docker --version)"
    echo "   → Docker Compose  : $(docker compose version)"
fi

# ─── 4. Pare-feu UFW ─────────────────────────────────────────────────────────
echo ""
echo "[ 4/6 ] Configuration du pare-feu (UFW)..."
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp    comment "SSH"
ufw allow 80/tcp    comment "HTTP"
ufw allow 443/tcp   comment "HTTPS (futur)"
ufw --force enable
echo "   → UFW actif : $(ufw status | head -1)"

# ─── 5. Créer le répertoire de déploiement ────────────────────────────────────
echo ""
echo "[ 5/6 ] Préparation du répertoire $APP_DIR..."
mkdir -p "$APP_DIR"

if [ -d "$APP_DIR/.git" ]; then
    echo "   → Dépôt Git déjà cloné, mise à jour..."
    cd "$APP_DIR"
    git fetch origin
    git reset --hard "origin/$BRANCH"
else
    echo "   → Clonage du dépôt..."
    git clone --branch "$BRANCH" "$REPO_URL" "$APP_DIR"
fi

# ─── 6. Créer le .env de production ──────────────────────────────────────────
echo ""
echo "[ 6/6 ] Configuration de l'environnement..."

if [ -f "$APP_DIR/.env" ]; then
    echo "   → .env déjà présent, ignoré."
else
    cp "$APP_DIR/.env.production.example" "$APP_DIR/.env"
    # Générer une APP_KEY automatiquement (besoin de PHP dans le conteneur)
    echo "   ⚠  .env créé depuis le template."
    echo "   ⚠  IMPORTANT : éditer $APP_DIR/.env avant de lancer l'application !"
    echo "   ⚠  Commandes utiles après édition :"
    echo "       cd $APP_DIR && docker compose up -d --build"
fi

# ─── Résumé ──────────────────────────────────────────────────────────────────
echo ""
echo "============================================"
echo "  ✅  Setup terminé !"
echo ""
echo "  PROCHAINES ÉTAPES :"
echo "  1. Configurer le .env :"
echo "       nano $APP_DIR/.env"
echo ""
echo "  2. Lancer l'application :"
echo "       cd $APP_DIR"
echo "       docker compose up -d --build"
echo ""
echo "  3. Générer l'APP_KEY (après build) :"
echo "       docker exec buyourbook php artisan key:generate"
echo ""
echo "  4. Vérifier que tout tourne :"
echo "       docker compose ps"
echo "       curl http://$SERVER_IP/up"
echo "============================================"
