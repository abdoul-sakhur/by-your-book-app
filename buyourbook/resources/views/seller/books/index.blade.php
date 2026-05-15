<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes livres en vente</h2>
    </x-slot>

    <div class="py-8" x-data="{}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- En-tête avec bouton --}}
            <div class="flex items-center justify-between mb-6">
                <div></div>
                <a href="{{ route('seller.books.create') }}" class="btn-primary">
                    + Soumettre un livre
                </a>
            </div>

            {{-- Alerte collecte à domicile --}}
            @php $pickupBooks = $books->filter(fn($b) => $b->status === \App\Enums\BookStatus::PickupPending); @endphp
            @if($pickupBooks->count() > 0)
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-blue-900 text-sm">
                            Collecte à domicile prévue pour {{ $pickupBooks->count() > 1 ? $pickupBooks->count().' livres' : '1 livre' }}
                        </p>
                        <p class="text-blue-700 text-sm mt-0.5">
                            Notre livreur passera bientôt à votre adresse pour récupérer
                            @foreach($pickupBooks as $pb)
                                <strong>« {{ $pb->officialBook->title }} »</strong>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            et procéder au règlement. Assurez-vous d'être disponible.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Filtres --}}
            <form method="GET" class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Titre du livre…"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                        <option value="">Tous</option>
                        @foreach(\App\Enums\BookStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filtrer</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('seller.books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
                @endif
            </form>

            {{-- Tableau --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">École / Classe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qté</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($books as $book)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $book->officialBook->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $book->officialBook->subject->name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $book->officialBook->grade->school->name ?? '' }}<br>
                                    <span class="text-xs text-gray-400">{{ $book->officialBook->grade->name ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $book->condition->color() }}-100 text-{{ $book->condition->color() }}-800">
                                        {{ $book->condition->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ number_format($book->price, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $book->quantity }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $book->status->color() }}-100 text-{{ $book->status->color() }}-800">
                                        {{ $book->status->label() }}
                                    </span>
                                    @if($book->status === \App\Enums\BookStatus::PickupPending)
                                        <p class="text-xs text-blue-700 mt-1 leading-snug">
                                            Notre équipe passera bientôt<br>récupérer le livre chez vous.
                                        </p>
                                    @endif
                                    @if($book->status === \App\Enums\BookStatus::Rejected && $book->rejection_reason)
                                        <p class="text-xs text-red-600 mt-1">{{ $book->rejection_reason }}</p>
                                    @endif
                                    {{-- Badge offre de rachat --}}
                                    @if($book->buyback_status === 'negotiating' && $book->buyback_price)
                                        <span class="mt-1 inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Offre rachat : {{ number_format($book->buyback_price, 0, ',', ' ') }} F
                                        </span>
                                    @elseif($book->buyback_status === 'accepted')
                                        <span class="mt-1 inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Rachat accepté{{ $book->admin_paid_seller ? ' · Payé ✓' : '' }}
                                        </span>
                                    @elseif($book->buyback_status === 'rejected')
                                        <span class="mt-1 inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Rachat refusé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    @if(in_array($book->status, [\App\Enums\BookStatus::Pending, \App\Enums\BookStatus::Rejected]))
                                        <a href="{{ route('seller.books.edit', $book) }}"
                                           style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.85rem;background:#4f46e5;color:#fff;border-radius:0.4rem;font-size:0.78rem;font-weight:600;text-decoration:none;transition:opacity .2s;"
                                           onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:.85rem;height:.85rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Modifier
                                        </a>
                                    @endif
                                    @unless($book->orderItems()->exists())
                                        <form action="{{ route('seller.books.destroy', $book) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer ce livre ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.85rem;background:#dc2626;color:#fff;border-radius:0.4rem;font-size:0.78rem;font-weight:600;border:none;cursor:pointer;transition:opacity .2s;"
                                                    onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="width:.85rem;height:.85rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Supprimer
                                            </button>
                                        </form>
                                    @endunless
                                    {{-- Répondre à une offre de rachat --}}
                                    @if($book->buyback_status === 'negotiating' && $book->buyback_price)
                                        <button type="button"
                                                @click="$dispatch('open-buyback', { id: {{ $book->id }}, price: {{ $book->buyback_price }}, notes: {{ json_encode($book->buyback_notes ?? '') }} })"
                                                style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.85rem;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border-radius:0.4rem;font-size:0.78rem;font-weight:600;border:none;cursor:pointer;transition:opacity .2s;"
                                                onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                                            <svg xmlns="http://www.w3.org/2000/svg" style="width:.85rem;height:.85rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            Répondre
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    Aucun livre soumis. <a href="{{ route('seller.books.create') }}" class="text-indigo-600 hover:underline">Soumettre votre premier livre</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $books->links() }}</div>
        </div>
    </div>

    {{-- Modal réponse offre de rachat --}}
    <div x-data="{
            open: false, bookId: null, offerPrice: 0, offerNotes: '',
            action: 'accept', counterPrice: ''
         }"
         @open-buyback.window="open = true; bookId = $event.detail.id; offerPrice = $event.detail.price; offerNotes = $event.detail.notes; action = 'accept'; counterPrice = ''">

        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div @click.outside="open = false" class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Offre de rachat</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        L'administrateur propose de racheter ce livre pour
                        <strong x-text="offerPrice.toLocaleString('fr-FR') + ' FCFA'"></strong>.
                    </p>
                    <p x-show="offerNotes" class="text-sm italic text-gray-500 mb-4" x-text="offerNotes"></p>

                    <form :action="'/seller/books/' + bookId + '/buyback-respond'" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="action" value="accept" x-model="action" class="text-green-600">
                                <span class="text-sm font-medium text-gray-800">Accepter l'offre</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="action" value="counter" x-model="action" class="text-blue-600">
                                <span class="text-sm font-medium text-gray-800">Faire une contre-offre</span>
                            </label>
                            <div x-show="action === 'counter'" x-transition class="ml-7">
                                <input type="number" name="counter_price" x-model="counterPrice"
                                       :required="action === 'counter'"
                                       min="1" placeholder="Votre prix (FCFA)"
                                       class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="action" value="reject" x-model="action" class="text-red-600">
                                <span class="text-sm font-medium text-gray-800">Refuser l'offre</span>
                            </label>
                        </div>
                        <div class="mt-6 flex gap-3 justify-end">
                            <button type="button" @click="open = false"
                                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Annuler</button>
                            <button type="submit"
                                    :style="action === 'reject'
                                        ? 'background:linear-gradient(135deg,#dc2626,#b91c1c);box-shadow:0 2px 6px rgba(220,38,38,.35);'
                                        : action === 'accept'
                                            ? 'background:linear-gradient(135deg,#16a34a,#15803d);box-shadow:0 2px 6px rgba(22,163,74,.35);'
                                            : 'background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 2px 6px rgba(37,99,235,.35);'"
                                    style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.4rem;color:#fff;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;transition:opacity .2s;"
                                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Confirmer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
