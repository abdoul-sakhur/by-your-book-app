<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\SellerBook;
use App\Models\Setting;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    /**
     * Page de validation de commande.
     */
    public function index(): View|RedirectResponse
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

        $deliveryFeeThreshold = (int) Setting::get('free_delivery_threshold', 500000);
        $deliveryFee = $total >= $deliveryFeeThreshold ? 0 : (int) Setting::get('delivery_fee', 3000);

        return view('checkout.index', compact('items', 'total', 'deliveryFee', 'deliveryFeeThreshold'));
    }

    /**
     * Crée la commande à partir du panier (session).
     */
    public function store(CheckoutRequest $request)
    {
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
            $total = 0;

            foreach ($cart as $id => $qty) {
                if (!$sellerBooks->has($id)) continue;
                $book = $sellerBooks[$id];
                $actualQty = min($qty, $book->quantity);
                $total += $book->price * $actualQty;
            }

            $deliveryFeeThreshold = (int) Setting::get('free_delivery_threshold', 500000);
            $deliveryFee = $total >= $deliveryFeeThreshold ? 0 : (int) Setting::get('delivery_fee', 3000);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'status'           => OrderStatus::Pending,
                'total_amount'     => 0,
                'delivery_fee'     => $deliveryFee,
                'delivery_address' => $request->delivery_address,
                'delivery_phone'   => $request->delivery_phone,
                'payment_method'   => $request->payment_method,
                'delivery_notes'   => $request->delivery_notes,
            ]);

            $orderTotal = 0;

            foreach ($cart as $id => $qty) {
                if (!$sellerBooks->has($id)) continue;

                $book = $sellerBooks[$id];
                $actualQty = min($qty, $book->quantity);

                if ($actualQty <= 0) continue;

                $order->items()->create([
                    'seller_book_id' => $book->id,
                    'quantity'       => $actualQty,
                    'unit_price'     => $book->price,
                ]);

                $book->decrement('quantity', $actualQty);
                $orderTotal += $book->price * $actualQty;
            }

            $order->update(['total_amount' => $orderTotal]);

            OrderEvent::create([
                'order_id' => $order->id,
                'status'   => $order->status,
                'comment'  => 'Commande passée par le client.',
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

        $order->load(['items.sellerBook.officialBook', 'items.sellerBook.seller:id,name']);

        return view('checkout.confirmation', compact('order'));
    }
}

