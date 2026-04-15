<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; padding: 40px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; }
        .brand { font-size: 22px; font-weight: bold; color: #1B4D3E; }
        .brand-sub { font-size: 10px; color: #888; }
        .invoice-title { font-size: 20px; color: #1B4D3E; margin-bottom: 5px; }
        .meta-table { width: 100%; margin-bottom: 25px; }
        .meta-table td { padding: 3px 0; }
        .meta-label { font-weight: bold; color: #555; width: 140px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1B4D3E; margin-bottom: 10px; border-bottom: 2px solid #1B4D3E; padding-bottom: 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #1B4D3E; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        table.items tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .total-row { background: #f3f4f6; }
        .total-row td { font-weight: bold; font-size: 14px; padding: 10px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>

    {{-- En-tête --}}
    <div class="header">
        <div class="header-left">
            <div class="brand">📚 BuyYourBook</div>
            <div class="brand-sub">Plateforme d'achat de livres scolaires — Abidjan, Côte d'Ivoire</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div style="font-size: 11px; color: #666;">
                N° {{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}<br>
                Date : {{ $order->created_at->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- Infos client --}}
    <div style="margin-bottom: 25px;">
        <div class="section-title">Informations client</div>
        <table class="meta-table">
            <tr>
                <td class="meta-label">Nom :</td>
                <td>{{ $order->user->name }}</td>
            </tr>
            <tr>
                <td class="meta-label">Email :</td>
                <td>{{ $order->user->email }}</td>
            </tr>
            @if($order->user->phone)
            <tr>
                <td class="meta-label">Téléphone :</td>
                <td>{{ $order->user->phone }}</td>
            </tr>
            @endif
            @if($order->relayPoint)
            <tr>
                <td class="meta-label">Point de retrait :</td>
                <td>{{ $order->relayPoint->name }} — {{ $order->relayPoint->address }}, {{ $order->relayPoint->district }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Articles --}}
    <div style="margin-bottom: 10px;">
        <div class="section-title">Détail de la commande</div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 40%;">Livre</th>
                <th>Matière</th>
                <th>Vendeur</th>
                <th class="text-right">Prix unit.</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->sellerBook->officialBook->title ?? 'Livre supprimé' }}</td>
                    <td>{{ $item->sellerBook->officialBook->subject->name ?? '-' }}</td>
                    <td>{{ $item->sellerBook->seller->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} F</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', ' ') }} F</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($order->total_amount, 0, ',', ' ') }} F CFA</td>
            </tr>
        </tfoot>
    </table>

    @if($order->delivery_notes)
        <div style="background: #fef9c3; border: 1px solid #fde68a; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 11px;">
            <strong>Notes :</strong> {{ $order->delivery_notes }}
        </div>
    @endif

    {{-- Statut --}}
    <div style="margin-bottom: 20px;">
        <strong>Statut :</strong> {{ $order->status->label() }}
    </div>

    {{-- Footer --}}
    <div class="footer">
        BuyYourBook — Plateforme d'achat et vente de livres scolaires<br>
        Abidjan, Côte d'Ivoire | contact@buyyourbook.ci<br>
        Ce document fait office de facture. Merci pour votre confiance.
    </div>

</body>
</html>
