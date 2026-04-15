<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::orderBy('key')->get();

        return view('admin.settings.index', compact('settings'));
    }

    public function create(): View
    {
        return view('admin.settings.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:settings,key', 'regex:/^[a-z0-9_.]+$/'],
            'value' => ['nullable', 'string', 'max:2000'],
        ]);

        Setting::set($validated['key'], $validated['value'] ?? '');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètre créé avec succès.');
    }

    public function edit(Setting $setting): View
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['nullable', 'string', 'max:2000'],
        ]);

        Setting::set($setting->key, $validated['value'] ?? '');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètre mis à jour.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        \Illuminate\Support\Facades\Cache::forget("settings.{$setting->key}");
        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètre supprimé.');
    }
}
