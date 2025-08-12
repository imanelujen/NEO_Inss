<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis Assurance Habitation - Neo Assurances</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-4 max-w-2xl">
        <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Devis Assurance Habitation</h1>
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
                <span class="{{ $step >= 1 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Info propriété</span>
                <span class="{{ $step >= 2 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Adresse</span>
                <span class="{{ $step == 3 ? 'text-blue-600 font-bold' : 'text-gray-500' }}">Résultat</span>
            </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($step / 3) * 100 }}%"></div>
            </div>
        </div>
        <!-- Form -->
        @if ($step == 1 || $step == 2)
            <form method="POST" action="{{ route('habit.simulation.store') }}" class="bg-white p-6 rounded-lg shadow-md" x-data="formValidation()">
                @csrf
                <input type="hidden" name="step" value="{{ $step }}">
                @if ($step == 1)
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Sélectionnez votre type de logement</label>
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
                    <label class="block text-gray-700 font-medium mb-2">Surface (m²)</label>
                    <input type="number" name="surface_area" x-model="surface_area" @input="validateSurfaceArea()" value="{{ $data['surface_area'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': surfaceError }">
                    <span x-show="surfaceError" class="text-red-500 text-sm">La surface est requise.</span>
                    @error('surface_area') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Statut d'Occupation</label>
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
                    <label class="block text-gray-700 font-medium mb-2">Valeur de la maison (DH)</label>
                    <input type="number" name="housing_value" x-model="housing_value" @input="validateHouseValue()" value="{{ $data['housing_value'] ?? '' }}" min="10000" required class="w-full border rounded p-2" :class="{ 'border-red-500': houseValueError }">
                    <span x-show="houseValueError" class="text-red-500 text-sm">La valeur doit être supérieure ou égale à 10000 DH.</span>
                    @error('housing_value') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Date de construction</label>
                    <input type="number" name="construction_year" x-model="construction_year" min="1800" max="{{ now()->year }}" placeholder="YYYY" @input="validateConstructionYear()" value="{{ $data['construction_year'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': constructionYearError }">
                    <span x-show="constructionYearError" class="text-red-500 text-sm">L'année doit être comprise entre 1800 et {{ date('Y') }}.</span>
                    @error('construction_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('habit.simulation.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Réinitialiser</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Suivant</button>
                </div>
            @elseif ($step == 2)
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Ville</label>
                    <input type="text" name="ville" x-model="ville" @input="validateVille()" value="{{ $data['ville'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': villeError }">
                    <span x-show="villeError" class="text-red-500 text-sm">La ville est requise.</span>
                    @error('ville') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Rue</label>
                    <input type="text" name="rue" x-model="rue" @input="validateRue()" value="{{ $data['rue'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': rueError }">
                    <span x-show="rueError" class="text-red-500 text-sm">La rue est requise.</span>
                    @error('rue') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Code postal</label>
                    <input type="text" name="code_postal" x-model="code_postal" @input="validateCodePostal()" value="{{ $data['code_postal'] ?? '' }}" required class="w-full border rounded p-2" :class="{ 'border-red-500': codePostalError }">
                    <span x-show="codePostalError" class="text-red-500 text-sm">Le code postal est requis.</span>
                    @error('code_postal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('habit.simulation.show', ['step' => 1]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Obtenir Devis</button>
                </div>
                @endif
            </form>
        @endif
             @if ($step == 3 && isset($data['devis_id']))
                <input type="hidden" name="devis_id" value="{{ $data['devis_id'] }}">
                 <div class="text-center">
                    <h2 class="text-2xl font-bold text-blue-600 mb-4">Votre Devis Assurance Habitation</h2>
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <p><strong>Type de logement :</strong> {{ $data['housing_type'] }}</p>
                        <p><strong>Surface :</strong> {{ $data['surface_area'] }} m²</p>
                        <p><strong>Statut d'occupation :</strong> {{ $data['occupancy_status'] }}</p>
                        <p><strong>Valeur estimée :</strong> {{ number_format($data['housing_value'], 2) }} DH</p>
                        <p><strong>Année de construction :</strong> {{ $data['construction_year'] }}</p>
                        <p><strong>Adresse :</strong> {{ $data['rue'] }}, {{ $data['ville'] }}, {{ $data['code_postal'] }}</p>
                    </div>
                    @if (isset($data['devis_status']) && $data['devis_status'] == 'BROUILLON')
                        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                            <h3 class="text-lg font-semibold mb-4">Choisissez votre formule</h3>
                            <p class="text-gray-500 text-sm mb-2">Form action: {{ route('habit.select_offer', ['devis_id' => $data['devis_id']]) }}</p>
                        <form id="offer-selection-form" method="POST" action="{{ route('habit.select_offer', ['devis_id' => $data['devis_id']]) }}">
                            @csrf
                            <input type="hidden" name="devis_id" value="{{ $data['devis_id'] }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                @foreach (['essentiel' => 'Essentiel', 'confort' => 'Confort', 'excellence' => 'Excellence'] as $key => $label)
                                    <label class="border rounded-lg p-4 cursor-pointer hover:bg-blue-50 {{ $key == 'essentiel' ? 'border-blue-600' : 'border-gray-300' }}">
                                        <input type="radio" name="offer" value="{{ $key }}" required class="mr-2" x-model="selectedOffer">
                                        <span class="text-lg font-semibold">{{ $label }}</span>
                                        <p class="text-2xl font-bold text-blue-600">{{ number_format($data['formules_choisis'][$key] ?? 0, 2) }} DH/an</p>
                                        <ul class="mt-2 text-sm text-gray-700">
                                            <li>
                                                @if ($key == 'essentiel')
                                                    Garanties essentielles, Incendie, Dégâts des eaux, Vol, Catastrophes naturelles
                                                @elseif ($key == 'confort')
                                                    Essentiel + Vol/Incendie renforcé
                                                @else
                                                    Essentiel + Tous risques
                                                @endif
                                            </li>
                                        </ul>
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Confirmer la formule</button>
                        </form>
                    </div>
                @else
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <p><strong>Formule choisie :</strong> {{ ucfirst($data['selected_offer'] ?? 'Aucune') }}</p>
                        <p><strong>Montant du devis :</strong> {{ number_format($data['montant_base'] ?? 0, 2) }} DH/an</p>
                        <div class="mt-6 flex justify-center space-x-4">
                            <a href="{{ route('habit.subscribe', ['devis_id' => $data['devis_id']]) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Souscrire</a>
                            <form action="{{ route('habit.email', ['devis_id' => $data['devis_id']]) }}" method="POST" class="inline">
                                @csrf
                                <input type="email" name="email" placeholder="Votre e-mail" required class="border rounded p-2 mr-2">
                                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Envoyer par e-mail</button>
                            </form>
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    @foreach (['essentiel' => 'Essentiel', 'confort' => 'Confort', 'excellence' => 'Excellence'] as $key => $label)
                        <div class="border rounded-lg p-4 shadow-md {{ $key == 'essentiel' ? 'border-blue-600' : 'border-gray-300' }}">
                            <h3 class="text-lg font-semibold mb-2">{{ $label }}</h3>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($data['formules_choisis'][$key] ?? 0, 2) }} DH</p>
                            <p class="text-sm text-gray-600">/an</p>
                            <ul class="mt-2 text-sm text-gray-700">
                                <li>
                                    @if ($key == 'essentiel')
                                        Garanties essentielles, Contribution au Fond des Garanties des Victimes, Incendie, événements climatiques et dégâts des eaux, Vol et actes de vandalisme, Catastrophes naturelles & technologiques, Indemnisation du mobilier, Assistance
                                    @elseif ($key == 'confort')
                                        Essentiel + Vol/Incendie
                                    @else
                                        Essentiel + Tous risques
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('habit.simulation.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Nouveau Devis</a>
                </div>
            </div>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>Erreur : Aucun devis trouvé. Veuillez recommencer.</p>
                <a href="{{ route('habit.simulation.reset') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mt-4 inline-block">Recommencer</a>
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
</body>
</html>


