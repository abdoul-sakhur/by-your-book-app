<x-app-layout>
    <x-slot name="title">Mon panier</x-slot>

    <section class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold text-gray-900 mb-6">🛒 Mon panier</h1>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($items) > 0)
                <div class="space-y-4">
                    @foreach($items as $item)
                        @php $book = $item['seller_book']; @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Photo --}}
                            <div class="flex-shrink-0">
                                @if($book->images && count($book->images) > 0)
                                    <img src="{{ Storage::url($book->images[0]) }}" alt="Photo" class="w-16 h-16 object-cover rounded-lg border">
                                @else
                                    <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center text-gray-300">📷</div>
                                @endif
                            </div>

                            {{-- Infos --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $book->officialBook->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $book->officialBook->grade->school->name }} — {{ $book->officialBook->grade->name }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $book->condition->color() }}-100 text-{{ $book->condition->color() }}-800">
                                        {{ $book->condition->label() }}
                                    </span>
                                    <span class="text-xs text-gray-400">par {{ $book->seller->name }}</span>
                                </div>
                            </div>

                            {{-- Quantité --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    <input type="hidden" name="seller_book_id" value="{{ $book->id }}">
                                    <select name="quantity" onchange="this.form.submit()"
                                            class="rounded-md border-gray-300 text-sm py-1 pr-8">
                                        @for($i = 1; $i <= min($book->quantity, 10); $i++)
                                            <option value="{{ $i }}" {{ $item['quantity'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </form>
                            </div>

                            {{-- Prix --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm text-gray-500">{{ number_format($book->price, 0, ',', ' ') }} F × {{ $item['quantity'] }}</p>
                                <p class="text-lg font-bold" style="color: var(--color-primary);">
                                    {{ number_format($item['subtotal'], 0, ',', ' ') }} F
                                </p>
                            </div>

                            {{-- Supprimer --}}
                            <form action="{{ route('cart.remove') }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <input type="hidden" name="seller_book_id" value="{{ $book->id }}">
                                <button type="submit" class="text-red-400 hover:text-red-600 p-1" title="Retirer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                {{-- Total + boutons --}}
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center">
                        <p class="text-lg text-gray-600">Total</p>
                        <p class="text-2xl font-bold" style="color: var(--color-primary);">
                            {{ number_format($total, 0, ',', ' ') }} F CFA
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('catalog.schools') }}" class="text-center py-3 px-6 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                            Continuer mes achats
                        </a>
                        <a href="{{ route('checkout.index') }}" class="btn-primary text-center flex-1 !py-3">
                            Passer la commande
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-5xl mb-4">🛒</p>
                    <h2 class="text-xl font-semibold text-gray-700">Votre panier est vide</h2>
                    <p class="text-gray-400 mt-2">Parcourez le catalogue pour trouver vos livres scolaires.</p>
                    <a href="{{ route('catalog.schools') }}" class="btn-primary mt-6 inline-block !py-3 !px-8">
                        Voir le catalogue
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
