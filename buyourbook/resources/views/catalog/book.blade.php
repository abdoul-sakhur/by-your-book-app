<x-app-layout>
    <x-slot name="title">{{ $officialBook->title }}</x-slot>

    <section class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ route('catalog.schools') }}" class="hover:text-gray-700">Catalogue</a>
                <span class="mx-2">›</span>
                <a href="{{ route('catalog.grade', [$officialBook->grade->school, $officialBook->grade]) }}" class="hover:text-gray-700">
                    {{ $officialBook->grade->school->name }} — {{ $officialBook->grade->name }}
                </a>
                <span class="mx-2">›</span>
                <span class="text-gray-900 font-medium">{{ $officialBook->title }}</span>
            </nav>

            {{-- Entête livre --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex flex-col sm:flex-row gap-6">
                    @if($officialBook->cover_image)
                        <img src="{{ Storage::url($officialBook->cover_image) }}" alt="{{ $officialBook->title }}"
                             class="w-32 h-40 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div class="w-32 h-40 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300 flex-shrink-0">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $officialBook->title }}</h1>
                            @auth
                                <div x-data="{ wishlisted: {{ auth()->user()->wishlists()->where('official_book_id', $officialBook->id)->exists() ? 'true' : 'false' }}, loading: false }"
                                     class="flex-shrink-0">
                                    <button @click="loading = true; fetch('{{ route('wishlist.toggle') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ official_book_id: {{ $officialBook->id }} }) }).then(r => r.json()).then(d => { wishlisted = d.added; loading = false; })"
                                            :disabled="loading"
                                            class="p-1.5 rounded-full hover:bg-red-50 transition"
                                            :title="wishlisted ? 'Retirer des favoris' : 'Ajouter aux favoris'">
                                        <svg class="w-6 h-6 transition" :class="wishlisted ? 'text-red-500' : 'text-gray-300 hover:text-red-400'" :fill="wishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            @endauth
                        </div>
                        <p class="text-gray-600 mt-1">{{ $officialBook->subject->name }}</p>
                        @if($officialBook->author)
                            <p class="text-sm text-gray-500 mt-1">Auteur : {{ $officialBook->author }}</p>
                        @endif
                        @if($officialBook->publisher)
                            <p class="text-sm text-gray-500">Éditeur : {{ $officialBook->publisher }}</p>
                        @endif
                        @if($officialBook->isbn)
                            <p class="text-xs text-gray-400 mt-1">ISBN : {{ $officialBook->isbn }}</p>
                        @endif
                        <p class="text-sm text-gray-500 mt-2">
                            📍 {{ $officialBook->grade->school->name }} — {{ $officialBook->grade->name }}
                        </p>
                        @if($officialBook->description)
                            <p class="text-sm text-gray-600 mt-3">{{ $officialBook->description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Offres vendeurs --}}
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ $offers->count() }} offre(s) disponible(s)
            </h2>

            @if($offers->count() > 0)
                <div class="space-y-3">
                    @foreach($offers as $offer)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Photos --}}
                            <div class="flex gap-2 flex-shrink-0">
                                @if($offer->images && count($offer->images) > 0)
                                    @foreach(array_slice($offer->images, 0, 2) as $img)
                                        <img src="{{ Storage::url($img) }}" alt="Photo"
                                             class="w-16 h-16 object-cover rounded-lg border">
                                    @endforeach
                                @else
                                    <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center text-gray-300">
                                        📷
                                    </div>
                                @endif
                            </div>

                            {{-- Infos offre --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $offer->condition->color() }}-100 text-{{ $offer->condition->color() }}-800">
                                        {{ $offer->condition->label() }}
                                    </span>
                                    <span class="text-sm text-gray-500">par <a href="{{ route('seller.public-profile', $offer->seller) }}" class="hover:underline" style="color: var(--color-primary);">{{ $offer->seller->name }}</a></span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Quantité disponible : {{ $offer->quantity }}</p>
                            </div>

                            {{-- Prix & bouton --}}
                            <div class="flex items-center gap-4 flex-shrink-0">
                                <p class="text-xl font-bold" style="color: var(--color-primary);">
                                    {{ number_format($offer->price, 0, ',', ' ') }} F
                                </p>
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
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
                    <p class="text-lg">Aucune offre de vendeur pour ce livre actuellement.</p>
                    <p class="text-sm mt-2">Revenez plus tard ou consultez d'autres livres.</p>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
