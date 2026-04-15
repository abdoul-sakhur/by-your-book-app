<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelayPoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelayPointController extends Controller
{
    public function index(Request $request): View
    {
        $relayPoints = RelayPoint::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('address', 'like', "%{$s}%"))
            ->when($request->city, fn ($q, $c) => $q->where('city', $c))
            ->withCount('orders')
            ->orderBy('city')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $cities = RelayPoint::select('city')->distinct()->orderBy('city')->pluck('city');

        return view('admin.relay-points.index', compact('relayPoints', 'cities'));
    }

    public function create(): View
    {
        return view('admin.relay-points.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        RelayPoint::create($validated);

        return redirect()->route('admin.relay-points.index')
            ->with('success', 'Point relais créé avec succès.');
    }

    public function edit(RelayPoint $relayPoint): View
    {
        return view('admin.relay-points.edit', compact('relayPoint'));
    }

    public function update(Request $request, RelayPoint $relayPoint): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $relayPoint->update($validated);

        return redirect()->route('admin.relay-points.index')
            ->with('success', 'Point relais mis à jour.');
    }

    public function destroy(RelayPoint $relayPoint): RedirectResponse
    {
        if ($relayPoint->orders()->exists()) {
            return redirect()->back()->with('error', 'Impossible de supprimer : des commandes utilisent ce point relais.');
        }

        $relayPoint->delete();

        return redirect()->route('admin.relay-points.index')
            ->with('success', 'Point relais supprimé.');
    }
}
