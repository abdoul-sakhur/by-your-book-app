# BuyOurBook — Contexte du Projet

## 1. Présentation Générale

**BuyOurBook** (buyyourbook.ci) est une plateforme de vente de livres scolaires d'occasion en Côte d'Ivoire. Elle met en relation des **vendeurs** (particuliers qui revendent leurs manuels) et des **acheteurs** (parents/élèves) via un catalogue organisé par école, classe et matière. La livraison s'effectue via des **points relais**.

## 2. Stack Technique

| Composant        | Technologie                                       |
|------------------|---------------------------------------------------|
| **Framework**    | Laravel 11.31 (PHP ≥ 8.2)                         |
| **Auth**         | Laravel Breeze (sessions, Blade)                   |
| **Frontend**     | Blade + Tailwind CSS 3 + Alpine.js                 |
| **Build**        | Vite 6 + laravel-vite-plugin                       |
| **PDF**          | barryvdh/laravel-dompdf (factures)                 |
| **Base de données** | SQLite par défaut (configurable via `.env`)      |
| **Tests**        | PHPUnit 11                                         |
| **Dev tools**    | Pint (linting), Pail (logs), Sail (Docker)         |

## 3. Rôles Utilisateurs

| Rôle      | Enum `UserRole` | Description                                                 |
|-----------|-----------------|-------------------------------------------------------------|
| **Buyer** | `buyer`         | Parcourt le catalogue, ajoute au panier, passe commande     |
| **Seller**| `seller`        | Publie des livres à vendre, suit ses ventes                 |
| **Admin** | `admin`         | Gère tout : écoles, classes, livres, commandes, utilisateurs|

Le middleware `CheckRole` (alias `role`) protège les routes admin et vendeur.

## 4. Modèles & Base de Données

### 4.1 Schéma Relationnel

```
School (1) ──→ (N) Grade (1) ──→ (N) OfficialBook (1) ──→ (N) SellerBook
                                         ↑                        ↑
                                   Subject (1:N)            User/Seller
                                                                  ↓
User (1) ──→ (N) Order (1) ──→ (N) OrderItem ──→ SellerBook
  │                 │
  │                 ├──→ (N) OrderEvent
  │                 └──→ RelayPoint
  └──→ (N) Wishlist ──→ OfficialBook

Banner ──→ School (optionnel)
Setting (clé-valeur)
```

### 4.2 Modèles Détaillés

| Modèle | Table | Champs Clés | Relations |
|--------|-------|-------------|-----------|
| **School** | `schools` | name, city, district, logo, is_active | → grades, → banners |
| **Grade** | `grades` | school_id, name, level, academic_year | → school, → officialBooks |
| **Subject** | `subjects` | name | → officialBooks |
| **OfficialBook** | `official_books` | grade_id, subject_id, title, author, isbn, publisher, cover_image, is_active | → grade, → subject, → sellerBooks |
| **SellerBook** | `seller_books` | user_id, official_book_id, condition, price, quantity, images, status, rejection_reason | → seller (User), → officialBook, → orderItems |
| **User** | `users` | name, email, password, role, phone, address, is_active | → sellerBooks, → orders, → wishlists |
| **Order** | `orders` | user_id, relay_point_id, status, total_amount, delivery_notes | → user, → relayPoint, → items, → events |
| **OrderItem** | `order_items` | order_id, seller_book_id, quantity, unit_price | → order, → sellerBook |
| **OrderEvent** | `order_events` | order_id, status, comment | → order |
| **RelayPoint** | `relay_points` | name, address, city, district, contact_phone, schedule, is_active, coordinates | → orders |
| **Banner** | `banners` | title, image, link_url, position, target_type, school_id, is_active, starts_at, ends_at | → school |
| **Setting** | `settings` | key, value | — (accès statique get/set avec cache) |
| **Wishlist** | `wishlists` | user_id, official_book_id | → user, → officialBook |

### 4.3 Enums

| Enum | Valeurs |
|------|---------|
| `UserRole` | buyer, seller, admin |
| `BookStatus` | pending, approved, rejected |
| `BookCondition` | new, good, acceptable |
| `OrderStatus` | pending, confirmed, preparing, ready, delivered, cancelled |
| `BannerPosition` | home_top, home_mid, sidebar |
| `BannerTarget` | all, school |

Tous les enums sont `string`-backed avec méthodes `label()` (libellé FR) et `color()`.

## 5. Architecture des Routes

### 5.1 Routes Publiques

| Route | Contrôleur | Description |
|-------|-----------|-------------|
| `/` | Closure | Page d'accueil (écoles actives) |
| `/catalogue` | `CatalogController@schools` | Liste des écoles |
| `/catalogue/recherche` | `CatalogController@search` | Recherche de livres |
| `/catalogue/{school}/{grade}` | `CatalogController@grade` | Livres d'une classe |
| `/livre/{officialBook}` | `CatalogController@book` | Fiche d'un livre officiel + offres vendeurs |
| `/vendeur/{user}` | `SellerProfileController@show` | Profil public d'un vendeur |
| `/panier/*` | `CartController` | Panier (session, sans auth) |
| Pages statiques | `PageController` | Comment ça marche, Contact, CGU, Confidentialité, Mentions légales |

