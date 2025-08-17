<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souscription - Neo Assurances</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-4 max-w-md">
        <h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Souscription</h1>
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
        <form method="POST" action="{{ route('habit.store_subscription', ['devis_id' => $devis->id_devis]) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Adresse</label>
                <input type="text" name="address" value="{{ old('address') }}" required class="w-full border rounded p-2">
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full border rounded p-2">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Garanties</label>
                @foreach (['Incendie', 'Dégâts des eaux', 'Vol', 'Catastrophes naturelles'] as $garantie)
                    <label class="block">
                        <input type="checkbox" name="garanties[]" value="{{ $garantie }}" {{ in_array($garantie, old('garanties', [])) ? 'checked' : '' }}>
                        {{ $garantie }}
                    </label>
                @endforeach
                @error('garanties') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Franchise (DH)</label>
                <input type="number" name="franchise" value="{{ old('franchise', 0) }}" min="0" required class="w-full border rounded p-2">
                @error('franchise') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-between">
                <a href="{{ route('habit.result', ['devis_id' => $devis->id_devis]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Retour</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Confirmer la souscription</button>
            </div>
        </form>
    </div>
</body>
</html>