<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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

    /**
     * L'acheteur annule sa commande (uniquement si encore en attente).
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->canBeCancelledByBuyer(), 422, 'Cette commande ne peut plus être annulée.');

        DB::transaction(function () use ($order) {
            $order->load('items.sellerBook');
            foreach ($order->items as $item) {
                if ($item->sellerBook) {
                    $item->sellerBook->increment('quantity', $item->quantity);
                }
            }

            $order->update(['status' => OrderStatus::Cancelled]);

            OrderEvent::create([
                'order_id' => $order->id,
                'status'   => OrderStatus::Cancelled,
                'comment'  => 'Commande annulée par le client.',
            ]);
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Votre commande a été annulée.');
    }
}

