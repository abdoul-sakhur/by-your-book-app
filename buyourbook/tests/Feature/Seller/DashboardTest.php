<?php

namespace Tests\Feature\Seller;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Enums\OrderStatus;
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

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_dashboard_shows_stats(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);

        $school = School::create(['name' => 'École', 'city' => 'Abidjan', 'district' => 'Cocody']);
        $grade = Grade::create(['school_id' => $school->id, 'name' => '6ème', 'level' => '6ème', 'academic_year' => '2024-2025']);
        $subject = Subject::create(['name' => 'Maths']);
        $ob = OfficialBook::create(['grade_id' => $grade->id, 'subject_id' => $subject->id, 'title' => 'Maths 6', 'is_active' => true]);

        // 2 approved, 1 pending
        SellerBook::create(['user_id' => $seller->id, 'official_book_id' => $ob->id, 'condition' => BookCondition::Good, 'price' => 3000, 'quantity' => 2, 'status' => BookStatus::Approved]);
        SellerBook::create(['user_id' => $seller->id, 'official_book_id' => $ob->id, 'condition' => BookCondition::New, 'price' => 5000, 'quantity' => 1, 'status' => BookStatus::Approved]);
        SellerBook::create(['user_id' => $seller->id, 'official_book_id' => $ob->id, 'condition' => BookCondition::Acceptable, 'price' => 2000, 'quantity' => 1, 'status' => BookStatus::Pending]);

        $response = $this->actingAs($seller)->get('/seller');
        $response->assertOk();
        $response->assertViewHas('totalBooks', 3);
        $response->assertViewHas('approvedBooks', 2);
        $response->assertViewHas('pendingBooks', 1);
    }
}
