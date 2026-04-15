<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Confirmer le mot de passe</h2>

    <div class="mb-4 text-sm text-gray-600">
        Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary w-full text-center">Confirmer</button>
        </div>
    </form>
</x-guest-layout>
