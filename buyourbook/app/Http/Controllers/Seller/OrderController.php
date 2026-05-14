<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Liste des commandes contenant des livres du vendeur connecté.
     */
    public function index(Request $request): View
    {
        $sellerId = auth()->id();

        $orders = Order::query()
            ->whereHas('items.sellerBook', fn ($q) => $q->where('user_id', $sellerId))
            ->with(['user', 'relayPoint'])
            ->withCount(['items as seller_items_count' => fn ($q) => $q->whereHas('sellerBook', fn ($sb) => $sb->where('user_id', $sellerId))])
            ->withSum(['items as seller_total' => fn ($q) => $q->whereHas('sellerBook', fn ($sb) => $sb->where('user_id', $sellerId))], 'unit_price')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Statistiques vendeur
        $sellerItemsQuery = OrderItem::whereHas('sellerBook', fn ($q) => $q->where('user_id', $sellerId));
        $totalSales = (clone $sellerItemsQuery)->sum(\DB::raw('quantity * unit_price'));
        $totalItemsSold = (clone $sellerItemsQuery)->sum('quantity');
        $totalOrders = Order::whereHas('items.sellerBook', fn ($q) => $q->where('user_id', $sellerId))->count();

        return view('seller.orders.index', compact('orders', 'totalSales', 'totalItemsSold', 'totalOrders'));
    }

    /**
     * Détail d'une commande (seulement les articles du vendeur).
     */
    public function show(Order $order): View
    {
        $sellerId = auth()->id();

        abort_unless(
            $order->items()->whereHas('sellerBook', fn ($q) => $q->where('user_id', $sellerId))->exists(),
            403
        );

        $order->load(['user', 'relayPoint', 'events']);

        $sellerItems = $order->items()
            ->whereHas('sellerBook', fn ($q) => $q->where('user_id', $sellerId))
            ->with('sellerBook.officialBook.subject')
            ->get();

        $sellerTotal = $sellerItems->sum(fn ($item) => $item->quantity * $item->unit_price);

        return view('seller.orders.show', compact('order', 'sellerItems', 'sellerTotal'));
    }

    /**
     * Le vendeur marque un de ses articles comme préparé / prêt pour envoi.
     */
    public function markItemReady(Order $order, OrderItem $item): RedirectResponse
    {
        $sellerId = auth()->id();

        // Vérifier que l'item appartient bien à ce vendeur
        abort_unless($item->order_id === $order->id, 403);
        abort_unless($item->sellerBook->user_id === $sellerId, 403);
        abort_if($item->seller_ready, 422, 'Cet article est déjà marqué comme prêt.');

        $item->update([
            'seller_ready'    => true,
            'seller_ready_at' => now(),
        ]);

        return redirect()->route('seller.orders.show', $order)
            ->with('success', 'Article marqué comme prêt. Merci !');
    }
}

