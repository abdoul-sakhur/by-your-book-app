{{-- Composant partiel : formulaire école (utilisé par create & edit) --}}
@props(['school' => null])

<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nom de l'école <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $school?->name) }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
               required>
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700">Ville <span class="text-red-500">*</span></label>
            <input type="text" name="city" id="city"
                   value="{{ old('city', $school?->city) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                   placeholder="Ex: Abidjan"
                   required>
            @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="district" class="block text-sm font-medium text-gray-700">Commune / Quartier <span class="text-red-500">*</span></label>
            <input type="text" name="district" id="district"
                   value="{{ old('district', $school?->district) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                   placeholder="Ex: Cocody"
                   required>
            @error('district') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="logo" class="block text-sm font-medium text-gray-700">Logo (optionnel)</label>
        <input type="file" name="logo" id="logo" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
        @if($school?->logo)
            <p class="mt-1 text-sm text-gray-500">Logo actuel : {{ basename($school->logo) }}</p>
        @endif
        @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $school?->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
        <label for="is_active" class="text-sm font-medium text-gray-700">École active</label>
    </div>
</div>
