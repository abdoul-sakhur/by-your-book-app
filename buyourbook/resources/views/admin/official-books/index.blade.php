<x-admin-layout>
    <x-slot name="header">Livres officiels</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Titre, auteur, ISBN..."
                   class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
            <select name="school_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Toutes les écoles</option>
                @foreach($schools as $id => $name)
                    <option value="{{ $id }}" {{ request('school_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="subject_id" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Toutes les matières</option>
                @foreach($subjects as $id => $name)
                    <option value="{{ $id }}" {{ request('subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm px-4 py-2">Filtrer</button>
            @if(request()->hasAny(['search', 'school_id', 'subject_id']))
                <a href="{{ route('admin.official-books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </form>

        <a href="{{ route('admin.official-books.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau livre
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matière</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">École</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Offres</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($books as $book)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                            @if($book->author)
                                <div class="text-xs text-gray-400">{{ $book->author }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $book->subject->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $book->grade->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $book->grade->school->name }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-500">{{ $book->seller_books_count }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $book->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $book->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.official-books.edit', $book) }}" class="text-blue-600 hover:text-blue-800">Modifier</a>
                            <form method="POST" action="{{ route('admin.official-books.destroy', $book) }}" class="inline"
                                  onsubmit="return confirm('Supprimer ce livre officiel ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">Aucun livre officiel trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $books->links() }}
    </div>
</x-admin-layout>
