<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Espace vendeur</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Welcome --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Bienvenue, {{ auth()->user()->name }} !</h3>
                <p class="mt-1 text-gray-600">
                    Gérez vos livres en vente depuis cet espace.
                </p>
            </div>

            {{-- Stats livres --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-500">Total livres</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalBooks }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-yellow-600">En attente</p>
                    <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $pendingBooks }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-green-600">Approuvés</p>
                    <p class="text-2xl font-bold text-green-700 mt-1">{{ $approvedBooks }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5">
                    <p class="text-sm font-medium text-red-600">Refusés</p>
                    <p class="text-2xl font-bold text-red-700 mt-1">{{ $rejectedBooks }}</p>
                </div>
            </div>

            {{-- Stats ventes --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4" style="border-left-color: var(--color-primary);">
                    <p class="text-sm font-medium text-gray-500">Commandes reçues</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4" style="border-left-color: var(--color-secondary);">
                    <p class="text-sm font-medium text-gray-500">Articles vendus</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalItemsSold }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-5 border-l-4" style="border-left-color: var(--color-accent);">
                    <p class="text-sm font-medium text-gray-500">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalSales, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-400">FCFA</span></p>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('seller.books.create') }}"
                   class="flex items-center gap-4 bg-white rounded-lg shadow-sm p-5 hover:bg-gray-50 transition">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" style="background-color: var(--color-primary);">
                        <x-icon name="plus" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Soumettre un livre</p>
                        <p class="text-sm text-gray-500">Proposez un nouveau livre à la vente</p>
                    </div>
                </a>

                <a href="{{ route('seller.books.index') }}"
                   class="flex items-center gap-4 bg-white rounded-lg shadow-sm p-5 hover:bg-gray-50 transition">
                    <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <x-icon name="clipboard" class="w-6 h-6 text-indigo-600" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Mes livres</p>
                        <p class="text-sm text-gray-500">Consulter et gérer vos annonces</p>
                    </div>
                </a>

                <a href="{{ route('seller.orders.index') }}"
                   class="flex items-center gap-4 bg-white rounded-lg shadow-sm p-5 hover:bg-gray-50 transition">
                    <div class="flex-shrink-0 w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                        <x-icon name="archive" class="w-6 h-6 text-amber-600" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Mes ventes</p>
                        <p class="text-sm text-gray-500">Suivre les commandes de vos livres</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Mes ventes</p>
                        <p class="text-sm text-gray-500">Suivre les commandes de vos livres</p>
                    </div>
                </a>
            </div>
