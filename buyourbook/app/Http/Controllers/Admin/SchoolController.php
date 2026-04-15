<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchoolRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $schools = School::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->city, fn ($q, $c) => $q->where('city', $c))
            ->withCount('grades')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $cities = School::select('city')->distinct()->orderBy('city')->pluck('city');

        return view('admin.schools.index', compact('schools', 'cities'));
    }

    public function create(): View
    {
        return view('admin.schools.create');
    }

    public function store(SchoolRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools', 'public');
        }

        School::create($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'École créée avec succès.');
    }

    public function edit(School $school): View
    {
        return view('admin.schools.edit', compact('school'));
    }

    public function update(SchoolRequest $request, School $school): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools', 'public');
        }

        $school->update($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'École mise à jour avec succès.');
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'École supprimée avec succès.');
    }
}
