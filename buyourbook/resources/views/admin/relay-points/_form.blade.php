@props(['relayPoint' => null])

@php $rp = $relayPoint; @endphp

<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nom *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $rp?->name) }}" required
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">Adresse *</label>
        <textarea name="address" id="address" rows="2" required
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('address', $rp?->address) }}</textarea>
        @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700">Ville *</label>
            <input type="text" name="city" id="city" value="{{ old('city', $rp?->city) }}" required
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="district" class="block text-sm font-medium text-gray-700">Commune</label>
            <input type="text" name="district" id="district" value="{{ old('district', $rp?->district) }}"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('district') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $rp?->contact_phone) }}" required
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('contact_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="schedule" class="block text-sm font-medium text-gray-700">Horaires</label>
            <input type="text" name="schedule" id="schedule" value="{{ old('schedule', $rp?->schedule) }}"
                   placeholder="Lun-Sam 8h-18h"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('schedule') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $rp?->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
        <label for="is_active" class="text-sm text-gray-700">Actif</label>
    </div>
</div>
