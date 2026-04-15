<x-guest-layout>
    <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Vérification de l'email</h2>

    <div class="mb-4 text-sm text-gray-600">
        Merci pour votre inscription ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'email, nous vous en enverrons un autre.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Un nouveau lien de vérification a été envoyé à votre adresse email.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Renvoyer l'email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">
                Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
