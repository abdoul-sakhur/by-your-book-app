<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Commande confirmée !</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Succès --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center bg-green-100 mb-4"><x-icon name="check-circled" class="w-8 h-8 text-green-600" /></div>
                <h3 class="text-xl font-bold text-green-800">Merci pour votre commande !</h3>
                <p class="mt-2 text-green-700">Votre commande <strong>#{{ $order->id }}</strong> a bien été enregistrée.</p>
            </div>

            {{-- Résumé --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200" style="background-color: var(--color-primary);">
                    <h4 class="font-semibold text-white">Récapitulatif de la commande</h4>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Numéro</p>
                            <p class="font-semibold text-gray-900">#{{ $order->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Statut</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                    </div>

                    {{-- Articles --}}
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-2 text-gray-500">Livre</th>
                                <th class="text-center px-4 py-2 text-gray-500">Qté</th>
                                <th class="text-right px-4 py-2 text-gray-500">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $item->sellerBook->officialBook->title }}</p>
                                        <p class="text-xs text-gray-400">Vendeur : {{ $item->sellerBook->seller->name }}</p>
                                    </td>
                                    <td class="text-center px-4 py-3 text-gray-700">{{ $item->quantity }}</td>
                                    <td class="text-right px-4 py-3 font-medium text-gray-900">{{ number_format($item->subtotal, 0, ',', ' ') }} F</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200">
                                <td colspan="2" class="px-4 py-3 text-right font-bold text-gray-900">Total</td>
                                <td class="text-right px-4 py-3 font-bold text-lg" style="color: var(--color-primary);">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Point relais --}}
            @if($order->relayPoint)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><x-icon name="drawing-pin" class="w-5 h-5" /> Point de retrait</h4>
                    <p class="font-medium text-gray-900">{{ $order->relayPoint->name }}</p>
                    <p class="text-sm text-gray-500">{{ $order->relayPoint->address }}, {{ $order->relayPoint->city }}</p>
                    @if($order->relayPoint->contact_phone)
                        <p class="text-sm text-gray-500 mt-1"><x-icon name="mobile" class="w-3 h-3 inline" /> {{ $order->relayPoint->contact_phone }}</p>
                    @endif
                </div>
            @endif

            {{-- Prochaines étapes --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center gap-2"><x-icon name="clipboard" class="w-5 h-5" /> Prochaines étapes</h4>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-700">
                    <li>Votre commande est en cours de traitement par notre équipe</li>
                    <li>Les livres seront préparés et déposés au point relais choisi</li>
                    <li>Vous serez notifié quand votre commande sera prête à être récupérée</li>
                    <li>Présentez-vous au point relais avec votre numéro de commande <strong>#{{ $order->id }}</strong></li>
                </ol>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('orders.show', $order) }}" class="btn-primary text-center">Voir ma commande</a>
                <a href="{{ route('catalog.schools') }}" class="text-center px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium">Continuer mes achats</a>
            </div>

        </div>
    </div>
</x-app-layout>
