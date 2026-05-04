<?php

namespace Tests\Feature;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private SellerBook $sellerBook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create(['role' => 'buyer']);

        $school = School::create(['name' => 'École Test', 'city' => 'Abidjan', 'district' => 'Cocody']);
        $grade = Grade::create(['school_id' => $school->id, 'name' => '6ème A', 'level' => '6ème', 'academic_year' => '2024-2025']);
        $subject = Subject::create(['name' => 'Français']);
        $officialBook = OfficialBook::create([
            'grade_id' => $grade->id,
            'subject_id' => $subject->id,
            'title' => 'Livre Test',
            'is_active' => true,
        ]);

        $seller = User::factory()->create(['role' => 'seller']);
        $this->sellerBook = SellerBook::create([
            'user_id' => $seller->id,
            'official_book_id' => $officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 5000,
            'quantity' => 3,
            'status' => BookStatus::Approved,
        ]);

    }

    public function test_add_to_cart(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('cart.add'), [
                'seller_book_id' => $this->sellerBook->id,
                'quantity' => 1,
            ])
            ->assertRedirect();

        $cart = session('cart');
        $this->assertArrayHasKey($this->sellerBook->id, $cart);
        $this->assertEquals(1, $cart[$this->sellerBook->id]);
    }

    public function test_add_to_cart_respects_stock_limit(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 2]])
            ->post(route('cart.add'), [
                'seller_book_id' => $this->sellerBook->id,
                'quantity' => 5,
            ]);

        $cart = session('cart');
        // Max 3 (stock available)
        $this->assertLessThanOrEqual(3, $cart[$this->sellerBook->id]);
    }

    public function test_update_cart_quantity(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 1]])
            ->post(route('cart.update'), [
                'seller_book_id' => $this->sellerBook->id,
                'quantity' => 2,
            ])
            ->assertRedirect(route('cart.index'));

        $this->assertEquals(2, session('cart')[$this->sellerBook->id]);
    }

    public function test_remove_from_cart(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 1]])
            ->post(route('cart.remove'), [
                'seller_book_id' => $this->sellerBook->id,
            ])
            ->assertRedirect(route('cart.index'));

        $this->assertArrayNotHasKey($this->sellerBook->id, session('cart'));
    }

    public function test_checkout_page_requires_cart(): void
    {
        $this->actingAs($this->buyer)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_checkout_page_shows_with_cart(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 1]])
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Livre Test');
    }

    public function test_checkout_creates_order(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 2]])
            ->post(route('checkout.store'), [
                'delivery_address' => '15 Rue des Fleurs, Cocody',
                'delivery_phone'   => '0707070707',
                'payment_method'   => 'cash',
                'delivery_notes'   => 'Merci',
            ]);

        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(10000, $order->total_amount); // 5000 × 2
        $this->assertEquals('15 Rue des Fleurs, Cocody', $order->delivery_address);

        // Stock decremented
        $this->sellerBook->refresh();
        $this->assertEquals(1, $this->sellerBook->quantity); // was 3, bought 2

        // Order items created
        $this->assertEquals(1, $order->items()->count());
        $item = $order->items->first();
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(5000, $item->unit_price);

        // Order event created
        $this->assertEquals(1, $order->events()->count());

        // Cart emptied
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_with_empty_cart_redirects(): void
    {
        $this->actingAs($this->buyer)
            ->post(route('checkout.store'), [
                'delivery_address' => '15 Rue des Fleurs, Cocody',
                'delivery_phone'   => '0707070707',
                'payment_method'   => 'cash',
            ])
            ->assertRedirect(route('cart.index'));
    }

    public function test_checkout_validates_required_delivery_fields(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 1]])
            ->post(route('checkout.store'), [])
            ->assertSessionHasErrors(['delivery_address', 'delivery_phone', 'payment_method']);
    }

    public function test_cart_count_api(): void
    {
        $this->actingAs($this->buyer)
            ->withSession(['cart' => [$this->sellerBook->id => 2]])
            ->getJson(route('cart.count'))
            ->assertOk()
            ->assertJson(['count' => 2]);
    }

    public function test_cannot_add_unapproved_book_to_cart(): void
    {
        $this->sellerBook->update(['status' => BookStatus::Pending]);

        $this->actingAs($this->buyer)
            ->post(route('cart.add'), [
                'seller_book_id' => $this->sellerBook->id,
                'quantity' => 1,
            ])
            ->assertStatus(404);
    }

    public function test_cannot_add_out_of_stock_book(): void
    {
        $this->sellerBook->update(['quantity' => 0]);

        $this->actingAs($this->buyer)
            ->post(route('cart.add'), [
                'seller_book_id' => $this->sellerBook->id,
                'quantity' => 1,
            ])
            ->assertStatus(404);
    }
}
