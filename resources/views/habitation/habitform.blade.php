<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis Assurance Habitation - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-3">
    <div class="container mx-auto p-4 max-w-2xl">
        <h1 class="text-2xl md:text-3xl font-bold text-green mb-6 text-center ">Votre Devis Assurance Habitation</h1>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Progress bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <span class="{{ $step >= 1 ? 'text-green-600 font-bold' : 'text-gray-540' }}">Info propriété</span>
                <span class="{{ $step >= 2 ? 'text-green-600 font-bold' : 'text-gray-400' }}">Adresse</span>
                <span class="{{ $step == 3 ? 'text-green-600 font-bold' : 'text-gray-400' }}">Résultat</span>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($step / 3) * 100 }}%"></div>
            </div>
        </div>
        <!-- Form -->
        @if ($step == 1 || $step == 2)
            <form method="POST" action="{{ route('habit.simulation.store') }}" class="bg-white p-6 rounded-lg shadow-md" x-data="formValidation()">
                @csrf
                <input type="hidden" name="step" value="{{ $step }}">
                @if ($step == 1)
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Sélectionnez votre type de logement</label>
                    <select name="housing_type" x-model="housing_type" @change="validateHousingType()" required class="w-full border rounded p-2" :class="{ 'border-red-500': housingTypeError }">
                        <option value="" disabled selected>Sélectionner</option>
                        <option value="APPARTEMENT">Appartement</option>
                        <option value="MAISON">Maison</option>
                        <option value="PAVILLON">Pavillon</option>
                        <option value="STUDIO">Studio</option>
                        <option value="LOFT">Loft</option>
                        <option value="VILLA">Villa</option>
                    </select>
                    <span x-show="housingTypeError" class="text-red-500 text-sm">Le type de propriété est requis.</span>
                    @error('housing_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Surface (m²)</label>
                    <input type="number" name="surface_area" x-model="surface_area" @input="validateSurfaceArea()" value="{{ $data['surface_area'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': surfaceError }">
                    <span x-show="surfaceError" class="text-red-500 text-sm">La surface est requise.</span>
                    @error('surface_area') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Statut d'Occupation</label>
                    <select name="occupancy_status" x-model="occupancy_status" @change="validateOccupancyStatus()" required class="w-full border rounded p-2" :class="{ 'border-red-500': occupancyStatusError }">
                        <option value="" disabled selected>Sélectionner</option>
                        <option value="Locataire">Locataire</option>
                        <option value="Propriétaire occupant">Propriétaire occupant</option>
                        <option value="Propriétaire non-occupant">Propriétaire non-occupant</option>
                    </select>
                    <span x-show="occupancyStatusError" class="text-red-500 text-sm">Le statut d'occupation est requis.</span>
                    @error('occupancy_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Valeur de la maison (DH)</label>
                    <input type="number" name="housing_value" x-model="housing_value" @input="validateHouseValue()" value="{{ $data['housing_value'] ?? '' }}" min="10000" required class="w-full border rounded p-2" :class="{ 'border-red-500': houseValueError }">
                    <span x-show="houseValueError" class="text-red-500 text-sm">La valeur doit être supérieure ou égale à 10000 DH.</span>
                    @error('housing_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Date de construction</label>
                    <input type="number" name="construction_year" x-model="construction_year" min="1800" max="{{ now()->year }}" placeholder="YYYY" @input="validateConstructionYear()" value="{{ $data['construction_year'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': constructionYearError }">
                    <span x-show="constructionYearError" class="text-red-500 text-sm">L'année doit être comprise entre 1800 et {{ date('Y') }}.</span>
                    @error('construction_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('habit.simulation.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Réinitialiser</a>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Suivant</button>
                </div>
            @elseif ($step == 2)
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Ville</label>
                    <input type="text" id="ville" name="ville" x-model="ville" @input="validateVille()" value="{{ $data['ville'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': villeError }">
                    <span x-show="villeError" class="text-red-500 text-sm">La ville est requise.</span>
                    @error('ville') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Rue</label>
                    <input type="text" id="rue" name="rue" x-model="rue" @input="validateRue()" value="{{ $data['rue'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': rueError }">
                    <span x-show="rueError" class="text-red-500 text-sm">La rue est requise.</span>
                    @error('rue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-green-700 font-medium mb-2">Code postal</label>
                    <input type="text" id="code_postal" name="code_postal" x-model="code_postal" @input="validateCodePostal()" value="{{ $data['code_postal'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': codePostalError }">
                    <span x-show="codePostalError" class="text-red-500 text-sm">Le code postal est requis.</span>
                    @error('code_postal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4 ">
                    <button type="button" onclick="getLocation()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Utiliser ma position actuelle</button>
                <p id="location-status" class="text-sm text-green-600 mt-2"></p>
                </div>
                    <div class="flex justify-between">
                    <a href="{{ route('habit.simulation.show', ['step' => 1]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-green-600">Retour</a>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Obtenir Devis</button>
                </div>
                @endif
            </form>
        @endif
             @if ($step == 3 && isset($data['devis_id']))
                <input type="hidden" name="devis_id" value="{{ $data['devis_id'] }}">
                 <div class="text-center">
                    @if (isset($data['devis_status']) && $data['devis_status'] == 'BROUILLON')
                        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                            <h3 class="text-lg font-semibold mb-4">Choisissez votre formule</h3>

                         <form id="offer-selection-form" method="POST" action="{{ route('habit.select_offer', ['devis_id' => $data['devis_id']]) }}">
                            @csrf
                            <input type="hidden" name="devis_id" value="{{ $data['devis_id'] }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach (['essentiel' => 'Essentiel', 'confort' => 'Confort', 'excellence' => 'Excellence'] as $key => $label)
                                    <div x-data="{ openCalculation: false, openGaranties: {} }" class="border rounded-lg p-4 {{ $data['selected_offer'] == $key ? 'border-green-600 bg-green-50' : 'border-gray-200' }} shadow-sm hover:shadow-md transition">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <label class="block text-lg font-medium text-gray-800">{{ $label }}</label>
                                                <p class="text-2xl font-bold text-green-700">{{ number_format($data['formules_choisis'][$key] ?? 0, 2) }} DH/an</p>
                                            </div>
                                            <input type="radio" name="offer" value="{{ $key }}" {{ $data['selected_offer'] == $key ? 'checked' : '' }} required class="h-5 w-5 text-green-600">
                                        </div>
                                        <h3 class="font-semibold mt-4 mb-2">Garanties incluses</h3>
                                        <ul class="text-sm text-gray-600 space-y-2">
                                            @if ($key == 'essentiel')
                                                <li x-data="{ open: false }">
                                                    <div class="flex justify-between items-center">
                                                        <span>Responsabilité Civile (RC)</span>
                                                        <button @click="open = !open" type="button" class="text-green-600 hover:text-green-800 text-xs flex items-center">
                                                            <span></span>
                                                            <svg x-bind:class="{ 'rotate-180': open }" class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div x-show="open" class="mt-1 text-xs bg-green-50 p-2 rounded">
                                                        Obligatoire si tu es locataire : couvre les dommages que tu pourrais causer au logement (incendie, explosion, dégât des eaux transmis aux voisins).

                                                        Si tu es propriétaire, elle protège aussi contre les réclamations de tiers (ex. ton chauffe-eau explose et cause des dégâts à ton voisin).
                                                    </div>
                                                </li>
                                            @elseif ($key == 'confort')
                                                @foreach (['Responsabilité Civile (RC)' => 'Obligatoire si tu es locataire : couvre les dommages que tu pourrais causer au logement (incendie, explosion, dégât des eaux transmis aux voisins).

Si tu es propriétaire, elle protège aussi contre les réclamations de tiers (ex. ton chauffe-eau explose et cause des dégâts à ton voisin).', 'Incendie' => 'Indemnise les dommages causés par un incendie ou une explosion.', 'Dégâts des eaux' => 'Couvre les Dégâts des eaux du logement ou les dommages liés à Dégâts des eaux.'] as $garantie => $description)
                                                    <li x-data="{ open: false }">
                                                        <div class="flex justify-between items-center">
                                                            <span>{{ $garantie }} {{ $garantie != 'Responsabilité Civile (RC)' ? '(' . number_format($data['calculation_factors'][strtolower(str_replace(' ', '_', $garantie)) . '_factor'] ?? 0, 2) . ' DH)' : '' }}</span>
                                                            <button @click="open = !open" type="button" class="text-green-600 hover:text-green-800 text-xs flex items-center">
                                                                <span></span>
                                                                <svg x-bind:class="{ 'rotate-180': open }" class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div x-show="open" class="mt-1 text-xs bg-gray-50 p-2 rounded">
                                                            {{ $description }} {{ $garantie != 'Responsabilité Civile (RC)' ? 'Coût: ' . number_format($data['calculation_factors'][strtolower(str_replace(' ', '_', $garantie)) . '_factor'] ?? 0, 2) . ' DH.' : '' }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @elseif ($key == 'excellence')
                                                @foreach (['Responsabilité Civile (RC)' =>
                                                'Obligatoire si tu es locataire : couvre les dommages que tu pourrais causer au logement (incendie, explosion, dégât des eaux transmis aux voisins).

                                                 Si tu es propriétaire, elle protège aussi contre les réclamations de tiers (ex. ton chauffe-eau explose et cause des dégâts à ton voisin).',
                                                'Incendie / explosion / foudre' => 'Indemnise les dommages causés par un incendie ou une explosion.',
                                                'Vol' => 'Couvre le vol du véhicule ou les dommages liés à une tentative de vol.',
                                                'Dégâts des eaux' => 'Couvre les Dégâts des eaux du logement ou les dommages liés à Dégâts des eaux.',
                                                'Bris de glace' => 'Prend en charge la réparation ou le remplacement.',
                                                'les Catastrophes naturelles' => 'Couvre les Catastrophes naturelles et événements climatiques (inondation, tempête, tremblement de terre…)',
                                                'Assistance Habitation' => 'Assistance habitation (plombier, serrurier en urgence, relogement provisoire, etc.)',
                                                ] as $garantie => $description)
                                                    <li x-data="{ open: false }">
                                                        <div class="flex justify-between items-center">
                                                            <span>{{ $garantie }} {{ $garantie != 'Responsabilité Civile (RC)' ? '(' . number_format($data['calculation_factors'][strtolower(str_replace(' ', '_', $garantie)) . '_factor'] ?? 0, 2) . ' DH)' : '' }}</span>
                                                            <button @click="open = !open" type="button" class="text-green-600 hover:text-green-800 text-xs flex items-center">
                                                                <span></span>
                                                                <svg x-bind:class="{ 'rotate-180': open }" class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div x-show="open" class="mt-1 text-xs bg-gray-50 p-2 rounded">
                                                            {{ $description }} {{ $garantie != 'Responsabilité Civile (RC)' ? 'Coût: ' . number_format($data['calculation_factors'][strtolower(str_replace(' ', '_', $garantie)) . '_factor'] ?? 0, 2) . ' DH.' : '' }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                            @error('offer') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                            <div class="flex justify-between mt-6">
                                <a href="{{ route('habit.simulation.show', ['step' => 2]) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Retour</a>
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirmer la formule</button>
                            </div>
                        </form>

                    </div>

                @endif
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('habit.simulation.reset') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Nouveau Devis</a>
                </div>
            </div>
        @else
            <div >
                <p></p>

        @endif



    </div>
    <script>
        function formValidation() {
            return {
                housing_type: '{{ $data['housing_type'] ?? '' }}',
                surface_area: '{{ $data['surface_area'] ?? '' }}',
                housing_value: '{{ $data['housing_value'] ?? '' }}',
                construction_year: '{{ $data['construction_year'] ?? '' }}',
                ville: '{{ $data['ville'] ?? '' }}',
                rue: '{{ $data['rue'] ?? '' }}',
                code_postal: '{{ $data['code_postal'] ?? '' }}',
                occupancy_status: '{{ $data['occupancy_status'] ?? '' }}',
                housingTypeError: false,
                surfaceError: false,
                houseValueError: false,
                constructionYearError: false,
                villeError: false,
                rueError: false,
                codePostalError: false,
                occupancyStatusError: false,
                validateHousingType() {
                    this.housingTypeError = !this.housing_type;
                },
                validateSurfaceArea() {
                    this.surfaceError = !this.surface_area || this.surface_area < 10;
                },
                validateHouseValue() {
                    this.houseValueError = !this.housing_value || this.housing_value < 10000;
                },
                validateConstructionYear() {
                    const year = parseInt(this.construction_year);
                    const currentYear = new Date().getFullYear();
                    this.constructionYearError = isNaN(year) || year < 1800 || year > currentYear || this.construction_year.length !== 4;
                },
                validateVille() {
                    this.villeError = !this.ville.trim();
                },
                validateRue() {
                    this.rueError = !this.rue.trim();
                },
                validateCodePostal() {
                    this.codePostalError = !this.code_postal.trim();
                },
                validateOccupancyStatus() {
                    this.occupancyStatusError = !this.occupancy_status;
                }
            }
        }
    </script>
    <script>
        function getLocation() {
    const status = document.getElementById("location-status");
    if (navigator.geolocation) {
        status.innerText = "Recherche de votre position...";
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        status.innerText = "La géolocalisation n'est pas supportée par votre navigateur.";
    }
}

function success(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;

fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`, {
    headers: {
        'User-Agent': 'NeoAssurancesApp/1.0 (contact@neoassurances.ma)', // obligatoire
        'Accept-Language': 'fr'
    }
})
.then(response => {
    if (!response.ok) throw new Error("HTTP error " + response.status);
    return response.json();
})
.then(data => {
    if (data.address) {
        document.getElementById("ville").value = data.address.city
            || data.address.town
            || data.address.village
            || "";
        document.getElementById("rue").value = data.address.road || "";
        document.getElementById("code_postal").value = data.address.postcode || "";

        document.getElementById("location-status").innerText = "✅ Adresse détectée automatiquement.";
    } else {
        document.getElementById("location-status").innerText = "Impossible de récupérer l'adresse.";
    }
})
.catch(err => {
    document.getElementById("location-status").innerText = "Erreur lors de la récupération de l'adresse : " + err.message;
});

}


function error(err) {
    document.getElementById("location-status").innerText = "Erreur de géolocalisation : " + err.message;
}
    </script>
</body>
</html>


