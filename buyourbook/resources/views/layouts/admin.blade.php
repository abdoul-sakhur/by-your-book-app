<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin — {{ $title ?? config('app.name', 'BuyYourBook') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    </head>
    <body class="font-sans antialiased" style="background-color: var(--color-bg);">
        <div class="min-h-screen flex">

            <!-- Sidebar -->
            <aside class="w-64 min-h-screen text-white flex-shrink-0" style="background-color: var(--color-primary);">
                <div class="p-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white flex items-center gap-1">
                        <x-icon name="reader" class="w-5 h-5" /> BYB Admin
                    </a>
                </div>

                <nav class="mt-4 space-y-1 px-3">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="home" class="w-5 h-5" />
                        Tableau de bord
                    </a>

                    <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/50 uppercase tracking-wider">Fournitures</p>

                    <a href="{{ route('admin.schools.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.schools.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="cube" class="w-5 h-5" />
                        Écoles
                    </a>

                    <a href="{{ route('admin.grades.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.grades.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="reader" class="w-5 h-5" />
                        Classes
                    </a>

                    <a href="{{ route('admin.subjects.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.subjects.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="lightning-bolt" class="w-5 h-5" />
                        Matières
                    </a>

                    <a href="{{ route('admin.official-books.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.official-books.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="reader" class="w-5 h-5" />
                        Livres officiels
                    </a>

                    <p class="px-3 pt-4 pb-1 text-xs font-semibold text-white/50 uppercase tracking-wider">Gestion</p>

                    <a href="{{ route('admin.seller-books.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.seller-books.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="check-circled" class="w-5 h-5" />
                        Validation livres
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.orders.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="archive" class="w-5 h-5" />
                        Commandes
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="person" class="w-5 h-5" />
                        Utilisateurs
                    </a>

                    <a href="{{ route('admin.relay-points.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.relay-points.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="drawing-pin" class="w-5 h-5" />
                        Points relais
                    </a>

                    <a href="{{ route('admin.banners.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.banners.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="image" class="w-5 h-5" />
                        Bannières
                    </a>

                    <a href="{{ route('admin.sliders.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.sliders.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="image" class="w-5 h-5" />
                        Sliders pub
                    </a>

                    <a href="{{ route('admin.popups.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.popups.*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="info-circled" class="w-5 h-5" />
                        Popups pub
                    </a>

                    <a href="{{ route('admin.settings.general') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.settings.general*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <x-icon name="gear" class="w-5 h-5" />
                        Paramètres généraux
                    </a>
                </nav>

                <!-- Lien retour site -->
                <div class="mt-8 px-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-sm text-white/50 hover:text-white">
                        ← Retour au site
                    </a>
                </div>
            </aside>

            <!-- Contenu principal -->
            <div class="flex-1 flex flex-col">
                <!-- Top bar admin -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="flex items-center justify-between px-6 py-4">
                        @isset($header)
                            <h1 class="text-xl font-semibold text-gray-800">{{ $header }}</h1>
                        @endisset

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                                {{ Auth::user()->name }}
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Flash messages -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms
                         class="mx-6 mt-4">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex justify-between items-center">
                            {{ session('success') }}
                            <button @click="show = false" class="text-green-700 hover:text-green-900">&times;</button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" x-transition.opacity.duration.500ms
                         class="mx-6 mt-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded flex justify-between items-center">
                            {{ session('error') }}
                            <button @click="show = false" class="text-red-700 hover:text-red-900">&times;</button>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
