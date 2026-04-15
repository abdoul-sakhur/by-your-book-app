<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Créer un compte</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Téléphone -->
        <div class="mt-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                   placeholder="+225 07 00 00 00 00"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Rôle -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Je souhaite</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex items-center justify-center px-4 py-3 rounded-lg border-2 cursor-pointer transition
                    {{ old('role', 'buyer') === 'buyer' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}"
                    onclick="this.parentElement.querySelectorAll('label').forEach(l => { l.classList.remove('border-green-500','bg-green-50'); l.classList.add('border-gray-200'); }); this.classList.remove('border-gray-200'); this.classList.add('border-green-500','bg-green-50');">
                    <input type="radio" name="role" value="buyer" {{ old('role', 'buyer') === 'buyer' ? 'checked' : '' }} class="sr-only">
                    <span class="text-sm font-medium text-gray-700"><x-icon name="backpack" class="w-4 h-4 inline" /> Acheter</span>
                </label>
                <label class="relative flex items-center justify-center px-4 py-3 rounded-lg border-2 cursor-pointer transition
                    {{ old('role') === 'seller' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}"
                    onclick="this.parentElement.querySelectorAll('label').forEach(l => { l.classList.remove('border-green-500','bg-green-50'); l.classList.add('border-gray-200'); }); this.classList.remove('border-gray-200'); this.classList.add('border-green-500','bg-green-50');">
                    <input type="radio" name="role" value="seller" {{ old('role') === 'seller' ? 'checked' : '' }} class="sr-only">
                    <span class="text-sm font-medium text-gray-700"><x-icon name="tokens" class="w-4 h-4 inline" /> Vendre</span>
                </label>
            </div>
            @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Mot de passe -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Confirmer mot de passe -->
        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary w-full text-center">S'inscrire</button>
        </div>

        <div class="mt-4 text-center">
            <a class="text-sm font-medium hover:underline" style="color: var(--color-primary);" href="{{ route('login') }}">
                Déjà inscrit ? Se connecter
            </a>
        </div>
    </form>
</x-guest-layout>
