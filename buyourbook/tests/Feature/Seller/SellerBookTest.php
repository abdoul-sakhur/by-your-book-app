<?php

namespace Tests\Feature\Seller;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerBookTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private OfficialBook $officialBook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['role' => 'seller']);

        $school = School::create(['name' => 'École Test', 'city' => 'Abidjan', 'district' => 'Cocody']);
        $grade = Grade::create(['school_id' => $school->id, 'name' => '6ème A', 'level' => '6ème', 'academic_year' => '2024-2025']);
        $subject = Subject::create(['name' => 'Maths']);
        $this->officialBook = OfficialBook::create([
            'grade_id' => $grade->id,
            'subject_id' => $subject->id,
            'title' => 'Manuel Maths 6ème',
            'is_active' => true,
        ]);
    }

    public function test_seller_can_list_books(): void
    {
        SellerBook::create([
            'user_id' => $this->seller->id,
            'official_book_id' => $this->officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 3000,
            'quantity' => 1,
            'status' => BookStatus::Pending,
        ]);

        $this->actingAs($this->seller)
            ->get('/seller/books')
            ->assertOk()
            ->assertSee('Manuel Maths 6ème');
    }

    public function test_seller_can_create_book(): void
    {
        $this->actingAs($this->seller)
            ->get('/seller/books/create')
            ->assertOk();
    }

    public function test_seller_can_store_book(): void
    {
        $this->actingAs($this->seller)
            ->post('/seller/books', [
                'official_book_id' => $this->officialBook->id,
                'condition' => 'good',
                'price' => 4500,
                'quantity' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('seller_books', [
            'user_id' => $this->seller->id,
            'official_book_id' => $this->officialBook->id,
            'price' => 4500,
            'quantity' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_seller_can_update_book(): void
    {
        $book = SellerBook::create([
            'user_id' => $this->seller->id,
            'official_book_id' => $this->officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 3000,
            'quantity' => 1,
            'status' => BookStatus::Pending,
        ]);

        $this->actingAs($this->seller)
            ->put("/seller/books/{$book->id}", [
                'official_book_id' => $this->officialBook->id,
                'condition' => 'acceptable',
                'price' => 2500,
                'quantity' => 1,
            ])
            ->assertRedirect();

        $book->refresh();
        $this->assertEquals(2500, $book->price);
        $this->assertEquals(BookCondition::Acceptable, $book->condition);
    }

    public function test_seller_can_delete_book(): void
    {
        $book = SellerBook::create([
            'user_id' => $this->seller->id,
            'official_book_id' => $this->officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 3000,
            'quantity' => 1,
            'status' => BookStatus::Pending,
        ]);

        $this->actingAs($this->seller)
            ->delete("/seller/books/{$book->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('seller_books', ['id' => $book->id]);
    }

    public function test_seller_cannot_see_other_sellers_books(): void
    {
        $otherSeller = User::factory()->create(['role' => 'seller']);

        SellerBook::create([
            'user_id' => $otherSeller->id,
            'official_book_id' => $this->officialBook->id,
            'condition' => BookCondition::Good,
            'price' => 3000,
            'quantity' => 1,
            'status' => BookStatus::Approved,
        ]);

        $this->actingAs($this->seller)
            ->get('/seller/books')
            ->assertOk()
            ->assertDontSee('3 000'); // price of other seller's book
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->seller)
            ->post('/seller/books', [])
            ->assertSessionHasErrors(['official_book_id', 'condition', 'price', 'quantity']);
    }
}
