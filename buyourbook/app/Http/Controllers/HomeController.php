<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SellerBook;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Écoles existantes
        $schools = School::active()
            ->withCount('grades')
            ->with(['grades' => fn($q) => $q->orderBy('level')->limit(1)])
            ->orderBy('name')
            ->limit(8)
            ->get();

        // Livres populaires (les plus commandés ou approuvés récents)
        $bestSelling = SellerBook::where('status', 'approved')
            ->where('quantity', '>', 0)
            ->with(['officialBook.subject', 'officialBook.grade.school'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($sb) => (object) [
                'id' => $sb->id,
                'title' => $sb->officialBook->title ?? 'Livre',
                'price' => $sb->price,
                'image' => $sb->officialBook->cover_image
                    ? asset('storage/' . $sb->officialBook->cover_image)
                    : null,
                'condition' => $sb->condition->label(),
                'seller_book_id' => $sb->id,
            ]);

        // Si pas assez de vrais livres, compléter avec du mock
        if ($bestSelling->count() < 4) {
            $bestSelling = collect([
                (object) ['id' => 1, 'title' => 'Mathématiques 6ème', 'price' => 3500, 'image' => null, 'condition' => 'Bon état', 'seller_book_id' => null],
                (object) ['id' => 2, 'title' => 'Français CM2', 'price' => 2800, 'image' => null, 'condition' => 'Neuf', 'seller_book_id' => null],
                (object) ['id' => 3, 'title' => 'Sciences Physiques 3ème', 'price' => 4200, 'image' => null, 'condition' => 'Acceptable', 'seller_book_id' => null],
                (object) ['id' => 4, 'title' => 'Anglais 5ème', 'price' => 3000, 'image' => null, 'condition' => 'Bon état', 'seller_book_id' => null],
                (object) ['id' => 5, 'title' => 'Histoire-Géo Tle', 'price' => 5000, 'image' => null, 'condition' => 'Neuf', 'seller_book_id' => null],
                (object) ['id' => 6, 'title' => 'SVT 4ème', 'price' => 3200, 'image' => null, 'condition' => 'Bon état', 'seller_book_id' => null],
                (object) ['id' => 7, 'title' => 'Philosophie Tle', 'price' => 4500, 'image' => null, 'condition' => 'Neuf', 'seller_book_id' => null],
                (object) ['id' => 8, 'title' => 'Informatique 2nde', 'price' => 3800, 'image' => null, 'condition' => 'Acceptable', 'seller_book_id' => null],
            ]);
        }

        // Slides publicitaires
        $slides = [
            (object) [
                'title' => 'Promotion Assurance Scolaire',
                'description' => 'Protégez vos enfants dès aujourd\'hui avec nos partenaires assureurs.',
                'image' => null,
                'cta_text' => 'En savoir plus',
                'cta_link' => '#',
                'bg_color' => 'from-emerald-700 to-teal-600',
            ],
            (object) [
                'title' => 'Rentrée Scolaire 2025-2026',
                'description' => 'Trouvez tous les manuels scolaires de votre école au meilleur prix.',
                'image' => null,
                'cta_text' => 'Voir le catalogue',
                'cta_link' => route('catalog.schools'),
                'bg_color' => 'from-blue-700 to-indigo-600',
            ],
            (object) [
                'title' => 'Vendez vos anciens livres',
                'description' => 'Donnez une seconde vie à vos manuels et gagnez de l\'argent facilement.',
                'image' => null,
                'cta_text' => 'Devenir vendeur',
                'cta_link' => route('register'),
                'bg_color' => 'from-amber-600 to-orange-500',
            ],
        ];

        // Bannière intermédiaire
        $adBanner = (object) [
            'text' => 'Assurez l\'avenir scolaire de vos enfants',
            'subtext' => 'Livres vérifiés, prix imbattables, livraison en point relais à Abidjan.',
            'cta_text' => 'Découvrir le catalogue',
            'cta_link' => route('catalog.schools'),
        ];

        // Catégories (matières principales)
        $categories = collect([
            (object) ['name' => 'Mathématiques', 'icon' => '📐', 'color' => 'bg-blue-100 text-blue-700'],
            (object) ['name' => 'Français', 'icon' => '📖', 'color' => 'bg-red-100 text-red-700'],
            (object) ['name' => 'Anglais', 'icon' => '🇬🇧', 'color' => 'bg-purple-100 text-purple-700'],
            (object) ['name' => 'Sciences', 'icon' => '🔬', 'color' => 'bg-green-100 text-green-700'],
            (object) ['name' => 'Histoire-Géo', 'icon' => '🌍', 'color' => 'bg-amber-100 text-amber-700'],
            (object) ['name' => 'Philosophie', 'icon' => '💭', 'color' => 'bg-indigo-100 text-indigo-700'],
            (object) ['name' => 'Physique-Chimie', 'icon' => '⚗️', 'color' => 'bg-teal-100 text-teal-700'],
            (object) ['name' => 'Informatique', 'icon' => '💻', 'color' => 'bg-gray-100 text-gray-700'],
        ]);

        return view('home', compact('schools', 'bestSelling', 'slides', 'adBanner', 'categories'));
    }
}
