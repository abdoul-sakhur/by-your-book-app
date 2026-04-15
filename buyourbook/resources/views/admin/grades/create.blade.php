<x-admin-layout>
    <x-slot name="header">Ajouter une classe</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.grades.store') }}">
                @csrf
                @include('admin.grades._form', ['schools' => $schools, 'selectedSchool' => $selectedSchool])

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="btn-primary px-6 py-2">Créer la classe</button>
                    <a href="{{ route('admin.grades.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
