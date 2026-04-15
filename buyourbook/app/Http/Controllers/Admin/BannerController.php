<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BannerPosition;
use App\Enums\BannerTarget;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        $banners = Banner::query()
            ->when($request->position, fn ($q, $p) => $q->where('position', $p))
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->with('school')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.banners.index', [
            'banners' => $banners,
            'positions' => BannerPosition::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create', [
            'positions' => BannerPosition::cases(),
            'targets' => BannerTarget::cases(),
            'schools' => School::orderBy('name')->get(),
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['image'] = $request->file('image')->store('banners', 'public');
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['target_type'] !== BannerTarget::School->value) {
            $validated['school_id'] = null;
        }

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Bannière créée avec succès.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
            'positions' => BannerPosition::cases(),
            'targets' => BannerTarget::cases(),
            'schools' => School::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $validated['image'] = $request->file('image')->store('banners', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['target_type'] !== BannerTarget::School->value) {
            $validated['school_id'] = null;
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Bannière mise à jour.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Bannière supprimée.');
    }
}
