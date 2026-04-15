<?php

namespace Tests\Feature;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\RelayPoint;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\OrderConfirmationNotification;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Test E2E du workflow complet d'achat d'un livre :
 *
 * 1. Vendeur soumet un livre → admin l'approuve
 * 2. Acheteur consulte le catalogue → trouve le livre
 * 3. Acheteur ajoute au panier → modifie quantité → voit le panier
 * 4. Acheteur passe au checkout → confirme la commande
 * 5. Page de confirmation affichée → notification email envoyée
 * 6. Acheteur consulte ses commandes → détail → facture PDF
 * 7. Admin met à jour le statut → notification envoyée
 * 8. Vendeur voit la commande dans ses ventes
 */
class PurchaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;
    private User $admin;
    private School $school;
    private Grade $grade;
    private Subject $subject;
    private OfficialBook $officialBook;
    private RelayPoint $relayPoint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['role' => 'seller', 'name' => 'Vendeur Koné']);
        $this->buyer = User::factory()->create(['role' => 'buyer', 'name' => 'Acheteur Diallo']);
        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Touré']);

        $this->school = School::create([
            'name' => 'Lycée Classique Abidjan',
            'city' => 'Abidjan',
            'district' => 'Cocody',
            'is_active' => true,
        ]);

        $this->grade = Grade::create([
            'school_id' => $this->school->id,
            'name' => '6ème A',
            'level' => '6ème',
            'academic_year' => '2024-2025',
        ]);

        $this->subject = Subject::create(['name' => 'Mathématiques']);

        $this->officialBook = OfficialBook::create([
            'grade_id' => $this->grade->id,
            'subject_id' => $this->subject->id,
            'title' => 'CIAM Maths 6ème',
            'author' => 'Collectif',
            'is_active' => true,
        ]);

        $this->relayPoint = RelayPoint::create([
            'name' => 'Point Relais Cocody',
            'address' => 'Rue des Jardins, Cocody',
            'city' => 'Abidjan',
            'district' => 'Cocody',
            'contact_phone' => '0707070707',
            'is_active' => true,
        ]);
    }

    // ==========================================
    // ÉTAPE 1 : Le vendeur soumet un livre
    // ==========================================

    public function test_step1_seller_submits_book(): void
    {
        $response = $this->actingAs($this->seller)
            ->post(route('seller.books.store'), [
                'official_book_id' => $this->officialBook->id,
                'condition' => 'good',
                'price' => 4500,
                'quantity' => 5,
            ]);

        $response->assertRedirect(route('seller.books.index'));

        $sellerBook = SellerBook::where('user_id', $this->seller->id)->first();
        $this->assertNotNull($sellerBook);
        $this->assertEquals(BookStatus::Pending, $sellerBook->status);
        $this->assertEquals(4500, $sellerBook->price);
        $this->assertEquals(5, $sellerBook->quantity);
    }

    // ==========================================
    // ÉTAPE 2 : L'admin approuve le livre
    // ==========================================

    public function test_step2_admin_approves_book(): void
    {
        $sellerBook = SellerBook::create([
            'user_id' => $this->seller->id,
            'official_book_id' => $this->officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 4500,
            'quantity' => 5,
            'status' => BookStatus::Pending,
        ]);

        Notification::fake();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.seller-books.approve', $sellerBook));

        $response->assertRedirect();

        $sellerBook->refresh();
        $this->assertEquals(BookStatus::Approved, $sellerBook->status);

        Notification::assertSentTo($this->seller, \App\Notifications\SellerBookStatusNotification::class);
    }

    // ==========================================
    // ÉTAPE 3 : L'acheteur parcourt le catalogue
    // ==========================================

    public function test_step3_buyer_browses_catalogue(): void
    {
        $sellerBook = $this->createApprovedBook();

        // 3a. Page des écoles
        $this->get(route('catalog.schools'))
            ->assertOk()
            ->assertSee('Lycée Classique Abidjan');

        // 3b. Page d'une classe
        $this->get(route('catalog.grade', [$this->school, $this->grade]))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème');

        // 3c. Détail du livre avec offres vendeurs
        $this->get(route('catalog.book', $this->officialBook))
            ->assertOk()
            ->assertSee('4 500')
            ->assertSee('Vendeur Koné');

        // 3d. Recherche
        $this->get(route('catalog.search', ['q' => 'CIAM']))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème');
    }

    // ==========================================
    // ÉTAPE 4 : Ajout au panier + gestion
    // ==========================================

    public function test_step4_cart_management(): void
    {
        $sellerBook = $this->createApprovedBook();

        // 4a. Ajouter au panier
        $this->actingAs($this->buyer)
            ->post(route('cart.add'), [
                'seller_book_id' => $sellerBook->id,
                'quantity' => 1,
            ])
            ->assertRedirect();

        // 4b. Vérifier le panier en session
        $cart = session('cart');
        $this->assertEquals(1, $cart[$sellerBook->id]);

        // 4c. Modifier la quantité
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$sellerBook->id => 1]])
            ->post(route('cart.update'), [
                'seller_book_id' => $sellerBook->id,
                'quantity' => 3,
            ])
            ->assertRedirect(route('cart.index'));

        $this->assertEquals(3, session('cart')[$sellerBook->id]);

        // 4d. Voir le panier
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$sellerBook->id => 3]])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème')
            ->assertSee('13 500'); // 4500 × 3

        // 4e. API cart count
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$sellerBook->id => 3]])
            ->getJson(route('cart.count'))
            ->assertJson(['count' => 3]);
    }

    // ==========================================
    // ÉTAPE 5 : Checkout + création commande
    // ==========================================

    public function test_step5_checkout_and_order_creation(): void
    {
        $sellerBook = $this->createApprovedBook();

        Notification::fake();

        // 5a. Page checkout avec articles
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$sellerBook->id => 2]])
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème')
            ->assertSee('9 000') // 4500 × 2
            ->assertSee('Point Relais Cocody');

        // 5b. Confirmer la commande
        $response = $this->actingAs($this->buyer)
            ->withSession(['cart' => [$sellerBook->id => 2]])
            ->post(route('checkout.store'), [
                'relay_point_id' => $this->relayPoint->id,
                'delivery_notes' => 'Merci de préparer rapidement',
            ]);

        // 5c. Redirection vers la page de confirmation
        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.confirmation', $order));

        // 5d. Vérifier la commande
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(9000, $order->total_amount); // 4500 × 2
        $this->assertEquals($this->relayPoint->id, $order->relay_point_id);
        $this->assertEquals('Merci de préparer rapidement', $order->delivery_notes);

        // 5e. Vérifier les articles
        $this->assertEquals(1, $order->items()->count());
        $item = $order->items->first();
        $this->assertEquals($sellerBook->id, $item->seller_book_id);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(4500, $item->unit_price);
        $this->assertEquals(9000, $item->subtotal);

        // 5f. Stock décrémenté
        $sellerBook->refresh();
        $this->assertEquals(3, $sellerBook->quantity); // 5 - 2

        // 5g. Événement de commande créé
        $this->assertEquals(1, $order->events()->count());
        $this->assertEquals(OrderStatus::Pending, $order->events->first()->status);

        // 5h. Panier vidé
        $this->assertEmpty(session('cart'));

        // 5i. Notification email envoyée
        Notification::assertSentTo($this->buyer, OrderConfirmationNotification::class);
    }

    // ==========================================
    // ÉTAPE 6 : Page de confirmation
    // ==========================================

    public function test_step6_confirmation_page(): void
    {
        $order = $this->createOrder();

        $this->actingAs($this->buyer)
            ->get(route('checkout.confirmation', $order))
            ->assertOk()
            ->assertSee('Merci pour votre commande')
            ->assertSee('#' . $order->id)
            ->assertSee('CIAM Maths 6ème');
    }

    // ==========================================
    // ÉTAPE 7 : Mes commandes (acheteur)
    // ==========================================

    public function test_step7_buyer_orders_list_and_detail(): void
    {
        $order = $this->createOrder();

        // 7a. Liste des commandes
        $this->actingAs($this->buyer)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Commande #' . $order->id);

        // 7b. Détail de la commande
        $this->actingAs($this->buyer)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème')
            ->assertSee('9 000')
            ->assertSee('Point Relais Cocody');
    }

    // ==========================================
    // ÉTAPE 8 : Facture PDF
    // ==========================================

    public function test_step8_invoice_download(): void
    {
        $order = $this->createOrder();

        $response = $this->actingAs($this->buyer)
            ->get(route('orders.invoice', $order));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    // ==========================================
    // ÉTAPE 9 : Admin met à jour le statut
    // ==========================================

    public function test_step9_admin_updates_order_status(): void
    {
        $order = $this->createOrder();

        Notification::fake();

        // 9a. Admin confirme la commande
        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'confirmed',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();
        $this->assertEquals(OrderStatus::Confirmed, $order->status);

        // 9b. Événement créé
        $this->assertEquals(2, $order->events()->count());

        // 9c. Notification envoyée à l'acheteur
        Notification::assertSentTo($this->buyer, OrderStatusChangedNotification::class);

        // 9d. Admin prépare → prête → livrée
        Notification::fake();

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'preparing',
            ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'ready',
            ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.orders.update-status', $order), [
                'status' => 'delivered',
            ]);

        $order->refresh();
        $this->assertEquals(OrderStatus::Delivered, $order->status);
        $this->assertEquals(5, $order->events()->count()); // pending + confirmed + preparing + ready + delivered

        Notification::assertSentTo($this->buyer, OrderStatusChangedNotification::class, function ($notification) {
            return true;
        });
    }

    // ==========================================
    // ÉTAPE 10 : Le vendeur voit la commande
    // ==========================================

    public function test_step10_seller_sees_order(): void
    {
        $order = $this->createOrder();

        // 10a. Liste des ventes du vendeur
        $this->actingAs($this->seller)
            ->get(route('seller.orders.index'))
            ->assertOk()
            ->assertSee('#' . $order->id);

        // 10b. Détail d'une vente
        $this->actingAs($this->seller)
            ->get(route('seller.orders.show', $order))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème');
    }

    // ==========================================
    // ÉTAPE 11 : Sécurité — un autre acheteur ne peut pas voir
    // ==========================================

    public function test_step11_other_buyer_cannot_see_order(): void
    {
        $order = $this->createOrder();
        $otherBuyer = User::factory()->create(['role' => 'buyer']);

        $this->actingAs($otherBuyer)
            ->get(route('orders.show', $order))
            ->assertForbidden();

        $this->actingAs($otherBuyer)
            ->get(route('checkout.confirmation', $order))
            ->assertForbidden();

        $this->actingAs($otherBuyer)
            ->get(route('orders.invoice', $order))
            ->assertForbidden();
    }

    // ==========================================
    // ÉTAPE 12 : Scénario complet bout-en-bout en un seul test
    // ==========================================

    public function test_full_purchase_workflow_end_to_end(): void
    {
        Notification::fake();

        // --- Vendeur soumet un livre ---
        $this->actingAs($this->seller)
            ->post(route('seller.books.store'), [
                'official_book_id' => $this->officialBook->id,
                'condition' => 'good',
                'price' => 3500,
                'quantity' => 4,
            ]);
        $sellerBook = SellerBook::where('user_id', $this->seller->id)->latest()->first();
        $this->assertEquals(BookStatus::Pending, $sellerBook->status);

        // --- Admin approuve ---
        $this->actingAs($this->admin)
            ->post(route('admin.seller-books.approve', $sellerBook));
        $sellerBook->refresh();
        $this->assertEquals(BookStatus::Approved, $sellerBook->status);

        // --- Acheteur consulte le catalogue ---
        $this->get(route('catalog.book', $this->officialBook))
            ->assertOk()
            ->assertSee('3 500');

        // --- Acheteur ajoute au panier ---
        $this->actingAs($this->buyer)
            ->post(route('cart.add'), [
                'seller_book_id' => $sellerBook->id,
                'quantity' => 2,
            ]);

        // --- Checkout ---
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$sellerBook->id => 2]])
            ->post(route('checkout.store'), [
                'relay_point_id' => $this->relayPoint->id,
                'delivery_notes' => 'Workflow complet',
            ]);

        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(7000, $order->total_amount); // 3500 × 2
        $this->assertEquals(OrderStatus::Pending, $order->status);

        // Stock mis à jour
        $sellerBook->refresh();
        $this->assertEquals(2, $sellerBook->quantity); // 4 - 2

        // Notification de confirmation
        Notification::assertSentTo($this->buyer, OrderConfirmationNotification::class);

        // --- Confirmation page ---
        $this->actingAs($this->buyer)
            ->get(route('checkout.confirmation', $order))
            ->assertOk()
            ->assertSee('Merci pour votre commande');

        // --- Mes commandes ---
        $this->actingAs($this->buyer)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('#' . $order->id);

        // --- Détail commande ---
        $this->actingAs($this->buyer)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème');

        // --- Facture ---
        $this->actingAs($this->buyer)
            ->get(route('orders.invoice', $order))
            ->assertOk();

        // --- Admin met le statut à confirmed → preparing → ready → delivered ---
        Notification::fake();

        foreach (['confirmed', 'preparing', 'ready', 'delivered'] as $status) {
            $this->actingAs($this->admin)
                ->patch(route('admin.orders.update-status', $order), [
                    'status' => $status,
                ]);
        }

        $order->refresh();
        $this->assertEquals(OrderStatus::Delivered, $order->status);
        $this->assertEquals(5, $order->events()->count());

        // --- Vendeur voit sa vente ---
        $this->actingAs($this->seller)
            ->get(route('seller.orders.index'))
            ->assertOk();

        $this->actingAs($this->seller)
            ->get(route('seller.orders.show', $order))
            ->assertOk()
            ->assertSee('CIAM Maths 6ème');
    }

    // ==========================================
    // Helpers
    // ==========================================

    private function createApprovedBook(): SellerBook
    {
        return SellerBook::create([
            'user_id' => $this->seller->id,
            'official_book_id' => $this->officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 4500,
            'quantity' => 5,
            'status' => BookStatus::Approved,
        ]);
    }

    private function createOrder(): Order
    {
        $sellerBook = $this->createApprovedBook();

        $order = Order::create([
            'user_id' => $this->buyer->id,
            'relay_point_id' => $this->relayPoint->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 9000,
            'delivery_notes' => 'Test workflow',
        ]);

        $order->items()->create([
            'seller_book_id' => $sellerBook->id,
            'quantity' => 2,
            'unit_price' => 4500,
        ]);

        OrderEvent::create([
            'order_id' => $order->id,
            'status' => OrderStatus::Pending,
            'comment' => 'Commande passée par le client.',
        ]);

        $sellerBook->decrement('quantity', 2);

        return $order;
    }
}
