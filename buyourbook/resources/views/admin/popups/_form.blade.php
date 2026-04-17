{{-- Popup form partial --}}
<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Titre <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title', $popup->title ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
        <textarea name="message" id="message" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('message', $popup->message ?? '') }}</textarea>
        @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Image (facultative)</label>
        @if(isset($popup) && $popup->image)
            <img src="{{ Storage::url($popup->image) }}" alt="" class="mt-1 h-20 w-40 object-cover rounded mb-2">
        @endif
        <input type="file" name="image" id="image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="cta_text" class="block text-sm font-medium text-gray-700">Texte du bouton</label>
            <input type="text" name="cta_text" id="cta_text" value="{{ old('cta_text', $popup->cta_text ?? 'En savoir plus') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('cta_text') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="cta_link" class="block text-sm font-medium text-gray-700">Lien du bouton</label>
            <input type="text" name="cta_link" id="cta_link" value="{{ old('cta_link', $popup->cta_link ?? '') }}"
                   placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('cta_link') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', isset($popup) && $popup->start_date ? $popup->start_date->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', isset($popup) && $popup->end_date ? $popup->end_date->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                   {{ old('is_active', $popup->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm text-gray-700">Active</span>
        </label>
    </div>
</div>
