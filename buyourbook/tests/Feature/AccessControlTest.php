<?php

namespace Tests\Feature;

use App\Enums\BookStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RelayPoint;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true]);
    }

    public function test_guest_cannot_access_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_guest_cannot_access_seller_panel(): void
    {
        $this->get('/seller')->assertRedirect('/login');
    }

    public function test_buyer_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->createUser('buyer'))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_buyer_cannot_access_seller_panel(): void
    {
        $this->actingAs($this->createUser('buyer'))
            ->get('/seller')
            ->assertForbidden();
    }

    public function test_seller_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->createUser('seller'))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_seller_can_access_seller_panel(): void
    {
        $this->actingAs($this->createUser('seller'))
            ->get('/seller')
            ->assertOk();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $this->actingAs($this->createUser('admin'))
            ->get('/admin')
            ->assertOk();
    }

    public function test_inactive_user_role_helpers(): void
    {
        $admin = $this->createUser('admin');
        $seller = $this->createUser('seller');
        $buyer = $this->createUser('buyer');

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isSeller());
        $this->assertTrue($seller->isSeller());
        $this->assertFalse($seller->isAdmin());
        $this->assertTrue($buyer->isBuyer());
        $this->assertFalse($buyer->isAdmin());
    }
}
