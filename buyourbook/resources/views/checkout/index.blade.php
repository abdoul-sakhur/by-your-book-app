<x-app-layout>
    <x-slot name="title">Passer la commande</x-slot>

    <section class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold text-gray-900 mb-6">Valider ma commande</h1>

            {{-- Message livraison à domicile --}}
            <div class="mb-6 rounded-xl p-4 flex items-start gap-3" style="background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); border: 1px solid #a5d6a7;">
                <x-icon name="home" class="w-6 h-6 mt-0.5 flex-shrink-0" style="color: var(--color-primary);" />
                <div>
                    <p class="font-semibold text-gray-800">Livraison à domicile</p>
                    <p class="text-sm text-gray-600 mt-0.5">Vous n'avez pas besoin de vous déplacer – achetez vos livres et recevez-les chez vous.</p>
                </div>
            </div>

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

                <div class="border-t border-gray-200 mt-4 pt-4 space-y-2">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>Sous-total</span>
                        <span>{{ number_format($total, 0, ',', ' ') }} F CFA</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">
                            Frais de livraison
                            @if($deliveryFee === 0)
                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Gratuit</span>
                            @endif
                        </span>
                        <span class="{{ $deliveryFee === 0 ? 'text-green-600 font-medium' : 'text-gray-700' }}">
                            {{ $deliveryFee === 0 ? 'Gratuit' : number_format($deliveryFee, 0, ',', ' ') . ' F CFA' }}
                        </span>
                    </div>
                    @if($deliveryFee > 0)
                        <p class="text-xs text-gray-400">Livraison gratuite dès {{ number_format($deliveryFeeThreshold, 0, ',', ' ') }} F CFA d'achats.</p>
                    @endif
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <p class="text-lg font-semibold text-gray-800">Total</p>
                        <p class="text-2xl font-bold" style="color: var(--color-primary);">{{ number_format($total + $deliveryFee, 0, ',', ' ') }} F CFA</p>
                    </div>
                </div>
            </div>

            {{-- Formulaire --}}
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf

                {{-- Adresse de livraison --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <x-icon name="home" class="w-5 h-5" /> Adresse de livraison
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse complète <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="delivery_address" name="delivery_address"
                                   value="{{ old('delivery_address', auth()->user()->address ?? '') }}"
                                   placeholder="Quartier, rue, numéro, points de repère…"
                                   class="w-full rounded-lg border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                   required>
                            @error('delivery_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="delivery_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Téléphone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="delivery_phone" name="delivery_phone"
                                   value="{{ old('delivery_phone', auth()->user()->phone ?? '') }}"
                                   placeholder="Ex : 07 00 00 00 00"
                                   class="w-full rounded-lg border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                   required>
                            @error('delivery_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Mode de paiement --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <x-icon name="card-stack" class="w-5 h-5" /> Mode de paiement
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition hover:border-gray-300
                            {{ old('payment_method', 'cash') === 'cash' ? 'border-[var(--color-primary)] bg-green-50' : 'border-gray-200' }}">
                            <input type="radio" name="payment_method" value="cash"
                                   {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}
                                   class="mt-0.5 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">💵 Cash à la livraison</p>
                                <p class="text-xs text-gray-500 mt-0.5">Payez en espèces à la réception de vos livres.</p>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border-2 rounded-xl cursor-pointer transition hover:border-gray-300
                            {{ old('payment_method') === 'mobile_money' ? 'border-[var(--color-primary)] bg-green-50' : 'border-gray-200' }}">
                            <input type="radio" name="payment_method" value="mobile_money"
                                   {{ old('payment_method') === 'mobile_money' ? 'checked' : '' }}
                                   class="mt-0.5 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">📱 Mobile Money</p>
                                <p class="text-xs text-gray-500 mt-0.5">Orange Money, MTN MoMo, Wave, Moov Money…</p>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Instructions (optionnel)</h2>
                    <textarea name="delivery_notes" rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                              placeholder="Horaires préférés, points de repère supplémentaires…">{{ old('delivery_notes') }}</textarea>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('cart.index') }}" class="text-center py-3 px-6 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                        Retour au panier
                    </a>
                    <button type="submit" class="btn-primary flex-1 !py-3 text-center flex items-center justify-center gap-2">
                        <x-icon name="check-circled" class="w-5 h-5" />
                        Confirmer la commande — {{ number_format($total + $deliveryFee, 0, ',', ' ') }} F CFA
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
