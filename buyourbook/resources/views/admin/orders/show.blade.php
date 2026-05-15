<x-admin-layout>
    <x-slot name="header">Commande #{{ $order->id }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour aux commandes</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Infos client + statut --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Client</p>
            <p class="font-semibold text-gray-900 mt-1">{{ $order->user->name }}</p>
            <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
            @if($order->user->phone)
                <p class="text-sm text-gray-500 flex items-center gap-1"><x-icon name="mobile" class="w-4 h-4" /> {{ $order->user->phone }}</p>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total / Date</p>
            <p class="text-xl font-bold mt-1" style="color: var(--color-primary);">{{ number_format($order->total_amount + $order->delivery_fee, 0, ',', ' ') }} F CFA</p>
            <p class="text-xs text-gray-400">dont {{ number_format($order->delivery_fee, 0, ',', ' ') }} F livraison</p>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Livraison</p>
            @if($order->relayPoint)
                <p class="font-semibold text-gray-900 mt-1">{{ $order->relayPoint->name }}</p>
                <p class="text-xs text-gray-400">{{ $order->relayPoint->address }}, {{ $order->relayPoint->district }}</p>
            @elseif($order->delivery_address)
                <p class="font-semibold text-gray-900 mt-1">{{ $order->delivery_address }}</p>
                @if($order->delivery_phone)<p class="text-xs text-gray-400">Tél. {{ $order->delivery_phone }}</p>@endif
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

    {{-- Paiement COD --}}
    @if($order->payment_received_at)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-sm text-green-800 flex items-center gap-2">
            <x-icon name="check-circled" class="w-5 h-5 text-green-600" />
            Paiement à la livraison reçu le {{ $order->payment_received_at->format('d/m/Y à H:i') }}
        </div>
    @endif

    {{-- ====================== WORKFLOW ACTIONS ====================== --}}
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-icon name="activity-log" class="w-4 h-4" />
            Statut actuel :
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                {{ $order->status->label() }}
            </span>
        </h2>

        <div class="flex flex-wrap gap-3">
            @if($order->status === \App\Enums\OrderStatus::Pending)
                <form action="{{ route('admin.orders.confirm', $order) }}" method="POST">
                    @csrf
                    <button style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.25rem;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(37,99,235,.4);transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Confirmer la commande
                    </button>
                </form>
            @endif

            @if($order->status === \App\Enums\OrderStatus::Confirmed)
                <form action="{{ route('admin.orders.mark-preparing', $order) }}" method="POST">
                    @csrf
                    <button style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.25rem;background:linear-gradient(135deg,#4f46e5,#3730a3);color:#fff;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(79,70,229,.4);transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Marquer en préparation
                    </button>
                </form>
            @endif

            @if($order->status === \App\Enums\OrderStatus::Preparing)
                <form action="{{ route('admin.orders.mark-ready', $order) }}" method="POST">
                    @csrf
                    <button class="inline-flex items-center gap-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        <x-icon name="package" class="w-4 h-4" /> Marquer prête pour livraison
                    </button>
                </form>
            @endif

            @if($order->status === \App\Enums\OrderStatus::Ready)
                <form action="{{ route('admin.orders.mark-delivered', $order) }}" method="POST"
                      onsubmit="return confirm('Confirmer la livraison et l\'encaissement du paiement ?')">
                    @csrf
                    <button style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.25rem;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(22,163,74,.4);transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Livrée + Paiement reçu
                    </button>
                </form>
            @endif

            @if(!in_array($order->status, [\App\Enums\OrderStatus::Delivered, \App\Enums\OrderStatus::Cancelled]))
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST"
                      onsubmit="return confirm('Annuler cette commande et restaurer les stocks ?')">
                    @csrf
                    <button class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                        <x-icon name="cross-circled" class="w-4 h-4" /> Annuler la commande
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Articles + paiement vendeur --}}
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Articles ({{ $order->items->count() }})</h2>
            @php $allSellersPaid = $order->items->every(fn($i) => $i->seller_paid); @endphp
            @if($order->status === \App\Enums\OrderStatus::Delivered)
                <span class="text-xs {{ $allSellersPaid ? 'text-green-600' : 'text-orange-500' }} font-medium">
                    {{ $allSellersPaid ? '✓ Tous les vendeurs payés' : 'Paiements vendeurs en attente' }}
                </span>
            @endif
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendeur</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prêt</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sous-total</th>
                    @if($order->status === \App\Enums\OrderStatus::Delivered)
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Vendeur payé</th>
                    @endif
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
                        <td class="px-6 py-4 text-center">
                            @if($item->seller_ready)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                    <x-icon name="check" class="w-3 h-3" /> Prêt
                                </span>
                            @else
                                <span class="text-xs text-gray-400">En attente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-semibold" style="color: var(--color-primary);">
                            {{ number_format($item->subtotal, 0, ',', ' ') }} F
                        </td>
                        @if($order->status === \App\Enums\OrderStatus::Delivered)
                            <td class="px-6 py-4 text-center">
                                @if($item->seller_paid)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                        <x-icon name="check" class="w-3 h-3" /> Payé
                                        <span class="text-gray-400 font-normal">{{ $item->seller_paid_at?->format('d/m') }}</span>
                                    </span>
                                @else
                                    <form action="{{ route('admin.orders.mark-seller-paid', [$order, $item]) }}" method="POST">
                                        @csrf
                                        <button class="text-xs px-3 py-1 bg-indigo-600 text-white rounded-full hover:bg-indigo-700">
                                            Marquer payé
                                        </button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="{{ $order->status === \App\Enums\OrderStatus::Delivered ? 5 : 4 }}" class="px-6 py-3 text-right font-semibold text-gray-700">Total articles</td>
                    <td class="px-6 py-3 text-right text-lg font-bold" style="color: var(--color-primary);">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Historique --}}
    @if($order->events && $order->events->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <x-icon name="clipboard" class="w-4 h-4" /> Historique
                </h2>
            </div>
            <div class="px-6 py-4">
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-5">
                        @foreach($order->events->sortByDesc('created_at') as $event)
                            <div class="relative flex items-start gap-4 pl-10">
                                <div class="absolute left-2.5 w-3 h-3 rounded-full border-2 border-white shadow" style="background-color: var(--color-primary);"></div>
                                <div>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $event->status->color() }}-100 text-{{ $event->status->color() }}-800">
                                        {{ $event->status->label() }}
                                    </span>
                                    @if($event->comment)
                                        <p class="text-sm text-gray-600 mt-1">{{ $event->comment }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">{{ $event->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>

