<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\OfficialBookController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RelayPointController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SellerBookValidationController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\SellerBookController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Enums\BookStatus;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PageView;
use App\Models\Popup;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Slider;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Route;

// --- Page d'accueil ---
Route::get('/', function () {
    $schools = School::active()->withCount('grades')->with(['grades' => fn($q) => $q->orderBy('level')->limit(1)])->orderBy('name')->limit(8)->get();
    $slides = Slider::active()->ordered()->get();
    $popup = Popup::active()->currentlyValid()->latest()->first();
    return view('home', compact('schools', 'slides', 'popup'));
})->name('home');

// --- Pages statiques ---
Route::get('/comment-ca-marche', [PageController::class, 'howItWorks'])->name('pages.how-it-works');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'contactSend'])->name('pages.contact.send');
Route::get('/conditions-utilisation', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/confidentialite', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/mentions-legales', [PageController::class, 'legal'])->name('pages.legal');

// --- Catalogue public ---
Route::get('/catalogue', [CatalogController::class, 'schools'])->name('catalog.schools');
Route::get('/catalogue/recherche', [CatalogController::class, 'search'])->name('catalog.search');
Route::get('/catalogue/{school}/{grade}', [CatalogController::class, 'grade'])->name('catalog.grade');
Route::get('/livre/{officialBook}', [CatalogController::class, 'book'])->name('catalog.book');

// --- Profil vendeur public ---
Route::get('/vendeur/{user}', [SellerProfileController::class, 'show'])->name('seller.public-profile');

