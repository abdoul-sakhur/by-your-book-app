<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes livres en vente</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- En-tête avec bouton --}}
            <div class="flex items-center justify-between mb-6">
                <div></div>
                <a href="{{ route('seller.books.create') }}" class="btn-primary">
                    + Soumettre un livre
                </a>
            </div>

            {{-- Filtres --}}
            <form method="GET" class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Titre du livre…"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                        <option value="">Tous</option>
                        @foreach(\App\Enums\BookStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filtrer</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('seller.books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
                @endif
            </form>

            {{-- Tableau --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">École / Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qté</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($books as $book)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $book->officialBook->title }}</div>
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
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $book->quantity }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $book->status->color() }}-100 text-{{ $book->status->color() }}-800">
                                        {{ $book->status->label() }}
                                    </span>
                                    @if($book->status === \App\Enums\BookStatus::Rejected && $book->rejection_reason)
                                        <p class="text-xs text-red-600 mt-1">{{ $book->rejection_reason }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    @if(in_array($book->status, [\App\Enums\BookStatus::Pending, \App\Enums\BookStatus::Rejected]))
                                        <a href="{{ route('seller.books.edit', $book) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                    @endif
                                    @unless($book->orderItems()->exists())
                                        <form action="{{ route('seller.books.destroy', $book) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer ce livre ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    Aucun livre soumis. <a href="{{ route('seller.books.create') }}" class="text-indigo-600 hover:underline">Soumettre votre premier livre</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $books->links() }}</div>
        </div>
    </div>
</x-app-layout>
