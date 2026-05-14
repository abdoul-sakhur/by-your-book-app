<x-admin-layout>
    <x-slot name="header">Examen du livre — {{ $sellerBook->officialBook->title }}</x-slot>

    <div class="p-6">
        <div class="max-w-4xl mx-auto">

            {{-- Infos vendeur --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations vendeur</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nom</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->seller->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->seller->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Téléphone</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->seller->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Détails du livre --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails de l'annonce</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Livre officiel</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->officialBook->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Matière</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->officialBook->subject->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">École</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->officialBook->grade->school->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Classe</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->officialBook->grade->name ?? '—' }} ({{ $sellerBook->officialBook->grade->level ?? '' }})</p>
                    </div>
                    <div>
                        <p class="text-gray-500">État du livre</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $sellerBook->condition->color() }}-100 text-{{ $sellerBook->condition->color() }}-800">
                            {{ $sellerBook->condition->label() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500">Prix demandé</p>
                        <p class="font-bold text-lg text-gray-900">{{ number_format($sellerBook->price, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Quantité</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->quantity }}</p>
                    </div>
                    @if($sellerBook->author)
                    <div>
                        <p class="text-gray-500">Auteur</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->author }}</p>
                    </div>
                    @endif
                    @if($sellerBook->isbn)
                    <div>
                        <p class="text-gray-500">ISBN</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->isbn }}</p>
                    </div>
                    @endif
                    @if($sellerBook->publisher)
                    <div>
                        <p class="text-gray-500">Éditeur</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->publisher }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-500">Statut actuel</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $sellerBook->status->color() }}-100 text-{{ $sellerBook->status->color() }}-800">
                            {{ $sellerBook->status->label() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500">Soumis le</p>
                        <p class="font-medium text-gray-900">{{ $sellerBook->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                {{-- Images --}}
                @if($sellerBook->images && count($sellerBook->images) > 0)
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-2">Photos</p>
                        <div class="flex gap-4 flex-wrap">
                            @foreach($sellerBook->images as $img)
                                <a href="{{ Storage::url($img) }}" target="_blank">
                                    <img src="{{ Storage::url($img) }}" alt="Photo du livre"
                                         class="w-32 h-32 object-cover rounded-lg border hover:opacity-75 transition">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Actions de validation --}}
            @if($sellerBook->status === \App\Enums\BookStatus::Pending)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Décision</h3>

                    <div class="flex flex-col sm:flex-row gap-4">
                        {{-- Valider (→ pickup_pending) --}}
                        <form action="{{ route('admin.seller-books.approve', $sellerBook) }}" method="POST"
                              onsubmit="return confirm('Valider ce livre et notifier le vendeur pour la collecte à domicile ?')" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                                <x-icon name="check" class="w-5 h-5 inline" /> Valider le livre
                            </button>
                            <p class="text-xs text-gray-500 mt-1 text-center">Le vendeur sera notifié d'une collecte à domicile</p>
                        </form>

                        {{-- Refuser --}}
                        <div x-data="{ showReject: false }" class="flex-1">
                            <button @click="showReject = !showReject" type="button"
                                    class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                                <x-icon name="cross-2" class="w-5 h-5 inline" /> Refuser
                            </button>

                            <form x-show="showReject" x-transition
                                  action="{{ route('admin.seller-books.reject', $sellerBook) }}" method="POST" class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Raison du refus <span class="text-red-500">*</span></label>
                                    <textarea name="rejection_reason" rows="3" required
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                              placeholder="Expliquez la raison du refus au vendeur…">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes admin (internes)</label>
                                    <textarea name="admin_notes" rows="2"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-400 focus:ring-gray-400"
                                              placeholder="Notes internes (non visibles par le vendeur)…">{{ old('admin_notes') }}</textarea>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                                    Confirmer le refus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            @elseif($sellerBook->status === \App\Enums\BookStatus::PickupPending)
                {{-- ===== SECTION COLLECTE À DOMICILE ===== --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-sm p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <x-icon name="map-pin" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">Collecte à domicile en attente</h3>
                            <p class="text-sm text-blue-700 mt-0.5">
                                Le livre a été validé. Un livreur doit se rendre chez le vendeur pour récupérer le livre et collecter le paiement.
                            </p>
                        </div>
                    </div>

                    {{-- Infos de contact du vendeur --}}
                    <div class="bg-white rounded-lg p-4 mb-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase">Vendeur</p>
                            <p class="font-semibold text-gray-900 mt-0.5">{{ $sellerBook->seller->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase">Téléphone</p>
                            <p class="font-semibold text-gray-900 mt-0.5">
                                @if($sellerBook->seller->phone)
                                    <a href="tel:{{ $sellerBook->seller->phone }}" class="text-blue-600 hover:underline">{{ $sellerBook->seller->phone }}</a>
                                @else
                                    <span class="text-gray-400 italic">Non renseigné</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase">Email</p>
                            <p class="font-semibold text-gray-900 mt-0.5">
                                <a href="mailto:{{ $sellerBook->seller->email }}" class="text-blue-600 hover:underline text-xs">{{ $sellerBook->seller->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        {{-- Marquer collecté → approved --}}
                        <form action="{{ route('admin.seller-books.mark-collected', $sellerBook) }}" method="POST"
                              onsubmit="return confirm('Confirmer que le livre ET le paiement ont bien été collectés à domicile ?')" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                                <x-icon name="check" class="w-5 h-5 inline" /> Collecte effectuée — Mettre en ligne
                            </button>
                            <p class="text-xs text-gray-500 mt-1 text-center">Le livre sera publié dans le catalogue et le vendeur notifié</p>
                        </form>

                        {{-- Refuser même depuis pickup_pending --}}
                        <div x-data="{ showReject: false }" class="flex-1">
                            <button @click="showReject = !showReject" type="button"
                                    class="w-full px-6 py-3 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition">
                                <x-icon name="cross-2" class="w-5 h-5 inline" /> Annuler et refuser
                            </button>
                            <form x-show="showReject" x-transition
                                  action="{{ route('admin.seller-books.reject', $sellerBook) }}" method="POST" class="mt-4 space-y-3">
                                @csrf
                                <textarea name="rejection_reason" rows="3" required
                                          class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                          placeholder="Raison du refus (visible par le vendeur)…">{{ old('rejection_reason') }}</textarea>
                                <input type="hidden" name="admin_notes" value="">
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                                    Confirmer l'annulation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            @else
                {{-- Approved or Rejected — already decided --}}
                <div class="bg-gray-50 rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-600">
                        Ce livre est
                        <strong class="text-{{ $sellerBook->status->color() }}-700">{{ strtolower($sellerBook->status->label()) }}</strong>
                        depuis le {{ $sellerBook->updated_at->format('d/m/Y à H:i') }}.
                    </p>
                    @if($sellerBook->rejection_reason)
                        <p class="mt-2 text-sm text-red-600"><strong>Raison :</strong> {{ $sellerBook->rejection_reason }}</p>
                    @endif
                    @if($sellerBook->admin_notes)
                        <p class="mt-2 text-sm text-gray-500"><strong>Notes admin :</strong> {{ $sellerBook->admin_notes }}</p>
                    @endif
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('admin.seller-books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour à la liste</a>
            </div>

            {{-- ===== SECTION RACHAT ===== --}}
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">Rachat de livre</h3>
                <p class="text-sm text-gray-500 mb-5">
                    Proposez un prix au vendeur pour racheter ce livre. Le vendeur pourra accepter ou refuser.
                </p>

                {{-- Prix d'achat du vendeur --}}
                <div class="mb-4 flex items-center gap-3">
                    <span class="text-sm text-gray-600">Prix d'achat déclaré par le vendeur :</span>
                    @if($sellerBook->purchase_price)
                        <span class="font-semibold text-gray-900">{{ number_format($sellerBook->purchase_price, 0, ',', ' ') }} FCFA</span>
                    @else
                        <span class="text-gray-400 italic">Non renseigné</span>
                    @endif
                </div>

                {{-- Statut actuel --}}
                @php
                    $bsColors = ['pending'=>'gray','negotiating'=>'blue','accepted'=>'green','rejected'=>'red'];
                    $bsLabels = ['pending'=>'Aucune offre','negotiating'=>'Offre en cours','accepted'=>'Accepté','rejected'=>'Refusé'];
                    $bs = $sellerBook->buyback_status ?? 'pending';
                    $bsColor = $bsColors[$bs] ?? 'gray';
                    $bsLabel = $bsLabels[$bs] ?? $bs;
                @endphp
                <div class="flex flex-wrap items-center gap-4 mb-5">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-{{ $bsColor }}-100 text-{{ $bsColor }}-800">
                        {{ $bsLabel }}
                    </span>
                    @if($sellerBook->buyback_price)
                        <span class="text-sm text-gray-700">Offre admin : <strong>{{ number_format($sellerBook->buyback_price, 0, ',', ' ') }} FCFA</strong></span>
                    @endif
                    @if($sellerBook->counter_price)
                        <span class="text-sm text-amber-700">Contre-offre vendeur : <strong>{{ number_format($sellerBook->counter_price, 0, ',', ' ') }} FCFA</strong></span>
                    @endif
                    @if($sellerBook->admin_paid_seller)
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Payé</span>
                    @endif
                </div>
                @if($sellerBook->buyback_notes)
                    <p class="text-sm text-gray-600 mb-4 italic">{{ $sellerBook->buyback_notes }}</p>
                @endif

                <div class="flex flex-wrap gap-4">
                    {{-- Formulaire d'offre (toujours disponible pour modifier) --}}
                    @unless($sellerBook->admin_paid_seller)
                    <form action="{{ route('admin.seller-books.buyback-propose', $sellerBook) }}" method="POST"
                          x-data="{ open: false }" class="flex-1 min-w-[260px]">
                        @csrf
                        <button type="button" @click="open = !open"
                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                            {{ $sellerBook->buyback_status === 'negotiating' ? 'Modifier l\'offre' : 'Proposer un rachat' }}
                        </button>
                        <div x-show="open" x-transition class="mt-3 space-y-3 border border-blue-200 rounded-lg p-4 bg-blue-50">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Prix proposé (FCFA) <span class="text-red-500">*</span></label>
                                <input type="number" name="buyback_price" min="1"
                                       value="{{ old('buyback_price', $sellerBook->buyback_price) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                       placeholder="ex: 5000">
                                @error('buyback_price')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Message au vendeur</label>
                                <textarea name="buyback_notes" rows="2"
                                          class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                          placeholder="Explication facultative…">{{ old('buyback_notes', $sellerBook->buyback_notes) }}</textarea>
                            </div>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-700 text-white text-sm rounded-lg hover:bg-blue-800">
                                Envoyer l'offre
                            </button>
                        </div>
                    </form>
                    @endunless

                    {{-- Marquer comme payé --}}
                    @if($sellerBook->buyback_status === 'accepted' && !$sellerBook->admin_paid_seller)
                        <form action="{{ route('admin.seller-books.mark-paid', $sellerBook) }}" method="POST"
                              onsubmit="return confirm('Confirmer le paiement au vendeur ?')">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition">
                                ✓ Marquer comme payé
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            {{-- ===== FIN SECTION RACHAT ===== --}}

        </div>
    </div>
</x-admin-layout>