// --- Panier (session, accessible à tous) ---
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
Route::post('/panier/modifier', [CartController::class, 'update'])->name('cart.update');
Route::post('/panier/supprimer', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/panier/count', [CartController::class, 'count'])->name('cart.count');

// --- Espace authentifié commun ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Checkout (authentifié obligatoire)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Commandes acheteur
    Route::get('/mes-commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mes-commandes/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/mes-commandes/{order}/facture', [InvoiceController::class, 'download'])->name('orders.invoice');

    // Favoris / Wishlist
    Route::get('/favoris', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/favoris/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/favoris/{wishlist}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

// --- Espace Admin ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        // Stats de base
        $schoolsCount = School::count();
        $booksCount = OfficialBook::count();
        $pendingCount = SellerBook::where('status', 'pending')->count();
        $ordersCount = Order::count();
        $usersCount = User::count();
        $revenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $recentOrders = Order::with('user:id,name')->latest()->take(5)->get();
        $pendingBooks = SellerBook::with(['seller:id,name', 'officialBook:id,title'])->where('status', 'pending')->latest()->take(5)->get();

        // Chiffre d'affaires des 6 derniers mois
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueData[] = [
                'month' => $date->translatedFormat('M Y'),
                'total' => (int) Order::where('status', '!=', 'cancelled')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount'),
            ];
        }

        // Top 5 vendeurs — remplacé par livres les plus vendus
        $topSellingBooks = OrderItem::selectRaw('seller_books.official_book_id, official_books.title as book_title, SUM(order_items.quantity) as total_sold')
            ->join('seller_books', 'seller_books.id', '=', 'order_items.seller_book_id')
            ->join('official_books', 'official_books.id', '=', 'seller_books.official_book_id')
            ->groupBy('seller_books.official_book_id', 'official_books.title')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Top 5 livres les plus populaires (wishlist)
        $topPopularBooks = \App\Models\Wishlist::selectRaw('wishlists.official_book_id, official_books.title as book_title, COUNT(*) as wishlist_count')
            ->join('official_books', 'official_books.id', '=', 'wishlists.official_book_id')
            ->groupBy('wishlists.official_book_id', 'official_books.title')
            ->orderByDesc('wishlist_count')
            ->take(5)
            ->get();

        // Statistiques de fréquentation (30 derniers jours)
        $last30 = now()->subDays(30)->startOfDay();
        $uniqueVisitors = PageView::where('viewed_at', '>=', $last30)
            ->distinct('session_id')->count('session_id');
        $totalPageViews = PageView::where('viewed_at', '>=', $last30)->count();
        $ordersLast30 = Order::where('created_at', '>=', $last30)->count();
        $conversionRate = $uniqueVisitors > 0 ? round($ordersLast30 / $uniqueVisitors * 100, 1) : 0;

        // Indicateurs financiers
        $totalExpenses = SellerBook::where('admin_paid_seller', true)->sum('buyback_price');
        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->selectRaw('SUM(total_amount + delivery_fee) as grand')
            ->value('grand') ?? 0;
        $netProfit = (int)$totalRevenue - (int)$totalExpenses;

        // Commandes par statut
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', compact(
            'schoolsCount', 'booksCount', 'pendingCount', 'ordersCount', 'usersCount', 'revenue',
            'recentOrders', 'pendingBooks', 'revenueData', 'ordersByStatus',
            'topSellingBooks', 'topPopularBooks',
            'uniqueVisitors', 'totalPageViews', 'conversionRate',
            'totalExpenses', 'totalRevenue', 'netProfit'
        ));
    })->name('dashboard');

    // CRUD Écoles
    Route::resource('schools', SchoolController::class)->except('show');

    // CRUD Classes
    Route::resource('grades', GradeController::class)->except('show');

    // CRUD Matières
    Route::resource('subjects', SubjectController::class)->except('show');

    // CRUD Livres officiels
    Route::resource('official-books', OfficialBookController::class)->except('show');

    // Validation livres vendeurs
    Route::get('seller-books', [SellerBookValidationController::class, 'index'])->name('seller-books.index');
    Route::get('seller-books/{sellerBook}', [SellerBookValidationController::class, 'show'])->name('seller-books.show');
    Route::post('seller-books/{sellerBook}/approve', [SellerBookValidationController::class, 'approve'])->name('seller-books.approve');
    Route::post('seller-books/{sellerBook}/reject', [SellerBookValidationController::class, 'reject'])->name('seller-books.reject');
    Route::post('seller-books/{sellerBook}/buyback-propose', [SellerBookValidationController::class, 'buybackPropose'])->name('seller-books.buyback-propose');
    Route::post('seller-books/{sellerBook}/mark-paid', [SellerBookValidationController::class, 'markPaid'])->name('seller-books.mark-paid');

    // API interne — classes par école (pour select dynamique Alpine.js)
    Route::get('api/grades', function () {
        $grades = Grade::where('school_id', request('school_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'level', 'academic_year']);

        return response()->json($grades);
    })->name('api.grades');

    // Commandes admin
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Utilisateurs admin
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');

    // Points relais
    Route::resource('relay-points', RelayPointController::class)->except('show');

    // Bannières
    Route::resource('banners', BannerController::class)->except('show');

    // Paramètres
    Route::resource('settings', SettingController::class)->except('show');
    Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::post('settings/general', [SettingController::class, 'saveGeneral'])->name('settings.general.save');

    // Sliders publicitaires
    Route::resource('sliders', SliderController::class)->except('show');

    // Popups publicitaires
    Route::resource('popups', PopupController::class)->except('show');
});

// --- Espace Vendeur ---
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    // Dashboard
    Route::get('/', SellerDashboardController::class)->name('dashboard');

    // CRUD Livres vendeur
    Route::resource('books', SellerBookController::class)->except('show');
    Route::post('books/{book}/buyback-respond', [SellerBookController::class, 'buybackRespond'])->name('books.buyback-respond');

    // Commandes (ventes du vendeur)
    Route::get('orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');

    // API interne — classes par école (pour select dynamique Alpine.js — vendeur)
    Route::get('api/grades', function () {
        $grades = Grade::where('school_id', request('school_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'level', 'academic_year']);

        return response()->json($grades);
    })->name('api.grades');

    // API interne — livres officiels par classe (pour select dynamique Alpine.js)
    Route::get('api/official-books', function () {
        $books = OfficialBook::where('grade_id', request('grade_id'))
            ->where('is_active', true)
            ->with('subject:id,name')
            ->orderBy('title')
            ->get(['id', 'title', 'subject_id'])
            ->map(fn ($b) => ['id' => $b->id, 'title' => $b->title, 'subject' => $b->subject->name ?? '']);

        return response()->json($books);
    })->name('api.official-books');
});

require __DIR__.'/auth.php';
