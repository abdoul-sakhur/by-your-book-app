<x-admin-layout>
    <x-slot name="header">
        Validation des livres vendeurs
        @if($pendingCount > 0)
            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                {{ $pendingCount }} en attente
            </span>
        @endif
    </x-slot>

    <div class="p-6">

        {{-- Filtres --}}
        <form method="GET" class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Vendeur ou titre…"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                    <option value="">En attente (défaut)</option>
                    @foreach(\App\Enums\BookStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrer</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.seller-books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </form>

        {{-- Tableau --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre officiel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">École / Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($books as $book)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $book->seller->name }}</div>
                                <div class="text-xs text-gray-500">{{ $book->seller->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $book->officialBook->title }}</div>
                                <div class="text-xs text-gray-500">{{ $book->officialBook->subject->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $book->officialBook->grade->school->name ?? '' }}<br>
                                <span class="text-xs text-gray-400">{{ $book->officialBook->grade->name ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $book->condition->color() }}-100 text-{{ $book->condition->color() }}-800">
                                    {{ $book->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ number_format($book->price, 0, ',', ' ') }} F
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $book->status->color() }}-100 text-{{ $book->status->color() }}-800">
                                    {{ $book->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $book->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.seller-books.show', $book) }}"
                                   class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Examiner
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                Aucun livre à valider.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $books->links() }}</div>
    </div>
</x-admin-layout>
