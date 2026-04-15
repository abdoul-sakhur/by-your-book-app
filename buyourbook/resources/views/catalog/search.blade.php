<x-app-layout>
    <x-slot name="title">Recherche — {{ $query ?: 'Tous les livres' }}</x-slot>

    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Barre de recherche --}}
            <form method="GET" action="{{ route('catalog.search') }}" class="mb-8">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <input type="text" name="q" value="{{ $query }}" placeholder="Rechercher un livre, un auteur, une matière, une école…"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 pl-10 pr-4 py-3">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="btn-primary !px-6">Rechercher</button>
                </div>

                {{-- Filtres --}}
                <div x-data="{ open: {{ ($subjectId || $condition || $priceMin || $priceMax) ? 'true' : 'false' }} }" class="mt-4">
                    <button type="button" @click="open = !open" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">
                        <span x-text="open ? '▾ Masquer les filtres' : '▸ Afficher les filtres'"></span>
                    </button>

                    <div x-show="open" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 bg-white rounded-lg border border-gray-200 p-4">
                        {{-- Matière --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Matière</label>
                            <select name="subject" class="w-full rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Toutes</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}" {{ $subjectId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- État --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">État</label>
                            <select name="condition" class="w-full rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Tous</option>
                                @foreach($conditions as $c)
                                    <option value="{{ $c->value }}" {{ $condition == $c->value ? 'selected' : '' }}>{{ $c->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Prix min --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Prix min (F)</label>
                            <input type="number" name="price_min" value="{{ $priceMin }}" min="0" step="500" placeholder="0"
                                   class="w-full rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        {{-- Prix max --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Prix max (F)</label>
                            <input type="number" name="price_max" value="{{ $priceMax }}" min="0" step="500" placeholder="50 000"
                                   class="w-full rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        {{-- Tri --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Trier par</label>
                            <select name="sort" class="w-full rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                                <option value="pertinence" {{ $sort === 'pertinence' ? 'selected' : '' }}>Pertinence</option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Plus récent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Résultats --}}
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-900">
                    @if($query)
                        Résultats pour « {{ $query }} »
                    @else
                        Tous les livres disponibles
                    @endif
                    <span class="text-base font-normal text-gray-500">({{ $offers->total() }} offre{{ $offers->total() > 1 ? 's' : '' }})</span>
                </h1>
            </div>

            @if($offers->count() > 0)
                <div class="space-y-3">
                    @foreach($offers as $offer)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition">
                            {{-- Photo --}}
                            <div class="flex-shrink-0">
                                @if($offer->officialBook->cover_image)
                                    <img src="{{ Storage::url($offer->officialBook->cover_image) }}" alt="{{ $offer->officialBook->title }}"
                                         class="w-16 h-20 object-cover rounded-lg">
                                @elseif($offer->images && count($offer->images) > 0)
                                    <img src="{{ Storage::url($offer->images[0]) }}" alt="Photo"
                                         class="w-16 h-20 object-cover rounded-lg border">
                                @else
                                    <div class="w-16 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">📚</div>
                                @endif
                            </div>

                            {{-- Infos --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('catalog.book', $offer->officialBook) }}" class="font-semibold text-gray-900 hover:underline">
                                    {{ $offer->officialBook->title }}
                                </a>
                                <p class="text-sm text-gray-500">
                                    {{ $offer->officialBook->subject->name ?? '' }}
                                    @if($offer->officialBook->author) — {{ $offer->officialBook->author }} @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $offer->officialBook->grade->school->name ?? '' }} — {{ $offer->officialBook->grade->name ?? '' }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $offer->condition->color() }}-100 text-{{ $offer->condition->color() }}-800">
                                        {{ $offer->condition->label() }}
                                    </span>
                                    <span class="text-xs text-gray-400">par <a href="{{ route('seller.public-profile', $offer->seller) }}" class="hover:underline" style="color: var(--color-primary);">{{ $offer->seller->name }}</a></span>
                                </div>
                            </div>

                            {{-- Prix & bouton --}}
                            <div class="flex items-center gap-4 flex-shrink-0">
                                <div class="text-right">
                                    <p class="text-xl font-bold" style="color: var(--color-primary);">
                                        {{ number_format($offer->price, 0, ',', ' ') }} F
                                    </p>
                                    <p class="text-xs text-gray-400">Dispo : {{ $offer->quantity }}</p>
                                </div>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seller_book_id" value="{{ $offer->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-secondary !py-2 !px-4 text-sm">
                                        🛒 Ajouter
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $offers->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-lg text-gray-400">Aucun résultat trouvé.</p>
                    <p class="text-sm text-gray-300 mt-2">Essayez avec d'autres termes ou ajustez vos filtres.</p>
                    <a href="{{ route('catalog.schools') }}" class="inline-block mt-6 btn-primary">Parcourir le catalogue</a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
