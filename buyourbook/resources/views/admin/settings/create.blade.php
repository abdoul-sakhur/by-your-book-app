<x-admin-layout>
    <x-slot name="header">Nouveau paramètre</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.settings.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="key" class="block text-sm font-medium text-gray-700">Clé *</label>
                        <input type="text" name="key" id="key" value="{{ old('key') }}" required
                               placeholder="ex: site.contact_email"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 font-mono">
                        <p class="mt-1 text-xs text-gray-500">Minuscules, chiffres, points et underscores uniquement.</p>
                        @error('key') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700">Valeur</label>
                        <textarea name="value" id="value" rows="3"
                                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('value') }}</textarea>
                        @error('value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="btn-primary">Créer</button>
                    <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
