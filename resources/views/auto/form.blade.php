<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis Assurance Auto - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-4 max-w-4xl">
        <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Devis Assurance Auto</h1>
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

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <span class="{{ $step >= 1 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Info Véhicule</span>
                <span class="{{ $step >= 2 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Conducteur</span>
                <span class="{{ $step == 3 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Résultat</span>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($step / 3) * 100 }}%"></div>
            </div>
        </div>

        <!-- Form -->
        @if ($step == 1 || $step == 2)
            <form method="POST" action="{{ route('auto.store') }}" class="bg-white p-6 rounded-lg shadow-md" x-data="formValidation()">
                @csrf
                <input type="hidden" name="step" value="{{ $step }}">
                @if ($step == 1)
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="vehicle_type">Type de véhicule</label>
                        <select name="vehicle_type" id="vehicle_type" x-model="vehicle_type" @change="validateVehicleType()" required class="w-full border rounded p-2" :class="{ 'border-red-500': vehicleTypeError }">
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="sedan" {{ old('vehicle_type', $data['vehicle_type'] ?? '') == 'sedan' ? 'selected' : '' }}>Sedan</option>
                            <option value="suv" {{ old('vehicle_type', $data['vehicle_type'] ?? '') == 'suv' ? 'selected' : '' }}>SUV</option>
                            <option value="truck" {{ old('vehicle_type', $data['vehicle_type'] ?? '') == 'truck' ? 'selected' : '' }}>Camion</option>
                            <option value="motorcycle" {{ old('vehicle_type', $data['vehicle_type'] ?? '') == 'motorcycle' ? 'selected' : '' }}>Moto</option>
                        </select>
                        <span x-show="vehicleTypeError" class="text-red-500 text-sm">Le type de véhicule est requis.</span>
                        @error('vehicle_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="make">Marque</label>
                        <input type="text" name="make" x-model="make" @input="validateMake()" value="{{ old('make', $data['make'] ?? '') }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': makeError }">
                        <span x-show="makeError" class="text-red-500 text-sm">La marque est requise.</span>
                        @error('make') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="model">Modèle</label>
                        <input type="text" name="model" x-model="model" @input="validateModel()" value="{{ old('model', $data['model'] ?? '') }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': modelError }">
                        <span x-show="modelError" class="text-red-500 text-sm">Le modèle est requis.</span>
                        @error('model') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="fuel_type">Carburant</label>
                        <select name="fuel_type" id="fuel_type" x-model="fuel_type" @change="validateFuelType()" required class="w-full border rounded p-2" :class="{ 'border-red-500': fuelTypeError }">
                            <option value="" disabled selected>Sélectionner</option>
                            <option value="ESSENCE" {{ old('fuel_type', $data['fuel_type'] ?? '') == 'ESSENCE' ? 'selected' : '' }}>Essence</option>
                            <option value="DIESEL" {{ old('fuel_type', $data['fuel_type'] ?? '') == 'DIESEL' ? 'selected' : '' }}>Diesel</option>
                            <option value="ELECTRIQUE" {{ old('fuel_type', $data['fuel_type'] ?? '') == 'ELECTRIQUE' ? 'selected' : '' }}>Électrique</option>
                            <option value="HYBRIDE" {{ old('fuel_type', $data['fuel_type'] ?? '') == 'HYBRIDE' ? 'selected' : '' }}>Hybride</option>
                        </select>
                        <span x-show="fuelTypeError" class="text-red-500 text-sm">Le type de carburant est requis.</span>
                        @error('fuel_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="tax_horsepower">Puissance Fiscale (CV)</label>
                        <input type="number" name="tax_horsepower" x-model="tax_horsepower" @input="validateTaxHorsepower()" value="{{ old('tax_horsepower', $data['tax_horsepower'] ?? '') }}" min="1" required class="w-full border rounded p-2" :class="{ 'border-red-500': taxHorsepowerError }">
                        <span x-show="taxHorsepowerError" class="text-red-500 text-sm">La puissance fiscale doit être positive.</span>
                        @error('tax_horsepower') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="vehicle_value">Valeur du Véhicule (DH)</label>
                        <input type="number" name="vehicle_value" x-model="vehicle_value" @input="validateVehicleValue()" value="{{ old('vehicle_value', $data['vehicle_value'] ?? '') }}" min="1000" required class="w-full border rounded p-2" :class="{ 'border-red-500': vehicleValueError }">
                        <span x-show="vehicleValueError" class="text-red-500 text-sm">La valeur doit être supérieure ou égale à 1000 DH.</span>
                        @error('vehicle_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="registration_date">Date de Mise en Circulation</label>
                        <input type="date" name="registration_date" x-model="registration_date" @input="validateRegistrationDate()" value="{{ old('registration_date', $data['registration_date'] ?? '') }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': registrationDateError }">
                        <span x-show="registrationDateError" class="text-red-500 text-sm">La date doit être antérieure à aujourd'hui.</span>
                        @error('registration_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ route('auto.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Réinitialiser</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Suivant</button>
                    </div>
                @elseif ($step == 2)
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="date_obtention_permis">Date d'Obtention du Permis</label>
                        <input type="date" name="date_obtention_permis" x-model="date_obtention_permis" @input="validateDateObtentionPermis()" value="{{ old('date_obtention_permis', $data['date_obtention_permis'] ?? '') }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': dateObtentionPermisError }">
                        <span x-show="dateObtentionPermisError" class="text-red-500 text-sm">La date doit être antérieure à aujourd'hui.</span>
                        @error('date_obtention_permis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="bonus_malus">Bonus-Malus</label>
                        <input type="number" step="0.01" name="bonus_malus" x-model="bonus_malus" @input="validateBonusMalus()" value="{{ old('bonus_malus', $data['bonus_malus'] ?? '1.00') }}" min="0.50" max="3.50" required class="w-full border rounded p-2" :class="{ 'border-red-500': bonusMalusError }">
                        <span x-show="bonusMalusError" class="text-red-500 text-sm">Le bonus-malus doit être entre 0.50 et 3.50.</span>
                        @error('bonus_malus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="historique_accidents">Historique des Accidents</label>
                        <textarea name="historique_accidents" x-model="historique_accidents" class="w-full border rounded p-2" :class="{ 'border-red-500': historiqueAccidentsError }">{{ old('historique_accidents', $data['historique_accidents'] ?? '') }}</textarea>
                        @error('historique_accidents') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ route('auto.show', ['step' => 1]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Obtenir Devis</button>
                    </div>
                @endif
            </form>
       @elseif ($step == 3 && isset($data['devis_id']))
            <div class="text-center">
                <h2 class="text-2xl font-bold text-blue-600 mb-6">Votre Devis Assurance Auto</h2>
                @if (isset($data['devis_status']) && $data['devis_status'] == 'BROUILLON')
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <h2 class="text-2xl font-semibold mb-4 text-center">Choisissez votre formule</h2>
                        <form id="offer-selection-form" method="POST" action="{{ route('auto.select_offer', ['devis_id' => $data['devis_id']]) }}">
                            @csrf
                            <input type="hidden" name="devis_id" value="{{ $data['devis_id'] }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach (['basic' => 'Basique', 'standard' => 'Standard', 'premium' => 'Premium'] as $key => $label)
                                    <div x-data="{ openCalculation: false, openGaranties: {} }" class="border rounded-lg p-4 {{ $data['selected_offer'] == $key ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }} shadow-sm hover:shadow-md transition">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <label class="block text-lg font-medium text-gray-800">{{ $label }}</label>
                                                <p class="text-2xl font-bold text-blue-600">{{ number_format($data['formules_choisis'][$key] ?? 0, 2) }} DH/an</p>
                                            </div>
                                            <input type="radio" name="offer" value="{{ $key }}" {{ $data['selected_offer'] == $key ? 'checked' : '' }} required class="h-5 w-5 text-blue-600">
                                        </div>
                                        <h3 class="font-semibold mt-4 mb-2">Garanties incluses</h3>
                                        <ul class="text-sm text-gray-600 space-y-2">
                                            @if ($key == 'basic')
                                                <li x-data="{ open: false }">
                                                    <div class="flex justify-between items-center">
                                                        <span>Responsabilité Civile (RC)</span>
                                                        <button @click="open = !open" type="button" class="text-blue-600 hover:text-blue-800 text-xs flex items-center">
                                                            <span>Détails</span>
                                                            <svg x-bind:class="{ 'rotate-180': open }" class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div x-show="open" class="mt-1 text-xs bg-gray-50 p-2 rounded">
                                                        Couvre les dommages matériels et corporels causés aux tiers en cas d'accident dont vous êtes responsable.
                                                    </div>
                                                </li>
                                            @elseif ($key == 'standard')
                                                @foreach (['Responsabilité Civile (RC)' => 'Couvre les dommages matériels et corporels causés aux tiers.', 'Incendie' => 'Indemnise les dommages causés par un incendie ou une explosion.', 'Vol' => 'Couvre le vol du véhicule ou les dommages liés à une tentative de vol.', 'Bris de glace' => 'Prend en charge la réparation ou le remplacement des vitres.'] as $garantie => $description)
                                                    <li x-data="{ open: false }">
                                                        <div class="flex justify-between items-center">
                                                            <span>{{ $garantie }} {{ $garantie != 'Responsabilité Civile (RC)' ? '(' . number_format($data['calculation_factors'][strtolower(str_replace(' ', '_', $garantie)) . '_factor'] ?? 0, 2) . ' DH)' : '' }}</span>
                                                            <button @click="open = !open" type="button" class="text-blue-600 hover:text-blue-800 text-xs flex items-center">
                                                                <span>Détails</span>
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
                                            @elseif ($key == 'premium')
                                                @foreach (['Responsabilité Civile (RC)' => 'Couvre les dommages matériels et corporels causés aux tiers.', 'Incendie' => 'Indemnise les dommages causés par un incendie ou une explosion.', 'Vol' => 'Couvre le vol du véhicule ou les dommages liés à une tentative de vol.', 'Bris de glace' => 'Prend en charge la réparation ou le remplacement des vitres.', 'Dommages Collision' => 'Couvre les dommages à votre véhicule en cas de collision, même si vous êtes responsable.', 'Assistance' => 'Fournit une assistance routière en cas de panne ou d\'accident.', 'Protection Juridique' => 'Prend en charge les frais juridiques en cas de litige lié à votre véhicule.'] as $garantie => $description)
                                                    <li x-data="{ open: false }">
                                                        <div class="flex justify-between items-center">
                                                            <span>{{ $garantie }} {{ $garantie != 'Responsabilité Civile (RC)' ? '(' . number_format($data['calculation_factors'][strtolower(str_replace(' ', '_', $garantie)) . '_factor'] ?? 0, 2) . ' DH)' : '' }}</span>
                                                            <button @click="open = !open" type="button" class="text-blue-600 hover:text-blue-800 text-xs flex items-center">
                                                                <span>Détails</span>
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
                                        <button @click="openCalculation = !openCalculation" type="button" class="mt-4 text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <span>Détails du calcul</span>
                                            <svg x-bind:class="{ 'rotate-180': openCalculation }" class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="openCalculation" class="mt-2 text-sm text-gray-600 bg-gray-50 p-4 rounded">
                                            <h3 class="font-semibold mb-2">Calcul du tarif</h3>
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li><strong>Base (1840 DH pour 6CV)</strong>: {{ number_format($data['calculation_factors']['base_rate'] ?? 0, 2) }} DH</li>
                                                <li><strong>Puissance fiscale ({{ $data['tax_horsepower'] ?? '' }} CV)</strong>: {{ number_format($data['calculation_factors']['tax_horsepower_factor'] ?? 0, 2) }} DH (Facteur: {{ $data['tax_horsepower'] <= 5 ? '1x' : ($data['tax_horsepower'] <= 7 ? '1.3x' : ($data['tax_horsepower'] <= 10 ? '1.7x' : ($data['tax_horsepower'] <= 14 ? '2.2x' : '3x'))) }})</li>
                                                <li><strong>Valeur du véhicule ({{ number_format($data['vehicle_value'] ?? 0, 2) }} DH)</strong>: {{ number_format($data['calculation_factors']['vehicle_value_factor'] ?? 0, 2) }} (Valeur/1000)</li>
                                                <li><strong>Type de véhicule ({{ ucfirst($data['vehicle_type'] ?? '') }})</strong>: Facteur {{ $data['calculation_factors']['vehicle_type_factor'] ?? 1.0 }}</li>
                                                <li><strong>Carburant ({{ $data['fuel_type'] ?? '' }})</strong>: Facteur {{ $data['calculation_factors']['fuel_factor'] ?? 1.0 }}</li>
                                                @if ($key == 'basic')
                                                    <li><strong>Formule</strong>: Base + (Carburant × Puissance fiscale) = {{ number_format($data['formules_choisis']['basic'] ?? 0, 2) }} DH</li>
                                                @elseif ($key == 'standard')
                                                    <li><strong>Formule</strong>: Basic + (0.2 × Valeur × Type × Expérience × Âge véhicule × Bonus-Malus × Incendie × Vol × Bris de glace) = {{ number_format($data['formules_choisis']['standard'] ?? 0, 2) }} DH</li>
                                                @elseif ($key == 'premium')
                                                    <li><strong>Formule</strong>: Standard + (0.5 × Dommages collision × Assistance × Protection juridique × Bonus-Malus) = {{ number_format($data['formules_choisis']['premium'] ?? 0, 2) }} DH</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('offer') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                            <div class="flex justify-between mt-6">
                                <a href="{{ route('auto.show', ['step' => 2]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Confirmer la formule</button>
                            </div>
                        </form>
                        
                    </div>
                @else
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <p><strong>Formule choisie :</strong> {{ ucfirst($data['selected_offer'] ?? 'Aucune') }}</p>
                        <p><strong>Montant du devis :</strong> {{ number_format($data['montant_base'] ?? 0, 2) }} DH/an</p>
                        <div class="mt-6 flex justify-center space-x-4">
                            <a href="{{ route('auto.download', ['devis_id' => $data['devis_id']]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Télécharger le devis</a>
                            <form action="{{ route('auto.email', ['devis_id' => $data['devis_id']]) }}" method="POST" class="inline-flex items-center">
                                @csrf
                                <input type="email" name="email" placeholder="Votre e-mail" required class="border rounded p-2 mr-2">
                                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Envoyer par e-mail</button>
                            </form>
                           <a href="{{ route('auto.subscribe', ['devis_id' => $data['devis_id']]) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Souscrire</a>
                         </div>
                    </div>
                @endif
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('auto.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Nouveau Devis</a>
                </div>
            </div>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>Erreur : Aucun devis trouvé. Veuillez recommencer.</p>
                <a href="{{ route('auto.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mt-4 inline-block">Recommencer</a>
            </div>
        @endif
        
        <div class="mt-8">
            <h2 class="text-xl font-bold text-blue-600 mb-4">Actualités (WordPress)</h2>
            @if (!empty($posts))
                <ul class="list-disc pl-5">
                    @foreach ($posts as $post)
                        <li><a href="{{ $post['link'] }}" class="text-blue-600 hover:underline">{{ $post['title']['rendered'] }}</a></li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Aucune actualité disponible.</p>
            @endif
        </div>
    </div>
    <script>
        function formValidation() {
            return {
                vehicle_type: '{{ $data['vehicle_type'] ?? '' }}',
                make: '{{ $data['make'] ?? '' }}',
                model: '{{ $data['model'] ?? '' }}',
                fuel_type: '{{ $data['fuel_type'] ?? '' }}',
                tax_horsepower: '{{ $data['tax_horsepower'] ?? '' }}',
                vehicle_value: '{{ $data['vehicle_value'] ?? '' }}',
                registration_date: '{{ $data['registration_date'] ?? '' }}',
                date_obtention_permis: '{{ $data['date_obtention_permis'] ?? '' }}',
                bonus_malus: '{{ $data['bonus_malus'] ?? '1.00' }}',
                historique_accidents: '{{ $data['historique_accidents'] ?? '' }}',
                vehicleTypeError: false,
                makeError: false,
                modelError: false,
                fuelTypeError: false,
                taxHorsepowerError: false,
                vehicleValueError: false,
                registrationDateError: false,
                dateObtentionPermisError: false,
                bonusMalusError: false,
                historiqueAccidentsError: false,
                validateVehicleType() {
                    this.vehicleTypeError = !this.vehicle_type;
                },
                validateMake() {
                    this.makeError = !this.make.trim();
                },
                validateModel() {
                    this.modelError = !this.model.trim();
                },
                validateFuelType() {
                    this.fuelTypeError = !this.fuel_type;
                },
                validateTaxHorsepower() {
                    this.taxHorsepowerError = !this.tax_horsepower || this.tax_horsepower < 1;
                },
                validateVehicleValue() {
                    this.vehicleValueError = !this.vehicle_value || this.vehicle_value < 1000;
                },
                validateRegistrationDate() {
                    const today = new Date().toISOString().split('T')[0];
                    this.registrationDateError = !this.registration_date || this.registration_date > today;
                },
                validateDateObtentionPermis() {
                    const today = new Date().toISOString().split('T')[0];
                    this.dateObtentionPermisError = !this.date_obtention_permis || this.date_obtention_permis > today;
                },
                validateBonusMalus() {
                    this.bonusMalusError = !this.bonus_malus || this.bonus_malus < 0.50 || this.bonus_malus > 3.50;
                },
                validateHistoriqueAccidents() {
                    this.historiqueAccidentsError = false; // Optional field
                }
            }
        }
    </script>
</body>
</html>