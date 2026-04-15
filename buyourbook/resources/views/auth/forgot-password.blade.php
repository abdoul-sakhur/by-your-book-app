<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Mot de passe oublié</h2>

    <div class="mb-4 text-sm text-gray-600">
        Pas de souci ! Indiquez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary w-full text-center">Envoyer le lien</button>
        </div>

        <div class="mt-4 text-center">
            <a class="text-sm font-medium hover:underline" style="color: var(--color-primary);" href="{{ route('login') }}">
                Retour à la connexion
            </a>
        </div>
    </form>
</x-guest-layout>
