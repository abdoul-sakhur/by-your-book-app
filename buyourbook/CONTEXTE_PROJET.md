# BuyOurBook — Contexte du Projet

## 1. Présentation Générale

**BuyOurBook** (buyyourbook.ci) est une plateforme de vente de livres scolaires d'occasion en Côte d'Ivoire. Elle met en relation des **vendeurs** (particuliers qui revendent leurs manuels) et des **acheteurs** (parents/élèves) via un catalogue organisé par école, classe et matière. La livraison s'effectue via des **points relais**.

## 2. Stack Technique

| Composant        | Technologie                                       |
|------------------|---------------------------------------------------|
| **Framework**    | Laravel 11.51 (PHP 8.5.1)                         |
| **Auth**         | Laravel Breeze (sessions, Blade)                   |
| **Frontend**     | Blade + Tailwind CSS 3 + Alpine.js                 |
| **Build**        | Vite 6 + laravel-vite-plugin                       |
| **PDF**          | barryvdh/laravel-dompdf (factures)                 |
| **Base de données** | MySQL (configurable via `.env`)                 |
| **Tests**        | PHPUnit 11                                         |
| **Dev tools**    | Pint (linting), Pail (logs), Sail (Docker)         |
| **Chat**         | Tawk.to (widget configurable via paramètres admin) |

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
| **SellerBook** | `seller_books` | user_id, official_book_id, condition, price, quantity, images, status, rejection_reason, **purchase_price, buyback_status, buyback_price, buyback_notes, counter_price, admin_paid_seller** | → seller (User), → officialBook, → orderItems |
| **User** | `users` | name, email, password, role, phone, address, is_active | → sellerBooks, → orders, → wishlists |
| **Order** | `orders` | user_id, **delivery_type**, status, total_amount, **delivery_fee**, delivery_notes, **buyer_name, buyer_phone, buyer_address, buyer_city** | → user, → items, → events |
| **PageView** | `page_views` | url, session_id, user_id, ip_address, created_at | — (analytics trafic) |
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

**Enum `BuybackStatus`** (DB enum inline sur `seller_books.buyback_status`) :
- `pending` — aucune offre
- `negotiating` — offre admin proposée, en attente réponse vendeur
- `accepted` — vendeur a accepté
- `rejected` — vendeur ou admin a refusé

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
| Validation livres vendeurs | `Admin\SellerBookValidationController` | index, show, approve, reject, **buybackPropose, markPaid** |
| Commandes | `Admin\OrderController` | index, show, updateStatus |
| Utilisateurs | `Admin\UserController` | index, show, toggleActive, updateRole |
| Points relais | `Admin\RelayPointController` | resource (sauf show) |
| Bannières | `Admin\BannerController` | resource (sauf show) |
| Paramètres | `Admin\SettingController` | resource (sauf show) **+ GET/POST settings/general** |

### 5.4 Routes Vendeur (`/seller`, middleware `role:seller`)