### 5.2 Routes Authentifiées (tous rôles)

| Route | Contrôleur | Description |
|-------|-----------|-------------|
| `/profile` | `ProfileController` | Gestion du profil |
| `/checkout` | `CheckoutController` | Passage de commande |
| `/mes-commandes` | `OrderController` | Commandes de l'acheteur |
| `/mes-commandes/{order}/facture` | `InvoiceController` | Téléchargement facture PDF |
| `/favoris` | `WishlistController` | Liste de souhaits |

### 5.3 Routes Admin (`/admin`, middleware `role:admin`)

| Ressource | Contrôleur | CRUD |
|-----------|-----------|------|
| Écoles | `Admin\SchoolController` | resource (sauf show) |
| Classes | `Admin\GradeController` | resource (sauf show) |
| Matières | `Admin\SubjectController` | resource (sauf show) |
| Livres officiels | `Admin\OfficialBookController` | resource (sauf show) |
| Validation livres vendeurs | `Admin\SellerBookValidationController` | index, show, approve, reject |
| Commandes | `Admin\OrderController` | index, show, updateStatus |
| Utilisateurs | `Admin\UserController` | index, show, toggleActive, updateRole |
| Points relais | `Admin\RelayPointController` | resource (sauf show) |
| Bannières | `Admin\BannerController` | resource (sauf show) |
| Paramètres | `Admin\SettingController` | resource (sauf show) |

### 5.4 Routes Vendeur (`/seller`, middleware `role:seller`)

| Route | Contrôleur | Description |
|-------|-----------|-------------|
| Dashboard | Closure | Stats : livres, ventes, commandes |
| Livres | `Seller\SellerBookController` | CRUD livres mis en vente |
| Commandes | `Seller\OrderController` | Suivi des ventes |
| API livres par classe | Closure | Select dynamique Alpine.js |

## 6. Notifications

| Notification | Déclencheur |
|-------------|-------------|
| `OrderConfirmationNotification` | Commande passée |
| `OrderStatusChangedNotification` | Changement de statut de commande |
| `SellerBookStatusNotification` | Approbation/rejet d'un livre vendeur |

## 7. Vues (Blade)

```
resources/views/
├── home.blade.php              # Page d'accueil
├── layouts/                    # Layouts partagés
├── components/                 # Composants Blade
├── partials/                   # Fragments réutilisables
├── auth/                       # Login, Register, Reset password
├── catalog/                    # Catalogue public
├── cart/                       # Panier
├── checkout/                   # Passage de commande
├── orders/                     # Commandes acheteur
├── invoices/                   # Factures PDF
├── wishlist/                   # Favoris
├── profile/                    # Profil utilisateur
├── seller/                     # Espace vendeur
├── admin/                      # Espace admin
├── pages/                      # Pages statiques
└── errors/                     # Pages d'erreur
```

## 8. Seeders

| Seeder | Rôle |
|--------|------|
| `AdminSeeder` | Crée l'utilisateur admin (admin@buyyourbook.ci / password) |
| `DemoSeeder` | Données de démonstration |
| `DatabaseSeeder` | Orchestrateur principal |

**Comptes de test :**
- Admin : `admin@buyyourbook.ci` / `password`
- Client : `sarbaclient` / `password`

## 9. Flux Métier Principaux

### 9.1 Publication d'un livre (Vendeur)
1. Le vendeur sélectionne école → classe → livre officiel
2. Il renseigne : état, prix, quantité, photos
3. Le livre est soumis en statut **pending**
4. L'admin approuve ou rejette → notification au vendeur

### 9.2 Achat (Acheteur)
1. L'acheteur parcourt le catalogue (école → classe → livre)
2. Il ajoute des offres vendeurs au panier (session)
3. À la commande : choix du point relais, confirmation
4. La commande passe par les statuts : **pending → confirmed → preparing → ready → delivered**
5. Facture PDF téléchargeable

### 9.3 Gestion Admin
- Dashboard avec stats : CA, commandes, vendeurs top, livres en attente
- CRUD complet sur l'ensemble du référentiel (écoles, classes, matières, livres)
- Modération des livres vendeurs (approve/reject)
- Gestion des commandes (changement de statut)
- Gestion des utilisateurs (activation, changement de rôle)

## 10. Commandes de Développement

```bash
# Démarrer l'environnement complet (serveur + queue + logs + vite)
composer dev

# Ou séparément :
php artisan serve          # Serveur web
npm run dev                # Vite (assets)
php artisan queue:listen   # Jobs

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Tests
php artisan test
```
