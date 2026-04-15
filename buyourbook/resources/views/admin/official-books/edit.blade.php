<x-admin-layout>
    <x-slot name="header">Modifier — {{ $officialBook->title }}</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.official-books.update', $officialBook) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('admin.official-books._form', ['officialBook' => $officialBook, 'schools' => $schools, 'subjects' => $subjects])

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="btn-primary px-6 py-2">Enregistrer</button>
                    <a href="{{ route('admin.official-books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
