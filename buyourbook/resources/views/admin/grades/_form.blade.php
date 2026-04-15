{{-- Composant partiel : formulaire classe --}}
@props(['grade' => null, 'schools', 'selectedSchool' => null])

<div class="space-y-6">
    <div>
        <label for="school_id" class="block text-sm font-medium text-gray-700">École <span class="text-red-500">*</span></label>
        <select name="school_id" id="school_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                required>
            <option value="">— Choisir une école —</option>
            @foreach($schools as $id => $name)
                <option value="{{ $id }}" {{ old('school_id', $grade?->school_id ?? $selectedSchool) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('school_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nom de la classe <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $grade?->name) }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
               placeholder="Ex: 6ème A"
               required>
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="level" class="block text-sm font-medium text-gray-700">Niveau <span class="text-red-500">*</span></label>
            <select name="level" id="level"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required>
                <option value="">— Choisir —</option>
                @foreach(['CP1','CP2','CE1','CE2','CM1','CM2','6ème','5ème','4ème','3ème','2nde','1ère','Tle'] as $lvl)
                    <option value="{{ $lvl }}" {{ old('level', $grade?->level) === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                @endforeach
            </select>
            @error('level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="academic_year" class="block text-sm font-medium text-gray-700">Année scolaire <span class="text-red-500">*</span></label>
            <input type="text" name="academic_year" id="academic_year"
                   value="{{ old('academic_year', $grade?->academic_year ?? '2025-2026') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                   placeholder="2025-2026"
                   required>
            @error('academic_year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
