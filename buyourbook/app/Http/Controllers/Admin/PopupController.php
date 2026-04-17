<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PopupController extends Controller
{
    public function index(Request $request): View
    {
        $popups = Popup::query()
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.popups.index', compact('popups'));
    }

    public function create(): View
    {
        return view('admin.popups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('popups', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        Popup::create($validated);

        return redirect()->route('admin.popups.index')
            ->with('success', 'Popup créée avec succès.');
    }

    public function edit(Popup $popup): View
    {
        return view('admin.popups.edit', compact('popup'));
    }

    public function update(Request $request, Popup $popup): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($popup->image) {
                Storage::disk('public')->delete($popup->image);
            }
            $validated['image'] = $request->file('image')->store('popups', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $popup->update($validated);

        return redirect()->route('admin.popups.index')
            ->with('success', 'Popup mise à jour.');
    }

    public function destroy(Popup $popup): RedirectResponse
    {
        if ($popup->image) {
            Storage::disk('public')->delete($popup->image);
        }
        $popup->delete();

        return redirect()->route('admin.popups.index')
            ->with('success', 'Popup supprimée.');
    }
}
