<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription ?? 'BuyYourBook — Achetez et vendez des livres scolaires d\'occasion à Abidjan, Côte d\'Ivoire.' }}">

        <title>{{ $title ?? config('app.name', 'BuyYourBook') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Classes Tailwind absentes du build CSS (Node.js non disponible pour rebuild) --}}
        <style>
            /* Opacités couleurs */
            .bg-black\/50 { background-color: rgba(0,0,0,0.5); }
            .bg-black\/20 { background-color: rgba(0,0,0,0.2); }
            .bg-white\/5 { background-color: rgba(255,255,255,0.05); }
            .bg-white\/20 { background-color: rgba(255,255,255,0.2); }
            .bg-white\/40 { background-color: rgba(255,255,255,0.4); }
            .text-white\/80 { color: rgba(255,255,255,0.8); }
            .text-white\/90 { color: rgba(255,255,255,0.9); }
            .text-white\/75 { color: rgba(255,255,255,0.75); }
            .text-white\/60 { color: rgba(255,255,255,0.6); }
            .border-white\/30 { border-color: rgba(255,255,255,0.3); }
            /* Gradients */
            .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
            .bg-gradient-to-t { background-image: linear-gradient(to top, var(--tw-gradient-stops)); }
            .from-black\/70 { --tw-gradient-from: rgba(0,0,0,0.7); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, transparent); }
            .from-black\/60 { --tw-gradient-from: rgba(0,0,0,0.6); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, transparent); }
            .via-black\/40 { --tw-gradient-stops: var(--tw-gradient-from), rgba(0,0,0,0.4), var(--tw-gradient-to, transparent); }
            .to-black\/20 { --tw-gradient-to: rgba(0,0,0,0.2); }
            .via-transparent { --tw-gradient-stops: var(--tw-gradient-from), transparent, var(--tw-gradient-to, transparent); }
            /* Transforms / Hover effects */
            .hover\:scale-105:hover { transform: scale(1.05); }
            .hover\:scale-110:hover { transform: scale(1.1); }
            .active\:scale-95:active { transform: scale(0.95); }
            .hover\:bg-white\/40:hover { background-color: rgba(255,255,255,0.4); }
            .hover\:bg-black\/40:hover { background-color: rgba(0,0,0,0.4); }
            /* Shadows */
            .drop-shadow-lg { filter: drop-shadow(0 10px 8px rgba(0,0,0,0.04)) drop-shadow(0 4px 3px rgba(0,0,0,0.1)); }
            .drop-shadow-md { filter: drop-shadow(0 4px 3px rgba(0,0,0,0.07)) drop-shadow(0 2px 2px rgba(0,0,0,0.06)); }
            /* Object */
            .object-center { object-position: center; }
        </style>

        @stack('styles')
    </head>
    <body class="font-sans antialiased" style="background-color: var(--color-bg);">
        @php
            $errors = $errors ?? new Illuminate\Support\ViewErrorBag;
        @endphp
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms
                     class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex justify-between items-center">
                        {{ session('success') }}
                        <button @click="show = false" class="text-green-700 hover:text-green-900">&times;</button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" x-transition.opacity.duration.500ms
                     class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex justify-between items-center">
                        {{ session('error') }}
                        <button @click="show = false" class="text-red-700 hover:text-red-900">&times;</button>
                    </div>
                </div>
            @endif

            @if($errors->any() && !request()->routeIs('checkout.*'))
                <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
                     class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex justify-between items-center">
                        <div>
                            <p class="font-medium">Une erreur est survenue :</p>
                            <ul class="list-disc list-inside text-sm mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button @click="show = false" class="text-red-700 hover:text-red-900">&times;</button>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
        @stack('scripts')

        @php $tawktoId = \App\Models\Setting::get('tawkto_widget_id', ''); @endphp
        @if($tawktoId)
        <script type="text/javascript">
            var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
            (function(){
                var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                s1.async=true;
                s1.src='https://embed.tawk.to/'+{{ json_encode($tawktoId) }};
                s1.charset='UTF-8';
                s1.setAttribute('crossorigin','*');
                s0.parentNode.insertBefore(s1,s0);
            })();
        </script>
        @endif
    </body>
</html>
