<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis Assurance Auto - UMBRELLA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-3">
    <div class="container mx-auto max-w-3xl w-full">
        <h1 class="text-2xl md:text-3xl font-bold text-green mb-6 text-center ">Devis Assurance Auto</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 shadow-sm text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 shadow-sm text-sm">
              {{ session('success') }}
            </div>
        @endif

        <!-- Progress Bar -->
  <div class="mb-8">
      <div class="flex justify-between text-xs sm:text-sm font-medium mb-2">
        <span class="{{ $step >= 1 ? 'text-green-600 font-bold' : 'text-gray-400' }}">1. Véhicule</span>
        <span class="{{ $step >= 2 ? 'text-green-600 font-bold' : 'text-gray-400' }}">2. Conducteur</span>
        <span class="{{ $step == 3 ? 'text-green-600 font-bold' : 'text-gray-400' }}">3. Résultat</span>
      </div>
      <div class="w-full bg-gray-200 h-2 rounded-full">
        <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
             style="width: {{ ($step / 3) * 100 }}%"></div>
      </div>
    </div>

        <!-- Form -->
     <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8">
        @if ($step == 1 || $step == 2)
            <form method="POST" action="{{ route('auto.store') }}" x-data="formValidation()">
                @csrf
                <input type="hidden" name="step" value="{{ $step }}">
                @if ($step == 1)
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="vehicle_type">Type de véhicule</label>
                        <select name="vehicle_type" id="vehicle_type" x-model="vehicle_type" @change="validateVehicleType()" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': vehicleTypeError }">
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
                        <div x-data="{ make: '' }" class="space-y-4">
                        <label class="block text-lg font-medium text-blue-900" for="make">Marque</label>
                         <select
        x-model="make"
        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500">
   <option value="">-- Sélectionnez --</option>
   <option value="ALFA_ROMEO">Alfa Romeo</option>
   <option value="ASTON_MARTIN">Aston Martin</option>
   <option value="AUDI">Audi</option>
   <option value="BENTLEY">Bentley</option>
   <option value="BMW">BMW</option>
   <option value="BUGATTI">Bugatti</option>
   <option value="BYD">BYD</option>
   <option value="CADILLAC">Cadillac</option>
   <option value="CHEVROLET">Chevrolet</option>
   <option value="CHRYSLER">Chrysler</option>
   <option value="CITROEN">Citroën</option>
   <option value="CUPRA">Cupra</option>
   <option value="DACIA">Dacia</option>
   <option value="DAEWOO">Daewoo</option>
   <option value="DAIHATSU">Daihatsu</option>
