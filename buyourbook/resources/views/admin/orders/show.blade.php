<x-admin-layout>
    <x-slot name="header">Commande #{{ $order->id }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour aux commandes</a>
    </div>

    {{-- Infos client + statut --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Client</p>
            <p class="font-semibold text-gray-900 mt-1">{{ $order->user->name }}</p>
            <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
            @if($order->user->phone)
                <p class="text-sm text-gray-500">📞 {{ $order->user->phone }}</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total / Date</p>
            <p class="text-xl font-bold mt-1" style="color: var(--color-primary);">{{ number_format($order->total_amount, 0, ',', ' ') }} F CFA</p>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Point relais</p>
            @if($order->relayPoint)
                <p class="font-semibold text-gray-900 mt-1">{{ $order->relayPoint->name }}</p>
                <p class="text-xs text-gray-400">{{ $order->relayPoint->address }}, {{ $order->relayPoint->district }}</p>
                @if($order->relayPoint->contact_phone)
                    <p class="text-xs text-gray-400">📞 {{ $order->relayPoint->contact_phone }}</p>
                @endif
            @else
                <p class="text-gray-400 mt-1 text-sm">Non spécifié</p>
            @endif
        </div>
    </div>

    @if($order->delivery_notes)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-sm text-yellow-800">
            <strong>Notes du client :</strong> {{ $order->delivery_notes }}
        </div>
    @endif

    {{-- Changer statut --}}
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">Changer le statut</h2>
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex items-center gap-3">
            @csrf
            @method('PATCH')
            <select name="status" class="rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm px-4 py-2">Mettre à jour</button>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                Actuel : {{ $order->status->label() }}
            </span>
        </form>
    </div>

    {{-- Articles --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Articles ({{ $order->items->count() }})</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendeur</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">P.U.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sous-total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm">
                            <p class="font-medium text-gray-900">{{ $item->sellerBook->officialBook->title ?? 'Supprimé' }}</p>
                            <p class="text-xs text-gray-400">{{ $item->sellerBook->officialBook->subject->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->sellerBook->seller->name ?? '?' }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-right text-gray-500">{{ number_format($item->unit_price, 0, ',', ' ') }} F</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold" style="color: var(--color-primary);">{{ number_format($item->subtotal, 0, ',', ' ') }} F</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-3 text-right font-semibold text-gray-700">Total</td>
                    <td class="px-6 py-3 text-right text-lg font-bold" style="color: var(--color-primary);">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-admin-layout>
