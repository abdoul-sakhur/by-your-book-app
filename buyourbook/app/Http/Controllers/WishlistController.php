<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()
            ->with(['officialBook.grade.school', 'officialBook.subject'])
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'official_book_id' => 'required|exists:official_books,id',
        ]);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('official_book_id', $request->official_book_id)
            ->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'official_book_id' => $request->official_book_id,
            ]);
            $added = true;
        }

        if ($request->wantsJson()) {
            return response()->json(['added' => $added]);
        }

        return back()->with('success', $added ? 'Livre ajouté aux favoris.' : 'Livre retiré des favoris.');
    }

    public function remove(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) {
            abort(403);
        }

        $wishlist->delete();

        return back()->with('success', 'Livre retiré des favoris.');
    }
}
