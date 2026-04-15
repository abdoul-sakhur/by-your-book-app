<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mentions Légales</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-8 prose prose-green max-w-none">

                <h3>Éditeur du site</h3>
                <ul>
                    <li><strong>Nom :</strong> BuyYourBook</li>
                    <li><strong>Forme juridique :</strong> Société en cours de création</li>
                    <li><strong>Siège social :</strong> Abidjan, Côte d'Ivoire</li>
                    <li><strong>Email :</strong> <a href="mailto:contact@buyyourbook.ci">contact@buyyourbook.ci</a></li>
                    <li><strong>Directeur de la publication :</strong> L'équipe BuyYourBook</li>
                </ul>

                <h3>Hébergement</h3>
                <ul>
                    <li><strong>Hébergeur :</strong> À définir</li>
                    <li><strong>Adresse :</strong> À définir</li>
                </ul>

                <h3>Propriété intellectuelle</h3>
                <p>L'ensemble des contenus de ce site (textes, images, graphismes, logo, icônes) est la propriété exclusive de BuyYourBook ou de ses partenaires. Toute reproduction, distribution ou utilisation sans autorisation préalable est interdite.</p>

                <h3>Responsabilité</h3>
                <p>BuyYourBook s'efforce d'assurer l'exactitude des informations diffusées sur le site. Toutefois, la plateforme ne peut garantir l'exactitude, la complétude et l'actualité des informations mises à disposition.</p>

                <h3>Liens hypertextes</h3>
                <p>Le site peut contenir des liens vers d'autres sites. BuyYourBook n'exerce aucun contrôle sur le contenu de ces sites tiers et décline toute responsabilité quant à leur contenu.</p>

                <h3>Données personnelles</h3>
                <p>Le traitement des données personnelles est décrit dans notre <a href="{{ route('pages.privacy') }}">Politique de confidentialité</a>.</p>

                <h3>Droit applicable</h3>
                <p>Les présentes mentions légales sont régies par le droit ivoirien. Tout litige relatif à l'utilisation du site sera soumis à la compétence des tribunaux d'Abidjan.</p>
            </div>
        </div>
    </div>
</x-app-layout>
