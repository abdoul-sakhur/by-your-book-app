<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Liste des commandes de l'acheteur.
     */
    public function index(): View
    {
        $orders = Order::forUser(auth()->id())
            ->with(['items.sellerBook.officialBook', 'relayPoint'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Détail d'une commande.
     */
    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.sellerBook.officialBook.subject', 'items.sellerBook.seller:id,name', 'relayPoint', 'events']);

        return view('orders.show', compact('order'));
    }
}
