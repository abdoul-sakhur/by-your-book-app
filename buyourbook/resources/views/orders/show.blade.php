<x-app-layout>
    <x-slot name="title">Commande #{{ $order->id }}</x-slot>

    <section class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Entête --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Mes commandes</a>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">Commande #{{ $order->id }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('orders.invoice', $order) }}" class="btn-secondary !py-2 !px-4 text-sm inline-flex items-center gap-1">
                        📄 Télécharger la facture
                    </a>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                        {{ $order->status->label() }}
                    </span>
                </div>
            </div>

            {{-- Infos commande --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Date</p>
                    <p class="font-semibold text-gray-900 mt-1">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
                    <p class="font-bold text-lg mt-1" style="color: var(--color-primary);">{{ number_format($order->total_amount, 0, ',', ' ') }} F CFA</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Point relais</p>
                    @if($order->relayPoint)
                        <p class="font-semibold text-gray-900 mt-1">{{ $order->relayPoint->name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->relayPoint->address }}, {{ $order->relayPoint->district }}</p>
                    @else
                        <p class="text-gray-400 mt-1 text-sm">Non spécifié</p>
                    @endif
                </div>
            </div>

            @if($order->delivery_notes)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-sm text-yellow-800">
                    <strong>Notes :</strong> {{ $order->delivery_notes }}
                </div>
            @endif

            {{-- Articles --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Articles ({{ $order->items->count() }})</h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $item->sellerBook->officialBook->title ?? 'Livre supprimé' }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $item->sellerBook->officialBook->subject->name ?? '' }}
                                    — par {{ $item->sellerBook->seller->name ?? '?' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-400">
                                    {{ number_format($item->unit_price, 0, ',', ' ') }} F × {{ $item->quantity }}
                                </p>
                                <p class="font-semibold" style="color: var(--color-primary);">
                                    {{ number_format($item->subtotal, 0, ',', ' ') }} F
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                    <p class="font-semibold text-gray-700">Total</p>
                    <p class="text-xl font-bold" style="color: var(--color-primary);">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} F CFA
                    </p>
                </div>
            </div>

            {{-- Timeline des événements --}}
            @if($order->events && $order->events->count() > 0)
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">📋 Historique de la commande</h2>
                    </div>
                    <div class="px-6 py-4">
                        <div class="relative">
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="space-y-6">
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
        </div>
    </section>
</x-app-layout>
