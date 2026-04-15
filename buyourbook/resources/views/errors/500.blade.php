<x-app-layout>
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center px-6">
        <div class="text-8xl font-bold text-primary mb-4">500</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Erreur serveur</h1>
        <p class="text-gray-600 mb-8">Une erreur interne est survenue. Veuillez réessayer plus tard.</p>
        <a href="{{ route('home') }}" class="btn-primary inline-block">Retour à l'accueil</a>
    </div>
</div>
</x-app-layout>
