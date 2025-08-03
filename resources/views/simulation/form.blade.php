<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Devis Assurance Auto - Neo Assurances</title>



    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
   @include('blog.navbar')
    <div class="container mx-auto p-4 max-w-2xl">
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
        <!--progress bar-->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <span class="{{ $step >= 1 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Info Véhicule</span>
                <span class="{{ $step >= 2 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Conducteur</span>
                <span class="{{ $step == 3 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Résultat</span>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($step / 3) * 100 }}%">
                </div>
            </div>
</div>
        <!--form-->
        <form method="POST" action="{{ route('simulation.store') }}" class="bg-white p-6 rounded-lg shadow-md" x-data="formValidation()">
            @csrf
            <input type="hidden" name="step" value="{{$step}}">
            @if ($step == 1)
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Type de Véhicule</label>
                <select name="vehicle_type" x-model="vehicle_type" @change="validateVehicleType()" required class="w-full border rounded p-2" :class="{ 'border-red-500': vehicleTypeError }">
                        <option value="" disabled selected>Sélectionner</option>
                        <option value="sedan">Berline</option>
                        <option value="suv">SUV</option>
                        <option value="truck">Camion</option>
                        <option value="motorcycle">Moto</option>
                    </select>
                <span x-show="vehicleTypeError" class="text-red-500 text-sm">Le type de véhicule est requis.</span>
            </div>
<!--marque-->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Marque</label>
                    <input type="text" name="make" x-model="make" @input="validateMake()" value="{{ $data['make'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': makeError }">
                    <span x-show="makeError" class="text-red-500 text-sm">La marque est requise.</span>
                    @error('make') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
<!--model-->
           <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Modèle</label>
                    <input type="text" name="model" x-model="model" @input="validateModel()" value="{{ $data['model'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': modelError }">
                    <span x-show="modelError" class="text-red-500 text-sm">Le modèle est requis.</span>
                    @error('model') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
<!--energie-->
<div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Carburant</label>
                    <select name="fuel_type" x-model="fuel_type" @change="validateFuelType()" required class="w-full border rounded p-2" :class="{ 'border-red-500': fuelTypeError }">
                        <option value="" disabled selected>Sélectionner</option>
                        <option value="ESSENCE">Essence</option>
                        <option value="DIESEL">Diesel</option>
                        <option value="ELECTRIQUE">Électrique</option>
                        <option value="HYBRIDE">Hybride</option>
                    </select>
<span x-show="fuelTypeError" class="text-red-500 text-sm">Le type de carburant est requis.</span>
                    @error('fuel_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
<!--Puissance fiscale-->
            <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Puissance Fiscale (CV)</label>
                    <input type="number" name="tax_horsepower" x-model="tax_horsepower" @input="validateTaxHorsepower()" value="{{ $data['tax_horsepower'] ?? '' }}" min="1" required class="w-full border rounded p-2" :class="{ 'border-red-500': taxHorsepowerError }">
                    <span x-show="taxHorsepowerError" class="text-red-500 text-sm">La puissance fiscale doit être positive.</span>
                    @error('tax_horsepower') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
<!--vehicule value-->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Valeur du Véhicule (€)</label>
                    <input type="number" name="vehicle_value" x-model="vehicle_value" @input="validateVehicleValue()" value="{{ $data['vehicle_value'] ?? '' }}" min="1000" required class="w-full border rounded p-2" :class="{ 'border-red-500': vehicleValueError }">
                    <span x-show="vehicleValueError" class="text-red-500 text-sm">La valeur doit être supérieure ou égale à 1000 €.</span>
                    @error('vehicle_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
<!--vehicule value-->
<div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Date de Mise en Circulation</label>
                    <input type="date" name="registration_date" x-model="registration_date" @input="validateRegistrationDate()" value="{{ $data['registration_date'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': registrationDateError }">
                    <span x-show="registrationDateError" class="text-red-500 text-sm">La date doit être antérieure à aujourd'hui.</span>
                    @error('registration_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

   <div class="flex justify-between">
                    <a href="{{ route('simulation.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Réinitialiser</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Suivant</button>
                </div>

            @elseif ($step == 2)
    <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Date d'Obtention du Permis</label>
                    <input type="date" name="date_obtention_permis" x-model="date_obtention_permis" @input="validateDateObtentionPermis()" value="{{ $data['date_obtention_permis'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': dateObtentionPermisError }">
                    <span x-show="dateObtentionPermisError" class="text-red-500 text-sm">La date doit être antérieure à aujourd'hui.</span>
                    @error('date_obtention_permis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Bonus-Malus</label>
                    <input type="number" step="0.01" name="bonus_malus" x-model="bonus_malus" @input="validateBonusMalus()" value="{{ $data['bonus_malus'] ?? '1.00' }}" min="0.50" max="3.50" required class="w-full border rounded p-2" :class="{ 'border-red-500': bonusMalusError }">
                    <span x-show="bonusMalusError" class="text-red-500 text-sm">Le bonus-malus doit être entre 0.50 et 3.50.</span>
                    @error('bonus_malus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Historique des Accidents</label>
                    <textarea name="historique_accidents" x-model="historique_accidents" class="w-full border rounded p-2" :class="{ 'border-red-500': historiqueAccidentsError }">{{ $data['historique_accidents'] ?? '' }}</textarea>
                    @error('historique_accidents') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('simulation.show', ['step' => 1]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Obtenir Devis</button>
                </div>

         <!-- Navigation Buttons -->
@elseif ($step == 3)
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4">Votre Devis Assurance Auto</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach (['basic' => 'Basique', 'standard' => 'Standard', 'premium' => 'Premium'] as $key => $label)
                            <div class="border rounded-lg p-4 shadow-md {{ $key == 'standard' ? 'border-blue-600' : 'border-gray-300' }}">
                                <h3 class="text-lg font-semibold mb-2">{{ $label }}</h3>
                                <p class="text-2xl font-bold text-blue-600">{{ $data['formules_choisis'][$key] }} €</p>
                                <p class="text-sm text-gray-600">/an</p>
                                <ul class="mt-2 text-sm text-gray-700">
                                    <li>{{ $key == 'basic' ? 'Responsabilité civile' : ($key == 'standard' ? 'RC + Vol/Incendie' : 'RC + Tous risques') }}</li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex justify-center">
                        <a href="{{ route('simulation.reset') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Nouveau Devis</a>
                    </div>
                </div>
            @endif
</form>
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
                    this.historiqueAccidentsError = !this.historique_accidents;
                }
            }


        }

    </script>
</body>
</html>