| Route | Contrôleur | Description |
|-------|-----------|-------------|
| Dashboard | Closure | Stats : livres, ventes, commandes |
| Livres | `Seller\SellerBookController` | CRUD livres mis en vente **+ buybackRespond** |
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
├── home.blade.php              # Page d'accueil (CTA conditionnel buyer/seller)
├── layouts/                    # Layouts partagés (app.blade.php intègre Tawk.to)
├── components/                 # Composants Blade
├── partials/                   # Fragments réutilisables
├── auth/                       # Login, Register, Reset password
├── catalog/                    # Catalogue public
├── cart/                       # Panier
├── checkout/                   # Passage de commande (livraison domicile uniquement)
├── orders/                     # Commandes acheteur
├── invoices/                   # Factures PDF
├── wishlist/                   # Favoris
├── profile/                    # Profil utilisateur
├── seller/                     # Espace vendeur
│   └── books/
│       └── index.blade.php     # Liste + modal Alpine.js réponse rachat
├── admin/                      # Espace admin
│   ├── dashboard.blade.php     # Stats trafic + financières + top livres
│   ├── seller-books/
│   │   └── show.blade.php      # Détail + section gestion rachat
│   └── settings/
│       └── general.blade.php   # Frais livraison, seuil gratuit, Tawk.to
├── pages/                      # Pages statiques
└── errors/                     # Pages d'erreur
```

## 8. Seeders

| Seeder | Rôle |
|--------|------|
| `AdminSeeder` | Crée l'utilisateur admin (admin@buyyourbook.ci / password) |
| `SettingsSeeder` | Valeurs par défaut : delivery_fee=3000, free_delivery_threshold=500000, tawkto_widget_id='' |
| `DemoSeeder` | Données de démonstration (écoles, livres, vendeur, commandes) |
| `DatabaseSeeder` | Orchestrateur principal |

**Comptes de test :**
- Admin : `admin@buyyourbook.ci` / `password`
- Client : `sarbaclient` / `password`
- Vendeur : `saravendeur@buyyourbook.ci` / `password`

## 9. Flux Métier Principaux

### 9.1 Publication d'un livre (Vendeur)
1. Le vendeur sélectionne école → classe → livre officiel
2. Il renseigne : état, prix, quantité, photos
3. Le livre est soumis en statut **pending**
4. L'admin approuve ou rejette → notification au vendeur

### 9.2 Achat (Acheteur)
1. L'acheteur parcourt le catalogue (école → classe → livre)
2. Il ajoute des offres vendeurs au panier (session)
3. À la commande : **livraison à domicile uniquement** (nom, téléphone, adresse, ville)
4. Frais de livraison : **3 000 FCFA** (gratuit si total ≥ seuil configuré)
5. La commande passe par les statuts : **pending → confirmed → preparing → ready → delivered**
6. Facture PDF téléchargeable

### 9.3 Gestion Admin
- Dashboard avec stats : CA, commandes, vendeurs top, livres en attente
- **Stats de trafic** : visiteurs uniques, pages vues (via `PageView` + middleware `TrackPageView`)
- **Indicateurs financiers** : revenus du mois, commandes du mois, panier moyen
- **Top 5 livres vendus** et **5 livres les plus consultés**
- CRUD complet sur l'ensemble du référentiel (écoles, classes, matières, livres)
- Modération des livres vendeurs (approve/reject)
- Gestion des commandes (changement de statut)
- Gestion des utilisateurs (activation, changement de rôle)
- **Paramètres généraux** (`/admin/settings/general`) : frais livraison, seuil gratuit, ID Tawk.to

### 9.4 Rachat de livre (Buyback)

**Flux complet :**
1. Le vendeur déclare un `purchase_price` lors de la mise en vente (optionnel)
2. L'admin ouvre la fiche du livre → propose un prix de rachat (`buyback_price`) + note optionnelle
   → `buyback_status` passe à `negotiating`
3. Le vendeur voit le badge "Offre rachat : X FCFA" dans sa liste de livres
4. Le vendeur clique "Répondre" → modal Alpine.js avec 3 options :
   - **Accepter** → `buyback_status = accepted`
   - **Contre-offre** → saisit `counter_price`, `buyback_status` reste `negotiating`
   - **Refuser** → `buyback_status = rejected`
5. L'admin voit la contre-offre sur la fiche → peut accepter ou ajuster
6. Une fois accepté, l'admin clique "Marquer comme payé" → `admin_paid_seller = true`

## 10. Commandes de Développement

```bash
# Démarrer l'environnement complet (serveur + queue + logs + vite)
composer dev

# Ou séparément :
php artisan serve          # Serveur web (http://127.0.0.1:8000)
npm run dev                # Vite (assets)
php artisan queue:listen   # Jobs

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Tests
php artisan test
```

## 11. Fonctionnalités Implémentées (v2 — Mai 2026)

| # | Fonctionnalité | Fichiers principaux |
|---|---------------|---------------------|
| 1 | Livraison à domicile uniquement (pas de points relais) | `CheckoutController`, `checkout/index.blade.php`, migration `add_delivery_fields_to_orders` |
| 2 | Frais de livraison configurables (3 000 FCFA / seuil gratuit) | `Setting::get('delivery_fee')`, `SettingsSeeder` |
| 3 | Stats de trafic admin (visiteurs, pages vues) | `PageView`, `TrackPageView` (middleware), `admin/dashboard.blade.php` |
| 4 | Indicateurs financiers admin | `admin/dashboard.blade.php` (CA, panier moyen, commandes) |
| 5 | Top livres vendus + populaires | `admin/dashboard.blade.php` (OrderItem agrégation + PageView) |
| 6 | Widget de chat Tawk.to | `layouts/app.blade.php` (Setting::get('tawkto_widget_id')) |
| 7 | Bouton "Vendre mes livres" conditionnel | `home.blade.php` (auth+role → seller.books.create sinon register) |
| 8 | Processus de rachat (Buyback) | Migration, `SellerBook` (champs), routes, contrôleurs, vues |
| 9 | Interface admin paramètres généraux | `admin/settings/general.blade.php`, `SettingController@general/saveGeneral` |
| 10 | Gestion admin rachat (proposer prix, marquer payé) | `SellerBookValidationController@buybackPropose/markPaid`, `admin/seller-books/show.blade.php` |
| 11 | Réponse vendeur à offre de rachat | `SellerBookController@buybackRespond`, `seller/books/index.blade.php` (modal Alpine.js) |
