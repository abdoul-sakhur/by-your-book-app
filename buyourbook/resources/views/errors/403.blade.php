@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center px-6">
        <div class="text-8xl font-bold text-primary mb-4">403</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Accès interdit</h1>
        <p class="text-gray-600 mb-8">Vous n'avez pas la permission d'accéder à cette page.</p>
        <a href="{{ route('home') }}" class="btn-primary inline-block">Retour à l'accueil</a>
    </div>
</div>
@endsection
