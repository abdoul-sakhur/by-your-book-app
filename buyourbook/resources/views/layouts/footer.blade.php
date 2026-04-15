<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Marque -->
            <div>
                <h3 class="text-lg font-bold" style="color: var(--color-primary);">📚 BuyYourBook</h3>
                <p class="mt-2 text-sm text-gray-500">
                    La plateforme d'achat et vente de livres scolaires en Côte d'Ivoire.
                    Trouvez les manuels de votre école au meilleur prix.
                </p>
            </div>

            <!-- Liens rapides -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Liens utiles</h4>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">Accueil</a></li>
                    <li><a href="{{ route('pages.how-it-works') }}" class="text-sm text-gray-500 hover:text-gray-700">Comment ça marche</a></li>
                    <li><a href="{{ route('pages.contact') }}" class="text-sm text-gray-500 hover:text-gray-700">Contact</a></li>
                </ul>
            </div>

            <!-- Légal -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Informations légales</h4>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('pages.terms') }}" class="text-sm text-gray-500 hover:text-gray-700">Conditions d'utilisation</a></li>
                    <li><a href="{{ route('pages.privacy') }}" class="text-sm text-gray-500 hover:text-gray-700">Politique de confidentialité</a></li>
                    <li><a href="{{ route('pages.legal') }}" class="text-sm text-gray-500 hover:text-gray-700">Mentions légales</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-400">
                &copy; {{ date('Y') }} BuyYourBook.com — Tous droits réservés. Fait avec ❤️ à Abidjan.
            </p>
        </div>
    </div>
</footer>
