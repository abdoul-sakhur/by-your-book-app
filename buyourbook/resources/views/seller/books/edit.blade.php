<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le livre</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">

                @if($book->status === \App\Enums\BookStatus::Rejected && $book->rejection_reason)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm font-semibold text-red-800">Raison du refus :</p>
                        <p class="text-sm text-red-700 mt-1">{{ $book->rejection_reason }}</p>
                    </div>
                @endif

                <form action="{{ route('seller.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    @include('seller.books._form')

                    <div class="flex items-center justify-end gap-3 mt-8">
                        <a href="{{ route('seller.books.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Annuler</a>
                        <button type="submit" class="btn-primary">Enregistrer & re-soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
