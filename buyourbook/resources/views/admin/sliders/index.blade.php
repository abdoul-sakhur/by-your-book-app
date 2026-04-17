<x-admin-layout>
    <x-slot name="header">Sliders publicitaires</x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Filtres --}}
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
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
                <a href="{{ route('admin.sliders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.sliders.create') }}" class="btn-primary">+ Nouveau slide</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CTA</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sliders as $slider)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $slider->order }}</td>
                        <td class="px-4 py-3">
                            @if($slider->image)
                                <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" class="h-10 w-16 object-cover rounded">
                            @else
                                <span class="inline-flex items-center justify-center h-10 w-16 rounded text-xs text-gray-400 bg-gray-100">Aucune</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $slider->title }}</p>
                            <p class="text-xs text-gray-400 line-clamp-1">{{ $slider->description }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $slider->cta_text }}
                            @if($slider->cta_link)
                                <span class="block text-xs text-gray-400 truncate max-w-[150px]">{{ $slider->cta_link }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($slider->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Actif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Inactif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm space-x-3">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-blue-600 hover:underline">Modifier</a>
                            <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce slide ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">Aucun slide pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sliders->links() }}</div>
</x-admin-layout>
