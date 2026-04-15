<?php

namespace App\Http\Controllers\Seller;

use App\Enums\BookStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerBook;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $userId = auth()->id();

        $totalSales = OrderItem::whereHas('sellerBook', fn ($q) => $q->where('user_id', $userId))
            ->sum(DB::raw('quantity * unit_price'));

        $totalItemsSold = OrderItem::whereHas('sellerBook', fn ($q) => $q->where('user_id', $userId))
            ->sum('quantity');

        $totalOrders = Order::whereHas('items.sellerBook', fn ($q) => $q->where('user_id', $userId))
            ->count();

        return view('seller.dashboard', [
            'totalBooks' => SellerBook::forSeller($userId)->count(),
            'pendingBooks' => SellerBook::forSeller($userId)->pending()->count(),
            'approvedBooks' => SellerBook::forSeller($userId)->approved()->count(),
            'rejectedBooks' => SellerBook::forSeller($userId)->where('status', BookStatus::Rejected)->count(),
            'totalSales' => $totalSales,
            'totalItemsSold' => $totalItemsSold,
            'totalOrders' => $totalOrders,
        ]);
    }
}
