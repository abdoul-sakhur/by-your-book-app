<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSettingRequest;
use App\Http\Requests\Admin\UpdateSettingRequest;
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

    public function store(StoreSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Setting::set($validated['key'], $validated['value'] ?? '');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètre créé avec succès.');
    }

    public function edit(Setting $setting): View
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(UpdateSettingRequest $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validated();

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
