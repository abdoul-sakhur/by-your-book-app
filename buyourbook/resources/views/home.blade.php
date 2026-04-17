<x-app-layout>
    <!-- Hero Section -->
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, var(--color-primary) 0%, #2d6a4f 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    Les livres scolaires <br>
                    <span style="color: var(--color-secondary);">au meilleur prix</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-white/80 max-w-2xl mx-auto">
                    Achetez ou vendez les manuels scolaires de votre école à Abidjan.
                    Des livres d'occasion vérifiés, adaptés par école et par classe.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('catalog.schools') }}"
                       class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold rounded-lg text-white shadow-lg hover:shadow-xl transition"
                       style="background-color: var(--color-secondary);">
                        <x-icon name="reader" class="w-5 h-5 inline" /> Acheter mes livres
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold rounded-lg bg-white shadow-lg hover:shadow-xl transition"
                       style="color: var(--color-primary);">
                        <x-icon name="tokens" class="w-5 h-5 inline" /> Vendre mes livres
                    </a>
                </div>
            </div>
        </div>

        <!-- Vague décorative -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,48L48,42.7C96,37,192,27,288,32C384,37,480,59,576,64C672,69,768,59,864,48C960,37,1056,27,1152,32C1248,37,1344,59,1392,69.3L1440,80L1440,80L0,80Z" fill="var(--color-bg)"/>
            </svg>
        </div>
    </section>

    <!-- Bannière Home Top -->
    @include('partials.banner', ['position' => 'home_top'])

    <!-- Comment ça marche -->
    <section class="py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Comment ça marche ?</h2>
                <p class="mt-3 text-lg text-gray-500">En 3 étapes simples</p>
            </div>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Étape 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-2xl font-bold text-white"
                         style="background-color: var(--color-primary);">
                        1
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-800">Choisissez votre école</h3>
                    <p class="mt-3 text-gray-500">
                        Sélectionnez l'école et la classe de votre enfant pour voir la liste
                        des livres recommandés.
                    </p>
                </div>

                <!-- Étape 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-2xl font-bold text-white"
                         style="background-color: var(--color-secondary);">
                        2
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-800">Trouvez vos livres</h3>
                    <p class="mt-3 text-gray-500">
                        Comparez les offres de vendeurs vérifiés et ajoutez les livres
                        à votre panier au meilleur prix.
                    </p>
                </div>

                <!-- Étape 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-2xl font-bold text-white"
                         style="background-color: var(--color-accent);">
                        3
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-800">Récupérez au point relais</h3>
                    <p class="mt-3 text-gray-500">
                        Commandez et récupérez vos livres dans le point relais le plus
                        proche de chez vous à Abidjan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Slider publicitaire -->
    <x-slider-pub :slides="$slides" />

    <!-- Section écoles populaires -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900">Écoles populaires</h2>
            <p class="mt-2 text-gray-500">Consultez les listes de fournitures par école</p>

            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($schools as $school)
                    <a href="{{ $school->grades->first() ? route('catalog.grade', [$school, $school->grades->first()]) : route('catalog.schools') }}"
                       class="border border-gray-200 rounded-lg p-5 text-center hover:shadow-md hover:border-[var(--color-primary)] transition group">
                        @if($school->logo)
                            <img src="{{ Storage::url($school->logo) }}" alt="{{ $school->name }}" class="w-12 h-12 mx-auto rounded-full object-cover mb-3">
                        @else
                            <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center text-xl mb-3"
                                 style="background-color: var(--color-primary); color: white;">
                                {{ mb_substr($school->name, 0, 1) }}
                            </div>
                        @endif
                        <p class="font-semibold text-gray-800 text-sm group-hover:text-[var(--color-primary)] transition">{{ $school->name }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $school->city }} — {{ $school->grades_count }} classe(s)</p>
                    </a>
                @empty
                    <div class="col-span-full border border-dashed border-gray-300 rounded-lg p-6 text-center text-gray-400">
                        Les écoles s'afficheront ici
                    </div>
                @endforelse
            </div>

            @if($schools->count() > 0)
                <div class="mt-6 text-center">
                    <a href="{{ route('catalog.schools') }}" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">
                        Voir toutes les écoles →
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Bannière Home Mid -->
    @include('partials.banner', ['position' => 'home_mid'])

    <!-- CTA vendeur -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl p-8 lg:p-12 text-center text-white"
                 style="background: linear-gradient(135deg, var(--color-primary) 0%, #2d6a4f 100%);">
                <h2 class="text-2xl lg:text-3xl font-bold">Vous avez des livres à vendre ?</h2>
                <p class="mt-4 text-white/80 max-w-xl mx-auto">
                    Inscrivez-vous en tant que vendeur et donnez une seconde vie à vos manuels
                    scolaires. C'est simple, rapide et gratuit.
                </p>
                <a href="{{ route('register') }}"
                   class="mt-8 inline-flex items-center justify-center px-8 py-3 text-base font-semibold rounded-lg transition"
                   style="background-color: var(--color-secondary); color: white;">
                    Devenir vendeur
                </a>
            </div>
        </div>
    </section>

    <!-- Popup publicitaire -->
    <x-popup-ad :popup="$popup" />
</x-app-layout>
