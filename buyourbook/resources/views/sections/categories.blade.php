{{-- Categories Section --}}
@props(['categories' => collect()])

<section class="py-12 lg:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-section-header
            title="Catégories"
            subtitle="Parcourez les livres par matière"
            link-text="Tout parcourir"
            link-url="{{ route('catalog.schools') }}"
        />

        <div class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide snap-x snap-mandatory">
            @foreach($categories as $category)
                <a href="{{ route('catalog.search', ['q' => $category->name]) }}"
                   class="snap-start shrink-0 w-36 sm:w-40 group">
                    <div class="rounded-xl p-5 text-center transition-all duration-300 group-hover:shadow-lg group-hover:scale-105 {{ $category->color }}">
                        <span class="text-4xl block">{{ $category->icon }}</span>
                        <p class="mt-3 text-sm font-semibold truncate">{{ $category->name }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
