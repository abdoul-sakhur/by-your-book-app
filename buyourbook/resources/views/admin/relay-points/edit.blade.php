<x-admin-layout>
    <x-slot name="header">Modifier le point relais : {{ $relayPoint->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.relay-points.update', $relayPoint) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.relay-points._form', ['relayPoint' => $relayPoint])

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.relay-points.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
