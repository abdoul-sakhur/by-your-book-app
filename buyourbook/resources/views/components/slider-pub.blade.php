{{-- Slider publicitaire — Alpine.js --}}
@props(['slides'])

@if($slides->isNotEmpty())
<style>
    .slider-container { height: 280px; }
    @media (min-width: 640px) { .slider-container { height: 360px; } }
    @media (min-width: 1024px) { .slider-container { height: 440px; } }
</style>
<section class="py-6 lg:py-10" style="background-color: var(--color-bg);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div x-data="{
                current: 0,
                total: {{ $slides->count() }},
                autoplay: null,
                startAutoplay() {
                    this.autoplay = setInterval(() => this.next(), 5000);
                },
                stopAutoplay() {
                    clearInterval(this.autoplay);
                },
                next() {
                    this.current = (this.current + 1) % this.total;
                },
                prev() {
                    this.current = (this.current - 1 + this.total) % this.total;
                },
                goTo(i) {
                    this.current = i;
                    this.stopAutoplay();
                    this.startAutoplay();
                }
             }"
             x-init="startAutoplay()"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()"
             class="relative rounded-2xl overflow-hidden shadow-xl slider-container">

            {{-- Slides --}}
            @foreach($slides as $index => $slide)
                <div x-show="current === {{ $index }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0">

                    {{-- Image de fond --}}
                    @if($slide->image)
                        <img src="{{ Storage::url($slide->image) }}"
                             alt="{{ $slide->title }}"
                             class="absolute inset-0 w-full h-full object-cover object-center">
                    @else
                        <div class="absolute inset-0" style="background: linear-gradient(135deg, var(--color-primary) 0%, #2d6a4f 100%);"></div>
                    @endif

                    {{-- Overlay sombre --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-black/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                    {{-- Contenu texte --}}
                    <div class="relative z-10 flex items-center h-full">
                        <div class="w-full px-6 sm:px-10 lg:px-14">
                            <div class="max-w-xl text-center sm:text-left mx-auto sm:mx-0">
                                @if($slide->title)
                                    <h3 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-white leading-tight drop-shadow-lg">
                                        {{ $slide->title }}
                                    </h3>
                                @endif

                                @if($slide->description)
                                    <p class="mt-4 text-sm sm:text-base lg:text-lg text-white/90 leading-relaxed max-w-md drop-shadow-md">
                                        {{ $slide->description }}
                                    </p>
                                @endif

                                @if($slide->cta_link)
                                    <div class="mt-6 sm:mt-8">
                                        <a href="{{ $slide->cta_link }}"
                                           class="inline-flex items-center gap-2 px-6 py-3 sm:px-8 sm:py-3.5 text-sm sm:text-base font-bold text-white rounded-full shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl active:scale-95"
                                           style="background-color: var(--color-secondary);">
                                            {{ $slide->cta_text ?? 'En savoir plus' }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Flèche gauche --}}
            <button @click="prev(); stopAutoplay(); startAutoplay();"
                    class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white border border-white/30 transition-all duration-200 hover:bg-white/40 hover:scale-110 active:scale-95"
                    aria-label="Slide précédent">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Flèche droite --}}
            <button @click="next(); stopAutoplay(); startAutoplay();"
                    class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white border border-white/30 transition-all duration-200 hover:bg-white/40 hover:scale-110 active:scale-95"
                    aria-label="Slide suivant">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Dots indicateurs --}}
            <div class="absolute bottom-4 sm:bottom-5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
                @foreach($slides as $index => $slide)
                    <button @click="goTo({{ $index }})"
                            :class="current === {{ $index }}
                                ? 'w-8 bg-[var(--color-secondary)] opacity-100'
                                : 'w-2.5 bg-white opacity-50 hover:opacity-80'"
                            class="h-2.5 rounded-full transition-all duration-300"
                            aria-label="Aller au slide {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
