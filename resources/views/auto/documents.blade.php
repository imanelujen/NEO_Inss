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
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-white shadow-lg rounded-xl p-6 sm:p-8 animate-fade-in">
        
        <!-- Header -->
        <div class="form-card-header rounded-lg mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold">Finaliser votre contrat</h1>
        </div>

        <!-- Alerts -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('auto.documents.store', ['devis_id' => $devis_id]) }}" 
              enctype="multipart/form-data" 
              x-data="{ selectedAgence: {{ session('auto_data.agence_id', 'null') }} }"
              class="space-y-6">
            @csrf

            <!-- Documents Upload -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Télécharger les documents</h2>
                <p class="text-sm text-gray-500 mb-4">Formats acceptés : JPG, PNG, JPEG</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ([
                        'carte_grise' => 'Carte Grise',
                        'permis' => 'Permis de Conduire',
                        'cin_recto' => 'CIN (Recto)',
                        'cin_verso' => 'CIN (Verso)'
                    ] as $field => $label)
                        <div>
                            <label for="{{ $field }}" class="block text-blue-900 mb-2">{{ $label }}</label>
                            <input type="file" name="{{ $field }}" id="{{ $field }}" 
                                   accept="image/jpeg,image/png" required
                                   class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500">
                            @error($field) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Agencies -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Choisir une agence</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($agences as $agence)
                        <div x-data="{ mapLoaded: false }" 
                             class="border rounded-lg p-4 transition hover:shadow-md cursor-pointer"
                             :class="{ 'border-blue-600 bg-blue-50': selectedAgence == {{ $agence->id }} }"
                             @click="selectedAgence = {{ $agence->id }}; $refs.agenceRadio{{ $agence->id }}.checked = true">
                            
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-blue-900">{{ $agence->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $agence->address }}</p>
                                    <p class="text-sm text-gray-600">📞 {{ $agence->phone }}</p>
                                    <p class="text-sm text-gray-600">✉️ {{ $agence->email }}</p>
                                </div>
                                <input type="radio" name="agence_id" value="{{ $agence->id }}" 
                                       x-ref="agenceRadio{{ $agence->id }}" 
                                       :checked="selectedAgence == {{ $agence->id }}" 
                                       required class="h-5 w-5 text-blue-600">
                            </div>

                            <!-- Toggle Map -->
                            <button @click="mapLoaded = !mapLoaded" type="button" 
                                    class="mt-3 text-green-600 hover:text-green-800 text-sm flex items-center">
                                <span x-text="mapLoaded ? 'Masquer la carte' : 'Voir la carte'"></span>
                                <svg x-bind:class="{ 'rotate-180': mapLoaded }" 
                                     class="ml-1 h-4 w-4 transition-transform" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Google Map -->
                            <div x-show="mapLoaded" class="mt-3 h-64 rounded overflow-hidden">
                                <div x-init="new google.maps.Map($el, {
                                    center: { lat: {{ $agence->latitude ?? 33.5731 }}, lng: {{ $agence->longitude ?? -7.5898 }} },
                                    zoom: 15
                                }); new google.maps.Marker({
                                    position: { lat: {{ $agence->latitude ?? 33.5731 }}, lng: {{ $agence->longitude ?? -7.5898 }} },
                                    map: $el,
                                    title: '{{ $agence->name }}'
                                });" class="h-full w-full"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('agence_id') <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> @enderror
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <a href="{{ route('auto.result', ['devis_id' => $devis_id]) }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded text-center">
                    Retour
                </a>
                <button type="submit" 
                        class="bg-green-600 text-white px-4 py-2 rounded">
                    Finaliser le contrat
                </button>
            </div>
        </form>
    </div>
</body>
</html>
