# 🚀 Guide de Déploiement BuyOurBook sur Coolify

## Architecture

```
Internet → Coolify (reverse proxy) → Container Docker
                                      ├── PHP 8.3-FPM
                                      ├── Nginx
                                      └── SQLite (volume persistant)
```

---

## Prérequis

| Élément | Détail |
|---------|--------|
| **VPS** | Hetzner CX22 (2 vCPU, 4 Go RAM, 40 Go SSD) — ~4,50 €/mois |
| **OS** | Ubuntu 24.04 LTS |
| **Coolify** | Installé sur le VPS |
| **Dépôt Git** | Code poussé sur GitHub (privé ou public) |
| **Domaine** | (optionnel) un domaine pointant vers l'IP du VPS |

---

## Étape 1 — Acheter et configurer le VPS

### 1.1 Créer le VPS sur Hetzner
1. Aller sur [console.hetzner.cloud](https://console.hetzner.cloud)
2. Créer un projet → **Add Server**
3. Choisir :
   - **Location** : Falkenstein (eu-central) ou Nuremberg
   - **Image** : Ubuntu 24.04
   - **Type** : CX22 (2 vCPU / 4 Go RAM)
   - **SSH Key** : ajouter votre clé SSH publique
4. Cliquer sur **Create & Buy Now**
5. Noter l'**IP publique** du serveur

### 1.2 Connexion SSH initiale
```bash
ssh root@VOTRE_IP
```

---

## Étape 2 — Installer Coolify

Sur le VPS, exécuter :

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

L'installation prend ~3 minutes. À la fin, Coolify est accessible sur :

```
http://VOTRE_IP:8000
```

### 2.1 Première configuration
1. Ouvrir `http://VOTRE_IP:8000` dans le navigateur
2. Créer le compte administrateur
3. Choisir **localhost** comme serveur (le VPS lui-même)

---

## Étape 3 — Pousser le code sur GitHub

### 3.1 Initialiser et pousser le dépôt

```bash
# Sur votre machine locale, dans le dossier du projet
cd C:\Users\DELL\bybook\buyourbook

git init
git add .
git commit -m "Initial commit - BuyOurBook"

# Créer le dépôt sur GitHub puis :
git remote add origin https://github.com/VOTRE_USER/buyourbook.git
git branch -M main
git push -u origin main
```

### 3.2 Connecter GitHub à Coolify
1. Dans Coolify → **Sources** → **Add GitHub App**
2. Suivre la procédure OAuth pour autoriser Coolify
3. Sélectionner le dépôt `buyourbook`

---

## Étape 4 — Créer la ressource dans Coolify

1. **Dashboard** → **+ New Resource** → **Application**
2. Choisir **GitHub** comme source
3. Sélectionner le dépôt `buyourbook`, branche `main`
4. **Build Pack** : choisir **Dockerfile**
5. Coolify détecte automatiquement le `Dockerfile` à la racine

### 4.1 Configuration de base

| Paramètre | Valeur |
|-----------|--------|
| **Build Pack** | Dockerfile |
| **Dockerfile Location** | `/Dockerfile` |
| **Port exposé** | `8080` |
| **Domaine** | `https://buyourbook.votre-domaine.com` ou l'IP auto-générée |

---

## Étape 5 — Variables d'environnement

Dans Coolify → onglet **Environment Variables**, ajouter :

### Variables obligatoires

```env
APP_NAME=BuyOurBook
APP_ENV=production
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_DEBUG=false
APP_TIMEZONE=Africa/Abidjan
APP_URL=https://buyourbook.votre-domaine.com

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

LOG_CHANNEL=stderr
LOG_LEVEL=warning

DB_CONNECTION=sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

BCRYPT_ROUNDS=12
```

### Générer la clé APP_KEY

Exécuter localement :
```bash
php artisan key:generate --show
```
Copier la valeur `base64:xxxx...` dans Coolify.

### Variables mail (optionnel — pour les notifications)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-application
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contact@buyyourbook.ci
MAIL_FROM_NAME=BuyOurBook
```

> 💡 Pour Gmail, créer un **mot de passe d'application** dans les paramètres de sécurité Google.

---

## Étape 6 — Volume persistant (base de données SQLite)

⚠️ **Critique** : sans volume persistant, la base SQLite est perdue à chaque redéploiement.

Dans Coolify → onglet **Storages** :

| Chemin dans le conteneur | Nom du volume |
|--------------------------|---------------|
| `/var/www/html/database` | `buyourbook-database` |
| `/var/www/html/storage/app` | `buyourbook-storage` |

### Configuration dans Coolify
1. Aller dans les **Settings** de l'application
2. Section **Persistent Storage**
3. Ajouter :
   - **Source** : `buyourbook-database` → **Destination** : `/var/www/html/database`
   - **Source** : `buyourbook-storage` → **Destination** : `/var/www/html/storage/app`

---

## Étape 7 — Commande Post-Deployment

Dans Coolify → **Settings** → **Custom Start Command** ou **Post Deployment Command** :

```bash
bash /var/www/html/docker/deploy.sh
```

Ce script (inclus dans le projet) exécute automatiquement :
- ✅ `php artisan migrate --force`
- ✅ `php artisan storage:link`
- ✅ Cache de configuration, routes, vues
- ✅ Seed initial (seulement si la base est vide)

---

## Étape 8 — SSL / HTTPS

### Avec un domaine
1. Dans la configuration DNS de votre domaine, ajouter un enregistrement **A** :
   ```
   Type: A
   Nom: buyourbook (ou @)
   Valeur: IP_DU_VPS
   TTL: 300
   ```
2. Dans Coolify, l'application détecte le domaine et génère automatiquement un certificat **Let's Encrypt**

### Sans domaine (IP uniquement)
Coolify fournit une URL temporaire type : `http://VOTRE_IP:PORT`

---

## Étape 9 — Déployer !

1. Cliquer sur **Deploy** dans Coolify
2. Observer les logs de build en temps réel :
   - Stage 1 : Build des assets (npm ci + npm run build) ~1-2 min
   - Stage 2 : Installation Composer ~30s
   - Stage 3 : Assemblage de l'image finale ~20s
3. Le script post-deployment s'exécute
4. L'application est en ligne ✅

---

## Étape 10 — Vérification post-déploiement

### Checklist
- [ ] La page d'accueil s'affiche correctement
- [ ] Les styles CSS/Tailwind sont bien chargés
- [ ] Le slider fonctionne
- [ ] Inscription acheteur + vendeur fonctionne
- [ ] Connexion admin : `admin@buyyourbook.ci` / `password`
- [ ] Connexion vendeur : `vendeur1@buyyourbook.ci` / `password`
- [ ] Les images de livres s'affichent
- [ ] Le panier et le checkout fonctionnent

### Comptes de test (créés par le seeder)

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | admin@buyyourbook.ci | password |
| Vendeur 1 | vendeur1@buyyourbook.ci | password |
| Vendeur 2 | vendeur2@buyyourbook.ci | password |
| Acheteur | acheteur@buyyourbook.ci | password |

---

## Étape 11 — Déploiement automatique (CI/CD)

### Webhook GitHub
1. Dans Coolify → onglet **Webhooks**
2. Copier l'URL du webhook
3. Dans GitHub → **Settings** → **Webhooks** → **Add webhook**
   - URL : celle copiée depuis Coolify
   - Events : `push`
4. Désormais, chaque `git push` sur `main` déclenche un redéploiement automatique

---

## Résumé des fichiers créés

| Fichier | Rôle |
|---------|------|
| `Dockerfile` | Image Docker multi-stage (Node → Composer → PHP/Nginx) |
| `.dockerignore` | Exclut les fichiers inutiles du build |
| `docker/nginx.conf` | Configuration Nginx (cache assets, upload max) |
| `docker/deploy.sh` | Script post-déploiement (migrations, cache, seed) |
| `.env.example` | Template des variables d'environnement documenté |

---

## Résolution de problèmes

### La page est blanche
→ Vérifier les logs dans Coolify → onglet **Logs**
→ Vérifier que `APP_KEY` est bien défini

### Les styles ne s'affichent pas
→ Le build Vite a échoué : vérifier les logs du stage 1 dans le build
→ Vérifier que `/public/build/` existe dans le conteneur

### Erreur 500
→ Vérifier que `APP_DEBUG=false` n'est pas masquant l'erreur
→ Temporairement mettre `APP_DEBUG=true` pour voir l'erreur détaillée
→ Vérifier les volumes (base SQLite accessible et persistante)

### Les images uploadées disparaissent
→ Le volume `buyourbook-storage` n'est pas monté
→ Vérifier dans Coolify → **Storages**

### Erreur de migration
→ Se connecter au conteneur : **Terminal** dans Coolify
→ Exécuter : `php artisan migrate:status`

---

## Coût mensuel estimé

| Service | Coût |
|---------|------|
| VPS Hetzner CX22 | ~4,50 €/mois |
| Coolify | **Gratuit** (self-hosted) |
| Domaine .ci (optionnel) | ~15 000 FCFA/an |
| **Total** | **~4,50 €/mois** (~3 000 FCFA) |
