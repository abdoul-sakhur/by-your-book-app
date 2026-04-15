<?php

namespace Database\Seeders;

use App\Enums\BannerPosition;
use App\Enums\BannerTarget;
use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Banner;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\RelayPoint;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // --- Écoles ---
        $school1 = School::create([
            'name' => 'Groupe Scolaire Les Étoiles',
            'city' => 'Abidjan',
            'district' => 'Cocody',
            'is_active' => true,
        ]);

        $school2 = School::create([
            'name' => 'Institution Sainte-Marie',
            'city' => 'Abidjan',
            'district' => 'Marcory',
            'is_active' => true,
        ]);

        // --- Niveaux (Grades) ---
        $grade6A = Grade::create([
            'school_id' => $school1->id,
            'name' => '6ème A',
            'level' => '6ème',
            'academic_year' => '2025-2026',
        ]);

        $grade5B = Grade::create([
            'school_id' => $school1->id,
            'name' => '5ème B',
            'level' => '5ème',
            'academic_year' => '2025-2026',
        ]);

        $grade6SM = Grade::create([
            'school_id' => $school2->id,
            'name' => '6ème',
            'level' => '6ème',
            'academic_year' => '2025-2026',
        ]);

        // --- Matières (Subjects) ---
        $maths = Subject::create(['name' => 'Mathématiques']);
        $francais = Subject::create(['name' => 'Français']);
        $anglais = Subject::create(['name' => 'Anglais']);
        $svt = Subject::create(['name' => 'Sciences de la Vie et de la Terre']);
        $histoire = Subject::create(['name' => 'Histoire-Géographie']);

        // --- Livres officiels ---
        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $maths->id,
            'title' => 'CIAM Mathématiques 6ème',
            'author' => 'Collection CIAM',
            'publisher' => 'EDICEF',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $francais->id,
            'title' => 'Lecture et Expression 6ème',
            'author' => 'B. Koné',
            'publisher' => 'NEI-CEDA',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $anglais->id,
            'title' => 'English for Africa 6ème',
            'author' => 'J. Adu',
            'publisher' => 'Macmillan',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $svt->id,
            'title' => 'SVT 6ème - Collection AREX',
            'author' => 'A. Touré',
            'publisher' => 'AREX',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $histoire->id,
            'title' => 'Histoire-Géo 6ème',
            'author' => 'M. Diallo',
            'publisher' => 'Hatier CI',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade5B->id,
            'subject_id' => $maths->id,
            'title' => 'CIAM Mathématiques 5ème',
            'author' => 'Collection CIAM',
            'publisher' => 'EDICEF',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade5B->id,
            'subject_id' => $francais->id,
            'title' => 'Lecture et Expression 5ème',
            'author' => 'B. Koné',
            'publisher' => 'NEI-CEDA',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6SM->id,
            'subject_id' => $maths->id,
            'title' => 'CIAM Mathématiques 6ème',
            'author' => 'Collection CIAM',
            'publisher' => 'EDICEF',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6SM->id,
            'subject_id' => $francais->id,
            'title' => 'Français 6ème - Sainte-Marie',
            'author' => 'S. Bamba',
            'publisher' => 'NEI-CEDA',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6SM->id,
            'subject_id' => $anglais->id,
            'title' => 'Go for English 6ème',
            'author' => 'P. Williams',
            'publisher' => 'Macmillan',
            'is_active' => true,
        ]);

        // --- Points relais ---
        $relay1 = RelayPoint::create([
            'name' => 'Librairie Le Savoir - Cocody',
            'address' => 'Boulevard Latrille, près du carrefour de la Vie',
            'city' => 'Abidjan',
            'district' => 'Cocody',
            'contact_phone' => '+225 0707070707',
            'schedule' => 'Lun-Sam 8h-18h',
            'is_active' => true,
        ]);

        $relay2 = RelayPoint::create([
            'name' => 'Papeterie Centrale - Plateau',
            'address' => 'Avenue Terrasson de Fougères, Immeuble Alpha',
            'city' => 'Abidjan',
            'district' => 'Plateau',
            'contact_phone' => '+225 0505050505',
            'schedule' => 'Lun-Ven 9h-17h',
            'is_active' => true,
        ]);

        RelayPoint::create([
            'name' => 'Kiosque Lecture - Marcory',
            'address' => 'Rue 12, Zone 4C',
            'city' => 'Abidjan',
            'district' => 'Marcory',
            'contact_phone' => '+225 0103030303',
            'schedule' => 'Lun-Sam 8h-19h',
            'is_active' => true,
        ]);

        // --- Utilisateurs vendeurs ---
        $seller1 = User::create([
            'name' => 'Kouamé Yao',
            'email' => 'vendeur1@buyyourbook.ci',
            'password' => bcrypt('password'),
            'role' => UserRole::Seller,
            'phone' => '+225 0708090102',
            'address' => 'Cocody Angré, Abidjan',
            'is_active' => true,
        ]);

        $seller2 = User::create([
            'name' => 'Aminata Diallo',
            'email' => 'vendeur2@buyyourbook.ci',
            'password' => bcrypt('password'),
            'role' => UserRole::Seller,
            'phone' => '+225 0504030201',
            'address' => 'Marcory Résidentiel, Abidjan',
            'is_active' => true,
        ]);

        // --- Utilisateur acheteur ---
        $buyer = User::create([
            'name' => 'Sarba Client',
            'email' => 'sarbaclient@buyyourbook.ci',
            'password' => bcrypt('password'),
            'role' => UserRole::Buyer,
            'phone' => '+225 0101020304',
            'address' => 'Yopougon Toits Rouges, Abidjan',
            'is_active' => true,
        ]);

        // --- Livres vendeurs (approuvés, en attente, rejetés) ---
        $officialBooks = OfficialBook::all();

        // Vendeur 1 : 5 livres approuvés
        $sellerBooks1 = [];
        foreach ($officialBooks->take(5) as $i => $ob) {
            $sellerBooks1[] = SellerBook::create([
                'user_id' => $seller1->id,
                'official_book_id' => $ob->id,
                'condition' => $i % 2 === 0 ? BookCondition::Good->value : BookCondition::New->value,
                'price' => rand(1500, 5000),
                'quantity' => rand(1, 3),
                'status' => BookStatus::Approved->value,
            ]);
        }

        // Vendeur 2 : 3 livres approuvés + 1 en attente + 1 rejeté
        foreach ($officialBooks->skip(2)->take(3) as $ob) {
            SellerBook::create([
                'user_id' => $seller2->id,
                'official_book_id' => $ob->id,
                'condition' => BookCondition::Acceptable->value,
                'price' => rand(1000, 3500),
                'quantity' => rand(1, 2),
                'status' => BookStatus::Approved->value,
            ]);
        }

        SellerBook::create([
            'user_id' => $seller2->id,
            'official_book_id' => $officialBooks->last()->id,
            'condition' => BookCondition::Good->value,
            'price' => 2500,
            'quantity' => 1,
            'status' => BookStatus::Pending->value,
        ]);

        SellerBook::create([
            'user_id' => $seller1->id,
            'official_book_id' => $officialBooks->skip(5)->first()->id,
            'condition' => BookCondition::Acceptable->value,
            'price' => 800,
            'quantity' => 1,
            'status' => BookStatus::Rejected->value,
            'rejection_reason' => 'Photos illisibles, veuillez reprendre les images.',
        ]);

        // --- Commandes de démo ---

        // Commande 1 : livrée
        $order1 = Order::create([
            'user_id' => $buyer->id,
            'relay_point_id' => $relay1->id,
            'status' => OrderStatus::Delivered->value,
            'total_amount' => 0,
        ]);
        $item1 = OrderItem::create([
            'order_id' => $order1->id,
            'seller_book_id' => $sellerBooks1[0]->id,
            'quantity' => 1,
            'unit_price' => $sellerBooks1[0]->price,
        ]);
        $item2 = OrderItem::create([
            'order_id' => $order1->id,
            'seller_book_id' => $sellerBooks1[1]->id,
            'quantity' => 1,
            'unit_price' => $sellerBooks1[1]->price,
        ]);
        $order1->update(['total_amount' => $item1->unit_price + $item2->unit_price]);

        // Historique des événements
        foreach ([OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Preparing, OrderStatus::Ready, OrderStatus::Delivered] as $status) {
            OrderEvent::create([
                'order_id' => $order1->id,
                'status' => $status->value,
                'comment' => 'Statut mis à jour automatiquement (démo)',
            ]);
        }

        // Commande 2 : en préparation
        $order2 = Order::create([
            'user_id' => $buyer->id,
            'relay_point_id' => $relay2->id,
            'status' => OrderStatus::Preparing->value,
            'total_amount' => 0,
            'delivery_notes' => 'Merci de bien emballer les livres.',
        ]);
        $item3 = OrderItem::create([
            'order_id' => $order2->id,
            'seller_book_id' => $sellerBooks1[2]->id,
            'quantity' => 2,
            'unit_price' => $sellerBooks1[2]->price,
        ]);
        $order2->update(['total_amount' => $item3->unit_price * $item3->quantity]);

        foreach ([OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Preparing] as $status) {
            OrderEvent::create([
                'order_id' => $order2->id,
                'status' => $status->value,
                'comment' => 'Statut mis à jour automatiquement (démo)',
            ]);
        }

        // Commande 3 : en attente (vient d'être passée)
        $order3 = Order::create([
            'user_id' => $buyer->id,
            'relay_point_id' => $relay1->id,
            'status' => OrderStatus::Pending->value,
            'total_amount' => $sellerBooks1[4]->price,
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'seller_book_id' => $sellerBooks1[4]->id,
            'quantity' => 1,
            'unit_price' => $sellerBooks1[4]->price,
        ]);
        OrderEvent::create([
            'order_id' => $order3->id,
            'status' => OrderStatus::Pending->value,
            'comment' => 'Commande passée par le client.',
        ]);

        // --- Deuxième acheteur ---
        $buyer2 = User::create([
            'name' => 'Fatou Bamba',
            'email' => 'fatou@buyyourbook.ci',
            'password' => bcrypt('password'),
            'role' => UserRole::Buyer,
            'phone' => '+225 0709080706',
            'address' => 'Cocody Riviera 2, Abidjan',
            'is_active' => true,
        ]);

        // Commande 4 : annulée (buyer2)
        $order4 = Order::create([
            'user_id' => $buyer2->id,
            'relay_point_id' => $relay2->id,
            'status' => OrderStatus::Cancelled->value,
            'total_amount' => $sellerBooks1[3]->price,
        ]);
        OrderItem::create([
            'order_id' => $order4->id,
            'seller_book_id' => $sellerBooks1[3]->id,
            'quantity' => 1,
            'unit_price' => $sellerBooks1[3]->price,
        ]);
        foreach ([OrderStatus::Pending, OrderStatus::Cancelled] as $status) {
            OrderEvent::create([
                'order_id' => $order4->id,
                'status' => $status->value,
                'comment' => $status === OrderStatus::Cancelled
                    ? 'Annulée par le client.'
                    : 'Commande passée.',
            ]);
        }

        // Commande 5 : confirmée (buyer2)
        $order5 = Order::create([
            'user_id' => $buyer2->id,
            'relay_point_id' => $relay1->id,
            'status' => OrderStatus::Confirmed->value,
            'total_amount' => 0,
        ]);
        $item5a = OrderItem::create([
            'order_id' => $order5->id,
            'seller_book_id' => $sellerBooks1[0]->id,
            'quantity' => 1,
            'unit_price' => $sellerBooks1[0]->price,
        ]);
        $item5b = OrderItem::create([
            'order_id' => $order5->id,
            'seller_book_id' => $sellerBooks1[2]->id,
            'quantity' => 1,
            'unit_price' => $sellerBooks1[2]->price,
        ]);
        $order5->update(['total_amount' => $item5a->unit_price + $item5b->unit_price]);
        foreach ([OrderStatus::Pending, OrderStatus::Confirmed] as $status) {
            OrderEvent::create([
                'order_id' => $order5->id,
                'status' => $status->value,
                'comment' => 'Statut mis à jour automatiquement (démo)',
            ]);
        }

        // --- Wishlists ---
        $allBooks = OfficialBook::all();

        // Buyer 1 a 3 livres en favoris
        foreach ($allBooks->take(3) as $book) {
            Wishlist::create([
                'user_id' => $buyer->id,
                'official_book_id' => $book->id,
            ]);
        }

        // Buyer 2 a 2 livres en favoris
        foreach ($allBooks->skip(4)->take(2) as $book) {
            Wishlist::create([
                'user_id' => $buyer2->id,
                'official_book_id' => $book->id,
            ]);
        }

        // --- Bannières ---
        Banner::create([
            'title' => 'Rentrée Scolaire 2025-2026',
            'image' => 'banners/rentree-2025.jpg',
            'link_url' => '/catalogue/recherche',
            'position' => BannerPosition::HomeTop,
            'target_type' => BannerTarget::All,
            'school_id' => null,
            'is_active' => true,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonths(3),
        ]);

        Banner::create([
            'title' => 'Livres à petits prix',
            'image' => 'banners/petits-prix.jpg',
            'link_url' => '/catalogue/recherche?sort=price_asc',
            'position' => BannerPosition::HomeMid,
            'target_type' => BannerTarget::All,
            'school_id' => null,
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ]);

        Banner::create([
            'title' => 'Offres spéciales Les Étoiles',
            'image' => 'banners/etoiles-promo.jpg',
            'link_url' => '/catalogue/' . $school1->id . '/classes/' . $grade6A->id,
            'position' => BannerPosition::Sidebar,
            'target_type' => BannerTarget::School,
            'school_id' => $school1->id,
            'is_active' => true,
            'starts_at' => now()->subWeek(),
            'ends_at' => now()->addMonth(),
        ]);

        Banner::create([
            'title' => 'Bannière expirée (test)',
            'image' => 'banners/old-promo.jpg',
            'link_url' => '/catalogue/recherche',
            'position' => BannerPosition::HomeTop,
            'target_type' => BannerTarget::All,
            'school_id' => null,
            'is_active' => false,
            'starts_at' => now()->subMonths(6),
            'ends_at' => now()->subMonth(),
        ]);

        // --- Settings ---
        Setting::set('site_name', 'BuyYourBook');
        Setting::set('site_description', 'Marketplace de livres scolaires d\'occasion en Côte d\'Ivoire');
        Setting::set('contact_email', 'contact@buyyourbook.ci');
        Setting::set('contact_phone', '+225 0101010101');
        Setting::set('max_cart_items', '10');
        Setting::set('commission_rate', '5');
        Setting::set('currency', 'FCFA');
        Setting::set('academic_year', '2025-2026');
    }
}
