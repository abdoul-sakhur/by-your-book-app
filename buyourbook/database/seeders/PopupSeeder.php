<?php

namespace Database\Seeders;

use App\Models\Popup;
use Illuminate\Database\Seeder;

class PopupSeeder extends Seeder
{
    public function run(): void
    {
        Popup::create([
            'title'      => 'Bienvenue sur BuyYourBook !',
            'message'    => 'Profitez de -10% sur votre première commande avec le code BIENVENUE10.',
            'cta_text'   => 'Voir les offres',
            'cta_link'   => '/catalogue',
            'start_date' => now(),
            'end_date'   => now()->addMonths(3),
            'is_active'  => true,
        ]);
    }
}
