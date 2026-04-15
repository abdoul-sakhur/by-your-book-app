<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OfficialBookRequest;
use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficialBookController extends Controller
{
    public function index(Request $request): View
    {
        $books = OfficialBook::query()
            ->with(['grade.school', 'subject'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('author', 'like', "%{$s}%")
                ->orWhere('isbn', 'like', "%{$s}%"))
            ->when($request->school_id, fn ($q, $id) => $q->whereHas('grade', fn ($g) => $g->where('school_id', $id)))
            ->when($request->grade_id, fn ($q, $id) => $q->where('grade_id', $id))
            ->when($request->subject_id, fn ($q, $id) => $q->where('subject_id', $id))
            ->withCount('sellerBooks')
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        $schools = School::orderBy('name')->pluck('name', 'id');
        $subjects = Subject::orderBy('name')->pluck('name', 'id');

        return view('admin.official-books.index', compact('books', 'schools', 'subjects'));
    }

    public function create(Request $request): View
    {
        $schools = School::active()->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->pluck('name', 'id');
        $selectedGrade = $request->grade_id;

        return view('admin.official-books.create', compact('schools', 'subjects', 'selectedGrade'));
    }

    public function store(OfficialBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        OfficialBook::create($validated);

        return redirect()->route('admin.official-books.index')
            ->with('success', 'Livre officiel créé avec succès.');
    }

    public function edit(OfficialBook $officialBook): View
    {
        $schools = School::active()->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->pluck('name', 'id');

        return view('admin.official-books.edit', compact('officialBook', 'schools', 'subjects'));
    }

    public function update(OfficialBookRequest $request, OfficialBook $officialBook): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        $officialBook->update($validated);

        return redirect()->route('admin.official-books.index')
            ->with('success', 'Livre officiel mis à jour avec succès.');
    }

    public function destroy(OfficialBook $officialBook): RedirectResponse
    {
        $officialBook->delete();

        return redirect()->route('admin.official-books.index')
            ->with('success', 'Livre officiel supprimé avec succès.');
    }
}
