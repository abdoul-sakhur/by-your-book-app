<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Politique de Confidentialité</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-8 prose prose-green max-w-none">
                <p class="text-sm text-gray-400">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>

                <h3>1. Collecte des données</h3>
                <p>BuyYourBook collecte les données personnelles suivantes lors de votre inscription et utilisation de la plateforme :</p>
                <ul>
                    <li>Nom et prénom</li>
                    <li>Adresse email</li>
                    <li>Numéro de téléphone</li>
                    <li>Informations relatives aux transactions (commandes, ventes)</li>
                </ul>

                <h3>2. Utilisation des données</h3>
                <p>Vos données personnelles sont utilisées pour :</p>
                <ul>
                    <li>Gérer votre compte et vos transactions</li>
                    <li>Vous contacter au sujet de vos commandes</li>
                    <li>Améliorer nos services et votre expérience utilisateur</li>
                    <li>Vous envoyer des notifications relatives à vos activités sur la plateforme</li>
                </ul>

                <h3>3. Partage des données</h3>
                <p>Vos données personnelles ne sont jamais vendues à des tiers. Elles peuvent être partagées de manière limitée avec :</p>
                <ul>
                    <li>Les points relais partenaires (nom et commande pour le retrait)</li>
                    <li>Les vendeurs/acheteurs concernés par une transaction (nom uniquement)</li>
                </ul>

                <h3>4. Sécurité</h3>
                <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données contre tout accès non autorisé, modification ou destruction.</p>

                <h3>5. Conservation</h3>
                <p>Vos données sont conservées pendant la durée de votre inscription et pendant une période de 2 ans après la suppression de votre compte, conformément aux obligations légales.</p>

                <h3>6. Vos droits</h3>
                <p>Conformément à la réglementation en vigueur en Côte d'Ivoire, vous disposez des droits suivants :</p>
                <ul>
                    <li>Droit d'accès à vos données personnelles</li>
                    <li>Droit de rectification des informations inexactes</li>
                    <li>Droit de suppression de votre compte et vos données</li>
                    <li>Droit d'opposition au traitement de vos données</li>
                </ul>
                <p>Pour exercer ces droits, contactez-nous à <a href="mailto:contact@buyyourbook.ci">contact@buyyourbook.ci</a>.</p>

                <h3>7. Cookies</h3>
                <p>BuyYourBook utilise des cookies techniques essentiels au fonctionnement de la plateforme (session utilisateur, panier). Aucun cookie publicitaire n'est utilisé.</p>

                <h3>8. Modifications</h3>
                <p>Cette politique de confidentialité peut être mise à jour. Tout changement significatif sera communiqué aux utilisateurs inscrits.</p>
            </div>
        </div>
    </div>
</x-app-layout>
