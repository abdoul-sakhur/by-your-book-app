<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Comment ça marche ?</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Intro --}}
            <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                <p class="text-lg text-gray-600 leading-relaxed">
                    <strong>BuyYourBook</strong> est la plateforme de référence pour acheter et vendre des livres scolaires d'occasion à Abidjan.
                    Que vous soyez parent, élève ou vendeur, tout se fait en quelques étapes simples.
                </p>
            </div>

            {{-- Pour les acheteurs --}}
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2"><x-icon name="backpack" class="w-6 h-6" /> Pour les acheteurs</h3>
            <div class="space-y-6 mb-12">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-primary);">1</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Choisissez votre école et votre classe</h4>
                        <p class="text-gray-500 mt-1">Parcourez notre catalogue par école, puis sélectionnez la classe de votre enfant pour voir la liste des livres recommandés.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-primary);">2</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Comparez et ajoutez au panier</h4>
                        <p class="text-gray-500 mt-1">Pour chaque livre, comparez les offres de différents vendeurs vérifiés. Choisissez le prix et l'état qui vous conviennent, puis ajoutez au panier.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-primary);">3</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Validez et choisissez votre point relais</h4>
                        <p class="text-gray-500 mt-1">Passez commande et sélectionnez le point relais le plus proche de chez vous à Abidjan pour récupérer vos livres.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-primary);">4</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Récupérez vos livres</h4>
                        <p class="text-gray-500 mt-1">Vous serez notifié quand votre commande est prête. Rendez-vous au point relais choisi pour récupérer vos manuels.</p>
                    </div>
                </div>
            </div>

            {{-- Pour les vendeurs --}}
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2"><x-icon name="tokens" class="w-6 h-6" /> Pour les vendeurs</h3>
            <div class="space-y-6 mb-12">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-secondary);">1</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Créez votre compte vendeur</h4>
                        <p class="text-gray-500 mt-1">Inscrivez-vous en choisissant le profil « Vendeur ». C'est gratuit et rapide.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-secondary);">2</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Soumettez vos livres</h4>
                        <p class="text-gray-500 mt-1">Ajoutez les livres que vous souhaitez vendre en précisant l'état, le prix et la quantité. Joignez des photos pour rassurer les acheteurs.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-secondary);">3</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Validation par notre équipe</h4>
                        <p class="text-gray-500 mt-1">Notre équipe vérifie chaque annonce pour garantir la qualité. Vous serez notifié une fois le livre approuvé.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--color-secondary);">4</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Suivez vos ventes</h4>
                        <p class="text-gray-500 mt-1">Consultez votre tableau de bord pour suivre vos commandes, vos articles vendus et votre chiffre d'affaires.</p>
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="rounded-2xl p-8 text-center text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, #2d6a4f 100%);">
                <h3 class="text-xl font-bold">Prêt à commencer ?</h3>
                <div class="mt-4 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('catalog.schools') }}" class="inline-flex items-center justify-center px-6 py-3 font-semibold rounded-lg text-white" style="background-color: var(--color-secondary);">
                        <x-icon name="reader" class="w-5 h-5 inline" /> Acheter des livres
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 font-semibold rounded-lg bg-white" style="color: var(--color-primary);">
                        <x-icon name="tokens" class="w-5 h-5 inline" /> Devenir vendeur
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
