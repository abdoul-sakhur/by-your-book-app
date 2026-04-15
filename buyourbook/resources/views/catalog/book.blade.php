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
                            <x-icon name="reader" class="w-12 h-12" />
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
                                        <svg class="w-6 h-6 transition" :class="wishlisted ? 'text-red-500' : 'text-gray-300 hover:text-red-400'" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path x-show="!wishlisted" d="M4.89346 2.35248C3.49195 2.35248 2.35248 3.49359 2.35248 4.90532C2.35248 6.38164 3.20954 7.9168 4.37255 9.33522C5.39396 10.581 6.59464 11.6702 7.50002 12.4778C8.4054 11.6702 9.60608 10.581 10.6275 9.33522C11.7905 7.9168 12.6476 6.38164 12.6476 4.90532C12.6476 3.49359 11.5081 2.35248 10.1066 2.35248C9.27059 2.35248 8.81894 2.64323 8.5397 2.95843C8.27877 3.25295 8.14623 3.58566 8.02501 3.88993C8.00391 3.9429 7.98315 3.99501 7.96211 4.04591C7.88482 4.23294 7.7024 4.35494 7.50002 4.35494C7.29765 4.35494 7.11523 4.23295 7.03793 4.04592C7.01689 3.99501 6.99612 3.94289 6.97502 3.8899C6.8538 3.58564 6.72126 3.25294 6.46034 2.95843C6.18109 2.64323 5.72945 2.35248 4.89346 2.35248ZM1.35248 4.90532C1.35248 2.94498 2.936 1.35248 4.89346 1.35248C6.0084 1.35248 6.73504 1.76049 7.20884 2.2953C7.32062 2.42147 7.41686 2.55382 7.50002 2.68545C7.58318 2.55382 7.67941 2.42147 7.79119 2.2953C8.265 1.76049 8.99164 1.35248 10.1066 1.35248C12.064 1.35248 13.6476 2.94498 13.6476 4.90532C13.6476 6.74041 12.6013 8.50508 11.4008 9.96927C10.2636 11.3562 8.92194 12.5508 8.00601 13.3664C7.94645 13.4194 7.88869 13.4709 7.83291 13.5206C7.64324 13.6899 7.3568 13.6899 7.16713 13.5206C7.11135 13.4709 7.05359 13.4194 6.99403 13.3664C6.0781 12.5508 4.73641 11.3562 3.59926 9.96927C2.39872 8.50508 1.35248 6.74041 1.35248 4.90532Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"/>
                                            <path x-show="wishlisted" d="M1.35248 4.90532C1.35248 2.94498 2.936 1.35248 4.89346 1.35248C6.25769 1.35248 6.86058 1.92336 7.50002 2.93545C8.13946 1.92336 8.74235 1.35248 10.1066 1.35248C12.064 1.35248 13.6476 2.94498 13.6476 4.90532C13.6476 6.74041 12.6013 8.50508 11.4008 9.96927C10.2636 11.3562 8.92194 12.5508 8.00601 13.3664C7.94645 13.4194 7.88869 13.4709 7.83291 13.5206C7.64324 13.6899 7.3568 13.6899 7.16713 13.5206C7.11135 13.4709 7.05359 13.4194 6.99403 13.3664C6.0781 12.5508 4.73641 11.3562 3.59926 9.96927C2.39872 8.50508 1.35248 6.74041 1.35248 4.90532Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"/>
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
                        <p class="text-sm text-gray-500 mt-2 flex items-center gap-1">
                            <x-icon name="drawing-pin" class="w-4 h-4" /> {{ $officialBook->grade->school->name }} — {{ $officialBook->grade->name }}
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
                                        <x-icon name="camera" class="w-6 h-6" />
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
                                    <button type="submit" class="btn-secondary !py-2 !px-4 text-sm flex items-center gap-1">
                                        <x-icon name="backpack" class="w-4 h-4" /> Ajouter
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
