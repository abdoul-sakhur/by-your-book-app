{{-- Product Card Component --}}
@props(['book'])

<div class="bg-white rounded-xl shadow-md hover:shadow-xl hover:scale-[1.02] transition-all duration-300 overflow-hidden group">
    {{-- Image --}}
    <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden">
        @if($book->image)
            <img src="{{ $book->image }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200">
                <svg class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="mt-2 text-xs text-gray-400">Pas d'image</span>
            </div>
        @endif

        {{-- Condition Badge --}}
        @if(!empty($book->condition))
            <span class="absolute top-3 left-3 px-2.5 py-1 text-xs font-semibold rounded-full bg-white/90 backdrop-blur-sm text-gray-700 shadow-sm">
                {{ $book->condition }}
            </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4">
        <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 leading-snug min-h-[2.5rem]">
            {{ $book->title }}
        </h3>

        <div class="mt-3 flex items-center justify-between">
            <span class="text-lg font-bold" style="color: var(--color-primary);">
                {{ number_format($book->price, 0, ',', ' ') }} <span class="text-xs font-medium">FCFA</span>
            </span>
        </div>

        @if($book->seller_book_id)
            <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="seller_book_id" value="{{ $book->seller_book_id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                        class="w-full py-2.5 text-sm font-semibold text-white rounded-lg bg-green-600 hover:bg-green-700 active:bg-green-800 transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    Ajouter au panier
                </button>
            </form>
        @else
            <button data-id="{{ $book->id }}"
                    class="mt-3 w-full py-2.5 text-sm font-semibold text-white rounded-lg bg-green-600 hover:bg-green-700 active:bg-green-800 transition-colors duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                Ajouter au panier
            </button>
        @endif
    </div>
</div>
