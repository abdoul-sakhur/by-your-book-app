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
     * Catalogue principal — tous les livres disponibles, avec filtres.
     */
    public function index(Request $request): View
    {
        $q        = $request->input('q', '');
        $city     = $request->input('city', '');
        $schoolId = $request->input('school', '');
        $gradeId  = $request->input('grade', '');
        $subjectId = $request->input('subject', '');
        $condition = $request->input('condition', '');
        $priceMin  = $request->input('price_min', '');
        $priceMax  = $request->input('price_max', '');
        $sort      = $request->input('sort', 'newest');

        $books = OfficialBook::query()
            ->where('is_active', true)
            ->whereHas('sellerBooks', fn ($sq) => $sq->approved()->where('quantity', '>', 0))
            ->with(['subject', 'grade.school'])
            ->withCount(['sellerBooks' => fn ($sq) => $sq->approved()->where('quantity', '>', 0)])
            ->withMin(['sellerBooks' => fn ($sq) => $sq->approved()->where('quantity', '>', 0)], 'price')
            ->addSelect([
                'cheapest_seller_book_id' => SellerBook::select('id')
                    ->whereColumn('official_book_id', 'official_books.id')
                    ->approved()
                    ->where('quantity', '>', 0)
                    ->orderBy('price')
                    ->limit(1),
            ])
            ->when($q, fn ($sq) => $sq->where(fn ($inner) => $inner
                ->where('title', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%")
                ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$q}%"))
            ))
            ->when($city, fn ($sq) => $sq->whereHas('grade.school', fn ($s) => $s->where('city', $city)))
            ->when($schoolId, fn ($sq) => $sq->whereHas('grade', fn ($g) => $g->where('school_id', $schoolId)))
            ->when($gradeId, fn ($sq) => $sq->where('grade_id', $gradeId))
            ->when($subjectId, fn ($sq) => $sq->where('subject_id', $subjectId))
            ->when($condition, fn ($sq) => $sq->whereHas('sellerBooks', fn ($sb) =>
                $sb->approved()->where('quantity', '>', 0)->where('condition', $condition)
            ))
            ->when($priceMin, fn ($sq) => $sq->whereHas('sellerBooks', fn ($sb) =>
                $sb->approved()->where('quantity', '>', 0)->where('price', '>=', (int) $priceMin)
            ))
            ->when($priceMax, fn ($sq) => $sq->whereHas('sellerBooks', fn ($sb) =>
                $sb->approved()->where('quantity', '>', 0)->where('price', '<=', (int) $priceMax)
            ));

        $books = match ($sort) {
            'price_asc'  => $books->orderByRaw("(SELECT MIN(price) FROM seller_books WHERE official_book_id = official_books.id AND status = 'approved' AND quantity > 0) ASC"),
            'price_desc' => $books->orderByRaw("(SELECT MIN(price) FROM seller_books WHERE official_book_id = official_books.id AND status = 'approved' AND quantity > 0) DESC"),
            'title'      => $books->orderBy('title'),
            default      => $books->latest(),
        };

        $books = $books->paginate(24)->withQueryString();

        $cities   = School::active()->distinct()->orderBy('city')->pluck('city');
        $schools  = School::active()->orderBy('name')->get(['id', 'name', 'city']);
        $grades   = $schoolId
            ? Grade::where('school_id', $schoolId)->orderBy('name')->get(['id', 'name'])
            : collect();
        $subjects   = Subject::orderBy('name')->get();
        $conditions = BookCondition::cases();

        return view('catalog.index', compact(
            'books', 'q', 'city', 'schoolId', 'gradeId', 'subjectId',
            'condition', 'priceMin', 'priceMax', 'sort',
            'cities', 'schools', 'grades', 'subjects', 'conditions'
        ));
    }

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
            ->addSelect(['cheapest_seller_book_id' => SellerBook::select('id')
                ->whereColumn('official_book_id', 'official_books.id')
                ->approved()
                ->where('quantity', '>', 0)
                ->orderBy('price')
                ->limit(1)
            ])
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
