<x-admin-layout>
    <x-slot name="header">Ajouter une matière</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom de la matière <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Ex: Mathématiques"
                           required>
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="btn-primary px-6 py-2">Créer la matière</button>
                    <a href="{{ route('admin.subjects.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
