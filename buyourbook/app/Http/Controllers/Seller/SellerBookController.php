<?php

namespace App\Http\Controllers\Seller;

use App\Enums\BookCondition;
use App\Enums\BookStatus;
use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'official_book_id' => ['required', 'exists:official_books,id'],
            'condition' => ['required', 'string', 'in:new,good,acceptable'],
            'price' => ['required', 'integer', 'min:500', 'max:100000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

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

    public function update(Request $request, SellerBook $book): RedirectResponse
    {
        abort_unless($book->user_id === auth()->id(), 403);
        abort_unless(in_array($book->status, [BookStatus::Pending, BookStatus::Rejected]), 403);

        $validated = $request->validate([
            'official_book_id' => ['required', 'exists:official_books,id'],
            'condition' => ['required', 'string', 'in:new,good,acceptable'],
            'price' => ['required', 'integer', 'min:500', 'max:100000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

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
}
