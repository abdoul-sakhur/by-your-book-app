<x-app-layout>
    <x-slot name="title">Passer la commande</x-slot>

    <section class="py-10" x-data="{ selectedCity: '' }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold text-gray-900 mb-6">Valider ma commande</h1>

            {{-- Erreurs de validation --}}
            @if($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex items-center gap-2 text-red-800 font-medium text-sm mb-1">
                        <x-icon name="cross-circled" class="w-5 h-5" />
                        Veuillez corriger les erreurs suivantes :
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Récapitulatif articles --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Récapitulatif ({{ count($items) }} article(s))</h2>

                <div class="divide-y divide-gray-100">
                    @foreach($items as $item)
                        @php $book = $item['seller_book']; @endphp
                        <div class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-900">{{ $book->officialBook->title }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $book->condition->label() }} — par {{ $book->seller->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-400">{{ number_format($book->price, 0, ',', ' ') }} F × {{ $item['quantity'] }}</p>
                                <p class="font-semibold" style="color: var(--color-primary);">{{ number_format($item['subtotal'], 0, ',', ' ') }} F</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between items-center">
                    <p class="text-lg font-semibold text-gray-800">Total</p>
                    <p class="text-2xl font-bold" style="color: var(--color-primary);">{{ number_format($total, 0, ',', ' ') }} F CFA</p>
                </div>
            </div>

            {{-- Formulaire --}}
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf

                {{-- Point relais --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2"><x-icon name="drawing-pin" class="w-5 h-5" /> Point de retrait</h2>

                    @if($relayPoints->count() > 0)
                        {{-- Filtre par ville --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filtrer par ville</label>
                            <select x-model="selectedCity" class="rounded-lg border-gray-300 w-full sm:w-auto">
                                <option value="">Toutes les villes</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($relayPoints as $rp)
                                <label x-show="!selectedCity || selectedCity === '{{ $rp->city }}'"
                                       class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:border-[var(--color-primary)] transition"
                                       :class="{ 'border-[var(--color-primary)] bg-green-50 ring-1 ring-[var(--color-primary)]': $refs.rp{{ $rp->id }}?.checked }">
                                    <input type="radio" name="relay_point_id" value="{{ $rp->id }}"
                                           x-ref="rp{{ $rp->id }}"
                                           class="mt-0.5 text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                           {{ old('relay_point_id') == $rp->id ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900 text-sm">{{ $rp->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $rp->address }}, {{ $rp->district }} — {{ $rp->city }}</p>
                                        @if($rp->schedule)
                                            <p class="text-xs text-gray-400 mt-1"><x-icon name="clock" class="w-3 h-3 inline" /> {{ $rp->schedule }}</p>
                                        @endif
                                        @if($rp->contact_phone)
                                            <p class="text-xs text-gray-400"><x-icon name="mobile" class="w-3 h-3 inline" /> {{ $rp->contact_phone }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">Aucun point relais disponible pour le moment.</p>
                    @endif

                    @error('relay_point_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Notes (optionnel)</h2>
                    <textarea name="delivery_notes" rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                              placeholder="Instructions spéciales, horaires préférés...">{{ old('delivery_notes') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('cart.index') }}" class="text-center py-3 px-6 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                        Retour au panier
                    </a>
                    <button type="submit" class="btn-primary flex-1 !py-3 text-center flex items-center justify-center gap-2">
                        <x-icon name="check-circled" class="w-5 h-5" /> Confirmer la commande — {{ number_format($total, 0, ',', ' ') }} F CFA
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
