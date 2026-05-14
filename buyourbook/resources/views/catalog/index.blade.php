<x-app-layout>
    <x-slot name="title">Catalogue — Tous les livres disponibles</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{
            sidebarOpen: false,
            schoolId: '{{ $schoolId }}',
            city: '{{ $city }}',
         }">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Catalogue</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $books->total() }} livre{{ $books->total() > 1 ? 's' : '' }} disponible{{ $books->total() > 1 ? 's' : '' }}</p>
            </div>
            <button @click="sidebarOpen = !sidebarOpen"
                    class="sm:hidden inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-icon name="adjustments-horizontal" class="w-4 h-4" /> Filtres
            </button>
        </div>

        <div class="flex gap-8">

            {{-- ═══════════ SIDEBAR FILTRES ═══════════ --}}
            <aside class="hidden sm:block w-64 flex-shrink-0"
                   x-show="true"
                   :class="sidebarOpen ? 'block' : ''">
                <form method="GET" action="{{ route('catalog.index') }}" id="filterForm">

                    {{-- Recherche --}}
                    <div class="mb-6">
                        <div class="relative">
                            <input type="text" name="q" value="{{ $q }}"
                                   placeholder="Titre, auteur, matière…"
                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm pl-9 pr-3 py-2.5 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" />
                        </div>
                    </div>

                    {{-- Tri --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Trier par</label>
                        <select name="sort" onchange="document.getElementById('filterForm').submit()"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>Plus récent</option>
                            <option value="price_asc"  {{ $sort === 'price_asc'  ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="title"      {{ $sort === 'title'      ? 'selected' : '' }}>Titre A→Z</option>
                        </select>
                    </div>

                    {{-- Ville --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Ville</label>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="city" value=""
                                       {{ !$city ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()"
                                       class="text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                Toutes les villes
                            </label>
                            @foreach($cities as $c)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="city" value="{{ $c }}"
                                           {{ $city === $c ? 'checked' : '' }}
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                    {{ $c }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- École --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">École</label>
                        <select name="school" onchange="document.getElementById('filterForm').submit()"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <option value="">Toutes les écoles</option>
                            @foreach($schools as $school)
                                @if(!$city || $school->city === $city)
                                    <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Classe (visible seulement si école sélectionnée) --}}
                    @if($schoolId && $grades->isNotEmpty())
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Classe</label>
                        <select name="grade" onchange="document.getElementById('filterForm').submit()"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <option value="">Toutes les classes</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ $gradeId == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Matière --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Matière</label>
                        <select name="subject" onchange="document.getElementById('filterForm').submit()"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <option value="">Toutes les matières</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" {{ $subjectId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- État --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">État du livre</label>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="condition" value=""
                                       {{ !$condition ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()"
                                       class="text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                Tous les états
                            </label>
                            @foreach($conditions as $cond)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="condition" value="{{ $cond->value }}"
                                           {{ $condition === $cond->value ? 'checked' : '' }}
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                    {{ $cond->label() }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Prix --}}
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Prix (FCFA)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="price_min" value="{{ $priceMin }}" min="0" step="500" placeholder="Min"
                                   class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <span class="text-gray-400 text-sm">—</span>
                            <input type="number" name="price_max" value="{{ $priceMax }}" min="0" step="500" placeholder="Max"
                                   class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                        </div>
                        <button type="submit" class="mt-2 w-full text-xs btn-secondary !py-1.5">Appliquer</button>
                    </div>

                    {{-- Réinitialiser --}}
                    @if($q || $city || $schoolId || $gradeId || $subjectId || $condition || $priceMin || $priceMax)
                    <a href="{{ route('catalog.index') }}"
                       class="block text-center text-xs text-gray-500 hover:text-gray-700 underline underline-offset-2 mt-2">
                        Réinitialiser les filtres
                    </a>
                    @endif

                </form>
            </aside>

            {{-- ═══════════ GRILLE LIVRES ═══════════ --}}
            <div class="flex-1 min-w-0">

                {{-- Filtres actifs (badges) --}}
                @if($q || $city || $schoolId || $subjectId || $condition || $priceMin || $priceMax)
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($q)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-[var(--color-primary-light)] text-[var(--color-primary)] border border-[var(--color-primary)]">
                            « {{ $q }} »
                            <a href="{{ route('catalog.index', array_merge(request()->except('q'), ['q' => ''])) }}" class="ml-1 hover:opacity-70">×</a>
                        </span>
                    @endif
                    @if($city)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            📍 {{ $city }}
                            <a href="{{ route('catalog.index', array_merge(request()->except(['city','school','grade']), ['city' => '', 'school' => '', 'grade' => ''])) }}" class="ml-1 hover:opacity-70">×</a>
                        </span>
                    @endif
                    @if($schoolId)
                        @php $selectedSchool = $schools->find($schoolId); @endphp
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            🏫 {{ $selectedSchool?->name ?? $schoolId }}
                            <a href="{{ route('catalog.index', array_merge(request()->except(['school','grade']), ['school' => '', 'grade' => ''])) }}" class="ml-1 hover:opacity-70">×</a>
                        </span>
                    @endif
                    @if($subjectId)
                        @php $selectedSubject = $subjects->find($subjectId); @endphp
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                            📚 {{ $selectedSubject?->name ?? $subjectId }}
                            <a href="{{ route('catalog.index', array_merge(request()->except('subject'), ['subject' => ''])) }}" class="ml-1 hover:opacity-70">×</a>
                        </span>
                    @endif
                    @if($condition)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            ✅ {{ collect($conditions)->first(fn($c) => $c->value === $condition)?->label() ?? $condition }}
                            <a href="{{ route('catalog.index', array_merge(request()->except('condition'), ['condition' => ''])) }}" class="ml-1 hover:opacity-70">×</a>
                        </span>
                    @endif
                    @if($priceMin || $priceMax)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                            💰 @if($priceMin){{ number_format($priceMin,0,',',' ') }} F@endif — @if($priceMax){{ number_format($priceMax,0,',',' ') }} F@endif
                            <a href="{{ route('catalog.index', array_merge(request()->except(['price_min','price_max']), ['price_min' => '', 'price_max' => ''])) }}" class="ml-1 hover:opacity-70">×</a>
                        </span>
                    @endif
                </div>
                @endif

                @if($books->count() > 0)
                    {{-- Grille produits --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($books as $book)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow flex flex-col overflow-hidden">

                            {{-- Couverture --}}
                            <a href="{{ route('catalog.book', $book) }}" class="block relative bg-gray-50">
                                @if($book->cover_image)
                                    <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}"
                                         class="w-full h-44 object-cover">
                                @else
                                    <div class="w-full h-44 flex items-center justify-center">
                                        <x-icon name="reader" class="w-16 h-16 text-gray-300" />
                                    </div>
                                @endif
                                {{-- Badge offres --}}
                                <span class="absolute top-2 right-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-white/90 shadow text-gray-700">
                                    {{ $book->seller_books_count }} offre{{ $book->seller_books_count > 1 ? 's' : '' }}
                                </span>
                            </a>

                            {{-- Infos --}}
                            <div class="p-4 flex flex-col flex-1">
                                <a href="{{ route('catalog.book', $book) }}"
                                   class="font-semibold text-gray-900 text-sm leading-tight hover:text-[var(--color-primary)] line-clamp-2 mb-1">
                                    {{ $book->title }}
                                </a>
                                <p class="text-xs text-gray-500 mb-1">{{ $book->subject->name ?? '' }}</p>
                                @if($book->grade)
                                    <p class="text-xs text-gray-400 line-clamp-1">
                                        {{ $book->grade->school->name ?? '' }} — {{ $book->grade->name }}
                                    </p>
                                @endif

                                <div class="mt-auto pt-3 flex items-end justify-between gap-2">
                                    <div>
                                        <p class="text-xs text-gray-400">à partir de</p>
                                        <p class="text-lg font-bold leading-tight" style="color: var(--color-primary);">
                                            {{ number_format($book->seller_books_min_price, 0, ',', ' ') }} F
                                        </p>
                                    </div>
                                    @if($book->cheapest_seller_book_id)
                                    <form action="{{ route('cart.add') }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="seller_book_id" value="{{ $book->cheapest_seller_book_id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 btn-primary !py-2 !px-3 text-xs">
                                            <x-icon name="backpack" class="w-3.5 h-3.5" /> Ajouter
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $books->links() }}
                    </div>

                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
                        <x-icon name="reader" class="w-16 h-16 text-gray-200 mx-auto mb-4" />
                        <p class="text-lg font-medium text-gray-400">Aucun livre trouvé</p>
                        <p class="text-sm text-gray-400 mt-1">Essayez de modifier ou réinitialiser vos filtres.</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-block btn-secondary !py-2 !px-4 text-sm">
                            Voir tous les livres
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Mobile sidebar drawer --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="sm:hidden fixed inset-0 z-40 bg-black/50"
             @click="sidebarOpen = false">
        </div>
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="sm:hidden fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl overflow-y-auto p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-900">Filtres</h2>
                <button @click="sidebarOpen = false" class="p-1 rounded text-gray-400 hover:text-gray-600">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            {{-- Same form duplicated for mobile --}}
            <form method="GET" action="{{ route('catalog.index') }}" id="mobileFilterForm">
                <div class="mb-5">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Titre, auteur…"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Ville</label>
                    <select name="city" onchange="document.getElementById('mobileFilterForm').submit()"
                            class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Toutes</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ $city === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">École</label>
                    <select name="school" onchange="document.getElementById('mobileFilterForm').submit()"
                            class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Toutes</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Matière</label>
                    <select name="subject" onchange="document.getElementById('mobileFilterForm').submit()"
                            class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Toutes</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ $subjectId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">État</label>
                    <select name="condition" onchange="document.getElementById('mobileFilterForm').submit()"
                            class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Tous</option>
                        @foreach($conditions as $cond)
                            <option value="{{ $cond->value }}" {{ $condition === $cond->value ? 'selected' : '' }}>{{ $cond->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Prix (FCFA)</label>
                    <div class="flex gap-2">
                        <input type="number" name="price_min" value="{{ $priceMin }}" min="0" step="500" placeholder="Min"
                               class="w-full rounded-md border-gray-300 text-sm">
                        <input type="number" name="price_max" value="{{ $priceMax }}" min="0" step="500" placeholder="Max"
                               class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                </div>
                <button type="submit" class="w-full btn-primary !py-2.5">Appliquer</button>
            </form>
        </div>

    </div>
</x-app-layout>
