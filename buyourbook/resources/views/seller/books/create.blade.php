<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Soumettre un livre</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">

                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Comment ça marche ?</strong> Sélectionnez l'école, la classe et le livre officiel correspondant.
                        Indiquez l'état, le prix et ajoutez des photos. Votre livre sera visible après validation par l'administrateur.
                    </p>
                </div>

                <form action="{{ route('seller.books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @include('seller.books._form')

                    <div class="flex items-center justify-end gap-3 mt-8">
                        <a href="{{ route('seller.books.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Annuler</a>
                        <button type="submit" class="btn-primary">Soumettre le livre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
