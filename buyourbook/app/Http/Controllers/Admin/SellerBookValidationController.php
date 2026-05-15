<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectSellerBookRequest;
use App\Models\SellerBook;
use App\Notifications\BuybackOfferNotification;
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
        abort_unless($sellerBook->status === BookStatus::Pending, 403, 'Ce livre n\'est pas en attente de validation.');

        $sellerBook->update([
            'status'           => BookStatus::PickupPending,
            'rejection_reason' => null,
        ]);

        $sellerBook->load('officialBook');
        $sellerBook->seller->notify(new SellerBookStatusNotification($sellerBook));

        return redirect()->route('admin.seller-books.show', $sellerBook)
            ->with('success', "Livre validé. Le vendeur a été notifié — collecte à domicile à planifier.");
    }

    public function markCollected(SellerBook $sellerBook): RedirectResponse
    {
        abort_unless($sellerBook->status === BookStatus::PickupPending, 403, 'Ce livre n\'est pas en attente de collecte.');

        $sellerBook->update([
            'status' => BookStatus::Approved,
        ]);

        $sellerBook->load('officialBook');
        $sellerBook->seller->notify(new SellerBookStatusNotification($sellerBook));

        return redirect()->route('admin.seller-books.show', $sellerBook)
            ->with('success', "Collecte effectuée. Le livre est maintenant visible dans le catalogue.");
    }

    public function reject(RejectSellerBookRequest $request, SellerBook $sellerBook): RedirectResponse
    {
        abort_unless(
            in_array($sellerBook->status, [BookStatus::Pending, BookStatus::PickupPending]),
            403,
            'Ce livre ne peut pas être refusé dans son état actuel.'
        );

        $validated = $request->validated();

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

    public function buybackPropose(Request $request, SellerBook $sellerBook): RedirectResponse
    {
        $validated = $request->validate([
            'buyback_price' => ['required', 'integer', 'min:1'],
            'buyback_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $sellerBook->update([
            'buyback_price'  => $validated['buyback_price'],
            'buyback_notes'  => $validated['buyback_notes'] ?? null,
            'buyback_status' => 'negotiating',
        ]);

        $sellerBook->load('officialBook');
        $sellerBook->seller->notify(new BuybackOfferNotification($sellerBook));

        return redirect()->route('admin.seller-books.show', $sellerBook)
            ->with('success', 'Offre de rachat envoyée au vendeur.');
    }

    public function markPaid(SellerBook $sellerBook): RedirectResponse
    {
        abort_unless($sellerBook->buyback_status === 'accepted', 403, 'Le vendeur n\'a pas encore accepté l\'offre.');

        $sellerBook->update(['admin_paid_seller' => true]);

        return redirect()->route('admin.seller-books.show', $sellerBook)
            ->with('success', 'Paiement du vendeur marqué comme effectué.');
    }

    public function buybackAcceptDirect(SellerBook $sellerBook): RedirectResponse
    {
        abort_unless($sellerBook->status === BookStatus::Approved, 403);
        abort_unless(
            in_array($sellerBook->buyback_status, ['pending', 'negotiating']),
            403,
            'Cette action n\'est pas disponible pour ce statut de rachat.'
        );

        $sellerBook->update([
            'buyback_price'  => $sellerBook->price,
            'buyback_status' => 'accepted',
        ]);

        $sellerBook->load('officialBook');
        $sellerBook->seller->notify(new BuybackOfferNotification($sellerBook));

        return redirect()->route('admin.seller-books.show', $sellerBook)
            ->with('success', 'Prix du vendeur accepté. Le vendeur a été notifié.');
    }
}
