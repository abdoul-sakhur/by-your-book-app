<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookStatus;
use App\Http\Controllers\Controller;
use App\Models\SellerBook;
use App\Notifications\SellerBookStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerBookValidationController extends Controller
{
    public function index(Request $request): View
    {
        $books = SellerBook::query()
            ->with(['seller', 'officialBook.grade.school', 'officialBook.subject'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s), fn ($q) => $q->pending())
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->whereHas('seller', fn ($u) => $u->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('officialBook', fn ($ob) => $ob->where('title', 'like', "%{$s}%"));
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = SellerBook::pending()->count();

        return view('admin.seller-books.index', compact('books', 'pendingCount'));
    }

    public function show(SellerBook $sellerBook): View
    {
        $sellerBook->load(['seller', 'officialBook.grade.school', 'officialBook.subject']);

        return view('admin.seller-books.show', compact('sellerBook'));
    }

    public function approve(SellerBook $sellerBook): RedirectResponse
    {
        $sellerBook->update([
            'status' => BookStatus::Approved,
            'rejection_reason' => null,
        ]);

        $sellerBook->load('officialBook');
        $sellerBook->seller->notify(new SellerBookStatusNotification($sellerBook));

        return redirect()->route('admin.seller-books.index')
            ->with('success', "Livre de « {$sellerBook->seller->name} » approuvé.");
    }

    public function reject(Request $request, SellerBook $sellerBook): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $sellerBook->update([
            'status' => BookStatus::Rejected,
            'rejection_reason' => $validated['rejection_reason'],
            'admin_notes' => $validated['admin_notes'] ?? $sellerBook->admin_notes,
        ]);

        $sellerBook->load('officialBook');
        $sellerBook->seller->notify(new SellerBookStatusNotification($sellerBook));

        return redirect()->route('admin.seller-books.index')
            ->with('success', "Livre de « {$sellerBook->seller->name} » refusé.");
    }
}
