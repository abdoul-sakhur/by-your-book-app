{{-- Best Selling Books Section --}}
@props(['books' => collect()])

<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-section-header
            title="Livres populaires"
            subtitle="Les manuels les plus demandés par les élèves"
            link-text="Voir tout"
            link-url="{{ route('catalog.schools') }}"
        />

        @if($books->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($books as $book)
                    <x-product-card :book="$book" />
                @endforeach
            </div>

            {{-- Mobile "Voir tout" link --}}
            <div class="mt-6 text-center sm:hidden">
                <a href="{{ route('catalog.schools') }}"
                   class="inline-flex items-center gap-1 text-sm font-medium hover:underline"
                   style="color: var(--color-primary);">
                    Voir tous les livres
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        @else
            <div class="border border-dashed border-gray-300 rounded-lg p-10 text-center text-gray-400">
                Les livres populaires s'afficheront ici
            </div>
        @endif
    </div>
</section>
