@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center px-6">
        <div class="text-8xl font-bold text-primary mb-4">404</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Page introuvable</h1>
        <p class="text-gray-600 mb-8">La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="btn-primary inline-block">Retour à l'accueil</a>
            <a href="{{ route('catalogue.index') }}" class="btn-secondary inline-block">Voir le catalogue</a>
        </div>
    </div>
</div>
@endsection
