<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes ventes</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats vendeur --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Commandes reçues</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--color-primary);">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Articles vendus</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--color-accent);">{{ $totalItemsSold }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--color-secondary);">{{ number_format($totalSales, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            {{-- Filtre par statut --}}
            <div class="mb-4 bg-white rounded-lg shadow-sm p-4">
                <form method="GET" class="flex items-end gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" id="status" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Tous</option>
                            @foreach(\App\Enums\OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary !py-2 !px-4 text-sm">Filtrer</button>
                    <a href="{{ route('seller.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
                </form>
            </div>

            {{-- Table des commandes --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mes articles</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mon total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Point relais</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $order->user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $order->seller_items_count }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ number_format($order->seller_total ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $order->relayPoint?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $order->status->color() }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('seller.orders.show', $order) }}" class="text-sm font-medium" style="color: var(--color-primary);">Voir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Aucune vente pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
