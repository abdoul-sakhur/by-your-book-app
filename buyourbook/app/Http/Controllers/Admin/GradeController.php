<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $grades = Grade::query()
            ->with('school')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->school_id, fn ($q, $id) => $q->where('school_id', $id))
            ->when($request->academic_year, fn ($q, $y) => $q->where('academic_year', $y))
            ->withCount('officialBooks')
            ->orderBy('school_id')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $schools = School::orderBy('name')->pluck('name', 'id');
        $years = Grade::select('academic_year')->distinct()->orderByDesc('academic_year')->pluck('academic_year');

        return view('admin.grades.index', compact('grades', 'schools', 'years'));
    }

    public function create(Request $request): View
    {
        $schools = School::active()->orderBy('name')->pluck('name', 'id');
        $selectedSchool = $request->school_id;

        return view('admin.grades.create', compact('schools', 'selectedSchool'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        Grade::create($validated);

        return redirect()->route('admin.grades.index')
            ->with('success', 'Classe créée avec succès.');
    }

    public function edit(Grade $grade): View
    {
        $schools = School::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.grades.edit', compact('grade', 'schools'));
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        $grade->update($validated);

        return redirect()->route('admin.grades.index')
            ->with('success', 'Classe mise à jour avec succès.');
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        $grade->delete();

        return redirect()->route('admin.grades.index')
            ->with('success', 'Classe supprimée avec succès.');
    }
}
