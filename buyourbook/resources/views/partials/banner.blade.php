@props(['position'])

@php
    use App\Enums\BannerPosition;
    use App\Models\Banner;

    $bannerPosition = BannerPosition::from($position);
    $banners = Banner::visible()->atPosition($bannerPosition)->get();
@endphp

@if($banners->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        @if($banners->count() === 1)
            @php $banner = $banners->first(); @endphp
            <a href="{{ $banner->link_url ?? '#' }}" class="block" @if($banner->link_url) target="_blank" rel="noopener" @endif>
                <img src="{{ Storage::url($banner->image) }}"
                     alt="{{ $banner->title }}"
                     class="w-full rounded-lg shadow-sm object-cover max-h-64">
            </a>
        @else
            <div x-data="{ current: 0, total: {{ $banners->count() }} }"
                 x-init="setInterval(() => current = (current + 1) % total, 5000)"
                 class="relative overflow-hidden rounded-lg shadow-sm">
                @foreach($banners as $i => $banner)
                    <a href="{{ $banner->link_url ?? '#' }}"
                       x-show="current === {{ $i }}"
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="opacity-0"
                       x-transition:enter-end="opacity-100"
                       x-transition:leave="transition ease-in duration-200"
                       x-transition:leave-start="opacity-100"
                       x-transition:leave-end="opacity-0"
                       class="block"
                       @if($banner->link_url) target="_blank" rel="noopener" @endif>
                        <img src="{{ Storage::url($banner->image) }}"
                             alt="{{ $banner->title }}"
                             class="w-full object-cover max-h-64">
                    </a>
                @endforeach

                {{-- Indicateurs --}}
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
                    @foreach($banners as $i => $banner)
                        <button @click="current = {{ $i }}"
                                :class="current === {{ $i }} ? 'bg-white' : 'bg-white/50'"
                                class="w-2.5 h-2.5 rounded-full transition"></button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
