<x-app-layout>
    <x-slot name="title">{{ $school->name }} — {{ $grade->name }}</x-slot>

    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ route('catalog.schools') }}" class="hover:text-gray-700">Catalogue</a>
                <span class="mx-2">›</span>
                <span class="text-gray-900 font-medium">{{ $school->name }}</span>
                <span class="mx-2">›</span>
                <span class="text-gray-900 font-medium">{{ $grade->name }}</span>
            </nav>

            {{-- En-tête --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $grade->name }} — {{ $school->name }}</h1>
                    <p class="text-gray-500 mt-1">{{ $grade->level }} • Année {{ $grade->academic_year }}</p>
                </div>

                {{-- Sélecteur de classe --}}
                <div>
                    <select onchange="if(this.value) window.location = this.value"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                        <option value="">Changer de classe</option>
                        @foreach($school->grades->sortBy('level') as $g)
                            <option value="{{ route('catalog.grade', [$school, $g]) }}" {{ $g->id === $grade->id ? 'selected' : '' }}>
                                {{ $g->name }} ({{ $g->level }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Liste des livres --}}
            <div class="space-y-4">
                @forelse($books as $book)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                        <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Couverture --}}
                            <div class="flex-shrink-0">
                                @if($book->cover_image)
                                    <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}"
                                         class="w-16 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                        <x-icon name="reader" class="w-8 h-8" />
                                    </div>
                                @endif
                            </div>

                            {{-- Infos --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900">{{ $book->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    @if($book->author) {{ $book->author }} — @endif
                                    {{ $book->subject->name }}
                                </p>
                                @if($book->publisher)
                                    <p class="text-xs text-gray-400 mt-1">Éditeur : {{ $book->publisher }}</p>
                                @endif
                            </div>

                            {{-- Prix & dispo --}}
                            <div class="flex-shrink-0 text-right">
                                @if($book->seller_books_count > 0)
                                    <p class="text-lg font-bold" style="color: var(--color-primary);">
                                        à partir de {{ number_format($book->seller_books_min_price, 0, ',', ' ') }} F
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $book->seller_books_count }} offre(s)</p>
                                    <div class="mt-2 flex items-center gap-2 justify-end">
                                        @if($book->cheapest_seller_book_id)
                                            <form action="{{ route('cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="seller_book_id" value="{{ $book->cheapest_seller_book_id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="inline-flex items-center btn-primary !py-2 !px-4 text-sm gap-1">
                                                    <x-icon name="backpack" class="w-4 h-4" /> Acheter
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('catalog.book', $book) }}"
                                           class="inline-flex btn-secondary !py-2 !px-4 text-sm">
                                            Voir les offres
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-400">Aucune offre</p>
                                    <p class="text-xs text-gray-300 mt-1">actuellement disponible</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        Aucun livre officiel enregistré pour cette classe.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
