<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\RelayPoint;
use App\Models\OrderEvent;
use App\Models\SellerBook;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Page de validation de commande.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $items = [];
        $total = 0;

        $sellerBooks = SellerBook::whereIn('id', array_keys($cart))
            ->approved()
            ->where('quantity', '>', 0)
            ->with(['officialBook.subject', 'officialBook.grade.school', 'seller:id,name'])
            ->get()
            ->keyBy('id');

        foreach ($cart as $id => $qty) {
            if ($sellerBooks->has($id)) {
                $book = $sellerBooks[$id];
                $subtotal = $book->price * $qty;
                $items[] = [
                    'seller_book' => $book,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        $relayPoints = RelayPoint::active()->orderBy('city')->orderBy('name')->get();
        $cities = $relayPoints->pluck('city')->unique()->sort()->values();

        return view('checkout.index', compact('items', 'total', 'relayPoints', 'cities'));
    }

    /**
     * Crée la commande à partir du panier (session).
     */
    public function store(Request $request)
    {
        $request->validate([
            'relay_point_id' => 'nullable|exists:relay_points,id',
            'delivery_notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $sellerBooks = SellerBook::whereIn('id', array_keys($cart))
            ->approved()
            ->where('quantity', '>', 0)
            ->get()
            ->keyBy('id');

        if ($sellerBooks->isEmpty()) {
            session()->forget('cart');
            return redirect()->route('cart.index')->with('error', 'Les articles ne sont plus disponibles.');
        }

        $order = null;

        DB::transaction(function () use ($request, $cart, $sellerBooks, &$order) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'relay_point_id' => $request->relay_point_id,
                'status' => OrderStatus::Pending,
                'total_amount' => 0,
                'delivery_notes' => $request->delivery_notes,
            ]);

            $total = 0;

            foreach ($cart as $id => $qty) {
                if (!$sellerBooks->has($id)) {
                    continue;
                }

                $book = $sellerBooks[$id];
                $actualQty = min($qty, $book->quantity);

                if ($actualQty <= 0) {
                    continue;
                }

                $order->items()->create([
                    'seller_book_id' => $book->id,
                    'quantity' => $actualQty,
                    'unit_price' => $book->price,
                ]);

                // Décrémenter le stock
                $book->decrement('quantity', $actualQty);

                $total += $book->price * $actualQty;
            }

            $order->update(['total_amount' => $total]);

            OrderEvent::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'comment' => 'Commande passée par le client.',
            ]);
        });

        session()->forget('cart');

        auth()->user()->notify(new OrderConfirmationNotification($order));

        return redirect()->route('checkout.confirmation', $order);
    }

    /**
     * Page de confirmation après commande.
     */
    public function confirmation(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.sellerBook.officialBook', 'items.sellerBook.seller:id,name', 'relayPoint']);

        return view('checkout.confirmation', compact('order'));
    }
}
