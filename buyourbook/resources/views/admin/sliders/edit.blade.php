<x-admin-layout>
    <x-slot name="header">Modifier le slide : {{ $slider->title }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('admin.sliders._form', ['slider' => $slider])

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.sliders.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
