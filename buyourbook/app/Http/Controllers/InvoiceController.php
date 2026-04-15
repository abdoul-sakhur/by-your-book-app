<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load(['user', 'relayPoint', 'items.sellerBook.officialBook.subject', 'items.sellerBook.seller']);

        $pdf = Pdf::loadView('invoices.order', compact('order'))
            ->setPaper('a4');

        return $pdf->download('facture-' . $order->id . '.pdf');
    }
}
