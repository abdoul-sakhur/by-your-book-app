<x-admin-layout>
    <x-slot name="header">Modifier la bannière : {{ $banner->title }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.banners._form', [
                    'banner' => $banner,
                    'positions' => $positions,
                    'targets' => $targets,
                    'schools' => $schools,
                ])

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.banners.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
