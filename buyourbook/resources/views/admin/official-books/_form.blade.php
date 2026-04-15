{{-- Composant partiel : formulaire livre officiel --}}
@props(['officialBook' => null, 'schools', 'subjects', 'selectedGrade' => null])

<div class="space-y-6" x-data="{
    schoolId: '{{ old('school_id', $officialBook?->grade?->school_id ?? '') }}',
    grades: [],
    loadGrades() {
        if (!this.schoolId) { this.grades = []; return; }
        fetch('/admin/api/grades?school_id=' + this.schoolId)
            .then(r => r.json())
            .then(data => this.grades = data);
    }
}" x-init="if(schoolId) loadGrades()">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="school_id" class="block text-sm font-medium text-gray-700">École <span class="text-red-500">*</span></label>
            <select id="school_id" x-model="schoolId" @change="loadGrades()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">— Choisir une école —</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->district }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="grade_id" class="block text-sm font-medium text-gray-700">Classe <span class="text-red-500">*</span></label>
            <select name="grade_id" id="grade_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required>
                <option value="">— Choisir d'abord une école —</option>
                <template x-for="grade in grades" :key="grade.id">
                    <option :value="grade.id" x-text="grade.name + ' (' + grade.level + ' — ' + grade.academic_year + ')'"
                            :selected="grade.id == '{{ old('grade_id', $officialBook?->grade_id ?? $selectedGrade) }}'"></option>
                </template>
            </select>
            @error('grade_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="subject_id" class="block text-sm font-medium text-gray-700">Matière <span class="text-red-500">*</span></label>
        <select name="subject_id" id="subject_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                required>
            <option value="">— Choisir une matière —</option>
            @foreach($subjects as $id => $name)
                <option value="{{ $id }}" {{ old('subject_id', $officialBook?->subject_id) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Titre du livre <span class="text-red-500">*</span></label>
        <input type="text" name="title" id="title"
               value="{{ old('title', $officialBook?->title) }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
               required>
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="author" class="block text-sm font-medium text-gray-700">Auteur</label>
            <input type="text" name="author" id="author"
                   value="{{ old('author', $officialBook?->author) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('author') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
            <input type="text" name="isbn" id="isbn"
                   value="{{ old('isbn', $officialBook?->isbn) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('isbn') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="publisher" class="block text-sm font-medium text-gray-700">Éditeur</label>
            <input type="text" name="publisher" id="publisher"
                   value="{{ old('publisher', $officialBook?->publisher) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('publisher') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $officialBook?->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="cover_image" class="block text-sm font-medium text-gray-700">Image de couverture</label>
        <input type="file" name="cover_image" id="cover_image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
        @if($officialBook?->cover_image)
            <p class="mt-1 text-sm text-gray-500">Image actuelle : {{ basename($officialBook->cover_image) }}</p>
        @endif
        @error('cover_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               {{ old('is_active', $officialBook?->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
        <label for="is_active" class="text-sm font-medium text-gray-700">Livre actif (visible pour les vendeurs)</label>
    </div>
</div>
