{{-- Shared form partial for seller book create/edit --}}
<div x-data="{
    schoolId: '{{ old('school_id', isset($book) ? $book->officialBook->grade->school_id : '') }}',
    gradeId: '{{ old('grade_id', isset($book) ? $book->officialBook->grade_id : '') }}',
    officialBookId: '{{ old('official_book_id', isset($book) ? $book->official_book_id : '') }}',
    grades: [],
    officialBooks: [],

    async loadGrades() {
        if (!this.schoolId) { this.grades = []; this.gradeId = ''; this.officialBooks = []; this.officialBookId = ''; return; }
        const res = await fetch(`/seller/api/grades?school_id=${this.schoolId}`);
        this.grades = await res.json();
        if (!this.grades.find(g => g.id == this.gradeId)) { this.gradeId = ''; this.officialBooks = []; this.officialBookId = ''; }
        else { this.loadBooks(); }
    },
    async loadBooks() {
        if (!this.gradeId) { this.officialBooks = []; this.officialBookId = ''; return; }
        const res = await fetch(`/seller/api/official-books?grade_id=${this.gradeId}`);
        this.officialBooks = await res.json();
        if (!this.officialBooks.find(b => b.id == this.officialBookId)) { this.officialBookId = ''; }
    },

    init() {
        if (this.schoolId) this.loadGrades();
    }
}" class="space-y-6">

    {{-- École --}}
    <div>
        <label for="school_id" class="block text-sm font-medium text-gray-700 mb-1">École <span class="text-red-500">*</span></label>
        <select id="school_id" x-model="schoolId" @change="loadGrades()"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
            <option value="">— Sélectionner une école —</option>
            @foreach($schools as $school)
                <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->city }})</option>
            @endforeach
        </select>
    </div>

    {{-- Classe --}}
    <div>
        <label for="grade_id" class="block text-sm font-medium text-gray-700 mb-1">Classe <span class="text-red-500">*</span></label>
        <select id="grade_id" x-model="gradeId" @change="loadBooks()"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
            <option value="">— Sélectionner une classe —</option>
            <template x-for="grade in grades" :key="grade.id">
                <option :value="grade.id" x-text="`${grade.name} (${grade.level} — ${grade.academic_year})`"></option>
            </template>
        </select>
    </div>

    {{-- Livre officiel --}}
    <div>
        <label for="official_book_id" class="block text-sm font-medium text-gray-700 mb-1">Livre officiel <span class="text-red-500">*</span></label>
        <select id="official_book_id" name="official_book_id" x-model="officialBookId"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]" required>
            <option value="">— Sélectionner un livre —</option>
            <template x-for="book in officialBooks" :key="book.id">
                <option :value="book.id" x-text="`${book.title} — ${book.subject}`"></option>
            </template>
        </select>
        @error('official_book_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- État du livre --}}
    <div>
        <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">État du livre <span class="text-red-500">*</span></label>
        <select id="condition" name="condition"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]" required>
            @foreach($conditions as $condition)
                <option value="{{ $condition->value }}" {{ old('condition', isset($book) ? $book->condition->value : '') === $condition->value ? 'selected' : '' }}>
                    {{ $condition->label() }}
                </option>
            @endforeach
        </select>
        @error('condition') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Prix & Quantité --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prix de vente (FCFA) <span class="text-red-500">*</span></label>
            <input type="number" id="price" name="price" value="{{ old('price', isset($book) ? $book->price : '') }}"
                   min="500" max="100000" step="100" required
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                   placeholder="Ex: 3500">
            @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité <span class="text-red-500">*</span></label>
            <input type="number" id="quantity" name="quantity" value="{{ old('quantity', isset($book) ? $book->quantity : 1) }}"
                   min="1" max="20" required
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
            @error('quantity') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Prix d'achat initial (pour rachat) --}}
    <div>
        <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Prix auquel vous avez acheté ce livre (FCFA) <span class="text-gray-400 font-normal">— optionnel</span></label>
        <input type="number" id="purchase_price" name="purchase_price"
               value="{{ old('purchase_price', isset($book) ? $book->purchase_price : '') }}"
               min="0" step="100"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
               placeholder="Laissez vide si inconnu">
        <p class="text-xs text-gray-500 mt-1">Cette information aide à déterminer une offre de rachat équitable.</p>
        @error('purchase_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Images --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Photos du livre (max 3)</label>
        <input type="file" name="images[]" multiple accept="image/*"
               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WebP — 2 Mo max par image</p>
        @error('images.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

        @if(isset($book) && $book->images)
            <div class="mt-3 flex gap-3">
                @foreach($book->images as $img)
                    <img src="{{ Storage::url($img) }}" alt="Photo" class="w-20 h-20 object-cover rounded-lg border">
                @endforeach
            </div>
        @endif
    </div>
</div>
