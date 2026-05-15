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

    {{-- Informations du livre --}}
    <div class="bg-gray-50 rounded-lg p-4 space-y-4 border border-gray-200">
        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Informations du livre</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Auteur <span class="text-gray-400 font-normal">— optionnel</span></label>
                <input type="text" id="author" name="author"
                       value="{{ old('author', isset($book) ? $book->author : '') }}"
                       placeholder="Ex : M. Diallo"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                @error('author') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN <span class="text-gray-400 font-normal">— optionnel</span></label>
                <input type="text" id="isbn" name="isbn"
                       value="{{ old('isbn', isset($book) ? $book->isbn : '') }}"
                       placeholder="Ex : 978-2-01-235464-9"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                @error('isbn') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="publisher" class="block text-sm font-medium text-gray-700 mb-1">Éditeur <span class="text-gray-400 font-normal">— optionnel</span></label>
                <input type="text" id="publisher" name="publisher"
                       value="{{ old('publisher', isset($book) ? $book->publisher : '') }}"
                       placeholder="Ex : Hachette Éducation"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                @error('publisher') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
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
    <div x-data="{
        maxPhotos: 6,
        pendingFiles: [],
        existingImages: @json(isset($book) && $book->images ? $book->images : []),
        removedImages: [],
        get keptImages() {
            return this.existingImages.filter(img => !this.removedImages.includes(img));
        },
        get totalCount() {
            return this.pendingFiles.length + this.keptImages.length;
        },
        canAddMore() {
            return this.totalCount < this.maxPhotos;
        },
        pickFiles(event) {
            const files = Array.from(event.target.files);
            const available = this.maxPhotos - this.totalCount;
            files.slice(0, available).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => this.pendingFiles.push({ file, preview: e.target.result, name: file.name });
                reader.readAsDataURL(file);
            });
            event.target.value = '';
            this.$nextTick(() => this.syncInput());
        },
        removeExisting(img) {
            this.removedImages.push(img);
        },
        restoreExisting(img) {
            this.removedImages = this.removedImages.filter(r => r !== img);
        },
        removePending(idx) {
            this.pendingFiles.splice(idx, 1);
            this.$nextTick(() => this.syncInput());
        },
        syncInput() {
            const dt = new DataTransfer();
            this.pendingFiles.forEach(p => dt.items.add(p.file));
            if (this.$refs.fileInput) this.$refs.fileInput.files = dt.files;
        }
    }">
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">
                Photos du livre
                <span class="text-gray-400 font-normal text-xs">— face, dos, tranche, pages intérieures…</span>
            </label>
            <span class="text-xs text-gray-400" x-text="totalCount + ' / ' + maxPhotos"></span>
        </div>

        {{-- Grille d'aperçu --}}
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 mb-2">

            {{-- Images existantes (modification) --}}
            <template x-for="img in existingImages" :key="img">
                <div class="relative group aspect-square rounded-lg overflow-hidden border"
                     :class="removedImages.includes(img) ? 'opacity-40 border-red-300' : 'border-gray-200'">
                    <img :src="'/storage/' + img" class="w-full h-full object-cover">
                    {{-- Bouton supprimer --}}
                    <button type="button"
                            x-show="!removedImages.includes(img)"
                            @click="removeExisting(img)"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    {{-- Annuler la suppression --}}
                    <button type="button"
                            x-show="removedImages.includes(img)"
                            @click="restoreExisting(img)"
                            class="absolute inset-0 flex items-center justify-center bg-black/50 text-white text-xs font-semibold">
                        Annuler
                    </button>
                </div>
            </template>

            {{-- Nouvelles images en attente --}}
            <template x-for="(item, idx) in pendingFiles" :key="idx">
                <div class="relative group aspect-square rounded-lg overflow-hidden border border-indigo-200">
                    <img :src="item.preview" class="w-full h-full object-cover">
                    <button type="button"
                            @click="removePending(idx)"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <span style="position:absolute;bottom:0;left:0;right:0;background:rgba(99,102,241,.8);color:#fff;text-align:center;font-size:0.625rem;padding:0.125rem 0.25rem;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;" x-text="item.name.split('.')[0]"></span>
                </div>
            </template>

            {{-- Bouton ajouter --}}
            <label x-show="canAddMore()"
                   class="aspect-square flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span style="font-size:0.625rem;color:#9ca3af;margin-top:0.125rem;">Ajouter</span>
                <input type="file" accept="image/jpeg,image/png,image/webp" multiple style="display:none" @change="pickFiles($event)">
            </label>
        </div>

        <p class="text-xs text-gray-400 mb-2">JPG, PNG ou WebP — 2 Mo max par photo</p>

        {{-- Input réel pour la soumission du formulaire (via DataTransfer) --}}
        <input type="file" name="images[]" multiple x-ref="fileInput" style="display:none" accept="image/jpeg,image/png,image/webp">

        {{-- Images existantes conservées --}}
        <template x-for="img in keptImages" :key="img">
            <input type="hidden" name="keep_images[]" :value="img">
        </template>

        @error('images') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        @error('images.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>
