<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Rentrée scolaire 2025',
                'description' => 'Trouvez tous vos manuels scolaires à prix réduit pour la rentrée.',
                'cta_text' => 'Voir le catalogue',
                'cta_link' => '/catalogue',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Vendez vos anciens livres',
                'description' => 'Donnez une seconde vie à vos manuels et gagnez de l\'argent facilement.',
                'cta_text' => 'Devenir vendeur',
                'cta_link' => '/register',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Livraison en point relais',
                'description' => 'Récupérez vos commandes dans le point relais le plus proche à Abidjan.',
                'cta_text' => 'Comment ça marche',
                'cta_link' => '/comment-ca-marche',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
