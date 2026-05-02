<?php

namespace App\Http\Controllers\Seller;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\SellerBookRequest;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\School;
use App\Models\SellerBook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerBookController extends Controller
{
    public function index(Request $request): View
    {
        $books = SellerBook::query()
            ->forSeller(auth()->id())
            ->with(['officialBook.grade.school', 'officialBook.subject'])
            ->when($request->search, fn ($q, $s) => $q->whereHas('officialBook', fn ($ob) => $ob->where('title', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('seller.books.index', compact('books'));
    }

    public function create(Request $request): View
    {
        $schools = School::active()->orderBy('name')->get();
        $conditions = BookCondition::cases();

        return view('seller.books.create', compact('schools', 'conditions'));
    }

    public function store(SellerBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('seller-books', 'public');
            }
        }

        SellerBook::create([
            'user_id' => auth()->id(),
            'official_book_id' => $validated['official_book_id'],
            'condition' => $validated['condition'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'images' => $images ?: null,
            'status' => BookStatus::Pending->value,
        ]);

        return redirect()->route('seller.books.index')
            ->with('success', 'Livre soumis avec succès ! Il sera visible après validation par l\'administrateur.');
    }

    public function edit(SellerBook $book): View
    {
        // Ensure seller owns this book
        abort_unless($book->user_id === auth()->id(), 403);

        // Can only edit pending or rejected books
        abort_unless(in_array($book->status, [BookStatus::Pending, BookStatus::Rejected]), 403);

        $book->load('officialBook.grade.school', 'officialBook.subject');
        $schools = School::active()->orderBy('name')->get();
        $conditions = BookCondition::cases();

        return view('seller.books.edit', compact('book', 'schools', 'conditions'));
    }

    public function update(SellerBookRequest $request, SellerBook $book): RedirectResponse
    {
        abort_unless($book->user_id === auth()->id(), 403);
        abort_unless(in_array($book->status, [BookStatus::Pending, BookStatus::Rejected]), 403);

        $validated = $request->validated();

        $images = $book->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('seller-books', 'public');
            }
        }

        $book->update([
            'official_book_id' => $validated['official_book_id'],
            'condition' => $validated['condition'],
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'images' => $images ?: null,
            'status' => BookStatus::Pending->value, // Re-submit for review
            'rejection_reason' => null,
        ]);

        return redirect()->route('seller.books.index')
            ->with('success', 'Livre modifié et re-soumis pour validation.');
    }

    public function destroy(SellerBook $book): RedirectResponse
    {
        abort_unless($book->user_id === auth()->id(), 403);

        // Cannot delete a book that has orders
        if ($book->orderItems()->exists()) {
            return redirect()->route('seller.books.index')
                ->with('error', 'Impossible de supprimer un livre ayant des commandes.');
        }

        $book->delete();

        return redirect()->route('seller.books.index')
            ->with('success', 'Livre supprimé avec succès.');
    }

    public function buybackRespond(Request $request, SellerBook $book): RedirectResponse
    {
        abort_unless($book->user_id === auth()->id(), 403);
        abort_unless($book->buyback_status === 'negotiating', 403, 'Aucune offre de rachat en attente.');

        $validated = $request->validate([
            'action'        => ['required', 'in:accept,reject,counter'],
            'counter_price' => ['required_if:action,counter', 'nullable', 'integer', 'min:1'],
        ]);

        match ($validated['action']) {
            'accept'  => $book->update(['buyback_status' => 'accepted']),
            'reject'  => $book->update(['buyback_status' => 'rejected']),
            'counter' => $book->update([
                'counter_price'  => $validated['counter_price'],
                'buyback_status' => 'negotiating',
            ]),
        };

        $messages = [
            'accept'  => 'Vous avez accepté l\'offre de rachat. L\'administrateur va vous contacter.',
            'reject'  => 'Vous avez refusé l\'offre de rachat.',
            'counter' => 'Votre contre-offre a été envoyée à l\'administrateur.',
        ];

        return redirect()->route('seller.books.index')
            ->with('success', $messages[$validated['action']]);
    }
}
