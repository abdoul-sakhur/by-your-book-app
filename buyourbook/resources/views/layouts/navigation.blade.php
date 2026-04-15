<nav x-data="{ open: false, cartCount: 0 }"
     x-init="fetch('{{ route('cart.count') }}').then(r => r.json()).then(d => cartCount = d.count)"
     class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Gauche : Logo + Liens -->
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-xl font-bold" style="color: var(--color-primary);">📚 BuyYourBook</span>
                </a>

                <!-- Liens principaux (desktop) -->
                <div class="hidden sm:flex sm:ml-10 sm:space-x-6">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('home') ? 'border-[var(--color-primary)] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Accueil
                    </a>

                    <a href="{{ route('catalog.schools') }}"
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('catalog.*') ? 'border-[var(--color-primary)] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Catalogue
                    </a>

                    {{-- Recherche rapide --}}
                    <form action="{{ route('catalog.search') }}" method="GET" class="inline-flex items-center">
                        <div class="relative">
                            <input type="text" name="q" placeholder="Rechercher…" value="{{ request('q') }}"
                                   class="w-40 lg:w-56 rounded-full border-gray-300 text-sm pl-8 pr-3 py-1.5 focus:border-green-500 focus:ring-green-500 focus:w-64 transition-all">
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('admin.*') ? 'border-[var(--color-primary)] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Administration
                            </a>
                        @endif

                        @if(auth()->user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 {{ request()->routeIs('seller.*') ? 'border-[var(--color-primary)] text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Mes ventes
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Droite : Panier + Auth -->
            <div class="flex items-center gap-4">
                <!-- Panier icon avec badge (Alpine.js) -->
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-500 hover:text-gray-700" title="Mon panier">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-show="cartCount > 0"
                          x-text="cartCount"
                          class="absolute -top-1 -right-1 text-xs font-bold text-white rounded-full w-5 h-5 flex items-center justify-center"
                          style="background-color: var(--color-secondary); display: none;">
                    </span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-primary text-sm !py-2 !px-4">Inscription</a>
                @else
                    <!-- Dropdown utilisateur (desktop) -->
                    <div class="hidden sm:block relative" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 rounded-md focus:outline-none">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div x-show="userOpen" @click.away="userOpen = false" x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes commandes</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">❤️ Mes favoris</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest

                <!-- Hamburger (mobile) -->
                <button @click="open = !open" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-200">
        <div class="py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">Accueil</a>
            <a href="{{ route('catalog.schools') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">Catalogue</a>
            <a href="{{ route('cart.index') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">Mon panier</a>

            @auth
                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">Mes commandes</a>
                <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">❤️ Mes favoris</a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">Administration</a>
                @endif

                @if(auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">Mes ventes</a>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base text-gray-600 hover:bg-gray-50">Mon profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-base text-gray-600 hover:bg-gray-50">Déconnexion</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
