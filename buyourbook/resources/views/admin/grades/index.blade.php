<x-admin-layout>
    <x-slot name="header">Classes</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
            <select name="school_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Toutes les écoles</option>
                @foreach($schools as $id => $name)
                    <option value="{{ $id }}" {{ request('school_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="academic_year" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Toutes les années</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('academic_year') === $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm px-4 py-2">Filtrer</button>
            @if(request()->hasAny(['search', 'school_id', 'academic_year']))
                <a href="{{ route('admin.grades.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </form>

        <a href="{{ route('admin.grades.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle classe
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">École</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Année</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Livres</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($grades as $grade)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $grade->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $grade->school->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $grade->level }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500">{{ $grade->academic_year }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500">{{ $grade->official_books_count }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.grades.edit', $grade) }}" class="text-blue-600 hover:text-blue-800">Modifier</a>
                            <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}" class="inline"
                                  onsubmit="return confirm('Supprimer cette classe et ses livres associés ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">Aucune classe trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $grades->links() }}
    </div>
</x-admin-layout>
