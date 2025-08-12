<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souscription Habitation - Neo Assurances</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-4 max-w-md">
        <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Souscription Habitation</h1>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('habitation.store_subscription', ['devis_id' => $devis->id]) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="address">Adresse</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" required class="w-full border rounded p-2">
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="phone">Téléphone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required class="w-full border rounded p-2">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="payment_frequency">Fréquence de paiement</label>
                <select name="payment_frequency" id="payment_frequency" required class="w-full border rounded p-2">
                    <option value="" disabled selected>Sélectionner</option>
                    <option value="manual">Manuel</option>
                    <option value="monthly">Mensuel</option>
                </select>
                @error('payment_frequency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Garanties</label>
                <label><input type="checkbox" name="garanties[]" value="incendie"> Incendie</label>
                <label><input type="checkbox" name="garanties[]" value="degat_des_eaux"> Dégât des eaux</label>
                <label><input type="checkbox" name="garanties[]" value="vol"> Vol</label>
                @error('garanties') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="franchise">Franchise (€)</label>
                <input type="number" name="franchise" id="franchise" value="{{ old('franchise') }}" required class="w-full border rounded p-2">
                @error('franchise') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Payer</button>
            </div>
        </form>
    </div>
</body>
</html>
