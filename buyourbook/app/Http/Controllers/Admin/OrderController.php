<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\SellerNewOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->when($request->search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->with(['user:id,name,email', 'relayPoint:id,name'])
            ->withCount('items')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user:id,name,email,phone',
            'relayPoint',
            'items.sellerBook.officialBook.subject',
            'items.sellerBook.seller:id,name',
            'events',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // -------------------------------------------------------------------------
    // Actions workflow
    // -------------------------------------------------------------------------

    /**
     * pending → confirmed : notifie l'acheteur et chaque vendeur concerné.
     */
    public function confirm(Order $order): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Pending, 422, 'La commande ne peut pas être confirmée.');

        $order->update(['status' => OrderStatus::Confirmed]);

        OrderEvent::create([
            'order_id' => $order->id,
            'status'   => OrderStatus::Confirmed,
            'comment'  => 'Commande confirmée par l\'administration.',
        ]);

        // Notifier l'acheteur
        $order->user->notify(new OrderStatusChangedNotification($order, OrderStatus::Confirmed->value));

        // Notifier chaque vendeur unique impliqué
        $order->load('items.sellerBook.seller');
        $sellers = $order->items
            ->map(fn ($item) => $item->sellerBook->seller)
            ->filter()
            ->unique('id');

        foreach ($sellers as $seller) {
            $seller->notify(new SellerNewOrderNotification($order));
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande confirmée. Les vendeurs ont été notifiés.');
    }

    /**
     * confirmed → preparing
     */
    public function markPreparing(Order $order): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Confirmed, 422, 'Statut incorrect.');

        $order->update(['status' => OrderStatus::Preparing]);

        OrderEvent::create([
            'order_id' => $order->id,
            'status'   => OrderStatus::Preparing,
            'comment'  => 'Commande en cours de préparation.',
        ]);

        $order->user->notify(new OrderStatusChangedNotification($order, OrderStatus::Preparing->value));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande marquée en préparation.');
    }

    /**
     * preparing → ready
     */
    public function markReady(Order $order): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Preparing, 422, 'Statut incorrect.');

        $order->update(['status' => OrderStatus::Ready]);

        OrderEvent::create([
            'order_id' => $order->id,
            'status'   => OrderStatus::Ready,
            'comment'  => 'Commande prête — en attente de livraison.',
        ]);

        $order->user->notify(new OrderStatusChangedNotification($order, OrderStatus::Ready->value));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande marquée prête pour livraison.');
    }

    /**
     * ready → delivered + enregistrement du paiement COD.
     */
    public function markDelivered(Order $order): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Ready, 422, 'Statut incorrect.');

        $order->update([
            'status'               => OrderStatus::Delivered,
            'payment_received_at'  => now(),
        ]);

        $order->items()->update(['seller_ready' => true]);

        OrderEvent::create([
            'order_id' => $order->id,
            'status'   => OrderStatus::Delivered,
            'comment'  => 'Commande livrée. Paiement reçu.',
        ]);

        $order->user->notify(new OrderStatusChangedNotification($order, OrderStatus::Delivered->value));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande livrée et paiement enregistré.');
    }

    /**
     * Annule la commande et restaure les stocks.
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_unless(
            in_array($order->status, [OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Preparing, OrderStatus::Ready]),
            422,
            'Cette commande ne peut plus être annulée.'
        );

        DB::transaction(function () use ($order) {
            // Restaurer les quantités des livres vendeurs
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
                'comment'  => 'Commande annulée par l\'administration. Stock restauré.',
            ]);
        });

        $order->user->notify(new OrderStatusChangedNotification($order, OrderStatus::Cancelled->value));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Commande annulée et stocks restaurés.');
    }

    /**
     * Marque le vendeur comme payé pour un article de la commande.
     */
    public function markSellerPaid(Order $order, OrderItem $item): RedirectResponse
    {
        abort_unless($order->status === OrderStatus::Delivered, 422, 'La commande doit être livrée pour payer le vendeur.');
        abort_unless($item->order_id === $order->id, 403);
        abort_if($item->seller_paid, 422, 'Ce vendeur a déjà été payé pour cet article.');

        $item->update([
            'seller_paid'    => true,
            'seller_paid_at' => now(),
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Vendeur marqué comme payé pour cet article.');
    }
}

