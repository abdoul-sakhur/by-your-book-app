<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Models\SellerBook;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Affiche le panier.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $items = [];
        $total = 0;

        if (!empty($cart)) {
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
                } else {
                    // Livre retiré ou plus disponible — nettoyer
                    unset($cart[$id]);
                }
            }

            session()->put('cart', $cart);
        }

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Ajoute un livre au panier (session).
     */
    public function add(CartAddRequest $request)
    {
        $book = SellerBook::where('id', $request->seller_book_id)
            ->approved()
            ->where('quantity', '>', 0)
            ->firstOrFail();

        $cart = session()->get('cart', []);
        $id = $book->id;
        $currentQty = $cart[$id] ?? 0;
        $newQty = $currentQty + $request->quantity;

        // Ne pas dépasser le stock disponible
        $cart[$id] = min($newQty, $book->quantity);
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Livre ajouté au panier !');
    }

    /**
     * Met à jour la quantité d'un article.
     */
    public function update(CartAddRequest $request)
    {

        $cart = session()->get('cart', []);
        $id = $request->seller_book_id;

        if (isset($cart[$id])) {
            $book = SellerBook::find($id);
            $cart[$id] = min($request->quantity, $book?->quantity ?? 1);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Quantité mise à jour.');
    }

    /**
     * Supprime un article du panier.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'seller_book_id' => 'required',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$request->seller_book_id]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Article retiré du panier.');
    }

    /**
     * Retourne le nombre d'articles dans le panier (API pour Alpine.js).
     */
    public function count()
    {
        $cart = session()->get('cart', []);
        return response()->json(['count' => array_sum($cart)]);
    }
}
