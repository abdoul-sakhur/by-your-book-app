{{-- Ad Banner - Horizontal promotional banner --}}
@props(['banner' => null])

@if($banner)
<section class="py-8 lg:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-2xl"
             style="background: linear-gradient(135deg, var(--color-primary) 0%, #2d6a4f 60%, var(--color-primary-light) 100%);">
            {{-- Decorative circles --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white/5 rounded-full"></div>
            <div class="absolute top-1/2 right-1/4 w-20 h-20 bg-white/5 rounded-full"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between px-6 sm:px-10 py-8 sm:py-10 gap-6">
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                        <span class="text-2xl">📚</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-white/60">Publicité</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white leading-snug">
                        {{ $banner->text }}
                    </h3>
                    @if(!empty($banner->subtext))
                        <p class="mt-2 text-sm sm:text-base text-white/75 max-w-lg">
                            {{ $banner->subtext }}
                        </p>
                    @endif
                </div>
                @if(!empty($banner->cta_link))
                    <a href="{{ $banner->cta_link }}"
                       class="shrink-0 inline-flex items-center px-7 py-3.5 text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
                       style="background-color: var(--color-secondary); color: white;">
                        {{ $banner->cta_text ?? 'En savoir plus' }}
                        <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
