<x-admin-layout>
    <x-slot name="header">Nouveau slide</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.sliders._form')

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn-primary">Créer</button>
                    <a href="{{ route('admin.sliders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