<option value="DODGE">Dodge</option>
<option value="DS">DS</option>
<option value="FERRARI">Ferrari</option>
<option value="FIAT">Fiat</option>
<option value="FORD">Ford</option>
<option value="GENESIS">Genesis</option>
<option value="GMC">GMC</option>
<option value="HONDA">Honda</option>
<option value="HUMMER">Hummer</option>
<option value="HYUNDAI">Hyundai</option>
<option value="INFITI">Infiniti</option>
<option value="ISUZU">Isuzu</option>
<option value="JAGUAR">Jaguar</option>
<option value="JEEP">Jeep</option>
<option value="KIA">Kia</option>
<option value="KOENIGSEGG">Koenigsegg</option>
<option value="LADA">Lada</option>
<option value="LAMBORGHINI">Lamborghini</option>
<option value="LANCIA">Lancia</option>
<option value="LAND_ROVER">Land Rover</option>
<option value="LEXUS">Lexus</option>
<option value="LINCOLN">Lincoln</option>
<option value="LOTUS">Lotus</option>
<option value="MASERATI">Maserati</option>
<option value="MAYBACH">Maybach</option>
<option value="MAZDA">Mazda</option>
<option value="MCLAREN">McLaren</option>
<option value="MERCEDES">Mercedes</option>
<option value="MG">MG</option>
<option value="MINI">Mini</option>
<option value="MITSUBISHI">Mitsubishi</option>
<option value="NISSAN">Nissan</option>
<option value="OPEL">Opel</option>
<option value="PEUGEOT">Peugeot</option>
<option value="PORSCHE">Porsche</option>
<option value="RENAULT">Renault</option>
<option value="ROLLS_ROYCE">Rolls Royce</option>
<option value="ROVER">Rover</option>
<option value="SAAB">Saab</option>
<option value="SEAT">Seat</option>
<option value="ŠKODA">Škoda</option>
<option value="SMART">Smart</option>
<option value="SSANGYONG">SsangYong</option>
<option value="SUBARU">Subaru</option>
<option value="SUZUKI">Suzuki</option>
<option value="TESLA">Tesla</option>
<option value="TOYOTA">Toyota</option>
<option value="VOLKSWAGEN">Volkswagen</option>
<option value="VOLVO">Volvo</option>

        </select>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-3">
          <!-- Example card -->
        <div
            @click="make = 'AUDI'"
            :class="make === 'AUDI' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/audi.png" alt="Audi" class="h-10 sm:h-12 object-contain mb-1">
            <span class="text-sm font-semibold">AUDI</span>
        </div>

        <div
            @click="make = 'BMW'"
            :class="make === 'BMW' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/bmw.png" alt="BMW" class="h-8 sm:h-12 object-contain mb-1">
            <span class="text-sm font-semibold">BMW</span>
        </div>
        <div
            @click="make = 'CITROEN'"
            :class="make === 'CITROEN' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/citreon.jpg" alt="Citroën" class="h-8 sm:h-12 object-contain mb-1">
            <span class="text-sm font-semibold">CITROËN</span>
       </div>
        <div
            @click="make = 'DACIA'"
            :class="make === 'DACIA' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/dacia.jpg" alt="Dacia" class="h-8 sm:h-12 object-contain mb-1">
            <span class="text-sm font-semibold">DACIA</span>
        </div>
        <div
            @click="make = 'FIAT'"
            :class="make === 'FIAT' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/fiat.jpg" alt="Fiat" class="h-8 sm:h-10 object-contain mb-1">
            <span class="text-sm font-semibold">FIAT</span>
        </div>
        <div
            @click="make = 'HYUNDAI'"
            :class="make === 'HYUNDAI' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/HYUNDAI.jpg" alt="HYUNDAI" class="h-8 sm:h-12 object-contain mb-1">
            <span class="text-sm font-semibold">HYUNDAI</span>
        </div>
        <div
            @click="make = 'MERCEDES'"
            :class="make === 'MERCEDES' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/MERCEDES.jpg" alt="MERCEDES" class="h-8 sm:h-10 object-contain mb-1">
            <span class="text-sm font-semibold">MERCEDES</span>
        </div>
        <div
            @click="make = 'PEUGEOT'"
            :class="make === 'PEUGEOT' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/PEUGEOT.png" alt="PEUGEOT" class="h-8 sm:h-10 object-contain mb-1">
            <span class="text-sm font-semibold">PEUGEOT</span>
        </div>
        <div
            @click="make = 'RENAULT'"
            :class="make === 'RENAULT' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/RENAULT.jpg" alt="RENAULT" class="h-8 sm:h-10 object-contain mb-1">
            <span class="text-sm font-semibold">RENAULT</span>
        </div>
        <div
            @click="make = 'TOYOTA'"
            :class="make === 'TOYOTA' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/TOYOTA.jpg" alt="TOYOTA" class="h-8 sm:h-10 object-contain mb-1">
            <span class="text-sm font-semibold">TOYOTA</span>
        </div>
        <div
            @click="make = 'VOLKSWAGEN'"
            :class="make === 'VOLKSWAGEN' ? 'border-red-500 ring-2 ring-red-500' : 'border-gray-200'"
            class="cursor-pointer border rounded-lg p-3 flex flex-col items-center justify-center hover:shadow-md transition">
            <img src="/images/VOLKSWAGEN.png" alt="VOLKSWAGEN" class="h-10 sm:h-12 object-contain mb-1">
            <span class="text-sm font-semibold">VOLKSWAGEN</span>
        </div>
      <span x-show="makeError" class="text-red-500 text-sm">La marque est requise.</span>
         @error('make') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
     <input type="hidden" name="make" x-model="make" @input="validateMake()" value="{{ old('make', $data['make'] ?? '') }}">
                    </div>
                </div>


                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="model">Modèle</label>
                        <input type="text" name="model" x-model="model" @input="validateModel()" value="{{ old('model', $data['model'] ?? '') }}" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': modelError }">
                        <span x-show="modelError" class="text-red-500 text-sm">Le modèle est requis.</span>
                        @error('model') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="fuel_type">Carburant</label>
                        <select name="fuel_type" id="fuel_type" x-model="fuel_type" @change="validateFuelType()" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': fuelTypeError }">
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
                        <label class="block text-lg font-medium text-blue-900" for="tax_horsepower">Puissance Fiscale (CV)</label>
                        <input type="number" name="tax_horsepower" x-model="tax_horsepower" @input="validateTaxHorsepower()" value="{{ old('tax_horsepower', $data['tax_horsepower'] ?? '') }}" min="5" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': taxHorsepowerError }">
                        <span x-show="taxHorsepowerError" class="text-red-500 text-sm">La puissance fiscale doit être supérieure ou égale à 5..</span>
                        @error('tax_horsepower') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="vehicle_value">Valeur du Véhicule (DH)</label>
                        <input type="number" name="vehicle_value" x-model="vehicle_value" @input="validateVehicleValue()" value="{{ old('vehicle_value', $data['vehicle_value'] ?? '') }}" min="1000" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': vehicleValueError }">
                        <span x-show="vehicleValueError" class="text-red-500 text-sm">La valeur doit être supérieure ou égale à 1000 DH.</span>
                        @error('vehicle_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="registration_date">Date de Mise en Circulation</label>
                        <input type="date" name="registration_date" x-model="registration_date" @input="validateRegistrationDate()" value="{{ old('registration_date', $data['registration_date'] ?? '') }}" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': registrationDateError }">
                        <span x-show="registrationDateError" class="text-red-500 text-sm">La date doit être antérieure à aujourd'hui.</span>
                        @error('registration_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
           <div class="flex justify-between items-center mt-6 space-x-4">
    <a href="{{ route('auto.reset') }}"
       class="bg-gray-500 text-white px-5.5 py-3 rounded-xl shadow-md hover:bg-gray-600 hover:shadow-lg transition">
       Réinitialiser
    </a>

    <button type="submit"
       class="bg-green-600 text-white px-6 py-3 rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg transition">
       Suivant →
    </button>
</div>

                @elseif ($step == 2)
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="date_obtention_permis">Date d'Obtention du Permis</label>
                        <input type="date" name="date_obtention_permis" x-model="date_obtention_permis" @input="validateDateObtentionPermis()" value="{{ old('date_obtention_permis', $data['date_obtention_permis'] ?? '') }}" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" :class="{ 'border-red-500': dateObtentionPermisError }">
                        <span x-show="dateObtentionPermisError" class="text-red-500 text-sm">La date doit être antérieure à aujourd'hui.</span>
                        @error('date_obtention_permis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="bonus_malus">Bonus-Malus</label>
                        <input type="number"  name="bonus_malus" x-model="bonus_malus" @input="validateBonusMalus()" value="{{ old('bonus_malus', $data['bonus_malus'] ?? '1.00') }}" min="0.50" max="3.50" readonly class="w-full border rounded-lg p-2.5 bg-gray-100 cursor-not-allowed focus:ring-0 focus:border-gray-300 transition shadow-sm">
                        <span x-show="bonusMalusError" class="text-red-500 text-sm">Le bonus-malus doit être entre 0.50 et 3.50.</span>
                        @error('bonus_malus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-lg font-medium text-blue-900" for="historique_accidents">Historique des Accidents</label>
                        <input  name="historique_accidents" x-model="historique_accidents" class="w-full border rounded p-2" :class="{ 'border-red-500': historiqueAccidentsError }">{{ old('historique_accidents', $data['historique_accidents'] ?? '') }}</textarea>
                        @error('historique_accidents') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('auto.show', ['step' => 1]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Obtenir Devis</button>
                    </div>
                @endif
            </form>
       @elseif ($step == 3 && isset($data['devis_id']))
 <div class="relative">
        <!-- Background image area -->
        <div class="step3-header-bg">
            <h2 class="text-2xl font-bold text-green-600 mb-6 text-center relative z-10">
                Votre Devis Assurance Auto
            </h2>
        </div>
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
                                                <p class="text-2xl font-bold text-green-700">{{ number_format($data['formules_choisis'][$key] ?? 0, 2) }} DH/An</p>
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
                                    <!--    <button @click="openCalculation = !openCalculation" type="button" class="mt-4 text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <span>Détails du calcul</span>
                                            <svg x-bind:class="{ 'rotate-180': openCalculation }" class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>-->
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
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirmer la formule</button>
                            </div>
                        </form>

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
                    this.taxHorsepowerError = !this.tax_horsepower || this.tax_horsepower < 5;
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
