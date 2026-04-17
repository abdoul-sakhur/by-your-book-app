{{-- Slider form partial --}}
<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Titre <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title" value="{{ old('title', $slider->title ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $slider->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Image {{ isset($slider) ? '' : '*' }}</label>
        @if(isset($slider) && $slider->image)
            <img src="{{ Storage::url($slider->image) }}" alt="" class="mt-1 h-20 w-40 object-cover rounded mb-2">
        @endif
        <input type="file" name="image" id="image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
               {{ isset($slider) ? '' : 'required' }}>
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="cta_text" class="block text-sm font-medium text-gray-700">Texte du bouton</label>
            <input type="text" name="cta_text" id="cta_text" value="{{ old('cta_text', $slider->cta_text ?? 'En savoir plus') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('cta_text') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="cta_link" class="block text-sm font-medium text-gray-700">Lien du bouton</label>
            <input type="text" name="cta_link" id="cta_link" value="{{ old('cta_link', $slider->cta_link ?? '') }}"
                   placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('cta_link') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="order" class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
            <input type="number" name="order" id="order" value="{{ old('order', $slider->order ?? 0) }}" min="0"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center pt-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                       {{ old('is_active', $slider->is_active ?? true) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Actif</span>
            </label>
        </div>
    </div>
</div>
