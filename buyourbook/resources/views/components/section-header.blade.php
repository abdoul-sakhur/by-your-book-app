{{-- Section Header Component --}}
@props(['title', 'subtitle' => null, 'linkText' => null, 'linkUrl' => null])

<div class="flex items-end justify-between mb-8">
    <div>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $title }}</h2>
        @if($subtitle)
            <p class="mt-1 text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if($linkText && $linkUrl)
        <a href="{{ $linkUrl }}"
           class="hidden sm:inline-flex items-center gap-1 text-sm font-medium hover:underline shrink-0"
           style="color: var(--color-primary);">
            {{ $linkText }}
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    @endif
</div>
