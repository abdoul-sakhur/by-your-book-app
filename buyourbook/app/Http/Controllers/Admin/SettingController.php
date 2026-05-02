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

    public function general(): View
    {
        return view('admin.settings.general', [
            'delivery_fee'             => Setting::get('delivery_fee', 3000),
            'free_delivery_threshold'  => Setting::get('free_delivery_threshold', 500000),
            'tawkto_widget_id'         => Setting::get('tawkto_widget_id', ''),
        ]);
    }

    public function saveGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_fee'            => ['required', 'integer', 'min:0'],
            'free_delivery_threshold' => ['required', 'integer', 'min:0'],
            'tawkto_widget_id'        => ['nullable', 'string', 'max:200'],
        ]);

        Setting::set('delivery_fee',            (string) $validated['delivery_fee']);
        Setting::set('free_delivery_threshold', (string) $validated['free_delivery_threshold']);
        Setting::set('tawkto_widget_id',        $validated['tawkto_widget_id'] ?? '');

        return redirect()->route('admin.settings.general')
            ->with('success', 'Paramètres enregistrés avec succès.');
    }
}
