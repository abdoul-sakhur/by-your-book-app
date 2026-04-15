<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Commande #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center text-sm mb-4" style="color: var(--color-primary);">
                ← Retour aux ventes
            </a>

            {{-- Informations --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Client</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $order->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                    @if($order->user->phone)
                        <p class="text-sm text-gray-500">{{ $order->user->phone }}</p>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Statut & Date</p>
                    <div class="mt-1">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $order->status->color() }}">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Point relais</p>
                    @if($order->relayPoint)
                        <p class="mt-1 font-semibold text-gray-900">{{ $order->relayPoint->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->relayPoint->address }}</p>
                        <p class="text-sm text-gray-500">{{ $order->relayPoint->city }}{{ $order->relayPoint->district ? ' — ' . $order->relayPoint->district : '' }}</p>
                    @else
                        <p class="mt-1 text-gray-400">—</p>
                    @endif
                </div>
            </div>

            @if($order->delivery_notes)
                <div class="mb-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                    <p class="text-sm font-medium text-yellow-800">Note de livraison :</p>
                    <p class="text-sm text-yellow-700 mt-1">{{ $order->delivery_notes }}</p>
                </div>
            @endif

            {{-- Mes articles dans cette commande --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Mes articles dans cette commande</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">P.U.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($sellerItems as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $item->sellerBook->officialBook->title ?? '—' }}
                                    <span class="block text-xs text-gray-400">{{ $item->sellerBook->condition->label() }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $item->sellerBook->officialBook->subject->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">Mon total :</td>
                            <td class="px-4 py-3 text-sm font-bold text-right" style="color: var(--color-primary);">{{ number_format($sellerTotal, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
