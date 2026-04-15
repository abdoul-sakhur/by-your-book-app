<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubjectRequest;
use App\Http\Requests\Admin\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = Subject::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->withCount('officialBooks')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Matière créée avec succès.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validated();

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Matière supprimée avec succès.');
    }
}
