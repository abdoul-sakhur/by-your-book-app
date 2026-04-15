<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contactez-nous</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Infos de contact --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Nos coordonnées</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <x-icon name="drawing-pin" class="w-5 h-5 mt-0.5 text-gray-400" />
                            <div>
                                <p class="font-medium text-gray-700">Adresse</p>
                                <p class="text-sm text-gray-500">Abidjan, Côte d'Ivoire</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-icon name="envelope-closed" class="w-5 h-5 mt-0.5 text-gray-400" />
                            <div>
                                <p class="font-medium text-gray-700">Email</p>
                                <p class="text-sm text-gray-500">contact@buyyourbook.ci</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-icon name="mobile" class="w-5 h-5 mt-0.5 text-gray-400" />
                            <div>
                                <p class="font-medium text-gray-700">Téléphone</p>
                                <p class="text-sm text-gray-500">+225 07 00 00 00 00</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-sm text-green-800">
                            <strong>Horaires :</strong> Lundi — Vendredi, 8h — 18h (GMT)
                        </p>
                    </div>
                </div>

                {{-- Formulaire --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Envoyez-nous un message</h3>

                    <form action="{{ route('pages.contact.send') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
                                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="message" id="message" rows="5" required
                                      class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="btn-primary w-full">Envoyer le message</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
