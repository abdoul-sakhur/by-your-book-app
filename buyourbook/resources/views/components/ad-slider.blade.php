{{-- Ad Slider / Hero Carousel --}}
@props(['slides' => []])

<section class="relative overflow-hidden" x-data="{
    current: 0,
    total: {{ count($slides) }},
    autoplay: null,
    init() {
        this.autoplay = setInterval(() => {
            this.current = (this.current + 1) % this.total;
        }, 5000);
    },
    next() {
        this.current = (this.current + 1) % this.total;
        this.resetAutoplay();
    },
    prev() {
        this.current = (this.current - 1 + this.total) % this.total;
        this.resetAutoplay();
    },
    goTo(index) {
        this.current = index;
        this.resetAutoplay();
    },
    resetAutoplay() {
        clearInterval(this.autoplay);
        this.autoplay = setInterval(() => {
            this.current = (this.current + 1) % this.total;
        }, 5000);
    }
}">
    <div class="relative min-h-[400px] sm:min-h-[450px] lg:min-h-[500px]">
        @foreach($slides as $index => $slide)
            <div x-show="current === {{ $index }}"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform -translate-x-8"
                 class="absolute inset-0">
                {{-- Background --}}
                @if($slide->image)
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $slide->image }}');">
                        <div class="absolute inset-0 bg-black/50"></div>
                    </div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-r {{ $slide->bg_color ?? 'from-emerald-700 to-teal-600' }}"></div>
                @endif

                {{-- Content --}}
                <div class="relative z-10 flex items-center justify-center h-full">
                    <div class="max-w-4xl mx-auto px-6 sm:px-8 text-center">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">
                            {{ $slide->title }}
                        </h2>
                        <p class="mt-4 text-lg sm:text-xl text-white/85 max-w-2xl mx-auto">
                            {{ $slide->description }}
                        </p>
                        @if(!empty($slide->cta_link))
                            <a href="{{ $slide->cta_link }}"
                               class="mt-8 inline-flex items-center px-8 py-3.5 text-base font-semibold rounded-lg bg-white shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
                               style="color: var(--color-primary);">
                                {{ $slide->cta_text ?? 'En savoir plus' }}
                                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Navigation Arrows --}}
    @if(count($slides) > 1)
        <button @click="prev()"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-300">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="next()"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-300">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2.5">
            @foreach($slides as $index => $slide)
                <button @click="goTo({{ $index }})"
                        :class="current === {{ $index }} ? 'w-8 bg-white' : 'w-2.5 bg-white/50 hover:bg-white/70'"
                        class="h-2.5 rounded-full transition-all duration-300"></button>
            @endforeach
        </div>
    @endif

    {{-- Decorative wave --}}
    <div class="absolute bottom-0 left-0 right-0 z-10">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,36L60,33C120,30,240,24,360,26C480,28,600,38,720,40C840,42,960,36,1080,30C1200,24,1320,18,1380,15L1440,12L1440,60L0,60Z" fill="var(--color-bg)"/>
        </svg>
    </div>
</section>
