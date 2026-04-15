<x-admin-layout>
    <x-slot name="header">Modifier : {{ $setting->key }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.settings.update', $setting) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Clé</label>
                        <p class="mt-1 text-sm font-mono font-medium text-gray-900 bg-gray-50 rounded-md px-3 py-2">{{ $setting->key }}</p>
                    </div>

                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700">Valeur</label>
                        <textarea name="value" id="value" rows="4"
                                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('value', $setting->value) }}</textarea>
                        @error('value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn-primary">Mettre à jour</button>
                    <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
