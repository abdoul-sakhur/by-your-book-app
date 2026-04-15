<x-app-layout>
    <x-slot name="title">Mes favoris</x-slot>

    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2"><x-icon name="heart-filled" class="w-6 h-6 text-red-500" /> Mes favoris</h1>

            @if($wishlists->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($wishlists as $wishlist)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
                            <div class="flex gap-4">
                                @if($wishlist->officialBook->cover_image)
                                    <img src="{{ Storage::url($wishlist->officialBook->cover_image) }}" alt="{{ $wishlist->officialBook->title }}"
                                         class="w-16 h-20 object-cover rounded-lg flex-shrink-0">
                                @else
                                    <div class="w-16 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-300 flex-shrink-0"><x-icon name="reader" class="w-8 h-8" /></div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('catalog.book', $wishlist->officialBook) }}" class="font-semibold text-gray-900 hover:underline line-clamp-2">
                                        {{ $wishlist->officialBook->title }}
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">{{ $wishlist->officialBook->subject->name ?? '' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $wishlist->officialBook->grade->school->name ?? '' }} — {{ $wishlist->officialBook->grade->name ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                <a href="{{ route('catalog.book', $wishlist->officialBook) }}" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">
                                    Voir les offres →
                                </a>
                                <form action="{{ route('wishlist.remove', $wishlist) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:text-red-700" title="Retirer des favoris">
                                        <x-icon name="trash" class="w-4 h-4 inline" /> Retirer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $wishlists->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-lg text-gray-400">Vous n'avez aucun favori pour le moment.</p>
                    <p class="text-sm text-gray-300 mt-2">Parcourez le catalogue et ajoutez des livres à vos favoris.</p>
                    <a href="{{ route('catalog.schools') }}" class="inline-block mt-6 btn-primary">Parcourir le catalogue</a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
