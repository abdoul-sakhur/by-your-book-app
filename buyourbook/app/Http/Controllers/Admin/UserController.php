<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->withCount(['sellerBooks', 'orders'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = UserRole::cases();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user): View
    {
        $user->loadCount(['sellerBooks', 'orders']);

        return view('admin.users.show', compact('user'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        // Ne pas désactiver soi-même
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous désactiver.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $msg = $user->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.';
        return redirect()->back()->with('success', $msg);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string'],
        ]);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas changer votre propre rôle.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', 'Rôle mis à jour.');
    }
}
