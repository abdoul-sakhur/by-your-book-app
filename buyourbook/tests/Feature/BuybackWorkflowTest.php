<?php

namespace Tests\Feature;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\BuybackOfferNotification;
use App\Notifications\BuybackResponseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Test du flux de rachat (Buyback) complet :
 *
 * 1. Admin propose un prix de rachat → vendeur notifié
 * 2. Vendeur accepte → admin notifié
 * 3. Flux contre-offre vendeur → admin notifié
 * 4. Vendeur refuse → admin notifié
 * 5. Admin marque le vendeur comme payé
 * 6. Protections d'accès (seller ne peut pas proposer un rachat, etc.)
 */
class BuybackWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $seller;
    private SellerBook $sellerBook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin  = User::factory()->create(['role' => 'admin', 'name' => 'Admin Test']);
        $this->seller = User::factory()->create(['role' => 'seller', 'name' => 'Vendeur Test']);

        $school        = School::create(['name' => 'Lycée Test', 'city' => 'Abidjan', 'district' => 'Cocody']);
        $grade         = Grade::create(['school_id' => $school->id, 'name' => '3ème A', 'level' => '3ème', 'academic_year' => '2024-2025']);
        $subject       = Subject::create(['name' => 'Physique']);
        $officialBook  = OfficialBook::create([
            'grade_id'   => $grade->id,
            'subject_id' => $subject->id,
            'title'      => 'Physique-Chimie 3ème',
            'is_active'  => true,
        ]);

        $this->sellerBook = SellerBook::create([
            'user_id'          => $this->seller->id,
            'official_book_id' => $officialBook->id,
            'condition'        => BookCondition::Good,
            'price'            => 6000,
            'quantity'         => 1,
            'status'           => BookStatus::Approved,
            'buyback_status'   => 'pending',
        ]);
    }

    // =====================================================
    // ÉTAPE 1 : Admin propose un prix de rachat
    // =====================================================

    public function test_admin_can_propose_buyback_offer(): void
    {
        Notification::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.seller-books.buyback-propose', $this->sellerBook), [
                'buyback_price' => 3000,
                'buyback_notes' => 'Bon état, offre correcte.',
            ])
            ->assertRedirect(route('admin.seller-books.show', $this->sellerBook));

        $this->sellerBook->refresh();
        $this->assertEquals(3000, $this->sellerBook->buyback_price);
        $this->assertEquals('negotiating', $this->sellerBook->buyback_status);
        $this->assertEquals('Bon état, offre correcte.', $this->sellerBook->buyback_notes);

        Notification::assertSentTo($this->seller, BuybackOfferNotification::class);
    }

    public function test_admin_buyback_propose_requires_price(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.seller-books.buyback-propose', $this->sellerBook), [
                'buyback_notes' => 'Pas de prix',
            ])
            ->assertSessionHasErrors('buyback_price');
    }

    public function test_admin_buyback_propose_price_must_be_positive(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.seller-books.buyback-propose', $this->sellerBook), [
                'buyback_price' => 0,
            ])
            ->assertSessionHasErrors('buyback_price');
    }

    // =====================================================
    // ÉTAPE 2 : Vendeur accepte l'offre
    // =====================================================

    public function test_seller_can_accept_buyback_offer(): void
    {
        Notification::fake();

        $this->sellerBook->update(['buyback_status' => 'negotiating', 'buyback_price' => 3000]);

        $this->actingAs($this->seller)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action' => 'accept',
            ])
            ->assertRedirect(route('seller.books.index'));

        $this->sellerBook->refresh();
        $this->assertEquals('accepted', $this->sellerBook->buyback_status);

        Notification::assertSentTo($this->admin, BuybackResponseNotification::class, function ($notification) {
            return $notification->action === 'accept';
        });
    }

    // =====================================================
    // ÉTAPE 3 : Vendeur fait une contre-offre
    // =====================================================

    public function test_seller_can_send_counter_offer(): void
    {
        Notification::fake();

        $this->sellerBook->update(['buyback_status' => 'negotiating', 'buyback_price' => 3000]);

        $this->actingAs($this->seller)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action'        => 'counter',
                'counter_price' => 4500,
            ])
            ->assertRedirect(route('seller.books.index'));

        $this->sellerBook->refresh();
        $this->assertEquals('negotiating', $this->sellerBook->buyback_status);
        $this->assertEquals(4500, $this->sellerBook->counter_price);

        Notification::assertSentTo($this->admin, BuybackResponseNotification::class, function ($notification) {
            return $notification->action === 'counter';
        });
    }

    public function test_seller_counter_offer_requires_price(): void
    {
        $this->sellerBook->update(['buyback_status' => 'negotiating', 'buyback_price' => 3000]);

        $this->actingAs($this->seller)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action' => 'counter',
                // counter_price manquant
            ])
            ->assertSessionHasErrors('counter_price');
    }

    // =====================================================
    // ÉTAPE 4 : Vendeur refuse l'offre
    // =====================================================

    public function test_seller_can_reject_buyback_offer(): void
    {
        Notification::fake();

        $this->sellerBook->update(['buyback_status' => 'negotiating', 'buyback_price' => 3000]);

        $this->actingAs($this->seller)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action' => 'reject',
            ])
            ->assertRedirect(route('seller.books.index'));

        $this->sellerBook->refresh();
        $this->assertEquals('rejected', $this->sellerBook->buyback_status);

        Notification::assertSentTo($this->admin, BuybackResponseNotification::class, function ($notification) {
            return $notification->action === 'reject';
        });
    }

    // =====================================================
    // ÉTAPE 5 : Admin marque le vendeur comme payé
    // =====================================================

    public function test_admin_can_mark_seller_as_paid(): void
    {
        $this->sellerBook->update(['buyback_status' => 'accepted']);

        $this->actingAs($this->admin)
            ->post(route('admin.seller-books.mark-paid', $this->sellerBook))
            ->assertRedirect(route('admin.seller-books.show', $this->sellerBook));

        $this->sellerBook->refresh();
        $this->assertTrue((bool) $this->sellerBook->admin_paid_seller);
    }

    public function test_admin_cannot_mark_paid_if_not_accepted(): void
    {
        $this->sellerBook->update(['buyback_status' => 'negotiating']);

        $this->actingAs($this->admin)
            ->post(route('admin.seller-books.mark-paid', $this->sellerBook))
            ->assertForbidden();
    }

    // =====================================================
    // Contrôle d'accès
    // =====================================================

    public function test_seller_cannot_propose_buyback(): void
    {
        $this->actingAs($this->seller)
            ->post(route('admin.seller-books.buyback-propose', $this->sellerBook), [
                'buyback_price' => 3000,
            ])
            ->assertForbidden();
    }

    public function test_seller_cannot_respond_to_other_sellers_book(): void
    {
        $otherSeller = User::factory()->create(['role' => 'seller']);
        $this->sellerBook->update(['buyback_status' => 'negotiating']);

        $this->actingAs($otherSeller)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action' => 'accept',
            ])
            ->assertForbidden();
    }

    public function test_seller_cannot_respond_if_no_offer_pending(): void
    {
        // buyback_status = 'pending' (pas d'offre en cours)
        $this->actingAs($this->seller)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action' => 'accept',
            ])
            ->assertForbidden();
    }

    public function test_buyer_cannot_access_buyback_respond(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $this->sellerBook->update(['buyback_status' => 'negotiating']);

        $this->actingAs($buyer)
            ->post(route('seller.books.buyback-respond', $this->sellerBook), [
                'action' => 'accept',
            ])
            ->assertForbidden();
    }

    public function test_guest_cannot_respond_to_buyback(): void
    {
        $this->sellerBook->update(['buyback_status' => 'negotiating']);

        $this->post(route('seller.books.buyback-respond', $this->sellerBook), [
            'action' => 'accept',
        ])->assertRedirect('/login');
    }
}
