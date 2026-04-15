@props(['banner' => null])

@php $b = $banner; @endphp

<div class="space-y-4">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Titre *</label>
        <input type="text" name="title" id="title" value="{{ old('title', $b?->title) }}" required
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Image {{ $b ? '' : '*' }}</label>
        @if($b?->image)
            <img src="{{ Storage::url($b->image) }}" alt="{{ $b->title }}" class="mb-2 h-20 rounded">
        @endif
        <input type="file" name="image" id="image" accept="image/*" {{ $b ? '' : 'required' }}
               class="mt-1 w-full text-sm text-gray-600 file:mr-4 file:rounded file:border-0 file:bg-green-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-green-700 hover:file:bg-green-100">
        <p class="mt-1 text-xs text-gray-500">Max 2 Mo. Formats : jpg, png, gif, webp.</p>
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="link_url" class="block text-sm font-medium text-gray-700">Lien (URL)</label>
        <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $b?->link_url) }}"
               placeholder="https://..."
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        @error('link_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Position *</label>
            <select name="position" id="position" required
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">-- Choisir --</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->value }}" {{ old('position', $b?->position?->value) === $pos->value ? 'selected' : '' }}>{{ $pos->label() }}</option>
                @endforeach
            </select>
            @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div x-data="{ target: '{{ old('target_type', $b?->target_type?->value ?? 'all') }}' }">
            <label for="target_type" class="block text-sm font-medium text-gray-700">Cible *</label>
            <select name="target_type" id="target_type" required x-model="target"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                @foreach($targets as $tgt)
                    <option value="{{ $tgt->value }}">{{ $tgt->label() }}</option>
                @endforeach
            </select>
            @error('target_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

            <div x-show="target === 'school'" x-cloak class="mt-3">
                <label for="school_id" class="block text-sm font-medium text-gray-700">École *</label>
                <select name="school_id" id="school_id"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">-- Choisir --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id', $b?->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
                @error('school_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="starts_at" class="block text-sm font-medium text-gray-700">Date de début</label>
            <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $b?->starts_at?->format('Y-m-d')) }}"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="ends_at" class="block text-sm font-medium text-gray-700">Date de fin</label>
            <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at', $b?->ends_at?->format('Y-m-d')) }}"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $b?->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
        <label for="is_active" class="text-sm text-gray-700">Active</label>
    </div>
</div>
