<?php

namespace Tests\Feature\Admin;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // --- Schools ---

    public function test_admin_can_list_schools(): void
    {
        School::create(['name' => 'GSFA', 'city' => 'Abidjan', 'district' => 'Cocody']);

        $this->actingAs($this->admin)
            ->get('/admin/schools')
            ->assertOk()
            ->assertSee('GSFA');
    }

    public function test_admin_can_store_school(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/schools', [
                'name' => 'Nouveau Lycée',
                'city' => 'Bouaké',
                'district' => 'Centre',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('schools', ['name' => 'Nouveau Lycée', 'city' => 'Bouaké']);
    }

    public function test_admin_can_update_school(): void
    {
        $school = School::create(['name' => 'Old', 'city' => 'Abidjan', 'district' => 'Cocody']);

        $this->actingAs($this->admin)
            ->put("/admin/schools/{$school->id}", [
                'name' => 'Updated',
                'city' => 'Abidjan',
                'district' => 'Plateau',
            ])
            ->assertRedirect();

        $school->refresh();
        $this->assertEquals('Updated', $school->name);
        $this->assertEquals('Plateau', $school->district);
    }

    public function test_admin_can_delete_school(): void
    {
        $school = School::create(['name' => 'ToDelete', 'city' => 'Abidjan', 'district' => 'Cocody']);

        $this->actingAs($this->admin)
            ->delete("/admin/schools/{$school->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
    }

    // --- Grades ---

    public function test_admin_can_store_grade(): void
    {
        $school = School::create(['name' => 'École', 'city' => 'Abidjan', 'district' => 'Cocody']);

        $this->actingAs($this->admin)
            ->post('/admin/grades', [
                'school_id' => $school->id,
                'name' => '6ème A',
                'level' => '6ème',
                'academic_year' => '2024-2025',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('grades', ['name' => '6ème A', 'school_id' => $school->id]);
    }

    // --- Subjects ---

    public function test_admin_can_store_subject(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/subjects', ['name' => 'Physique'])
            ->assertRedirect();

        $this->assertDatabaseHas('subjects', ['name' => 'Physique']);
    }

    public function test_subject_name_must_be_unique(): void
    {
        Subject::create(['name' => 'Maths']);

        $this->actingAs($this->admin)
            ->post('/admin/subjects', ['name' => 'Maths'])
            ->assertSessionHasErrors('name');
    }

    // --- Relay Points ---

    public function test_admin_can_crud_relay_point(): void
    {
        // Store
        $this->actingAs($this->admin)
            ->post('/admin/relay-points', [
                'name' => 'Point Test',
                'address' => '123 rue',
                'city' => 'Abidjan',
                'contact_phone' => '0700000000',
            ])
            ->assertRedirect();

        $rp = RelayPoint::first();
        $this->assertNotNull($rp);

        // Update
        $this->actingAs($this->admin)
            ->put("/admin/relay-points/{$rp->id}", [
                'name' => 'Point Modifié',
                'address' => '456 avenue',
                'city' => 'Abidjan',
                'contact_phone' => '0700000000',
            ])
            ->assertRedirect();

        $rp->refresh();
        $this->assertEquals('Point Modifié', $rp->name);

        // Delete
        $this->actingAs($this->admin)
            ->delete("/admin/relay-points/{$rp->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('relay_points', ['id' => $rp->id]);
    }

    // --- Seller Book Validation ---

    public function test_admin_can_approve_seller_book(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $school = School::create(['name' => 'École', 'city' => 'Abidjan', 'district' => 'Cocody']);
        $grade = Grade::create(['school_id' => $school->id, 'name' => '6ème', 'level' => '6ème', 'academic_year' => '2024-2025']);
        $subject = Subject::create(['name' => 'SVT']);
        $ob = OfficialBook::create(['grade_id' => $grade->id, 'subject_id' => $subject->id, 'title' => 'SVT 6ème', 'is_active' => true]);

        $book = SellerBook::create([
            'user_id' => $seller->id,
            'official_book_id' => $ob->id,
            'condition' => BookCondition::Good,
            'price' => 3000,
            'quantity' => 1,
            'status' => BookStatus::Pending,
        ]);

        $this->actingAs($this->admin)
            ->post("/admin/seller-books/{$book->id}/approve")
            ->assertRedirect();

        $book->refresh();
        $this->assertEquals(BookStatus::Approved, $book->status);
    }

    public function test_admin_can_reject_seller_book(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $school = School::create(['name' => 'École', 'city' => 'Abidjan', 'district' => 'Cocody']);
        $grade = Grade::create(['school_id' => $school->id, 'name' => '6ème', 'level' => '6ème', 'academic_year' => '2024-2025']);
        $subject = Subject::create(['name' => 'Anglais']);
        $ob = OfficialBook::create(['grade_id' => $grade->id, 'subject_id' => $subject->id, 'title' => 'English', 'is_active' => true]);

        $book = SellerBook::create([
            'user_id' => $seller->id,
            'official_book_id' => $ob->id,
            'condition' => BookCondition::Good,
            'price' => 3000,
            'quantity' => 1,
            'status' => BookStatus::Pending,
        ]);

        $this->actingAs($this->admin)
            ->post("/admin/seller-books/{$book->id}/reject", [
                'rejection_reason' => 'Photo floue',
            ])
            ->assertRedirect();

        $book->refresh();
        $this->assertEquals(BookStatus::Rejected, $book->status);
        $this->assertEquals('Photo floue', $book->rejection_reason);
    }

    // --- Order Status ---

    public function test_admin_can_update_order_status(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $rp = RelayPoint::create(['name' => 'RP', 'address' => 'addr', 'city' => 'Abidjan', 'contact_phone' => '07000']);

        $order = Order::create([
            'user_id' => $buyer->id,
            'relay_point_id' => $rp->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 5000,
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $order->refresh();
        $this->assertEquals(OrderStatus::Confirmed, $order->status);

        // Order event logged
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    // --- User Role ---

    public function test_admin_can_update_user_role(): void
    {
        $user = User::factory()->create(['role' => 'buyer']);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/role", [
                'role' => 'seller',
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertEquals(UserRole::Seller, $user->role);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->admin->id}/role", [
                'role' => 'buyer',
            ])
            ->assertRedirect();

        $this->admin->refresh();
        $this->assertEquals(UserRole::Admin, $this->admin->role);
    }

    // --- User Toggle Active ---

    public function test_admin_can_toggle_user_active(): void
    {
        $user = User::factory()->create(['role' => 'buyer', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->patch("/admin/users/{$user->id}/toggle-active")
            ->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/users/{$this->admin->id}/toggle-active")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);
    }
}
