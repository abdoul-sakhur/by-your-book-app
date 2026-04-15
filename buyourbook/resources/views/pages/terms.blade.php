<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Conditions Générales d'Utilisation</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-8 prose prose-green max-w-none">
                <p class="text-sm text-gray-400">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>

                <h3>1. Objet</h3>
                <p>Les présentes Conditions Générales d'Utilisation (CGU) régissent l'utilisation de la plateforme <strong>BuyYourBook</strong> (accessible à l'adresse buyyourbook.ci), qui permet l'achat et la vente de livres scolaires d'occasion en Côte d'Ivoire.</p>

                <h3>2. Inscription</h3>
                <p>L'utilisation de la plateforme en tant qu'acheteur ou vendeur nécessite la création d'un compte. L'utilisateur s'engage à fournir des informations exactes et à maintenir la confidentialité de ses identifiants de connexion.</p>

                <h3>3. Rôle de la plateforme</h3>
                <p>BuyYourBook agit en qualité d'intermédiaire technique entre vendeurs et acheteurs de livres scolaires. La plateforme n'est pas partie aux transactions entre utilisateurs et ne garantit pas la qualité des livres proposés au-delà du processus de validation.</p>

                <h3>4. Obligations du vendeur</h3>
                <ul>
                    <li>Proposer uniquement des livres scolaires en bon état et conformes à la description</li>
                    <li>Fixer des prix justes et raisonnables</li>
                    <li>Fournir des photos fidèles à l'état réel du livre</li>
                    <li>Respecter les délais de mise à disposition au point relais</li>
                </ul>

                <h3>5. Obligations de l'acheteur</h3>
                <ul>
                    <li>Fournir des informations de contact valides</li>
                    <li>Récupérer sa commande au point relais dans les délais indiqués</li>
                    <li>Signaler tout problème dans les 48 heures suivant la récupération</li>
                </ul>

                <h3>6. Validation des annonces</h3>
                <p>Chaque livre soumis par un vendeur est soumis à une validation par l'équipe BuyYourBook. La plateforme se réserve le droit de refuser toute annonce ne respectant pas ses critères de qualité.</p>

                <h3>7. Points relais</h3>
                <p>Les livres sont retirés dans les points relais partenaires situés à Abidjan. Les horaires et conditions de retrait sont communiqués lors de la commande.</p>

                <h3>8. Limitation de responsabilité</h3>
                <p>BuyYourBook ne saurait être tenu responsable des litiges entre acheteurs et vendeurs, des retards de livraison ou de l'indisponibilité temporaire de la plateforme.</p>

                <h3>9. Modification des CGU</h3>
                <p>BuyYourBook se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés de toute modification importante.</p>

                <h3>10. Contact</h3>
                <p>Pour toute question relative aux présentes CGU, contactez-nous à l'adresse <a href="mailto:contact@buyyourbook.ci">contact@buyyourbook.ci</a>.</p>
            </div>
        </div>
    </div>
</x-app-layout>
