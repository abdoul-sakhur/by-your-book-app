<x-admin-layout>
    <x-slot name="header">Paramètres</x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.settings.create') }}" class="btn-primary">+ Nouveau paramètre</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clé</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($settings as $setting)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-mono font-medium text-gray-900">{{ $setting->key }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-md truncate">{{ Str::limit($setting->value, 100) }}</td>
                        <td class="px-4 py-3 text-right text-sm space-x-3">
                            <a href="{{ route('admin.settings.edit', $setting) }}" class="text-blue-600 hover:text-blue-800">Modifier</a>
                            <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce paramètre ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Aucun paramètre défini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
