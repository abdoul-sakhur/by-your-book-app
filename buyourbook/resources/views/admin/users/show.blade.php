<x-admin-layout>
    <x-slot name="header">Utilisateur : {{ $user->name }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour aux utilisateurs</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Infos utilisateur --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nom</dt>
                    <dd class="font-medium text-gray-900">{{ $user->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-900">{{ $user->email }}</dd>
                </div>
                @if($user->phone)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Téléphone</dt>
                    <dd class="text-gray-900">{{ $user->phone }}</dd>
                </div>
                @endif
                @if($user->address)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Adresse</dt>
                    <dd class="text-gray-900">{{ $user->address }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Inscrit le</dt>
                    <dd class="text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Livres soumis</dt>
                    <dd class="font-semibold text-gray-900">{{ $user->seller_books_count }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Commandes</dt>
                    <dd class="font-semibold text-gray-900">{{ $user->orders_count }}</dd>
                </div>
            </dl>
        </div>

        {{-- Actions --}}
        <div class="space-y-6">
            {{-- Changer rôle --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Rôle</h2>
                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="flex items-center gap-3">
                    @csrf
                    @method('PATCH')
                    <select name="role" class="rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        @foreach(\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}" {{ $user->role === $role ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-sm px-4 py-2">Modifier</button>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $user->role === \App\Enums\UserRole::Admin ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $user->role === \App\Enums\UserRole::Seller ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $user->role === \App\Enums\UserRole::Buyer ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ $user->role->label() }}
                    </span>
                </form>
            </div>

            {{-- Activer / Désactiver --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Statut du compte</h2>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm font-medium px-4 py-2 rounded-lg border {{ $user->is_active ? 'border-red-300 text-red-600 hover:bg-red-50' : 'border-green-300 text-green-600 hover:bg-green-50' }}">
                                {{ $user->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-gray-400">C'est votre compte</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
