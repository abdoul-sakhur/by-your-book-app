<x-app-layout>
    <x-slot name="title">Catalogue — Choisissez votre école</x-slot>

    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Choisissez votre école</h1>
                <p class="mt-2 text-gray-500">Sélectionnez l'école pour voir les listes de livres par classe</p>
            </div>

            {{-- Filtres par ville --}}
            <div x-data="{ city: '{{ request('city') }}' }" class="mb-8 flex flex-wrap gap-2 justify-center">
                <button @click="city = ''; $refs.cityInput.value = ''; $refs.filterForm.submit()"
                        :class="city === '' ? 'btn-primary !py-2 !px-4 text-sm' : 'px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50'">
                    Toutes
                </button>
                @foreach($cities as $c)
                    <button @click="city = '{{ $c }}'; $refs.cityInput.value = '{{ $c }}'; $refs.filterForm.submit()"
                            :class="city === '{{ $c }}' ? 'btn-primary !py-2 !px-4 text-sm' : 'px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50'">
                        {{ $c }}
                    </button>
                @endforeach
                <form x-ref="filterForm" method="GET" class="hidden">
                    <input x-ref="cityInput" type="hidden" name="city" value="{{ request('city') }}">
                </form>
            </div>

            {{-- Grille écoles --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($schools as $school)
                    @if(!request('city') || $school->city === request('city'))
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">
                            <div class="p-6">
                                <div class="flex items-start gap-4">
                                    @if($school->logo)
                                        <img src="{{ Storage::url($school->logo) }}" alt="{{ $school->name }}"
                                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-lg flex items-center justify-center flex-shrink-0"
                                             style="background-color: var(--color-primary); color: white;">
                                            <x-icon name="cube" class="w-7 h-7" />
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $school->name }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $school->city }} — {{ $school->district }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $school->grades_count }} classe(s)</p>
                                    </div>
                                </div>

                                {{-- Liens vers les classes --}}
                                @if($school->grades_count > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">Classes disponibles</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($school->grades->sortBy('level') as $grade)
                                                <a href="{{ route('catalog.grade', [$school, $grade]) }}"
                                                   class="inline-flex px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 text-gray-700 hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] transition">
                                                    {{ $grade->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400">
                        Aucune école disponible pour le moment.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</x-app-layout>
