<x-app-layout>
    <x-slot name="title">Mes commandes</x-slot>

    <section class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold text-gray-900 mb-6">📦 Mes commandes</h1>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        Commande #{{ $order->id }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $order->created_at->format('d/m/Y à H:i') }}
                                        — {{ $order->items->count() }} article(s)
                                    </p>
                                    @if($order->relayPoint)
                                        <p class="text-xs text-gray-400 mt-1">📍 {{ $order->relayPoint->name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                                        {{ $order->status->label() }}
                                    </span>
                                    <p class="text-lg font-bold" style="color: var(--color-primary);">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-5xl mb-4">📦</p>
                    <h2 class="text-xl font-semibold text-gray-700">Aucune commande</h2>
                    <p class="text-gray-400 mt-2">Vous n'avez pas encore passé de commande.</p>
                    <a href="{{ route('catalog.schools') }}" class="btn-primary mt-6 inline-block !py-3 !px-8">
                        Voir le catalogue
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
