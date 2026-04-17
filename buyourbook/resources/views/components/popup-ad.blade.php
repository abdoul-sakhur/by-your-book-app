{{-- Popup publicitaire — Alpine.js + sessionStorage --}}
@props(['popup'])

@if($popup)
<div x-data="{
        open: false,
        init() {
            const key = 'popup_seen_{{ $popup->id }}';
            if (!sessionStorage.getItem(key)) {
                this.open = true;
                sessionStorage.setItem(key, '1');
            }
        }
     }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;"
     @keydown.escape.window="open = false">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>

    {{-- Modal --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-white rounded-2xl shadow-2xl overflow-hidden max-w-lg w-full">

        {{-- Close button --}}
        <button @click="open = false"
                class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/20 text-white hover:bg-black/40 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        @if($popup->image)
            <img src="{{ Storage::url($popup->image) }}" alt="{{ $popup->title }}" class="w-full h-48 sm:h-56 object-cover">
        @endif

        <div class="p-6 sm:p-8 text-center">
            @if($popup->title)
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">{{ $popup->title }}</h3>
            @endif
            @if($popup->message)
                <p class="mt-3 text-gray-600">{{ $popup->message }}</p>
            @endif
            @if($popup->cta_link)
                <a href="{{ $popup->cta_link }}"
                   class="mt-6 inline-flex items-center px-6 py-3 text-sm font-semibold rounded-lg text-white transition hover:opacity-90"
                   style="background-color: var(--color-primary);">
                    {{ $popup->cta_text ?? 'En savoir plus' }}
                </a>
            @endif
            <button @click="open = false" class="mt-3 block mx-auto text-sm text-gray-400 hover:text-gray-600 transition">
                Non merci, fermer
            </button>
        </div>
    </div>
</div>
@endif
