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
                        {{-- Approuver --}}
                        <form action="{{ route('admin.seller-books.approve', $sellerBook) }}" method="POST"
                              onsubmit="return confirm('Approuver ce livre ?')" class="flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                                ✓ Approuver
                            </button>
                        </form>

                        {{-- Refuser --}}
                        <div x-data="{ showReject: false }" class="flex-1">
                            <button @click="showReject = !showReject" type="button"
                                    class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                                ✗ Refuser
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
            @else
                {{-- Already decided --}}
                <div class="bg-gray-50 rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-600">
                        Ce livre a déjà été
                        <strong class="text-{{ $sellerBook->status->color() }}-700">{{ strtolower($sellerBook->status->label()) }}</strong>
                        le {{ $sellerBook->updated_at->format('d/m/Y à H:i') }}.
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
        </div>
    </div>
</x-admin-layout>
