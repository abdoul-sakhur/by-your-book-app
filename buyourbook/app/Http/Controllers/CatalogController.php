<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\School;
use App\Models\SellerBook;
use App\Models\Subject;
use App\Enums\BookCondition;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Recherche dans le catalogue.
     */
    public function search(Request $request): View
    {
        $query = $request->input('q', '');
        $subjectId = $request->input('subject');
        $condition = $request->input('condition');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $sort = $request->input('sort', 'pertinence');

        $offers = SellerBook::query()
            ->approved()
            ->where('quantity', '>', 0)
            ->with(['officialBook.subject', 'officialBook.grade.school', 'seller:id,name'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('officialBook', fn ($ob) => $ob->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$query}%"))
                    ->orWhereHas('grade.school', fn ($s) => $s->where('name', 'like', "%{$query}%"))
                );
            })
            ->when($subjectId, fn ($q) => $q->whereHas('officialBook', fn ($ob) => $ob->where('subject_id', $subjectId)))
            ->when($condition, fn ($q) => $q->where('condition', $condition))
            ->when($priceMin, fn ($q) => $q->where('price', '>=', (int) $priceMin))
            ->when($priceMax, fn ($q) => $q->where('price', '<=', (int) $priceMax));

        $offers = match ($sort) {
            'price_asc' => $offers->orderBy('price', 'asc'),
            'price_desc' => $offers->orderBy('price', 'desc'),
            'newest' => $offers->latest(),
            default => $query ? $offers->latest() : $offers->latest(),
        };

        $offers = $offers->paginate(20)->withQueryString();

        $subjects = Subject::orderBy('name')->get();
        $conditions = BookCondition::cases();

        return view('catalog.search', compact('offers', 'query', 'subjectId', 'condition', 'priceMin', 'priceMax', 'sort', 'subjects', 'conditions'));
    }

    /**
     * Liste des écoles actives.
     */
    public function schools(): View
    {
        $schools = School::active()
            ->withCount('grades')
            ->orderBy('name')
            ->get();

        $cities = School::active()->distinct()->orderBy('city')->pluck('city');

        return view('catalog.schools', compact('schools', 'cities'));
    }

    /**
     * Liste des livres officiels pour une classe donnée (avec offres vendeurs).
     */
    public function grade(School $school, Grade $grade): View
    {
        abort_unless($grade->school_id === $school->id, 404);

        $books = OfficialBook::where('grade_id', $grade->id)
            ->where('is_active', true)
            ->with(['subject'])
            ->withCount(['sellerBooks' => fn ($q) => $q->approved()->where('quantity', '>', 0)])
            ->withMin(['sellerBooks' => fn ($q) => $q->approved()->where('quantity', '>', 0)], 'price')
            ->get();

        $school->load('grades');

        return view('catalog.grade', compact('school', 'grade', 'books'));
    }

    /**
     * Détail d'un livre officiel avec toutes les offres vendeurs disponibles.
     */
    public function book(OfficialBook $officialBook): View
    {
        $officialBook->load(['subject', 'grade.school']);

        $offers = SellerBook::where('official_book_id', $officialBook->id)
            ->approved()
            ->where('quantity', '>', 0)
            ->with('seller:id,name')
            ->orderBy('price')
            ->get();

        return view('catalog.book', compact('officialBook', 'offers'));
    }
}
