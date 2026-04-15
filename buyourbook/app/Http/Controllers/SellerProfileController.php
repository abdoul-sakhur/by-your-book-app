<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\View\View;

class SellerProfileController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->role === UserRole::Seller && $user->is_active, 404);

        $books = $user->sellerBooks()
            ->approved()
            ->available()
            ->with(['officialBook.subject', 'officialBook.grade.school'])
            ->latest()
            ->paginate(12);

        $totalBooks = $user->sellerBooks()->approved()->count();
        $totalSold = \App\Models\OrderItem::whereHas('sellerBook', fn ($q) => $q->where('user_id', $user->id))->sum('quantity');

        return view('seller.profile', compact('user', 'books', 'totalBooks', 'totalSold'));
    }
}
