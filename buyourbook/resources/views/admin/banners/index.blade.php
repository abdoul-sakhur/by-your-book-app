<x-admin-layout>
    <x-slot name="header">Bannières</x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Filtres --}}
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                <select name="position" id="position" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Toutes</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->value }}" {{ request('position') === $pos->value ? 'selected' : '' }}>{{ $pos->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="active" class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="active" id="active" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Tous</option>
                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="{{ route('admin.banners.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.banners.create') }}" class="btn-primary">+ Nouvelle bannière</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cible</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($banners as $banner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <img src="{{ Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="h-10 w-16 object-cover rounded">
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $banner->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $banner->position->label() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $banner->target_type->label() }}
                            @if($banner->school)
                                <span class="block text-xs text-gray-400">{{ $banner->school->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($banner->starts_at || $banner->ends_at)
                                {{ $banner->starts_at?->format('d/m/Y') ?? '…' }} → {{ $banner->ends_at?->format('d/m/Y') ?? '…' }}
                            @else
                                <span class="text-gray-400">Permanente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($banner->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Actif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Inactif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm space-x-3">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-blue-600 hover:text-blue-800">Modifier</a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette bannière ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Aucune bannière trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $banners->links() }}</div>
</x-admin-layout>
