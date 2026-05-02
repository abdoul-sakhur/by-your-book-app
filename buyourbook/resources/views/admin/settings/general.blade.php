<x-admin-layout>
    <x-slot name="header">Paramètres généraux</x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.general.save') }}" method="POST" class="max-w-2xl space-y-6">
        @csrf

        {{-- Livraison --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-icon name="bag" class="w-5 h-5 text-gray-400" /> Livraison à domicile
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">
                        Frais de livraison (FCFA)
                    </label>
                    <input type="number" id="delivery_fee" name="delivery_fee"
                           value="{{ old('delivery_fee', $delivery_fee) }}"
                           min="0" step="100"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                    @error('delivery_fee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Montant fixe facturé à chaque commande.</p>
                </div>
                <div>
                    <label for="free_delivery_threshold" class="block text-sm font-medium text-gray-700 mb-1">
                        Seuil de livraison gratuite (FCFA)
                    </label>
                    <input type="number" id="free_delivery_threshold" name="free_delivery_threshold"
                           value="{{ old('free_delivery_threshold', $free_delivery_threshold) }}"
                           min="0" step="1000"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                    @error('free_delivery_threshold')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Livraison offerte au-dessus de ce montant. Mettre 0 pour désactiver.</p>
                </div>
            </div>
        </div>

        {{-- Chat Tawk.to --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <x-icon name="chat-bubble" class="w-5 h-5 text-gray-400" /> Widget de chat (Tawk.to)
            </h3>
            <div>
                <label for="tawkto_widget_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Identifiant Tawk.to
                </label>
                <input type="text" id="tawkto_widget_id" name="tawkto_widget_id"
                       value="{{ old('tawkto_widget_id', $tawkto_widget_id) }}"
                       placeholder="ex: 6441a4d54247f20fec6b5b7a/1gue4v5vt"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] font-mono text-sm">
                @error('tawkto_widget_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">
                    Trouvez votre ID sur <strong>tawk.to → Administration → Widget → Code d'intégration</strong>.
                    Format : <code class="bg-gray-100 px-1 rounded">PROPERTY_ID/WIDGET_ID</code>.<br>
                    Laissez vide pour désactiver le chat.
                </p>
            </div>
            @if($tawkto_widget_id)
                <div class="mt-3 inline-flex items-center gap-1.5 text-xs text-green-700 bg-green-50 px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Chat actif
                </div>
            @else
                <div class="mt-3 inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span> Chat désactivé
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Enregistrer les paramètres</button>
            <a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Tous les paramètres →</a>
        </div>
    </form>
</x-admin-layout>
