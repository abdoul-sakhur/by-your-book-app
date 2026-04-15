<x-admin-layout>
    <x-slot name="header">Écoles</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <!-- Filtres -->
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
            <select name="city" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Toutes les villes</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm px-4 py-2">Filtrer</button>
            @if(request()->hasAny(['search', 'city']))
                <a href="{{ route('admin.schools.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </form>

        <a href="{{ route('admin.schools.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm">
            <x-icon name="plus" class="w-4 h-4" />
            Nouvelle école
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ville</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commune</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Classes</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($schools as $school)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $school->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $school->city }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $school->district }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500">{{ $school->grades_count }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $school->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $school->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.schools.edit', $school) }}" class="text-blue-600 hover:text-blue-800">Modifier</a>
                            <form method="POST" action="{{ route('admin.schools.destroy', $school) }}" class="inline"
                                  onsubmit="return confirm('Supprimer cette école ? Les classes et livres associés seront aussi supprimés.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">Aucune école trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $schools->links() }}
    </div>
</x-admin-layout>
