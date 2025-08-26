<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents et Agence - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 max-w-4xl">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">Finaliser votre contrat</h1>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('auto.documents.store', ['devis_id' => $devis_id]) }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md" x-data="{ selectedAgence: {{ session('auto_data.agence_id', 'null') }} }">
            @csrf
            <h2 class="text-xl font-semibold mb-4">Télécharger les documents (images uniquement : JPG, PNG)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="carte_grise">Carte Grise</label>
                    <input type="file" name="carte_grise" accept="image/jpeg,image/png" required class="w-full border rounded p-2">
                    @error('carte_grise') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="permis">Permis de Conduire</label>
                    <input type="file" name="permis" accept="image/jpeg,image/png" required class="w-full border rounded p-2">
                    @error('permis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="cin_recto">CIN (Recto)</label>
                    <input type="file" name="cin_recto" accept="image/jpeg,image/png" required class="w-full border rounded p-2">
                    @error('cin_recto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="cin_verso">CIN (Verso)</label>
                    <input type="file" name="cin_verso" accept="image/jpeg,image/png" required class="w-full border rounded p-2">
                    @error('cin_verso') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <h2 class="text-xl font-semibold mb-4">Choisir une agence</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @foreach ($agences as $agence)
                    <div x-data="{ mapLoaded: false }" class="border rounded-lg p-4" :class="{ 'border-blue-600 bg-blue-50': selectedAgence == {{ $agence->id }} }" @click="selectedAgence = {{ $agence->id }}; $refs.agenceRadio{{ $agence->id }}.checked = true">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium">{{ $agence->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $agence->address }}</p>
                                <p class="text-sm text-gray-600">Tél: {{ $agence->phone }}</p>
                                <p class="text-sm text-gray-600">Email: {{ $agence->email }}</p>
                            </div>
                            <input type="radio" name="agence_id" value="{{ $agence->id }}" x-ref="agenceRadio{{ $agence->id }}" :checked="selectedAgence == {{ $agence->id }}" required class="h-5 w-5 text-blue-600">
                        </div>
                        <button @click="mapLoaded = !mapLoaded" type="button" class="mt-2 text-blue-600 hover:text-blue-800 text-sm flex items-center">
                            <span x-text="mapLoaded ? 'Masquer la carte' : 'Voir la carte'"></span>
                            <svg x-bind:class="{ 'rotate-180': mapLoaded }" class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="mapLoaded" class="mt-2 h-64">
                            <div x-init="new google.maps.Map($el, {
                                center: { lat: {{ $agence->latitude ?? 33.5731 }}, lng: {{ $agence->longitude ?? -7.5898 }} },
                                zoom: 15
                            }); new google.maps.Marker({
                                position: { lat: {{ $agence->latitude ?? 33.5731 }}, lng: {{ $agence->longitude ?? -7.5898 }} },
                                map: $el,
                                title: '{{ $agence->name }}'
                            });" class="h-full w-full rounded"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('agence_id') <span class="text-red-500 text-sm mb-4 block">{{ $message }}</span> @enderror
            <div class="flex justify-between">
                <a href="{{ route('auto.result', ['devis_id' => $devis_id]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Finaliser le contrat</button>
            </div>
        </form>
    </div>
</body>
</html>