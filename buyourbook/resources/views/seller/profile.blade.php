<x-app-layout>
    <x-slot name="title">Vendeur — {{ $user->name }}</x-slot>

    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Profil vendeur --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold text-white" style="background-color: var(--color-primary);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500">Vendeur depuis {{ $user->created_at->translatedFormat('F Y') }}</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold" style="color: var(--color-primary);">{{ $totalBooks }}</p>
                        <p class="text-sm text-gray-500">Livres en vente</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold" style="color: var(--color-secondary);">{{ $totalSold }}</p>
                        <p class="text-sm text-gray-500">Livres vendus</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $books->total() }}</p>
                        <p class="text-sm text-gray-500">Offres actives</p>
                    </div>
                </div>
            </div>

            {{-- Livres en vente --}}
            <h2 class="text-xl font-bold text-gray-900 mb-4">Livres proposés par {{ $user->name }}</h2>

            @if($books->count() > 0)
                <div class="space-y-3">
                    @foreach($books as $book)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-md transition">
                            <div class="flex-shrink-0">
                                @if($book->officialBook->cover_image)
                                    <img src="{{ Storage::url($book->officialBook->cover_image) }}" alt="{{ $book->officialBook->title }}"
                                         class="w-16 h-20 object-cover rounded-lg">
                                @elseif($book->images && count($book->images) > 0)
                                    <img src="{{ Storage::url($book->images[0]) }}" alt="Photo"
                                         class="w-16 h-20 object-cover rounded-lg border">
                                @else
                                    <div class="w-16 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">📚</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('catalog.book', $book->officialBook) }}" class="font-semibold text-gray-900 hover:underline">
                                    {{ $book->officialBook->title }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $book->officialBook->subject->name ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $book->officialBook->grade->school->name ?? '' }} — {{ $book->officialBook->grade->name ?? '' }}
                                </p>
                                <span class="inline-flex mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $book->condition->color() }}-100 text-{{ $book->condition->color() }}-800">
                                    {{ $book->condition->label() }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 flex-shrink-0">
                                <div class="text-right">
                                    <p class="text-xl font-bold" style="color: var(--color-primary);">
                                        {{ number_format($book->price, 0, ',', ' ') }} F
                                    </p>
                                    <p class="text-xs text-gray-400">Dispo : {{ $book->quantity }}</p>
                                </div>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="seller_book_id" value="{{ $book->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-secondary !py-2 !px-4 text-sm">
                                        🛒 Ajouter
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $books->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-lg text-gray-400">Ce vendeur n'a aucune offre active pour le moment.</p>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
